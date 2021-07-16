@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/icheck.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/custom.css') }}">
<!-- END PAGE LEVEL CSS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/plugins/select2-cascade.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/datatables.net-plugins/api/fnPagingInfo.js') }}"></script>
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/icheck/icheck.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
let bundle_dipilih = [];
let table;

let filter = {
	matkul_id: null,
	topik_id: null,
	gel: null,
	smt: null,
	tahun: null,
	bundle: null,
	is_reported: null,
};

function init_page_level(){
	ajaxcsrf();

	$('.select2').select2();

	$('.select2-container--default:first .select2-selection--single').css('background-color', '#ffc');

	$('#bundle').select2({
		placeholder: "Pilih bundle soal"
	});
	$('#show_length_number').select2({
		placeholder: "Pilih banyaknya soal"
	});
	$('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-red',
        radioClass: 'iradio_square-red',
    });

	let options = {};
	cascadLoading = new Select2Cascade($('#matkul_filter'), $('#topik_filter'), '{{ url('soal/ajax/get_topic_by_matkul/') }}?id=:parentId:&empty=0', options);
    cascadLoading.then( function(parent, child, items) {
        // Open the child listbox immediately
        // child.select2('open');
        // or Dump response data
        // console.log(items);
		child.prepend('<option value="">Semua Topik</option>');
		child.val("").trigger('change');
    });

	// $('#matkul_filter').val('null').trigger('change');

	table = $("#soal").DataTable({
		initComplete: function() {
		var api = this.api();
		$("#soal_filter input")
			.off(".DT")
			.on("keypress.DT", function(e) {
			if(e.which == 13) {
				api.search(this.value).draw();
				return false;
			}
			});
		},
		lengthChange: false,
		// lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
		dom:
		"<'row'<'col-lg-3'l><'col-lg-6 text-center'B><'col-lg-3'f>>" +
		"<'row'<'col-lg-12'tr>>" +
		"<'row'<'col-lg-5'i><'col-lg-7'p>>",
		buttons: [
		{
			text: 'Show Entries',
			action: function ( e, dt, node, config ) {
				$('#show_length_number').val(10).trigger('change');
				$('#show_length_number_custom').val('');
				$('#modal_page_length').modal('show');
			},
			className: 'btn-info'
		},
		{
			extend: "copy",
			exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8], format: {
				body: function ( data, columnIdx, rowIdx ) {
					if(rowIdx == 0)
					return (columnIdx + 1);
					else
					return data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
				}
			} }
		},
		{
			extend: "print",
			exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8], format: {
				body: function ( data, columnIdx, rowIdx ) {
					if(rowIdx == 0)
					return (columnIdx + 1);
					else
					return data;
				}
			} }
		},
		{
			extend: "excel",
			exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8], format: {
				body: function ( data, columnIdx, rowIdx ) {
					if(rowIdx == 0)
					return (columnIdx + 1);
					else
					return data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
				}
			} }
		},
		{
			extend: "pdf",
			exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8], format: {
				body: function ( data, columnIdx, rowIdx ) {
					if(rowIdx == 0)
					return (columnIdx + 1);
					else
					return data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
				}
			} }
		}
		],
		oLanguage: {
		sProcessing: "loading..."
		},
		processing: true,
		serverSide: true,
		ajax: {
		url: base_url + "soal/ajax/data",
		type: "POST"
		},
		columns: [
		{
			data: "id_soal",
			orderable: false,
			searchable: false
		},
		{
			data: "no_urut",
			// orderable: false,
			searchable: false
		},
		{ data: "nama_matkul" },
		{ data: "nama_topik" },
		{ data: "soal" },
		{ data: "bobot" },
		{ data: "bundle" },
		{ data: "created_at" },
		{ data: "oleh" }
		],
		columnDefs: [
		{
			targets: 0,
			data: "id_soal",
			render: function(data, type, row, meta) {
			return `<div class="text-center">
										<input name="checked[]" class="check" value="${data}" type="checkbox">
									</div>`;
			}
		},
		{
			targets: 6,
			data: "bundle",
			render: function(data, type, row, meta) {
			let str_return = '';
			if(data != null) {
				let data_array = data.split('---');
				data_array.forEach(function (item, index) {
				str_return += '<span class="badge bg-info">' + item + '</span> ';
				});
			}
			return str_return;
			}
		},
		{
			targets: 9,
			data: "id_soal",
			render: function(data, type, row, meta) {
			return `<div class="btn-group btn-group-sm" role="group" aria-label="">
									<a href="${base_url}soal/detail/${data}" class="btn btn-sm btn-info">
										<i class="fa fa-eye"></i>
									</a>
									<a href="${base_url}soal/edit/${data}" class="btn btn-sm btn-success">
										<i class="fa fa-edit"></i>
									</a>
									<button type="button" data-id="${data}" class="btn btn-sm btn-danger btn-report-soal">
										<i class="ft-alert-circle"></i>
									</button>
								</div>`;
			}
		}
		],
		order: [[2, "asc"], [3, "asc"], [7, "asc"]],
		rowId: function(data) {
		return 'dt_tr_' + data.id_soal;
		},
		createdRow: function (row, data, dataIndex) {
		if(data.is_reported == 1){
			$(row).addClass('bg-warning');
		}else{
			$(row).removeClass('bg-warning');
		}

		},
		rowCallback: function(row, data, iDisplayIndex) {
		// var info = this.fnPagingInfo();
		// var page = info.iPage;
		// var length = info.iLength;
		// var index = page * length + (iDisplayIndex + 1);
		// $("td:eq(1)", row).html(index);
		},
		// scrollX:        true,
		// fixedColumns:   {
		//     leftColumns: 3,
		// }
	});

	if(window.location.hash) {
		// Fragment exists
		let bundle = window.location.hash.substring(1);
		let array_bundle = bundle.split('---');
		if(array_bundle.length){
			filter.bundle = array_bundle[0];
			bundle_dipilih.push(array_bundle[0]);
			filter_soal();
			$('#bundle_filter_text').text(decodeURI(array_bundle[1]));
			$('#bundle_filter_div').show();

			// $('#bundle').val(bundle_dipilih).trigger('change');
		}
	}

}

