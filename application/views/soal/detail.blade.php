@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>--}}
{{--<script src="//cdn.datatables.net/plug-ins/1.10.21/api/fnPagingInfo.js"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

function init_page_level(){


}

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
    <div class="col-lg-12">
        <div class="mb-2">
            <a href="{{ site_url('soal') }}" class="btn btn-flat btn-warning">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ site_url('soal/add') }}" class="btn btn-flat btn-outline-primary">
                <i class="fa fa-plus"></i> Tambah Soal
            </a>
            <a href="{{ site_url('soal/edit/' . segment(3)) }}" class="btn btn-flat btn-outline-primary">
                <i class="fa fa-edit"></i> Edit
            </a>
            <div class="pull-right">
                @if (!empty($prev))
                <a href="{{ url('soal/detail/' . $prev) }}" class="btn btn-info"><i class="fa fa-chevron-left"></i> Prev</a>
                @endif

                @if (!empty($next))
                <a href="{{ url('soal/detail/' . $next) }}" class="btn btn-info">Next <i class="fa fa-chevron-right"></i></a>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <table class="table table-bordered">
            <tr>
                <td>Matkul</td><td>{{ $soal_orm->topik->matkul->nama_matkul }}</td>
            </tr>
            <tr>
                <td>Topik</td><td>{{ $soal_orm->topik->nama_topik }}</td>
            </tr>
            <tr>
                <td>No Urut</td><td>{{ $soal_orm->no_urut }}</td>
            </tr>
            <tr>
                <td>Bobot</td><td>{{ $soal_orm->bobot_soal->bobot }}</td>
            </tr>
            <tr>
                <td>Dibuat Oleh</td><td>{{ get_nama_lengkap_user($user) }}</td>
            </tr>
            <tr>
                <td>Dibuat Pada</td><td>{{ $soal->created_at }}</td>
            </tr>
            <tr>
                <td>Terakhir Diupdate</td><td>{{ $soal->updated_at }}</td>
            </tr>
        </table>
        <hr>
        <div class="alert bg-info mb-2" role="alert">
            <strong>Pertanyaan</strong>
        </div>
        <?php if(!empty($soal->file)): ?>
            <div class="w-50">
                <?= tampil_media('uploads/bank_soal/'.$soal->file); ?>
            </div>
        <?php endif; ?>
        {!! $soal->soal !!}
        <div class="alert bg-danger mb-2 mt-2" role="alert">
            <strong>Jawaban</strong>
        </div>
        <?php
        $abjad = ['a', 'b', 'c', 'd', 'e'];

        foreach ($abjad as $abj) :
            $ABJ = strtoupper($abj);
            $opsi = 'opsi_'.$abj;
            $file = 'file_'.$abj;
        ?>

            <div
            class="alert alert-light border-success {{ ($ABJ === $soal->jawaban) ? 'bg-success' : '' }}">
            <?php $text_color = ($ABJ == $soal->jawaban) ? 'white' : 'success';  ?>
            <span style="font-size: 1.5rem"
                class="float-left mr-1 text-{{ $text_color }}">{{ $ABJ }}. </span>{!!
            $soal->$opsi !!}
            </div>

            <?php if(!empty($soal->$file)): ?>
            <div class="w-50 mx-auto">
                <?= tampil_media('uploads/bank_soal/'.$soal->$file); ?>
            </div>
            <?php endif;?>

        <?php endforeach;?>
        <div class="alert bg-warning mb-2" role="alert">
            <strong>Penjelasan</strong>
        </div>
        <div class="alert border-warning rounded-0" style="">
            {!! !empty($soal->penjelasan) ? $soal->penjelasan : 'Belum ada penjelasan' !!}
        </div>
        <hr>
    </div>
    <div class="col-lg-12 text-center">
        <div class="">
            @if (!empty($prev))
            <a href="{{ url('soal/detail/' . $prev) }}" class="btn btn-info"><i class="fa fa-chevron-left"></i> Prev</a>
            @endif

            @if (!empty($next))
            <a href="{{ url('soal/detail/' . $next) }}" class="btn btn-info">Next <i class="fa fa-chevron-right"></i></a>
            @endif
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
