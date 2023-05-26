@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
<!-- END PAGE LEVEL CSS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('assets/npm/node_modules/datatables.net-plugins/api/fnPagingInfo.js') }}"></script>
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

let id = null ;
let table = null ;

$(document).on('click', '.bayar', function(){
    id = $(this).data('id');
    let stts_item = $(this).data('stts');
    ajaxcsrf();
    ajx_overlay(true);
    $.ajax({
        url: '{{ url('payment/ajax/status') }}',
        data: {'id': id},
        type: 'POST',
        success: function(res) {
            if (res) {
                $('#th_order_id').text(res.order_id);
                $('#th_payment_type').text(res.payment_type);
                $('#th_bank').text(res.bank);
                $('#th_va_number').text(res.va_number);
                $('#th_status').text(res.status);
                $('#th_transaction_time').text(res.transaction_time);
                $('#th_gross_amount').text(res.gross_amount);

                if(res.status == 'SETTLEMENT' && stts_item == '{{ PAYMENT_ORDER_BELUM_DIPROSES }}')
                    $('#btn_exec_trx').addClass('btn-danger');
                else
                    $('#btn_exec_trx').removeClass('btn-danger');

                $('#modal_info').modal('show');
            }
        },
        error: function () {
            Swal.fire({
                title: "Gagal",
                text: "Terjadi kesalahan",
                icon: "error"
            });
        },
        complete: function(){
            ajx_overlay(false);
        },
    });
});

function init_page_level(){

    ajaxcsrf();

    table = $("#tb_history").DataTable({
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
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    dom:
      "<'row'<'col-lg-3'l><'col-lg-6 text-center'B><'col-lg-3'f>>" +
      "<'row'<'col-lg-12'tr>>" +
      "<'row'<'col-lg-5'i><'col-lg-7'p>>",
    buttons: [
      {
        extend: "copy",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6], format: {
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
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6], format: {
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
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6], format: {
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
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6], format: {
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
      url: base_url + "payment/ajax/data_order_list",
      type: "POST"
    },
    columns: [
    //   {
    //     data: "id_soal",
    //     orderable: false,
    //     searchable: false
    //   },
    //   {
    //     data: "no_urut",
    //     // orderable: false,
    //     searchable: false
    //   },
      { data: "order_number" },
      { data: "keterangan" },
      { data: "tgl_order" },
      { data: "tgl_bayar" },
      { data: "jml_bayar" },
      { data: "stts" },
      { data: "aksi" }
    ],
    columnDefs: [
    //   {
    //     targets: 0,
    //     data: "id_soal",
    //     render: function(data, type, row, meta) {
    //       return `<div class="text-center">
	// 								<input name="checked[]" class="check" value="${data}" type="checkbox">
	// 							</div>`;
    //     }
    //   },
    ],
    order: [[2, "desc"]],
  });
}

$(document).on('click', '#btn_exec_trx', function(){
    if($(this).hasClass('btn-danger')){
        Swal.fire({
            title: "Perhatian",
            text: "Order yang sudah diproses tidak bisa dikembalikan",
            icon: "warning",
            confirmButtonText: "Proses",
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then(result => {
            if (result.value) {
                ajx_overlay(true);
                $.ajax({
                    type: "POST",
                    url: "{{ site_url('payment/ajax/do_exec_payment') }}",
                    data: {
                        'id': id,
                    },
                    success: function (r) {

                        table.ajax.reload();

                    },
                    complete: function(){
                        $('#modal_info').modal('hide');
                        ajx_overlay(false);
                    },
                });
            }
        });
    }
});

</script>
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_custom_css')
<!-- BEGIN PAGE CUSTOM CSS-->
<style type="text/css">

</style>
<!-- END PAGE CUSTOM CSS-->
@endpush



@section('content')
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Order Histary</h4>
                <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
            </div>
            <div class="card-content">
                <div class="card-body">

<!---- --->
<div class="row">
    <div class="col-12">
        <div class="alert bg-info mb-3"><i class="fa fa-exclamation-triangle"></i> Pembayaran terbaru diproses sekitar 1 menit untuk proses update data</div>
        <div class="table-responsive">
            <table class="table table-striped w-100" id="tb_history">
                <thead>
                    <tr>
                        <th>Order Nmr</th>
                        <th>Keterangan</th>
                        <th>Tgl Order</th>
                        <th>Tgl Bayar</th>
                        <th>Jml Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
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
<div class="modal fade" id="modal_info">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Payment Info</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <table class="w-100 table">
                    <thead>
                        <tr>
                            <th>Order Nmr</th>
                            <th id="th_order_id" class="text-danger"></th>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <th id="th_gross_amount" class="text-danger"></th>
                        </tr>
                        <tr>
                            <th>Payment Type</th>
                            <th id="th_payment_type" class="text-danger"></th>
                        </tr>
                        <tr>
                            <th>Bank</th>
                            <th id="th_bank" class="text-danger"></th>
                        </tr>
                        <tr>
                            <th>VA Number</th>
                            <th id="th_va_number" class="text-danger"></th>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <th id="th_status" class="text-danger"></th>
                        </tr>
                        <tr>
                            <th>Trx Time</th>
                            <th id="th_transaction_time" class="text-danger"></th>
                        </tr>
                    </thead>
                </table>
                <div class="text-center">
                    <button class="btn btn-lg" id="btn_exec_trx"><i class="fa fa-pencil-square-o"></i>
                        Execute Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection