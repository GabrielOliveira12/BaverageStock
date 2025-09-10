<?php

namespace App\Models;

class HistoryRepository extends BaseModelRepository
{
    protected string $table = 'history';
    
    public function findBySection(int $sectionId, string $orderBy = 'created_at', string $order = 'DESC'): array
    {
        $allowedColumns = ['created_at', 'section_id', 'beverage_type_id'];
        $allowedOrders = ['ASC', 'DESC'];
        
        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'created_at';
        }
        if (!in_array($order, $allowedOrders)) {
            $order = 'DESC';
        }
        
        $query = "SELECT h.*, b.name as beverage_name, s.name as section_name, bt.name as type_name
                  FROM " . $this->table . " h
                  JOIN beverages b ON h.beverage_id = b.id
                  JOIN sections s ON h.section_id = s.id
                  JOIN beverage_types bt ON h.beverage_type_id = bt.id
                  WHERE h.section_id = :section_id
                  ORDER BY " . $orderBy . " " . $order;
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':section_id', $sectionId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findByDate(string $startDate, string $endDate, string $orderBy = 'created_at', string $order = 'DESC'): array
    {
        $allowedColumns = ['created_at', 'section_id', 'beverage_type_id'];
        $allowedOrders = ['ASC', 'DESC'];
        
        if (!in_array($orderBy, $allowedColumns)) {
            $orderBy = 'created_at';
        }
        if (!in_array($order, $allowedOrders)) {
            $order = 'DESC';
        }
        
        $query = "SELECT h.*, b.name as beverage_name, s.name as section_name, bt.name as type_name
                  FROM " . $this->table . " h
                  JOIN beverages b ON h.beverage_id = b.id
                  JOIN sections s ON h.section_id = s.id
                  JOIN beverage_types bt ON h.beverage_type_id = bt.id
                  WHERE DATE(h.created_at) BETWEEN :start_date AND :end_date
                  ORDER BY " . $orderBy . " " . $order;
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function createMovement(array $movementData): int
    {
        $this->db->beginTransaction();
        
        try {
            $historyId = $this->create($movementData);
            $this->db->commit();
            return $historyId;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
