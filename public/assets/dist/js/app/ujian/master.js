$(document).ready(function () {

    ajaxcsrf();

    // $(document).on('change', '.select_all', function () {
        // $(this).is(':checked') ? $('.DTFC_LeftBodyLiner table .check').prop('checked', true) : $('.DTFC_LeftBodyLiner table .check').prop('checked', false);
        // $('.DTFC_LeftBodyLiner table .check').trigger('change');
    // });

    // $(document).on('change', '.select_all', function () {
    //     $(this).is(':checked') ? $('#ujian tbody tr .check').prop('checked', true) : $('#ujian tbody tr .check').prop('checked', false);
    //     // $('#ujian tbody tr .check').trigger('change');
    // });

    // $(document).on('change','.DTFC_LeftBodyLiner table .check',function () {
        // $(this).is(':checked') ? null : $('.select_all').prop('checked', false);
    // });

    // $(document).on('change','#ujian tbody tr .check',function () {
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
    
      $("#ujian tbody").on("click", "tr .check", function() {
        var check = $("#ujian tbody tr .check").length;
        var checked = $("#ujian tbody tr .check:checked").length;
        if (check === checked) {
          $(".select_all").prop("checked", true);
        } else {
          $(".select_all").prop("checked", false);
        }
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
                    Swal.fire({
                        title: "Perhatian",
                        text: "Anda bukan pembuat ujian.",
                        icon: "warning"
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
                    Swal.fire({
                        title: "Berhasil",
                        text: respon.total + " data berhasil dihapus",
                        icon:"success"
                    });
                } else {
                    Swal.fire({
                        title: "Perhatian",
                        text: "Anda bukan pembuat ujian.",
                        icon: "warning"
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
    });

    // table.ajax.url(base_url+'ujian/json/'+id_dosen).load();
});

function bulk_delete() {
    // console.log("$('#bulk').serialize()", $('#bulk').serialize());
    if ($('#ujian tbody tr .check:checked').length == 0) {
    // if ($('.DTFC_LeftBodyLiner table .check:checked').length == 0) {
        Swal.fire({
            title: "Gagal",
            text: 'Tidak ada data yang dipilih',
            icon: 'error'
        });
    } else {
        Swal.fire({
            title: 'Anda yakin?',
            text: "Data akan dihapus!",
            icon: 'warning',
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
