<?php

namespace App\Models;

class BeverageTypeRepository extends BaseModelRepository
{
    protected string $table = 'beverage_types';
    
    public function findByType(string $type): array
    {
        $query = "SELECT * FROM " . $this->table . " WHERE section_type = :type";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
