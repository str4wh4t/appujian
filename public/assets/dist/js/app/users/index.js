var table;

$(document).ready(function() {
    ajaxcsrf();

    table = $("#users").DataTable({
        initComplete: function() {
            var api = this.api();
            $("#users_filter input")
                .off(".DT")
                .on("keyup.DT", function(e) {
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
                exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            },
            {
                extend: "print",
                exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            },
            {
                extend: "excel",
                exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            },
            {
                extend: "pdf",
                exportOptions: { columns: [1, 2, 3, 4, 5, 6] }
            }
        ],
        oLanguage: {
            sProcessing: "loading..."
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "users/data/" + user_id,
            type: "POST"
        },
        columns: [
            {
                data: "id",
                orderable: false,
                searchable: false
            },
            { data: "full_name" },
            { data: "username" },
            { data: "email" },
            { data: "level" },
            { data: "created_on" }
        ],
        columnDefs: [
            {
                targets: 4,
                data: "level",
                render: function(data, type, row, meta) {
                    return `<span class="badge badge-info">${data}</span>`;
                }
            },
            {
                targets: 6,
                orderable: false,
                searchable: false,
                title: "Status",
                data: "active",
                render: function(data, type, row, meta) {
                    if (data === "1") {
                        return `<span class="badge bg-green">Active</span>`;
                    } else {
                        return `<span class="badge bg-red">Not Active</span>`;
                    }
                }
            },
            {
                targets: 7,
                data: "id",
                render: function(data, type, row, meta) {
                    if (data === user_id) {
                        return `<a class="btn btn-sm btn-primary" href="${base_url}users/edit/${data}">
                                    <i class="fa fa-cog fa-spin"></i>
                                </a>`;
                    } else {
                        return `<div class="btn-group btn-group-sm" role="group" aria-label="">
                                    <a class="btn btn-sm btn-warning" href="${base_url}users/edit/${data}">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="hapus(${data})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>`;
                    }
                }
            }
        ],
        order: [[4, "asc"], [1, "asc"]],
        rowId: function(a) {
            return a;
        },
        rowCallback: function(row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $("td:eq(0)", row).html(index);
        },
        scrollX:        true,
        fixedColumns:   {
            leftColumns: 3,
        }
    });

    // table
    //     .buttons()
    //     .container()
    //     .appendTo("#users_wrapper .col-md-6:eq(0)");

    $("#show_me").on("change", function() {
        let src = base_url + "users/data";
        let url = $(this).prop("checked") === true ? src : src + "/" + user_id;
        table.ajax.url(url).load();
    });


    $('.datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        // Your Icons
        // as Bootstrap 4 is not using Glyphicons anymore
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-check',
            clear: 'fa fa-trash',
            close: 'fa fa-times'
        }
    });
    
});

function hapus(id) {
    Swal.fire({
        title: "Anda yakin?",
        text: "Data akan dihapus.",
        icon:"question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Hapus!"
    }).then(result => {
        if (result.value) {
            $.getJSON(base_url + "users/delete/" + id, function(data) {
                Swal.fire({
                    title: data.status ? "Berhasil" : "Gagal",
                    text: data.status
                        ? "User berhasil dihapus"
                        : "User gagal dihapus",
                    icon: data.status ? "success" : "error"
                });
                reload_ajax();
            });
        }
    });
}
