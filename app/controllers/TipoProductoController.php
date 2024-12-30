<?php

namespace App\Controllers;

use App\Models\TipoProducto;
use PDO;

class TipoProductoController {
    private $tipoProductoModel;

    public function __construct(PDO $dbConnection) {
        $this->tipoProductoModel = new TipoProducto($dbConnection);
    }

    public function getAllTipoProductos() {
        return $this->tipoProductoModel->readAll();
    }
}
