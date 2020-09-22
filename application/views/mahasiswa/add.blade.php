@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/yarn/node_modules/bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css') }}">
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
<script src="{{ asset('assets/yarn/node_modules/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/bootstrap4-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

function init_page_level(){
    $('.select2').select2();
    $('#matkul').select2({placeholder:'Pilih Materi Ujian'});
    $('.datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        // Your Icons
        // as Bootstrap 4 is not using Glyphicons anymore
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-check',
            clear: 'fa fa-trash',
            close: 'fa fa-times'
        }
    });
}

function checkImage(imageSrc, good, bad) {
    let img = new Image();
    img.onload = good;
    img.onerror = bad;
    img.src = imageSrc;
}

$(document).on('keyup','input[name="foto"]',function(){
    let src_img = $(this).val();
    checkImage(src_img, function(){
            console.log('good');
            $('#img_profile').attr('src',src_img);
        }, function(){
            console.log('bad');
            $('#img_profile').attr('src','{{ asset('assets/imgs/no_profile.jpg') }}');
        }
    );
});

</script>
<script src="{{ asset('assets/dist/js/app/master/mahasiswa/add.js') }}"></script>
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
<?=form_open('mahasiswa/ajax/save', array('id'=>'mahasiswa'), array('method'=>'add'))?>
<div class="row">
    <div class="col-md-6">
        <div class="form-body">
            <h4 class="form-section"><i class="ft-user"></i> Personal Info</h4>
            <div class="row">
                <div class="col-md-12">
            <div class="form-group">
                <label for="nim">No Peserta</label>
                <input autofocus="autofocus" onfocus="this.select()" placeholder="No Peserta" type="text" name="nim" id="nim" class="form-control">
                <small class="help-block"></small>
            </div>
            <div class="form-group">
                <label for="nama">Nama</label>
                <input placeholder="Nama" type="text" name="nama" id="nama" class="form-control">
                <small class="help-block"></small>
            </div>
            <div class="form-group">
                <label for="nim">NIK</label>
                <input placeholder="NIK" type="text" name="nik" id="nik" class="form-control">
                <small class="help-block"></small>
            </div>
            <div class="form-group">
                <label for="nama">Tmp Lahir</label>
                <input placeholder="Tmp Lahir" type="text" name="tmp_lahir" id="tmp_lahir" class="form-control">
                <small class="help-block"></small>
            </div>
            <div class="form-group">
                <label for="nama">Tgl Lahir</label>
                <input placeholder="Tgl Lahir" type="text" name="tgl_lahir" id="tgl_lahir" class="datetimepicker form-control">
                <small class="help-block"></small>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input placeholder="Email" type="text" name="email" id="email" class="form-control">
                <small class="help-block"></small>
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control select2">
                    <option value="" disabled selected>-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
                <small class="help-block"></small>
            </div>
            <div class="form-group">
                <label for="no_billkey">No Billkey</label>
                <input value="" placeholder="No Billkey" type="text" name="no_billkey" id="no_billkey" class="form-control">
                <small class="help-block"></small>
            </div>
            <div class="form-group">
                <label for="matkul">Materi Ujian</label>
                <select name="matkul[]" id="matkul" class="form-control" multiple="multiple" style="width: 100%!important">
{{--                            <option value="" disabled selected>Pilih Mata Kuliah</option>--}}
                    <?php foreach ($matkul as $row) : ?>
                        <option value="<?=$row->id_matkul?>"><?=$row->nama_matkul?></option>
                    <?php endforeach; ?>
                </select>
                <small class="help-block"></small>
            </div>

{{--            <div class="form-group">--}}
{{--                <label for="jurusan">Jurusan</label>--}}
{{--                <select id="jurusan" name="jurusan" class="form-control select2">--}}
{{--                    <option value="" disabled selected>-- Pilih --</option>--}}
{{--                </select>--}}
{{--                <small class="help-block"></small>--}}
{{--            </div>--}}
{{--            <div class="form-group">--}}
{{--                <label for="kelas">Kelas</label>--}}
{{--                <select id="kelas" name="kelas" class="form-control select2">--}}
{{--                    <option value="" disabled selected>-- Pilih --</option>--}}
{{--                </select>--}}
{{--                <small class="help-block"></small>--}}
{{--            </div>--}}

            <div class="form-group pull-right">
                <a href="{{ site_url('mahasiswa') }}" class="btn btn-flat btn-warning">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" id="submit" class="btn btn-flat btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
            </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
            <!--
            <div class="alert bg-info">
                <strong>Perhatian : </strong>
                <ol>
                    <li>Foto yg diupload maks. 500 KB</li>
                    <li>Ukuran foto 320 x 420 pixel</li>
                    <li>Ekstensi file foto .jpg</li>
                </ol>
            </div>
            <div class="form-group" style="text-align: center">
                <input type='file' id="imgInp"  style="display: none" />
                <button class="btn btn-success" id="btnUpload" type="button"><i class="icon-cloud-upload"></i> Upload foto</button>
            </div>
            -->
            <div class="alert bg-info">
                <strong>Perhatian : </strong>
                <ol>
                    <li>Silahkan isikan url foto peserta ujian</li>
                    <li>Apabila link path foto benar, maka gambar akan terupdate</li>
                </ol>
            </div>
            <div class="form-group">
                <label for="nama">Foto</label>
                <input placeholder="Foto" type="text" name="foto" class="form-control">
                <small class="help-block"></small>
            </div>
            <div style="text-align: center">
                <img id="img_profile" style="width: 320px" src="{{ asset('assets/imgs/no_profile.jpg') }}" />
            </div>
    </div>
</div>
<?=form_close()?>
<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>
@endsection
