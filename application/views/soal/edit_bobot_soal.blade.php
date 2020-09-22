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
<script src="{{ asset('assets/yarn/node_modules/inputmask/dist/jquery.inputmask.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

function init_page_level(){
    // $('#matkul').select2({placeholder: "Pilih Matkul"});

    $(".inp_decimal").inputmask("decimal",{
        digits: 2,
        digitsOptional: false,
        radixPoint: ".",
        groupSeparator: ",",
        allowPlus: false,
        allowMinus: false,
        rightAlign: false,
        autoUnmask: true,
    });
}



</script>
<script src="{{ asset('assets/dist/js/app/soal/add_bobot_soal.js') }}"></script>
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
    <?=form_open('soal/ajax/save_bobot_soal', array('id'=>'bobot_soal'), array('method'=>'edit','id'=>$bobot_soal->id));?>
        <div class="form-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="nip">Bobot</label>
                        <input autofocus="autofocus" onfocus="this.select()" value="{{ $bobot_soal->bobot }}" type="text" id="bobot" class="form-control" name="bobot" placeholder="Bobot">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="nama_dosen">Nilai</label>
                        <input type="text" class="form-control inp_decimal" value="{{ $bobot_soal->nilai }}" name="nilai" placeholder="nilai">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group pull-right">
                        <a href="{{ site_url('soal/bobot_soal') }}" class="btn btn-flat btn-warning">
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
