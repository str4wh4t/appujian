let waktu_buka_soal = null;
let nomer_soal_before = 1; // DI INIT NOMER PERTAMA DULU
let nomer_soal_dibuka = 1; // DI INIT NOMER PERTAMA DULU

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
        indexed_array[n["name"]] = n["value"];
    });
    return indexed_array;
}

function buka(id_widget) {
    nomer_soal_dibuka = id_widget;

    $(".next").attr("rel", id_widget + 1);
    $(".back").attr("rel", id_widget - 1);
    $(".ragu_ragu").attr("rel", id_widget);

    // if($('input[name="tipe_soal_'+ nomer_soal_before +'"]').val() == tipe_soal_essay){
    //     let flag_check_jawaban_essay = $('input[name="flag_check_jawaban_essay_'+ nomer_soal_before +'"]').val();
    //     if(flag_check_jawaban_essay == 'N'){
    //         $('textarea[name="opsi_' + nomer_soal_before + '"]').summernote('code', '');
    //     }
    // }

    if (
        $('input[name="tipe_soal_' + id_widget + '"]').val() == tipe_soal_mcsa
    ) {
        if (
            $('input[type="radio"][rel="' + id_widget + '"]:checked').length > 0
        ) {
            $(".ragu_ragu").show();
        } else {
            $(".ragu_ragu").hide();
        }
    } else if (
        $('input[name="tipe_soal_' + id_widget + '"]').val() == tipe_soal_essay
    ) {
        if (
            !$('textarea[name="opsi_' + id_widget + '"]').summernote("isEmpty")
        ) {
            $(".ragu_ragu").show();
        } else {
            $(".ragu_ragu").hide();
        }
    }

    cek_status_ragu(id_widget);
    cek_terakhir(id_widget);

    let soal_ke = $("#btn_soal_" + id_widget).text();
    $("#soalke").html(soal_ke);

    let topik_id_buka = $("#topik_id_" + id_widget).val();
    $("#text_info_topik").text(topik_nama[topik_id_buka]);

    $(".step").hide();
    $("#widget_" + id_widget).show();
    $("#widget_jawaban_" + id_widget).show();

    simpan_view();

    let sid = $('input[name="id_soal_' + id_widget + '"]').val();
    // waktu_buka_soal[sid] = waktu_buka_soal[sid] ? waktu_buka_soal[sid] : moment().format("YYYY-MM-DD HH:mm:ss");
    waktu_buka_soal = moment().format("YYYY-MM-DD HH:mm:ss");
    // console.log('buka', waktu_buka_soal);
    // $('#q_n_a').scrollTop(0);
    $("#lembar_ujian").scrollTop(0);

    nomer_soal_before = id_widget;
}

