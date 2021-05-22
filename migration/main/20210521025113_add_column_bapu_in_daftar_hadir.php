<?php

use Phoenix\Migration\AbstractMigration;

class AddColumnBapuInDaftarHadir extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('daftar_hadir')
            ->addColumn('is_terlihat_pada_layar', 'tinyinteger', ['default' => 0])
            // ->addColumn('is_fraud', 'tinyinteger', ['default' => 0])
            ->save();
    }

    protected function down(): void
    {
        $this->table('daftar_hadir')
            ->dropColumn('is_terlihat_pada_layar')
            // ->dropColumn('is_fraud')
            ->save();
    }
}
