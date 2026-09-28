<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobantes de pago | cfsistem</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <link href="/myvet/css/solicitudesCompra.css" rel="stylesheet" />

</head>

<body>
    <?php renderizarLayout($paginaActual); ?>

    <main class="main-content border border-subtle">

        <div class="glass-card p-4 mb-1">

            <div class="row align-items-center ">
                <div class="col-md-8">
                    <h1 class="h3 fw-bold mb-1">Comprobante de pago</h1>
                    <p class="text-body-secondary small">Gestión de Comprobate de pago</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-dark" onclick="nuevaCotizacion()">
                        <i class="bi bi-plus-lg me-2"></i> Crear Comprobante de pago
                    </button>
                    
                </div> 
                </div>
            </div>

           <div class="row g-3 align-items-end  mb-3 p-3">

        <!-- Fecha Inicio -->
        <div class="col-lg-2 col-md-6">
            <label class="form-label small fw-bold text-body-secondary text-uppercase">
                Inicio
            </label>

            <input
                type="date"
                id="fechaInicio"
                value="<?= date('Y-m-01') ?>"
                class="form-control  border border-subtle shadow-sm"
                style="border-radius:12px;">
        </div>

        <!-- Fecha Fin -->
        <div class="col-lg-2 col-md-6">
            <label class="form-label small fw-bold text-body-secondary text-uppercase">
                Fin
            </label>

            <input
                type="date"
                id="fechaFin"
                value="<?= date('Y-m-d') ?>"
                class="form-control  border border-subtle shadow-sm"
                style="border-radius:12px;">
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
<?php endif ;?>

                <?php foreach ($almacenes as $alm): ?>

                <option value="<?= htmlspecialchars($alm['id']) ?>">
                    <?= htmlspecialchars($alm['nombre']) ?>
                </option>

                <?php endforeach; ?>

            </select>
        </div>

        <!-- Estado -->
       <div class="col-lg-2 col-md-12">
                    <label class="form-label small fw-bold text-body-secondary text-uppercase">Estado</label>
                    <select id="filtroEstado" class="form-select border-light shadow-sm">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                     
                        <option value="cancelado">Cancelado</option>
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

                <input
                    type="text"
                    id="buscadorGeneral"
                    class="form-control  border border-subtle"
                    placeholder="Folio o Cliente">

            </div>
        </div>

    </div>

</div>
        </div>

      <div class="glass-card p-4 border border-subtle">
    <div class="table-responsive">
        <table class="table align-middle w-100">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Almacén</th>
                    <th>Monto</th>
                    <th>Pendiente por aplicar</th>
                    <th>Recibido</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaComprobantes">
                </tbody>
        </table>
        <div class="d-flex justify-content-end align-items-center mb-3">
    <button type="button" class="btn btn-success rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 fw-semibold shadow-sm" onclick="exportarComprobantesCSV()">
        <i class="bi bi-file-earmark-excel-fill fs-5"></i> Exportar Comprobantes (CSV)
    </button>
</div>
    </div>
