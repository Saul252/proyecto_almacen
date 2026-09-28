 <div class="modal fade" id="modalFinalizarVenta" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
         <div class="modal-content  shadow-lg"
             style="border-radius: 2rem; background: #f2f2f7; overflow: hidden;">

             <div class="modal-header  bg-white bg-opacity-75 pt-4 px-4" style="backdrop-filter: blur(10px);">
                 <div class="d-flex align-items-center">
                     <div class="bg-primary bg-opacity-10 p-2 rounded-4 me-3">
                         <i class="bi bi-receipt-cutoff text-primary fs-4"></i>
                     </div>
                     <div>
                         <h5 class="modal-title fw-bold text-dark mb-0">Finalizar Transacción</h5>
                         <small class="text-body-secondary fw-medium">Revisa los detalles antes de confirmar</small>
                     </div>
                 </div>
                 <button type="button" class="btn-close shadow-none bg-light rounded-circle p-2" data-bs-dismiss="modal"
                     aria-label="Close" style="font-size: 0.7rem;"></button>
             </div>

             <div class="modal-body p-4">
                 <div class="row g-4">

                     <div class="col-lg-7">
                         <div class="card  shadow-sm h-100" style="border-radius: 1.5rem; background: #ffffff;">
                             <div class="card-body p-4">
                                 <h6 class="text-uppercase fw-bold mb-4 text-secondary"
                                     style="font-size: 0.7rem; letter-spacing: 1px;">
                                     <i class="bi bi-list-ul me-2 text-primary"></i>Resumen de Salida
                                 </h6>

                                 <div class="table-responsive" style="max-height: 80%;">
                                     <table class="table table-borderless align-middle">
                                         <thead>
                                             <tr class="text-body-secondary small border-bottom border-light">
                                                 <th class="pb-3 fw-bold">Producto</th>
                                                 <th class="pb-3 text-center fw-bold">Venta</th>

                                                 <th class="pb-3 text-end fw-bold">Subtotal</th>
                                             </tr>
                                         </thead>
                                         <tbody id="tablaConfirmacion">
                                         </tbody>
                                     </table>
                                 </div>

                                 <div class="mt-auto pt-4">
                                     <div class="p-4 rounded-4 bg-primary shadow-sm text-white d-flex justify-content-between align-items-center"
                                         style="background: linear-gradient(135deg, #007aff 0%, #0056b3 100%) !important;">
                                         <div>
                                             <span class="d-block small opacity-75 fw-bold text-uppercase"
                                                 style="font-size: 0.6rem;">Total a Cobrar</span>
                                             <input type="hidden" id="descuentoGeneral" value="0">
                                         </div>
                                         <h2 class="fw-bold mb-0" style="letter-spacing: -1px;">$<span
                                                 id="totalFinalModal">0.00</span></h2>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>

                     <div class="col-lg-5">
                         <div class="d-flex flex-column gap-3">

                             <div class="card  shadow-sm" style="border-radius: 1.5rem;">
                                 <div class="card-body p-4">
                                     <h6 class="text-uppercase fw-bold mb-3 text-secondary"
                                         style="font-size: 0.7rem; letter-spacing: 1px;">
                                         <i class="bi bi-person-circle me-2 text-primary"></i>Información del Cliente
                                     </h6>
                                     <div class="input-group mb-3 shadow-none">
                                         <select id="selectCliente"
                                             class="form-select  bg-light rounded-4 p-2 px-3 shadow-none fw-medium">
                                             <?php foreach($clientes as $c): 
                                             
                                            ?>
                                             <option value="<?= $c['id'] ?>" data-rfc="<?= $c['rfc'] ?>"
                                                 data-rs="<?= $c['razon_social'] ?>"
                                                 data-regimen="<?= $c['regimen_fiscal'] ?>">
                                                 <?= htmlspecialchars($c['nombre_comercial']) ?>
                                             </option>
                                             <?php  endforeach; ?>
                                         </select>
                                         <button class="btn btn-primary rounded-4 ms-2 px-3 shadow-none" type="button"
                                             onclick="abrirModalNuevoCliente()">
                                             <i class="bi bi-plus-lg"></i>
                                         </button>
                                     </div>

                                     <div class="p-3 rounded-4 bg-light  small">
                                         <div class="mb-2">
                                             <span class="text-body-secondary d-block"
                                                 style="font-size: 0.6rem; font-weight: 800;">RAZÓN SOCIAL</span>
                                             <span id="f_razon_social"
                                                 class="fw-bold text-dark text-truncate d-block">---</span>
                                         </div>
                                         <div class="row g-0">
                                             <div class="col-6">
                                                 <span class="text-body-secondary d-block"
                                                     style="font-size: 0.6rem; font-weight: 800;">RFC</span>
                                                 <span id="f_rfc" class="fw-bold text-dark">---</span>
                                             </div>
                                             <div class="col-6 text-end">
                                                 <span id="f_regimen"
                                                     class="badge rounded-pill bg-white text-primary border border-primary border-opacity-25 px-3 py-2">---</span>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <div class="card  shadow-sm" style="border-radius: 1.5rem; background: #ffffff;">
                                 <div class="card-body p-4">
                                     <h6 class="text-uppercase fw-bold mb-3 text-success"
                                         style="font-size: 0.7rem; letter-spacing: 1px;">
                                         <i class="bi bi-credit-card me-2"></i>Método de Pago
                                     </h6>
                                     <div class="row g-2">
                                         <div class="col-4">
                                             <label class="small fw-bold text-body-secondary mb-1" style="font-size: 0.6rem;">A
                                                 PAGAR</label>
                                             <input type="number" id="monto_pagar"
                                                 class="form-control  bg-light p-2 rounded-3 fw-bold text-primary shadow-none text-center"
                                                 readonly>
                                         </div>
                                         <div class="col-4">
                                             <label class="small fw-bold text-success mb-1"
                                                 style="font-size: 0.6rem;">EFECTIVO</label>
                                             <input type="number" id="efectivo_recibido"
                                                 class="form-control  bg-success bg-opacity-10 p-2 rounded-3 fw-bold text-success shadow-none text-center"
                                                 placeholder="0.00" step="0.01">
                                             <input id="efectivoPagado" type="hidden">

                                         </div>
                                         <div class="mb-3">
                                             <label for="select-usuarios"
                                                 class="form-label fw-bold small text-body-secondary text-uppercase">Atendió /
                                                 Usuario</label>
                                             <select class="form-select rounded-pill" id="select-usuarios"
                                                 name="usuario_id" required>

                                             </select>
                                         </div>
                                         <div class="col-4">
                                             <label class="small fw-bold text-body-secondary mb-1"
                                                 style="font-size: 0.6rem;">MÉTODO</label>
                                             <select id="metodo_pago" onchange="verificarMetodoPago(this.value)"
                                                 class="form-select  bg-light p-2 rounded-3 shadow-none fw-bold small">
                                                 <option value="Efectivo">Efectivo</option>
                                                 <option value="Transferencia">Transferencia</option>
                                                 <option value="Tarjeta">Tarjeta</option>
                                             </select>
                                         </div>

                                         <div class="mb-3" id="contenedorReferencia" style="display:none;">
                                             <label class="form-label small fw-bold text-secondary text-uppercase">
                                                 Referencia
                                             </label>
                                             <input type="text" id="inputReferencia" class="form-control"
                                                 placeholder="Ingrese referencia">
                                         </div>
                                     </div>

                                     <div id="contenedor_cambio" class="mt-3 p-3 rounded-4 text-center d-none"
                                         style="background: #e1fcef; border: 1px dashed #34c759;">
                                         <span class="text-success small fw-bold text-uppercase"
                                             style="font-size: 0.6rem; letter-spacing: 1px;">Cambio para el
                                             cliente</span>
                                         <h3 class="fw-bold text-success mb-0" id="texto_cambio">$0.00</h3>
                                     </div>
                                 </div>
                             </div>

                             <textarea id="obsVenta"
                                 class="form-control text-uppercase  bg-white p-3 rounded-4 shadow-sm" rows="2"
                                 placeholder="Notas adicionales de la venta..." style="font-size: 0.85rem;"></textarea>
                         </div>
                     </div>

                     <div style=" display:none;" class="col-12">
                         <div class="card  shadow-sm" style="border-radius: 1.5rem;">
                             <div class="card-body p-4">
                                 <h6 class="text-uppercase fw-bold mb-4 text-secondary"
                                     style="font-size: 0.7rem; letter-spacing: 1px;">
                                     <i class="bi bi-truck me-2 text-primary"></i>Datos de Despacho
                                 </h6>
                                 <div class="row g-3">
                                     <div style=" display:none;" class="col-md-4">
                                         <div class="p-3 rounded-4 bg-light">
                                             <label class="form-label small fw-bold text-body-secondary mb-1"
                                                 style="font-size: 0.6rem;">DESPACHADOR RESPONSABLE</label>
                                             <select name="chofer_id" id="patio_chofer_id"
                                                 class="form-select  bg-transparent shadow-none fw-bold p-0">
                                                 <option value="">Seleccione encargado...</option>
                                             </select>
                                         </div>
                                     </div>
                                     <div style=" display:none;" class="col-md-4">
                                         <div class="p-3 rounded-4 bg-light">
                                             <label class="form-label small fw-bold text-body-secondary mb-1"
                                                 style="font-size: 0.6rem;">AYUDANTES (MULTIPLE)</label>
                                             <select name="tripulantes[]" id="patio_tripulantes"
                                                 class="form-select  bg-transparent shadow-none fw-bold p-0"
                                                 multiple style="min-height: 24px;">
                                             </select>
                                         </div>
                                     </div>
                                     <div style=" display:none;" class="col-md-4">
                                         <div class="p-3 rounded-4 bg-light">
                                             <label class="form-label small fw-bold text-body-secondary mb-1"
                                                 style="font-size: 0.6rem;">OBSERVACIONES DE ENTREGA</label>
                                             <textarea name="observaciones"
                                                 class="form-control  bg-transparent shadow-none p-0 fw-medium"
                                                 rows="1" placeholder="Ej. Revisado por cliente..."></textarea>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

             <div class="modal-footer  bg-white p-4 pt-2">
                 <button class="btn btn-link text-body-secondary fw-bold text-decoration-none me-auto"
                     data-bs-dismiss="modal">Cancelar</button>
                 <button id="btnFinalizarVenta" class="btn btn-success rounded-pill px-5 py-3 fw-bold shadow-sm"
                     onclick="procesarVenta()" style="background: #34c759 !important; border: none !important;">
                     <i class="bi bi-check-lg me-2"></i>Finalizar Transacción
                 </button>
             </div>
         </div>
     </div>
 </div>
 <style>
