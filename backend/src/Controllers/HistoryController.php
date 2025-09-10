<?php

namespace App\Controllers;

use App\Models\HistoryRepository;

class HistoryController
{
    private HistoryRepository $historyRepository;

    public function __construct()
    {
        $this->historyRepository = new HistoryRepository();
    }

    public function index(): void
    {
        try {
            $orderBy = $_GET['order_by'] ?? 'created_at';
            $order = $_GET['order'] ?? 'DESC';
            $sectionId = $_GET['section_id'] ?? null;
            $startDate = $_GET['start_date'] ?? null;
            $endDate = $_GET['end_date'] ?? null;

            if ($sectionId) {
                $history = $this->historyRepository->findBySection($sectionId, $orderBy, $order);
            } elseif ($startDate && $endDate) {
                $history = $this->historyRepository->findByDate($startDate, $endDate, $orderBy, $order);
            } else {
                $history = $this->historyRepository->findAll();
            }

            echo json_encode([
                'success' => true,
                'data' => $history
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function create(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $required = ['operation_type', 'beverage_id', 'section_id', 'beverage_type_id', 'quantity', 'volume', 'responsible'];
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

            $historyId = $this->historyRepository->createMovement($input);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'data' => ['id' => $historyId],
                'message' => 'Movimentação registrada com sucesso'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getBySection(int $sectionId): void
    {
        try {
            $orderBy = $_GET['order_by'] ?? 'created_at';
            $order = $_GET['order'] ?? 'DESC';
            
            $history = $this->historyRepository->findBySection($sectionId, $orderBy, $order);
            
            echo json_encode([
                'success' => true,
                'data' => $history
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
