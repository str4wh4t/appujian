@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script type="text/javascript" src="{{ MIDTRANS_SNAP_JS_URL }}" data-client-key="{{ MIDTRANS_CLIENT_KEY }}"></script>
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

$(document).on('click', '#btn_checkout', function(){
    // toastr.success('Maaf, anda telah membeli paket tsb.', 'Perhatian!', 
    //     {
    //         positionClass: 'toast-top-center',
    //         containerId: 'toast-top-center',
    //         showMethod: "slideDown",
    //         hideMethod: "fadeOut",
    //     }
    // );
    // return false;

    let va_provider_id = $('input[name="pilihan_va"]:checked').val();

    Swal.fire({
        title: "Perhatian",
        text: "Apakah yakin, tagihan anda akan di issue",
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Yakin",
        cancelButtonColor: '#d33',
    }).then(result => {
        if (result.value) {
            ajaxcsrf();
            ajx_overlay(true);
            $.ajax({
                url: '{{ url('payment/ajax/snap') }}',
                data: {'info': '{{ $info }}', 'va_provider_id' : va_provider_id},
                type: 'POST',
                success: function(response) {
                    if (response.token) {
                        // JIKA PEMBAYARAN VIA MIDTRANS
                        snap.pay(response.token);
                        // snap.pay(response.token,
                        // {
                        //     onSuccess: function(result){
                        //         /* You may add your own implementation here */
                        //         // alert("payment success!"); console.log(result);
                        //         Swal.fire({
                        //             title: "Perhatian",
                        //             text: "Pembayaran berhasil, anda akan diredirect ke dashboard",
                        //             icon: "success",
                        //             confirmButtonText: "Selesai"
                        //         }).then(result => {
                        //             if (result.value) {
                        //                 window.location.href = '{{ url('dashboard') }}';
                        //             }
                        //         });
                        //         setTimeout(function() {
                        //             window.location.href = '{{ url('dashboard') }}';
                        //         }, 3000);
                        //     },
                        //     onPending: function(result){
                        //         /* You may add your own implementation here */
                        //         // alert("wating your payment!"); console.log(result);
                        //         // alert("payment failed!"); console.log(result);
                        //         Swal.fire({
                        //             title: "Perhatian",
                        //             text: "Menunggu pembayaran",
                        //             icon: "info",
                        //             confirmButtonText: "Selesai"
                        //         });
                        //     },
                        //     onError: function(result){
                        //         /* You may add your own implementation here */
                        //         // alert("payment failed!"); console.log(result);
                        //         Swal.fire({
                        //             title: "Perhatian",
                        //             text: "Pembayaran gagal",
                        //             icon: "error",
                        //             confirmButtonText: "Selesai"
                        //         });
                        //     },
                        //     onClose: function(){
                        //         /* You may add your own implementation here */
                        //         // alert('you closed the popup without finishing the payment');
                        //     }
                        // });
                        
                    }else{
                        // JIKA PEMBAYARAN VIA SELAIN MIDTRANS / UDID
                        if(response.status == 'ok'){
                            Swal.fire({
                                title: "Perhatian",
                                text: "Order berhasil",
                                icon: "success",
                                confirmButtonText: "Lihat pembayaran",
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then(result => {
                                if (result.value) {
                                    window.location.href = '{{ url('payment/history') }}';
                                }
                            });

                        }else{
                            Swal.fire({
                                title: "Gagal",
                                text: "Terjadi kesalahan : " + response.msg,
                                icon: "error"
                            });

                        }
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
        }
    });
});

function init_page_level(){
    $('input[name="pilihan_va"]:first').prop('checked', true); // PILIH VA PERTAMA JIKA TERSEDIA
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
                <h4 class="card-title">Checkout</h4>
                <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
            </div>
            <div class="card-content">
                <div class="card-body">

<!---- --->
<div class="row">
    <div class="col-12">
        <div class="alert bg-info">Rincian Pembelian</div>
        <div class="table-responsive">
            @php($total_biaya = 0)
            <table class="table w-100">
                <thead>
                    <tr>
                        <th>{{ substr($info, 0, 1) == 'M' ? 'Membership' : 'Paket' }}</th>
                        <th>Deskripsi</th>
                        <th>{{ substr($info, 0, 1) == 'M' ? 'Masa Berlaku' : 'Kuota Latihan Soal' }}</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ strtoupper($item->name) }}</td>
                        <td>
                            {!! $item->description !!}
                            @if (substr($info, 0, 1) == 'P')
                                <hr>
                                <h6 class="text-danger"><b>Include :</b></h6>
                                <ol class="ol-include-paket">
                                    @foreach ($item->m_ujian as $m_ujian)
                                    <li>{{ $m_ujian->nama_ujian }}</li>
                                    @endforeach
                                </ol>

                            @endif
                        </td>
                        <td>{!! substr($info, 0, 1) == 'M' ? $item->durasi . ' Bulan' : $item->kuota_latihan_soal . 'x' . ' atau <b class="text-danger">UNLIMITED</b> jika membership '  !!}</td>
                        <td style="text-align: right">{{ number_format($item->price, 0, ",", ".") }}</td>
                    </tr>
                    @php($total_biaya = $total_biaya + $item->price)
                </tbody>
                <tfoot>

                    @if(APP_UDID)
                    
                    @if(!empty($additional_cost))
                    <tr>
                        <td colspan="5" style="background-color: #fee; border: 1px solid #fee;"></td>
                    </tr>
                    @foreach ($additional_cost as $add_cost)
                    <tr>
                        <td colspan="2">&nbsp;</td>
                        <td >
                            {{ strtoupper($add_cost->name) }}
                        </td>
                        <td style="text-align: right">{{ number_format($add_cost->nominal, 0, ",", ".") }}</td>
                    </tr>
                    @php($total_biaya = $total_biaya + $add_cost->nominal)
                    @endforeach

                    @endif

                    <tr>
                        <td colspan="2">&nbsp;</td>
                        <td >
                            <b>TOTAL BIAYA</b>
                        </td>
                        <td style="text-align: right"><b>{{ number_format($total_biaya, 0, ",", ".") }}</b></td>
                    </tr>

                    @if(!empty($va_provider))
                    <tr>
                        <td colspan="5" style="background-color: #eef; border: 1px solid #eef;"></td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                        <td >
                            PILIHAN PEMBAYARAN
                        </td>
                        <td style="text-align: center">
                            @foreach ($va_provider as $vp)
                            <div style="display: block">
                                <input type="radio" name="pilihan_va" value="{{ $vp->virtual_account_provider_code }}" >
                                {!! show_logo_va_udid($vp->provider_name) !!}
                            </div>
                            @endforeach
                        </td>
                    </tr>
                    @endif

                    @endif

                    <tr>
                        <td colspan="3" style="">
                            <div class="alert bg-warning rounded-0">
                                <i class="fa fa-exclamation-circle"></i> Selalu pastikan kesesuaian harga dengan produk yg akan dibayar
                            </div>
                        </td>
                        <td><button type="button" class="btn btn-danger" id="btn_checkout"><i class="fa fa-check-circle"></i> Checkout</button></td>
                    </tr>
                </tfoot>
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