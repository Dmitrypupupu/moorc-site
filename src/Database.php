<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $host    = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port    = (int) ($_ENV['DB_PORT'] ?? 5432);
            $db      = $_ENV['DB_DATABASE'] ?? 'moorc_dev';
            $user    = $_ENV['DB_USERNAME'] ?? 'moorc';
            $pass    = $_ENV['DB_PASSWORD'] ?? 'moorc';
            $sslmode = $_ENV['DB_SSLMODE'] ?? 'prefer';

            $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                throw $e;
            }
        }

        return self::$pdo;
    }
}