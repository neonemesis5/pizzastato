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

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
