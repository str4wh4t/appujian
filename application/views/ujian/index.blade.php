@extends('template.main')

@push('page_level_css')
    <!-- BEGIN PAGE LEVEL JS-->
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/enjoyhint/enjoyhint.css') }}" rel="stylesheet">
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
    color: #fff;
    background: #86C186;
    font-weight: bold;
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
.enjoyhint{
    z-index: 9996 !important;
}
.swal2-container{
    z-index: 9997 !important;
}
.enjoyhint_close_btn{
    display: none;
}
</style>
@endpush

@push('page_vendor_level_js')
    <!-- BEGIN PAGE VENDOR JS-->
{{--    <script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
    <script src="{{ asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="https://momentjs.com/downloads/moment-with-locales.js"></script>
{{--     <script src="https://cdnjs.cloudflare.com/ajax/libs/screenfull.js/5.0.2/screenfull.min.js"></script>--}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/kineticjs/5.2.0/kinetic.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.2/jquery.scrollTo.min.js"></script>
    <script src="{{ asset('assets/plugins/enjoyhint/enjoyhint.min.js') }}"></script>

    <!-- END PAGE VENDOR JS -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

let id_ujian = '{{  uuid_create_from_integer($h_ujian->id) }}';
let key = '{{ $one_time_token }}';

let topik = [];
let topik_nama = [];
@php
$i=1;
@endphp
@foreach($h_ujian->soal as $s)
    @php
        if(!isset($topik_ujian_jml[$s->topik_id]))
            $i = 1;
        $topik_ujian_jml[$s->topik_id] = $i;
        $topik_ujian_nama[$s->topik_id] = $s->topik->nama_topik;
        $i++;
    @endphp
@endforeach
@foreach($topik_ujian_jml as $topik_id => $jml_topik)
    topik[{{ $topik_id }}] = '{{ $jml_topik }}';
@endforeach
@foreach($topik_ujian_nama as $topik_id => $nama_topik)
    topik_nama[{{ $topik_id }}] = '{{ $nama_topik }}';
@endforeach

let curr_date;

let update_time = () => {
    moment.locale('id');
    /**
     * date from server
     */
    $.ajaxSetup({
        url: '<?= site_url('get_server_time') ?>',
        global: false,
        type: "GET"
    });

    $.ajax({
        success: function (date_ajax) {
            moment.locale('id');
            curr_date = moment(date_ajax, "YYYY-MM-DD HH:mm:ss");

            let interval = 1000;

            let ujian_selesai = moment('{{ date('Y-m-d H:i:s', strtotime($h_ujian->tgl_selesai)) }}', "YYYY-MM-DD HH:mm:ss");
            let diffTime = ujian_selesai.unix() - curr_date.unix();

            let duration = moment.duration(diffTime*1000, 'milliseconds');
            let duration_text = '';

            let refreshIntervalId = setInterval(function(){

                curr_date.add(1, 'second');
                // datetime_el.html(curr_date.format('dddd, Do MMMM YYYY, HH:mm:ss'));

                duration = moment.duration(duration - interval, 'milliseconds');
                if(duration.as('milliseconds') > 0){
                    duration_text = Math.floor(duration.as('hours')) + ":" + duration.minutes() + ":" + duration.seconds() ;
                    $('#btn_lanjut_ujian').removeClass('btn-danger').addClass('btn-success');
                    if(duration.as('milliseconds') == 599000){
                        // JIKA WAKTU KURANG 10 MENIT
                        Swal({
                          title: "Perhatian",
                          text: "Waktu ujian kurang dari 10 menit",
                          type: "warning"
                        });
                        // alert("Waktu ujian kurang dari 10 menit");
                    }
                }else{
                   duration_text = "0:0:0";
                   $('#btn_lanjut_ujian').removeClass('btn-success').addClass('btn-danger');
                   selesai();
                   clearInterval(refreshIntervalId);

                }

                $('#sisa_waktu').text(duration_text);

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

$(document).on('click','#btn_lanjut_ujian',function(){
    if($(this).hasClass('btn-danger')){
        Swal('Perhatian', 'Anda berada diluar jadwal ujian', 'error');
        return false;
    }else{
        location.href = '{!! site_url('ujian/?key='. $one_time_token .'&id='. $id_tes ) !!}';
    }
});

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

let enjoyhint_instance = null;

let enjoyhint_script_steps = [
    {
        'next #tb_identitas_peserta': 'Selamat datang di ujian CAT UNDIP. <br>Sebelum memulai ujian pastikan identitas anda benar',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
    },
    {
        'next #sisa_waktu': 'Perhatikan sisa waktu ujian anda',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
    },
    {
        'next #nav_opener': 'Anda dapat "BUKA" / "TUTUP" navigasi untuk melihat pertanyaan yg tersedia',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
        'shape': 'circle',
        'radius': 50,
        'onBeforeStart': function () {
            $('#lembar_ujian').animate({
                scrollTop: $("#lembar_ujian").offset().top
            }, 100, 'swing', function(){
                setTimeout(function(){
                    $('#nav_content').slideDown();
                    $('#nav_opener').text('TUTUP');
                    nav_is_open = true;
                }, 100);
            });
        },
        'timeout': 100,
    },
    {
        'next #btn_soal_2': 'Pilih soal yang ingin dikerjakan, contoh : kita pilih soal nomer 2',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
        'onBeforeStart': function () {
            $('#lembar_ujian').animate({
                scrollTop: $("#lembar_ujian").offset().top
            }, 100, 'swing', function(){
                setTimeout(function(){
                    $('#nav_content').slideDown();
                    $('#nav_opener').text('TUTUP');
                    nav_is_open = true;
                }, 100);
            });
        },
        'timeout': 100,
    },
    {
        'next #isi_pertanyaan': 'Pertanyaan yang ditampilkan',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
        'onBeforeStart': function () {
            $('#btn_soal_2').trigger('click');
            $('#lembar_ujian').scrollTo('#isi_pertanyaan');
            // setTimeout(function(){
            //     $('#lembar_ujian').animate({
            //         scrollTop: $("#isi_pertanyaan").offset().top
            //     }, 100);
            // }, 100);
        },
        // 'timeout': 100,
    },
    {
        'next .funkyradio-success:visible:nth-child(2)': 'Terdiri 5 (lima) opsi jawaban (a, b, c, d, dan e), contoh : kita pilih jawaban "b"',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
        'onBeforeStart': function () {
            $('#lembar_ujian').scrollTo('#pil_jawaban');
            setTimeout(function(){
               $(".funkyradio-success:visible:nth-child(2) > input").trigger('click');
            },1000);
        },
    },
    {
        'next #next_prev_pertanyaan': 'Anda dapat men-skip atau kembali ke pertanyaan sebelumnya, dan juga dapat menandai "RAGU" pada pilihan jawaban anda',
        "nextButton" : {className: "black border-amber btn-amber", text: "Lanjut"},
        'showSkip': false,
        'onBeforeStart': function () {
            $('#lembar_ujian').scrollTo('#next_prev_pertanyaan');
        },
    },
    {
        'skip #btn_akhiri_ujian': 'Tombol ini untuk mengakhiri ujian apabila anda ingin menyelesaikan ujian lebih awal',
        'showNext': false,
        'skipButton' : {className: "white border-white bg-transparent width-200", text: "Sudahi petunjuk."},
        'onBeforeStart': function () {
            $('#lembar_ujian').scrollTo('#tb_identitas_peserta');
        },
    }
];

function setup_hint(){
    enjoyhint_instance = new EnjoyHint({
        onSkip: function(){
            $('#lembar_ujian').animate({
                scrollTop: $("#lembar_ujian").offset().top
            }, 1000, 'swing', function(){
                setTimeout(function(){
                    $('#nav_content').slideUp();
                    $('#nav_opener').text('BUKA');
                    nav_is_open = false;
                }, 100);
            });
        }
    });
    // enjoyhint_instance.setScript(enjoyhint_script_data);
    // enjoyhint_instance.runScript();
    enjoyhint_instance.set(enjoyhint_script_steps);
    enjoyhint_instance.run();
}

function init_page_level(){
    update_time();
    update_status_ujian();
    @if($h_ujian->m_ujian->tampilkan_tutorial)
    setup_hint();
    @endif
    // ofs = $('#q_n_a').offset();
    // console.log('ofs',ofs);
    // $('body').bind('copy paste cut drag drop', function (e) {
    //   e.preventDefault();
    // });
    document.addEventListener('contextmenu', event => event.preventDefault());
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
    Swal({
        title: "Perhatian",
        text: "Anda diperingatkan tidak boleh membuka halaman lain, semua aktifitas anda direkam oleh sistem untuk penilaian",
        type: "warning"
    });
    conn.send(JSON.stringify({
        'nim':'{{ get_logged_user()->username }}',
        'as':'{{ get_selected_role()->name }}',
        'cmd':'MHS_LOST_FOCUS',
        'app_id': '{{ APP_ID }}',
    }));
};

window.onfocus = function () {
    conn.send(JSON.stringify({
        'nim':'{{ get_logged_user()->username }}',
        'as':'{{ get_selected_role()->name }}',
        'cmd':'MHS_GET_FOCUS',
        'app_id': '{{ APP_ID }}',
    }));
};

function update_status_ujian(){
    setTimeout(function() {
      //your code to be executed after 1 second
        conn.send(JSON.stringify({
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
                conn.send(JSON.stringify({
                    'nim':'{{ get_logged_user()->username }}',
                    'as':'{{ get_selected_role()->name }}',
                    'cmd':'MHS_STOP_UJIAN',
                    'app_id': '{{ APP_ID }}',
                }));
                Swal({
                    title: "Perhatian",
                    text: "Ujian telah selesai, anda akan keluar ujian dalam 3 detik",
                    type: "success"
                });
                setTimeout(function() {
                    window.location.href = '{{ url('ujian/list') }}';
                }, 3000);
            }
        },
        error: function () {
            Swal({
                title: "Perhatian",
                text: "Ujian telah selesai, Anda akan keluar ujian dalam 3 detik", // INI TERJADI JIKA TELAH FINISH OLEH CRON TP FUNGSI SELESAI TELAT DITRIGER PESERTA
                type: "warning"
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

</script>
<script src="{{ asset('assets/dist/js/app/ujian/index.js') }}"></script>
<!-- END PAGE LEVEL JS-->
@endpush

@section('content')
<section id="lembar_ujian"  style="background-color: #f3f3f3; overflow-x: hidden;" class="card card-fullscreen">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="padding: 1rem">
{{--                <h4 class="card-title" style="width: 300px; float: left"><?=$subjudul?></h4>--}}
                <h4 class="card-title" style="width: 500px; margin: 0 auto;text-align: center;">
                    <span>Sisa Waktu </span><hr>
                    <span id="sisa_waktu" style="font-size: 2rem">0:0:0</span>
                </h4>
{{--                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>--}}
{{--					<div class="heading-elements">--}}
{{--						<ul class="list-inline mb-0">--}}
{{--							<li><a data-action="expand"><i class="ft-maximize"></i></a></li>--}}
{{--							<li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>--}}
{{--							<li><a data-action="close"><i class="ft-x"></i></a></li>--}}
{{--							<li><a data-action="collapse"><i class="ft-minus"></i></a></li>--}}
{{--						</ul>--}}
{{--					</div>--}}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-content">
                <div class="card-header" style="width: 500px; margin: 0 auto;text-align: center;">
                    <h4 class="card-title" id="navigas_ujian">NAVIGASI [ <a href="#" id="nav_opener">BUKA</a> ]</h4>
				</div>
				<div class="card-content collapse show" id="nav_content" style="display: none">
                    <div class="card-body" style="padding-top: 0px">
                        <div id="tampil_jawaban"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!---- --->
{!! form_open('', array('id'=>'ujian'), ['id'=> $id_tes, 'key' => $one_time_token]) !!}
<div class="row">
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-bordered" id="tb_identitas_peserta">
                                <tr>
                                    <th colspan="2" style="text-align: center; white-space: normal;">
                                        <label style="display: block;">{{ $h_ujian->mhs->nama }}</label>
                                        <label style="display: block; font-weight: normal;">( {{ $h_ujian->mhs->nim }} )</label>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="2" style="text-align: center"><img src="{{ $h_ujian->mhs->foto }}" style="height: 150px; width: 120px;" /></th>
                                </tr>
                                <tr>
                                    <th>Ujian</th>
                                    <td>{{ $h_ujian->m_ujian->matkul->nama_matkul }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Soal</th>
                                    <td>{{ $h_ujian->m_ujian->jumlah_soal }} soal</td>
                                </tr>
{{--                                <tr>--}}
{{--                                    <th>Waktu</th>--}}
{{--                                    <td>{{ strftime('%T', strtotime($h_ujian->tgl_mulai)) }} - {{ strftime('%T', strtotime($h_ujian->tgl_selesai)) }}</td>--}}
{{--                                </tr>--}}
                                <tr>
                                    <th>Waktu</th>
                                    <td>{{ $h_ujian->m_ujian->waktu }} menit</td>
                                </tr>
                            </table>
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
    <div class="col-md-8" id="q_n_a">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body" id="isi_pertanyaan">
                            <span style="font-size: 20px" class="">Pertanyaan #<span id="soalke"></span></span>
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
                        <hr>
                        <div class="card-body text-center">
                            <div class="btn-group" role="group" aria-label="" id="next_prev_pertanyaan">
                                <button type="button" class="action back btn btn-info" rel="0" onclick="return back();"><i class="fa fa-chevron-left"></i> Back</button>
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

{{--    <div class="col-md-9">--}}
{{--        --}}
{{--        <div class="box box-primary">--}}
{{--            <div class="box-header with-border">--}}
{{--                <h3 class="box-title"><span class="badge bg-blue">Soal #<span id="soalke"></span> </span></h3>--}}
{{--                <div class="box-tools pull-right">--}}
{{--                    <span class="badge bg-red">Sisa Waktu <span class="sisawaktu" data-time="<?=$soal->tgl_selesai?>"></span></span>--}}
{{--                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        --}}
{{--    </div>--}}

{!! form_close() !!}
<!---- --->

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
