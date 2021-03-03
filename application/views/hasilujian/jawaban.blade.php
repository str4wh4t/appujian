@php
use Illuminate\Database\Eloquent\Builder;
@endphp
@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
<link rel="stylesheet" type="text/css"
	href="{{ asset('assets/template/robust/app-assets/vendors/css/extensions/toastr.css') }}">
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
	function init_page_level(){
		
		ajaxcsrf();
		
	}
	
	
	$(document).on('click','.btn_penjelasan',function(){
		toastr.warning('Fitur tsb sedang dalam pengembangan.', 'Mohon Maaf');
	});
	
</script>
<!-- END PAGE LEVEL JS-->
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
							<?php $back_link = in_group('admin') ? site_url('hasilujian/detail/' . $h_ujian->m_ujian->id_ujian) : site_url('hasilujian/history/' . uuid_create_from_integer($h_ujian->mahasiswa_ujian_id)) ; ?>
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
									<th>Jumlah Soal/Waktu</th>
									<td>{{ $h_ujian->m_ujian->jumlah_soal }} Soal / {{ $h_ujian->m_ujian->waktu }} Menit
									</td>
								</tr>
								<tr>
									<th>Tgl Ujian</th>
									<td>{{ strftime('%A, %d %B %Y', strtotime($h_ujian->tgl_mulai)) }}</td>
								</tr>
								<tr>
									<th>Waktu Ujian</th>
									<td>{{ strftime('%H:%M:%S', strtotime($h_ujian->tgl_mulai)) }} -
										{{ strftime('%H:%M:%S', strtotime($h_ujian->tgl_selesai)) }}
										({{ $waktu_mengerjakan }})</td>
								</tr>
							</table>
						</div>
						<div class="col-md-6">
							<table class="table w-100">
								<tr>
									<th>Mata Kuliah</th>
									<td>{{ $h_ujian->m_ujian->matkul->nama_matkul }}</td>
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

				<?php $i = 1; ?>

				@foreach ($h_ujian->m_ujian->topik as $topik)
				<div class="alert bg-info w-100"><b>Topik : </b> {{ $topik->nama_topik }} ( Poin Topik :
					{{ $topik->poin_topik }} )</div>

				<?php
							$jawaban_ujian_list = $h_ujian->jawaban_ujian()->whereHas('soal', 
							function (Builder $query) use($topik) {
								$query->where('topik_id', $topik->id);
							})->get();
							?>

				@foreach ($jawaban_ujian_list as $jawaban_ujian)
				<div class="row">
					<div class="col-md-8 col-sm-12">
						<div class="card border-top-danger box-shadow-0 border-bottom-danger">
							{{-- <div class="card-header">
										</div> --}}
							<div class="card-content collapse show">
								<div class="card-body">
									<h4 class="card-title">Pertanyaan : {{ $i }} <span class="float-right">( Poin
											Soal : {{ $jawaban_ujian->soal->bobot_soal->nilai }} )</span></h4>
									<div class="">{!! $jawaban_ujian->soal->soal !!}</div>
									<div
										class="alert alert-light border-success {{ ('A' == $jawaban_ujian->jawaban) ? (($jawaban_ujian->jawaban == $jawaban_ujian->soal->jawaban) ? 'bg-success' : 'bg-danger') :  (('A' == $jawaban_ujian->soal->jawaban) ? 'bg-success' : '') }}">
										<?php $text_color = ('A' == $jawaban_ujian->jawaban) ? 'white' : (('A' == $jawaban_ujian->soal->jawaban) ? 'white' : 'success');  ?>
										<span style="font-size: 1.5rem"
											class="float-left mr-1 text-{{ $text_color }}">A. </span>{!!
										$jawaban_ujian->soal->opsi_a !!}
									</div>
									<div
										class="alert alert-light border-success {{ ('B' == $jawaban_ujian->jawaban) ? (($jawaban_ujian->jawaban == $jawaban_ujian->soal->jawaban) ? 'bg-success' : 'bg-danger') :  (('B' == $jawaban_ujian->soal->jawaban) ? 'bg-success' : '') }}">
										<?php $text_color = ('B' == $jawaban_ujian->jawaban) ? 'white' : (('B' == $jawaban_ujian->soal->jawaban) ? 'white' : 'success');  ?>
										<span style="font-size: 1.5rem"
											class="float-left mr-1 text-{{ $text_color }}">B. </span>{!!
										$jawaban_ujian->soal->opsi_b !!}
									</div>
									<div
										class="alert alert-light border-success {{ ('C' == $jawaban_ujian->jawaban) ? (($jawaban_ujian->jawaban == $jawaban_ujian->soal->jawaban) ? 'bg-success' : 'bg-danger') :  (('C' == $jawaban_ujian->soal->jawaban) ? 'bg-success' : '') }}">
										<?php $text_color = ('C' == $jawaban_ujian->jawaban) ? 'white' : (('C' == $jawaban_ujian->soal->jawaban) ? 'white' : 'success');  ?>
										<span style="font-size: 1.5rem"
											class="float-left mr-1 text-{{ $text_color }}">C. </span>{!!
										$jawaban_ujian->soal->opsi_c !!}
									</div>
									<div
										class="alert alert-light border-success {{ ('D' == $jawaban_ujian->jawaban) ? (($jawaban_ujian->jawaban == $jawaban_ujian->soal->jawaban) ? 'bg-success' : 'bg-danger') :  (('D' == $jawaban_ujian->soal->jawaban) ? 'bg-success' : '') }}">
										<?php $text_color = ('D' == $jawaban_ujian->jawaban) ? 'white' : (('D' == $jawaban_ujian->soal->jawaban) ? 'white' : 'success');  ?>
										<span style="font-size: 1.5rem"
											class="float-left mr-1 text-{{ $text_color }}">D. </span>{!!
										$jawaban_ujian->soal->opsi_d !!}
									</div>
									<div
										class="alert alert-light border-success {{ ('E' == $jawaban_ujian->jawaban) ? (($jawaban_ujian->jawaban == $jawaban_ujian->soal->jawaban) ? 'bg-success' : 'bg-danger') :  (('E' == $jawaban_ujian->soal->jawaban) ? 'bg-success' : '') }}">
										<?php $text_color = ('E' == $jawaban_ujian->jawaban) ? 'white' : (('E' == $jawaban_ujian->soal->jawaban) ? 'white' : 'success');  ?>
										<span style="font-size: 1.5rem"
											class="float-left mr-1 text-{{ $text_color }}">E. </span>{!!
										$jawaban_ujian->soal->opsi_e !!}
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-12">
						<div class="card box-shadow-0 border-blue bg-transparent">
							{{-- <div class="card-header">
											</div> --}}
							<div class="card-content collapse show">
								<div class="card-body">
									<?php 
													$badge_benar = '<div class="badge badge-success round"><i class="fa fa-check font-medium-2"></i><span> Benar</span></div>';    
													$badge_salah = '<div class="badge badge-danger round"><i class="fa fa-times font-medium-2"></i><span> Salah</span></div>';    
													?>
									<h4 class="card-title">
										@if (!empty($jawaban_ujian->jawaban))
										Anda Menjawab : {{ $jawaban_ujian->jawaban }}
										@else
										Anda Tidak Menjawab
										@endif
										{!! ($jawaban_ujian->jawaban == $jawaban_ujian->soal->jawaban) ?
										$badge_benar : $badge_salah !!}
									</h4>
									<h4 class="card-title">Penjelasan :</h4>
									<div class="">
										@if (empty($jawaban_ujian->soal->penjelasan))
										<p>Maaf, belum ada penjelasan mengenai soal ini, anda dapat meminta
											penjelasan dengan klik tombol minta penjelasan dibawah.</p>
										@else
										{{ $jawaban_ujian->soal->penjelasan }}
										@endif
									</div>
								</div>
							</div>
							<div class="card-footer border-top-blue-grey border-top-lighten-5 text-muted">
								<h4 class="card-title">
									Jawaban : {{ $jawaban_ujian->soal->jawaban }} <span class="float-right">( Poin :
										{!! ($jawaban_ujian->jawaban == $jawaban_ujian->soal->jawaban) ? '<div
											class="badge badge-success round">'.
											$jawaban_ujian->soal->bobot_soal->nilai * $topik->poin_topik .'</div>' :
										'<div class="badge badge-danger round">'. 0 .'</div>' !!} )</span>
								</h4>
								<button class="btn btn-info btn-block btn_penjelasan"
									data-id="{{ $jawaban_ujian->soal->id_soal }}">Minta Penjelasan</button>
							</div>
						</div>
					</div>
				</div>
				<?php $i++ ?>
				@endforeach
				@endforeach
				<!---- --->
			</div>
		</div>
	</div>
	</div>
</section>
@endsection