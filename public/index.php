<?php

require_once '../config/autoload.php';

use App\Models\DatabaseConnection;
use App\Controllers\TipoProductoController;
use App\Models\Producto;
use App\Models\Tasa;

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
    // Obtiene las tasas de cambio más recientes
    $tasaModel = new Tasa($connection);
    $exchangeRates = $tasaModel->getLatestRates();
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
                    <div class="icon" onclick="addToCart('<?php echo htmlspecialchars($producto['nombre']); ?>', <?php echo $producto['preciov']; ?>, <?php echo $producto['id']; ?>)">
                        <?php echo htmlspecialchars($producto['nombre']); ?>
                    </div>

                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div>
        <table id="orderTable" style="text-align:center;">
            <thead>
                <tr>
                    <th syle="width:50px;">Eliminar</th>
                    <th style="width:120px;">Producto</th>
                    <th style="width:100px;text-align:center;">Cantidad</th>
                    <th syle="width:120px;text-align:right;">Precio</th>
                    <th syle="width:170px;text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <!-- Las filas se generarán dinámicamente -->
            </tbody>
        </table>
    </div>

    <!-- Totales -->
    <div>
        <p>Total COP: <span id="totalCOP">0.00</span></p>
        <p>Total USD: <span id="totalUSD">0.00</span></p>
        <p>Total VES: <span id="totalVES">0.00</span></p>
    </div>
    <div>
        <button onclick="saveOrder()">Guardar Pedido</button>
    </div>
    <script>
        // Tasas de cambio cargadas desde el backend
        const exchangeRates = <?php echo json_encode($exchangeRates); ?>;

        let cart = [];
        let selectedCurrency = 'COP'; // Moneda por defecto

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

        function convertPrice(priceInCOP, targetCurrency) {
            if (!exchangeRates) {
                console.error('No se cargaron las tasas de cambio.');
                return priceInCOP;
            }
            // Moneda base: COP
            if (targetCurrency === 'USD') {
                return priceInCOP / exchangeRates['20_10']; // 20 (COP) a 10 (USD)
            } else if (targetCurrency === 'BSS') {
                return priceInCOP * exchangeRates['10_30']; //(priceInCOP * exchangeRates['10_30']); // COP a USD, luego USD a BSS
            } else {
                return priceInCOP; // Retorna en COP si no hay conversión
            }
        }

        function addToCart(product, price, productId) {
            const convertedPrice = convertPrice(price, selectedCurrency);
            console.log(`Agregando ${product} con precio ${convertedPrice.toFixed(2)} ${selectedCurrency} al carrito.`);

            const existingProduct = cart.find(item => item.productId === productId); // Comparar por productId
            if (existingProduct) {
                existingProduct.quantity++;
                existingProduct.total += price;
            } else {
                cart.push({
                    productId: productId,
                    product: product,
                    price: price,
                    quantity: 1,
                    total: price,
                    markedForDeletion: false // Por defecto, no está marcado para eliminar
                });
            }

            updateCart();
        }


        function updateCart() {
            const tableBody = document.querySelector('#orderTable tbody');
            if (!tableBody) {
                console.error('El elemento #orderTable no existe en el DOM.');
                return;
            }

            tableBody.innerHTML = ''; // Limpiar tabla
            let totalCOP = 0;
            cart.forEach((item, index) => {
                const convertedPrice = convertPrice(item.price, selectedCurrency);
                const convertedTotal = convertPrice(item.total, selectedCurrency);

                tableBody.innerHTML += `
                <tr>
                    <td>
                        <input type="checkbox" class="delete-checkbox" data-index="${index}">
                        
                    </td>
                    <td>${item.product}</td>
                    <td>${item.quantity}</td>
                    <td>${convertedPrice.toFixed(2)} ${selectedCurrency}</td>
                    <td>${convertedTotal.toFixed(2)} ${selectedCurrency}</td>
                </tr>
            `;
                totalCOP += item.total;
            });

            document.getElementById('totalCOP').textContent = totalCOP.toFixed(2);
            document.getElementById('totalUSD').textContent = (totalCOP / exchangeRates['20_10']).toFixed(2);
            document.getElementById('totalVES').textContent = (totalCOP * exchangeRates['10_30']).toFixed(2);
            // Agregar evento para los checkboxes
            document.querySelectorAll('.delete-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', toggleItemSelection);
            });
        }

        function toggleItemSelection(event) {
            const checkbox = event.target;
            const itemIndex = checkbox.dataset.index; // Obtén el índice del elemento del dataset
            if (itemIndex !== undefined && cart[itemIndex]) {
                // Actualizar el estado del item en el carrito
                cart[itemIndex].markedForDeletion = checkbox.checked;
                console.log(`Item ${cart[itemIndex].product} marcado para eliminar: ${checkbox.checked}`);

                // Actualizar el monto total
                const totalCOP = parseFloat(document.getElementById('totalCOP').textContent);
                const itemTotal = cart[itemIndex].total;

                if (checkbox.checked) {
                    // Restar el monto si el producto fue marcado
                    document.getElementById('totalCOP').textContent = (totalCOP - itemTotal).toFixed(2);
                } else {
                    // Sumar el monto si el producto fue desmarcado
                    document.getElementById('totalCOP').textContent = (totalCOP + itemTotal).toFixed(2);
                }

                // Recalcular conversiones
                const updatedTotalCOP = parseFloat(document.getElementById('totalCOP').textContent);
                document.getElementById('totalUSD').textContent = (updatedTotalCOP / exchangeRates['20_10']).toFixed(2);
                document.getElementById('totalVES').textContent = (updatedTotalCOP * exchangeRates['10_30']).toFixed(2);
            }
        }

        function saveOrder() {
            const order = {
                date: new Date().toISOString(),
                status: 'D',
                items: cart.map(item => ({
                    productId: item.productId,
                    quantity: item.quantity,
                    price: item.price,
                    // Determinar el status según el checkbox
                    status: item.markedForDeletion ? 'I' : 'A'
                }))
            };

            console.log("Orden a enviar:", order); // Debug para verificar los datos enviados

            fetch('saveOrder.php', { // Asegúrate de que la ruta sea correcta
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(order),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pedido guardado exitosamente');
                        cart = [];
                        updateCart(); // Limpiar la tabla después de guardar
                    } else {
                        alert('Error al guardar el pedido: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }


        function saveOrder() {
            const order = {
                date: new Date().toISOString(),
                status: 'D',
                items: cart.map(item => ({
                    productId: item.productId,
                    quantity: item.quantity,
                    price: item.price,
                    status: item.markedForDeletion ? 'I' : 'A'
                }))
            };

            fetch('saveOrder.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(order)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pedido guardado exitosamente');
                        cart = [];
                        updateCart();
                    } else {
                        alert('Error al guardar el pedido');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
</body>

</html>