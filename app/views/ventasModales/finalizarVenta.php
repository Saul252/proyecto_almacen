<style>
/* ── Widget Estado de Cuenta ─────────────────────────── */
#widgetEstadoCuenta {
    border-radius: 16px;
    overflow: hidden;
    margin-top: 10px;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.widget-header-deuda {
    background: linear-gradient(135deg, #ff3b30 0%, #c0392b 100%);
    padding: 16px 18px;
    color: white;
}

.widget-header-ok {
    background: linear-gradient(135deg, #1d7a45 0%, #155d35 100%);
    padding: 16px 18px;
    color: white;
}

.widget-header-neutral {
    background: linear-gradient(135deg, #1c1c1e 0%, #2c2c2e 100%);
    padding: 16px 18px;
    color: white;
}

.widget-saldo-label {
    font-size: 0.6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    opacity: 0.75;
    margin-bottom: 2px;
}

.widget-saldo-monto {
    font-size: 1.8rem;
    font-weight: 800;
    letter-spacing: -0.04em;
    line-height: 1;
}

.widget-update-time {
    font-size: 0.6rem;
    opacity: 0.6;
    margin-top: 4px;
}

.widget-body {
    background: #f8f9fb;
    max-height: 240px;
    overflow-y: auto;
    padding: 10px 12px;
}

.widget-body::-webkit-scrollbar {
    width: 3px;
}

.widget-body::-webkit-scrollbar-thumb {
    background: #d1d1d6;
    border-radius: 4px;
}

.mov-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: white;
    border-radius: 12px;
    padding: 10px 12px;
    margin-bottom: 7px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.04);
    transition: transform 0.15s;
}

.mov-item:last-child {
    margin-bottom: 0;
}

.mov-item:hover {
    transform: translateX(2px);
}

.mov-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.mov-icon.cargo {
    background: #fff1f0;
    color: #ff3b30;
}

.mov-icon.abono {
    background: #e6faea;
    color: #28a745;
}

.mov-folio {
    font-size: 0.72rem;
    font-weight: 700;
    color: #1d1d1f;
}

.mov-obs {
    font-size: 0.62rem;
    color: #86868b;
    line-height: 1.3;
    margin-top: 1px;
}

.mov-fecha {
    font-size: 0.55rem;
    color: #aeaeb2;
    margin-top: 3px;
}

.mov-monto {
    font-size: 0.82rem;
    font-weight: 800;
    white-space: nowrap;
}

.mov-monto.cargo {
    color: #ff3b30;
}

.mov-monto.abono {
    color: #28a745;
}

.widget-footer {
    background: white;
    padding: 10px 12px;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

/* ── Ficha del cliente ───────────────────────────────── */
.ficha-cliente {
    background: #f8f9fb;
    border: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 14px;
    padding: 12px 14px;
    margin-bottom: 12px;
}

.ficha-label {
    font-size: 0.55rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #aeaeb2;
    margin-bottom: 2px;
}

.ficha-valor {
    font-size: 0.8rem;
    font-weight: 600;
    color: #1d1d1f;
}

/* ── Bloque de pago ──────────────────────────────────── */
.pago-block {
    background: #f0faf4;
    border: 1px solid rgba(40, 167, 69, 0.2);
    border-radius: 14px;
    padding: 14px;
    margin-bottom: 12px;
}

.pago-block .pago-title {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #1d7a45;
    margin-bottom: 10px;
}
</style>

<div class="modal fade" id="modalFinalizarVenta" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content  shadow-lg" style="border-radius: 20px; overflow: hidden;">

            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-receipt-cutoff me-2"></i>Finalizar Transacción
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <div class="row g-0">

                    <!-- ── Columna izquierda: detalle de productos ── -->
                    <div class="col-lg-7 p-4 border-end">
                        <h6 class="text-uppercase fw-bold mb-3 text-primary"
                            style="font-size:0.68rem;letter-spacing:0.08em;">
                            Detalle de Salida de Material
                        </h6>
                        <div class="table-responsive border rounded-3 bg-white mb-3" style="max-height: 80%;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr class="small text-uppercase text-body-secondary">
                                        <th class="ps-3">Producto</th>
                                        <th class="text-center">Venta</th>
                                        <th class="text-center">Entregar Hoy</th>
                                        <th class="text-end pe-3">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaConfirmacion"></tbody>
                            </table>
                        </div>

                        <!-- Total -->
                        <div style="background:#eff6ff;border-radius:14px;padding:14px 18px;text-align:right;">
                            <input type="hidden" id="descuentoGeneral" value="0">
                            <div
                                style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#6b7280;margin-bottom:2px;">
                                Total a Cobrar
                            </div>
                            <div
                                style="font-size:2rem;font-weight:800;color:#0071e3;letter-spacing:-0.04em;line-height:1;">
                                $<span id="totalFinalModal">0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- ── Columna derecha: cliente + pago ── -->
                    <div class="col-lg-5 p-4" style="background:#fafafa;">

                        <h6 class="text-uppercase fw-bold mb-3 text-primary"
                            style="font-size:0.68rem;letter-spacing:0.08em;">
                            Información del Cliente
                        </h6>

                        <!-- Selector de cliente -->
                        <div class="d-flex gap-2 mb-2">
                            <select id="selectCliente" class="form-select border-primary" style="border-radius:10px;">
                                <?php foreach($clientes as $c):
                                    
                                ?>
                                <option value="<?= $c['id'] ?>" data-rfc="<?= $c['rfc'] ?>"
                                    data-rs="<?= $c['razon_social'] ?>" data-cp="<?= $c['codigo_postal'] ?>"
                                    data-regimen="<?= $c['regimen_fiscal'] ?>">
                                    <?= htmlspecialchars($c['nombre_comercial']) ?>
                                </option>
                                <?php  endforeach; ?>
                            </select>
                            <button class="btn btn-outline-primary flex-shrink-0" type="button"
                                onclick="abrirModalNuevoCliente()" style="border-radius:10px;">
                                <i class="bi bi-person-plus"></i>
                            </button>
                        </div>

                       <div class="dropdown mt-2">
    <style>
        /* Botón estilo iOS con efecto Glassmorphism sutil */
        #btnDropdownEstado {
            font-size: 13px;
            font-weight: 600;
            color: #1c1c1e;
            border-radius: 12px;
            padding: 11px 16px;
            background: rgba(242, 242, 247, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #btnDropdownEstado:hover, #btnDropdownEstado:focus {
            background: rgba(230, 230, 235, 0.9);
            border-color: rgba(0, 0, 0, 0.1);
            transform: scale(0.99);
        }

        /* Menú desplegable flotante estilo tarjeta premium iOS */
        .dropdown-menu {
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            border-radius: 16px !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08), 0 5px 15px rgba(0, 0, 0, 0.04) !important;
            padding: 16px !important;
            animation: iosFadeIn 0.2s cubic-bezier(0.1, 0.76, 0.55, 0.94);
        }

        /* Animación suave de aparición */
        @keyframes iosFadeIn {
            from { opacity: 0; transform: translateY(-6px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Contenedor interno de movimientos con scroll nativo fluido */
        #listaMovimientos {
            max-height: 0px;
            overflow-y: auto;
            padding-right: 2px;
        }

        /* Scrollbar estético ultra fino tipo iOS */
        #listaMovimientos::-webkit-scrollbar {
            width: 4px;
        }
        #listaMovimientos::-webkit-scrollbar-track {
            background: transparent;
        }
        #listaMovimientos::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }
        #listaMovimientos::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }
    </style>

    <button class="btn w-100 text-start dropdown-toggle d-flex justify-content-between align-items-center" type="button" id="btnDropdownEstado" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <span><i class="bi bi-wallet2 me-2 text-primary"></i>Gestión de Cuenta</span>
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="btnDropdownEstado" style="min-width: 340px; max-width: 420px;">
        
        <div id="widgetEstadoCuenta" style="display:none;">
            <div id="widgetHeader" class="widget-header-neutral">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="widget-saldo-label">
                            <i class="bi bi-wallet2 me-1"></i>Estado de Cuenta
                        </div>
                        <div class="widget-saldo-monto" id="lblSaldoTotal">$0.00</div>
                    </div>
                    <span id="txtUltimaCarga" class="widget-update-time"></span>
                </div>
                <div id="widgetBadge" class="mt-2"></div>
            </div>

            <div class="widget-body" id="listaMovimientos">
                <div class="text-center py-4 text-body-secondary small">
                    <div class="spinner-border spinner-border-sm"></div>
                </div>
            </div>

            </div>

        <div id="contenedorSaldoFavor" class="p-3 mb-2 mt-2"
            style="display:none; background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px;">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="checkUsarSaldo"
                    onchange="toggleSaldoInput()">
                <label class="form-check-label fw-bold text-success" for="checkUsarSaldo">
                    ¿Usar saldo a favor en esta compra?
                </label>
            </div>

            <div id="inputSaldoContainer" class="mt-2" style="display:none;">
                <label class="small text-body-secondary">Cantidad a descontar:</label>
                <div class="input-group">
                    <span class="input-group-text bg-success text-white border-success">$</span>
                    <input type="number" id="monto_usar_favor"
                        class="form-control border-success fw-bold" value="0" step="0.01" min="0"
                        oninput="validarMontoMaximo(this)">
                </div>
                <div id="msgMaximo" class="text-body-secondary" style="font-size: 0.7rem;"></div>
            </div>
        </div>

    </div>
</div>
                        <!-- Ficha fiscal del cliente -->
                        <div class="ficha-cliente mt-3">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="ficha-label">Razón Social</div>
                                    <div class="ficha-valor text-truncate" id="f_razon_social">---</div>
                                </div>
                                <div class="col-6">
                                    <div class="ficha-label">RFC</div>
                                    <div class="ficha-valor" id="f_rfc">---</div>
                                </div>
                                <div class="col-6">
                                    <div class="ficha-label">Régimen</div>
                                    <span id="f_regimen" class="badge bg-info text-dark">---</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
    <label for="select-usuarios" class="form-label fw-bold small text-body-secondary text-uppercase">Atendió / Vendedor</label>
    <select class="form-select rounded-pill" id="select-usuarios" name="usuario_id" required>
        <option value="" selected disabled>Cargando vendedores...</option>
    </select>
</div>

                        <!-- Bloque de pago -->
                        <div class="pago-block">
                            <div class="pago-title"><i class="bi bi-cash-coin me-1"></i>Registro de Pago</div>
                            <div class="row g-2">
                                <div class="col-7">
                                    
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-success text-white border-success fw-bold">$</span>
                                        <input type="number" id="monto_pagar"
                                            class="form-control border-success fw-bold text-success" value="0"
                                            step="0.01" min="0" style="border-radius:0 8px 8px 0;">
                                    </div>
                                </div>
                                <div class="col-5">
                                     <select id="metodo_pago" class="form-select fw-bold" onchange="verificarMetodoPago(this.value)">
        <option value="Efectivo">Efectivo</option>
        <option value="Transferencia">Transferencia</option>
        <option value="Tarjeta">Tarjeta</option>
        
    </select>
</div>

<div class="mb-3" id="contenedorReferencia" style="display:none;">
    <label class="form-label small fw-bold text-secondary text-uppercase">
        Referencia
    </label>
    <input 
        type="text" 
        id="inputReferencia" 
        class="form-control"
        placeholder="Ingrese referencia"
    >
</div>
                            </div>
                            <div id="pago_aviso" class="small mt-2 text-center fw-bold"></div>
                        </div>

                        <textarea id="obsVenta" class="form-control" rows="2" placeholder="Notas adicionales..."
                            style="border-radius:10px;font-size:0.85rem;"></textarea>
                    </div>

                </div>
            </div>

            <div class="modal-footer bg-light " style="border-radius:0 0 20px 20px;">
                <button class="btn btn-link text-body-secondary me-auto" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-success btn-lg px-5 shadow fw-bold rounded-pill" onclick="procesarVenta()">
                    <i class="bi bi-check-circle-fill me-1"></i> FINALIZAR VENTA
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    cargarUsuariosSelect();
    async function cargarUsuariosSelect() {
    const select = document.getElementById('select-usuarios');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = '/myvet/app/controllers/ventasController.php?action=obtenerUsuarios';
        const respuesta = await fetch(url);
        
        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');
        
        const resultado = await respuesta.json();

        // 2. Verificar que la respuesta sea exitosa y contenga los datos
        if (resultado.success && Array.isArray(resultado.data)) {

            // Limpiamos el select y dejamos una opción inicial neutra
            select.innerHTML = '<option value="1" selected disabled>-- Seleccione un vendedor --</option>';

            // 3. Recorrer los usuarios y crear las opciones
            resultado.data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.id; // El ID que se enviará en el formulario
                
                // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                const almacen = usuario.almacen_nombre || 'Sin Almacén';
                opcion.textContent = `${usuario.nombre} (${almacen})`;
                
                // Agregamos la opción al select
                select.appendChild(opcion);
            });

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
            console.error('El backend no devolvió success:true o la estructura cambió');
        }

    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarUsuariosSelect:', error);
    }
}

