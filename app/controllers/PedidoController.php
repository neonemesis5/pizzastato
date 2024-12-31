<?php

namespace App\Controllers;

use App\Models\DatabaseConnection;
use App\Models\Pedido;
use App\Models\DetallePed;

class PedidoController {
    private $pedidoModel;
    private $detallePedModel;

    public function __construct($dbConnection) {
        $this->pedidoModel = new Pedido($dbConnection);
        $this->detallePedModel = new DetallePed($dbConnection);
    }

    public function saveOrder($data) {
        try {
            // Log para verificar datos del pedido
            // file_put_contents('../logs/controller.log', "Pedido Data: " . print_r($data, true), FILE_APPEND);
    
            // Crear el pedido
            $pedidoId = $this->pedidoModel->createPedido(
                $data['date'], 
                $data['nombre'], 
                $data['apellido'], 
                $data['total'], 
                $data['status']
            );
    
            // Log para verificar el ID del pedido creado
            // file_put_contents('../logs/controller.log', "Pedido ID: $pedidoId" . PHP_EOL, FILE_APPEND);
    
            // Crear los detalles del pedido
            foreach ($data['items'] as $item) {
                $this->detallePedModel->createDetalle(
                    $pedidoId,
                    $item['productId'],
                    $item['quantity'],
                    $item['price'],
                    $item['status']
                );
    
                // Log para cada detalle insertado
                // file_put_contents('../logs/controller.log', "Detalle: " . print_r($item, true), FILE_APPEND);
            }
    
            return ['success' => true];
        } catch (\Exception $e) {
            // Log de errores
            // file_put_contents('../logs/error.log', "Error en PedidoController: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
}
