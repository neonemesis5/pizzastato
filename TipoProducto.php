<?php
class TipoProducto {
    private $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    // Crear un nuevo tipo de producto
    public function create($nombre) {
        try {
            $query = "INSERT INTO tipo_producto (nombre) VALUES (:nombre)";
            $stmt = $this->dbConnection->prepare($query);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            die("Error al crear tipo de producto: " . $e->getMessage());
        }
    }

    // Leer todos los tipos de productos
    public function readAll() {
        try {
            $query = "SELECT id, nombre FROM tipo_producto ORDER BY id ASC";
            $stmt = $this->dbConnection->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Error al obtener los tipos de productos: " . $e->getMessage());
        }
    }

    // Actualizar un tipo de producto
    public function update($id, $nombre) {
        try {
            $query = "UPDATE tipo_producto SET nombre = :nombre WHERE id = :id";
            $stmt = $this->dbConnection->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            die("Error al actualizar tipo de producto: " . $e->getMessage());
        }
    }

    // Eliminar un tipo de producto
    public function delete($id) {
        try {
            $query = "DELETE FROM tipo_producto WHERE id = :id";
            $stmt = $this->dbConnection->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            die("Error al eliminar tipo de producto: " . $e->getMessage());
        }
    }
}