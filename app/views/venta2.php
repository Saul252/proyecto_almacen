<!DOCTYPE html>
<html lang="es">

<head>
    
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ventas | Sistema</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <?php
 
     require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

   <style>
    /* Variables de color para Modo Claro (por defecto) */
    :root {
        --bg-main: #f8fafc;
        --card-bg: #ffffff;
        --card-shadow: rgba(15, 23, 42, 0.08);
        --header-gradient: linear-gradient(135deg, #09b009 0%, #0f2a13 100%);
        --filter-box-bg: #f8fafc;
        --filter-box-border: #e2e8f0;
        --form-label-color: #64748b;
        --table-header-bg: #0f172a;
        --table-header-text: #94a3b8;
        --tooltip-bg: #212529;
        --tooltip-text: #ffffff;
    }

    /* Sobrescritura de variables para Modo Oscuro */
    [data-bs-theme="dark"] {
        --bg-main: #0f172a;
        --card-bg: #1e293b;
        --card-shadow: rgba(0, 0, 0, 0.35);
        --header-gradient: linear-gradient(135deg, #059669 0%, #022c22 100%);
        --filter-box-bg: #0f172a;
        --filter-box-border: #334155;
        --form-label-color: #94a3b8;
        --table-header-bg: #020617;
        --table-header-text: #cbd5e1;
        --tooltip-bg: #020617;
        --tooltip-text: #f8fafc;
    }

    body {
        background-color: var(--bg-main);
        transition: background-color 0.3s ease;
    }

    .main-content {
       
         background-color: var(--bg-main) !important;
    }

    .main-card {
        background: var(--card-bg);
        border-radius: 20px;
        
        box-shadow: 0 10px 30px var(--card-shadow);
        overflow: hidden;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .page-header-gradient {
        background: var(--header-gradient);
        color: #ffffff;
        padding: 1.5rem 2rem;
    }

    .card-filter-box {
        background-color: var(--filter-box-bg);
        border: 1px solid var(--filter-box-border);
        border-radius: 18px;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .form-label-custom {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--form-label-color);
        text-transform: uppercase;
    }

    .btn-action-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: #ffffff;
        transition: all 0.2s ease;
    }

    .btn-action-primary:hover {
        background-color: #4338ca;
        border-color: #4338ca;
        color: #ffffff;
    }

    .table-custom-header {
        background-color: var(--table-header-bg) !important;
        color: var(--table-header-text) !important;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .table-custom-header th {
        background-color: transparent !important;
        color: var(--table-header-text) !important;
        border-bottom: none !important;
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .total-badge-card {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: #ffffff;
        border-radius: 16px;
        padding: 0 28px;
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
    }

    .entregado-tooltip {
        position: relative;
        display: inline-flex;
        align-items: center;
        cursor: help;
    }

    .fixed-top {
        z-index: 1050;
    }

    .tooltip-custom {
        position: absolute;
        bottom: 130%;
        left: 50%;
        transform: translateX(-50%);
        min-width: 240px;
        max-width: 300px;
        padding: .6rem .8rem;
        background: var(--tooltip-bg);
        color: var(--tooltip-text);
        border-radius: .6rem;
        font-size: .82rem;
        line-height: 1.3;
        text-align: center;
        opacity: 0;
        visibility: hidden;
        transition: .2s ease;
        z-index: 9999;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .25);
    }

    .tooltip-custom::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 6px;
        border-style: solid;
        border-color: var(--tooltip-bg) transparent transparent transparent;
    }

    .entregado-tooltip:hover .tooltip-custom {
        opacity: 1;
        visibility: visible;
    }
    /* ==========================================================================
   ESTILOS BASE & VARIABLES DE TEMA
   ========================================================================== */
:root {
  /* MODO CLARO (EFECTO CRISTAL / GLASSMORPHISM) */
  --pos-bg-main: #f0f4f9;
  --pos-card-bg: rgba(255, 255, 255, 0.65);
  --pos-card-border: rgba(255, 255, 255, 0.8);
  --pos-glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.08);
  --pos-backdrop-blur: blur(16px);
  --pos-text-main: #1e293b;
  --pos-text-muted: #64748b;
  --pos-input-bg: rgba(255, 255, 255, 0.75);
  --pos-input-border: rgba(203, 213, 225, 0.6);
  --pos-table-header-bg: rgba(241, 245, 249, 0.8);
  --pos-accent: #3b82f6;
  --pos-success: #10b981;
  --pos-header-gradient: linear-gradient(135deg, #2563eb, #1d4ed8);
}

[data-bs-theme="dark"],
.dark-mode,
body.dark {
  /* MODO OSCURO (EFECTO HUMO NEBULAR / SMOKE) */
  --pos-bg-main: #0b0f19;
  --pos-card-bg: rgba(18, 24, 38, 0.7);
  --pos-card-border: rgba(255, 255, 255, 0.08);
  --pos-glass-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 40px rgba(15, 23, 42, 0.6);
  --pos-backdrop-blur: blur(20px);
  --pos-text-main: #f1f5f9;
  --pos-text-muted: #94a3b8;
  --pos-input-bg: rgba(30, 41, 59, 0.6);
  --pos-input-border: rgba(255, 255, 255, 0.12);
  --pos-table-header-bg: rgba(30, 41, 59, 0.5);
  --pos-accent: #60a5fa;
  --pos-success: #34d399;
  --pos-header-gradient: linear-gradient(135deg, #1e1b4b, #311b92);
}

/* ==========================================================================
   ESTRUCTURA GENERAL & EFECTOS BÁSICOS
   ========================================================================== */
.main-content {
  background: var(--pos-bg-main);
  color: var(--pos-text-main);
  padding: 1.5rem;
  min-height: 100vh;
  transition: all 0.3s ease;
}

/* Encabezado */
.page-header-gradient {
  background: var(--pos-header-gradient) !important;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

/* Tarjetas Transparentes con Cristal/Humo */
.card-filter-box, 
.main-content .card {
  background: var(--pos-card-bg) !important;
  backdrop-filter: var(--pos-backdrop-blur) !important;
  -webkit-backdrop-filter: var(--pos-backdrop-blur) !important;
  border: 1px solid var(--pos-card-border) !important;
  box-shadow: var(--pos-glass-shadow) !important;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

/* Efecto Humo Animado en Modo Oscuro */
[data-bs-theme="dark"] .card-filter-box,
[data-bs-theme="dark"] .main-content .card {
  position: relative;
  overflow: hidden;
}

[data-bs-theme="dark"] .card-filter-box::before,
[data-bs-theme="dark"] .main-content .card::before {
  content: "";
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.03) 0%, transparent 60%);
  animation: humoMovimiento 18s ease-in-out infinite alternate;
  pointer-events: none;
}

@keyframes humoMovimiento {
  0% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(-5%, 5%) scale(1.1); }
  100% { transform: translate(5%, -5%) scale(1); }
}

/* ==========================================================================
   ELEMENTOS DE FORMULARIO & INPUTS
   ========================================================================== */
.main-content .form-control,
.main-content  {
  background-color: var(--pos-input-bg) !important;
  border: 1px solid var(--pos-input-border) !important;
  color: var(--pos-text-main) !important;
  backdrop-filter: blur(8px);
  transition: all 0.2s ease;
}

.main-content .form-control:focus,
.main-content  {
  border-color: var(--pos-accent) !important;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
}

.main-content .text-body-secondary,
.main-content .text-secondary {
  color: var(--pos-text-muted) !important;
}

/* Select2 Integración */


/* ==========================================================================
   TABLA DE PRODUCTOS
   ========================================================================== */
.main-content table {
  color: var(--pos-text-main) !important;
}

.main-content table thead tr {
  background-color: var(--pos-table-header-bg) !important;
  border-bottom: 2px solid var(--pos-input-border);
}

.main-content table tbody tr {
  border-bottom: 1px solid var(--pos-input-border);
  transition: background-color 0.15s ease;
}

.main-content table tbody tr:hover {
  background-color: rgba(59, 130, 246, 0.05) !important;
}

/* ==========================================================================
   WIDGETS Y CONTENEDORES ESPECIALES
   ========================================================================== */
/* Contenedor de Cambio al cliente */
#contenedor_cambio {
  background: rgba(16, 185, 129, 0.12) !important;
  border: 1px dashed var(--pos-success) !important;
  backdrop-filter: blur(10px);
}

/* Total Venta Estilizado */
#costoTotalCompraEditar {
  font-size: 2.75rem;
  letter-spacing: -1px;
  background: linear-gradient(135deg, #10b981, #059669);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 800;
}

[data-bs-theme="dark"] #costoTotalCompraEditar {
  background: linear-gradient(135deg, #34d399, #10b981);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
</style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="main-content">
    <div class="main-card">
        <form id="formEditarSolicitud">
            <input type="hidden" id="editar_venta_id" name="cotizacion_id" value="">

            <!-- Encabezado de la Página -->
            <div class="page-header-gradient d-flex justify-content-between align-items-center p-2">
                <div>
                    <h4 class="fw-bold mb-1 text-white d-flex align-items-center">
                        <i class="bi bi-cart-check-fill me-2 fs-3"></i> Caja Rápida
                    </h4>
                </div>
            </div>

            <div class="p-4">
                <!-- GRID PRINCIPAL: LATERAL Y CONTENIDO -->
                <div class="row g-4">

                    <!-- ==========================================
                         BARRA LATERAL IZQUIERDA (CONTROLES GENERALES)
                         ========================================== -->
                   

                    <!-- ==========================================
                         ÁREA PRINCIPAL DERECHA (BÚSQUEDA Y TABLA)
                         ========================================== -->
                    <div class="col-lg-9">

                        <!-- Añadir Producto (ARRIBA DEL CONTENIDO) -->
                        <div class="card-filter-box p-3 mb-4 shadow-sm rounded-4">
                            <label class="form-label text-body-secondary fw-semibold small mb-1">
                                <i class="bi bi-search me-1 text-primary"></i> Añadir Producto
                            </label>
                            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                <select id="buscadorProductosEditar" class="form-select select2-pagina border-slate-300">
                                    <option value="">Escribe SKU o nombre...</option>
                                </select>
                                <button type="button" class="btn btn-primary d-flex align-items-center px-3" onclick="abrirModalProducto()" title="Agregar nuevo producto">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tabla de Artículos -->
                        <div class="card shadow-sm rounded-4 overflow-hidden mb-4">
                            <div class="table-responsive" style="min-height:280px !important; max-height: 340px;">
                                <table class="table table-hover align-middle mb-0" id="tablaDetalleEditar">
                                    <thead class="border-bottom">
                                        <tr class="text-secondary small fw-bold text-uppercase tracking-wider">
                                            <th class="ps-4 py-3" style="width: 30%;">Producto</th>
                                            <th style="width: 12%;">Existencias</th>
                                            <th style="width: 12%;">Cantidad</th>
                                            <th style="width: 18%;">Presentación</th>
                                            <th style="width: 18%;">Tipo Precio</th>
                                            <th style="width: 12%;">Precio Unit.</th>
                                            <th style="width: 15%;">Total</th>
                                            <th style="width: 5%;" class="text-center pe-4">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <!-- Filas dinámicas -->
                                    </tbody>
                                </table>

                                <!-- Estado Vacío -->
                                <div id="emptyStateEditar" class="text-center py-5">
                                    <div class="avatar-icon-box text-body-secondary mx-auto mb-3">
                                        <i class="bi bi-cart-x fs-1 text-secondary opacity-50"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">La lista está vacía</h6>
                                    <p class="text-body-secondary small mb-0">Busca e incluye artículos a esta venta desde el buscador superior</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sección Inferior: Pagos, Notas y Total -->
                        <div class="row g-3 align-items-stretch">
                            <!-- Panel Izquierdo: Formas de Pago y Observaciones -->
                            <div class="col-lg-12">
                                <div class="card shadow-sm rounded-4  p-4">
                                    <h6 class="fw-bold mb-3 d-flex align-items-center">
                                        <i class="bi bi-credit-card-2-front text-primary me-2"></i> Datos del Pago y Observaciones
                                    </h6>

                                    <div class="row g-3 ">
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold text-body-secondary">Monto Pagado</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-success-subtle text-success fw-bold">$</span>
                                                <input type="number" id="monto_pagar" class="form-control border-light-subtle fw-bold fs-5 text-success" placeholder="0.0" step="0.01" min="0" oninput="calcularCambio()" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label small fw-semibold text-body-secondary">Método de Pago</label>
                                            <select id="metodo_pago" class="form-select border-light-subtle fw-semibold" onchange="verificarMetodoPago(this.value)">
                                                <option value="Efectivo">💵 Efectivo</option>
                                                <option value="Transferencia">🏦 Transferencia</option>
                                                <option value="Tarjeta">💳 Tarjeta</option>
                                            </select>
                                        </div>
                                        <div id="contenedor_cambio" class="rounded-4 text-center p-2" style="background: #e1fcef; border: 1px dashed #34c759;">
                                            <span class="text-success small fw-bold text-uppercase" style="font-size: 0.6rem; letter-spacing: 1px;">Cambio para el cliente</span>
                                            <h3 class="fw-bold text-success mb-0" id="texto_cambio">$0.00</h3>
                                        </div>
                                    </div>

                                    <!-- Referencia condicional -->
                                    <div class="" id="contenedorReferencia" style="display:none;">
                                        <label class="form-label small fw-bold text-secondary text-uppercase">Referencia de Transacción</label>
                                        <input type="text" id="inputReferencia" class="form-control border-light-subtle" placeholder="Ej. N° de Rastreo / Voucher">
                                    </div>

                                    <div id="pago_aviso" class="small  fw-semibold"></div>

                                    <div>
                                        <label class="form-label small fw-semibold text-body-secondary">Notas / Observaciones</label>
                                        <textarea id="obsVenta" class="form-control border-light-subtle rounded-3" rows="2" placeholder="Agrega detalles o instrucciones sobre esta orden..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel Derecho: Resumen Total y Finalización -->
                           
                        </div>

                    </div>
                  
                      <div class="col-lg-3  d-flex flex-column gap-4">

    <!-- CONTENEDOR 1: Datos de la Venta -->
    <div class="card-filter-box p-3 shadow-sm rounded-4">
        <h6 class="fw-bold mb-3 text-body-secondary d-flex align-items-center border-bottom pb-2">
            <i class="bi bi-sliders me-2 text-primary"></i> Datos Venta
        </h6>

        <div class="d-flex flex-column gap-3">
            <!-- 1. Almacén -->
            <div>
                <label class="form-label text-body-secondary fw-semibold small mb-1">
                    <i class="bi bi-box-seam me-1 text-primary"></i> Almacén de Cargo
                </label>
                <select name="almacen_id_editar" id="almacen_id_editar" class="form-select shadow-sm rounded-3 py-2" required>
                    <?php foreach($almacenes as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 2. Cliente -->
            <div>
                <label class="form-label text-body-secondary fw-semibold small mb-1">
                    <i class="bi bi-person me-1 text-primary"></i> Cliente
                </label>
                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                    <select name="cliente_id_editar" id="cliente_id_editar" class="form-select select2-pagina bg-light" required>
                        <option value="">Seleccionar cliente...</option>
                        <?php foreach($clientes as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre_comercial']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary-light px-3" type="button" onclick="abrirModalNuevoCliente()" title="Nuevo Cliente">
                        <i class="bi bi-person-plus-fill"></i>
                    </button>
                </div>
            </div>

            <!-- 3. Vendedor -->
            <div>
                <label class="form-label text-body-secondary fw-semibold small mb-1">
                    <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                </label>
                <select name="select-vendedor1" id="select-vendedor1" class="form-select shadow-sm rounded-3 py-2 select2-pagina" required>
                    <option value="">Seleccionar vendedor...</option>
                </select>
            </div>
        </div>
    </div>

    <!-- CONTENEDOR 2: Resumen Final y Botones (Toma el alto restante) -->
    <div class="card-filter-box  shadow-sm rounded-4 p-4  d-flex flex-column justify-content-between flex-grow-1">
        <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small" style="font-size: 0.75rem;">
                    <i class="bi bi-receipt me-1"></i> Resumen Final
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                    Orden Lista
                </span>
            </div>

            <p class="text-success-50 small mb-1 text-uppercase fw-semibold tracking-wider">
                Monto Total de la Operación
            </p>

            <div id="costoTotalCompraEditar" class="display-4 fw-black text-success mb-2 tracking-tight">
                $0.00
            </div>

            <input type="hidden" id="totalCotizacionEditar" name="totalCotizacionEditar" value="0">
        </div>

        <div class="pt-4 mt-3 border-top border-white-10 d-flex gap-2 align-items-center">
            <button type="button" onclick="location.reload()" class="btn btn-outline-dark rounded-pill px-3 py-2.5 fw-semibold border-white-10 btn-hover-light d-flex align-items-center justify-content-center">
                <i class="bi bi-arrow-clockwise me-1 fs-6"></i> Reiniciar
            </button>

            <button type="submit" id="botonEnviar" class="btn btn-success btn-lg rounded-pill px-3 py-3 fw-bold flex-grow-1 shadow-lg d-flex align-items-center justify-content-center text-nowrap">
                <i class="bi bi-check2-circle me-2 fs-5"></i> Finalizar Venta
            </button>
        </div>
    </div>

</div>
                        
                    </div>
                </div>
          
        </form>
    </div>

  
    <div style="display:none;" class="col-12 mt-3">
        <div class="card shadow-sm" style="border-radius: 1.5rem;">
            <div class="card-body p-4">
                <h6 class="text-uppercase fw-bold mb-4 text-secondary" style="font-size: 0.7rem; letter-spacing: 1px;">
                    <i class="bi bi-truck me-2 text-primary"></i>Datos de Despacho
                </h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded-4">
                            <label class="form-label small fw-bold text-body-secondary mb-1" style="font-size: 0.6rem;">DESPACHADOR RESPONSABLE</label>
                            <select name="chofer_id" id="patio_chofer_id" class="form-select bg-transparent shadow-none fw-bold p-0">
                                <option value="">Seleccione encargado...</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4">
                            <label class="form-label small fw-bold text-body-secondary mb-1" style="font-size: 0.6rem;">AYUDANTES (MULTIPLE)</label>
                            <select name="tripulantes[]" id="patio_tripulantes" class="form-select bg-transparent shadow-none fw-bold p-0" multiple style="min-height: 24px;">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4">
                            <label class="form-label small fw-bold text-body-secondary mb-1" style="font-size: 0.6rem;">OBSERVACIONES DE ENTREGA</label>
                            <textarea name="observaciones" class="form-control bg-transparent shadow-none p-0 fw-medium" rows="1" placeholder="Ej. Revisado por cliente..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php require_once __DIR__ . '/egresosComponets/agregarPoductoModal.php'; ?>
    <?php require_once __DIR__ . '/clientes/clientesModal.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function vaciarTablaEditar() {
    // 1. Remueve todas las filas del tbody
    $('#tablaDetalleEditar tbody').empty();

    // 2. Muestra el estado vacío (empty state)
    $('#emptyStateEditar').removeClass('d-none');

    // 3. Recalcula el total enviando null
    calcularTotalSolEditar(null);
}
   
    async function recargarProductosEditar() {

        const id = $('#almacen_id_editar').val();
        const $select = $('#buscadorProductosEditar');

        if (!id) return;

        const url = `/myvet/app/controllers/accesoController.php?action=obtenerProductosAlmacen&id=${id}`;
        console.log("Consultando URL:", url);

        // 1. Hacemos la petición
        const resp = await fetch(url);

        // 2. Leemos la respuesta como TEXTO plano primero para ver si hay errores de PHP
        const textoServidor = await resp.text();
        console.log("=== RESPUESTA CRUDA DEL SERVIDOR ===");
        console.log(textoServidor);

        // 3. Intentamos convertir a JSON
        let res;
        try {
            res = JSON.parse(textoServidor);
        } catch (errJson) {
            console.error("❌ EL SERVIDOR NO DEVOLVIÓ JSON VÁLIDO. Mira el texto arriba.");
            return;
        }

        console.log("=== JSON DECODIFICADO ===", res);

        if (!res.success) {
            console.error("❌ El PHP devolvió success: false ->", res.message);
            return;
        }

        // 4. Si todo está bien, actualizamos el Select2
        $select.empty();
        $select.append(new Option("Escribe SKU o nombre...", "", true, true));

        if (Array.isArray(res.data)) {
            res.data.forEach(pr => {
                const option = new Option(`[${pr.sku}] ${pr.nombre}`, pr.producto_id, false, false);

                $(option).attr({
                    'data-nombre': pr.nombre || '',
                    'data-medidas': JSON.stringify(pr.medidas_adicionales || []),
                    'data-sku': pr.sku || '',
                    'data-um': pr.unidad_medida || '',
                    'data-ur': pr.unidad_reporte || '',
                    'data-premin': pr.precio_minorista || 0,
                    'data-premat': pr.precio_mayorista || 0,
                    'data-predis': pr.precio_distribuidor || 0,
                    'data-factor': pr.factor_conversion || 1,
                    'data-stock': (pr.stock) || 1
                });

                $select.append(option);
            });
        }

        // Notificar a Select2 del cambio
        $select.trigger('change.select2');
    }
    const URL_CONTROLADOR_EDITAR = '/myvet/app/controllers/cotizacionesController.php';
    let total_inicial = 0;
    let cliente_nombre = '';
    let dataEdicion = null;
    let recalculandoFilaEditar = false;

    // Inicializar Select2 en la página
    $('.select2-pagina').select2({
        theme: 'bootstrap-5'
    });

    $(document).ready(async function() {
        try {

            await cargarVendedores3();
        } catch (error) {
            console.error("Error en inicialización:", error);
        }
    });

    // =====================================================
    // CARGAR VENDEDORES
    // =====================================================
   async function cargarVendedores3(vendedor_id = null) {
    const almacenId = $('#almacen_id_editar').val();
    const select = document.getElementById('select-vendedor1');
    if (!select) return;

    // ID del usuario desde la sesión de PHP
    const idUsuarioSesion = "<?= $_SESSION['usuario_id'] ?? '' ?>";

    try {
        const url = `/myvet/app/controllers/accesoController.php?action=obtenerUsuarios&almacen_id=${almacenId}`;
        const respuesta = await fetch(url);

        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

        const resultado = await respuesta.json();

        if (resultado.success && Array.isArray(resultado.data)) {
            resultado.data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.id;
                opcion.textContent = `${usuario.nombre}`;
                select.appendChild(opcion);
            });

            // Prioridad: 
            // 1. vendedor_id (si se recibe explícitamente en la función)
            // 2. idUsuarioSesion (ID guardado en la sesión de PHP)
            const valorASeleccionar = vendedor_id || idUsuarioSesion;

            if (valorASeleccionar) {
                $('#select-vendedor1').val(valorASeleccionar).trigger('change.select2');
            }
        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
        }
    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarVendedores3:', error);
    }
}
async function cargarClientes() {
    console.log("cargo clientes");
    
    // Obtenemos el ID del almacén actual
    const almacenId = $('#almacen_id_editar').val();
    const select = document.getElementById('cliente_id_editar');
    if (!select) return;

    // Limpiamos el select antes de poblarlo
    select.innerHTML = '<option value="">-- Seleccione un cliente --</option>';

    try {
        const url = `/myvet/app/controllers/accesoController.php?action=obtenerClientes`;
        const respuesta = await fetch(url);

        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

        const resultado = await respuesta.json();
        console.log(resultado);

        if (resultado.success && Array.isArray(resultado.data)) {
            
            // FILTRADO:
            // 1. Conserva clientes cuyo nombre NO contenga "público en general".
            // 2. Para "público en general", solo conserva el que coincida con el almacenId actual.
            const clientesFiltrados = resultado.data.filter(cliente => {
                const nombreNorm = cliente.nombre_comercial.toLowerCase().trim();
                const esPublicoGeneral = nombreNorm.includes('publico en general') || nombreNorm.includes('público en general');

                if (esPublicoGeneral) {
                    return cliente.almacen_id == almacenId;
                }

                return true;
            });

            // Llenamos el select con la lista filtrada
            clientesFiltrados.forEach(cliente => {
                const opcion = document.createElement('option');
                opcion.value = cliente.id;
                opcion.textContent = `${cliente.nombre_comercial}`;
                select.appendChild(opcion);
            });

            // SELECCIÓN AUTOMÁTICA DEL PRIMER REGISTRO ENCONTRADO
            if (clientesFiltrados.length > 0) {
                const primerClienteId = clientesFiltrados[0].id;

                // Soporte nativo y compatibilidad con Select2/jQuery
                $('#cliente_id_editar').val(primerClienteId).trigger('change');
            }

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
        }
    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarClientes:', error);
    }
}
    // =====================================================
    // CALCULAR TOTAL EDITAR
    // =====================================================
    function calcularTotalSolEditar(input) {
        if (recalculandoFilaEditar) return;
        recalculandoFilaEditar = true;

        try {
            const fila = input ? input.closest('tr') : null;
            if (fila) {
                const cantidad = parseFloat(fila.querySelector('.cantidad-editar')?.value) || 0;
                const precioUnitarioOriginal = parseFloat(fila.querySelector('.precio-unitario-editar')?.value) || 0;

                const precioTotal = cantidad * precioUnitarioOriginal;
                fila.querySelector('.precio-total-editar').value = precioTotal.toFixed(2);
            }

            // SUMA GENERAL
            let totalCompraEditar = 0;
            document.querySelectorAll('#tablaDetalleEditar .precio-total-editar').forEach(el => {
                totalCompraEditar += parseFloat(el.value) || 0;
            });

            document.getElementById('costoTotalCompraEditar').textContent = totalCompraEditar.toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN'
            });
            document.getElementById('totalCotizacionEditar').value = totalCompraEditar.toFixed(2);
          calcularCambio();

        } finally {
            recalculandoFilaEditar = false;
        }
    }

    // =====================================================
    // RECARGAR PRODUCTOS
    // =====================================================
    // =====================================================
    // EVENTO SELECT2: AGREGAR PRODUCTO
    // =====================================================
    $('#buscadorProductosEditar').on('select2:select', function(e) {
        const d = e.params.data.element.dataset;
        const id = $(this).val();

        if ($(`#filaEditar-${id}`).length) {
            Swal.fire('Aviso', 'El producto ya está en la lista', 'info');
            return;
        }

        $('#emptyStateEditar').addClass('d-none');
        const medidas = JSON.parse(d.medidas || '[]');

        let opcionesUnidad = ``;
        medidas.forEach(m => {
            opcionesUnidad += `
                <option value="${m.id}" data-equivalencia="${m.equivalencia}" data-medida-id="${m.id}">
                    ${m.nombre}
                </option>`;
        });

        const preMin = parseFloat(d.premin) || 0;
        const preMat = parseFloat(d.premat) || 0;
        const preDis = parseFloat(d.predis) || 0;
        const factor = parseFloat(d.factor) || 1;

        $('#tablaDetalleEditar tbody').append(`
            <tr id="filaEditar-${id}">
            
                <td >
                    <b>${d.nombre}</b><br>
                    <small class="text-body-secondary">${d.sku}</small>
                </td>
<td class="ps-4">
<input 
                        type="hidden"
                        name="itemsEditar[${id}][exis]"
                        class=" cantidad-exis" 
                      
                        value="${d.stock}"
                        min="0.01"
                        
                       required >
<spam   
                     
                      
                       >${d.stock/d.factor}</spam>
                   <b> ${d.ur}</b>
                    
                </td>
                <td>
                    <input 
                        type="number"
                        name="itemsEditar[${id}][cant]"
                        class="form-control cantidad-editar"
                        step="0.01"
                        value="1"
                        min="0.01"
                        required
                        oninput="actualizarEquivalencia(this);calcularTotalSolEditar(this)" required>
                    <input 
                        type="hidden"
                        name="itemsEditar[${id}][equivalencia]"
                        class="form-control equivalencia"
                        value="1">
                </td>

                <td>
                    <select 
                        name="itemsEditar[${id}][unidad]" 
                        class="form-select unidad-select-editar"
                        onchange="actualizarEquivalencia(this);calcularPrecioSugeridoEditar(this)">
                       
                        ${opcionesUnidad}
                    </select>
                </td>
                
                <td>
                    <select 
                        name="itemsEditar[${id}][tipoPrecio]" 
                        class="form-select tipoPrecio-select-editar"
                        onchange="calcularPrecioSugeridoEditar(this)" required>
                        <option value="seleccionar" data-precio="0">Seleccione</option>
                        <option value="minorista" data-precio="${preMin}">Min $${(preMin * factor).toFixed(2)} x ${d.ur}</option>
                        <option value="mayorista" data-precio="${preMat}">May $${(preMat * factor).toFixed(2)} x ${d.ur}</option>
                        <option value="distribuidor" data-precio="${preDis}">Dis $${(preDis * factor).toFixed(2)} x ${d.ur}</option>
                    </select>
                </td>

                <td>
                    <input 
                        type="number"
                        lang="en-US"
                        name="itemsEditar[${id}][precioUnitario]"
                        class="form-control precio-unitario-editar"
                        step="0.01"
                        min="0.01"
                        placeholder="0.00"
                        required
                        oninput="calcularTotalSolEditar(this)">
                </td>

                <td style="min-width:140px;">
                    <input 
                        type="number"
                        lang="en-US"
                        name="itemsEditar[${id}][precio]"
                        class="form-control precio-total-editar fw-bold text-success "
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                        oninput="calcularTotalSolEditar(this)"
                        style="font-size:1.1rem; height:40px;">
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-link text-danger p-0" onclick="quitarFilaEditar('${id}')">
                        <i class="bi bi-trash fs-5"></i>
                    </button>
                </td>
            </tr>
            `);

        $(this).val(null).trigger('change');
    });

    function validarExistencias(fila, cantidad, equivalencia, existencias) {

        const maximoPermitido = existencias / equivalencia;
        const soli = cantidad * equivalencia;
        console.log(cantidad, equivalencia, existencias);
        console.log(soli, maximoPermitido);

        if (soli > existencias) {

            fila.find('.cantidad-editar').val(maximoPermitido.toFixed(4));

            Swal.fire({
                icon: 'warning',
                title: 'Existencias insuficientes',
                text: `Solo hay ${maximoPermitido.toFixed(4)} unidades disponibles.`
            });

            return maximoPermitido;
        }

        return cantidad;
    }

    function actualizarEquivalencia(input) {

        const fila = $(input).closest('tr');

        const cantidad = parseFloat(fila.find('.cantidad-editar').val()) || 0;
        const existencias = parseFloat(fila.find('.cantidad-exis').val()) || 0;

        const unidad = fila.find('.unidad-select-editar').val();

        // Todavía no se eligió una unidad
        if (!unidad || unidad == "0") {
            return;
        }

        let equivalencia = parseFloat(
            fila.find('.unidad-select-editar option:selected').data('equivalencia')
        );

        if (isNaN(equivalencia) || equivalencia <= 0) {
            return;
        }

        // Obtener la equivalencia inversa

        equivalencia = 1 / equivalencia;

        // Solo redondear si es >= 1 y está muy cerca de un entero
        if (
            equivalencia >= 1 &&
            Math.abs(equivalencia - Math.round(equivalencia)) <= 0.00001
        ) {
            equivalencia = Math.round(equivalencia);
        }

        // Si está prácticamente en un entero, redondearlo

        // Guardar el valor que se enviará
        fila.find('.equivalencia').val(equivalencia);

        validarExistencias(
            fila,
            cantidad,
            equivalencia,
            existencias
        );

        calcularTotalSolEditar(input);
    }

    function calcularPrecioSugeridoEditar(select) {
        const fila = select.closest('tr');
        const inputPrecio = fila.querySelector('.precio-unitario-editar');
        const unidadSelect = fila.querySelector('.unidad-select-editar');
        const tipoSelect = fila.querySelector('.tipoPrecio-select-editar');
        const inputtotal = fila.querySelector('.precio-total-editar');

        const unidadOption = unidadSelect.options[unidadSelect.selectedIndex];
        const tipoOption = tipoSelect.options[tipoSelect.selectedIndex];

        const equivalencia = Number(unidadOption?.dataset.equivalencia || 1);
        const precioBase = Number(tipoOption?.dataset.precio || 0);

        const sugerido = equivalencia > 0 ? (precioBase / equivalencia) : precioBase;
        inputPrecio.value = sugerido.toFixed(2);

        const cantidad = parseFloat(fila.querySelector('.cantidad-editar')?.value) || 0;
        inputtotal.value = (sugerido * cantidad).toFixed(2);

        calcularTotalSolEditar(null);
    }

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('precio-unitario-editar')) {
            e.target.dataset.editado = "1";
        }
    });
    document.addEventListener('DOMContentLoaded', () => {
    // 1. Cargas iniciales
    recargarProductosEditar();
            cargarPersonalDespacho();
            cargarClientes();
            vaciarTablaEditar();
    // 2. Manejo del select de almacén
    const selectAlmacen = document.getElementById('almacen_id_editar');

    if (selectAlmacen) {
        selectAlmacen.addEventListener('change', function() {
            const almacenId = this.value;
            
            if (!almacenId) {
                console.log('Se deseleccionó el almacén');
                return;
            }

            console.log(`Almacén cambiado a ID: ${almacenId} - ${this.options[this.selectedIndex].text}`);
            
            // Ejecución de funciones
            recargarProductosEditar();
            cargarPersonalDespacho();
            cargarClientes();
            vaciarTablaEditar();
        });
    }
});
    function verificarMetodoPago(metodo) {
         
    


        const contenedor = document.getElementById('contenedorReferencia');
        const contenedorcambio = document.getElementById('contenedor_cambio');
        const input = document.getElementById('inputReferencia');

        if (!contenedor || !input) return;

        if (metodo === 'Tarjeta' || metodo === 'Transferencia') {
            if (metodo === 'Tarjeta' )
            {
                 contenedor.style.display = 'block';
            input.required = false;
            const totalVenta = parseFloat($('#totalCotizacionEditar').val()) || 0;
    $('#monto_pagar').val(totalVenta);
      contenedorcambio.classList.add('d-none');
      calcularCambio();

            }
            contenedor.style.display = 'block';
            input.required = true;
            const totalVenta = parseFloat($('#totalCotizacionEditar').val()) || 0;
    $('#monto_pagar').val(totalVenta);
      contenedorcambio.classList.add('d-none');
      calcularCambio();
      

        } else {
            contenedor.style.display = 'none';
            input.required = false;
            input.value = '';
            contenedorcambio.classList.remove('d-none');
            calcularCambio();
        }
    }
    // =====================================================
    // GUARDAR ACTUALIZACIÓN (SUBMIT FORM)
    // =====================================================
    // =====================================================
   $('#formEditarSolicitud').on('submit', async function(e) {
    e.preventDefault();

    if (!$('#tablaDetalleEditar tbody tr').length) {
        Swal.fire('Error', 'Agregue al menos un producto a la venta', 'warning');
        return;
    }

    const $btnFinalizar = $(this).find('button[type="submit"]');
    $btnFinalizar.prop('disabled', true);

    const totalVenta = parseFloat($('#totalCotizacionEditar').val()) || 0;
    const montoPagado = parseFloat($('#monto_pagar').val()) || 0; // Dinero real recibido para cobro/cambio

    // =========================================================
    // VALIDACIÓN: Verificar que el dinero recibido cubra el total
    // =========================================================
    if (montoPagado < totalVenta) {
        Swal.fire({
            title: 'Monto insuficiente',
            text: `No es posible continuar por el monto ingresado ($${montoPagado.toFixed(2)}). Debe ser igual o mayor al total ($${totalVenta.toFixed(2)}).`,
            icon: 'warning',
            customClass: {
                popup: 'rounded-4 bg-body text-body'
            }
        });
        $btnFinalizar.prop('disabled', false); // Rehabilitar el botón
        return; // Detener la ejecución
    }

    const payload = {
        accion: 'guardar_venta',
        id_cliente: $('#cliente_id_editar').val(),
        id_vendedor: $('#select-vendedor1').val(),
        monto_pagado: totalVenta, // Queda asignado exactamente como lo tenías
        monto_usado_favor: 0,
        total_venta: totalVenta,
        almacen_id: $('#almacen_id_editar').val(),
        metodo_pago: $('#metodo_pago').val(),
        referencia: $('#inputReferencia').val(),
        observaciones: $('#obsVenta').val(),
        usar_saldo_favor: 0,
        chofer_id: parseInt(document.getElementById('patio_chofer_id').value) || 0,
        tripulantes: $('#patio_tripulantes').val() || [], // Array de IDs de ayudantes
        observaciones_entrega: document.querySelector('textarea[name="observaciones"]')?.value || 'Entrega en Patio',
        carrito: []
    };

    $('#tablaDetalleEditar tbody tr').each(function() {
        const fila = $(this);
        const id = fila.attr('id').replace('filaEditar-', '');

        const unidadSelect = fila.find('.unidad-select-editar option:selected');
        const tipoPrecioSelect = fila.find('.tipoPrecio-select-editar option:selected');
        let cantidadInicial = parseFloat(fila.find('.cantidad-editar').val()) || 0;
        let equivalencia = parseFloat(fila.find('.equivalencia').val()) || 1;

        let cantidadT = (cantidadInicial * equivalencia);
        let cantidadTotal = (cantidadT % 1 > 0) ? cantidadT.toFixed(2) : cantidadT;

        payload.carrito.push({
            producto_id: id,
            almacen_id: $('#almacen_id_editar').val(),
            cantidad: cantidadTotal,
            unidad_base: unidadSelect.val(),
            entrega_hoy: cantidadTotal,               
            idunidadMedida: unidadSelect.data('medida-id'),
            unidadEquivalencia: unidadSelect.data('equivalencia'),
            tipo_precio: tipoPrecioSelect.val(),
            precio_unitario: fila.find('.precio-unitario-editar').val(),
            subtotal: fila.find('.precio-total-editar').val()
        });
    });

    Swal.fire({
        title: 'Actualizando venta...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const resp = await fetch(`/myvet/app/controllers/cajaRapidaController.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const res = await resp.json();

        if (res.status === 'success') {
            const tieneDeuda = payload.monto_pagado < payload.total_venta;
            const esEntregaTotal = (res.total_entregado ?? 0) >= (res.total_pedido ?? 0);
            const iconoFinal = esEntregaTotal ? 'success' : 'warning';

            let htmlExtra = `<p class="mb-2">Folio: <span class="badge bg-body-secondary text-body border">${res.folio || 'N/A'}</span></p>`;

            if (tieneDeuda) {
                htmlExtra += `
                <div class="alert alert-danger py-1 px-2  mb-2" style="font-size:0.75rem; border-radius:10px;">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> Saldo pendiente registrado en cuenta
                </div>`;
            }

            // Calcular cambio para mostrarlo en el SweetAlert final de éxito si aplica
            const cambio = montoPagado - totalVenta;
            let htmlCambio = cambio > 0 
                ? `<div class="alert bg-success-subtle text-success  fw-bold py-2 mb-3" style="border-radius:12px; font-size:1rem;">
                    Cambio a entregar: $${cambio.toFixed(2)}
                   </div>` 
                : '';

            Swal.fire({
                title: esEntregaTotal ? '¡Venta Exitosa!' : 'Entrega Parcial Registrada',
                html: `
                <div class="alert bg-body-tertiary  small text-start py-2 mb-2" style="border-radius:12px;">
                    ${res.message || 'Operación realizada correctamente.'}
                </div>
                ${htmlCambio}
                ${htmlExtra}
                <p class="text-body-secondary small mb-0">¿Deseas imprimir el comprobante?</p>
            `,
                icon: iconoFinal,
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-receipt"></i> Con Precios',
                denyButtonText: '<i class="bi bi-receipt"></i> Ticket Formal',
                cancelButtonText: 'Cerrar',
                confirmButtonColor: '#34c759',
                denyButtonColor: '#5856d6',
                customClass: {
                    popup: 'rounded-4  shadow-lg bg-body text-body' 
                }
            }).then((result) => {
                let url = '';
                if (result.isConfirmed) {
                    url = `/myvet/app/backend/ventas/ticket_venta.php?id=${res.id_venta}`;
                } else if (result.isDenied) {
                    url = `/myvet/app/backend/ventas/ticketFormal.php?id=${res.id_venta}`;
                }

                if (url !== '') window.open(url, '_blank');
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: res.message || 'Error desconocido',
                icon: 'error',
                customClass: {
                    popup: 'rounded-4 bg-body text-body'
                }
            });
            $btnFinalizar.prop('disabled', false);
        }

    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Fallo de conexión con el servidor', 'error');
        $btnFinalizar.prop('disabled', false);
    }
}); // ELIMINAR FILA
    // =====================================================
    function quitarFilaEditar(id) {
        $(`#filaEditar-${id}`).remove();

        if (!$('#tablaDetalleEditar tbody tr').length) {
            $('#emptyStateEditar').removeClass('d-none');
        }

        calcularTotalSolEditar(null);
    }

    // =====================================================
    // GESTIÓN DE SALDOS Y EGRESOS
    // =====================================================
    function mostrarModalSaldo() {
        let total = parseFloat($('#totalCotizacionEditar').val()) || 0;
        const diferencia = total_inicial - total;

        $('#txtTotalOriginal').text(total_inicial.toFixed(2));
        $('#txtNuevoTotal').text(total.toFixed(2));
        $('#txtDiferencia').text(diferencia.toFixed(2));

        const elemModal = document.getElementById('modalSaldoFavor');
        if (elemModal) {
            new bootstrap.Modal(elemModal).show();
        }
    }

    $('#btnSaldoFavor').click(async function() {
        const elemModal = document.getElementById('modalSaldoFavor');
        if (elemModal) {
            const modalInst = bootstrap.Modal.getInstance(elemModal);
            if (modalInst) modalInst.hide();
        }

        try {
            const id = $('#editar_venta_id').val();
            const cliente = $('#cliente_id_editar').val();
            const total = parseFloat($('#totalCotizacionEditar').val()) || 0;
            const diferencia = total_inicial - total;

            const fd = new FormData();
            fd.append('venta_id', id);
            fd.append('cliente_id', cliente);
            fd.append('diferencia', diferencia);

            const resp = await fetch(
                '/myvet/app/controllers/editarVentaController.php?action=guardarComoABono', {
                    method: 'POST',
                    body: fd
                });

            const res = await resp.json();

            if (!res.success && res.status !== 'success') {
                throw new Error(res.message);
            }

            await Swal.fire({
                icon: 'success',
                title: 'Saldo aplicado',
                text: res.message || 'El saldo a favor fue registrado correctamente.'
            });

            window.location.reload();

        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: e.message
            });
        }
    });

    async function guardarComoGastoSalidadeDinero() {
        try {
            const respFolio = await fetch(
                '/myvet/app/controllers/egresosController.php?action=getSiguienteFolioGasto');
            const folioRes = await respFolio.json();

            if (!folioRes.success) {
                throw new Error("No fue posible obtener el folio.");
            }

            const folio = folioRes.folio;
            const id = $('#editar_venta_id').val();
            const total = parseFloat($('#totalCotizacionEditar').val()) || 0;
            const diferencia = total_inicial - total;
            const observaciones = `SALIDA DE DINERO POR EDICIÓN DE VENTA ${id}`;
            const fecha = new Date().toISOString().split('T')[0];

            const fd = new FormData();
            fd.append('folio', folio);
            fd.append('monto', diferencia);
            fd.append('fecha', fecha);
            fd.append('observaciones', observaciones);
            fd.append('venta_id', id);

            const resp = await fetch('/myvet/app/controllers/egresosController.php?action=guardarSalidaDinero', {
                method: 'POST',
                body: fd
            });

            const res = await resp.json();

            if (res.success || res.status === 'success') {
                await Swal.fire('Éxito', 'Salida de dinero registrada correctamente.', 'success');
                window.location.reload();
            } else {
                throw new Error(res.message || 'Error al procesar el egreso');
            }

        } catch (e) {
            Swal.fire('Error', e.message, 'error');
        }
    }

    async function cargarPersonalDespacho() {

 const alm = $('#almacen_id_editar').val();
        const rutaControlador =
            '/myvet/app/controllers/cajaRapidaController.php';

        const selectC = $('#patio_chofer_id');

        const selectT = $('#patio_tripulantes');

        if (!selectC.length) return;

        selectC.empty()
            .append('<option value="">Cargando personal...</option>');

        selectT.empty();

        try {

            const response = await fetch(
                `${rutaControlador}?action=get_recursos_sucursal&almacen_id=${alm}`
            );

            const res = await response.json();
            console.log(res);
            if (res.success && res.choferes) {

                selectC.empty();
                selectT.empty();

                // =====================================================
                // 🔥 CARGAR PERSONAL
                // =====================================================

                res.choferes.forEach((persona, index) => {

                    const option =
                        `<option value="${persona.id}">
                        ${persona.nombre}
                    </option>`;

                    selectC.append(option);

                    selectT.append(option);

                });

                // =====================================================
                // 🔥 AUTOSELECCIONAR PRIMER CHOFER
                // =====================================================

                if (res.choferes.length > 0) {

                    const primerId =
                        res.choferes[0].id;

                    selectC
                        .val(primerId)
                        .trigger('change');
                }

                // =====================================================
                // 🔥 FILTRAR TRIPULANTES
                // =====================================================

                selectC.off('change')
                    .on('change', function() {

                        const seleccionadoId =
                            $(this).val();

                        // 🔥 reset
                        selectT.find('option')
                            .show()
                            .prop('disabled', false);

                        if (seleccionadoId) {

                            // 🔥 ocultar encargado
                            selectT.find(
                                    `option[value="${seleccionadoId}"]`
                                )
                                .hide()
                                .prop('disabled', true);

                            // 🔥 quitar selección si coincide
                            if (
                                selectT.val() == seleccionadoId
                            ) {

                                selectT.val(null)
                                    .trigger('change');
                            }
                        }

                    });

                // 🔥 ejecutar filtro inicial
                selectC.trigger('change');

                console.log(
                    `Personal cargado para almacén ${alm}`
                );

            } else {

                throw new Error(
                    res.message ||
                    'No se encontró personal'
                );
            }

        } catch (e) {

            console.error(
                "Error en cargarPersonalDespacho:",
                e
            );

            selectC.empty()
                .append(
                    '<option value="">Error al cargar personal</option>'
                );
        }
    }
