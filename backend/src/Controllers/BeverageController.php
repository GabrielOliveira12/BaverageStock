<?php

namespace App\Controllers;

use App\Models\BeverageRepository;
use App\Models\HistoryRepository;
use App\Models\SectionRepository;

/**
 * @OA\Schema(
 *     schema="Beverage",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Cerveja Pilsen"),
 *     @OA\Property(property="brand", type="string", example="Brahma"),
 *     @OA\Property(property="volume_per_unit", type="number", format="float", example=0.355),
 *     @OA\Property(property="quantity", type="integer", example=24),
 *     @OA\Property(property="total_volume", type="number", format="float", example=8.52),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class BeverageController
{
    private BeverageRepository $beverageRepository;
    private HistoryRepository $historyRepository;
    private SectionRepository $sectionRepository;

    public function __construct()
    {
        $this->beverageRepository = new BeverageRepository();
        $this->historyRepository = new HistoryRepository();
        $this->sectionRepository = new SectionRepository();
    }

    /**
     * @OA\Get(
     *     path="/beverages",
     *     tags={"beverages"},
     *     summary="Listar todas as bebidas",
     *     description="Retorna a lista de todas as bebidas com detalhes de seção e tipo",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de bebidas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Beverage")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): void
    {
        try {
            $beverages = $this->beverageRepository->findWithDetails();
            echo json_encode([
                'success' => true,
                'data' => $beverages
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/beverages/{id}",
     *     tags={"beverages"},
     *     summary="Buscar bebida por ID",
     *     description="Retorna uma bebida específica pelo ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da bebida",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bebida encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Beverage")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bebida não encontrada"
     *     )
     * )
     */
    public function show(int $id): void
    {
        try {
            $beverage = $this->beverageRepository->findById($id);
            if (!$beverage) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Bebida não encontrada'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'data' => $beverage
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/beverages",
     *     tags={"beverages"},
     *     summary="Cadastrar nova bebida",
     *     description="Cria uma nova bebida e vincula a uma seção e tipo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "brand", "volume_per_unit", "quantity", "section_id", "beverage_type_id"},
     *             @OA\Property(property="name", type="string", example="Cerveja Pilsen"),
     *             @OA\Property(property="brand", type="string", example="Brahma"),
     *             @OA\Property(property="volume_per_unit", type="number", format="float", example=0.355),
     *             @OA\Property(property="quantity", type="integer", example=24),
     *             @OA\Property(property="section_id", type="integer", example=1),
     *             @OA\Property(property="beverage_type_id", type="integer", example=1),
     *             @OA\Property(property="responsible", type="string", example="João Silva")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bebida criada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer")),
     *             @OA\Property(property="message", type="string", example="Bebida criada com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos ou capacidade insuficiente"
     *     )
     * )
     */
    public function create(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $required = ['name', 'brand', 'volume_per_unit', 'quantity', 'section_id', 'beverage_type_id'];
            foreach ($required as $field) {
                if (!isset($input[$field])) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => "Campo obrigatório: $field"
                    ]);
                    return;
                }
            }

            $totalVolume = $input['volume_per_unit'] * $input['quantity'];
            if (!$this->sectionRepository->checkCapacity($input['section_id'], $totalVolume)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Capacidade insuficiente na seção'
                ]);
                return;
            }

            $beverageData = [
                'name' => $input['name'],
                'brand' => $input['brand'],
                'volume_per_unit' => $input['volume_per_unit'],
                'quantity' => $input['quantity']
            ];

            $beverageId = $this->beverageRepository->createWithLinks(
                $beverageData,
                $input['section_id'],
                $input['beverage_type_id']
            );
            
            // Atualizar volume da seção
            $this->sectionRepository->addVolume($input['section_id'], $totalVolume);

            $this->historyRepository->create([
                'operation_type' => 'entry',
                'beverage_id' => $beverageId,
                'section_id' => $input['section_id'],
                'beverage_type_id' => $input['beverage_type_id'],
                'quantity' => $input['quantity'],
                'volume' => $totalVolume,
                'responsible' => $input['responsible'] ?? 'Sistema'
            ]);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'data' => ['id' => $beverageId],
                'message' => 'Bebida criada com sucesso'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/beverages/section/{sectionId}",
     *     tags={"beverages"},
     *     summary="Buscar bebidas por seção",
     *     description="Retorna bebidas armazenadas em uma seção específica",
     *     @OA\Parameter(
     *         name="sectionId",
     *         in="path",
     *         required=true,
     *         description="ID da seção",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de bebidas da seção",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Beverage")
     *             )
     *         )
     *     )
     * )
     */
    public function getBySection(int $sectionId): void
    {
        try {
            $beverages = $this->beverageRepository->findBySection($sectionId);
            echo json_encode([
                'success' => true,
                'data' => $beverages
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Put(
     *     path="/beverages/{id}",
     *     tags={"beverages"},
     *     summary="Atualizar bebida",
     *     description="Atualiza uma bebida existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da bebida",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Cerveja Premium"),
     *             @OA\Property(property="brand", type="string", example="Brahma Premium"),
     *             @OA\Property(property="volume_per_unit", type="number", format="float", example=500),
     *             @OA\Property(property="quantity", type="integer", example=12)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bebida atualizada com sucesso"
     *     )
     * )
     */
    public function update(int $id): void
    {
        try {
            $beverage = $this->beverageRepository->findById($id);
            if (!$beverage) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Bebida não encontrada'
                ]);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            
            $updateData = [];
            if (isset($input['name'])) $updateData['name'] = $input['name'];
            if (isset($input['brand'])) $updateData['brand'] = $input['brand'];
            if (isset($input['volume_per_unit'])) $updateData['volume_per_unit'] = $input['volume_per_unit'];
            if (isset($input['quantity'])) $updateData['quantity'] = $input['quantity'];

            $this->beverageRepository->update($id, $updateData);
            
            // Se volume ou quantidade mudaram, recalcular o volume da seção
            if (isset($input['volume_per_unit']) || isset($input['quantity'])) {
                // Encontrar a seção desta bebida
                $beverages = $this->beverageRepository->findWithDetails();
                foreach ($beverages as $bev) {
                    if ($bev['id'] == $id) {
                        $this->sectionRepository->recalculateVolume($bev['section_id']);
                        break;
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Bebida atualizada com sucesso'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/beverages/{id}",
     *     tags={"beverages"},
     *     summary="Deletar bebida",
     *     description="Remove uma bebida do sistema",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da bebida",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bebida deletada com sucesso"
     *     )
     * )
     */
    public function delete(int $id): void
    {
        try {
            $beverage = $this->beverageRepository->findById($id);
            if (!$beverage) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Bebida não encontrada'
                ]);
                return;
            }

            // Encontrar a seção desta bebida antes de deletar
            $beverageDetails = $this->beverageRepository->findWithDetails();
            $sectionId = null;
            $totalVolume = 0;
            
            foreach ($beverageDetails as $bev) {
                if ($bev['id'] == $id) {
                    $sectionId = $bev['section_id'];
                    $totalVolume = $bev['total_volume'];
                    break;
                }
            }

            $this->beverageRepository->delete($id);
            
            // Subtrair volume da seção
            if ($sectionId) {
                $this->sectionRepository->subtractVolume($sectionId, $totalVolume);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Bebida deletada com sucesso'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}