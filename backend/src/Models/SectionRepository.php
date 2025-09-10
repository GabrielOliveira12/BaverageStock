<?php

namespace App\Models;

class SectionRepository extends BaseModelRepository
{
    protected string $table = 'sections';
    
    public function findByType(string $type): array
    {
        $query = "SELECT * FROM " . $this->table . " WHERE type = :type";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function checkCapacity(int $sectionId, float $volumeToAdd): bool
    {
        $query = "SELECT (max_capacity - current_volume) as available_capacity FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $sectionId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result && $result['available_capacity'] >= $volumeToAdd;
    }
    
    public function getTotalVolumeByType(string $type): float
    {
        $query = "SELECT SUM(current_volume) as total_volume FROM " . $this->table . " WHERE type = :type";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total_volume'] ?? 0.0;
    }
    
    public function addVolume(int $sectionId, float $volume): bool
    {
        $query = "UPDATE " . $this->table . " SET current_volume = current_volume + :volume WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':volume', $volume);
        $stmt->bindParam(':id', $sectionId);
        return $stmt->execute();
    }
    
    public function subtractVolume(int $sectionId, float $volume): bool
    {
        $query = "UPDATE " . $this->table . " SET current_volume = GREATEST(0, current_volume - :volume) WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':volume', $volume);
        $stmt->bindParam(':id', $sectionId);
        return $stmt->execute();
    }
    
    public function recalculateVolume(int $sectionId): bool
    {
        $query = "UPDATE " . $this->table . " s SET current_volume = (
            SELECT COALESCE(SUM(b.total_volume), 0) 
            FROM beverages b 
            JOIN beverage_links bl ON b.id = bl.beverage_id 
            WHERE bl.section_id = s.id
        ) WHERE s.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $sectionId);
        return $stmt->execute();
    }
}
