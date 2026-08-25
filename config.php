<?php

declare(strict_types=1);

function database(): mysqli
{
    static $connection = null;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    $config = [
        'host' => getenv('DB_HOST'),
        'port' => getenv('DB_PORT'),
        'user' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
        'database' => getenv('DB_NAME'),
    ];

    foreach ($config as $value) {
        if ($value === false || $value === '') {
            http_response_code(503);
            exit('Database configuration is incomplete.');
        }
    }

    $port = filter_var($config['port'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 65535],
    ]);

    if ($port === false) {
        http_response_code(503);
        exit('Database configuration is incomplete.');
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $connection = new mysqli(
            (string) $config['host'],
            (string) $config['user'],
            (string) $config['password'],
            (string) $config['database'],
            $port
        );
        $connection->set_charset('utf8mb4');
    } catch (mysqli_sql_exception) {
        error_log('Database connection failed.');
        http_response_code(503);
        exit('Database service is unavailable.');
    }

    return $connection;
}
