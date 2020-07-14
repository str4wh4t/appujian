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

</script>
<script src="{{ asset('assets/dist/js/app/master/jurusan/edit.js') }}"></script>
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
    <div class="col-md-6">
        <div class="alert alert-info mb-2" role="alert">
            <strong>Jumlah data :</strong> {{ count($jurusan) }}
        </div>
        <?=form_open('jurusan/save', array('id'=>'jurusan'), array('mode'=>'edit'))?>
            <table id="form-table" class="table text-center table-condensed">
                <thead>
                    <tr>
                        <th># No</th>
                        <th>Jurusan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($jurusan as $j) : ?>
                    <tr>
                        <td><?=$no?></td>
                        <td>
                            <div class="form-group">
                                <?=form_hidden('id_jurusan['.$no.']', $j->id_jurusan)?>
                                <input autofocus="autofocus" onfocus="this.select()" autocomplete="off" value="<?=$j->nama_jurusan?>" type="text" name="nama_jurusan[<?=$no?>]" class="input-sm form-control">
                                <small class="help-block text-right"></small>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $no++;
                    endforeach;
                    ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-block btn-flat btn-outline-primary">
                <i class="fa fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ site_url('jurusan') }}" class="btn btn-block btn-flat btn-warning">
                <i class="fa fa-arrow-left"></i> Batal
            </a>
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
