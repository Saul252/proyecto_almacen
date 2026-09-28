/**
 * SISTEMA DE VENTAS - Gestión de Carrito con Brinco Automático (Cálculos Exactos)
 */

window.carrito = window.carrito || [];

/**
 * 1. AGREGAR PRODUCTO AL CARRITO
 */
window.agregarProducto = function(btn) {
    const fila = btn.closest("tr");
    const producto_id = parseInt(btn.dataset.productoId || btn.getAttribute("data-producto-id"));
    const almacen_id = parseInt(btn.dataset.almacenId);
    const almacen_nombre = btn.dataset.almacen;
    
    // Captura estricta de factores
    const factor = parseFloat(fila.dataset.factor) || 1;
    const unidadReporte = fila.dataset.reporteNom || 'Fact.';

    const nombre = fila.cells[1].innerText; 
    const cantidadInput = fila.querySelector(".cantidad");
    let cantidadBase = parseFloat(cantidadInput.value) || 0;
    
    const modoVenta = fila.querySelector(".select-modo-venta")?.value || 'individual';
    
    // Si se agrega en modo "Tonelada", convertimos a piezas inmediatamente
    if(modoVenta === 'referencia') {
        cantidadBase = factor; 
    }

    const selectPrecio = fila.querySelector(".select-precio");
    const precioUnitario = parseFloat(selectPrecio.value) || 0;
    
    let textoPrecio = selectPrecio.options[selectPrecio.selectedIndex].text.toLowerCase();
    let tipo_p = textoPrecio.includes("dist") ? "distribuidor" : (textoPrecio.includes("may") ? "mayorista" : "minorista");

    if (cantidadBase <= 0) {
        Swal.fire('Atención', 'Ingresa una cantidad válida', 'warning');
        return;
    }

    let itemExistente = window.carrito.find(item => 
        item.producto_id === producto_id && item.almacen_id === almacen_id && item.tipo_precio === tipo_p
    );

    if (itemExistente) {
        itemExistente.cantidad += cantidadBase;
    } else {
        window.carrito.push({
            producto_id, 
            almacen_id, 
            almacen_nombre, 
            nombre,
            cantidad: cantidadBase,
            entrega_hoy: cantidadBase,
            precio_unitario: precioUnitario,
            tipo_precio: tipo_p,
            factor: factor,
            unidad_reporte: unidadReporte
        });
    }

    window.renderCarrito();
    cantidadInput.value = 1;
};

/**
 * 2. RENDERIZAR TABLA
 */
window.renderCarrito = function() {
    const tablaBody = document.querySelector("#tablaCarrito tbody");
    if (!tablaBody) return;
    
    tablaBody.innerHTML = "";
    
    window.carrito.forEach((item, index) => {
        // CÁLCULO DE DISTRIBUCIÓN (Usando round para evitar errores de precisión de JS)
        const cantFactor = Math.floor(item.cantidad / item.factor);
        const cantPza = Math.round((item.cantidad % item.factor) * 100) / 100;

        // Recalcular subtotal antes de mostrar
        item.subtotal = item.cantidad * item.precio_unitario;

        const tr = document.createElement("tr");
        tr.dataset.index = index;
        tr.innerHTML = `
            <td><small>${item.almacen_nombre}</small></td>
            <td><div class="fw-bold" style="font-size: 0.8rem;">${item.nombre}</div></td>
            <td>
                <input type="number" class="form-control form-control-sm text-center input-factor-cambio" 
                    data-index="${index}" value="${cantFactor}" min="0" step="1">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm text-center input-pza-cambio" 
                    data-index="${index}" value="${cantPza}" min="0" step="any">
            </td>
            <td class="text-end fw-bold subtotal-celda">$${item.subtotal.toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-link text-danger p-0 btn-remove-item" data-index="${index}">
                    <i class="bi bi-x-circle"></i>
                </button>
            </td>
        `;
        tablaBody.appendChild(tr);
    });

    actualizarTotalesUI();
};

/**
 * 3. LÓGICA DE CÁLCULO SINCRONIZADA (input)
 */
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('input-factor-cambio') || e.target.classList.contains('input-pza-cambio')) {
        const index = e.target.dataset.index;
        const item = window.carrito[index];
        const tr = e.target.closest('tr');
        
        // Obtenemos valores de los inputs
        const valFactor = parseFloat(tr.querySelector('.input-factor-cambio').value) || 0;
        const valPza = parseFloat(tr.querySelector('.input-pza-cambio').value) || 0;

        // RE-CALCULO TOTAL DE PIEZAS (La base de todo)
        item.cantidad = (valFactor * item.factor) + valPza;
        item.subtotal = item.cantidad * item.precio_unitario;
        item.entrega_hoy = item.cantidad;

        // Actualización visual inmediata de la fila y el total general
        tr.querySelector('.subtotal-celda').innerText = `$${item.subtotal.toFixed(2)}`;
        actualizarTotalesUI();
    }
});

/**
 * 4. LÓGICA DE BRINCO (change)
 */
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('input-factor-cambio') || e.target.classList.contains('input-pza-cambio')) {
        // Al terminar de editar, ejecutamos el render para limpiar los campos
        // Ejemplo: Si puso 40 en piezas y el factor es 40, se convierte en 1 Factor y 0 piezas.
        window.renderCarrito();
    }
});

/**
 * FUNCIÓN PARA ACTUALIZAR TOTALES GLOBALES
 */
function actualizarTotalesUI() {
    // Sumamos todos los subtotales del carrito
    let totalAcumulado = window.carrito.reduce((acc, item) => acc + (item.cantidad * item.precio_unitario), 0);
    const totalStr = totalAcumulado.toFixed(2);

    const elTotal = document.getElementById("total");
    const elTotalModal = document.getElementById("totalFinalModal");
    const elPago = document.getElementById("monto_pagar");

    if (elTotal) elTotal.innerText = totalStr;
    if (elTotalModal) elTotalModal.innerText = totalStr;
    if (elPago) {
        elPago.value = totalStr;
        // Disparamos input para actualizar cambios en vuelto/cambio si existen
        elPago.dispatchEvent(new Event('input'));
    }
}

// Listener para eliminar item
document.addEventListener('click', function(e) {
    const btnDelete = e.target.closest('.btn-remove-item');
    if (btnDelete) {
        const index = btnDelete.dataset.index;
        window.carrito.splice(index, 1);
        window.renderCarrito();
    }
});