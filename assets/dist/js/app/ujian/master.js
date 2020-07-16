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
            extend: "copy",
            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9, 10], format: {
                  body: function ( data, columnIdx, rowIdx ) {
                    if(rowIdx == 0)
                      return (columnIdx + 1);
                    else
                      return data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
                  }
              } }
          },
          {
            extend: "print",
            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9, 10], format: {
                  body: function ( data, columnIdx, rowIdx ) {
                    if(rowIdx == 0)
                      return (columnIdx + 1);
                    else
                      return data;
                  }
              } }
          },
          {
            extend: "excel",
            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9, 10], format: {
                  body: function ( data, columnIdx, rowIdx ) {
                    if(rowIdx == 0)
                      return (columnIdx + 1);
                    else
                      return data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
                  }
              } }
          },
          {
            extend: "pdf",
            exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9, 10], format: {
                  body: function ( data, columnIdx, rowIdx ) {
                    if(rowIdx == 0)
                      return (columnIdx + 1);
                    else
                      return data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
                  }
              } }
          }
        ],
        oLanguage: {
            sProcessing: "loading..."
        },
        processing: true,
        serverSide: true,
        ajax: {
            "url": base_url+"ujian/ajax/data",
            "type": "POST",
        },
        columns: [
            {
                "data": "id_ujian",
                "orderable": false,
                "searchable": false
            },
            { "data": 'nama_ujian' },
            { "data": 'nama_matkul' },
            { "data": 'status_ujian' },
            { "data": 'jumlah_soal' },
            { "data": 'tgl_mulai' },
            { "data": 'terlambat' },
            { "data": 'waktu' },
            { "data": 'jenis' },
            { "data": 'oleh' },
            {
                "data": 'token',
                "orderable": false
            },
        ],
        columnDefs: [
            {
                "targets": 0,
                "data": "id_ujian",
                "render": function (data, type, row, meta) {
                    return `<div class="text-center">
									<input name="checked[]" class="check" value="${data}" type="checkbox">
								</div>`;
                }
            },
            {
                "targets": 1,
                "data": "nama_ujian",
            },
            {
                "targets": 3,
                "render": function (data, type, row, meta) {
                    if(data == 'active'){
                        return `<span class="badge badge-success">
                                        ${data}
                                    </span>`;
                    }else{
                        if(data == 'expired'){
                            return `<span class="badge badge-warning">
                                            ${data}
                                        </span>`;
                        }else{
                            return `<span class="badge badge-danger">
                                            ${data}
                                        </span>`;
                        }
                    }
                }
            },
            {
                "targets": 10,
                "data": "token",
                "render": function (data, type, row, meta) {
                    if(row.pakai_token == '0'){
                        return '&nbsp';
                    }else {
                        return `<div class="text-center">
								<b><span style="padding-bottom: 5px; border-bottom: 3px dashed #f00;">${data}</span></b>
								</div>`;
                    }
                }
            },
            {
                "targets": 11,
                "data": "aksi",
            },
        ],
        order: [
            [2, 'desc']
        ],
        rowId: function (a) {
            return a;
        },
        rowCallback: function (row, data, iDisplayIndex) {
            // var info = this.fnPagingInfo();
            // var page = info.iPage;
            // var length = info.iLength;
            // var index = page * length + (iDisplayIndex + 1);
            // $('td:eq(1)', row).html(index);
        },
        scrollX:        true,
        fixedColumns:   {
            leftColumns: 2,
        }
    });

    $(document).on('change', '.select_all', function () {
        $(this).is(':checked') ? $('.DTFC_LeftBodyLiner table .check').prop('checked', true) : $('.DTFC_LeftBodyLiner table .check').prop('checked', false);
        $('.DTFC_LeftBodyLiner table .check').trigger('change');
    });

    $(document).on('change','.DTFC_LeftBodyLiner table .check',function () {
        $(this).is(':checked') ? null : $('.select_all').prop('checked', false);
    });

    // $('.select_all').on('click', function () {
    //     if (this.checked) {
    //         $('.check').each(function () {
    //             this.checked = true;
    //             $('.select_all').prop('checked', true);
    //         });
    //     } else {
    //         $('.check').each(function () {
    //             this.checked = false;
    //             $('.select_all').prop('checked', false);
    //         });
    //     }
    // });

    // $('#ujian tbody').on('click', 'tr .check', function () {
    //     var check = $('#ujian tbody tr .check').length;
    //     var checked = $('#ujian tbody tr .check:checked').length;
    //     if (check === checked) {
    //         $('.select_all').prop('checked', true);
    //     } else {
    //         $('.select_all').prop('checked', false);
    //     }
    // });

    $('#ujian').on('click', '.btn-token', function () {
        let id = $(this).data('id');
        $(this).attr('disabled', 'disabled').children().addClass('fa-spin');
        $.ajax({
            url: base_url+'ujian/ajax/refresh_token/',
            type: 'post',
            data: {'id' : id},
            dataType: 'json',
            success: function (data) {
                if (!data.status) {
                    Swal({
                        "title": "Perhatian",
                        "text": "Anda bukan pembuat ujian.",
                        "type": "warning"
                    });
                }
                reload_ajax();
                $(this).children().removeClass('fa-spin');
                $(this).removeAttr('disabled');
            }
        });
    });

    $('#bulk').on('submit', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: 'POST',
            success: function (respon) {
                if (respon.status) {
                    Swal({
                        "title": "Berhasil",
                        "text": respon.total + " data berhasil dihapus",
                        "type": "success"
                    });
                } else {
                    Swal({
                        "title": "Perhatian",
                        "text": "Anda bukan pembuat ujian.",
                        "type": "warning"
                    });
                }
                reload_ajax();
            },
            error: function () {
                Swal({
                    "title": "Gagal",
                    "text": "Ada data yang sedang digunakan",
                    "type": "error"
                });
            }
        });
    });

    // table.ajax.url(base_url+'ujian/json/'+id_dosen).load();
});

function bulk_delete() {
    // console.log("$('#bulk').serialize()", $('#bulk').serialize());
    // if ($('#ujian tbody tr .check:checked').length == 0) {
    if ($('.DTFC_LeftBodyLiner table .check:checked').length == 0) {
        Swal({
            title: "Gagal",
            text: 'Tidak ada data yang dipilih',
            type: 'error'
        });
    } else {
        Swal({
            title: 'Anda yakin?',
            text: "Data akan dihapus!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus!'
        }).then((result) => {
            if (result.value) {
                $('#bulk').submit();
            }
        });
    }
}
