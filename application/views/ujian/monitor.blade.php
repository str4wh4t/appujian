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
.enjoyhint{
    z-index: 9996 !important;
}
.swal2-container{
    z-index: 9997 !important;
}
.enjoyhint_close_btn{
    display: none;
}
.modal {
    z-index: 9996;
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
    let list_mhs_online_ips = {};
    let list_absensi = [];
    let list_absensi_by_self = [];
    let jml_daftar_hadir = {{ $jml_daftar_hadir }};
    let jml_daftar_hadir_by_pengawas = {{ $jml_daftar_hadir_by_pengawas }};
    let as = null ;
    let user_id = null ;
    function init_page_level() {
        ajaxcsrf();

        $('#jml_mhs_absen').text(jml_daftar_hadir);
        $('#jml_mhs_absen_by_self').text(jml_daftar_hadir_by_pengawas);

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
            init_socket();
        },
        "drawCallback": function( settings ) {
            $.each(list_mhs_online, function(index, item){
                $('#badge_koneksi_' + item).text('ONLINE').removeClass('bg-danger').addClass('bg-success');
                $('#badge_ip_' + item).text(list_mhs_online_ips[item]).show();
            });
            // $.each(list_absensi, function(index, item){
            //     $('#badge_absensi_' + item).text('SUDAH').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
            // });
        },
        lengthMenu: [[10, 50, -1], [10, 50, "All"]],
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
                  load_absen_pengawas();
              }
          },
          @endif
            {
                  text: '<i class="fa fa-th"></i> Tampilkan Absensi Semua',
                  className: 'btn btn-success btn-glow ml-1',
                  action: function ( e, dt, node, config ) {
                      load_absen_semua();
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
                "searchable": false
            },
            {
                "data": 'absen_by',
                "orderable": false,
                "searchable": false
            },
            {
                "data": 'koneksi',
                "orderable": false,
                "searchable": false
            },
            {
                "data": 'status',
                "orderable": false,
                "searchable": false
            },
            {
                "data": 'aksi',
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
            }
        ],
        order: [[5, "asc"], [9, "asc"]],
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
            console.log('wesocket status opened');
            conn.send(JSON.stringify({
                'user_id':'{{ get_logged_user()->id }}',
                'm_ujian_id':'{{ $m_ujian->id_ujian }}',
                'as':'{{ get_selected_role()->name }}',
                'cmd':'OPEN',
                'app_id': '{{ APP_ID }}',
            }));
        };

        conn.onmessage = function(e) {
            // console.log('conn.onmessage', e.data);
            let data = jQuery.parseJSON(e.data);

            if(data.app_id == '{{ APP_ID }}') {

                if (data.cmd == 'OPEN') {

                    // $.each(data.absensi, function(index, nim){
                    //     push_absensi(nim);
                    //     $('#badge_absensi_' + nim).text('SUDAH').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
                    // });
                    // $('#jml_mhs_absen').text(list_absensi.length);

                    {{--if (data.user_id == '{{ get_logged_user()->id }}') {--}}
                    {{--    $.each(data.absensi_by_self, function (index, nim) {--}}
                    {{--        push_absensi_by_self(nim);--}}
                    {{--    });--}}
                    {{--    $('#jml_mhs_absen_by_self').text(list_absensi_by_self.length);--}}
                    {{--}--}}

                    $.each(data.mhs_online, function (index, code) {
                        let nim = index;
                        push_mhs_online(nim);
                        $('#badge_koneksi_' + nim).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                        push_mhs_online_ips(nim, data.mhs_online_ips[nim]);
                        $('#badge_ip_' + nim).text(data.mhs_online_ips[nim]).show();
                    });
                    $('#jml_mhs_online').text(list_mhs_online.length);
                    // console.log('list_mhs_online', list_mhs_online);
                } else if (data.cmd == 'MHS_ONLINE') {
                    push_mhs_online(data.nim);
                    $('#badge_koneksi_' + data.nim).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                    $('#badge_ip_' + data.nim).text(data.ip).show();
                    $('#jml_mhs_online').text(list_mhs_online.length);
                } else if (data.cmd == 'MHS_LOST_FOCUS') {
                    $('#badge_koneksi_' + data.nim).text('BUKA PAGE LAIN').removeClass('bg-danger').removeClass('bg-success').addClass('bg-warning');
                } else if (data.cmd == 'MHS_GET_FOCUS') {
                    $('#badge_koneksi_' + data.nim).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                } else if (data.cmd == 'MHS_START_UJIAN') {
                    $('#badge_status_' + data.nim).text('SEDANG UJIAN').removeClass('bg-secondary').removeClass('bg-success').addClass('bg-info');
                } else if (data.cmd == 'MHS_STOP_UJIAN') {
                    $('#badge_status_' + data.nim).text('SUDAH UJIAN').removeClass('bg-secondary').removeClass('bg-info').addClass('bg-success');
                } else if (data.cmd == 'DO_ABSENSI') {
                    if (data.ok) {

                        {{--push_absensi(data.nim);--}}
                        {{--$('#jml_mhs_absen').text(list_absensi.length);--}}
                        {{--if (data.user_id == '{{ get_logged_user()->id }}') {--}}
                        {{--    push_absensi_by_self(data.nim);--}}
                        {{--    $('#jml_mhs_absen_by_self').text(list_absensi_by_self.length);--}}
                        {{--}--}}

                        $('#jml_mhs_absen').text(++jml_daftar_hadir);
                        if (data.user_id == '{{ get_logged_user()->id }}') {
                            $('#jml_mhs_absen_by_self').text(++jml_daftar_hadir_by_pengawas);
                        }

                        $('#badge_absensi_' + data.nim).text('SUDAH').removeClass('danger').removeClass('border-danger').addClass('border-success').addClass('success');
                    }
                } else if (data.cmd == 'DO_ABSENSI_BATAL') {
                    if (data.ok) {

                        {{--pop_absensi(data.nim);--}}
                        {{--$('#jml_mhs_absen').text(list_absensi.length);--}}
                        {{--if (data.user_id == '{{ get_logged_user()->id }}') {--}}
                        {{--    pop_absensi_by_self(data.nim);--}}
                        {{--    $('#jml_mhs_absen_by_self').text(list_absensi_by_self.length);--}}
                        {{--}--}}

                        $('#jml_mhs_absen').text(--jml_daftar_hadir);
                        if (data.user_id == '{{ get_logged_user()->id }}') {
                            $('#jml_mhs_absen_by_self').text(--jml_daftar_hadir_by_pengawas);
                        }

                        $('#badge_absensi_' + data.nim).text('BELUM').removeClass('success').removeClass('border-success').addClass('border-danger').addClass('danger');
                    } else {
                        if (data.user_id == '{{ get_logged_user()->id }}') {
                            Swal({
                                title: "Perhatian",
                                text: "Bukan absensi anda",
                                type: "error",
                                confirmButtonText: "Tutup"
                            });
                        }
                    }
                } else if (data.cmd == 'PING') {
                    $('#badge_koneksi_' + data.nim).text('ONLINE').removeClass('bg-danger').removeClass('bg-warning').addClass('bg-success');
                    $('#badge_ip_' + data.nim).text(data.ip).show();
                    $('#badge_latency_' + data.nim).text(data.latency + 'ms').show();
                }
            }else if (data.cmd == 'MHS_OFFLINE') {
                pop_mhs_online(data.nim);
                $('#badge_koneksi_' + data.nim).text('OFFLINE').removeClass('bg-success').removeClass('bg-warning').addClass('bg-danger');
                $('#badge_ip_' + data.nim).hide();
                $('#badge_latency_' + data.nim).hide();
                $('#jml_mhs_online').text(list_mhs_online.length);
            }
        };

        conn.onclose = function(e) {
            // console.log('conn.onclose', e.data);
        };
    }


    $(document).on('click','.btn_absensi',function(){
        let mahasiswa_ujian_id = $(this).data('id');
        let nim = $(this).data('nim');

        $.post('{{ url('ujian/ajax/absen_pengawas') }}', {'mahasiswa_ujian_id' : mahasiswa_ujian_id, 'nim' : nim}, function (res){
            if(res.ok) {
                conn.send(JSON.stringify({
                    'mahasiswa_ujian_id': mahasiswa_ujian_id,
                    'user_id': '{{ get_logged_user()->id }}',
                    'as': '{{ get_selected_role()->name }}',
                    'nim': nim,
                    'cmd': 'DO_ABSENSI',
                    'app_id': '{{ APP_ID }}',
                }));
            }
        });

    });

    $(document).on('click','.btn_absensi_batal',function(){
        Swal({
            title: "Anda yakin",
            text: "Absensi peserta tsb akan dihapus",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Hapus"
        }).then(result => {
            if (result.value) {
                let mahasiswa_ujian_id = $(this).data('id');
                let nim = $(this).data('nim');
                $.post('{{ url('ujian/ajax/absen_pengawas') }}', {'mahasiswa_ujian_id' : mahasiswa_ujian_id, 'nim' : nim, 'aksi' : 'batal'}, function (res){
                    if(res.ok) {
                        conn.send(JSON.stringify({
                            'mahasiswa_ujian_id': mahasiswa_ujian_id,
                            'user_id': '{{ get_logged_user()->id }}',
                            'as': '{{ get_selected_role()->name }}',
                            'nim': nim,
                            'cmd': 'DO_ABSENSI_BATAL',
                            'app_id': '{{ APP_ID }}',
                        }));
                    }
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
        ip = ip.toString();
        delete list_mhs_online_ips[nim];
    }

    function load_absen_pengawas(){
        as = 'pengawas';
        user_id = '{{ get_logged_user()->id }}';

        // table.ajax.url('{!! url('ujian/ajax/data_absen_pengawas/?id=' . $m_ujian->id_ujian . '&user_id=' . get_logged_user()->id) !!}').load();
        table.ajax.reload();
    }

    function load_absen_semua(){
        as = null;
        user_id = null;

        // table.ajax.url('{!! url('ujian/ajax/data_daftar_hadir') !!}') .load()
        table.ajax.reload();
    }

    $(document).on('click','.btn_kick',function(){
        Swal({
            title: "Anda yakin",
            text: "Sesi ujian peserta tsb akan diakhiri",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Akhiri"
        }).then(result => {
            if (result.value) {
                let mahasiswa_ujian_id = $(this).data('id');
                let nim = $(this).data('nim');
                conn.send(JSON.stringify({
                    'mahasiswa_ujian_id': mahasiswa_ujian_id,
                    'user_id':'{{ get_logged_user()->id }}',
                    'as':'{{ get_selected_role()->name }}',
                    'nim': nim,
                    'cmd':'DO_KICK',
                    'app_id': '{{ APP_ID }}',
                }));
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
            Swal({
                title: "Info Absen",
                text: txt,
                type: type,
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
            console.log('good');
            $('#img_profile').attr('src',foto_url);
            ajx_overlay(false);
        }, function(){
            console.log('bad');
            $('#img_profile').attr('src','{{ asset('assets/imgs/no_profile.jpg') }}');
            ajx_overlay(false);
        }
    );
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

                <button type="button" class="btn btn-outline-success btn-glow">
                    JUMLAH MHS ONLINE : <span id="jml_mhs_online">0</span>
                </button>

                @if(in_group('pengawas'))
                <button type="button" class="btn btn-outline-primary btn-glow">
                    JUMLAH MHS ABSEN OLEH ANDA : <span id="jml_mhs_absen_by_self">0</span>
                </button>
                @endif

                <button type="button" class="btn btn-outline-danger btn-glow">
                    JUMLAH MHS SUDAH ABSEN TOTAL : <span id="jml_mhs_absen">0</span>
                </button>
        </div>
    </div>
    <div class="table-responsive pb-3" style="">
        <table id="tb_daftar_hadir" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th class="text-center">Absensi</th>
                <th>Absensi</th>
                <th>Online</th>
                <th>Status</th>
                <th>Aksi</th>
                <th>No Peserta</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Tgl Lahir</th>
                <th>Prodi</th>
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
</div>
</section>

<!-- Modal -->
<div class="modal text-left"
     id="modal_foto_peserta"
     tabindex="-1"
     role="dialog"
     aria-labelledby="myModalLabel11"
     aria-hidden="true">
    <div class="modal-dialog"
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
@endsection
