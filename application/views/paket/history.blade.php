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


    $('#tb_history_2').DataTable({
        "order": [[ 0, "asc" ]]
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
                <h4 class="card-title">Paket History</h4>
                <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
            </div>
            <div class="card-content">
                <div class="card-body">

<!---- --->
<div class="row">
    <div class="col-12">
        <div class="alert bg-yellow text-danger border-danger mb-3"><i class="fa fa-exclamation-triangle"></i> Anda berada dalam membership <b style="text-transform: uppercase">{{ $mhs_membership->membership->name }} {{ is_mhs_membership_expired() ? '(EXPIRED)' : '' }}</b>  , dan kuota latihan soal anda {!! is_mhs_limit_by_kuota() ? 'sesuai yg tertera pada masing - masing materi' : '<b>UNLIMITED</b>' !!}</b></div>
        <ul class="nav nav-tabs nav-top-border no-hover-bg nav-justified mb-2">
            <li class="nav-item">
                <a class="nav-link active" id="active-tab1" data-toggle="tab" href="#active1" aria-controls="active1" aria-expanded="true">Paket</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="link-tab1" data-toggle="tab" href="#link1" aria-controls="link1" aria-expanded="false">Ujian</a>
            </li>
        </ul>
        <div class="tab-content px-1 pt-1">
            <div role="tabpanel" class="tab-pane active" id="active1" aria-labelledby="active-tab1" aria-expanded="true">
                <div class="alert bg-info text-center">Riwayat pembelian paket anda</div>
                <div class="table-responsive">
                    <table class="table table-striped w-100" id="tb_history">
                        <thead>
                            <tr>
                                <th>Paket</th>
                                <th>Deskripsi</th>
                                <th>Kuota Latihan Soal</th>
                                <th>Tgl Beli</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paket_history_list as $item)
                            <tr>
                                <td>{{ strtoupper($item->paket->name) }}</td>
                                <td>
                                    {!! $item->paket->description !!}
                                </td>
                                <td>{{ $item->paket->kuota_latihan_soal . 'x' }}</td>
                                <td>{{ $item->created_at }}</td>
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
            <div class="tab-pane" id="link1" role="tabpanel" aria-labelledby="link-tab1" aria-expanded="false">
                <div class="alert bg-info text-center">Ujian yang anda miliki</div>
                <div class="table-responsive col-sm-12">
                    <table class="table table-striped" id="tb_history_2">
                        <thead>
                            <tr>
                                <th>Nama Ujian</th>
                                <th>Kuota Latihan Soal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mhs_ujian_list as $item)
                            <tr>
                                <td>{{ strtoupper($item->m_ujian->nama_ujian) }}</td>
                                <td>{!! is_mhs_limit_by_kuota() ? $item->sisa_kuota_latihan_soal . 'x'  : '&infin;' !!}</td>
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
    </div>
</div>
<!---- --->

                </div>
            </div>
        </div>
    </div>
</section>
@endsection