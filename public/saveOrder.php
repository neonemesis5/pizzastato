<?php

require_once '../config/autoload.php';

use App\Models\DatabaseConnection;
use App\Controllers\PedidoController;

header('Content-Type: application/json');

try {
    $db = new DatabaseConnection();
    $connection = $db->connect();

    // Datos recibidos en formato JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Instanciar el controlador
    $pedidoController = new PedidoController($connection);

    // Guardar el pedido
    $response = $pedidoController->saveOrder($data);

    echo json_encode($response); // Solo devuelve JSON
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// require_once '../config/autoload.php';

// use App\Models\DatabaseConnection;
// use App\Controllers\PedidoController;

// header('Content-Type: application/json');

// try {
//     $db = new DatabaseConnection();
//     $connection = $db->connect();

//     // Datos recibidos en formato JSON
//     $data = json_decode(file_get_contents('php://input'), true);

//     // Log para verificar los datos recibidos
//     file_put_contents('../logs/save_order.log', print_r($data, true), FILE_APPEND);

//     // Instanciar el controlador
//     $pedidoController = new PedidoController($connection);

//     // Guardar el pedido
//     $response = $pedidoController->saveOrder($data);

//     echo json_encode($response);
// } catch (Exception $e) {
//     // Log de errores
//     file_put_contents('../logs/error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
//     echo json_encode(['success' => false, 'error' => $e->getMessage()]);
// }
