<?php

namespace App\Tests\Unit;

use App\Tests\BaseTestCase;
use App\Models\BeverageRepository;
use App\Models\BeverageLinkRepository;
use App\Models\SectionRepository;
use App\Models\BeverageTypeRepository;
use PHPUnit\Framework\Attributes\Test;

class BeverageRepositoryTest extends BaseTestCase
{
    private BeverageRepository $repository;
    private SectionRepository $sectionRepository;
    private BeverageTypeRepository $typeRepository;
    private int $testSectionId;
    private int $testTypeId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new BeverageRepository();
        $this->sectionRepository = new SectionRepository();
        $this->typeRepository = new BeverageTypeRepository();
        $this->cleanupTestData();

        // Criar seção e tipo para testes
        $this->testSectionId = $this->sectionRepository->create([
            'name' => 'Test Section for Beverages',
            'type' => 'alcoholic',
            'max_capacity' => 500000
        ]);

        $this->testTypeId = $this->typeRepository->create([
            'name' => 'Test Beer Type',
            'section_id' => $this->testSectionId
        ]);
    }

    protected function tearDown(): void
    {
        $this->cleanupTestData();
        parent::tearDown();
    }

    #[Test]
    public function testCreateBeverage(): void
    {
        $data = [
            'name' => 'Test Beer',
            'brand' => 'Test Brand',
            'volume_per_unit' => 500,
            'quantity' => 12
        ];

        $id = $this->repository->create($data);
        
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    #[Test]
    public function testCreateBeverageWithLinks(): void
    {
        $beverageData = [
            'name' => 'Test Beer with Links',
            'brand' => 'Test Brand',
            'volume_per_unit' => 500,
            'quantity' => 24
        ];

        $beverageId = $this->repository->createWithLinks(
            $beverageData,
            $this->testSectionId,
            $this->testTypeId
        );

        $this->assertIsInt($beverageId);
        $this->assertGreaterThan(0, $beverageId);

        // Verificar se o link foi criado
        $linkRepository = new BeverageLinkRepository();
        $links = $linkRepository->findAll();
        
        $linkFound = false;
        foreach ($links as $link) {
            if ($link['beverage_id'] == $beverageId) {
                $linkFound = true;
                $this->assertEquals($this->testSectionId, $link['section_id']);
                $this->assertEquals($this->testTypeId, $link['beverage_type_id']);
                break;
            }
        }
        
        $this->assertTrue($linkFound, 'Link não foi criado');
    }

    #[Test]
    public function testFindBeverageById(): void
    {
        $data = [
            'name' => 'Test Wine',
            'brand' => 'Test Winery',
            'volume_per_unit' => 750,
            'quantity' => 6
        ];
        $id = $this->repository->create($data);

        $beverage = $this->repository->findById($id);

        $this->assertNotNull($beverage);
        $this->assertEquals('Test Wine', $beverage['name']);
        $this->assertEquals('Test Winery', $beverage['brand']);
        $this->assertEquals('750.00', $beverage['volume_per_unit']);
        $this->assertEquals(6, $beverage['quantity']);
        $this->assertEquals('4500.00', $beverage['total_volume']); // 750 * 6
    }

    #[Test]
    public function testUpdateBeverage(): void
    {
        $id = $this->repository->create([
            'name' => 'Test Beverage Update',
            'brand' => 'Original Brand',
            'volume_per_unit' => 500,
            'quantity' => 12
        ]);

        $result = $this->repository->update($id, [
            'name' => 'Test Beverage Updated',
            'brand' => 'New Brand',
            'quantity' => 24
        ]);

        $this->assertTrue($result);

        $beverage = $this->repository->findById($id);
        $this->assertEquals('Test Beverage Updated', $beverage['name']);
        $this->assertEquals('New Brand', $beverage['brand']);
        $this->assertEquals(24, $beverage['quantity']);
        $this->assertEquals('12000.00', $beverage['total_volume']); // 500 * 24
    }

    #[Test]
    public function testDeleteBeverage(): void
    {
        $id = $this->repository->create([
            'name' => 'Test Beverage Delete',
            'brand' => 'Test Brand',
            'volume_per_unit' => 500,
            'quantity' => 12
        ]);

        $beverage = $this->repository->findById($id);
        $this->assertNotNull($beverage);

        $result = $this->repository->delete($id);
        $this->assertTrue($result);

        $beverage = $this->repository->findById($id);
        $this->assertNull($beverage);
    }

    #[Test]
    public function testFindBySection(): void
    {
        // Criar bebidas com links
        $beverage1Id = $this->repository->createWithLinks([
            'name' => 'Test Beer 1',
            'brand' => 'Brand 1',
            'volume_per_unit' => 500,
            'quantity' => 12
        ], $this->testSectionId, $this->testTypeId);

        $beverage2Id = $this->repository->createWithLinks([
            'name' => 'Test Beer 2',
            'brand' => 'Brand 2',
            'volume_per_unit' => 350,
            'quantity' => 24
        ], $this->testSectionId, $this->testTypeId);

        $beveragesInSection = $this->repository->findBySection($this->testSectionId);

        $this->assertIsArray($beveragesInSection);
        $this->assertGreaterThanOrEqual(2, count($beveragesInSection));

        $foundIds = array_column($beveragesInSection, 'id');
        $this->assertContains($beverage1Id, $foundIds);
        $this->assertContains($beverage2Id, $foundIds);
    }
}
