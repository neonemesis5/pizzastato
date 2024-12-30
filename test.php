<?php
try {
    $dsn = 'pgsql:host=localhost;port=5432;dbname=dbpizzas'; // Cambia 'nombre_de_tu_db' por el nombre real de tu base de datos
    $username = 'postgres'; // Cambia 'postgres' por tu usuario de PostgreSQL
    $password = '123'; // Cambia 'tu_password' por la contraseña de PostgreSQL
    
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexión exitosa a PostgreSQL desde PHP.";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
