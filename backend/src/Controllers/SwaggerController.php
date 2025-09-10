<?php

namespace App\Controllers;

use OpenApi\Generator;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Beverage Stock API",
 *     description="API para gerenciamento de estoque de bebidas",
 *     @OA\Contact(
 *         email="contato@beveragestock.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8082",
 *     description="Servidor local"
 * )
 * 
 * @OA\Tag(
 *     name="sections",
 *     description="Operações relacionadas às seções"
 * )
 * 
 * @OA\Tag(
 *     name="beverage-types",
 *     description="Operações relacionadas aos tipos de bebida"
 * )
 * 
 * @OA\Tag(
 *     name="beverages",
 *     description="Operações relacionadas às bebidas"
 * )
 * 
 * @OA\Tag(
 *     name="history",
 *     description="Operações relacionadas ao histórico"
 * )
 */
class SwaggerController
{
    public function generateDocs(): void
    {
        // Usar arquivo estático com todos os endpoints
        $swaggerFile = __DIR__ . '/../../public/swagger_complete.json';
        
        header('Content-Type: application/json');
        echo file_get_contents($swaggerFile);
    }
    
    public function showUI(): void
    {
        $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beverage Stock API - Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui.css" />
</head>
<body>
    <div id="swagger-ui"></div>
    
    <script src="https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui-standalone-preset.js"></script>
    <script>
        const ui = SwaggerUIBundle({
            url: "/swagger.json",
            dom_id: "#swagger-ui",
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: "StandaloneLayout"
        });
    </script>
</body>
</html>';
        
        header('Content-Type: text/html');
        echo $html;
    }
}
