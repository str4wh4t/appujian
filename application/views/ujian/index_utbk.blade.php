@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
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

.funkyradio input[type="radio"]:empty ~ label,
.funkyradio input[type="checkbox"]:empty ~ label {
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

.funkyradio input[type="radio"]:empty ~ label:before,
.funkyradio input[type="checkbox"]:empty ~ label:before {
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

.funkyradio input[type="radio"]:hover:not(:checked) ~ label,
.funkyradio input[type="checkbox"]:hover:not(:checked) ~ label {
    color: #888;
}

.funkyradio input[type="radio"]:hover:not(:checked) ~ label:before,
.funkyradio input[type="checkbox"]:hover:not(:checked) ~ label:before {
/*content: '\2714';*/
    text-indent: .9em;
    color: #000;
    font-weight: bold;
    padding: 5px 0 0 0;
}

.funkyradio input[type="radio"]:checked ~ label,
.funkyradio input[type="checkbox"]:checked ~ label {
    /* color: #fff; */
    /* background: #86C186; */
    /* font-weight: bold; */
    border-color: #86C186;
}

.funkyradio input[type="radio"]:checked ~ label:before,
.funkyradio input[type="checkbox"]:checked ~ label:before {
/*content: '\2714';*/
    text-indent: .9em;
    color: #333;
    background-color: #ccc;
    padding: 5px 0 0 0;
}

.funkyradio input[type="radio"]:focus ~ label:before,
.funkyradio input[type="checkbox"]:focus ~ label:before {
    box-shadow: 0 0 0 3px #999;
}

.funkyradio-default input[type="radio"]:checked ~ label:before,
.funkyradio-default input[type="checkbox"]:checked ~ label:before {
    color: #333;
    background-color: #ccc;
}

.funkyradio-primary input[type="radio"]:checked ~ label:before,
.funkyradio-primary input[type="checkbox"]:checked ~ label:before {
    color: #fff;
    background-color: #337ab7;
}

.funkyradio-success input[type="radio"]:checked ~ label:before,
.funkyradio-success input[type="checkbox"]:checked ~ label:before {
    color: #fff;
    background-color: #5cb85c;
}

.funkyradio-danger input[type="radio"]:checked ~ label:before,
.funkyradio-danger input[type="checkbox"]:checked ~ label:before {
    color: #fff;
    background-color: #d9534f;
}

.funkyradio-warning input[type="radio"]:checked ~ label:before,
.funkyradio-warning input[type="checkbox"]:checked ~ label:before {
    color: #fff;
    background-color: #f0ad4e;
}

.funkyradio-info input[type="radio"]:checked ~ label:before,
.funkyradio-info input[type="checkbox"]:checked ~ label:before {
    color: #fff;
    background-color: #5bc0de;
}
.huruf_opsi {
    margin-left: -36px;
    /*margin-top: 9px;*/
    position: absolute;
}
.funkyradio-success .huruf_opsi{
    color: #fff;
}
.pertanyaan{
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

.card{
    border-radius: 0px
}

fieldset{
    border:1px solid #ccc;
    padding: 10px;
    margin-bottom: 10px;
}

legend{
    font-size: 1rem;
    width: auto;
    padding-left: 5px;
    padding-right: 5px;
    margin-bottom: 0px;
}
.card.card-fullscreen{
    z-index: 9995 !important;
}
.swal2-container{
    z-index: 9997 !important;
}

#ul_topik_ujian li {
    text-transform: uppercase;
}

#q_n_a{
    /* max-height: 700px; OVERIDED LATER */
    overflow-y: scroll;
}

#panel_user{
    /* max-height: 750px; OVERIDED LATER */
    overflow-y: scroll;
}

#lembar_ujian{
    background-color: #fff; 
    overflow-x: hidden;
}

#ujian_card_header{
    border-bottom: 2px solid blue;
}

#div_navigasi{
    border-top: 1px solid grey;
}

#fixed_panel{
    width: 250px;
    height: 63px;
    position: fixed;
    top: 0;
    background-color: #ffc;
    border-left: 1px solid #F00;
    border-bottom: 1px solid #f00;
    border-radius: 0 0 0 10px;
    z-index: 9996 !important;
    right: 0;
}

#sisa_waktu_2{
    margin: 10px auto;
    width: 100px;
    text-align: center;
    font-size: 2rem;
}

