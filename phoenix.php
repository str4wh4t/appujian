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
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'], // optional
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'db_name' => $_ENV['DB_NAME'],
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4',
        ],
    ],
    'default_environment' => 'local',
    'log_table_name' => 'phoenix_log',
];