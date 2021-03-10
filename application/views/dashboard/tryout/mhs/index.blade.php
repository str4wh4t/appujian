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

</script>
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_custom_css')
<!-- BEGIN PAGE CUSTOM CSS-->
<style type="text/css">
/* .avatar-sm {
    width: 20px;
} */

.media-list a.media {
    padding: 1.5rem;
}

.media-list a.media {
    padding-top: 0;
}

</style>
<!-- END PAGE CUSTOM CSS-->
@endpush



@section('content')
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
            	<h4 class="card-title">Dashboard</h4>
            	<a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
            </div>
            <div class="card-content">
                <div class="card-body">


<!---- --->
<div class="row">
    <div class="col-xl-4 col-lg-12">
        <div class="card height-600 profile-card-with-cover border-grey border-lighten-2">
            <div class="card-img-top img-fluid bg-cover height-200" style="background: url('{{ asset('assets/template/robust/app-assets/images/carousel/12.jpg') }}');"></div>
            <div class="card-profile-image">
                @if ($mahasiswa->jenis_kelamin == 'L')
                <img src="{{ asset('assets/imgs/user_default_112x112.jpg') }}" class="rounded-circle img-border" alt="Card image">
                @else
                <img src="{{ asset('assets/imgs/user_default_female_112x112.jpg') }}" class="rounded-circle img-border" alt="Card image">
                @endif
            </div>
            <div class="profile-card-with-cover-content text-center">
                <div class="card-body">
                    <h4 class="card-title">{{ $mahasiswa->nama }}<hr>{{ $mahasiswa->nim }}</h4>
                    <p class="text-muted m-0">{{ $mahasiswa->email }}</p>
                </div>
                {{-- <div class="card-body">
                    <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-facebook"><span class="ft-facebook"></span></a>
                    <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-twitter"><span class="ft-twitter"></span></a>
                    <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-linkedin"><span class="ft-github font-medium-4"></span></a>
                </div> --}}
            </div>
            {{-- <div class="card-body">
                <dl class="row">
                    <dt class="col-md-3">NIK</dt>
                    <dd class="col-md-9">: <?=$mahasiswa->nik?></dd>
                </dl>
                <dl class="row">
                    <dt class="col-md-3">TTL</dt>
                    <dd class="col-md-9">: <?=$mahasiswa->tmp_lahir?> / <?=$mahasiswa->tgl_lahir?></dd>
                </dl>
            </div> --}}
            <div class="card-body">
                <div class="alert bg-info rounded-0 text-center">Materi Ujian</div>
                <div class="chart-stats text-center">
                    @forelse($mahasiswa->matkul as $matkul)
                        <li><?=$matkul->nama_matkul?></li>
                    @empty
                        <li>Belum ada materi ujian</li>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-12">
        <div class="row">
            <div class="col-12">
                <div class="card bg-{{ get_membership_color($user->membership_id) }}">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media">
                                <div class="media-left media-middle">
                                    <i class="ft-bar-chart-2 white font-large-2 float-left"></i>
                                </div>
                                <div class="media-body white text-center">
                                    <h3 class="white">Membership</h3>
                                    <span>Saat ini anda terdaftar dalam paket : 
                                        {!! get_membership_star($user->membership_id) !!}
                                        <b style="text-transform: uppercase">{{ get_membership_text($user->membership_id) }}</b></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card border-info bg-transparent">
                            <div class="card-header" style="background-color: #2a4659">
                                <h4 class="card-title text-white">Event Menarik</h4>
                            </div>
                            <div class="card-content text-center" style="background-color: #2a4659">
                                <div id="carousel-example" class="carousel slide" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        <li data-target="#carousel-example" data-slide-to="0" class="active"></li>
                                        <li data-target="#carousel-example" data-slide-to="1" class=""></li>
                                        <li data-target="#carousel-example" data-slide-to="2" class=""></li>
                                    </ol>
                                    <div class="carousel-inner" role="listbox">
                                        <div class="carousel-item active">
                                            <a href="#">
                                                <img src="{{ asset('assets/template/robust/app-assets/images/pages/slide-1.jpg') }}" alt="First slide">
                                            </a>
                                        </div>
                                        <div class="carousel-item">
                                            <a href="#">
                                                <img src="{{ asset('assets/template/robust/app-assets/images/pages/slide-2.jpg') }}" alt="Second slide">
                                            </a>
                                        </div>
                                    </div>
                                    <a class="carousel-control-prev" href="#carousel-example" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carousel-example" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card" style="">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <div class="badge border-danger danger round badge-border">
                                        <i class="fa fa-bar-chart"></i> 
                                    </div> Leaderboard
                                </h4>
                            </div>
                            <div class="card-content">
                                <div id="friends-activity" class="media-list height-350 position-relative">
                                    <a href="#" class="media active">
                                        <div class="media-left">
                                            <img class="media-object avatar avatar-sm rounded-circle" src="{{ asset('assets/imgs/user_default_female_112x112.jpg') }}" alt="Generic placeholder image">
                                        </div>
                                        <div class="media-body">
                                            <h5 class="list-group-item-heading">Kristopher Candy <span class="font-medium-4 float-right">1,0215</span></h5>
                                            <p class="list-group-item-text mb-0">
                                                <span class="badge badge-{{ get_membership_color(3) }}">
                                                    {!! get_membership_star(3, 'small') !!}
                                                    <b style="text-transform: capitalize">{{ get_membership_text(3) }}</b>
                                                </span>
                                            </p>
                                        </div>
                                    </a>
                                    <a href="#" class="media">
                                        <div class="media-left">
                                            <img class="media-object avatar avatar-sm rounded-circle" src="{{ asset('assets/imgs/user_default_female_112x112.jpg') }}" alt="Generic placeholder image">
                                        </div>
                                        <div class="media-body">
                                            <h5 class="list-group-item-heading">Lawrence Fowler <span class="font-medium-4 float-right">2,0215</span></h5>
                                            <p class="list-group-item-text mb-0">
                                                <span class="badge badge-danger">Premium</span>
                                            </p>
                                        </div>
                                    </a>
                                    <a href="#" class="media">
                                        <div class="media-left">
                                            <img class="media-object avatar avatar-sm rounded-circle" src="{{ asset('assets/imgs/user_default_female_112x112.jpg') }}" alt="Generic placeholder image">
                                        </div>
                                        <div class="media-body">
                                            <h5 class="list-group-item-heading">Linda Olson <span class="font-medium-4 float-right">1,112</span></h5>
                                            <p class="list-group-item-text mb-0">
                                                <span class="badge badge-success">Standart</span>
                                            </p>
                                        </div>
                                    </a>
                                    <a href="#" class="media">
                                        <div class="media-left">
                                            <img class="media-object avatar avatar-sm rounded-circle" src="{{ asset('assets/imgs/user_default_female_112x112.jpg') }}" alt="Generic placeholder image">
                                        </div>
                                        <div class="media-body">
                                            <h5 class="list-group-item-heading">Linda Olson <span class="font-medium-4 float-right">1,112</span></h5>
                                            <p class="list-group-item-text mb-0">
                                                <span class="badge badge-success">Standart</span>
                                            </p>
                                        </div>
                                    </a>
                                    <a href="#" class="media">
                                        <div class="media-left">
                                            <img class="media-object avatar avatar-sm rounded-circle" src="{{ asset('assets/imgs/user_default_female_112x112.jpg') }}" alt="Generic placeholder image">
                                        </div>
                                        <div class="media-body">
                                            <h5 class="list-group-item-heading">Linda Olson <span class="font-medium-4 float-right">1,112</span></h5>
                                            <p class="list-group-item-text mb-0">
                                                <span class="badge badge-success">Standart</span>
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="card-footer border-top-blue-grey border-top-lighten-5 text-center">
                                <a href="#">Lihat Selengkapnya</a>
                            </div>
                        </div>
                    </div>
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