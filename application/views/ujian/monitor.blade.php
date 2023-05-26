@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL JS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/forms/icheck/icheck.css') }}">
<!-- END PAGE LEVEL JS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('assets/npm/node_modules/datatables.net-plugins/api/fnPagingInfo.js') }}"></script>
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/forms/icheck/icheck.min.js') }}"></script>
<!-- END PAGE VENDOR -->
@endpush

@push('page_custom_css')
<style type="text/css">
/* styling opsi */
html body {
    height: auto;
}
body {
    /*overflow: hidden;*/
}
.card.card-fullscreen{
    z-index: 9995 !important;
}
.swal2-container{
    z-index: 9997 !important;
}
.modal {
    z-index: 9996;
}
.table th, .table td {
    padding: 10px !important;
}
#tb_daftar_hadir > thead > tr > th {
    vertical-align: middle;
}
.div_catatan {
    border-bottom: 1px dashed #aaa;
    padding-bottom: 5px;
    margin: 0 8px;
    /* padding: 0 5px 5px 5px; */
    cursor: pointer;
}
</style>
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
	{{--let id_dosen = '{{ $dosen->id_dosen }}';--}}
    let table ;
    // let list_online = [];
    let list_mhs_online = [];
    let list_mhs_online_ips = {}; // KARENA ARRAY DI JS TIDAK BISA DI ISI KEY NYA SCR TEXT JD PAKAI OBJECT
    let list_absensi = [];
    let list_absensi_by_self = [];
    let jml_daftar_hadir = {{ $jml_daftar_hadir }};
    let jml_daftar_hadir_by_pengawas = {{ $jml_daftar_hadir_by_pengawas }};
    let as = null ;
    let user_id = null ;
    let trigger_by_user = true;
    
    function init_page_level() {
        ajaxcsrf();

        $('#jml_mhs_absen').text(jml_daftar_hadir);
        $('#jml_mhs_absen_by_self').text(jml_daftar_hadir_by_pengawas);

        $('#counter_mhs_online').text(0);

        $('.icheck').iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red',
        });

        table = $("#tb_daftar_hadir").DataTable({
            initComplete: function() {
                var api = this.api();
                $("#tb_daftar_hadir_filter input")
                    .off(".DT")
                    .on("keypress.DT", function(e) {
                    if(e.which == 13) {
                        api.search(this.value).draw();
                        return false;
                    }
                });

                if(socket_enable)
                    init_socket();

            },
            "drawCallback": function( settings ) {
                $.each(list_mhs_online, function(index, item){
                    $('#badge_koneksi_' + item).text('ONLINE').removeClass('bg-danger').addClass('bg-success');
                    // $('#badge_ip_' + item).text(list_mhs_online_ips[item]).show();
                });
                // $.each(list_absensi, function(index, item){
                //     $('#badge_absensi_' + item).text('SUDAH').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
                // });

                $('.icheck').iCheck({
                    checkboxClass: 'icheckbox_square-red',
                    radioClass: 'iradio_square-red',
                });
            },
            lengthMenu: [[10, 50, -1], [10, 50, "All"]],
            dom:
            "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
            @if(in_group(PENGAWAS_GROUP_ID))
            // {
            //     text: '<i class="fa fa-save"></i> Tampilkan Absensi Anda',
            //     className: 'btn btn-info btn-glow',
            //     action: function ( e, dt, node, config ) {
            //         load_absen_pengawas();
            //     }
            // },
            @endif
                {
                    text: '<i class="fa fa-th"></i> Tampilkan Semua Daftar Peserta',
                    className: 'btn btn-success btn-glow ml-1',
                    action: function ( e, dt, node, config ) {
                        reset_filter_dt();
                    }
                }
            ],
            oLanguage: {
                sProcessing: "loading..."
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('ujian/ajax/data_daftar_hadir') }}",
                type: "POST",
                data: function ( d ) {
                    d.id = '{{ $m_ujian->id_ujian }}';
                    d.as = as;
                    d.user_id = user_id;
                }
            },
            columns: [
                {
                    "data": 'absensi',
                    "orderable": false,
                    "searchable": false,
                    "width": "10%"
                },
                // {
                //     "data": 'absen_by',
                //     "orderable": false,
                //     "searchable": false,
                //     "width": "5%"
                // },
                {
                    "data": 'absen_by_username',
                    "orderable": false,
                    "searchable": false,
                    "width": "5%"
                },
                {
                    "data": 'bapu_a',
                    "orderable": false,
                    "searchable": false,
                    "width": "3%"
                },
                {
                    "data": 'bapu_b',
                    "orderable": false,
                    "searchable": false,
                    "width": "3%"
                },
                {
                    "data": 'bapu_c',
                    "orderable": false,
                    "searchable": false,
                    "width": "3%"
                },
                {
                    "data": 'bapu_catatan',
                    "orderable": false,
                    "searchable": false,
                    "width": "3%"
                },
                {
                    "data": 'koneksi',
                    "orderable": false,
                    "searchable": false,
                    "width": "5%"
                },
                {
                    "data": 'latency',
                    "orderable": false,
                    "searchable": false,
                    "width": "5%"
                },
                {
                    "data": 'status',
                    "orderable": false,
                    "searchable": false,
                    "width": "5%"
                },
                {
                    "data": 'aksi',
                    "orderable": false,
                    "searchable": false,
                    "width": "5%"
                },
                { 
                    "data": 'nim', 
                    "width": "10%" 
                },
                { "data": 'nama' },
                {
                    "data": 'prodi',
                    "orderable": false,
                    "searchable": false,
                    "width": "15%"
                }
            ],
            order: [[10, "asc"], [11, "asc"]],
            // rowId: function(a) {
            //   return a;
            // },
            // rowCallback: function(row, data, iDisplayIndex) {
            // var info = this.fnPagingInfo();
            // var page = info.iPage;
            // var length = info.iLength;
            // var index = page * length + (iDisplayIndex + 1);
            // $("td:eq(1)", row).html(index);
            // },
            // scrollX:        true,
            // fixedColumns:   {
            //     leftColumns: 6,
            // }
        });

    }

    function init_socket(){
        // conn = new WebSocket();
        conn = new WebSocket('{{ ws_url() }}');

        conn.onopen = function(e) {
            // console.log('wesocket status opened');
            sendmsg(JSON.stringify({
                'user_id':'{{ get_logged_user()->id }}',
                'm_ujian_id':'{{ $m_ujian->id_ujian }}',
                'as':'{{ get_selected_role()->name }}',
                'cmd':'OPEN',
                'app_id': '{{ $_ENV['APP_ID'] }}',
            }));
        };

        conn.onmessage = function(e) {
            // console.log('conn.onmessage', e.data);
            let data = jQuery.parseJSON(e.data);

            if(data.app_id == '{{ $_ENV['APP_ID'] }}') {

                if (data.cmd == 'OPEN') {

                    // $.each(data.absensi, function(index, nim){
                    //     push_absensi(nim);
                    //     $('#badge_absensi_' + nim).text('SUDAH').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
                    // });
                    // $('#jml_mhs_absen').text(list_absensi.length);

                    // if (data.user_id == '{{ get_logged_user()->id }}') { 
                    //     $.each(data.absensi_by_self, function (index, nim) { 
                    //         push_absensi_by_self(nim); 
                    //     }); 
                    //     $('#jml_mhs_absen_by_self').text(list_absensi_by_self.length); 
                    // } 

                    // $.each(data.mhs_online, function (index, code) {
                    //     let nim = index;
                    //     push_mhs_online(nim);
                    //     $('#badge_koneksi_' + nim).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                    //     // push_mhs_online_ips(nim, data.mhs_online_ips[nim]);
                    //     // $('#badge_ip_' + nim).text(data.mhs_online_ips[nim]).show();
                    // });
                    // $('#jml_mhs_online').text(list_mhs_online.length);

                    $('#counter_mhs_online').text(data.mhs_online_counter);
                    // console.log('list_mhs_online', list_mhs_online);
                } else if (data.cmd == 'MHS_ONLINE') {
                    // push_mhs_online(data.nim);
                    $('#badge_koneksi_' + data.nim).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                    // $('#badge_ip_' + data.nim).text(data.ip).show();
                    // $('#jml_mhs_online').text(list_mhs_online.length);

                    $('#counter_mhs_online').text(data.mhs_online_counter);
                } else if (data.cmd == 'MHS_LOST_FOCUS') {
                    $('#badge_focus_' + data.nim).show();
                } else if (data.cmd == 'MHS_GET_FOCUS') {
                    $('#badge_focus_' + data.nim).hide();
                } else if (data.cmd == 'MHS_START_UJIAN') {
                    $('#badge_status_' + data.nim).text('SEDANG UJIAN').removeClass('bg-secondary').removeClass('bg-success').addClass('bg-danger');
                } else if (data.cmd == 'MHS_STOP_UJIAN') {
                    $('#badge_status_' + data.nim).text('SUDAH UJIAN').removeClass('bg-secondary').removeClass('bg-danger').addClass('bg-success');
                } else if (data.cmd == 'DO_ABSENSI') {
                    if (data.ok) {

                        // push_absensi(data.nim);
                        // $('#jml_mhs_absen').text(list_absensi.length);
                        // if (data.user_id == '{{ get_logged_user()->id }}') {
                        //     push_absensi_by_self(data.nim);
                        //     $('#jml_mhs_absen_by_self').text(list_absensi_by_self.length);
                        // }

                        $('#jml_mhs_absen').text(++jml_daftar_hadir);
                        if (data.user_id == '{{ get_logged_user()->id }}') {
                            $('#jml_mhs_absen_by_self').text(++jml_daftar_hadir_by_pengawas);
                        }

                        if (data.user_id != '{{ get_logged_user()->id }}') {
                            $('#badge_absensi_' + data.nim).text('SUDAH').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
                            trigger_by_user = false;
                            $('#bapu_catatan_' + data.nim).removeClass('text-danger').addClass('text-success');
                            $('#checkbox_is_terlihat_pada_layar_' + data.nim).iCheck('uncheck');
                            $('#checkbox_is_perjokian_' + data.nim).iCheck('uncheck');
                            $('#checkbox_is_sering_buka_page_lain_' + data.nim).iCheck('uncheck');
                            trigger_by_user = true;
                        }
                    }
                } else if (data.cmd == 'DO_ABSENSI_BATAL') {
                    if (data.ok) {

                        // pop_absensi(data.nim);
                        // $('#jml_mhs_absen').text(list_absensi.length);
                        // if (data.user_id == '{{ get_logged_user()->id }}') {
                        //     pop_absensi_by_self(data.nim);
                        //     $('#jml_mhs_absen_by_self').text(list_absensi_by_self.length);
                        // }

                        $('#jml_mhs_absen').text(--jml_daftar_hadir);
                        if (data.user_id == '{{ get_logged_user()->id }}') {
                            $('#jml_mhs_absen_by_self').text(--jml_daftar_hadir_by_pengawas);
                        }
                        
                        if (data.user_id != '{{ get_logged_user()->id }}') {
                            $('#badge_absensi_' + data.nim).text('BELUM').removeClass('success').removeClass('border-success').addClass('border-danger').addClass('danger');
                            trigger_by_user = false;
                            $('#bapu_catatan_' + data.nim).removeClass('text-danger').addClass('text-success');
                            $('#checkbox_is_terlihat_pada_layar_' + data.nim).iCheck('uncheck');
                            $('#checkbox_is_perjokian_' + data.nim).iCheck('uncheck');
                            $('#checkbox_is_sering_buka_page_lain_' + data.nim).iCheck('uncheck');
                            trigger_by_user = true;
                        }
                    }
                } else if (data.cmd == 'DO_BAPU') {
                    if (data.ok) {
                        trigger_by_user = false ;
                        if (data.user_id != '{{ get_logged_user()->id }}') { // BIAR TIDAK TER-TRIGGER 2 KALI
                            if(data.bapu.is_terlihat_pada_layar)
                                $('#checkbox_is_terlihat_pada_layar_' + data.nim).iCheck('check');
                            else
                                $('#checkbox_is_terlihat_pada_layar_' + data.nim).iCheck('uncheck');

                            if(data.bapu.is_perjokian)
                                $('#checkbox_is_perjokian_' + data.nim).iCheck('check');
                            else
                                $('#checkbox_is_perjokian_' + data.nim).iCheck('uncheck');

                            if(data.bapu.is_sering_buka_page_lain)
                                $('#checkbox_is_sering_buka_page_lain_' + data.nim).iCheck('check');
                            else
                                $('#checkbox_is_sering_buka_page_lain_' + data.nim).iCheck('uncheck');

                            if(data.bapu.catatan_pengawas)
                                $('#bapu_catatan_' + data.nim).removeClass('text-success').addClass('text-danger');
                            else
                                $('#bapu_catatan_' + data.nim).removeClass('text-danger').addClass('text-success');
                        }
                        trigger_by_user = true ;
                    }
                } else if (data.cmd == 'PING') {
                    $('#badge_koneksi_' + data.nim).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                    // $('#badge_ip_' + data.nim).text(data.ip).show();
                    $('#badge_latency_' + data.nim).text(data.latency + 'ms').removeClass('bg-grey').show();
                    if(data.latency > 1000)
                        $('#badge_latency_' + data.nim).removeClass('bg-success').addClass('bg-danger');
                    else
                        $('#badge_latency_' + data.nim).removeClass('bg-danger').addClass('bg-success');

                    $('#counter_mhs_online').text(data.mhs_online_counter);

                } else if (data.cmd == 'MHS_OFFLINE') {
                    // pop_mhs_online(data.nim);
                    $('#badge_koneksi_' + data.nim).text('OFFLINE').removeClass('bg-success').removeClass('bg-warning').addClass('bg-danger');
                    // $('#badge_ip_' + data.nim).hide();
                    $('#badge_latency_' + data.nim).text('0ms').removeClass('bg-success').removeClass('bg-danger').addClass('bg-grey');
                    // $('#jml_mhs_online').text(list_mhs_online.length);

                    $('#counter_mhs_online').text(data.mhs_online_counter);
                    
                }else if (data.cmd == 'DO_KICK') {
                    $('#badge_status_' + data.nim).text('SUDAH UJIAN').removeClass('bg-secondary').removeClass('bg-danger').addClass('bg-success');
                    $('#badge_focus_' + data.nim).hide();
                }
            }

            
        };

        conn.onclose = function(e) {
            // console.log('conn.onclose', e.data);
        };

        sendmsg(JSON.stringify({
            'nim':'{{ get_logged_user()->username }}',
            'as':'{{ get_selected_role()->name }}',
            'cmd':'PING',
            'ip': '-',
            'app_id': '{{ $_ENV['APP_ID'] }}',
            'latency': '-' ,
        }));

        if(enable_ping){
            setInterval(function() {
                if(!stop_ping){
                    sendmsg(JSON.stringify({
                        'nim':'{{ get_logged_user()->username }}',
                        'as':'{{ get_selected_role()->name }}',
                        'cmd':'PING',
                        'ip': '-',
                        'app_id': '{{ $_ENV['APP_ID'] }}',
                        'latency': '-' ,
                    }));
                }
            },{{ get_ping_interval() }});
        }
    }


    $(document).on('click','.btn_absensi',function(){
        let mahasiswa_ujian_id = $(this).data('id');
        let nim = $(this).data('nim');

        ajx_overlay(true);
        $.post('{{ url('ujian/ajax/absen_pengawas') }}', {'mahasiswa_ujian_id' : mahasiswa_ujian_id, 'nim' : nim}, function (res){
            if(res.ok) {
                $('#badge_absensi_' + nim).text('SUDAH').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
                trigger_by_user = false;
                $('#bapu_catatan_' + nim).removeClass('text-danger').addClass('text-success');
                $('#checkbox_is_terlihat_pada_layar_' + nim).iCheck('uncheck');
                $('#checkbox_is_perjokian_' + nim).iCheck('uncheck');
                $('#checkbox_is_sering_buka_page_lain_' + nim).iCheck('uncheck');
                trigger_by_user = true;
                sendmsg(JSON.stringify({
                    'mahasiswa_ujian_id': mahasiswa_ujian_id,
                    'user_id': '{{ get_logged_user()->id }}',
                    'as': '{{ get_selected_role()->name }}',
                    'nim': nim,
                    'cmd': 'DO_ABSENSI',
                    'app_id': '{{ $_ENV['APP_ID'] }}',
                }));
            }else{
                Swal.fire({
                    title: "Terjadi Kesalahan",
                    text: res.msg,
                    icon: "warning"
                });
            }
        }).fail(function(){
            Swal.fire({
                title: "Perhatian",
                text: "Terjadi kesalahan",
                icon: "warning"
            });
        }).always(function() {
            ajx_overlay(false);
        });
    });

    $(document).on('click','.btn_absensi_batal',function(){
        Swal.fire({
            title: "Anda yakin",
            text: "Absensi peserta tsb akan dihapus",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Hapus"
        }).then(result => {
            if (result.value) {
                let mahasiswa_ujian_id = $(this).data('id');
                let nim = $(this).data('nim');
                ajx_overlay(true);
                $.post('{{ url('ujian/ajax/absen_pengawas') }}', {'mahasiswa_ujian_id' : mahasiswa_ujian_id, 'nim' : nim, 'aksi' : 'batal'}, function (res){
                    if(res.ok) {
                        $('#badge_absensi_' + nim).text('BELUM').removeClass('success').removeClass('border-success').addClass('border-danger').addClass('danger');
                        trigger_by_user = false;
                        $('#bapu_catatan_' + nim).removeClass('text-danger').addClass('text-success');
                        $('#checkbox_is_terlihat_pada_layar_' + nim).iCheck('uncheck');
                        $('#checkbox_is_perjokian_' + nim).iCheck('uncheck');
                        $('#checkbox_is_sering_buka_page_lain_' + nim).iCheck('uncheck');
                        trigger_by_user = true;

                        sendmsg(JSON.stringify({
                            'mahasiswa_ujian_id': mahasiswa_ujian_id,
                            'user_id': '{{ get_logged_user()->id }}',
                            'as': '{{ get_selected_role()->name }}',
                            'nim': nim,
                            'cmd': 'DO_ABSENSI_BATAL',
                            'app_id': '{{ $_ENV['APP_ID'] }}',
                        }));
                    }else{
                        Swal.fire({
                            title: "Terjadi Kesalahan",
                            text: res.msg,
                            icon: "warning"
                        });
                    }
                }).fail(function(){
                    Swal.fire({
                        title: "Perhatian",
                        text: "Terjadi kesalahan",
                        icon: "warning"
                    });
                }).always(function() {
                    ajx_overlay(false);
                });
            }
        });
    });

    // function push_absensi(nim){
    //     nim = nim.toString();
    //     const index = list_absensi.indexOf(nim);
    //     if (index > -1) {
    //       list_absensi.splice(index, 1);
    //     }
    //     list_absensi.push(nim);
    // }

    // function pop_absensi(nim){
    //     nim = nim.toString();
    //     const index = list_absensi.indexOf(nim);
    //     if (index > -1) {
    //       list_absensi.splice(index, 1);
    //     }
    // }

    // function push_absensi_by_self(nim){
    //     nim = nim.toString();
    //     const index = list_absensi_by_self.indexOf(nim);
    //     if (index > -1) {
    //       list_absensi_by_self.splice(index, 1);
    //     }
    //     list_absensi_by_self.push(nim);
    // }
    //
    // function pop_absensi_by_self(nim){
    //     nim = nim.toString();
    //     const index = list_absensi_by_self.indexOf(nim);
    //     if (index > -1) {
    //       list_absensi_by_self.splice(index, 1);
    //     }
    // }

    function push_mhs_online(nim){
        nim = nim.toString();
        const index = list_mhs_online.indexOf(nim);
        if (index > -1) {
          list_mhs_online.splice(index, 1);
        }
        list_mhs_online.push(nim);
    }

    function pop_mhs_online(nim){
        nim = nim.toString();
        const index = list_mhs_online.indexOf(nim);
        if (index > -1) {
          list_mhs_online.splice(index, 1);
        }
    }

    function push_mhs_online_ips(nim, ip){
        nim = nim.toString();
        ip = ip.toString();
        delete list_mhs_online_ips[nim];
        list_mhs_online_ips[nim] = ip;
        // console.log(list_mhs_online_ips);
    }

    function pop_mhs_online_ips(nim){
        nim = nim.toString();
        // ip = ip.toString();
        delete list_mhs_online_ips[nim];
    }

    function load_absen_pengawas(){
        as = 'pengawas';
        user_id = '{{ get_logged_user()->id }}';

        // table.ajax.url('{!! url('ujian/ajax/data_absen_pengawas/?id=' . $m_ujian->id_ujian . '&user_id=' . get_logged_user()->id) !!}').load();
        table.ajax.reload();
    }

    function load_absen_semua(){
        as = 'pengawas';
        user_id = 'ALL';

        // table.ajax.url('{!! url('ujian/ajax/data_daftar_hadir') !!}') .load()
        table.ajax.reload();
    }

    function reset_filter_dt(){
        as = null;
        user_id = null;
        table.search('').columns().search('').draw();
    }

    $(document).on('click','.btn_kick',function(){

        let mahasiswa_ujian_id = $(this).data('id');
        let nim = $(this).data('nim');
        let status_ujian = $('#badge_status_' + nim).text();

        if((status_ujian == 'BELUM UJIAN') || (status_ujian == 'SUDAH UJIAN')){
            Swal.fire({
                title: "Perhatian",
                text: "Peserta belum / selesai ujian",
                icon: "info"
            });
            return;
        }

        Swal.fire({
            title: "Anda yakin",
            text: "Sesi ujian peserta tsb akan diakhiri",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Akhiri"
        }).then(result => {
            if (result.value) {
                ajaxcsrf();
                ajx_overlay(true);
                $.ajax({
                    type: "POST",
                    url: "{{ url('ujian/ajax/force_close_ujian') }}",
                    data: {
                        'id': mahasiswa_ujian_id,
                        'ended_by': '{{ get_logged_user()->username }}',
                    },
                    success: function (r) {
                        if (r.status) {
                            sendmsg(JSON.stringify({
                                'mahasiswa_ujian_id': mahasiswa_ujian_id,
                                'user_id': '{{ get_logged_user()->id }}',
                                'username': '{{ get_logged_user()->username }}',
                                'as':'{{ get_selected_role()->name }}',
                                'nim': nim,
                                'cmd':'DO_KICK',
                                'app_id': '{{ $_ENV['APP_ID'] }}',
                            }));
                            $('#badge_status_' + nim).text('SUDAH UJIAN').removeClass('bg-secondary').removeClass('bg-danger').addClass('bg-success');
                            $('#badge_focus_' + nim).hide();

                            Swal.fire({
                                title: "Perhatian",
                                text: "Ujian peserta tsb telah diakhiri",
                                icon: "success"
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: "Perhatian",
                            text: "Maaf, terjadi kesalahan",
                            icon: "warning"
                        });
                    },
                    complete: function () {
                        ajx_overlay(false);
                    }
                });
            }
        });
    });

    $(document).on('click','.btn_absensi_check',function() {
        $.post('{{ url('ujian/ajax/check_pengabsen') }}',{'mahasiswa_ujian_id': $(this).data('id') },function(res){
            let txt = "Belum diabsenkan" ;
            let type = "warning";
            let nama_pengabsen = res.nama_pengabsen;
            if(nama_pengabsen) {
                txt = "Telah Diabsen Oleh : " + res.nama_pengabsen;
                type = "success";
            }
            Swal.fire({
                title: "Info Absen",
                text: txt,
                icon: type,
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Tutup"
            });
        });
    });

function checkImage(imageSrc, good, bad) {
    let img = new Image();
    img.onload = good;
    img.onerror = bad;
    img.src = imageSrc;
}

let foto_url = null ;
$(document).on('click','.btn_foto',function(){
    ajx_overlay(true);
    let no_peserta = $(this).data('nim');
    $.post('{{ url('ujian/ajax/get_foto_url') }}',{'nim': no_peserta},function(data){
        // $('#img_profile').attr('src',data.src_img);
        // $('#modal_foto_peserta').modal('show');
        foto_url = data.src_img ;
        $('#span_no_peserta').text(no_peserta);
        checkImage(data.src_img, function(){
                $('#img_profile').attr('src',data.src_img);
                $('#modal_foto_peserta').modal('show');
                ajx_overlay(false);
            }, function(){
                $('#img_profile').attr('src','{{ asset('assets/imgs/no_profile.jpg') }}');
                $('#modal_foto_peserta').modal('show');
                ajx_overlay(false);
            }
        );
    });
});

$(document).on('click','#btn_reload_foto',function(){
    ajx_overlay(true);
    checkImage(foto_url, function(){
            // console.log('good');
            $('#img_profile').attr('src',foto_url);
            ajx_overlay(false);
        }, function(){
            // console.log('bad');
            $('#img_profile').attr('src','{{ asset('assets/imgs/no_profile.jpg') }}');
            ajx_overlay(false);
        }
    );
});

$(document).on('click','#btn_absensi_pengawas',function(){
    load_absen_pengawas();
});

$(document).on('click','#btn_absensi_semua',function(){
    load_absen_semua();
});


$(document).on('ifChanged','.checkbox_bapu',function(){

    if(trigger_by_user){
        let mahasiswa_ujian_id = $(this).data('id');
        let nim = $(this).data('nim');

        let bapu = {
            'is_terlihat_pada_layar': $('#checkbox_is_terlihat_pada_layar_' + nim).is(':checked') ? 1 : 0,
            'is_perjokian': $('#checkbox_is_perjokian_' + nim).is(':checked') ? 1 : 0,
            'is_sering_buka_page_lain': $('#checkbox_is_sering_buka_page_lain_' + nim).is(':checked') ? 1 : 0,
            'catatan_pengawas': $('#bapu_catatan_' + nim).hasClass('text-danger') ? 1 : 0,
        };

        ajx_overlay(true);
        let el = $(this);
        $.post('{{ url('ujian/ajax/bapu_pengawas') }}', {'mahasiswa_ujian_id' : mahasiswa_ujian_id, 'nim' : nim, 'bapu' : bapu}, function (res){
            if(res.ok) {
                sendmsg(JSON.stringify({
                    'mahasiswa_ujian_id': mahasiswa_ujian_id,
                    'user_id': '{{ get_logged_user()->id }}',
                    'as': '{{ get_selected_role()->name }}',
                    'nim': nim,
                    'cmd': 'DO_BAPU',
                    'bapu': bapu,
                    'app_id': '{{ $_ENV['APP_ID'] }}',
                }));
            }else{
                Swal.fire({
                    title: "Perhatian",
                    text: "Anda bukan yg mengabsenkan / Belum diabsenkan",
                    icon: "warning"
                });
                // el.prop('checked', false);
                // el.iCheck('update');
                // if(el.is(':checked'))
                trigger_by_user = false;
                if(el.is(':checked'))
                    el.iCheck('uncheck');
                else
                    el.iCheck('check');
                trigger_by_user = true;
            }
        }).always(function() {
            ajx_overlay(false);
        });
    }
});

$(document).on('click','#btn_submit_catatan',function(){
    if(trigger_by_user){
        let mahasiswa_ujian_id = $(this).data('id');
        let nim = $(this).data('nim');
        let catatan_pengawas = $('#catatan_pengawas').val();

        let bapu = {
            'is_terlihat_pada_layar': $('#checkbox_is_terlihat_pada_layar_' + nim).is(':checked') ? 1 : 0,
            'is_perjokian': $('#checkbox_is_perjokian_' + nim).is(':checked') ? 1 : 0,
            'is_sering_buka_page_lain': $('#checkbox_is_sering_buka_page_lain_' + nim).is(':checked') ? 1 : 0,
            'catatan_pengawas': catatan_pengawas.length ? 1 : 0,
        };

        ajx_overlay(true);
        $.post('{{ url('ujian/ajax/set_catatan_pengawas') }}',{'mahasiswa_ujian_id': mahasiswa_ujian_id, 'catatan_pengawas': catatan_pengawas}, function(res){
            if(res.ok){

                sendmsg(JSON.stringify({
                    'mahasiswa_ujian_id': mahasiswa_ujian_id,
                    'user_id': '{{ get_logged_user()->id }}',
                    'as': '{{ get_selected_role()->name }}',
                    'nim': nim,
                    'cmd': 'DO_BAPU',
                    'bapu': bapu,
                    'app_id': '{{ $_ENV['APP_ID'] }}',
                }));

                Swal.fire({
                    title: "Perhatian",
                    text: "Catatan berhasil disimpan",
                    icon: "success"
                });

                trigger_by_user = false;
                if(catatan_pengawas.length)
                    $('#bapu_catatan_' + nim).removeClass('text-success').addClass('text-danger');
                else
                    $('#bapu_catatan_' + nim).removeClass('text-danger').addClass('text-success');
                trigger_by_user = true;
                $('#modal_catatan_peserta').modal('hide');
            }else{
                Swal.fire({
                    title: "Terjadi Kesalahan",
                    text: res.msg,
                    icon: "warning"
                });
            }
        }).fail(function() {
            Swal.fire({
                title: "Perhatian",
                text: "Terjadi kesalahan",
                icon: "warning"
            });
        }).always(function() {
            ajx_overlay(false);
        });
    }
});

$(document).on('click','.div_catatan',function(){
    ajx_overlay(true);
    let mahasiswa_ujian_id = $(this).data('id');
    let nim = $(this).data('nim');
    $.post('{{ url('ujian/ajax/get_catatan_pengawas') }}',{'mahasiswa_ujian_id': mahasiswa_ujian_id}, function(data){
        $('#span_no_peserta_2').text(nim);
        $('#btn_submit_catatan').data('id', mahasiswa_ujian_id);
        $('#btn_submit_catatan').data('nim', nim);
        $('#catatan_pengawas').val(data.catatan_pengawas);
        $('#modal_catatan_peserta').modal('show');
    }).fail(function() {
        Swal.fire({
            title: "Perhatian",
            text: "Terjadi kesalahan / Belum diabsenkan",
            icon: "warning"
        });
    }).always(function() {
        ajx_overlay(false);
    });
});

</script>
<!-- END PAGE LEVEL JS-->
@endpush

@section('content')
<section style="background-color: #f3f3f3; overflow-x: hidden;" class="card card-fullscreen">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
            	<h4 class="card-title"><?=$subjudul?> : {{ strtoupper($m_ujian->nama_ujian) }}</h4>
            	<a class="heading-elements-toggle"><i class="ft-ellipsis-h font-medium-3"></i></a>
            </div>
            <div class="card-content">
                <div class="card-body">


<!---- --->
<div class="box">
    <div class="box-body">
        <div class="mb-4" style="text-align: center">
                <a href="{{ site_url('ujian/master') }}" class="btn btn-warning btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
{{--                <button type="button" onclick="reload_ajax()" class="btn btn-outline-secondary btn-glow"><i class="fa fa-refresh"></i> Reload</button>--}}

                {{-- <button type="button" class="btn btn-outline-success btn-glow">
                    JUMLAH MHS ONLINE : <span id="jml_mhs_online">0</span>
                </button> --}}

                <button type="button" id="btn_mhs_online" class="btn btn-outline-success btn-glow">
                    JUMLAH MHS ONLINE : <span id="counter_mhs_online">0</span>
                </button>

                @if(in_group(PENGAWAS_GROUP_ID))
                <button type="button" class="btn btn-outline-primary btn-glow" id="btn_absensi_pengawas">
                    JUMLAH MHS ABSEN OLEH ANDA : <span id="jml_mhs_absen_by_self">0</span>
                </button>
                @endif

                <button type="button" class="btn btn-outline-danger btn-glow" id="btn_absensi_semua">
                    JUMLAH MHS SUDAH ABSEN TOTAL : <span id="jml_mhs_absen">0</span>
                </button>
        </div>
    </div>
    <div class="table-responsive pb-2" style="">
        <table id="tb_daftar_hadir" class="table table-striped table-bordered table-hover w-100">
        <thead>
            <tr>
                <th style="text-align: center" rowspan="2">Menu</th>
                <th rowspan="2">Absensi</th>
                <th style="text-align: center" colspan="4">Bapu</th>
                <th rowspan="2">Online</th>
                <th rowspan="2">Ltcy</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">Aksi</th>
                <th rowspan="2">No Peserta</th>
                <th rowspan="2">Nama</th>
                <th >Prodi</th>
            </tr>
            <tr>
                <th style="text-align: center"><small>Tidak Terlihat<br/>Pada Layar</small></th>
                <th style="text-align: center"><small>Perjokian</small></th>
                <th style="text-align: center"><small>Sering Buka<br/>Laman Lain</small></th>
                <th style="text-align: center"><small>Catatan</small></th>
                <th style=""><small>Pilihan Prodi</small></th>
            </tr>

            {{-- <tr>
                <th style="text-align: center">Menu</th>
                <th>Absensi</th>
                <th style="text-align: center">A</th>
                <th style="text-align: center">B</th>
                <th style="text-align: center">C</th>
                <th>Online</th>
                <th>Lat</th>
                <th>Status</th>
                <th>Aksi</th>
                <th>No Peserta</th>
                <th>Nama</th>
                <th>Prodi</th>
            </tr> --}}

        </thead>
        </table>
    </div>
</div>

<script type="text/javascript">

</script>
<!---- --->

				</div>
            </div>
        </div>
    </div>
</div>
</section>

<!-- Modal -->
<div class="modal"
     id="modal_foto_peserta"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel11"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"
         role="document">
        <div class="modal-content">
            <div class="modal-header bg-info white">
                <h4 class="modal-title white"
                    id="myModalLabel11">No Peserta : <span id="span_no_peserta"></span></h4>
            </div>
            <div class="modal-body" style="text-align: center">
                <img id="img_profile" style="width: 250px" src="{{ asset('assets/imgs/no_profile.jpg') }}" />
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-info" id="btn_reload_foto">Reload Foto
                </button>
                <button type="button"
                        class="btn grey btn-outline-secondary"
                        data-dismiss="modal">Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal"
     id="modal_catatan_peserta"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel12"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"
         role="document">
        <div class="modal-content">
            <div class="modal-header bg-info white">
                <h4 class="modal-title white"
                    id="myModalLabel12">No Peserta : <span id="span_no_peserta_2"></span></h4>
            </div>
            <div class="modal-body">
                <label>Catatan Pengawas</label>
                <textarea id="catatan_pengawas" rows="10" class="form_input w-100" {{ (!in_group(PENGAWAS_GROUP_ID) ? 'disabled="disabled"' : '') }}></textarea>
            </div>
            <div class="modal-footer">
                @if(in_group(PENGAWAS_GROUP_ID))
                <button type="button"
                        class="btn btn-info" id="btn_submit_catatan">Submit
                </button>
                @endif
                <button type="button"
                        class="btn grey btn-outline-secondary"
                        data-dismiss="modal">Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection