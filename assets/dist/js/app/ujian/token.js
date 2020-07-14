$(document).ready(function () {
    ajaxcsrf();

    $('#btncek').on('click', function () {
        if($(this).hasClass('btn-danger')){
            Swal('Perhatian', 'Anda berada diluar jadwal ujian', 'error');
            return false;
        }
        var idUjian = $(this).data('id');
        let token = $('#token').length ? $('#token').val() : 'XXX';
        if (token == '') {
            Swal('Gagal', 'Token harus diisi', 'error');
        } else {
            // var key = $('#id_ujian').data('key');
            ajx_overlay(true);
            $.ajax({
                url: base_url + 'ujian/ajax/cektoken/',
                type: 'POST',
                data: {
                    id_ujian: idUjian,
                    token: token
                },
                cache: false,
                success: function (result) {
                    ajx_overlay(false);
                    if(!result.status){
                        Swal({
                            "type": "error",
                            "title": "Gagal",
                            "text": "Token Salah"
                        })
                    }else{
                        // Swal({
                        //     title: "Mulai Ujian",
                        //     text: "Ujian yang sudah dimulai tidak dapat dibatalkan.",
                        //     type: "warning",
                        //     showCancelButton: true,
                        //     confirmButtonColor: "#37bc9b",
                        //     cancelButtonColor: "#f6bb42",
                        //     confirmButtonText: "Mulai"
                        // }).then(result => {
                        //     if (result.value) {
                        //         location.href = base_url + 'ujian/?key=' + key + '&id=' + idUjian;
                        //     }
                        //     });
                        $('#modal_tata_tertib').data('id',result.token);
                        $('#modal_tata_tertib').modal('show');
                    }

                },
                error: function (result) {
                    ajx_overlay(false);
                    Swal({
                        "type": "error",
                        "title": "Gagal",
                        "text": "Terjadi kesalahan"
                    });
                }
            });
        }
    });

    var time = $('.countdown');
    if (time.length) {
        countdown(time.data('time'));
    }
});
