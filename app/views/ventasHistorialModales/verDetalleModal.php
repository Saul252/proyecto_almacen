<div class="modal fade" id="modalDetalle" tabindex="-1">
    <!-- Se añadió estilo para forzar el 70% del ancho de la pantalla (70vw) -->
    <div class="modal-dialog modal-dialog-centered" style="max-width: 90vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Gestión de Venta: <span id="spanFolio"></span></h6>
                <span id="IdFolio" style="visibility: hidden;"></span>
                <span id="Almacen_id" style="visibility: hidden;"></span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-3 border-end p-4">
                        <div class="d-flex flex-column gap-2 mb-4">

                            <!-- Cliente -->
                            <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                <small class="d-block text-body-secondary fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-person me-1 text-primary"></i> Cliente
                                </small>
                                <span id="detCliente" class="fw-bold card-title-text small d-block text-truncate">--</span>
                            </div>

                            <!-- Almacén -->
                            <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                <small class="d-block text-body-secondary fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-box-seam me-1 text-primary"></i> Almacén
                                </small>
                                <span id="detAlmacen" class="fw-bold card-title-text small d-block text-truncate">--</span>
                            </div>

                            <!-- Vendedor -->
                            <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                <small class="d-block text-body-secondary fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-person-badge me-1 text-primary"></i> Vendedor
                                </small>
                                <span id="detVendedor" class="fw-bold card-title-text small d-block text-truncate">--</span>
                            </div>

                            <!-- Folio Factura -->
                            <div class="p-2 px-3 rounded-3 bg-body-tertiary border border-light-subtle">
                                <small class="d-block text-body-secondary fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-receipt me-1 text-primary"></i> Folio / Factura
                                </small>
                                <span id="folioFactura" class="fw-bold card-title-text small d-block text-truncate">--</span>
                            </div>

                        </div>

                        <div class="mb-4 p-2 border rounded shadow-sm text-center">
                            <div class="mb-2 pb-2 border-bottom">
                                <span class="d-block small text-body-secondary text-uppercase fw-bold">Total de Venta</span>
                                <span id="detTotalLabel" class="h6 fw-bold card-title-text">$0.00</span>
                            </div>

                            <div>
                                <span class="d-block small text-body-secondary text-uppercase fw-bold">Saldo Pendiente</span>
                                <span id="detSaldoLabel" class="h5 fw-bold text-danger">$0.00</span>
                            </div>
                        </div>

                        <?php if($_SESSION['rol_id']==1||$_SESSION['rol_id']==2): ?>
                        <div id="contenedorBoton">
                            <button id="btnHabilitar" class="btn btn-action w-100 mb-2 py-2 fw-bold" onclick="abrirModalDespachoVentaTotal($('#Almacen_id').text(), $('#IdFolio').text())">
                                Nueva Entrega
                            </button>
                        </div>
                        <?php endif; ?>

                        <div class="text-end pe-3"></div>
                    </div>

                    <div class="col-md-9 p-4">
                        <div class="table-responsive border rounded mb-3" style="max-height: 180px;">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small text-uppercase">
                                        <th>Producto</th>
                                        <th class="text-center">Venta</th>
                                        <th class="text-center">Surtido</th>
                                        <th class="text-center text-danger">Falta</th>
                                        <th class="text-center col-input d-none">Entrega</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyDetalle" class="small"></tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="small fw-bold text-uppercase text-body-secondary">
                                    <i class="bi bi-truck"></i> Historial de Entregas
                                </h6>
                                <div class="table-responsive border rounded" style="max-height: 180px;">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="small text-uppercase">
                                                <th>Fecha</th>
                                                <th>Responsable</th>
                                                <th>Producto</th>
                                                <th class="text-center">Cant</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyHistorial" class="small"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="small fw-bold text-uppercase text-body-secondary">
                                    <i class="bi bi-cash-stack"></i> Historial de Pagos
                                </h6>
                                <div class="table-responsive border rounded" style="max-height: 180px;">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="small text-uppercase">
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                                <th>Método</th>
                                                <th>Referencia</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyPagos" class="small"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <h6 class="small fw-bold text-uppercase text-body-secondary">
                                    <i class="bi bi-map"></i> Repartos
                                </h6>

                                <div class="table-responsive border rounded" style="max-height: 220px;">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="small text-uppercase">
                                                <th># Reparto</th>
                                                <th>Fecha Entrega</th>
                                                <th>Direccion</th>
                                                <th class="text-center">Ruta</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyRepartos" class="small"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <h4 id="cancelado" class="fw-bold text-danger padding-top-3 mb-3"></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>