/* Personalización de Scroll para iOS Look */
#modalFinalizarVenta .table-responsive::-webkit-scrollbar {
    width: 4px;
}

#modalFinalizarVenta .table-responsive::-webkit-scrollbar-thumb {
    background: #d1d1d6;
    border-radius: 10px;
}

#modalFinalizarVenta .form-select,
#modalFinalizarVenta .form-control {
    transition: all 0.2s ease;
}

#modalFinalizarVenta .form-select:focus,
#modalFinalizarVenta .form-control:focus {
    background-color: #e5e5ea !important;
}

#modalFinalizarVenta .btn-primary {
    background-color: #007aff !important;
    
}

#modalFinalizarVenta .text-primary {
    color: #007aff !important;
}
 </style>

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


            // 3. Recorrer los usuarios y crear las opciones
            resultado.data.forEach(usuario => {
                const opcion = document.createElement('option');
                opcion.value = usuario.id; // El ID que se enviará en el formulario

                // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                const almacen = usuario.almacen_nombre || 'Sin Almacén';
                opcion.textContent = `${usuario.nombre}`;

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
async function cargarPersonalDespacho(alm) {

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
 </script>

 <script>
// Escuchar cambios en el efectivo recibido
document.addEventListener('input', function(e) {
    if (e.target.id === 'efectivo_recibido' || e.target.id === 'monto_pagar') {
        calcularCambio();
    }
});

function calcularCambio() {
    const totalVenta = parseFloat(document.getElementById('monto_pagar').value) || 0;
    const efectivo = parseFloat(document.getElementById('efectivo_recibido').value) || 0;
    const contenedor = document.getElementById('contenedor_cambio');
    const textoCambio = document.getElementById('texto_cambio');
    const efectivoPagado = document.getElementById('efectivoPagado');
    console.log('Dinero', parseFloat(document.getElementById('efectivo_recibido').value) || 0);


    if (efectivo > 0) {
        const cambio = efectivo - totalVenta;

        // Mostrar el contenedor de cambio
        contenedor.classList.remove('d-none');
        efectivoPagado.value = efectivo;

        if (cambio < 0) {
            // Si falta dinero
            textoCambio.classList.replace('text-success', 'text-danger');
            textoCambio.innerText = `Faltan: $${Math.abs(cambio).toFixed(2)}`;
        } else {
            // Si hay cambio o es exacto
            textoCambio.classList.replace('text-danger', 'text-success');
            textoCambio.innerText = `Cambio: $${cambio.toFixed(2)}`;
        }
    } else {
        contenedor.classList.add('d-none');
    }
}

// Resetear calculadora cuando se abra el modal
const modalVenta = document.getElementById('modalFinalizarVenta');
if (modalVenta) {
    modalVenta.addEventListener('shown.bs.modal', function() {
        document.getElementById('efectivo_recibido').value = "";
        document.getElementById('contenedor_cambio').classList.add('d-none');
        document.getElementById('efectivo_recibido').focus(); // Auto-focus para rapidez
    });
}
document.addEventListener('change', function(e) {
    if (e.target.id === 'metodo_pago') {
        const metodo = e.target.value;
        const campoEfectivo = document.getElementById('efectivo_recibido');
        const contenedorCambio = document.getElementById('contenedor_cambio');

        // Contenedor del input (el col-md-4) para ocultarlo por completo
        const columnaEfectivo = campoEfectivo.closest('.col-md-4');

        if (metodo === 'Efectivo') {
            // Mostrar campos de calculadora
            columnaEfectivo.classList.remove('d-none');
            // El contenedor de cambio se mostrará solo si hay un valor (vía calcularCambio)
        } else {
            // Ocultar campos para Tarjeta o Transferencia
            columnaEfectivo.classList.add('d-none');
            contenedorCambio.classList.add('d-none');

            // Limpiar valores para no enviar basura al controlador
            campoEfectivo.value = "";
        }
    }
});
 </script>
 <script>
// --- 1. VALIDACIÓN DE PAGO Y EVENTOS DE CLIENTE (Tus funciones originales) ---
document.getElementById('monto_pagar').addEventListener('input', function() {
    const totalTexto = document.getElementById('totalFinalModal').innerText.replace(/[$,]/g, '');
    const totalFinal = parseFloat(totalTexto) || 0;
    let valor = parseFloat(this.value) || 0;
    const aviso = document.getElementById('pago_aviso');

    if (valor < 0) this.value = 0;
    else if (valor > totalFinal) this.value = totalFinal;

    if (valor === totalFinal && totalFinal > 0) aviso.innerHTML =
        '<span class="text-success"><i class="bi bi-check-all"></i> PAGO COMPLETO</span>';
    else if (valor > 0 && valor < totalFinal) aviso.innerHTML =
        '<span class="text-warning"><i class="bi bi-pie-chart"></i> PAGO PARCIAL</span>';
    else if (valor === 0 && totalFinal > 0) aviso.innerHTML =
        '<span class="text-danger"><i class="bi bi-exclamation-triangle"></i> CRÉDITO</span>';
    else aviso.innerHTML = '';
});

document.getElementById('selectCliente').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    document.getElementById('f_rfc').textContent = selected?.dataset.rfc || '---';
    document.getElementById('f_razon_social').textContent = selected?.dataset.rs || '---';
    document.getElementById('f_regimen').textContent = selected?.dataset.regimen || '---';
});

document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('selectCliente');
    if (select) select.dispatchEvent(new Event('change'));
});

