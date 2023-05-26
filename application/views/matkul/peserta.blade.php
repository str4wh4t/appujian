@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/npm/node_modules/bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css') }}">
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
<script src="{{ asset('assets/npm/node_modules/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/npm/node_modules/bootstrap4-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>--}}
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

let prodi_avail = [];
let prodi_mhs_selected = [];

let filter_mhs = {
    kelompok_ujian: null,
    tgl_ujian: null,
    tahun: null,
};

function init_page_level(){
    ajaxcsrf();
    $('.select2').select2();
    $('#prodi_id').select2({placeholder : 'Pilih Prodi'});
    $('#prodi_id').prepend('<option value="ALL">Semua Prodi</option>');

    filter_mhs.kelompok_ujian    = $('#kelompok_ujian').val();
    filter_mhs.tgl_ujian    = $('#tgl_ujian').val() == '' ? 'null' : $('#tgl_ujian').val();
    filter_mhs.tahun    = $('#tahun_mhs').val();

    @foreach($prodi as $p)
        prodi_avail.push('{{ $p->kodeps }}');
    @endforeach

    @if(!empty($prodi_mhs_selected))
        @foreach($prodi_mhs_selected as $kodeps)
            prodi_mhs_selected.push('{{ $kodeps }}');
        @endforeach

        $('#prodi_id').val(prodi_mhs_selected).trigger('change');
        ajx_overlay(true);
        init_peserta_table_value().then(function(){
            ajx_overlay(false);
        });
    @endif

    $('.datepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        // Your Icons
        // as Bootstrap 4 is not using Glyphicons anymore
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-check',
            clear: 'fa fa-trash',
            close: 'fa fa-times'
        }
    });

}

// $('#matkul_id').on('select2:select', function (e) {
//     init_topik_table_value();
// });

$('#prodi_id').on('select2:select', function (e) {
    let data = e.params.data;
    if(data.id == 'ALL'){
        $(this).val(null).trigger('change');
        $(this).val('ALL').trigger('change');
    }else{
        let values = $(this).val();
        if (values) {
            let i = values.indexOf('ALL');
            if (i >= 0) {
                values.splice(i, 1);
                $(this).val(values).change();
            }
        }
    }
    ajx_overlay(true);
    init_peserta_table_value().then(function(){
        ajx_overlay(false);
    });
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
    ajx_overlay(true);
    init_peserta_table_value().then(function(){
        ajx_overlay(false);
    });

});

const init_peserta_table_value = () => {
    let selected_ids = $('#prodi_id').val();
    if($.inArray('ALL', selected_ids) !== -1){
        selected_ids = [];
        $.each(prodi_avail,function(i,v){
            selected_ids.push(v);
        });
    }
    return $.ajax({
        url: "{{ site_url('matkul/ajax/get_mhs_prodi') }}",
        data: { 'matkul_id' : '{{ $matkul->id_matkul }}', 
                'kodeps': JSON.stringify(selected_ids),
                'filter' : filter_mhs },
        type: 'POST',
        async: false,
        success: function (response) {
            $('#tbody_tb_peserta').html('');
            let mhs_matkul_existing = [];
            let mhs_matkul_has_ujian_existing = [];
            if(!$.isEmptyObject(response.mhs_matkul)) {
                $.each(response.mhs_matkul, function (i, item) {
                    // mhs_matkul_existing.push(item.id_mahasiswa);
                    mhs_matkul_existing.push(item.id_mahasiswa);
                });
            }
            if(!$.isEmptyObject(response.mhs_matkul_has_ujian)) {
                $.each(response.mhs_matkul_has_ujian, function (i, item) {
                    // mhs_matkul_existing.push(item.id_mahasiswa);
                    mhs_matkul_has_ujian_existing.push(item.id_mahasiswa);
                });
            }
            if(!$.isEmptyObject(response.mhs)) {
                $.each(response.mhs, function (i, item) {
                    let chkbox = $('<input>').attr('class', 'chkbox_pilih_peserta').attr('type', 'checkbox').attr('name', 'peserta[]').attr('value', item.id_mahasiswa);
                    if(mhs_matkul_existing.includes(item.id_mahasiswa))
                        chkbox.prop('checked', true);
                    $('<tr data-urut="'+ (i + 1) +'">').append(
                        // $('<td>').text(i + 1),
                        $('<td>').css('text-align', 'center').append(chkbox),
                        $('<td>').text(item.nama),
                        $('<td>').text(item.nim),
                        $('<td>').text(item.prodi),
                        $('<td>').text(item.jalur),
                        $('<td>').text(item.gel),
                        $('<td>').text(item.smt),
                        $('<td>').text(item.tahun),
                    ).appendTo('#tbody_tb_peserta');
                });
            }else{
                $('<tr>').append(
                        $('<td>').text('Tidak ada peserta tersedia').attr('colspan', '8').css('text-align', 'center')
                    ).appendTo('#tbody_tb_peserta');
            }
            $('#peserta_hidden').val(JSON.stringify(mhs_matkul_existing));
            $('#span_jml_mhs').text(response.jml_mhs_matkul_belum_asign_ujian);
            $('#span_jml_mhs_has_ujian').text(response.jml_mhs_matkul_sudah_asign_ujian);
        }
    });
};

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

