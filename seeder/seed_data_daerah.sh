#!/bin/sh
# Seeder: memanggil Pub::generate_data_daerah() untuk mengisi tabel data_daerah dari API
# Dijalankan dari root project (atau dengan path lengkap ke public)
cd "$(dirname "$0")/../public" && php index.php pub generate_data_daerah
