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
<script src="//cdn.datatables.net/plug-ins/1.10.21/api/fnPagingInfo.js"></script>
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
                        <li>Data tidak boleh ada yang kosong, harus terisi semua</li>
                        <li>Data Materi Ujian harus sesuai dengan ID yang ada</li>
                        <li>Maks. ukuran file yg diupload 5mb</li>
                    </ul>
                </div>
                <div class="text-center">
                    <a data-toggle="modal" href="#materiId" style="text-decoration:none" class="btn btn-outline-primary btn-glow">ID MATKUL</a>
                    <a href="<?= base_url('uploads/import/format/mahasiswa.xlsx') ?>" class="btn btn-outline-info btn-glow">FORMAT EXCEL</a>
                    <a href="<?= site_url('mahasiswa/edit_on_table') ?>" class="btn btn-outline-success btn-glow">TABEL</a>
                </div>
                <br>
                <div class="text-center">
                    <?= form_open_multipart('mahasiswa/preview'); ?>
                    <label for="file" class="">Pilih File</label>
                    <div class="form-group">
                        <input type="file" name="upload_file" style="border: 3px solid #000; padding: 3px;">
                    </div>
                    <button name="preview" type="submit" class="btn btn-sm btn-success"><i class="fa fa-th"></i> Preview</button> <a href="{{ site_url('mahasiswa') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i> Batal</a>
                     <?= form_close(); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?php if (isset($_POST['preview'])) : ?>
                    <hr>
                    <div class="alert bg-danger">
                        <p>Perhatian :</p>
                        <ul class="">
                            <li>Apabila ditemukan data !! ERROR !! , silahkan perbaiki data terlebih dahulu sebelum bisa diimpor.</li>
                            <li>Data No Peserta tepat {{ MHS_ID_LENGTH }} karakter</li>
                            <li>Data Nama min. 3 karakter dan maks. 250 karakter</li>
                            <li>Data Email maks. 250 karakter</li>
                            <li>Data No Billkey tepat {{ NO_BILLKEY_LENGTH }} karakter</li>
                            <li>Data Jk hanya berisi L atau P</li>
                            <li>Data Materi Ujian harus sesuai dengan ID yang ada</li>
                        </ul>
                    </div>
                    <div style="overflow-x: scroll">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>No Peserta</td>
                                <td>Nama</td>
                                <td>NIK</td>
                                <td>Tmp Lahir</td>
                                <td>Tgl Lahir</td>
                                <td>Email</td>
                                <td>No Billkey</td>
                                <td>Foto</td>
                                <td>Jk</td>
                                <td>Materi Ujian</td>
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
                                        <td class="<?= ($data['nim'] == null || $data['nim'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['nim'] == null || $data['nim'] == '!! ERROR !!') ? '!! ERROR !!' : $data['nim']; ?>
                                        </td>
                                        <td class="<?= ($data['nama'] == null || $data['nama'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['nama'] == null || $data['nama'] == '!! ERROR !!') ? '!! ERROR !!' : $data['nama']; ?>
                                        </td>
                                        <td class="<?= ($data['nik'] == null || $data['nik'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['nik'] == null || $data['nik'] == '!! ERROR !!') ? '!! ERROR !!' : $data['nik']; ?>
                                        </td>
                                        <td class="<?= ($data['tmp_lahir'] == null || $data['tmp_lahir'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['tmp_lahir'] == null || $data['tmp_lahir'] == '!! ERROR !!') ? '!! ERROR !!' : $data['tmp_lahir']; ?>
                                        </td>
                                        <td class="<?= ($data['tgl_lahir'] == null || $data['tgl_lahir'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['tgl_lahir'] == null || $data['tgl_lahir'] == '!! ERROR !!') ? '!! ERROR !!' : $data['tgl_lahir']; ?>
                                        </td>
                                        <td class="<?= ($data['email'] == null || $data['email'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['email'] == null || $data['email'] == '!! ERROR !!') ? '!! ERROR !!' : $data['email']; ?>
                                        </td>
                                         <td class="<?= ($data['no_billkey'] == null || $data['no_billkey'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['no_billkey'] == null || $data['no_billkey'] == '!! ERROR !!') ? '!! ERROR !!' : $data['no_billkey']; ?>
                                        </td>
                                        <td class="<?= ($data['foto'] == null || $data['foto'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['foto'] == null || $data['foto'] == '!! ERROR !!') ? '!! ERROR !!' : '<img style="width:80px; height:100px;" src="' . $data['foto'] . '" />'; ?>
                                        </td>
                                        <td class="<?= ($data['jenis_kelamin'] == null || $data['jenis_kelamin'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['jenis_kelamin'] == null || $data['jenis_kelamin'] == '!! ERROR !!') ? '!! ERROR !!' : $data['jenis_kelamin']; ?>
                                        </td>
                                        <td class="<?= ($data['matkul'] == null || $data['matkul'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            @forelse($data['matkul'] as $matkul)
                                                <span class="badge bg-success">{{ $matkul->nama_matkul }}</span>
                                            @empty
                                                !! ERROR !!
                                            @endforelse
                                        </td>
                                    </tr>
                            <?php
                                        if ($data['nim'] == null || $data['nama'] == null || $data['email'] == null || $data['no_billkey'] == null || $data['jenis_kelamin'] == null || $data['matkul'] == null) {
                                            $status = false;
                                        }

                                        if ($data['nim'] == '!! ERROR !!' || $data['nama'] == '!! ERROR !!' || $data['email'] == '!! ERROR !!' || $data['no_billkey'] == '!! ERROR !!' || $data['jenis_kelamin'] == '!! ERROR !!' || $data['matkul'] == '!! ERROR !!') {
                                            $status = false;
                                        }

                                    endforeach;
                                }
                                ?>
                        </tbody>
                    </table>
                    </div>
                    <?php if ($status) : ?>
                        <?= form_open('mahasiswa/do_import', null, ['data' => json_encode($import)]); ?>
                        <button type='submit' class='btn btn-block btn-flat btn-primary'><i class="fa fa-arrow-circle-o-up"></i> Import</button>
                        <?= form_close(); ?>
                    <?php endif; ?>
                    <br>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="materiId">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Data Materi Ujian</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <table id="matkul" class="table table-bordered table-condensed table-striped">
                    <thead>
                        <th>ID</th>
                        <th>Materi Ujian</th>
                    </thead>
                    <tbody>
                        <?php foreach ($matkul_list as $m) : ?>
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
