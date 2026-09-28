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
    <title>Solicitudes de Compra | cfsistem</title>
    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>
    <link href="/myvet/css/solicitudesCompra.css" rel="stylesheet" />

</head>

<body>
    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content">

        <div class="glass-card p-4 mb-4">

            <div class="row align-items-center mb-5">
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

            <div class="glass-card p-4 mb-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-funnel-fill text-primary me-2"></i>
                            Filtros de búsqueda
                        </h5>
                        <small class="text-body-secondary">
                            Filtra las cotizaciones por fecha, almacén, estado o cliente.
                        </small>
                    </div>
                </div>

                <div class="row g-3 align-items-end">

                    <!-- Fecha Inicio -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">
                            Inicio
                        </label>

                        <input type="date" id="fechaInicio" value="<?= date('Y-m-01') ?>"
                            class="form-control  border border-subtle shadow-sm" style="border-radius:12px;">
                    </div>

                    <!-- Fecha Fin -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">
                            Fin
                        </label>

                        <input type="date" id="fechaFin" value="<?= date('Y-m-d') ?>"
                            class="form-control  border border-subtle shadow-sm" style="border-radius:12px;">
                    </div>

                    <!-- Almacén -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">
                            Almacén
                        </label>

                        <select id="filtroAlmacen" class="form-select  border border-subtle shadow-sm"
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

                    <!-- Estado -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">
                            Estado
                        </label>



                        <select id="filtroEstado" class="form-select border-light shadow-sm">
                            <option value="">Todos los estados</option>
                            <option value="PENDIENTE">Pendiente</option>

                            <option value="RECIBIDO">Recibida</option>
                            <option value="CANCELADO">CANCELADA</option>
                        </select>

                    </div>

                    <!-- Buscador -->
                    <div class="col-lg-3 col-md-12">
                        <label class="form-label small fw-bold text-body-secondary text-uppercase">
                            Buscar
                        </label>

                        <div class="input-group shadow-sm">

                            <span class="input-group-text border border-subtle ">
                                <i class="bi bi-search text-secondary"></i>
                            </span>

                            <input type="text" id="buscadorGeneral" class="form-control  border border-subtle"
                                placeholder="Folio o Proveedor">

                        </div>
                    </div>

                </div>

            </div>

            <div class="glass-card p-4">
                <div class="table-responsive">
                    <table class="table align-middle w-100">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>Almacén</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaSolicitudes">
                        </tbody>
                    </table>
                </div>
            </div>

    </main>


    <div class="modal fade" id="modalGestionSolicitud" tabindex="-1" data-bs-backdrop="static">
        <!-- Ajustado max-width y width al 95% de la pantalla -->
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
            style="max-width: 95vw; width: 95vw; height: 92vh;">
            <div class="modal-content border-0 shadow-lg h-100" style="border-radius: 16px; overflow:hidden;">

                <!-- El <form> envuelve TODO (Header, Body y Footer) para que funcione como contenedor flexible -->
                <form id="formConvertirCompra" enctype="multipart/form-data" class="d-flex flex-column h-100 m-0">

                    <!-- HEADER -->
                    <div class="modal-header bg-success text-white px-4 py-3 border-0 flex-shrink-0">
                        <div>
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-box-arrow-in-down me-2"></i>
                                Convertir Solicitud <span name="uni-folio" id="uni-folio"></span>
                            </h5>
                            <small class="opacity-75">Generación de compra e inventario</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <input type="hidden" name="solicitud_id" id="uni-solicitud-id">

                    <!-- MODAL BODY: flex-grow-1 y overflow-y-auto obligatorios para el scroll interno -->
                    <div class="modal-body border border-subtle px-4 py-4 flex-grow-1 overflow-y-auto">

                        <!-- ====================================== -->
                        <!-- CARD PRINCIPAL -->
                        <!-- ====================================== -->
                        <div class="rounded-4 shadow-sm p-4 mb-4 border">
                            <!-- HEADER DE LA CARD -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h5 class="fw-bold mb-1 text-dark">
                                        <i class="bi bi-cart-check me-2 text-success"></i>
                                        Información de Compra
                                    </h5>
                                    <small class="text-body-secondary">Datos generales de la entrada</small>
                                </div>
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                                    Compra en proceso
                                </span>
                            </div>

                            <!-- FILA 1 -->
                            <div class="row g-3">
                                <!-- ALMACÉN -->
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-body-secondary text-uppercase">Almacén
                                        destino</label>
                                    <?php if (isset($es_admin) && $es_admin): ?>
                                        <select id="almacen_id2" name="almacen_id2" class="form-select rounded-3 shadow-sm"
                                            required>
                                            <option value="">-- Seleccionar --</option>
                                            <?php foreach ($almacenes as $alm): ?>
                                                <option value="<?= $alm['id'] ?>">
                                                    <?= htmlspecialchars($alm['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text"
                                            class="form-control rounded-3 shadow-sm border border-subtle fw-bold"
                                            value="<?= htmlspecialchars($almacenes[0]['nombre'] ?? 'Almacén Asignado') ?>"
                                            readonly>
                                        <input type="hidden" id="almacen_id2" name="almacen_id2"
                                            value="<?= $almacen_usuario ?? ($almacenes[0]['id'] ?? '') ?>">
                                    <?php endif; ?>
                                </div>

                                <!-- PROVEEDOR -->
                                <div class="col-md-3">
                                    <label
                                        class="form-label small fw-bold text-body-secondary text-uppercase">Proveedor</label>
                                    <input type="text" id="uni-proveedor"
                                        class="form-control rounded-3 shadow-sm border border-subtle fw-bold" readonly>
                                    <input type="hidden" name="proveedor" id="uni-proveedor-nombre">
                                </div>

                                <!-- FOLIO -->
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold text-body-secondary text-uppercase">Folio
                                        factura</label>
                                    <input type="text" name="folio" class="form-control rounded-3 shadow-sm"
                                        placeholder="FAC-000" required>
                                </div>

                                <!-- MÉTODO -->
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold text-body-secondary text-uppercase">Método
                                        pago</label>
                                    <select name="metodo_pago" id="metodo_pago" class="form-select rounded-3 shadow-sm"
                                        required>
                                        <option value="">Seleccione...</option>
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia">Transferencia</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                    </select>
                                </div>

                                <!-- EVIDENCIA -->
                                <div class="col-md-2">
                                    <label
                                        class="form-label small fw-bold text-body-secondary text-uppercase">Evidencia</label>
                                    <input type="file" name="evidencia_compra" class="form-control rounded-3 shadow-sm"
                                        accept="image/*,.pdf">
                                </div>
                            </div>

                            <!-- FILA 2 -->
                            <div class="row g-3 mt-2 align-items-stretch">
                                <!-- DEUDA -->
                                <div class="col-md-4">
                                    <div class="bg-danger-subtle border border-danger-subtle rounded-4 p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-danger fw-semibold d-block mb-1">Deuda actual
                                                    proveedor</small>
                                                <input type="text" name="deudaProveedor" id="uni-proveedor-deuda"
                                                    class="form-control border-0 bg-transparent text-danger fw-bold fs-4 p-0"
                                                    readonly>
                                            </div>
                                            <i class="bi bi-exclamation-triangle-fill text-danger fs-2"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- ABONO -->
                                <div class="col-md-3">
                                    <div class="bg-primary-subtle border border-primary-subtle rounded-4 p-3 h-100">
                                        <label class="form-label small text-primary fw-bold mb-2">
                                            <i class="bi bi-cash-coin me-1"></i> Abono a deuda
                                        </label>
                                        <input type="number" id="input_pagar_deuda" name="saldo_a_pagar"
                                            class="form-control border-primary shadow-sm rounded-3" value="0" min="0"
                                            step="0.1">
                                        <small class="label-abono-info text-body-secondary mt-2 d-block"></small>
                                    </div>
                                </div>

                                <!-- TOTAL -->
                                <div class="col-md-5">
                                    <div
                                        class="bg-success-subtle border border-success-subtle rounded-4 p-3 h-100 text-end">
                                        <small class="text-success fw-semibold text-uppercase d-block mb-2">Total
                                            compra</small>
                                        <span class="fw-bold text-success" id="uni-gran-total" style="font-size:2rem;">$
                                            0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ====================================== -->
                        <!-- TABLA -->
                        <!-- ====================================== -->
                        <div class="rounded-4 shadow-sm border overflow-hidden">
                            <div class="p-3 border-bottom border-subtle">
                                <h6 class="fw-bold mb-1">
                                    <i class="bi bi-box-seam me-2 text-success"></i> Productos
                                </h6>
                                <small class="text-body-secondary">Conversión de unidades y costos</small>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0" id="tablaConversion">
                                    <thead class="table-light">
                                        <tr class="small text-body-secondary">
                                            <th class="ps-4">Producto</th>
                                            <th>Cantidad Solicitada</th>
                                            <th>Llegado</th>
                                            <th>Costo</th>
                                            <th class="text-end pe-4">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <!-- FOOTER: flex-shrink-0 para evitar que se comprima o desaparezca -->
                    <div class="modal-footer border-top border-subtle px-4 py-3 flex-shrink-0 bg-white">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm fw-semibold">
                            <i class="bi bi-check2-circle me-2"></i> Confirmar compra
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalImprimirSolicitud" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content  shadow-lg" style="border-radius: 16px; overflow: hidden;">

                <!-- HEADER -->
                <div class="modal-header text-white " style="background:  #060606 100%;">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-printer me-2"></i>Vista de Impresión
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">

                    <div id="areaImpresion" class="p-3 " style="min-height:650px;font-size:12px;color:#1f2937;">

                        <!-- ENCABEZADO -->
                        <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-3">

                            <div>

                                <div class="row g-1">
                                    <!-- -->
                                    <div class="col-6">
                                        <div class="fw-bold text-uppercase mb-0" style="letter-spacing:.5px;">
                                            <?= $_SESSION['nombre_almacen'] ?>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold text-uppercase text-end mb-0" style="letter-spacing:.5px;">
                                            Solicitud de Compra <spam class="fw-bold text-uppercase mb-0"
                                                id="print-folio">

                                            </spam>
                                        </div>

                                    </div>
                                    <div class="col-6">
                                        <div style="letter-spacing:.5px; font-size:11px;">
                                            <spam class="fw-bold text-uppercase mb-0">RFC:</spam> BFC121126CKA
                                        </div>
                                        <div style="letter-spacing:.5px; font-size:11px;">
                                            <spam class="fw-bold text-uppercase mb-0">DOMICILIO:</spam> EMILIANO ZAPATA
                                            S/N SAN MARTIN CUAUTLALPAN CHALCO ESTADO DE MEXICO <spam
                                                class="fw-bold text-uppercase mb-0"> CP:</spam> 56644
                                        </div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <div class="fw-bold text-uppercase mb-0" style="letter-spacing:.5px;">
                                            <div class="">

                                                <spam class="text-body-secondary text end" id="print-fecha"></spam>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>



                        </div>


                        <!-- DATOS PROVEEDOR -->
                        <div class="border rounded p-2 mb-3">


                            <div class="row g-1">
                                <!-- BODEGA FORTALEZA CHALCO S.A DE C,V RFC:BFC121126CKA DOMICILIO EMILIANO ZAPATA S/N SAN MARTIN CUAUTLALPAN CHALCO ESTADO DE MEXICO CP:56644 -->
                                <div class="col-12">
                                    <small style="font-size:11px;" class="text-body-secondary">Proveedor</small><br>
                                    <span style="font-size:11px;" class="fw-bold" id="print-proveedor"></span>
                                </div>

                                <div class="col-6">
                                    <small style="font-size:11px;" class="text-body-secondary">RFC</small><br>
                                    <span style="font-size:11px;" id="print-rfc"></span>
                                </div>

                                <div class="col-6">
                                    <small style="font-size:11px;" class="text-body-secondary">Teléfono</small><br>
                                    <span style="font-size:11px;" id="print-telefono"></span>
                                </div>

                                <div class="col-8">
                                    <small style="font-size:11px;" class="text-body-secondary">Dirección</small><br>
                                    <span style="font-size:11px;" id="print-direccion"></span>
                                </div>

                                <div class="col-2">
                                    <small style="font-size:11px;" class="text-body-secondary">Tel. 2</small><br>
                                    <span style="font-size:11px;" id="print-telefono2"></span>
                                </div>

                                <div class="col-2">
                                    <small style="font-size:11px;" class="text-body-secondary">Ext.</small><br>
                                    <span style="font-size:11px;" id="print-extencion"></span>
                                </div>

                            </div>

                        </div>


                        <!-- TABLA -->
                        <table class="table table-bordered table-sm mb-3" style="font-size:11px;">

                            <thead class="table-dark">

                                <tr>

                                    <th style="width:48%;">Descripción</th>
                                    <th width="14%" class="text-center">Cant.</th>
                                    <th width="19%" class="text-end">P. Unit.</th>
                                    <th width="19%" class="text-end">Importe</th>

                                </tr>

                            </thead>

                            <tbody id="print-tabla-cuerpo"></tbody>

                        </table>


                        <!-- TOTAL -->
                        <div class="d-flex justify-content-end mb-4">

                            <table style="width:220px;font-size:13px;">

                                <tr>

                                    <td class="text-end fw-bold">
                                        Total:
                                    </td>

                                    <td class="text-end fw-bold fs-6" id="costo_total">

                                    </td>

                                </tr>

                            </table>

                        </div>


                        <!-- FIRMAS -->
                        <div class="mt-5">

                            <div class="row text-center">

                                <div class="col-4">

                                    <div style="border-top:1px solid #444;padding-top:6px;">
                                        <small class="text-uppercase fw-semibold">
                                            Solicita
                                        </small>
                                    </div>

                                </div>

                                <div class="col-4">

                                    <div style="border-top:1px solid #444;padding-top:6px;">
                                        <small class="text-uppercase fw-semibold">
                                            Autoriza
                                        </small>
                                    </div>

                                </div>

                                <div class="col-4">

                                    <div style="border-top:1px solid #444;padding-top:6px;">
                                        <small class="text-uppercase fw-semibold">
                                            Recibe
                                        </small>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer border border-subtle ">
                    <button class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Cerrar
                    </button>

                    <button class="btn btn-dark rounded-pill px-4"
                        style="background: linear-gradient(135deg,#1f2a37,#334155); border:0;"
                        onclick="ejecutarImpresion()">
                        Imprimir
                    </button>
                </div>

            </div>
        </div>
    </div>
    <style>
        /* Estilos generales para el modal */
        #modalImprimirSolicitud .modal-content {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        #modalImprimirSolicitud .modal-header {
            background: #060606;
            color: white;

            padding: 1.5rem;
        }

        #modalImprimirSolicitud .modal-body {
            padding: 0;
        }

        #modalImprimirSolicitud .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 1.5rem;
        }

        /* Estilos para el área de impresión */
        #areaImpresion {
            padding: 2rem;
            background-color: white;
            min-height: 600px;
        }

        /* Encabezado del documento */
        #areaImpresion h2 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        #areaImpresion h5 {
            color: #4a6572;
            font-weight: 600;
        }

        /* Tarjetas de información */
        .card {
            border-radius: 10px;

            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        /* Estilos para la tabla */
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background: #1b9707;
            color: white;

            padding: 12px 15px;
            font-weight: 600;
            text-transform: uppercase;

            letter-spacing: 0.5px;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
        }

        /* Líneas de separación */
        .divider {
            height: 2px;
            background: linear-gradient(90deg, rgba(44, 62, 80, 0.8) 0%, rgba(74, 101, 114, 0.8) 100%);
            margin: 1.5rem 0;
        }

        /* Sección de firmas */
        .signature-line {
            width: 150px;
            height: 1px;
            background-color: #2c3e50;
            margin-bottom: 8px;
        }

        .signature-label {
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Estilos para impresión */
        @media print {

            /* Ocultar elementos innecesarios */
            body * {
                visibility: hidden;
            }

            #modalImprimirSolicitud,
            #modalImprimirSolicitud * {
                visibility: visible;
            }

            #modalImprimirSolicitud {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            /* Ajustes para el modal al imprimir */
            .modal {
                position: static !important;
                display: block !important;
                overflow: visible !important;
            }

            .modal-dialog {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                position: static !important;
                transform: none !important;
            }

            .modal-content {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .modal-header,
            .modal-footer {
                display: none !important;
            }

            /* Ajustes para el área de impresión */
            #areaImpresion {
                padding: 1.5rem;
                background-color: white !important;
                color: black !important;
            }

            /* Asegurar colores legibles en impresión */
            #areaImpresion * {
                color: black !important;
                background-color: white !important;
            }

            /* Ajustes para la tabla al imprimir */
            .table {
                box-shadow: none !important;
            }

            .table thead th {
                background-color: #f0f0f0 !important;
                color: black !important;
                border: 1px solid #ddd !important;
            }

            .table tbody tr {
                background-color: white !important;
            }

            .table tbody tr:nth-child(odd) {
                background-color: #f9f9f9 !important;
            }

            /* Ajustes para las tarjetas al imprimir */
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                background-color: white !important;
            }

            /* Optimizar saltos de página */
            .table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            /* Ajustes de fuentes para impresión */
            h2 {
                font-size: 1.5rem !important;
            }

            h5 {
                font-size: 1.1rem !important;
            }

            /* Ajustes para las firmas */
            .signature-line {
                background-color: black !important;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>





    <?php require_once __DIR__ . '/solicitudesCompra/ModalSolicitud.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/agregarPoductoModal.php'; ?>
    <?php require_once __DIR__ . '/solicitudesCompra/modalProveedoresCompra.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalAjuste.php'; ?>
    <script>
        // Selecciona todos los inputs de texto y también los textareas
        document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
            elemento.addEventListener('input', function () {
                // Convierte el valor a mayúsculas en tiempo real
                this.value = this.value.toUpperCase();
            });
        });
    </script>
    <script>
        const URL_CONTROLADOR_SOLICITUD = '/myvet/app/controllers/solicitudesCompraController.php';
        $(document).ready(function () {
            cargarSolicitudes();
        });
        // Se ejecuta automáticamente en cuanto el navegador termina de estructurar el HTML de la página

        /**
         * Función asíncrona que consulta las cotizaciones/solicitudes al controlador PHP
         * mediante Fetch API, procesa las condiciones de negocio y dibuja las filas.
         */
        async function cargarSolicitudes() {
            try {
                // Almacenamos los valores actuales de los filtros de la pantalla para enviarlos al servidor
                let almacen = $('#filtroAlmacen').val();
                let fechaInicio = $('#fechaInicio').val();
                let fechaFin = $('#fechaFin').val();

                // Mensaje de control informativo en la consola para confirmar qué almacén se está procesando
                console.log("Almacén filtrado:", almacen);

                // Agrupación y formateo seguro de todos los parámetros de filtrado que viajarán en la URL
                const params = new URLSearchParams({
                    action: 'listarSolicitudes',                     // Acción que mapea al método del controlador PHP
                    almacen: $('#filtroAlmacen').val() || '',        // ID del almacén seleccionado
                    fechaInicio: $('#fechaInicio').val() || '',      // Rango de fecha inicial
                    fechaFin: $('#fechaFin').val() || '',            // Rango de fecha final
                    estado: $('#filtroEstado').val() || '',          // Estado actual (Pendiente, Procesada, Recibida)
                    buscador: $('#buscadorGeneral').val() || ''      // Texto de búsqueda libre escrito por el usuario
                });

                // Captura del ID de rol de usuario desde las sesiones nativas de PHP
                let rol = <?= isset($_SESSION['rol_id']) ? (int) $_SESSION['rol_id'] : 0 ?>;
                // Variable tipo String que irá acumulando el código HTML de cada fila generada
                let tablaHTML = '';

                // Petición AJAX (Fetch) enviando la ruta del controlador acompañada de los parámetros de búsqueda
                const res = await fetch(
                    `/myvet/app/controllers/solicitudesCompraController.php?${params.toString()}`
                );

                // Transforma la respuesta cruda del servidor en un formato de Objeto JSON legible por JavaScript
                let data = await res.json();
                console.log("Datos recibidos del servidor:", data.data);

                // Validación de seguridad: Comprueba si la respuesta no trae datos o el arreglo viene totalmente vacío
                if (!data.data || data.data.length === 0) {
                    // Inserta una fila única con un mensaje centralizado indicando que no hay registros y frena el script
                    $('#tablaSolicitudes').html('<tr><td colspan="6" class="text-center text-body-secondary py-3">No se encontraron registros</td></tr>');
                    return;
                }

                // Bucle que recorre uno a uno cada objeto "s" (Solicitud) dentro del arreglo de datos
                data.data.forEach(s => {

                    // 1. FORMATEO DEL FOLIO NUMÉRICO
                    // Transforma el ID a texto y rellena con ceros a la izquierda hasta asegurar un tamaño fijo de 5 dígitos
                    // Hace exactamente lo mismo que el método de PHP: str_pad($s['id'], 5, "0", STR_PAD_LEFT)
                    const folio = String(s.id).padStart(5, '0');

                    // 2. PROCESAMIENTO DE FECHA UNIVERSAL
                    let fechaFormateada = s.fecha_creacion || '';
                    if (s.fecha_creacion) {
                        // Reemplaza los espacios por una "T" para forzar la compatibilidad con el estándar ISO.
                        // Esto previene fallos silenciosos de "Invalid Date" en entornos estrictos como Safari de Apple o iOS.
                        const date = new Date(s.fecha_creacion.replace(/\s/, 'T'));

                        // Si la fecha se pudo interpretar de manera completamente correcta
                        if (!isNaN(date.getTime())) {
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0'); // Se suma 1 porque en JS Enero es 0
                            const year = date.getFullYear();
                            const hours = String(date.getHours()).padStart(2, '0');
                            const minutes = String(date.getMinutes()).padStart(2, '0');

                            // Une los fragmentos en el formato final legible para el usuario: dd/mm/aaaa hh:mm
                            fechaFormateada = `${day}/${month}/${year} ${hours}:${minutes}`;
                        }
                    }

                    // 3. CONTROL DE ESTADOS Y COLORES (Insignias / Badges)
                    // Convierte a mayúsculas el estado del registro. Si es nulo, asigna 'PENDIENTE' de manera preventiva
                    const status = (s.estado || 'PENDIENTE').toUpperCase();
                    let claseEstado = 'bg-secondary text-white'; // Estilo gris predeterminado por si ocurre un estado desconocido

                    // Estructura de control Switch que emula el bloque 'match' original de tu código PHP
                    switch (status) {
                        case 'PENDIENTE':
                            claseEstado = 'bg-warning text-dark';   // Insignia Amarilla
                            break;
                        case 'PROCESADA':
                            claseEstado = 'bg-primary text-white';  // Insignia Azul
                            break;
                        case 'RECIBIDA':
                            claseEstado = 'bg-success text-white';  // Insignia Verde
                            break;
                    }

                    // 4. GENERACIÓN DE ACCIONES EXCLUSIVAS
                    // Variable temporal para guardar botones que solo deben aparecer bajo condiciones estrictas
                    let botonesAccion = '';
                    if (status === 'PENDIENTE') {
                        // Si la solicitud está PENDIENTE, concatena los botones de Gestionar y Eliminar inyectando sus ID reales
                        botonesAccion = `
                    <button class="btn btn-sm btn-white border shadow-sm" onclick="gestionarSolicitud(${s.id})">
                        <i class="bi bi-eye text-primary"></i> Gestionar
                    </button>
                    <button class="btn btn-sm btn-white border shadow-sm" onclick="eliminarSolicitud(${s.id})">
                        <i class="bi bi-trash text-danger"></i>
                    </button>
                `;
                    }

                    // 5. ARMADO E INYECCIÓN DE LA FILA (HTML Template Literal)
                    // Se va acumulando dinámicamente la estructura completa de la fila actual dentro de 'tablaHTML'
                    tablaHTML += `
                <tr>
                    <td><span class=" fw-bold">#${s.folio}</span></td>
                    <td class="text-body-secondary small">${fechaFormateada}</td>
                    <td class="fw-medium">${escapeHtml(s.proveedor_nombre || 'Sin asignar')}</td>
                    <td><span class="badge bg-light text-dark border">${escapeHtml(s.almacen_nombre || '')}</span></td>
                    <td>
                        <span class="badge badge-status ${claseEstado} rounded-pill">${status}</span>
                    </td>
                    <td class="text-end">
                        ${botonesAccion}
                        <button class="btn btn-sm btn-white border shadow-sm rounded-pill px-3"
                            onclick="prepararImpresion(${s.id})" title="Imprimir solicitud">
                            <i class="bi bi-printer text-primary me-1"></i>
                            <span class="fw-medium">Imprimir</span>
                        </button>
                    </td>
                </tr>
            `;
                });

                // 6. ACTUALIZACIÓN DIRECTA DEL DOM DE LA TABLA
                // CORRECCIÓN: Al tener el id="tablaSolicitudes" en el <tbody>, inyectamos directamente con $('#tablaSolicitudes')
                $('#tablaSolicitudes').html(tablaHTML);

            } catch (error) {
                // Atrapa cualquier error de red, fallos del servidor o inconsistencias de código y lo imprime de forma limpia
                console.error("Error crítico capturado en cargarSolicitudes:", error);
            }
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
        $(document).ready(function () {


            $('#buscadorGeneral').on('keyup', cargarSolicitudes);
            $('#filtroAlmacen').on('change', cargarSolicitudes);
            $('#filtroEstado').on('change', cargarSolicitudes);
            $('#fechaInicio').on('change', cargarSolicitudes);
            $('#fechaFin').on('change', cargarSolicitudes);


        });
        $('.select2-modal').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalSolicitud')
        });
        function calcularTotal(input) {

            const fila = input.closest('tr');

            const cantidad = parseFloat(
                fila.querySelector('.cantidad').value
            ) || 0;

            const precioUnitario = parseFloat(
                fila.querySelector('.precio-unitario').value
            ) || 0;

            const factor = parseFloat(
                fila.querySelector('.unidad-select').value
            ) || 1;

            // 🔥 TOTAL
            const total = cantidad * precioUnitario * factor;

            fila.querySelector('.precio-total').value =
                total.toFixed(2);
        }



        // ENVÍO DEL FORMULARIO DE CONVERSIÓN

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
            if (r.isConfirmed) {
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
        }
    </script>
    <script>

        function asignarSiguienteFolioCompra() {
            const inputFolio = document.getElementsByName('folio')[0];
            let almacen = 1;
            console.log(almacen);

            if (!inputFolio) return;
            fetch(`${URL_CONTROLADOR_SOLICITUD}?action=getSiguienteFolio&almacen_id=${almacen}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) inputFolio.value = data.folio;
                })
                .catch(err => console.error("Error al obtener folio:", err));
        }
        let solicitudIDI = 0;
        async function gestionarSolicitud(id) {
            try {
                $('#tablaConversion tbody').empty();
                console.log(id); solicitudIDI = id;

                const resp = await fetch(`${URL_CONTROLADOR_SOLICITUD}?action=obtenerDetalle&id=${id}`);
                asignarSiguienteFolioCompra();

                if (!resp.ok) throw new Error(`Error de servidor: ${resp.status}`);

                const res = await resp.json();

                if (res.status !== 'success') {
                    throw new Error(res.message || 'Error al obtener datos');
                }

                console.log(res);

                const items = res.data || [];

                if (items.length === 0) {
                    Swal.fire('Info', 'La solicitud no tiene productos.', 'info');
                    return;
                }

                // 🔥 MANEJO SEGURO DE DEUDA (UNA SOLA VEZ)
                const deuda = Number(res?.deuda?.[0]?.pendiente) || 0;

                // 🔹 Datos generales
                $('#uni-solicitud-id').val(id);
                $('#uni-folio').text(`#${id.toString().padStart(5, '0')}`);
                $('#uni-proveedor').val(items[0].proveedor_nombre || 'Sin Proveedor');
                $('#uni-proveedor-nombre').val(items[0].proveedor_nombre || '');

                // 🔹 Deuda segura
                $('#uni-proveedor-deuda').val(deuda);
                $('#input_pagar_deuda').val(0);
                $('#input_pagar_deuda').attr('max', deuda);
                $('.label-abono-info').text(`Máximo: ${deuda}`);



                // 🔥 DESACTIVAR SI NO HAY DEUDA
                if (deuda <= 0) {

                    $('#input_pagar_deuda')
                        .prop('disabled', true)


                } else {

                    $('#input_pagar_deuda')
                        .prop('disabled', false)

                }
                const almacenInput = document.getElementById('almacen_id2');

                const almacen_id2 = almacenInput ? almacenInput.value : 0;

                console.log('ALMACEN:', almacen_id2);
                let html = '';

                items.forEach((i, index) => {


                    const factor = parseFloat(i.factor_conversion) || 1;
                    const uBase = i.unidad_medida || 'pzas';
                    const uRep = i.unidad_reporte || 'Mayoreo';
                    const costo = i.costo;
                    const cantidadSolicitada = parseFloat(i.cantidad) || 0;
                    const cantidad = parseFloat(i.cantidad);

                    const cantMayoreo = Math.floor(cantidad); // 1
                    const cantSueltas = ((cantidad - cantMayoreo) * factor);    // 0.5


                    const totalUnidad = cantidadSolicitada / factor;

                    html += `
            <tr class="fila-item" data-index="${index}">
                <td>
                    <input type="hidden" name="items[${index}][producto_id]" value="${i.producto_id}">
                    <input type="hidden" class="h-factor" value="${factor}">
                    <div class="fw-bold text-dark">${i.producto_nombre} </div>
                     <small class="text-body-secondary d-block">1 ${uRep} = ${factor} ${uBase}</small>
                </td>

                <td>
                 <div class="fw-bold ">
                    ${cantMayoreo}.${cantSueltas} ${uRep}
                </div>
                    <input type="hidden" class="form-control form-control-sm i-mayoreo border-success" 
                        value="${cantMayoreo}" step=".01" oninput="recalcularFila(${index})" readonly>
               
                    <input type="hidden" class="form-control form-control-sm i-sueltas border-primary" 
                        value="${cantSueltas}" step="0.01" oninput="recalcularFila(${index})" readonly>
                </td>

                
                    
          <td>
    <label class="form-label small text-success fw-semibold mb-1">Llegado</label>

    <input type="number"
        id="llegado_${index}"
        class="form-control form-control-sm border-success shadow-sm i-llegado"
        name="items[${index}][cantidad_llegado]"
        value="${cantidadSolicitada}"
        step=".01"
        min="0"
        oninput="recalcularFila(${index})">

    <input type="hidden"
        id="faltante_${index}"
        class="form-control form-control-sm border-danger shadow-sm i-faltante"
        name="items[${index}][cantidad_faltante]"
        value="0"
        step=".01"
        min="0"
        >



    <input type="hidden"
        name="items[${index}][cantidad_excedente]"
        class="form-control form-control-sm border-success shadow-sm i-excedente"
        value="0"
        min="0"
        step="0.01"
        >
</td>

                <td>
                    <label class="small text-body-secondary fw-bold">Costo Total Renglón</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text ">$</span>
                        <input type="number" step="0.01" class="form-control i-costo-total" 
                            placeholder="0.00" required value="${costo}"
                            oninput="recalcularFila(${index})">
                    </div>

                    <input type="hidden" class="h-precio-lote">

                    <div class="mt-1" style="font-size:0.75rem">
                        Cost. Unit: 
                        <span class="s-precio-lote fw-bold text-secondary">$ 0.00</span>
                    </div>
                </td>

               
    
    <input 
        type="hidden"
        value="${almacen_id2}"
        class="form-control i-almacen-id"
    >
</td>       
                </td>

                <td class="text-end bg-light-subtle">
                    <div class="h5 mb-0 fw-bold text-primary s-total-piezas">0</div>
                    
                    <small class="text-body-secondary umedida" >${cantMayoreo >= 1 ? uRep : uBase}</small>
                    <div>                    <small class=" mb-0 text-danger s-faltantes-piezas"></small>
                    <small class=" mb-0  text-success s-exedentes-piezas"></small>
                    <small class=" mb-0  text-dark s-unidad-piezas"></small>
                    <input type="hidden" class="h-total-piezas">
                    </div>
                </td>
            </tr>`;
                });

                $('#tablaConversion tbody').html(html);

                $('.fila-item').each(function (idx) {
                    recalcularFila(idx);
                });

                $('#modalGestionSolicitud').removeAttr('aria-hidden').modal('show');

            } catch (e) {
                console.error(e);
                Swal.fire('Error', e.message, 'error');
            }
        }
        function recalcularFila(index) {

            const fila = $(`.fila-item[data-index="${index}"]`);

            const factor = parseFloat(fila.find('.h-factor').val()) || 1;
            const mayoreo = parseFloat(fila.find('.i-mayoreo').val()) || 0;
            const sueltas = parseFloat(fila.find('.i-sueltas').val()) || 0;
            const llegado = parseFloat((fila.find('.i-llegado').val()) || 0) * factor;
            const unidadTexto = fila.find('.umedida').text().trim();

            let faltante = (parseFloat(fila.find('.i-faltante').val()) || 0) * factor;
            let excedente = (parseFloat(fila.find('.i-excedente').val()) || 0) * factor;

            const costoTotalRenglon = parseFloat(fila.find('.i-costo-total').val()) || 0;

            const totalBase = (mayoreo * factor) + sueltas;

            if (llegado > totalBase) {
                let total = llegado - totalBase;
                fila.find('.i-excedente').val(total / factor);
                fila.find('.i-faltante').val(0);
                fila.find('.s-faltantes-piezas').text('');
                fila.find('.s-exedentes-piezas').text(total / factor);
                fila.find('.s-unidad-piezas').text(unidadTexto);
                excedente = total;
                faltante = 0;
            }

            if (llegado < totalBase) {
                let total = totalBase - llegado;
                fila.find('.i-faltante').val(total / factor);
                fila.find('.i-excedente').val(0);
                fila.find('.s-exedentes-piezas').text();

                fila.find('.s-faltantes-piezas').text(total / factor);
                fila.find('.s-unidad-piezas').text(unidadTexto);

                faltante = total;
                excedente = 0;
            }
            if (llegado == totalBase) {

                fila.find('.s-faltantes-piezas').text('');
                fila.find('.s-exedentes-piezas').text('');
                fila.find('.i-faltante').val(0);
                fila.find('.i-excedente').val(0);
                faltante = 0;
                excedente = 0;

            }

            const totalPiezasFinal = totalBase - faltante + excedente;
            mayoreo >= 1 ?

                fila.find('.s-total-piezas').text(
                    Number.isInteger(totalPiezasFinal / factor)
                        ? totalPiezasFinal / factor
                        : (totalPiezasFinal / factor).toFixed(2)
                ) :
                fila.find('.s-total-piezas').text(
                    Number.isInteger(totalPiezasFinal)
                        ? totalPiezasFinal
                        : (totalPiezasFinal).toFixed(2)
                );


            fila.find('.h-total-piezas').val(totalPiezasFinal);
            console.log(totalPiezasFinal);

            let precioUnitario = totalBase > 0
                ? costoTotalRenglon / totalBase
                : 0;

            fila.find('.h-precio-lote').val(precioUnitario.toFixed(4));

            fila.find('.s-precio-lote').text(
                '$ ' + precioUnitario.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 4
                })
            );

            actualizarGranTotal();
        }
        function actualizarGranTotal() {
            let granTotal = 0;
            $('.i-costo-total').each(function () {
                granTotal += parseFloat($(this).val()) || 0;
            });

            $('#uni-gran-total').text('$ ' + granTotal.toLocaleString(undefined, {
                minimumFractionDigits: 2
            }));
        }
    </script>
    <script>
        $(document).ready(function () {
            // Usamos .off() para evitar registros duplicados
            $('#formConvertirCompra').off('submit').on('submit', function (e) {

                e.preventDefault();

                const detalle = [];

                // 🔥 RECORRER TODAS LAS FILAS
                $('.fila-item').each(function () {

                    const fila = $(this);
                    const index = fila.data('index');

                    const almId = $('#almacen_id2').val();
                    const cantTotal = parseFloat(
                        fila.find('.h-total-piezas').val()
                    ) || 0;
                    console.log(calcularTotal);
                    const costoTotal = parseFloat(
                        fila.find('.i-costo-total').val()
                    ) || 0;

                    const excedente = parseFloat(
                        fila.find('.i-excedente').val()
                    ) || 0;

                    const faltante = parseFloat(
                        fila.find('.i-faltante').val()
                    ) || 0;

                    const productoId = fila.find(
                        `input[name="items[${index}][producto_id]"]`
                    ).val();

                    // 🔥 VALIDAR PRODUCTO
                    if (!productoId) {
                        console.warn('Producto inválido en fila:', index);
                        return;
                    }
                    const factor = parseFloat(fila.find('.h-factor').val()) || 1;
                    detalle.push({

                        producto_id: productoId,

                        input_mayoreo: parseFloat(
                            fila.find('.i-mayoreo').val()
                        ) || 0,

                        input_sueltas: parseFloat(
                            fila.find('.i-sueltas').val()
                        ) || 0,

                        cantidad_excedente: (excedente * factor),

                        cantidad_faltante: (faltante * factor),

                        total_item: costoTotal,

                        precio_lote: parseFloat(
                            fila.find('.h-precio-lote').val()
                        ) || 0,

                        hidden_factor: parseFloat(
                            fila.find('.h-factor').val()
                        ) || 1,

                        almacenes: {
                            [almId]: {
                                activo: 'on',
                                cantidad: cantTotal
                            }
                        }

                    });
                    console.log(detalle);

                });

                console.log('DETALLE A ENVIAR:', detalle);

                // 🔥 VALIDACIÓN GENERAL
                if (detalle.length === 0) {

                    Swal.fire(
                        'Atención',
                        'No hay productos para guardar.',
                        'warning'
                    );

                    return;
                }

                // 🔥 VALIDAR COSTOS
                const costoInvalido = detalle.some(item =>
                    parseFloat(item.total_item) <= 0
                );

                if (costoInvalido) {

                    Swal.fire(
                        'Atención',
                        'Todos los productos deben tener un costo mayor a 0.',
                        'warning'
                    );

                    return;
                }

                // 🔥 FORM DATA
                const formData = new FormData(this);

                formData.append(
                    'action',
                    'guardarCompraCompleta'
                );

                // 🔥 ENVIAR JSON COMPLETO
                formData.append(
                    'items',
                    JSON.stringify(detalle)
                );

                formData.append(
                    'solicitud_id',
                    $('#uni-solicitud-id').val()
                );

                formData.append(
                    'almacen_id',
                    $('#almacen_id2').val()
                );

                formData.append(
                    'proveedor',
                    $('#uni-proveedor').val()
                );

                Swal.fire({

                    title: '¿Confirmar Ingreso?',

                    text: 'Se registrará la entrada en inventario y se cerrará la solicitud.',

                    icon: 'question',

                    showCancelButton: true,

                    confirmButtonColor: '#198754',

                    confirmButtonText: 'Sí, guardar',

                    cancelButtonText: 'Cancelar'

                }).then((result) => {

                    if (!result.isConfirmed) return;

                    Swal.fire({

                        title: 'Procesando...',

                        html: 'Guardando datos y generando lotes',

                        allowOutsideClick: false,

                        didOpen: () => {
                            Swal.showLoading();
                        }

                    });

                    $.ajax({

                        url: URL_CONTROLADOR_SOLICITUD,

                        type: 'POST',

                        data: formData,

                        processData: false,

                        contentType: false,

                        dataType: 'json',

                        success: function (res) {

                            console.log('RESPUESTA:', res);

                            if (res.success) {

                                Swal.fire(
                                    '¡Éxito!',
                                    res.message,
                                    'success'
                                )

                                    .then(() => {
                                        $('#modalGestionSolicitud').modal('hide');
                                        cargarSolicitudes();

                                    });
                            } else {

                                Swal.fire(
                                    'Error de negocio',
                                    res.message || 'Error desconocido',
                                    'error'
                                );

                            }
                        },

                        error: function (jqXHR, textStatus, errorThrown) {

                            console.error('AJAX ERROR:', textStatus);
                            console.error('ERROR THROWN:', errorThrown);
                            console.error('RESPUESTA:', jqXHR.responseText);

                            Swal.fire({

                                icon: 'error',

                                title: 'Error del Servidor',

                                html: `
                    <div style="
                        text-align:left;
                        font-size:11px;
                        background:#eee;
                        padding:10px;
                        max-height:250px;
                        overflow:auto;
                        border-radius:6px;
                    ">
                        ${jqXHR.responseText || 'Error desconocido (posible 500)'}
                    </div>`,

                                footer: 'Revisa la pestaña Network en F12 para más detalles.'

                            });

                        }

                    });

                });

            });
        });


    </script>

    <script>
        /**
         * Llena el modal de impresión con la data de la solicitud
         */



        function prepararImpresion(id) {
            fetch(`${URL_CONTROLADOR_SOLICITUD}?action=obtenerDetalle&id=${id}`)
                .then(async (res) => {
                    const text = await res.text(); // 👈 primero ver crudo

                    console.log('RAW TEXT RESPONSE:', text);

                    try {
                        return JSON.parse(text); // 👈 intento manual
                    } catch (e) {
                        console.error('ERROR JSON:', e);
                        throw new Error('Respuesta no es JSON válido');
                    }
                })
                .then(res => {

                    console.log('RAW RESPONSE OBJ:', res);
                    console.log('STATUS:', res.status);
                    console.log('DATA:', res.data);
                    console.log('COSTO:', res.costo);

                    if (res.status !== 'success') return;

                    const data = res.data;
                    const infoBase = data[0];
                    const costo = res.costo;

                    $('#print-folio').text(` #${id.toString().padStart(5, '0')}`);
                    $('#print-fecha').text(`Fecha: ${new Date().toLocaleDateString()}`);
                    $('#print-almacen').text(infoBase.almacen_nombre);
                    $('#print-proveedor').text(infoBase.proveedor_nombre || 'No especificado');
                    $('#print-direccion').text(infoBase.dp_direccion || 'No especificado');
                    $('#print-rfc').text(infoBase.dp_rfc || 'No especificado');
                    $('#print-telefono').text(infoBase.dp_telefono || 'No especificado');

                    $('#costo_total').text(costo.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' }) || 'nohay costo');

                    let html = '';

                    data.forEach(i => {

                        const factor = parseFloat(i.factor_conversion) || 1;
                        const uBase = i.unidad_medida || 'pzas';
                        const uRep = i.unidad_reporte || 'Mayoreo';
                        const costo = parseFloat(i.costo) || 0;

                        const cantidad = parseFloat(i.cantidad) || 0;

                        const costoUnitario = cantidad > 0 ? (costo / cantidad) : 0;

                        html += `
                <tr>
                    <td class="fw-bold">${i.producto_nombre}</td>

                    <td class="text-center">
                        ${cantidad} ${uRep}
                    </td>

                    <td class="text-center">
                        ${costoUnitario.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}
                    </td>

                    <td class="text-center">
                        ${costo.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })}
                    </td>
                </tr>`;
                    });

                    $('#print-tabla-cuerpo').html(html);

                    new bootstrap.Modal(document.getElementById('modalImprimirSolicitud')).show();
                })
                .catch(err => {
                    console.error('FETCH ERROR:', err);
                });
        }
        /**
         * Llama al comando de impresión del navegador
         */
        function ejecutarImpresion() {
            // 1. Obtener el contenido del área de impresión
            const contenido = document.getElementById('areaImpresion').innerHTML;
            const folio = $('#print-folio').text();

            // 2. Crear una ventana nueva
            const ventana = window.open('', '_blank', 'height=600,width=800');

            // 3. Escribir el HTML necesario para que se vea bien
            ventana.document.write(`
        <html>
            <head>
                <title>Imprimir ${folio}</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: 'Inter', sans-serif; padding: 30px; }
                    .table{
                    background-color: #efefef00 !important;
                    }
                    .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
                    .fw-bold { font-weight: bold !important; }
                    @media print {
                        .no-print { display: none; }
                        @page { margin: 0 !important; }
                    }
                    .firma-linea { border-top: 1px solid #000; margin-top: 50px; text-align: center; padding-top: 5px; font-size: 12px; }
                </style>
            </head>
            <body>
             <img
    src="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
    style="
        position: fixed;
        top: 19.5%;
        left: 50%;
        transform: translate(-70%, -70%);
        width: 180px;
        opacity: 0.08;
        z-index: -1;
    "
>
      
                ${contenido}
                <script>
                    // Esperar a que cargue el CSS y luego imprimir
                    window.onload = function() {
                        window.print();
                        window.onafterprint = function() { window.close(); };
                    };
                <\/script>
            </body>
        </html>
    `);

            ventana.document.close();
        }
        function actualizarListaProveedores() {
            fetch('egresosController.php?action=getProveedoresJSON')
                .then(res => res.json())
                .then(data => {
                    let $select = $('#select_proveedor');
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