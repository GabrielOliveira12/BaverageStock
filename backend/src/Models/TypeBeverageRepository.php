<?php
namespace App\Models;

use PDO;
use PDOException;

class TypeBeverageRepository extends BaseModelRepository
{
    protected string $table = 'beverages';

    public function findByType(string $type): array
    {
        $query = "SELECT * FROM " . $this->table . " WHERE type = :type";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
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
}
