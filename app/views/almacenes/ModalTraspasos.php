 <div class="modal fade" id="modalTraspaso" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-arrow-left-right"></i> Nuevo Traspaso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formTraspaso" action="/myvet/app/backend/almacen/procesar_traspaso.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">1. Almacén de Origen</label>
                            <select name="almacen_origen_id" id="origen_id" class="form-select border-primary" required
                                onchange="filtrarProductosPorOrigen()">
                                <option value="">Seleccione donde sale la mercancía...</option>
                                <?php foreach($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>"><?= htmlspecialchars($alm['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">2. Producto a mover</label>
                            <select name="producto_id" id="traspaso_producto" class="form-select" required disabled
                                onchange="actualizarMaximo()">
                                <option value="">Primero seleccione un origen...</option>
                            </select>
                            <div id="info_stock" class="form-text text-primary fw-bold"></div>
                        </div>
 <div class="mb-3">
                            <label class="form-label fw-bold">Seleccione lote</label>
                            <select name="lote_id" id="lote_id" class="form-select" 
                              >
                                <option value="">seleccione lote (opcional)...</option>
                            </select>
                            <div id="info_stock" class="form-text text-primary fw-bold"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">3. Almacén Destino</label>
                                <select name="almacen_destino_id" id="destino_id" class="form-select" required>
                                    <option value="">¿A dónde va la mercancía?</option>
                                    <?php foreach($todosLosAlmacenes as $alm_dest): ?>
                                    <?php if ($almacen_usuario > 0 && $alm_dest['id'] == $almacen_usuario) continue; ?>
                                    <option value="<?= $alm_dest['id'] ?>"><?= htmlspecialchars($alm_dest['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class=" row mb-3">
                                <label class="form-label fw-bold">4. Cantidad a Traspasar</label>
                                <div class=" col-md-6 mb-3" > 
                                    <input type="number" id="traspaso_factor_input" class="form-control text-center"
                                        placeholder="0" min="0" step="0.01">
                                    <span class="input-group-text" id="label_unidad_reporte"
                                        style="min-width: 80px;">Unid.</span>
</div>
                                   <div id="bloque_traspaso" class="col-md-6 mb-3">
    <input type="number" id="traspaso_piezas_input" class="form-control text-center"
        placeholder="0" min="0" step=".01">

    <span class="input-group-text" id="label_unidad_medida"
        style="min-width: 80px;">Unid.</span>
</div>
                                

                                <input type="hidden" name="cantidad" id="cantidad_traspaso_final" required>

                                <div id="resumen_conversion"
                                    class="mt-2 p-2 rounded border border-subtle border-start border-4 border-primary"
                                    style="display:none; font-size: 0.9rem;">
                                    <strong>Movimiento total:</strong> <span id="txt_total_pzas">0</span> <span id="unidadF"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarTraspaso">Solicitar
                            Movimiento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalTraspasosGestion" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Gestión de Traspasos entre Almacenes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if($_SESSION['rol_id'] == 1): ?>
                    <div class="row mb-4 border-subtle p-3 rounded border">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ver movimientos del Almacén:</label>
                            <select id="admin_filtro_almacen" class="form-select" onchange="cargarTraspasos()">
                                <option value="">Seleccione un almacén para autorizar...</option>
                                <?php foreach($almacenes as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pills-arribos">
                                📥 Arribos (Por Recibir)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-envios">
                                📤 Envíos (En Tránsito)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-arribos">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Producto</th>
                                            <th>Cant.</th>
                                            <th>Origen</th>
                                            <th>Enviado por</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contenedor-arribos">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-envios">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Producto</th>
                                            <th>Cant.</th>
                                            <th>Destino</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contenedor-envios">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="/myvet/app/backend/js/informacion_productos_envio.js"></script>
    <script src="/myvet/app/backend/js/cargar_traspasos.js"></script>
    <script src="/myvet/app/backend/js/aceptar_arribo.js"></script>
    
   