function next() {
    let berikutnya = $(".next").attr("rel");
    berikutnya = parseInt(berikutnya);
    berikutnya = berikutnya > total_widget ? total_widget : berikutnya;

    nomer_soal_dibuka = berikutnya;

    let topik_id_next = $("#topik_id_" + berikutnya).val();
    if (is_sekuen_topik) {
        if (topik_id_next != topik_aktif) {
            Swal.fire({
                title: "Perhatian",
                text: "Maaf, tidak diperbolehkan", // INI TERJADI JIKA INGIN MENERUKAN KE TOPIK SESUDAHNYA
                icon: "warning",
            });
            return;
        }
    }

    let soal_ke = $("#btn_soal_" + berikutnya).text();
    $("#soalke").html(soal_ke);

    let topik_id_buka = $("#topik_id_" + berikutnya).val();
    $("#text_info_topik").text(topik_nama[topik_id_buka]);

    $(".next").attr("rel", berikutnya + 1);
    $(".back").attr("rel", berikutnya - 1);
    $(".ragu_ragu").attr("rel", berikutnya);

    // if($('input[name="tipe_soal_'+ nomer_soal_before +'"]').val() == tipe_soal_essay){
    //     let flag_check_jawaban_essay = $('input[name="flag_check_jawaban_essay_'+ nomer_soal_before +'"]').val();
    //     if(flag_check_jawaban_essay == 'N'){
    //         $('textarea[name="opsi_' + nomer_soal_before + '"]').summernote('code', '');
    //     }
    // }

    if (
        $('input[name="tipe_soal_' + berikutnya + '"]').val() == tipe_soal_mcsa
    ) {
        if (
            $('input[type="radio"][rel="' + berikutnya + '"]:checked').length >
            0
        ) {
            $(".ragu_ragu").show();
        } else {
            $(".ragu_ragu").hide();
        }
    } else if (
        $('input[name="tipe_soal_' + berikutnya + '"]').val() == tipe_soal_essay
    ) {
        if (
            !$('textarea[name="opsi_' + berikutnya + '"]').summernote("isEmpty")
        ) {
            $(".ragu_ragu").show();
        } else {
            $(".ragu_ragu").hide();
        }
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

    let sid = $('input[name="id_soal_' + berikutnya + '"]').val();
    // waktu_buka_soal[sid] = waktu_buka_soal[sid] ? waktu_buka_soal[sid] : moment().format("YYYY-MM-DD HH:mm:ss");
    waktu_buka_soal = moment().format("YYYY-MM-DD HH:mm:ss");
    // console.log('next', waktu_buka_soal);
    // $('#q_n_a').scrollTop(0);

    // $("#lembar_ujian").scrollTop(0);

    $("html, body").animate(
        { scrollTop: $("#isi_pertanyaan").offset().top },
        2500
    );

    nomer_soal_before = berikutnya;
}

function back() {
    let back = $(".back").attr("rel");
    back = parseInt(back);
    back = back < 1 ? 1 : back;

    nomer_soal_dibuka = back;

    let topik_id_back = $("#topik_id_" + back).val();
    if (is_sekuen_topik) {
        if (topik_id_back != topik_aktif) {
            Swal.fire({
                title: "Perhatian",
                text: "Maaf, tidak diperbolehkan", // INI TERJADI JIKA INGIN MENERUKAN KE TOPIK SESUDAHNYA
                icon: "warning",
            });
            return;
        }
    }

    let soal_ke = $("#btn_soal_" + back).text();
    $("#soalke").html(soal_ke);

    let topik_id_buka = $("#topik_id_" + back).val();
    $("#text_info_topik").text(topik_nama[topik_id_buka]);

    $(".back").attr("rel", back - 1);
    $(".next").attr("rel", back + 1);
    $(".ragu_ragu").attr("rel", back);

    // if($('input[name="tipe_soal_'+ nomer_soal_before +'"]').val() == tipe_soal_essay){
    //     let flag_check_jawaban_essay = $('input[name="flag_check_jawaban_essay_'+ nomer_soal_before +'"]').val();
    //     if(flag_check_jawaban_essay == 'N'){
    //         $('textarea[name="opsi_' + nomer_soal_before + '"]').summernote('code', '');
    //     }
    // }

    if ($('input[name="tipe_soal_' + back + '"]').val() == tipe_soal_mcsa) {
        if ($('input[type="radio"][rel="' + back + '"]:checked').length > 0) {
            $(".ragu_ragu").show();
        } else {
            $(".ragu_ragu").hide();
        }
    } else if (
        $('input[name="tipe_soal_' + back + '"]').val() == tipe_soal_essay
    ) {
        if (!$('textarea[name="opsi_' + back + '"]').summernote("isEmpty")) {
            $(".ragu_ragu").show();
        } else {
            $(".ragu_ragu").hide();
        }
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
    // $('#q_n_a').scrollTop(0);

    // $("#lembar_ujian").scrollTop(0);

    $("html, body").animate(
        { scrollTop: $("#isi_pertanyaan").offset().top },
        2500
    );

    nomer_soal_before = back;
}

function tidak_jawab() {
    var id_step = $(".ragu_ragu").attr("rel");

    if ($('input[name="tipe_soal_' + id_step + '"]').val() == tipe_soal_mcsa) {
        if (!$('input[name="opsi_' + id_step + '"]').is(":checked")) {
            return false;
        }
    } else if (
        $('input[name="tipe_soal_' + id_step + '"]').val() == tipe_soal_essay
    ) {
        if ($('textarea[name="opsi_' + id_step + '"]').summernote("isEmpty")) {
            return false;
        }
    }

    var status_ragu = $("#rg_" + id_step).val();
    let ragu = null;

    if (status_ragu == "N") {
        $("#rg_" + id_step).val("Y");
        $("#btn_soal_" + id_step).removeClass("btn-success");
        $("#btn_soal_" + id_step).addClass("btn-warning");
        ragu = "Y";
    } else {
        $("#rg_" + id_step).val("N");
        $("#btn_soal_" + id_step).removeClass("btn-warning");
        $("#btn_soal_" + id_step).addClass("btn-success");
        ragu = "N";
    }

    cek_status_ragu(id_step);

    let sid = $('input[name="id_soal_' + id_step + '"]').val();
    let answer = "";

    if ($('input[name="tipe_soal_' + id_step + '"]').val() == tipe_soal_mcsa) {
        answer = $('input[name="opsi_' + id_step + '"]:checked').val();
    } else if (
        $('input[name="tipe_soal_' + id_step + '"]').val() == tipe_soal_mcma
    ) {
        let answer_array = [];
        let selection_els = $('input[name="opsi_' + id_step + '[]"]:checked');
        selection_els.each(function (i, el) {
            let val = $(el).val();
            answer_array.push(val);
        });
        answer = JSON.stringify(answer_array);
    } else if (
        $('input[name="tipe_soal_' + id_step + '"]').val() == tipe_soal_essay
    ) {
        answer = $('textarea[name="opsi_' + id_step + '"]').val();
    }

    simpan_jawaban_satu(sid, answer, ragu, false);
}

function cek_status_ragu(nomer_soal) {
    var status_ragu = $("#rg_" + nomer_soal).val();

    if (status_ragu == "N") {
        $(".ragu_ragu > span").html("Ragu");
    } else {
        $(".ragu_ragu > span").html("Tidak Ragu");
    }
}

function cek_terakhir(id_soal) {
    var jml_soal = $("#jml_soal").val();
    jml_soal = parseInt(jml_soal) - 1;

    if (jml_soal === id_soal) {
        $(".next").hide();
        $(".back").show();
        // $(".selesai").show(); // DI-HIDDEN DIGANTI TOMBOL AKHIRI
    } else {
        if (1 == id_soal) {
            $(".next").show();
            $(".back").hide();
        } else {
            $(".next").show();
            $(".back").show();
        }
        // $(".selesai").hide();
    }
}

function simpan_view() {
    check_isian_essay();

    let f_asal = $("#ujian");
    let form = getFormData(f_asal);
    //form = JSON.stringify(form);
    let jml_soal = form.jml_soal;
    jml_soal = parseInt(jml_soal);

    // let hasil_jawaban = '<div class="btn-group" role="group" aria-label="">';
    let hasil_jawaban = "<div >";
    let count_jml_soal_per_topik = [];
    let label_soal = 1;
    for (let i = 1; i < jml_soal; i++) {
        let ragu = form["rg_" + i];
        let topik_id = form["topik_id_" + i];
        let tipe_soal = form["tipe_soal_" + i];

        let jawab = null;
        if (tipe_soal == tipe_soal_mcsa) {
            jawab = form["opsi_" + i];
        } else if (tipe_soal == tipe_soal_mcma) {
            jawab = form["opsi_" + i + "[]"];
        } else if (tipe_soal == tipe_soal_essay) {
            jawab = form["jawaban_essay_before_" + i];
        }

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

        // console.log('jawab, i : ', i, jawab);
        // console.log('ragu, i : ', i, ragu);

        if (jawab != undefined) {
            if (ragu == "Y") {
                if (jawab == "-" || jawab == "") {
                    hasil_jawaban +=
                        '<button type="button" id="btn_soal_' +
                        i +
                        '" class="class_topik_id_' +
                        topik_id +
                        ' btn btn-outline-primary btn_soal" style="padding: 5px; font-size:10px; margin-right:5px; margin-bottom: 8px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' +
                        i +
                        ');">' +
                        label_soal +
                        "</button>";
                } else {
                    hasil_jawaban +=
                        '<button type="button" id="btn_soal_' +
                        i +
                        '" class="class_topik_id_' +
                        topik_id +
                        ' btn btn-warning btn_soal" style="padding: 5px; font-size:10px; margin-right:5px; margin-bottom: 8px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' +
                        i +
                        ');">' +
                        label_soal +
                        "</button>";
                }
            } else {
                if (jawab == "-" || jawab == "") {
                    hasil_jawaban +=
                        '<button type="button" id="btn_soal_' +
                        i +
                        '" class="class_topik_id_' +
                        topik_id +
                        ' btn btn-outline-primary btn_soal" style="padding: 5px; font-size:10px; margin-right:5px; margin-bottom: 8px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' +
                        i +
                        ');">' +
                        label_soal +
                        "</button>";
                } else {
                    hasil_jawaban +=
                        '<button type="button" id="btn_soal_' +
                        i +
                        '" class="class_topik_id_' +
                        topik_id +
                        ' btn btn-success btn_soal" style="padding: 5px; font-size:10px; margin-right:5px; margin-bottom: 8px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' +
                        i +
                        ');">' +
                        label_soal +
                        "</button>";
                }
            }
        } else {
            hasil_jawaban +=
                '<button type="button" id="btn_soal_' +
                i +
                '" class="class_topik_id_' +
                topik_id +
                ' btn btn-outline-primary btn_soal" style="padding: 5px; font-size:10px; margin-right:5px; margin-bottom: 8px; border-radius: 0; border-color: #967adc !important" onclick="return buka(' +
                i +
                ');">' +
                label_soal +
                "</button>";
        }

        if (label_soal == topik[topik_id]) {
            label_soal = 0;
        }

        label_soal = label_soal + 1;
    }

    hasil_jawaban += "</div>";
    $("#tampil_jawaban").html('<div id="yes">' + hasil_jawaban + "</div>");

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
            sid: sid,
            answer: answer,
            id: id_ujian,
            key: key,
            ragu: ragu,
            waktu_buka_soal: waktu_buka_soal,
            waktu_jawab_soal: moment().format("YYYY-MM-DD HH:mm:ss"),
        },
        dataType: "json",
        success: function (data) {
            let urutan_soal = $("#opsi_a_" + sid).attr("rel"); // AMBIL SALAH SATU ABJAD, contoh : A
            let next_soal_id = parseInt(urutan_soal) + 1;

            let topik_id_next = $("#topik_id_" + next_soal_id).val();
            let valid_next = false;
            if (is_sekuen_topik) {
                if (topik_id_next == topik_aktif) {
                    valid_next = true;
                }
            } else {
                valid_next = true;
            }

            let sudah_akhir = next_soal_id > total_widget ? 1 : 0;
            if (!sudah_akhir && valid_next && go_next) {
                // next(next_soal_id); // UNTUK MENUJU SOAL SELANJUTNYA JIKA SUDAH MENJAWAB
            }
        },
        error: function () {
            Swal.fire({
                title: "Perhatian",
                text: "Terjadi kesalahan, halaman akan di-reload",
                icon: "warning",
                confirmButtonColor: "#37bc9b",
                confirmButtonText: "Reload",
            }).then((result) => {
                if (result.value) {
                    // location.href = base_url + "ujian/list";
                    location.reload();
                }
            });
        },
        complete: function () {
            ajx_overlay(false);
        },
    });
}

