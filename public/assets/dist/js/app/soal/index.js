$(document).ready(function() {
    ajaxcsrf();

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
