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
@if($user->id === $users->id)
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
@endif

@if(is_admin())
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
            btn.attr('disabled', 'disabled').text('Process...');

            url = $(this).attr('action');
            data = $(this).serialize();
            msg = "Password berhasil direset";
            $.ajax({
                url: url,
                data: data,
                type: 'POST',
                success: function (response) {
                    if (response.status) {
                        Swal.fire({
                            title: "Berhasil",
                            text: msg,
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
@endif

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
        @if(is_admin())
        <div class="row">
            <div class="col-md-2">
                <a href="{{ site_url('users') }}" class="btn btn-warning">
                    <i class="fa fa-arrow-left"></i> Batal
                </a>
            </div>
            <div class="col-md-10">
                <div class="alert bg-info"><b>User :</b> {{ $users->full_name }} ( {{ $users->username }} ), <b>Level :</b> {{ strtoupper($level->name) }}</div>
            </div>
        </div>
        @endif
       <div class="row">
        @if(is_admin())
            <!--
            <div class="col-md-4">
                <div class="alert mt-2" style="border: 1px solid #ccc">
                <?=form_open('users/edit_info', array('id'=>'user_info'), array('id'=>$users->id))?>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Data User</h3>
                        <hr>
                    </div>
                    <div class="box-body pb-0">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" class="form-control" disabled="disabled" value="<?=$users->username?>">
                            <small class="help-block"></small>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="first_name">Nama</label>
                                <input type="text" name="full_name" class="form-control" value="<?=$users->full_name?>">
                                <small class="help-block"></small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" value="<?=$users->email?>">
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" id="btn-info" class="btn btn-info">Simpan</button>
                    </div>
                </div>
                <?=form_close()?>
                </div>
            </div>
            -->
            @if($user->id !== $users->id)
            <div class="col-md-4">
                <!--
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert mt-2" style="border: 1px solid #ccc">
                            <?=form_open('users/edit_level', array('id'=>'user_level'), array('id'=>$users->id))?>
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Level</h3>
                                    <hr>
                                </div>
                                <div class="box-body pb-0">
                                    <div class="form-group">
{{--                                        <select id="level" name="level" class="form-control select2" style="width: 100%!important">--}}
                                        <select id="level" name="level" class="form-control select2" disabled="disabled" style="width: 100%!important">
                                            <option value="">Pilih Level</option>
                                            <?php foreach ($groups as $row) : ?>
                                                <option <?=$level->id===$row->id ? "selected" : ""?> value="<?=$row->id?>"><?=$row->name?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="help-block"></small>
                                    </div>
                                </div>
                                <div class="box-footer">
{{--                                    <button type="submit" id="btn-level" class="btn btn-primary">Simpan</button>--}}
                                </div>
                            </div>
                            <?=form_close()?>
                        </div>
                    </div>
                </div>
                -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert mt-2" style="border: 1px solid #ccc; height: 250px">
                        <?=form_open('users/edit_status', array('id'=>'user_status'), array('id'=>$users->id))?>
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Status</h3>
                                <hr>
                            </div>
                            <div class="alert " style="border: 1px solid #ff0000; background-color: #ffeded;">
                                <b><span class="text-danger">**</span></b>  User tidak aktif tidak dapat login
                            </div>
                            <div class="box-body pb-0">
                                <div class="form-group">
                                    <select id="status" name="status" class="form-control select2" style="width: 100%!important">
                                        @php($status_list = ["0" => "Non Aktif", "1" => "Aktif"])
                                        @foreach ($status_list as $val => $status)
                                            <option {{ $users->active == $val ? 'selected="selected"' : '' }} value="{{ $val }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" id="btn-status" class="btn btn-success">Simpan</button>
                            </div>
                        </div>
                        <?=form_close()?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                    <div class="alert mt-2" style="border: 1px solid #ccc; height: 250px">
                        <?=form_open('users/reset_password_by_admin', array('id'=>'form_reset_password_by_admin'), array('id'=>$users->id))?>
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Reset Password</h3>
                                <hr>
                            </div>
                            <div class="box-body pb-0">
                                <div class="form-group">
                                    <div class="alert " style="border: 1px solid #ff0000; background-color: #ffeded;">
                                        @if($level->id == MHS_GROUP_ID)
                                        <b><span class="text-danger">Perhatian : </span></b><hr>Password = no_billkey [ {{ $users->no_billkey }} ]
                                        @else
                                        <b><span class="text-danger">Perhatian : </span></b><hr>Password = tgl_lahir [ {{ $users->tgl_lahir }} ]
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
            @else
            <div class="col-md-4">
                <div class="alert mt-2" style="border: 1px solid #ccc">
                    {!! form_open('users/change_password', array('id'=>'change_password'), array('id'=>$users->id)) !!}
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Ubah Password</h3>
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
            @endif
        @else
{{--            <div class="col-md-4">--}}
{{--                <div class="alert mt-2" style="border: 1px solid #ccc">--}}
{{--                <?=form_open('users/edit_info', array('id'=>'user_info'), array('id'=>$users->id))?>--}}
{{--                <div class="box box-info">--}}
{{--                    <div class="box-header with-border">--}}
{{--                        <h3 class="box-title">Data User</h3>--}}
{{--                        <hr>--}}
{{--                    </div>--}}
{{--                    <div class="box-body pb-0">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="username">Username</label>--}}
{{--                            <input type="text" name="username" class="form-control" disabled="disabled" value="<?=$users->username?>">--}}
{{--                            <small class="help-block"></small>--}}
{{--                        </div>--}}
{{--                        <div class="row">--}}
{{--                            <div class="form-group col-md-12">--}}
{{--                                <label for="first_name">Nama</label>--}}
{{--                                <input type="text" name="full_name" class="form-control" value="<?=$users->full_name?>">--}}
{{--                                <small class="help-block"></small>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="email">Email</label>--}}
{{--                            <input type="email" name="email" class="form-control" value="<?=$users->email?>">--}}
{{--                            <small class="help-block"></small>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="box-footer">--}}
{{--                        <button type="submit" id="btn-info" class="btn btn-info">Simpan</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <?=form_close()?>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="col-md-4">
                <div class="alert mt-2" style="border: 1px solid #ccc">
                    {!! form_open('users/change_password', array('id'=>'change_password'), array('id'=>$users->id)) !!}
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Ubah Password</h3>
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
