@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
$(document).on('click', '.btn_sudah_beli', function(){
    toastr.success('Maaf, anda telah membeli paket tsb.', 'Perhatian!', 
        {
            positionClass: 'toast-top-center',
            containerId: 'toast-top-center',
            showMethod: "slideDown",
            hideMethod: "fadeOut",
        }
    );
    return false;
});
</script>
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_custom_css')
<!-- BEGIN PAGE CUSTOM CSS-->
<style type="text/css">
#toast-top-center div{
    width: 350px;
}
</style>
<!-- END PAGE CUSTOM CSS-->
@endpush



@section('content')
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Paket</h4>
                <a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
            </div>
            <div class="card-content">
                <div class="card-body">

<!---- --->
<div class="row">
    <div class="col-12">
        <div class="card bg-{{ get_membership_color($user->mhs->membership_aktif->membership_id) }}">
            <div class="card-content">
                <div class="card-body">
                    <div class="media">
                        <div class="media-left media-middle">
                            <i class="ft-bar-chart-2 white font-large-2 float-left"></i>
                        </div>
                        <div class="media-body white text-center">
                            <h3 class="white">Membership</h3>
                            <span>Saat ini anda terdaftar dalam paket : 
                                {!! get_membership_star($user->mhs->membership_aktif->membership_id) !!}
                                <b style="text-transform: uppercase">{{ get_membership_text($user->mhs->membership_aktif->membership_id) }} {{ is_mhs_membership_expired() ? '(EXPIRED)' : '' }}</b></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @forelse ($paket_list->sortBy('urut') as $paket)
    <div class="col-lg-3 pb-2">
        <div class="card h-100">
            <div class="card-header bg-{{ $paket->text_color }}">
                <h3 class="my-0 font-weight-bold text-white text-center" style="text-transform: uppercase">{{ $paket->name }}</h3>
              </div>
            <div class="card-body border border-secondary">
                @if (!empty($paket->delete_price))
                <h4 class="text-center"><del>Rp {{ number_format($paket->delete_price, 0, ",", ".") }}</del></h4>
                @endif
                <h3 class="text-center mb-3" style="{{ !empty($paket->delete_price) ? 'margin-bottom: 0.8rem !important;' : '' }}">
                    Rp {{ number_format($paket->price, 0, ",", ".") }}
                </h3>
                {{-- @if(empty($user->mhs->paket_history()->where('paket_id', $paket->id)->first())) --}}
                {{-- @if(is_mhs_limit_by_kuota()) --}}
                <a href="{{ url('payment/beli/P/' . uuid_create_from_integer($paket->id)) }}"
                    class="btn btn-glow round w-100 fw-600 my-2 text-white btn-primary">
                    <i class="ft-shopping-cart text-white icon-md "></i> Beli sekarang
                </a>
                {{-- @else
                <button type="button" class="btn btn-glow round w-100 fw-600 my-2 text-white btn-secondary btn_sudah_beli" disabled="disabled">
                    <i class="ft-shopping-cart text-white icon-md "></i> Sudah dibeli
                    </button>
                @endif --}}
                <hr>
                
                {!! $paket->description !!}
                
            </div>
        </div>
    </div>
    @empty
    
    @endforelse
</div>
<!---- --->

                </div>
            </div>
        </div>
    </div>
</section>
@endsection