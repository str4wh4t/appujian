<?php

use Phoenix\Migration\AbstractMigration;

class AddColumnInTbSoal extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('tb_soal')
            ->addColumn('opsi_f', 'longtext', ['null' => true])
            ->addColumn('jml_pilihan_jawaban', 'tinyinteger', ['null' => true])
            ->addColumn('is_bobot_per_jawaban', 'tinyinteger', ['default' => 0])
            ->addColumn('opsi_a_bobot', 'decimal', ['null' => true, 'decimals' => 2])
            ->addColumn('opsi_b_bobot', 'decimal', ['null' => true, 'decimals' => 2])
            ->addColumn('opsi_c_bobot', 'decimal', ['null' => true, 'decimals' => 2])
            ->addColumn('opsi_d_bobot', 'decimal', ['null' => true, 'decimals' => 2])
            ->addColumn('opsi_e_bobot', 'decimal', ['null' => true, 'decimals' => 2])
            ->addColumn('opsi_f_bobot', 'decimal', ['null' => true, 'decimals' => 2])
            ->addColumn('file_f', 'string', ['null' => true, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])     
        ->save();

        $this->table('jawaban_ujian')
            ->addColumn('jawaban_mcma', 'json', ['null' => true])
            ->save();

        $this->table('jawaban_ujian_history')
            ->addColumn('jawaban_mcma', 'json', ['null' => true])
            ->save();

        $this->execute("ALTER TABLE jawaban_ujian MODIFY jawaban CHAR(1) NULL");
        $this->execute("ALTER TABLE jawaban_ujian_history MODIFY jawaban CHAR(1) NULL");

        $this->execute("ALTER TABLE tb_soal MODIFY bobot_soal_id INT NULL");
        $this->execute("ALTER TABLE topik_ujian MODIFY bobot_soal_id INT NULL");
        
        $this->execute("UPDATE tb_soal SET jml_pilihan_jawaban = 5");
    }

    protected function down(): void
    {
        $this->table('tb_soal')
            ->dropColumn('opsi_f')
            ->dropColumn('jml_pilihan_jawaban')
            ->dropColumn('is_bobot_per_jawaban')
            ->dropColumn('opsi_a_bobot')
            ->dropColumn('opsi_b_bobot')
            ->dropColumn('opsi_c_bobot')
            ->dropColumn('opsi_d_bobot')
            ->dropColumn('opsi_e_bobot')
            ->dropColumn('opsi_f_bobot')
        ->dropColumn('file_f')
        ->save();

        $this->table('jawaban_ujian')
            ->dropColumn('jawaban_mcma')
            ->save();

        $this->table('jawaban_ujian_history')
            ->dropColumn('jawaban_mcma')
            ->save();

        $this->execute("ALTER TABLE jawaban_ujian MODIFY jawaban ENUM('A','B','C','D','E') NULL");
        $this->execute("ALTER TABLE jawaban_ujian_history MODIFY jawaban ENUM('A','B','C','D','E') NULL");

        $this->execute("ALTER TABLE tb_soal MODIFY bobot_soal_id INT NOT NULL");
        $this->execute("ALTER TABLE topik_ujian MODIFY bobot_soal_id INT NOT NULL");
    }
}