</div>
    </main>


    <div class="modal fade" id="modalGestionSolicitud" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content  shadow-lg" style="border-radius: 24px; overflow:hidden;">

                <!-- HEADER -->
                <div class="modal-header bg-success text-white px-4 py-3 ">
                    <div>
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-box-arrow-in-down me-2"></i>
                            Convertir Solicitud <span name="uni-folio" id="uni-folio"></span>
                        </h5>
                        <small class="opacity-75">Generación de compra e inventario</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="formConvertirCompra" enctype="multipart/form-data">

                    <input type="hidden" name="solicitud_id" id="uni-solicitud-id">

                    <div class="modal-body bg-light px-4 py-4">

                        <!-- ====================================== -->
                        <!-- CARD PRINCIPAL -->
                        <!-- ====================================== -->

                        <div class="bg-white rounded-4 shadow-sm p-4 mb-4 border">

                            <!-- HEADER -->
                            <div class="d-flex justify-content-between align-items-center mb-4">

                                <div>
                                    <h5 class="fw-bold mb-1 text-dark">
                                        <i class="bi bi-cart-check me-2 text-success"></i>
                                        Información de Compra
                                    </h5>

                                    <small class="text-body-secondary">
                                        Datos generales de la entrada
                                    </small>
                                </div>

                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                                    Compra en proceso
                                </span>

                            </div>

                            <!-- ====================================== -->
                            <!-- FILA 1 -->
                            <!-- ====================================== -->

                            <div class="row g-3">

                                <!-- ALMACÉN -->
                                <div class="col-md-3">

                                    <label class="form-label small fw-bold text-body-secondary text-uppercase">
                                        Almacén destino
                                    </label>

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

                                    <input type="text" class="form-control rounded-3 shadow-sm bg-light fw-bold"
                                        value="<?= htmlspecialchars($almacenes[0]['nombre'] ?? 'Almacén Asignado') ?>"
                                        readonly>

                                    <input type="hidden" id="almacen_id2" name="almacen_id2"
                                        value="<?= $almacen_usuario ?? ($almacenes[0]['id'] ?? '') ?>">

                                    <?php endif; ?>

                                </div>

                                <!-- PROVEEDOR -->
                                <div class="col-md-3">

                                    <label class="form-label small fw-bold text-body-secondary text-uppercase">
                                        Proveedor
                                    </label>

                                    <input type="text" id="uni-proveedor"
                                        class="form-control rounded-3 shadow-sm bg-light fw-bold" readonly>

                                    <input type="hidden" name="proveedor" id="uni-proveedor-nombre">

                                </div>

                                <!-- FOLIO -->
                                <div class="col-md-2">

                                    <label class="form-label small fw-bold text-body-secondary text-uppercase">
                                        Folio factura
                                    </label>

                                    <input type="text" name="folio" class="form-control rounded-3 shadow-sm"
                                        placeholder="FAC-000" required>

                                </div>

                                <!-- MÉTODO -->
                                <div class="col-md-2">

                                    <label class="form-label small fw-bold text-body-secondary text-uppercase">
                                        Método pago
                                    </label>

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

                                    <label class="form-label small fw-bold text-body-secondary text-uppercase">
                                        Evidencia
                                    </label>

                                    <input type="file" name="evidencia_compra" class="form-control rounded-3 shadow-sm"
                                        accept="image/*,.pdf">

                                </div>

                            </div>

                            <!-- ====================================== -->
                            <!-- FILA 2 -->
                            <!-- ====================================== -->

                            <div class="row g-3 mt-2 align-items-stretch">

                                <!-- DEUDA -->
                                <div class="col-md-4">

                                    <div class="bg-danger-subtle border border-danger-subtle rounded-4 p-3 h-100">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <div>

                                                <small class="text-danger fw-semibold d-block mb-1">
                                                    Deuda actual proveedor
                                                </small>

                                                <input type="text" name="deudaProveedor" id="uni-proveedor-deuda"
                                                    class="form-control  bg-transparent text-danger fw-bold fs-4 p-0"
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
                                            <i class="bi bi-cash-coin me-1"></i>
                                            Abono a deuda
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

                                        <small class="text-success fw-semibold text-uppercase d-block mb-2">
                                            Total compra
                                        </small>

                                        <span class="fw-bold text-success" id="uni-gran-total" style="font-size:2rem;">
                                            $ 0.00
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- ====================================== -->
                        <!-- TABLA -->
                        <!-- ====================================== -->

                        <div class="bg-white rounded-4 shadow-sm border overflow-hidden">

                            <div class="p-3 border-bottom bg-light">

                                <h6 class="fw-bold mb-1">
                                    <i class="bi bi-box-seam me-2 text-success"></i>
                                    Productos
                                </h6>

                                <small class="text-body-secondary">
                                    Conversión de unidades y costos
                                </small>

                            </div>

                            <div class="table-responsive">

                                <table class="table align-middle mb-0" id="tablaConversion">

                                    <thead class="table-light">

                                        <tr class="small text-body-secondary">

                                            <th class="ps-4">Producto</th>
                                            <th>Mayoreo</th>
                                            <th>Sueltas</th>
                                            <th>Faltantes</th>
                                            <th>Excedentes</th>
                                            <th>Costo</th>
                                            <th class="text-end pe-4">Total</th>

                                        </tr>

                                    </thead>

                                    <tbody></tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                    <!-- ====================================== -->
                    <!-- FOOTER -->
                    <!-- ====================================== -->

                    <div class="modal-footer bg-light  px-4 pb-4">

                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm fw-semibold">
                            <i class="bi bi-check2-circle me-2"></i>
                            Confirmar compra
                        </button>

                    </div>

                </form>
            </div>
        </div>
    </div><div class="modal fade" id="modalImprimirSolicitud" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 16px; overflow: hidden;">

            <div class="modal-header text-white  py-3"
                style="background: linear-gradient(135deg, #1f2a37 0%, #334155 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-receipt fs-4"></i> <h5 class="fw-bold mb-0">Detalle del Comprobante</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0 bg-secondary bg-opacity-10">
                <div id="areaImpresion" class="my-3 mx-auto p-4 shadow-sm" 
                     style="width: 320px; font-family: 'Courier New', Courier, monospace; font-size: 0.85rem; border-radius: 4px; background-color: #fff; color: #000000;">
                    
                    <div style="text-align: center; margin-bottom: 8px;">
                        <h4 style="font-family: sans-serif; font-weight: 800; text-transform: uppercase; margin-bottom: 0; letter-spacing: 1px; color: #1f2a37; font-size: 1.3rem;">
                            CF SYSTEM
                        </h4>
                        <p style="font-family: sans-serif; font-size: 0.7rem; color: #6b7280; margin-top: 4px; margin-bottom: 4px;">COMPROBANTE DE PAGO</p>
                        
                        <div id="print-folio" style="font-size: 0.85rem; font-weight: bold; border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; margin: 8px 0;">
                            FOLIO: #00000
                        </div>
                    </div>
                     
                    <div style="font-family: sans-serif; padding: 4px 0;">
                        <span style="font-size: 0.65rem; font-weight: bold; text-transform: uppercase; color: #6b7280; display: block; letter-spacing: 0.5px;">Cliente:</span>
                        <div id="print-cliente" style="font-weight: bold; font-size: 1rem; color: #000000; line-height: 1.2;">---</div>
                    </div>

                    <div style="margin-bottom: 8px;">
                        <table class="style-ticket-table" style="width: 100%; font-size: 0.8rem; line-height: 1.4; border-collapse: collapse;">
                            <tr>
                                <td style="width: 40%; color: #6b7280; padding: 4px 0;">NÚMERO VENTA:</td>
                                <td id="print-numero_venta" style="width: 60%; font-weight: bold; text-align: right; color: #000000; padding: 4px 0;">---</td>
                            </tr>
                            <tr>
                                <td style="color: #6b7280; padding: 4px 0;">FECHA:</td>
                                <td id="print-fecha_dep" style="font-weight: 600; text-align: right; color: #000000; padding: 4px 0;">---</td>
                            </tr>
                            <tr>
                                <td style="color: #6b7280; padding: 4px 0;">REFERENCIA:</td>
                                <td id="print-referencia" style="text-align: right; color: #000000; padding: 4px 0;">---</td>
                            </tr>
                        </table>
                    </div>

                    <div>
                        <table style="width: 100%; font-family: sans-serif; table-layout: fixed; font-size: 11px; line-height: 1.2; border-collapse: collapse;">
                            <tr>
                                <td style="width: 45%; color: #6b7280; font-weight: bold; text-transform: uppercase; padding-bottom: 4px;">
                                    MÉTODO PAGO:
                                </td>
                                <td id="metodo_pago_dep" style="width: 55%; font-weight: bold; text-align: right; text-transform: uppercase; color: #000000; font-size: 12px; padding-bottom: 4px;">
                                    ---
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 45%; color: #6b7280; font-weight: bold; text-transform: uppercase;">
                                    TOTAL RECIBIDO:
                                </td>
                                <td id="costo_total" style="width: 55%; font-weight: bold; text-align: right; color: #000000; font-size: 14px;">
                                    $0.00
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="text-align: center; margin-top: 16px; padding-top: 8px; border-top: 1px dashed #000; font-family: sans-serif; font-size: 0.7rem;">
                        <p style="color: #6b7280; text-transform: uppercase; margin: 0;">*** Gracias por su confianza ***</p>
                    </div>

                    <div style="display:none;">
                        <div id="print-almacen">---</div>
                        <div id="print-usuario">---</div>
                    </div>

                </div>
            </div>

            <div class="modal-footer bg-light border-top-0 justify-content-end gap-2 py-3 px-4" id="footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>
    <style>
    /* =========================
   MODAL BASE
========================= */
    #modalImprimirSolicitud .modal-content {
        border-radius: 10px;
        
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        background: #fff;
    }

    #modalImprimirSolicitud .modal-header {
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        color: #111827;
        padding: 1.2rem 1.5rem;
    }

    #modalImprimirSolicitud .modal-footer {
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
    }

    /* =========================
   ÁREA DE IMPRESIÓN
========================= */
    #areaImpresion {
        padding: 2rem;
        background: #ffffff;
        color: #111827;
        font-family: "Segoe UI", system-ui, sans-serif;
    }

    /* =========================
   ENCABEZADOS
========================= */
    #areaImpresion h2 {
        font-weight: 700;
        font-size: 1.6rem;
        color: #1f2937;
        margin-bottom: 0.3rem;
    }

    #areaImpresion h5 {
        font-weight: 500;
        color: #4b5563;
    }

    /* =========================
   BLOQUES INFO
========================= */
    .card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: none;
        background: #ffffff;
    }

    .card:hover {
        transform: none;
        box-shadow: none;
    }

    /* =========================
   TABLA ESTILO DOCUMENTO
========================= */
    .table {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead th {
        background: #f3f4f6;
        color: #111827;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e5e7eb;
        padding: 12px;
    }

    .table tbody td {
        border-color: #f1f5f9;
        padding: 10px;
        font-size: 0.95rem;
    }

    .table tbody tr:nth-child(even) {
        background: #fafafa;
    }

    /* =========================
   DIVISOR LIMPIO
========================= */
    .divider {
        height: 1px;
        background: #e5e7eb;
        margin: 1.5rem 0;
    }

    /* =========================
   FIRMAS
========================= */
    .signature-line {
        width: 180px;
        height: 1px;
        background: #111827;
        margin-bottom: 6px;
    }

    .signature-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
    }

    /* =========================
   BOTONES / ACCIONES
========================= */
    .btn-primary {
        background: #2563eb;
        
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    /* =========================
   PRINT MODE
========================= */
    @media print {

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

        .modal-header,
        .modal-footer {
            display: none !important;
        }

        .modal-content {
            box-shadow: none !important;
            border: none !important;
        }

        #areaImpresion {
            padding: 1rem;
        }

        .table thead th {
            background: #f0f0f0 !important;
            color: #000 !important;
        }

        .table tbody tr:nth-child(even) {
            background: #fff !important;
        }
    }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>





    <?php require_once __DIR__ . '/comprobantes_pago/modalComprobante.php'; ?>
    <?php require_once __DIR__ . '/comprobantes_pago/multipleRegistroPago.php'; ?>
    <?php require_once __DIR__ . '/cotizacionesModales/nuevoClienteModal.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/agregarPoductoModal.php'; ?>
    <?php require_once __DIR__ . '/egresosComponets/modalProveedoresCompra.php'; ?>
   
    <script>
    let totalGlobalPago = 0;
    let datost=0;
    // Se ejecuta automáticamente en cuanto el navegador termina de estructurar el árbol HTML de la página
