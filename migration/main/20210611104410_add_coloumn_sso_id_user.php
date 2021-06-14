<?php

use Phoenix\Migration\AbstractMigration;

class AddColoumnSsoIdUser extends AbstractMigration
{
    protected function up(): void
    {
        $this->table('tb_soal')
            ->addForeignKey('created_by', 'users', 'username', 'restrict', 'restrict')
            ->save();

        $this->table('users')
            ->addColumn('sso_udid_id', 'string', ['length' => 255, 'null' => true])
            ->save();

        $this->table('users_temp')
            ->addColumn('sso_udid_id', 'string', ['length' => 255, 'null' => true])
            ->save();

        $this->table('trx_payment')
            ->addColumn('order_id_udid', 'string', ['length' => 255, 'null' => true])
            ->save();

        $this->table('trx_payment')
            ->addColumn('jml_bayar_nett', 'decimal', ['null' => true, 'length' => 13, 'decimals' => 2])
            ->save();

        $this->table('daftar_hadir')
            ->addColumn('catatan_pengawas', 'longtext', ['null' => true])
            ->save();

    }

    protected function down(): void
    {
        $this->table('tb_soal')
            ->dropForeignKey('created_by')
            ->save();

        $this->table('users')
            ->dropColumn('sso_udid_id')
            ->save();   

        $this->table('users_temp')
            ->dropColumn('sso_udid_id')
            ->save();

        $this->table('trx_payment')
            ->dropColumn('order_id_udid')
            ->save();

        $this->table('trx_payment')
            ->dropColumn('jml_bayar_nett')
            ->save();

        $this->table('daftar_hadir')
            ->dropColumn('catatan_pengawas')
            ->save();
        
        
    }
}
