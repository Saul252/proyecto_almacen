<div class="modal fade" id="modalMovimiento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card  p-2">
            <div class="modal-header ">
                <h5 class="fw-bold m-0">Registrar Movimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3 text-center">
                    <div class="col-4">
                        <div class="p-2 rounded-4 bg-light border">
                            <small class="text-body-secondary d-block text-uppercase" style="font-size: 9px; font-weight: 800;">Efectivo</small>
                            <span id="display_saldo_efectivo" class="fw-bold text-dark">$0.00</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-4 bg-light border">
                            <small class="text-body-secondary d-block text-uppercase" style="font-size: 9px; font-weight: 800;">Tarjeta</small>
                            <span id="display_saldo_tarjeta" class="fw-bold text-dark">$0.00</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded-4 bg-light border">
                            <small class="text-body-secondary d-block text-uppercase" style="font-size: 9px; font-weight: 800;">Transf.</small>
                            <span id="display_saldo_transferencia" class="fw-bold text-dark">$0.00</span>
                        </div>
                    </div>
                </div>

                <form id="formNuevoMovimiento">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Sucursal Origen</label>
                            <select name="almacen_id" id="sel_almacen_origen" class="form-select ios-input" required>
                                <?php if($esAdmin): ?>
                                    <option value="0">🌐 Todas (Administrador)</option>
                                <?php endif; ?>
                                <?php foreach($listaAlmacenes as $alm): ?>
                                    <option value="<?= $alm['id'] ?>"><?= $alm['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="text-xs fw-bold text-body-secondary text-uppercase m-0">Categoría</label>
                                <span id="badge_tipo_operacion" class="badge bg-light text-dark" style="font-size: 10px; display: none;"></span>
                            </div>
                            <select name="categoria_id" id="sel_categoria" class="form-select ios-input" required>
                                <option value="">Seleccione una categoría...</option>
                                <?php foreach($categoriasCapital as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" data-tipo="<?= $cat['tipo_operacion'] ?>">
                                        <?= $cat['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select> 
                        </div>

                        <div class="col-6">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Fecha</label>
                            <input type="date" name="fecha_movimiento" id="fecha_movimiento" 
                                   class="form-control ios-input" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-6" id="col_metodo_pago">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Método</label>
                            <select name="metodo_pago" id="sel_metodo_pago" class="form-select ios-input" required>
                                <option value="efectivo">💵 Efectivo</option>
                                <option value="tarjeta">💳 Tarjeta</option>
                                <option value="transferencia">🏛️ Transferencia</option>
                            </select>
                        </div>

                        <div id="col_destino_almacen" class="col-12 d-none">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Almacén Destino</label>
                            <select name="almacen_destino_id" class="form-select ios-input">
                                <option value="">Seleccione destino...</option>
                                <?php foreach($listaAlmacenes as $alm): ?>
                                    <option value="<?= $alm['id'] ?>"><?= $alm['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="col_destino_caja" class="col-12 d-none">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Caja Fuerte Destino</label>
                            <select name="caja_fuerte_id" id="sel_caja_fuerte" class="form-select ios-input"></select>
                        </div>
                        <div id="col_destino_banco" class="col-12 d-none">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Banco Destino</label>
                            <select name="banco_id" id="sel_banco" class="form-select ios-input"></select>
                        </div>

                        <div class="col-md-12">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Monto del Movimiento</label>
                            <input type="number" step="0.01" name="monto" class="form-control ios-input fw-bold" placeholder="0.00" required>
                        </div>

                        <div class="col-12">
                            <label class="text-xs fw-bold text-body-secondary text-uppercase">Concepto / Observación</label>
                            <textarea name="conceptos" class="form-control text-uppercase ios-input" rows="2" placeholder="Describa el motivo..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100 btn-ios py-3 shadow">Confirmar Operación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
const ModalMovimiento = {
    url: '/myvet/app/controllers/tesoreriaController.php',
    saldosLocales: { efectivo: 0, tarjeta: 0, transferencia: 0 },

    init: function() {
        this.eventos();
        this.cargarCatalogos($('#sel_almacen_origen').val());
    },

    eventos: function() {
        const self = this;

        // Actualizar saldos si cambia almacén o fecha
        $('#sel_almacen_origen, #fecha_movimiento').on('change', function() {
            self.cargarCatalogos($('#sel_almacen_origen').val());
        });

        $('#sel_categoria').on('change', function() {
            const optionSelected = $(this).find('option:selected');
            const tipoOp = optionSelected.data('tipo'); 
            const nombreCat = optionSelected.text().toLowerCase();

            if (tipoOp) {
                $('#badge_tipo_operacion').text(tipoOp.toUpperCase()).show()
                    .removeClass('bg-success bg-danger bg-warning text-white text-dark');
                if(tipoOp === 'entrada') $('#badge_tipo_operacion').addClass('bg-success text-success');
                else if(tipoOp === 'salida') $('#badge_tipo_operacion').addClass('bg-danger text-danger');
                else $('#badge_tipo_operacion').addClass('bg-warning text-dark');
            }

            $('#col_destino_almacen, #col_destino_caja, #col_destino_banco').addClass('d-none');
            if (nombreCat.includes('banco')) $('#col_destino_banco').removeClass('d-none');
            else if (nombreCat.includes('caja fuerte')) $('#col_destino_caja').removeClass('d-none');
            else if (tipoOp === 'traspaso' || nombreCat.includes('almacen')) $('#col_destino_almacen').removeClass('d-none');
        });

        $('#formNuevoMovimiento').on('submit', function(e) {
            e.preventDefault();
            const montoIngresado = parseFloat($('input[name="monto"]').val()) || 0;
            const tipoOp = $('#sel_categoria').find('option:selected').data('tipo');
            const metodo = $('select[name="metodo_pago"]').val(); // Usamos name por si no tiene ID
            
            const saldoDisponible = self.saldosLocales[metodo] || 0;

            if ((tipoOp === 'salida' || tipoOp === 'traspaso') && montoIngresado > saldoDisponible) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Saldo Insuficiente',
                    text: `Intenta retirar $${montoIngresado.toLocaleString()} en ${metodo}, pero solo hay $${saldoDisponible.toLocaleString()} disponible.`,
                    confirmButtonColor: '#007AFF'
                });
                return false;
            }

            const $btn = $(this).find('button[type="submit"]');
            $btn.prop('disabled', true).html('Procesando...');

            $.post(self.url, $(this).serialize() + '&action=registrar', (res) => {
               if (res.status === 'success') {
    Swal.fire({ 
        icon: 'success', 
        title: '¡Éxito!', 
        text: res.message, 
        timer: 1500, 
        showConfirmButton: false 
    }).then(() => {
        // Este bloque se ejecuta cuando el timer termina o la alerta se cierra
        location.reload(); 
    });

    // Opcional: Esto ayuda a que el UI se vea limpio mientras recarga
    $('#modalMovimiento').modal('hide');
    $('#formNuevoMovimiento')[0].reset();
} else {
                    Swal.fire('Error', res.message, 'error');
                }
            }, 'json').always(() => {
                $btn.prop('disabled', false).text('Confirmar Operación');
            });
        });
    },

    cargarCatalogos: function(almacenId) {
        const self = this;
        const fecha = $('#fecha_movimiento').val();

        // 1. Llamada a 'listar' para traer los saldos reales desde obtenerSaldoInicialMonitor
        $.getJSON(this.url, { action: 'listar', almacen_id: almacenId, fecha: fecha }, (res) => {
            if (res.status === 'success') {
                const d = res.data; // Aquí vienen los montos de obtenerSaldoInicialMonitor
                
                // Guardar saldos para validación
                self.saldosLocales.efectivo = parseFloat(d.monto_efectivo) || 0;
                self.saldosLocales.tarjeta = parseFloat(d.monto_tarjeta) || 0;
                self.saldosLocales.transferencia = parseFloat(d.monto_transferencia) || 0;

                // Pintar saldos en los labels del modal
                $('#display_saldo_efectivo').text('$' + self.saldosLocales.efectivo.toLocaleString('es-MX', {minimumFractionDigits: 2}));
                $('#display_saldo_tarjeta').text('$' + self.saldosLocales.tarjeta.toLocaleString('es-MX', {minimumFractionDigits: 2}));
                $('#display_saldo_transferencia').text('$' + self.saldosLocales.transferencia.toLocaleString('es-MX', {minimumFractionDigits: 2}));
            }
        });

        // 2. Cargar cajas y bancos (catálogos estáticos)
        $.getJSON(this.url, { action: 'catalogos_modal', almacen_id: almacenId }, (res) => {
            if (res.status === 'success') {
                let htmlCajas = res.cajas_fuertes.map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');
                $('#sel_caja_fuerte').html(htmlCajas || '<option value="">Sin cajas disponibles</option>');

                let htmlBancos = res.bancos.map(b => `<option value="${b.id_cuenta}">${b.nombre_cuenta}</option>`).join('');
                $('#sel_banco').html(htmlBancos || '<option value="">Sin bancos vinculados</option>');
            }
        });
    },

    abrir: function() {
        $('#modalMovimiento').modal('show');
    }
};

$(document).ready(() => ModalMovimiento.init());
</script>