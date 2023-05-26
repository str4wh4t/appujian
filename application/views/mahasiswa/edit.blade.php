@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/npm/node_modules/bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css') }}">
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
<script src="{{ asset('assets/npm/node_modules/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/npm/node_modules/bootstrap4-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/toggle/bootstrap-switch.min.js') }}"></script>
<script src="{{ asset('assets/npm/node_modules/jquery-validation/dist/jquery.validate.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

let validator;

function init_page_level(){
    $('.select2').select2();
    $('#matkul').select2({placeholder: "Pilih Materi Ujian"});
    let selected = [];
    @foreach($mhs->matkul as $mhs_matkul)
    selected.push('{{ $mhs_matkul->id_matkul }}');
    @endforeach
    $('#matkul').val(selected).trigger('change');
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

    validator = $("#form_add_prodi").validate({
        debug: false,
        ignore: [],
        rules: {
            'nama_prodi': {required: true},
        },
        messages: {
            'nama_prodi': {
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
            } else if ( element.hasClass("summernote_editor")) {
                // error.insertAfter(element.parent().parent().parent().siblings(".error_select2"));
                error.css('display','block');
                error.insertAfter(element.siblings(".error_summernote"));
                // error.insertAfter(element);
            }
            else {
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
        }
    });

}

let fileTypes = ['jpg'];
function readURL(input) {
    if (input.files && input.files[0]) {
        let extension = input.files[0].name.split('.').pop().toLowerCase();
        let allow = fileTypes.indexOf(extension) > -1;

        if(allow){
            let reader = new FileReader();
            reader.onload = function (e) {
                //Initiate the JavaScript Image object.
                let image = new Image();
                //Set the Base64 string return from FileReader as source.
                image.src = e.target.result;
                //Validate the File Height and Width.
                image.onload = function () {
                    let height = this.height;
                    let width = this.width;
                    // console.log('height',height);
                    // console.log('width',width);
                    // console.log('this.src',this.src);
                    if (height == 420 && width == 320) {
                        $('#img_profile').attr('src', this.src);
                        reader.readAsDataURL(input.files[0]);
                        return true;
                    }else{
                        // console.log('err');
                        return false;
                    }
                };
            };
        }else{
            return false;
        }
    }
}

$("#imgInp").change(function(e) {
  let ok = readURL(this);
  if(!ok){
      Swal.fire({
           title: "Perhatian",
           text: "File foto tidak didukung",
           icon: "warning"
        });
      $(this).val('');
  }
});

$(document).on('click','#btnUpload',function(){
    $("#imgInp").trigger('click');
});

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
            $('#img_profile').attr('src','{{ $mahasiswa->foto }}');
        }
    );
});

$(document).on('click','#btn_set_materi',function(){
    let val = $('#matkul').val();
    if(!val.length){
        Swal.fire({
            title: "Perhatian",
            text: "Materi ujian tidak boleh kosong",
            icon: "warning"
        });
        return false;
    }
    $.ajax({
        url: '{{ url('mahasiswa/ajax/set_matkul') }}',
        type: 'POST',
        data: {'matkul' : JSON.stringify(val), 'mhs_id' : '{{ $mahasiswa->id_mahasiswa }}'},
        success: function (data) {
            Swal.fire({
                title: "Sukses",
                text: "Data berhasil disimpan",
                icon: "success"
            });
        },
        error: function(){
            Swal.fire({
                title: "Perhatian",
                text: "Simpan gagal",
                icon: "warning"
            });
        }
    });
});

$(document).on('click','#btn_add_prodi',function(e){
    validator.resetForm();
    $('#nama_prodi').val('');
    $('#modal_add_prodi').modal('show');
    return false;
});

$(document).on('click','#btn_submit_add_prodi',function(e){  

    let result = validator.form();
    if(result){
        let nama_prodi = $('#nama_prodi').val();
        let newopt = new Option(nama_prodi, nama_prodi, true, true);
        $('#prodi').append(newopt).trigger('change');
        validator.resetForm();
        $('#nama_prodi').val('');
        $('#modal_add_prodi').modal('hide');
        Swal.fire({
            title: "Perhatian",
            text: "Prodi telah ditambahkan",
            icon: "success",
            confirmButtonText: "Ok",
        });
    }

});

