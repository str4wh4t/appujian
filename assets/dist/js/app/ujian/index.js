$(document).ready(function () {
    ajaxcsrf();

    // var t = $('.sisawaktu');
    // if (t.length) {
    //     sisawaktu(t.data('time'));
    // }

    buka(1);
    simpan_sementara();

    widget = $(".step");
    btnnext = $(".next");
    btnback = $(".back");
    btnsubmit = $(".submit");

    $(".step, .back, .selesai").hide();
    $("#widget_1").show();
    $("#widget_jawaban_1").show();
});

function getFormData($form) {
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};
    $.map(unindexed_array, function (n, i) {
        indexed_array[n['name']] = n['value'];
    });
    return indexed_array;
}

function buka(id_widget) {
    $(".next").attr('rel', (id_widget + 1));
    $(".back").attr('rel', (id_widget - 1));
    $(".ragu_ragu").attr('rel', (id_widget));

    if($('input[type="radio"][rel="'+ id_widget +'"]:checked').length > 0){
        // $(".ragu_ragu").removeAttr('disabled');
        $('.ragu_ragu').show();
    }else{
        // $(".ragu_ragu").attr('disabled','disabled');
        $('.ragu_ragu').hide();
    }

    cek_status_ragu(id_widget);
    cek_terakhir(id_widget);

    $("#soalke").html(id_widget);

    $(".step").hide();
    $("#widget_" + id_widget).show();
    $("#widget_jawaban_" + id_widget).show();

    // simpan();
}

function next() {
    var berikutnya = $(".next").attr('rel');
    berikutnya = parseInt(berikutnya);
    berikutnya = berikutnya > total_widget ? total_widget : berikutnya;

    $("#soalke").html(berikutnya);

    $(".next").attr('rel', (berikutnya + 1));
    $(".back").attr('rel', (berikutnya - 1));
    $(".ragu_ragu").attr('rel', (berikutnya));

    if($('input[type="radio"][rel="'+ berikutnya +'"]:checked').length > 0){
        // $(".ragu_ragu").removeAttr('disabled');
        $('.ragu_ragu').show();
    }else{
        // $(".ragu_ragu").attr('disabled','disabled');
        $('.ragu_ragu').hide();
    }

    cek_status_ragu(berikutnya);
    cek_terakhir(berikutnya);

    var sudah_akhir = berikutnya == total_widget ? 1 : 0;

    $(".step").hide();
    $("#widget_" + berikutnya).show();
    $("#widget_jawaban_" + berikutnya).show();

    if (sudah_akhir == 1) {
        $(".back").show();
        $(".next").hide();
    } else if (sudah_akhir == 0) {
        $(".next").show();
        $(".back").show();
    }

    simpan();
}

function back() {
    var back = $(".back").attr('rel');
    back = parseInt(back);
    back = back < 1 ? 1 : back;

    $("#soalke").html(back);

    $(".back").attr('rel', (back - 1));
    $(".next").attr('rel', (back + 1));
    $(".ragu_ragu").attr('rel', (back));

    if($('input[type="radio"][rel="'+ back +'"]:checked').length > 0){
        // $(".ragu_ragu").removeAttr('disabled');
        $('.ragu_ragu').show();
    }else{
        // $(".ragu_ragu").attr('disabled','disabled');
        $('.ragu_ragu').hide();
    }

    cek_status_ragu(back);
    cek_terakhir(back);

    $(".step").hide();
    $("#widget_" + back).show();
    $("#widget_jawaban_" + back).show();

    var sudah_awal = back == 1 ? 1 : 0;

    if (sudah_awal == 1) {
        $(".back").hide();
        $(".next").show();
    } else if (sudah_awal == 0) {
        $(".next").show();
        $(".back").show();
    }

    simpan();
}

function tidak_jawab() {

    var id_step = $(".ragu_ragu").attr('rel');

    if(!$('input[name="opsi_'+ id_step +'"]').is(':checked')) {
        return false;
    }

    var status_ragu = $("#rg_" + id_step).val();

    if (status_ragu == "N") {
        $("#rg_" + id_step).val('Y');
        $("#btn_soal_" + id_step).removeClass('btn-success');
        $("#btn_soal_" + id_step).addClass('btn-warning');

    } else {
        $("#rg_" + id_step).val('N');
        $("#btn_soal_" + id_step).removeClass('btn-warning');
        $("#btn_soal_" + id_step).addClass('btn-success');
    }

    cek_status_ragu(id_step);

    // simpan();
    do_save();
}

