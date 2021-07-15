<?php

use Phoenix\Migration\AbstractMigration;

class AddVariabelSetting extends AbstractMigration
{
    protected function up(): void
    {
        $this->insert('setting', [
            [
                'variabel' => 'is_show_banner_ads',
                'nilai' => '0',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'banner_ads_link',
                'nilai' => '',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'is_enable_socket',
                'nilai' => '0',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'ping_interval',
                'nilai' => '30000',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
        ]);

        $this->table('daftar_hadir')
            ->dropForeignKey('absen_by')
            ->save();

        $this->table('daftar_hadir')
            ->addForeignKey('absen_by', 'users_groups', 'id', 'cascade', 'no action')
            ->save();
        
        $this->execute("ALTER TABLE tb_soal MODIFY jawaban LONGTEXT");

        $this->table('tb_soal')
            ->addColumn('tipe_soal', 'tinyinteger', ['null' => true, 'default' => 1]) // 1 : MULTIPLE CHOICE SINGLE ANSWER (MCSA)
            ->save();

        $this->table('jawaban_ujian')
            ->addColumn('jawaban_essay', 'longtext', ['null' => true])
            ->save();

        $this->table('jawaban_ujian')
            ->addColumn('nilai_essay', 'decimal', ['decimals' => 2])
            ->save();

        $this->table('jawaban_ujian')
            ->addColumn('penilai_essay', 'string', ['null' => true, 'length' => 50])
            ->save();

        $this->table('jawaban_ujian')
            ->addColumn('waktu_menilai_essay', 'datetime', ['null' => true])
            ->save();

        $this->table('jawaban_ujian_history')
            ->addColumn('jawaban_essay', 'longtext', ['null' => true])
            ->save();

        $this->table('jawaban_ujian_history')
            ->addColumn('nilai_essay', 'decimal', ['decimals' => 2])
            ->save();

        $this->table('jawaban_ujian_history')
            ->addColumn('penilai_essay', 'string', ['null' => true, 'length' => 50])
            ->save();

        $this->table('jawaban_ujian_history')
            ->addColumn('waktu_menilai_essay', 'datetime', ['null' => true])
            ->save();
            
        $this->execute("ALTER TABLE daftar_hadir MODIFY absen_by INT UNSIGNED");

        $this->table('daftar_hadir')
            ->addColumn('absen_by_username', 'string', ['null' => true, 'length' => 50])
            ->save();

    }

    protected function down(): void
    {
        $this->delete('setting', ['variabel' => 'is_show_banner_ads']);
        $this->delete('setting', ['variabel' => 'banner_ads_link']);
        $this->delete('setting', ['variabel' => 'is_enable_socket']);
        $this->delete('setting', ['variabel' => 'ping_interval']);

        $this->table('daftar_hadir')
            ->dropForeignKey('absen_by')
            ->save();

        $this->table('daftar_hadir')
            ->addForeignKey('absen_by', 'users_groups', 'id', 'no action', 'no action')
            ->save();
        
        $this->execute("ALTER TABLE tb_soal MODIFY jawaban VARCHAR(5)");

        $this->table('tb_soal')
            ->dropColumn('tipe_soal')
            ->save();

        $this->table('jawaban_ujian')
            ->dropColumn('jawaban_essay')
            ->save();

        $this->table('jawaban_ujian')
            ->dropColumn('nilai_essay')
            ->save();

        $this->table('jawaban_ujian')
            ->dropColumn('penilai_essay')
            ->save();

        $this->table('jawaban_ujian')
            ->dropColumn('waktu_menilai_essay')
            ->save();

        $this->table('jawaban_ujian_history')
            ->dropColumn('jawaban_essay')
            ->save();

        $this->table('jawaban_ujian_history')
            ->dropColumn('nilai_essay')
            ->save();

        $this->table('jawaban_ujian_history')
            ->dropColumn('penilai_essay')
            ->save();

        $this->table('jawaban_ujian_history')
            ->dropColumn('waktu_menilai_essay')
            ->save();

        $this->execute("ALTER TABLE daftar_hadir MODIFY absen_by INT UNSIGNED NOT NULL");

        $this->table('daftar_hadir')
            ->dropColumn('absen_by_username')
            ->save();

    }
}
