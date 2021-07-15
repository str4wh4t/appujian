<?php

use Phoenix\Migration\AbstractMigration;

class AddVariabelSetting2 extends AbstractMigration
{
    protected function up(): void
    {
        $this->insert('setting', [
            [
                'variabel' => 'api_auth_username',
                'nilai' => 'myusername',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'api_auth_password',
                'nilai' => 'mypassword',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ]
        ]);
    }

    protected function down(): void
    {
        $this->delete('setting', ['variabel' => 'api_auth_username']);
        $this->delete('setting', ['variabel' => 'api_auth_password']);
    }
}
