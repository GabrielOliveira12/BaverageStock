<?php
namespace App\Controllers;

abstract class Controller
{
    protected function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function errorResponse($message, $statusCode = 500)
    {
        $this->jsonResponse(['error' => $message], $statusCode);
    }

    protected function successResponse($data, $message = null)
    {
        $response = ['success' => true, 'data' => $data];
        if ($message) {
            $response['message'] = $message;
        }
        $this->jsonResponse($response);
    }
    
    protected function validateRequired($data, $required)
    {
        $missing = [];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            throw new \Exception("Campos obrigatórios: " . implode(', ', $missing));
        }
    }
}