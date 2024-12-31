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
                    <div class="icon" onclick="handleAsados('<?php echo htmlspecialchars($producto['nombre']); ?>', <?php echo $producto['preciov']; ?>, <?php echo $producto['id']; ?>, '<?php echo strtolower($tipoNombre); ?>')">
                        <?php echo htmlspecialchars($producto['nombre']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- Elementos adicionales para Asados -->
    <div id="asadoInputs" style="display: none; margin-top: 10px;">
        <label for="gramosInput">Gramos:</label>
        <input type="number" id="gramosInput" placeholder="Ingrese gramos" min="1" style="margin-right: 10px;">
        <button onclick="addAsadoToCart()">Agregar</button>
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
        let cart = [];
        let selectedCurrency = 'COP'; // Moneda por defecto
        let currentAsado = null; // Para rastrear el producto de tipo Asado seleccionado

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

        function handleAsados(product, price, productId, tipoNombre) {
            if (tipoNombre === 'asados') {
                // Mostrar el campo de gramos y el botón de agregar para asados
                document.getElementById('asadoInputs').style.display = 'block';
                currentAsado = {
                    product,
                    price,
                    productId
                };
            } else {
                // Ocultar los elementos si no es tipo "asados"
                document.getElementById('asadoInputs').style.display = 'none';
                currentAsado = null;

                // Agregar el producto al carrito directamente
                addToCart(product, price, productId, 1); // Cantidad por defecto es 1
            }
        }

        function addAsadoToCart() {
            const gramosInput = document.getElementById('gramosInput');
            const gramos = parseInt(gramosInput.value);

            if (!currentAsado || isNaN(gramos) || gramos <= 0) {
                alert('Por favor, seleccione un producto válido y asegúrese de ingresar un valor válido en gramos.');
                return;
            }

            const product = `${currentAsado.product} (${gramos}g)`;
            const totalPrice = (currentAsado.price * gramos) / 1000; // Precio proporcional según gramos

            console.log(`Agregando ${product} con precio ${totalPrice.toFixed(2)} al carrito.`);

            cart.push({
                productId: currentAsado.productId,
                product: product,
                price: currentAsado.price,
                quantity: gramos,
                total: totalPrice,
                markedForDeletion: false // Por defecto, no está marcado para eliminar
            });

            updateCart();

            // Limpiar el campo de entrada y ocultar los inputs
            gramosInput.value = '';
            document.getElementById('asadoInputs').style.display = 'none';
            currentAsado = null;
        }
        // Tasas de cambio cargadas desde el backend
        const exchangeRates = <?php echo json_encode($exchangeRates); ?>;

        function convertPrice(priceInCOP, targetCurrency) {
            if (!exchangeRates) {
                console.error('No se cargaron las tasas de cambio.');
                return priceInCOP; // Retorna el precio original si no hay tasas de cambio
            }

            // Moneda base: COP
            if (targetCurrency === 'USD') {
                return priceInCOP / exchangeRates['20_10']; // 20 (COP) a 10 (USD)
            } else if (targetCurrency === 'VES') {
                return priceInCOP * exchangeRates['10_30']; // 10 (COP) a 30 (VES)
            } else {
                return priceInCOP; // Retorna en COP si no hay conversión
            }
        }
        function addToCart(product, price, productId, quantity = 1) {
            const totalPrice = price * quantity;

            console.log(`Agregando ${product} con precio ${totalPrice.toFixed(2)} al carrito.`);

            cart.push({
                productId,
                product,
                price,
                quantity,
                total: totalPrice,
                markedForDeletion: false // Por defecto, no está marcado para eliminar
            });

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

            // Agregar eventos para los checkboxes
            document.querySelectorAll('.delete-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', toggleItemSelection);
            });
        }

        function toggleItemSelection(event) {
            const checkbox = event.target;
            const itemIndex = checkbox.dataset.index;

            if (itemIndex !== undefined && cart[itemIndex]) {
                cart[itemIndex].markedForDeletion = checkbox.checked;

                const totalCOP = parseFloat(document.getElementById('totalCOP').textContent);
                const itemTotal = cart[itemIndex].total;

                if (checkbox.checked) {
                    document.getElementById('totalCOP').textContent = (totalCOP - itemTotal).toFixed(2);
                } else {
                    document.getElementById('totalCOP').textContent = (totalCOP + itemTotal).toFixed(2);
                }

                const updatedTotalCOP = parseFloat(document.getElementById('totalCOP').textContent);
                document.getElementById('totalUSD').textContent = (updatedTotalCOP / exchangeRates['20_10']).toFixed(2);
                document.getElementById('totalVES').textContent = (updatedTotalCOP * exchangeRates['10_30']).toFixed(2);
            }
        }
    </script>
</body>

</html>