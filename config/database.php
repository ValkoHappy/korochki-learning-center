<?php
$local = __DIR__ . '/local.php';
return is_file($local) ? require $local : [
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => (int) (getenv('DB_PORT') ?: 3306),
    'name' => getenv('DB_NAME') ?: 'korochki',
    'user' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
];
