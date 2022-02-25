<?php
declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;

class Database
{
    /**
     * @var PDO|null
     */
    public static ?PDO $pdo = null;

    /**
     * @return PDO
     */
    public static function connect(): PDO
    {
        if(self::$pdo !== null) {
            return self::$pdo;
        }

        $dsn = sprintf('mysql:host=%s;dbname=%s', getenv('DB_HOST'), getenv('DB_NAME'));
        $conn = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return self::$pdo = $conn;
    }

    /**
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        if(self::$pdo !== null) {
            return self::$pdo;
        }

        return self::connect();
    }
}