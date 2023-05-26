@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">--}}
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
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>--}}
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

function init_page_level(){
    let table;
    table = $("#matkul").DataTable({
        "lengthMenu": [
            [5, 10, 25, 50, 100, -1],
            [5, 10, 25, 50, 100, "All"]
        ],
    });
}

</script>
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_custom_js')
<!-- BEGIN PAGE CUSTOM JS-->
<style type="text/css">

</style>
<!-- END PAGE CUSTOM JS-->
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
            <div class="col-md-6 offset-md-3">
                <div class="alert bg-info">
                    <ul class="">
                        <li>Silahkan import data dari excel, menggunakan format yang sudah disediakan</li>
                        <li>Data tidak boleh ada yang kosong, harus terisi semua.</li>
                        <li>Untuk data Mata Kuliah, hanya bisa diisi menggunakan ID Mata Kuliah.</li>
                    </ul>
                </div>
                <div class="text-center">
                    <a data-toggle="modal" href="#matkulId" style="text-decoration:none" class="btn btn-outline-primary btn-glow">Lihat ID</a>
                    <a href="<?= base_url('uploads/import/format/dosen.xlsx') ?>" class="btn btn-outline-info btn-glow">Download Format</a>
                </div>
                <br>
                <div class="text-center">
                    <?= form_open_multipart('dosen/preview'); ?>
                    <label for="file" class="">Pilih File</label>
                    <div class="form-group">
                        <input type="file" name="upload_file" style="border: 3px solid #000; padding: 3px;">
                    </div>
                    <button name="preview" type="submit" class="btn btn-sm btn-success"><i class="fa fa-th"></i> Preview</button> <a href="{{ site_url('dosen') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i> Batal</a>
                     <?= form_close(); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (isset($_POST['preview'])) : ?>
                    <br>
                    <h4>Preview Data</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>NIP</td>
                                <td>Nama</td>
                                <td>Email</td>
                                <td>ID Mata Kuliah</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $status = true;
                    if (empty($import)) {
                        echo '<tr><td colspan="2" class="text-center">Data kosong! pastikan anda menggunakan format yang telah disediakan.</td></tr>';
                    } else {
                        $no = 1;
                        foreach ($import as $data) :
                            ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td class="<?= $data['nip'] == null ? 'bg-danger' : ''; ?>">
                                            <?= $data['nip'] == null ? 'BELUM DIISI' : $data['nip']; ?>
                                        </td>
                                        <td class="<?= $data['nama_dosen'] == null ? 'bg-danger' : ''; ?>">
                                            <?= $data['nama_dosen'] == null ? 'BELUM DIISI' : $data['nama_dosen']; ?>
                                        </td>
                                        <td class="<?= $data['email'] == null ? 'bg-danger' : ''; ?>">
                                            <?= $data['email'] == null ? 'BELUM DIISI' : $data['email']; ?>
                                        </td>
                                        <td class="<?= $data['matkul_id'] == null ? 'bg-danger' : ''; ?>">
                                            <?= $data['matkul_id'] == null ? 'BELUM DIISI' : $data['matkul_id']; ?>
                                        </td>
                                    </tr>
                            <?php
                            if ($data['nip'] == null || $data['nama_dosen'] == null || $data['email'] == null || $data['matkul_id'] == null) {
                                $status = false;
                            }
                        endforeach;
                    }
            	?>
                        </tbody>
                    </table>
                    <?php if ($status) : ?>

                        <?= form_open('dosen/do_import', null, ['data' => json_encode($import)]); ?>
                        <button type='submit' class='btn btn-block btn-flat btn-primary'><i class="fa fa-arrow-circle-o-down"></i> Import</button>
                        <?= form_close(); ?>

                    <?php endif; ?>
                    <br>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="matkulId">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Data Mata Kuliah</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <table id="matkul" class="table table-bordered table-condensed table-striped">
                    <thead>
                        <th>ID</th>
                        <th>Mata Kuliah</th>
                    </thead>
                    <tbody>
                        <?php foreach ($matkul as $m) : ?>
                            <tr>
                                <td><?= $m->id_matkul; ?></td>
                                <td><?= $m->nama_matkul; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
