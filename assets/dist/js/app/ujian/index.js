let waktu_buka_soal = null;

$(document).ready(function () {
    ajaxcsrf();

    // var t = $('.sisawaktu');
    // if (t.length) {
    //     sisawaktu(t.data('time'));
    // }

    // buka(1);
    // simpan_view();

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

    let soal_ke = $('#btn_soal_' + id_widget).text();
    $("#soalke").html(soal_ke);

    let topik_id_buka = $('#topik_id_' + id_widget).val();
    $('#text_info_topik').text(topik_nama[topik_id_buka]);

    $(".step").hide();
    $("#widget_" + id_widget).show();
    $("#widget_jawaban_" + id_widget).show();

    let sid = $('input[name="id_soal_'+ id_widget +'"]').val();
    // waktu_buka_soal[sid] = waktu_buka_soal[sid] ? waktu_buka_soal[sid] : moment().format("YYYY-MM-DD HH:mm:ss");
    waktu_buka_soal = moment().format("YYYY-MM-DD HH:mm:ss");
    // console.log('buka', waktu_buka_soal);
}

function next() {
    var berikutnya = $(".next").attr('rel');
    berikutnya = parseInt(berikutnya);
    berikutnya = berikutnya > total_widget ? total_widget : berikutnya;

    let topik_id_next = $('#topik_id_' + berikutnya).val();
    if(is_sekuen_topik){
        if(topik_id_next != topik_aktif){
            Swal.fire({
                title: "Perhatian",
                text: "Maaf, tidak diperbolehkan", // INI TERJADI JIKA INGIN MENERUKAN KE TOPIK SESUDAHNYA
                icon: "warning"
            });
            return;
        }
    }

    let soal_ke = $('#btn_soal_' + berikutnya).text();
    $("#soalke").html(soal_ke);

    let topik_id_buka = $('#topik_id_' + berikutnya).val();
    $('#text_info_topik').text(topik_nama[topik_id_buka]);

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

    simpan_view();

    let sid = $('input[name="id_soal_'+ berikutnya +'"]').val();
    // waktu_buka_soal[sid] = waktu_buka_soal[sid] ? waktu_buka_soal[sid] : moment().format("YYYY-MM-DD HH:mm:ss");
    waktu_buka_soal = moment().format("YYYY-MM-DD HH:mm:ss");
    // console.log('next', waktu_buka_soal);

}

function back() {
    var back = $(".back").attr('rel');
    back = parseInt(back);
    back = back < 1 ? 1 : back;

    let topik_id_back = $('#topik_id_' + back).val();
    if(is_sekuen_topik){
        if(topik_id_back != topik_aktif){
            Swal.fire({
                title: "Perhatian",
                text: "Maaf, tidak diperbolehkan", // INI TERJADI JIKA INGIN MENERUKAN KE TOPIK SESUDAHNYA
                icon: "warning"
            });
            return;
        }
    }

    let soal_ke = $('#btn_soal_' + back).text();
    $("#soalke").html(soal_ke);

    let topik_id_buka = $('#topik_id_' + back).val();
    $('#text_info_topik').text(topik_nama[topik_id_buka]);


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

    simpan_view();

    // waktu_buka_soal[sid] = waktu_buka_soal[sid] ? waktu_buka_soal[sid] : moment().format("YYYY-MM-DD HH:mm:ss");
    waktu_buka_soal = moment().format("YYYY-MM-DD HH:mm:ss");
    // console.log('buka', waktu_buka_soal);

}

function tidak_jawab() {

    var id_step = $(".ragu_ragu").attr('rel');

    if(!$('input[name="opsi_'+ id_step +'"]').is(':checked')) {
        return false;
    }

    var status_ragu = $("#rg_" + id_step).val();
    let ragu = null ;

    if (status_ragu == "N") {
        $("#rg_" + id_step).val('Y');
        $("#btn_soal_" + id_step).removeClass('btn-success');
        $("#btn_soal_" + id_step).addClass('btn-warning');
        ragu = 'Y';

    } else {
        $("#rg_" + id_step).val('N');
        $("#btn_soal_" + id_step).removeClass('btn-warning');
        $("#btn_soal_" + id_step).addClass('btn-success');
        ragu = 'N';
    }

    cek_status_ragu(id_step);

    let sid = $('input[name="id_soal_'+ id_step +'"]').val();
    let answer = $('input[name="opsi_'+ id_step +'"]:checked').val();
    simpan_jawaban_satu(sid, answer, ragu, false);
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

function simpan_view() {
    var f_asal = $("#ujian");
    var form = getFormData(f_asal);
    //form = JSON.stringify(form);
    var jml_soal = form.jml_soal;
    jml_soal = parseInt(jml_soal);

    // var hasil_jawaban = '<div class="btn-group" role="group" aria-label="">';
    var hasil_jawaban = '<div >';
    let count_jml_soal_per_topik = [];
    let label_soal = 1 ;
    for (var i = 1; i < jml_soal; i++) {
        var idx = 'opsi_' + i;
        var idx2 = 'rg_' + i;
        var idx3 = 'topik_id_' + i;

        var jawab = form[idx];
        var ragu = form[idx2];
        var topik_id = form[idx3];


        // count_jml_soal_per_topik[topik_id] = count_jml_soal_per_topik[topik_id] ? count_jml_soal_per_topik[topik_id] : 0 ;
        // count_jml_soal_per_topik[topik_id] = count_jml_soal_per_topik[topik_id] + 1;

        

        
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
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-outline-primary btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (label_soal) + "</button>";
                } else {
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-warning btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (label_soal) + "</button>";
                }
            } else {
                if (jawab == "-") {
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-outline-primary btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (label_soal) + "</button>";
                } else {
                    hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-success btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (label_soal) + "</button>";
                }
            }
        } else {
            hasil_jawaban += '<button type="button" id="btn_soal_' + (i) + '" class="class_topik_id_' + topik_id + ' btn btn-outline-primary btn_soal" style="padding: 10px; font-size:10px; margin-right:10px; margin-bottom: 10px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' + (i) + ');">' + (label_soal) + "</button>";
        }

        if(label_soal == topik[topik_id]){
            label_soal = 0;
        }

        label_soal = label_soal + 1 ;

    }

    hasil_jawaban += '</div>';
    $("#tampil_jawaban").html('<div id="yes">' + hasil_jawaban + '</div>');


    wrap_navigasi();
}

