<?php
namespace App\Config;

use PDO;
use PDOException;

class DatabaseConfig 
{
    private $host;
    private $dbName;
    private $username;
    private $password;
    private $connection;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->dbName = $_ENV['DB_DATABASE'];
        $this->username = $_ENV['DB_USERNAME'];
        $this->password = $_ENV['DB_PASSWORD'];
    }

    public function getConnection(): ?PDO
    {
        $this->connection = null;

        try {
            $port = $_ENV['DB_PORT'] ?? '3306';
            $dsn = "mysql:host=" . $this->host . ";port=" . $port . ";dbname=" . $this->dbName . ";charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Definir charset UTF-8 para a conexão
            $this->connection->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->connection->exec("SET CHARACTER SET utf8mb4");
        } catch (PDOException $e) {
            throw new PDOException("Erro de conexão: " . $e->getMessage());
        }
        return $this->connection;
    }
}