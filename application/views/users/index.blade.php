@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/npm/node_modules/bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/icheck.css') }}">
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('assets/npm/node_modules/datatables.net-plugins/api/fnPagingInfo.js') }}"></script>
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<script src="{{ asset('assets/npm/node_modules/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/npm/node_modules/bootstrap4-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/npm/node_modules/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/icheck/icheck.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
let user_id = '{{ $user->id }}';

let ok_submit_pengawas = false;
let ok_submit_penyusun_soal = false;
let nip_pengawas = '';

$(document).on('click','#btn_tambah_pengawas',function(){
    $('#input_pegawai').val('');
    $('#nm_lengkap').val('');
    $('#unit').val('');
    $('#tgl_lahir').val('');
    $('#modal_tambah_pengawas').modal('show');
    ok_submit_pengawas = false;
    nip_pengawas = '';
});

$(document).on('click','#btn_tambah_penyusun_soal',function(){
    $('#nm_lengkap_ps').val('');
    $('#tgl_lahir_ps').val('');
    $('#email_ps').val('');
    $('#modal_tambah_penyusun_soal').modal('show');
    ok_submit_penyusun_soal = false;
    validator.resetForm();
});

$(document).on('click','#btn_cari_pegawai',function(){
    $('#nm_lengkap').val('');
    $('#unit').val('');
    $('#tgl_lahir').val('');
    let input_pegawai = $('#input_pegawai').val();
    if(input_pegawai.trim() == ''){
        return false;
    }
    ajx_overlay(true);
    $.ajax({
            url: '{{ url('users/ajax/cari_pegawai') }}',
            data: {'nip': input_pegawai},
            type: 'POST',
            success: function (res) {
                // res = $.parseJSON(res);
                let r = res.record;
                if ($.isEmptyObject(r)) {
                    Swal.fire({
                        title: "Perhatian",
                        text: "Pegawai tidak ditemukan",
                        icon: "warning"
                    });
                    ok_submit_pengawas = false;
                } else {
                    $('#nm_lengkap').val(r.nama);
                    $('#unit').val(r.unit);
                    $('#tgl_lahir').val(r.tgl_lahir);
                    ok_submit_pengawas = true;
                    nip_pengawas = r.nip;
                }
            },
        error: function () {
            Swal.fire({
                title: "Perhatian",
                text: "Terjadi kesalahan",
                icon: "warning"
            });
            ok_submit_pengawas = false;
        },
        complete: function(){
            ajx_overlay(false);
        }
    })
});

