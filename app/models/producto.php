<?php

namespace App\Models;

use PDO;

class Producto extends BaseModel {
    public function getByTipoProducto($tipoProductoId) {
        $query = "SELECT id, nombre, COALESCE(preciov, 0) AS preciov FROM producto WHERE tipoproducto_id = :tipoProductoId";
        return $this->prepareAndExecute($query, [':tipoProductoId' => $tipoProductoId])->fetchAll(PDO::FETCH_ASSOC);
    }
}
