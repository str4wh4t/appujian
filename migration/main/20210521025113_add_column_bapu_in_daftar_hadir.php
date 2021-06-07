<?php

use Phoenix\Migration\AbstractMigration;

class AddColumnBapuInDaftarHadir extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('daftar_hadir')
            ->addColumn('is_terlihat_pada_layar', 'tinyinteger', ['default' => 0])
            ->addColumn('is_perjokian', 'tinyinteger', ['default' => 0])
            ->addColumn('is_sering_buka_page_lain', 'tinyinteger', ['default' => 0])
            ->save();

        $this->insert('groups', [
            [
                'id' => '6',
                'name' => 'koord_pengawas',
                'description' => 'Koordinator pengawas',
            ],
        ]);
    }

    protected function down(): void
    {
        $this->table('daftar_hadir')
            ->dropColumn('is_terlihat_pada_layar')
            ->dropColumn('is_perjokian')
            ->dropColumn('is_sering_buka_page_lain')
            ->save();
        
        $this->delete('groups', ['id' => 6]);
        // $this->execute("ALTER TABLE groups AUTO_INCREMENT=6;");
    }
}
