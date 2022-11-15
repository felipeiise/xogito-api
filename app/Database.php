<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;

/**
 * Class to handle database
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $conn;

    private $host = DB_HOST;
    private $port = DB_PORT;
    private $user = DB_USER;
    private $password = DB_PASSWORD;
    private $database = DB_DATABASE;

    private function __construct()
    {
        $this->conn = new PDO(
            'pgsql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->database . ';',
            $this->user,
            $this->password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    /**
     * @return Database|null
     */
    public static function getInstance(): ?Database
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        try {
            return $this->conn;
        } catch (PDOException $exception) {
            die($exception->getMessage());
        }
    }
}