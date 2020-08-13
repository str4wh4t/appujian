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
$(document).on('click','#btn_sync_pendaftaran',function() {
    $('#modal_sync_mahasiswa').modal('show');
});

$(document).on('click','#btn_proses_sync_mhs',function(){
    {{--$.post('{{ url('mahasiswa/ajax/check_sync') }}', $('#form_sync_mhs').serialize(), function(){--}}

    {{--});--}}
    ajx_overlay(true);
    $.ajax({
        url: '{{ url('mahasiswa/ajax/check_sync') }}',
        data: $('#form_sync_mhs').serialize(),
        type: "POST",
        success: function (respon) {
            Swal({
                title: "Perhatian",
                text: "Data akan di-tambah : " + respon.jml_tambah , // + ", dan di-hapus : " + respon.jml_hapus,
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Proses"
            }).then(result => {
                if (result.value) {
                    ajx_overlay(true);
                    $.ajax({
                        url: '{{ url('mahasiswa/ajax/proses_sync') }}',
                        data: $('#form_sync_mhs').serialize(),
                        type: "POST",
                        success: function (respon) {
                            if (respon.status) {
                                Swal({
                                    title: "Berhasil",
                                    text: "Data berhasil di-sync",
                                    type: "success"
                                });
                            } else {
                                Swal({
                                    title: "Gagal",
                                    text: "Tidak ada data yg di sync",
                                    type: "error"
                                });
                            }
                            reload_ajax();
                            ajx_overlay(false);
                        }
                    });
                }
            });
            ajx_overlay(false);
        }, error: function () {
            Swal({
                title: "Gagal",
                text: "Terjadi kesalahan",
                type: "error"
            });
            ajx_overlay(false);
        }
    });
});
</script>
<script src="{{ asset('assets/dist/js/app/master/mahasiswa/index.js?u=') . mt_rand() }}"></script>
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
		<div class="mb-4">
            @if(APP_ID == 'cat.undip.ac.id')
            <a href="{{ site_url('mahasiswa/add') }}" class="btn btn-sm btn-flat btn-outline-primary"><i class="fa fa-plus"></i> Tambah</a>
            <a href="{{ site_url('mahasiswa/import') }}" class="btn btn-sm btn-flat btn-success"><i class="fa fa-upload"></i> Import</a>
            @endif
            <button type="button" onclick="reload_ajax()" class="btn btn-sm btn-flat btn-outline-secondary"><i class="fa fa-refresh"></i> Reload</button>

            <div class="pull-right">
                @if(APP_ID == 'ujian.undip.ac.id')
                <button class="btn btn-sm btn-flat btn-danger" id="btn_sync_pendaftaran" type="button"><i class="fa fa-refresh"></i> Syncron Data</button>
                @endif
                <button onclick="bulk_delete()" class="btn btn-sm btn-flat btn-danger" type="button"><i class="fa fa-trash"></i> Delete</button>
            </div>

		</div>
        <?= form_open('', array('id' => 'bulk')); ?>
{{--        <div class="table-responsive">--}}
{{--            <table id="mahasiswa" class="table table-striped table-bordered table-hover pb-3">--}}
        <div class="table-responsive pb-3" style="border: 0">
		    <table id="mahasiswa" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>No Peserta</th>
                        <th>Nama</th>
{{--                        <th>Email</th>--}}
                        <th>Materi Ujian</th>
{{--                        <th>Kelas</th>--}}
                        <th>Prodi</th>
                        <th>Aksi</th>
                        <th class="text-center">
                            <input class="select_all" type="checkbox">
                        </th>
                    </tr>
                </thead>
{{--                <tfoot>--}}
{{--                    <tr>--}}
{{--                        <th>No.</th>--}}
{{--                        <th>No Peserta</th>--}}
{{--                        <th>Nama</th>--}}
{{--                        <th>Email</th>--}}
{{--                        <th>Materi Ujian</th>--}}
{{--                        <th>Aksi</th>--}}
{{--                        <th class="text-center">--}}
{{--                            <input class="select_all" type="checkbox">--}}
{{--                        </th>--}}
{{--                    </tr>--}}
{{--                </tfoot>--}}
            </table>
        </div>
        <?= form_close() ?>
    </div>
</div>

<!-- Modal -->
<div class="modal text-left"
     id="modal_sync_mahasiswa"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel11"
     aria-hidden="true">
    <div class="modal-dialog"
         role="document">
        <div class="modal-content">
            <div class="modal-header bg-info white">
                <h4 class="modal-title white"
                    id="myModalLabel11">Sync Mhs</h4>
            </div>
            <div class="modal-body">
                    <?=  form_open('', ['id' => 'form_sync_mhs', 'class' => 'form'], ['method' => 'post']) ?>
                    <div class="form-body">
                        <h4 class="form-section">Jalur Masuk</h4>
                        @forelse($jalur as $j)
                        <label><input type="checkbox" name="jalur[]" value="{{ $j->jalur }}"> {{ $j->kode_jalur }} - {{ $j->jalur }}</label>&nbsp;
                        @empty
                        <label>- data jalur kosong -</label>
                        @endforelse
                        <h4 class="form-section">Gelombang</h4>
                        <label><input type="checkbox" name="gel[]" value="1"> 1</label>&nbsp;
                        <label><input type="checkbox" name="gel[]" value="2"> 2</label>&nbsp;
                        <h4 class="form-section">Tahun</h4>
                        <label><input type="checkbox" name="tahun[]" value="2020"> 2020</label>&nbsp;
                    </div>
                    <?= form_close() ?>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn grey btn-outline-secondary"
                        data-dismiss="modal">Batal
                </button>
                <button type="button"
                        class="btn btn-outline-info" id="btn_proses_sync_mhs">Proses
                </button>
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
