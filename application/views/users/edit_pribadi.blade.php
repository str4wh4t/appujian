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

$(document).on('submit','form#change_password', function(e){
    e.preventDefault();
    e.stopImmediatePropagation();

    let btn = $('#btn-pass');
    btn.attr('disabled', 'disabled').text('Process...');

    url = $(this).attr('action');
    data = $(this).serialize();
    msg = "Password anda berhasil diganti";
    submitajax(url, data, msg, btn);
});

</script>
<script src="{{ asset('assets/dist/js/app/users/edit.js') }}"></script>
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
    <div class="col-md-12">
            <div class="col-md-4">
                <div class="alert mt-2" style="border: 1px solid #ccc">
                    {!! form_open('users/change_password', array('id'=>'change_password'), array('id'=>$user_login->id)) !!}
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Edit Password</h3>
                            <hr>
                        </div>
                        <div class="box-body pb-0">
                            <div class="form-group">
                                <label for="old">Password Lama</label>
                                <input type="password" placeholder="Password Lama" name="old" class="form-control">
                                <small class="help-block"></small>
                            </div>
                            <div class="form-group">
                                <label for="new">Password Baru</label>
                                <input type="password" placeholder="Password Baru" name="new" class="form-control">
                                <small class="help-block"></small>
                            </div>
                            <div class="form-group">
                                <label for="new_confirm">Konfirmasi Password</label>
                                <input type="password" placeholder="Konfirmasi Password Baru" name="new_confirm" class="form-control">
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" id="btn-pass" class="btn btn-flat btn-warning">Simpan</button>
                        </div>
                    </div>
                    {!! form_close() !!}
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