$(document).on('change','#matkul_filter', function(){
	let matkul_id = $(this).val();
	filter.matkul_id = matkul_id;
	// let src = '{{ url('soal/ajax/data') }}';
	
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

$(document).on('change','#topik_filter', function(){
	let topik_id = $(this).val();
	filter.topik_id = topik_id;
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


$(document).on('change', '#is_reported_filter', function(){
	let is_reported = $(this).val();
	filter.is_reported = is_reported;
	filter_soal();
});

function filter_soal(){
	let src = '{{ url('soal/ajax/data') }}';
	let url = src + '?matkul_id=' + filter.matkul_id + '&topik_id=' + filter.topik_id + '&gel=' + filter.gel + '&smt=' + filter.smt + '&tahun=' + filter.tahun + '&is_reported=' + filter.is_reported  + '&bundle=' + filter.bundle ;
	table.ajax.url(url);
	reload_ajax();
}

$(document).on('click', '#btn_create_bundle', function(){
	if ($('table .check:checked').length == 0) {
		Swal.fire({
			title: "Gagal",
			text: "Tidak ada data yang dipilih",
			icon:"error"
		});
	} else {
		$('#bundle').val(bundle_dipilih).trigger('change');
		$('#is_ignore_bundle').iCheck("check");
		$('input[name="nama_bundle"]').val('');
		$('#modal_bundle').modal('show');
		// $("#bulk").attr("action", base_url + "soal/ajax/delete");
		// 	Swal.fire({
		// 	title: "Anda yakin?",
		// 	text: "Data akan dihapus!",
		// 	icon:"warning",
		// 	showCancelButton: true,
		// 	confirmButtonColor: "#3085d6",
		// 	cancelButtonColor: "#d33",
		// 	confirmButtonText: "Hapus!"
		// 	}).then(result => {
		// 	if (result.value) {
		// 		$("#bulk").submit();
		// 	}
		// });
	}
});

$(document).on('keypress','input[name="nama_bundle"]',function(e){
    if(e.which == 13) {
        let nama_bundle = $(this).val();
        if(nama_bundle.trim() == ''){
            Swal.fire({
                title: "Perhatian",
                text: "Nama bundle tidak boleh kosong",
                icon: "warning",
                confirmButtonText: "Ok",
            });
        }else{
            ajx_overlay(true);
            $.ajax({
                url: "{{ url('soal/ajax/save_bundle') }}",
                data: { 'nama_bundle' : $(this).val() },
                type: 'POST',
                success: function (response) {
                    if(response.stts == 'ok'){
                        var newopt = new Option(response.bundle.nama_bundle, response.bundle.id, true, true);
                        $('#bundle').append(newopt).trigger('change');
                        $('input[name="nama_bundle"]').val('');
                    }else{
                        Swal.fire({
                            title: "Perhatian",
                            text: "Kesalahan : " . response.msg,
                            icon: "warning",
                            confirmButtonText: "Ok",
                        });
                    }
                },
                error: function(){

                },
                complete: function(){
                    ajx_overlay(false);
                }
            });
        }
        e.preventDefault();
    }
});

$(document).on('click', '#submit_bundle', function(){
	let selected_bundle = $('#bundle').val();
	let selected_soal = [];
	$('table .check:checked').each(function(){
		let soal_id = $(this).val();
		selected_soal.push(soal_id);
	});
	let is_ignore_bundle = $('#is_ignore_bundle').is(":checked");
	if(selected_soal.length){
		if(!selected_bundle.length){
			// JIKA TIDAK MEMILIH BUNDLE
			if(!is_ignore_bundle){
				Swal.fire({
					title: "Anda belum memilih bundle?",
					text: "Semua bundle akan hilang pada soal tsb",
					icon:"warning",
					showCancelButton: true,
					confirmButtonColor: "#3085d6",
					cancelButtonColor: "#d33",
					confirmButtonText: "Ok"
				}).then(result => {
					if (result.value) {
						proses_soal_bundle(selected_bundle, selected_soal, is_ignore_bundle);
					}
				});
			}else{
				$('#modal_bundle').modal('hide');
			}
		}else{
			proses_soal_bundle(selected_bundle, selected_soal, is_ignore_bundle);
		}
	}
});

const proses_soal_bundle = (selected_bundle, selected_soal, is_ignore_bundle) => {
	ajx_overlay(true);
	return $.ajax({
			url: "{{ url('soal/ajax/asign_soal_bundle') }}",
			data: { 'selected_bundle' : JSON.stringify(selected_bundle), 'selected_soal' : JSON.stringify(selected_soal), 'is_ignore_bundle' : is_ignore_bundle },
			type: 'POST',
			success: function (response) {
				if(response.stts == 'ok'){
					Swal.fire({
						title: "Perhatian",
						text: "Soal berhasil di-asign dengan bundle",
						icon: "success",
						confirmButtonText: "Ok",
					});
					reload_ajax();
				}else{
					Swal.fire({
						title: "Perhatian",
						text: "Kesalahan : " . response.msg,
						icon: "warning",
						confirmButtonText: "Ok",
					});
				}
			},
			error: function(){

			},
			complete: function(){
				ajx_overlay(false);
				$('#modal_bundle').modal('hide');
			}
		});
};

$(document).on('click', '#btn_close_alert_bundle', function(){

	bundle_dipilih = [];
	filter.bundle = null;
	filter_soal();

});

$(document).on('click', '.btn-report-soal', function(){

	let id_soal = $(this).data('id');
	ajx_overlay(true);
	$.ajax({
		url: "{{ url('soal/ajax/report_soal') }}",
		data: { 'id_soal': id_soal },
		type: 'POST',
		success: function (response) {
			if(response.stts == 'ok'){
				// if(response.is_reported)
				// 	$('#dt_tr_' + response.id_soal).addClass('bg-warning');
				// else
				// 	$('#dt_tr_' + response.id_soal).removeClass('bg-warning');

				reload_ajax();
			}else{
				Swal.fire({
					title: "Perhatian",
					text: "Kesalahan : " . response.msg,
					icon: "warning",
					confirmButtonText: "Ok",
				});
			}
		},
		error: function(){

			Swal.fire({
					title: "Perhatian",
					text: "Terjadi kesalahan/Anda bukan admin",
					icon: "warning",
					confirmButtonText: "Ok",
				});

		},
		complete: function(){
			ajx_overlay(false);
			$('#modal_bundle').modal('hide');
		}
	});

});

$(document).on('click', '#submit_show_length', function(){
  let jml_data = $('#show_length_number').val();
  let jml_data_custom = $('#show_length_number_custom').val();

  let jml_data_valid = jml_data_custom ? jml_data_custom : jml_data ;
  table.page.len( jml_data_valid ).draw();
  $('#modal_page_length').modal('hide');

});

</script>
<script src="{{ asset('assets/dist/js/app/soal/index.js') }}"></script>
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_level_css')
<!-- START PAGE LEVEL JS-->
<style type="text/css">
.select2-selection--multiple .select2-search__field{
  width:100%!important;
}

</style>
<!-- END PAGE LEVEL CSS-->
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
		<div class="row pb-1">
        	<div class="col-lg-8 col-sm-12">
				<a href="{{ url('soal/add') }}" class="btn btn-success btn-flat btn-sm"><i class="fa fa-plus"></i> Buat Soal</a>
				<a href="{{ url('soal/import') }}" class="btn btn-sm btn-flat btn-success"><i class="fa fa-upload"></i> Import</a>
				@if(is_admin())
				<button class="btn btn-sm btn-flat btn-success" id="btn_create_bundle"><i class="ft-link"></i> Jadikan Bundle</button>
				@endif
				<button type="button" onclick="reload_ajax()" class="btn btn-flat btn-sm btn-outline-secondary"><i class="fa fa-refresh"></i> Reload</button>
			</div>
			<div class="col-lg-4 col-sm-12">
				<div class="pull-right">
					<button type="button" onclick="bulk_delete()" class="btn btn-flat btn-sm btn-danger"><i class="fa fa-trash"></i> Bulk Delete</button>
				</div>
			</div>
			<div class="col-lg-12 col-sm-12" id="bundle_filter_div" style="display: none">
				<div class="alert mb-0 mt-1 border-red text-danger" style="background-color: #fdf5b2" role="alert">
					<button type="button" id="btn_close_alert_bundle" class="close pr-1" data-dismiss="alert" aria-label="Close" style="padding-top: 3px; float: left;">
						<span aria-hidden="true">&times;</span>
					  </button>
					Menampilkan soal dengan bundle : <span id="bundle_filter_text" class="badge bg-info"></span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 text-center">
				<div class="row">
					<div class="col-sm-12">
						<div class="row">
							<div class="col-lg-2 col-sm-12">
								<select id="is_reported_filter" class="form-control select2" style="">
									<option value="null">Semua Soal</option>
									<option value="{{ REPORTED_SOAL }}">Reported</option>
									<option value="{{ NON_REPORTED_SOAL }}">Non Reported</option>
								</select>
							</div>
							<div class="col-lg-2 col-sm-12">
								<select id="matkul_filter" class="form-control select2" style="">
									<option value="null">Semua Matkul</option>
									<?php foreach ($matkul as $m) :?>
										<option value="<?=$m->id_matkul?>"><?=$m->nama_matkul?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-lg-2 col-sm-12">
								<select id="topik_filter" class="form-control select2" style="">
									<option value="null">Semua Topik</option>
									<?php foreach ($topik as $t) :?>
										<option value="<?=$t->id?>"><?=$t->nama_topik?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-lg-2 col-sm-12">
								<select id="gel_filter" class="form-control select2" style="">
									<option value="null">Semua Gel</option>
									<?php foreach ($gel as $g) :?>
										<option value="{{ $g }}">{{ 'GEL-'. $g }}</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-lg-2 col-sm-12">
								<select id="smt_filter" class="form-control select2" style="">
									<option value="null">Semua Smt</option>
									<?php foreach ($smt as $s) :?>
										<option value="{{ $s }}">{{ 'SMT-'. $s }}</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-lg-2 col-sm-12">
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
							<th>No Urut</th>
							<th>Materi Ujian</th>
							<th>Topik</th>
							<th>Soal</th>
							<th>Bobot</th>
							<th>Bundle</th>
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

<div class="modal" id="modal_bundle">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pilihan Bundle</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <fieldset class="form-group" style="padding: 10px; border: 1px solid #ccc;">
					<legend class="col-form-label col-sm-12" style="border: 1px solid #ccc; background-color: #d4ffd7;">Bundle Soal</legend>
					<div class="form-group">
						<div class="">
							<input type="checkbox" class="icheck" value="ignore" name="is_ignore_bundle" id="is_ignore_bundle" checked="checked" /><label for="is_ignore_bundle" style="margin: 0.6em">Ignore existing bundle</label>
						</div>
					</div>
					<div class="form-group">
						<select name="bundle[]" id="bundle" class="form-control"
							style="width:100%!important" multiple="multiple">
							@foreach ($bundle_avail as $bundle)
							<option value="{{ $bundle->id }}" {{ in_array($bundle->id, $bundle_selected) ? "selected" : "" }}>{{ $bundle->nama_bundle }}</option>    
							@endforeach
						</select>
						<input placeholder="Tambah bundle soal disini" type="text" value="" class="form-control" name="nama_bundle" style="margin-top: 3px">
					</div>
					<div class="form-group pull-right">
						<button type="button" id="submit_bundle" class="btn btn-flat btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
					</div>
				</fieldset>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="modal_page_length">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Show Entries</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <fieldset class="form-group" style="padding: 10px; border: 1px solid #ccc;">
					<legend class="col-form-label col-sm-12" style="border: 1px solid #ccc; background-color: #d4ffd7;">Jumlah Soal</legend>
					<div class="form-group">
						<select name="show_length_number" id="show_length_number" class="form-control"
							style="width:100%!important">
							@php($show_length_number = [10 => 10, 20 => 20, 50 => 50, 100 => 100, -1 => 'Semua'])
							@foreach ($show_length_number as $number => $text)
							<option value="{{ $number }}">{{ $text }}</option>    
							@endforeach
						</select>
						<input placeholder="Custom number" type="text" value="" class="form-control" name="show_length_number_custom" id="show_length_number_custom" style="margin-top: 3px">
					</div>
					<div class="form-group pull-right">
						<button type="button" id="submit_show_length" class="btn btn-flat btn-outline-primary"><i class="fa fa-eye"></i> Show</button>
					</div>
				</fieldset>
            </div>
        </div>
    </div>
</div>

@endsection

