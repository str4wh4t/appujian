@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">

<!-- Include TUI CSS. -->
{{--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tui-image-editor@3.2.2/dist/tui-image-editor.css">--}}
{{--<link rel="stylesheet" href="https://uicdn.toast.com/tui-color-picker/latest/tui-color-picker.css">--}}
<!-- textarea editor -->
{{--<link rel="stylesheet" href="{{ asset('assets/bower_components/codemirror/lib/codemirror.min.css') }}">--}}
{{--<link rel="stylesheet" href="{{ asset('assets/bower_components/froala_editor/css/froala_editor.pkgd.min.css') }}">--}}
{{--<link rel="stylesheet" href="{{ asset('assets/bower_components/froala_editor/css/froala_style.min.css') }}">--}}
{{--<link rel="stylesheet" href="{{ asset('assets/bower_components/froala_editor/css/themes/royal.min.css') }}">--}}
<!-- Include TUI Froala Editor CSS. -->
{{--<link rel="stylesheet" href="{{ asset('assets/bower_components/froala_editor/css/third_party/image_tui.min.css') }}">--}}

{{--<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">--}}
<link href="{{ asset('assets/yarn/node_modules/summernote/dist/summernote-bs4.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/plugins/summernote_plugins/summernote-audio.css') }}" rel="stylesheet">

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

<!-- Include TUI JS. -->
{{--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/1.6.7/fabric.min.js"></script>--}}
{{--<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tui-code-snippet@1.4.0/dist/tui-code-snippet.min.js"></script>--}}
{{--<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tui-image-editor@3.2.2/dist/tui-image-editor.min.js"></script>--}}
<!-- Textarea editor -->
{{--<script src="{{ asset('assets/bower_components/codemirror/lib/codemirror.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/bower_components/codemirror/mode/xml.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/bower_components/froala_editor/js/froala_editor.pkgd.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/assets/plugins/froala_wiris/wiris.js') }}"></script>--}}
<!-- Include TUI plugin. -->
{{--<script src="{{ asset('assets/bower_components/froala_editor/js/third_party/image_tui.min.js') }}"></script>--}}

{{--<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>--}}
<script src="{{ asset('assets/yarn/node_modules/summernote/dist/summernote-bs4.min.js') }}"></script>

<script src="{{ asset('assets/plugins/select2-cascade.js') }}"></script>
<script src="{{ asset('assets/plugins/summernote_plugins/summernote-cleaner.js') }}"></script>
<script src="{{ asset('assets/plugins/summernote_plugins/summernote-audio.js') }}"></script>
{{--<script src="https://rawgit.com/RobinHerbots/Inputmask/5.x/dist/jquery.inputmask.js"></script>--}}
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

function init_page_level(){
    ajaxcsrf();

    // $('.froala-editor').froalaEditor({
	// 	theme: 'royal',
	// 	toolbarButtons: ['fullscreen', '|', 'bold', 'italic', 'strikeThrough', 'underline', 'color','fontSize','fontFamily','|', 'align', 'insertTable', 'insertLink','formatOL', 'formatUL', '|', 'html','insertImage'],
	// });
    //
    // $('.froala-editor').on('froalaEditor.image.beforeUpload', function (e, editor, images) {
    //     if (images.length) {
    //         let reader = new FileReader();
    //         reader.onload = function (e) {
    //             let result = e.target.result;
    //             editor.image.insert(result, null, null, editor.image.get());
    //         };
    //         reader.readAsDataURL(images[0]);
    //     }
    //     return false
    // });

    // let editor = new Quill('.editor', {
    //     modules: { toolbar: '#toolbar' },
    //     theme: 'snow'
    // });

    $('.t_editor').summernote({
        toolbar: [
            // [groupName, [list of button]]
            // ['cleaner',['cleaner']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['table', ['table']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            // ['view', ['codeview']],
            ['insert', ['audio']],
        ],
        cleaner:{
              action: 'both', // both|button|paste 'button' only cleans via toolbar button, 'paste' only clean when pasting content, both does both options.
              // newline: '<br>', // Summernote's default is to use '<p><br></p>'
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

    });
    $('.note-btn').attr('title', '').attr('data-original-title', ''); // DISABLED SUMMERNOTE TOOLTIP

    $('.select2').select2();

    let options = {};
    cascadLoading = new Select2Cascade($('#matkul_id'), $('#topik_id'), '{{ site_url('soal/ajax/get_topic_by_matkul/') }}?id=:parentId:', options);
    cascadLoading.then( function(parent, child, items) {
        // Open the child listbox immediately
        // child.select2('open');
        // or Dump response data
        // console.log(items);
        @if(!empty(set_value('topik_id')))
            child.val("{{ set_value('topik_id') }}").trigger('change');
        @endif
    });

    {{--$('#topik_id').select2({--}}
    {{--    placeholder: "Silahkan pilih mata kuliah dahulu",--}}
    {{--    ajax: {--}}
    {{--        url: '{{ site_url('topik/ajax/get_topic_by_matkul/') }}',--}}
    {{--        dataType: 'json',--}}
    {{--        method: "POST",--}}
    {{--        data: {'id' : '3'},--}}
    {{--        processResults: function (data) {--}}
    {{--            // Transforms the top-level key of the response object from 'items' to 'results'--}}
    {{--            return {--}}
    {{--                results: data--}}
    {{--            };--}}
    {{--        }--}}
    {{--    }--}}
    {{--});--}}

    @if(!empty(set_value('matkul_id')))
        $('select[name="matkul_id"]').val("{{ set_value('matkul_id') }}");
        $('select[name="matkul_id"]').trigger('change');
    @endif

    @if(!empty(set_value('jawaban')))
        $('select[name="jawaban"]').val("{{ set_value('jawaban') }}");
    @endif

    @if(!empty(set_value('bobot_soal_id')))
        $('select[name="bobot_soal_id"]').val("{{ set_value('bobot_soal_id') }}");
    @endif

    @if(!empty(form_error('matkul_id')))
        $('#matkul_id').parent('.form-group').addClass('has-error');
    @endif

    @if(!empty(form_error('topik_id')))
        $('#topik_id').parent('.form-group').addClass('has-error');
    @endif

    @if(!empty(form_error('jawaban')))
        $('#jawaban').parent('.form-group').addClass('has-error');
    @endif

     @if(!empty(form_error('bobot_soal_id')))
        $('#bobot_soal_id').parent('.form-group').addClass('has-error');
     @endif

{{--    @if(!empty(form_error('bobot')))--}}
{{--        $('#bobot').parent('.form-group').addClass('has-error');--}}
{{--    @endif--}}



}

</script>
<!-- END PAGE LEVEL JS-->
@endpush

@section('content')
    <div id="tui-image-editor-container"></div>
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
        <div class="col-md-8">
        <?=form_open_multipart('soal/save', array('id'=>'formsoal'), array('method'=>'add'));?>

                    <label>Materi Ujian</label>
                    <div class="form-group">
                        <select name="matkul_id" id="matkul_id" class="select2 form-group" style="width:100% !important">
                            <option value="" disabled selected>Pilih Materi Ujian</option>
                            <?php foreach ($matkul as $d) : ?>
                            <option value="<?=$d->id_matkul?>"><?=$d->nama_matkul?></option>
                            <?php endforeach; ?>
                        </select> <small class="help-block" style="color: #dc3545"><?=form_error('matkul_id')?></small>
                    </div>
                    <label>
                        <span>Topik</span>
                        <small class="help-block text-info"><span class="text-danger"><b>***</b></span> Sebelum memilh topik, silahkan pilih matkul dahulu</small>
                    </label>
                    <div class="form-group">
                        <select name="topik_id" id="topik_id" class="select2 form-group" style="width:100% !important">
                            <option value="" disabled selected>Pilih Topik</option>
                        </select> <small class="help-block" style="color: #dc3545"><?=form_error('topik_id')?></small>
                    </div>


                    <div class="alert bg-info mb-2" role="alert">
                        <strong>Pertanyaan</strong>
                    </div>

{{--                    <div class="form-group">--}}
{{--                        <input type="file" name="file_soal" class="form-control">--}}
{{--                        <small class="help-block" style="color: #dc3545"><?=form_error('file_soal')?></small>--}}
{{--                    </div>--}}

                    <div class="form-group">
                        <textarea name="soal" id="soal" class="form-control froala-editor t_editor"><?=set_value('soal')?></textarea>
                        <small class="help-block" style="color: #dc3545"><?=form_error('soal')?></small>
                    </div>

                    <div class="alert bg-danger mb-2" role="alert">
                        <strong>Jawaban</strong>
                    </div>
                <!--
                    Membuat perulangan A-E
                -->
                <?php  $abjad = ['a', 'b', 'c', 'd', 'e'];
                foreach ($abjad as $abj) :
                    $ABJ = strtoupper($abj); // Abjad Kapital
                ?>

                    <label for="opsi">Opsi : <strong class="text-danger"><?= $ABJ; ?></strong></label>
{{--                    <div class="form-group">--}}
{{--                        <input type="file" name="file_<?= $abj; ?>" class="form-control">--}}
{{--                        <small class="help-block" style="color: #dc3545"><?=form_error('file_'.$abj)?></small>--}}
{{--                    </div>--}}
                    <div class="form-group">
                        <textarea name="jawaban_<?= $abj; ?>" id="jawaban_<?= $abj; ?>" class="form-control froala-editor t_editor"><?=set_value('jawaban_'.$abj)?></textarea>
                        <small class="help-block" style="color: #dc3545"><?=form_error('jawaban_'.$abj)?></small>
                    </div>


                <?php endforeach; ?>

                <div class="form-group">
                    <label for="jawaban" class="control-label">Kunci Jawaban</label>
                    <select name="jawaban" id="jawaban" class="form-control select2" style="width:100%!important">
                        <option value="" disabled selected>Pilih Kunci Jawaban</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                    </select>
                    <small class="help-block" style="color: #dc3545"><?=form_error('jawaban')?></small>
                </div>

{{--                <div class="form-group" >--}}
{{--                    <label for="bobot" class="control-label">Bobot Soal</label>--}}
{{--                    <input value="{{ set_value('bobot', '1') }}" type="number" name="bobot" placeholder="Bobot Soal" id="bobot" class="form-control">--}}
{{--                    <small class="help-block" style="color: #dc3545"><?=form_error('bobot')?></small>--}}
{{--                </div>--}}

                <div class="form-group" >
                    <label for="bobot_soal_id" class="control-label">Bobot Soal</label>
                    <select name="bobot_soal_id" id="bobot_soal_id" class="form-control select2" style="width:100%!important">
                        <option value="" disabled selected>Pilih Bobot</option>
                        @forelse($bobot_soal as $d)
                            <option value="{{ $d->id }}">{{ $d->bobot }}</option>
                        @empty

                        @endforelse
                    </select>
                    <small class="help-block" style="color: #dc3545"><?=form_error('bobot_soal_id')?></small>
                </div>

                <div class="form-group pull-right">
                    <a href="{{ site_url('soal') }}" class="btn btn-flat btn-warning">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" id="submit" class="btn btn-flat btn-outline-primary"><i class="fa fa-save"></i> Simpan</button>
                </div>
        <?=form_close();?>
</div>
</div>
<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>
@endsection
