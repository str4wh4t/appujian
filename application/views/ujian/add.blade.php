@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL CSS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/icheck.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/custom.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/yarn/node_modules/bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css') }}">
<!-- END PAGE LEVEL CSS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>--}}
{{--<script src="//cdn.datatables.net/plug-ins/1.10.21/api/fnPagingInfo.js"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2-cascade.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/bootstrap4-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<!-- END PAGE VENDOR JS-->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

let topik_id_dipilih = [];
let topik_jumlah_soal = [];
let topik_jumlah_soal_asli = [];
let topik_jumlah_waktu = [];
let topik_urutan = [];
let topik_avail = [];
let topik_ids_from_selected_bundle  = [];
let topik_ids_from_selected_bundle_key  = [];
let bundle_id_list = [];

let filter = {
		gel: null,
		smt: null,
		tahun: null,
	};

let filter_mhs = {
    kelompok_ujian: null,
    // tgl_ujian: null,
    tahun: null,
    // mhs_matkul: null,
};

let filter_table = {
    prodi: [],
    jalur: [],
    gel: [],
    smt: [],
};

function init_page_level(){
    ajaxcsrf();
    $('.select2').select2();
    $('#matkul_id').select2();
    $('#topik_id').select2({placeholder : 'Pilih topik'});
    $('#bundle').select2({placeholder : 'Pilih bundle soal'});
    // $('#mhs_matkul').select2({placeholder : 'Pilih matkul terkait mhs'});

    $('#prodi').select2({placeholder : 'Pilih prodi'});
    $('#jalur').select2({placeholder : 'Pilih jalur'});
    $('#gel_mhs').select2({placeholder : 'Pilih gelombang'});
    $('#smt_mhs').select2({placeholder : 'Pilih semester'});

    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-red',
        radioClass: 'iradio_square-red',
    });

    filter.gel      = $('#gel').val();
    filter.smt      = $('#smt').val();
    filter.tahun    = $('#tahun').val();

    filter_mhs.kelompok_ujian    = $('#kelompok_ujian').val();
    filter_mhs.tahun    = $('#tahun_mhs').val();
    // filter_mhs.mhs_matkul    = 'null';

    filter_table.prodi    = $('#prodi').val();
    filter_table.jalur   = $('#jalur').val();
    filter_table.gel    = $('#gel_mhs').val();
    filter_table.smt    = $('#smt_mhs').val();

    $('#matkul_id').val("").trigger('change');

    $('#sumber_materi').iCheck('check');

    $(".switchBootstrap").bootstrapSwitch();

    $('.datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
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

    // ajx_overlay(true);
    // init_peserta_table_value(bundle_id_list).then(function(){
    //     ajx_overlay(false);
    // });

}

const init_cascade_select2 = () => {
    let options = {};
    let cascadLoading = new Select2Cascade($('#matkul_id'), $('#topik_id'), '{{ site_url('soal/ajax/get_topic_by_matkul/') }}?id=:parentId:&empty=1', options);
    cascadLoading.then( function(parent, child, items) {
        topik_id_dipilih = [];
        topik_jumlah_soal = [];
        topik_jumlah_soal_asli = [];
        topik_jumlah_waktu = [];
        topik_urutan = [];

        child.select2({placeholder : '- Pilih topik -'});
        if(!$.isEmptyObject(items)){
            topik_avail = items;
            child.prepend('<option value="ALL">Semua Topik</option>');
            // child.val('ALL');
            // child.trigger('change');
            // ajx_overlay(true);
        }
        init_topik_table_value().then(function(){
                // init_peserta_table_value(bundle_id_list).then(function(){
                    ajx_overlay(false);
                // });
            }); // TO RESET ALL DATA TABLE JML SOAL
    });
};

$('#matkul_id').on('select2:select', function (e) {
    // init_topik_table_value();
    ajx_overlay(true);
    // overlay(false) ==> is triggered on init_page_level()
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

$('#bundle').on('select2:select', function (e) {
    let bundle_ids = $(this).val();
    bundle_id_list = bundle_ids;
    ajx_overlay(true);
    init_topik_table_value(bundle_ids).then(function(){
        // get_matkul_from_selected_bundle(bundle_ids).then(function(){
            // init_peserta_table_value(bundle_ids).then(function(){
                ajx_overlay(false);
            // });
        // });
    });
});

$('#bundle').on('select2:unselect', function (e) {
    let data = e.params.data;

    delete topik_ids_from_selected_bundle_key[data.id] ;

    let topik_ids_from_selected_bundle_temp = []
    let bundle_ids = [];
    $.each(topik_ids_from_selected_bundle_key, function(i, j){
        bundle_ids.push(i);
        $.each(j, function(ii, jj){
            topik_ids_from_selected_bundle_temp.push(jj);
        });
    });

    bundle_id_list = bundle_ids;

    topik_ids_from_selected_bundle = topik_ids_from_selected_bundle_temp;

    ajx_overlay(true);
    init_topik_table_value(bundle_ids).then(function(){
        // get_matkul_from_selected_bundle(bundle_ids).then(function(){
            // init_peserta_table_value(bundle_id_list).then(function(){
                ajx_overlay(false);
            // });
        // });
    });
});

// const get_matkul_from_selected_bundle = (bundle_ids) => {
//     return $.ajax({
//         url: "{{ site_url('soal/ajax/get_matkul_from_selected_bundle') }}",
//         data: { 'bundle_ids' : JSON.stringify(bundle_ids) },
//         type: 'POST',
//         success: function (response) {
//             $('#mhs_matkul').empty();
//             if(!$.isEmptyObject(response.matkul_list)){
//                 $.each(response.matkul_list, function(i, matkul){
//                     var newOption = new Option(matkul.nama_matkul, matkul.id_matkul, true, true);
//                     $('#mhs_matkul').append(newOption);
//                 })
//                 $('#mhs_matkul').trigger('change');
//             }
//         }
//     });
// };

const get_jml_soal_per_topik = (selected_ids, bundle_ids) => {
    if(bundle_ids === undefined){
        bundle_ids = [];
    }
    return $.ajax({
        url: "{{ site_url('soal/ajax/get_jml_soal_per_topik') }}",
        data: { 'topik_ids' : JSON.stringify(selected_ids), 'bundle_ids' : JSON.stringify(bundle_ids), 'filter' : filter },
        type: 'POST',
        success: function (response) {
            data_jml_soal = response;
        }
    });
};

const get_topik_from_selected_bundle = (bundle_ids) => {
    return $.ajax({
        url: "{{ site_url('soal/ajax/get_topik_from_selected_bundle') }}",
        data: { 'bundle_ids' : JSON.stringify(bundle_ids) },
        type: 'POST',
        success: function (response) {
            topik_ids_from_selected_bundle = response.ids;
            topik_avail = response.topik;
            topik_ids_from_selected_bundle_key  = response.topik_id_ref_bundle;
        }
    });
};

async function init_topik_table_value(bundle_ids){
    let selected_ids = [] ;
    if(bundle_ids === undefined){
        selected_ids = $('#topik_id').val();
        if($.inArray('ALL', selected_ids) !== -1){
            selected_ids = [];
            $.each(topik_avail, function(i,v){
                selected_ids.push(i);
            });
        }
    }else{
        await get_topik_from_selected_bundle(bundle_ids);
        selected_ids = topik_ids_from_selected_bundle ;
    }
    data_jml_soal = [];
    if(selected_ids.length)
        await get_jml_soal_per_topik(selected_ids, bundle_ids);
    $('.tr-cloned-topik').remove();
    $('#jumlah_soal_total').text('0');
    $('input[name="jumlah_soal_total"]').val('0');
    $.each(selected_ids,function(i,topik_id){
        let nama = topik_avail[topik_id];

        // if($.inArray(v, topik_id_dipilih) !== -1){
        //     val = topik_jumlah_soal[v] ;
        // }

        let clone  = $('#tr-master-topik').clone()
                                        .attr('id','tr-cloned-topik-' + topik_id)
                                        .addClass('tr-cloned-topik');
        clone.find('label.label_topik').html(nama);

        // let bobot_soal_id = clone.data('bobot_soal_id');

        clone.find('.input_jml').each(function(j){
            let bobot_soal_id = $(this).data('bobot_soal_id');
            let val = topik_jumlah_soal[topik_id] && topik_jumlah_soal[topik_id][bobot_soal_id] ? topik_jumlah_soal[topik_id][bobot_soal_id] : 0;
            $(this).attr('name','jumlah_soal['+ topik_id +']['+ bobot_soal_id +']')
                        .addClass('input_jumlah_soal')
                        .val(val)
                        .data('topik_id',topik_id)
                        .removeAttr('disabled');
        });

        clone.find('.jml_soal').each(function(j){
            let bobot_soal_id = $(this).data('bobot_soal_id');
            if(data_jml_soal[topik_id] && data_jml_soal[topik_id][bobot_soal_id])
                $(this).text(data_jml_soal[topik_id][bobot_soal_id]);
            else
                $(this).closest('.row').remove();
        });

        clone.find('.input_waktu').each(function(j){
            let val = topik_jumlah_waktu[topik_id] ? topik_jumlah_waktu[topik_id] : 0;
            $(this).attr('name','waktu_topik['+ topik_id +']')
                        .addClass('input_waktu_topik')
                        .val(val)
                        .data('topik_id',topik_id);

            let is_sekuen_topik = $('#is_sekuen_topik').is(':checked');
            
            if(is_sekuen_topik)
                $(this).removeAttr('disabled');
            else
                $(this).attr('disabled', 'disabled');
        });

        clone.find('.input_urutan').each(function(j){
            let val = topik_urutan[topik_id] ? topik_urutan[topik_id] : 0;
            $(this).attr('name','urutan_topik['+ topik_id +']')
                        .addClass('input_urutan_topik')
                        .val(val)
                        .data('topik_id',topik_id)
                        .removeAttr('disabled');
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

$(document).on('keyup mouseup','.input_jumlah_soal',function () {
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

$(document).on('keyup','.input_waktu_topik',function () {
    let  topik_id = $(this).data('topik_id');
    topik_jumlah_waktu[topik_id] = topik_jumlah_waktu[topik_id] ? topik_jumlah_waktu[topik_id] : 0 ;
    topik_jumlah_waktu[topik_id] = $(this).val();
    sum_input_jumlah_waktu();
});

function sum_input_jumlah_waktu(){
    let jumlah_waktu_total = 0;
    $('.input_waktu_topik').each(function(i){
       let  jumlah_waktu = $(this).val() == '' ? 0 : $(this).val() ;
       jumlah_waktu_total = jumlah_waktu_total + parseInt(jumlah_waktu);
    });
    // $('#jumlah_waktu_total').text(jumlah_waktu_total);
    // $('input[name="jumlah_waktu_total"]').val(jumlah_waktu_total);
    $('input[name="waktu"]').val(jumlah_waktu_total);
}

const init_peserta_table_value = (bundle_ids) => { // SEKARANG bundle_ids TIDAK DIPAKAI 31/MAY/2021
    // let sumber_ujian = $('input[name="sumber_ujian"]:checked').val();
    // let id_matkul = $('#matkul_id').val();

    let tgl_ujian = $('#tgl_ujian').val() == '' ? 'null' : $('#tgl_ujian').val();

    return $.ajax({
        // url: "{{ site_url('matkul/ajax/get_peserta_ujian_matkul') }}",
        // data: { 'id' : id_matkul, 'bundle_ids' : JSON.stringify(bundle_ids), 'sumber_ujian' : sumber_ujian , 'filter' : filter_mhs },
        url: "{{ site_url('ujian/ajax/get_peserta') }}",
        data: { 'filter' : filter_mhs, 'filter_table' : filter_table, 'tgl_ujian' : tgl_ujian },
        type: 'POST',
        success: function (response) {
            init_peserta_table(response.mhs);
        }
    });

};

const init_peserta_table = (response_mhs) => {
    $('#tbody_tb_peserta').html('');
    if(!$.isEmptyObject(response_mhs)) {
        $.each(response_mhs, function (i, item) {
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
    $('#chkbox_pilih_semua_peserta').trigger('change');
    
    $('.search_pes').val('');

    $('#panel_submit_ujian').hide();
};

$(document).on('change','#chkbox_pilih_semua_peserta',function () {
    if($(this).is(':checked')){
        $('.chkbox_pilih_peserta:visible').prop('checked', true)
    }else{
        $('.chkbox_pilih_peserta:visible').prop('checked', false)
    }
    $('.chkbox_pilih_peserta:visible').trigger('change');
    $('#span_total_peserta').text($('.chkbox_pilih_peserta:checked').length);
});

$(document).on('change','.chkbox_pilih_peserta',function () {
    $(this).is(':checked') ? null : $('#chkbox_pilih_semua_peserta').prop('checked', false);

    let values = $('input[name="peserta[]"]:checked').map(function (idx, el) {
       return $(el).val();
    }).get();
    values = values.length ? JSON.stringify(values) : '';
    $('#peserta_hidden').val(values);
    $('#peserta_hidden').nextAll('.help-block').eq(0).text('');
    $('#span_total_peserta').text($('.chkbox_pilih_peserta:checked').length);
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

$(document).on('change','#kelompok_ujian', function(){
    let kelompok_ujian = $(this).val();
    filter_mhs.kelompok_ujian = kelompok_ujian;
    init_peserta_table(null);
});

$(document).on('dp.hide','#tgl_ujian', function(){
    let tgl_ujian = $(this).val() == '' ? 'null' : $(this).val();
    filter_mhs.tgl_ujian = tgl_ujian;
    init_peserta_table(null);
});

$(document).on('change','#tahun_mhs', function(){
    let tahun = $(this).val();
    filter_mhs.tahun = tahun;
    init_peserta_table(null);
});

$(document).on('change','#prodi', function(){
    let prodi = $(this).val();
    filter_table.prodi = prodi;
    init_peserta_table(null);
});

$(document).on('change','#jalur', function(){
    let jalur = $(this).val();
    filter_table.jalur = jalur;
    init_peserta_table(null);
});

$(document).on('change','#gel_mhs', function(){
    let gel = $(this).val();
    filter_table.gel = gel;
    init_peserta_table(null);
});

$(document).on('change','#smt_mhs', function(){
    let smt = $(this).val();
    filter_table.smt = smt;
    init_peserta_table(null);
});

// $(document).on('change','#mhs_matkul', function(){
//     let mhs_matkul = $(this).val();
//     filter_mhs.mhs_matkul = mhs_matkul;
//     ajx_overlay(true);
//     init_peserta_table_value(bundle_id_list).then(function(){
//         ajx_overlay(false);
//     });
// });

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

$('#is_sekuen_topik').on('switchChange.bootstrapSwitch', function(event, state) {
    if(event.target.checked){ // DETEKSI JIKA TRUE 
        $('.input_waktu_topik').removeAttr('disabled');
        $('input[name="waktu"]').attr('disabled', 'disabled');
        sum_input_jumlah_waktu();
    }else{
        $('.input_waktu_topik').attr('disabled', 'disabled');
        $('input[name="waktu"]').removeAttr('disabled');
        $('input[name="waktu"]').val('');
    }
});

$('#sumber_materi').on('ifChecked', function(event){
    $('#panel_materi').removeClass('d-none');
    $('#panel_bundle').addClass('d-none');
    // $('#div_group_mhs_matkul').addClass('d-none');
    $('#bundle').val(null).trigger('change');
    $('#bundle').select2('close');
    $('#tahun').val("{{ get_selected_tahun() }}").trigger('change');
    init_cascade_select2();
});

$('#sumber_bundle').on('ifChecked', function(event){
    $('#panel_materi').addClass('d-none');
    $('#panel_bundle').removeClass('d-none');
    // $('#div_group_mhs_matkul').removeClass('d-none');
    $('#matkul_id').select2('close');
    $('#matkul_id').val("").trigger('change');
    $('#topik_id').val(null).trigger('change');
    $('#tahun').val("null").trigger('change');
    ajx_overlay(true);
    init_topik_table_value().then(function(){
        ajx_overlay(false);
    });
});

$(document).on('click','#btn_refine_peserta', function(){
    ajx_overlay(true);
    init_peserta_table_value(bundle_id_list).then(function(){
        ajx_overlay(false);
        $('#panel_submit_ujian').show();
    });
});

</script>
<script src="{{ asset('assets/dist/js/app/ujian/add.js') }}"></script>
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_level_css')
<!-- START PAGE LEVEL JS-->
<style type="text/css">
.select2-selection--multiple .select2-search__field{
  width:100%!important;
}
</style>
<!-- END PAGE LEVEL CSS-->
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
                    <small class="help-block text-danger"><b>***</b> Ujian dari materi / dari bundle soal</small>
                    <div class="">
                        <input type="radio" class="icheck" value="materi" name="sumber_ujian" id="sumber_materi" /><label for="sumber_materi" style="margin: 0.6em">Materi</label>
                        <input type="radio" class="icheck" value="bundle" name="sumber_ujian" id="sumber_bundle" /><label for="sumber_bundle" style="margin: 0.6em">Bundle</label>
                    </div>
                    <small class="help-block"></small>
                </div>
                <div id="panel_materi" class="">
                    <div class="form-group">
                        <label>Materi Ujian</label>
                        <select name="matkul_id" id="matkul_id" class="form-control" style="width:100% !important">
                            <option value="" disabled selected>Pilih materi ujian</option>
                            @foreach($matkul as $d)
                                <option value="{{ $d->id_matkul }}">{{ $d->nama_matkul }}</option>
                            @endforeach
                        </select> <small class="help-block" style="color: #dc3545"></small>
                    </div>
                    <div class="form-group">
                        <label>Topik</label>
                        <select name="topik_id" id="topik_id" class="form-control" style="width:100% !important" multiple="multiple">
                            @forelse($topik as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_topik }}</option>
                            @empty
                            @endforelse
                        </select> <small class="help-block" style="color: #dc3545"></small>
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
                            <small class="help-block" style="color: #dc3545"></small>
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
                            <small class="help-block" style="color: #dc3545"></small>
                        </div>
                        <div class="form-group">
                            <label for="tahun" class="control-label">Tahun</label>
                            <select name="tahun" id="tahun" class="form-control select2"
                                style="width:100%!important">
                                <option value="null">Semua Tahun</option>
                                @foreach ($tahun_soal as $tahun)
                                <option value="{{ $tahun }}" {{ $tahun == get_selected_tahun() ? "selected" : "" }}>{{ $tahun }}</option>    
                                @endforeach
                            </select>
                            <small class="help-block" style="color: #dc3545"></small>
                        </div>
                    </fieldset>
                </div>

                <fieldset id="panel_bundle" class="form-group d-none" style="padding: 10px; border: 1px solid #ccc;">
                    <legend class="col-form-label col-sm-2" style="border: 1px solid #ccc; background-color: #d4ffd7;">Bundle Soal</legend>
                    <div class="form-group">
                        <select name="bundle[]" id="bundle" class="form-control"
                            style="width:100%!important" multiple="multiple">
                            @foreach ($bundle_avail as $bundle)
                            <option value="{{ $bundle->id }}" {{ in_array($bundle->id, $bundle_selected) ? "selected" : "" }}>{{ $bundle->nama_bundle }}</option>    
                            @endforeach
                        </select>
                        <small class="help-block" style="color: #dc3545"></small>
                    </div>
                </fieldset>

                <div class="form-group">
                    <label for="is_sekuen_topik">Is Sekuen Topik</label> <small class="help-block text-danger"><b>***</b> Mengerjakan soal scr sekuensial (pengerjaan topik bergantian)</small>
                    <div>
                        <input type="radio" class="switchBootstrap" id="is_sekuen_topik" name="is_sekuen_topik" data-on-text="Ya" data-off-text="Tidak" data-radio-all-off="true" data-on-color="success" data-off-color="danger" />
                    </div>
                    <small class="help-block"></small>
                </div>
                <div>
                    <label for="jumlah_soal">
                        <span>Jumlah Soal</span>
                        <small class="help-block text-danger"><b>***</b> Silahkan isikan jumlah soal sesuai topik</small>
                    </label>
                    <table class="table table-bordered" id="table-topik">
                        <tr class="head" style="background-color: #eee">
                            <td class="w-70">
                                <div class="row">
                                    <label for="" style="" class="col-md-2">Urutan</label>
                                    <label for="" style="" class="col-md-8">Nm Topik</label>
                                    <label for="" style="" class="col-md-2">Waktu Topik</label>
                                </div>
                            </td>
                            <td class="w-30">
                                <div class="row">
                                    <label for="" style="" class="col-md-8">Bobot Soal</label>
                                    <label for="" style="text-align: right" class="col-md-4">Jml Soal</label>
                                </div>
                            </td>
                        </tr>
                        <tr id="tr-master-topik" style="display: none">
                            <td>
                                <div class="row">
                                    <div class="form-group col-md-2">
                                        <input placeholder="Urutan" type="number" data-topik_id="DATA-TOPIK-ID" class="form-control input_urutan input-sm input_number" name="urutan_topik[ID-TOPIK]" style="" value="0" disabled="disabled">
                                        <small class="help-block"></small>
                                    </div>
                                    <label class="label_topik col-md-8" for="">NAMA-TOPIK</label>
                                    <div class="form-group col-md-2">
                                        <input placeholder="Waktu Topik" type="number" data-topik_id="DATA-TOPIK-ID" class="form-control input_waktu input-sm input_number" name="waktu_topik[ID-TOPIK]" style="" disabled="disabled">
                                        <small class="help-block"></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach($bobot_soal as $d)
                                <div class="row">
                                    <label for="" style="" class="col-md-8">
                                        {{ $d->bobot }}
                                        <small class="text-danger">
                                            <span data-bobot_soal_id="{{ $d->id }}" class="jml_soal"></span> soal
                                        </small>
                                    </label>
                                    <div class="form-group col-md-4">
                                        <input placeholder="Jml Soal" type="number" data-topik_id="DATA-TOPIK-ID" data-bobot_soal_id="{{ $d->id }}" class="form-control input_jml input-sm input_number" name="jumlah_soal[ID-TOPIK][ID-BOBOT-SOAL]" style="text-align: right" disabled="disabled">
                                        <small class="help-block"></small>
                                    </div>
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
                    <label for="tgl_selesai">Tanggal Selesai</label> <small class="help-block text-danger"><b>***</b> Kosongi jika tidak ada batas waktu ujian</small>
                    <input name="tgl_selesai" type="text" class="datetimepicker form-control" placeholder="Tanggal Selesai">
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="waktu">Waktu</label> <small class="help-block text-danger"><b>***</b></span> Waktu otomatis terisi jika ujian scr sekuensial topik</small>
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
                        <option value="urut">Urut Soal</option>
                        <option value="acak">Acak Soal</option>
                    </select>
                    <small class="help-block"></small>
                </div>
                <div class="form-group">
                    <label for="jenis_jawaban">Acak Jawaban</label>
                    <select name="jenis_jawaban" class="form-control select2">
                        <option value="" disabled selected>- Pilihan -</option>
                        <option value="urut">Urut Jawaban</option>
                        <option value="acak">Acak Jawaban</option>
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
                @if(APP_TYPE == 'ujian')
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
                        <small class="help-block" style="color: #dc3545"></small>
                    </div>
                    <div class="form-group">
                        <label for="tgl_ujian" class="control-label">Tgl Ujian</label> <small class="help-block text-danger"><b>***</b> Diisi sesuai dengan tgl ujian peserta jika ada</small>
                        <input id="tgl_ujian" name="tgl_ujian" type="text" class="datetimepicker form-control" placeholder="Tanggal Ujian">
                        <small class="help-block" style="color: #dc3545"></small>
                    </div>
                    <div class="form-group">
                        <label for="prodi" class="control-label">Prodi</label>
                        <select name="prodi[]" id="prodi" class="form-control select2"
                            style="width:100%!important" multiple="multiple">
                            {{-- <option value="null">Semua Prodi</option> --}}
                            @foreach ($prodi_list as $prodi)
                            <option value="{{ $prodi }}">{{ $prodi }}</option>    
                            @endforeach
                        </select>
                        <small class="help-block" style="color: #dc3545"></small>
                    </div>
                    <div class="form-group">
                        <label for="jalur" class="control-label">Jalur</label>
                        <select name="jalur[]" id="jalur" class="form-control select2"
                            style="width:100%!important" multiple="multiple">
                            {{-- <option value="null">Semua Jalur</option> --}}
                            @foreach ($jalur_list as $jalur)
                            <option value="{{ $jalur }}">{{ $jalur }}</option>    
                            @endforeach
                        </select>
                        <small class="help-block" style="color: #dc3545"></small>
                    </div>
                    <div class="form-group">
                        <label for="gel_mhs" class="control-label">Gelombang</label>
                        <select name="gel_mhs[]" id="gel_mhs" class="form-control select2"
                            style="width:100%!important" multiple="multiple">
                            {{-- <option value="null">Semua Gel</option> --}}
                            @foreach ($gel_list as $gel)
                            <option value="{{ $gel }}">{{ $gel }}</option>    
                            @endforeach
                        </select>
                        <small class="help-block" style="color: #dc3545"></small>
                    </div>
                    <div class="form-group">
                        <label for="smt_mhs" class="control-label">Semester</label>
                        <select name="smt_mhs[]" id="smt_mhs" class="form-control select2"
                            style="width:100%!important" multiple="multiple">
                            {{-- <option value="null">Semua Smt</option> --}}
                            @foreach ($smt_list as $smt)
                            <option value="{{ $smt }}">{{ $smt }}</option>    
                            @endforeach
                        </select>
                        <small class="help-block" style="color: #dc3545"></small>
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
                        <small class="help-block" style="color: #dc3545"></small>
                    </div>
                    {{-- <div class="form-group d-none" id="div_group_mhs_matkul" >
                        <label for="mhs_matkul" class="control-label">Matkul Terkait</label> <small class="help-block text-danger"><b>***</b> Filter mhs yg akan diasign dalam ujian, jika ujian berdasarkan bundle soal</small>
                        <select name="mhs_matkul[]" id="mhs_matkul" class="form-control"
                            style="width:100%!important" multiple="multiple">
                        </select>
                        <small class="help-block" style="color: #dc3545"></small>
                    </div> --}}
                    <br />
                    <div class="form-group text-center">
                        <button type="button" id="btn_refine_peserta" class="btn btn-outline-info"><i class="icon-info"></i> Refine Peserta</button>
                    </div>
                </fieldset>
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
                    <div class="alert border-danger text-center text-danger"><i class="icon-info"></i> Total peserta dipilih : <b><span id="span_total_peserta">0</span></b></div>
                </div>
                @else 
                <input type="hidden" name="kelompok_ujian" value="null">
                <input type="hidden" name="tahun_mhs" value="null">
                <hr />
                @endif
                <div class="form-group text-center" id="panel_submit_ujian" style="display: none">
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
