// var save_label;
var table;

$(document).ready(function() {
  ajaxcsrf();

  table = $("#matkul").DataTable({
    initComplete: function() {
      var api = this.api();
      $("#matkul_filter input")
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
        exportOptions: { 
          columns: [0, 1, 2], 
          format: {
              body: function ( data, columnIdx, rowIdx ) {
                if(rowIdx == 0)
                  return (columnIdx + 1);
                else
                  return data;
              }
          } 
        }
      },
      {
        extend: "print",
        exportOptions: { 
          columns: [0, 1, 2], 
          format: {
              body: function ( data, columnIdx, rowIdx ) {
                if(rowIdx == 0)
                  return (columnIdx + 1);
                else
                  return data;
              }
          } 
        }
      },
      {
        extend: "excel",
        exportOptions: { 
          columns: [0, 1, 2], 
          format: {
              body: function ( data, columnIdx, rowIdx ) {
                if(rowIdx == 0)
                  return (columnIdx + 1);
                else
                  return data;
              }
          } 
        }
      },
      {
        extend: "pdf",
        exportOptions: { 
          columns: [0, 1, 2], 
          format: {
              body: function ( data, columnIdx, rowIdx ) {
                if(rowIdx == 0)
                  return (columnIdx + 1);
                else
                  return data;
              }
          } 
        }
      }
    ],
    oLanguage: {
      sProcessing: "loading..."
    },
    processing: true,
    serverSide: true,
    ajax: {
      url: base_url + "matkul/ajax/data",
      type: "POST"
      //data: csrf
    },
    columns: [
      {
        data: "id_matkul",
        orderable: false,
        searchable: false,
      },
      {
        data: "id_matkul",
        orderable: false,
        searchable: false,
      },
      { data: "nama_matkul" },
      // {
      //   data: "jml_peserta",
      //   orderable: false,
      //   searchable: false,
      // }
    ],
    columnDefs: [
      {
        searchable: false,
        targets: 0,
        data: "id_matkul",
        render: function(data, type, row, meta) {
          return `<div class="text-center">
									<input name="checked[]" class="check" value="${data}" type="checkbox">
								</div>`;
        }
      },
      {
        searchable: false,
        targets: 1,
      },
      // {
      //   searchable: false,
      //   targets: 4,
      //   data: "id_matkul",
      //   render: function(data, type, row, meta) {
      //     return `<a class="btn btn-sm btn-info" href="${base_url}matkul/peserta/${data}">Peserta</a>`;
      //   }
      // }
    ],
    order: [[2, "asc"]],
    rowId: function(a) {
      return a;
    },
    rowCallback: function(row, data, iDisplayIndex) {
      var info = this.fnPagingInfo();
      var page = info.iPage;
      var length = info.iLength;
      var index = page * length + (iDisplayIndex + 1);
      $("td:eq(1)", row).html(index);
    }
  });

  // table
  //   .buttons()
  //   .container()
  //   .appendTo("#matkul_wrapper .col-md-6:eq(0)");

  $("#myModal").on("shown.modal.bs", function() {
    $(':input[name="banyak"]').select();
  });

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

  $("#matkul tbody").on("click", "tr .check", function() {
    var check = $("#matkul tbody tr .check").length;
    var checked = $("#matkul tbody tr .check:checked").length;
    if (check === checked) {
      $(".select_all").prop("checked", true);
    } else {
      $(".select_all").prop("checked", false);
    }
  });

  $("#bulk").on("submit", function(e) {
    if ($(this).attr("action") == base_url + "matkul/ajax/delete") {
      e.preventDefault();
      e.stopImmediatePropagation();

      $.ajax({
        url: $(this).attr("action"),
        data: $(this).serialize(),
        type: "POST",
        success: function(respon) {
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
        error: function() {
          Swal.fire({
            title: "Gagal",
            text: "Ada data yang sedang digunakan",
            icon: "error"
          });
        }
      });
    }
  });
});

function bulk_delete() {
  if ($("#matkul tbody tr .check:checked").length == 0) {
    Swal.fire({
      title: "Gagal",
      text: "Tidak ada data yang dipilih",
      icon: "error"
    });
  } else {
    $("#bulk").attr("action", base_url + "matkul/ajax/delete");
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

function bulk_edit() {
  if ($("#matkul tbody tr .check:checked").length == 0) {
    Swal.fire({
      title: "Gagal",
      text: "Tidak ada data yang dipilih",
      icon: "error"
    });
  } else {
    $("#bulk").attr("action", base_url + "matkul/edit");
    $("#bulk").submit();
  }
}
