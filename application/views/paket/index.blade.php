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
<script src="{{ asset('assets/node_modules/datatables.net-plugins/api/fnPagingInfo.js') }}"></script>

{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
let table;

function init_page_level(){
  ajaxcsrf();

  table = $("#bundle_soal").DataTable({
    initComplete: function() {
      var api = this.api();
      $("#soal_filter input")
        .off(".DT")
        .on("keyup.DT", function(e) {
          api.search(this.value).draw();
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
        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
      },
      {
        extend: "print",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
      },
      {
        extend: "excel",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
      },
      {
        extend: "pdf",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
      }
    ],
    oLanguage: {
      sProcessing: "loading..."
    },
    processing: true,
    serverSide: true,
    ajax: {
      url: '{{ url('paket/ajax/data') }}',
      type: "POST"
    },
    columns: [
      { data: "name" },
      { data: "urut" },
      { data: "kuota_latihan_soal" },
      { data: "is_show" },
      { data: "jml_ujian" },
      { data: "jml_taker" },
      {
        data: "id",
        orderable: false,
        searchable: false
      },
    ],
    columnDefs: [
      {
        targets: 3,
        data: "is_show",
        render: function(data, type, row, meta) {
          return data == '1' ? '<span class="badge bg-success">show</span>' : '<span class="badge bg-danger">hidden</span>';
        }
      },
      {
        targets: 4,
        data: "jml_ujian",
        render: function(data, type, row, meta) {
          return data + " ujian";
        }
      },
      {
        targets: 5,
        data: "jml_taker",
        render: function(data, type, row, meta) {
          return data + " mhs";
        }
      },
      {
        targets: 6,
        data: "id",
        render: function(data, type, row, meta) {
          return `<div class="btn-group">
                  <a class="btn btn-sm btn-info" href="${base_url}ujian/master#${data}---${row.name}">
										<i class="fa fa-folder-open"></i>
									</a>
                  <a class="btn btn-sm btn-warning btn_edit" href="${base_url}paket/edit/${data}">
										<i class="fa fa-pencil"></i>
									</a>
                  <button class="btn btn-sm btn-danger btn_delete" data-id="${data}">
										<i class="fa fa-times"></i>
									</button>
                  </div>`;
        }
      }
    ],
    order: [[1, "asc"]],
    // rowId: function(a) {
    //   return a;
    // },
    // rowCallback: function(row, data, iDisplayIndex) {
      // var info = this.fnPagingInfo();
      // var page = info.iPage;
      // var length = info.iLength;
      // var index = page * length + (iDisplayIndex + 1);
      // $("td:eq(1)", row).html(index);
    // }
  });

}

$(document).on("click", ".btn_delete", function() {
    let id = $(this).data('id');
    if (confirm('Yakin akan dihapus ?')){
      ajx_overlay(true);
      $.ajax({
        type: "POST",
        url: "{{ url('paket/ajax/delete') }}",
        data: {
            'id': id,
        },
        success: function (r) {
          if(r.stts == 'ok'){
            Swal.fire({
                title: "Perhatian",
                text: "Data berhasil dihapus",
                icon: "success"
            });
            reload_ajax();
          }else{
            Swal.fire({
                title: "Perhatian",
                text: "Terdapat data yang masih terkait",
                icon: "warning"
            });
          }
        },
        error: function () {
          Swal.fire({
              title: "Perhatian",
              text: "Terjadi kesalahan",
              icon: "warning"
            });
        },
        complete: function () {
          ajx_overlay(false);
        }
      });
    }
});

$(document).on("click", ".btn_edit", function() {
    let id = $(this).data('id');

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
    <div class="col-md-12">
		<div class="mb-4">
			<a href="{{ site_url('paket/add') }}" class="btn btn-sm btn-flat btn-outline-primary"><i class="fa fa-plus"></i> Tambah Data</a>
			<button type="button" onclick="reload_ajax()" class="btn btn-sm btn-flat btn-outline-secondary"><i class="fa fa-refresh"></i> Reload</button>
			<div class="pull-right">
{{--				<button onclick="bulk_edit()" class="btn btn-sm btn-flat btn-warning" type="button"><i class="fa fa-edit"></i> Edit</button>--}}
{{--				<button onclick="bulk_delete()" class="btn btn-sm btn-flat btn-danger" type="button"><i class="fa fa-trash"></i> Delete</button>--}}
			</div>
		</div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
		<div class="table-responsive pb-2">
			<table id="bundle_soal" class="table table-striped table-bordered table-hover w-100">
				<thead>
					<tr>
						<th>Paket</th>
            <th>Urut</th>
            <th>Kuota</th>
            <th>Stts</th>
						<th>Jml Ujian</th>
            <th>Jml Taker</th>
						<th>
							Aksi
						</th>
					</tr>
				</thead>
			</table>
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
