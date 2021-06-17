<?php

use Phoenix\Migration\AbstractMigration;

class AddColumnPaketIdInTrxPayment extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('trx_payment')
            ->addColumn('paket_id', 'integer', ['null' => true])
            ->addColumn('membership_id', 'integer', ['null' => true])
            ->save();

        $this->table('trx_payment')
            ->addForeignKey('paket_id', 'paket', 'id', 'restrict', 'restrict')
            ->addForeignKey('membership_id', 'membership', 'id', 'restrict', 'restrict')
            ->save();

        $this->table('membership_history')
            ->dropForeignKey('membership_id')
            ->save();
        
        $this->table('membership_history')
            ->addForeignKey('membership_id', 'membership', 'id', 'cascade', 'no action')
            ->save();

        $this->table('paket_history')
            ->dropForeignKey('paket_id')
            ->save();
        
        $this->table('paket_history')
            ->addForeignKey('paket_id', 'paket', 'id', 'cascade', 'no action')
            ->save();
    }

    protected function down(): void
    {
        $this->table('trx_payment')
            ->dropForeignKey('paket_id')
            ->dropForeignKey('membership_id')
            ->save();

        $this->table('trx_payment')
            ->dropColumn('paket_id')
            ->dropColumn('membership_id')
            ->save();

        $this->table('membership_history')
            ->dropForeignKey('membership_id')
            ->save();
        
        $this->table('membership_history')
            ->addForeignKey('membership_id', 'membership', 'id', 'restrict', 'no action')
            ->save();

        $this->table('paket_history')
            ->dropForeignKey('paket_id')
            ->save();
        
        $this->table('paket_history')
            ->addForeignKey('paket_id', 'paket', 'id', 'restrict', 'no action')
            ->save();
    }
}
