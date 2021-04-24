@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/yarn/node_modules/enjoyhint.js/dist/enjoyhint.css') }}">
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_custom_css')
<style type="text/css">
    /* styling opsi */
    .funkyradio div {
        clear: both;
        overflow: hidden;
    }

    .funkyradio label {
        width: 100%;
        border-radius: 3px;
        border: 1px solid #D1D3D4;
        font-weight: normal;
        padding: 5px 0 5px 50px;
    }

    .funkyradio input[type="radio"]:empty,
    .funkyradio input[type="checkbox"]:empty {
        display: none;
    }

    .funkyradio input[type="radio"]:empty~label,
    .funkyradio input[type="checkbox"]:empty~label {
        position: relative;
        line-height: 2em;
        /*text-indent: 3.25em;*/
        margin-top: 5px;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .funkyradio input[type="radio"]:empty~label:before,
    .funkyradio input[type="checkbox"]:empty~label:before {
        position: absolute;
        display: block;
        top: 0;
        bottom: 0;
        left: 0;
        content: '';
        width: 2.5em;
        background: #D1D3D4;
        border-radius: 3px 0 0 3px;
    }

    .funkyradio input[type="radio"]:hover:not(:checked)~label,
    .funkyradio input[type="checkbox"]:hover:not(:checked)~label {
        color: #888;
    }

    .funkyradio input[type="radio"]:hover:not(:checked)~label:before,
    .funkyradio input[type="checkbox"]:hover:not(:checked)~label:before {
        /*content: '\2714';*/
        text-indent: .9em;
        color: #000;
        font-weight: bold;
        padding: 5px 0 0 0;
    }

    .funkyradio input[type="radio"]:checked~label,
    .funkyradio input[type="checkbox"]:checked~label {
        color: #fff;
        background: #86C186;
        font-weight: bold;
    }

    .funkyradio input[type="radio"]:checked~label:before,
    .funkyradio input[type="checkbox"]:checked~label:before {
        /*content: '\2714';*/
        text-indent: .9em;
        color: #333;
        background-color: #ccc;
        padding: 5px 0 0 0;
    }

    .funkyradio input[type="radio"]:focus~label:before,
    .funkyradio input[type="checkbox"]:focus~label:before {
        box-shadow: 0 0 0 3px #999;
    }

    .funkyradio-default input[type="radio"]:checked~label:before,
    .funkyradio-default input[type="checkbox"]:checked~label:before {
        color: #333;
        background-color: #ccc;
    }

    .funkyradio-primary input[type="radio"]:checked~label:before,
    .funkyradio-primary input[type="checkbox"]:checked~label:before {
        color: #fff;
        background-color: #337ab7;
    }

    .funkyradio-success input[type="radio"]:checked~label:before,
    .funkyradio-success input[type="checkbox"]:checked~label:before {
        color: #fff;
        background-color: #5cb85c;
    }

    .funkyradio-danger input[type="radio"]:checked~label:before,
    .funkyradio-danger input[type="checkbox"]:checked~label:before {
        color: #fff;
        background-color: #d9534f;
    }

    .funkyradio-warning input[type="radio"]:checked~label:before,
    .funkyradio-warning input[type="checkbox"]:checked~label:before {
        color: #fff;
        background-color: #f0ad4e;
    }

    .funkyradio-info input[type="radio"]:checked~label:before,
    .funkyradio-info input[type="checkbox"]:checked~label:before {
        color: #fff;
        background-color: #5bc0de;
    }

    .huruf_opsi {
        margin-left: -36px;
        /*margin-top: 9px;*/
        position: absolute;
    }

    .pertanyaan {
        line-height: 2rem;
    }

    html body {
        height: auto;
    }

    body {
        -webkit-user-select: none;
        -moz-user-select: -moz-none;
        -ms-user-select: none;
        user-select: none;

        /*overflow: hidden;*/
    }

    .card {
        border-radius: 0px
    }

    fieldset {
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
    }

    legend {
        font-size: 1rem;
        width: auto;
        padding-left: 5px;
        padding-right: 5px;
        margin-bottom: 0px;
    }

    .card.card-fullscreen {
        z-index: 9995 !important;
    }

    .enjoyhint {
        z-index: 9996 !important;
    }

    .swal2-container {
        z-index: 9997 !important;
    }

    .enjoyhint_close_btn {
        display: none;
    }
</style>
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/yarn/node_modules/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/moment/min/moment-with-locales.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/kinetic/kinetic.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/jquery.scrollto/jquery.scrollTo.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/enjoyhint.js/dist/enjoyhint.min.js') }}"></script>
<!-- END PAGE VENDOR JS -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
let id_ujian = '';
let key = '';

let topik = [];
let topik_nama = [];
topik[1] = '2';
topik[2] = '1';
topik_nama[1] = 'PENGETAHUAN UMUM';
topik_nama[2] = 'NUMERIK';

let curr_date;

let update_time = () => {

    moment.locale('id');
    curr_date = moment("{{ date('Y-m-d H:i:s') }}", "YYYY-MM-DD HH:mm:ss");

    let interval = 1000;

    let ujian_selesai = moment('{{ date('Y-m-d H:i:s', strtotime("+". JML_WAKTU_TUTORIAL ." min")) }}', "YYYY-MM-DD HH:mm:ss");
    let diffTime = ujian_selesai.unix() - curr_date.unix();

    let duration = moment.duration(diffTime*1000, 'milliseconds');
    let duration_text = '';

    let refreshIntervalId = setInterval(function(){

        curr_date.add(1, 'second');

        duration = moment.duration(duration - interval, 'milliseconds');
        if(duration.as('milliseconds') > 0){
            duration_text = Math.floor(duration.as('hours')) + ":" + duration.minutes() + ":" + duration.seconds() ;
            $('#btn_lanjut_ujian').removeClass('btn-danger').addClass('btn-success');
            if(duration.as('milliseconds') == 599000){
                // JIKA WAKTU KURANG 10 MENIT
                Swal.fire({
                    title: "Perhatian",
                    text: "Waktu ujian kurang dari 10 menit",
                    icon: "warning"
                });
            }
        }else{
            duration_text = "0:0:0";
            $('#btn_lanjut_ujian').removeClass('btn-success').addClass('btn-danger');
            selesai();
            clearInterval(refreshIntervalId);

        }

        $('#sisa_waktu').text(duration_text);

    },interval);

    /**
     * date from local computer
     */
    // date = moment(new Date());
    // datetime_el.html(date.format('dddd, MMMM Do YYYY, h:mm:ss a'));

};

// setInterval(update_time, 1000); // PER SECOND

$(document).on('click','#btn_lanjut_ujian',function(){
    if($(this).hasClass('btn-danger')){
        Swal.fire('Perhatian', 'Anda berada diluar jadwal ujian', 'error');
        return false;
    }else{
        location.href = '{!! site_url('ujian/list') !!}';
    }
});

let id_tes          = "";
let widget          = $(".step_pertanyaan");
let total_widget    = widget.length;

let ofs = 0;

let enjoyhint_instance = null;

let enjoyhint_script_steps = [
    {
        'next #tb_identitas_peserta': 'Selamat datang di ujian CAT<br>Sebelum memulai ujian pastikan identitas anda benar',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
    },
    {
        'next #sisa_waktu': 'Perhatikan sisa waktu ujian anda',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
    },
    {
        'next #btn_soal_1': 'Pilih soal yang ingin dikerjakan, contoh : kita pilih soal nomer 1',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
        // 'onBeforeStart': function () {
        //     $('#lembar_ujian').animate({
        //         scrollTop: $("#lembar_ujian").offset().top
        //     }, 100, 'swing', function(){
        //         setTimeout(function(){
        //             $('#nav_content').slideDown();
        //             $('#nav_opener').text('TUTUP');
        //             nav_is_open = true;
        //         }, 100);
        //     });
        // },
        // 'timeout': 100,
    },
    {
        'next #widget_1': 'Pertanyaan yang ditampilkan',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
        // 'onBeforeStart': function () {
        //     $('#btn_soal_1').trigger('click');
        //     $('#lembar_ujian').scrollTo('#widget_1');
        // },
    },
    {
        'click .funkyradio-success:visible:nth-child(3)': 'Terdiri 5 (lima) opsi jawaban (a, b, c, d, dan e), contoh : kita pilih jawaban "c", silahkan klik !',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
        // 'onBeforeStart': function () {
        //     $('#lembar_ujian').scrollTo('#pil_jawaban');
        //     setTimeout(function(){
        //        $(".funkyradio-success:visible:nth-child(3) > input").trigger('click');
        //     },1000);
        // },
    },
    {
        'next #next_prev_pertanyaan': 'Anda dapat men-skip atau kembali ke pertanyaan sebelumnya, dan juga dapat menandai "RAGU" pada pilihan jawaban anda',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
        // 'onBeforeStart': function () {
        //     $('#lembar_ujian').scrollTo('#next_prev_pertanyaan');
        // },
    },
    {
        'skip #btn_akhiri_ujian': 'Tombol ini untuk mengakhiri ujian apabila anda ingin menyelesaikan ujian lebih awal',
        'showNext': false,
        'skipButton' : {className: "white border-white bg-transparent width-200", text: "Sudahi petunjuk."},
        // 'onBeforeStart': function () {
        //     $('#lembar_ujian').scrollTo('#tb_identitas_peserta');
        // },
    }
];

function setup_hint(){
    enjoyhint_instance = new EnjoyHint({
        onSkip: function(){
            // $('#lembar_ujian').animate({
            //     scrollTop: $("#lembar_ujian").offset().top
            // }, 1000, 'swing', function(){
            //     setTimeout(function(){
            //         $('#nav_content').slideUp();
            //         $('#nav_opener').text('BUKA');
            //         nav_is_open = false;
            //     }, 100);
            // });
            $('#lembar_ujian').animate({
                scrollTop: $("#lembar_ujian").offset().top
            }, 1000, 'swing', function(){

            });
        }
    });
    enjoyhint_instance.set(enjoyhint_script_steps);
    enjoyhint_instance.run();
}

let nav_is_open = false ;

$(document).on('click','#nav_opener',function() {
    nav_is_open = nav_is_open ? false : true ;
    if(nav_is_open) {
        $('#nav_content').slideDown()
        $(this).text('TUTUP');
    }
    else {
        $('#nav_content').slideUp()
        $(this).text('BUKA');
    }
    return false;
});

window.onblur = function () {
    // Swal.fire({
    //     title: "Perhatian",
    //     text: "Anda diperingatkan tidak boleh membuka halaman lain selama ujian berlangsung",
    //     icon: "warning"
    // });
};

window.onfocus = function () {

};

function wrap_navigasi(){
    $.each(topik, function(i,v){
        if(v){
            $('.class_topik_id_' + i).wrapAll('<fieldset class="legend_topik" data-id="'+ i +'" />');
        }
    });

    $('.legend_topik').each(function(i,v){
        let id = $(this).data('id');
        if(v)
            $(this).prepend('<legend>'+ topik_nama[id] +'</legend>');
    });
}


/** [START] FUNGSI SELESAI UJIAN */
function selesai(ended_by = '') {

    ajx_overlay(true);

    setTimeout(function(){
        Swal.fire({
            title: "Perhatian",
            text: "Ujian telah selesai, anda akan keluar ujian dalam 3 detik",
            icon: "success",
            allowOutsideClick: false,
            allowEscapeKey: false,
        });
        ajx_overlay(false);
        setTimeout(function(){
            location.href = '{{ url('ujian/list') }}' ;
        }, 3000);
    }, 2000);

}

$(document).on('click','#btn_akhiri_ujian',function() {
    Swal.fire({
        title: "Akhiri Ujian",
        text: "Ujian yang sudah diakhiri tidak dapat diulangi.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#37bc9b",
        cancelButtonColor: "#f6bb42",
        confirmButtonText: "Akhiri"
    }).then(result => {
        if (result.value) {
            selesai();
        }
    });
});
/** [END] FUNGSI SELESAI UJIAN */

function init_page_level(){
    update_time();
    setup_hint();
    
    document.addEventListener('contextmenu', event => event.preventDefault());

    buka(1);
    simpan_view();

    $(".step, .back, .selesai").hide();
    $("#widget_1").show();
    $("#widget_jawaban_1").show();
}

function buka(id_widget) {
    $(".next").attr('rel', (id_widget + 1));
    $(".back").attr('rel', (id_widget - 1));
    $(".ragu_ragu").attr('rel', (id_widget));

    if($('input[type="radio"][rel="'+ id_widget +'"]:checked').length > 0){
        // $(".ragu_ragu").removeAttr('disabled');
        $('.ragu_ragu').show();
    }else{
        // $(".ragu_ragu").attr('disabled','disabled');
        $('.ragu_ragu').hide();
    }

    cek_status_ragu(id_widget);
    cek_terakhir(id_widget);

    let topik_id_buka = $('#topik_id_' + id_widget).val();
    $('#text_info_topik').text(topik_nama[topik_id_buka]);

    $("#soalke").html(id_widget);

    $(".step").hide();
    $("#widget_" + id_widget).show();
    $("#widget_jawaban_" + id_widget).show();

}

function simpan_view() {
    var f_asal = $("#ujian");
    var form = getFormData(f_asal);
    var jml_soal = form.jml_soal;
    jml_soal = parseInt(jml_soal);
    var hasil_jawaban = '<div >';
    
    for (var i = 1; i < jml_soal; i++) {
        var idx = 'opsi_' + i;
        var idx2 = 'rg_' + i;
        var idx3 = 'topik_id_' + i;

        var jawab = form[idx];
        var ragu = form[idx2];
        var topik_id = form[idx3];


        if (jawab != undefined) {
            if (ragu == "Y") {
                if (jawab == "-") {
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-outline-primary btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + "</button>";
                } else {
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-warning btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + "</button>";
                }
            } else {
                if (jawab == "-") {
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-outline-primary btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + "</button>";
                } else {
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-success btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + "</button>";
                }
            }
        } else {
            hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-outline-primary btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + "</button>";
        }

    }

    hasil_jawaban += '</div>';
    $("#tampil_jawaban").html('<div id="yes">' + hasil_jawaban + '</div>');

    wrap_navigasi();
}

function cek_terakhir(id_soal) {
    var jml_soal = $("#jml_soal").val();
    jml_soal = (parseInt(jml_soal) - 1);

    if (jml_soal === id_soal) {
        $('.next').hide();
        $(".back").show();
        // $(".selesai").show(); // DI-HIDDEN DIGANTI TOMBOL AKHIRI

    } else {
        if(1 == id_soal){
            $('.next').show();
            $(".back").hide();
        }else{
            $('.next').show();
            $(".back").show();
        }
        // $(".selesai").hide();
    }
}

function next() {
    var berikutnya = $(".next").attr('rel');
    berikutnya = parseInt(berikutnya);
    berikutnya = berikutnya > total_widget ? total_widget : berikutnya;

    $("#soalke").html(berikutnya);

    $(".next").attr('rel', (berikutnya + 1));
    $(".back").attr('rel', (berikutnya - 1));
    $(".ragu_ragu").attr('rel', (berikutnya));

    if($('input[type="radio"][rel="'+ berikutnya +'"]:checked').length > 0){
        // $(".ragu_ragu").removeAttr('disabled');
        $('.ragu_ragu').show();
    }else{
        // $(".ragu_ragu").attr('disabled','disabled');
        $('.ragu_ragu').hide();
    }

    cek_status_ragu(berikutnya);
    cek_terakhir(berikutnya);

    let topik_id_buka = $('#topik_id_' + berikutnya).val();
    $('#text_info_topik').text(topik_nama[topik_id_buka]);

    var sudah_akhir = berikutnya == total_widget ? 1 : 0;

    $(".step").hide();
    $("#widget_" + berikutnya).show();
    $("#widget_jawaban_" + berikutnya).show();

    if (sudah_akhir == 1) {
        $(".back").show();
        $(".next").hide();
    } else if (sudah_akhir == 0) {
        $(".next").show();
        $(".back").show();
    }

    simpan_view();
}

function back() {
    var back = $(".back").attr('rel');
    back = parseInt(back);
    back = back < 1 ? 1 : back;

    $("#soalke").html(back);

    $(".back").attr('rel', (back - 1));
    $(".next").attr('rel', (back + 1));
    $(".ragu_ragu").attr('rel', (back));

    if($('input[type="radio"][rel="'+ back +'"]:checked').length > 0){
        // $(".ragu_ragu").removeAttr('disabled');
        $('.ragu_ragu').show();
    }else{
        // $(".ragu_ragu").attr('disabled','disabled');
        $('.ragu_ragu').hide();
    }

    cek_status_ragu(back);
    cek_terakhir(back);

    let topik_id_buka = $('#topik_id_' + back).val();
    $('#text_info_topik').text(topik_nama[topik_id_buka]);

    $(".step").hide();
    $("#widget_" + back).show();
    $("#widget_jawaban_" + back).show();

    var sudah_awal = back == 1 ? 1 : 0;

    if (sudah_awal == 1) {
        $(".back").hide();
        $(".next").show();
    } else if (sudah_awal == 0) {
        $(".next").show();
        $(".back").show();
    }

    simpan_view();
}

function getFormData($form) {
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};
    $.map(unindexed_array, function (n, i) {
        indexed_array[n['name']] = n['value'];
    });
    return indexed_array;
}

