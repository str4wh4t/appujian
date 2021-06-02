<?php

use Phoenix\Migration\AbstractMigration;

class ChangeConstraitsMhs extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('membership_history')
            ->dropForeignKey('mahasiswa_id')
            ->save();
        
        $this->table('membership_history')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'cascade', 'no action')
            ->save();

        $this->table('paket_history')
            ->dropForeignKey('mahasiswa_id')
            ->save();
        
        $this->table('paket_history')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'cascade', 'no action')
            ->save();
    }

    protected function down(): void
    {
        $this->table('membership_history')
            ->dropForeignKey('mahasiswa_id')
            ->save();
        
        $this->table('membership_history')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'no action', 'no action')
            ->save();

        $this->table('paket_history')
            ->dropForeignKey('mahasiswa_id')
            ->save();
        
        $this->table('paket_history')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'restrict', 'no action')
            ->save();
    }
}
