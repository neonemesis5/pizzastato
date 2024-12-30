<?php

namespace App\Models;

use PDO;

abstract class BaseModel {
    protected $dbConnection;

    public function __construct($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    protected function prepareAndExecute($query, $params = []) {
        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}