$(document).on('click','#btn_simpan_pengawas',function(){
    let is_koord_pengawas = $('#is_koord_pengawas').is(':checked') ? 1 : 0 ;
    if(ok_submit_pengawas && (nip_pengawas != '')){
        ajx_overlay(true);
        $.ajax({
            url: '{{ url('users/ajax/save_pengawas') }}',
            data: {'nip': nip_pengawas, 'is_koord_pengawas' : is_koord_pengawas},
            type: 'POST',
            success: function (res) {
                if(res.status){
                    reload_ajax();
                    $('#modal_tambah_pengawas').modal('hide');
                    Swal.fire({
                        title: "Perhatian",
                        text: "Pengawas telah ditambahkan",
                        icon: "success"
                    });
                }else{
                    Swal.fire({
                        title: "Perhatian",
                        text: res.msg,
                        icon: "warning"
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: "Perhatian",
                    text: "Terjadi kesalahan",
                    icon: "warning"
                });
            },
            complete: function(){
                ajx_overlay(false);
            }
        });
    }else{
        Swal.fire({
            title: "Perhatian",
            text: "Pegawai tidak ditemukan",
            icon: "warning"
        });
    }
});

jQuery.validator.addMethod("valid_email", function(value, element) {
    // allow any non-whitespace characters as the host part
    return this.optional( element ) || /\S+@\S+\.\S+/.test( value );
}, 'Please enter a valid email');

jQuery.validator.addMethod("valid_date", function(value, element) {
    // allow any non-whitespace characters as the host part
    return this.optional( element ) || /{{ REGEX_DATE_VALID }}/.test( value );
}, 'Please enter a valid date');

let validator = $("#form_tambah_penyusun_soal").validate({
    debug: false,
    ignore: [],
    rules: {
        'nm_lengkap_ps': {required: true},
        'email_ps': {required: true, valid_email: true},
        'tgl_lahir_ps': {required: true, valid_date: true},
    },
    messages: {
        'nm_lengkap_ps': {
            required: "tidak boleh kosong",
        },
        'email_ps': {
            required: "tidak boleh kosong",
            valid_email: "email yg dimasukan salah"
        },
        'tgl_lahir_ps': {
            required: "tidak boleh kosong",
        },
    },
    errorElement: "small",
    // <p class="badge-default badge-danger block-tag text-right"><small class="block-area white">Helper aligned to right</small></p>
    errorPlacement: function ( error, element ) {
        error.addClass("badge-default badge-danger block-tag pl-2");
        // error.css('display','block');
        if ( element.prop("type") === "radio" ) {
            error.appendTo(element.siblings(".error_radio"));
        } else if ( element.hasClass("only_input_select2multi")) {
            // error.insertAfter(element.parent().parent().parent().siblings(".error_select2"));
            error.css('display','block');
            error.insertAfter(element.siblings(".error_select2"));
            // error.insertAfter(element);
        } else if ( element.hasClass("only_input_select2single")) {
            // error.insertAfter(element.parent().parent().parent().siblings(".error_select2"));
            error.css('display','block');
            error.insertAfter(element.siblings(".error_select2"));
            // error.insertAfter(element);
        } else if ( element.prop("type") === "checkbox" ) {
            error.appendTo(element.siblings(".error_checkbox"));
        } else {
            error.insertAfter(element);
            element.addClass('border-danger');
        }
    },
    highlight: function ( element, errorClass, validClass ) {
        // $(element).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
        $(element).addClass('border-danger');
    },
    unhighlight: function (element, errorClass, validClass) {
        // $(element).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
        $(element).removeClass('border-danger');
    },
    submitHandler: function(form) {
        if(confirm('Yakin akan menambah penyusun soal ?')){
            form.submit();
        }
    }
});

$(document).on('click','#btn_simpan_penyusun_soal',function(){
    let valid = validator.form();
    
    if(valid){
    
        let nm_lengkap_ps = $('#nm_lengkap_ps').val();
        let tgl_lahir_ps = $('#tgl_lahir_ps').val();
        let email_ps = $('#email_ps').val();
        
        ajx_overlay(true);
        $.ajax({
            url: '{{ url('users/ajax/save_penyusun_soal') }}',
            data: {'nm_lengkap': nm_lengkap_ps, 'tgl_lahir': tgl_lahir_ps, 'email': email_ps},
            type: 'POST',
            success: function (res) {
                if(res.status){
                    reload_ajax();
                    $('#modal_tambah_penyusun_soal').modal('hide');
                    Swal.fire({
                        title: "Perhatian",
                        text: "Penyusun_soal telah ditambahkan",
                        icon: "success"
                    });
                }else{
                    Swal.fire({
                        title: "Perhatian",
                        text: res.msg,
                        icon: "warning"
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: "Perhatian",
                    text: "Terjadi kesalahan",
                    icon: "warning"
                });
            },
            complete: function(){
                ajx_overlay(false);
            }
        });

    }
});

$(document).on('click','.btn_loginas',function(){
    let id_user = $(this).data('id');
    let nama = $(this).data('nama');
    Swal.fire({
        title: "Perhatian",
        text: "Anda akan masuk sebagai user : " + nama ,
        icon: "warning",
        confirmButtonText: "Masuk Sekarang",
        allowOutsideClick: false,
        allowEscapeKey: false,
        showCancelButton: true,
    }).then(result => {
        if (result.value) {
            window.location.href = '{{ url('auth/login_as/') }}' + id_user;
        }
    });
});

function init_page_level(){
    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-red',
        radioClass: 'iradio_square-red',
    });
}