$(document).ready(function () {
    cargarComprobantes(); // Ejecuta de inmediato la consulta y renderizado inicial
});
 $(document).ready(function() {
       
       
$('#buscadorGeneral').on('keyup', cargarComprobantes);
$('#filtroAlmacen').on('change', cargarComprobantes);
$('#filtroEstado').on('change', cargarComprobantes);
$('#fechaInicio').on('change', cargarComprobantes);
$('#fechaFin').on('change', cargarComprobantes);
        

     });
/**
 * Función asíncrona que consulta los comprobantes de pago al controlador PHP
 * mediante Fetch API, procesa las reglas de negocio y dibuja las filas del tbody.
 */
/**
 * Abre el modal de dispersión de pagos e inicializa el monto
 * @param {number} monto - El monto inicial que se va a distribuir
 */
function abrirModalDispersion(id,monto,idComprobante,aplicado) {
    getDeuda(id,monto,idComprobante,aplicado);
    // 1. Buscamos el elemento por su ID exacto
    const modalElement = document.getElementById('modalDispersión');
    
    // 2. Creamos o recuperamos la instancia de Bootstrap 5
    const modalInstancia = bootstrap.Modal.getOrCreateInstance(modalElement);
    
    // 3. Inicializamos tus variables y renderizado con el monto recibido
    
    
    // 4. Mostramos el modal en pantalla
    modalInstancia.show();
}
let referencia='';
async function cargarComprobantes() {
    try {
        
        // Agrupación y formateo seguro de todos los parámetros de filtrado que viajarán en la URL hacia PHP
        const params = new URLSearchParams({
            action: 'listarComprobantes',                     // Acción que mapea al método del controlador PHP
            almacen: $('#filtroAlmacen').val() || '',        // ID del almacén seleccionado en tus selectores
            fechaInicio: $('#fechaInicio').val() || '',      // Rango de fecha inicial de búsqueda
            fechaFin: $('#fechaFin').val() || '',            // Rango de fecha final de búsqueda
            estado: $('#filtroEstado').val() || '',          // Estado actual del comprobante
            buscador: $('#buscadorGeneral').val() || ''      // Texto de búsqueda libre (Buscador general)
        });

        // Variable tipo String encargada de ir acumulando secuencialmente el HTML de cada fila (tr)
        let tablaHTML = '';
        
        // Petición AJAX (Fetch) enviando la ruta de tu controlador acompañada de los parámetros de búsqueda estructurados
        const res = await fetch(
            `/myvet/app/controllers/comprobantesPagoController.php?${params.toString()}`
        );

        // Transforma la respuesta cruda del servidor en un objeto JSON nativo de JavaScript
        let data = await res.json();
        console.log("Comprobantes recibidos del servidor:", data.data);

        // Validación de seguridad: Comprueba si la respuesta no trae datos o el arreglo viene totalmente vacío
        if (!data.data || data.data.length === 0) {
            // Inserta una fila única con un mensaje centralizado indicando que no hay registros y frena el script
            $('#tablaComprobantes').html('<tr><td colspan="7" class="text-center text-body-secondary py-3">No se encontraron registros de pago</td></tr>');
            return;
        }

        // Bucle que recorre uno a uno cada objeto "c" (Comprobante/Cotización) dentro del arreglo de datos
        data.data.forEach(c => {

            // 1. FORMATEO DEL FOLIO NUMÉRICO
            // Transforma el ID a texto y rellena con ceros a la izquierda hasta asegurar un tamaño fijo de 5 dígitos
            // Emula exactamente al método de PHP: str_pad($s['id'], 5, "0", STR_PAD_LEFT)
            const folio = String(c.id).padStart(5, '0');

            // 2. PROCESAMIENTO DE FECHA UNIVERSAL
            let fechaFormateada = c.fecha || '';
            if (c.fecha) {
                // Reemplaza los espacios por una "T" para forzar la compatibilidad con el estándar ISO.
                // Esto previene fallos silenciosos de "Invalid Date" en entornos estrictos como Safari de Apple o dispositivos iOS.
                const date = new Date(c.fecha.replace(/\s/, 'T'));
                
                // Si la fecha se pudo interpretar y parsear de manera completamente correcta
                if (!isNaN(date.getTime())) {
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0'); // Se suma 1 porque en JS Enero es el mes 0
                    const year = date.getFullYear();
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    
                    // Une los fragmentos en el formato final legible para el usuario: dd/mm/aaaa hh:mm
                    fechaFormateada = `${day}/${month}/${year} ${hours}:${minutes}`;
                }
            }

            // 3. CAPTURA Y NORMALIZACIÓN DEL ESTADO
            // Convierte a minúsculas el estado para evaluar la condición de manera exacta y segura
            const estado = (c.estado || 'pendiente').toLowerCase();

            // 4. GENERACIÓN DE ACCIONES EXCLUSIVAS (BOTONES CONDICIONALES)
            // Variable temporal para guardar botones que solo deben aparecer si el comprobante NO está cancelado
            let botonesAccion = '';
            let activar='';
            let rolact=<?= $rolAct ?>;
            let editar='';
            let admin='';
        // Parseo de valores numéricos para evitar fallos de comparación de tipos
const aplicado = parseFloat(c.aplicado) || 0;
const monto = parseFloat(c.monto) || 0;

// 1. Botón o Indicador de Dispersión
const dispersar = (aplicado < monto)
    ? `<button type="button" class="btn btn-outline-dark btn-sm rounded-2 d-inline-flex align-items-center gap-1 shadow-sm" onclick="abrirModalDispersion(${c.id_cliente}, ${c.monto}, ${c.id},${c.aplicado})">
            <i class="bi bi-diagram-3-fill"></i> Dispersar
       </button>`
    : `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fw-semibold">
            <i class="bi bi-check-circle-fill me-1"></i> Aplicado
       </span>`;

// 2. Estado de Recibido (Badge limpio y centrado)
activar = (c.recibido == 1)
    ? `<span class="badge bg-success-subtle text-success p-1 rounded-circle" title="Recibido">
            <i class="bi bi-check-lg fs-6"></i>
       </span>`
    : `<span class="badge bg-danger-subtle text-danger p-1 rounded-circle" title="Pendiente">
            <i class="bi bi-x-lg fs-6"></i>
       </span>`;

// 3. Botón de Edición / Factura (Solo Admin)
editar = (rolact == 1)
    ? `<button type="button" class="btn btn-sm btn-light text-primary  rounded-2" onclick="actualizar(${c.id})" title="Agregar Factura">
            <i class="bi bi-pencil-square fs-6"></i>
       </button>`
    : '';

// 4. Lógica de Acciones por Rol y Estado


if (estado !== 'cancelado') {
    if (rolact <= 2) {
        admin = `
            <button type="button" class="btn btn-outline-danger btn-sm rounded-2 d-inline-flex align-items-center gap-1" onclick="eliminarSolicitud(${c.id})" title="Cancelar Solicitud">
                <i class="bi bi-x-circle"></i> Cancelar
            </button>
            ${dispersar}
        `;
    }

    // Grupo de botones agrupados con flexbox y espaciado uniforme
    botonesAccion = `
        <div class="d-inline-flex align-items-center gap-1">
          
            <button type="button" class="btn btn-primary btn-sm rounded-2 d-inline-flex align-items-center gap-1 shadow-sm" onclick="imprmirComprobante(${c.id})">
                <i class="bi bi-eye-fill"></i> VER
            </button>
            ${admin}
        </div>
    `;
} else {
    // Indicador sutil para registros cancelados
    botonesAccion = `
        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-2 fw-normal">
            <i class="bi bi-slash-circle me-1"></i> Cancelado
        </span>
    `;
}
            // 5. ARMADO E INYECCIÓN DE LA FILA (HTML Template Literal)
            // Se va acumulando dinámicamente la estructura completa de la fila actual dentro de 'tablaHTML'
            tablaHTML += `
                <tr>
                    <td><span class="text-dark fw-bold">#${c.folio}</span></td>
                    <td class="text-body-secondary small">${fechaFormateada}</td>
                    <td class="fw-medium">${escapeHtml(c.nombre_comercial || 'Sin asignar')}</td>
                    <td><span class="badge bg-light text-dark border">${escapeHtml(c.almacen || '')}</span></td>
                    <td class="fw-bold">$${parseFloat(c.monto || 0).toFixed(2)}</td>
                   <td class="fw-bold">$${parseFloat(c.monto-c.aplicado || 0).toFixed(2)}</td>
                    <td>
                        <span class="badge bg-light text-dark border">${escapeHtml(c.estado || '')}</span>
                    </td>
                    <td class="text-end">
                   ${editar}
                   ${activar}
                    </td>
                    <td class="text-end">
                        ${botonesAccion}
                    </td>
                </tr>
            `;
        });

        // 6. ACTUALIZACIÓN DIRECTA DEL DOM DE LA TABLA
        // Inyecta el bloque HTML acumulado directamente en el contenedor del tbody seleccionado por su ID
        $('#tablaComprobantes').html(tablaHTML);

    } catch (error) {
        // Atrapa cualquier error de red, fallos del servidor o inconsistencias de código y lo imprime de forma limpia en la consola
        console.error("Error crítico capturado en cargarComprobantes:", error);
    }
}

