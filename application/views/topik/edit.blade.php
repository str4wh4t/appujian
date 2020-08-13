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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.3/jquery.inputmask.min.js"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">

function init_page_level(){
    $('.select2').select2();

    $(".inp_decimal").inputmask("decimal",{
        digits: 2,
        digitsOptional: false,
        radixPoint: ".",
        groupSeparator: ",",
        allowPlus: false,
        allowMinus: false,
        rightAlign: false,
        autoUnmask: true,
    });
}

</script>
<script src="{{ asset('assets/dist/js/app/master/topik/edit.js') }}"></script>
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
    <div class="col-md-8">
        <div class="alert alert-info mb-2" role="alert">
            <strong>Jumlah data :</strong> {{ count($topik) }}
        </div>
        <?=form_open('topik/ajax/save', array('id'=>'topik'), array('mode'=>'edit'))?>
            <table id="form-table" class="table text-center table-condensed">
                <thead>
                    <tr>
                        <th># No</th>
                        <th>Materi Ujian</th>
                        <th>Topik</th>
                        <th>Poin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach($topik as $row) : ?>
                        <tr>
                            <td><?=$i?></td>
                            <td width="200">
                                <div class="form-group">
                                    <select required="required" name="matkul_id[<?=$i?>]" class="input-sm form-control select2" style="width: 100%!important">
                                        <option value="" disabled>-- Pilih --</option>
                                        <?php foreach ($matkul as $j) : ?>
                                            <option <?= $row->matkul_id == $j->id_matkul ? "selected='selected'" : "" ?> value="<?=$j->id_matkul?>"><?=$j->nama_matkul?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="help-block text-right"></small>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <?=form_hidden('id['.$i.']', $row->id);?>
                                    <input required="required" autofocus="autofocus" onfocus="this.select()" value="<?=$row->nama_topik?>" type="text" name="nama_topik[<?=$i?>]" class="form-control">
                                    <span class="d-none">DON'T DELETE THIS</span>
                                    <small class="help-block text-right"></small>
                                </div>
                            </td>
                            <td  width="150">
                            <div class="form-group">
                                <input autocomplete="off" type="text" class="form-control inp_decimal" name="poin_topik[<?=$i?>]" value="<?=$row->poin_topik?>">
                                <span class="d-none">DON'T DELETE THIS</span>
                                <small class="help-block text-right"></small>
                            </div>
                        </td>
                        </tr>
                    <?php $i++;endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-block btn-flat btn-outline-primary">
                <i class="fa fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ site_url('topik') }}" class="btn btn-block btn-flat btn-warning">
                <i class="fa fa-arrow-left"></i> Batal
            </a>
        <?=form_close()?>
    </div>
</div>
<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>
@endsection
