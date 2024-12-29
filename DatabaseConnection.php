<?php

class DatabaseConnection {
    private $host = 'localhost';
    private $port = '5432';
    private $dbname = 'pizzasdb'; // Cambia esto por el nombre de tu base de datos
    private $user = 'postgres';       // Cambia esto por tu usuario de PostgreSQL
    private $password = '123';   // Cambia esto por tu contraseña de PostgreSQL
    private $connection;

    public function connect() {
        try {
            $this->connection = new PDO("pgsql:host=$this->host;port=$this->port;dbname=$this->dbname", $this->user, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->connection;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function disconnect() {
        $this->connection = null;
    }
}