</script>
<script src="{{ asset('assets/dist/js/app/master/mahasiswa/edit.js') }}"></script>
<!-- END PAGE LEVEL JS-->
@endpush

@section('content')
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
            	<h4 class="card-title">{{ $subjudul }}</h4>
            	<a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
            </div>
            <div class="card-content">
                <div class="card-body">

{{--                <div class="row">--}}
{{--                    <div class="col-md-12 pb-1">--}}
{{--                    <a href="{{ site_url('mahasiswa') }}" class="btn btn-flat btn-warning">--}}
{{--                        <i class="fa fa-arrow-left"></i> Kembali--}}
{{--                    </a>--}}
{{--                    </div>--}}
{{--                </div>--}}


<!---- --->
<?= form_open('mahasiswa/ajax/save', ['id'=>'mahasiswa'], ['method'=>'edit', 'id_mahasiswa'=>$mahasiswa->id_mahasiswa]) ?>
<div class="row">
    <div class="col-md-6">
        <div class="form-body">
            <h4 class="form-section"><i class="ft-user"></i> Personal Info</h4>
            <div class="row">
                <div class="col-md-12">
        <div class="form-group">
            <label for="nim">No Peserta</label>
            <input value="{{ $mahasiswa->nim }}" {{ $user_is_exist ? 'readonly="readonly"' : '' }} placeholder="No Peserta" type="text" name="nim" id="nim" class="form-control">
            <small class="help-block"></small>
        </div>
        <div class="form-group">
            <label for="nama">Nama</label>
            <input value="{{ $mahasiswa->nama }}" autofocus="autofocus" onfocus="this.select()" placeholder="Nama" type="text" name="nama" id="nama" class="form-control">
            <small class="help-block"></small>
        </div>
        <div class="form-group">
            <label for="nik">NIK</label>
            <input value="{{ $mahasiswa->nik }}" placeholder="NIK" type="text" name="nik" id="nik" class="form-control">
            <small class="help-block"></small>
        </div>
        <div class="form-group">
            <label for="tmp_lahir">Tmp Lahir</label>
            {{-- <input value="{{ $mahasiswa->tmp_lahir }}" placeholder="Tmp Lahir" type="text" name="tmp_lahir" id="tmp_lahir" class="form-control"> --}}
            <select name="tmp_lahir" id="tmp_lahir" class="form-control select2">
                @foreach ($kota_kab_list as $item)
                <option value="{{ $item->kota_kab }}" {{ $mahasiswa->tmp_lahir == $item->kota_kab ? 'selected="selected"' : '' }}>{{ $item->kota_kab }}</option>
                @endforeach
            </select>
            <small class="help-block"></small>
        </div>
        <div class="form-group">
            <label for="tgl_lahir">Tgl Lahir</label>
            <input value="{{ $mahasiswa->tgl_lahir }}" placeholder="Tgl Lahir" type="text" name="tgl_lahir" id="tgl_lahir" class="datetimepicker form-control">
            <small class="help-block"></small>
        </div>
        {{-- <div class="form-group">
            <label for="kota_asal">Kota Asal</label>
            <input value="{{ $mahasiswa->kota_asal }}" placeholder="Kota Asal" type="text" name="kota_asal" id="kota_asal" class="form-control">
            <small class="help-block"></small>
        </div> --}}
        <div class="form-group">
            <label for="email">Email</label>
            <input value="{{ $mahasiswa->email }}" placeholder="Email" type="text" name="email" id="email" class="form-control">
            <small class="help-block"></small>
        </div>
        <div class="form-group">
            <label for="jenis_kelamin">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control select2">
                <option value="">-- Pilih --</option>
                <option {{ $mahasiswa->jenis_kelamin === "L" ? "selected" : ""  }} value="L">Laki-laki</option>
                <option {{ $mahasiswa->jenis_kelamin === "P" ? "selected" : ""  }} value="P">Perempuan</option>
            </select>
            <small class="help-block"></small>
        </div>
        <div class="form-group">
            <label for="no_billkey">No Billkey</label> 
            <small class="help-block text-danger"><b>***</b> Digunakan sbg password</small>
            <input value="{{ $mahasiswa->no_billkey }}" placeholder="No Billkey" type="text" name="no_billkey" id="no_billkey" class="form-control" disabled="disabled">
            <small class="help-block"></small>
        </div>
        <div class="form-group">
            <label for="prodi" style="display: block">Prodi <span class="pull-right">[ <b><a href="#" id="btn_add_prodi"><i class="fa fa-plus"></i> Add prodi</a></b> ]</span></label>
            <select id="prodi" name="prodi" class="form-control select2">
                 @foreach ($prodi_list as $item)
                 <option value="{{ $item }}" {{ $mahasiswa->prodi == $item ? 'selected="selected"' : '' }}>{{ $item }}</option>
                 @endforeach
            </select>
            <small class="help-block"></small>
        </div>
        <!-- 
        <div class="form-group">
            <label for="matkul">Materi Ujian</label>
            <div class="row">
                <div class="col-md-12">
                    <select name="matkul[]" id="matkul" class="form-control select2" style="width: 100%!important" multiple="multiple">
                        @foreach ($matkul as $row)
                            <option value="{{ $row->id_matkul }}">{{ $row->nama_matkul }}</option>
                        @endforeach
                    </select>
                </div>
            
