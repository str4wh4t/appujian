<?php
return [
    'migration_dirs' => [
        'main' => __DIR__ . '/migration/main',
        // 'first' => __DIR__ . '/migration/first',
        // 'second' => __DIR__ . '/migration/second',
    ],
    'environments' => [
        'local' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'port' => 3306, // optional
            'username' => 'root',
            'password' => 'root',
            'db_name' => 'ujianundip',
            'charset' => 'utf8mb4',
        ],
        // 'production' => [
        //     'adapter' => 'mysql',
        //     'host' => 'production_host',
        //     'port' => 3306, // optional
        //     'username' => 'user',
        //     'password' => 'pass',
        //     'db_name' => 'my_production_db',
        //     'charset' => 'utf8mb4',
        // ],
        'APP1' => [ // untuk ujian.undip.ac.id
            'adapter' => 'mysql',
            'host' => '10.37.19.27',
            'port' => 3306, // optional
            'username' => 'idris',
            'password' => 'q1w2e3r4',
            'db_name' => 'ujian',
            'charset' => 'utf8mb4',
        ],
        'APP2' => [ // untuk catkerjasama.undip.ac.id
            'adapter' => 'mysql',
            'host' => '10.37.19.27',
            'port' => 3306, // optional
            'username' => 'idris',
            'password' => 'q1w2e3r4',
            'db_name' => 'oltest',
            'charset' => 'utf8mb4',
        ],
        'APP3' => [
            'adapter' => 'mysql',
            'host' => '10.37.19.27',
            'port' => 3306, // optional
            'username' => 'idris',
            'password' => 'q1w2e3r4',
            'db_name' => 'tryout_01',
            'charset' => 'utf8mb4',
        ],
        'APP3-2' => [
            'adapter' => 'mysql',
            'host' => '10.170.0.6',
            'port' => 3306, // optional
            'username' => 'db_tryout_dev',
            'password' => '2E5uViDA1Oyu',
            'db_name' => 'db_tryout',// salah ini
            'charset' => 'utf8mb4',
        ],
        'APP4' => [
            'adapter' => 'mysql',
            'host' => 'global-db',
            'port' => 3306, // optional
            'username' => 'tryout.exampel.id-GYZQS5',
            'password' => 'kl912AoYXVtT',
            'db_name' => 'tryout_exampel_id',
            'charset' => 'utf8mb4',
        ]
    ],
    'default_environment' => 'local',
    'log_table_name' => 'phoenix_log',
];