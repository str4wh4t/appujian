CREATE DATABASE `tryout_01` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

-- tryout_01.bobot_soal definition

CREATE TABLE `bobot_soal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bobot` varchar(250) NOT NULL,
  `nilai` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.`groups` definition

CREATE TABLE `groups` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.jurusan definition

CREATE TABLE `jurusan` (
  `id_jurusan` int NOT NULL AUTO_INCREMENT,
  `nama_jurusan` varchar(30) NOT NULL,
  PRIMARY KEY (`id_jurusan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.kelas definition

CREATE TABLE `kelas` (
  `id_kelas` int NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(30) NOT NULL,
  `jurusan_id` int NOT NULL,
  PRIMARY KEY (`id_kelas`),
  KEY `jurusan_id` (`jurusan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.login_attempts definition

CREATE TABLE `login_attempts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.login_log definition

CREATE TABLE `login_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int NOT NULL,
  `status_login` tinyint NOT NULL COMMENT '1 : online , 0 : offline',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.matkul definition

CREATE TABLE `matkul` (
  `id_matkul` int NOT NULL AUTO_INCREMENT,
  `nama_matkul` varchar(50) NOT NULL,
  PRIMARY KEY (`id_matkul`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.membership definition

CREATE TABLE `membership` (
  `id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `urut` tinyint NOT NULL DEFAULT '0',
  `price` bigint NOT NULL,
  `delete_price` bigint DEFAULT NULL,
  `discount` tinyint DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_general_ci,
  `show` tinyint NOT NULL DEFAULT '1' COMMENT '0 : hide, 1 : show',
  `text_color` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'primary',
  `durasi` tinyint NOT NULL DEFAULT '0' COMMENT 'dalam bulan',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.paket definition

CREATE TABLE `paket` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `urut` tinyint NOT NULL DEFAULT '0',
  `price` bigint NOT NULL,
  `delete_price` bigint DEFAULT NULL,
  `discount` tinyint DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `show` tinyint NOT NULL DEFAULT '1' COMMENT '0 : hide, 1 : show',
  `text_color` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'primary',
  `kuota_latihan_soal` tinyint NOT NULL DEFAULT '0' COMMENT 'brp x latihan soal',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.setting definition

CREATE TABLE `setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `variabel` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nilai` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `flag` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.trx_midtrans definition

CREATE TABLE `trx_midtrans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_time` datetime DEFAULT NULL,
  `status_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `order_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fraud_status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gross_amount` decimal(13,2) NOT NULL,
  `signature_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `signature_key_check` tinyint DEFAULT '0',
  `is_settlement_processed` tinyint NOT NULL DEFAULT '0',
  `is_capture_processed` tinyint NOT NULL DEFAULT '0',
  `is_expire_processed` tinyint NOT NULL DEFAULT '0',
  `log_status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.users definition

CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tgl_lahir` char(8) DEFAULT NULL,
  `no_billkey` varchar(50) DEFAULT NULL,
  `activation_selector` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `forgotten_password_selector` varchar(255) DEFAULT NULL,
  `forgotten_password_code` varchar(255) DEFAULT NULL,
  `forgotten_password_time` int unsigned DEFAULT NULL,
  `remember_selector` varchar(255) DEFAULT NULL,
  `remember_code` varchar(255) DEFAULT NULL,
  `created_on` int unsigned NOT NULL,
  `last_login` int unsigned DEFAULT NULL,
  `active` tinyint unsigned DEFAULT NULL,
  `first_name` varchar(250) DEFAULT NULL,
  `last_name` varchar(250) DEFAULT NULL,
  `full_name` varchar(250) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_online` tinyint DEFAULT '0' COMMENT '0 : offline , 1 : online',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `uc_activation_selector` (`activation_selector`),
  UNIQUE KEY `uc_forgotten_password_selector` (`forgotten_password_selector`),
  UNIQUE KEY `uc_remember_selector` (`remember_selector`),
  UNIQUE KEY `no_billkey` (`no_billkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.dosen definition

CREATE TABLE `dosen` (
  `id_dosen` int NOT NULL AUTO_INCREMENT,
  `nip` char(25) NOT NULL,
  `nama_dosen` varchar(50) NOT NULL,
  `email` varchar(254) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `matkul_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_dosen`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `nip` (`nip`),
  KEY `matkul_id` (`matkul_id`),
  CONSTRAINT `dosen_ibfk_1` FOREIGN KEY (`matkul_id`) REFERENCES `matkul` (`id_matkul`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- tryout_01.dosen_matkul definition

CREATE TABLE `dosen_matkul` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `dosen_id` int NOT NULL,
  `matkul_id` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_dosen_matkul_matkul` (`matkul_id`),
  KEY `FK_dosen_matkul_dosen` (`dosen_id`),
  CONSTRAINT `FK_dosen_matkul_dosen` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id_dosen`) ON DELETE CASCADE,
  CONSTRAINT `FK_dosen_matkul_matkul` FOREIGN KEY (`matkul_id`) REFERENCES `matkul` (`id_matkul`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- tryout_01.jurusan_matkul definition

CREATE TABLE `jurusan_matkul` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matkul_id` int NOT NULL,
  `jurusan_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jurusan_id` (`jurusan_id`),
  KEY `matkul_id` (`matkul_id`),
  CONSTRAINT `jurusan_matkul_ibfk_1` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id_jurusan`),
  CONSTRAINT `jurusan_matkul_ibfk_2` FOREIGN KEY (`matkul_id`) REFERENCES `matkul` (`id_matkul`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.kelas_dosen definition

CREATE TABLE `kelas_dosen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kelas_id` int NOT NULL,
  `dosen_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kelas_id` (`kelas_id`),
  KEY `dosen_id` (`dosen_id`),
  CONSTRAINT `kelas_dosen_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id_dosen`),
  CONSTRAINT `kelas_dosen_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id_kelas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.m_ujian definition

CREATE TABLE `m_ujian` (
  `id_ujian` int NOT NULL AUTO_INCREMENT,
  `dosen_id` int DEFAULT NULL,
  `matkul_id` int NOT NULL,
  `nama_ujian` varchar(200) NOT NULL,
  `jumlah_soal` int NOT NULL,
  `jumlah_soal_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `waktu` int NOT NULL,
  `jenis` enum('acak','urut') NOT NULL,
  `jenis_jawaban` enum('acak','urut') NOT NULL,
  `tgl_mulai` datetime NOT NULL,
  `terlambat` datetime NOT NULL,
  `pakai_token` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 : tanpa token ; 1 : dengan token',
  `token` varchar(5) NOT NULL,
  `status_ujian` tinyint(1) NOT NULL COMMENT '0 : tidak aktif ; 1 : aktif',
  `tampilkan_hasil` tinyint(1) NOT NULL COMMENT '0 : tidak ; 1 : iya',
  `masa_berlaku_sert` tinyint(1) NOT NULL COMMENT 'masa berlaku sertifikat dalam satuan waktu',
  `tampilkan_jawaban` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 : tidak ; 1 : iya',
  `soal_gel` tinyint(1) DEFAULT NULL,
  `soal_smt` tinyint(1) DEFAULT NULL,
  `soal_tahun` year DEFAULT NULL,
  `repeatable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 : tidak ; 1 : iya',
  `is_sekuen_topik` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 : tidak ; 1 : iya',
  `urutan_topik` json DEFAULT NULL,
  `mhs_kelompok_ujian` tinyint(1) DEFAULT NULL,
  `mhs_tgl_ujian` datetime DEFAULT NULL,
  `mhs_tahun` year DEFAULT NULL,
  `created_by` varchar(100) NOT NULL COMMENT 'berisi username dari tabel users',
  `updated_by` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_ujian`),
  KEY `matkul_id` (`matkul_id`),
  KEY `dosen_id` (`dosen_id`),
  CONSTRAINT `m_ujian_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id_dosen`),
  CONSTRAINT `m_ujian_ibfk_2` FOREIGN KEY (`matkul_id`) REFERENCES `matkul` (`id_matkul`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.mahasiswa definition

CREATE TABLE `mahasiswa` (
  `id_mahasiswa` int unsigned NOT NULL,
  `nama` varchar(250) NOT NULL,
  `nik` varchar(50) DEFAULT NULL,
  `nim` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(250) NOT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `foto` text,
  `tmp_lahir` varchar(250) NOT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `kota_asal` varchar(255) DEFAULT NULL,
  `kelas_id` int DEFAULT NULL COMMENT 'kelas&jurusan',
  `kodeps` int DEFAULT NULL,
  `prodi` varchar(250) DEFAULT NULL,
  `no_billkey` varchar(20) DEFAULT NULL,
  `jalur` varchar(250) DEFAULT NULL,
  `gel` tinyint DEFAULT NULL,
  `smt` tinyint DEFAULT NULL,
  `tahun` year DEFAULT NULL,
  `kelompok_ujian` tinyint NOT NULL DEFAULT '0',
  `tgl_ujian` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_mahasiswa`),
  UNIQUE KEY `nim` (`nim`),
  UNIQUE KEY `no_billkey` (`no_billkey`),
  KEY `kelas_id` (`kelas_id`),
  CONSTRAINT `mahasiswa_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id_kelas`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- tryout_01.mahasiswa_matkul definition

CREATE TABLE `mahasiswa_matkul` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int unsigned NOT NULL,
  `matkul_id` int NOT NULL,
  `sisa_kuota_latihan_soal` int DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 4` (`mahasiswa_id`,`matkul_id`),
  KEY `FK_mahasiswa_matkul_matkul` (`matkul_id`),
  KEY `mahasiswa_id` (`mahasiswa_id`),
  CONSTRAINT `FK_mahasiswa_matkul_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE,
  CONSTRAINT `FK_mahasiswa_matkul_matkul` FOREIGN KEY (`matkul_id`) REFERENCES `matkul` (`id_matkul`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- tryout_01.mahasiswa_ujian definition

CREATE TABLE `mahasiswa_ujian` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mahasiswa_matkul_id` int unsigned NOT NULL,
  `ujian_id` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 4` (`mahasiswa_matkul_id`,`ujian_id`),
  KEY `mahasiswa_matkul_id` (`mahasiswa_matkul_id`),
  KEY `FK_mahasiswa_ujian_m_ujian` (`ujian_id`),
  CONSTRAINT `FK_mahasiswa_ujian_m_ujian` FOREIGN KEY (`ujian_id`) REFERENCES `m_ujian` (`id_ujian`) ON DELETE CASCADE,
  CONSTRAINT `FK_mahasiswa_ujian_mahasiswa_matkul` FOREIGN KEY (`mahasiswa_matkul_id`) REFERENCES `mahasiswa_matkul` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.membership_history definition

CREATE TABLE `membership_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int unsigned NOT NULL,
  `membership_id` int NOT NULL,
  `upgrade_ke` int NOT NULL,
  `expired_at` datetime NOT NULL,
  `keterangan` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stts` tinyint NOT NULL COMMENT '0 : tidak atif , 1 : aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `membership_history_UN` (`mahasiswa_id`,`membership_id`,`upgrade_ke`),
  KEY `membership_history_FK_1` (`membership_id`),
  CONSTRAINT `membership_history_FK` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id_mahasiswa`),
  CONSTRAINT `membership_history_FK_1` FOREIGN KEY (`membership_id`) REFERENCES `membership` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.paket_history definition

CREATE TABLE `paket_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int unsigned NOT NULL,
  `upgrade_ke` int NOT NULL,
  `paket_id` int NOT NULL,
  `keterangan` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stts` tinyint NOT NULL COMMENT '0 : tidak atif , 1 : aktif',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paket_history_UN` (`mahasiswa_id`,`upgrade_ke`,`paket_id`),
  KEY `membership_history_FK` (`mahasiswa_id`) USING BTREE,
  KEY `membership_history_FK_1` (`paket_id`) USING BTREE,
  CONSTRAINT `paket_history_FK` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `paket_history_FK_1` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.paket_matkul definition

CREATE TABLE `paket_matkul` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `paket_id` int NOT NULL,
  `matkul_id` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paket_matkul_UN` (`paket_id`,`matkul_id`),
  KEY `paket_matkul_FK_1` (`matkul_id`),
  CONSTRAINT `paket_matkul_FK` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `paket_matkul_FK_1` FOREIGN KEY (`matkul_id`) REFERENCES `matkul` (`id_matkul`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.topik definition

CREATE TABLE `topik` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matkul_id` int NOT NULL,
  `nama_topik` varchar(250) NOT NULL,
  `poin_topik` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `created_by` varchar(100) DEFAULT NULL COMMENT 'berisi username dari tabel user',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_topik_matkul` (`matkul_id`),
  CONSTRAINT `FK_topik_matkul` FOREIGN KEY (`matkul_id`) REFERENCES `matkul` (`id_matkul`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- tryout_01.topik_ujian definition

CREATE TABLE `topik_ujian` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `topik_id` int NOT NULL,
  `ujian_id` int NOT NULL,
  `bobot_soal_id` int NOT NULL,
  `jumlah_soal` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 5` (`topik_id`,`ujian_id`,`bobot_soal_id`),
  KEY `FK_topik_ujian_topik` (`topik_id`),
  KEY `FK_topik_ujian_m_ujian` (`ujian_id`),
  KEY `FK_topik_ujian_bobot_soal` (`bobot_soal_id`),
  CONSTRAINT `FK_topik_ujian_bobot_soal` FOREIGN KEY (`bobot_soal_id`) REFERENCES `bobot_soal` (`id`),
  CONSTRAINT `FK_topik_ujian_m_ujian` FOREIGN KEY (`ujian_id`) REFERENCES `m_ujian` (`id_ujian`) ON DELETE CASCADE,
  CONSTRAINT `FK_topik_ujian_topik` FOREIGN KEY (`topik_id`) REFERENCES `topik` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- tryout_01.trx_payment definition

CREATE TABLE `trx_payment` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int unsigned NOT NULL,
  `order_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `stts` tinyint NOT NULL DEFAULT '0',
  `membership_history_id` bigint unsigned DEFAULT NULL COMMENT 'jika trx untuk pembelian membership',
  `paket_history_id` bigint unsigned DEFAULT NULL,
  `keterangan` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tgl_order` datetime DEFAULT NULL,
  `tgl_bayar` datetime DEFAULT NULL,
  `jml_bayar` decimal(13,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trx_payment_UN` (`order_number`),
  KEY `trx_payment_FK_1` (`membership_history_id`),
  KEY `trx_payment_FK` (`mahasiswa_id`),
  CONSTRAINT `trx_payment_FK` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE RESTRICT,
  CONSTRAINT `trx_payment_FK_1` FOREIGN KEY (`membership_history_id`) REFERENCES `membership_history` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.users_groups definition

CREATE TABLE `users_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `group_id` mediumint unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`),
  CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.daftar_hadir definition

CREATE TABLE `daftar_hadir` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mahasiswa_ujian_id` int unsigned NOT NULL,
  `absen_by` int unsigned NOT NULL COMMENT 'pengawas_id dari users_groups',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mahasiswa_ujian_id_unik` (`mahasiswa_ujian_id`),
  KEY `FK_daftar_hadir_users_groups` (`absen_by`),
  KEY `mahasiswa_ujian_id` (`mahasiswa_ujian_id`),
  CONSTRAINT `FK_daftar_hadir_mahasiswa_ujian` FOREIGN KEY (`mahasiswa_ujian_id`) REFERENCES `mahasiswa_ujian` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_daftar_hadir_users_groups` FOREIGN KEY (`absen_by`) REFERENCES `users_groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.h_ujian definition

CREATE TABLE `h_ujian` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ujian_id` int NOT NULL,
  `mahasiswa_id` int unsigned NOT NULL,
  `mahasiswa_ujian_id` int unsigned NOT NULL,
  `list_soal` longtext,
  `list_jawaban` longtext COMMENT 'Y : ragu , N : tidak ragu',
  `jml_soal` int NOT NULL,
  `jml_benar` int NOT NULL,
  `jml_salah` int NOT NULL,
  `nilai` decimal(10,2) NOT NULL,
  `nilai_bobot` decimal(10,2) NOT NULL,
  `nilai_bobot_benar` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_bobot` decimal(10,2) NOT NULL DEFAULT '0.00',
  `detail_bobot_benar` text,
  `tgl_mulai` datetime NOT NULL,
  `tgl_selesai` datetime NOT NULL,
  `ujian_selesai` enum('Y','N') NOT NULL COMMENT 'Y : ujian diakhiri ,  N : ujian belum diakhiri',
  `ended_by` varchar(50) DEFAULT NULL,
  `fixed_nilai` tinyint NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 4` (`ujian_id`,`mahasiswa_id`),
  UNIQUE KEY `mahasiswa_ujian_id` (`mahasiswa_ujian_id`),
  KEY `ujian_id` (`ujian_id`),
  KEY `FK_h_ujian_mahasiswa` (`mahasiswa_id`),
  KEY `fk_mahasiswa_ujian_id` (`mahasiswa_ujian_id`),
  CONSTRAINT `FK_h_ujian_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id_mahasiswa`),
  CONSTRAINT `FK_h_ujian_mahasiswa_ujian` FOREIGN KEY (`mahasiswa_ujian_id`) REFERENCES `mahasiswa_ujian` (`id`),
  CONSTRAINT `h_ujian_ibfk_1` FOREIGN KEY (`ujian_id`) REFERENCES `m_ujian` (`id_ujian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.h_ujian_deleted definition

CREATE TABLE `h_ujian_deleted` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ujian_id` int NOT NULL,
  `mahasiswa_id` int unsigned NOT NULL,
  `mahasiswa_ujian_id` int unsigned DEFAULT NULL,
  `list_soal` longtext,
  `list_jawaban` longtext COMMENT 'Y : ragu , N : tidak ragu',
  `jml_soal` int NOT NULL,
  `jml_benar` int NOT NULL,
  `jml_salah` int NOT NULL,
  `nilai` decimal(10,2) NOT NULL,
  `nilai_bobot` decimal(10,2) NOT NULL,
  `nilai_bobot_benar` int NOT NULL,
  `total_bobot` int NOT NULL,
  `detail_bobot_benar` text,
  `tgl_mulai` datetime NOT NULL,
  `tgl_selesai` datetime NOT NULL,
  `ujian_selesai` enum('Y','N') NOT NULL COMMENT 'Y : ujian diakhiri ,  N : ujian belum diakhiri',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ujian_id` (`ujian_id`),
  KEY `FK_h_ujian_deleted_mahasiswa` (`mahasiswa_id`),
  KEY `FK_h_ujian_deleted_mahasiswa_ujian` (`mahasiswa_ujian_id`),
  CONSTRAINT `FK_h_ujian_deleted_mahasiswa` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE,
  CONSTRAINT `FK_h_ujian_deleted_mahasiswa_ujian` FOREIGN KEY (`mahasiswa_ujian_id`) REFERENCES `mahasiswa_ujian` (`mahasiswa_matkul_id`) ON DELETE CASCADE,
  CONSTRAINT `h_ujian_deleted_ibfk_1` FOREIGN KEY (`ujian_id`) REFERENCES `m_ujian` (`id_ujian`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- tryout_01.h_ujian_history definition

CREATE TABLE `h_ujian_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ujian_id` int NOT NULL,
  `mahasiswa_id` int unsigned NOT NULL,
  `mahasiswa_ujian_id` int unsigned NOT NULL,
  `list_soal` longtext,
  `list_jawaban` longtext COMMENT 'Y : ragu , N : tidak ragu',
  `jml_soal` int NOT NULL,
  `jml_benar` int NOT NULL,
  `jml_salah` int NOT NULL,
  `nilai` decimal(10,2) NOT NULL,
  `nilai_bobot` decimal(10,2) NOT NULL,
  `nilai_bobot_benar` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_bobot` decimal(10,2) NOT NULL DEFAULT '0.00',
  `detail_bobot_benar` text,
  `tgl_mulai` datetime NOT NULL,
  `tgl_selesai` datetime NOT NULL,
  `ujian_selesai` enum('Y','N') NOT NULL COMMENT 'Y : ujian diakhiri ,  N : ujian belum diakhiri',
  `ended_by` varchar(50) DEFAULT NULL,
  `fixed_nilai` tinyint NOT NULL DEFAULT '0',
  `ujian_ke` tinyint NOT NULL,
  `peringkat` smallint NOT NULL DEFAULT '0',
  `jml_peserta` smallint NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 4` (`ujian_id`,`mahasiswa_id`,`ujian_ke`),
  UNIQUE KEY `mahasiswa_ujian_id` (`mahasiswa_ujian_id`,`ujian_ke`),
  KEY `ujian_id` (`ujian_id`),
  KEY `FK_h_ujian_mahasiswa` (`mahasiswa_id`),
  KEY `fk_mahasiswa_ujian_id` (`mahasiswa_ujian_id`),
  CONSTRAINT `h_ujian_history_ibfk_1` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON DELETE CASCADE,
  CONSTRAINT `h_ujian_history_ibfk_2` FOREIGN KEY (`mahasiswa_ujian_id`) REFERENCES `mahasiswa_ujian` (`id`) ON DELETE CASCADE,
  CONSTRAINT `h_ujian_history_ibfk_3` FOREIGN KEY (`ujian_id`) REFERENCES `m_ujian` (`id_ujian`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


-- tryout_01.tb_soal definition

CREATE TABLE `tb_soal` (
  `id_soal` int NOT NULL AUTO_INCREMENT,
  `dosen_id` int DEFAULT NULL,
  `matkul_id` int DEFAULT NULL,
  `topik_id` int NOT NULL,
  `bobot_soal_id` int NOT NULL,
  `no_urut` int unsigned DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `tipe_file` varchar(50) DEFAULT NULL,
  `soal` longtext NOT NULL,
  `opsi_a` longtext NOT NULL,
  `opsi_b` longtext NOT NULL,
  `opsi_c` longtext NOT NULL,
  `opsi_d` longtext NOT NULL,
  `opsi_e` longtext NOT NULL,
  `file_a` varchar(255) DEFAULT NULL,
  `file_b` varchar(255) DEFAULT NULL,
  `file_c` varchar(255) DEFAULT NULL,
  `file_d` varchar(255) DEFAULT NULL,
  `file_e` varchar(255) DEFAULT NULL,
  `jawaban` varchar(5) DEFAULT NULL,
  `gel` tinyint DEFAULT NULL,
  `smt` tinyint DEFAULT NULL,
  `tahun` year DEFAULT NULL,
  `penjelasan` longtext,
  `created_by` varchar(100) NOT NULL COMMENT 'berisi username dari tabel user',
  `updated_by` varchar(100) DEFAULT NULL COMMENT 'berisi username dari tabel user',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_soal`),
  KEY `matkul_id` (`matkul_id`),
  KEY `dosen_id` (`dosen_id`),
  KEY `FK_tb_soal_topik` (`topik_id`),
  KEY `FK_tb_soal_setting_bobot` (`bobot_soal_id`),
  CONSTRAINT `FK_tb_soal_setting_bobot` FOREIGN KEY (`bobot_soal_id`) REFERENCES `bobot_soal` (`id`),
  CONSTRAINT `FK_tb_soal_topik` FOREIGN KEY (`topik_id`) REFERENCES `topik` (`id`),
  CONSTRAINT `tb_soal_ibfk_1` FOREIGN KEY (`matkul_id`) REFERENCES `matkul` (`id_matkul`),
  CONSTRAINT `tb_soal_ibfk_2` FOREIGN KEY (`dosen_id`) REFERENCES `dosen` (`id_dosen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.jawaban_ujian definition

CREATE TABLE `jawaban_ujian` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ujian_id` int NOT NULL,
  `soal_id` int NOT NULL,
  `jawaban` enum('A','B','C','D','E') DEFAULT NULL,
  `status_jawaban` enum('Y','N') DEFAULT NULL COMMENT 'N : tidak ragu ; Y : ragu',
  `waktu_buka_soal` datetime DEFAULT NULL,
  `waktu_jawab_soal` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 4` (`ujian_id`,`soal_id`),
  KEY `FK_soal_ujian_tb_soal` (`soal_id`),
  KEY `FK_soal_ujian_h_ujian` (`ujian_id`),
  CONSTRAINT `FK_soal_ujian_h_ujian` FOREIGN KEY (`ujian_id`) REFERENCES `h_ujian` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_soal_ujian_tb_soal` FOREIGN KEY (`soal_id`) REFERENCES `tb_soal` (`id_soal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- tryout_01.jawaban_ujian_deleted definition

CREATE TABLE `jawaban_ujian_deleted` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ujian_id` int NOT NULL,
  `soal_id` int NOT NULL,
  `jawaban` char(1) DEFAULT NULL,
  `status_jawaban` enum('Y','N') DEFAULT NULL COMMENT 'N : tidak ragu ; Y : ragu',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_soal_ujian_tb_soal` (`soal_id`),
  KEY `FK_soal_ujian_h_ujian` (`ujian_id`),
  CONSTRAINT `FK_jawaban_ujian_deleted_h_ujian_deleted` FOREIGN KEY (`ujian_id`) REFERENCES `h_ujian_deleted` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jawaban_ujian_deleted_ibfk_2` FOREIGN KEY (`soal_id`) REFERENCES `tb_soal` (`id_soal`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;


-- tryout_01.jawaban_ujian_history definition

CREATE TABLE `jawaban_ujian_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ujian_id` int NOT NULL,
  `soal_id` int NOT NULL,
  `jawaban` enum('A','B','C','D','E') DEFAULT NULL,
  `status_jawaban` enum('Y','N') DEFAULT NULL COMMENT 'N : tidak ragu ; Y : ragu',
  `waktu_buka_soal` datetime DEFAULT NULL,
  `waktu_jawab_soal` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 4` (`ujian_id`,`soal_id`),
  KEY `FK_soal_ujian_tb_soal` (`soal_id`),
  KEY `FK_soal_ujian_h_ujian` (`ujian_id`),
  CONSTRAINT `FK_jawaban_ujian_history_h_ujian_history` FOREIGN KEY (`ujian_id`) REFERENCES `h_ujian_history` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jawaban_ujian_history_ibfk_2` FOREIGN KEY (`soal_id`) REFERENCES `tb_soal` (`id_soal`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;


-- tryout_01.vw_mhs_matkul source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `tryout_01`.`vw_mhs_matkul` AS
select
    `a`.`id` AS `id`,
    `a`.`mahasiswa_id` AS `mahasiswa_id`,
    `a`.`matkul_id` AS `matkul_id`,
    `a`.`created_at` AS `created_at`,
    `a`.`updated_at` AS `updated_at`,
    `b`.`nama_matkul` AS `nama_matkul`
from
    (`tryout_01`.`mahasiswa_matkul` `a`
join `tryout_01`.`matkul` `b` on
    ((`a`.`matkul_id` = `b`.`id_matkul`)));


-- tryout_01.vw_prodi source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `tryout_01`.`vw_prodi` AS
select
    `m`.`kodeps` AS `kodeps`,
    `m`.`prodi` AS `prodi`
from
    `tryout_01`.`mahasiswa` `m`
group by
    `m`.`kodeps`;


INSERT INTO `groups` (id, name, description) VALUES(1, 'admin', 'Administrator');
INSERT INTO `groups` (id, name, description) VALUES(2, 'dosen', 'Pembuat Soal dan ujian');
INSERT INTO `groups` (id, name, description) VALUES(3, 'mahasiswa', 'Peserta Ujian');
INSERT INTO `groups` (id, name, description) VALUES(4, 'pengawas', 'Pengawas Ujian');
INSERT INTO `groups` (id, name, description) VALUES(5, 'penyusun_soal', 'Penyusun Soal');

INSERT INTO `users` (id, ip_address, username, password, email, tgl_lahir, no_billkey, activation_selector, activation_code, forgotten_password_selector, forgotten_password_code, forgotten_password_time, remember_selector, remember_code, created_on, last_login, active, first_name, last_name, full_name, company, phone, is_online, created_at, updated_at)
VALUES(1, '127.0.0.1', 'admin', '$2y$12$5RvCbsM.8/tX.E3bAv1F7OuRM4zl7pCGmM0Ue9hkcoIPtskloWdxq', 'admin@admin.com', '01010101', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1268889823, 1617782351, 1, 'Administrator', 'Administrator', 'Administrator', 'ADMIN', '0', 0, NULL, NULL);


INSERT INTO `users_groups` (id, user_id, group_id, created_at, updated_at) VALUES(1, 1, 1, NULL, NULL);


