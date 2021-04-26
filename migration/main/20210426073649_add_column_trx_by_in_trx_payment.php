<?php

use Phoenix\Migration\AbstractMigration;

class AddColumnTrxByInTrxPayment extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('trx_payment')
            ->addColumn('trx_by', 'string', ['length' => 100, 'null' => true])
            ->save();
    }

    protected function down(): void
    {
        $this->table('trx_payment')
            ->dropColumn('trx_by')
            ->save();
    }
}
