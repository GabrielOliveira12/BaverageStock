<?php

namespace App\Models;

class BeverageLinkRepository extends BaseModelRepository
{
    protected string $table = 'beverage_links';
    
    public function findByBeverage(int $beverageId): ?array
    {
        $query = "SELECT * FROM " . $this->table . " WHERE beverage_id = :beverage_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':beverage_id', $beverageId);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }
    
    public function findBySection(int $sectionId): array
    {
        $query = "SELECT * FROM " . $this->table . " WHERE section_id = :section_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':section_id', $sectionId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