function cek_status_ragu(id_soal) {
    var status_ragu = $("#rg_" + id_soal).val();

    if (status_ragu == "N") {
        $(".ragu_ragu > span").html('Ragu');
    } else {
        $(".ragu_ragu > span").html('Tidak Ragu');
    }
}

function cek_terakhir(id_soal) {
    var jml_soal = $("#jml_soal").val();
    jml_soal = (parseInt(jml_soal) - 1);

    if (jml_soal === id_soal) {
        $('.next').hide();
        $(".back").show();
        // $(".selesai").show(); // DI-HIDDEN DIGANTI TOMBOL AKHIRI

    } else {
        if(1 == id_soal){
            $('.next').show();
            $(".back").hide();
        }else{
            $('.next').show();
            $(".back").show();
        }
        // $(".selesai").hide();
    }
}

function simpan_sementara() {
    var f_asal = $("#ujian");
    var form = getFormData(f_asal);
    //form = JSON.stringify(form);
    var jml_soal = form.jml_soal;
    jml_soal = parseInt(jml_soal);

    // var hasil_jawaban = '<div class="btn-group" role="group" aria-label="">';
    var hasil_jawaban = '<div >';

    for (var i = 1; i < jml_soal; i++) {
        var idx = 'opsi_' + i;
        var idx2 = 'rg_' + i;
        var idx3 = 'topik_id_' + i;

        var jawab = form[idx];
        var ragu = form[idx2];
        var topik_id = form[idx3];

        // if (jawab != undefined) {
        //     if (ragu == "Y") {
        //         if (jawab == "-") {
        //             hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="btn btn-outline-primary mr-1 mb-1 btn_soal" style="border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + ". " + jawab + "</button>";
        //         } else {
        //             hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="btn btn-warning mr-1 mb-1 btn_soal" style="border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + ". " + jawab + "</button>";
        //         }
        //     } else {
        //         if (jawab == "-") {
        //             hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="btn btn-outline-primary mr-1 mb-1 btn_soal" style="border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + ". " + jawab + "</button>";
        //         } else {
        //             hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="btn btn-success mr-1 mb-1 btn_soal" style="border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + ". " + jawab + "</button>";
        //         }
        //     }
        // } else {
        //     hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="btn btn-outline-primary mr-1 mb-1 btn_soal" style="border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + ". -</button>";
        // }

        if (jawab != undefined) {
            if (ragu == "Y") {
                if (jawab == "-") {
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-outline-primary btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + "</button>";
                } else {
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-warning btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + "</button>";
                }
            } else {
                if (jawab == "-") {
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-outline-primary btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + "</button>";
                } else {
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-success btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + "</button>";
                }
            }
        } else {
            hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-outline-primary btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (i) + "</button>";
        }

    }

    hasil_jawaban += '</div>';
    $("#tampil_jawaban").html('<div id="yes">' + hasil_jawaban + '</div>');


    wrap_navigasi();



    // do_save();
}

function do_save(){
    var form = $("#ujian");

    $.ajax({
        type: "POST",
        url: base_url + "ujian/ajax/simpan_satu",
        data: form.serialize(),
        dataType: 'json',
        success: function (data) {
            // $('.ajax-loading').show();
            console.log(data);
        }
    });
}

function simpan() {
    simpan_sementara();

}

function selesai() {
    simpan();
    ajaxcsrf();
    $.ajax({
        type: "POST",
        // url: base_url + "ujian/simpan_akhir",
        url: base_url + "ujian/ajax/close_ujian",
        // data: { id: id_tes },
        data: {
            'id': id_ujian,
            'key': key
        },
        beforeSend: function () {
            simpan();
            // $('.ajax-loading').show();
        },
        success: function (r) {
            // console.log(r);
            if (r.status) {
                window.location.href = base_url + 'ujian/list';
            }
        }
    });
}

function waktuHabis() {
    selesai();
    alert('Waktu ujian telah habis!');
}

function simpan_akhir() {
    simpan();
    // if (confirm('Yakin ingin mengakhiri ujian ?')) {
    //     selesai();
    // }

    Swal({
        title: "Akhiri Ujian",
        text: "Ujian yang sudah diakhiri tidak dapat diulangi.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#37bc9b",
        cancelButtonColor: "#f6bb42",
        confirmButtonText: "Akhiri"
    }).then(result => {
        if (result.value) {
            selesai();
        }
    });

}

///
$(document).on('click','input[type="radio"]',function(){
    if($(this).prop("checked", true)){
        simpan_sementara();
        do_save();
        // $('.ragu_ragu').removeAttr('disabled');
        $('.ragu_ragu').show();
    }
});
