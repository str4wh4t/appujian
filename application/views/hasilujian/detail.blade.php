@extends('template.main')

@push('page_level_css')
<!-- BEGIN PAGE LEVEL CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/rowReorder.dataTables.min.css') }}">--}}
{{--<link rel="stylesheet" type="text/css" href="{{ asset('assets/template/robust/app-assets/vendors/css/tables/extensions/responsive.dataTables.min.css') }}">--}}
<link rel="stylesheet" type="text/css" href="{{ asset('assets/yarn/node_modules/featherlight/release/featherlight.min.css') }}" />
<!-- END PAGE LEVEL CSS-->
@endpush

@push('page_vendor_level_js')
<!-- BEGIN PAGE VENDOR JS-->
<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/datatables.net-plugins/api/fnPagingInfo.js') }}"></script>
<script src="{{ asset('assets/yarn/node_modules/featherlight/release/featherlight.min.js') }}"></script>
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/jquery.dataTables.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>--}}
{{--<script src="{{ asset('assets/template/robust/app-assets/vendors/js/tables/datatable/dataTables.rowReorder.min.js') }}"></script>--}}
{{--    <script src="{{ asset('assets/bower_components/moment/min/moment.min.js') }}"></script>--}}
<!-- END PAGE VENDOR JS -->
@endpush

@push('page_level_js')
<!-- BEGIN PAGE LEVEL JS-->
<script type="text/javascript">
let id = '{{ segment(3) }}';

$(document).on('click','.btn_reset_hasil',function(){
    let id = $(this).data('id');

     Swal.fire({
        title: "Perhatian",
        text: "Ujian yang sudah reset tidak dapat dikembalikan.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#37bc9b",
        cancelButtonColor: "#f6bb42",
        confirmButtonText: "Reset"
    }).then(result => {
        if (result.value) {
            ajx_overlay(true);
            $.ajax({
                url: '{{ url('hasilujian/ajax/reset_hasil_ujian') }}',
                type: 'post',
                data: {'id' : id},
                dataType: 'json',
                success: function (data) {
                    if (!data.status) {
                        Swal.fire({
                            title: "Perhatian",
                            text: "Terjadi kesalahan.",
                            icon: "warning"
                        });
                    }else{
                        table.ajax.reload();
                        $.post('{{ url('hasilujian/ajax/get_stat_nilai') }}', {'id' : data.mujian_id}, function(data){
                            console.log(data.nilai_terendah);
                            $('#nilai_terendah').text(data.nilai_terendah);
                            $('#nilai_tertinggi').text(data.nilai_tertinggi);
                            $('#nilai_rata_rata').text(data.nilai_rata_rata);
                        });
                    }
                },
                error: function (data){
                    Swal.fire({
                        title: "Perhatian",
                        text: "Terjadi kesalahan.",
                        icon: "warning"
                    });
                },
                complete: function(){
                    ajx_overlay(false);
                }
            });
        }
    });
});

function init_page_level(){

    if(is_show_banner_ads){
        setTimeout(function () {
            $.featherlight('{{ asset('assets/imgs/tryout_udid_banner.png') }}');
            stop_ping = true;
        }, 2000);
    }

}

$(document).on('click','img.featherlight-image',function(){
    window.location = "https://sso.undip.id";
});

