var save_label;
var table;

$(document).ready(function() {
  ajaxcsrf();

  table = $("#topik").DataTable({
    initComplete: function() {
      var api = this.api();
      $("#topik_filter input")
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
                  return data;
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
      url: base_url + "topik/ajax/data",
      type: "POST"
      //data: csrf
    },
    columns: [
      {
        data: "id",
        orderable: false,
        searchable: false
      },
      { data: "nama_matkul" },
      { data: "nama_topik" },
      { data: "poin_topik" },
      {
        data: "jml_soal",
        orderable: false,
        searchable: false,
      },
      {
        data: "bulk_select",
        orderable: false,
        searchable: false
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
    }
  });

  // table
  //   .buttons()
  //   .container()
  //   .appendTo("#topik_wrapper .col-md-6:eq(0)");

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

  $("#topic tbody").on("click", "tr .check", function() {
    var check = $("#topic tbody tr .check").length;
    var checked = $("#topic tbody tr .check:checked").length;
    if (check === checked) {
      $(".select_all").prop("checked", true);
    } else {
      $(".select_all").prop("checked", false);
    }
  });

  $("#bulk").on("submit", function(e) {
    if ($(this).attr("action") == base_url + "topik/ajax/delete") {
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

function load_matkul() {
  var matkul = $('select[name="nama_matkul"]');
  matkul.children("option:not(:first)").remove();

  ajaxcsrf(); // get csrf token
  $.ajax({
    url: base_url + "matkul/load_matkul",
    type: "GET",
    success: function(data) {
      //console.log(data);
      if (data.length) {
        var dataJurusan;
        $.each(data, function(key, val) {
          dataJurusan = `<option value="${val.id_matkul}">${val.nama_matkul}</option>`;
          matkul.append(dataJurusan);
        });
      }
    }
  });
}

function bulk_delete() {
  if ($("#topik tbody tr .check:checked").length == 0) {
    Swal.fire({
      title: "Gagal",
      text: "Tidak ada data yang dipilih",
      icon: "error"
    });
  } else {
    $("#bulk").attr("action", base_url + "topik/ajax/delete");
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
  if ($("#topik tbody tr .check:checked").length == 0) {
    Swal.fire({
      title: "Gagal",
      text: "Tidak ada data yang dipilih",
      icon: "error"
    });
  } else {
    $("#bulk").attr("action", base_url + "topik/edit");
    $("#bulk").submit();
  }
}