function simpan_akhir() {
    Swal.fire({
        title: "Akhiri Ujian",
        text: "Ujian yang sudah diakhiri tidak dapat diulangi.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#37bc9b",
        cancelButtonColor: "#f6bb42",
        confirmButtonText: "Akhiri"
    }).then(result => {
        if (result.value) {
            selesai();
        }
    });
}

function tidak_jawab() {

    var id_step = $(".ragu_ragu").attr('rel');

    if(!$('input[name="opsi_'+ id_step +'"]').is(':checked')) {
        return false;
    }

    var status_ragu = $("#rg_" + id_step).val();
    let ragu = null ;

    if (status_ragu == "N") {
        $("#rg_" + id_step).val('Y');
        $("#btn_soal_" + id_step).removeClass('btn-success');
        $("#btn_soal_" + id_step).addClass('btn-warning');
        ragu = 'Y';

    } else {
        $("#rg_" + id_step).val('N');
        $("#btn_soal_" + id_step).removeClass('btn-warning');
        $("#btn_soal_" + id_step).addClass('btn-success');
        ragu = 'N';
    }

    cek_status_ragu(id_step);

    let sid = $('input[name="id_soal_'+ id_step +'"]').val();
    let answer = $('input[name="opsi_'+ id_step +'"]:checked').val();

    simpan_jawaban_satu(sid, answer, ragu);

}