$(document).on('change','#kelompok_ujian', function(){
    let kelompok_ujian = $(this).val();
    filter_mhs.kelompok_ujian = kelompok_ujian;
    ajx_overlay(true);
    init_peserta_table_value().then(function(){
        ajx_overlay(false);
    });
});

$(document).on('focusout','#tgl_ujian', function(){ /** DATEPICKER WORKS ON FOCUSOUT */
    let tgl_ujian = $(this).val() == '' ? 'null' : $(this).val();
    filter_mhs.tgl_ujian = tgl_ujian;
    ajx_overlay(true);
    init_peserta_table_value().then(function(){
        ajx_overlay(false);
    });
});

$(document).on('change','#tahun_mhs', function(){
    let tahun = $(this).val();
    filter_mhs.tahun = tahun;
    ajx_overlay(true);
    init_peserta_table_value().then(function(){
        ajx_overlay(false);
    });
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
                <?=form_open('matkul/peserta/' . $matkul->id_matkul, ['id' => 'formpeserta', 'name' => 'formpeserta'], ['method'=>'post'])?>
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
                <fieldset class="form-group" style="padding: 10px; border: 1px solid #ccc;">
                    <legend class="col-form-label col-sm-2" style="border: 1px solid #ccc; background-color: #fffcd4;">Cluster Peserta</legend>
                    <div class="form-group">
                        <label for="kelompok_ujian" class="control-label">Kelompok Ujian</label>
                        <select name="kelompok_ujian" id="kelompok_ujian" class="form-control select2"
                            style="width:100%!important">
                            @foreach (KELOMPOK_UJIAN_AVAIL as $key => $val)
                            <option value="{{ $key }}">{{ $key !== 'null' ? $key . ' : ' : ''  }}{{ $val }}</option>    
                            @endforeach
                        </select>
                        <small class="help-block" style="color: #dc3545"><?=form_error('kelompok_ujian')?></small>
                    </div>
                    <div class="form-group">
                        <label for="tgl_ujian" class="control-label">Tgl Ujian</label> <small class="help-block text-danger"><b>***</b> Diisi sesuai dengan tgl ujian peserta jika ada</small>
                        <input id="tgl_ujian" name="tgl_ujian" type="text" class="datepicker form-control" placeholder="Tanggal Ujian">
                        <small class="help-block" style="color: #dc3545"><?=form_error('tgl_ujian')?></small>
                    </div>
                    <div class="form-group">
                        <label for="tahun_mhs" class="control-label">Tahun</label>
                        <select name="tahun_mhs" id="tahun_mhs" class="form-control select2"
                            style="width:100%!important">
                            <option value="null">Semua Tahun</option>
                            @foreach ($tahun_mhs as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun == get_selected_tahun() ? "selected" : "" }}>{{ $tahun }}</option>    
                            @endforeach
                        </select>
                        <small class="help-block" style="color: #dc3545"><?=form_error('tahun_mhs')?></small>
                    </div>
                </fieldset>
                <div class="form-group">
                    <label for="status_ujian">Peserta Ujian</label>  <small class="help-block text-danger"><b>***</b> Pilih peserta yg akan di-asign ke materi ujian dipilih</small>
                    <input type="hidden" name="peserta_hidden" class="form-control" id="peserta_hidden">
                    <div class="alert" style="background-color: #ffb; border: 1px solid #f00; color: #333;">
                        Jumlah mhs yang di-asign : <b><span id="span_jml_mhs" class="text-danger">0</span></b> mhs ( Belum diasign ke ujian ) , 
                        <b><span id="span_jml_mhs_has_ujian" class="text-danger">0</span></b> mhs ( Sudah diasign ke ujian )
                    </div>
                    <div style="overflow-x: scroll">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center">
                                    <input type="checkbox" id="chkbox_pilih_semua_peserta">
                                </th>
                                <th>Nama</th>
                                <th>No Peserta</th>
                                <th>Prodi</th>
                                <th>Jalur</th>
                                <th>Gel</th>
                                <th>Smt</th>
                                <th>Tahun</th>

                            </tr>
                            <tr>
                                <th style="text-align: center">
                                    <button id="btn_reset_search" class="btn btn-danger btn-sm" type="button"><i class="fa fa-refresh"></i></button>
                                    <button id="btn_submit_search" class="btn btn-info btn-sm" type="button"><i class="fa fa-search"></i></button>
                                </th>
                                <th><input class="search_pes" style="width: 150px" id="search_nama_pes"></th>
                                <th><input class="search_pes" style="width: 100px" id="search_no_pes"></th>
                                <th><input class="search_pes" style="width: 150px" id="search_prodi_pes"></th>
                                <th><input class="search_pes" style="width: 50px" id="search_jalur_pes"></th>
                                <th><input class="search_pes" style="width: 25px" id="search_gel_pes"></th>
                                <th><input class="search_pes" style="width: 25px" id="search_smt_pes"></th>
                                <th><input class="search_pes" style="width: 50px" id="search_tahun_pes"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody_tb_peserta">
                            <tr>
                                <td colspan="8" style="text-align: center">Tidak ada peserta tersedia</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
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
