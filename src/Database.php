<?php

namespace Framework;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private PDO $connection;

    public function __construct(
        string $host,
        string $db,
        string $user,
        string $pass,
        int $port = 3306
    ) {
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

            $this->connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]);
        } catch (PDOException $e) {
            error_log("DB ERROR: " . $e->getMessage());
            http_response_code(500);
            die("Database connection failed");
        }
    }

    public function query(string $query): PDOStatement|false
    {
        return $this->connection->query($query);
    }

    /**
     * @param string $sql
     * @param array<string>|null $params
     * @return PDOStatement
     */
    public function run(string $sql, array|null $params = null): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function prepare(string $sql): PDOStatement
    {
        return $this->connection->prepare($sql);
    }

    public function exec(string $sql): false|int
    {
        return $this->connection->exec($sql);
    }

    public function getLastID(string|null $field = null): int
    {
        return (int)$this->connection->lastInsertId($field);
    }

    public function migrate(string $migrationsDirectory): void
    {
        $files = scandir($migrationsDirectory);
        if ($files === false) {
            die('Could not read database migration files');
        }
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            echo "Migrating: " . $file . "\n";
            if ($contents = file_get_contents($migrationsDirectory . $file)) {
                $this->connection->exec($contents);
            }
        }
    }
}
