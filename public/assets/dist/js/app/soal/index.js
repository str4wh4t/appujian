let table;

$(document).ready(function() {
  ajaxcsrf();

  table = $("#soal").DataTable({
    initComplete: function() {
      var api = this.api();
      $("#soal_filter input")
        .off(".DT")
        .on("keypress.DT", function(e) {
          if(e.which == 13) {
            api.search(this.value).draw();
            return false;
          }
        });
    },
    lengthChange: false,
    // lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    dom:
      "<'row'<'col-lg-3'l><'col-lg-6 text-center'B><'col-lg-3'f>>" +
      "<'row'<'col-lg-12'tr>>" +
      "<'row'<'col-lg-5'i><'col-lg-7'p>>",
    buttons: [
      {
          text: 'Show Entries',
          action: function ( e, dt, node, config ) {
              $('#show_length_number').val(10).trigger('change');
              $('#show_length_number_custom').val('');
              $('#modal_page_length').modal('show');
          },
          className: 'btn-info'
      },
      {
        extend: "copy",
        exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8], format: {
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
        exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8], format: {
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
        exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8], format: {
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
        exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8], format: {
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
      url: base_url + "soal/ajax/data",
      type: "POST"
    },
    columns: [
      {
        data: "id_soal",
        orderable: false,
        searchable: false
      },
      {
        data: "no_urut",
        // orderable: false,
        searchable: false
      },
      { data: "nama_matkul" },
      { data: "nama_topik" },
      { data: "soal" },
      { data: "bobot" },
      { data: "bundle" },
      { data: "created_at" },
      { data: "oleh" }
    ],
    columnDefs: [
      {
        targets: 0,
        data: "id_soal",
        render: function(data, type, row, meta) {
          return `<div class="text-center">
									<input name="checked[]" class="check" value="${data}" type="checkbox">
								</div>`;
        }
      },
      {
        targets: 6,
        data: "bundle",
        render: function(data, type, row, meta) {
          let str_return = '';
          if(data != null) {
            let data_array = data.split('---');
            data_array.forEach(function (item, index) {
              str_return += '<span class="badge bg-info">' + item + '</span> ';
            });
          }
          return str_return;
        }
      },
      {
        targets: 9,
        data: "id_soal",
        render: function(data, type, row, meta) {
          return `<div class="btn-group btn-group-sm" role="group" aria-label="">
                                <a href="${base_url}soal/detail/${data}" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="${base_url}soal/edit/${data}" class="btn btn-sm btn-success">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" data-id="${data}" class="btn btn-sm btn-danger btn-report-soal">
                                    <i class="ft-alert-circle"></i>
                                </button>
                            </div>`;
        }
      }
    ],
    order: [[2, "asc"], [3, "asc"], [7, "asc"]],
    rowId: function(data) {
      return 'dt_tr_' + data.id_soal;
    },
    createdRow: function (row, data, dataIndex) {
      if(data.is_reported == 1){
        $(row).addClass('bg-warning');
      }else{
        $(row).removeClass('bg-warning');
      }

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
    //     leftColumns: 3,
    // }
  });

  // table
  //   .buttons()
  //   .container()
  //   .appendTo("#soal_wrapper .col-md-6:eq(0)");

    // $(document).on('change', '.select_all', function () {
    //     $(this).is(':checked') ? $('table .check').prop('checked', true) : $('table .check').prop('checked', false);
    //     $('.DTFC_LeftBodyLiner table .check').trigger('change');
    // });

    // $(document).on('change','table .check',function () {
    //     $(this).is(':checked') ? null : $('.select_all').prop('checked', false);
    // });

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
  
    $("table tbody").on("click", "tr .check", function() {
      var check = $("table tbody tr .check").length;
      var checked = $("table tbody tr .check:checked").length;
      if (check === checked) {
        $(".select_all").prop("checked", true);
      } else {
        $(".select_all").prop("checked", false);
      }
    });

  // $(".select_all").on("click", function() {
  //   if (this.checked) {
  //     $(".check").each(function() {
  //       this.checked = true;
  //       $(".select_all").prop("checked", true);
  //     });
  //   } else {
  //     $(".check").each(function() {
  //       this.checked = false;
  //       $(".select_all").prop("checked", false);
  //     });
  //   }
  // });
  //
  // $("#soal tbody").on("click", "tr .check", function() {
  //   var check = $("#soal tbody tr .check").length;
  //   var checked = $("#soal tbody tr .check:checked").length;
  //   if (check === checked) {
  //     $(".select_all").prop("checked", true);
  //   } else {
  //     $(".select_all").prop("checked", false);
  //   }
  // });

  $("#bulk").on("submit", function(e) {
    if ($(this).attr("action") == base_url + "soal/ajax/delete") {
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
              icon:"success"
            });
            reload_ajax();
          } else {
            Swal.fire({
              title: "Perhatian",
              text: "Anda bukan pembuat soal.",
              icon:"warning"
            });
          }
        },
        error: function () {
          Swal.fire({
            title: "Gagal",
            text: "Ada data yang sedang digunakan",
            icon:"error"
          });
        },
        complete: function(){

        }
      });
    }
  });
});

function bulk_delete() {
  // console.log("$('#bulk').serialize()", $('#bulk').serialize());
  // if ($('#ujian tbody tr .check:checked').length == 0) {
  if ($('table .check:checked').length == 0) {
    Swal.fire({
      title: "Gagal",
      text: "Tidak ada data yang dipilih",
      icon:"error"
    });
  } else {
    $("#bulk").attr("action", base_url + "soal/ajax/delete");
    Swal.fire({
      title: "Anda yakin?",
      text: "Data akan dihapus!",
      icon:"warning",
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

$(document).on('click', '#submit_show_length', function(){
  let jml_data = $('#show_length_number').val();
  let jml_data_custom = $('#show_length_number_custom').val();

  let jml_data_valid = jml_data_custom ? jml_data_custom : jml_data ;
  table.page.len( jml_data_valid ).draw();
  $('#modal_page_length').modal('hide');

});
