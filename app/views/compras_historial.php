<?php
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0); // 0 = admin
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Lotes</title>
    <link rel="icon" type="image/png" href="/myvet/public/assets/logo.png">

    <link rel="shortcut icon" href="/myvet/public/assets/logo.ico" type="image/x-icon">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
    <?php require_once __DIR__ . '/layout/icono.php' ?>

    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 65px;
            --apple-bg: #f5f5f7;
            --accent-blue: #007aff;
        }

        body {
            background-color: var(--apple-bg);
            font-family: 'SF Pro Display', -apple-system, sans-serif;

        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 40px;
            padding-top: calc(var(--navbar-height) + 20px);
        }

        .card-premium {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
                padding-top: 90px;
            }
        }
    </style>
</head>

<body>
    <?php if (function_exists('renderizarLayout')) {
        renderizarLayout($paginaActual);
    } ?>

    <main class="main-content">
        <h3 class="mb-3">📦 Historial Compras</h3>

        <div class="card p-3 mb-3">
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Almacén</label>
                    <select id="filtroAlmacen" class="form-select">
                        <option value="0">Todos</option>
                        <?php foreach ($almacenes as $a): ?>
                            <option value="<?= $a['id'] ?>">
                                <?= htmlspecialchars($a['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>




                <div class="col-md-2">
                    <label class="form-label small fw-bold">Desde</label>
                    <input type="date" id="fecha_inicio" class="form-control" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Hasta</label>
                    <input type="date" id="fecha_fin" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>

                <div class="col-md-2 d-grid">
                    <label class="invisible">.</label>
                    <button class="btn btn-dark" onclick="cargarHistorial()">
                        Consultar
                    </button>
                </div>
            </div>
        </div>


        <div class="card p-3">
            <div class="row g-3 mb-3 align-items-end">
                <div class="col-md-6">
                    <label for="buscarTabla" class="form-label small fw-bold">Buscar en resultados</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="search" id="buscarTabla" class="form-control"
                            placeholder="Folio, proveedor, producto, estado...">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="proveedor_id" class="form-label small fw-bold">Proveedor</label>
                    <select name="proveedor_id" id="proveedor_id" class="form-select border-slate-200" required>
                        <option value="">Seleccionar proveedor...</option>
                        <?php foreach ($proveedores as $p): ?>
                            <option value="<?= $p['id'] ?>" data-razon="<?= htmlspecialchars($p['razon_social'] ?? '') ?>">
                                <?= htmlspecialchars($p['nombre_comercial']) ?>
                                <?= !empty($p['razon_social']) ? ' | ' . htmlspecialchars($p['razon_social']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="contenedorTablaHistorial" class="table-responsive overflow-auto"
                style="max-height: calc(100dvh - 280px);">
                <table class="table table-hover align-middle mb-0">
                    <thead class="sticky-top bg-body z-1 shadow-sm">
                        <tr>
                            <th>Almacén</th>
                            <th>RECEPCION</th>
                            <th>COMPRA</th>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Productos</th>
                            <th>Estado</th>
                            <th>Costo</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tablaHistorial">
                        <tr>
                            <td colspan="9" class="text-center text-body-secondary">Selecciona un producto</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>




    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>
    <script>
        function ajustarAltoTablaHistorial() {
            const el = document.getElementById('contenedorTablaHistorial');
            if (!el) return;

            const margenInferior = 24; // Respiro abajo del card/pantalla
            const rect = el.getBoundingClientRect();
            const altoDisponible = window.innerHeight - rect.top - margenInferior;

            // Evita que quede demasiado pequeña en pantallas muy bajas
            el.style.maxHeight = Math.max(220, Math.floor(altoDisponible)) + 'px';
        }

        $(document).ready(function () {
            ajustarAltoTablaHistorial();
            $(window).on('resize', ajustarAltoTablaHistorial);
        });

        $(function () {
            // Almacén
            $('#filtroAlmacen').select2({
                theme: 'bootstrap-5',
                language: 'es',
                width: '100%'
            });

            // Proveedor: el texto del option ya incluye "nombre | razón", así busca por ambos
            $('#proveedor_id').select2({
                theme: 'bootstrap-5',
                language: 'es',
                width: '100%',
                placeholder: 'Seleccionar proveedor...',
                allowClear: true,

                templateResult: function (state) {
                    if (!state.id) return state.text;

                    const razon = $(state.element).data('razon') || '';
                    const nombre = state.text.split(' | ')[0].trim();

                    return $(`
                <div style="line-height:1.15; padding:2px 0;">
                    <div style="font-weight:600; color:#052a52; font-size:0.9rem;">${nombre}</div>
                    ${razon ? `<div style="font-size:0.72rem; color:#5a7090; margin-top:2px;">${razon}</div>` : ''}
                </div>`);
                },

                templateSelection: function (state) {
                    return state.id ? state.text.split(' | ')[0].trim() : state.text;
                }
            });

            $('#proveedor_id').on('change', filtrarTablaHistorial);

            // Carga inicial respetando filtros actuales si ya existieran
            cargarHistorial();
        });

        function filtrarTablaHistorial() {
            const termino = ($('#buscarTabla').val() || '').toLowerCase().trim();

            // Proveedor seleccionado: nombre comercial y razón social
            const $opt = $('#proveedor_id option:selected');
            const provNombre = $opt.val() ? $opt.text().split(' | ')[0].trim().toLowerCase() : '';
            const provRazon = $opt.val() ? String($opt.data('razon') || '').trim().toLowerCase() : '';

            // Quitar fila previa de "sin coincidencias" si existía
            $('#filaSinCoincidencias').remove();

            // Las filas de estado (vacío / cargando) tienen una sola celda y no se filtran
            const $filasDatos = $('#tablaHistorial tr').filter(function () {
                return $(this).find('td').length > 1;
            });

            if (!$filasDatos.length) return;

            if (!termino && !provNombre) {
                $filasDatos.show();
                return;
            }

            let visibles = 0;
            $filasDatos.each(function () {
                const $fila = $(this);

                // 1) Coincidencia con el buscador de texto (toda la fila + títulos)
                const textoFila = $fila.text().toLowerCase();
                const titulosFila = $fila.find('[title]').map(function () {
                    return $(this).attr('title');
                }).get().join(' ').toLowerCase();
                const coincideTexto = !termino || textoFila.includes(termino) || titulosFila.includes(termino);

                // 2) Coincidencia con el proveedor (columna 4 = Proveedor)
                const celdaProveedor = $fila.find('td').eq(3).text().toLowerCase();
                const coincideProveedor = !provNombre ||
                    celdaProveedor.includes(provNombre) ||
                    (provRazon !== '' && celdaProveedor.includes(provRazon));

                const coincide = coincideTexto && coincideProveedor;
                $fila.toggle(coincide);
                if (coincide) visibles++;
            });

            if (visibles === 0) {
                $('#tablaHistorial').append(`
            <tr id="filaSinCoincidencias">
                <td colspan="9" class="text-center text-body-secondary py-3">
                    No se encontraron compras que coincidan con los filtros.
                </td>
            </tr>`);
            }
        }

        // Escuchar escritura y borrado (incluye botón 'x' de input type="search")
        $(document).on('input search keyup', '#buscarTabla', function () {
            filtrarTablaHistorial();
        });

        function cargarHistorial() {
            const producto = $('#filtroProducto').val();
            const almacen = $('#filtroAlmacen').val() ?? 0;
            const f_ini = $('#fecha_inicio').val();
            const f_fin = $('#fecha_fin').val();

            $.ajax({
                url: '/myvet/app/controllers/comprasHistorialController.php',
                type: 'GET',
                data: {
                    action: 'obtenerCompras',
                    almacen_id: almacen,
                    periodo: 'personalizado',
                    f_inicio: f_ini,
                    f_fin: f_fin
                },
                dataType: 'json',
                success: function (res) {
                    console.log(res.data);

                    let html = '';
                    if (!res.success || !res.data.length) {
                        $('#tablaHistorial').html(
                            '<tr><td colspan="9" class="text-center">Sin datos</td></tr>');
                        return;
                    }

                    // 1. Agrupamos usando Map para mantener el ORDEN EXACTO en el que vienen los datos
                    const comprasMap = new Map();

                    res.data.forEach(item => {
                        if (!comprasMap.has(item.id)) {
                            comprasMap.set(item.id, {
                                id: item.id,
                                folio: item.folio,
                                almacen: item.almacen,
                                fecha_compra: item.fecha_compra,
                                orden_compra: item.orden_compra ?? '—',
                                estado: item.estado,
                                estado_compra: item.estado_compra,
                                total: item.total,
                                proveedor: item.proveedor,
                                productosList: []
                            });
                        }
                        comprasMap.get(item.id).productosList.push({
                            id: item.producto_id,
                            nombre: item.productos,
                            cantidad: item.cantidadProd,
                            faltante: item.faltante,
                            sobrante: item.sobrante,
                            factor: item.factor_conversion,
                            uMedida: item.unidad_medida,
                            uReporte: item.unidad_reporte
                        });
                    });

                    // 2. Generamos el HTML manteniendo el orden original
                    comprasMap.forEach(compra => {
                        let nombresProductos =
                            '<ul class="list-unstyled m-0 p-0" style="font-size: 11px; line-height: 1.3;">';
                        compra.productosList.forEach(p => {
                            let cantidad = p.cantidad / p.factor;
                            let totalC = cantidad >= 1
                                ? p.nombre + ' ' + cantidad + ' ' + p.uReporte
                                : p.nombre + ' ' + p.cantidad + ' ' + p.uMedida;

                            nombresProductos +=
                                `<li class="text-truncate" style="max-width: 250px;" title="${totalC}">• ${totalC}</li>`;
                        });
                        nombresProductos += '</ul>';

                        let columnaAccion = '';

                        if (compra.productosList.length === 1) {
                            let prod = compra.productosList[0];

                            columnaAccion = `
                        <button
                            class="btn btn-sm btn-outline-primary"
                            onclick='verMovimientos(
                                ${prod.id},
                                ${compra.id},
                                ${JSON.stringify(compra.folio)},
                                ${JSON.stringify(prod)}
                            )'>
                            Ver
                        </button>
                    `;
                        } else {
                            let opciones = '';
                            compra.productosList.forEach(prod => {
                                let cantidad = prod.cantidad / prod.factor;
                                let totalC = cantidad >= 1
                                    ? prod.nombre + ' ' + cantidad + ' ' + prod.uReporte
                                    : prod.nombre + ' ' + prod.cantidad + ' ' + prod.uMedida;
                                opciones += `
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center gap-2"
                                   href="#"
                                   onclick='event.preventDefault(); verMovimientos(
                                       ${prod.id},
                                       ${compra.id},
                                       ${JSON.stringify(compra.folio)},
                                       ${JSON.stringify(prod)}
                                   );'>
                                    <span class="text-truncate" style="max-width: 150px;">${totalC}</span>
                                    <small class="text-primary fw-bold">Ver</small>
                                </a>
                            </li>
                        `;
                            });

                            columnaAccion = `
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Opciones (${compra.productosList.length})
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="font-size: 11px;">
                                ${opciones}
                            </ul>
                        </div>
                    `;
                        }

                        (compra.faltante > 0 && compra.estado != 'cancelada') ? compra.estado = 'pendiente' : '';

                        const color2 =
                            compra.estado === 'confirmada' ? 'success' :
                                compra.estado === 'pendiente' ? 'primary' :
                                    compra.estado === 'cancelada' ? 'danger' :
                                        'secondary';

                        html += `
                    <tr>
                        <td>${compra.almacen}</td>
                        <td class="fw-bold">#${compra.folio}</td>
                        <td class="text-body-secondary">${compra.orden_compra}</td>
                        <td class="text-body-secondary">${compra.proveedor}</td>
                        <td>${compra.fecha_compra}</td>
                        <td style="max-width: 220px;" class="text-truncate">${nombresProductos}</td>
                        <td>
                            <span class="badge bg-${color2}">
                                ${compra.estado}
                            </span>
                        </td>
                        <td class="text-end fw-bold">$${parseFloat(compra.total || 0).toFixed(2)}</td>
                        <td class="text-center">${compra.estado != 'Cancelada' ? columnaAccion : ''}</td>
                    </tr>`;
                    });

                    $('#tablaHistorial').html(html);

                    // Aplica en automático el filtro de proveedor y búsqueda respetando la selección actual
                    filtrarTablaHistorial();
                    ajustarAltoTablaHistorial();
                }
            });
        }


        function verMovimientos(producto_id, compra_id, compra_folio, prod) {

            console.log("Compra enviada:", compra_folio);

            $.ajax({
                url: '/myvet/app/controllers/comprasHistorialController.php',
                type: 'GET',
                data: {
                    action: 'obtenerVentasCompra',
                    producto_id: producto_id,
                    compra_id: compra_id
                },
                dataType: 'json',

                success: function (res) {
                    console.log("RESPUESTA:", res);

                    if (!res.success) {
                        console.error(res.message);
                        alert(res.message);
                        return;
                    }
                    generarReporteMovimientos(res.reparto, res.data, compra_folio, prod);

                    let html = '';

                    if (res.data.length > 0) {
                        console.log(res.data);
                    } else {
                        html = '<tr><td colspan="14" class="text-center">Sin movimientos</td></tr>';
                    }

                    $('#tablaMovimientosLote').html(html);
                },

                error: function (xhr, status, error) {

                    console.log(xhr);
                    console.log(xhr.responseText);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error AJAX',
                        text: typeof xhr.responseText === 'string' ?
                            xhr.responseText : JSON.stringify(xhr.responseText)
                    });

                }
            });
        }

        /* =====================================================================
           REPORTE POR ALMACÉN
           Una tarjeta por almacén, en el orden en que viajó el material:
             Compra -> Almacén A (ventas, traspasos)
                 Traspaso -> Almacén B (ventas, traspasos)
                     Traspaso -> Almacén C ...
           Cada traspaso es una línea y enlaza a la tabla del almacén que lo recibe.
           ===================================================================== */

        // true  = la utilidad total incluye lo vendido en los almacenes a donde se traspasó
        // false = solo ventas de los almacenes de la compra (como estaba antes)
        const INCLUIR_VENTAS_DE_TRASPASOS = true;

        /* ---------- Utilidades de formato ---------- */

        const rmEsc = v => String(v ?? '').replace(/[&<>"]/g, c =>
            ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
        const rmNum = v => (parseFloat(v) || 0).toLocaleString('es-MX', { maximumFractionDigits: 2 });
        const rmDinero = v => '$' + (parseFloat(v) || 0).toLocaleString('es-MX',
            { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Cantidad en unidad de reporte (caja, bulto...) si llega a 1; si no, en unidad base
        function rmCantidad(base, factor, uRep, uMed) {
            base = parseFloat(base) || 0;
            factor = parseFloat(factor) || 1;
            const grande = base / factor;
            return grande >= 1 ? `${rmNum(grande)} ${rmEsc(uRep)}` : `${rmNum(base)} ${rmEsc(uMed)}`;
        }

        // Precio o costo por unidad de reporte o por unidad base, según la cantidad
        function rmPrecio(unitario, cantidadBase, factor, uRep, uMed) {
            unitario = parseFloat(unitario) || 0;
            factor = parseFloat(factor) || 1;
            const grande = (parseFloat(cantidadBase) || 0) / factor;
            return grande >= 1
                ? unitario * grande
                : unitario * cantidadBase;
        }
        function rmCosto(unitario, cantidadBase, factor, uRep, uMed) {
            unitario = parseFloat(unitario) || 0;
            factor = parseFloat(factor) || 1;
            const grande = (parseFloat(cantidadBase) || 0) / factor;
            return grande >= 1
                ? `${rmDinero(unitario * cantidadBase)} x ${rmEsc(uRep)}`
                : `${rmDinero(unitario * cantidadBase)} x ${rmEsc(uMed)}`;
        }
        function rmCostoNumero(unitario, cantidadBase, factor, uRep, uMed) {
            unitario = parseFloat(unitario) || 0;
            factor = parseFloat(factor) || 1;
            const grande = (parseFloat(cantidadBase) || 0) / factor;
            return grande >= 1
                ? unitario * cantidadBase
                : unitario * cantidadBase;
        }

        /* ---------- Movimientos del lote que recibe un traspaso ---------- */

        function rmMovimientosLote(lote_id) {
            let data = [];
            $.ajax({
                url: '/myvet/app/controllers/lotesHistorialController.php',
                type: 'GET',
                async: false, // síncrono para armar el reporte en orden
                data: { action: 'obtenerVentasLote', lote_id: parseInt(lote_id) },
                dataType: 'json',
                success: res => { if (res.success && Array.isArray(res.data)) data = res.data; },
                error: xhr => console.error('Error al consultar el lote', lote_id, xhr.responseText)
            });
            return data;
        }

        /* ---------- Construye la tarjeta de un almacén (y las de sus destinos) ---------- */

        function rmConstruirSeccion(base, movs, visitados, secciones) {
            const s = { ...base, id: secciones.length, vendido: 0, traspasado: 0, ganancia: 0, filas: '' };
            secciones.push(s);

            movs.forEach(m => {
                console.log(m);
                const salida = parseFloat(m.cantidad_salida) || 0;
                const f = m.factor, uR = m.unidad_reporte, uM = m.unidad_medida;

                if (m.tipo_movimiento !== 'TRASPASO') {

                    const factor = parseFloat(f) || 1;
                    let costoUnit = m.costo_unitario;
                    const costoVendido = (salida / factor) * (parseFloat(m.costo_unitario) || 0);
                    // Si el margen sobre el costo supera 30%, la venta es de una entrega con varios lotes
                    // y precio_venta es el precio de la entrega: se usa precio_venta / factor
                    let precio = m.precio_venta;
                    if (costoUnit > 0 && (precio / salida) > costoUnit * 1.30) {
                        precio = precio / factor;

                    }
                    const g = rmPrecio(precio, salida, f, uR, uM) - (m.costo_unitario * salida);
                    const precio_real = rmPrecio(precio, salida, f, uR, uM);

                    s.ganancia += g;
                    s.vendido += salida;

                    s.filas += `
                    <tr>
                        <td>${rmEsc(m.fecha_movimiento)}</td>
                        <td>${rmEsc(m.documento)}</td>
                        <td><span class="tag venta">${rmEsc(m.tipo_movimiento)}</span></td>
                        <td>${rmEsc(m.cliente_proveedor)}</td>
                        <td class="num">${rmCantidad(salida, f, uR, uM)}</td>
                        <td class="num">${rmCantidad(m.saldo_final, f, uR, uM)}</td>
                        <td class="num">${rmDinero(precio_real)}</td>
                        <td class="num gain">${rmDinero(g)}</td>
                    </tr>`;
                    return;
                }

                // ----- Traspaso: una línea aquí + una tarjeta nueva para el almacén que recibe -----
                s.traspasado += salida;

                const lote = parseInt(m.referencia_extra);
                const hijo = { id: null };

                if (lote && !visitados.has(lote)) {
                    visitados.add(lote);
                    const movsDestino = rmMovimientosLote(lote);
                    const h = rmConstruirSeccion({
                        almacen: m.cliente_proveedor,
                        lote: m.lote_destino_traspaso,
                        origen: `Traspaso desde ${s.almacen} (lote ${s.lote})`,
                        recibido: m.cantidad_salida,
                        factor: f, uRep: uR, uMed: uM,
                        costoUnit: m.costo_unitario,
                        fecha: m.fecha_movimiento,
                        nivel: s.nivel + 1,
                        tipo: 'traspaso'
                    }, movsDestino, visitados, secciones);
                    hijo.id = h.id;
                }

                s.filas += `
                <tr class="traspaso">
                    <td>${rmEsc(m.fecha_movimiento)}</td>
                    <td>${rmEsc(m.documento)}</td>
                    <td><span class="tag tras">TRASPASO</span></td>
                    <td>Enviado a <b>${rmEsc(m.cliente_proveedor)}</b>
                        <span class="muted">· lote ${rmEsc(m.lote_destino_traspaso)}</span></td>
                    <td class="num">${rmCantidad(salida, f, uR, uM)}</td>
                    <td class="num">${rmCantidad(m.saldo_final, f, uR, uM)}</td>
                    <td class="num" colspan="2">
                        ${hijo.id !== null ? `<a href="#almacen-${hijo.id}">Ver qué pasó allá ↓</a>` : '<span class="muted">—</span>'}
                    </td>
                </tr>`;
            });

            if (!movs.length) {
                s.filas = `<tr><td colspan="8" class="vacio">Sin movimientos: todo el material sigue en inventario.</td></tr>`;
            }
            return s;
        }

        /* ---------- Reporte principal ---------- */

        function generarReporteMovimientos(reparto, data, compra_folio, prod) {
            const secciones = [];
            const visitados = new Set();
            const proveedor = reparto[0]?.nombre_comercial || '';

            // Un almacén por cada renglón de la distribución de la compra
            reparto.forEach(rep => {
                const movs = data.filter(m =>
                    Number(m.almacen_id) === Number(rep.almacen_id) &&
                    String(m.codigo_lote || '').trim() === String(rep.codigo_lote || '').trim());

                rmConstruirSeccion({
                    almacen: rep.almacen,
                    lote: rep.codigo_lote,
                    origen: `Compra ${compra_folio}`,
                    recibido: rep.cantidad_inicial,
                    factor: rep.factor_conversion, uRep: rep.unidad_reporte, uMed: rep.unidad_medida,
                    costoUnit: rep.costo_unitario,
                    fecha: rep.fecha_ingreso,
                    nivel: 0,
                    tipo: 'compra'
                }, movs, visitados, secciones);
            });

            const utilidadTotal = secciones
                .filter(s => INCLUIR_VENTAS_DE_TRASPASOS || s.nivel === 0)
                .reduce((acc, s) => acc + s.ganancia, 0);

            /* ----- Resumen (ruta del material) ----- */
            const filasResumen = secciones.map(s => {
                const saldo = (parseFloat(s.recibido) || 0) - s.vendido - s.traspasado;
                const c = (v) => rmCantidad(v, s.factor, s.uRep, s.uMed);

                return `
                <tr>
                    <td style="padding-left:${8 + s.nivel * 18}px">
                        ${s.nivel ? '<span class="muted">↳</span> ' : ''}<a href="#almacen-${s.id}"><b>${rmEsc(s.almacen)}</b></a>
                        <span class="muted">· lote ${rmEsc(s.lote)}</span>
                    </td>
                    <td class="num">${c(s.recibido)}</td>
                    <td class="num">${c(s.vendido)}</td>
                    <td class="num">${c(s.traspasado)}</td>
                    <td class="num">${c(saldo)}</td>
                    <td class="num gain">${rmDinero(s.ganancia)}</td>
                </tr>`;
            }).join('');

            /* ----- Tarjetas por almacén ----- */
            const tarjetas = secciones.map(s => {

                const costo = rmCosto(s.costoUnit, s.recibido, s.factor, s.uRep, s.uMed);
                const costoNumero = rmCostoNumero(s.costoUnit, s.recibido, s.factor, s.uRep, s.uMed);
                const utilidad = (s.ganancia);

                return `
                <section class="almacen ${s.tipo}" id="almacen-${s.id}">
                    <header>
                        <div>
                            <h3>${rmEsc(s.almacen)} <span class="lote">Lote ${rmEsc(s.lote)}</span></h3>
                            <div class="muted">${rmEsc(s.origen)} · ${rmEsc(s.fecha)}</div>
                        </div>
                        <div class="recibio">
                            Recibió <b>${rmCantidad(s.recibido, s.factor, s.uRep, s.uMed)}</b>
                            · Costo <b> ${rmDinero(costoNumero)} </b>
                        </div>
                    </header>
                    <table>
                        <thead><tr>
                            <th>Fecha</th><th>Documento</th><th>Movimiento</th><th>Cliente / destino</th>
                            <th class="num">Salida</th><th class="num">Saldo</th>
                            <th class="num">Precio</th><th class="num">Ganancia</th>
                        </tr></thead>
                        <tbody>${s.filas}</tbody>
                    </table>
                    <footer>Ganancia en este almacén: <b class="gain">${rmDinero(utilidad)}</b></footer>
                </section>`;
            }).join('');

            /* ----- Datos del producto ----- */
            const cant = prod.cantidad / prod.factor;
            const totalC = cant >= 1
                ? `${prod.nombre} · ${cant} ${prod.uReporte} (${prod.cantidad} ${prod.uMedida})`
                : `${prod.nombre} · ${prod.cantidad} ${prod.uMedida}`;
            const faltante = rmCantidad(prod.faltante, prod.factor, prod.uReporte, prod.uMedida);

            const ventana = window.open('', '_blank', 'width=1500,height=950');
            ventana.document.write(`<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Compra ${rmEsc(compra_folio)} · Movimientos</title>
<link rel="icon" type="image/png" href="/myvet/public/assets/logo.png">
<style>
    :root {
        --tinta: #1c2430; --suave: #6b7480; --linea: #e3e6ea; --fondo: #f4f6f8;
        --compra: #0f766e; --traspaso: #b45309; --traspaso-bg: #fff8ec;
        --ganancia: #0a7a45; --alerta: #b42318;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { font: 12px/1.45 system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
           color: var(--tinta); padding: 24px; max-width: 1200px; margin: 0 auto; }
    a { color: var(--traspaso); text-decoration: none; }
    a:hover { text-decoration: underline; }
    .muted { color: var(--suave); font-weight: normal; }
    .gain { color: var(--ganancia); font-weight: 600; }
    h2 { font-size: 15px; margin: 28px 0 10px; }

    /* Encabezado */
    .top { display: flex; justify-content: space-between; align-items: flex-end;
           border-bottom: 2px solid var(--tinta); padding-bottom: 12px; }
    .top h1 { font-size: 22px; }
    .top .utilidad { text-align: right; color: var(--suave); }
    .top .utilidad b { display: block; font-size: 24px; color: var(--ganancia); }
    .datos { display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 12px; margin-top: 14px; }
    .datos div { background: var(--fondo); padding: 10px 12px; border-radius: 6px; }
    .datos span { display: block; color: var(--suave); font-size: 11px; }
    .datos b { font-size: 14px; }
    .datos .falta b { color: var(--alerta); }

    /* Tablas */
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; font-weight: 600; font-size: 11px; color: var(--suave);
         padding: 7px 8px; border-bottom: 1px solid var(--tinta); }
    td { padding: 7px 8px; border-bottom: 1px solid var(--linea); vertical-align: top; }
    .num { text-align: right; font-variant-numeric: tabular-nums; }
    th.num { text-align: right; }

    /* Tarjeta por almacén */
    .almacen { margin-bottom: 26px; border: 1px solid var(--linea); border-left: 4px solid var(--compra);
               border-radius: 6px; overflow: hidden; scroll-margin-top: 14px; }
    .almacen.traspaso { border-left-color: var(--traspaso); }
    .almacen > header { display: flex; justify-content: space-between; align-items: flex-end;
                        gap: 16px; padding: 12px 14px; background: var(--fondo); }
    .almacen h3 { font-size: 15px; }
    .lote { font-size: 11px; font-weight: 600; background: #fff; border: 1px solid var(--linea);
            border-radius: 10px; padding: 1px 8px; margin-left: 6px; }
    .recibio { text-align: right; }
    .almacen > footer { padding: 9px 14px; text-align: right; background: var(--fondo); }
    tr.traspaso td { background: var(--traspaso-bg); }
    td.vacio { color: var(--suave); font-style: italic; padding: 14px; }

    .tag { font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 10px; white-space: nowrap; }
    .tag.venta { background: #e6f4ec; color: var(--ganancia); }
    .tag.tras  { background: #fdebd0; color: var(--traspaso); }

    @media print {
        body { padding: 0; }
        .almacen { break-inside: avoid-page; }
    }
</style>
</head>
<body>

<div class="top">
    <div>
        <h1>Compra ${rmEsc(compra_folio)}</h1>
        <div class="muted">Recorrido del material, almacén por almacén</div>
    </div>
    <div class="utilidad">Utilidad total<b>${rmDinero(utilidadTotal)}</b></div>
</div>

<div class="datos">
    <div><span>Proveedor</span><b>${rmEsc(proveedor)}</b></div>
    <div><span>Producto</span><b>${rmEsc(totalC)}</b></div>
    <div class="falta"><span>Faltante</span><b>${faltante}</b></div>
</div>

<h2>Ruta del material</h2>
<table>
    <thead><tr>
        <th>Almacén</th>
        <th class="num">Recibió</th><th class="num">Vendió</th>
        <th class="num">Traspasó</th><th class="num">Saldo</th>
        <th class="num">Utilidad</th>
    </tr></thead>
    <tbody>${filasResumen}</tbody>
</table>

<h2>Detalle por almacén</h2>
${tarjetas}

</body>
</html>`);
            ventana.document.close();
        }









        function cargarTraspasos(lote_id) {
            const f_ini = $('#fecha_inicio').val();
            const f_fin = $('#fecha_fin').val();

            $.ajax({
                url: '/myvet/app/controllers/lotesHistorialController.php',
                type: 'GET',
                data: {
                    action: 'obtenerTraspasos',
                    lote_id: lote_id,

                    f_inicio: f_ini,
                    f_fin: f_fin
                },
                dataType: 'json',
                success: function (res) {
                    let html = '';
                    if (res.data && res.data.length > 0) {
                        res.data.forEach(t => {
                            html +=
                                `<tr><td>TRASPASO</td><td>${t.fecha}</td><td>${t.movimiento_id}</td><td>${t.nombreOrigen}</td><td>${t.codigo_lote_origen}</td><td>${t.nombreDestino}</td><td>${t.codigo_lote_destino}</td><td>${t.cantidad}</td></tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="9" class="text-center">Sin traspasos</td></tr>';
                    }
                    $('#tablaTraspasosLote').html(html);
                }
            });
        }

        // $('#filtroAlmacen').on('change', function() {
        //     verMovimientos(0);
        //     let almacen = $(this).val();
        //     $.ajax({
        //         url: '/myvet/app/controllers/lotesHistorialController.php',
        //         type: 'GET',
        //         data: {
        //             action: 'productos',
        //             almacen_id: almacen
        //         },
        //         dataType: 'json',
        //         success: function(res) {
        //             let html = '<option value="">Selecciona producto</option>';
        //             if (res.success) {
        //                 res.data.forEach(p => {
        //                     html += `<option value="${p.id}">${p.nombre}</option>`;
        //                 });
        //             }
        //             $('#filtroProducto').html(html);
        //         }
        //     });
        // });
    </script>
</body>

</html>