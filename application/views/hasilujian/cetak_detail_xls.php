<?php
$mulai = indo_date(strftime('%A, %d %B %Y %H:%M:%S', strtotime($ujian->tgl_mulai)));
$selesai = empty($ujian->terlambat) ? '-' : indo_date(strftime('%A, %d %B %Y %H:%M:%S', strtotime($ujian->terlambat)));

$t = [];

$ujian_topik = $ujian->topik()->groupBy('id')->get();
foreach ($ujian_topik as $topik) {
    $t[] = $topik->nama_topik;
    $t = array_unique($t);
}
$str_topik = implode(',', $t);

$min_nilai = number_format((float) ($nilai->min_nilai ?? 0), 2, '.', '');
$max_nilai = number_format((float) ($nilai->max_nilai ?? 0), 2, '.', '');
$rata_rata_ujian = number_format((float) ($nilai->avg_nilai ?? 0), 2, '.', '');
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
        <td rowspan="2" ><b>Absen By</b></td>
        <td rowspan="2" ><b>Start/End By</b></td>
        <td colspan="4" ><b>Bapu</b></td>
        <?php if (is_show_detail_hasil()): ?>
        <?php foreach ($ujian_topik as $topik): ?>
            <td rowspan="2"><b><?= $topik->nama_topik ?></b></td>
        <?php endforeach; ?>
        <?php endif; ?>
        <td colspan="2" ><b>Waktu Mengerjakan</b></td>
        <td rowspan="2" ><b>Lama Pengerjaan<br>(Menit)</b></td>
        <td rowspan="2" ><b>Bobot</b></td>
        <td rowspan="2" ><b>Nilai</b></td>
    </tr>
    <tr>
        <td ><b>Terlihat Pada Layar</b></td>
        <td ><b>Perjokian</b></td>
        <td ><b>Sering Buka Laman Lain</b></td>
        <td ><b>Catatan</b></td>
        <td ><b>Mulai</b></td>
        <td ><b>Selesai</b></td>
    </tr>
    <?php
    $no = 1;
foreach ($hasil as $row) {
    // $nilai_bobot_benar = number_format($row['nilai_bobot_benar'] / 3,2,'.', '') ;
    $nilai_bobot_benar = number_format($row['nilai_bobot_benar'], 2, '.', '');
    $hasil = number_format($row['nilai'], 2, '.', ''); ?>

    <tr>
        <td ><?= $no ?></td>
        <td style='mso-number-format:"\@"'><?= $row['nim'] ?></td>
        <td ><?= $row['nama'] ?></td>
        <td ><?= $row['absensi'] ?></td>
        <td ><?= $row['absensi_oleh'] ?></td>
        <td ><?= $row['start_end_by'] ?></td>
        <td ><?= $row['is_terlihat_pada_layar'] ?></td>
        <td ><?= $row['is_perjokian'] ?></td>
        <td ><?= $row['is_sering_buka_page_lain'] ?></td>
        <td ><?= $row['catatan_pengawas'] ?></td>
        <?php if (is_show_detail_hasil()): ?>
        <?php foreach ($ujian_topik as $topik): ?>
            <td><?php
                echo $row['detail_nilai'][$topik->id] ?? ''; ?></td>
        <?php endforeach; ?>
        <?php endif; ?>
        <td ><?= $row['waktu_mulai']?></td>
        <td ><?= $row['waktu_selesai'] ?></td>
        <td ><?= $row['lama_pengerjaan'] ?></td>
        <td ><?= $nilai_bobot_benar ?></td>
        <td ><?= $hasil ?></td>
    </tr>
    <?php $no++;
} ?>
</table>