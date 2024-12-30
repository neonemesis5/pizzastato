<?php

require_once '../config/autoload.php';

use App\Models\DatabaseConnection;
use App\Controllers\TipoProductoController;
use App\Models\Producto;

try {
    $db = new DatabaseConnection();
    $connection = $db->connect();

    // Controlador para tipos de producto
    $tipoProductoController = new TipoProductoController($connection);
    $tipoProductos = $tipoProductoController->getAllTipoProductos();

    // Obtiene los productos por tipo
    $productosPorTipo = [];
    foreach ($tipoProductos as $tipoProducto) {
        $productoModel = new Producto($connection);
        $productosPorTipo[$tipoProducto['nombre']] = $productoModel->getByTipoProducto($tipoProducto['id']);
    }
} catch (Exception $e) {
    echo "Error detectado: " . $e->getMessage();
    exit;
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
    <div>
    <table id="orderTable">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <!-- Las filas se generarán dinámicamente -->
        </tbody>
    </table>
</div>

<!-- Totales -->
<div>
    <p>Total USD: <span id="totalUSD">0.00</span></p>
    <p>Total COP: <span id="totalCOP">0.00</span></p>
    <p>Total VES: <span id="totalVES">0.00</span></p>
</div>

    <div class="container">
        <div class="products">
            <?php foreach ($tipoProductos as $producto): ?>
                <button onclick="showGroup('<?php echo strtolower($producto['nombre']); ?>')">
                    <?php echo htmlspecialchars($producto['nombre']); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($productosPorTipo as $tipoNombre => $productos): ?>
            <div id="<?php echo strtolower($tipoNombre); ?>" class="group" style="display: none;">
                <?php foreach ($productos as $producto): ?>
                    <div class="icon" onclick="addToCart('<?php echo htmlspecialchars($producto['nombre']); ?>', <?php echo $producto['preciov']; ?>)">
                        <?php echo htmlspecialchars($producto['nombre']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
      // Carrito para almacenar los productos seleccionados
let cart = [];
let totalUSD = 0;

function showGroup(groupId) {
    console.log(`showGroup ejecutado para: ${groupId}`);
    // Ocultar todos los grupos
    document.querySelectorAll('.group').forEach(group => {
        group.style.display = 'none';
    });

    // Mostrar solo el grupo seleccionado
    const group = document.getElementById(groupId);
    if (group) {
        group.style.display = 'flex';
    }
}


function addToCart(product, price) {
    console.log(`Agregando ${product} con precio ${price} al carrito.`);
    // Buscar si el producto ya está en el carrito
    const existingProduct = cart.find(item => item.product === product);
    if (existingProduct) {
        existingProduct.quantity++;
        existingProduct.total += price;
        console.log(`Agregando ${product} con precio ${price} al carrito.`);
    } else {
        // Si no está, agregarlo al carrito
        cart.push({
            product: product,
            price: price,
            quantity: 1,
            total: price
        });
    }

    // Actualizar el carrito
    updateCart();
}
function updateCart() {
    const tableBody = document.querySelector('#orderTable tbody');
    if (!tableBody) {
        console.error('El elemento #orderTable no existe en el DOM.');
        return;
    }

    tableBody.innerHTML = ''; // Limpiar tabla

    totalUSD = 0;

    // Agregar filas de productos al carrito
    cart.forEach(item => {
        totalUSD += item.total;
        tableBody.innerHTML += `
            <tr>
                <td>${item.product}</td>
                <td>${item.quantity}</td>
                <td>${item.price}</td>
                <td>${item.total}</td>
            </tr>
        `;
    });

    // Actualizar los totales
    document.getElementById('totalUSD').textContent = totalUSD.toFixed(2);
    document.getElementById('totalCOP').textContent = (totalUSD * 4000).toFixed(2); // Tasa de ejemplo
    document.getElementById('totalVES').textContent = (totalUSD * 35).toFixed(2);   // Tasa de ejemplo
}

function showModal() {
    const modalDetails = document.getElementById('modalDetails');
    modalDetails.innerHTML = cart.map(item => `
        <p>${item.product} - ${item.quantity} x ${item.price} = ${item.total}</p>
    `).join('');

    // Mostrar el modal
    document.getElementById('orderModal').style.display = 'block';
}

function closeModal() {
    // Ocultar el modal
    document.getElementById('orderModal').style.display = 'none';
}

    </script>
</body>
</html>