// 4. Ejecutar la función automáticamente cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    cargarUsuariosSelect();
});
      function verificarMetodoPago(metodo) {
    

    const contenedor = document.getElementById('contenedorReferencia');
    const input = document.getElementById('inputReferencia');

    if (!contenedor || !input) return;

    if (metodo === 'Tarjeta' || metodo === 'Transferencia') {
        contenedor.style.display = 'block';
        input.required = true;
    } else {
        contenedor.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}
// Cache de elementos DOM para evitar búsquedas repetidas
const elements = {
    selectCliente: document.getElementById('selectCliente'),
    f_rfc: document.getElementById('f_rfc'),
    f_razon_social: document.getElementById('f_razon_social'),
    f_regimen: document.getElementById('f_regimen'),
    widgetEstadoCuenta: document.getElementById('widgetEstadoCuenta'),
    listaMovimientos: document.getElementById('listaMovimientos'),
    widgetHeader: document.getElementById('widgetHeader'),
    contenedorSaldoFavor: document.getElementById('contenedorSaldoFavor'),
    checkUsarSaldo: document.getElementById('checkUsarSaldo'),
    lblSaldoTotal: document.getElementById('lblSaldoTotal'),
    txtUltimaCarga: document.getElementById('txtUltimaCarga'),
    widgetBadge: document.getElementById('widgetBadge'),
    modalFinalizarVenta: document.getElementById('modalFinalizarVenta')
};

// Cache del formateador de moneda
const _fmt = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN'
});

