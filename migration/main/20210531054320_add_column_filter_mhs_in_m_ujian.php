<?php

use Phoenix\Migration\AbstractMigration;

class AddColumnFilterMhsInMUjian extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('m_ujian')
            ->addColumn('filter_mhs', 'json', ['null' => true])
            ->save();

        $value = '{"jalur": [], "prodi": [], "gel_mhs": [], "smt_mhs": []}';
        $this->execute("UPDATE m_ujian SET filter_mhs = '{$value}'");
    }

    protected function down(): void
    {
        $this->table('m_ujian')
            ->dropColumn('filter_mhs')
            ->save();
    }
}
