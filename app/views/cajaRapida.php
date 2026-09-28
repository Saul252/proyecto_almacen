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
    :root {
        --primary-color: #007aff;
        /* Azul iOS */
        --success-color: #34c759;
        /* Verde iOS */
        --bg-light: #f5f5f7;
        --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
    }

    .main-content {
        background-color: var(--bg-light);
        padding: 40px;
        min-height: 100vh;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    /* Cambio de nombre del título sin tocar HTML */
    .main-content h2.fw-bold {
        font-size: 1.8rem;
        letter-spacing: -0.5px;
        color: #1d1d1f;
        visibility: hidden;
        /* Escondemos el original */
        position: relative;
    }

    .main-content h2.fw-bold::after {
        content: "Caja Rápida";
        /* El nuevo nombre */
        visibility: visible;
        position: absolute;
        left: 40px;
        /* Ajuste por el icono bi-cart-fill */
        top: 0;
    }

    .main-content h2.fw-bold i {
        visibility: visible;
        color: var(--primary-color) !important;
    }

    /* --- Cards Estilo Elegante --- */
    .card {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: var(--card-shadow) !important;
        background: #ffffff;
        transition: transform 0.2s ease;
    }

    /* --- Tabla de Productos --- */
    .tabla-productos {
        border: none !important;
    }

    .tabla-productos thead th {
        background-color: #f8f9fa !important;
        color: #86868b !important;
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        border: none !important;
        padding: 12px;
    }

    .tabla-productos tbody tr {
        border-bottom: 1px solid #f2f2f2;
        transition: all 0.2s;
    }

    .tabla-productos tbody tr:hover {
        background-color: #fafafa !important;
    }

    .tabla-productos td {
        padding: 14px 12px !important;
        vertical-align: middle;
        border: none !important;
    }

    /* Inputs y Selects más limpios */
    .form-control,
    .form-select {
        border: 1px solid #d2d2d7 !important;
        border-radius: 10px !important;
        font-size: 0.9rem;
        padding: 0.6rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1) !important;
    }

    /* --- Carrito de Compras Lateral --- */
    .carrito {
        position: sticky;
        top: 20px;
        border-top: 4px solid var(--success-color) !important;
    }

    #tablaCarrito thead th {
        font-size: 0.65rem;
        color: #86868b;
        border-bottom: 1px solid #eee;
    }

    #tablaCarrito td {
        font-size: 0.85rem;
        padding: 8px 4px;
    }

    #total {
        color: var(--primary-color);
    }

    /* --- Botones --- */
    .btn-primary {
        background-color: var(--primary-color) !important;
        border: none !important;
        border-radius: 12px !important;
        padding: 10px 20px;
        font-weight: 600;
    }

    .btn-success {
        background-color: var(--success-color) !important;
        border: none !important;
        border-radius: 10px !important;
        font-weight: 600;
    }

    .btn-sm {
        padding: 5px 10px;
    }

    /* --- Modal Estilo Apple --- */
    .modal-content {
        border-radius: 20px !important;
        overflow: hidden;
    }

    .modal-header {
        border-bottom: 1px solid #f2f2f2 !important;
        padding: 1.5rem !important;
    }

    .bg-dark {
        background-color: #1d1d1f !important;
    }

    .badge {
        border-radius: 6px !important;
        padding: 5px 8px !important;
        font-weight: 500 !important;
    }

    /* Scroll personalizado */
    .tabla-scroll {
        max-height: 600px;
        overflow-y: auto;
    }

    .tabla-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .tabla-scroll::-webkit-scrollbar-thumb {
        background: #d2d2d7;
        border-radius: 10px;
    }

    /* --- Efecto de Botón Flotante para el total --- */
    .bg-primary.bg-opacity-10 {
        background-color: rgba(0, 122, 255, 0.05) !important;
        border: 1px dashed var(--primary-color) !important;
    }

    /* --- Corrección de Superposición de Modales --- */
    /* --- Elevación de SweetAlert por encima de los modales --- */
    .swal2-container {
        z-index: 2000 !important;
        /* Lo mandamos muy por encima del 1061 de los modales */
    }

    /* Ajuste preventivo para el fondo oscuro de los modales */
    .modal-backdrop {
        z-index: 1050 !important;
        /* Mantenlo bajo para que no tape los modales activos */
    }

    /* Modal base */
    #modalFinalizarVenta {
        z-index: 1055 !important;
    }

    /* Modal secundario (el que se abre después) */
    #modalNuevoCliente {
        z-index: 1061 !important;
    }
    </style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="main-content">

        <h2 class="mb-4 fw-bold">
            <i class="bi bi-cart-fill text-primary"></i> Módulo de Ventas
        </h2>

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
                                   
                                    <th  width="90">Producto</th>
                                    <th  width="90">Stock</th>
                                      
                                   
                                    <th width="150">Unidad</th>
                                      <th width="90">Cant</th>
                                    <th width="150">Tipo Cliente</th>
                                  
                                    <th width="110">Precio Unitario</th>


                                  
                                    
                                    
                                    
                                    <th width="60">Agregar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($productos as $p): 
                                    $tieneReporte = (!empty($p['unidad_reporte']) && $p['factor_conversion'] > 1);
                                ?>
                                <tr data-categoria="<?= $p['categoria_id'] ?>" data-almacen="<?= $p['almacen_id'] ?>"
                                    data-factor="<?= $p['factor_conversion'] ?>"
                                    data-reporte-nom="<?= htmlspecialchars($p['unidad_reporte']) ?>">
                                    <input type="hidden" class="factorC" value="<?= $p['factor_conversion'] ?>">

 <td class="d-none"><?= htmlspecialchars($p['almacen_nombre']) ?></td>
                                    <td class ="d-none"><?= $p['sku'] ?></td>
                                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td>
                                        <span class="badge bg-success"><?= $p['stock'] ?></span>
                                        <small class="d-block text-body-secondary" style="font-size: 0.65rem;">
                                            <?= htmlspecialchars($p['unidad_medida'] ?? 'unid.') ?>
                                        </small>
                                    </td>
                                   
   <td style="width:1px; padding:0; border:none;">
                        <?php if($tieneReporte): ?>
                        <select class="form-select form-select-sm select-modo-venta" style="
            opacity:0;
            position:absolute;
            pointer-events:none;
            height:0;
            width:0;
            padding:0;
            border:0;
        ">

                            <option value="individual"
                                data-nombre="<?= htmlspecialchars($p['unidad_medida'] ?? 'PZA') ?>">

                                <?= htmlspecialchars($p['unidad_medida'] ?? 'PZA') ?>

                            </option>

                            <option value="referencia" data-nombre="<?= htmlspecialchars($p['unidad_reporte']) ?>">

                                <?= htmlspecialchars($p['unidad_reporte']) ?>

                            </option>

                        </select>
                        <?php else: ?>
                        <span class="d-none">Individual</span>
                        <?php endif; ?>

                        <select class="form-select border-primary medidas_adicionales"
                            <?= empty($p['medidas_adicionales']) ? 'disabled' : '' ?>>
                            <option value='0'>Seleccione</option>
                            <?php foreach($p['medidas_adicionales'] as $ma): ?>
                            <option value="<?= $ma['equivalencia'] ?>" data-id="<?= $ma['id'] ?> "
                                data-nombre="<?= $ma['nombre'] ?>">
                                <?= htmlspecialchars($ma['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>


                    </td>
                     <td>
                        <input type="number" class="form-control form-control-sm cantidad_usuario" min="1" value="1">

                        <!-- REAL -->
                        <input type="hidden" class="cantidad" value="0">

                    </td>
                 
                                    <td>

                                        <select class="form-select form-select-sm select-precio">
                                            <option value="<?= $p['precio_minorista'] ?>">Publico </option>
                                            <option value="<?= $p['precio_mayorista'] ?>">Constructora </option>
                                            <option value="<?= $p['precio_distribuidor'] ?>">Distribuidor </option>
                                        </select>
                                    </td>
                                    

                                  


                 
                        <td>
                                        <input type="number" step="0.01"
                                                class="form-control form-control-sm input-precioMayor"
                                                value="<?= $p['precio_minorista']??0 ?>">
                                                <input type="hidden" step="0.01"
                                                class="form-control form-control-sm input-precio"
                                                value="<?= $p['precio_minorista'] ?>">
                                        
                   


                    </td>

                    <td class="text-center">
                        <button type="button" class="btn btn-success btn-sm" data-producto-id="<?= $p['id'] ?>"
                            data-almacen-id="<?= $p['almacen_id'] ?>"
                            data-almacen="<?= htmlspecialchars($p['almacen_nombre']) ?>"
                            onclick="validarYAgregar(this)">
                            <i class="bi bi-plus"></i>
                        </button>
                    </td>
                    </tr>
                    <?php endforeach; ?>
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
                        <tbody></tbody>
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


    <div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-labelledby="modalNuevoClienteLabel"
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
                                <label class="form-label fw-bold">Contacto *</label>
                                <input type="text" name="contacto" class="form-control"
                                    placeholder="Contacto" >
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Razón Social</label>
                                <input type="text" name="razon_social" class="form-control"
                                    placeholder="Nombre legal completo">
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
                                <div class="col-md-12 mb-2"  style="visibility: hidden;">
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

    <?php cargarScripts(); ?>
    <script>
    // Selecciona todos los inputs de texto y también los textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            // Convierte el valor a mayúsculas en tiempo real
            this.value = this.value.toUpperCase();
        });
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="/myvet/app/backend/js_ventas/filtros.js"></script>
    <script src="/myvet/app/backend/js_ventas/nuevo_cliente.js"></script>
    <?php require_once __DIR__ . '/cajaRapida/ModalFinalizarVenta.php'; ?>
    <script>
    /**
     * SISTEMA DE VENTAS CF SYSTEM - Gestión de Carrito con Validación de Stock
     */

    window.carrito = window.carrito || [];

    /**
     * 1. AGREGAR PRODUCTO AL CARRITO
     */
    window.validarYAgregar = function(btn) {
        const fila = btn.closest("tr");
        const producto_id = parseInt(btn.dataset.productoId || btn.getAttribute("data-producto-id"));
        const almacen_id = parseInt(btn.dataset.almacenId);
        const almacen_nombre = btn.dataset.almacen;

        // Captura de Stock y Factores
        const stockMaximo = parseFloat(fila.querySelector(".badge.bg-success").innerText) || 0;
        const factor = parseFloat(fila.dataset.factor) || 1;
        const unidadReporte = fila.dataset.reporteNom || 'Fact.';
        const nombre = fila.cells[2].innerText;
const inputUsuario =
            fila.querySelector('.cantidad_usuario')?.value;
           
        
        const modoVenta = fila.querySelector(".select-modo-venta")?.value || 'individual';
        const modoVent =
            fila.querySelector(".select-modo-venta");

        const unidad_medida =
            modoVent?.options?. [modoVent.selectedIndex]?.dataset?.nombre || 'PZA';
        console.log(modoVenta);

        console.log(unidad_medida);
        const select =
            fila.querySelector('.medidas_adicionales');

        const equivalencia =
            parseFloat(select.value);
const cantidadInput = fila.querySelector(".cantidad");
        let cantidadAAgregar = parseFloat(( inputUsuario*(1/equivalencia)).toFixed(5))|| 0;

        const medidaId =
            select.options[select.selectedIndex].dataset.id;
        const medidaNombre =
            select.options[select.selectedIndex].dataset.nombre;


        console.log(equivalencia);
        console.log(medidaId, medidaNombre);
        select.selectedIndex = 0;

        // Si se agrega en modo "Referencia/Reporte" (ej. Tonelada), convertimos a piezas
        if (modoVenta === 'referencia') {
            cantidadAAgregar = cantidadAAgregar * factor;
        }

        const selectPrecio = fila.querySelector(".select-precio");

        const selectPrecioInput = fila.querySelector(".input-precio");
        const precioUnitario = parseFloat(selectPrecioInput.value) || 0;
        let textoPrecio = selectPrecio.options[selectPrecio.selectedIndex].text.toLowerCase();
        let tipo_p = textoPrecio.includes("dist") ? "distribuidor" : (textoPrecio.includes("may") ? "mayorista" :
            "minorista");

        if (cantidadAAgregar <= 0) {
            Swal.fire('Atención', 'Ingresa una cantidad válida', 'warning');
            return;
        }

        // Buscar si ya existe en el carrito para validar stock acumulado
        let itemExistente = window.carrito.find(item =>
            item.producto_id === producto_id && item.almacen_id === almacen_id && item.tipo_precio === tipo_p
        );

        let cantidadTotalFutura = (itemExistente ? itemExistente.cantidad : 0) + cantidadAAgregar;

        // VALIDACIÓN DE STOCK FÍSICO
        if (cantidadTotalFutura > stockMaximo) {
            Swal.fire('Stock Insuficiente', `No puedes agregar esa cantidad. Stock disponible: ${stockMaximo}`,
                'error');
            return;
        }
        console.log((inputUsuario*precioUnitario));

        if (itemExistente) {
            itemExistente.cantidad = cantidadTotalFutura;
            itemExistente.subtotal += cantidadAAgregar*precioUnitario;
        } else {
            window.carrito.push({
                producto_id,
                almacen_id,
                almacen_nombre,
                nombre,
                cantidad: cantidadAAgregar,
                stock_max: stockMaximo, // Guardamos el límite físico
                entrega_hoy: cantidadAAgregar,
                cantidadUsuario:inputUsuario,
                precio_unitario: precioUnitario,
                subtotal:inputUsuario*precioUnitario,
                tipo_precio: tipo_p,
                factor: factor,
                unidad_reporte: unidadReporte,
                unidad_medida: unidad_medida || 'Fact.',
                unidadMedidaSelect: medidaId ?? '0',
                unidadMedidaNombre: medidaNombre ?? '',
                unidadEquivalencia: equivalencia ?? 1
            });
        }

        window.renderCarrito();
        cantidadInput.value = 1;
    };

    /**
     * 2. RENDERIZAR TABLA (Ajuste de inputs con MAX)
     */









    window.renderCarrito = function() {
        const tablaBody = document.querySelector("#tablaCarrito tbody");
        if (!tablaBody) return;

        tablaBody.innerHTML = "";

        window.carrito.forEach((item, index) => {
            console.log(item);
            const cantFactor = Math.floor(item.cantidad / item.factor);
         const cantPza = Math.round((item.cantidad % item.factor) * 10000) / 10000;


            

            const tr = document.createElement("tr");
            tr.dataset.index = index;
            tr.innerHTML = `
        
            <td><small>${item.almacen_nombre}</small></td>
            <td><div class="fw-bold" style="font-size: 0.8rem;">${item.nombre}</div></td>
            <td>
                <input type="hidden" class="form-control text-uppercase form-control-sm text-center input-factor-cambio" 
                    data-index="${index}" value="${cantFactor}" min="0" 
                    max="${Math.floor(item.stock_max / item.factor)}" step="1">
                    <small class="form-control form-control-sm text-center ">${item.cantidad*item.unidadEquivalencia}</small>
              
            </td>
            <td>
                <input type="hidden" class="form-control form-control-sm text-center input-pza-cambio" 
                    data-index="${index}" value="${cantPza}" min="0" step=".0001">
            <small class="form-control form-control-sm text-center ">${item.unidadMedidaNombre}</small>
                
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
     * 3. LÓGICA DE CONTROL DE STOCK DINÁMICO (input)
     */
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('input-factor-cambio') || e.target.classList.contains(
                'input-pza-cambio')) {
            const index = e.target.dataset.index;
            const item = window.carrito[index];
            const tr = e.target.closest('tr');

            const inputFactor = tr.querySelector('.input-factor-cambio');
            const inputPza = tr.querySelector('.input-pza-cambio');

            let valFactor = parseFloat(inputFactor.value) || 0;
            let valPza = parseFloat(inputPza.value) || 0;

            // Validar primero el Factor contra el stock total
            let maxFactoresPosibles = Math.floor(item.stock_max / item.factor);
            if (valFactor > maxFactoresPosibles) {
                valFactor = maxFactoresPosibles;
                inputFactor.value = valFactor;
            }

            // Calcular piezas restantes permitidas basándose en los factores ya puestos
            let stockRestantePzas = item.stock_max - (valFactor * item.factor);

            if (valPza > stockRestantePzas) {
                valPza = stockRestantePzas;
                inputPza.value = valPza;
                // Feedback visual rápido
                inputPza.style.borderColor = "#ff3b30";
                setTimeout(() => inputPza.style.borderColor = "#d2d2d7", 500);
            }

            // Actualización del objeto
            item.cantidad = (valFactor * item.factor) + valPza;
            item.subtotal = item.cantidad * item.precio_unitario;
            item.entrega_hoy = item.cantidad;

            tr.querySelector('.subtotal-celda').innerText = `$${item.subtotal.toFixed(2)}`;
            actualizarTotalesUI();
        }
    });

    /**
     * 4. LÓGICA DE NORMALIZACIÓN (change)
     */
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('input-factor-cambio') || e.target.classList.contains(
                'input-pza-cambio')) {
            // Redibujamos para que si puso piezas equivalentes a 1 factor, se "brinde" al campo correcto
            window.renderCarrito();
        }
    });

    /**
     * 5. ACTUALIZAR INTERFAZ
     */
    function actualizarTotalesUI() {
        let totalAcumulado = window.carrito.reduce((acc, item) => acc + (item.subtotal), 0);
        const totalStr = totalAcumulado.toFixed(2);

        const elTotal = document.getElementById("total");
        const elTotalModal = document.getElementById("totalFinalModal");
        const elPago = document.getElementById("monto_pagar");

        if (elTotal) elTotal.innerText = totalStr;
        if (elTotalModal) elTotalModal.innerText = totalStr;
        if (elPago) {
            elPago.value = totalStr;
            elPago.dispatchEvent(new Event('input'));
        }
    }

    // Eliminar producto
    document.addEventListener('click', function(e) {
        const btnDelete = e.target.closest('.btn-remove-item');
        if (btnDelete) {
            const index = btnDelete.dataset.index;
            window.carrito.splice(index, 1);
            window.renderCarrito();
        }
    });

    /**
     * Soporte para agregar productos al presionar ENTER
     */
    document.addEventListener('keydown', function(e) {
        // 1. Verificamos que la tecla sea Enter y que el foco esté en un input de cantidad
        if (e.key === 'Enter' && e.target.classList.contains('input-precioMayor')) {

            // Evitamos que el Enter haga un submit accidental del formulario principal
            e.preventDefault();

            // 2. Localizamos la fila (tr) donde se presionó Enter
            const fila = e.target.closest('tr');

            // 3. Buscamos el botón de "Agregar" (+) en esa misma fila
            const btnAgregar = fila.querySelector('button.btn-success');

            if (btnAgregar) {
                // 4. Ejecutamos la función de agregar que ya tienes definida
                validarYAgregar(btnAgregar);

                // Opcional: Feedback visual rápido para el usuario
                btnAgregar.style.transform = "scale(0.9)";
                setTimeout(() => btnAgregar.style.transform = "scale(1)", 100);
            }
        }
    });
    </script>
   
<script>
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

    const equi = Math.round((1 / equivalencia) * 10000) / 10000;

    let nuevoPrecio = (equi)*precio;

   

    // Aquí sí actualizamos ambos porque NO viene del input-precioMayor
    inputPrecio.value = nuevoPrecio;
    inputPrecioMayor.value = nuevoPrecio;

    const totalReal = Math.round((cantidadUsuario / equivalencia* 10000) / 10000);
    inputReal.value = totalReal;

    console.log({
        usuario: cantidadUsuario,
        equivalencia,
        real: totalReal,
        total:nuevoPrecio
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