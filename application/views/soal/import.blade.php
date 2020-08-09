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
    table = $("#topik").DataTable({
        "lengthMenu": [
            [5, 10, 25, 50, 100, -1],
            [5, 10, 25, 50, 100, "All"]
        ],
    });

    let table2;
    table2 = $("#bobot").DataTable({
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
                        <li>Data tidak boleh ada yang kosong, harus terisi semua.</li>
                        <li>Untuk data Kelas, hanya bisa diisi menggunakan ID materi ujian.</li>
                        <li>Maks. ukuran file yg diupload 5mb</li>
                    </ul>
                </div>
                <div class="text-center">
                    <a data-toggle="modal" href="#materiId" style="text-decoration:none" class="btn btn-outline-primary btn-glow">ID TOPIK</a>
                    <a data-toggle="modal" href="#bobotId" style="text-decoration:none" class="btn btn-outline-primary btn-glow">ID BOBOT</a>
                    <a href="<?= base_url('uploads/import/format/soal.xlsx') ?>" class="btn btn-outline-info btn-glow">FORMAT EXCEL</a>
                    <a href="<?= site_url('soal/edit_on_table') ?>" class="btn btn-outline-success btn-glow">TABEL</a>
                </div>
                <br>
                <div class="text-center">
                    <?= form_open_multipart('soal/preview'); ?>
                    <label for="file" class="">Pilih File</label>
                    <div class="form-group">
                        <input type="file" name="upload_file" style="border: 3px solid #000; padding: 3px;">
                    </div>
                    <button name="preview" type="submit" class="btn btn-sm btn-success"><i class="fa fa-th"></i> Preview</button>
                    <a href="{{ site_url('soal') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i> Batal</a>
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
                            <li>Data Topik dan Bobot harus sesuai dengan ID yang ada</li>
                            <li>Data Jawaban harus sesuai dengan opsi yang ada</li>
                        </ul>
                    </div>
                    <div style="overflow-x: scroll">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>Topik</td>
                                <td>Soal</td>
                                <td>Opsi A</td>
                                <td>Opsi B</td>
                                <td>Opsi C</td>
                                <td>Opsi D</td>
                                <td>Opsi E</td>
                                <td>Jawaban</td>
                                <td>Bobot</td>
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
                                        <td class="<?= ($data['topik_id'] == null || $data['topik_id'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['topik_id'] == null || $data['topik_id'] == '!! ERROR !!') ? '!! ERROR !!' : $data['topik']->nama_topik; ?>
                                        </td>
                                        <td class="<?= ($data['soal'] == null || $data['soal'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['soal'] == null || $data['soal'] == '!! ERROR !!') ? '!! ERROR !!' : $data['soal']; ?>
                                        </td>
                                        <td class="<?= ($data['opsi_a'] == null || $data['opsi_a'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['opsi_a'] == null || $data['opsi_a'] == '!! ERROR !!') ? '!! ERROR !!' : $data['opsi_a']; ?>
                                        </td>
                                        <td class="<?= ($data['opsi_b'] == null || $data['opsi_b'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['opsi_b'] == null || $data['opsi_b'] == '!! ERROR !!') ? '!! ERROR !!' : $data['opsi_b']; ?>
                                        </td>
                                        <td class="<?= ($data['opsi_c'] == null || $data['opsi_c'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['opsi_c'] == null || $data['opsi_c'] == '!! ERROR !!') ? '!! ERROR !!' : $data['opsi_c']; ?>
                                        </td>
                                        <td class="<?= ($data['opsi_d'] == null || $data['opsi_d'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['opsi_d'] == null || $data['opsi_d'] == '!! ERROR !!') ? '!! ERROR !!' : $data['opsi_d']; ?>
                                        </td>
                                        <td class="<?= ($data['opsi_e'] == null || $data['opsi_e'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['opsi_e'] == null || $data['opsi_e'] == '!! ERROR !!') ? '!! ERROR !!' : $data['opsi_e']; ?>
                                        </td>
                                        <td class="<?= ($data['jawaban'] == null || $data['jawaban'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['jawaban'] == null || $data['jawaban'] == '!! ERROR !!') ? '!! ERROR !!' : $data['jawaban']; ?>
                                        </td>
                                        <td class="<?= ($data['bobot_soal_id'] == null || $data['bobot_soal_id'] == '!! ERROR !!') ? 'bg-danger text-white' : ''; ?>">
                                            <?= ($data['bobot_soal_id'] == null || $data['bobot_soal_id'] == '!! ERROR !!') ? '!! ERROR !!' : $data['bobot_soal']->bobot; ?>
                                        </td>
                                    </tr>
                            <?php
                                        if ($data['topik_id'] == null || $data['soal'] == null || $data['opsi_a'] == null || $data['opsi_b'] == null || $data['opsi_c'] == null || $data['opsi_d'] == null || $data['opsi_e'] == null || $data['jawaban'] == null || $data['bobot_soal_id'] == null) {
                                            $status = false;
                                        }

                                        if ($data['topik_id'] == '!! ERROR !!' || $data['soal'] == '!! ERROR !!' || $data['opsi_a'] == '!! ERROR !!' || $data['opsi_b'] == '!! ERROR !!' || $data['opsi_c'] == '!! ERROR !!' || $data['opsi_d'] == '!! ERROR !!' || $data['opsi_e'] == '!! ERROR !!' || $data['jawaban'] == '!! ERROR !!' || $data['bobot_soal_id'] == '!! ERROR !!') {
                                            $status = false;
                                        }

                                    endforeach;
                                }
                                ?>
                        </tbody>
                    </table>
                    </div>
                    <?php if ($status) : ?>
                        <?= form_open('soal/do_import', null, ['data' => json_encode($import)]); ?>
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
                <h4 class="modal-title">Data Topik</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <table id="topik" class="table table-bordered table-condensed table-striped">
                    <thead>
                        <th>ID</th>
                        <th>Materi Ujian</th>
                        <th>Topik</th>
                    </thead>
                    <tbody>
                        <?php foreach ($topik_list as $topik) : ?>
                            <tr>
                                <td><?= $topik->id; ?></td>
                                <td><?= $topik->matkul->nama_matkul; ?></td>
                                <td><?= $topik->nama_topik; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bobotId">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Data Bobot</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <table id="bobot" class="table table-bordered table-condensed table-striped">
                    <thead>
                        <th>ID</th>
                        <th>Bobot</th>
                        <th>Nilai</th>
                    </thead>
                    <tbody>
                        <?php foreach ($bobot_list as $bobot) : ?>
                            <tr>
                                <td><?= $bobot->id; ?></td>
                                <td><?= $bobot->bobot; ?></td>
                                <td><?= $bobot->nilai; ?></td>
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