// function simpan_jawaban_all() {
//     let form = $("#ujian");
//     ajx_overlay(true);
//     $.ajax({
//         type: "POST",
//         url: base_url + "ujian/ajax/simpan_jawaban_all",
//         data: form.serialize(),
//         dataType: 'json',
//         success: function (data) {

//         },
//         error: function () {
//             Swal.fire({
//                 title: "Perhatian",
//                 text: "Terjadi kesalahan, halaman akan di-reload",
//                 icon: "warning",
//                 confirmButtonColor: "#37bc9b",
//                 confirmButtonText: "Reload"
//             }).then(result => {
//                 if (result.value) {
//                     // location.href = base_url + "ujian/list";
//                     location.reload();
//                 }
//             });
//         },
//         complete: function () {
//             ajx_overlay(false);
//         }
//     });
// }

function simpan_jawaban_satu(sid, answer, ragu, go_next = true) {
    ajx_overlay(true);
    $.ajax({
        type: "POST",
        url: base_url + "ujian/ajax/simpan_jawaban_satu",
        data: {
            'sid': sid,
            'answer': answer,
            'id': id_ujian,
            'key': key,
            'ragu': ragu,
            'waktu_buka_soal': waktu_buka_soal,
            'waktu_jawab_soal': moment().format("YYYY-MM-DD HH:mm:ss"),
        },
        dataType: 'json',
        success: function (data) {
            let urutan_soal = $('#opsi_a_' + sid ).attr('rel'); // AMBIL SALAH SATU ABJAD, contoh : A
            let next_soal_id = parseInt(urutan_soal) + 1;

            let topik_id_next = $('#topik_id_' + next_soal_id).val();
            let valid_next = false ;
            if(is_sekuen_topik){
                if(topik_id_next == topik_aktif){
                    valid_next = true ;
                }
            }else{
                valid_next = true ;
            }

            let sudah_akhir = next_soal_id > total_widget ? 1 : 0;
            if((!sudah_akhir) && valid_next && go_next){
                // next(next_soal_id); // UNTUK MENUJU SOAL SELANJUTNYA JIKA SUDAH MENJAWAB
            }
        },
        error: function () {
            Swal.fire({
                title: "Perhatian",
                text: "Terjadi kesalahan, halaman akan di-reload",
                icon: "warning",
                confirmButtonColor: "#37bc9b",
                confirmButtonText: "Reload"
            }).then(result => {
                if (result.value) {
                    // location.href = base_url + "ujian/list";
                    location.reload();
                }
            });
        },
        complete: function () {
            ajx_overlay(false);
        }
    });
}

function simpan_akhir() {
    if(is_sekuen_topik){
        if(last_topik_id != topik_aktif){
            Swal.fire({
                title: "Perhatian",
                text: "Anda belum bisa untuk mengakhiri ujian saat ini",
                icon: "warning",
            });
            return ;
        }
    }
    Swal.fire({
        title: "Akhiri Ujian",
        text: "Ujian yang sudah diakhiri tidak dapat diulangi.",
        icon: "warning",
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

$(document).on('click','input[type="radio"]',function(){
    if($(this).prop("checked", true)){
        simpan_view();
        let sid = $(this).data('sid');
        let answer = $(this).val();
        let id_step = $(this).attr('rel');
        let ragu = $("#rg_" + id_step).val() ;
        simpan_jawaban_satu(sid, answer, ragu);
        $('.ragu_ragu').show();
    }
});
