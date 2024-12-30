<?php

spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

try {
    $database = new DatabaseConnection();
    $connection = $database->connect();

    if ($connection) {
        echo "La conexión a la base de datos es válida.<br>";
    }

    $tipoProductoManager = new TipoProducto($connection);
    $tipoProductos = $tipoProductoManager->readAll();

    // Depura los resultados de la base de datos
    echo "<pre>";
    print_r($tipoProductos);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error detectado: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Pedidos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Selecciona tus opciones</h1>
    <div class="container">
        <div class="products">
            <?php foreach ($tipoProductos as $producto): ?>
                <button onclick="showGroup('<?php echo strtolower($producto['nombre']); ?>')">
                    <?php echo htmlspecialchars($producto['nombre']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div id="pizzas" class="group">
            <div class="icon" onclick="addToCart('Normal', 5000)">🍕 Normal</div>
            <div class="icon" onclick="addToCart('Bocadillo', 6000)">🍕 Bocadillo</div>
            <div class="icon" onclick="addToCart('Hawaiana', 7000)">🍕 Hawaiana</div>
            <div class="icon" onclick="addToCart('Caprese', 8000)">🍕 Caprese</div>
        </div>

        <div id="refrescos" class="group" style="display: none;">
            <div class="icon" onclick="addToCart('Coca-Cola', 2000)">🥤 Coca-Cola</div>
            <div class="icon" onclick="addToCart('Pepsi', 2000)">🥤 Pepsi</div>
        </div>

        <div id="adicionales" class="group" style="display: none;">
            <div class="icon" onclick="addToCart('Queso', 1000)">🧀 Queso</div>
            <div class="icon" onclick="addToCart('Champiñón', 1500)">🍄 Champiñón</div>
        </div>

        <div class="summary">
            <h2>Resumen del Pedido</h2>
            <table id="orderTable">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="totals">
                <p>Total en USD: <span id="totalUSD">0</span> USD</p>
                <p>Total en COP: <span id="totalCOP">0</span> COP</p>
                <p>Total en VES: <span id="totalVES">0</span> VES</p>
                <button onclick="showModal()">Ver Detalles</button>
            </div>
        </div>

        <div id="orderModal" class="modal" style="display: none;">
            <div class="modal-content">
                <h2>Detalles del Pedido</h2>
                <div id="modalDetails"></div>
                <button onclick="closeModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>