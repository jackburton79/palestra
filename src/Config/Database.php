<?php

namespace Config;

use PDO;


class Database
{
    private $connection;
    
    public function connect()
    {
        $configs = include(__DIR__ . '/config.php');
        try {
            $this->connection = new PDO("mysql:host=$configs->dbHost;dbname=$configs->dbName",
                    $configs->dbUser, $configs->dbPass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->connection;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
            // http_response_code(500);
            //echo json_encode(['error' => 'Database connection failed']);
        }
    }
}
