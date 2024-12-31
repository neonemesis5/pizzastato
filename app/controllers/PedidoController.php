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
            // Crear el pedido
            $pedidoId = $this->pedidoModel->createPedido(
                $data['date'], 
                $data['nombre'], 
                $data['apellido'], 
                $data['total'], 
                $data['status']
            );

            // Crear los detalles del pedido
            foreach ($data['items'] as $item) {
                $this->detallePedModel->createDetalle(
                    $pedidoId,
                    $item['productId'],
                    $item['quantity'],
                    $item['price'],
                    $item['status']
                );
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
