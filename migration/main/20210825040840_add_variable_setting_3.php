<?php

use Phoenix\Migration\AbstractMigration;

class AddVariableSetting3 extends AbstractMigration
{
    protected function up(): void
    {
        $this->insert('setting', [
            [
                'variabel' => 'is_show_registration',
                'nilai' => '1',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'is_show_detail_hasil',
                'nilai' => '1',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'is_enable_tambah_mhs',
                'nilai' => '1',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'is_show_tata_tertib',
                'nilai' => '1',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'is_show_warning_saat_ujian',
                'nilai' => '1',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'is_show_membership',
                'nilai' => '1',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'is_show_paket',
                'nilai' => '1',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
        ]);

        $this->table('m_ujian')
            ->addColumn('is_grouping_by_matkul', 'boolean', ['default' => false, 'comment' => 'hanya jika sumber_ujian = bundle, 0 : tidak , 1 : ya'])
            ->addColumn('urutan_matkul', 'json', ['null' => true])
            ->addColumn('is_grouping_by_parent_topik', 'boolean', ['default' => false, 'comment' => 'hanya jika sumber_ujian = bundle dan tidak grouping by matkul, 0 : tidak , 1 : ya'])
            ->addColumn('urutan_parent_topik', 'json', ['null' => true])
            ->addColumn('is_sekuen_matkul', 'boolean', ['default' => false, 'comment' => '0 : tidak ; 1 : iya'])
            ->save();

    }

    protected function down(): void
    {
        $this->delete('setting', ['variabel' => 'is_show_registration']);
        $this->delete('setting', ['variabel' => 'is_show_detail_hasil']);
        $this->delete('setting', ['variabel' => 'is_enable_tambah_mhs']);
        $this->delete('setting', ['variabel' => 'is_show_tata_tertib']);
        $this->delete('setting', ['variabel' => 'is_show_warning_saat_ujian']);
        $this->delete('setting', ['variabel' => 'is_show_membership']);
        $this->delete('setting', ['variabel' => 'is_show_paket']);

        $this->table('m_ujian')
            ->dropColumn('is_grouping_by_matkul')
            ->dropColumn('urutan_matkul')
            ->dropColumn('is_grouping_by_parent_topik')
            ->dropColumn('urutan_parent_topik')
            ->dropColumn('is_sekuen_matkul')
            ->save();
    }
}