function validarYAgregar(btn) {
    const fila = btn.closest('tr');
    const modo = fila.querySelector('.select-modo-venta')?.value || 'individual';
    const inputCant = fila.querySelector('.cantidad');
    const factor = parseFloat(fila.dataset.factor) || 1;
    const stockDisponible = parseFloat(fila.querySelector('.badge').innerText);

    let cantidadUsuario = parseFloat(inputCant.value) || 0;
    let cantidadReal = (modo === 'referencia') ? (cantidadUsuario * factor) : cantidadUsuario;

    if (cantidadReal > stockDisponible) {
        Swal.fire('Stock insuficiente', `No puedes agregar ${cantidadReal} unidades. Stock: ${stockDisponible}`,
            'error');
        return;
    }

    inputCant.value = cantidadReal;
    if (typeof agregarProducto === "function") agregarProducto(btn);
    inputCant.value = 1;
}
 </script>
 <script>
window.procesarVenta = function() {
    // 1. Validaciones básicas
    if (!window.carrito || window.carrito.length === 0) {
        return Swal.fire('Carrito vacío', 'Agrega productos.', 'warning');
    }

    const idCliente = document.getElementById('selectCliente').value;
    const idChofer = document.getElementById('patio_chofer_id').value; // <--- OBLIGATORIO PARA LOGÍSTICA

    if (!idCliente) return Swal.fire('Cliente requerido', 'Selecciona un cliente.', 'warning');
    if (!idChofer) return Swal.fire('Despachador requerido', 'Selecciona quién entrega en patio.', 'warning');

    // 2. Captura de datos (Incluyendo los nuevos campos de logística iOS)
    const totalVenta = parseFloat(document.getElementById('totalFinalModal').innerText.replace(/[$,]/g, '')) || 0;
    const montoPagado = parseFloat(document.getElementById('monto_pagar').value) || 0;

    // Obtenemos los ayudantes del select múltiple (jQuery)
    const ayudantesIds = $('#patio_tripulantes').val() || [];
    console.log(montoPagado);
    // 3. Confirmación
    Swal.fire({
        title: '¿Finalizar Transacción?',
        text: `Se registrará la salida con el despachador seleccionado.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, finalizar',
        confirmButtonColor: '#34c759'
    }).then((result) => {
        if (result.isConfirmed) {

            const btnFinalizar = document.querySelector('#btnFinalizarVenta');
            btnFinalizar.disabled = true;
            btnFinalizar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
            const efectivoPagado = parseFloat(
                document.getElementById('efectivo_recibido').value
            ) || 0;
            let referencia = document.getElementById('inputReferencia').value;
            let vendedor_id = document.getElementById("select-usuarios").value ?? 0;


            // 4. MAPEO DE DATOS COMPLETO (Venta + Logística)
            // --- MAPEO DE DATOS COMPLETO (Venta + Logística Automática) ---
            console.log('chofer', parseInt(document.getElementById('patio_chofer_id').value) || 0);
            const datosVenta = {
                // 1. Datos de la Transacción
                id_cliente: parseInt(idCliente),
                id_almacen: parseInt(document.getElementById('modal_select_almacen')?.value || 0),
                id_vendedor: parseInt(vendedor_id),
                monto_pagado: parseFloat(montoPagado) || 0,
                metodo_pago: document.getElementById('metodo_pago').value,
                referencia: referencia ?? '',
                total_venta: parseFloat(totalVenta) || 0,
                observaciones: document.getElementById('obsVenta').value,
                efectivoPagado: efectivoPagado,

                // 2. Datos de Logística (Para tu función cajaRapidaEntregarEnPatioCliente)
                chofer_id: parseInt(document.getElementById('patio_chofer_id').value) || 0,
                tripulantes: $('#patio_tripulantes').val() || [], // Array de IDs de ayudantes
                observaciones_entrega: document.querySelector('textarea[name="observaciones"]')
                    ?.value || 'Entrega en Patio',


                // 3. Detalle de Productos (Carrito)    console.log(item.unidadMedidaNombre??'');

                carrito: window.carrito.map(item => ({
                    producto_id: parseInt(item.producto_id),
                    almacen_id: parseInt(item.almacen_id),
                    cantidad: parseFloat(item.cantidad),
                    entrega_hoy: parseFloat(item.entrega_hoy || item.cantidad),
                    precio_unitario: parseFloat(item.precio_unitario),
                    tipo_precio: item.tipo_precio,
                    unidad_base: item.unidad_base,
                    unidadMedidaNombre: item.unidadMedidaNombre ?? '',

                    idunidadMedida: item.unidadMedidaSelect ?? 0,
                    unidadEquivalencia: item.unidadEquivalencia ?? 1,


                }))
            };


            // --- ENVÍO AL CONTROLADOR ---
            fetch('/myvet/app/controllers/cajaRapidaController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datosVenta)
                })
                .then(res => {
                    // Si el servidor manda un error de PHP, esto lo captura antes de fallar el JSON
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error("Error en servidor: " + text)
                        });
                    }
                    return res.json();
                })
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({
                            title: '¡Venta y Despacho OK!',
                            text: res.message,
                            icon: 'success',
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
                        throw new Error(res.message || "Error desconocido al guardar.");
                    }
                })
                .catch(err => {
                    console.error("Error Crítico:", err);
                    Swal.fire('Error de Sistema', err.message, 'error');

                    // Reactivamos el botón para reintentar
                    const btn = document.querySelector('#btnFinalizarVenta');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>PROCESAR TRANSACCIÓN';
                    }
                });
        }
    });
};
 </script>
 <script>
/**
 * 4. FUNCIÓN PARA ABRIR EL MODAL DE FINALIZACIÓN
 */
window.abrirModalFinalizar = function() {
    // 1. Validación de Carrito
    if (!window.carrito || window.carrito.length === 0) {
        Swal.fire('Carrito vacío', 'Agrega productos antes de finalizar la venta.', 'warning');
        return;
    }

    // 2. OBTENER Y VALIDAR ALMACÉN (Especialmente para Admin con id 0)
    let idAlmacenFinal = <?= (int)($_SESSION['almacen_id'] ?? 0) ?>;

    // Si la sesión es 0 (Admin), buscamos el valor del select 'filtroAlmacen'
    if (idAlmacenFinal === 0) {
        const selectAlm = document.getElementById('filtroAlmacen');
        if (!selectAlm || selectAlm.value === "" || selectAlm.value === "0") {
            Swal.fire({
                title: 'Seleccione Almacén',
                text: 'Es obligatorio elegir un almacén de origen para asignar el personal de despacho.',
                icon: 'warning',
                confirmButtonColor: '#007aff'
            });
            return; // Bloquea la apertura del modal
        }
        idAlmacenFinal = selectAlm.value;
    }

    // 3. Renderizado de la tabla (Tu lógica original)
    const tabla = document.getElementById("tablaConfirmacion");
    if (!tabla) return;
    tabla.innerHTML = "";

    window.carrito.forEach((item, index) => {
        if (item.entrega_hoy === undefined || item.entrega_hoy === null) {
            item.entrega_hoy = item.cantidad;
        }

        const cantFactorVenta = Math.floor(item.cantidad / item.factor);
        const piezasRestantesVenta = Math.round((item.cantidad % item.factor) * 100) / 100;
        let leyenda = '';
        let cantidadT = item.cantidad;
        if (item.cantidad < 1) {
            cantidadT = item.unidadEquivalencia > 0 ? (item.cantidad / (1 / item.unidadEquivalencia)) : item
                .cantidad;
            leyenda =
                '1 ' + item.unidadMedidaNombre +
                ' = ' +
                (1 / item.unidadEquivalencia) +
                ' de ' +
                item.unidad_medida +
                '\n';
        }
        let nombreuni = '';
        let unidadNombre = '';
        if (cantFactorVenta == 0) {
            nombreuni = item.unidadMedidaNombre;
            unidadNombre = item.unidad_medida;

        }
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>
                <div class="fw-bold" style="font-size: 0.85rem;">${item.nombre}</div>
                <small class="text-body-secondary d-block">${item.almacen_nombre} | ${item.tipo_precio.toUpperCase()}</small>
                
                <div class="mt-1" style="font-size: 0.7rem; color: #055160; background: #e3f2fd; padding: 4px 8px; border-radius: 4px; border-left: 3px solid #0d6efd;">
                    <i class="bi bi-info-circle-fill"></i> Factor: 1 <b>${item.unidad_reporte}</b> = <b>${item.factor}</b>${item.unidad_medida}<br>
                   Vendido:${leyenda}  ${cantFactorVenta>0?item.unidad_reporte:''} ${piezasRestantesVenta > 0 ? piezasRestantesVenta + ' pzas' : ''}<br>
                    
               </div>
            </td>
            <td class="text-center">
              <div class="fw-bold" style="font-size: 0.9rem;">

${
    (() => {
        const EPS = 0.0001;

        const residuo = item.cantidad % item.factor;

        

        if (item.unidadEquivalencia > (Math.floor(item.cantidad / item.factor))) {
            console.log('unidad especial',(item.cantidad)/(1/item.unidadEquivalencia));

            return (
               
                item.cantidad / (1 / item.unidadEquivalencia)
            ).toFixed(3);
        }

        const divisionFactor = item.cantidad / item.factor;

        const cantFactorVenta =
            Math.abs(divisionFactor - 1) < 0.001
                ? 1
                : Math.floor(divisionFactor);

        return cantFactorVenta >= 1
            ? cantFactorVenta
            : cantidadT.toFixed(3);
    })()
}

${
    cantFactorVenta > 0
        ? item.unidad_reporte
        : nombreuni
}

</div>       
            </td>
          
               
                    <input type="hidden" 
                           class="form-control text-center input-entrega-modal" 
                           data-index="${index}" 
                           value="${item.cantidad}" 
                           min="0" 
                           max="${item.cantidad}"
                           step="any" readonly>
                    
            <td class="text-end fw-bold">$${item.subtotal.toFixed(2)}</td>
        `;
        tabla.appendChild(tr);
    });

    // 4. Actualizaciones finales antes de mostrar
    window.recalcularTotalModal();

    // Sincronizar el select interno del modal (si existe para el admin)
    const modalSelectAlm = document.getElementById('modal_select_almacen');
    if (modalSelectAlm) {
        modalSelectAlm.value = idAlmacenFinal;
    }

    // CARGA DE PERSONAL: Usamos el ID validado
    if (typeof cargarPersonalDespacho === "function") {
        cargarPersonalDespacho(idAlmacenFinal);
    }

    // 5. Apertura del Modal
    const modalElement = document.getElementById('modalFinalizarVenta');
    if (modalElement) {
        const myModal = new bootstrap.Modal(modalElement);
        myModal.show();
    }
};
/**
 * 5. RECALCULAR TOTALES DENTRO DEL MODAL
 */