function simpan_akhir() {
    if (is_sekuen_topik) {
        if (last_topik_id != topik_aktif) {
            Swal.fire({
                title: "Perhatian",
                text: "Anda belum bisa untuk mengakhiri ujian saat ini",
                icon: "warning",
            });
            return;
        }
    }
    Swal.fire({
        title: "Akhiri Ujian",
        text: "Ujian yang sudah diakhiri tidak dapat diulangi.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#37bc9b",
        cancelButtonColor: "#f6bb42",
        confirmButtonText: "Akhiri",
    }).then((result) => {
        if (result.value) {
            selesai();
        }
    });
}

$(document).on("click", 'input[type="radio"]', function () {
    if ($(this).prop("checked", true)) {
        let sid = $(this).data("sid");
        let answer = $(this).val();
        let id_step = $(this).attr("rel");
        let ragu = $("#rg_" + id_step).val();
        simpan_jawaban_satu(sid, answer, ragu);
        $(".ragu_ragu").show();
        simpan_view();
    }
});

$(document).on("click", 'input[type="checkbox"]', function () {
    let answer = [];
    // if($(this).prop("checked", true)){
    //     let val = $(this).val();
    //     const index = answer.indexOf(val);
    //     if (index > -1) {
    //       answer.splice(index, 1);
    //     }
    //     answer.push(val);
    // }else{
    //     let val = $(this).val();
    //     const index = answer.indexOf(val);
    //     if (index > -1) {
    //       answer.splice(index, 1);
    //     }
    // }

    let sid = $(this).data("sid");
    let id_step = $(this).attr("rel");
    let ragu = $("#rg_" + id_step).val();
    let selection_els = $('input[name="opsi_' + id_step + '[]"]:checked');
    selection_els.each(function (i, el) {
        let val = $(el).val();
        answer.push(val);
    });
    simpan_jawaban_satu(sid, JSON.stringify(answer), ragu);
    if (answer.length) $(".ragu_ragu").show();
    else $(".ragu_ragu").hide();
    simpan_view();
});

