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