{{--                <div class="col-md-3">--}}
{{--                    <button type="button" class="btn btn-info" id="btn_set_materi"><i class="fa fa-check"></i> Set Materi</button>--}}
{{--                </div>--}}

            </div>
            <small class="help-block"></small>
        </div>
    -->

{{--        <div class="form-group">--}}
{{--            <label for="jurusan">Jurusan</label>--}}
{{--            <select id="jurusan" name="jurusan" class="form-control select2">--}}
{{--                <option value="" disabled selected>-- Pilih --</option>--}}
{{--                @foreach ($jurusan as $j) --}}
{{--                <option {{ $mahasiswa->id_jurusan === $j->id_jurusan ? "selected" : ""  }} value="{{ $j->id_jurusan }}">--}}
{{--                    {{ $j->nama_jurusan }}--}}
{{--                </option>--}}
{{--                @endforeach --}}
{{--            </select>--}}
{{--            <small class="help-block"></small>--}}
{{--        </div>--}}
{{--        <div class="form-group">--}}
{{--            <label for="kelas">Kelas</label>--}}
{{--            <select id="kelas" name="kelas" class="form-control select2">--}}
{{--                <option value="" disabled selected>-- Pilih --</option>--}}
{{--                @foreach ($kelas as $k) --}}
{{--                <option {{ $mahasiswa->id_kelas === $k->id_kelas ? "selected" : ""  }} value="{{ $k->id_kelas }}">--}}
{{--                    {{ $k->nama_kelas }}--}}
{{--                </option>--}}
{{--                @endforeach --}}
{{--            </select>--}}
{{--            <small class="help-block"></small>--}}
{{--        </div>--}}

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

    {{-- <div class="col-md-6">
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
                <input value="{{ $mahasiswa->foto }}" placeholder="Foto" type="text" name="foto" class="form-control">
                <small class="help-block"></small>
            </div>
            <div style="text-align: center">
                <img id="img_profile" style="width: 320px" src="{{ empty($mahasiswa->foto) ? asset(FOTO_DEFAULT_URL) : $mahasiswa->foto }}" />
            </div>
    </div> --}}
    
</div>
<?= form_close() ?>
<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>

<div class="modal" id="modal_add_prodi">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Prodi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <form id="form_add_prodi" method="POST" action="#" >
                <div class="form-group">
                    <label for="nama_prodi">Nama Prodi</label>
                    <input type="text" class="form-control" value="" name="nama_prodi" id="nama_prodi" placeholder=""/>
                </div>
                <div class="form-group pull-right">
                    <button type="button" id="btn_submit_add_prodi" class="btn btn-flat btn-success">Tambah</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
