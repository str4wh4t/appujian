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
<script type="text/javascript" src="{{ MIDTRANS_SNAP_JS_URL }}" data-client-key="{{ MIDTRANS_CLIENT_KEY }}"></script>
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

// $(document).on('click', '.bayar', function(){
//     let id = $(this).data('id');

//     ajaxcsrf();
//     ajx_overlay(true);
//     $.ajax({
//         url: '{{ url('payment/ajax/status') }}',
//         data: {'id': id},
//         type: 'POST',
//         success: function(res) {
//             if (res) {
//                 $('#th_order_id').text(res.order_id);
//                 $('#th_payment_type').text(res.payment_type);
//                 $('#th_bank').text(res.bank);
//                 $('#th_va_number').text(res.va_number);
//                 $('#th_status').text(res.status);
//                 $('#modal_info').modal('show');
//             }
//         },
//         error: function () {
//             Swal.fire({
//                 title: "Gagal",
//                 text: "Terjadi kesalahan",
//                 icon: "error"
//             });
//         },
//         complete: function(){
//             ajx_overlay(false);
//         },
//     });
// });

function init_page_level(){
    $('#tb_history').DataTable({
        "order": [[ 3, "desc" ]]
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
                <h4 class="card-title">Membership History</h4>
                <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
            </div>
            <div class="card-content">
                <div class="card-body">

<!---- --->
<div class="row">
    <div class="col-12">
        <div class="alert bg-yellow text-danger border-danger mb-3"><i class="fa fa-exclamation-triangle"></i> Anda berada dalam membership <b style="text-transform: uppercase">{{ $mhs_membership->membership->name }}</b> dan akan expire dalam : <b>{{ $count_expire_days }}</b></div>
        <div class="table-responsive col-sm-12">
            <table class="table table-striped" id="tb_history">
                <thead>
                    <tr>
                        <th>Membership</th>
                        <th>Upgrade Ke</th>
                        <th>Tgl Expire</th>
                        <th>Tgl Upgrade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($membership_history_list as $item)
                    <tr>
                        <td>{{ strtoupper($item->membership->name) }}</td>
                        <td>{{ $item->upgrade_ke }}</td>
                        <td>{{ date('M d, Y', strtotime($item->expired_at)) }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>
                            @if ($item->stts)
                                <span class="text-success"><b>AKTIF</b></span>
                            @else
                                <span class="text-danger"><b>NON AKTIF</b></span>
                            @endif
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
@endsection