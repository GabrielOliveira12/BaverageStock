<?php
namespace App\Models;

use App\Config\DatabaseConfig;
use PDO;
use PDOException;

abstract class BaseModelRepository
{
    protected $db;
    protected string $table;

    public function __construct()
    {
        $database = new DatabaseConfig();
        $this->db = $database->getConnection();
    }

     public function findAll(): array
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $query = "INSERT INTO " . $this->table . " (" . $columns . ") VALUES (" . $placeholders . ")";
        $stmt = $this->db->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = $key . ' = :' . $key;
        }
        
        $query = "UPDATE " . $this->table . " SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
