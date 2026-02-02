@extends('template.main')

@push('page_vendor_level_css')
<!-- BEGIN PAGE VENDOR LEVEL JS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/handsontable/handsontable.full.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/handsontable/jsgrid-theme.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/handsontable/jsgrid.min.css') }}">
<!-- END PAGE VENDOR LEVEL JS-->
@endpush

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">--}}
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/css/plugins/tables/handsontable.css') }}">
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_custom_css')
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/node_modules/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/handsontable/handsontable.full.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/handsontable/jsgrid.min.js') }}"></script>
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/handsontable/languages.min.js') }}"></script>
<!-- END PAGE VENDOR JS -->
@endpush

@push('page_level_js')
    <!-- BEGIN PAGE LEVEL JS-->
    <script type="text/javascript">
        let data = [
          ['','','','','','','','','','','','','','','',''],
        ];

        function isEmail(email) {
            let regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }

        let jk_avail = ['{{ JK_AVAIL[0] }}', '{{ JK_AVAIL[1] }}'];

        let emailValidator = function (value, callback) {
            setTimeout(function () {
                if (value != '') {
                    if (value.length <= {{ MAX_EMAIL_PESERTA_LENGTH }}) {
                        if (isEmail(value)) {
                            callback(true);
                        } else {
                            callback(false);
                        }
                    } else {
                        callback(false);
                    }
                } else {
                    callback(true);
                }
            }, 100);
        };

        let isValidDate = function (value, callback){
            value = value.trim();
            var date = moment(value);
            setTimeout(function(){
            if(value != '') {
                if (value.length == 10) {
                    if (date.isValid()) {
                        callback(true);
                    } else {
                        callback(false);
                    }
                } else {
                    callback(false);
                }
            }else {
                callback(true);
            }
          }, 100);
        };

        let isValidNopes = function (value, callback){
            value = value.trim();
            var date = moment(value);
            setTimeout(function(){
            if(value != '') {
                if (!isNaN(value)){
                    if (value.length <= {{ MAX_MHS_ID_LENGTH }}) {
                        callback(true);
                    } else {
                        callback(false);
                    }
                } else {
                    callback(false);
                }
            }else {
                callback(true);
            }
          }, 100);
        };

        let isValidNobillkey = function (value, callback){
            value = value.trim();
            var date = moment(value);
            setTimeout(function(){
            if(value != '') {
                if (!isNaN(value)){
                    if (value.length <= {{ MAX_NO_BILLKEY_LENGTH }}) {
                        callback(true);
                    } else {
                        callback(false);
                    }
                } else {
                    callback(false);
                }
            }else {
                callback(true);
            }
          }, 100);
        };

        let isValidNik = function (value, callback){
            value = value.trim();
            var date = moment(value);
            setTimeout(function(){
            if(value != '') {
                if (!isNaN(value)){
                    if (value.length == {{ NIK_LENGTH }}) {
                        callback(true);
                    } else {
                        callback(false);
                    }
                } else {
                    callback(false);
                }
            }else {
                callback(true);
            }
          }, 100);
        };

        let isValidJk = function (value, callback){
            value = value.trim();
            var date = moment(value);
            setTimeout(function(){
            if(value != '') {
                if (jk_avail.includes(value)) {
                    callback(true);
                } else {
                    callback(false);
                }
            }else {
                callback(true);
            }
          }, 100);
        };

        let isValidNmpes = function (value, callback){
            value = value.trim();
            var date = moment(value);
            setTimeout(function(){
            if(value != '') {
                if ((value.length <= {{ MAX_NM_PESERTA_LENGTH }})) {
                    callback(true);
                } else {
                    callback(false);
                }
            }else {
                callback(true);
            }
          }, 100);
        };

        let isValidProdi = function (value, callback){
            value = value.trim();
            var date = moment(value);
            setTimeout(function(){
            if(value != '') {
                if (value.length <= {{ MAX_PRODI_LENGTH }}) {
                    callback(true);
                } else {
                    callback(false);
                }
            }else {
                callback(true);
            }
          }, 100);
        };

        let isValidTmplahir = function (value, callback){
            value = value.trim();
            var date = moment(value);
            setTimeout(function(){
            if(value != '') {
                if (value.length <= {{ MAX_TMP_LAHIR_PESERTA_LENGTH }}) {
                    callback(true);
                } else {
                    callback(false);
                }
            }else {
                callback(true);
            }
          }, 100);
        };

        

        let container = document.getElementById('hot');
        let hot = new Handsontable(container, {
                data: data,
                rowHeaders: true,
                // colHeaders: true,
                // colHeaders:  ['NO PESERTA','NAMA','NIK','TMP LAHIR','TGL LAHIR<br>(YYYY-MM-DD)','L/P','EMAIL','NO BILLKEY','FOTO','KODEPS','PRODI','JALUR','GEL','SMT','TAHUN','ID MATERI UJIAN'],
                // colHeaders:  ['NO PESERTA','NAMA','NIK','TMP LAHIR','TGL LAHIR<br>(YYYY-MM-DD)','L/P','EMAIL','NO BILLKEY','FOTO','KODEPS','PRODI'],
                colHeaders:  ['NO PESERTA','NAMA','NIK','TMP LAHIR','TGL LAHIR<br>(YYYY-MM-DD)','JENIS KELAMIN<br>({{ JK_AVAIL[0] }}/{{ JK_AVAIL[1] }})','EMAIL','NO BILLKEY','PRODI'],
                filters: true,
                dropdownMenu: true,
                // minRows: 50,
                // maxRows: 50,
                width: 1000,
                height: 500,
                // colWidths: [150, 200, 150, 150, 150, 150, 150, 150, 150],
                colWidths: [150, 200, 150, 150, 150, 150, 150, 150, 150],
                // rowHeights: [50, 40, 100],
                manualColumnResize: false,
                manualRowResize: true,
                contextMenu: true,
                // columns: [
                //     {},
                //     {},
                //     {},
                //     {},
                //     {validator: isValidDate, allowInvalid: false},
                //     {},
                //     {validator: emailValidator, allowInvalid: false},
                //     {},
                //     {},
                //     {},
                //     {},
                //     {},
                //     {},
                //     {},
                //     {},
                //     {}
                // ],
                columns: [
                    {validator: isValidNopes, allowInvalid: false},
                    {validator: isValidNmpes, allowInvalid: false},
                    {validator: isValidNik, allowInvalid: false},
                    {validator: isValidTmplahir, allowInvalid: false},
                    {validator: isValidDate, allowInvalid: false},
                    {validator: isValidJk, allowInvalid: false},
                    {validator: emailValidator, allowInvalid: false},
                    {validator: isValidNobillkey, allowInvalid: false},
                    {validator: isValidProdi, allowInvalid: false},
                ]

        });

        {{--Handsontable.dom.addEvent(document.getElementById('btn_import'), 'click', function() {--}}
        {{--  // save all cell's data--}}
        {{--    let data = 'aa';--}}
        {{--  $.post('{{ site_url('mahasiswa/ajax/table_import') }}', 'GET',{'data' : data}, function (res) {--}}
        {{--    let response = JSON.parse(res.response);--}}
        {{--    console.log(response.result);--}}
        {{--  });--}}
        {{--});--}}

        $(document).on('click', '#btn_import', function(){

            let data = hot.getData();
            let allow = true ;
            let row_number = 0;
            let col_number = 0;
            $.each(data, function (i, v) {
                row_number = i;
                $.each(v, function (j, val) {
                    col_number = j;
                    // if(col_number != 8){ // colom 8 , foto boleh dikosongi
                        if(!val){
                            allow = false;
                            return false;
                        }
                    // }
                });
                if(!allow)
                    return false;
            });
            if(!allow) {
                alert('Data ada yang kosong pada row : ' + (row_number + 1) + ', col : ' + (col_number + 1) + ', silahkan diperbaiki dahulu');
                hot.selectCell(row_number,col_number);
            }else{
                ajx_overlay(true);
                ajaxcsrf();
                $.post('{{ site_url('mahasiswa/ajax/table_import') }}', {'data': JSON.stringify(data)}, function (res) {
                    if (!res.status) {
                        Swal.fire({
                            title: "Perhatian",
                            text: res.msg,
                            icon: "warning"
                        });
                    } else {
                        Swal.fire({
                            title: "Perhatian",
                            text: "Data berhasil di impor.",
                            icon: "success"
                        });
                        // hot.updateSettings({data: [['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']]});
                        hot.updateSettings({data: [['', '', '', '', '', '', '', '', '']]});
                    }
                    ajx_overlay(false);
                });
            }

            // console.log(data);
            {{--$.post('{{ site_url('mahasiswa/ajax/table_import') }}', 'GET',{'data' : data}, function (res) {--}}
            {{--    let response = JSON.parse(res.response);--}}
            {{--    console.log(response.result);--}}
            {{--});--}}
        });


    </script>
    <!-- END PAGE LEVEL JS-->
@endpush

@push('page_custom_css')
<style type="text/css">
/* styling opsi */
html body {
    height: auto;
}
.htContextMenu:not(.htGhostTable) {
    z-index: 9999;
}
.swal2-container{
    z-index: 9999 !important;
}
</style>
@endpush

@section('content')
<section id="lembar_ujian"  style="background-color: #f3f3f3; overflow-x: hidden;" class="card card-fullscreen">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="padding: 1rem">
{{--                <h4 class="card-title" style="width: 300px; float: left"><?=$subjudul?></h4>--}}
                <h4 class="card-title" style="width: 500px; margin: 0 auto;text-align: center;">
                    <span><?=$subjudul?></span>
                </h4>
{{--                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>--}}
{{--					<div class="heading-elements">--}}
{{--						<ul class="list-inline mb-0">--}}
{{--							<li><a data-action="expand"><i class="ft-maximize"></i></a></li>--}}
{{--							<li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>--}}
{{--							<li><a data-action="close"><i class="ft-x"></i></a></li>--}}
{{--							<li><a data-action="collapse"><i class="ft-minus"></i></a></li>--}}
{{--						</ul>--}}
{{--					</div>--}}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="alert bg-danger">
                                <p>Perhatian :</p>
                                <ul class="">
                                    <li>Data No Peserta maks. {{ MAX_MHS_ID_LENGTH }} karakter</li>
                                    <li>Data Nama maks. {{ MAX_NM_PESERTA_LENGTH }} karakter</li>
                                    <li>Data NIK adalah {{ NIK_LENGTH }} karakter</li>
                                    <li>Data Tmp Lahir adalah {{ MAX_TMP_LAHIR_PESERTA_LENGTH }} karakter</li>
                                    <li>Data Jenis Kel. hanya berisi {{ JK_AVAIL[0] }} atau {{ JK_AVAIL[1] }}</li>
                                    <li>Data Email maks. {{ MAX_EMAIL_PESERTA_LENGTH }} karakter</li>
                                    <li>Data Billkey diisi sebagai <span class="bg-yellow text-danger"><b>&nbsp;PASSWORD&nbsp;</b></span> login peserta</li>
                                    <li>Data No Billkey maks. {{ MAX_NO_BILLKEY_LENGTH }} karakter</li>
                                    <li>Data prodi default = <span class="bg-yellow text-danger"><b>&nbsp;{{ PRODI_TXT_DEFAULT }}&nbsp;</b></span></li>
                                    {{-- <li>Data Foto berisi url dari foto peserta, boleh dikosongi</li> --}}
                                    {{-- <li>Jika ada, data Materi Ujian harus sesuai dengan ID yang ada</li> --}}
                                </ul>
                            </div>
                            <div class="alert bg-white border-red text-dark">
                                <ul>
                                    <li>Silahkan download format excel dan dapat anda copy-paste kedalam table
                                    <li>Template Excel : [ <a href="<?= base_url('assets/dist/format/mahasiswa_simple.xlsx') ?>" class="">DOWNLOAD</a> ]</li>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-block btn-flat btn-primary" id="btn_import"><i class="fa fa-arrow-circle-o-up"></i> Simpan</button>
                            <a href="{{ site_url('mahasiswa/index') }}" class="btn btn-block btn-flat btn-warning"><i class="fa fa-arrow-left"></i> Batal</a>
                        </div>
                        <div class="col-md-9">
                            {{--                    <div class="table-responsive scroll-container">--}}
                            <div class="table-respove">
                            <div id="hot" class="hot" style="margin: 0 auto"></div>
                            </div>
                            {{--                        </div>--}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</section>
@endsection
