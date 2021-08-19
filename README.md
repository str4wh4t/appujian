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
<li>yarn install</li>

<li>composer install</li>
<li>migrate db</li>
<ul>buat symlink dari node_module didalam dir writeable/yarn/node_modules ke public/assets/yarn/<li>node_modules</li>

<li>buat dir application/cache agar bisa diaccess (writeable)</li>
<li>buat dir upload/import agar bisa diaccess (writeable)</li>
<li>buat dir upload/img_soal agar bisa diaccess (writeable)</li>
<li>buat dir log/* agar bisa diaccess (writeable)</li>

<li>membuat cron cronjob.sh</li>

<li>membuat emailer (jika membutuhkan)</li>
<li>jika time tryout silahkan buat akin recaptcha v3 untuk kebutuhan proteksi di registrasi peserta</li>
</ul>

<h3>User</h3>
<ul>
<li>Username : </li>
<li>Password : </li>
</ul>