</script>
<script src="{{ asset('assets/dist/js/app/users/index.js') }}"></script>
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
    <div class="col-12">
        <div class="mb-3">
            <button type="button" onclick="reload_ajax()" class="btn btn-sm btn-flat btn-outline-secondary"><i class="fa fa-refresh"></i> Reload</button>
            @if($_ENV['IS_ENABLE_ADD_PENGAWAS'])
            <button type="button" class="btn btn-sm btn-flat btn-primary" id="btn_tambah_pengawas"><i class="fa fa-plus-circle"></i> Tambah Pengawas</button>
            @endif
            @if(is_admin())
            <button type="button" class="btn btn-sm btn-flat btn-info" id="btn_tambah_penyusun_soal"><i class="fa fa-plus-circle"></i> Tambah Penyusun Soal</button>
            <div class="pull-right">
                <label for="show_me">
                    <input type="checkbox" id="show_me" class="icheck">
                    Tampilkan saya
                </label>
            </div>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="table-responsive pb-2">
        <table id="users" class="table table-striped table-bordered table-hover w-100">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Level</th>
                    <th>Created On</th>
                    <th class="text-center">Stts</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
        </table>
        </div>
    </div>
</div>
<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal text-left"
     id="modal_tambah_pengawas"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel11"
     aria-hidden="true">
    <div class="modal-dialog"
         role="document">
        <div class="modal-content">
            <div class="modal-header bg-info white">
                <h4 class="modal-title white"
                    id="myModalLabel11">Tambah Pengawas</h4>
            </div>
            <div class="modal-body">
                <form class="form">
                    <div class="form-body">
                        <h4 class="form-section"><i class="fa fa-eye"></i> Identitas</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="input_pegawai">NIP</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="input_pegawai" placeholder="Masukan nip disini" aria-label="" name="nip">
                                        <div class="input-group-append" id="btn_cari_pegawai" style="cursor: pointer">
                                            <span class="input-group-text bg-info text-white">Cari</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nm_lengkap">Nama Lengkap</label>
                                    <input readonly="readonly" type="text" id="nm_lengkap" class="form-control border-primary" placeholder="Nama Lengkap" name="nm_lengkap">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="unit">Unit</label>
                                    <input readonly="readonly" type="text" id="unit" class="form-control border-primary" placeholder="Unit" name="unit">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tgl_lahir">Tgl Lahir <small class="text-muted"><b class="text-danger">***</b> Digunakan sbg password default</small></label>
                                    <input readonly="readonly" type="text" id="tgl_lahir" class="form-control border-primary" placeholder="Tgl Lahir" name="tgl_lahir">
                                </div>
                            </div>
                        </div>
                        @if(is_admin())
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="is_koord_pengawas" style="display: block">Koord Pengawas</label>
                                    <input type="checkbox" id="is_koord_pengawas" class="icheck"> &nbsp;&nbsp; Jadikan sbg koordinator pengawas
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn grey btn-outline-secondary"
                        data-dismiss="modal">Batal
                </button>
                <button type="button"
                        class="btn btn-outline-info" id="btn_simpan_pengawas">Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal text-left"
     id="modal_tambah_penyusun_soal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalPenyusunSoal"
     aria-hidden="true">
    <div class="modal-dialog"
         role="document">
        <div class="modal-content">
            <div class="modal-header bg-info white">
                <h4 class="modal-title white"
                    id="myModalPenyusunSoal">Tambah Penyusun Soal</h4>
            </div>
            <div class="modal-body">
                <form id="form_tambah_penyusun_soal" class="form">
                    <div class="form-body">
                        <h4 class="form-section"><i class="fa fa-eye"></i> Identitas</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nm_lengkap_ps">Nama Lengkap</label>
                                    <input type="text" id="nm_lengkap_ps" class="form-control border-primary" placeholder="Nama Lengkap" name="nm_lengkap_ps">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tgl_lahir_ps">Tgl Lahir <small class="text-muted"><b class="text-danger">***</b> Digunakan sbg password default</small></label>
                                    <input type="text" id="tgl_lahir_ps" class="form-control border-primary datetimepicker" placeholder="Tgl Lahir" name="tgl_lahir_ps">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email_ps">Email</label>
                                    <input type="text" id="email_ps" class="form-control border-primary" placeholder="Email" name="email_ps">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn grey btn-outline-secondary"
                        data-dismiss="modal">Batal
                </button>
                <button type="button"
                        class="btn btn-outline-info" id="btn_simpan_penyusun_soal">Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
