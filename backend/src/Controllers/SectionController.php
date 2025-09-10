<?php

namespace App\Controllers;

use App\Models\SectionRepository;

/**
 * @OA\Schema(
 *     schema="Section",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Seção Alcoólicas A"),
 *     @OA\Property(property="type", type="string", enum={"alcoholic", "non_alcoholic"}, example="alcoholic"),
 *     @OA\Property(property="max_capacity", type="number", format="float", example=500.00),
 *     @OA\Property(property="current_volume", type="number", format="float", example=150.50),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class SectionController
{
    private SectionRepository $sectionRepository;

    public function __construct()
    {
        $this->sectionRepository = new SectionRepository();
    }

    /**
     * @OA\Get(
     *     path="/sections",
     *     tags={"sections"},
     *     summary="Listar todas as seções",
     *     description="Retorna a lista de todas as seções cadastradas",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de seções",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Section")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): void
    {
        try {
            $sections = $this->sectionRepository->findAll();
            echo json_encode([
                'success' => true,
                'data' => $sections
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
     *     path="/sections/{id}",
     *     tags={"sections"},
     *     summary="Buscar seção por ID",
     *     description="Retorna uma seção específica pelo ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da seção",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Seção encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Section")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Seção não encontrada"
     *     )
     * )
     */
    public function show(int $id): void
    {
        try {
            $section = $this->sectionRepository->findById($id);
            if (!$section) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Seção não encontrada'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'data' => $section
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
     *     path="/sections",
     *     tags={"sections"},
     *     summary="Criar nova seção",
     *     description="Cria uma nova seção de armazenamento",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "type", "max_capacity"},
     *             @OA\Property(property="name", type="string", example="Seção Alcoólicas B"),
     *             @OA\Property(property="type", type="string", enum={"alcoholic", "non_alcoholic"}, example="alcoholic"),
     *             @OA\Property(property="max_capacity", type="number", format="float", example=500.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Seção criada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer")),
     *             @OA\Property(property="message", type="string", example="Seção criada com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos"
     *     )
     * )
     */
    public function create(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['name']) || !isset($input['type']) || !isset($input['max_capacity'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Dados obrigatórios: name, type, max_capacity'
                ]);
                return;
            }

            $sectionId = $this->sectionRepository->create([
                'name' => $input['name'],
                'type' => $input['type'],
                'max_capacity' => $input['max_capacity']
            ]);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'data' => ['id' => $sectionId],
                'message' => 'Seção criada com sucesso'
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
     *     path="/sections/type/{type}",
     *     tags={"sections"},
     *     summary="Buscar seções por tipo",
     *     description="Retorna seções filtradas por tipo",
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Tipo da seção",
     *         @OA\Schema(type="string", enum={"alcoholic", "non_alcoholic"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de seções do tipo especificado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Section")
     *             )
     *         )
     *     )
     * )
     */
    public function getByType(string $type): void
    {
        try {
            $sections = $this->sectionRepository->findByType($type);
            echo json_encode([
                'success' => true,
                'data' => $sections
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
     *     path="/sections/volume/{type}",
     *     tags={"sections"},
     *     summary="Consultar volume total por tipo",
     *     description="Retorna o volume total armazenado por tipo de bebida",
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Tipo da bebida",
     *         @OA\Schema(type="string", enum={"alcoholic", "non_alcoholic"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Volume total por tipo",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="type", type="string", example="alcoholic"),
     *                 @OA\Property(property="total_volume", type="number", format="float", example=245.75)
     *             )
     *         )
     *     )
     * )
     */
    public function getTotalVolume(string $type): void
    {
        try {
            $totalVolume = $this->sectionRepository->getTotalVolumeByType($type);
            echo json_encode([
                'success' => true,
                'data' => [
                    'type' => $type,
                    'total_volume' => $totalVolume
                ]
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
     *     path="/sections/{id}",
     *     tags={"sections"},
     *     summary="Atualizar seção",
     *     description="Atualiza uma seção existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da seção",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Seção Alcoólicas Atualizada"),
     *             @OA\Property(property="type", type="string", enum={"alcoholic", "non_alcoholic"}, example="alcoholic"),
     *             @OA\Property(property="max_capacity", type="number", format="float", example=600.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Seção atualizada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Seção não encontrada"
     *     )
     * )
     */
    public function update(int $id): void
    {
        try {
            $section = $this->sectionRepository->findById($id);
            if (!$section) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Seção não encontrada'
                ]);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            
            $updateData = [];
            if (isset($input['name'])) $updateData['name'] = $input['name'];
            if (isset($input['type'])) $updateData['type'] = $input['type'];
            if (isset($input['max_capacity'])) $updateData['max_capacity'] = $input['max_capacity'];

            $this->sectionRepository->update($id, $updateData);

            echo json_encode([
                'success' => true,
                'message' => 'Seção atualizada com sucesso'
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
     *     path="/sections/{id}",
     *     tags={"sections"},
     *     summary="Deletar seção",
     *     description="Remove uma seção do sistema",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da seção",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Seção deletada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Seção não encontrada"
     *     )
     * )
     */
    public function delete(int $id): void
    {
        try {
            $section = $this->sectionRepository->findById($id);
            if (!$section) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Seção não encontrada'
                ]);
                return;
            }

            $this->sectionRepository->delete($id);

            echo json_encode([
                'success' => true,
                'message' => 'Seção deletada com sucesso'
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
