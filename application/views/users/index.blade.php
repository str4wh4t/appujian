@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.21/api/fnPagingInfo.js"></script>

{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
let user_id = '{{ $user->id }}';

let ok_submit_pengawas = false;
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

$(document).on('click','#btn_cari_pegawai',function(){
    $('#nm_lengkap').val('');
    $('#unit').val('');
    $('#tgl_lahir').val('');
    let input_pegawai = $('#input_pegawai').val();
    if(input_pegawai.trim() == ''){
        return false;
    }

    $.ajax({
            url: '{{ url('users/ajax/cari_pegawai') }}',
            data: {'nip': input_pegawai},
            type: 'POST',
            success: function (res) {
                // res = $.parseJSON(res);
                let r = res.record;
                if ($.isEmptyObject(r)) {
                    Swal({
                        title: "Perhatian",
                        text: "Pegawai tidak ditemukan",
                        type: "warning"
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
            Swal({
                title: "Perhatian",
                text: "Terjadi kesalahan",
                type: "warning"
            });
            ok_submit_pengawas = false;
        }
    })
});

$(document).on('click','#btn_simpan_pengawas',function(){
    if(ok_submit_pengawas && (nip_pengawas != '')){
        $.ajax({
            url: '{{ url('users/ajax/save_pengawas') }}',
            data: {'nip': nip_pengawas},
            type: 'POST',
            success: function (res) {
                if(res.status){
                    $('#modal_tambah_pengawas').modal('hide');
                    Swal({
                        title: "Perhatian",
                        text: "Pengawas telah ditambahkan",
                        type: "success"
                    });
                }else{
                    Swal({
                        title: "Perhatian",
                        text: "Terjadi kesalahan",
                        type: "warning"
                    });
                }
            },
            error: function () {
                Swal({
                    title: "Perhatian",
                    text: "Terjadi kesalahan",
                    type: "warning"
                });
            }
        });
    }else{
        Swal({
                title: "Perhatian",
                text: "Pegawai tidak ditemukan",
                type: "warning"
              });

    }
});

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
<div class="box">
    <div class="box-body">
        <div class="mb-3">
            <button type="button" onclick="reload_ajax()" class="btn btn-sm btn-flat btn-outline-secondary"><i class="fa fa-refresh"></i> Reload</button>
            <button type="button" class="btn btn-sm btn-flat btn-primary" id="btn_tambah_pengawas"><i class="fa fa-plus-circle"></i> Tambah Pengawas</button>
            <div class="pull-right">
                <label for="show_me">
                    <input type="checkbox" id="show_me">
                    Tampilkan saya
                </label>
            </div>
        </div>
    </div>
    <div class="table-responsive pb-3" style="">
        <table id="users" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Level</th>
                    <th>Created On</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
{{--            <tfoot>--}}
{{--                <tr>--}}
{{--                    <th>No.</th>--}}
{{--                    <th>Nama</th>--}}
{{--                    <th>Username</th>--}}
{{--                    <th>Email</th>--}}
{{--                    <th>Level</th>--}}
{{--                    <th>Created On</th>--}}
{{--                    <th class="text-center">Status</th>--}}
{{--                    <th class="text-center">Action</th>--}}
{{--                </tr>--}}
{{--            </tfoot>--}}
        </table>
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
                </button>
            </div>
            <div class="modal-body">
                <form class="form">
                    <div class="form-body">
                        <h4 class="form-section"><i class="fa fa-eye"></i> Masukan Pengawas</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="userinput1">NIP</label>
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
                                    <label for="userinput3">Nama Lengkap</label>
                                    <input readonly="readonly" type="text" id="nm_lengkap" class="form-control border-primary" placeholder="Nama Lengkap" name="nm_lengkap">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="userinput4">Unit</label>
                                    <input readonly="readonly" type="text" id="unit" class="form-control border-primary" placeholder="Unit" name="unit">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="userinput4">Tgl Lahir</label>
                                    <input readonly="readonly" type="text" id="tgl_lahir" class="form-control border-primary" placeholder="Tgl Lahir" name="tgl_lahir">
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
                        class="btn btn-outline-info" id="btn_simpan_pengawas">Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
