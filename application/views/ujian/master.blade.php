@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/datatables.net-plugins/api/fnPagingInfo.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
	{{--let id_dosen = '{{ $dosen->id_dosen }}';--}}

    let status_ujian ;

    function init_page_level(){
        $('.select2').select2({
            width: '100%',
        });

        $('#status_ujian').val('active').trigger('change');

        status_ujian = $('#status_ujian').val();
    }

    $(document).on('change','#status_ujian',function () {
		status_ujian = $(this).val();
		table.ajax.reload();
	});

</script>
<script src="{{ asset('assets/dist/js/app/ujian/master.js') }}"></script>
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
<div class="row pb-3">
    <div class="col-sm-4">
    @if(is_admin())
        <a href="{{ site_url('ujian/add') }}" class="btn btn-outline-primary btn-sm btn-flat"><i class="fa fa-file-text-o"></i> Ujian Baru</a>
    @endif
    <button type="button" onclick="reload_ajax()" class="btn btn-sm btn-flat btn-outline-secondary"><i class="fa fa-refresh"></i> Reload</button>
    </div>

    <div class="col-sm-4" style="text-align: center">
        <select name="status_ujian" id="status_ujian" class="select2" >
            <option value="active">ACTIVE</option>
            <option value="close">CLOSE</option>
            <option value="expired">EXPIRED</option>
            <option value="semua">SEMUA</option>
        </select>
    </div>

    <div class="col-sm-4">
    @if(is_admin())
    <div class="pull-right">
        <button type="button" onclick="bulk_delete()" class="btn btn-sm btn-flat btn-danger"><i class="fa fa-trash"></i> Bulk Delete</button>
    </div>
    @endif
    </div>

</div>

<div class="row">
    <div class="col-md-12">
	<?=form_open('ujian/delete', array('id'=>'bulk'))?>
    <div class="table-responsive pb-2">
        <table id="ujian" class="table table-striped table-bordered table-hover w-100">
        <thead>
            <tr>
				<th class="text-center">
					<input type="checkbox" class="select_all">
				</th>
                <th>Nama Ujian</th>
                <th>Materi Ujian</th>
                <th>Status</th>
                <th>Soal</th>
                <th>Wkt Mulai</th>
                <th>Wkt Selesai</th>
                <th>Lama Ujian</th>
                <th>Acak Soal/Jwbn</th>
                <th>Oleh</th>
                <th	class="text-center">Token</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
{{--        <tfoot>--}}
{{--            <tr>--}}
{{--				<th class="text-center">--}}
{{--					<input type="checkbox" class="select_all">--}}
{{--				</th>--}}
{{--                <th>Nama Ujian</th>--}}
{{--                <th>Materi Ujian</th>--}}
{{--                <th>Status</th>--}}
{{--                <th>Soal</th>--}}
{{--                <th>Wkt Mulai</th>--}}
{{--                <th>Wkt Selesai</th>--}}
{{--                <th>Lama Ujian</th>--}}
{{--                <th>Acak Soal/Jwbn</th>--}}
{{--                <th>Oleh</th>--}}
{{--                <th	class="text-center">Token</th>--}}
{{--                <th class="text-center">Aksi</th>--}}
{{--            </tr>--}}
{{--        </tfoot>--}}
        </table>
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
