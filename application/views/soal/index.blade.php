@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/datatables.net-plugins/api/fnPagingInfo.js') }}"></script>
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

	let filter = {
		matkul_id: null,
		gel: null,
		smt: null,
		tahun: null,
	};

	function init_page_level(){

		$('.select2').select2();

		// $('#matkul_filter').val('null').trigger('change');

	}

	$(document).on('change','#matkul_filter', function(){
		let matkul_id = $(this).val();
		filter.matkul_id = matkul_id;
		// let src = '{{ site_url('soal/ajax/data') }}';
		
		// let url ;
		// if(id_matkul !== 'all'){
		// 	let src2 = src + '/?id=' + id_matkul;
		// 	url = $(this).prop('checked') === true ? src : src2;
		// }else{
		// 	url = src;
		// }

		// table.ajax.url(url).load();

		filter_soal();
	});

	$(document).on('change','#gel_filter', function(){
		let gel = $(this).val();
		filter.gel = gel;
		filter_soal();
	});

	$(document).on('change','#smt_filter', function(){
		let smt = $(this).val();
		filter.smt = smt;
		filter_soal();
	});

	$(document).on('change','#tahun_filter', function(){
		let tahun = $(this).val();
		filter.tahun = tahun;
		filter_soal();
	});

	function filter_soal(){
		let src = '{{ site_url('soal/ajax/data') }}';
		let url = src + '?matkul_id=' + filter.matkul_id + '&gel=' + filter.gel + '&smt=' + filter.smt + '&tahun=' + filter.tahun ;
		table.ajax.url(url);
		reload_ajax();
	}

</script>
<script src="{{ asset('assets/dist/js/app/soal/index.js') }}"></script>
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
	<div class="col-lg-12 pb-1">
		<div class="row">
        	<div class="col-sm-4">
				<a href="{{ site_url('soal/add') }}" class="btn btn-outline-primary btn-flat btn-sm"><i class="fa fa-plus"></i> Buat Soal</a>
				<a href="{{ site_url('soal/import') }}" class="btn btn-sm btn-flat btn-success"><i class="fa fa-upload"></i> Import</a>
				<button type="button" onclick="reload_ajax()" class="btn btn-flat btn-sm btn-outline-secondary"><i class="fa fa-refresh"></i> Reload</button>
			</div>
			<div class="form-group col-sm-4 text-center">

				<select id="matkul_filter" class="form-control select2" style="width:100% !important">
					<option value="null">Semua Matkul</option>
					<?php foreach ($matkul as $m) :?>
						<option value="<?=$m->id_matkul?>"><?=$m->nama_matkul?></option>
					<?php endforeach; ?>
				</select>

			</div>
			<div class="col-sm-4">
				<div class="pull-right">
					<button type="button" onclick="bulk_delete()" class="btn btn-flat btn-sm btn-danger"><i class="fa fa-trash"></i> Bulk Delete</button>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 text-center">
				<div class="row">
					<div class="offset-lg-2 col-lg-8 col-sm-12">
						<div class="row">
							<div class="col-lg-4 col-sm-12">
								<select id="gel_filter" class="form-control select2" style="">
									<option value="null">Semua Gel</option>
									<?php foreach ($gel as $g) :?>
										<option value="{{ $g }}">{{ 'GEL-'. $g }}</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-lg-4 col-sm-12">
								<select id="smt_filter" class="form-control select2" style="">
									<option value="null">Semua Smt</option>
									<?php foreach ($smt as $s) :?>
										<option value="{{ $s }}">{{ 'SMT-'. $s }}</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-lg-4 col-sm-12">
								<select id="tahun_filter" class="form-control select2" style="">
									<option value="null">Semua Tahun</option>
									<?php foreach ($tahun as $t) :?>
										<option value="{{ $t }}">{{ $t }}</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-12">
		<?=form_open('', array('id'=>'bulk'))?>
			<div class="table-responsive pb-2">
				<table id="soal" class="table table-striped table-bordered table-hover w-100">
					<thead>
						<tr>
							<th class="text-center">
								<input type="checkbox" class="select_all">
							</th>
							<th>Urut</th>
							<th>Materi Ujian</th>
							<th>Topik</th>
							<th>Soal</th>
							<th>Bobot</th>
							<th>Tgl Dibuat</th>
							<th>Oleh</th>
							<th>Aksi</th>
						</tr>
					</thead>
				</table>
			</div>
		<?=form_close();?>
	</div>
</div>

<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>
@endsection

