$(document).ready(function () {
    $('#tgl_mulai').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        date: tgl_mulai,
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
    $('#tgl_selesai').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        date: terlambat,
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


    $('#formujian input, #formujian select').on('change', function () {
        $(this).closest('.form-group').eq(0).removeClass('has-error');
        $(this).nextAll('.help-block').eq(0).text('');
    });

    $('#formujian').on('submit', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        let btn = $('#submit');
        // btn.attr('disabled', 'disabled').text('Proses...');
        ajx_overlay(true);
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: 'POST',
            success: function (data) {
                // console.log(data);
                if (data.status) {
                    Swal.fire({
                        title: "Berhasil",
                        icon:"success",
                        text: "Data berhasil disimpan"
                    }).then(result => {
                        window.location.href = base_url+"ujian/master";
                    });
                } else {
                    Swal.fire({
                        title: "Perhatian",
                        icon: "warning",
                        text: "Terdapat kesalahan pada data"
                    });
                    if (data.errors) {
                        $.each(data.errors, function (key, val) {
                            $('[name="' + key + '"]').closest('.form-group').eq(0).addClass('has-error');
                            $('[name="' + key + '"]').nextAll('.help-block').eq(0).text(val);
                            if (val === '') {
                                $('[name="' + key + '"]').closest('.form-group').eq(0).removeClass('has-error');
                                $('[name="' + key + '"]').nextAll('.help-block').eq(0).text('');
                            }
                        });
                    }
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error",
                    icon: "warning",
                    text: "Terdapat kesalahan pada data"
                });
            },
            complete: function () {
                ajx_overlay(false);
            }
        });
    });
});
