@extends('template.main')

@push('page_level_css')
    <!-- BEGIN PAGE LEVEL JS-->
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/plugins/animate/animate.css') }}">
    <!-- END PAGE LEVEL JS-->
@endpush

@push('page_custom_css')
<style type="text/css">
.blink{
    animation:blink 100ms infinite alternate;
}

@keyframes blink {
    from { opacity:1; }
    to { opacity:0; }
};
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
    <!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
    <!-- BEGIN PAGE LEVEL JS-->
    <script type="text/javascript">

        let datetime_el = $("#time_now");
        let curr_date;

        @if(@$h_ujian)
        let close_ujian = () => {
            $.ajax({
                type: "POST",
                url: "{{ site_url('ujian/ajax/close_ujian') }}",
                data: {
                    'id': '{{  uuid_create_from_integer($h_ujian->id) }}',
                    'key': '{{ $one_time_token }}'
                },
                success: function (r) {
                    if (r.status) {
                        window.location.href = '{{ site_url('ujian/list') }}';
                    }
                }
            });
        };
        @endif

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

                    @if(!@$h_ujian)
                        let jadwal_mulai = moment('{{ date('Y-m-d H:i:s', strtotime($ujian->tgl_mulai)) }}', "YYYY-MM-DD HH:mm:ss");
                        let jadwal_selesai = moment('{{ date('Y-m-d H:i:s', strtotime($ujian->terlambat)) }}', "YYYY-MM-DD HH:mm:ss");
                        let diffTime = jadwal_selesai.unix() - curr_date.unix();
                    @else
                        let ujian_selesai = moment('{{ date('Y-m-d H:i:s', strtotime($h_ujian->tgl_selesai)) }}', "YYYY-MM-DD HH:mm:ss");
                        let diffTime = ujian_selesai.unix() - curr_date.unix();

                    @endif

                    let duration = moment.duration(diffTime*1000, 'milliseconds');
                    let duration_text = '';

                    setInterval(function(){

                        curr_date.add(1, 'second');
                        datetime_el.html(curr_date.format('dddd, Do MMMM YYYY, HH:mm:ss'));

                        @if(!@$h_ujian)
                            duration = moment.duration(duration - interval, 'milliseconds');
                            if(curr_date.isBetween(jadwal_mulai, jadwal_selesai, undefined, '[)')){
                                if(duration.as('milliseconds') > 0){
                                    duration_text = Math.floor(duration.as('hours')) + ":" + duration.minutes() + ":" + duration.seconds() ;
                                    $('#btncek').removeClass('btn-danger').addClass('btn-success');
                                }else{
                                   duration_text = "0:0:0";
                                   $('#btncek').removeClass('btn-success').addClass('btn-danger');
                                }
                            }else{
                                duration_text = "0:0:0";
                                $('#btncek').removeClass('btn-success').addClass('btn-danger');
                                if(curr_date.isSameOrAfter(jadwal_selesai)){
                                    $('#pesan_ujian_expired').show();
                                }
                            }
                        @else
                            duration = moment.duration(duration - interval, 'milliseconds');
                            if(duration.as('milliseconds') > 0){
                                duration_text = Math.floor(duration.as('hours')) + ":" + duration.minutes() + ":" + duration.seconds() ;
                                $('#btn_lanjut_ujian').removeClass('btn-danger').addClass('btn-success');
                            }else{
                               duration_text = "0:0:0";
                               $('#btn_lanjut_ujian').removeClass('btn-success').addClass('btn-danger');
                               close_ujian();

                            }
                        @endif
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
                location.href = '{!! site_url('ujian/?key='. $one_time_token .'&id='. $encrypted_id .'&token='. $token ) !!}';
            }
        });

        $(document).on('click','#btn_lanjut_modal_tata_tertib',function(){
            let setuju = $('#chk_setuju_tata_tertib').is(":checked");
            if(setuju){
                 Swal({
                    title: "Mulai Ujian",
                    text: "Ujian yang sudah dimulai tidak dapat dibatalkan.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#37bc9b",
                    cancelButtonColor: "#f6bb42",
                    confirmButtonText: "Mulai"
                }).then(result => {
                        if (result.value) {
                            let idUjian = $('#btncek').data('id');
                            let key = $('#id_ujian').data('key');
                            let token = $('#modal_tata_tertib').data('id');
                            location.href = '{{ url('ujian') }}?key=' + key + '&id=' + idUjian + '&token=' + token;
                        }
                    });
            }else{
                Swal({
                    title: "Perhatian",
                    text: "Anda belum menyetujui tata tertib ujian.",
                    type: "warning"
                }).then(result => {
                    if (result.value) {
                        $('#div_setuju_tata_tertib').addClass('blink');
                        setTimeout(function(){ $('#div_setuju_tata_tertib').removeClass('blink'); }, 500);
                    }
                });
            }
        });

        function init_page_level(){
            update_time();
            // $.LoadingOverlay("show");
        }

    </script>
    <script src="{{ asset('assets/dist/js/app/ujian/token.js') }}"></script>
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
<div class="box box-primary">
    <div class="box-body">
        <div class="row">
            <div class="col-sm-6">
                <a href="{{ site_url('ujian/list') }}" class="btn btn-warning mb-1"><i class="fa fa-arrow-left"></i> Kembali</a>
                <div class="alert bg-danger">
                    <span style="font-size: 20px">Waktu<i class="pull-right fa fa-clock-o"></i></span>
                    <hr>
                    <span class="d-block"> <span class="live-clock" id="time_now" style="font-size: 20px"><?=date('H:i:s')?></span></span>
                </div>
                <div class="alert bg-info">
                    <p style="font-size: 20px">Mengenal Ujian Online Undip<i class="pull-right fa fa-exclamation-triangle"></i></p>
                    <hr>
                    <p>
                    Computer Assisted Test (CAT) Universitas Diponegoro yang dikelola oleh Lembaga Pengembangan dan Penjaminan Mutu Pendidikan memberikan kemudahan dalam pelayanan test baik secara online maupun offline.</p>
                <p>Test yang diadakan meliputi : Tes Potensi Akademik (TPA), Tes Substansi Bidang, Tes Prediksi dan lain-lain. Sistem ujian menggunakan CAT akan diperoleh hasil seleksi yang kredible, akurat dan cepat. </p>
                <p>Informasi layanan CAT dapat menghubungi layanan ujian Undip di : </p>
                <p><i class="fa fa-phone-square"></i> +62-24 7460041</p>
                <p><i class="fa fa-whatsapp"></i> 0812-2561-1333</p>
                <p><i class="ft-mail"></i> lp2mp@live.undip.ac.id</p>
                <p><i class="icon-globe"></i> www.lp2mp.undip.ac.id</p>
                </div>
            </div>
            <div class="col-sm-6">
                <table class="table table-bordered">
                    <tr>
                        <th>No Peserta</th>
                        <td><?=$mhs->nim?></td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td><?=$mhs->nama?></td>
                    </tr>
                    <tr>
                        <th>Materi Ujian</th>
                        <td><?=$ujian->matkul->nama_matkul?></td>
                    </tr>
                    <tr>
                        <th>Nama Ujian</th>
                        <td><?=$ujian->nama_ujian?></td>
                    </tr>
                    <tr>
                        <th>Jumlah Soal</th>
                        <td><?=$ujian->jumlah_soal?> soal</td>
                    </tr>
                    <tr>
                        <th>Jadwal Mulai</th>
                        <td>
                            <?=strftime('%d %B %Y', strtotime($ujian->tgl_mulai))?>
                            <?=date('H:i:s', strtotime($ujian->tgl_mulai))?>
                        </td>
                    </tr>
                    <tr>
                        <th>Jadwal Selesai</th>
                        <td>
                            <?=strftime('%d %B %Y', strtotime($ujian->terlambat))?>
                            <?=date('H:i:s', strtotime($ujian->terlambat))?>
                        </td>
                    </tr>
                    <tr>
                        <th>Lama Ujian</th>
                        <td><?=$ujian->waktu?> Menit</td>
                    </tr>
                    @if(!@$h_ujian)
                        <tr>
                            <th>Sisa Waktu</th>
                            <td id="sisa_waktu">0:0:0</td>
                        </tr>
                        @if($ujian->pakai_token == 1)
                        <tr>
                            <th style="vertical-align:middle">Token</th>
                            <td>
                                <input autocomplete="off" id="token" placeholder="Masukan token ujian disini" type="text" class="form-control">
                            </td>
                        </tr>
                        @endif
                    @else
                        <tr style="background-color: #f5f5f5">
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th>Mulai Ujian</th>
                            <td>
                                <?=strftime('%d %B %Y', strtotime($h_ujian->tgl_mulai))?>
                                <?=date('H:i:s', strtotime($h_ujian->tgl_mulai))?>
                             </td>
                        </tr>
                        <tr>
                            <th>Selesai Ujian</th>
                            <td>
                                <?=strftime('%d %B %Y', strtotime($h_ujian->tgl_selesai))?>
                                <?=date('H:i:s', strtotime($h_ujian->tgl_selesai))?>
                             </td>
                        </tr>
                        <tr>
                            <th>Sisa Waktu</th>
                            <td id="sisa_waktu">0:0:0</td>
                        </tr>
                    @endif
                </table>
                <span id="id_ujian" data-key="<?=$one_time_token?>"></span>
                @if(@$h_ujian)
                    <div class="alert alert-warning" style="border-color: #ff0000 !important; background-color: #f8fbb8 !important;">
                        Perhatian! ujian telah anda mulai klik lanjutkan untuk masuk.
                    </div>
                    <button id="btn_lanjut_ujian" data-id="<?=$encrypted_id?>" class="btn btn-danger btn-block mb-1">
                        <i class="fa fa-pencil"></i> Lanjutkan Ujian
                    </button>
                @else
                    <div id="pesan_ujian_expired" class="alert alert-warning" style="border-color: #ff0000 !important; background-color: #f8fbb8 !important; display: none;">
                        Perhatian! waktu ujian telah selesai, silahkan hubungi dosen.
                    </div>
                    <button id="btncek" data-id="<?=$encrypted_id?>" class="btn btn-danger btn-block mb-1">
                        <i class="fa fa-pencil"></i> Mulai Ujian
                    </button>
                @endif
                <p>
                    <b><span class="text-danger">**</span></b> Waktu mengerjakan ujian adalah saat tombol diatas berwarna <b><span class="text-success">HIJAU</span></b>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal text-left"
     id="modal_tata_tertib"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel9"
     aria-hidden="true"
    data-keyboard="false" data-backdrop="static"    >
    <div class="modal-dialog modal-lg"
         role="document">
        <div class="modal-content">
            <div class="modal-header bg-success white">
                <h4 class="modal-title white"
                    id="myModalLabel9">Tata Tertib Ujian CAT UNDIP</h4>
{{--                <button type="button"--}}
{{--                        class="close"--}}
{{--                        data-dismiss="modal"--}}
{{--                        aria-label="Close">--}}
{{--                    <span aria-hidden="true">&times;</span>--}}
                </button>
            </div>
            <div class="modal-body">
                <h5 style="text-align: center; border: 1px solid #333; padding: 5px; font-weight: bold;">
                    TATA TERTIB PESERTA UJIAN TULIS<br>COMPUTER ASSISTED TEST</h5>
                <p>
                    <ol>
                        <li>Peserta tidak boleh melakukan perjokian atau berlaku untuk tidak jujur.</li>
                        <li>Peserta mulai mengerjakan berdasarkan waktu dimulainya pada aplikasi CAT.</li>
                        <li>Peserta boleh melaporkan kepada panitia jika terjadi masalah pada aplikasi CAT.</li>
                        <li>Peserta boleh menekan pilihan Ragu, jika ada keraguan dalam menjawab salah satu soal.</li>
                        <li>Peserta yang sudah menyelesaikan seluruh soal ujian sebelum waktu ujian habis boleh menekan tombol selesai jika memang akan mengakhiri sebelum waktunya habis.</li>
                        <li>Peserta tidak boleh melakukan duplikat soal dengan mengambil gambar menggunakan kamera maupun meng copy paste soal.</li>
                        <li>Peserta tetap akan menjaga nama baik Universitas Diponegoro dan aplikasi CAT dari tindak kejahatan lainnya.</li>
                    </ol>
                </p>
                <div class="alert" id="div_setuju_tata_tertib" style="border: 1px solid #f00;background-color: #ffff9a;">
                    <fieldset>
                    <input type="checkbox" class="inp" value="setuju" id="chk_setuju_tata_tertib"> <label for="chk_setuju_tata_tertib" style="display: inline">Saya menyetujui untuk mengikuti seleksi ujian menggunakan CAT ini sesuai ketentuan yang berlaku di Universitas Diponegoro</label>
                    </fieldset>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn grey btn-outline-secondary"
                        data-dismiss="modal">Close
                </button>
                <button type="button"
                        class="btn btn-outline-success" id="btn_lanjut_modal_tata_tertib">Lanjut
                </button>
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
