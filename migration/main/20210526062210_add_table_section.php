<?php

use Phoenix\Migration\AbstractMigration;

class AddTableSection extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('section', 'id')
            ->setCharset('utf8mb4')
            ->setCollation('utf8mb4_general_ci')
            ->addColumn('id', 'integer', ['signed' => false,'autoincrement' => true])
            ->addColumn('keterangan', 'string', ['length' => 100, 'charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('konten', 'longtext', ['charset' => 'utf8', 'collation' => 'utf8_general_ci'])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create();
        
        $this->table('tb_soal')
            ->addColumn('section_id', 'integer', ['signed' => false, 'null' => true])
            ->addForeignKey('section_id', 'section', 'id', 'restrict', 'no action')
            ->save();
    }

    protected function down(): void
    {
        
        $this->table('tb_soal')
            ->dropForeignKey('section_id')
            ->dropColumn('section_id')
            ->save();

        $this->table('section')
            ->drop();

    }
}
