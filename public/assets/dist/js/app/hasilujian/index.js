var table;

$(document).ready(function () {
    ajaxcsrf();

    table = $("#hasil").DataTable({
        initComplete: function () {
            var api = this.api();
            $("#hasil_filter input")
                .off(".DT")
                // .on("keyup.DT", function(e) {
                //   api.search(this.value).draw();
                // });
                .on("keypress.DT", function (e) {
                    if (e.which == 13) {
                        api.search(this.value).draw();
                        return false;
                    }
                });
        },
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],
        dom:
            "<'row'<'col-lg-3'l><'col-lg-6 text-center'B><'col-lg-3'f>>" +
            "<'row'<'col-lg-12'tr>>" +
            "<'row'<'col-lg-5'i><'col-lg-7'p>>",
        buttons: [
            {
                text: '<i class="fa fa-refresh"></i> Reload',
                className: "btn btn-secondary",
                action: function (e, dt, node, config) {
                    reload_ajax();
                },
            },
            // {
            //     text: "Hapus Semua",
            //     action: function (e, dt, node, config) {
            //         deleteHasilUjian();
            //     },
            //     className: "btn-danger",
            // },
        ],
        oLanguage: {
            sProcessing: "loading...",
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + "hasilujian/ajax/data",
            type: "POST",
        },
        columns: [
            {
                data: "id_ujian",
                orderable: false,
                searchable: false,
            },
            { data: "nama_ujian" },
            // { data: "nama_matkul" },
            { data: "jumlah_soal" },
            { data: "waktu" },
            { data: "tgl_mulai" },
            {
                orderable: false,
                searchable: false,
            },
        ],
        columnDefs: [
            {
                targets: 5,
                data: "id_ujian",
                render: function (data, type, row, meta) {
                    return `
                    <div class="btn-group text-center">
                        <a class="btn btn-sm btn-info" href="${base_url}hasilujian/detail/${data}" >
                            Lihat
                        </a>
                        <button class="btn btn-sm btn-danger btn_hapus_hasil_ujian" data-id="${data}">
                            Hapus
                        </button>
                    </div>
                    `;
                },
            },
        ],
        order: [[4, "desc"]],
        rowId: function (a) {
            return a;
        },
        rowCallback: function (row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $("td:eq(0)", row).html(index);
        },
    });
});

$(document).on("click", ".btn_hapus_hasil_ujian", function (e) {
    let id = $(this).data("id");
    deleteHasilUjian(id);
});

function deleteHasilUjian(id = 0) {
    let textConfirm = "Yakin akan menghapus ujian<br>- Masukan token -";

    if (id == 0) {
        textConfirm = "Semua data akan dihapus- Masukan token -";
    }
    Swal.fire({
        title: "Perhatian",
        html: textConfirm,
        input: "password",
        inputAttributes: {
            autocapitalize: "off",
        },
        showCancelButton: true,
        confirmButtonText: "Hapus",
        showLoaderOnConfirm: true,
        preConfirm: (token) => {
            let csrfname = Object.keys(csrf)[0];
            let formData = new URLSearchParams();
            formData.append("token", token);
            formData.append("id", id);
            formData.append(Object.keys(csrf)[0], csrf[csrfname]);

            return fetch(`${base_url}hasilujian/ajax/hapus`, {
                method: "POST", // or 'PUT'
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(response.statusText);
                    }
                    return response.json();
                })
                .catch((error) => {
                    console.log(error);
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
        },
        allowOutsideClick: () => !Swal.isLoading(),
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.status) {
                reload_ajax();
                Swal.fire({
                    icon: "success",
                    title: "Data berhasil dihapus",
                    showConfirmButton: false,
                });
            }
        }
    });
}
