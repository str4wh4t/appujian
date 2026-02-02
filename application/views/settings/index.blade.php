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
<script src="{{ asset('assets/node_modules/datatables.net-plugins/api/fnPagingInfo.js') }}"></script>
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
        $(document).on('click','#truncate', function(e){
            e.preventDefault();

            Swal.fire({
                // heightAuto : false,
                text: "Kosongkan Table",
                title: "Anda yakin?",
                icon: "question",
                showCancelButton: true,
                cancelButtonColor: '#dd4b39'
            }).then((result) => {
                if(result.value){
                    $(this).attr('disabled', 'disabled').text('Proses...');
                    var jqxhr = $.getJSON('{{ site_url('settings/ajax/truncate') }}', function(response){
                        if(response.status){
                            Swal.fire({
                                title: "Berhasil",
                                text: "Semua table sudah dikosongkan, kecuali akun Admin pada table user.",
                                icon: "success",
                            });
                        }
                    });

                    jqxhr.done(function() {
                        console.log( "ajax complete" );
                        $('#truncate').removeAttr('disabled').html('<i class="fa fa-trash"></i> Kosongkan Table');
                    });

                }
            });

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
<div class="box box-danger box-solid">
    <div class="box-body">
        <p>
            Semua data akan dihapus kecuali akun admin.
        </p>
        <button type="button" id="truncate" class="btn btn-danger btn-flat">
            <i class="fa fa-trash"></i> Kosongkan Table
        </button>
    </div>
</div>
<!---- --->

				</div>
            </div>
        </div>
    </div>
</section>
@endsection
