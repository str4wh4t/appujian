@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/yarn/node_modules/bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css') }}">
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
<script src="{{ asset('assets/plugins/select2-cascade.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/bootstrap4-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

let topik_id_dipilih = [];
let topik_jumlah_soal = [];
let topik_jumlah_soal_asli = [];
let topik_avail = [];

let filter = {
		gel: null,
		smt: null,
		tahun: null,
	};

function init_page_level(){
    ajaxcsrf();
    $('.select2').select2();
    $('#matkul_id').select2();
    $('#topik_id').select2({placeholder : 'Pilih Topik'});

    filter.gel      = $('#gel').val();
    filter.smt      = $('#smt').val();
    filter.tahun    = $('#tahun').val();

    let options = {};
    cascadLoading = new Select2Cascade($('#matkul_id'), $('#topik_id'), '{{ site_url('soal/ajax/get_topic_by_matkul/') }}?id=:parentId:', options);
    cascadLoading.then( function(parent, child, items) {
        topik_id_dipilih = [];
        topik_jumlah_soal = [];
        topik_jumlah_soal_asli = [];

        topik_avail = items;
        child.select2({placeholder : 'Pilih Topik'});
        child.prepend('<option value="ALL">Semua Topik</option>');
        // child.val('ALL');
        // child.trigger('change');
        // ajx_overlay(true);
        init_topik_table_value().then(
            function(){
                init_peserta_table_value().then(function(){
                    ajx_overlay(false);
                });
            }
        ); // TO RESET ALL DATA TABLE JML SOAL
    });

    $(".switchBootstrap").bootstrapSwitch();
}

$('#matkul_id').on('select2:select', function (e) {
    // init_topik_table_value();
    ajx_overlay(true);
});

$('#topik_id').on('select2:select', function (e) {
    let data = e.params.data;
    if(data.id == 'ALL'){
        $(this).val(null).trigger('change');
        $(this).val('ALL').trigger('change');
        // init_topik_table_value();
    }else{
        let values = $(this).val();
        if (values) {
            let i = values.indexOf('ALL');
            if (i >= 0) {
                values.splice(i, 1);
                $(this).val(values).change();
            }
        }
        // init_topik_table_value();
    }
    ajx_overlay(true);
    init_topik_table_value().then(function(){
        ajx_overlay(false);
    });
});

const get_jml_soal_per_topik = (selected_ids) => {
    return $.ajax({
        url: "{{ site_url('soal/ajax/get_jml_soal_per_topik') }}",
        data: { 'topik_ids' : JSON.stringify(selected_ids), 'filter' : filter },
        type: 'POST',
        success: function (response) {
            data_jml_soal = response;
        }
    });
};

async function init_topik_table_value(){
    let selected_ids = $('#topik_id').val();
    if($.inArray('ALL', selected_ids) !== -1){
        selected_ids = [];
        $.each(topik_avail, function(i,v){
            selected_ids.push(i);
        });
    }
    data_jml_soal = [];
    await get_jml_soal_per_topik(selected_ids);
    $('.tr-cloned-topik').remove();
    $('#jumlah_soal_total').text('0');
    $('input[name="jumlah_soal_total"]').val('0');
    $.each(selected_ids,function(i,topik_id){
        let nama = topik_avail[topik_id];
        // if($.inArray(v, topik_id_dipilih) !== -1){
        //     val = topik_jumlah_soal[v] ;
        // }
        let clone  = $('#tr-master-topik').clone().attr('id','tr-cloned-topik-' + topik_id).addClass('tr-cloned-topik');
        clone.find('label.label_topik').text(nama);
        // let bobot_soal_id = clone.data('bobot_soal_id');
        clone.find('.input_jml').each(function(j){
            let bobot_soal_id = $(this).data('bobot_soal_id');
            let val = topik_jumlah_soal[topik_id] && topik_jumlah_soal[topik_id][bobot_soal_id] ? topik_jumlah_soal[topik_id][bobot_soal_id] : 0;
            $(this).attr('name','jumlah_soal['+ topik_id +']['+ bobot_soal_id +']').removeAttr('disabled').addClass('input_jumlah_soal').val(val).data('topik_id',topik_id);
        });

        clone.find('.jml_soal').each(function(j){
            let bobot_soal_id = $(this).data('bobot_soal_id');
            if(data_jml_soal[topik_id] && data_jml_soal[topik_id][bobot_soal_id])
                $(this).text(data_jml_soal[topik_id][bobot_soal_id]);
            else
                $(this).closest('.row').remove();
        });

        clone.insertAfter("#table-topik tr.head" );
        clone.show();
        sum_input_jumlah_soal();
    });
}

