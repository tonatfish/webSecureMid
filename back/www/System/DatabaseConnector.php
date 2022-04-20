<?php
namespace Src\System;

class DatabaseConnector {

    private $dbConnection = null;

    public function __construct()
    {
        $host = 'localhost';// getenv('DB_HOST');
        $port = '3307'; // getenv('DB_PORT');
        $db   = 'webpage_mid'; // getenv('DB_DATABASE');
        $user = 'api_user'; // getenv('DB_USERNAME');
        $pass = 'api_password'; // getenv('DB_PASSWORD');

        try {
            $this->dbConnection = new \PDO(
                "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db",
                $user,
                $pass
            );
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            exit($e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->dbConnection;
    }
}

