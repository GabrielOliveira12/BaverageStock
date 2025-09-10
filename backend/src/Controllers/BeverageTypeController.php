<?php

namespace App\Controllers;

use App\Models\BeverageTypeRepository;

/**
 * @OA\Schema(
 *     schema="BeverageType",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Cerveja"),
 *     @OA\Property(property="section_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class BeverageTypeController
{
    private BeverageTypeRepository $beverageTypeRepository;

    public function __construct()
    {
        $this->beverageTypeRepository = new BeverageTypeRepository();
    }

    /**
     * @OA\Get(
     *     path="/beverage-types",
     *     tags={"beverage-types"},
     *     summary="Listar tipos de bebida",
     *     description="Retorna todos os tipos de bebida cadastrados",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de bebida",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/BeverageType")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): void
    {
        try {
            $types = $this->beverageTypeRepository->findAll();
            echo json_encode([
                'success' => true,
                'data' => $types
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
     *     path="/beverage-types/{id}",
     *     tags={"beverage-types"},
     *     summary="Buscar tipo de bebida por ID",
     *     description="Retorna um tipo específico pelo ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do tipo de bebida",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/BeverageType")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo não encontrado"
     *     )
     * )
     */
    public function show(int $id): void
    {
        try {
            $type = $this->beverageTypeRepository->findById($id);
            if (!$type) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Tipo de bebida não encontrado'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'data' => $type
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
     *     path="/beverage-types",
     *     tags={"beverage-types"},
     *     summary="Criar tipo de bebida",
     *     description="Cria um novo tipo de bebida",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "section_id"},
     *             @OA\Property(property="name", type="string", example="Whisky"),
     *             @OA\Property(property="section_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo criado com sucesso"
     *     )
     * )
     */
    public function create(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['name']) || !isset($input['section_id'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Dados obrigatórios: name, section_id'
                ]);
                return;
            }

            $typeId = $this->beverageTypeRepository->create([
                'name' => $input['name'],
                'section_id' => $input['section_id']
            ]);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'data' => ['id' => $typeId],
                'message' => 'Tipo de bebida criado com sucesso'
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
     *     path="/beverage-types/{id}",
     *     tags={"beverage-types"},
     *     summary="Atualizar tipo de bebida",
     *     description="Atualiza um tipo existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do tipo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Whisky Premium"),
     *             @OA\Property(property="section_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo atualizado com sucesso"
     *     )
     * )
     */
    public function update(int $id): void
    {
        try {
            $type = $this->beverageTypeRepository->findById($id);
            if (!$type) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Tipo de bebida não encontrado'
                ]);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            
            $updateData = [];
            if (isset($input['name'])) $updateData['name'] = $input['name'];
            if (isset($input['section_id'])) $updateData['section_id'] = $input['section_id'];

            $this->beverageTypeRepository->update($id, $updateData);

            echo json_encode([
                'success' => true,
                'message' => 'Tipo de bebida atualizado com sucesso'
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
     *     path="/beverage-types/{id}",
     *     tags={"beverage-types"},
     *     summary="Deletar tipo de bebida",
     *     description="Remove um tipo do sistema",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do tipo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo deletado com sucesso"
     *     )
     * )
     */
    public function delete(int $id): void
    {
        try {
            $type = $this->beverageTypeRepository->findById($id);
            if (!$type) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Tipo de bebida não encontrado'
                ]);
                return;
            }

            $this->beverageTypeRepository->delete($id);

            echo json_encode([
                'success' => true,
                'message' => 'Tipo de bebida deletado com sucesso'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getByType(string $type): void
    {
        try {
            $types = $this->beverageTypeRepository->findByType($type);
            echo json_encode([
                'success' => true,
                'data' => $types
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
