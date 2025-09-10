<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Config\DatabaseConfig;
use PDO;

abstract class BaseTestCase extends TestCase
{
    protected ?PDO $db;
    protected DatabaseConfig $database;

    protected function setUp(): void
    {
        parent::setUp();
        
        try {
            $this->database = new DatabaseConfig();
            $connection = $this->database->getConnection();
            
            if ($connection === null) {
                $this->fail('Failed to establish database connection - getConnection() returned null');
            }
            
            $this->db = $connection;
        } catch (\Exception $e) {
            $this->fail('Database connection failed: ' . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        $this->db = null;
        parent::tearDown();
    }

    protected function createMockData(): array
    {
        return [
            'sections' => [
                ['id' => 1, 'name' => 'Test Section 1', 'type' => 'alcoholic', 'max_capacity' => 500000],
                ['id' => 2, 'name' => 'Test Section 2', 'type' => 'non_alcoholic', 'max_capacity' => 400000]
            ],
            'beverage_types' => [
                ['id' => 1, 'name' => 'Test Beer', 'section_id' => 1],
                ['id' => 2, 'name' => 'Test Soda', 'section_id' => 2]
            ],
            'beverages' => [
                ['id' => 1, 'name' => 'Test Beverage', 'brand' => 'Test Brand', 'volume_per_unit' => 500, 'quantity' => 12]
            ]
        ];
    }

    protected function cleanupTestData(): void
    {
        try {
            // Limpar em ordem correta devido às foreign keys
            $this->db->exec("DELETE FROM history WHERE responsible LIKE 'Test%'");
            
            // Primeiro, encontrar beverages de teste e limpar suas associações
            $stmt = $this->db->prepare("SELECT id FROM beverages WHERE name LIKE 'Test%'");
            $stmt->execute();
            $testBeverages = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($testBeverages)) {
                $ids = implode(',', $testBeverages);
                $this->db->exec("DELETE FROM beverage_links WHERE beverage_id IN ($ids)");
                $this->db->exec("DELETE FROM history WHERE beverage_id IN ($ids)");
            }
            
            $this->db->exec("DELETE FROM beverages WHERE name LIKE 'Test%'");
            $this->db->exec("DELETE FROM beverage_types WHERE name LIKE 'Test%'");
            $this->db->exec("DELETE FROM sections WHERE name LIKE 'Test%'");
        } catch (\Exception $e) {
            // Ignora erros de cleanup - pode ser que os dados já não existam
        }
    }
}
