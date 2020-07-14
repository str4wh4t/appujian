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

let inputs ='';
let banyak = '{{ $banyak }}';

</script>
<script src="{{ asset('assets/dist/js/app/master/kelas/add.js') }}"></script>
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
            <strong>Jumlah data :</strong> {{ $banyak }}
        </div>
        <?=form_open('kelas/save', array('id'=>'kelas'), array('mode'=>'add'))?>
        <table id="form-table" class="table text-center table-condensed">
            <thead>
                <tr>
                    <th># No</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i=1; $i <= $banyak; $i++) : ?>
                    <tr>
                        <td><?=$i?></td>
                        <td>
                            <div class="form-group">
                                <input autofocus="autofocus" onfocus="this.select()" autocomplete="off" type="text" name="nama_kelas[<?=$i?>]" class="form-control">
                                <span class="d-none">DON'T DELETE THIS</span>
                                <small class="help-block text-right"></small>
                            </div>
                        </td>
                        <td width="200">
                            <div class="form-group">
                                <select name="jurusan_id[<?=$i?>]" class="form-control input-sm select2" style="width: 100%!important">
                                    <option value="" disabled selected>-- Pilih --</option>
                                    <?php foreach ($jurusan as $j) : ?>
                                        <option value="<?=$j->id_jurusan?>"><?=$j->nama_jurusan?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="help-block text-right"></small>
                            </div>
                        </td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <button id="submit" type="submit" class="btn btn-block btn-flat btn-outline-primary">
            <i class="fa fa-save"></i> Simpan
        </button>
        <a href="{{ site_url('kelas') }}" class="btn btn-block btn-flat btn-warning">
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
