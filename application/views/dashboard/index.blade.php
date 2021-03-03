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
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

</script>
<!-- END PAGE LEVEL JS-->
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
@if(is_admin())
<div class="row">
    <div class="col-md-4">
        <div class="row mb-1 pt-1">
            <div class="col-md-4">
                <img src="{{ asset('assets/imgs/'. APP_ICON .'.png') }}" style="width: 85px" alt="avatar">
            </div>
            <div class="col-md-8">
                <span style="font-size: 18px;">Computer Assisted Test</span>
                <hr>
                <span style="font-size: 18px;">Universitas Diponegoro</span>
            </div>
            <hr>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card" style="margin-bottom: 0px">
                    <div class="card-body" style="padding: 1rem">
                        <blockquote class="blockquote pl-1 border-left-red border-left-3 mt-1">
                            <h4>Mengenal CAT UNDIP</h4>
                            <span style="font-size: 15px">
                            Dalam ujian dengan CAT UNDIP setiap aksi peserta pada setiap soal termonitor dalam sistem yang memudahkan dalam audit jika terjadi hal tak terduga selama ujian berlangsung.
                            </span>
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
{{--        <hr>--}}
    </div>
    <div class="col-md-8" style="border: 1px solid">
@php($i = 1)
@foreach($info_box as $info)
{!! $i == 1 ? '<div class="row">' : '' !!}
    <div class="col-md-4 border-right-blue-grey border-right-lighten-5 pb-1 pt-1">
        <div class="pb-1">
            <div class="clearfix mb-1">
                <i class="fa fa-{{ $info->icon }} font-large-1 blue-grey float-left mt-1"></i>
                <span class="font-large-2 text-bold-300 info float-right">{{ $info->total }}</span>
            </div>
            <div class="clearfix">
                <span class="text-muted">{{ $info->title }}</span>
                <span class="info float-right">
                    <a href="{{ site_url(strtolower($info->link)) }}" class="small-box-footer">
                        More info <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="progress mb-0" style="height: 7px;">
            <div class="progress-bar bg-{{ $info->box }}" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
{!! $i == 3 ? '</div>' : '' !!}
<?php
    $i++;
    if($i > 3){
        $i = 1;
    }
?>
@endforeach
    </div>
</div>

@elseif(in_group('dosen'))

<div class="row">
    <div class="col-md-6">
        <h3 class="box-title">Informasi Akun</h3>
        <hr>
        <dl class="row">
            <dt class="col-md-4">Nama</dt>
            <dd class="col-md-8"><?=$dosen->nama_dosen?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md-4">NIP</dt>
            <dd class="col-md-8"><?=$dosen->nip?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md-4">Email</dt>
            <dd class="col-md-8"><?=$dosen->email?></dd>
        </dl>
{{--        <dl class="row">--}}
{{--            <dt class="col-md-6">Matkul</dt>--}}
{{--            <dd class="col-md-6"><?=$dosen->nama_matkul?></dd>--}}
{{--        </dl>--}}
        <dl class="row">
            <dt class="col-md-4">Materi Ujian</dt>
            <dd class="col-md-8">
                <ol class="pl-4" style="padding-left: 15px !important">
                    @forelse($dosen->matkul as $matkul)
                        <li><?=$matkul->nama_matkul?></li>
                    @empty
                        <li>Belum ada materi ujian</li>
                    @endforelse
                </ol>
            </dd>
        </dl>
    </div>
    <div class="col-md-6">
        <h3 class="box-title">Pengumuman</h3>
        <hr>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quidem in animi quibusdam nihil esse ratione, nulla sint enim natus, aut mollitia quas veniam, tempore quia!</p>
        <ul class="pl-4">
            <li>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Consectetur, culpa.</li>
            <li>Provident dolores doloribus, fugit aperiam alias tempora saepe non omnis.</li>
            <li>Doloribus sed eum et repellat distinctio a repudiandae quia voluptates.</li>
            <li>Adipisci hic rerum illum odit possimus voluptatibus ad aliquid consequatur.</li>
            <li>Laudantium sapiente architecto excepturi beatae est minus, labore non libero.</li>
        </ul>
    </div>
</div>

@elseif(in_group('pengawas'))

<div class="row">
    <div class="col-md-6">
        <h3 class="box-title">Informasi Akun</h3>
        <hr>
        <dl class="row">
            <dt class="col-md-4">Nama</dt>
            <dd class="col-md-8"><?=$user->full_name?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md-4">NIP</dt>
            <dd class="col-md-8"><?=$user->username?></dd>
        </dl>
        <dl class="row">
            <dt class="col-md-4">Email</dt>
            <dd class="col-md-8"><?=$user->email?></dd>
        </dl>
{{--        <dl class="row">--}}
{{--            <dt class="col-md-6">Matkul</dt>--}}
{{--            <dd class="col-md-6"><?=$dosen->nama_matkul?></dd>--}}
{{--        </dl>--}}
        <dl class="row">
            <dt class="col-md-4">Ruang Ujian</dt>
            <dd class="col-md-8">
                <ol class="pl-4" style="padding-left: 15px !important">

                </ol>
            </dd>
        </dl>
    </div>
    <div class="col-md-6">
        <h3 class="box-title">Pengumuman</h3>
        <hr>
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Quidem in animi quibusdam nihil esse ratione, nulla sint enim natus, aut mollitia quas veniam, tempore quia!</p>
        <ul class="pl-4">
            <li>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Consectetur, culpa.</li>
            <li>Provident dolores doloribus, fugit aperiam alias tempora saepe non omnis.</li>
            <li>Doloribus sed eum et repellat distinctio a repudiandae quia voluptates.</li>
            <li>Adipisci hic rerum illum odit possimus voluptatibus ad aliquid consequatur.</li>
            <li>Laudantium sapiente architecto excepturi beatae est minus, labore non libero.</li>
        </ul>
    </div>
