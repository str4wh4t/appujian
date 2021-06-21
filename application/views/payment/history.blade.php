@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
<!-- END PAGE LEVEL CSS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/datatables.net-plugins/api/fnPagingInfo.js') }}"></script>
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

$(document).on('click', '.bayar', function(){
    let id = $(this).data('id');

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
                $('#th_gross_amount').text(res.gross_amount);
                $('#th_transaction_time').text(res.transaction_time);
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
    $('#tb_history').DataTable({
        "order": [[ 2, "desc" ]]
    });
}

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
                <tbody>
                    @forelse ($trx_payment_list as $item)
                    <tr>
                        <td>{{ $item->order_number }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td>{{ $item->tgl_order }}</td>
                        <td>{{ !empty($item->tgl_bayar) ? date('M d, Y', strtotime($item->tgl_bayar)) : '' }}</td>
                        <td>{{ number_format($item->jml_bayar, 0, ",", ".") }}</td>
                        <td>
                            @if ($item->stts == PAYMENT_ORDER_TELAH_DIPROSES)
                                <span class="text-success"><b>Sudah Dibayar</b></span>
                            @elseif ($item->stts == PAYMENT_ORDER_BELUM_DIPROSES)
                                <span class="text-danger"><b>Belum Dibayar</b></span>
                            @elseif ($item->stts == PAYMENT_ORDER_EXPIRED)
                                <span class="text-warning"><b>Expired</b></span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm bayar" data-id="{{ $item->order_number }}"> Bayar</button>
                        </td>
                    </tr>
                    @empty

                    @endforelse
                </tbody>
                {{-- <tfoot>
                    <tr>
                        <td colspan="3">
                            <div class="alert bg-warning rounded-0">
                                <i class="fa fa-exclamation-circle"></i> Selalu pastikan kesesuaian harga dengan produk yg akan dibayar
                            </div>
                        </td>
                        <td><button type="button" class="btn btn-danger" id="btn_checkout"><i class="fa fa-check-circle"></i> Checkout</button></td>
                    </tr>
                </tfoot> --}}
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
            </div>
        </div>
    </div>
</div>
@endsection