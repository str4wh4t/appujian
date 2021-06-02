@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/icheck.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/custom.css') }}">
<!-- END PAGE LEVEL CSS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/datatables.net-plugins/api/fnPagingInfo.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/icheck/icheck.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
{{--let id_dosen = '{{ $dosen->id_dosen }}';--}}

let status_ujian ;
let paket_dipilih = [];
let table;

let filter = {
	paket: null,
};

function init_page_level(){
    $('.select2').select2({
        width: '100%',
    });

	$('#paket').select2({
		placeholder: "Pilih paket ujian"
	});

	$('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-red',
        radioClass: 'iradio_square-red',
    });

    table = $("#ujian").DataTable({
        initComplete: function () {
            var api = this.api();
            $('#ujian_filter input')
                .off('.DT')
                .on("keypress.DT", function(e) {
                  if(e.which == 13) {
                    api.search(this.value).draw();
                    return false;
                  }
                });
        },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom:
          "<'row'<'col-lg-3'l><'col-lg-6 text-center'B><'col-lg-3'f>>" +
          "<'row'<'col-lg-12'tr>>" +
          "<'row'<'col-lg-5'i><'col-lg-7'p>>",
        buttons: [
          {
            extend: "copy",
            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9, 10], format: {
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
            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9, 10], format: {
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
            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9, 10], format: {
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
            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9, 10], format: {
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
            "url": "{{ url('ujian/ajax/data') }}",
            "type": "POST",
            data: function (d) {
                d.status_ujian = status_ujian;
            },
        },
        columns: [
            {
                "data": "id_ujian",
                "orderable": false,
                "searchable": false
            },
            { "data": 'nama_ujian' },
            { "data": 'status_ujian' },
            { "data": 'jumlah_soal' },
            { "data": 'tgl_mulai' },
            { "data": 'terlambat' },
            { "data": 'waktu' },
            { "data": 'jenis' },
            { "data": 'oleh' },
            {
                "data": 'token',
                "orderable": false
            },
        ],
        columnDefs: [
            {
                "targets": 0,
                "data": "id_ujian",
                "render": function (data, type, row, meta) {
                    return `<div class="text-center">
									<input name="checked[]" class="check" value="${data}" type="checkbox">
								</div>`;
                }
            },
            {
                "targets": 1,
                "render": function (data, type, row, meta) {
                    let str_return = '';
                    if(row.paket != "") {
                        let paket  = row.paket ;
                        str_return = '<hr/>';
                        let data_array = paket.split('---');
                        data_array.forEach(function (item, index) {
                            str_return += '<span class="badge bg-danger">' + item + '</span> ';
                        });
                    }
                    return data + str_return;
                },
            },
            {
                "targets": 2,
                "render": function (data, type, row, meta) {
                    if(data == 'active'){
                        return `<span class="badge badge-success">
                                        ${data}
                                    </span>`;
                    }else{
                        if(data == 'expired'){
                            return `<span class="badge badge-warning">
                                            ${data}
                                        </span>`;
                        }else{
                            return `<span class="badge badge-danger">
                                            ${data}
                                        </span>`;
                        }
                    }
                }
            },
            {
                "targets": 9,
                "data": "token",
                "render": function (data, type, row, meta) {
                    if(row.pakai_token == '0'){
                        return '&nbsp';
                    }else {
                        return `<div class="text-center">
								<b><span style="padding-bottom: 5px; border-bottom: 3px dashed #f00;">${data}</span></b>
								</div>`;
                    }
                }
            },
            {
                "targets": 10,
                "data": "aksi",
            },
        ],
        order: [
            [1, 'asc'],
        ],
        rowId: function (a) {
            return a;
        },
        rowCallback: function (row, data, iDisplayIndex) {
            // var info = this.fnPagingInfo();
            // var page = info.iPage;
            // var length = info.iLength;
            // var index = page * length + (iDisplayIndex + 1);
            // $('td:eq(1)', row).html(index);
        },
        // scrollX:        true,
        // fixedColumns:   {
        //     leftColumns: 2,
        // }
    });

    if(window.location.hash) {
		// Fragment exists
		let paket = window.location.hash.substring(1);
		let array_paket = paket.split('---');
		if(array_paket.length){
			filter.paket = array_paket[0];
			paket_dipilih.push(array_paket[0]);
			filter_soal();
			$('#paket_filter_text').text(decodeURI(array_paket[1]));
			$('#paket_filter_div').show();
		}
	}

    $('#status_ujian').val('active').trigger('change');
    status_ujian = $('#status_ujian').val();
}

function filter_soal(){
	let src = '{{ url('ujian/ajax/data') }}';
	let url = src + '?paket=' + filter.paket ;
	table.ajax.url(url);
	reload_ajax();
}

$(document).on('change','#status_ujian',function () {
    status_ujian = $(this).val();
    table.ajax.reload();
});

$(document).on('click', '#btn_create_paket', function(){
	if ($('table .check:checked').length == 0) {
		Swal.fire({
			title: "Gagal",
			text: "Tidak ada data yang dipilih",
			icon:"error"
		});
	} else {
		$('#paket').val(paket_dipilih).trigger('change');
		$('#is_ignore_paket').iCheck("check");
		$('input[name="nama_paket"]').val('');
		$('#modal_paket').modal('show');
	}
});

