@extends('template.main')

@push('page_level_css')
    <!-- BEGIN PAGE LEVEL JS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
    <!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    {{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
    <script src="{{ asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
    <!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
    <!-- BEGIN PAGE LEVEL JS-->
    <script type="text/javascript">

        let datetime_el = $("#time_now"), date;
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
                    date = moment(date_ajax, "YYYY-MM-DD HH:mm:ss");
                    let interval = 1000;
                    setInterval(function() {
                        date.add(1, 'second');
                        datetime_el.html(date.format('H:mm:ss'));
                    },interval);
                }
            });

            /**
             * date from local computer
             */
            // date = moment(new Date());
            // datetime_el.html(date.format('dddd, MMMM Do YYYY, h:mm:ss a'));

        };
        update_time();


    </script>
    <script src="{{ asset('assets/dist/js/app/ujian/list.js?u=') . mt_rand() }}"></script>
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
                        <div class="row">
{{--                            <div class="col-sm-3">--}}
{{--                                <div class="alert bg-success">--}}
{{--                                    <span style="font-size: 24px">Kelas<i class="pull-right fa fa-building-o"></i></span>--}}
{{--                                    <hr>--}}
{{--                                    <span class="d-block"> <?=$mhs->nama_kelas?></span>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-sm-3">--}}
{{--                                <div class="alert bg-info">--}}
{{--                                    <span style="font-size: 24px">Jurusan<i class="pull-right fa fa-graduation-cap"></i></span>--}}
{{--                                    <hr>--}}
{{--                                    <span class="d-block"> <?=$mhs->nama_jurusan?></span>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="col-sm-3">
                                <div class="alert bg-warning">
                                    <span style="font-size: 24px">Tanggal<i class="pull-right fa fa-calendar"></i></span>
                                    <hr>
                                    <span class="d-block"> <?=strftime('%A, %d %B %Y')?></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="alert bg-danger">
                                    <span style="font-size: 24px">Waktu<i class="pull-right fa fa-clock-o"></i></span>
                                    <hr>
                                    <span class="d-block"> <span class="live-clock"
                                                                 id="time_now"><?=date('H:i:s')?></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="table-responsive pb-3 pt-3"
                                             style="border: 0">
                                            <table id="ujian"
                                                   class="table table-striped table-bordered table-hover pb-3">
                                                <thead>
                                                <tr>
{{--                                                    <th>No.</th>--}}
                                                    <th class="text-center">Aksi</th>
                                                    <th>Nama Ujian</th>
                                                    <th>Materi Ujian</th>
                                                    <th>Status</th>
                                                    <th>Soal</th>
                                                    <th>Jadwal Mulai</th>
                                                    <th>Jadwal Selesai</th>
                                                    <th>Lama Ujian</th>
                                                </tr>
                                                </thead>
                                                <tfoot>
                                                <tr>
{{--                                                    <th>No.</th>--}}
                                                    <th class="text-center">Aksi</th>
                                                    <th>Nama Ujian</th>
                                                    <th>Materi Ujian</th>
                                                    <th>Status</th>
                                                    <th>Soal</th>
                                                    <th>Jadwal Mulai</th>
                                                    <th>Jadwal Selesai</th>
                                                    <th>Lama Ujian</th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
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
