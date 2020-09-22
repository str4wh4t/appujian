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
<div class="box">
    <div class="box-header with-header">
        <div class="mb-2">
            <a href="{{ site_url('soal') }}" class="btn btn-flat btn-warning">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ site_url('soal/edit/' . segment(3)) }}" class="btn btn-flat btn-outline-primary">
                <i class="fa fa-edit"></i> Edit
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-8">
                <table class="table table-bordered">
                    <tr>
                        <td>Matkul</td><td>{{ $soal_orm->topik->matkul->nama_matkul }}</td>
                    </tr>
                    <tr>
                        <td>Topik</td><td>{{ $soal_orm->topik->nama_topik }}</td>
                    </tr>
                    <tr>
                        <td>Dibuat Oleh</td><td>{{ $soal->created_by }}</td>
                    </tr>
                    <tr>
                        <td>Dibuat Pada</td><td>{{ $soal->created_at }}</td>
                    </tr>
                    <tr>
                        <td>Terkahir Diupdate</td><td>{{ $soal->updated_at }}</td>
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
                <?=$soal->soal?>
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

                    <h4></h4>
                    @if($soal->jawaban === $ABJ)
                    <div class="badge border-success success badge-square badge-border">
                        <span>Pilihan <?=$ABJ?></span> <i class="fa fa-check-circle font-medium-2"></i>
                    </div>
                    @else
                    <div class="badge border-danger danger badge-square badge-border">
                        Pilihan <?=$ABJ?>
                    </div>
                    @endif
                    <blockquote class="blockquote pl-1 border-left-grey border-left-3 mt-2 mb-2">
                    <?=$soal->$opsi?>
                    </blockquote>
                    <?php if(!empty($soal->$file)): ?>
                    <div class="w-50 mx-auto">
                        <?= tampil_media('uploads/bank_soal/'.$soal->$file); ?>
                    </div>
                    <?php endif;?>

                <?php endforeach;?>
                <hr>
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