function cek_status_ragu(id_soal) {
    var status_ragu = $("#rg_" + id_soal).val();

    if (status_ragu == "N") {
        $(".ragu_ragu > span").html('Ragu');
    } else {
        $(".ragu_ragu > span").html('Tidak Ragu');
    }
}

function simpan_jawaban_satu(sid, answer, ragu) {
    ajx_overlay(true);
    setTimeout(function(){
        ajx_overlay(false);
    }, 500);
}

$(document).on('click','input[type="radio"]',function(){
    if($(this).prop("checked", true)){
        simpan_view();
        let sid = $(this).data('sid');
        let answer = $(this).val();
        let id_step = $(this).attr('rel');
        let ragu = $("#rg_" + id_step).val() ;
        simpan_jawaban_satu(sid, answer, ragu);
        $('.ragu_ragu').show();
    }
});

</script>
<!-- END PAGE LEVEL JS-->
@endpush

@section('content')
<section id="lembar_ujian" style="background-color: #f3f3f3; overflow-x: hidden;" class="card card-fullscreen">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="padding: 1rem">
                    <h4 class="card-title" style="width: 500px; margin: 0 auto;text-align: center;">
                        <span id="sisa_waktu" style="font-size: 2rem">0:0:0</span>
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <!---- --->
    {!! form_open('', array('id'=>'ujian'), ['id'=> '', 'key' => '']) !!}
    <div class="row">
        <div class="col-md-3" id="panel_user">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <table class="table table-bordered" id="tb_identitas_peserta">
                                    <tr>
                                        <th colspan="2" style="text-align: center; white-space: normal;">
                                            <label style="display: block;">{{ $user->full_name }}</label>
                                            <label style="display: block; font-weight: normal;">( {{ $user->username }}
                                                )</label>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" style="text-align: center"><img
                                                src="{{ asset('assets/imgs/no_profile_120_150.jpg') }}"
                                                style="height: 150px; width: 120px;" /></th>
                                    </tr>
                                </table>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-content">
                                                <div class="card-content collapse show" id="nav_content" style="">
                                                    <div class="card-body" style="padding: 0px">
                                                        <div id="tampil_jawaban"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body" >
                                <button class="btn btn-danger btn-block" id="btn_akhiri_ujian" type="button"><i class="fa fa-stop"></i> Akhiri Ujian</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-12" id="q_n_a" style="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body" id="isi_pertanyaan">
                                        <span style="font-size: 20px" class="">Pertanyaan #<span id="soalke"></span></span>
                                        <span class="float-right text-danger" id="text_info_topik" style="font-size: 15px; font-weight: bold; padding-top: 5px; text-transform: uppercase">&nbsp;</span>
                                        <hr>
                                        <div class="step step_pertanyaan" id="widget_1">
                                            <div class="pertanyaan">
                                                <div class="media-pertanyaan"></div>
                                                <p>Yang bukan presiden Indonesia adalah<br></p>
                                            </div>
                                        </div>
                                        <div class="step step_pertanyaan" id="widget_2">
                                            <div class="pertanyaan">
                                                <div class="media-pertanyaan"></div>
                                                <p>Provinsi di pulau sumatera adalah<br></p>
                                            </div>
                                        </div>
                                        <div class="step step_pertanyaan" id="widget_3">
                                            <div class="pertanyaan">
                                                <div class="media-pertanyaan"></div>
                                                <p>20&nbsp;&nbsp; 30&nbsp;&nbsp; 25&nbsp;&nbsp; 35&nbsp;&nbsp; ...&nbsp;&nbsp; 40<br></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body" id="pil_jawaban">
                                        <input type="hidden" name="id_soal_1" value="120">
                                        <input type="hidden" name="rg_1" id="rg_1" value="N">
                                        <input type="hidden" name="topik_id_1" id="topik_id_1" value="1">
                                        <div class="step" id="widget_jawaban_1">
                                            <div class="funkyradio">
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_a_120" name="opsi_1" data-sid="120" value="A" rel="1">
                                                    <label for="opsi_a_120" class="label_pilihan">
                                                        <div class="huruf_opsi">a</div>
                                                        <div>
                                                            <p>Mega Wati<br></p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_b_120" name="opsi_1" data-sid="120" value="B" rel="1">
                                                    <label for="opsi_b_120" class="label_pilihan">
                                                        <div class="huruf_opsi">b</div>
                                                        <div>
                                                            <p>Joko Widodo<br></p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_c_120" name="opsi_1" data-sid="120" value="C" rel="1">
                                                    <label for="opsi_c_120" class="label_pilihan">
                                                        <div class="huruf_opsi">c</div>
                                                        <div>
                                                            <p>Ganjar Pranowo<br></p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_d_120" name="opsi_1" data-sid="120" value="D" rel="1">
                                                    <label for="opsi_d_120" class="label_pilihan">
                                                        <div class="huruf_opsi">d</div>
                                                        <div>
                                                            <p>Soeharto<br></p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_e_120" name="opsi_1" data-sid="120" value="E" rel="1">
                                                    <label for="opsi_e_120" class="label_pilihan">
                                                        <div class="huruf_opsi">e</div>
                                                        <div>
                                                            <p>Soekarno<br></p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                            </div>
                                        </div>
                                        <!-- -->
                                        <input type="hidden" name="id_soal_2" value="130">
                                        <input type="hidden" name="rg_2" id="rg_2" value="N">
                                        <input type="hidden" name="topik_id_2" id="topik_id_2" value="1">
                                        <div class="step" id="widget_jawaban_2">
                                            <div class="funkyradio">
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_a_130" name="opsi_2" data-sid="130" value="A" rel="2">
                                                    <label for="opsi_a_130" class="label_pilihan">
                                                        <div class="huruf_opsi">a</div>
                                                        <div>
                                                            <p>Jawa Barat<br></p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_b_130" name="opsi_2" data-sid="130" value="B" rel="2">
                                                    <label for="opsi_b_130" class="label_pilihan">
                                                        <div class="huruf_opsi">b</div>
                                                        <div>
                                                            <p>Maluku<br></p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_c_130" name="opsi_2" data-sid="130" value="C"
                                                        rel="2">
                                                    <label for="opsi_c_130" class="label_pilihan">
                                                        <div class="huruf_opsi">c</div>
                                                        <div>
                                                            <p>Kalimantan Utara<br></p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_d_130" name="opsi_2" data-sid="130" value="D"
                                                        rel="2">
                                                    <label for="opsi_d_130" class="label_pilihan">
                                                        <div class="huruf_opsi">d</div>
                                                        <div>
                                                            <p>Gorontalo<br></p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_e_130" name="opsi_2" data-sid="130" value="E"
                                                        rel="2">
                                                    <label for="opsi_e_130" class="label_pilihan">
                                                        <div class="huruf_opsi">e</div>
                                                        <div>
                                                            <p>Jambi<br>
                                                            </p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                            </div>
                                        </div>
                                        <!-- -->
                                        <input type="hidden" name="id_soal_3" value="142">
                                        <input type="hidden" name="rg_3" id="rg_3" value="N">
                                        <input type="hidden" name="topik_id_3" id="topik_id_3" value="2">
                                        <div class="step" id="widget_jawaban_3">
                                            <div class="funkyradio">
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_a_142" name="opsi_3" data-sid="142" value="A" rel="3">
                                                    <label for="opsi_a_142" class="label_pilihan">
                                                        <div class="huruf_opsi">a</div>
                                                        <div>
                                                            <p>15</p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_b_142" name="opsi_3" data-sid="142" value="B" rel="3">
                                                    <label for="opsi_b_142" class="label_pilihan">
                                                        <div class="huruf_opsi">b</div>
                                                        <div>
                                                            <p>30</p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_c_142" name="opsi_3" data-sid="142" value="C" rel="3">
                                                    <label for="opsi_c_142" class="label_pilihan">
                                                        <div class="huruf_opsi">c</div>
                                                        <div>
                                                            <p>45</p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_d_142" name="opsi_3" data-sid="142" value="D" rel="3">
                                                    <label for="opsi_d_142" class="label_pilihan">
                                                        <div class="huruf_opsi">d</div>
                                                        <div>
                                                            <p>5</p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                                <div class="funkyradio-success">
                                                    <input type="radio" id="opsi_e_142" name="opsi_3" data-sid="142" value="E" rel="3">
                                                    <label for="opsi_e_142" class="label_pilihan">
                                                        <div class="huruf_opsi">e</div>
                                                        <div>
                                                            <p>128</p>
                                                        </div>
                                                        <div class="w-25"></div>
                                                    </label></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card mb-0" style="background-color: rgb(255, 254, 212)">
                        <div class="card-content">
                            <div class="card-body text-center" style="padding: 0.5rem">
                                <div class="btn-group" role="group" aria-label="" id="next_prev_pertanyaan">
                                    <button type="button" class="action back btn btn-info" rel="0"
                                        onclick="return back();"><i class="fa fa-chevron-left"></i>
                                        Back</button>
                                    <button type="button" class="ragu_ragu btn btn-warning" rel="1"
                                        onclick="return tidak_jawab();" style="display: none"><i
                                            class="fa fa-pause"></i> <span
                                            class="span_ragu">Ragu-Ragu</span></button>
                                    <button type="button" class="action next btn btn-info" rel="2"
                                        onclick="return next();">Next <i
                                            class="fa fa-chevron-right"></i></button>
                                    <button type="button" class="selesai action submit btn btn-danger"
                                        onclick="return simpan_akhir();"><i class="fa fa-stop"></i>
                                        Selesai</button>
                                    <input type="hidden" name="jml_soal" id="jml_soal" value="<?= JML_SOAL_TUTORIAL + 1 ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
                {!! form_close() !!}
                <!---- --->
</section>
@endsection