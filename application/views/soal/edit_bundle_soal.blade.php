@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/selects/select2.min.css') }}">
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
<script src="{{ asset('assets/yarn/node_modules/inputmask/dist/jquery.inputmask.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

$(document).on('submit', '#bundle_soal', function (e) {
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
                            window.location.href = base_url+'soal/bundle_soal';
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

$(document).on('change', '#bundle_soal input, #bundle_soal select', function () {
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
    <?=form_open('soal/ajax/save_bundle_soal', array('id'=>'bundle_soal'), array('method' => 'post', 'aksi' => 'edit', 'id' => $bundle->id));?>
        <div class="form-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="nama_bundle">Nama Bundle</label>
                        <input autofocus="autofocus" onfocus="this.select()" value="{{ $bundle->nama_bundle }}" type="text" id="nama_bundle" class="form-control" name="nama_bundle" placeholder="Nama Bundle">
                        <small class="help-block"></small>
                    </div>
                    <div class="form-group pull-right">
                        <a href="{{ site_url('soal/bundle_soal') }}" class="btn btn-flat btn-warning">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" id="submit" class="btn btn-flat btn-outline-primary">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
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
