<?php
declare(strict_types=1);

final class Database
{
    public static function connect(): PDO
    {
        $config = require dirname(__DIR__) . '/config/database.php';
        return new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $config['host'], $config['port'], $config['name']),
            $config['user'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
        );
    }
}
