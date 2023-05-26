@php
use Illuminate\Database\Eloquent\Builder;
@endphp
@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/charts/morris.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/npm/node_modules/featherlight/release/featherlight.min.css') }}" />
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/npm/node_modules/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/npm/node_modules/moment/min/moment-with-locales.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/charts/raphael-min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/charts/morris.min.js') }}"></script>
<script src="{{ asset('assets/npm/node_modules/featherlight/release/featherlight.min.js') }}"></script>
<script src="{{ asset('assets/npm/node_modules/inputmask/dist/jquery.inputmask.min.js') }}"></script>
<!-- END PAGE VENDOR JS-->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
function init_page_level(){
	
	ajaxcsrf();
	
	/**[START] CHART */
	//Get the context of the Chart canvas element we want to select
	
	let mmt = moment();

	Morris.Line({
		element: 'line-chart',
		data: {!! $chart_label_and_data !!},
		xkey: 'soal_ke',
		ykeys: ['waktu_menjawab'],
		labels: ['Waktu'],
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
			let soal_ke = parseInt(x.src.soal_ke);
			return "Ke-" + soal_ke;
		},
		hoverCallback: function(index, options, content, row) {
			// console.log(row.waktu_menjawab);
			// var hover = "<div class='morris-hover-row-label'>"+row.period+"</div><div class='morris-hover-point' style='color: #A4ADD3'><p color:black>"+row.park1+"</p></div>";
			// return hover;

			let duration = moment.duration(row.waktu_menjawab * 1000);

			let mnt = duration.minutes() + ' Mnt'; 
			let dtk = duration.seconds() + ' Dtk'; 
			let new_content = '<div class="morris-hover-row-label">Ke-'+ row.soal_ke +'</div>'
								+ '<div class="morris-hover-point" style="color: #689bc3">'
								+	'Waktu:  '+ mnt + ' ' + dtk
								+ '</div>';
			
			// let mnt = mmt.minutes(row.waktu_menjawab).format('mm') + ' Mnt'; 
			// let dtk = mmt.seconds(row.waktu_menjawab).format('ss') + ' Dtk'; 
			// let new_content = '<div class="morris-hover-row-label">Ke-'+ row.soal_ke +'</div>'
			// 					+ '<div class="morris-hover-point" style="color: #689bc3">'
			// 					+	'Waktu:  '+ mnt + ' ' + dtk
			// 					+ '</div>';

			return(new_content);
		},
		xLabels: "soal_ke",
	});
	/**[STOP] CHART */

	$(".inp_decimal").inputmask("decimal",{
        digits: 2,
        digitsOptional: false,
        radixPoint: ".",
        groupSeparator: ",",
        allowPlus: false,
        allowMinus: false,
        rightAlign: false,
        autoUnmask: true,
    });

	if(is_show_banner_ads){
		setTimeout(function () {
			$.featherlight('{{ asset('assets/imgs/tryout_udid_banner.png') }}');
			stop_ping = true;
		}, 2000);
	}

}
	
	
$(document).on('click','.btn_penjelasan',function(){
	toastr.warning('Fitur tsb sedang dalam pengembangan.', 'Mohon Maaf');
});


$(document).on('click','img.featherlight-image',function(){
    window.location = "{{ get_banner_ads_link() }}";
});

$(document).on('click','.btn_submit_nilai_essay',function(){
	let id = $(this).data('id');
	let nilai = $('#input_nilai_essay_' + id).val();
	$.ajax({
        url: "{{ site_url('hasilujian/ajax/submit_nilai_essay') }}",
        data: { 'id' : id, 'nilai' : nilai },
        type: 'POST',
        success: function (response) {
			Swal.fire({
				title: "Nilai Berhasil Disimpan",
				text: "Reload untuk melihat perubahan, atau lanjutkan dahulu",
				icon: "success",
				confirmButtonText: "Reload",
				cancelButtonText: "Lanjut",
				showCancelButton: true,
				allowOutsideClick: false,
				allowEscapeKey: false,
				confirmButtonColor: "#37bc9b",
        		cancelButtonColor: "#f6bb42",
			}).then(result => {
				if (result.value) {
					location.reload();
				}
			});
             
        },
		error: function(){
			Swal.fire({
				title: "Perhatian",
				text: "Terjadi kesalahan",
				icon: "warning"
			});
		}
    });
});
	
</script>
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_custom_css')
<style type="text/css">
.border-gray {
    border-color: #ccc;
}
.text-gray {
    border-color: #ccc;
}
</style>
@endpush

