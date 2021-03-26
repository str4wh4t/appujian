@php
use Orm\Hujian_orm;
use Illuminate\Database\Capsule\Manager as DB;
@endphp
@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/charts/morris.css') }}">
<!-- END PAGE LEVEL CSS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/charts/raphael-min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/charts/morris.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

    function init_page_level(){
		
		ajaxcsrf();

        let data_keys = [{{ $chart_label_and_data->keys()->implode(',') }}];
        let data_values = [{{ $chart_label_and_data->values()->implode(',') }}];

        /**[START] CHART */
        //Get the context of the Chart canvas element we want to select
        
        Morris.Line({
            element: 'line-chart',
            data: {!! $chart_label_and_data !!},
            xkey: 'ujian_ke',
            ykeys: ['nilai_bobot_benar'],
            labels: ['Nilai Bobot'],
            parseTime: false,
            resize: true,
            smooth: false,
            pointSize: 3,
            pointStrokeColors:['#FF4558'],
            gridLineColor: '#e3e3e3',
            behaveLikeLine: true,
            numLines: 6,
            gridtextSize: 14,
            lineWidth: 3,
            hideHover: 'auto',
            lineColors: ['#FF4558'],
            xLabelFormat: function (x) {
                let ujian_ke = parseInt(x.src.ujian_ke);
                return "Ke-" + ujian_ke;
            },
            xLabels: "ujian_ke",
        });
		/**[STOP] CHART */
	}

	$(document).on('click','#btn_ulangi_ujian',function(){
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
					data: {'id' : '{{ !empty($h_ujian) ? uuid_create_from_integer($h_ujian->id) : 0 }}'},
					dataType: 'json',
					success: function (data) {
						if (data.status == 'ok') {
							location.href = '{{ url('ujian/token/' . uuid_create_from_integer($m_ujian->id_ujian) ) }}' ;
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
                        <div class="col-md-12 mb-4">
                            <?php $back_link = in_group('admin') ? site_url('hasilujian/detail/' . $m_ujian->id_ujian) : site_url('hasilujian/detail/' . uuid_create_from_integer($m_ujian->id_ujian)) ; ?>
                            <a href="{{ $back_link }}" class="btn btn-sm btn-warning btn-flat"><i
                                    class="fa fa-arrow-left"></i> Kembali</a>
                            {{-- <a target="_blank" href="{{ site_url('hasilujian/cetak_detail_jawaban/') }}"
                            class="btn btn-danger btn-flat btn-sm">
                            <i class="fa fa-print"></i> Print
                            </a> --}}
                        </div>
                        <div class="col-md-6">
                            <table class="table w-100">
                                <tr>
                                    <th>Mhs/Nim</th>
                                    <td>{{ $mhs->nama }} / {{ $mhs->nim }}</td>
                                </tr>
                                <tr>
                                    <th>Ujian</th>
                                    <td>{{ $m_ujian->nama_ujian }}</td>
                                </tr>
                                <tr>
                                    <th>Tgl Awal Ujian</th>
                                    <td>{{ strftime('%A, %d %B %Y', strtotime($m_ujian->tgl_mulai)) }}</td>
                                </tr>
                                <tr>
                                    <th>Tgl Akhir Ujian</th>
                                    <td>{{ strftime('%A, %d %B %Y', strtotime($m_ujian->terlambat)) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table w-100">
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <td>{{ $m_ujian->matkul->nama_matkul }}</td>
                                </tr>
                                <tr>
                                    <th>Topik</th>
                                    <td>@php($t = [])
                                        @foreach($m_ujian->topik AS $topik)
                                        @php($t[] = $topik->nama_topik)
                                        @php($t = array_unique($t))
                                        @endforeach
                                        {{ implode(',' , $t) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jumlah Soal</th>
                                    <td>{{ $m_ujian->jumlah_soal }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td>{{ $m_ujian->waktu }} Menit
                                    </td>
                                </tr>
                            </table>
                        </div>
                        @if (in_group('mahasiswa'))
                        @if ($m_ujian->repeatable)

                            <?php 
                                $today = date('Y-m-d H:i:s');
                                //echo $paymentDate; // echos today!
                                $date_start = date('Y-m-d H:i:s', strtotime($m_ujian->tgl_mulai));
                                $date_end = date('Y-m-d H:i:s', strtotime($m_ujian->terlambat));
                                ?>

                            @if (($today >= $date_start) && ($today < $date_end)) 
                                @if (!empty($h_ujian))
                                <div class="col-md-12 mt-2 mb-2"
                                    style="text-align: center">
                                    <button class="btn btn-outline-danger btn-lg" id="btn_ulangi_ujian">
                                        <i class="fa fa-refresh"></i> Ulangi Ujian
                                    </button>
                                </div>
                                @endif
                            @endif

                        @endif
                        @endif
                    </div>
                    <div class="border border-success p-1 mt-1 mb-1" style="">
                        <div class="height-350 w-100 mb-2">
                            <ul class="list-inline text-center">
                                <li>
                                    <h4 class="text-danger">Performance Hasil Ujian</h4>
                                </li>
                            </ul>
                            <div id="line-chart" class="height-300"></div>
                            <ul class="list-inline text-center">
                                <li>
                                    <h6><i class="ft-circle danger"></i> Ujian</h6>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <table class="table table-striped w-100">
                        <thead>
                            <tr>
                                <th>Ujian ke</th>
                                <th>Tgl Ujian</th>
                                <th>Benar/Salah</th>
                                <th>Nilai Bobot</th>
                                <th>Nilai</th>
                                <th>Waktu Pengerjaan</th>
                                <th>Rank</th>
                                <th class="text-center">Jawaban</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php $i = 1 ?>

                            @forelse ($h_ujian_history as $data)
                            <tr>
                                <td>{{ $data->ujian_ke }}</td>
                                <td>{{ $data->tgl_mulai }}</td>
                                <td>{{ $data->jml_benar }}/{{ $data->jml_salah }}</td>
                                <td>{{ $data->nilai_bobot_benar }}</td>
                                <td>{{ $data->nilai }}</td>

                                <?php 
                                $date1 = new DateTime($data->tgl_mulai);
                                $date2 = new DateTime($data->tgl_selesai);
                                $interval = $date1->diff($date2);

                                $waktu_mengerjakan = $interval->h  . ' jam ' . $interval->i . ' mnt ' . $interval->s . ' dtk' ;
                                ?>

                                <td>{{ $waktu_mengerjakan }}</td>
                                <td>{{ $data->peringkat }}/{{ $data->jml_peserta }}</td>
                                <td class="text-center"><a href="{{ url('hasilujian/jawaban/' . uuid_create_from_integer($data->id)) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a></td>
                            </tr>
                            <?php $i++ ?>
                            @empty

                            @endforelse

                            @if (!empty($h_ujian))
                                
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $h_ujian->tgl_mulai }}</td>
                                <td>{{ $h_ujian->jml_benar }}/{{ $h_ujian->jml_salah }}</td>
                                <td>{{ $h_ujian->nilai_bobot_benar }}</td>
                                <td>{{ $h_ujian->nilai }}</td>

                                <?php 
                                $date1 = new DateTime($h_ujian->tgl_mulai);
                                $date2 = new DateTime($h_ujian->tgl_selesai);
                                $interval = $date1->diff($date2);

                                $waktu_mengerjakan = $interval->h  . ' jam ' . $interval->i . ' mnt ' . $interval->s . ' dtk' ;
                                ?>

                                <td>{{ $waktu_mengerjakan }}</td>

                                <?php 
                                    $h_ujian_all = Hujian_orm::select('*', DB::raw('TIMESTAMPDIFF(SECOND, tgl_mulai, tgl_selesai) AS lama_pengerjaan'))
                                                        ->where(['ujian_id' =>  $h_ujian->ujian_id])
                                                        ->orderBy('nilai_bobot_benar', 'desc')
                                                        ->orderBy('lama_pengerjaan', 'asc')
                                                        ->get();
                                    
                                    $jml_peserta = $h_ujian_all->count();
                                    
                                    $peringkat = 1;
                                    foreach($h_ujian_all as $ujian){
                                        if($ujian->mahasiswa_id == $mhs->id_mahasiswa){
                                            break;
                                        }
                                        $peringkat++;
                                    }
                                ?>

                                <td>{{ $peringkat }}/{{ $jml_peserta }}</td>
                                <td class="text-center"><a href="{{ url('hasilujian/jawaban/' . uuid_create_from_integer($h_ujian->id)) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a></td>
                            </tr>

                            @endif

                        </tbody>
                    </table>
                    <!---- --->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection