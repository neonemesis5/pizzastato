<?php

namespace App\Models;
use \PDO; // Importa PDO desde el espacio de nombres global
class DetallePed extends BaseModel {
    /**
     * Inserta un nuevo detalle del pedido en la base de datos.
     * 
     * @param int $pedidoId ID del pedido al que pertenece el detalle.
     * @param int $productoId ID del producto.
     * @param float $qty Cantidad del producto.
     * @param float $preciov Precio del producto.
     * @param string $status Estado del detalle ('A' o 'I').
     */
    public function createDetalle($pedidoId, $productoId, $qty, $preciov, $status = 'A') {
        $query = "INSERT INTO detalleped (pedido_id, producto_id, qty, preciov, status) 
                  VALUES (:pedido_id, :producto_id, :qty, :preciov, :status)";
        $this->prepareAndExecute($query, [
            ':pedido_id' => $pedidoId,
            ':producto_id' => $productoId,
            ':qty' => $qty,
            ':preciov' => $preciov,
            ':status' => $status
        ]);
    }

    /**
     * Obtiene los detalles de un pedido por su ID.
     * 
     * @param int $pedidoId ID del pedido.
     * @return array Lista de detalles del pedido.
     */
    public function getDetallesByPedidoId($pedidoId) {
        $query = "SELECT * FROM detalleped WHERE pedido_id = :pedido_id";
        return $this->prepareAndExecute($query, [':pedido_id' => $pedidoId])->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina un detalle del pedido por su ID.
     * 
     * @param int $detalleId ID del detalle a eliminar.
     */
    public function deleteDetalleById($detalleId) {
        $query = "DELETE FROM detalleped WHERE id = :id";
        $this->prepareAndExecute($query, [':id' => $detalleId]);
    }
}
