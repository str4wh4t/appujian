<?php

use Phoenix\Migration\AbstractMigration;

class AddConstraitTrxPayment extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('trx_payment')
            ->addForeignKey('paket_history_id', 'paket_history', 'id', 'restrict', 'restrict')
            ->save();    
    }

    protected function down(): void
    {
        $this->table('trx_payment')
            ->dropForeignKey('paket_history_id')
            ->save();
    }
}
