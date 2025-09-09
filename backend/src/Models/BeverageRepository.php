<?php

namespace App\Models;
use PDO;
use PDOException;

class BeverageRepository extends BaseModelRepository
{
    protected string $table = 'beverages';

    public function listWithSection() : array
    {
        $query = "SELECT b.*, s.name AS section_name, s.type_beverage 
                  FROM beverages b
                  LEFT JOIN beverage_section bs ON b.id = bs.beverage_id
                  LEFT JOIN sections s ON bs.section_id = s.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findBySectionId(int $sectionId): array
    {
        $query = "SELECT b.* 
                  FROM beverages b
                  INNER JOIN beverage_section bs ON b.id = bs.beverage_id
                  WHERE bs.section_id = :sectionId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}