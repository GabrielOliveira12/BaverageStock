<?php

require_once '../vendor/autoload.php';

use App\Controllers\SectionController;
use App\Controllers\BeverageTypeController;
use App\Controllers\BeverageController;
use App\Controllers\HistoryController;
use App\Controllers\SwaggerController;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

\Flight::route('GET /', function(){
    echo json_encode([
        "message" => "Beverage Stock API",
        "version" => "1.0.0",
        "documentation" => "/swagger",
        "endpoints" => [
            "GET /sections" => "Listar seções",
            "POST /sections" => "Criar seção",
            "PUT /sections/{id}" => "Atualizar seção",
            "DELETE /sections/{id}" => "Deletar seção",
            "GET /beverage-types" => "Listar tipos de bebida",
            "POST /beverage-types" => "Criar tipo de bebida",
            "PUT /beverage-types/{id}" => "Atualizar tipo",
            "DELETE /beverage-types/{id}" => "Deletar tipo",
            "GET /beverages" => "Listar bebidas",
            "POST /beverages" => "Criar bebida",
            "PUT /beverages/{id}" => "Atualizar bebida",
            "DELETE /beverages/{id}" => "Deletar bebida",
            "GET /history" => "Listar histórico",
            "POST /history" => "Registrar operação"
        ]
    ]);
});

\Flight::route('GET /swagger', function(){
    $controller = new SwaggerController();
    $controller->showUI();
});

\Flight::route('GET /swagger.json', function(){
    $controller = new SwaggerController();
    $controller->generateDocs();
});

\Flight::route('GET /sections', function(){
    $controller = new SectionController();
    $controller->index();
});

\Flight::route('GET /sections/@id', function($id){
    $controller = new SectionController();
    $controller->show($id);
});

\Flight::route('POST /sections', function(){
    $controller = new SectionController();
    $controller->create();
});

\Flight::route('PUT /sections/@id', function($id){
    $controller = new SectionController();
    $controller->update($id);
});

\Flight::route('DELETE /sections/@id', function($id){
    $controller = new SectionController();
    $controller->delete($id);
});

\Flight::route('GET /sections/type/@type', function($type){
    $controller = new SectionController();
    $controller->getByType($type);
});

\Flight::route('GET /sections/volume/@type', function($type){
    $controller = new SectionController();
    $controller->getTotalVolume($type);
});

\Flight::route('GET /beverage-types', function(){
    $controller = new BeverageTypeController();
    $controller->index();
});

\Flight::route('GET /beverage-types/@id', function($id){
    $controller = new BeverageTypeController();
    $controller->show($id);
});

\Flight::route('POST /beverage-types', function(){
    $controller = new BeverageTypeController();
    $controller->create();
});

\Flight::route('PUT /beverage-types/@id', function($id){
    $controller = new BeverageTypeController();
    $controller->update($id);
});

\Flight::route('DELETE /beverage-types/@id', function($id){
    $controller = new BeverageTypeController();
    $controller->delete($id);
});

\Flight::route('GET /beverage-types/type/@type', function($type){
    $controller = new BeverageTypeController();
    $controller->getByType($type);
});

\Flight::route('GET /beverages', function(){
    $controller = new BeverageController();
    $controller->index();
});

\Flight::route('GET /beverages/@id', function($id){
    $controller = new BeverageController();
    $controller->show($id);
});

\Flight::route('POST /beverages', function(){
    $controller = new BeverageController();
    $controller->create();
});

\Flight::route('PUT /beverages/@id', function($id){
    $controller = new BeverageController();
    $controller->update($id);
});

\Flight::route('DELETE /beverages/@id', function($id){
    $controller = new BeverageController();
    $controller->delete($id);
});

\Flight::route('GET /beverages/section/@sectionId', function($sectionId){
    $controller = new BeverageController();
    $controller->getBySection($sectionId);
});

\Flight::route('GET /history', function(){
    $controller = new HistoryController();
    $controller->index();
});

\Flight::route('POST /history', function(){
    $controller = new HistoryController();
    $controller->create();
});

\Flight::route('GET /history/section/@sectionId', function($sectionId){
    $controller = new HistoryController();
    $controller->getBySection($sectionId);
});

\Flight::start();