window.recalcularTotalModal = function() {
    let total = 0;
    if (window.carrito) {
        window.carrito.forEach(i => {
            total += parseFloat(i.subtotal || 0);
        });
    }

    const totalDisplay = document.getElementById("totalFinalModal");
    if (totalDisplay) totalDisplay.innerText = total.toFixed(2);

    const inputPago = document.getElementById("monto_pagar");
    if (inputPago) {
        inputPago.value = total.toFixed(2);
        inputPago.dispatchEvent(new Event('input'));
    }
};

/**
 * 6. LISTENER PARA ACTUALIZAR DESGLOSE Y ENTREGA EN TIEMPO REAL
 */
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('input-entrega-modal')) {
        const index = e.target.dataset.index;
        const item = window.carrito[index];

        // CORRECCIÓN: Usa parseFloat para permitir decimales o cantidades enteras mayores a 1
        let valor = parseFloat(e.target.value);

        if (isNaN(valor)) valor = 0;

        // Validar que no entregue más de lo vendido
        if (valor > item.cantidad) {
            valor = item.cantidad;
            e.target.value = valor;
        }

        // Guardamos el valor real (ej. 2, 5, 10...)
        item.entrega_hoy = valor;

        // Actualizar el texto informativo (Aquí sí usamos floor solo para mostrar el texto de "bultos")
        const f = Math.floor(valor / item.factor);
        const p = Math.round((valor % item.factor) * 100) / 100;

        const elDesglose = document.getElementById(`desglose-entrega-${index}`);
        if (elDesglose) {
            elDesglose.innerHTML = `Entregando: ${f} ${item.unidad_reporte} + ${p} pzas.`;
        }
    }
});
 </script>