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
    $('.select2').select2();
}

</script>
<script src="{{ asset('assets/dist/js/app/relasi/kelasdosen/add.js') }}"></script>
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
    <div class="col-sm-4">
        <div class="alert bg-info">
            <h4 style="color: #FFF"><i class="fa fa-info-circle"></i> Informasi</h4>
            <hr>
            Jika kolom dosen kosong, berikut ini kemungkinan penyebabnya :
            <br><br>
            <ol>
                <li>Anda belum menambahkan master data dosen (Master dosen kosong/belum ada data sama sekali).</li>
                <li>Dosen sudah ditambahkan, jadi anda tidak perlu tambah lagi. Anda hanya perlu mengedit data kelas dosen nya saja.</li>
            </ol>
        </div>
    </div>
    <div class="col-sm-4">
        <?=form_open('kelasdosen/save', array('id'=>'kelasdosen'), array('method'=>'add'))?>
        <div class="form-group">
            <label>Dosen</label>
            <select name="dosen_id" class="form-control select2" style="width: 100%!important">
                <option value="" disabled selected></option>
                <?php foreach ($dosen as $d) : ?>
                    <option value="<?=$d->id_dosen?>"><?=$d->nama_dosen?></option>
                <?php endforeach; ?>
            </select>
            <small class="help-block text-right"></small>
        </div>
        <div class="form-group">
            <label>Kelas</label>
            <select id="kelas" multiple="multiple" name="kelas_id[]" class="form-control select2" style="width: 100%!important">
                <?php foreach ($kelas as $k) : ?>
                    <option value="<?=$k->id_kelas?>"><?=$k->nama_kelas?> - <?=$k->nama_jurusan?></option>
                <?php endforeach; ?>
            </select>
            <small class="help-block text-right"></small>
        </div>
        <div class="form-group pull-right">
            <a href="{{ site_url('kelasdosen') }}" class="btn btn-flat btn-warning">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            <button id="submit" type="submit" class="btn btn-flat btn-outline-primary">
                <i class="fa fa-save"></i> Simpan
            </button>
        </div>
        <?=form_close()?>
    </div>
</div>
<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>
@endsection
