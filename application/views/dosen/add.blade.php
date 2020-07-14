@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/bower_components/bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css') }}">
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
<script src="{{ asset('assets/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/bower_components/bootstrap4-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

function init_page_level(){
    $('#matkul').select2({placeholder: "Pilih Materi Ujian"});
    $('.datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        // Your Icons
        // as Bootstrap 4 is not using Glyphicons anymore
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-check',
            clear: 'fa fa-trash',
            close: 'fa fa-times'
        }
    });
}

</script>
<script src="{{ asset('assets/dist/js/app/master/dosen/add.js') }}"></script>
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
    <?=form_open('dosen/ajax/save', array('id'=>'formdosen'), array('method'=>'add'));?>
        <div class="form-body">
            <h4 class="form-section"><i class="ft-user"></i> Personal Info</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="nip">NIP</label>
                        <input autofocus="autofocus" onfocus="this.select()" type="text" id="nip" class="form-control" name="nip" placeholder="NIP">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="nama_dosen">Nama Dosen</label>
                        <input type="text" class="form-control" name="nama_dosen" placeholder="Nama Dosen">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Dosen</label>
                        <input type="text" class="form-control" name="email" placeholder="Email Dosen">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="nama">Tgl Lahir</label>
                        <input placeholder="Tgl Lahir" type="text" name="tgl_lahir" class="datetimepicker form-control">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="matkul">Materi Ujian</label>
                        <select name="matkul[]" id="matkul" class="form-control select2" multiple="multiple" style="width: 100%!important">
{{--                            <option value="" disabled selected>Pilih Mata Kuliah</option>--}}
                            <?php foreach ($matkul as $row) : ?>
                                <option value="<?=$row->id_matkul?>"><?=$row->nama_matkul?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group pull-right">
                        <a href="{{ site_url('dosen') }}" class="btn btn-flat btn-warning">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" id="submit" class="btn btn-flat btn-outline-primary">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?=form_close();?>
    </div>
</div>
<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>
@endsection
