<?php

use Phoenix\Migration\AbstractMigration;

class ChangeConstraitsBundleSoal extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('bundle_soal')
            ->dropForeignKey('bundle_id')
            ->save();
        
        $this->table('bundle_soal')
            ->addForeignKey('bundle_id', 'bundle', 'id', 'cascade', 'no action')
            ->save();

        $this->table('paket_ujian')
            ->dropForeignKey('paket_id')
            ->save();
        
        $this->table('paket_ujian')
            ->addForeignKey('paket_id', 'paket', 'id', 'cascade', 'no action')
            ->save();

        $this->table('h_ujian')
            ->dropForeignKey('mahasiswa_id')
            ->save();
        
        $this->table('h_ujian')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'cascade', 'no action')
            ->save();
    }

    protected function down(): void
    {
        $this->table('bundle_soal')
            ->dropForeignKey('bundle_id')
            ->save();
        
        $this->table('bundle_soal')
            ->addForeignKey('bundle_id', 'bundle', 'id', 'restrict', 'no action')
            ->save();

        $this->table('paket_ujian')
            ->dropForeignKey('paket_id')
            ->save();
        
        $this->table('paket_ujian')
            ->addForeignKey('paket_id', 'paket', 'id', 'restrict', 'no action')
            ->save();

        $this->table('h_ujian')
            ->dropForeignKey('mahasiswa_id')
            ->save();
        
        $this->table('h_ujian')
            ->addForeignKey('mahasiswa_id', 'mahasiswa', 'id_mahasiswa', 'no action', 'no action')
            ->save();
    }
}
