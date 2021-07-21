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
            ],
            [
                'variabel' => 'app_author',
                'nilai' => '',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'app_author_desc',
                'nilai' => '',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'variabel' => 'app_logo_cert',
                'nilai' => '',
                'flag' => '1',
                'created_at' => date("Y-m-d H:i:s"),
            ]
        ]);
    }

    protected function down(): void
    {
        $this->delete('setting', ['variabel' => 'api_auth_username']);
        $this->delete('setting', ['variabel' => 'api_auth_password']);
        $this->delete('setting', ['variabel' => 'app_author']);
        $this->delete('setting', ['variabel' => 'app_author_desc']);
        $this->delete('setting', ['variabel' => 'app_logo_cert']);
    }
}