// Debounce para evitar múltiples peticiones rápidas
let debounceTimer;
let currentController;

elements.selectCliente.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];

    // 1. Actualizar textos de la ficha fiscal
    elements.f_rfc.textContent = selected?.dataset.rfc || '---';
    elements.f_razon_social.textContent = selected?.dataset.rs || '---';
    elements.f_regimen.textContent = selected?.dataset.regimen || '---';

    // 2. Ejecutar consulta de estatus financiero con debounce
    const idCliente = this.value;
    if (idCliente) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => consultarEstatusFinanciero(idCliente), 300);
    }
});

// Función para realizar la petición al servidor
function consultarEstatusFinanciero(id) {
    const $widget = elements.widgetEstadoCuenta;
    const $lista = elements.listaMovimientos;
    const $header = elements.widgetHeader;

    if (!$widget || !$lista) return;

    // Cancelar petición anterior si existe
    if (currentController) {
        currentController.abort();
    }

    $widget.style.display = 'block';
    $lista.innerHTML =
        `<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-secondary"></div></div>`;

    // Usar AbortController para cancelar peticiones
    currentController = new AbortController();

    fetch(`/myvet/app/controllers/ventasController.php?action=obtenerEstatusCliente&id=${id}`, {
            signal: currentController.signal
        })
        .then(r => r.json())
        .then(data => {
            currentController = null;
            if (!data || data.nombre_comercial === undefined) throw new Error("Datos no encontrados");

            const res = data;
            const saldo = parseFloat(res.saldo_neto || 0);
            const condicion = res.estatus_financiero || 'AL DIA';

            // --- 1. LÓGICA DEL SWITCH DE SALDO A FAVOR ---
            const saldoAFavor = parseFloat(res.saldo_a_favor || 0);
            saldoDisponibleCliente = saldoAFavor; // Actualizamos la variable global

            const $panelSaldo = elements.contenedorSaldoFavor;
            const $chkSaldo = elements.checkUsarSaldo;

            if (saldoAFavor > 0) {
                $panelSaldo.style.display = 'block'; // Muestra el contenedor verde
            } else {
                $panelSaldo.style.display = 'none'; // Lo oculta si no hay saldo
                $chkSaldo.checked = false; // Resetea el switch
                toggleSaldoInput(); // Oculta el input de cantidad
            }
            // ----------------------------------------------

            // --- Lógica de Colores del Header ---
            $header.className = '';
            if (condicion === 'CON DEUDA') {
                $header.classList.add('widget-header-deuda');
            } else if (condicion === 'SALDO A FAVOR') {
                $header.classList.add('widget-header-ok');
            } else {
                $header.classList.add('widget-header-neutral');
            }

            // Actualizar montos principales
            elements.lblSaldoTotal.textContent = _fmt.format(Math.abs(saldo));
            elements.txtUltimaCarga.textContent =
                `Corte: ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;

            // Badge dinámico - cache de iconos
            const iconMap = {
                'CON DEUDA': 'bi-exclamation-triangle-fill',
                'SALDO A FAVOR': 'bi-plus-circle-fill',
                'default': 'bi-check-circle-fill'
            };
            const icon = iconMap[condicion] || iconMap.default;

            elements.widgetBadge.innerHTML = `
                <span style="background:rgba(255,255,255,0.2);color:white;font-size:0.6rem;font-weight:700;padding:3px 10px;border-radius:20px;">
                    <i class="bi ${icon} me-1"></i>${condicion}
                </span>`;

            // Resumen en el cuerpo - cache de strings
            const saldoEnContra = _fmt.format(res.saldo_en_contra || 0);
            const saldoAFavorFmt = _fmt.format(res.saldo_a_favor || 0);
            const estadoColor = saldo > 0 ? 'text-danger' : 'text-success';
            const estadoTexto = condicion === 'CON DEUDA' ? 'Pendiente de Pago' :
                (condicion === 'SALDO A FAVOR' ? 'Crédito Disponible' : 'Sin Adeudos');

            $lista.innerHTML = `
                <div class="p-2 small">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-body-secondary">Por Pagar:</span>
                        <span class="fw-bold text-danger">${saldoEnContra}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-body-secondary">A Favor:</span>
                        <span class="fw-bold text-success">${saldoAFavorFmt}</span>
                    </div>
                    <hr class="my-1" style="opacity:0.1">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-body-secondary">Estado:</span>
                        <span class="fw-bold ${estadoColor}">${estadoTexto}</span>
                    </div>
                </div>`;
        })
        .catch(err => {
            currentController = null;
            if (err.name !== 'AbortError') {
                console.error("Error:", err);
                $lista.innerHTML =
                    `<div class="text-center p-2 text-danger small">Error al consultar estatus</div>`;
            }
        });
}

document.addEventListener('DOMContentLoaded', () => {
    const select = elements.selectCliente;
    // Esto dispara el cambio inicial para el cliente seleccionado por defecto
    if (select) select.dispatchEvent(new Event('change'));

    // También forzamos el disparo cuando el modal de Bootstrap termina de abrirse
    const modal = elements.modalFinalizarVenta;
    if (modal) {
        modal.addEventListener('shown.bs.modal', () => {
            if (select) select.dispatchEvent(new Event('change'));
        });
    }
});

let saldoDisponibleCliente = 0;

function toggleSaldoInput() {
    const chk = elements.checkUsarSaldo;
    const container = document.getElementById('inputSaldoContainer');
    const input = document.getElementById('monto_usar_favor');

    container.style.display = chk.checked ? 'block' : 'none';
    if (!chk.checked) input.value = 0;
}

function validarMontoMaximo(input) {
    const valor = parseFloat(input.value) || 0;
    if (valor > saldoDisponibleCliente) {
        input.value = saldoDisponibleCliente;
    }
}
// Escuchar cuando se activa/desactiva el switch de saldo
document.getElementById('checkUsarSaldo')?.addEventListener('change', function() {
    toggleSaldoInput(); // Muestra/oculta el input
    actualizarTotalesUI(); // Recalcula todo
});

// Escuchar cuando el usuario escribe manualmente cuánto crédito usar
document.getElementById('monto_usar_favor')?.addEventListener('input', function() {
    validarMontoMaximo(this); // No dejar que use más de lo que tiene
    actualizarTotalesUI(); // Recalcula todo
});
</script>
<script>
// Lógica de validación de pago y avisos
document.getElementById('monto_pagar').addEventListener('input', function() {
    const modal = document.getElementById('totalFinalModal');
    const totalOriginal = parseFloat(modal.dataset.totalOriginal) || 0;
    const totalVisual = parseFloat(modal.innerText.replace(/[$,]/g, '')) || 0;
    const chkSaldo = document.getElementById('checkUsarSaldo');
    const aviso = document.getElementById('pago_aviso');

    let valorTecleado = parseFloat(this.value) || 0;

    // Validación de límites
    if (valorTecleado < 0) this.value = 0;
    if (valorTecleado > totalVisual) this.value = totalVisual.toFixed(2);
    valorTecleado = parseFloat(this.value) || 0;

    // Crear Leyenda si usa créditos
    let leyenda = '';
    if (chkSaldo?.checked) {
        leyenda = `<div class="mt-2 p-2 bg-primary-subtle text-primary border rounded-3" style="font-size:0.8rem">
            <i class="bi bi-info-circle-fill me-1"></i> Tu compra es de <b>${_fmtMXN.format(totalOriginal)}</b> porque estás usando tus créditos.
        </div>`;
    }

    // Estado del pago (Completo, Parcial, etc)
    let estado = '';
    if (totalVisual === 0 && totalOriginal > 0) {
        estado = '<span class="text-success fw-bold">CUBIERTO CON CRÉDITO</span>';
    } else if (valorTecleado === totalVisual && totalVisual > 0) {
        estado = '<span class="text-success fw-bold">PAGO COMPLETO</span>';
    } else if (valorTecleado > 0) {
        estado = '<span class="text-warning fw-bold">PAGO PARCIAL</span>';
    } else {
        estado = '<span class="text-danger fw-bold">CRÉDITO (DEUDA)</span>';
    }

    aviso.innerHTML = estado + leyenda;
});

function validarYAgregar(btn) {
    const fila = btn.closest('tr');
    const modo = fila.querySelector('.select-modo-venta')?.value || 'individual';
    const inputCant = fila.querySelector('.cantidad');
    const factor = parseFloat(fila.dataset.factor) || 1;
    const stockDisponible = parseFloat(fila.querySelector('.badge').innerText);

    let cantidadUsuario = parseFloat(inputCant.value) || 0;
    let cantidadReal = (modo === 'referencia') ? (cantidadUsuario * factor) : cantidadUsuario;

    // if (cantidadReal > stockDisponible) {
    //     Swal.fire('Stock insuficiente', `No puedes agregar ${cantidadReal} unidades. Stock: ${stockDisponible}`,
    //         'error');
    //     return;
    // }

    inputCant.value = cantidadReal; // Ajuste temporal para agregarProducto
    if (typeof agregarProducto === "function") agregarProducto(btn);
    inputCant.value = 1; // Reset
}
</script>

<script>
/**
 * SISTEMA DE VENTAS - Gestión de Carrito (Optimizado)
 */
window.carrito = window.carrito || [];

// Formateador de moneda reutilizable
const _fmtMXN = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN'
});

/**
 * 1. AGREGAR PRODUCTO AL CARRITO
 */
window.agregarProducto = function(btn) {
    const fila = btn.closest("tr");
    const {
        productoId,
        almacenId,
        almacen
    } = btn.dataset;
    const {
        factor: fStr,
        reporteNom
    } = fila.dataset;

    const producto_id = parseInt(productoId || btn.getAttribute("data-producto-id"));
    const almacen_id = parseInt(almacenId);
    const factor = parseFloat(fStr) || 1;

    const nombre = fila.cells[2].innerText;
    const cantidadInput = fila.querySelector(".cantidad");
     const inputUsuario =
            fila.querySelector('.cantidad_usuario')?.value;
           
    const modoVenta = fila.querySelector(".select-modo-venta")?.value || 'individual';
    const modoVent =
        fila.querySelector(".select-modo-venta");

    const unidad_medida =
        modoVent?.options?. [modoVent.selectedIndex]?.dataset?.nombre || 'PZA';
    console.log(unidad_medida);
    const select =
        fila.querySelector('.medidas_adicionales');

    const equivalencia =
        parseFloat(select.value);

    const medidaId =
        select.options[select.selectedIndex].dataset.id;
    const medidaNombre =
        select.options[select.selectedIndex].dataset.nombre;
        
    if (equivalencia <= 0) {
        Swal.fire('Atención', 'Ingresa una cantidad válida', 'warning');
        return;
    }
    console.log(equivalencia);
    console.log(medidaId, medidaNombre);


    // 🔥 resetear
    select.selectedIndex = 0;

    let cantidadBase = (modoVenta === 'referencia') ? factor : (parseFloat(cantidadInput.value) || 0);
cantidadBase=Math.round(cantidadBase * 1000000) / 1000000;
    const selectPrecio = fila.querySelector(".select-precio");
    const selectPrecioInput = fila.querySelector(".input-precio");
    const precioUnitario = parseFloat(selectPrecioInput.value) || 0;
    console.log('precio Unitario',precioUnitario);
    const textoPrecio = selectPrecio.options[selectPrecio.selectedIndex].text.toLowerCase();
    const tipo_p = textoPrecio.includes("dist") ? "distribuidor" : (textoPrecio.includes("may") ? "mayorista" :
        "minorista");
 console.log("total",inputUsuario*precioUnitario);
    if (cantidadBase <= 0) {
        Swal.fire('Atención', 'Ingresa una cantidad válida', 'warning');
        return;
    }



    const itemExistente = window.carrito.find(item =>
        item.producto_id === producto_id && item.almacen_id === almacen_id && item.tipo_precio === tipo_p
    );

    if (itemExistente) {
        itemExistente.cantidad += cantidadBase;
         itemExistente.subtotal += cantidadBase*precioUnitario;
    } else {
        window.carrito.push({
            producto_id,
            almacen_id,
            almacen_nombre: almacen,
            nombre,
            cantidad: cantidadBase,
            cantidadUsuario:inputUsuario,
            subtotal:inputUsuario*precioUnitario,
            entrega_hoy: cantidadBase,
            precio_unitario: precioUnitario,
            tipo_precio: tipo_p,
            factor: factor,
            unidad_reporte: reporteNom || 'Fact.',
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
 * 2. RENDERIZAR TABLA
 */
window.renderCarrito = function() {
    const tablaBody = document.querySelector("#tablaCarrito tbody");
    if (!tablaBody) return;

    // Generamos el HTML en un array para un solo "paint" al final
    const htmlCarrito = window.carrito.map((item, index) => {
        if (item.cantidad > 1) {
            item.cantidad = Math.round(item.cantidad * 10000) / 10000;       }
        const cantFactor = Math.floor(item.cantidad / item.factor);
        const cantPza = (item.cantidad % item.factor);
       
      
        //console.log((equivalencia), item.factor);
        
      
      
         
              
       

       


        return `
            <tr data-index="${index}">
                <td><small>${item.almacen_nombre}</small></td>
                <td><div class="fw-bold" style="font-size: 0.8rem;">${item.nombre}</div></td>
                <td>
                    <input type="hidden" class="form-control form-control-sm text-center input-factor-cambio" 
                        data-index="${index}" value="${(cantFactor<1 &&cantFactor>0)?cantFactor:cantFactor}" min="0" step="1">
                 <small class="form-control form-control-sm text-center ">${item.cantidad*item.unidadEquivalencia}</small>
                       </td>
                <td>
                
                    <input type="hidden" class="form-control form-control-sm text-center input-pza-cambio" 
                        data-index="${index}" value="${(cantPza<1&& cantPza>0)?cantPza:cantPza}" min="0" step="any">
              <small class="form-control form-control-sm text-center ">${item.unidadMedidaNombre}</small>
                </td>
                <td class="text-end fw-bold subtotal-celda">$${Math.round((item.subtotal) * 10000) / 10000}</td>
                <td>
                    <button type="button" class="btn btn-link text-danger p-0 btn-remove-item" data-index="${index}">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </td>
            </tr>`;
    }).join('');

    tablaBody.innerHTML = htmlCarrito;
    actualizarTotalesUI();
};

/**
 * 3. LÓGICA DE CÁLCULO SINCRONIZADA
 */
document.addEventListener('input', (e) => {
    const target = e.target;
    if (target.classList.contains('input-factor-cambio') || target.classList.contains('input-pza-cambio')) {
        const index = target.dataset.index;
        const item = window.carrito[index];
        const tr = target.closest('tr');

        const valFactor = parseFloat(tr.querySelector('.input-factor-cambio').value) || 0;
        const valPza = parseFloat(tr.querySelector('.input-pza-cambio').value) || 0;

        item.cantidad = (valFactor * item.factor) + valPza;
        item.subtotal = item.cantidad * item.precio_unitario;
        item.entrega_hoy = item.cantidad;


        tr.querySelector('.subtotal-celda').innerText = `$${Math.round((item.subtotal)* 10000) / 10000}`;
        actualizarTotalesUI();
    }
});

/**
 * 4. LÓGICA DE BRINCO
 */
document.addEventListener('change', (e) => {
    if (e.target.matches('.input-factor-cambio, .input-pza-cambio')) {
        window.renderCarrito();
    }
});

/**
 * FUNCIÓN PARA ACTUALIZAR TOTALES GLOBALES
 */
function actualizarTotalesUI() {
    const totalVentaReal = window.carrito.reduce((acc, item) => acc + item.subtotal, 0);

    const elTotal = document.getElementById("total");
    const elTotalModal = document.getElementById("totalFinalModal");
    const elPago = document.getElementById("monto_pagar");
    const elSaldoFavor = document.getElementById("monto_usar_favor");
    const chkSaldo = document.getElementById("checkUsarSaldo");

    // 1. Guardamos el total real de la mercancía (sin descuentos de crédito aún)
    if (elTotalModal) elTotalModal.dataset.totalOriginal = totalVentaReal.toFixed(2);
    if (elTotal) elTotal.innerText = totalVentaReal.toFixed(2);

    let montoPorCobrar = totalVentaReal;

    // 2. Si el switch está activo, restamos el crédito del total visual
    if (chkSaldo?.checked) {
        const creditoAUsar = parseFloat(elSaldoFavor.value) || 0;
        montoPorCobrar = Math.max(0, totalVentaReal - creditoAUsar);
    }

    // 3. Actualizamos el número que el usuario ve en el modal (lo que falta pagar)
    if (elTotalModal) elTotalModal.innerText = montoPorCobrar.toFixed(2);

    // 4. Ponemos por defecto el monto a pagar y disparamos el aviso de la leyenda
    if (elPago) {
        elPago.value = montoPorCobrar.toFixed(2);
        elPago.dispatchEvent(new Event('input'));
    }
} // Eliminar item
document.addEventListener('click', (e) => {
    const btnDelete = e.target.closest('.btn-remove-item');
    if (btnDelete) {
        window.carrito.splice(btnDelete.dataset.index, 1);
        window.renderCarrito();
    }
});
</script>

<script>
window.procesarVenta = function() {
    // 1. Validaciones de integridad (Sin cambios)
    if (!window.carrito || window.carrito.length === 0) {
        Swal.fire({
            title: 'Carrito vacío',
            text: 'Debes agregar al menos un producto.',
            icon: 'warning',
            customClass: {
                popup: 'rounded-4'
            }
        });
        return;
    }

    const idCliente = document.getElementById('selectCliente').value;
    if (!idCliente) {
        Swal.fire({
            title: 'Falta Cliente',
            text: 'Por favor selecciona un cliente para la venta.',
            icon: 'warning',
            customClass: {
                popup: 'rounded-4'
            }
        });
        return;
    }

    // 2. Captura de montos y estados
    const elTotalModal = document.getElementById('totalFinalModal');

    // El COSTO REAL de la mercancía (lo que vale la nota)
    const totalOriginalVenta = parseFloat(elTotalModal.dataset.totalOriginal) || 0;

    // Lo que el cliente paga en efectivo/bancos
    const efectivoRecibido = parseFloat(document.getElementById('monto_pagar').value) || 0;

    // Lo que el cliente decide usar de su "bolsa" de saldo a favor
    const creditoAplicado = document.getElementById('checkUsarSaldo').checked ?
        (parseFloat(document.getElementById('monto_usar_favor').value) || 0) :
        0;

    // La suma que cubre la nota (Efectivo + Crédito)
    const pagoTotalEnviado = efectivoRecibido + creditoAplicado;

    let metodoPago = document.getElementById('metodo_pago').value; 
     let referencia = document.getElementById('inputReferencia').value;// Cambia const por let
    const observaciones = document.getElementById('obsVenta').value;
    console.log(observaciones);

    if (creditoAplicado == totalOriginalVenta) {
        const metodoPago = "Saldo_a_Favor";
        console.log(metodoPago);
    }


    // 3. Confirmación Visual Estilo iOS
    Swal.fire({
        title: '¿Finalizar Venta?',
        html: `
            <div class="text-center mb-2">
                <span class="text-body-secondary d-block small">Total de la Nota</span>
                <h3 class="fw-bold" style="color: #007aff;">$${totalOriginalVenta.toFixed(2)}</h3>
            </div>
            <div class="p-2 rounded-3 bg-light small text-start border">
                <div class="d-flex justify-content-between text-dark">
                    <span>Efectivo/Bancos:</span> <b>$${efectivoRecibido.toFixed(2)}</b>
                </div>
                <div class="d-flex justify-content-between text-primary">
                    <span>Uso Saldo Favor:</span> <b>$${creditoAplicado.toFixed(2)}</b>
                </div>
                <hr class="my-1">
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total Cubierto:</span> <span>$${pagoTotalEnviado.toFixed(2)}</span>
                </div>
            </div>
            ${pagoTotalEnviado < totalOriginalVenta ? 
                `<div class="mt-2 badge bg-danger-subtle text-danger w-100 py-2">Quedará deuda de $${(totalOriginalVenta - pagoTotalEnviado).toFixed(2)}</div>` 
                : ''}
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007aff',
        cancelButtonColor: '#8e8e93',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'rounded-4 '
        }
    }).then((result) => {
        if (result.isConfirmed) {

            const btnFinalizar = document.querySelector('#modalFinalizarVenta .btn-primary');
            if (btnFinalizar) btnFinalizar.disabled = true;

            Swal.fire({
                title: 'Procesando...',
                text: 'Sincronizando stock y saldos...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                customClass: {
                    popup: 'rounded-4'
                }
            });
              let vendedor_id= document.getElementById("select-usuarios").value??0;
               

            // 4. Mapeo del carrito (Lógica de despacho)
            const carritoFinal = window.carrito.map((item, index) => {
                const inputEntrega = document.querySelector(
                    `.input-entrega-modal[data-index="${index}"]`);
                  let entregado = inputEntrega ? parseFloat(inputEntrega.value) : item.cantidad;
                console.log(item.unidadMedidaNombre);
                return {
                    producto_id: parseInt(item.producto_id),
                    almacen_id: parseInt(item.almacen_id),
                    cantidad: parseFloat(item.cantidad),
                    entrega_hoy: isNaN(entregado) ? 0 : entregado,
                    precio_unitario: parseFloat(item.precio_unitario),
                    subtotal: parseFloat(item.subtotal),
                    tipo_precio: item.tipo_precio,
                    unidad_base: item.unidad_base,
                    unidadMedidaNombre: item.unidadMedidaNombre ?? '',

                    idunidadMedida: item.unidadMedidaSelect ?? 0,
                    unidadEquivalencia: item.unidadEquivalencia ?? 1

                };
            });

            // 5. Envío al Controlador
            const datos = {
                accion: 'guardar_venta',
                id_cliente: parseInt(idCliente),
                id_vendedor:parseInt(vendedor_id),
                monto_pagado: efectivoRecibido, // Dinero real
                monto_usado_favor: creditoAplicado, // Lo que se resta de la bolsa
                total_venta: totalOriginalVenta, // El costo real (Para calcular deuda)
                metodo_pago: metodoPago,
                referencia:referencia??'',
                observaciones: observaciones,
                carrito: carritoFinal,
                usar_saldo_favor: creditoAplicado > 0 ? 1 : 0
            };

            fetch('/myvet/app/controllers/ventasController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datos)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        // Lógica de mensajes post-venta (Compara contra el costo real)
                        const tieneDeuda = pagoTotalEnviado < totalOriginalVenta;
                        const esEntregaTotal = res.total_entregado >= res.total_pedido;
                        const iconoFinal = esEntregaTotal ? 'success' : 'warning';

                        let htmlExtra =
                            `<p class="mb-2">Folio: <span class="badge bg-light text-dark border">${res.folio}</span></p>`;

                        if (tieneDeuda) {
                            htmlExtra += `
                            <div class="alert alert-danger py-1 px-2  mb-2" style="font-size:0.75rem; border-radius:10px;">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> Saldo pendiente registrado en cuenta
                            </div>`;
                        }

                        Swal.fire({
                            title: esEntregaTotal ? '¡Venta Exitosa!' :
                                'Entrega Parcial Registrada',
                            html: `
                            <div class="alert bg-body-tertiary text-body  small text-start py-2 mb-3" style="background:var(--bs-tertiary-bg);; border-radius:12px;">
                                ${res.message || 'Operación realizada correctamente.'}
                            </div>
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
                                popup: 'rounded-4  shadow-lg'
                            }
                        }).then((result) => {
                            let url = '';
                            if (result.isConfirmed) url =
                                `/myvet/app/backend/ventas/ticket_venta.php?id=${res.id_venta}`;
                            else if (result.isDenied) url =
                            
                                `/myvet/app/backend/ventas/ticketFormal.php?id=${res.id_venta}`;

                            if (url !== '') window.open(url, '_blank');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: res.message || 'Error desconocido',
                            icon: 'error',
                            customClass: {
                                popup: 'rounded-4'
                            }
                        });
                        if (btnFinalizar) btnFinalizar.disabled = false;
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    Swal.fire('Error Crítico', 'No se pudo conectar con el servidor.', 'error');
                    if (btnFinalizar) btnFinalizar.disabled = false;
                });
        }
    });
}
</script>