#watermark {
    position: fixed;
    right: 25%;
    top: 40%;
    opacity: 0.25;
    z-index: 9997 !important;
    font-size: 75px;
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}

</style>
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
{{-- <script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>--}}
{{-- <script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{-- <script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{-- <script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{-- <script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<script src="{{ asset('assets/yarn/node_modules/moment/min/moment.min.js') }}"></script>
{{-- <script src="https://momentjs.com/downloads/moment-with-locales.js"></script>--}}
<script src="{{ asset('assets/yarn/node_modules/moment/min/moment-with-locales.min.js') }}"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/screenfull.js/5.0.2/screenfull.min.js"></script>--}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/kineticjs/5.2.0/kinetic.js"></script>--}}
{{-- <script src="{{ asset('assets/yarn/node_modules/kinetic/kinetic.min.js') }}"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.2/jquery.scrollTo.min.js"></script>--}}
{{-- <script src="{{ asset('assets/yarn/node_modules/jquery.scrollto/jquery.scrollTo.min.js') }}"></script> --}}
<!-- END PAGE VENDOR JS -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

let id_ujian = '{{  uuid_create_from_integer($h_ujian->id) }}';
let key = '{{ $one_time_token }}';

let topik = [];
let topik_nama = [];
let topik_waktu = [];
let urutan_topik = [];

@php
@endphp

@foreach($h_ujian->soal as $s)
    @php
        $topik_ujian_jml[$s->topik_id] = !isset($topik_ujian_jml[$s->topik_id]) ? 1 : $topik_ujian_jml[$s->topik_id] + 1;
        $topik_ujian_nama[$s->topik_id] = $s->topik->nama_topik;
    @endphp
@endforeach

@foreach($topik_ujian_jml as $topik_id => $jml_topik)
    topik[{{ $topik_id }}] = '{{ $jml_topik }}';
@endforeach

@foreach($topik_ujian_nama as $topik_id => $nama_topik)
    topik_nama[{{ $topik_id }}] = '{{ $nama_topik }}';
@endforeach

/** UNTUK NGESET PADA AWAL START UJIAN */
@if($h_ujian->m_ujian->is_sekuen_topik)
    @if(!empty($urutan_topik))
        @php
        $date_akhir_topik = '';
        @endphp
        @foreach($urutan_topik as $topik_id => $v)
            @php 
                if(empty($date_akhir_topik))
                    $date_akhir_topik = date('Y-m-d H:i:s', strtotime($h_ujian->tgl_mulai . '+' . $v['waktu'] . ' minutes')) ;
                else
                    $date_akhir_topik = date('Y-m-d H:i:s', strtotime($date_akhir_topik . '+' . $v['waktu'] . ' minutes')) ;
            @endphp
            // topik_waktu[{{ $topik_id }}] = '{{ $date_akhir_topik }}'; // TELAH DISET SECARA AJAX, LIHAT FUNGSI  get_urutan_topik
            urutan_topik.push({{ $topik_id }}) ;
        @endforeach
    @endif
@endif

let is_sekuen_topik = {{ $h_ujian->m_ujian->is_sekuen_topik == 1 ? 'true' : 'false' }};

let curr_date;
let topik_aktif;
let last_topik_id = 0;
let waktu_selesai = '{{ date('Y-m-d H:i:s', strtotime($h_ujian->tgl_selesai)) }}';

let refreshIntervalId ;

const set_urutan_topik = () => {
    // let topik_waktu = [];
    // let urutan_topik = [];

    return $.ajax({
        url: "{{ site_url('ujian/ajax/get_urutan_topik') }}",
        data: { 'id' : {{ $h_ujian->id }} },
        type: 'POST',
        success: function (res) {
            topik_waktu = res.topik_waktu;
            waktu_selesai = res.fixed_waktu;
            // urutan_topik = res.urutan_topik ; // TIDAK BERUBAH
        }
    });

}

const update_time = () => {

    moment.locale('id');

    /**
     * date from server
     */
    $.ajaxSetup({
        url: '<?= site_url('get_server_time') ?>',
        global: false,
        type: "GET"
    });

    return $.ajax({
        success: function (date_ajax) {

            curr_date = moment(date_ajax, "YYYY-MM-DD HH:mm:ss");

            // console.log('srv_date', curr_date);
            
            $.each(urutan_topik, function(i, v){
                let akhir_topik = moment(topik_waktu[v], "YYYY-MM-DD HH:mm:ss");
                if(akhir_topik.isAfter(curr_date)){
                    topik_aktif  = v;
                    return false;
                }
            });

            $.each(urutan_topik, function(i, v){
                last_topik_id = v ;
            });

            let interval = 1000;
            let ujian_selesai ;
            if(is_sekuen_topik)
                ujian_selesai = moment(topik_waktu[topik_aktif], "YYYY-MM-DD HH:mm:ss");
            else
                ujian_selesai = moment(waktu_selesai, "YYYY-MM-DD HH:mm:ss");
            let diffTime = ujian_selesai.unix() - curr_date.unix();

            let duration = moment.duration(diffTime*1000, 'milliseconds');
            let duration_text = '';

            refreshIntervalId = setInterval(function(){

                curr_date.add(1, 'second');
                // datetime_el.html(curr_date.format('dddd, Do MMMM YYYY, HH:mm:ss'));

                duration = moment.duration(duration - interval, 'milliseconds');
                if(duration.as('milliseconds') > 0){
                    duration_text = Math.floor(duration.as('hours')) + ":" + duration.minutes() + ":" + duration.seconds() ;
                    if(duration.as('milliseconds') == 599000){
                        // JIKA WAKTU KURANG 10 MENIT
                        Swal.fire({
                          title: "Perhatian",
                          text: "Waktu ujian kurang dari 10 menit",
                          icon: "warning"
                        });
                        // alert("Waktu ujian kurang dari 10 menit");
                    }
                }else{
                    duration_text = "0:0:0";
                   
                    if(is_sekuen_topik){
                        if(last_topik_id == topik_aktif)
                            selesai();
                        else{
                            // ajx_overlay(true);
                            // set_urutan_topik().then(function(){
                            //     update_time().then(function(){
                            //         simpan_view();
                            //         $('.class_topik_id_' + topik_aktif).first().click(); // MENAMPILKAN SOAL PERTAMA PADA TOPIK TERKAIT
                            //         ajx_overlay(false); 
                            //     });
                            // });
                            setting_up_view();
                            // location.reload(); 
                        }
                    }else{
                        selesai();
                    }
                   
                    clearInterval(refreshIntervalId);

                }

                $('#sisa_waktu').text(duration_text);
                $('#sisa_waktu_2').text(duration_text);

            },interval);

        }
    });

    /**
     * date from local computer
     */
    // date = moment(new Date());
    // datetime_el.html(date.format('dddd, MMMM Do YYYY, h:mm:ss a'));

};

// setInterval(update_time, 1000); // PER SECOND

let id_tes          = "{{ $id_tes }}";
let widget          = $(".step_pertanyaan");
let total_widget    = widget.length;

// const element = $('#lembar_ujian')[0]; // Get DOM element from jQuery collection
//
// $(document).on('click','#maximize',function(){
//     if (screenfull.isEnabled) {
//         screenfull.request(element);
//     }
//     $(this).hide();
// });

let ofs = 0;

const setting_up_view = () => {
    ajx_overlay(true);
    set_urutan_topik().then(function(){
        update_time().then(function(){
            simpan_view();
            if(is_sekuen_topik){
                $('.class_topik_id_' + topik_aktif).first().click(); // MENAMPILKAN SOAL PERTAMA PADA TOPIK TERKAIT
            }else{
                buka(1);
            }

            // $('#div_topik_ujian').html('');
            // if(is_sekuen_topik){
            //     $.each(urutan_topik, function(i, v){
            //         if(v){
            //             let sub_el_1 = $('<dt class="col-md-8"></dt>').text((i+1) + '. ' + topik_nama[v]);
            //             let sub_el_2 = $('<dd class="col-md-4"></dd>').text(topik[v] + ' menit');
            //             let el = $('<dl class="row"></dl>').html(sub_el_1.prop('outerHTML') + sub_el_2.prop('outerHTML'));   
            //             $('#div_topik_ujian').append(el);
            //         }
            //     });
            // }else{
            //     $('.legend_topik').each(function(i,v){
            //         let id = $(this).data('id');
            //         if(v){
            //             let sub_el_1 = $('<dt class="col-md-12"></dt>').text((i+1) + '. ' + topik_nama[id]);
            //             let el = $('<dl class="row"></dl>').html(sub_el_1.prop('outerHTML'));   
            //             $('#div_topik_ujian').append(el);

            //         }
            //     });
            // }

            ajx_overlay(false); 
        });
    });
};

function init_page_level(){
    setting_up_view();
    update_status_ujian();
    // ofs = $('#q_n_a').offset();
    // console.log('ofs',ofs);
    // $('body').bind('copy paste cut drag drop', function (e) {
    //   e.preventDefault();
    // });

    // document.addEventListener('contextmenu', event => event.preventDefault());

    // let width = $(window).width();

    // let height = $(window).height();
    
    // $('#q_n_a').css('max-height', (height - (87.85 + 68.5)));
    // $('#q_n_a').css('min-height', (height - (87.85 + 68.5)));
    // $('#panel_user').css('max-height', (height - (87.85)));

}

function wrap_navigasi(){
    $.each(topik, function(i,v){
        if(v)
            $('.class_topik_id_' + i).wrapAll('<fieldset class="legend_topik" data-id="'+ i +'" />');
    });

    $('.legend_topik').each(function(i,v){
        let id = $(this).data('id');
        if(v)
            $(this).prepend('<legend>'+ topik_nama[id] +'</legend>');
    });

    if(is_sekuen_topik){
        $('fieldset.legend_topik').each(function(i, v){
            let fs_id = $(this).data('id');
            $(this).show();
            if(fs_id != topik_aktif){
                $(this).hide();
            }
        })
    }
}

// $('#lembar_ujian').on('scroll', function(event) {
//     let scrollValue = $(this).scrollTop();
//     console.log('scrollValue', scrollValue);
//     console.log('ofs' , ofs);
//     if(scrollValue > ofs.top){
//         // alert('s');
//         $('#q_n_a').addClass('fixed-top');
//         $('#q_n_a').css('left',ofs.left);
//     }else{
//         $('#q_n_a').removeClass('fixed-top');
//         $('#q_n_a').css('left',"");
//     }
// });

// window.onblur = function () {
    
// };

// window.onfocus = function () {
   
// };

$(window).focus(function() {
    sendmsg(JSON.stringify({
        'nim':'{{ get_logged_user()->username }}',
        'as':'{{ get_selected_role()->name }}',
        'cmd':'MHS_GET_FOCUS',
        'app_id': '{{ APP_ID }}',
    }));
}).blur(function() {
    @if(SHOW_WARNING_SAAT_UJIAN)
    Swal.fire({
        title: "Perhatian",
        text: "Anda diperingatkan tidak boleh membuka halaman lain, semua aktifitas anda direkam oleh sistem untuk penilaian",
        icon: "warning"
    });
    @endif
    sendmsg(JSON.stringify({
        'nim':'{{ get_logged_user()->username }}',
        'as':'{{ get_selected_role()->name }}',
        'cmd':'MHS_LOST_FOCUS',
        'app_id': '{{ APP_ID }}',
    }));
});

function update_status_ujian(){
    setTimeout(function() {
      //your code to be executed after 1 second
        sendmsg(JSON.stringify({
            'nim':'{{ get_logged_user()->username }}',
            'as':'{{ get_selected_role()->name }}',
            'cmd':'MHS_START_UJIAN',
            'app_id': '{{ APP_ID }}',
        }));
    }, 1000);
}

/** [START] FUNGSI SELESAI UJIAN */
function selesai(ended_by = '') {
    if(ended_by == ''){
        ended_by = '{{ get_logged_user()->username }}';
    }
    ajaxcsrf();
    ajx_overlay(true);
    $.ajax({
        type: "POST",
        url: "{{ url('ujian/ajax/close_ujian') }}",
        data: {
            'id': id_ujian,
            'key': key,
            'ended_by': ended_by,
        },
        success: function (r) {
            if (r.status) {
                sendmsg(JSON.stringify({
                    'nim':'{{ get_logged_user()->username }}',
                    'as':'{{ get_selected_role()->name }}',
                    'cmd':'MHS_STOP_UJIAN',
                    'app_id': '{{ APP_ID }}',
                }));
                Swal.fire({
                    title: "Perhatian",
                    text: "Ujian telah selesai, anda akan keluar ujian dalam 3 detik",
                    icon: "success",
                    confirmButtonText: "Keluar Sekarang",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then(result => {
			        if (result.value) {
                        window.location.href = '{{ url('ujian/list') }}';
                    }
                });
                setTimeout(function() {
                    window.location.href = '{{ url('ujian/list') }}';
                }, 3000);
            }
        },
        error: function () {
            Swal.fire({
                title: "Perhatian",
                text: "Ujian telah selesai, Anda akan keluar ujian dalam 3 detik", // INI TERJADI JIKA TELAH FINISH OLEH CRON TP FUNGSI SELESAI TELAT DITRIGER PESERTA
                icon: "warning",
                allowOutsideClick: false,
                allowEscapeKey: false,
            });
            setTimeout(function() {
                window.location.href = '{{ url('ujian/list') }}';
            }, 3000);
        },
        complete: function () {
            ajx_overlay(false);
        }
    });
}
/** [END] FUNGSI SELESAI UJIAN */

$('#lembar_ujian').on('scroll', function() {
    let docViewTop = $('#lembar_ujian').scrollTop();
    let hel = $('#ujian_card_header').height();

    if(docViewTop <= hel){
        $('#fixed_panel').hide();
    }else{
        $('#fixed_panel').show();
    }
});

</script>
<script src="{{ asset('assets/dist/js/app/ujian/index.js') }}"></script>
<!-- END PAGE LEVEL JS-->
@endpush

@section('content')
<div id="fixed_panel" style="display: none">
    <div id="sisa_waktu_2">
        0:0:0
    </div>
</div>
<div id="watermark">
    {{ $h_ujian->mhs->nim }}
</div>
<section id="lembar_ujian" class="card card-fullscreen">
<div class="row">
    <div class="col-12">
        <div class="card" id="ujian_card_header">
            <div class="card-header" style="padding: 1rem">
                <h4 class="card-title" style="width: 500px; margin: 0 auto;text-align: center;">
                    <span id="sisa_waktu" style="font-size: 2rem">0:0:0</span>
                </h4>
            </div>
        </div>
    </div>
</div>
<!---- --->
{!! form_open('', array('id'=>'ujian'), ['id'=> $id_tes, 'key' => $one_time_token]) !!}
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
                                        <label style="display: block;">{{ $h_ujian->mhs->nama }}</label>
                                        <label style="display: block; font-weight: normal; margin-bottom: 0;">( {{ $h_ujian->mhs->nim }} )</label>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="2" style="text-align: center"><img src="{{ APP_TYPE == 'tryout' ? asset('assets/imgs/no_profile_120_150.jpg') : (empty($h_ujian->mhs->foto) ? asset('assets/imgs/no_profile_120_150.jpg') : $h_ujian->mhs->foto ) }}" style="height: 150px; width: 120px;" /></th>
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
                            <button class="btn btn-danger btn-block" id="btn_akhiri_ujian" type="button" onclick="return simpan_akhir();"><i class="fa fa-stop"></i> Akhiri Ujian</button>
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
                                    <span style="font-size: 20px" class="">Soal #<span id="soalke"></span></span>
                                    <span class="float-right text-danger" id="text_info_topik" style="font-size: 15px; font-weight: bold; padding-top: 5px; text-transform: uppercase">&nbsp;</span>
                                    <hr>
                                    {!! $html_pertanyaan !!}
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
                                    {!! $html !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mb-0" id="div_navigasi">
                    <div class="card-content">
                        <div class="card-body text-center" style="padding: 1rem">
                            <div class="btn-group" role="group" aria-label="" id="next_prev_pertanyaan">
                                <button type="button" class="action back btn btn-info rounded-0" rel="0" onclick="return back();"><i class="fa fa-chevron-left"></i> Back</button>
                                <button type="button" class="ragu_ragu btn btn-warning" rel="1" onclick="return tidak_jawab();" style="display: none"><i class="fa fa-pause"></i> <span class="span_ragu">Ragu-Ragu</span></button>
                                <button type="button" class="action next btn btn-info" rel="2" onclick="return next();">Next <i class="fa fa-chevron-right"></i></button>
                                <button type="button" class="selesai action submit btn btn-danger" onclick="return simpan_akhir();"><i class="fa fa-stop"></i> Selesai</button>
                                <input type="hidden" name="jml_soal" id="jml_soal" value="<?=$no; ?>">
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
