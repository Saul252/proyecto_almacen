<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Compra | myvet</title>

    <!-- Favicons -->
    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <!-- CSS externos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <!-- CSS internos -->
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>
    <link href="/myvet/css/solicitudesCompra.css" rel="stylesheet" />
</head>

<body>
    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">
        <div class="glass-card p-4 ">

            <!-- Encabezado -->
            <div class="row align-items-center ">
                <div class="col-md-8">
                    <h1 class="h3 fw-bold mb-1">Solicitudes de Compra</h1>
                    <p class="text-body-secondary small">Gestión de requerimientos de materiales</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-add" onclick="nuevaSolicitud()">
                        <i class="bi bi-plus-lg me-2"></i> Crear Solicitud
                    </button>
                </div>
            </div>

            <!-- Filtros -->
            <div class="glass-card p-4 ">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-funnel-fill text-primary me-2"></i> Filtros de búsqueda
                        </h5>
                        <small class="text-body-secondary">
                            Filtra las cotizaciones por fecha, almacén, estado o cliente.
                        </small>
                    </div>
                </div>

                <div class="row g-3 align-items-end">
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">Inicio</label>
                        <input type="date" id="fechaInicio" value="<?= date('Y-m-01') ?>"
                            class="form-control border border-subtle shadow-sm" style="border-radius:12px;">
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">Fin</label>
                        <input type="date" id="fechaFin" value="<?= date('Y-m-d') ?>"
                            class="form-control border border-subtle shadow-sm" style="border-radius:12px;">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">Almacén</label>
                        <select id="filtroAlmacen" class="form-select border border-subtle shadow-sm"
                            style="border-radius:12px;">
                            <?php if (isset($es_admin) && $es_admin): ?>
                                <option value="">Todos los almacenes</option>
                            <?php endif; ?>
                            <?php foreach ($almacenes as $alm): ?>
                                <option value="<?= htmlspecialchars($alm['id']) ?>">
                                    <?= htmlspecialchars($alm['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">Estado</label>
                        <select id="filtroEstado" class="form-select border-light shadow-sm">
                            <option value="">Todos los estados</option>
                            <option value="PENDIENTE">Pendiente</option>
                            <option value="RECIBIDO">Recibida</option>
                            <option value="CANCELADO">CANCELADA</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-12">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">Buscar</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text border border-subtle">
                                <i class="bi bi-search text-secondary"></i>
                            </span>
                            <input type="text" id="buscadorGeneral" class="form-control border border-subtle"
                                placeholder="Folio o Proveedor">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="glass-card p-4">
                <!-- Contenedor con scroll vertical (max-height) y horizontal -->
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                    <table class="table align-middle w-100 mb-0" id="solicitudesTabla">
                        <!-- sticky-top mantiene la cabecera fija al hacer scroll -->
                        <thead class="table-light sticky-top" style="top: 0; z-index: 10; background-color: #ffffff;">
                            <tr>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>Almacén</th>
                                <th class="text-center align-middle">Costo</th>
                                <th class="text-center align-middle">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaSolicitudes">
                            <!-- Los registros se cargan dinámicamente aquí -->
                        </tbody>
                    </table>
                </div>

                <!-- Tarjetas de resumen de totales -->
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 bg-danger-subtle">
                            <div class="card-body py-2">
                                <small class="text-danger fw-bold text-uppercase"
                                    style="font-size:0.65rem;">Cancelados</small>
                                <h5 class="fw-bold text-danger mb-0">$<span id="totalGastoCancelados">0.00</span></h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 bg-warning-subtle">
                            <div class="card-body py-2">
                                <small class="text-warning fw-bold text-uppercase"
                                    style="font-size:0.65rem;">Pendientes</small>
                                <h5 class="fw-bold text-warning mb-0">$<span id="totalGastoPendientes">0.00</span></h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3 bg-success-subtle">
                            <div class="card-body py-2">
                                <small class="text-success fw-bold text-uppercase"
                                    style="font-size:0.65rem;">Aceptados</small>
                                <h5 class="fw-bold text-success mb-0">$<span id="totalGastoAceptados">0.00</span></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- ══════════════════════════════════════════════════════════
         SCRIPTS EXTERNOS
    ══════════════════════════════════════════════════════════ -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ══════════════════════════════════════════════════════════
         MODALES (cada uno contiene su propio HTML + JS)
    ══════════════════════════════════════════════════════════ -->
    <?php require_once __DIR__ . '/solicitudesCompra/enviarSolicituPorCorreoModel.php'; ?>
    <?php require_once __DIR__ . '/solicitudesCompra/editarSolicitud.php'; ?>
    <?php require_once __DIR__ . '/solicitudesCompra/ModalSolicitud.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/agregarPoductoModal.php'; ?>
    <?php require_once __DIR__ . '/solicitudesCompra/modalProveedoresCompra.php'; ?>
    <?php require_once __DIR__ . '/solicitudesCompra/modalAjuste.php'; ?>
    <?php require_once __DIR__ . '/solicitudesCompra/imprimirModal.php'; ?>
    <?php require_once __DIR__ . '/solicitudesCompra/gestionarSolicitudModal.php'; ?>

    <!-- ══════════════════════════════════════════════════════════
         UTILIDADES GENERALES
    ══════════════════════════════════════════════════════════ -->
    <script>
        // Mayúsculas automáticas en inputs de texto y textareas
        document.querySelectorAll('input[type="text"], textarea').forEach(el => {
            el.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });
    </script>

    <!-- ══════════════════════════════════════════════════════════
         LISTADO DE SOLICITUDES
    ══════════════════════════════════════════════════════════ -->
    <script>
        const URL_CONTROLADOR_SOLICITUD = '/myvet/app/controllers/solicitudesCompraController.php';

        $(document).ready(() => {
            cargarSolicitudes();
            $('#buscadorGeneral').on('keyup', cargarSolicitudes);
            $('#filtroAlmacen').on('change', cargarSolicitudes);
            $('#filtroEstado').on('change', cargarSolicitudes);
            $('#fechaInicio').on('change', cargarSolicitudes);
            $('#fechaFin').on('change', cargarSolicitudes);
        });

        /**
         * Consulta las solicitudes al controlador y dibuja las filas.
         */
        async function cargarSolicitudes() {
            try {
                // ── 1. Leer filtros una sola vez ──
                const almacen = $('#filtroAlmacen').val() || '';
                const fechaInicio = $('#fechaInicio').val() || '';
                const fechaFin = $('#fechaFin').val() || '';
                const estado = $('#filtroEstado').val() || '';
                const buscador = $('#buscadorGeneral').val() || '';

                console.log("Almacén filtrado:", almacen);

                // ── 2. Parámetros de la petición ──
                const params = new URLSearchParams({
                    action: 'listarSolicitudes',
                    almacen,
                    fechaInicio,
                    fechaFin,
                    estado,
                    buscador
                });

                // ── 3. Acumuladores de gastos por estado ──
                let totalAceptados = 0;   // RECIBIDA
                let totalPendientes = 0;  // PENDIENTE
                let totalProcesados = 0;  // PROCESADA
                let totalCancelados = 0;  // CANCELADA / CANCELADO
                let gastoTotal = 0;       // Suma global

                // ── 4. Petición al backend ──
                const res = await fetch(
                    `/myvet/app/controllers/solicitudesCompraController.php?${params.toString()}`
                );
                const data = await res.json();

                console.log("Datos recibidos del servidor:", data);

                // ── 5. Validar respuesta del backend ──
                if (!data || data.success === false) {
                    console.error('Backend error:', data?.message);
                    $('#tablaSolicitudes').html(
                        `<tr><td colspan="7" class="text-center text-danger py-4">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    ${escapeHtml(data?.message || 'Error al cargar solicitudes')}
                </td></tr>`
                    );
                    actualizarResumenGastos(0, 0, 0, 0);
                    return;
                }

                if (!Array.isArray(data.data) || data.data.length === 0) {
                    $('#tablaSolicitudes').html(
                        `<tr><td colspan="7" class="text-center text-secondary py-4">
                    <i class="bi bi-inbox me-1"></i> No se encontraron registros
                </td></tr>`
                    );
                    actualizarResumenGastos(0, 0, 0, 0);
                    return;
                }

                // ── 6. Construir filas ──
                let tablaHTML = '';
                data.data.forEach(s => {
                    // 6.1 Folio
                    const folio = String(s.folio).padStart(5, '0');

                    // 6.2 Fecha formateada
                    let fechaFormateada = s.fecha_creacion || '';
                    if (s.fecha_creacion) {
                        const date = new Date(String(s.fecha_creacion).replace(/\s/, 'T'));
                        if (!isNaN(date.getTime())) {
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            const hours = String(date.getHours()).padStart(2, '0');
                            const minutes = String(date.getMinutes()).padStart(2, '0');
                            fechaFormateada = `${day}/${month}/${year} ${hours}:${minutes}`;
                        }
                    }

                    // 6.3 Estado y faltantes
                    const tieneFaltantes = Number(s.faltantes) === 1 ? 1 : 0;
                    const status = String(s.estado || 'PENDIENTE').toUpperCase();
                    const textoFaltante = tieneFaltantes === 1 ? ' · FALTANTES' : '';

                    let claseEstado = 'bg-secondary text-white';
                    switch (status) {
                        case 'PENDIENTE':
                            claseEstado = 'bg-warning text-dark';
                            break;
                        case 'PROCESADA':
                            claseEstado = 'bg-info text-dark';
                            break;
                        case 'RECIBIDO':
                        case 'RECIBIDA':
                            claseEstado = tieneFaltantes === 1 ? 'bg-info text-dark' : 'bg-success text-white';
                            break;
                        case 'CANCELADA':
                        case 'CANCELADO':
                            claseEstado = 'bg-danger text-white';
                            break;
                    }

                    // 6.4 Acumular costo según estado
                    const costo = parseFloat(s.costo_total || 0);
                    gastoTotal += costo;

                    switch (status) {
                        case 'RECIBIDA':
                        case 'RECIBIDO':
                            totalAceptados += costo;
                            break;
                        case 'PENDIENTE':
                            totalPendientes += costo;
                            break;
                        case 'PROCESADA':
                            totalProcesados += costo;
                            break;
                        case 'CANCELADA':
                        case 'CANCELADO':
                            totalCancelados += costo;
                            break;
                    }

                    // 6.5 Botones de acción (cada uno con estilo e ícono distintivo de Bootstrap)
                    const botonFaltantes = tieneFaltantes === 1 ? `
                <button type="button"
                        class="btn btn-sm btn-warning text-dark fw-semibold shadow-sm"
                        onclick="abrirModalAjuste(${Number(s.compra_id)}, '${escapeHtml(String(s.folio))}')"
                        title="Ajustar faltantes de recepción">
                    <i class="bi bi-box-seam-fill me-1"></i>Faltantes
                </button>
            ` : '';

                    const botonesPendiente = status === 'PENDIENTE' ? `
                <button type="button"
                        class="btn btn-sm btn-warning shadow-sm"
                        onclick="edS_abrirModalEditar(${s.id})"
                        title="Editar solicitud">
                    <i class="bi bi-pencil-square me-1"></i>Editar
                </button>
                <button type="button"
                        class="btn btn-sm btn-outline-success  fw-semibold shadow-sm"
                        onclick="gestionarSolicitud(${s.id})"
                        title="Gestionar solicitud">
                    <i class="bi bi-sliders me-1"></i>Procesar
                </button>
                <button type="button"
                        class="btn btn-sm btn-outline-danger shadow-sm"
                        onclick="eliminarSolicitud(${s.id})"
                        title="Eliminar solicitud">
                    <i class="bi bi-trash3-fill"></i>
                </button>
            ` : '';

                    const botonImprimir = `
                <button type="button"
                        class="btn btn-sm btn-primary shadow-sm"
                        onclick="prepararImpresion(${s.id})"
                        title="Imprimir solicitud">
                    <i class="bi bi-printer-fill me-1"></i>Imprimir
                </button>
            `;

                    // 6.6 Fila organizada
                    tablaHTML += `
                <tr class="align-middle">
                    <td class="fw-semibold text-primary text-nowrap">#${folio}</td>
                    <td class="small text-secondary text-nowrap">${fechaFormateada}</td>
                    <td class="small fw-medium text-uppercase">${escapeHtml(s.proveedor_nombre || 'Sin asignar')}</td>
                    <td class="small text-uppercase">${escapeHtml(s.almacen_nombre || '—')}</td>
                    <td class="text-end fw-semibold text-nowrap">
                        $ ${costo.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                    </td>
                    <td class="text-center text-nowrap">
                        <span class="badge rounded-pill ${claseEstado} px-2 py-1 text-uppercase">
                            ${status}${textoFaltante}
                        </span>
                    </td>
                    <td class="text-end text-nowrap">
                        <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                            ${botonFaltantes}
                            ${botonesPendiente}
                            ${botonImprimir}
                        </div>
                    </td>
                </tr>
            `;
                });

                // ── 7. Pintar tabla ──
                $('#tablaSolicitudes').html(tablaHTML);

                // ── 8. Log + resumen ──
                console.log("💰 RESUMEN DE GASTOS:");
                console.log("   Aceptados :", totalAceptados.toFixed(2));
                console.log("   Pendientes:", totalPendientes.toFixed(2));
                console.log("   Procesados:", totalProcesados.toFixed(2));
                console.log("   Cancelados:", totalCancelados.toFixed(2));
                console.log("   Total     :", gastoTotal.toFixed(2));

                actualizarResumenGastos(totalAceptados, totalPendientes, totalCancelados, gastoTotal);

            } catch (error) {
                console.error("Error crítico capturado en cargarSolicitudes:", error);
                $('#tablaSolicitudes').html(
                    `<tr><td colspan="7" class="text-center text-danger py-4">
                <i class="bi bi-x-octagon-fill me-1"></i>
                Error al cargar solicitudes: ${escapeHtml(error.message || 'desconocido')}
            </td></tr>`
                );
                actualizarResumenGastos(0, 0, 0, 0);
            }
        } function actualizarResumenGastos(aceptados, pendientes, cancelados, total) {
            const fmt = (n) => n.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            // Si tienes elementos con estos IDs en tu HTML, se actualizarán automáticamente.
            // Si no existen, no pasa nada (jQuery ignora .text() en selectores vacíos).
            $('#totalGastoAceptados').text(fmt(aceptados));
            $('#totalGastoPendientes').text(fmt(pendientes));
            $('#totalGastoCancelados').text(fmt(cancelados));
            //  $('#totalGastoGeneral').text(fmt(total));
        }
        /**
         * Función auxiliar de Sanitización (Escape de Entidades HTML)
         * Recibe una cadena de texto y reemplaza caracteres especiales peligrosos (<, >, &, ", ') por texto plano seguro.
         * Protege la aplicación contra ataques Cross-Site Scripting (XSS) en caso de que los nombres contengan código malicioso.
         */
        // FUNCIÓN AUXILIAR REQUERIDA (Evita que el script se rompa)
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Select2 dentro del modal de nueva solicitud
        $('.select2-modal').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalSolicitud')
        });

        function calcularTotal(input) {
            const fila = input.closest('tr');
            const cantidad = parseFloat(fila.querySelector('.cantidad').value) || 0;
            const precioUnitario = parseFloat(fila.querySelector('.precio-unitario').value) || 0;
            const factor = parseFloat(fila.querySelector('.unidad-select').value) || 1;

            fila.querySelector('.precio-total').value = (cantidad * precioUnitario * factor).toFixed(2);
        }

        function quitarFila(id) {
            $(`#fila-${id}`).remove();
            if (!$('#tablaDetalle tbody tr').length) $('#emptyState').removeClass('d-none');
        }

        async function eliminarSolicitud(id) {
            const r = await Swal.fire({
                title: '¿Eliminar?',
                text: 'No podrás revertir esto',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, borrar'
            });

            if (!r.isConfirmed) return;

            const fd = new FormData();
            fd.append('id', id);

            const resp = await fetch(`${URL_CONTROLADOR_SOLICITUD}?action=eliminar`, {
                method: 'POST',
                body: fd
            });
            const res = await resp.json();

            if (res.status === 'success') location.reload();
            else Swal.fire('Error', res.message, 'error');
        }
    </script>

    <!-- ══════════════════════════════════════════════════════════
         LISTA DE PROVEEDORES (para modales)
    ══════════════════════════════════════════════════════════ -->
    <script>
        function actualizarListaProveedores() {
            fetch('egresosController.php?action=getProveedoresJSON')
                .then(res => res.json())
                .then(data => {
                    const $select = $('#select_proveedor');
                    $select.empty().append('<option value="">Seleccione o busque un proveedor...</option>');

                    data.forEach(p => {
                        $select.append(new Option(p.nombre_comercial, p.nombre_comercial));
                    });

                    $select.trigger('change');
                });
        }
    </script>

</body>

</html>