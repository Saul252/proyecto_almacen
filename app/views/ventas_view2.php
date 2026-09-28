<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas | Sistema</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <link href="/myvet/css/ventas.css" rel="stylesheet">
    <style>
    body {
        padding-top: 20px;
    }

    .tabla-scroll {
        max-height: 60vh;
        overflow-y: auto;
    }

    .carrito {
        position: sticky;
        top: 85px;
    }

    .badge-stock {
        font-size: 0.8rem;
        padding: 5px 10px;
    }

    .modalc {
        z-index: 2000 !important;
    }

    .swal-zindex {
        z-index: 2000 !important;
    }
    </style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="main-content">

        <h2 class="mb-4 fw-bold">
            <i class="bi bi-cart-fill text-primary"></i> Módulo de Ventas
        </h2>
           <button type="button" class="btn btn-primary d-flex align-items-center"
                                    onclick="abrirModalProducto()" title="Agregar nuevo producto">
                                    <i class="bi bi-plus-lg me-1"></i>
                                    <span class="d-none d-xl-inline">Nuevo</span>
                                </button>

        <div class="row">
            <div class="col-lg-8">
                <div class="card p-3">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select id="filtroCategoria" class="form-select">
                                <option value="">Todas las categorías</option>
                                <?php foreach($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select id="filtroAlmacen" class="form-select"
                                <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
                                <?php if($almacen_usuario == 0): ?>
                                <option value="">Todos los almacenes</option>
                                <?php endif; ?>

                                <?php foreach($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>"
                                    <?= ($almacen_usuario == $alm['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($alm['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <input type="text" id="buscador" class="form-control" placeholder="🔎 Buscar producto...">
                        </div>
                    </div>

                    <div class="table-responsive tabla-scroll">
    <table class="table table-bordered table-hover tabla-productos">
        <thead class="table-dark">
            <tr>
                <th>Almacén</th>
                <th >SKU</th>
                <th>Producto</th>
                <th width="10">Stock</th>
                <th width="120">Unidad</th>
                 <th width="90">Cant</th>
                <th>Precio</th>
               
                <th width="60"></th>
            </tr>
        </thead>
        <tbody id="productos">
            </tbody>
    </table>
</div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card p-3 carrito">
                    <h5 class="fw-bold mb-3"><i class="bi bi-bag-fill text-success"></i> Carrito</h5>
                    <div class="table-responsive">
                        <table class="table table-sm" id="tablaCarrito">
                            <thead>
                                <tr>
                                    <th>Almacén</th>
                                    <th>Producto</th>
                                    <th>Cant</th>
                                    <th>Unidad</th>
                                    <th>Sub</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody ></tbody>
                        </table>
                    </div>
                    <hr>
                    <h4 class="text-end fw-bold">Total: $<span id="total">0.00</span></h4>
                    <button class="btn btn-primary w-100 mt-3" onclick="abrirModalFinalizar()">
                        <i class="bi bi-cash-stack"></i> Finalizar Venta
                    </button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade modalc" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formNuevoCliente">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalNuevoClienteLabel">
                            <i class="fas fa-user-plus me-2"></i>Registrar Nuevo Cliente
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nombre Comercial *</label>
                                <input type="text" name="nombre_comercial" class="form-control"
                                    placeholder="Ej. Materiales El Centro" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Razón Social</label>
                                <input type="text" name="razon_social" class="form-control"
                                    placeholder="Nombre legal completo">
                            </div>

 <div class="col-md-12">
                                <label class="form-label fw-bold">Contacto *</label>
                                <input type="text" name="contacto" class="form-control"
                                    placeholder="Contacto" >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">RFC *</label>
                                <input type="text" name="rfc" class="form-control text-uppercase" maxlength="13"
                                    placeholder="ABCD000000XXX" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Código Postal *</label>
                                <input type="text" name="codigo_postal" class="form-control" maxlength="5"
                                    placeholder="00000" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Régimen Fiscal</label>
                                <input type="text" name="regimen_fiscal" class="form-control" maxlength="3"
                                    placeholder="Ej. 601">
                                <small class="text-body-secondary">Clave del catálogo del SAT</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Uso de CFDI</label>
                                <select name="uso_cfdi" class="form-select">
                                    <option value="G03" selected>G03 - Gastos en general</option>
                                    <option value="S01">S01 - Sin efectos fiscales</option>
                                    <option value="G01">G01 - Adquisición de mercancías</option>
                                    <option value="P01">P01 - Por definir</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" name="correo" class="form-control" placeholder="cliente@correo.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Teléfono</label>
                                <input type="tel" name="telefono" class="form-control" placeholder="55 0000 0000">
                            </div>

                           <div class="col-md-12">
                                <label class="form-label fw-bold">Calle</label>
                                <textarea name="calle" class="form-control text-uppercase" rows="2"
                                    placeholder="Calle y número"></textarea>
                            </div>
                             <div class="col-md-12">
                                <label class="form-label fw-bold">Colonia</label>
                                <textarea name="colonia" class="form-control text-uppercase" rows="2"
                                    placeholder="Colonia..."></textarea>
                            </div>
                             <div class="col-md-12">
                                <label class="form-label fw-bold">Pueblo</label>
                                <textarea name="pueblo" class="form-control text-uppercase" rows="2"
                                    placeholder="Pueblo"></textarea>
                            </div>
                             <div class="col-md-12">
                                <label class="form-label fw-bold">Ciudad</label>
                                <textarea name="ciudad" class="form-control text-uppercase" rows="2"
                                    placeholder="Ciudad"></textarea>
                            </div>
                            <div class="row g-3">
                                <?php if ($almacen_usuario == 0): ?>
                                <div class="col-md-12 mb-2" style="visibility: hidden;">
                                    <label class="form-label fw-bold text-primary">Asignar a Almacén *</label>
                                    <select name="almacen_id" class="form-select border-primary" required>
                                        <option value="1">-- Selecciona un almacén --</option>
                                        <?php foreach ($almacenes as $alm): ?>
                                        <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-body-secondary">Como administrador, debes elegir a qué sucursal pertenece
                                        este cliente.</small>
                                </div>
                                <?php else: ?>
                                <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarCliente">
                            <i class="fas fa-save me-1"></i> Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php require_once __DIR__ . '/ventasModales/finalizarVenta.php'; ?>

    <?php cargarScripts(); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <?php require_once __DIR__ . '/egresosComponets/agregarPoductoModal.php'; ?>
  
    <script src="/myvet/app/backend/js_ventas/filtros.js"></script>
    <script src="/myvet/app/backend/js_ventas/nuevo_cliente.js"></script>
    <script src="/myvet/app/backend/js_ventas/modal_finalizar.js"></script>
<script>
    // Selecciona todos los inputs de texto y también los textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            // Convierte el valor a mayúsculas en tiempo real
            this.value = this.value.toUpperCase();
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    recargarProductos();
});

async function recargarProductos() {
    // Cambia 'get_productos.php' por la ruta real de tu controlador/endpoint
     const params = new URLSearchParams({
    action: 'listarProductos',
    almacen: $('#filtroAlmacen').val(),
    categoria:$('#filtroCategoria').val()
   
});

    let rol = <?= isset($_SESSION['rol_id']) ? (int)$_SESSION['rol_id'] : 0 ?>;
let tablahtml = '';
const res = await fetch(
    `/myvet/app/controllers/ventasController.php?${params.toString()}`
);


let data = await res.json();
console.log(data);

// 1. Declaramos la variable como un string vacío
let tabla = '';

// Accedemos a data.data según tu respuesta del servidor
data.data.forEach(p => {
    // Lógica de reporte y factores
    const factorConversion = parseFloat(p.factor_conversion) || 1;
    const tieneReporte = (p.unidad_reporte && p.unidad_reporte.trim() !== '' && factorConversion > 1);
    const cantidad = parseFloat(p.stock) / factorConversion;
    
    // Lógica de colores de stock
    let colorStock = 'bg-success';
    if (cantidad <= 0) colorStock = 'bg-danger';
    else if (cantidad <= 5) colorStock = 'bg-warning text-dark';
    else if (cantidad <= 20) colorStock = 'bg-info text-dark';

    // Formato del texto del Badge de Stock
    const textoStock = cantidad >= 1 
        ? `${cantidad.toFixed(2)} ${p.unidad_reporte}`
        : `${parseFloat(p.stock).toFixed(2)} ${p.unidad_medida || 'PZA'}`;

    // Construcción del select de medidas adicionales
    let opcionesMedidas = `<option value="0">Seleccione</option>`;
    if (p.medidas_adicionales && p.medidas_adicionales.length > 0) {
        p.medidas_adicionales.forEach(ma => {
            opcionesMedidas += `<option value="${ma.equivalencia}" data-id="${ma.id}" data-nombre="${escapeHtml(ma.nombre)}">${escapeHtml(ma.nombre)}</option>`;
        });
    }
    const disabledMedidas = (!p.medidas_adicionales || p.medidas_adicionales.length === 0) ? 'disabled' : '';

    // 2. Concatenamos directamente la fila como un string HTML (Quitamos document.createElement)
    tabla += `
        <tr data-categoria="${p.categoria_id}" 
            data-almacen="${p.almacen_id}" 
            data-factor="${factorConversion}" 
            data-reporte-nom="${escapeHtml(p.unidad_reporte || '')}">
            
            <input type="hidden" class="factorC" value="${factorConversion}">
            <td>${escapeHtml(p.almacen_nombre)}</td>
            <td>${p.sku}</td>
            <td>${escapeHtml(p.nombre)}</td>
            <td>
                <span  class="badge ${colorStock}">${textoStock}</span>
            </td>
            <td style="width:1px; padding:0; border:none;">
                ${tieneReporte ? `
                    <select class="form-select form-select-sm select-modo-venta" style="opacity:0; position:absolute; pointer-events:none; height:0; width:0; padding:0; border:0;">
                        <option value="individual" data-nombre="${escapeHtml(p.unidad_medida || 'PZA')}">${escapeHtml(p.unidad_medida || 'PZA')}</option>
                        <option value="referencia" data-nombre="${escapeHtml(p.unidad_reporte)}">${escapeHtml(p.unidad_reporte)}</option>
                    </select>
                ` : `<span class="d-none">Individual</span>`}
                
                <select class="form-select border-primary medidas_adicionales" ${disabledMedidas}>
                    ${opcionesMedidas}
                </select>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm cantidad_usuario" min="1" value="1">
                <input type="hidden" class="cantidad" value="0">
            </td>
            <td>
                <div class="d-flex gap-1">
                    <select class="form-select form-select-sm select-precio">
                        <option value="${p.precio_minorista}">Publico - $${formatNumber(p.precio_minorista)}</option>
                        <option value="${p.precio_mayorista}">Constructora - $${formatNumber(p.precio_mayorista)}</option>
                        <option value="${p.precio_distribuidor}">Distribuidor - $${formatNumber(p.precio_distribuidor)}</option>
                    </select>
                    <input type="number" step="0.01" class="form-control form-control-sm input-precioMayor" value="${p.precio_minorista || 0}">
                    <input type="hidden" step="0.01" class="form-control form-control-sm input-precio" value="${p.precio_minorista || 0}">
                </div>
            </td>
            
            <td class="text-center">
                <button type="button" class="btn btn-success btn-sm"
                    data-producto-id="${p.id}" 
                    data-almacen-id="${p.almacen_id}"
                    data-almacen="${escapeHtml(p.almacen_nombre)}"
                    onclick="validarYAgregar(this)">
                    <i class="bi bi-plus"></i>
                </button>
            </td>
        </tr>
    `;
});

// 3. Insertamos el string acumulado directamente en el contenedor mediante jQuery
$('#productos').html(tabla);
                                }

// Funciones auxiliares para emular los formatos de PHP de forma segura
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function formatNumber(num) {
    return parseFloat(num || 0).toFixed(2);
}
/**
 * Soporte para agregar productos al presionar ENTER
 */
document.addEventListener('keydown', function(e) {

    if (e.key === 'Enter' && e.target.classList.contains('cantidad_usuario')) {

        e.preventDefault();

        const fila = e.target.closest('tr');
        const btnAgregar = fila.querySelector('button.btn-success');

        if (btnAgregar) {
            validarYAgregar(btnAgregar);

            btnAgregar.style.transform = "scale(0.9)";
            setTimeout(() => btnAgregar.style.transform = "scale(1)", 100);
        }
    }
});

/**
 * Eventos CHANGE
 */
document.addEventListener('change', function(e) {

    // Cambió el tipo de precio
    if (e.target.classList.contains('select-precio')) {

        const fila = e.target.closest('tr');

        const inputPrecio = fila.querySelector('.input-precio');
        const inputPrecioMayor = fila.querySelector('.input-precioMayor');

        inputPrecio.value = parseFloat(e.target.value).toFixed(2);
        inputPrecioMayor.value = parseFloat(e.target.value).toFixed(2);

        calcularPrecio(fila);
    }

    // Cambió la unidad
    if (e.target.classList.contains('medidas_adicionales')) {
        calcularPrecio(e.target.closest('tr'));
    }

});

/**
 * Eventos INPUT
 */
document.addEventListener('input', function(e) {

    // Cambió la cantidad
    if (e.target.classList.contains('cantidad_usuario')) {
        calcularPrecio(e.target.closest('tr'));
    }

    // Usuario escribe el precio manualmente
    if (e.target.classList.contains('input-precioMayor')) {
        actualizarDesdePrecioMayor(e.target.closest('tr'));
    }

});

/**
 * Calcula precio según unidad y tipo de precio
 */
function calcularPrecio(fila) {

    if (!fila) return;

    const inputPrecio = fila.querySelector('.input-precio');
    const inputPrecioMayor = fila.querySelector('.input-precioMayor');
    const inputUsuario = fila.querySelector('.cantidad_usuario');
    const inputReal = fila.querySelector('.cantidad');
    const selectMedida = fila.querySelector('.medidas_adicionales');
    const precio = parseFloat(fila.querySelector('.select-precio')?.value) || 0;

    if (!inputUsuario || !inputReal) return;

    const cantidadUsuario = parseFloat(inputUsuario.value) || 0;
    const equivalencia = parseFloat(selectMedida?.value) || 1;

    const factor = fila.querySelector('.factorC');
    const factorC = parseFloat(factor.value) || 1;

    const equi = Math.round((1 / equivalencia) * 100) / 100;

    let nuevoPrecio = 0;

    if (equi == factorC) {
        nuevoPrecio = precio * factorC;
    } else {
        nuevoPrecio = Math.round((precio * equi) * 100) / 100;
    }

    // Aquí sí actualizamos ambos porque NO viene del input-precioMayor
    inputPrecio.value = nuevoPrecio;
    inputPrecioMayor.value = nuevoPrecio;

    const totalReal = cantidadUsuario / equivalencia;
    inputReal.value = totalReal;

    console.log({
        usuario: cantidadUsuario,
        equivalencia,
        real: totalReal
    });
}

/**
 * Se ejecuta cuando el usuario escribe en input-precioMayor.
 * NO modifica input-precioMayor para no bloquear la escritura.
 */
function actualizarDesdePrecioMayor(fila) {

    if (!fila) return;

    const inputPrecio = fila.querySelector('.input-precio');
    const inputPrecioMayor = fila.querySelector('.input-precioMayor');

    const precioManual = parseFloat(inputPrecioMayor.value);

    if (isNaN(precioManual)) {
        return;
    }

    // Si quieres que ambos tengan el mismo valor:
    inputPrecio.value = precioManual;

    // NO hacer:
    // inputPrecioMayor.value = precioManual;
    // porque eso provoca que se dispare continuamente mientras escribe.
}
</script>
</body>

</html>