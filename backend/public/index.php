<?php
require_once '../vendor/autoload.php';

\Flight::route('GET/', function(){
    echo json_encode([
        'message' => "Beverage Api",
        'status' => "online",
        'version' => "1.0.0",
        'endpoints' => [
            'GET /beverage' => 'List all beverages',
            'GET /beverage/{id}' => 'Get beverage by ID'
        ]
    ]);
});

\Flight::route('GET /beverage', function(){
    echo json_encode([
        "message" => "Beverage Api"
    ]);
});

\Flight::route('GET /beverage/{id}', function($id){
    echo json_encode([
        "message" => "Beverage Api",
        "id" => $id
    ]);
});

\Flight::start();