</script>
<script src="{{ asset('assets/dist/js/app/hasilujian/detail.js') }}"></script>
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
    <div class="col-md-12 mb-4">
        @if(is_admin())
            <a href="{{ site_url('hasilujian') }}" class="btn btn-sm btn-warning btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
        @else
            <a href="{{ site_url('ujian/list') }}" class="btn btn-sm btn-warning btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
        @endif
        <button type="button" onclick="reload_ajax()" class="btn btn-sm btn-outline-secondary btn-flat"><i class="fa fa-refresh"></i> Reload</button>
        @if(is_admin())
        <div class="pull-right">
            <a target="_blank" href="{{ site_url('hasilujian/cetak_detail/' . segment(3)) }}" class="btn btn-danger btn-flat btn-sm">
                <i class="fa fa-print"></i> Cetak Hasil Ujian
            </a>
            <a target="_blank" href="{{ site_url('hasilujian/cetak_detail_xls/' . segment(3)) }}" class="btn btn-success btn-flat btn-sm">
                <i class="fa fa-print"></i> Cetak Hasil Ujian .xls
            </a>
        </div>
        @endif
    </div>
    <div class="col-md-6">
        <table class="table w-100">
            <tr>
                <th>Nama Ujian</th>
                <td><?=$ujian->nama_ujian?></td>
            </tr>
            <tr>
                <th>Jml Soal/Waktu</th>
                <td><?=$ujian->jumlah_soal?>/<?=$ujian->waktu?> Menit</td>
            </tr>
            <tr>
                <th>Jadwal Mulai Ujian</th>
                <td><?=strftime('%A, %d %B %Y', strtotime($ujian->tgl_mulai))?></td>
            </tr>
            <tr>
                <th>Jadwal Selesai Ujian</th>
                <td>{!! empty($ujian->terlambat) ? '&infin;' : strftime('%A, %d %B %Y', strtotime($ujian->terlambat)) !!}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table w-100">
            <tr>
                <th>Topik</th>
                <td>@php($t = [])
                    @foreach($ujian->topik AS $topik)
                        @php($t[] = $topik->nama_topik)
                        @php($t = array_unique($t))
                    @endforeach
                    {{ implode(',' , $t) }}
                </td>
            </tr>
            <tr>
                <th>Nilai Terendah</th>
                <td id="nilai_terendah"><?=number_format($nilai->min_nilai,2,'.', '')?></td>
            </tr>
            <tr>
                <th>Nilai Tertinggi</th>
                <td id="nilai_tertinggi"><?=number_format($nilai->max_nilai,2,'.', '')?></td>
            </tr>
            <tr>
                <th>Rata-rata Nilai</th>
                <td id="nilai_rata_rata"><?=number_format($nilai->avg_nilai,2,'.', '')?></td>
            </tr>
        </table>
    </div>
</div>
<div class="row">

    @if(in_group('mahasiswa'))

{{--    <div class="col-md-12 mt-2" style="text-align: center">--}}
{{--        <a class="btn btn-info btn_cetak_hasil" target="_blank" href="{{ url('pub/cetak_sertifikat/' . $user->username . '/' . uuid_create_from_integer($ujian->id_ujian)) }}" title="Cetak hasil"><i class="fa fa-print"></i> Cetak Sertifikat</a>--}}
{{--    </div>--}}

        @if($ujian->tampilkan_jawaban)
            <?php 
            $h_ujian = $ujian->h_ujian()->where('mahasiswa_id', $mhs->id_mahasiswa)->first();
            ?>
            @if(!empty($h_ujian))
            <div class="col-md-12 mt-2" style="text-align: center">
                <a class="btn btn-danger btn-lg" href="{{ url('hasilujian/history/' . uuid_create_from_integer($h_ujian->mahasiswa_ujian_id)) }}" title="Lihat Jawaban">
                    <i class="fa fa-list-alt"></i> Jawaban Ujian
                </a>
            </div>
            @endif
        @endif
        
        
    @endif

    <div class="col-md-12 mt-2">
        <div class="table-responsive pb-2">
            <table id="detail_hasil" class="table table-striped table-bordered table-hover w-100">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>No Peserta</th>
                    <th>Nama</th>
                    <th>Nilai Per Topik</th>
{{--                    <th>Jml Salah</th>--}}
                    <th>Bobot</th>
                    <th>Nilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            </table>
        </div>
    </div>
</div>
<!---- --->
				</div>
            </div>
        </div>
    </div>
</section>
@endsection
