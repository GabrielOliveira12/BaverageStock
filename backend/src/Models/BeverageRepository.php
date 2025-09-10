<?php

namespace App\Models;
use PDO;

class BeverageRepository extends BaseModelRepository
{
    protected string $table = 'beverages';
    
    public function createWithLinks(array $beverageData, int $sectionId, int $beverageTypeId): int
    {
        try {
            // Criar a bebida primeiro
            $beverageId = $this->create($beverageData);
            
            // Criar o link
            $linkRepository = new BeverageLinkRepository();
            $linkRepository->create([
                'beverage_id' => $beverageId,
                'section_id' => $sectionId,
                'beverage_type_id' => $beverageTypeId
            ]);
            
            return $beverageId;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function findWithDetails(): array
    {
        $query = "SELECT b.*, s.name as section_name, bt.name as type_name, bl.section_id, bl.beverage_type_id
                  FROM " . $this->table . " b
                  JOIN beverage_links bl ON b.id = bl.beverage_id
                  JOIN sections s ON bl.section_id = s.id
                  JOIN beverage_types bt ON bl.beverage_type_id = bt.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findBySection(int $sectionId): array
    {
        $query = "SELECT b.*, bt.name as type_name
                  FROM " . $this->table . " b
                  JOIN beverage_links bl ON b.id = bl.beverage_id
                  JOIN beverage_types bt ON bl.beverage_type_id = bt.id
                  WHERE bl.section_id = :section_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':section_id', $sectionId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}