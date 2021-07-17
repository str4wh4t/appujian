<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=". $nama_file .".xls");

$mulai = strftime('%A, %d %B %Y %H:%M:%S', strtotime($ujian->tgl_mulai));
$selesai = empty($ujian->terlambat) ? '-' : strftime('%A, %d %B %Y %H:%M:%S', strtotime($ujian->terlambat));

$t = [];
foreach($ujian->topik AS $topik){
    $t[] = $topik->nama_topik;
    $t = array_unique($t);
}
$str_topik = implode(',', $t);

$min_nilai = number_format($nilai->min_nilai,2,'.', '') ;
$max_nilai = number_format($nilai->max_nilai,2,'.', '') ;
$rata_rata_ujian = number_format($nilai->avg_nilai,2,'.', '') ;
?>
<table border="1">
    <tr>
        <td><b>Topik</b></td>
        <td><?= $str_topik ?></td>
        <td><b>Jadwal Selesai Ujian</b></td>
        <td><?= $selesai ?></td>
    </tr>
    <tr>
        <td><b>Jml Soal</b></td>
        <td><?= $ujian->jumlah_soal ?> soal</td>
        <td><b>Nilai Tertinggi</b></td>
        <td><?= $max_nilai ?></td>
    </tr>
    <tr>
        <td><b>Waktu</b></td>
        <td><?= $ujian->waktu ?> menit</td>
        <td><b>Nilai Terendah</b></td>
        <td><?= $min_nilai ?></td>
    </tr>
    <tr>
        <td><b>Jadwal Mulai Ujian</b></td>
        <td><?= $mulai ?></td>
        <td><b>Rata-rata Nilai</b></td>
        <td><?= $rata_rata_ujian ?></td>
    </tr>
    <tr>
        <td >&nbsp;</td>
    </tr>
    <tr>
        <td rowspan="2" ><b>No.</b></td>
        <td rowspan="2" ><b>No Peserta</b></td>
        <td rowspan="2" ><b>Nama</b></td>
        <td rowspan="2" ><b>Kehadiran</b></td>
        <td rowspan="2" ><b>Absen Oleh</b></td>
        <td colspan="4" ><b>Bapu</b></td>
        <?php if(SHOW_DETAIL_HASIL): ?>
        <?php foreach($ujian->topik AS $topik): ?>
            <td rowspan="2"><b><?= $topik->nama_topik ?></b></td>
        <?php endforeach; ?>
        <?php endif; ?>
        <td rowspan="2" ><b>Bobot</b></td>
        <td rowspan="2" ><b>Nilai</b></td>
    </tr>
    <tr>
        <td ><b>Terlihat Pada Layar</b></td>
        <td ><b>Perjokian</b></td>
        <td ><b>Sering Buka Laman Lain</b></td>
        <td ><b>Catatan</b></td>
    </tr>
    <?php
    $no = 1;
    foreach($hasil as $row) {
        // $nilai_bobot_benar = number_format($row['nilai_bobot_benar'] / 3,2,'.', '') ;
        $nilai_bobot_benar = number_format($row['nilai_bobot_benar'],2,'.', '') ;
        $hasil = number_format($row['nilai'],2,'.', '') ;
    ?>

    <tr>
        <td ><?= $no ?></td>
        <td style='mso-number-format:"\@"'><?= $row['nim'] ?></td>
        <td ><?= $row['nama'] ?></td>
        <td ><?= $row['absensi'] ?></td>
        <td ><?= $row['absensi_oleh'] ?></td>
        <td ><?= $row['is_terlihat_pada_layar'] ?></td>
        <td ><?= $row['is_perjokian'] ?></td>
        <td ><?= $row['is_sering_buka_page_lain'] ?></td>
        <td ><?= $row['catatan_pengawas'] ?></td>
        <?php if(SHOW_DETAIL_HASIL): ?>
        <?php foreach($ujian->topik AS $topik): ?>
            <td><?= $row['detail_bobot_benar'][$topik->id] ?></td>
        <?php endforeach; ?>
        <?php endif; ?>
        <td ><?= $nilai_bobot_benar ?></td>
        <td ><?= $hasil ?></td>
    </tr>
    <?php $no++; } ?>
</table>