@section('content')
<section class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">{{ $subjudul }}</h4>
				<a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
			</div>
			<div class="card-content">
				<div class="card-body">
					<!---- --->
					<div class="row">
						<div class="col-md-12 mb-4">
							<?php $back_link = in_group('admin') ? site_url('hasilujian/detail/' . $h_ujian->m_ujian->id_ujian) : site_url('hasilujian/history/' . uuid_create_from_integer($h_ujian->mahasiswa_ujian_id)); ?>
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
									<td>{{ $h_ujian->mhs->nama }} / {{ $h_ujian->mhs->nim }}</td>
								</tr>
								<tr>
									<th>Ujian</th>
									<td>{{ $h_ujian->m_ujian->nama_ujian }}</td>
								</tr>
								<tr>
									<th>Jml Soal/Waktu</th>
									<td>{{ $h_ujian->m_ujian->jumlah_soal }}/{{ $h_ujian->m_ujian->waktu }} Menit
									</td>
								</tr>
								<tr>
									<th>Tgl Ujian</th>
									<td>{{ indo_date(strftime('%A, %d %B %Y', strtotime($h_ujian->tgl_mulai))) }}</td>
								</tr>
								<tr>
									<th>Waktu Ujian</th>
									<td>{{ strftime('%H:%M:%S', strtotime($h_ujian->tgl_mulai)) }} -
										{{ strftime('%H:%M:%S', strtotime($h_ujian->tgl_selesai)) }}</td>
								</tr>
							</table>
						</div>
						<div class="col-md-6">
							<table class="table w-100">
								<tr>
									<th>Lama Pengerjaan</th>
									<td>{{ $waktu_mengerjakan }}</td>
								</tr>
								<tr>
									<th>Topik</th>
									<td>@php($t = [])
										@foreach($h_ujian->m_ujian->topik AS $topik)
										@php($t[] = $topik->nama_topik)
										@php($t = array_unique($t))
										@endforeach
										{{ implode(',' , $t) }}
									</td>
								</tr>
								<tr>
									<th>Jml Benar/Salah</th>
									<td>{{ $h_ujian->jml_benar .'/'. $h_ujian->jml_salah }}</td>
								</tr>
								<tr>
									<th>Nilai/Bobot</th>
									<td>{{ number_format($h_ujian->nilai,2,'.', '') }}/{{ number_format($h_ujian->nilai_bobot_benar,2,'.', '') }}
									</td>
								</tr>
								<tr>
									<th>Peringkat</th>
									<td>
										<div class="badge badge-success round">{{ $peringkat }}</div> dari
										{{ $jml_peserta }} Peserta
									</td>
								</tr>
							</table>
						</div>
				</div>

				<div class="border border-success p-1 mt-1 mb-1" style="">
					<div class="height-350 w-100 mb-2">
						<ul class="list-inline text-center">
							<li>
								<h4 class="text-danger">Grafik Waktu Menjawab</h4>
							</li>
						</ul>
						<div id="line-chart" class="height-300"></div>
						<ul class="list-inline text-center">
							<li>
								<h6><i class="ft-circle danger"></i> Soal</h6>
							</li>
						</ul>
					</div>
				</div>

				<?php $nomor_pertanyaan = 1; ?>

				@foreach ($topik_ujian_list as $topik)
				
					<?php
                    $jawaban_ujian_list = $h_ujian->jawaban_ujian()->whereHas('soal',
                        function (Builder $query) use ($topik) {
                            $query->where('topik_id', $topik->id);
                        })
                    ->get()
                    ->sortBy('id');

							if ($jawaban_ujian_list->isEmpty()) {
							    continue;
							}

							?>

					<div class="alert bg-info w-100">
						<b>Topik : </b> {{ $topik->nama_topik }} ( Poin Topik : {{ $topik->poin_topik }} )
					</div>

					@foreach ($jawaban_ujian_list as $jawaban_ujian)
						<div class="row">
							<div class="col-md-12 col-sm-12">
								<div class="card border-top-danger box-shadow-0 border-bottom-danger">
									{{-- <div class="card-header">
												</div> --}}
									<div class="card-content">
										<div class="card-body pl-1 pr-1">
											<h4 class="card-title" data-id="{{ $jawaban_ujian->soal->id_soal }}">Pertanyaan : 
												<div class="badge badge-danger round">{{ $nomor_pertanyaan }}</div> 
												@if(!$jawaban_ujian->soal->is_bobot_per_jawaban)
												<span class="float-right">( Poin Soal : {{ $jawaban_ujian->soal->bobot_soal->nilai }} )</span>
												@endif
											</h4>
											<div class="pb-2">
												@if(!empty($jawaban_ujian->soal->section_id))
												<div id="preview_section" class="alert text-muted border border-info" style="">
													{!! $jawaban_ujian->soal->section->konten !!}
												</div>
												<br />
												@endif
												{!! $jawaban_ujian->soal->soal !!}
											</div>

											@if($jawaban_ujian->soal->tipe_soal == TIPE_SOAL_MCSA || $jawaban_ujian->soal->tipe_soal == TIPE_SOAL_MCMA)
											<div class="panel_jawaban">
												@php($alphabet = range('a', 'z'))
												@for($i = 0; $i < $jawaban_ujian->soal->jml_pilihan_jawaban; $i++)
													@php($abj = $alphabet[$i])
													@php($ABJ = strtoupper($abj))

													@if($jawaban_ujian->soal->tipe_soal == TIPE_SOAL_MCSA)
														@if(!$jawaban_ujian->soal->is_bobot_per_jawaban)
														<?php $text_color = ($ABJ == $jawaban_ujian->jawaban) ? (($ABJ == $jawaban_ujian->soal->jawaban) ? 'success' : 'danger') : (($ABJ == $jawaban_ujian->soal->jawaban) ? 'success' : 'grey'); ?>
														<div class="alert alert-light text-{{ $text_color }} {{ ($ABJ == $jawaban_ujian->jawaban) ? (($ABJ == $jawaban_ujian->soal->jawaban) ? 'border-success border-3' : 'border-danger border-3') : (($ABJ == $jawaban_ujian->soal->jawaban) ? 'border-success border-3' : 'border-grey') }}">
														@else
														<?php $text_color = (($ABJ == $jawaban_ujian->jawaban) && ! empty($jawaban_ujian->jawaban)) ? 'success' : 'grey'; ?>
														<div class="alert alert-light text-{{ $text_color }} {{ (($ABJ == $jawaban_ujian->jawaban) && !empty($jawaban_ujian->jawaban)) ? 'border-success border-3' : 'border-grey' }}">
														@endif
													@else
													{{-- JIKA MCMA --}}
														@php($jawaban_mcma = empty($jawaban_ujian->jawaban_mcma) ? [] : json_decode($jawaban_ujian->jawaban_mcma))
														@php($jawaban_mcma_kunci = json_decode($jawaban_ujian->soal->jawaban))
														@if(!$jawaban_ujian->soal->is_bobot_per_jawaban)
														<?php $text_color = (in_array($ABJ, $jawaban_mcma)) ? ((in_array($ABJ, $jawaban_mcma_kunci)) ? 'success' : 'danger') : ((in_array($ABJ, $jawaban_mcma_kunci)) ? 'success' : 'grey'); ?>
														<div class="alert alert-light text-{{ $text_color }} {{ (in_array($ABJ, $jawaban_mcma)) ? ((in_array($ABJ, $jawaban_mcma_kunci)) ? 'border-success border-3' : 'border-danger border-3') : ((in_array($ABJ, $jawaban_mcma_kunci)) ? 'border-success border-3' : 'border-grey') }}">
														@else
														<?php $text_color = ((in_array($ABJ, $jawaban_mcma)) && ! empty($jawaban_mcma)) ? 'success' : 'grey'; ?>
														<div class="alert alert-light text-{{ $text_color }} {{ ((in_array($ABJ, $jawaban_mcma)) && !empty($jawaban_mcma)) ? 'border-success border-3' : 'border-grey' }}">
														@endif
													@endif
														<span style="font-size: 1.5rem"
															class="float-left mr-1">{{ $ABJ }}. </span>
															<?php $opsi = 'opsi_' . $abj ?>
															{!! $jawaban_ujian->soal->$opsi !!}

														@if($jawaban_ujian->soal->is_bobot_per_jawaban)
														@php($opsi_bobot = 'opsi_'. $abj . '_bobot')
														<div class="border-1 border-red mt-2 text-danger" style="width: 250px; padding-left:10px; font-size: smaller; background-color: #ffb;">
															BOBOT : {{ $jawaban_ujian->soal->$opsi_bobot }}
														</div>
														@endif
													</div>
												@endfor
											</div>
											@endif
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-sm-12">
								<div class="card box-shadow-0 border-blue bg-transparent">
									{{-- <div class="card-header">
													</div> --}}
									<div class="card-content">
										<div class="card-body">

											@if (!empty($jawaban_ujian->waktu_jawab_soal))
											<h4 class="card-title">
												<?php
							                            $date1 = new DateTime($jawaban_ujian->waktu_buka_soal);
							$date2 = new DateTime($jawaban_ujian->waktu_jawab_soal);
							$interval = $date1->diff($date2);

							$waktu_menjawab = $interval->i . ' mnt ' . $interval->s . ' dtk';
							?>
												Waktu Menjawab : {{ $waktu_menjawab }}
											</h4>
											@endif

											<?php
                                            $badge_benar = '<div class="badge badge-success round">Benar</span></div>';
							$badge_salah = '<div class="badge badge-danger round">Salah</span></div>';
							$badge_ragu = '<div class="badge badge-warning round">Ragu</span></div>';
							?>

											@if($jawaban_ujian->soal->tipe_soal == TIPE_SOAL_MCSA)
											
												<h4 class="card-title">
													@if (!empty($jawaban_ujian->jawaban))
													Anda Menjawab : {{ $jawaban_ujian->jawaban }}
													@else
													Anda Tidak Menjawab
													@endif

													@if(!$jawaban_ujian->soal->is_bobot_per_jawaban)
													{!! ($jawaban_ujian->jawaban == $jawaban_ujian->soal->jawaban) ? $badge_benar : $badge_salah !!}
													@else
													{!! !empty($jawaban_ujian->jawaban) ? '' : $badge_salah !!}
													@endif

													{!! ($jawaban_ujian->status_jawaban == 'Y') ? $badge_ragu : '' !!}
												</h4>

											@elseif($jawaban_ujian->soal->tipe_soal == TIPE_SOAL_MCMA)
											
												<h4 class="card-title">
													@if (!empty($jawaban_ujian->jawaban_mcma))
													Anda Menjawab : {{ $jawaban_ujian->jawaban_mcma }}
													@else
													Anda Tidak Menjawab
													@endif

													@if(!$jawaban_ujian->soal->is_bobot_per_jawaban)
													@php($jawaban_mcma = empty($jawaban_ujian->jawaban_mcma) ? [] : json_decode($jawaban_ujian->jawaban_mcma))
													@php($jawaban_mcma_kunci = json_decode($jawaban_ujian->soal->jawaban))
													{!! (empty(array_diff($jawaban_mcma, $jawaban_mcma_kunci)) && empty(array_diff($jawaban_mcma_kunci, $jawaban_mcma))) ? $badge_benar : $badge_salah !!}
													@else
													{!! !empty($jawaban_ujian->jawaban_mcma) ? '' : $badge_salah !!}
													@endif

													{!! ($jawaban_ujian->status_jawaban == 'Y') ? $badge_ragu : '' !!}
												</h4>

											@elseif($jawaban_ujian->soal->tipe_soal == TIPE_SOAL_ESSAY)

												<h4 class="card-title">
													@if (!empty($jawaban_ujian->jawaban_essay))
													Anda Menjawab : 
													@else
													Anda Tidak Menjawab
													@endif
													{!! !empty($jawaban_ujian->jawaban_essay) ? '' : $badge_salah !!}
													{!! ($jawaban_ujian->status_jawaban == 'Y') ? $badge_ragu : '' !!}
												</h4>
												<div class="pb-2">
												{!! html_entity_decode($jawaban_ujian->jawaban_essay, ENT_QUOTES, 'UTF-8') !!}
												</div>
											@endif

										</div>
									</div>
									<div class="card-footer border-top-blue-grey border-top-lighten-5 text-muted">
										@if($jawaban_ujian->soal->tipe_soal == TIPE_SOAL_MCSA)

											<h4 class="card-title">
												Jawaban : {{ $jawaban_ujian->soal->is_bobot_per_jawaban ? '-' : $jawaban_ujian->soal->jawaban }} 
												<span class="float-right">
													@if(!$jawaban_ujian->soal->is_bobot_per_jawaban)
														( Poin : 
														{!! ($jawaban_ujian->jawaban == $jawaban_ujian->soal->jawaban) ? '<div
															class="badge badge-success round">'.
															number_format($jawaban_ujian->soal->bobot_soal->nilai * $topik->poin_topik,2,'.', '') .'</div>' :
														'<div class="badge badge-danger round">'. 0 .'</div>' !!} )
													@else
														@php( $opsi_bobot = 'opsi_'. strtolower($jawaban_ujian->jawaban) .'_bobot' )
														( Poin : 
														<div class="badge badge-success round">
															{{ number_format($jawaban_ujian->soal->$opsi_bobot * $topik->poin_topik,2,'.', '') }}
														</div> )
													@endif 
													</span>
											</h4>
											{{-- <button class="btn btn-info btn-block btn_penjelasan"
												data-id="{{ $jawaban_ujian->soal->id_soal }}">Minta Penjelasan
											</button> --}} 

										@elseif($jawaban_ujian->soal->tipe_soal == TIPE_SOAL_MCMA)

											<h4 class="card-title">
												Jawaban : {{ $jawaban_ujian->soal->is_bobot_per_jawaban ? '-' : $jawaban_ujian->soal->jawaban }}
												<span class="float-right">
													@php($jawaban_mcma = empty($jawaban_ujian->jawaban_mcma) ? [] : json_decode($jawaban_ujian->jawaban_mcma))
													@php($jawaban_mcma_kunci = json_decode($jawaban_ujian->soal->jawaban))
													@if(!$jawaban_ujian->soal->is_bobot_per_jawaban)
														( Poin : 
														{!! (empty(array_diff($jawaban_mcma, $jawaban_mcma_kunci)) && empty(array_diff($jawaban_mcma_kunci, $jawaban_mcma))) ? '<div
															class="badge badge-success round">'.
															number_format($jawaban_ujian->soal->bobot_soal->nilai * $topik->poin_topik,2,'.', '') .'</div>' :
														'<div class="badge badge-danger round">'. 0 .'</div>' !!} )
													@else
														<?php
							                $nilai_poin_total = 0;
							if (! empty($jawaban_mcma)) {
							    foreach ($jawaban_mcma as $j_mcma) {
							        $opsi_bobot = 'opsi_' . strtolower($j_mcma) . '_bobot';
							        $nilai_poin_total = $nilai_poin_total + $jawaban_ujian->soal->$opsi_bobot * $topik->poin_topik;
							    }
							}
							?>
														( Poin : 
														<div class="badge badge-success round">
															{{ number_format($nilai_poin_total,2,'.', '') }}
														</div> )
													@endif 
													</span>
											</h4>
											{{-- <button class="btn btn-info btn-block btn_penjelasan"
												data-id="{{ $jawaban_ujian->soal->id_soal }}">Minta Penjelasan
											</button> --}} 

										@elseif($jawaban_ujian->soal->tipe_soal == TIPE_SOAL_ESSAY)

											<h4 class="card-title">Jawaban : 
												@if(is_admin() && !$is_data_history)
													<span class="float-right">
														Point : 
														<div class="input-group">
															<input type="text" class="form-control input_nilai_essay inp_decimal" id="input_nilai_essay_{{ $jawaban_ujian->id }}" placeholder="" aria-describedby="btn_submit_nilai_essay_{{ $jawaban_ujian->id }}" value="{{ $jawaban_ujian->nilai_essay }}" {{ empty($jawaban_ujian->jawaban_essay) ? 'disabled="disabled"' : '' }}>
															<div class="input-group-append">
																<button class="btn btn-success btn_submit_nilai_essay" data-id="{{ $jawaban_ujian->id }}" type="button" id="btn_submit_nilai_essay_{{ $jawaban_ujian->id }}" {{ empty($jawaban_ujian->jawaban_essay) ? 'disabled="disabled"' : '' }}><i class="ft-check-circle"></i></button>
															</div>
														</div>
													</span>
												@else
													<span class="float-right">( Poin : <div class="badge badge-success round">{{ number_format($jawaban_ujian->nilai_essay, 2,'.', '') }}</div> )</span>
												@endif
											</h4>
											<div class="pb-2">
											{!! html_entity_decode($jawaban_ujian->soal->jawaban, ENT_QUOTES, 'UTF-8') !!}
											</div>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-12 col-sm-12">
								<div class="card box-shadow-0 border-success bg-transparent">
									{{-- <div class="card-header">
												</div> --}}
									<div class="card-content">
										<div class="card-body">
											<h4 class="card-title">Penjelasan :</h4>
											<div class="pb-2">
												@if (empty($jawaban_ujian->soal->penjelasan))
												<p>Maaf, belum ada penjelasan mengenai soal ini.</p>
												@else
												{!! $jawaban_ujian->soal->penjelasan !!}
												@endif
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php $nomor_pertanyaan++ ?>
					@endforeach
				@endforeach
				<!---- --->
			</div>
		</div>
	</div>
	</div>
</section>
@endsection