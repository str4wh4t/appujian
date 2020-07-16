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
    let list_online = [];
    let list_absensi = [];
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
            // console.log(list_online);
            $.each(list_online, function(index, item){
                $('#badge_koneksi_' + item).text('ONLINE').removeClass('bg-danger').addClass('bg-success');
            });

            // console.log(list_absensi);
            $.each(list_absensi, function(index, item){
                $('#badge_absensi_' + item).text('SUDAH ABSEN').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
            });
            $('#jml_sudah_absen').text(list_absensi.length);
        },
        "drawCallback": function( settings ) {
            $.each(list_online, function(index, item){
                $('#badge_koneksi_' + item).text('ONLINE').removeClass('bg-danger').addClass('bg-success');
            });

            $.each(list_absensi, function(index, item){
                $('#badge_absensi_' + item).text('SUDAH ABSEN').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
            });
            $('#jml_sudah_absen').text(list_absensi.length);
        },
        lengthMenu: [[5, 10, 50, -1], [5, 10, 50, "All"]],
        dom:
          "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
          {
              text: '<i class="fa fa-save"></i> Tampilkan Absensi Anda',
              className: 'btn btn-info btn-glow',
              action: function ( e, dt, node, config ) {
                  // reload_ajax();
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
          data: {'id' : '{{ $m_ujian->id_ujian }}'}
        },
        columns: [
            { "data": 'nim' },
            { "data": 'nama' },
            { "data": 'nik' },
            {
                "data": 'jenis_kelamin',
                "orderable": false,
                "searchable": false

            },
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
            },
            {
                "data": 'absen_by',
                "orderable": false,
                "searchable": false
            },
            {
                "data": 'absensi',
                "orderable": false,
                "searchable": false
            },
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
            // console.log('conn.onopen', e.data);
{{--            conn.send(JSON.stringify({'username':'{{ get_logged_user()->username }}'}));--}}
        };

        conn.onmessage = function(e) {
            console.log('conn.onmessage', e.data);
            let data = jQuery.parseJSON(e.data);
            if(Array.isArray(data)){
                $.each(data, function(index, item){
                    if(item.cmd == 'ONLINE'){
                        list_online.push(item.username);
                        $('#badge_koneksi_' + item.username).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                    }else if(item.cmd == 'LIST ABSENSI'){
                        const index = list_absensi.indexOf(item.nim);
                        if (index > -1) {
                          list_absensi.splice(index, 1);
                        }
                        list_absensi.push(item.nim);
                        $('#badge_absensi_' + item.nim).text('SUDAH ABSEN').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
                    }else if(item.cmd == 'BUKA TAB LAIN'){
                        list_online.push(item.username);
                        $('#badge_koneksi_' + item.username).text('BUKA TAB LAIN').removeClass('bg-danger').removeClass('bg-success').addClass('bg-warning');
                    }
                });

            }else {
                if (data.cmd == 'ONLINE') {
                    list_online.push(data.username);
                    $('#badge_koneksi_' + data.username).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                }
                else if(data.cmd == 'OFFLINE') {
                    const index = list_online.indexOf(data.username);
                    if (index > -1) {
                      list_online.splice(index, 1);
                    }
                    $('#badge_koneksi_' + data.username).text('OFFLINE').removeClass('bg-success').removeClass('bg-warning').addClass('bg-danger');
                }
                else if(data.cmd == 'ABSENSI'){
                    // console.log(data.nim);
                    const index = list_absensi.indexOf(data.nim);
                    if (index > -1) {
                      list_absensi.splice(index, 1);
                    }
                    list_absensi.push(data.nim);
                    $('#badge_absensi_' + data.nim).text('SUDAH ABSEN').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
                    $('#jml_sudah_absen').text(list_absensi.length);
                }else if (data.cmd == 'BUKA TAB LAIN') {
                    list_online.push(data.username);
                    $('#badge_koneksi_' + data.username).text('BUKA TAB LAIN').removeClass('bg-danger').removeClass('bg-success').addClass('bg-warning');
                }
            }
        };

        conn.onclose = function(e) {
            // console.log('conn.onclose', e.data);
        };
    }


    $(document).on('click','.btn_absensi',function(){
        let id = $(this).data('id');
        let nim = $(this).data('nim');
        conn.send(JSON.stringify({
            'id':id,
            'nim':nim,
            'user':'{{ get_logged_user()->id }}',
            'cmd':'ABSENSI'
        }));
        $('#badge_absensi_' + nim).text('SUDAH ABSEN').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
        const index = list_absensi.indexOf( nim);
        if (index > -1) {
          list_absensi.splice(index, 1);
        }
        list_absensi.push( nim);
        $('#jml_sudah_absen').text(list_absensi.length);
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
<div class="box">
    <div class="box-body">
        <div class="mb-4">
            <button type="button" onclick="reload_ajax()" class="btn btn-flat btn-outline-secondary"><i class="fa fa-refresh"></i> Reload</button>

            <div class="pull-right">
                <button type="button" class="btn btn-outline-danger btn-glow mr-1 mb-1">
                    JUMLAH MHS SUDAH ABSEN : <span id="jml_sudah_absen">0</span>
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive pb-3" style="">
        <table id="tb_daftar_hadir" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No Peserta</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Jns Kel</th>
                <th>Tgl Lahir</th>
                <th>Prodi</th>
                <th>Status</th>
                <th>Absensi</th>
                <th class="text-center">Aksi</th>
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
