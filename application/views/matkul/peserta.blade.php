@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/bower_components/bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css') }}">
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>--}}
{{--<script src="//cdn.datatables.net/plug-ins/1.10.21/api/fnPagingInfo.js"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
{{--<script src="{{ asset('assets/plugins/select2-cascade.js') }}"></script>--}}
{{--<script src="{{ asset('assets/bower_components/moment/min/moment.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/bower_components/bootstrap4-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>--}}
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

let prodi_avail = [];
let prodi_mhs_selected = [];

function init_page_level(){
    ajaxcsrf();
    $('.select2').select2();
    $('#prodi_id').select2({placeholder : 'Pilih Prodi'});
    $('#prodi_id').prepend('<option value="ALL">Semua Prodi</option>');

    @foreach($prodi as $p)
        prodi_avail.push('{{ $p->kodeps }}');
    @endforeach

    @if(!empty($prodi_mhs_selected))
    @foreach($prodi_mhs_selected as $kodeps)
        prodi_mhs_selected.push('{{ $kodeps }}');
    @endforeach

    $('#prodi_id').val(prodi_mhs_selected).trigger('change');
    init_peserta_table_value();
    @endif

}

// $('#matkul_id').on('select2:select', function (e) {
//     init_topik_table_value();
// });

$('#prodi_id').on('select2:select', function (e) {
    let data = e.params.data;
    if(data.id == 'ALL'){
        $(this).val(null).trigger('change');
        $(this).val('ALL').trigger('change');
        init_peserta_table_value();
    }else{
        let values = $(this).val();
        if (values) {
            let i = values.indexOf('ALL');
            if (i >= 0) {
                values.splice(i, 1);
                $(this).val(values).change();
            }
        }
        init_peserta_table_value();
    }
});

$('#prodi_id').on('select2:unselect', function (e) {
    let data = e.params.data;
    if(data.id == 'ALL'){
        $.each(prodi_avail,function(i,v){
            // topik_jumlah_soal[i] = topik_jumlah_soal_asli[i];
        });
    }else{
        // topik_jumlah_soal[data.id] = [];
    }
    init_peserta_table_value();

});

function init_peserta_table_value(){
    let selected_ids = $('#prodi_id').val();
    if($.inArray('ALL', selected_ids) !== -1){
        selected_ids = [];
        $.each(prodi_avail,function(i,v){
            selected_ids.push(v);
        });
    }
    $.ajax({
        url: "{{ site_url('matkul/ajax/get_mhs_prodi') }}",
        data: { 'matkul_id' : '{{ $matkul->id_matkul }}', 'kodeps': JSON.stringify(selected_ids) },
        type: 'POST',
        async: false,
        success: function (response) {
            $('#tbody_tb_peserta').html('');
            let mhs_matkul_existing = [];
            if(!$.isEmptyObject(response.mhs_matkul)) {
                $.each(response.mhs_matkul, function (i, item) {
                    mhs_matkul_existing.push(item.id_mahasiswa);
                });
            }
            if(!$.isEmptyObject(response.mhs)) {
                $.each(response.mhs, function (i, item) {
                    let chkbox = $('<input>').attr('class', 'chkbox_pilih_peserta').attr('type', 'checkbox').attr('name', 'peserta[]').attr('value', item.id_mahasiswa);
                    if(mhs_matkul_existing.includes(item.id_mahasiswa))
                        chkbox.prop('checked', true);
                    $('<tr>').append(
                        $('<td>').text(i + 1),
                        $('<td>').text(item.nama),
                        $('<td>').text(item.nim),
                        $('<td>').text(item.prodi),
                        $('<td>').text(item.jalur),
                        $('<td>').text(item.gel),
                        $('<td>').text(item.smt),
                        $('<td>').text(item.tahun),
                        $('<td>').css('text-align', 'center').append(chkbox)
                    ).appendTo('#tbody_tb_peserta');
                });
            }else{
                $('<tr>').append(
                        $('<td>').text('Tidak ada peserta tersedia').attr('colspan', '9').css('text-align', 'center')
                    ).appendTo('#tbody_tb_peserta');
            }
            $('#peserta_hidden').val(JSON.stringify(mhs_matkul_existing));
            $('#span_jml_mhs').text(mhs_matkul_existing.length);
        }
    });
}

$(document).on('change','#chkbox_pilih_semua_peserta',function () {
    if($(this).is(':checked')){
        $('.chkbox_pilih_peserta:visible').prop('checked', true)
    }else{
        $('.chkbox_pilih_peserta:visible').prop('checked', false)
    }
    $('.chkbox_pilih_peserta:visible').trigger('change');
});

$(document).on('click','#btn_submit',function (e) {
    $('#formpeserta').submit();
});

$(document).on('click','#btn_reset_search',function () {
    $('#search_nama_pes').val('');
    $('#search_no_pes').val('');
    $('#search_prodi_pes').val('');
    $('#search_jalur_pes').val('');
    $('#search_gel_pes').val('');
    $('#search_smt_pes').val('');
    $('#search_tahun_pes').val('');
    $('#btn_submit_search').trigger('click');
});

$(document).on('click','#btn_submit_search',function () {
    let nama_pes = $('#search_nama_pes').val();
    let no_pes = $('#search_no_pes').val();
    let prodi_pes = $('#search_prodi_pes').val();
    let jalur_pes = $('#search_jalur_pes').val();
    let gel_pes = $('#search_gel_pes').val();
    let smt_pes = $('#search_smt_pes').val();
    let tahun_pes = $('#search_tahun_pes').val();

    let found = false ;
    $("#tr_search_not_found").remove();

    $("#tbody_tb_peserta tr").each(function(index) {
        let row = $(this);
        let td_nama_pes = row.find("td:nth-child(2)").text() ;
        let td_no_pes = row.find("td:nth-child(3)").text() ;
        let td_prodi_pes = row.find("td:nth-child(4)").text() ;
        let td_jalur_pes = row.find("td:nth-child(5)").text() ;
        let td_gel_pes = row.find("td:nth-child(6)").text() ;
        let td_smt_pes = row.find("td:nth-child(7)").text() ;
        let td_tahun_pes = row.find("td:nth-child(8)").text() ;

        if (td_nama_pes.includes(nama_pes.trim().toUpperCase())
            && td_no_pes.includes(no_pes.trim().toUpperCase())
            && td_prodi_pes.includes(prodi_pes.trim().toUpperCase())
            && td_jalur_pes.includes(jalur_pes.trim().toUpperCase())
            && td_gel_pes.includes(gel_pes.trim().toUpperCase())
            && td_smt_pes.includes(smt_pes.trim().toUpperCase())
            && td_tahun_pes.includes(tahun_pes.trim().toUpperCase())){
            row.show();
            found = true;
        }
        else
            row.hide();
    });

    if(!found){
        $('<tr id="tr_search_not_found">').append(
                        $('<td>').text('Tidak ada peserta tersedia').attr('colspan', '9').css('text-align', 'center')
                    ).appendTo('#tbody_tb_peserta');
    }
});

</script>
<!-- END PAGE LEVEL JS-->
@endpush

@section('content')
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
            	<h4 class="card-title"><?=$subjudul?></h4>
            	<a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
            </div>
            <div class="card-content">
                <div class="card-body">

<!---- --->
<div class="box">
    <div class="box-body">
        <div class="row">
{{--            <div class="col-md-4">--}}
{{--                <div class="alert bg-info">--}}
{{--                    <h4 style="color: #fff">Mata Kuliah <i class="fa fa-book pull-right"></i></h4>--}}
{{--                    <hr>--}}
{{--                    <p><?=$matkul->nama_matkul?></p>--}}
{{--                </div>--}}
{{--                <div class="alert bg-info">--}}
{{--                    <h4 style="color: #fff">Dosen <i class="fa fa-address-book-o pull-right"></i></h4>--}}
{{--                    <hr>--}}
{{--                    <p><?=$dosen->nama_dosen?></p>--}}
{{--                </div>--}}
{{--            </div>--}}


            <div class="col-md-12">
                @if(isset($msg_ok))
                    <div class="alert bg-info">Perhatian : {{ $msg_ok }}</div>
                @endif
                <?=form_open('matkul/peserta/' . $matkul->id_matkul, ['id' => 'formpeserta', 'name' => 'formpeserta'], array('method'=>'post'))?>
                <div class="form-group">
                    <label for="nama_matkul">Nama Materi Ujian</label>
                    <input value="{{ $matkul->nama_matkul }}" disabled="disabled" type="text" class="form-control" name="nama_matkul">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label>Program Studi</label>
                    <select name="prodi_id" id="prodi_id" class="form-control" style="width:100% !important" multiple="multiple">
                        @foreach($prodi as $p)
                            <option value="{{ $p->kodeps }}">{{ $p->prodi }}</option>
                        @endforeach
                    </select> <small class="help-block" style="color: #dc3545"><?=form_error('prodi_id')?></small>
                </div>
                <div class="form-group">
                    <label for="status_ujian">Peserta Ujian</label>  <small class="help-block text-danger"><b>***</b> Pilih peserta yg akan di-asign ke materi ujian dipilih</small>
                    <input type="hidden" name="peserta_hidden" class="form-control" id="peserta_hidden">
                    <div class="alert bg-success">Jumlah mhs yang di-asign : <span id="span_jml_mhs"><b>0</b></span> mhs</div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>No Peserta</th>
                                <th>Prodi</th>
                                <th>Jalur</th>
                                <th>Gel</th>
                                <th>Smt</th>
                                <th>Tahun</th>
                                <th style="text-align: center"><input type="checkbox" id="chkbox_pilih_semua_peserta"></th>
                            </tr>
                            <tr>
                                <th><button id="btn_reset_search" class="btn btn-danger" type="button"><i class="fa fa-refresh"></i></button></th>
                                <th><input class="form-control search_pes" id="search_nama_pes"></th>
                                <th><input class="form-control search_pes" id="search_no_pes"></th>
                                <th><input class="form-control search_pes" id="search_prodi_pes"></th>
                                <th><input class="form-control search_pes" id="search_jalur_pes"></th>
                                <th><input class="form-control search_pes" id="search_gel_pes"></th>
                                <th><input class="form-control search_pes" id="search_smt_pes"></th>
                                <th><input class="form-control search_pes" id="search_tahun_pes"></th>
                                <th style="text-align: center">
                                    <button id="btn_submit_search" class="btn btn-info" type="button"><i class="fa fa-search"></i></button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tbody_tb_peserta">
                            <tr>
                                <td colspan="9" style="text-align: center">Tidak ada peserta tersedia</td>
                            </tr>
                        </tbody>
                    </table>
                    <small class="help-block"></small>
                </div>
                <div class="form-group pull-right">
                    <a href="{{ site_url('matkul/index') }}" class="btn btn-flat btn-warning">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <button id="btn_submit" type="button" class="btn btn-flat btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
                </div>
                <?=form_close()?>
            </div>
        </div>
    </div>
</div>
<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>
@endsection
