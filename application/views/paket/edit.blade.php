@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/node_modules/summernote/dist/summernote-bs4.min.css') }}">
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
<script src="{{ asset('assets/node_modules/inputmask/dist/jquery.inputmask.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/summernote/dist/summernote-bs4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/summernote_plugins/summernote-cleaner.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

function init_page_level(){
    $('.select2').select2({
        width: '100%',
    });

    $('.t_editor').summernote({
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['table', ['table']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['view', ['codeview']],
          ],
        cleaner:{
              action: 'both', // both|button|paste 'button' only cleans via toolbar button, 'paste' only clean when pasting content, both does both options.
              newline: '<br>', // Summernote's default is to use '<p><br></p>'
              notStyle: 'position:absolute;top:0;left:0;right:0', // Position of Notification
              icon: '<i class="note-icon">Clean Format</i>',
              keepHtml: false, // Remove all Html formats
              keepOnlyTags: ['<p>', '<br>', '<ul>', '<li>', '<b>', '<strong>','<i>', '<a>'], // If keepHtml is true, remove all tags except these
              keepClasses: false, // Remove Classes
              badTags: ['style', 'script', 'applet', 'embed', 'noframes', 'noscript', 'html'], // Remove full tags with contents
              badAttributes: ['style', 'start'], // Remove attributes from remaining tags
              limitChars: false, // 0/false|# 0/false disables option
              limitDisplay: 'both', // text|html|both
              limitStop: false // true/false
        },
        placeholder: 'Ketik disini ...',
        followingToolbar: false,
        height: 150,
        callbacks: {
            onImageUpload: function (data) { // PREVENT ON UPLOADING IMAGE
                data.pop();
            }
        }
    });
    $('.note-btn').attr('title', '').attr('data-original-title', ''); // DISABLED SUMMERNOTE TOOLTIP
}

$(document).on('submit', '#paket_ujian', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    var btn = $('#submit');

    btn.attr('disabled', 'disabled').text('Wait...');

    $.ajax({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        type: 'POST',
        success: function (response) {
            btn.removeAttr('disabled').text('Update');
            if (response.status) {
                Swal.fire('Sukses', 'Data Berhasil diupdate', 'success')
                    .then((result) => {
                        if (result.value) {
                            window.location.href = '{{ url('paket/index') }}';
                        }
                    });
            } else {
                $.each(response.errors, function (key, val) {
                    $('[name="' + key + '"]').closest('.form-group').addClass('has-error');
                    $('[name="' + key + '"]').nextAll('.help-block').eq(0).text(val);
                    if (val === '') {
                        $('[name="' + key + '"]').closest('.form-group').removeClass('has-error').addClass('has-success');
                        $('[name="' + key + '"]').nextAll('.help-block').eq(0).text('');
                    }
                });
            }
        }
    })
});

$(document).on('change', '#paket_ujian input, #paket_ujian select', function () {
    $(this).closest('.form-group').removeClass('has-error has-success');
    $(this).nextAll('.help-block').eq(0).text('');
});



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
    <div class="col-md-6">
    <?=form_open('paket/ajax/save_paket', ['id'=>'paket_ujian'], ['method' => 'post', 'aksi' => 'edit', 'id' => $paket->id]); ?>
        <div class="form-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name">Nama Paket</label>
                        <input autofocus="autofocus" onfocus="this.select()" value="{{ $paket->name }}" type="text" id="name" class="form-control" name="name" placeholder="Nama paket">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="price">Harga</label>
                        <input value="{{ $paket->price }}" type="number" id="price" class="form-control" name="price" placeholder="Harga">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="delete_price">Harga Hapus</label>
                        <input value="{{ $paket->delete_price }}" type="number" id="delete_price" class="form-control" name="delete_price" placeholder="Harga dihapus">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" class="form-control t_editor">{!! $paket->description !!}</textarea>
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="kuota_latihan_soal">Kuota Latihan Soal</label>
                        <input value="{{ $paket->kuota_latihan_soal }}" type="number" id="kuota_latihan_soal" class="form-control" name="kuota_latihan_soal" placeholder="Kuota latihan soal">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="text_color">Pilihan Warna</label>
                        <select class="select2" name="text_color">
                            <option {!! $paket->text_color == "success" ? 'selected="selected"' : "" !!} value="success">SUCCESS</option>
                            <option {!! $paket->text_color == "info" ? 'selected="selected"' : "" !!} value="info">INFO</option>
                            <option {!! $paket->text_color == "warning" ? 'selected="selected"' : "" !!} value="warning">WARNING</option>
                            <option {!! $paket->text_color == "danger" ? 'selected="selected"' : "" !!} value="danger">DANGER</option>
                        </select>
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="is_show">Status</label>
                        <select class="select2" name="is_show">
                            <option {!! $paket->is_show == "1" ? 'selected="selected"' : "" !!} value="1">SHOW</option>
                            <option {!! $paket->is_show == "0" ? 'selected="selected"' : "" !!} value="0">HIDDEN</option>
                        </select>
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group">
                        <label for="urut">Urut</label>
                        <input value="{{ $paket->urut }}" type="number" id="urut" class="form-control" name="urut" placeholder="Urutan paket">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group pull-right">
                        <a href="{{ site_url('paket/index') }}" class="btn btn-flat btn-warning">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" id="submit" class="btn btn-flat btn-outline-primary">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?=form_close(); ?>
    </div>
</div>
<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>
@endsection
