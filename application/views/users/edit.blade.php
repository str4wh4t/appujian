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

$('form#form_reset_password_by_admin').on('submit', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

    Swal.fire({
        title: "Anda yakin?",
        text: "Password akan direset!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Reset!"
    }).then(result => {
        if (result.value) {
            let btn = $('#btn-reset-password-by-admin');
            btn.attr('disabled', 'disabled').text('Process..');

            url = $(this).attr('action');
            data = $(this).serialize();
            $.ajax({
                url: url,
                data: data,
                type: 'POST',
                success: function (response) {
                    if (response.status) {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Password berhasil direset",
                            icon: "success"
                        });
                    } else {
                        if (response.msg) {
                            Swal.fire({
                                title: "Gagal",
                                text: "Password lama tidak benar",
                                icon: "error"
                            });
                        }
                    }
                    btn.removeAttr('disabled').text('Reset Password');
                }
            });
        }
    });
});

$(document).on('click','#btn-unlock',function(){
    Swal.fire({
        title: "Anda yakin?",
        text: "User tsb akan di-unlock!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Unlock!"
    }).then(result => {
        if (result.value) {
            ajaxcsrf();
            $.ajax({
                url: "{{ url('users/unlock') }}",
                data: {'id' : '{{ $user_cari->id }}'},
                type: 'POST',
                success: function (response) {
                    if (response.status) {
                        Swal.fire({
                            title: "Berhasil",
                            text: "User tsb berhasil di-unlock",
                            icon: "success"
                        }).then(result => {
                            location.reload(); 
                        });
                    } else {
                        Swal.fire({
                            title: "Gagal",
                            text: response.msg,
                            icon: "error"
                        });
                    }
                }
            });
        }
    });
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
        <div class="row">
            <div class="col-md-2">
                <a href="{{ site_url('users') }}" class="btn btn-warning">
                    <i class="fa fa-arrow-left"></i> Batal
                </a>
            </div>
            <div class="col-md-10">
                <div class="alert bg-info"><b>User :</b> {{ $user_cari->full_name }} ( {{ $user_cari->username }} ), <b>Level :</b> {{ strtoupper($level->name) }}</div>
            </div>
        </div>
       <div class="row">

            @if(is_admin() || in_group(KOORD_PENGAWAS_GROUP_ID))
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert mt-2" style="border: 1px solid #ccc; height: 250px">
                        <?=form_open('users/edit_status', array('id'=>'user_status'), array('id'=>$user_cari->id))?>
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Status</h3>
                                <hr>
                            </div>
                            <div class="box-body pb-0">
                                @if($is_locked_user_cari)
                                <div class="form-group">
                                    <div class="alert " style="border: 1px solid #ff0000; background-color: #ffeded;">
                                        <b><span class="text-danger">Perhatian : </span></b><hr>User tsb sedang terkunci
                                    </div>
                                </div>
                                @else
                                <div class="alert " style="border: 1px solid #ff0000; background-color: #ffeded;">
                                    <b><span class="text-danger">**</span></b>  User tidak aktif tidak dapat login
                                </div>
                                <div class="form-group">
                                    <select id="status" name="status" class="form-control select2" style="width: 100%!important">
                                        @php($status_list = ["0" => "Non Aktif", "1" => "Aktif"])
                                        @foreach ($status_list as $val => $status)
                                            <option {{ $user_cari->active == $val ? 'selected="selected"' : '' }} value="{{ $val }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <small class="help-block"></small>
                                </div>
                                @endif
                            </div>
                            <div class="box-footer">
                                @if($is_locked_user_cari)
                                <button type="button" id="btn-unlock" class="btn btn-primary"><i class="ft-unlock"></i> Unlock</button>
                                @else
                                <button type="submit" id="btn-status" class="btn btn-success">Simpan</button>
                                @endif
                            </div>
                        </div>
                        <?=form_close()?>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-4">
                    <div class="alert mt-2" style="border: 1px solid #ccc; height: 250px">
                        <?=form_open('users/reset_password_by_admin', array('id'=>'form_reset_password_by_admin'), array('id'=>$user_cari->id))?>
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Reset Password</h3>
                                <hr>
                            </div>
                            <div class="box-body pb-0">
                                <div class="form-group">
                                    <div class="alert " style="border: 1px solid #ff0000; background-color: #ffeded;">
                                        @if($level->id == MHS_GROUP_ID)
                                        <b><span class="text-danger">Perhatian : </span></b><hr>Password = no_billkey [ {{ $user_cari->no_billkey }} ]
                                        @else
                                        <b><span class="text-danger">Perhatian : </span></b><hr>Password = tgl_lahir [ {{ $user_cari->tgl_lahir }} ]
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" id="btn-reset-password-by-admin" class="btn btn-success">Reset Password</button>
                            </div>
                        </div>
                        <?=form_close()?>
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
