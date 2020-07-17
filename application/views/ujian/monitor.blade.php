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
<script src="//cdn.datatables.net/plug-ins/1.10.21/api/fnPagingInfo.js"></script>
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
<!-- END PAGE VENDOR -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
	{{--let id_dosen = '{{ $dosen->id_dosen }}';--}}
    let table ;
    // let list_online = [];
    let list_mhs_online = [];
    let list_absensi = [];
    let list_absensi_by_self = [];
    let conn ;
    function init_page_level() {
        ajaxcsrf();
        init_socket();
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
            $.each(list_mhs_online, function(index, item){
                $('#badge_koneksi_' + item).text('ONLINE').removeClass('bg-danger').addClass('bg-success');
            });
            $.each(list_absensi, function(index, item){
                $('#badge_absensi_' + item).text('SUDAH ABSEN').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
            });
        },
        "drawCallback": function( settings ) {
            $.each(list_mhs_online, function(index, item){
                $('#badge_koneksi_' + item).text('ONLINE').removeClass('bg-danger').addClass('bg-success');
            });
            $.each(list_absensi, function(index, item){
                $('#badge_absensi_' + item).text('SUDAH ABSEN').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
            });
        },
        lengthMenu: [[5, 10, 50, -1], [5, 10, 50, "All"]],
        dom:
          "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
          @if(in_group('pengawas'))
          {
              text: '<i class="fa fa-save"></i> Tampilkan Absensi Anda',
              className: 'btn btn-info btn-glow',
              action: function ( e, dt, node, config ) {
                  // reload_ajax();
              }
          }
          @endif
      ],
        oLanguage: {
          sProcessing: "loading..."
        },
        processing: true,
        serverSide: true,
        ajax: {
          url: "{{ url('ujian/ajax/data_daftar_hadir') }}",
          type: "POST",
          data: {'id' : '{{ $m_ujian->id_ujian }}'}
        },
        columns: [
            {
                "data": 'absensi',
                "orderable": false,
                "searchable": false
            },
            {
                "data": 'absen_by',
                "orderable": false,
                "searchable": false
            },
            { "data": 'nim' },
            { "data": 'nama' },
            { "data": 'nik' },
            {
                "data": 'tgl_lahir',
                "orderable": false,
                "searchable": false
            },
            {
                "data": 'prodi',
                "orderable": false,
                "searchable": false
            },
            {
                "data": 'koneksi',
                "orderable": false,
                "searchable": false
            }
        ],
        order: [[0, "asc"], [5, "asc"]],
        rowId: function(a) {
          return a;
        },
        rowCallback: function(row, data, iDisplayIndex) {
          // var info = this.fnPagingInfo();
          // var page = info.iPage;
          // var length = info.iLength;
          // var index = page * length + (iDisplayIndex + 1);
          // $("td:eq(1)", row).html(index);
        },
        scrollX:        true,
        fixedColumns:   {
            leftColumns: 3,
        }
      });

    }

    function init_socket(){
        conn = new WebSocket('wss://ujian.undip.ac.id/wss2/NNN');
        // conn = new WebSocket('ws://localhost:8080');
        conn.onopen = function(e) {
            console.log('conn.onopen');
{{--            conn.send(JSON.stringify({'username':'{{ get_logged_user()->username }}'}));--}}
            conn.send(JSON.stringify({
                'user_id':'{{ get_logged_user()->id }}',
                'm_ujian_id':'{{ $m_ujian->id_ujian }}',
                'as':'{{ get_selected_role()->name }}',
                'cmd':'OPEN'
            }));
        };

        conn.onmessage = function(e) {
            // console.log('conn.onmessage', e.data);
            let data = jQuery.parseJSON(e.data);
            // if(Array.isArray(data)){
            //     $.each(data, function(index, item){
            //         if(item.cmd == 'ONLINE'){
            //             list_mhs_online.push(item.username);
            //             $('#badge_koneksi_' + item.username).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
            //         }else if(item.cmd == 'LIST ABSENSI'){
            //             const index = list_absensi.indexOf(item.nim);
            //             if (index > -1) {
            //               list_absensi.splice(index, 1);
            //             }
            //             list_absensi.push(item.nim);
            //             $('#badge_absensi_' + item.nim).text('SUDAH ABSEN').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
            //         }else if(item.cmd == 'BUKA TAB LAIN'){
            //             list_mhs_online.push(item.username);
            //             $('#badge_koneksi_' + item.username).text('BUKA TAB LAIN').removeClass('bg-danger').removeClass('bg-success').addClass('bg-warning');
            //         }
            //     });
            //
            // }else {
                if (data.cmd == 'OPEN') {
                    $.each(data.absensi,function(index, nim){
                        push_absensi(nim);
                        $('#badge_absensi_' + nim).text('SUDAH ABSEN').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
                    });
                    $.each(data.absensi_by_self,function(index, nim){
                        push_absensi_by_self(nim);
                    });
                    $.each(data.mhs_online,function(index, code){
                        let nim = index ;
                        push_mhs_online(nim);
                        $('#badge_koneksi_' + nim).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                    });
                    $('#jml_mhs_absen').text(list_absensi.length);
                    $('#jml_mhs_absen_by_self').text(list_absensi_by_self.length);
                    $('#jml_mhs_online').text(list_mhs_online.length);
                }else if (data.cmd == 'MHS_ONLINE') {
                    push_mhs_online(data.nim);
                    $('#badge_koneksi_' + data.nim).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                    $('#jml_mhs_online').text(list_mhs_online.length);
                }else if (data.cmd == 'MHS_OFFLINE') {
                    pop_mhs_online(data.nim);
                    $('#badge_koneksi_' + data.nim).text('OFFLINE').removeClass('bg-success').removeClass('bg-warning').addClass('bg-danger');
                    $('#jml_mhs_online').text(list_mhs_online.length);
                }else if (data.cmd == 'MHS_NEW_TAB') {
                    $('#badge_koneksi_' + data.nim).text('BUKA TAB LAIN').removeClass('bg-danger').removeClass('bg-success').addClass('bg-warning');
                    $('#jml_mhs_online').text(list_mhs_online.length);
                }
                // else if (data.cmd == 'ONLINE') {
                //     list_online.push(data.username);
                //     $('#badge_koneksi_' + data.username).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                // }
                // else if(data.cmd == 'OFFLINE') {
                //     const index = list_online.indexOf(data.username);
                //     if (index > -1) {
                //       list_online.splice(index, 1);
                //     }
                //     $('#badge_koneksi_' + data.username).text('OFFLINE').removeClass('bg-success').removeClass('bg-warning').addClass('bg-danger');
                // }
                // else if(data.cmd == 'ABSENSI'){
                //     // console.log(data.nim);
                //     const index = list_absensi.indexOf(data.nim);
                //     if (index > -1) {
                //       list_absensi.splice(index, 1);
                //     }
                //     list_absensi.push(data.nim);
                //     $('#badge_absensi_' + data.nim).text('SUDAH ABSEN').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
                //     $('#jml_sudah_absen').text(list_absensi.length);
                // }else if (data.cmd == 'BUKA TAB LAIN') {
                //     list_online.push(data.username);
                //
                // }
            // }
        };

        conn.onclose = function(e) {
            // console.log('conn.onclose', e.data);
        };
    }


    $(document).on('click','.btn_absensi',function(){
        let mahasiswa_ujian_id = $(this).data('id');
        let nim = $(this).data('nim');
        conn.send(JSON.stringify({
            'mahasiswa_ujian_id': mahasiswa_ujian_id,
            'user_id':'{{ get_logged_user()->id }}',
            'as':'{{ get_selected_role()->name }}',
            'cmd':'DO_ABSENSI'
        }));
        $('#badge_absensi_' + nim).text('SUDAH ABSEN').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
        push_absensi(nim);
        push_absensi_by_self(nim);
        $('#jml_mhs_absen').text(list_absensi.length);
        $('#jml_mhs_absen_by_self').text(list_absensi_by_self.length);
    });

    function push_absensi(nim){
        nim = nim.toString();
        const index = list_absensi.indexOf(nim);
        if (index > -1) {
          list_absensi.splice(index, 1);
        }
        list_absensi.push(nim);
    }

    function push_absensi_by_self(nim){
        nim = nim.toString();
        const index = list_absensi_by_self.indexOf(nim);
        if (index > -1) {
          list_absensi_by_self.splice(index, 1);
        }
        list_absensi_by_self.push(nim);
    }

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
<div class="box">
    <div class="box-body">
        <div class="mb-4">
                <button type="button" onclick="reload_ajax()" class="btn btn-outline-secondary btn-glow"><i class="fa fa-refresh"></i> Reload</button>

                <button type="button" class="btn btn-outline-success btn-glow ml-1">
                    JUMLAH MHS ONLINE : <span id="jml_mhs_online">0</span>
                </button>

                <button type="button" class="btn btn-outline-primary btn-glow ml-1">
                    JUMLAH MHS ABSEN OLEH ANDA : <span id="jml_mhs_absen_by_self">0</span>
                </button>

                <button type="button" class="btn btn-outline-danger btn-glow ml-1">
                    JUMLAH MHS SUDAH ABSEN TOTAL : <span id="jml_mhs_absen">0</span>
                </button>
        </div>
    </div>
    <div class="table-responsive pb-3" style="">
        <table id="tb_daftar_hadir" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th class="text-center">Aksi</th>
                <th>Absensi</th>
                <th>No Peserta</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Tgl Lahir</th>
                <th>Prodi</th>
                <th>Status</th>
            </tr>
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
</section>
@endsection
