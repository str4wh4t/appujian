<?php

use Phoenix\Migration\AbstractMigration;

class AddTablePaketUjian extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('paket_ujian', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('paket_id', 'integer')
            ->addColumn('ujian_id', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['paket_id', 'ujian_id'], 'unique', 'btree', 'paket_ujian_UN')
            ->addIndex('ujian_id', '', 'btree', 'paket_ujian_FK_1')
            ->create();

        $this->table('paket_ujian')
            ->addForeignKey('paket_id', 'paket', 'id', 'restrict', 'no action')
            ->addForeignKey('ujian_id', 'm_ujian', 'id_ujian', 'cascade', 'no action')
            ->save();

        $this->table('mahasiswa_ujian')
            ->addColumn('sisa_kuota_latihan_soal', 'integer', ['default' => 0])
            ->save();

        $this->table('paket')
            ->dropColumn('show')
            ->addColumn('is_show', 'tinyinteger', ['default' => 1, 'comment' => '0 : hide, 1 : show'])
            ->save();

    }

    protected function down(): void
    {
        $this->table('paket_ujian')
            ->drop();

        $this->table('mahasiswa_ujian')
            ->dropColumn('sisa_kuota_latihan_soal')
            ->save();

        $this->table('paket')
            ->dropColumn('is_show')
            ->addColumn('show', 'tinyinteger', ['default' => 1, 'comment' => '0 : hide, 1 : show'])
            ->save();


    }
}