$('#topik_id').on('select2:unselect', function (e) {
    let data = e.params.data;
    if(data.id == 'ALL'){
        topik_jumlah_soal = [];
        $.each(topik_avail,function(i,v){
            // topik_jumlah_soal[i] = topik_jumlah_soal_asli[i];
        });
    }else{
        topik_jumlah_soal[data.id] = [];
        // topik_jumlah_soal[data.id] = topik_jumlah_soal_asli[data.id];
    }
    ajx_overlay(true);
    init_topik_table_value().then(function(){
        ajx_overlay(false);
    });
});

$(document).on('keyup','.input_jumlah_soal',function () {
    let  topik_id = $(this).data('topik_id');
    let  bobot_soal_id = $(this).data('bobot_soal_id');
    topik_jumlah_soal[topik_id] = topik_jumlah_soal[topik_id] ? topik_jumlah_soal[topik_id] : [] ;
    topik_jumlah_soal[topik_id][bobot_soal_id] = $(this).val();
    sum_input_jumlah_soal();
});

function sum_input_jumlah_soal(){
    let jumlah_soal_total = 0;
    $('.input_jumlah_soal').each(function(i){
       let  jumlah_soal = $(this).val() == '' ? 0 : $(this).val() ;
       jumlah_soal_total = jumlah_soal_total + parseInt(jumlah_soal);
    });
    $('#jumlah_soal_total').text(jumlah_soal_total);
    $('input[name="jumlah_soal_total"]').val(jumlah_soal_total);
}

