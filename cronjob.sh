#!/bin/bash
# Gunakan direktori script agar jalan dari cron (cron pwd bukan project root)
cur_dir="$(cd "$(dirname "$0")" && pwd)"
php "${cur_dir}/public/index.php" "pub/cron_auto_close" >> "${cur_dir}/log/log_cron_auto_close.log"
php "${cur_dir}/public/index.php" "pub/cron_auto_start_ujian_for_unstarted_participants" >> "${cur_dir}/log/log_cron_auto_start_ujian_for_unstarted_participants.log"
php "${cur_dir}/public/index.php" "auth/cron_auto_registrasi" >> "${cur_dir}/log/log_cron_auto_registrasi.log"
php "${cur_dir}/public/index.php" "pub/fix_detail_nilai" >> "${cur_dir}/log/log_fix_detail_nilai.log"