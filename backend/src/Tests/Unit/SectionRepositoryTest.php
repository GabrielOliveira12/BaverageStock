<?php

namespace App\Tests\Unit;

use App\Tests\BaseTestCase;
use App\Models\SectionRepository;
use PHPUnit\Framework\Attributes\Test;

class SectionRepositoryTest extends BaseTestCase
{
    private SectionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new SectionRepository();
        $this->cleanupTestData();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
        parent::tearDown();
    }

    #[Test]
    public function testCreateSection(): void
    {
        $data = [
            'name' => 'Test Section Create',
            'type' => 'alcoholic',
            'max_capacity' => 500000
        ];

        $id = $this->repository->create($data);
        
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    #[Test]
    public function testFindSectionById(): void
    {
        // Criar uma seção para teste
        $data = [
            'name' => 'Test Section Find',
            'type' => 'non_alcoholic',
            'max_capacity' => 400000
        ];
        $id = $this->repository->create($data);

        // Buscar a seção
        $section = $this->repository->findById($id);

        $this->assertNotNull($section);
        $this->assertEquals('Test Section Find', $section['name']);
        $this->assertEquals('non_alcoholic', $section['type']);
        $this->assertEquals('400000.00', $section['max_capacity']);
    }

    #[Test]
    public function testFindSectionsByType(): void
    {
        // Criar seções de teste
        $this->repository->create([
            'name' => 'Test Alcoholic 1',
            'type' => 'alcoholic',
            'max_capacity' => 500000
        ]);

        $this->repository->create([
            'name' => 'Test Alcoholic 2',
            'type' => 'alcoholic',
            'max_capacity' => 600000
        ]);

        $this->repository->create([
            'name' => 'Test Non Alcoholic',
            'type' => 'non_alcoholic',
            'max_capacity' => 400000
        ]);

        // Buscar por tipo
        $alcoholicSections = $this->repository->findByType('alcoholic');
        $nonAlcoholicSections = $this->repository->findByType('non_alcoholic');

        $this->assertGreaterThanOrEqual(2, count($alcoholicSections));
        $this->assertGreaterThanOrEqual(1, count($nonAlcoholicSections));
    }

    #[Test]
    public function testUpdateSection(): void
    {
        // Criar seção
        $id = $this->repository->create([
            'name' => 'Test Section Update',
            'type' => 'alcoholic',
            'max_capacity' => 500000
        ]);

        // Atualizar
        $result = $this->repository->update($id, [
            'name' => 'Test Section Updated',
            'max_capacity' => 550000
        ]);

        $this->assertTrue($result);

        // Verificar atualização
        $section = $this->repository->findById($id);
        $this->assertEquals('Test Section Updated', $section['name']);
        $this->assertEquals('550000.00', $section['max_capacity']);
    }

    #[Test]
    public function testDeleteSection(): void
    {
        // Criar seção
        $id = $this->repository->create([
            'name' => 'Test Section Delete',
            'type' => 'alcoholic',
            'max_capacity' => 500000
        ]);

        // Verificar que existe
        $section = $this->repository->findById($id);
        $this->assertNotNull($section);

        // Deletar
        $result = $this->repository->delete($id);
        $this->assertTrue($result);

        // Verificar que não existe mais
        $section = $this->repository->findById($id);
        $this->assertNull($section);
    }

    #[Test]
    public function testCheckCapacity(): void
    {
        // Criar seção com capacidade conhecida
        $id = $this->repository->create([
            'name' => 'Test Section Capacity',
            'type' => 'alcoholic',
            'max_capacity' => 1000
        ]);

        // Testar capacidade disponível
        $canAdd500 = $this->repository->checkCapacity($id, 500);
        $this->assertTrue($canAdd500);

        $canAdd1500 = $this->repository->checkCapacity($id, 1500);
        $this->assertFalse($canAdd1500);
    }
}
