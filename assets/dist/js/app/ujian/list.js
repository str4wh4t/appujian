var table;

$(document).ready(function () {

    ajaxcsrf();

    table = $("#ujian").DataTable({
        initComplete: function () {
            var api = this.api();
            $('#ujian_filter input')
                .off('.DT')
                .on('keyup.DT', function (e) {
                    api.search(this.value).draw();
                });
        },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom:
          "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
          {
            text: '<i class="fa fa-refresh"></i> Reload',
            className: 'btn btn-secondary',
            action: function ( e, dt, node, config ) {
                reload_ajax();
            }
          },
        ],
        oLanguage: {
            sProcessing: "loading..."
        },
        processing: true,
        serverSide: true,
        ajax: {
            "url": base_url+"ujian/ajax/list_json",
            "type": "POST",
        },
        columns: [
            // {
            //     "data": "id_ujian",
            //     "orderable": false,
            //     "searchable": false
            // },
            { "data": 'nama_ujian' },
            { "data": 'nama_matkul' },
            { "data": 'status_ujian' },
            { "data": 'jumlah_soal' },
            { "data": 'tgl_mulai' },
            { "data": 'terlambat' },
            { "data": 'waktu' },
            {
                "searchable": false,
                "orderable": false
            }
        ],
        columnDefs: [
            {
                "targets": 7,
                "data": "id_ujian",
                "render": function (data, type, row, meta) {
                    let btn;
                    // if (data.ada > 0) {
                    //     btn = `
					// 			<a class="btn btn-sm btn-success" href="${base_url}hasilujian/cetak/${data.id_ujian}" target="_blank">
					// 				<i class="fa fa-print"></i> Cetak Hasil
					// 			</a>`;
                    // } else {
                    //     btn = `<a class="btn btn-sm btn-primary" href="${base_url}ujian/token/${data.id_ujian}">
					// 			<i class="fa fa-pencil"></i> Ikut Ujian
					// 		</a>`;
                    // }

                    if(row.ujian_selesai == 'Y'){
                        if(row.tampilkan_hasil == '1'){
                                btn = `<a class="btn btn-sm btn-info" href="${base_url}hasilujian/detail/${data}">
                                        <i class="fa fa-check-square"></i> Lihat hasil
                                    </a>`;
                        }else{
                            btn = `<button class="btn btn-sm btn-success" type="button">
                                    <i class="fa fa-exclamation-circle"></i> Sudah Ujian
                                </button>`;
                        }
                    }else {
                        btn = `<a class="btn btn-sm btn-primary" href="${base_url}ujian/token/${data}">
								<i class="fa fa-pencil"></i> Ikut Ujian
							</a>`;
                    }

                    return `<div class="text-center">
									${btn}
								</div>`;

                }
            },
            {
                "targets": 2,
                "render": function (data, type, row, meta) {
                    if(data == 'active'){
                        return `<span class="badge badge-success">
                                        ${data}
                                    </span>`;
                    }else if(data == 'expired'){
                        return `<span class="badge badge-warning">
                                        ${data}
                                    </span>`;
                    }else if(data == 'upcoming'){
                        return `<span class="badge badge-info">
                                        ${data}
                                    </span>`;
                    }else{
                        return `<span class="badge badge-danger">
                                        ${data}
                                    </span>`;
                    }

                }
            },
        ],
        order: [
            [1, 'asc']
        ],
        rowId: function (a) {
            return a;
        },
        rowCallback: function (row, data, iDisplayIndex) {
            // var info = this.fnPagingInfo();
            // var page = info.iPage;
            // var length = info.iLength;
            // var index = page * length + (iDisplayIndex + 1);
            // $('td:eq(0)', row).html(index);
        }
    });
});
