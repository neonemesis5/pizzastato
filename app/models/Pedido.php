<?php

namespace App\Models;
use \PDO; // Importa PDO desde el espacio de nombres global

class Pedido extends BaseModel {
    /**
     * Inserta un nuevo pedido en la base de datos.
     * 
     * @param string $fecha Fecha del pedido.
     * @param string $nombre Nombre del cliente.
     * @param string $apellido Apellido del cliente.
     * @param float $total Total del pedido.
     * @param string $status Estado del pedido.
     * 
     * @return int ID del pedido recién creado.
     */
    public function createPedido($fecha, $nombre, $apellido, $total, $status = 'D') {
        $query = "INSERT INTO pedido (fecha, nombre, apellido, total, status) 
                  VALUES (:fecha, :nombre, :apellido, :total, :status)";
        $this->prepareAndExecute($query, [
            ':fecha' => $fecha,
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':total' => $total,
            ':status' => $status
        ]);
        return $this->dbConnection->lastInsertId(); // Devuelve el ID del pedido recién insertado
    }

    /**
     * Obtiene un pedido por su ID.
     * 
     * @param int $id ID del pedido.
     * @return array Pedido obtenido.
     */
    public function getPedidoById($id) {
        $query = "SELECT * FROM pedido WHERE id = :id";
        return $this->prepareAndExecute($query, [':id' => $id])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los pedidos.
     * 
     * @return array Lista de pedidos.
     */
    public function getAllPedidos() {
        $query = "SELECT * FROM pedido";
        return $this->prepareAndExecute($query)->fetchAll(PDO::FETCH_ASSOC);
    }
}