$(document).on('click', '#submit_paket', function(){
	let selected_paket = $('#paket').val();
	let selected_ujian = [];
	$('table .check:checked').each(function(){
		let ujian_id = $(this).val();
		selected_ujian.push(ujian_id);
	});
	let is_ignore_paket = $('#is_ignore_paket').is(":checked");
	if(selected_ujian.length){
		if(!selected_paket.length){
			// JIKA TIDAK MEMILIH PAKET
			if(!is_ignore_paket){
				Swal.fire({
					title: "Anda belum memilih paket?",
					text: "Semua paket akan hilang pada ujian tsb",
					icon:"warning",
					showCancelButton: true,
					confirmButtonColor: "#3085d6",
					cancelButtonColor: "#d33",
					confirmButtonText: "Ok"
				}).then(result => {
					if (result.value) {
						proses_soal_paket(selected_paket, selected_ujian, is_ignore_paket);
					}
				});
			}else{
				$('#modal_paket').modal('hide');
			}
		}else{
			proses_soal_paket(selected_paket, selected_ujian, is_ignore_paket);
		}
	}
});

const proses_soal_paket = (selected_paket, selected_ujian, is_ignore_paket) => {
	ajx_overlay(true);
	return $.ajax({
        url: "{{ url('ujian/ajax/asign_ujian_paket') }}",
        data: { 'selected_paket' : JSON.stringify(selected_paket), 'selected_ujian' : JSON.stringify(selected_ujian), 'is_ignore_paket' : is_ignore_paket },
        type: 'POST',
        success: function (response) {
            if(response.stts == 'ok'){
                Swal.fire({
                    title: "Perhatian",
                    text: "Ujian berhasil di-asign dengan paket",
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
            $('#modal_paket').modal('hide');
        }
    });
};

$(document).on('click', '#btn_close_alert_paket', function(){

    paket_dipilih = [];
    filter.paket = null;
    filter_soal();

});

</script>
<script src="{{ asset('assets/dist/js/app/ujian/master.js') }}"></script>
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
<div class="row pb-3">
    <div class="col-lg-12 pb-1">
        <div class="row">
            <div class="col-sm-4">
            @if(is_admin())
                <a href="{{ url('ujian/add') }}" class="btn btn-outline-primary btn-sm btn-flat"><i class="fa fa-file-text-o"></i> Ujian Baru</a>
            @endif
            @if( APP_TYPE == 'tryout' )
                @if(is_admin())
                <button class="btn btn-sm btn-flat btn-success" id="btn_create_paket"><i class="ft-link"></i> Jadikan Paket</button>
                @endif
            @endif
            <button type="button" onclick="reload_ajax()" class="btn btn-sm btn-flat btn-outline-secondary"><i class="fa fa-refresh"></i> Reload</button>
            </div>
            <div class="offset-lg-4 col-sm-4">
                @if(is_admin())
                <div class="pull-right">
                    <button type="button" onclick="bulk_delete()" class="btn btn-sm btn-flat btn-danger"><i class="fa fa-trash"></i> Bulk Delete</button>
                </div>
                @endif
            </div>
            <div class="col-lg-12 col-sm-12" id="paket_filter_div" style="display: none">
				<div class="alert mb-0 mt-1 border-red text-danger" style="background-color: #fdf5b2" role="alert">
					<button type="button" id="btn_close_alert_paket" class="close pr-1" data-dismiss="alert" aria-label="Close" style="padding-top: 3px; float: left;">
						<span aria-hidden="true">&times;</span>
					  </button>
					Menampilkan soal dengan paket : <span id="paket_filter_text" class="badge bg-danger"></span>
				</div>
			</div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="row">
            <div class="offset-lg-4 col-sm-4" style="text-align: center">
                <select name="status_ujian" id="status_ujian" class="select2" >
                    <option value="active">ACTIVE</option>
                    <option value="close">CLOSE</option>
                    <option value="expired">EXPIRED</option>
                    <option value="semua">SEMUA</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
	<?=form_open('ujian/delete', array('id'=>'bulk'))?>
    <div class="table-responsive pb-2">
        <table id="ujian" class="table table-striped table-bordered table-hover w-100">
        <thead>
            <tr>
				<th class="text-center">
					<input type="checkbox" class="select_all">
				</th>
                <th>Nama Ujian</th>
                <th>Status</th>
                <th>Soal</th>
                <th>Wkt Mulai</th>
                <th>Wkt Selesai</th>
                <th>Lama Ujian</th>
                <th>Acak Soal/Jwbn</th>
                <th>Oleh</th>
                <th	class="text-center">Token</th>
                <th class="text-center">Aksi</th>
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

<div class="modal" id="modal_paket">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pilihan Paket</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="alert border-danger"><span><b>Perhatian :</b></span><br><span class="text-danger"><b>***</b> Asign paket akan berpengaruh pada peserta ujian</span></div>
                <fieldset class="form-group" style="padding: 10px; border: 1px solid #ccc;">
					<legend class="col-form-label col-sm-12" style="border: 1px solid #ccc; background-color: #d4ffd7;">Paket Ujian</legend>
					<div class="form-group">
						<div class="">
							<input type="checkbox" class="icheck" value="ignore" name="is_ignore_paket" id="is_ignore_paket" checked="checked" /><label for="is_ignore_paket" style="margin: 0.6em">Ignore existing paket</label>
						</div>
					</div>
					<div class="form-group">
						<select name="paket[]" id="paket" class="form-control"
							style="width:100%!important" multiple="multiple">
							@foreach ($paket_avail as $paket)
							<option value="{{ $paket->id }}" {{ in_array($paket->id, $paket_selected) ? "selected" : "" }}>{{ $paket->name }}</option>    
							@endforeach
						</select>
					</div>
					<div class="form-group pull-right">
						<button type="button" id="submit_paket" class="btn btn-flat btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
					</div>
				</fieldset>
            </div>
        </div>
    </div>
</div>

@endsection
