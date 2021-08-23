<?php

use Phoenix\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('bobot_soal', 'id')
            ->setCharset('latin1')
            ->setCollation('latin1_swedish_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('bobot', 'string', ['length' => 250])
            ->addColumn('nilai', 'decimal', ['default' => 0.00, 'decimals' => 2])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create();

        $this->table('bundle', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true, 'signed' => false])
            ->addColumn('nama_bundle', 'string', ['length' => 100])
            ->addColumn('created_by', 'string', ['length' => 100])
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->create();

        $this->table('bundle_soal', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true, 'signed' => false])
            ->addColumn('bundle_id', 'integer', ['signed' => false])
            ->addColumn('id_soal', 'integer')
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex('id_soal', '', 'btree', 'bundle_soal_FK')
            ->addIndex('bundle_id', '', 'btree', 'bundle_soal_FK_1')
            ->create();

        $this->table('daftar_hadir', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true,'signed' => false])
            ->addColumn('mahasiswa_ujian_id', 'integer',['signed' => false])
            ->addColumn('absen_by', 'integer', ['signed' => false,'comment' => 'pengawas_id dari users_groups'])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addIndex('mahasiswa_ujian_id', 'unique', 'btree', 'mahasiswa_ujian_id_unik')
            ->addIndex('absen_by', '', 'btree', 'FK_daftar_hadir_users_groups')
            ->addIndex('mahasiswa_ujian_id', '', 'btree', 'mahasiswa_ujian_id')
            ->create();

        $this->table('data_daerah', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('provinsi_id', 'integer',['signed' => false])
            ->addColumn('provinsi', 'string', ['length' => 200])
            ->addColumn('kota_kab_id', 'integer',['signed' => false])
            ->addColumn('kota_kab', 'string', ['length' => 200])
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->create();

        $this->table('dosen', 'id_dosen')
            ->setCharset('latin1')
            ->setCollation('latin1_swedish_ci')
            ->addColumn('id_dosen', 'integer', ['autoincrement' => true])
            ->addColumn('nip', 'char', ['length' => 25])
            ->addColumn('nama_dosen', 'string', ['length' => 50])
            ->addColumn('email', 'string', ['length' => 254])
            ->addColumn('tgl_lahir', 'date')
            ->addColumn('matkul_id', 'integer', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addIndex('email', 'unique', 'btree', 'email')
            ->addIndex('nip', 'unique', 'btree', 'nip')
            ->addIndex('matkul_id', '', 'btree', 'matkul_id')
            ->create();

        $this->table('dosen_matkul', 'id')
            ->setCharset('latin1')
            ->setCollation('latin1_swedish_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('dosen_id', 'integer')
            ->addColumn('matkul_id', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addIndex('matkul_id', '', 'btree', 'FK_dosen_matkul_matkul')
            ->addIndex('dosen_id', '', 'btree', 'FK_dosen_matkul_dosen')
            ->create();

        $this->table('groups', 'id')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id', 'mediuminteger', ['signed' => false,'autoincrement' => true])
            ->addColumn('name', 'string', ['length' => 20])
            ->addColumn('description', 'string', ['length' => 100])
            ->create();

        $this->table('h_ujian', 'id')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('ujian_id', 'integer')
            ->addColumn('mahasiswa_id', 'integer',['signed' => false])
            ->addColumn('mahasiswa_ujian_id', 'integer',['signed' => false])
            ->addColumn('list_soal', 'longtext', ['null' => true])
            ->addColumn('list_jawaban', 'longtext', ['null' => true, 'comment' => 'Y : ragu , N : tidak ragu'])
            ->addColumn('jml_soal', 'integer')
            ->addColumn('jml_benar', 'integer')
            ->addColumn('jml_salah', 'integer')
            ->addColumn('nilai', 'decimal', ['decimals' => 2])
            ->addColumn('nilai_bobot', 'decimal', ['decimals' => 2])
            ->addColumn('nilai_bobot_benar', 'decimal', ['default' => 0.00, 'decimals' => 2])
            ->addColumn('total_bobot', 'decimal', ['default' => 0.00, 'decimals' => 2])
            ->addColumn('detail_bobot_benar', 'text', ['null' => true])
            ->addColumn('tgl_mulai', 'datetime')
            ->addColumn('tgl_selesai', 'datetime')
            ->addColumn('ujian_selesai', 'enum', ['length' => 0, 'decimals' => 0, 'values' => ['Y', 'N'], 'comment' => 'Y : ujian diakhiri ,  N : ujian belum diakhiri'])
            ->addColumn('ended_by', 'string', ['null' => true, 'length' => 50])
            ->addColumn('fixed_nilai', 'tinyinteger', ['default' => 0])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['ujian_id', 'mahasiswa_id'], 'unique', 'btree', 'Index 4')
            ->addIndex('mahasiswa_ujian_id', 'unique', 'btree', 'mahasiswa_ujian_id')
            ->addIndex('ujian_id', '', 'btree', 'ujian_id')
            ->addIndex('mahasiswa_id', '', 'btree', 'FK_h_ujian_mahasiswa')
            ->addIndex('mahasiswa_ujian_id', '', 'btree', 'fk_mahasiswa_ujian_id')
            ->create();

        $this->table('h_ujian_deleted', 'id')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('ujian_id', 'integer')
            ->addColumn('mahasiswa_id', 'integer',['signed' => false])
            ->addColumn('mahasiswa_ujian_id', 'integer', ['signed' => false])
            ->addColumn('list_soal', 'longtext', ['null' => true])
            ->addColumn('list_jawaban', 'longtext', ['null' => true, 'comment' => 'Y : ragu , N : tidak ragu'])
            ->addColumn('jml_soal', 'integer')
            ->addColumn('jml_benar', 'integer')
            ->addColumn('jml_salah', 'integer')
            ->addColumn('nilai', 'decimal', ['decimals' => 2])
            ->addColumn('nilai_bobot', 'decimal', ['decimals' => 2])
            ->addColumn('nilai_bobot_benar', 'integer')
            ->addColumn('total_bobot', 'integer')
            ->addColumn('detail_bobot_benar', 'text', ['null' => true])
            ->addColumn('tgl_mulai', 'datetime')
            ->addColumn('tgl_selesai', 'datetime')
            ->addColumn('ujian_selesai', 'enum', ['length' => 0, 'decimals' => 0, 'values' => ['Y', 'N'], 'comment' => 'Y : ujian diakhiri ,  N : ujian belum diakhiri'])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex('ujian_id', '', 'btree', 'ujian_id')
            ->addIndex('mahasiswa_id', '', 'btree', 'FK_h_ujian_deleted_mahasiswa')
            ->addIndex('mahasiswa_ujian_id', '', 'btree', 'FK_h_ujian_deleted_mahasiswa_ujian')
            ->create();

        $this->table('h_ujian_history', 'id')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('ujian_id', 'integer')
            ->addColumn('mahasiswa_id', 'integer',['signed' => false])
            ->addColumn('mahasiswa_ujian_id', 'integer', ['signed' => false])
            ->addColumn('list_soal', 'longtext', ['null' => true])
            ->addColumn('list_jawaban', 'longtext', ['null' => true, 'comment' => 'Y : ragu , N : tidak ragu'])
            ->addColumn('jml_soal', 'integer')
            ->addColumn('jml_benar', 'integer')
            ->addColumn('jml_salah', 'integer')
            ->addColumn('nilai', 'decimal', ['decimals' => 2])
            ->addColumn('nilai_bobot', 'decimal', ['decimals' => 2])
            ->addColumn('nilai_bobot_benar', 'decimal', ['default' => 0.00, 'decimals' => 2])
            ->addColumn('total_bobot', 'decimal', ['default' => 0.00, 'decimals' => 2])
            ->addColumn('detail_bobot_benar', 'text', ['null' => true])
            ->addColumn('tgl_mulai', 'datetime')
            ->addColumn('tgl_selesai', 'datetime')
            ->addColumn('ujian_selesai', 'enum', ['length' => 0, 'decimals' => 0, 'values' => ['Y', 'N'], 'comment' => 'Y : ujian diakhiri ,  N : ujian belum diakhiri'])
            ->addColumn('ended_by', 'string', ['null' => true, 'length' => 50])
            ->addColumn('fixed_nilai', 'tinyinteger', ['default' => 0])
            ->addColumn('ujian_ke', 'tinyinteger')
            ->addColumn('peringkat', 'smallinteger', ['default' => 0])
            ->addColumn('jml_peserta', 'smallinteger', ['default' => 0])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['ujian_id', 'mahasiswa_id', 'ujian_ke'], 'unique', 'btree', 'Index 4')
            ->addIndex(['mahasiswa_ujian_id', 'ujian_ke'], 'unique', 'btree', 'mahasiswa_ujian_id')
            ->addIndex('ujian_id', '', 'btree', 'ujian_id')
            ->addIndex('mahasiswa_id', '', 'btree', 'FK_h_ujian_mahasiswa')
            ->addIndex('mahasiswa_ujian_id', '', 'btree', 'fk_mahasiswa_ujian_id')
            ->create();

        $this->table('jawaban_ujian', 'id')
            ->setCharset('latin1')
            ->setCollation('latin1_swedish_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('ujian_id', 'integer')
            ->addColumn('soal_id', 'integer')
            ->addColumn('jawaban', 'enum', ['null' => true, 'length' => 0, 'decimals' => 0, 'values' => ['A', 'B', 'C', 'D', 'E']])
            ->addColumn('status_jawaban', 'enum', ['null' => true, 'length' => 0, 'decimals' => 0, 'values' => ['Y', 'N'], 'comment' => 'N : tidak ragu ; Y : ragu'])
            ->addColumn('waktu_buka_soal', 'datetime', ['null' => true])
            ->addColumn('waktu_jawab_soal', 'datetime', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['ujian_id', 'soal_id'], 'unique', 'btree', 'Index 4')
            ->addIndex('soal_id', '', 'btree', 'FK_soal_ujian_tb_soal')
            ->addIndex('ujian_id', '', 'btree', 'FK_soal_ujian_h_ujian')
            ->create();

        $this->table('jawaban_ujian_deleted', 'id')
            ->setCharset('latin1')
            ->setCollation('latin1_swedish_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('ujian_id', 'integer')
            ->addColumn('soal_id', 'integer')
            ->addColumn('jawaban', 'char', ['null' => true, 'length' => 1])
            ->addColumn('status_jawaban', 'enum', ['null' => true, 'length' => 0, 'decimals' => 0, 'values' => ['Y', 'N'], 'comment' => 'N : tidak ragu ; Y : ragu'])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex('soal_id', '', 'btree', 'FK_soal_ujian_tb_soal')
            ->addIndex('ujian_id', '', 'btree', 'FK_soal_ujian_h_ujian')
            ->create();

        $this->table('jawaban_ujian_history', 'id')
            ->setCharset('latin1')
            ->setCollation('latin1_swedish_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('ujian_id', 'integer')
            ->addColumn('soal_id', 'integer')
            ->addColumn('jawaban', 'enum', ['null' => true, 'length' => 0, 'decimals' => 0, 'values' => ['A', 'B', 'C', 'D', 'E']])
            ->addColumn('status_jawaban', 'enum', ['null' => true, 'length' => 0, 'decimals' => 0, 'values' => ['Y', 'N'], 'comment' => 'N : tidak ragu ; Y : ragu'])
            ->addColumn('waktu_buka_soal', 'datetime', ['null' => true])
            ->addColumn('waktu_jawab_soal', 'datetime', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['ujian_id', 'soal_id'], 'unique', 'btree', 'Index 4')
            ->addIndex('soal_id', '', 'btree', 'FK_soal_ujian_tb_soal')
            ->addIndex('ujian_id', '', 'btree', 'FK_soal_ujian_h_ujian')
            ->create();

        $this->table('jurusan', 'id_jurusan')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id_jurusan', 'integer', ['autoincrement' => true])
            ->addColumn('nama_jurusan', 'string', ['length' => 30])
            ->create();

        $this->table('jurusan_matkul', 'id')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('matkul_id', 'integer')
            ->addColumn('jurusan_id', 'integer')
            ->addIndex('jurusan_id', '', 'btree', 'jurusan_id')
            ->addIndex('matkul_id', '', 'btree', 'matkul_id')
            ->create();

        $this->table('kelas', 'id_kelas')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id_kelas', 'integer', ['autoincrement' => true])
            ->addColumn('nama_kelas', 'string', ['length' => 30])
            ->addColumn('jurusan_id', 'integer')
            ->addIndex('jurusan_id', '', 'btree', 'jurusan_id')
            ->create();

        $this->table('kelas_dosen', 'id')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('kelas_id', 'integer')
            ->addColumn('dosen_id', 'integer')
            ->addIndex('kelas_id', '', 'btree', 'kelas_id')
            ->addIndex('dosen_id', '', 'btree', 'dosen_id')
            ->create();

        $this->table('login_attempts', 'id')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('ip_address', 'string', ['length' => 45])
            ->addColumn('login', 'string', ['length' => 100])
            ->addColumn('time', 'integer', ['signed' => false,'null' => true])
            ->create();

        $this->table('login_log', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('users_id', 'integer')
            ->addColumn('status_login', 'tinyinteger', ['comment' => '1 : online , 0 : offline'])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create();

        $this->table('m_ujian', 'id_ujian')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id_ujian', 'integer', ['autoincrement' => true])
            ->addColumn('dosen_id', 'integer', ['null' => true])
            ->addColumn('matkul_id', 'integer', ['null' => true])
            ->addColumn('nama_ujian', 'string', ['length' => 200])
            ->addColumn('jumlah_soal', 'integer')
            ->addColumn('jumlah_soal_detail', 'longtext', ['null' => true, 'charset' => 'utf8mb4', 'collation' => 'utf8mb4_general_ci'])
            ->addColumn('waktu', 'integer')
            ->addColumn('jenis', 'enum', ['length' => 0, 'decimals' => 0, 'values' => ['acak', 'urut']])
            ->addColumn('jenis_jawaban', 'enum', ['length' => 0, 'decimals' => 0, 'values' => ['acak', 'urut']])
            ->addColumn('tgl_mulai', 'datetime')
            ->addColumn('terlambat', 'datetime', ['null' => true])
            ->addColumn('pakai_token', 'boolean', ['default' => false, 'comment' => '0 : tanpa token ; 1 : dengan token'])
            ->addColumn('token', 'string', ['length' => 5])
            ->addColumn('status_ujian', 'boolean', ['default' => false, 'comment' => '0 : tidak aktif ; 1 : aktif'])
            ->addColumn('tampilkan_hasil', 'boolean', ['default' => false, 'comment' => '0 : tidak ; 1 : iya'])
            ->addColumn('masa_berlaku_sert', 'boolean', ['default' => false, 'comment' => 'masa berlaku sertifikat dalam satuan waktu'])
            ->addColumn('tampilkan_jawaban', 'boolean', ['default' => false, 'comment' => '0 : tidak ; 1 : iya'])
            ->addColumn('soal_gel', 'boolean', ['null' => true, 'default' => false])
            ->addColumn('soal_smt', 'boolean', ['null' => true, 'default' => false])
            ->addColumn('soal_tahun', 'smallinteger', ['null' => true])
            ->addColumn('repeatable', 'boolean', ['default' => false, 'comment' => '0 : tidak ; 1 : iya'])
            ->addColumn('is_sekuen_topik', 'boolean', ['default' => false, 'comment' => '0 : tidak ; 1 : iya'])
            ->addColumn('urutan_topik', 'json', ['null' => true])
            ->addColumn('mhs_kelompok_ujian', 'boolean', ['null' => true, 'default' => false])
            ->addColumn('mhs_tgl_ujian', 'datetime', ['null' => true])
            ->addColumn('mhs_tahun', 'smallinteger', ['null' => true])
            ->addColumn('sumber_ujian', 'string', ['null' => true, 'length' => 100])
            ->addColumn('created_by', 'string', ['length' => 100, 'comment' => 'berisi username dari tabel users'])
            ->addColumn('updated_by', 'string', ['null' => true, 'length' => 100])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex('matkul_id', '', 'btree', 'matkul_id')
            ->addIndex('dosen_id', '', 'btree', 'dosen_id')
            ->create();

        $this->table('mahasiswa', 'id_mahasiswa')
            ->setCharset('latin1')
            ->setCollation('latin1_swedish_ci')
            ->addColumn('id_mahasiswa', 'integer',['signed' => false])
            ->addColumn('nama', 'string', ['length' => 250])
            ->addColumn('nik', 'string', ['null' => true, 'length' => 50])
            ->addColumn('nim', 'string', ['length' => 50])
            ->addColumn('email', 'string', ['length' => 250])
            ->addColumn('jenis_kelamin', 'enum', ['null' => true, 'length' => 0, 'decimals' => 0, 'values' => ['L', 'P']])
            ->addColumn('foto', 'text', ['null' => true])
            ->addColumn('tmp_lahir', 'string', ['length' => 250])
            ->addColumn('tgl_lahir', 'date', ['null' => true])
            ->addColumn('kota_asal', 'string', ['null' => true])
            ->addColumn('kelas_id', 'integer', ['null' => true, 'comment' => 'kelas&jurusan'])
            ->addColumn('kodeps', 'integer', ['null' => true])
            ->addColumn('prodi', 'string', ['null' => true, 'length' => 250])
            ->addColumn('no_billkey', 'string', ['null' => true, 'length' => 20])
            ->addColumn('jalur', 'string', ['null' => true, 'length' => 250])
            ->addColumn('gel', 'tinyinteger', ['null' => true])
            ->addColumn('smt', 'tinyinteger', ['null' => true])
            ->addColumn('tahun', 'smallinteger', ['null' => true])
            ->addColumn('kelompok_ujian', 'tinyinteger', ['default' => 0])
            ->addColumn('tgl_ujian', 'datetime', ['null' => true])
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex('nim', 'unique', 'btree', 'nim')
            ->addIndex('kelas_id', '', 'btree', 'kelas_id')
            ->create();

        $this->table('mahasiswa_matkul', 'id')
            ->setCharset('latin1')
            ->setCollation('latin1_swedish_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('mahasiswa_id', 'integer',['signed' => false])
            ->addColumn('matkul_id', 'integer')
            ->addColumn('sisa_kuota_latihan_soal', 'integer', ['default' => 0])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['mahasiswa_id', 'matkul_id'], 'unique', 'btree', 'Index 4')
            ->addIndex('matkul_id', '', 'btree', 'FK_mahasiswa_matkul_matkul')
            ->addIndex('mahasiswa_id', '', 'btree', 'mahasiswa_id')
            ->create();

        $this->table('mahasiswa_ujian', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('mahasiswa_matkul_id', 'integer', ['signed' => false,'null' => true])
            ->addColumn('mahasiswa_id', 'integer',['signed' => false])
            ->addColumn('ujian_id', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['mahasiswa_matkul_id', 'ujian_id'], 'unique', 'btree', 'Index 4')
            ->addIndex(['mahasiswa_id', 'ujian_id'], 'unique', 'btree', 'Index 5')
            ->addIndex('mahasiswa_matkul_id', '', 'btree', 'mahasiswa_matkul_id')
            ->addIndex('ujian_id', '', 'btree', 'FK_mahasiswa_ujian_m_ujian')
            ->addIndex('mahasiswa_id', '', 'btree', 'mahasiswa_ujian_FK')
            ->create();

        $this->table('matkul', 'id_matkul')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id_matkul', 'integer', ['autoincrement' => true])
            ->addColumn('nama_matkul', 'string', ['length' => 50])
            ->create();

        $this->table('membership', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer')
            ->addColumn('name', 'string', ['length' => 50])
            ->addColumn('urut', 'tinyinteger', ['default' => 0])
            ->addColumn('price', 'biginteger')
            ->addColumn('delete_price', 'biginteger', ['null' => true])
            ->addColumn('discount', 'tinyinteger', ['null' => true])
            ->addColumn('description', 'longtext', ['null' => true])
            ->addColumn('show', 'tinyinteger', ['default' => 1, 'comment' => '0 : hide, 1 : show'])
            ->addColumn('text_color', 'string', ['default' => 'primary', 'length' => 10])
            ->addColumn('durasi', 'tinyinteger', ['default' => 0, 'comment' => 'dalam bulan'])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->create();

        $this->table('membership_history', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'biginteger', ['signed' => false,'autoincrement' => true])
            ->addColumn('mahasiswa_id', 'integer',['signed' => false])
            ->addColumn('membership_id', 'integer')
            ->addColumn('upgrade_ke', 'integer')
            ->addColumn('expired_at', 'datetime')
            ->addColumn('keterangan', 'string', ['null' => true, 'length' => 250])
            ->addColumn('stts', 'tinyinteger', ['comment' => '0 : tidak atif , 1 : aktif'])
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['mahasiswa_id', 'membership_id', 'upgrade_ke'], 'unique', 'btree', 'membership_history_UN')
            ->addIndex('membership_id', '', 'btree', 'membership_history_FK_1')
            ->create();

        $this->table('paket', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('name', 'string', ['length' => 50])
            ->addColumn('urut', 'tinyinteger', ['default' => 0])
            ->addColumn('price', 'biginteger')
            ->addColumn('delete_price', 'biginteger', ['null' => true])
            ->addColumn('discount', 'tinyinteger', ['null' => true])
            ->addColumn('description', 'longtext', ['null' => true])
            ->addColumn('show', 'tinyinteger', ['default' => 1, 'comment' => '0 : hide, 1 : show'])
            ->addColumn('text_color', 'string', ['default' => 'primary', 'length' => 10])
            ->addColumn('kuota_latihan_soal', 'tinyinteger', ['default' => 0, 'comment' => 'brp x latihan soal'])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->create();

        $this->table('paket_history', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'biginteger', ['signed' => false,'autoincrement' => true])
            ->addColumn('mahasiswa_id', 'integer',['signed' => false])
            ->addColumn('upgrade_ke', 'integer')
            ->addColumn('paket_id', 'integer')
            ->addColumn('keterangan', 'string', ['null' => true, 'length' => 250])
            ->addColumn('stts', 'tinyinteger', ['comment' => '0 : tidak atif , 1 : aktif'])
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['mahasiswa_id', 'upgrade_ke', 'paket_id'], 'unique', 'btree', 'paket_history_UN')
            ->addIndex('mahasiswa_id', '', 'btree', 'membership_history_FK')
            ->addIndex('paket_id', '', 'btree', 'membership_history_FK_1')
            ->create();

        $this->table('paket_matkul', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('paket_id', 'integer')
            ->addColumn('matkul_id', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['paket_id', 'matkul_id'], 'unique', 'btree', 'paket_matkul_UN')
            ->addIndex('matkul_id', '', 'btree', 'paket_matkul_FK_1')
            ->create();

        $this->table('setting', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('variabel', 'string')
            ->addColumn('nilai', 'string')
            ->addColumn('flag', 'tinyinteger')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->create();

        $this->table('tb_soal', 'id_soal')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id_soal', 'integer', ['autoincrement' => true])
            ->addColumn('dosen_id', 'integer', ['null' => true])
            ->addColumn('matkul_id', 'integer', ['null' => true])
            ->addColumn('topik_id', 'integer')
            ->addColumn('bobot_soal_id', 'integer')
            ->addColumn('no_urut', 'integer', ['null' => true])
            ->addColumn('file', 'string', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('tipe_file', 'string', ['null' => true, 'length' => 50, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('soal', 'longtext', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('opsi_a', 'longtext', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('opsi_b', 'longtext', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('opsi_c', 'longtext', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('opsi_d', 'longtext', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('opsi_e', 'longtext', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('file_a', 'string', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('file_b', 'string', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('file_c', 'string', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('file_d', 'string', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('file_e', 'string', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('jawaban', 'string', ['null' => true, 'length' => 5, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('gel', 'tinyinteger', ['null' => true])
            ->addColumn('smt', 'tinyinteger', ['null' => true])
            ->addColumn('tahun', 'smallinteger', ['null' => true])
            ->addColumn('penjelasan', 'longtext', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('created_by', 'string', ['length' => 100, 'charset' => 'utf8', 'collation' => 'utf8_general_ci', 'comment' => 'berisi username dari tabel user'])
            ->addColumn('updated_by', 'string', ['null' => true, 'length' => 100, 'charset' => 'utf8', 'collation' => 'utf8_general_ci', 'comment' => 'berisi username dari tabel user'])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex('matkul_id', '', 'btree', 'matkul_id')
            ->addIndex('dosen_id', '', 'btree', 'dosen_id')
            ->addIndex('topik_id', '', 'btree', 'FK_tb_soal_topik')
            ->addIndex('bobot_soal_id', '', 'btree', 'FK_tb_soal_setting_bobot')
            ->create();

        $this->table('topik', 'id')
            ->setCharset('latin1')
            ->setCollation('latin1_swedish_ci')
            ->addColumn('id', 'integer', ['autoincrement' => true])
            ->addColumn('matkul_id', 'integer')
            ->addColumn('nama_topik', 'string', ['length' => 250])
            ->addColumn('poin_topik', 'decimal', ['default' => 0.00, 'decimals' => 2])
            ->addColumn('created_at', 'datetime')
            ->addColumn('created_by', 'string', ['null' => true, 'length' => 100, 'comment' => 'berisi username dari tabel user'])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex('matkul_id', '', 'btree', 'FK_topik_matkul')
            ->create();

        $this->table('topik_ujian', 'id')
            ->setCharset('latin1')
            ->setCollation('latin1_swedish_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('topik_id', 'integer')
            ->addColumn('ujian_id', 'integer')
            ->addColumn('bobot_soal_id', 'integer')
            ->addColumn('jumlah_soal', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['topik_id', 'ujian_id', 'bobot_soal_id'], 'unique', 'btree', 'Index 5')
            ->addIndex('topik_id', '', 'btree', 'FK_topik_ujian_topik')
            ->addIndex('ujian_id', '', 'btree', 'FK_topik_ujian_m_ujian')
            ->addIndex('bobot_soal_id', '', 'btree', 'FK_topik_ujian_bobot_soal')
            ->create();

        $this->table('trx_midtrans', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'biginteger', ['signed' => false,'autoincrement' => true])
            ->addColumn('transaction_id', 'string', ['null' => true, 'length' => 100])
            ->addColumn('transaction_status', 'string', ['length' => 100])
            ->addColumn('transaction_time', 'datetime', ['null' => true])
            ->addColumn('status_code', 'string', ['null' => true, 'length' => 10])
            ->addColumn('payment_type', 'string', ['length' => 100])
            ->addColumn('order_id', 'string', ['length' => 100])
            ->addColumn('fraud_status', 'string', ['null' => true, 'length' => 100])
            ->addColumn('gross_amount', 'decimal', ['length' => 13, 'decimals' => 2])
            ->addColumn('signature_key', 'text')
            ->addColumn('signature_key_check', 'tinyinteger', ['null' => true, 'default' => 0])
            ->addColumn('is_settlement_processed', 'tinyinteger', ['default' => 0])
            ->addColumn('is_capture_processed', 'tinyinteger', ['default' => 0])
            ->addColumn('is_expire_processed', 'tinyinteger', ['default' => 0])
            ->addColumn('log_status', 'text', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->create();

        $this->table('trx_payment', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'biginteger', ['signed' => false,'autoincrement' => true])
            ->addColumn('mahasiswa_id', 'integer',['signed' => false])
            ->addColumn('order_number', 'string', ['length' => 100])
            ->addColumn('stts', 'tinyinteger', ['default' => 0])
            ->addColumn('membership_history_id', 'biginteger', ['signed' => false,'null' => true, 'comment' => 'jika trx untuk pembelian membership'])
            ->addColumn('paket_history_id', 'biginteger', ['signed' => false,'null' => true])
            ->addColumn('keterangan', 'string', ['null' => true, 'length' => 250])
            ->addColumn('tgl_order', 'datetime', ['null' => true])
            ->addColumn('tgl_bayar', 'datetime', ['null' => true])
            ->addColumn('jml_bayar', 'decimal', ['null' => true, 'length' => 13, 'decimals' => 2])
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex('order_number', 'unique', 'btree', 'trx_payment_UN')
            ->addIndex('membership_history_id', '', 'btree', 'trx_payment_FK_1')
            ->addIndex('mahasiswa_id', '', 'btree', 'trx_payment_FK')
            ->create();

        $this->table('ujian_bundle', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('ujian_id', 'integer')
            ->addColumn('bundle_id', 'integer',['signed' => false])
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex('bundle_id', '', 'btree', 'ujian_bundle_FK')
            ->addIndex('ujian_id', '', 'btree', 'ujian_bundle_FK_1')
            ->create();

        $this->table('ujian_matkul_enable', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('ujian_id', 'integer')
            ->addColumn('matkul_id', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex('matkul_id', '', 'btree', 'ujian_matkul_enable_FK_1')
            ->addIndex('ujian_id', '', 'btree', 'ujian_matkul_enable_FK')
            ->create();

        $this->table('users', 'id')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('ip_address', 'string', ['length' => 45])
            ->addColumn('username', 'string', ['length' => 100])
            ->addColumn('password', 'string')
            ->addColumn('email', 'string')
            ->addColumn('tgl_lahir', 'char', ['null' => true, 'length' => 8])
            ->addColumn('no_billkey', 'string', ['null' => true, 'length' => 50])
            ->addColumn('activation_selector', 'string', ['null' => true])
            ->addColumn('activation_code', 'string', ['null' => true])
            ->addColumn('forgotten_password_selector', 'string', ['null' => true])
            ->addColumn('forgotten_password_code', 'string', ['null' => true])
            ->addColumn('forgotten_password_time', 'integer', ['signed' => false,'null' => true])
            ->addColumn('remember_selector', 'string', ['null' => true])
            ->addColumn('remember_code', 'string', ['null' => true])
            ->addColumn('created_on', 'integer',['signed' => false])
            ->addColumn('last_login', 'integer', ['signed' => false,'null' => true])
            ->addColumn('active', 'tinyinteger', ['signed' => false,'null' => true])
            ->addColumn('first_name', 'string', ['null' => true, 'length' => 250])
            ->addColumn('last_name', 'string', ['null' => true, 'length' => 250])
            ->addColumn('full_name', 'string', ['null' => true, 'length' => 250])
            ->addColumn('company', 'string', ['null' => true, 'length' => 100])
            ->addColumn('phone', 'string', ['null' => true, 'length' => 20])
            ->addColumn('is_online', 'tinyinteger', ['null' => true, 'default' => 0, 'comment' => '0 : offline , 1 : online'])
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex('username', 'unique', 'btree', 'username')
            ->addIndex('activation_selector', 'unique', 'btree', 'uc_activation_selector')
            ->addIndex('forgotten_password_selector', 'unique', 'btree', 'uc_forgotten_password_selector')
            ->addIndex('remember_selector', 'unique', 'btree', 'uc_remember_selector')
            ->create();

        $this->table('users_groups', 'id')
            ->setCharset('utf8')
            ->setCollation('utf8_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('user_id', 'integer',['signed' => false])
            ->addColumn('group_id', 'mediuminteger',['signed' => false])
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['user_id', 'group_id'], 'unique', 'btree', 'uc_users_groups')
            ->addIndex('user_id', '', 'btree', 'fk_users_groups_users1_idx')
            ->addIndex('group_id', '', 'btree', 'fk_users_groups_groups1_idx')
            ->create();

        $this->table('users_temp', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('full_name', 'string', ['null' => true, 'length' => 250, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('nik', 'string', ['null' => true, 'length' => 16, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('email', 'string', ['charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('phone', 'string', ['null' => true, 'length' => 20, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('jenis_kelamin', 'string', ['null' => true, 'length' => 1, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('kota_asal', 'string', ['length' => 250, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('tmp_lahir', 'string', ['length' => 250, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('tgl_lahir', 'string', ['null' => true, 'length' => 10, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('password', 'string', ['charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('is_processed', 'tinyinteger', ['default' => 0])
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->create();

        $this->insert('bobot_soal', [
            [
                'id' => '1',
                'bobot' => 'Mudah',
                'nilai' => '1.00',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ],
        ]);

        $this->insert('groups', [
            [
                'id' => '1',
                'name' => 'admin',
                'description' => 'Administrator',
            ],
            [
                'id' => '2',
                'name' => 'dosen',
                'description' => 'Pembuat Soal dan ujian',
            ],
            [
                'id' => '3',
                'name' => 'mahasiswa',
                'description' => 'Peserta Ujian',
            ],
            [
                'id' => '4',
                'name' => 'pengawas',
                'description' => 'Pengawas Ujian',
            ],
            [
                'id' => '5',
                'name' => 'penyusun_soal',
                'description' => 'Penyusun Soal',
            ],
        ]);

        $this->insert('setting', [
            [
                'id' => '1',
                'variabel' => 'tahun_aktif',
                'nilai' => date("Y"),
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
        ]);

        $this->insert('users', [
            [
                'id' => '1',
                'ip_address' => '127.0.0.1',
                'username' => 'admin',
                'password' => '$2y$12$5RvCbsM.8/tX.E3bAv1F7OuRM4zl7pCGmM0Ue9hkcoIPtskloWdxq',
                'email' => 'admin@admin.com',
                'tgl_lahir' => '01010101',
                'created_on' => '1268889823',
                'last_login' => '1619275283',
                'active' => '1',
                'first_name' => 'Administrator',
                'last_name' => 'Administrator',
                'full_name' => 'Administrator',
                'company' => 'ADMIN',
                'phone' => '0',
                'is_online' => '0',
            ],
        ]);

        $this->insert('users_groups', [
            [
                'id' => '1',
                'user_id' => '1',
                'group_id' => '1',
            ],
        ]);

        $this->insert('membership', [
            [
                'id' => '0',
                'name' => 'default',
                'urut' => '0',
                'price' => '0',
                'description' => 'untuk user mode ujian',
                'show' => '0',
                'text_color' => 'primary',
                'durasi' => '0',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'id' => '1',
                'name' => 'gratis',
                'urut' => '1',
                'price' => '0',
                'description' => 'untuk user gratis',
                'show' => '1',
                'text_color' => 'info',
                'durasi' => '0',
                'created_at' => date("Y-m-d H:i:s"),
            ],
        ]);

        $this->table('bundle_soal')
            ->addForeignKey('id_soal', 'tb_soal', 'id_soal', 'cascade', 'no action')
            ->addForeignKey('bundle_id', 'bundle', 'id', 'restrict', 'no action')
            ->save();

        $this->table('daftar_hadir')
            ->addForeignKey('mahasiswa_ujian_id', 'mahasiswa_ujian', 'id', 'cascade', 'no action')
            ->addForeignKey('absen_by', 'users_groups', 'id', 'no action', 'no action')
            ->save();

        $this->table('dosen')
            ->addForeignKey('matkul_id', 'matkul', 'id_matkul', 'no action', 'no action')
            ->save();

        $this->table('dosen_matkul')
            ->addForeignKey('dosen_id', 'dosen', 'id_dosen', 'cascade', 'no action')
            ->addForeignKey('matkul_id', 'matkul', 'id_matkul', 'no action', 'no action')
            ->save();

        $this->table('h_ujian')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'no action', 'no action')
            ->addForeignKey('mahasiswa_ujian_id', 'mahasiswa_ujian', 'id', 'no action', 'no action')
            ->addForeignKey('ujian_id', 'm_ujian', 'id_ujian', 'no action', 'no action')
            ->save();

        $this->table('h_ujian_deleted')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'cascade', 'no action')
            ->addForeignKey('mahasiswa_ujian_id', 'mahasiswa_ujian', 'mahasiswa_matkul_id', 'cascade', 'no action')
            ->addForeignKey('ujian_id', 'm_ujian', 'id_ujian', 'cascade', 'no action')
            ->save();

        $this->table('h_ujian_history')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'cascade', 'no action')
            ->addForeignKey('mahasiswa_ujian_id', 'mahasiswa_ujian', 'id', 'cascade', 'no action')
            ->addForeignKey('ujian_id', 'm_ujian', 'id_ujian', 'cascade', 'no action')
            ->save();

        $this->table('jawaban_ujian')
            ->addForeignKey('ujian_id', 'h_ujian', 'id', 'cascade', 'no action')
            ->addForeignKey('soal_id', 'tb_soal', 'id_soal', 'no action', 'no action')
            ->save();

        $this->table('jawaban_ujian_deleted')
            ->addForeignKey('ujian_id', 'h_ujian_deleted', 'id', 'cascade', 'no action')
            ->addForeignKey('soal_id', 'tb_soal', 'id_soal', 'cascade', 'no action')
            ->save();

        $this->table('jawaban_ujian_history')
            ->addForeignKey('ujian_id', 'h_ujian_history', 'id', 'cascade', 'no action')
            ->addForeignKey('soal_id', 'tb_soal', 'id_soal', 'restrict', 'no action')
            ->save();

        $this->table('jurusan_matkul')
            ->addForeignKey('jurusan_id', 'jurusan', 'id_jurusan', 'no action', 'no action')
            ->addForeignKey('matkul_id', 'matkul', 'id_matkul', 'no action', 'no action')
            ->save();

        $this->table('kelas_dosen')
            ->addForeignKey('dosen_id', 'dosen', 'id_dosen', 'no action', 'no action')
            ->addForeignKey('kelas_id', 'kelas', 'id_kelas', 'no action', 'no action')
            ->save();

        $this->table('m_ujian')
            ->addForeignKey('dosen_id', 'dosen', 'id_dosen', 'no action', 'no action')
            ->addForeignKey('matkul_id', 'matkul', 'id_matkul', 'no action', 'no action')
            ->save();

        $this->table('mahasiswa')
            ->addForeignKey('kelas_id', 'kelas', 'id_kelas', 'no action', 'no action')
            ->save();

        $this->table('mahasiswa_matkul')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'cascade', 'no action')
            ->addForeignKey('matkul_id', 'matkul', 'id_matkul', 'no action', 'no action')
            ->save();

        $this->table('mahasiswa_ujian')
            ->addForeignKey('ujian_id', 'm_ujian', 'id_ujian', 'cascade', 'no action')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'cascade', 'no action')
            ->save();

        $this->table('membership_history')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'no action', 'no action')
            ->addForeignKey('membership_id', 'membership', 'id', 'restrict', 'no action')
            ->save();

        $this->table('paket_history')
            ->addForeignKey('paket_id', 'paket', 'id', 'restrict', 'no action')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'restrict', 'no action')
            ->save();

        $this->table('paket_matkul')
            ->addForeignKey('paket_id', 'paket', 'id', 'restrict', 'no action')
            ->addForeignKey('matkul_id', 'matkul', 'id_matkul', 'restrict', 'no action')
            ->save();

        $this->table('tb_soal')
            ->addForeignKey('bobot_soal_id', 'bobot_soal', 'id', 'no action', 'no action')
            ->addForeignKey('topik_id', 'topik', 'id', 'no action', 'no action')
            ->addForeignKey('matkul_id', 'matkul', 'id_matkul', 'no action', 'no action')
            ->addForeignKey('dosen_id', 'dosen', 'id_dosen', 'no action', 'no action')
            ->save();

        $this->table('topik')
            ->addForeignKey('matkul_id', 'matkul', 'id_matkul', 'no action', 'no action')
            ->save();

        $this->table('topik_ujian')
            ->addForeignKey('bobot_soal_id', 'bobot_soal', 'id', 'no action', 'no action')
            ->addForeignKey('ujian_id', 'm_ujian', 'id_ujian', 'cascade', 'no action')
            ->addForeignKey('topik_id', 'topik', 'id', 'no action', 'no action')
            ->save();

        $this->table('trx_payment')
            ->addForeignKey('membership_history_id', 'membership_history', 'id', 'no action', 'no action')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'restrict', 'no action')
            ->save();

        $this->table('ujian_bundle')
            ->addForeignKey('bundle_id', 'bundle', 'id', 'no action', 'no action')
            ->addForeignKey('ujian_id', 'm_ujian', 'id_ujian', 'cascade', 'no action')
            ->save();

        $this->table('ujian_matkul_enable')
            ->addForeignKey('matkul_id', 'matkul', 'id_matkul', 'no action', 'no action')
            ->addForeignKey('ujian_id', 'm_ujian', 'id_ujian', 'cascade', 'no action')
            ->save();

        $this->table('users_groups')
            ->addForeignKey('group_id', 'groups', 'id', 'cascade', 'no action')
            ->addForeignKey('user_id', 'users', 'id', 'cascade', 'no action')
            ->save();

        // CREATE VIEW
        $this->execute('CREATE OR REPLACE
            VIEW vw_prodi AS
            select
                kodeps AS kodeps,
                prodi AS prodi
            from
                mahasiswa m
            group by
                kodeps');
    }

    protected function down(): void
    {
        $this->table('bobot_soal')
            ->drop();

        $this->table('bundle')
            ->drop();

        $this->table('bundle_soal')
            ->drop();

        $this->table('daftar_hadir')
            ->drop();

        $this->table('data_daerah')
            ->drop();

        $this->table('dosen')
            ->drop();

        $this->table('dosen_matkul')
            ->drop();

        $this->table('groups')
            ->drop();

        $this->table('h_ujian')
            ->drop();

        $this->table('h_ujian_deleted')
            ->drop();

        $this->table('h_ujian_history')
            ->drop();

        $this->table('jawaban_ujian')
            ->drop();

        $this->table('jawaban_ujian_deleted')
            ->drop();

        $this->table('jawaban_ujian_history')
            ->drop();

        $this->table('jurusan')
            ->drop();

        $this->table('jurusan_matkul')
            ->drop();

        $this->table('kelas')
            ->drop();

        $this->table('kelas_dosen')
            ->drop();

        $this->table('login_attempts')
            ->drop();

        $this->table('login_log')
            ->drop();

        $this->table('m_ujian')
            ->drop();

        $this->table('mahasiswa')
            ->drop();

        $this->table('mahasiswa_matkul')
            ->drop();

        $this->table('mahasiswa_ujian')
            ->drop();

        $this->table('matkul')
            ->drop();

        $this->table('membership')
            ->drop();

        $this->table('membership_history')
            ->drop();

        $this->table('paket')
            ->drop();

        $this->table('paket_history')
            ->drop();

        $this->table('paket_matkul')
            ->drop();

        $this->table('setting')
            ->drop();

        $this->table('tb_soal')
            ->drop();

        $this->table('topik')
            ->drop();

        $this->table('topik_ujian')
            ->drop();

        $this->table('trx_midtrans')
            ->drop();

        $this->table('trx_payment')
            ->drop();

        $this->table('ujian_bundle')
            ->drop();

        $this->table('ujian_matkul_enable')
            ->drop();

        $this->table('users')
            ->drop();

        $this->table('users_groups')
            ->drop();

        $this->table('users_temp')
            ->drop();

        $this->execute('DROP VIEW vw_prodi');
    }
}