const init_peserta_table_value = () => {
    return $.ajax({
        url: "{{ site_url('matkul/ajax/get_peserta_ujian_matkul') }}",
        data: { 'id' : $('#matkul_id').val() },
        type: 'POST',
        success: function (response) {
            $('#tbody_tb_peserta').html('');
            if(!$.isEmptyObject(response.mhs_matkul)) {
                $.each(response.mhs_matkul, function (i, item) {
                    let chkbox = $('<input>').attr('class', 'chkbox_pilih_peserta').attr('type', 'checkbox').attr('name', 'peserta[]').attr('value', item.id_mahasiswa);
                    $('<tr>').append(
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
            $('#chkbox_pilih_semua_peserta').prop('checked', false);
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

$(document).on('change','.chkbox_pilih_peserta',function () {
    $(this).is(':checked') ? null : $('#chkbox_pilih_semua_peserta').prop('checked', false);

    let values = $('input[name="peserta[]"]:checked').map(function (idx, el) {
       return $(el).val();
    }).get();
    values = values.length ? JSON.stringify(values) : '';
    $('#peserta_hidden').val(values);
    $('#peserta_hidden').nextAll('.help-block').eq(0).text('');
});

$(document).on('click','#submit',function (e) {
    $('#formujian').submit();
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
                        $('<td>').text('Tidak ada peserta tersedia').attr('colspan', '8').css('text-align', 'center')
                    ).appendTo('#tbody_tb_peserta');
    }
});

$(document).on('change','#gel', function(){
    let gel = $(this).val();
    filter.gel = gel;
    ajx_overlay(true);
    init_topik_table_value().then(function(){
        ajx_overlay(false);
    });
});

$(document).on('change','#smt', function(){
    let smt = $(this).val();
    filter.smt = smt;
    ajx_overlay(true);
    init_topik_table_value().then(function(){
        ajx_overlay(false);
    });
});

$(document).on('change','#tahun', function(){
    let tahun = $(this).val();
    filter.tahun = tahun;
    ajx_overlay(true);
    init_topik_table_value().then(function(){
        ajx_overlay(false);
    });
});

$('#tampilkan_hasil').on('switchChange.bootstrapSwitch', function(event, state) {
    if(!event.target.checked){ // DETEKSI JIKA FALSE MAKA JUGA MENON-AKTIFKAN TAMPILKAN JAWABAN
        $('#tampilkan_jawaban').bootstrapSwitch('state', false, false);
    }
});

$('#tampilkan_jawaban').on('switchChange.bootstrapSwitch', function(event, state) {
    if(event.target.checked){ // DETEKSI JIKA TRUE MAKA JUGA MENGAKTIFKAN TAMPILKAN HASIL
        $('#tampilkan_hasil').bootstrapSwitch('state', true, true);
    }
});

</script>
<script src="{{ asset('assets/dist/js/app/ujian/add.js') }}"></script>
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
                <?=form_open('ujian/save', array('id'=>'formujian'), array('method'=>'add'))?>
                <div class="form-group">
                    <label for="nama_ujian">Nama Ujian</label>
                    <input placeholder="Nama Ujian" type="text" class="form-control" name="nama_ujian">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label>Materi Ujian</label>
                    <select name="matkul_id" id="matkul_id" class="form-control" style="width:100% !important">
                        <option value="" disabled selected>- Pilih Materi Ujian -</option>
                        @foreach($matkul as $d)
                            <option {{ $matkul_dipilih == $d->id_matkul ? 'selected="selected"' : '' }} value="{{ $d->id_matkul }}">{{ $d->nama_matkul }}</option>
                        @endforeach
                    </select> <small class="help-block" style="color: #dc3545"><?=form_error('matkul_id')?></small>
                </div>
                <div class="form-group">
                    <label>Topik</label>
                    <select name="topik_id" id="topik_id" class="form-control" style="width:100% !important" multiple="multiple">
                        @forelse($topik as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_topik }}</option>
                        @empty
                        @endforelse
                    </select> <small class="help-block" style="color: #dc3545"><?=form_error('topik_id')?></small>
                </div>
                <fieldset class="form-group" style="padding: 10px; border: 1px solid #ccc;">
                    <legend class="col-form-label col-sm-2" style="border: 1px solid #ccc; background-color: #d4fdff;">Cluster Soal</legend>
                    <div class="form-group">
                        <label for="gel" class="control-label">Gel</label>
                        <select name="gel" id="gel" class="form-control select2"
                            style="width:100%!important">
                            <option value="null">Semua Gel</option>
                            @foreach (GEL_AVAIL as $gel)
                            <option value="{{ $gel }}">GEL-{{ $gel }}</option>    
                            @endforeach
                        </select>
                        <small class="help-block" style="color: #dc3545"><?=form_error('gel')?></small>
                    </div>
                    <div class="form-group">
                        <label for="smt" class="control-label">Smt</label>
                        <select name="smt" id="smt" class="form-control select2"
                            style="width:100%!important">
                            <option value="null">Semua Smt</option>
                            @foreach (SMT_AVAIL as $smt)
                            <option value="{{ $smt }}">SMT-{{ $smt }}</option>    
                            @endforeach
                        </select>
                        <small class="help-block" style="color: #dc3545"><?=form_error('smt')?></small>
                    </div>
                    <div class="form-group">
                        <label for="tahun" class="control-label">Tahun</label>
                        <select name="tahun" id="tahun" class="form-control select2"
                            style="width:100%!important">
                            <option value="null">Semua Tahun</option>
                            @foreach (TAHUN_AVAIL as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun == get_selected_tahun() ? "selected" : "" }}>{{ $tahun }}</option>    
                            @endforeach
                        </select>
                        <small class="help-block" style="color: #dc3545"><?=form_error('tahun')?></small>
                    </div>
                </fieldset>
                <div>
                    <label for="jumlah_soal">
                        <span>Jumlah Soal</span>
                        <small class="help-block text-info"><span class="text-danger"><b>***</b></span> Silahkan isikan jumlah soal sesuai topik</small>
                    </label>
                    <table class="table table-bordered" id="table-topik">
                        <tr class="head" style="background-color: #eee">
                            <td>Nm Topik</td>
                            <td>
                                <div class="row">
                                    <label for="" style="" class="col-md-8">Bobot Soal</label>
                                    <label for="" style="text-align: right" class="col-md-4">Jml Soal</label>
                            </td>
                        </tr>
                        <tr id="tr-master-topik" style="display: none">
                            <td class="w-50">
                                <label class="label_topik" for="" style="margin-top: 7px;">NAMA-TOPIK</label>
                            </td>
                            <td>
                                @foreach($bobot_soal as $d)
                                <div class="form-group row">
                                    <label for="" style="" class="col-md-8">{{ $d->bobot }} <small class="text-danger"><span data-bobot_soal_id="{{ $d->id }}" class="jml_soal"></span> soal</small></label>
                                    <input placeholder="Jml Soal" type="number" data-topik_id="DATA-TOPIK-ID" data-bobot_soal_id="{{ $d->id }}" class="form-control input_jml input-sm input_number col-md-4" name="jumlah_soal[ID-TOPIK][ID-BOBOT-SOAL]" style="text-align: right" disabled="disabled">
                                    <small class="help-block"></small>
                                </div>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="" style="margin-top: 7px;">Total Soal</label>
                            </td>
                            <td>
                                <div class="form-group" style="text-align: right">
                                    <b><span id="jumlah_soal_total" class="text-success" style="">0</span></b>
                                    <input class="form-control input_number" type="hidden" name="jumlah_soal_total">
                                    <small class="help-block"></small>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="form-group">
                    <label for="tgl_mulai">Tanggal Mulai</label>
                    <input name="tgl_mulai" type="text" class="datetimepicker form-control" placeholder="Tanggal Mulai">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tgl_selesai">Tanggal Selesai</label>
                    <input name="tgl_selesai" type="text" class="datetimepicker form-control" placeholder="Tanggal Selesai">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="waktu">Waktu</label>
                    <input placeholder="Menit" type="number" class="form-control" name="waktu">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="waktu">Masa Berlaku Sertifikat</label> <small class="help-block text-danger"><b>***</b> Dalam satuan tahun, jika tidak perlu sertifikat silahkan beri angka 0</small>
                    <input placeholder="Masa berlaku sertifikat" type="number" value="0" class="form-control" name="masa_berlaku_sert">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="pakai_token">Pakai Token</label>
                    <div>
                        <input type="radio" class="switchBootstrap" id="pakai_token" name="pakai_token" data-on-text="Pakai" data-off-text="Tidak" data-radio-all-off="true" data-on-color="success" data-off-color="danger" checked="checked" />
                    </div>
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tampilkan_hasil">Tampilkah Hasil</label> <small class="help-block text-danger"><b>***</b> Tampilkan hasil ujian ke peserta</small>
                    <div>
                        <input type="radio" class="switchBootstrap" id="tampilkan_hasil" name="tampilkan_hasil" data-on-text="Tampilkan" data-off-text="Tidak" data-radio-all-off="true" data-on-color="success" data-off-color="danger" />
                    </div>
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="tampilkan_jawaban">Tampilkah Jawaban</label> <small class="help-block text-danger"><b>***</b> Tampilkan jawaban ujian ke peserta (jika ditampilkan otomatis juga menampilkan hasil)</small>
                    <div>
                        <input type="radio" class="switchBootstrap" id="tampilkan_jawaban" name="tampilkan_jawaban" data-on-text="Tampilkan" data-off-text="Tidak" data-radio-all-off="true" data-on-color="success" data-off-color="danger" />
                    </div>
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="jenis">Acak Soal</label>
                    <select name="jenis" class="form-control select2">
                        <option value="" disabled selected>- Pilihan -</option>
                        <option value="acak">Acak Soal</option>
                        <option value="urut">Urut Soal</option>
                    </select>
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="jenis_jawaban">Acak Jawaban</label>
                    <select name="jenis_jawaban" class="form-control select2">
                        <option value="" disabled selected>- Pilihan -</option>
                        <option value="acak">Acak Jawaban</option>
                        <option value="urut">Urut Jawaban</option>
                    </select>
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="repeatable">Repeatable</label> <small class="help-block text-danger"><b>***</b> Apakah ujian dapat diulang</small>
                    <div>
                        <input type="radio" class="switchBootstrap" id="repeatable" name="repeatable" data-on-text="Ya" data-off-text="Tidak" data-radio-all-off="true" data-on-color="success" data-off-color="danger" />
                    </div>
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="status_ujian">Peserta Ujian</label>  <small class="help-block text-danger"><b>***</b> Pilih peserta yg akan dienroll ke ujian</small>
                    <input type="hidden" name="peserta_hidden" class="form-control" id="peserta_hidden">
                    <div style="overflow-x: scroll">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="text-align: center"><input type="checkbox" id="chkbox_pilih_semua_peserta"></th>
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
                    <a href="{{ site_url('ujian/master') }}" class="btn btn-flat btn-warning">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <button id="submit" type="button" class="btn btn-flat btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
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
