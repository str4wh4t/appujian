var table;

$(document).ready(function() {
  ajaxcsrf();

  table = $("#dosen").DataTable({
    initComplete: function() {
      var api = this.api();
      $("#dosen_filter input")
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
      "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    buttons: [
      {
        extend: "copy",
        exportOptions: { columns: [0, 1, 2, 3, 4], format: {
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
        exportOptions: { columns: [0, 1, 2, 3, 4], format: {
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
        exportOptions: { columns: [0, 1, 2, 3, 4], format: {
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
        exportOptions: { columns: [0, 1, 2, 3, 4], format: {
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
      url: base_url + "dosen/ajax/data",
      type: "POST"
    },
    columns: [
      {
        data: "id_dosen",
        orderable: false,
        searchable: false
      },
      { data: "nip" },
      { data: "nama_dosen" },
      { data: "email" },
      { data: "nama_matkul" }
    ],
    columnDefs: [
      {
        searchable: false,
        targets: 5,
        data: {
          id_dosen: "id_dosen",
          ada: "ada"
        },
        render: function(data, type, row, meta) {
          let btn;
          if (data.ada > 0) {
            btn = "";
          } else {
            btn = `<button type="button" class="btn btn-aktif btn-primary btn-sm" data-id="${data.id_dosen}">
								<i class="fa fa-user-plus"></i>
							</button>`;
          }
          return `<div class="btn-group btn-group-sm" role="group" aria-label="">
							<a href="${base_url}dosen/edit/${data.id_dosen}" class="btn btn-sm btn-warning">
								<i class="fa fa-pencil"></i>
							</a>
							${btn}
						</div>`;
        }
      },
      {
        searchable: false,
        targets: 4,
        data: "nama_matkul",
        render: function(data, type, row, meta) {
          let data_array = data.split('---');
          let str_return = '';
          data_array.forEach(function (item,index) {
            str_return += '<span class="badge bg-info">' + item + '</span> ';
          });
          return str_return;
          // return `<div class="text-center">
			// 						<input name="checked[]" class="check" value="${data}" type="checkbox">
			// 					</div>`;
        }
      },
      {
        searchable: false,
        targets: 6,
        data: "id_dosen",
        render: function(data, type, row, meta) {
          return `<div class="text-center">
									<input name="checked[]" class="check" value="${data}" type="checkbox">
								</div>`;
        }
      }
    ],
    order: [[1, "asc"]],
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
  });

  // table
  //   .buttons()
  //   .container()
  //   .appendTo("#dosen_wrapper .col-md-6:eq(0)");

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

  $("#dosen tbody").on("click", "tr .check", function() {
    var check = $("#dosen tbody tr .check").length;
    var checked = $("#dosen tbody tr .check:checked").length;
    if (check === checked) {
      $(".select_all").prop("checked", true);
    } else {
      $(".select_all").prop("checked", false);
    }
  });

  $("#bulk").on("submit", function(e) {
    if ($(this).attr("action") == base_url + "dosen/ajax/delete") {
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

  $("#dosen").on("click", ".btn-aktif", function() {
    let id = $(this).data("id");

    $.ajax({
      url: base_url + "dosen/ajax/create_user",
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
  if ($("#dosen tbody tr .check:checked").length == 0) {
    Swal.fire({
      title: "Gagal",
      text: "Tidak ada data yang dipilih",
      icon: "error"
    });
  } else {
    $("#bulk").attr("action", base_url + "dosen/ajax/delete");
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
