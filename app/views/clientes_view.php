<?php
/**
 * clientes_view.php
 * Vista de administración de clientes: Filtros, CRUD por Modales y AJAX.
 * Lógica de permisos: Admin global vs Usuario de sucursal.
 */
$usosCFDI = ['G01' => 'Adquisición', 'G03' => 'Gastos', 'P01' => 'Por definir', 'S01' => 'Sin efectos'];
$almacen_usuario = intval($_SESSION['almacen_id'] ?? 0); // 0 es Admin
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes | cfsistem</title>
    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <?php require_once __DIR__ . '/layout/icono.php' ?>

    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <style>
        :root {
            --sidebar-width: 0px;
            --navbar-height: 65px;
            --apple-bg: #f5f5f7;
            --accent-blue: #007aff;
        }

        body {

            font-family: 'SF Pro Display', -apple-system, sans-serif;

        }

        .main-content {

            padding: 40px;

        }

        .card-premium {

            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);

            backdrop-filter: blur(10px);
        }

        .badge-ubicacion {
            background-color: #f2f2f7;
            color: #1d1d1f;
            border: 1px solid #d1d1d6;
            padding: 0.4rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
        }

        /* DataTables Custom */
        .dataTables_wrapper .pagination .page-item.active .page-link {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
            border-radius: 8px;
        }

        .table thead th {
            background: #fbfbfd;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #86868b;
            border-bottom: 1px solid #d1d1d6;
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

        <div class="d-flex justify-content-between align-items-center mb-5 animate__animated animate__fadeIn">
            <div>
                <h2 class="fw-bold m-0 card-title-text" style="font-size: 2rem; letter-spacing: -0.03em;">Cartera de
                    Clientes</h2>
                <p class="text-body-secondary mb-0 card-title-text">Gestión de datos fiscales y comerciales</p>
            </div>
            <div class="d-flex justify-content-end align-items-center mb-3">
                <button type="button"
                    class="btn btn-success rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 fw-semibold shadow-sm"
                    onclick="exportarClientesCSV()">
                    <i class="bi bi-file-earmark-excel-fill fs-5"></i> Exportar Clientes (CSV)
                </button>
            </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm d-flex align-items-center"
                onclick="nuevoCliente()"
                style="background: var(--accent-blue); border:none; height: 42px; font-weight: 600;">
                <i class="bi bi-person-plus-fill me-2 fs-5"></i> NUEVO CLIENTE
            </button>
        </div>

        <div class="card card-premium p-4 animate__animated animate__fadeInUp">
            <div class="row mb-4 g-3">
                <div class="col-md-5">
                    <div class="input-group border border-subtle rounded-3 p-1">
                        <span class="input-group-text bg-transparent  text-body-secondary"><i
                                class="bi bi-search"></i></span>
                        <input type="text" id="busquedaCliente" class="form-control  bg-transparent shadow-none"
                            placeholder="Buscar por nombre, RFC o correo...">
                    </div>
                </div>

                <?php if ($almacen_usuario == 0): ?>
                    <div class="col-md-4">
                        <select id="filtroAlmacenVista" class="form-select    border-subtle rounded-3 h-100 shadow-none">
                            <option value="">🌐 Todas las Sucursales</option>
                            <?php foreach ($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>">📍 <?= htmlspecialchars($alm['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary w-100 rounded-3   fw-bold text-body-secondary"
                            onclick="limpiarFiltros()">
                            <i class="bi bi-arrow-clockwise me-1"></i> RESET
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table id="tablaClientes" class="table table-hover align-middle w-100">
                    <thead>
                        <tr>
                            <th class="ps-3">Nombre Comercial / Razón Social</th>
                            <th>Identificación (RFC)</th>
                            <th>Sucursal</th>
                            <th>Estatus</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $c): ?>
                            <tr class="fila-cliente" data-almacen-id="<?= $c['almacen_id'] ?>">
                                <td class="ps-3">
                                    <div class="fw-bold card-title-text"><?= htmlspecialchars($c['nombre_comercial']) ?>
                                    </div>
                                    <?php if (!empty($c['razon_social'])): ?>
                                        <div class="text-body-secondary small" style="font-size: 0.7rem;">
                                            <?= htmlspecialchars($c['razon_social']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-white text-dark border px-2 py-1 fw-bold"
                                        style="font-family: monospace;">
                                        <?= htmlspecialchars($c['rfc']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-ubicacion">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <?php
                                        $nombreAlmacen = 'Global';
                                        foreach ($almacenes as $alm) {
                                            if ($alm['id'] == $c['almacen_id']) {
                                                $nombreAlmacen = $alm['nombre'];
                                                break;
                                            }
                                        }
                                        echo htmlspecialchars($nombreAlmacen);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" <?= $c['activo'] ? 'checked' : '' ?>
                                            onchange="cambiarEstado(<?= $c['id'] ?>, this.checked ? 1 : 0)"
                                            id="switch_<?= $c['id'] ?>">
                                        <label class="form-check-label small fw-medium" for="switch_<?= $c['id'] ?>">
                                            <?= $c['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </label>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm rounded-pill bg-white px-1">
                                        <button class="btn btn-link btn-sm text-primary p-2"
                                            onclick="editarCliente(<?= $c['id'] ?>)">
                                            <i class="bi bi-pencil-square"></i> <i class="bi bi-eye"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content  shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <form id="formCliente">
                    <div class="modal-header bg-dark text-white  py-3">
                        <h5 class="modal-title fw-bold px-2" id="modalTitulo">Nuevo Cliente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="cliente_id" id="cliente_id" value="0">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-body-secondary">NOMBRE COMERCIAL *</label>
                                <input type="text" name="nombre_comercial" id="nombre_comercial"
                                    class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-body-secondary">RAZÓN SOCIAL</label>
                                <input type="text" name="razon_social" id="razon_social" class="form-control rounded-3">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Contacto *</label>
                                <input type="text" name="contacto" id="contacto" class="form-control"
                                    placeholder="Contacto">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-body-secondary">TELEFONO</label>
                                <input type="text" name="telefono" id="telefono" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary">RFC *</label>
                                <input type="text" name="rfc" id="rfc" class="form-control text-uppercase rounded-3"
                                    maxlength="13" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Calle</label>
                                <textarea name="calle" id="calle" class="form-control text-uppercase" rows="2"
                                    placeholder="Calle, número, colonia..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Colonia</label>
                                <textarea name="colonia" id="colonia" class="form-control text-uppercase" rows="2"
                                    placeholder="Colonia"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Pueblo</label>
                                <textarea name="pueblo" id="pueblo" class="form-control text-uppercase" rows="2"
                                    placeholder="Pueblo"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Ciudad</label>
                                <textarea name="ciudad" id="ciudad" class="form-control text-uppercase" rows="2"
                                    placeholder="Calle, número, colonia..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary">CÓDIGO POSTAL</label>
                                <input type="text" name="codigo_postal" id="codigo_postal"
                                    class="form-control text-uppercase rounded-3" maxlength="5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary">USO CFDI</label>
                                <select name="uso_cfdi" id="uso_cfdi" class="form-select rounded-3">
                                    <?php foreach ($usosCFDI as $key => $val): ?>
                                        <option value="<?= $key ?>"><?= $key ?> - <?= $val ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary">CORREO</label>
                                <input type="email" name="correo" id="correo" class="form-control rounded-3">
                            </div>

                            <?php if ($almacen_usuario == 0): ?>
                                <div class="col-md-12"">
                                <div class=" bg-light p-3 rounded-4 mt-2 border border-dashed">
                                    <label class="form-label small fw-bold text-primary">ASIGNAR A SUCURSAL *</label>
                                    <select name="almacen_id" id="almacen_id_modal"
                                        class="form-select border-primary shadow-none" required>
                                        <option value="1">-- Seleccionar Almacén --</option>
                                        <?php foreach ($almacenes as $alm): ?>
                                            <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="almacen_id" value="<?= $almacen_usuario ?>">
                        <?php endif; ?>
                    </div>
            </div>
            <div class="modal-footer bg-light  px-4 pb-4">
                <button type="button" class="btn btn-link text-secondary fw-bold text-decoration-none"
                    data-bs-dismiss="modal">CANCELAR</button>
                <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow">GUARDAR DATOS</button>
            </div>
            </form>
        </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let tabla;

        $(document).ready(function () {
            tabla = $('#tablaClientes').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
                "dom": 'rt<"row mt-3 px-3"<"col-sm-12 col-md-5 small text-body-secondary"i><"col-sm-12 col-md-7"p>>',
                "pageLength": 15,
                "order": [[0, 'asc']],
                "columnDefs": [
                    { "targets": [4], "orderable": false },
                    { "targets": [2], "visible": <?= ($almacen_usuario == 0) ? 'true' : 'false' ?> }
                ]
            });

            $('#busquedaCliente').on('keyup', function () { tabla.search(this.value).draw(); });

            $('#filtroAlmacenVista').on('change', function () {
                const val = $(this).val();
                $.fn.dataTable.ext.search.pop();
                if (val !== "") {
                    $.fn.dataTable.ext.search.push(function (s, d, i) {
                        return $(tabla.row(i).node()).attr('data-almacen-id') == val;
                    });
                }
                tabla.draw();
            });
        });

        function limpiarFiltros() {
            $('#busquedaCliente').val('');
            $('#filtroAlmacenVista').val('');
            $.fn.dataTable.ext.search.pop();
            tabla.search('').draw();
        }

        function nuevoCliente() {
            $('#formCliente')[0].reset();
            $('#cliente_id').val('0');
            $('#modalTitulo').text('Nuevo Registro de Cliente');

            // Auto-seleccionar almacén si hay filtro activo
            const filtro = $('#filtroAlmacenVista').val();
            if (filtro) $('#almacen_id_modal').val(filtro);

            $('#modalCliente').modal('show');
        }

        async function editarCliente(id) {
            try {
                const resp = await fetch(`/myvet/app/controllers/clientesController.php?action=obtenerPorId&id=${id}`);
                const res = await resp.json();
                if (res.success) {
                    const c = res.data;
                    $('#modalTitulo').text('Actualizar Cliente');
                    $('#cliente_id').val(c.id);
                    $('#contacto').val(c.contacto);
                    $('#nombre_comercial').val(c.nombre_comercial);
                    $('#razon_social').val(c.razon_social);
                    $('#rfc').val(c.rfc);
                    $('#telefono').val(c.telefono);
                    const direccion = c.direccion || '';

                    const calle = (direccion.match(/calle\s(.*?)(?=,\scol|$)/i) || [, ''])[1];
                    const colonia = (direccion.match(/col\s(.*?)(?=,\spueblo|$)/i) || [, ''])[1];
                    const pueblo = (direccion.match(/pueblo\s(.*?)(?=,\sciudad|$)/i) || [, ''])[1];
                    const ciudad = (direccion.match(/ciudad\s(.*)$/i) || [, ''])[1];

                    $('#calle').val(calle.trim());
                    $('#colonia').val(colonia.trim());
                    $('#pueblo').val(pueblo.trim());
                    $('#ciudad').val(ciudad.trim());
                    $('#correo').val(c.correo);
                    $('#codigo_postal').val(c.codigo_postal);
                    $('#almacen_id_modal').val(c.almacen_id);
                    $('#modalCliente').modal('show');
                }
            } catch (e) { console.error(e); }
        }

        $('#formCliente').on('submit', async function (e) {
            e.preventDefault();
            try {
                const resp = await fetch('/myvet/app/controllers/clientesController.php?action=guardar', {
                    method: 'POST',
                    body: new FormData(this)
                });
                const res = await resp.json();
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Éxito', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) { console.error(e); }
        });

        async function cambiarEstado(id, estado) {
            const fd = new FormData();
            fd.append('id', id); fd.append('estado', estado);
            fetch('/myvet/app/controllers/clientesController.php?action=cambiarEstado', { method: 'POST', body: fd });
        }

        function verDetalles(id) {
            window.location.href = `/myvet/app/controllers/clientesController.php?action=detalles&id=${id}`;
        }
        async function exportarClientesCSV() {
            // ID del almacén actual para aplicar la misma regla de filtrado
            const almacenId = $('#filtroAlmacenVista').val();

            try {
                const url2 = `/myvet/app/controllers/accesoController.php?action=obtenerClientes&almacen_id=${almacenId}`;
                const respuesta = await fetch(url2);

                if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

                const resultado = await respuesta.json();

                if (!resultado.success || !Array.isArray(resultado.data) || resultado.data.length === 0) {
                    alert("No hay registros de clientes para exportar.");
                    return;
                }

                // Aplicamos la misma regla de filtrado que cargarClientes()
                const clientesFiltrados = resultado.data.filter(cliente => {
                    const nombreNorm = (cliente.nombre_comercial || '').toLowerCase().trim();
                    const esPublicoGeneral = nombreNorm.includes('publico en general') || nombreNorm.includes('público en general');

                    if (esPublicoGeneral) {
                        return cliente.almacen_id == almacenId;
                    }

                    return true;
                });

                if (clientesFiltrados.length === 0) {
                    alert("No se encontraron clientes disponibles para exportar con las condiciones actuales.");
                    return;
                }

                // Encabezados del CSV
                const headers = [
                    "ID Cliente",
                    "Nombre Comercial",
                    "Razón Social",
                    "RFC",
                    "Teléfono",
                    "Email",
                    "Almacén ID"
                ];

                // Sanitización para evitar errores de formato en columnas con comas o comillas
                const escapeCSV = (str) => {
                    if (str === null || str === undefined) return '""';
                    let val = String(str).replace(/"/g, '""');
                    return `"${val}"`;
                };

                // Construir filas sanitizadas
                const rows = clientesFiltrados.map(c => [
                    escapeCSV(c.id),
                    escapeCSV(c.nombre_comercial || 'Sin nombre'),
                    escapeCSV(c.razon_social || c.nombre_comercial || ''),
                    escapeCSV(c.rfc || 'XAXX010101000'),
                    escapeCSV(c.telefono || 'N/A'),
                    escapeCSV(c.email || 'N/A'),
                    escapeCSV(c.almacen_id || '')
                ].join(','));

                // Ensamblar contenido del archivo CSV con BOM de UTF-8
                const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\n');

                // Generar Blob y descargar
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url3 = URL.createObjectURL(blob);
                const link = document.createElement('a');

                const hoy = new Date().toISOString().split('T')[0];
                link.setAttribute('href', url3);
                link.setAttribute('download', `Reporte_Clientes_${hoy}.csv`);
                document.body.appendChild(link);

                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url3);

            } catch (error) {
                console.error('Error al exportar clientes a CSV:', error);
                alert('Ocurrió un error al procesar la exportación de clientes.');
            }
        }
    </script>
</body>

</html>
<script>
    // Selecciona todos los inputs de texto y también los textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function () {
            // Convierte el valor a mayúsculas en tiempo real
            this.value = this.value.toUpperCase();
        });
    });

</script>