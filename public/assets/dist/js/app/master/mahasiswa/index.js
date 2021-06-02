var table;

$(document).ready(function() {
  ajaxcsrf();

  table = $("#mahasiswa").DataTable({
    initComplete: function() {
      var api = this.api();
      // $("#mahasiswa_filter input")
      //   .off(".DT")
      //   .on("keyup.DT", function(e) {
      //     api.search(this.value).draw();
      //   });
      $("#mahasiswa_filter input")
        .off(".DT")
        .on("keypress.DT", function(e) {
          if(e.which == 13) {
            api.search(this.value).draw();
            return false;
          }
        });
    },
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    dom:
      "<'row'<'col-lg-3'l><'col-lg-6 text-center'B><'col-lg-3'f>>" +
      "<'row'<'col-lg-12'tr>>" +
      "<'row'<'col-lg-5'i><'col-lg-7'p>>",
    buttons: [
      {
        extend: "copy",
        exportOptions: { columns: [0, 1, 2, 3], format: {
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
        exportOptions: { columns: [0, 1, 2, 3], format: {
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
        exportOptions: { columns: [0, 1, 2, 3], format: {
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
        exportOptions: { columns: [0, 1, 2, 3], format: {
              body: function ( data, columnIdx, rowIdx ) {
                if(rowIdx == 0)
                  return (columnIdx + 1);
                else
                  return data;
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
      url: base_url + "mahasiswa/ajax/data",
      type: "POST",
      data: function (d) {
        d.tahun_dipilih = tahun_dipilih;
      },
    },
    columns: [
      {
        data: "id_mahasiswa",
        orderable: false,
        searchable: false
      },
      { data: "nim" },
      { data: "nama" },
      // { data: "email" },
      // { data: "nama_matkul" },
      { data: "prodi" },
      // { data: "nama_kelas" },
      // { data: "nama_jurusan" }
    ],
    columnDefs: [
      // {
      //   searchable: false,
      //   orderable: false,
      //   targets: 3,
      //   data: "nama_matkul",
      //   render: function(data, type, row, meta) {
      //     let str_return = '';
      //     if(data != null) {
      //       let data_array = data.split('---');
      //       data_array.forEach(function (item, index) {
      //         str_return += '<span class="badge bg-info">' + item + '</span> ';
      //       });
      //     }
      //     return str_return;
      //   }
      // },
      {
        searchable: false,
        orderable: false,
        targets: 4,
        data: "ada",
        render: function(data, type, row, meta) {
          let btn;
          if (data > 0) {
            btn = "";
          } else {
            btn = `<button data-id="${row.id_mahasiswa}" type="button" class="btn btn-sm btn-primary btn-aktif">
								<i class="fa fa-user-plus"></i>
							</button>`;
          }
          return `<div class="text-center">
                  <div class="btn-group btn-group-sm" role="group" aria-label="">
									<a class="btn btn-sm btn-warning" href="${base_url}mahasiswa/edit/${row.id_mahasiswa}">
										<i class="fa fa-pencil"></i>
									</a>
									${btn}
								</div>
                </div>`;
        }
      },
      {
        searchable: false,
        targets: 5,
        data: "id_mahasiswa",
        render: function(data, type, row, meta) {
          return `<div class="text-center">
									<input name="checked[]" class="check" value="${data}" type="checkbox">
								</div>`;
        }
      }
    ],
    order: [[4, "asc"],[1, "asc"]],
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
    // scrollX:        true,
    // fixedColumns:   {
    //     leftColumns: 2,
    // }
  });

  // table
  //   .buttons()
  //   .container()
  //   .appendTo("#mahasiswa_wrapper .col-md-6:eq(0)");

  $(".select_all").on("click", function() {
    if (this.checked) {
      $(".check").each(function() {
        this.checked = true;
        $(".select_all").prop("checked", true);
      });
    } else {
      $(".check").each(function() {
        this.checked = false;
        $(".select_all").prop("checked", false);
      });
    }
  });

  $("#mahasiswa tbody").on("click", "tr .check", function() {
    var check = $("#mahasiswa tbody tr .check").length;
    var checked = $("#mahasiswa tbody tr .check:checked").length;
    if (check === checked) {
      $(".select_all").prop("checked", true);
    } else {
      $(".select_all").prop("checked", false);
    }
  });

  $("#bulk").on("submit", function(e) {
    if ($(this).attr("action") == base_url + "mahasiswa/ajax/delete") {
      e.preventDefault();
      e.stopImmediatePropagation();

      $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        type: "POST",
        success: function (respon) {
          if (respon.status) {
            Swal.fire({
              title: "Berhasil",
              text: respon.total + " data berhasil dihapus",
              icon: "success"
            });
          } else {
            Swal.fire({
              title: "Gagal",
              text: "Tidak ada data yang dipilih",
              icon: "error"
            });
          }
          reload_ajax();
        },
        error: function () {
          Swal.fire({
            title: "Gagal",
            text: "Ada data yang sedang digunakan",
            icon: "error"
          });
        }
      });
    }
  });

  $("#mahasiswa").on("click", ".btn-aktif", function() {
    let id = $(this).data("id");

    $.ajax({
      url: base_url + "mahasiswa/ajax/create_user",
      data: "id=" + id,
      type: "GET",
      success: function(response) {
        if (response.msg) {
          var title = response.status ? "Berhasil" : "Gagal";
          var type = response.status ? "success" : "error";
          Swal.fire({
            title: title,
            text: response.msg,
            icon: type
          });
        }
        reload_ajax();
      }
    });
  });
});

function bulk_delete() {
  if ($("#mahasiswa tbody tr .check:checked").length == 0) {
    Swal.fire({
      title: "Gagal",
      text: "Tidak ada data yang dipilih",
      icon: "error"
    });
  } else {
    $("#bulk").attr("action", base_url + "mahasiswa/ajax/delete");
    Swal.fire({
      title: "Anda yakin?",
      text: "Data akan dihapus!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Hapus!"
    }).then(result => {
      if (result.value) {
        $("#bulk").submit();
      }
    });
  }
}
