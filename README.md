# UJOL - Ujian Online

Aplikasi Ujian Online Menggunakan CodeIgniter (PHP)

<h1>Catatan</h1>
<p>Disarankan upgrade PHP ke versi terbaru (7.3 atau lebih tinggi)</p>

<h3>Langkah setup aplikasi ujian</h3>
<ol>
<li>siapkan domain (dengan ssl lebih bagus)</li>
<li>siapkan database</li>
<li>git clone</li>
<li>copy file phoenix.php dan constants_depend_app.php</li>
<li>npm install</li>

<li>composer install</li>
<li>migrate db</li>
<li>buat symlink dari node_module didalam dir writable/npm/node_modules ke public/assets/npm/node_modules</li>

<li>buat dir application/cache agar bisa diaccess (writable)</li>
<li>buat dir upload/import agar bisa diaccess (writable)</li>
<li>buat dir upload/img_soal agar bisa diaccess (writable)</li>
<li>buat dir log/* agar bisa diaccess (writable)</li>

<li>membuat cron cronjob.sh</li>

<li>membuat emailer (jika membutuhkan)</li>
<li>jika time tryout silahkan buat akun recaptcha v3 untuk kebutuhan proteksi di registrasi peserta</li>
</ul>

<h3>Script bash yg dijalankan</h3>
<ol>
<li>php "$(pwd)/vendor/bin/phoenix" "migrate"</li>
<li>ln -s "$(pwd)/writable/npm/node_modules" "$(pwd)/public/assets/npm"</li>
<li>php "public/index.php" "pub/generate_data_daerah"</li>
</ol>

<h3>User</h3>
<ul>
<li>Username : </li>
<li>Password : </li>
</ul>

<h3>Log</h3>
<ul>
<li>Meneruskan koding di file ujian . untuk bisa akhiri dan mengenerate hasil ujain skala 100, <- DONE</li>
</ul>

<h3>Todo</h3>
<ul>
<li>Hasil ujian : detail_nilai masih di generate pakai cron (url : pub/fix_detail_nilai)</li>
<li>Untuk ujian model bundle tapi di grouping by materi belum selesai di develop (search text : GROUPING BY MATERI)</li>
</ul>