</div>

@else
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                        <img src="{{ asset('assets/imgs/android-chrome-512x512.png') }}" style="width: 85px" alt="avatar">
            </div>
            <div class="col-md-8">
                <span style="font-size: 18px;">Computer Assisted Test</span>
                <hr>
                <span style="font-size: 18px;">Universitas Diponegoro</span>
            </div>
            <hr>
        </div>
            </div>
        </div>
{{--        <hr>--}}
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <blockquote class="blockquote pl-1 border-left-red border-left-3">
                    <h4>Mengenal CAT UNDIP</h4>
                    <span style="font-size: 15px">
                    Dalam ujian dengan CAT UNDIP setiap aksi peserta pada setiap soal termonitor dalam sistem yang memudahkan dalam audit jika terjadi hal tak terduga selama ujian berlangsung.
                    </span>
                </blockquote>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card mb-0">
                    <div class="card-body pt-0 pb-0">
        <h3 class="box-title">Identitas</h3>
        <hr>
                    </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body ">
                <img id="img_profile"  style="height: 150px; width: 120px;" src="{{ $mahasiswa->foto }}" />
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body ">
        {{--        <h3 class="box-title">Informasi Akun</h3>--}}
        {{--        <hr>--}}
        {{--        <div style="" class="pb-1">--}}
        {{--            <img id="img_profile"  style="height: 150px; width: 120px;" src="{{ $mahasiswa->foto }}" />--}}
        {{--        </div>--}}
        {{--        <dl class="row">--}}
        {{--            <dd class="col-md-4" style="padding-left: 25px"><img id="img_profile"  style="height: 150px; width: 120px;" src="{{ $mahasiswa->foto }}" /></dd>--}}
        {{--        </dl>--}}
                <dl class="row">
                    <dt class="col-md-4">No Peserta</dt>
                    <dd class="col-md-8">: <?=$mahasiswa->nim?></dd>
                </dl>
                <dl class="row">
                    <dt class="col-md-4">Nama</dt>
                    <dd class="col-md-8">: <?=$mahasiswa->nama?></dd>
                </dl>
                <dl class="row">
                    <dt class="col-md-4">NIK</dt>
                    <dd class="col-md-8">: <?=$mahasiswa->nik?></dd>
                </dl>
                <dl class="row">
                    <dt class="col-md-4">TTL</dt>
                    <dd class="col-md-8">: <?=$mahasiswa->tmp_lahir?> / <?=$mahasiswa->tgl_lahir?></dd>
                </dl>
                <dl class="row">
                    <dt class="col-md-4">Jenkel</dt>
                    <dd class="col-md-8">: <?=$mahasiswa->jenis_kelamin === 'L' ? "Laki-laki" : "Perempuan" ;?></dd>
                </dl>
                <dl class="row">
                    <dt class="col-md-4">Email</dt>
                    <dd class="col-md-8">: <?=$mahasiswa->email?></dd>
                </dl>
                <dl class="row">
                    <dt class="col-md-4">Materi Ujian</dt>
                    <dd class="col-md-8">
                        <ol class="pl-4" style="padding-left: 15px !important">
                            @forelse($mahasiswa->matkul as $matkul)
                                <li><?=$matkul->nama_matkul?></li>
                            @empty
                                <li>Belum ada materi ujian</li>
                            @endforelse
                        </ol>
                    </dd>
                </dl>
        {{--        <dl class="row">--}}
        {{--            <dt class="col-md-4">Jurusan</dt>--}}
        {{--            <dd class="col-md-8"><?=$mahasiswa->nama_jurusan?></dd>--}}
        {{--        </dl>--}}
        {{--        <dl class="row">--}}
        {{--            <dt class="col-md-3">Kelas</dt>--}}
        {{--            <dd class="col-md-8"><?=$mahasiswa->nama_kelas?></dd>--}}
        {{--        </dl>--}}
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body pt-0">
        <h3 class="box-title">Pengumuman</h3>
        <hr>
                <p>
                    Computer Assisted Test (CAT) Universitas Diponegoro yang dikelola oleh Lembaga Pengembangan dan Penjaminan Mutu Pendidikan memberikan kemudahan dalam pelayanan test baik secara online maupun offline.</p>
                <p>Test yang diadakan meliputi : Tes Potensi Akademik (TPA), Tes Substansi Bidang, Tes Prediksi dan lain-lain. Sistem ujian menggunakan CAT akan diperoleh hasil seleksi yang kredible, akurat dan cepat. </p>
                <p>Informasi layanan CAT dapat menghubungi layanan ujian Undip di : </p>
                <p><i class="fa fa-phone-square"></i> +62-24 7460041</p>
                <p><i class="fa fa-whatsapp"></i> 0812-2561-1333</p>
                <p><i class="ft-mail"></i> lp2mp@live.undip.ac.id</p>
                <p><i class="icon-globe"></i> www.lp2mp.undip.ac.id</p>
            </div>
        </div>
    </div>

</div>

@endif
<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>
@endsection