/**
 * Función auxiliar de Sanitización (Escape de Entidades HTML)
 * Recibe una cadena de texto y reemplaza caracteres especiales peligrosos (<, >, &, ", ') por texto plano seguro.
 * Protege la aplicación contra ataques Cross-Site Scripting (XSS) en caso de que los datos de la base traigan código malicioso.
 */

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, "&amp;")
              .replace(/</g, "&lt;")
              .replace(/>/g, "&gt;")
              .replace(/"/g, "&quot;")
              .replace(/'/g, "&#039;");
}
async function imprmirComprobante(id) {
    try {
        console.log("Solicitando ID:", id);

        const resp = await fetch(`/myvet/app/controllers/comprobantesPagoController.php?action=obtenerDetalle&id=${id}`);
        
        // CORRECCIÓN 1: Cambiado .data() por .json()
        const datos = await resp.json(); 
        
        console.log('RESPUESTA DEL SERVIDOR:', datos);

        if (datos.status !== 'success') {
            Swal.fire('Error', datos.message || 'No se encontraron datos', 'error');
            return;
        }

        const data = datos.data; 

        // 1. FORMATEAR EL MONTO A MONEDA (MXN)
        const montoFormateado = parseFloat(data.monto).toLocaleString('es-MX', { 
            style: 'currency', 
            currency: 'MXN' 
        });

        // 2. INYECTAR LOS DATOS DIRECTAMENTE EN EL MODAL
        $('#print-folio').text(`#${String(data.id).padStart(5, '0')}`);
        $('#print-cliente').text(data.nombre_comercial);
        
        // CORRECCIÓN 2: Se usa 'nombre_almacen' que es el alias que viene del SQL
        $('#print-almacen').text(data.nombre_almacen); 
        
        $('#print-usuario').text(data.usuario);
        $('#print-referencia').text(data.referencia || 'Sin referencia');
        $('#print-fecha_dep').text(data.fecha);
        
        $('#costo_total').text(montoFormateado);
        $('#metodo_pago_dep').text(data.metodo_pago);
        $('#print-numero_venta').text(data.numero_ventas);

        // 3. GENERAR EL BOTÓN EN EL FOOTER
        const footer = `
        
            
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
    Cerrar
</button>

<button type="button" class="btn btn-dark rounded-pill px-4" onclick="prepararImpresion(${data.id})">
    Imprimir
</button>

        `;
        $('#footer').html(footer);

        // 4. LEVANTAR EL MODAL
        const miModal = new bootstrap.Modal(document.getElementById('modalImprimirSolicitud'));
        miModal.show();

    } catch (e) {
        console.error("Error en imprmirComprobante:", e);
        Swal.fire('Error', 'Fallo de conexión al recuperar el detalle', 'error');
    }
}

async function prepararImpresion(id) {
        try {

            $('#tablaConversion tbody').empty();

           console.log("Solicitando ID:", id);

        const resp = await fetch(`/myvet/app/controllers/comprobantesPagoController.php?action=obtenerDetalle&id=${id}`);
        
        // CORRECCIÓN 1: Cambiado .data() por .json()
        const datos = await resp.json(); 
        
        console.log('RESPUESTA DEL SERVIDOR:', datos);

        if (datos.status !== 'success') {
            Swal.fire('Error', datos.message || 'No se encontraron datos', 'error');
            return;
        }
       

        const data = datos.data; 
        let ref=$('#print-referencia').val();
        console.log(ref);
        referencia=data;


            

            const infoBase = data[0];
const montoFormateado = parseFloat(data.monto).toLocaleString('es-MX', { 
            style: 'currency', 
            currency: 'MXN' 
        });

        // 2. INYECTAR LOS DATOS DIRECTAMENTE EN EL MODAL
        $('#print-folio').text(`#${String(data.id).padStart(5, '0')}`);
        $('#print-cliente').text(data.nombre_comercial);
        
        // CORRECCIÓN 2: Se usa 'nombre_almacen' que es el alias que viene del SQL
        $('#print-almacen').text(data.nombre_almacen); 
        
        $('#print-usuario').text(data.usuario);
        
        $('#print-referencia').text(referencia.referencia);
        $('#print-fecha_dep').text(data.fecha);
        
        $('#costo_total').text(montoFormateado);
        $('#metodo_pago_dep').text(data.metodo_pago);
         $('#numero_venta').text(data.numero_ventas);

        


           

            ejecutarImpresion();

        } catch (e) {
            console.error(e);
        }
    }

   
function ejecutarImpresion() {
     
   
 

    const contenedorOriginal = document.getElementById('areaImpresion');

    if (!contenedorOriginal) {
        alert("No se encontró el área de impresión.");
        return;
    }

    // Clonar
    const clon = contenedorOriginal.cloneNode(true);

    // Copiar valores de inputs
    contenedorOriginal.querySelectorAll('input, textarea, select').forEach((elemento, i) => {

        const copia = clon.querySelectorAll('input, textarea, select')[i];

        if (!copia) return;

        if (elemento.tagName === "SELECT") {
            copia.value = elemento.value;
        } else {
            copia.setAttribute("value", elemento.value);
            copia.value = elemento.value;
        }

    });

    const contenido = clon.outerHTML;

    const folio = document.getElementById("print-folio")
        ? document.getElementById("print-folio").innerText
        : "";

    const ventana = window.open("", "_blank");

    if (!ventana) {
        alert("El navegador bloqueó la ventana emergente.");
        return;
    }

    ventana.document.open();

    ventana.document.write(`
<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">

<title>${folio}</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">


<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"><\/script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"><\/script>

<style>

body{
    margin:0;
    padding:5mm;
    font-family:Courier New, monospace;
    background:#FFF;
    color:#000;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}

#areaImpresion{
    width:80mm;
    margin:auto;
}

@page{
    size:80mm auto;
    margin:2mm;
}

table{
    width:100%;
}

img{
    max-width:100%;
}

</style>

</head>

<body>

${contenido}
<script>
window.onload = async function(){

    const { jsPDF } = window.jspdf;

    const area = document.getElementById("areaImpresion");

    // 1. Bajamos la escala a 2. Esto reduce el tamaño de los píxeles a la mitad y aligera el proceso de guardado.
    const canvas = await html2canvas(area, {
        scale: 2,
        useCORS: true
    });

    // 2. Cambiamos a JPEG y comprimimos al 75% (0.75). Esto reduce el peso drásticamente.
    const img = canvas.toDataURL("image/jpeg", 0.75);

    const anchoPdf = 80;
    const altoPdf = (canvas.height * anchoPdf) / canvas.width;

    const pdf = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: [anchoPdf, altoPdf]
    });

    // 3. Importante pasarle 'JPEG' aquí también para que jsPDF no intente reconvertirlo a algo pesado.
    pdf.addImage(img, 'JPEG', 0, 0, anchoPdf, altoPdf);

    // 4. Forzar la descarga del archivo en el navegador
    pdf.save("Comprobante.pdf");
}
<\/script>
</body>

</html>
`);

    ventana.document.close();

    ventana.onload = function () {

        setTimeout(function () {
            const esMovil = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        // 2. Esperar 1 segundo a que carguen estilos, fuentes e imágenes
       
            if (esMovil) {
                // --- COMPORTAMIENTO EN CELULARES: DESCARGA DE PDF AUTOMÁTICA ---
             
                
            } else {
                // --- COMPORTAMIENTO EN COMPUTADORAS: DIÁLOGO NATIVO DE IMPRESIÓN ---
               ventana.focus();

            ventana.print();
            }

            

            // No cerrar automáticamente en móviles
            if (!/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                setTimeout(() => ventana.close(), 500);
            }

        }, 1000);

    };

}
 $(document).ready(function() {
        const table = $('#tablaSolicitudes').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            order: [
                [0, 'desc']
            ],
            dom: 'rt<"d-flex justify-content-between align-items-center mt-3"ip>'
        });

        $('#buscadorGeneral').on('keyup', function() {
            table.search(this.value).draw();
        });
        $('#filtroAlmacen').on('change', function() {
            table.column(3).search(this.value).draw();
        });
        $('#filtroEstado').on('change', function() {
            table.column(5).search(this.value).draw();
        });

        $('#filtroFecha').on('change', function() {
            const rango = $(this).val();
            $.fn.dataTable.ext.search = [];
            if (rango !== 'todos') {
                $.fn.dataTable.ext.search.push(function(settings, data) {
                    const [d, m, a] = data[1].split(' ')[0].split('/');
                    const fechaFila = new Date(a, m - 1, d);
                    const hoy = new Date();
                    hoy.setHours(0, 0, 0, 0);
                    if (rango === 'hoy') return fechaFila.getTime() === hoy.getTime();
                    if (rango === 'ayer') {
                        const ayer = new Date(hoy);
                        ayer.setDate(hoy.getDate() - 1);
                        return fechaFila.getTime() === ayer.getTime();
                    }
                    if (rango === 'semana') {
                        const sem = new Date(hoy);
                        sem.setDate(hoy.getDate() - 7);
                        return fechaFila >= sem;
                    }
                    return true;
                });
            }
            table.draw();
        });

     });
      async function eliminarSolicitud(id) {
        console.log(id);
        const r = await Swal.fire({
            title: '¿Quieres cancelar el comprobante?',
            text: 'No podrás revertir esto',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar'
        });
        if (r.isConfirmed) {
            const fd = new FormData();
            fd.append('id', id);
            const resp = await fetch(`/myvet/app/controllers/comprobantesPagoController.php?action=eliminar`, {
                method: 'POST',
                body: fd
            });
            const res = await resp.json();

if (res.status === 'success') {
    Swal.fire({
        title: '¡Éxito!',
        text: res.message || 'Operación realizada correctamente.',
        icon: 'success',
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#1f2a37', // Combinando con el tono oscuro de tu CF System
        timer: 2000, // Se cierra automáticamente en 2 segundos si no dan clic
        timerProgressBar: true
    }).then(() => {
        // Al dar clic en "Aceptar" o cumplirse el tiempo, se recarga la página
        location.reload();
    });
} else {
    // Por si el servidor responde con un error controlado
    Swal.fire({
        title: 'Error',
        text: res.message || 'Ocurrió un problema en el servidor.',
        icon: 'error',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#334155'
    });
}
        }
    }

      async function actualizar(id) {
    console.log("Actualizando ID:", id);
    
    // Obtenemos el valor de la referencia desde tu input del ticket
    

    const r = await Swal.fire({
        title: '¿Actualizar recibo?',
        text: 'Se guardará el cambio este comprobante.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#1f2a37',
        cancelButtonColor: '#6b7280'
    });

    if (r.isConfirmed) {
        // CORRECCIÓN 1: Estructurar correctamente el FormData (una línea por variable)
        const fd = new FormData();
        fd.append('id', id);
       
        

        try {
            // CORRECCIÓN 2: Cambiado de '?action=eliminar' a '?action=actualizar'
            const resp = await fetch(`/myvet/app/controllers/comprobantesPagoController.php?action=actualizar`, {
                method: 'POST',
                body: fd
            });
            
            const res = await resp.json();

            if (res.status === 'success') {
                Swal.fire({
                    title: '¡Éxito!',
                    text: res.message || 'Operación realizada correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#1f2a37',
                   
                }).then(() => {
                   cargarComprobantes();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: res.message || 'Ocurrió un problema en el servidor.',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#334155'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Error de Red',
                text: 'No se pudo conectar con el servidor.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }
    }
}
    </script>
    <script>
    // Selecciona todos los inputs de texto y también los textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            // Convierte el valor a mayúsculas en tiempo real
            this.value = this.value.toUpperCase();
        });
    });
    async function exportarComprobantesCSV() {
    try {
        // Mismos parámetros de filtrado que utiliza cargarComprobantes()
        const params = new URLSearchParams({
            action: 'listarComprobantes',
            almacen: $('#filtroAlmacen').val() || '',
            fechaInicio: $('#fechaInicio').val() || '',
            fechaFin: $('#fechaFin').val() || '',
            estado: $('#filtroEstado').val() || '',
            buscador: $('#buscadorGeneral').val() || ''
        });

        const res = await fetch(
            `/myvet/app/controllers/comprobantesPagoController.php?${params.toString()}`
        );

        let data = await res.json();

        if (!data.data || data.data.length === 0) {
            alert("No hay registros de comprobantes para exportar con los filtros seleccionados.");
            return;
        }

        // Encabezados del archivo CSV
        const headers = [
            "Folio",
            "Fecha y Hora",
            "Cliente",
            "Almacén",
            "Monto Total ($)",
            "Monto Aplicado ($)",
            "Saldo Pendiente ($)",
            "Estado",
            "Recibido"
        ];

        // Construir filas sanitizando comas y comillas para compatibilidad con CSV
        const rows = data.data.map(c => {
            const folio = String(c.id).padStart(5, '0');

            // Formateo de fecha idéntico al de la tabla
            let fechaFormateada = c.fecha || '';
            if (c.fecha) {
                const date = new Date(c.fecha.replace(/\s/, 'T'));
                if (!isNaN(date.getTime())) {
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    fechaFormateada = `${day}/${month}/${year} ${hours}:${minutes}`;
                }
            }

            const monto = parseFloat(c.monto) || 0;
            const aplicado = parseFloat(c.aplicado) || 0;
            const pendiente = monto - aplicado;
            const recibidoTexto = (c.recibido == 1) ? 'Sí' : 'No';

            const escapeCSV = (str) => {
                if (str === null || str === undefined) return '""';
                let val = String(str).replace(/"/g, '""');
                return `"${val}"`;
            };

            return [
                escapeCSV(folio),
                escapeCSV(fechaFormateada),
                escapeCSV(c.nombre_comercial || 'Sin asignar'),
                escapeCSV(c.almacen || ''),
                monto.toFixed(2),
                aplicado.toFixed(2),
                pendiente.toFixed(2),
                escapeCSV(c.estado || 'Pendiente'),
                escapeCSV(recibidoTexto)
            ].join(',');
        });

        // Ensamblar contenido CSV agregando el BOM UTF-8 para Excel
        const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\n');

        // Generar descarga del archivo
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        
        const hoy = new Date().toISOString().split('T')[0];
        link.setAttribute('href', url);
        link.setAttribute('download', `Reporte_Comprobantes_${hoy}.csv`);
        document.body.appendChild(link);
        
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);

    } catch (error) {
        console.error("Error al exportar comprobantes a CSV:", error);
        alert("Ocurrió un error al generar la exportación de comprobantes.");
    }
}
</script>

</body>

</html>