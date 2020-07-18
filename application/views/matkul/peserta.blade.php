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

function init_page_level(){
    ajaxcsrf();
    $('.select2').select2();
    $('#prodi_id').select2({placeholder : 'Pilih Prodi'});
    $('#prodi_id').prepend('<option value="ALL">Semua Prodi</option>');

    @foreach($prodi as $p)
        prodi_avail.push('{{ $p->kodeps }}');
    @endforeach

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
        url: "{{ site_url('matkul/ajax/get_peserta_matkul') }}",
        data: { 'matkul_id' : '{{ $matkul->id_matkul }}', 'kodeps': JSON.stringify(selected_ids) },
        type: 'POST',
        async: false,
        success: function (response) {
            $('#tbody_tb_peserta').html('');
            if(!$.isEmptyObject(response.mhs_matkul)) {
                $.each(response.mhs_matkul, function (i, item) {
                    let chkbox = $('<input>').attr('class', 'chkbox_pilih_peserta').attr('type', 'checkbox').attr('name', 'peserta[]').attr('value', item.id_mahasiswa);
                    $('<tr>').append(
                        $('<td>').text(i + 1),
                        $('<td>').text(item.nama),
                        $('<td>').text(item.nim),
                        $('<td>').text(item.prodi),
                        $('<td>').css('text-align', 'center').append(chkbox)
                    ).appendTo('#tbody_tb_peserta');
                });
            }else{
                $('<tr>').append(
                        $('<td>').text('Tidak ada peserta tersedia').attr('colspan', '5').css('text-align', 'center')
                    ).appendTo('#tbody_tb_peserta');
            }
            $('#chkbox_pilih_semua_peserta').prop('checked', false);
        }
    });
}

$(document).on('change','#chkbox_pilih_semua_peserta',function () {
    $(this).is(':checked') ? $('.chkbox_pilih_peserta').prop('checked', true) : $('.chkbox_pilih_peserta').prop('checked', false);
    $('.chkbox_pilih_peserta').trigger('change');
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
            <div class="col-md-8">
                <?=form_open('matkul/peserta', array('id'=>'formpeserta'), array('method'=>'post'))?>
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
                    <label for="status_ujian">Peserta Ujian</label>  <small class="help-block text-danger"><b>***</b> Pilih peserta yg akan dienroll ke ujian</small>
                    <input type="hidden" name="peserta_hidden" class="form-control" id="peserta_hidden">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>No Peserta</th>
                                <th>Prodi</th>
                                <th style="text-align: center"><input type="checkbox" id="chkbox_pilih_semua_peserta"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody_tb_peserta">
                            <tr>
                                <td colspan="5" style="text-align: center">Tidak ada peserta tersedia</td>
                            </tr>
                        </tbody>
                    </table>
                    <small class="help-block"></small>
                </div>
                <div class="form-group pull-right">
                    <a href="{{ site_url('matkul/index') }}" class="btn btn-flat btn-warning">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <button id="submit" type="submit" class="btn btn-flat btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
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