function calcularCambio() {
    let metodo = $('#metodo_pago').val();
    // Convertimos ambos valores explícitamente a número
    const totalVenta = parseFloat($('#totalCotizacionEditar').val()) || 0;
    const efectivo = parseFloat(document.getElementById('monto_pagar').value) || 0;
    
    const contenedor = document.getElementById('contenedor_cambio');
    const textoCambio = document.getElementById('texto_cambio');
    let boton = $('#botonEnviar');

    if (metodo === 'Efectivo') {
        contenedor.classList.remove('d-none');

        // Evaluamos si el efectivo entregado cubre el total
        const cambio = efectivo - totalVenta;

        if (efectivo <= 0 || cambio < 0) {
            // Si no ha ingresado dinero o le falta
            textoCambio.classList.remove('text-success');
            textoCambio.classList.add('text-danger');
            
            if (efectivo <= 0) {
                textoCambio.innerText = `Ingrese el monto a pagar`;
            } else {
                textoCambio.innerText = `Faltan: $${Math.abs(cambio).toFixed(2)}`;
            }
            
            // Ocultar botón por pago insuficiente
            boton.addClass('d-none');
        } else {
            // Si el cambio es 0 o mayor (pago completo)
            textoCambio.classList.remove('text-danger');
            textoCambio.classList.add('text-success');
            textoCambio.innerText = `Cambio: $${cambio.toFixed(2)}`;
            
            // Mostrar botón
            boton.removeClass('d-none');
        }
    } else {
        // Si es otro método de pago (Tarjeta, Transferencia, etc.)
        contenedor.classList.add('d-none');
        document.getElementById('monto_pagar').value=totalVenta;
        // Nos aseguramos de que el botón esté visible
        boton.removeClass('d-none');
    }
}
    </script>

</body>

</html>