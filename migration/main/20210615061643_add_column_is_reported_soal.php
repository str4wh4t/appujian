<?php

use Phoenix\Migration\AbstractMigration;

class AddColumnIsReportedSoal extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('tb_soal')
            ->addColumn('is_reported', 'tinyinteger', ['default' => 0])
            ->save();
    }

    protected function down(): void
    {
        $this->table('tb_soal')
            ->dropColumn('is_reported')
            ->save();
    }
}
