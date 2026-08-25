<?php

$database_config = [
    'DB_HOST' => getenv('DB_HOST'),
    'DB_USER' => getenv('DB_USER'),
    'DB_PASSWORD' => getenv('DB_PASSWORD'),
    'DB_NAME' => getenv('DB_NAME'),
];

$missing_environment_variables = [];
foreach ($database_config as $name => $value) {
    if ($value === false || $value === '') {
        $missing_environment_variables[] = $name;
    }
}

if ($missing_environment_variables !== []) {
    throw new RuntimeException(
        'Missing database environment variables: ' . implode(', ', $missing_environment_variables)
    );
}

$db_connection = new mysqli(
    $database_config['DB_HOST'],
    $database_config['DB_USER'],
    $database_config['DB_PASSWORD'],
    $database_config['DB_NAME']
);

if ($db_connection->connect_errno !== 0) {
    throw new RuntimeException('Database connection failed.');
}