$(document).on("click", ".btn_simpan_essay", function () {
    let sid = $(this).data("sid");
    let nomer_soal = $(this).attr("rel");
    if (!$("#opsi_a_" + sid).summernote("isEmpty")) {
        let answer = $("#opsi_a_" + sid).val();
        let id_step = $(this).attr("rel");
        let ragu = $("#rg_" + id_step).val();
        simpan_jawaban_satu(sid, answer, ragu);
        // $('input[name="flag_check_jawaban_essay_'+ nomer_soal +'"]').val('Y');
        $('input[name="jawaban_essay_before_' + nomer_soal + '"]').val(answer);
        $("#label_essay_belum_disimpan_" + nomer_soal).hide();
        $(".ragu_ragu").show();
        simpan_view();
    } else {
        $("#opsi_a_" + sid).summernote("code", "");
    }
});

$(document).on("click", ".btn_clear_essay", function () {
    let sid = $(this).data("sid");
    let nomer_soal = $(this).attr("rel");
    if (!$("#opsi_a_" + sid).summernote("isEmpty")) {
        Swal.fire({
            title: "Hapus isian",
            text: "Isian yang dihapus tdk dapat dikembalikan",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#37bc9b",
            cancelButtonColor: "#f6bb42",
            confirmButtonText: "Hapus",
        }).then((result) => {
            if (result.value) {
                $("#opsi_a_" + sid).summernote("code", "");
                $("#opsi_a_" + sid).val("");
                let answer = "";
                // let ragu = $("#rg_" + nomer_soal).val() ;
                simpan_jawaban_satu(sid, "", "N");
                $("#rg_" + nomer_soal).val("N");
                // $('input[name="flag_check_jawaban_essay_'+ nomer_soal +'"]').val('N');
                $('input[name="jawaban_essay_before_' + nomer_soal + '"]').val(
                    answer
                );
                $("#label_essay_belum_disimpan_" + nomer_soal).hide();
                $(".ragu_ragu").hide();
                cek_status_ragu(nomer_soal);
                simpan_view();
            }
        });
    }
});

$(document).on("click", ".btn_revert_essay", function () {
    let sid = $(this).data("sid");
    let nomer_soal = $(this).attr("rel");
    Swal.fire({
        title: "Revert isian",
        text: "Isian yang diubah tdk dapat dikembalikan",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#37bc9b",
        cancelButtonColor: "#f6bb42",
        confirmButtonText: "Revert",
    }).then((result) => {
        if (result.value) {
            let jawaban_essay_before = $(
                'input[name="jawaban_essay_before_' + nomer_soal + '"]'
            ).val();
            $("#opsi_a_" + sid).summernote("code", jawaban_essay_before);
            check_isian_essay();
        }
    });
});
