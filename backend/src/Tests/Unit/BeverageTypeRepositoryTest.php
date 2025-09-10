<?php

namespace App\Tests\Unit;

use App\Tests\BaseTestCase;
use App\Models\BeverageTypeRepository;
use App\Models\SectionRepository;
use PHPUnit\Framework\Attributes\Test;

class BeverageTypeRepositoryTest extends BaseTestCase
{
    private BeverageTypeRepository $repository;
    private SectionRepository $sectionRepository;
    private int $testSectionId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new BeverageTypeRepository();
        $this->sectionRepository = new SectionRepository();
        $this->cleanupTestData();

        // Criar seção para testes
        $this->testSectionId = $this->sectionRepository->create([
            'name' => 'Test Section for Types',
            'type' => 'alcoholic',
            'max_capacity' => 500000
        ]);
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
        parent::tearDown();
    }

    #[Test]
    public function testCreateBeverageType(): void
    {
        $data = [
            'name' => 'Test Beer Type',
            'section_id' => $this->testSectionId
        ];

        $id = $this->repository->create($data);
        
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    #[Test]
    public function testFindBeverageTypeById(): void
    {
        $data = [
            'name' => 'Test Wine Type',
            'section_id' => $this->testSectionId
        ];
        $id = $this->repository->create($data);

        $type = $this->repository->findById($id);

        $this->assertNotNull($type);
        $this->assertEquals('Test Wine Type', $type['name']);
        $this->assertEquals($this->testSectionId, $type['section_id']);
    }

    #[Test]
    public function testUpdateBeverageType(): void
    {
        $id = $this->repository->create([
            'name' => 'Test Type Update',
            'section_id' => $this->testSectionId
        ]);

        $result = $this->repository->update($id, [
            'name' => 'Test Type Updated'
        ]);

        $this->assertTrue($result);

        $type = $this->repository->findById($id);
        $this->assertEquals('Test Type Updated', $type['name']);
    }

    #[Test]
    public function testDeleteBeverageType(): void
    {
        $id = $this->repository->create([
            'name' => 'Test Type Delete',
            'section_id' => $this->testSectionId
        ]);

        $type = $this->repository->findById($id);
        $this->assertNotNull($type);

        $result = $this->repository->delete($id);
        $this->assertTrue($result);

        $type = $this->repository->findById($id);
        $this->assertNull($type);
    }

    #[Test]
    public function testFindAllBeverageTypes(): void
    {
        $this->repository->create([
            'name' => 'Test Type 1',
            'section_id' => $this->testSectionId
        ]);

        $this->repository->create([
            'name' => 'Test Type 2',
            'section_id' => $this->testSectionId
        ]);

        $types = $this->repository->findAll();
        $this->assertIsArray($types);
        $this->assertGreaterThanOrEqual(2, count($types));
    }
}
