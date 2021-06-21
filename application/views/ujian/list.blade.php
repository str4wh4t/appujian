@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}"> --}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
{{-- <script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script> --}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<script src="{{ asset('assets/yarn/node_modules/moment/min/moment.min.js') }}"></script>
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

    function init_page_level(){
		
		ajaxcsrf();
        update_time();

	}

    $(document).on('click','.btn_ulangi_ujian',function(){

        let hid =  $(this).data('hid');
        let mid =  $(this).data('mid');
		Swal.fire({
			title: "Ulangi Ujian",
			text: "Yakin akan mengulang ujian.",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#37bc9b",
			cancelButtonColor: "#f6bb42",
			confirmButtonText: "Mulai"
		}).then(result => {
			if (result.value) {
				ajx_overlay(true);
				$.ajax({
					url: '{{ url('ujian/ajax/prepare_ujian_ulang') }}',
					type: 'post',
					data: {'id' : hid},
					dataType: 'json',
					success: function (data) {
						if (data.status == 'ok') {
							location.href = '{{ url('ujian/token/') }}' + mid ;
						}else if (data.status == 'ko') {
							Swal.fire({
								title: "Perhatian",
								text: data.msg,
								icon: "warning"
							});
						}else{
							Swal.fire({
								title: "Perhatian",
								text: "Terjadi kesalahan.",
								icon: "warning"
							});
						}
					},
					error: function (data){
						Swal.fire({
							title: "Error",
							text: "Terjadi kesalahan.",
							icon: "warning"
						});
					},
					complete: function (){
						ajx_overlay(false);
					}
				});
			}
		});
	});


    </script>
    {{-- <script src="{{ asset('assets/dist/js/app/ujian/list.js') }}"></script> --}}
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
                            <div class="col-md-6 col-sm-12">
                                <div class="alert bg-info">
                                    <span style="font-size: 24px"><i class="fa fa-calendar"></i> &nbsp;&nbsp;&nbsp;<?=strftime('%A, %d %B %Y')?></span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="alert bg-danger">
                                    <span style="font-size: 24px"><i class="fa fa-clock-o"></i> &nbsp;&nbsp;&nbsp;<span class="live-clock" id="time_now"><?=date('H:i:s')?></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="card box-shadow-0 border-primary bg-gradient-striped-grey">
                                    <div class="card-header card-head-inverse bg-primary">
                                        <h4 class="card-title text-white">Daftar Ujian</h4>
                                        {{-- <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                                        <div class="heading-elements">
                                            <ul class="list-inline mb-0">
                                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                                <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                                <li><a data-action="close"><i class="ft-x"></i></a></li>
                                            </ul>
                                        </div> --}}
                                    </div>
                                    <div class="card-content collapse show">
                                        <div class="card-body">
                                            {{-- [START] FOREACH UJIAN SUDAH  --}}
                                            <div class="row">
                                                @if ($is_show_tutorial)
                                                <div class="col-md-4 col-sm-12">
                                                    <div class="card box-shadow-0 border-success" style="background-color: #ffc;">
                                                        <div class="card-header " style="min-height: 85px;">
                                                            <h6><b>TUTORIAL UJIAN</b></h6>
                                                            <small class="text-danger"><b>***</b> Latihan contoh ujian dan cara menjawab nya.</small>
                                                        </div>
                                                        <div class="card-content collapse show">
                                                            <div class="card-body">
                                                                {{-- <dl class="row">
                                                                    <dt class="col-sm-5 text-left">Materi Ujian</dt>
                                                                    <dd class="col-sm-7">TUTORIAL</dd>
                                                                </dl> --}}
                                                                <dl class="row">
                                                                    <dt class="col-sm-5 text-left">Jml Soal</dt>
                                                                    <dd class="col-sm-7">{{ JML_SOAL_TUTORIAL }}</dd>
                                                                </dl>
                                                                <dl class="row">
                                                                    <dt class="col-sm-5 text-left">Lama Ujian</dt>
                                                                    <dd class="col-sm-7">{{ JML_WAKTU_TUTORIAL }} Mnt</dd>
                                                                </dl>
                                                                <dl class="row">
                                                                    <dt class="col-sm-5 text-left">Wkt Mulai</dt>
                                                                    <dd class="col-sm-7">-</dd>
                                                                </dl>
                                                                <dl class="row">
                                                                    <dt class="col-sm-5 text-left">Wkt Selesai</dt>
                                                                    <dd class="col-sm-7">-</dd>
                                                                </dl>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer border-top-blue-grey border-top-lighten-5 text-muted">
                                                            <a class="btn btn-sm btn-success" href="{{ url('ujian/tutorial') }}">
                                                                <i class="fa fa-pencil"></i> Masuk
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @forelse ($mhs_ujian_all as $mhs_ujian)

                                                @php
                                                    $today = date('Y-m-d H:i:s');
                                                    $date_start = date('Y-m-d H:i:s', strtotime($mhs_ujian->m_ujian->tgl_mulai));
                                                    $status_ujian = 'active';
                                                    if(!empty($mhs_ujian->m_ujian->terlambat)){
                                                        $date_end = date('Y-m-d H:i:s', strtotime($mhs_ujian->m_ujian->terlambat));
                                                        
                                                    }else{
                                                        $date_end = date('Y-m-d H:i:s', strtotime('+1 days'));
                                                    }

                                                    if (($today >= $date_start) && ($today <= $date_end)){
                                                        // $status_ujian = 'active';
                                                    }else{
                                                        if($today < $date_start)
                                                            $status_ujian = 'upcoming';
                                                        else
                                                            $status_ujian = 'expired';
                                                    }
                                                @endphp

                                                <div class="col-md-4 col-sm-12">
                                                    <div class="card box-shadow-0 border-primary" style="background-color: {{ (($status_ujian == 'active')||($status_ujian == 'upcoming')) ? '#fff' : '#ffcacf' }};">
                                                        {{-- <div class="card-header"></div> --}}
                                                        <div class="card-header " style="min-height: 85px;">
                                                            <h6><b>{{ $mhs_ujian->m_ujian->nama_ujian }}</b></h6>
                                                        </div>
                                                        <div class="card-content collapse show">
                                                            <div class="card-body">
                                                                {{-- <dl class="row">
                                                                    <dt class="col-sm-5 text-left">Materi Ujian</dt>
                                                                    <dd class="col-sm-7">{{ $mhs_ujian->m_ujian->matkul->nama_matkul }}</dd>
                                                                </dl> --}}
                                                                <dl class="row">
                                                                    <dt class="col-sm-5 text-left">Jml Soal</dt>
                                                                    <dd class="col-sm-7">{{ $mhs_ujian->m_ujian->jumlah_soal }}</dd>
                                                                </dl>
                                                                <dl class="row">
                                                                    <dt class="col-sm-5 text-left">Lama Ujian</dt>
                                                                    <dd class="col-sm-7">{{ $mhs_ujian->m_ujian->waktu }} Mnt</dd>
                                                                </dl>
                                                                <dl class="row">
                                                                    <dt class="col-sm-5 text-left">Wkt Mulai</dt>
                                                                    <dd class="col-sm-7">{{ $mhs_ujian->m_ujian->tgl_mulai }}</dd>
                                                                </dl>
                                                                <dl class="row">
                                                                    <dt class="col-sm-5 text-left">Wkt Selesai</dt>
                                                                    <dd class="col-sm-7">{!! empty($mhs_ujian->m_ujian->terlambat) ? '&infin;' : $mhs_ujian->m_ujian->terlambat  !!}</dd>
                                                                </dl>
                                                            </div>
                                                            <div class="card-footer border-top-blue-grey border-top-lighten-5 text-muted">
                                                                
                                                                @if(empty($mhs_ujian->h_ujian))

                                                                    @if (($status_ujian == 'active')||($status_ujian == 'upcoming'))
                                                                        <a class="btn btn-sm btn-success" href="{{ url('ujian/token/' . uuid_create_from_integer($mhs_ujian->m_ujian->id_ujian)) }}">
                                                                            <i class="fa fa-pencil"></i> Masuk
                                                                        </a>
                                                                    @elseif ($status_ujian == 'expired')
                                                                        <button type="button" class="btn btn-sm btn-warning">
                                                                            <i class="fa fa-times-circle"></i> Ujian Expired
                                                                        </button>
                                                                    @else
                                                                        
                                                                    @endif

                                                                @else
                                                                    @if ($mhs_ujian->m_ujian->tampilkan_hasil)
                                                                        <a class="btn btn-sm btn-primary" href="{{ url('hasilujian/detail/' . uuid_create_from_integer($mhs_ujian->m_ujian->id_ujian)) }}">
                                                                            <i class="fa fa-check-square"></i> Hasil Ujian
                                                                        </a>
                                                                    @else
                                                                        @if (($status_ujian == 'active')||($status_ujian == 'upcoming'))
                                                                            <a class="btn btn-sm btn-success" href="{{ url('ujian/token/' . uuid_create_from_integer($mhs_ujian->m_ujian->id_ujian)) }}">
                                                                                <i class="fa fa-pencil"></i> Masuk
                                                                            </a>
                                                                        @else
                                                                            <button type="button" class="btn btn-sm btn-warning">
                                                                                <i class="fa fa-exclamation-circle"></i> Sudah Ujian
                                                                            </button>
                                                                        @endif
                                                                    @endif

                                                                    @if($mhs_ujian->m_ujian->repeatable) 

                                                                        @if (($status_ujian == 'active')||($status_ujian == 'upcoming'))
                                                                            <button class="btn btn-sm btn-outline-danger float-right btn_ulangi_ujian" data-hid="{{ uuid_create_from_integer($mhs_ujian->h_ujian->id) }}" data-mid="{{ uuid_create_from_integer($mhs_ujian->m_ujian->id_ujian) }}">
                                                                                <i class="fa fa-refresh"></i> Ulangi Ujian
                                                                            </button>
                                                                        @endif
                                                                    
                                                                    @endif
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @empty
                                                    @if (!$is_show_tutorial)
                                                        <div class="alert bg-warning alert-icon-left alert-arrow-left w-100" role="alert">
                                                            <span class="alert-icon"><i class="fa fa-warning"></i></span> 
                                                            <b>Anda belum memiliki ujian.</b>
                                                        </div>
                                                    @endif
                                                @endforelse
                                            </div>
                                            {{-- [STOP] FOREACH UJIAN AKTIF  --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-sm-12">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="table-responsive pb-3 pt-3"
                                             style="border: 0">
                                            <table id="ujian"
                                                   class="table table-striped table-bordered table-hover pb-3">
                                                <thead>
                                                <tr>
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
                        </div> --}}
<!---- --->

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
