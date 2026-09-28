<!-- MODAL PRINCIPAL -->
<div class="modal fade" id="modalAjusteFaltante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content  shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-danger text-white px-4 py-3 ">
                <h5 class="modal-title fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-diagram-3-fill fs-5"></i>
                    <span>Distribución de Faltantes</span>
                    <span id="folioAjuste" class="badge bg-white text-danger fw-bold ms-2 px-3 py-1"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>

            <form id="formAjusteFaltante">
                <div class="modal-body px-4 py-4">

                    <input type="hidden" name="compra_id" id="ajuste_compra_id">

                    <!-- Campos ocultos para almacenar la selección de excedente si se confirma -->
                    <input type="hidden" name="excedente_confirmado" id="excedente_confirmado" value="0">
                    <input type="hidden" name="excedente_almacen_id" id="excedente_almacen_id" value="">
                    <input type="hidden" name="excedente_cantidad" id="excedente_cantidad" value="0">
                    <input type="hidden" name="excedente_producto_id" id="excedente_producto_id" value="">

                    <!-- ALERTA PREMIUM -->
                    <div
                        class="d-flex align-items-start gap-3 p-3 mb-4 rounded-4 shadow-sm border-start border-4 border-danger">
                        <div>
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                        </div>
                        <div class="small text-body-secondary">
                            <div class="fw-semibold mb-1">Control de Entradas</div>
                            Habilite el almacén de destino y después capture la cantidad recibida para evitar errores en
                            inventario.
                        </div>
                    </div>

                    <!-- CONTENEDOR DE PRODUCTOS -->
                    <div id="listaProductosFaltantes" class="row g-4">
                        <!-- contenido dinámico -->
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer px-4 py-3  d-flex justify-content-between">

                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <div class="d-flex gap-2">

                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-semibold shadow-sm"
                            onclick="aplicarFaltantesCompra()">
                            <i class="bi bi-arrow-repeat me-1"></i> Ajustar compra
                        </button>

                        <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold shadow"
                            onclick="procesarAjuste()">
                            <i class="bi bi-check-circle-fill me-1"></i> Registrar entrada
                        </button>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- MODAL PARA CONFIRMAR Y ASIGNAR EXCEDENTE -->
<div class="modal fade" id="modalConfirmarExcedente" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg rounded-4">
            <div class="modal-header bg-warning text-dark ">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>Cantidad Excedida Detectada
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3 fs-6">
                    Has sobrepasado la cantidad pendiente asignada. ¿Deseas guardar este sobrante como un
                    <b>excedente</b> en el inventario?
                </p>

                <div class="bg-light p-3 rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Cantidad Sobrante:</span>
                        <input type="number" id="cantidadReal">
                        <span id="lblCantidadExcedente" class="fw-bold text-danger">0.00</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Selecciona el Almacén para el Excedente:</label>
                    <select id="selAlmacenExcedente" class="form-select border-warning">
                        <!-- Opciones cargadas dinámicamente -->
                    </select>
                </div>
            </div>
            <div class="modal-footer  d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"
                    onclick="cancelarExcedente()">
                    Cancelar edición
                </button>
                <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold"
                    onclick="asignarExtra()">
                    Continuar y Asignar Excedente
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.pointer {
    cursor: pointer;
}

.form-switch .form-check-input:checked {
    background-color: #dc3545;
    border-color: #dc3545;
}
</style>

<script>
async function asignarExtra() {
    // 1. Loader de SweetAlert mientras se realiza la consulta
    Swal.fire({
        title: 'Consultando servidor...',
        text: 'Por favor espere un momento',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // 2. Obtener valores de los inputs
    let id = idFinal;
    console.log(id);
    let cantidad = document.getElementById('cantidadReal').value;
    let prodId = prod_id_temp;
     console.log(cantidad);
     console.log(prodId);

    // 3. Crear el objeto FormData y agregar los datos
    const formData = new FormData();
    formData.append('compra_id', id);
    formData.append('producto_id', prodId);
    formData.append('excedente', cantidad);

    try {
        // Mantenemos la acción en la URL o puedes meterla también al formData
        const url = '/myvet/app/controllers/egresosController.php?action=actualizarExcedente';

        // 4. Configurar fetch con el método POST y el body con FormData
        const respuesta = await fetch(url, {
            method: 'POST',
            body: formData
            // NOTA: No agregues 'Content-Type', la API de FormData lo establece automáticamente con el boundary.
        });

        if (!respuesta.ok) {
            throw new Error(`Respuesta no válida del servidor (HTTP ${respuesta.status})`);
        }

        const resultado = await respuesta.json();
        console.log('Respuesta del servidor:', resultado);

        // 5. Evaluar respuesta del backend
        if (resultado.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: resultado.message || 'La consulta se procesó correctamente.',
                timer: 2000,
                showConfirmButton: false
            });
            aplicarFaltantesCompra();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Aviso del Servidor',
                text: resultado.message || 'No se pudo completar la solicitud.',
                confirmButtonColor: '#3085d6'
            });
        }

    } catch (error) {
        console.error('Error al consultar el servidor:', error);

        // 6. Alerta en caso de falla de red o error de servidor
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo establecer comunicación con el servidor.',
            confirmButtonColor: '#d33'
        });
    }
}
let almacenesGlobal = [];
let excedenteTemp = {
    prodId: null,
    cantidad: 0,
    factor: 1,
    inputElem: null
};

function toggleAlmacen(check, prodId, almId) {
    const inputVisible = document.querySelector(`input[name="distribucion[${prodId}][${almId}]1"]`);
    const inputHidden = document.querySelector(`input[name="distribucion[${prodId}][${almId}]"]`);

    if (!inputVisible || !inputHidden) return;

    if (check.checked) {
        inputVisible.disabled = false;
        inputHidden.disabled = false;
        inputVisible.focus();
    } else {
        inputVisible.disabled = true;
        inputHidden.disabled = true;
        inputVisible.value = '';
        inputHidden.value = '';

        const factor = parseFloat(inputVisible.dataset.factor) || 1;
        recalcularRestante(prodId, factor, inputVisible);
    }
}
let prod_id_temp=0;
function recalcularRestante(prodId, factor = 1, inputModificado = null) {
    const inputs = document.querySelectorAll(`input[name^="distribucion[${prodId}]"][name$="]1"]`);

    let suma = 0;
    let maximo = 0;
    prod_id_temp=prodId;

    inputs.forEach(input => {
        if (!input.disabled) {
            const valor = parseFloat(input.value) || 0;
            suma += valor * factor;
            maximo = parseFloat(input.dataset.max) || 0;

            // Actualizar hidden
            const hiddenName = input.name.replace(']1', ']');
            const hidden = document.querySelector(`input[name="${hiddenName}"]`);
            if (hidden) {
                hidden.value = (valor * factor).toFixed(2);
            }
        }
    });

    suma = Math.round(suma * 1000) / 1000;

    // SI LA SUMA EXCEDE EL MÁXIMO MOSTRAR MODAL DE CONFIRMACIÓN
    if (suma > maximo) {
        const exceso = (suma - maximo) / factor;

        excedenteTemp = {
            prodId: prodId,
            cantidad: exceso,
            factor: factor,
            inputElem: inputModificado || document.activeElement
        };

        // Rellenar datos en el modal de excedente
        document.getElementById('lblCantidadExcedente').innerText = exceso.toFixed(2);
        document.getElementById('cantidadReal').value = exceso*factor;
        



        const selAlmacen = document.getElementById('selAlmacenExcedente');
        selAlmacen.innerHTML = '';
        almacenesGlobal.forEach(alm => {
            selAlmacen.innerHTML += `<option value="${alm.id}">${alm.nombre}</option>`;
        });

        const modalExcedente = new bootstrap.Modal(document.getElementById('modalConfirmarExcedente'));
        modalExcedente.show();
        return;
    }

    const restante = Math.max(0, (Math.round((maximo - suma) * 1000) / 1000) / factor);

    // Actualizar texto de restante
    const textos = document.querySelectorAll(`.restante-prod[data-restante="${prodId}"]`);
    textos.forEach(texto => {
        texto.innerHTML = `Restante por asignar: <b>${restante.toFixed(2)}</b>`;
    });
}

function guardarExcedenteModal() {
    const almacenId = document.getElementById('selAlmacenExcedente').value;

    document.getElementById('excedente_confirmado').value = "1";
    document.getElementById('excedente_almacen_id').value = almacenId;
    document.getElementById('excedente_cantidad').value = excedenteTemp.cantidad;
    document.getElementById('excedente_producto_id').value = excedenteTemp.prodId;

    const modalExcedenteElem = document.getElementById('modalConfirmarExcedente');
    const modalExcedente = bootstrap.Modal.getInstance(modalExcedenteElem);
    if (modalExcedente) modalExcedente.hide();

    // Actualizar interfaz para reflejar cero restante
    const textos = document.querySelectorAll(`.restante-prod[data-restante="${excedenteTemp.prodId}"]`);
    textos.forEach(texto => {
        texto.innerHTML =
            `Restante por asignar: <b>0.00</b> <span class="badge bg-warning text-dark ms-2">Excedente asignado</span>`;
    });
}

function cancelarExcedente() {
    if (excedenteTemp.inputElem) {
        excedenteTemp.inputElem.value = '';
        const hiddenName = excedenteTemp.inputElem.name.replace(']1', ']');
        const hidden = document.querySelector(`input[name="${hiddenName}"]`);
        if (hidden) hidden.value = '0';

        recalcularRestante(excedenteTemp.prodId, excedenteTemp.factor, excedenteTemp.inputElem);
    }

    document.getElementById('excedente_confirmado').value = "0";
    document.getElementById('excedente_almacen_id').value = "";
    document.getElementById('excedente_cantidad').value = "0";
    document.getElementById('excedente_producto_id').value = "";
}
let idFinal=0;
function abrirModalAjuste(id, folio) {
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAjusteFaltante'));
    document.getElementById('folioAjuste').innerText = folio;
    document.getElementById('ajuste_compra_id').value = id;
    idFinal=id;

    const contenedor = document.getElementById('listaProductosFaltantes');
    contenedor.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-danger"></div></div>';

    modal.show();
    almacenesGlobal = <?= json_encode($almacenUsuario) ?>;

    fetch(`/myvet/app/controllers/egresosController.php?action=obtenerFaltantes&compra_id=${id}`)
        .then(res => res.json())
        .then(data => {
            contenedor.innerHTML = '';

            data.forEach(p => {
                let tablaAlmacenes = `
                    <table class="table table-sm align-middle mb-0">
                        <thead class="text-body-secondary" style="font-size: 0.75rem;">
                            <tr>
                                <th width="50" class="text-center">Envío</th>
                                <th>Almacén Destino</th>
                                <th width="140">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>`;

                almacenesGlobal.forEach(alm => {
                    tablaAlmacenes += `
                        <tr>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input pointer" type="checkbox" 
                                           onchange="toggleAlmacen(this, ${p.producto_id}, ${alm.id})">
                                </div>
                            </td>
                            <td class="small fw-semibold text-secondary">${alm.nombre}</td>
                            <td>
                                <input type="number" 
                                       name="distribucion[${p.producto_id}][${alm.id}]1" 
                                       class="form-control form-control-sm border-danger input-dist1" 
                                       data-prod-id="${p.producto_id}"
                                       data-max="${p.cantidad_pendiente}"
                                       data-factor="${p.factor_conversion}"
                                       disabled 
                                       placeholder="0.00" 
                                       step=".01" 
                                       min="0"
                                       oninput="recalcularRestante(${p.producto_id}, ${p.factor_conversion}, this)">

                                <input type="hidden" 
                                       name="distribucion[${p.producto_id}][${alm.id}]" 
                                       class="form-control form-control-sm border-danger input-dist" 
                                       data-prod-id="${p.producto_id}"
                                       data-max="${p.cantidad_pendiente}"
                                       disabled>
                            </td>
                        </tr>`;
                });

                tablaAlmacenes += `</tbody></table>`;

                const restanteInicial = ((p.cantidad_pendiente) / p.factor_conversion).toFixed(2);

                contenedor.innerHTML += `
                    <div class="col-lg-6">
                        <div class="card  shadow-sm h-100">
                            <div class="card-header border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0">${p.nombre}</h6>
                                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">
                                    Pendiente: ${restanteInicial} ${p.unidad_reporte}
                                </span>
                            </div>
                            <div class="card-body pt-0">
                                <div class="border rounded-3 overflow-hidden">
                                    ${tablaAlmacenes}
                                </div>
                                <div class="small text-danger fw-bold mt-2 restante-prod" data-restante="${p.producto_id}">
                                    Restante por asignar: <b>${restanteInicial}</b>
                                </div>
                            </div>
                        </div>
                    </div>`;
            });
        })
        .catch(err => {
            contenedor.innerHTML =
                '<div class="col-12 text-center text-danger py-4">Error al cargar los productos faltantes.</div>';
            console.error(err);
        });
}

function aplicarFaltantesCompra() {
    const compra_id = document.getElementById('ajuste_compra_id').value;

    Swal.fire({
        title: '¿Aplicar faltantes?',
        text: 'Se descontarán del total y los faltantes se pondrán en 0.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, aplicar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                text: 'Aplicando cambios',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(
                    `/myvet/app/controllers/egresosController.php?action=aplicarFaltantesCompras&compra_id=${compra_id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Actualizado',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'No se pudo aplicar'
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor'
                    });
                    console.error(err);
                });
        }
    });
}

function procesarAjuste() {
    const form = document.getElementById('formAjusteFaltante');
    const formData = new FormData(form);

    let hayDatos = false;

    form.querySelectorAll('.input-dist:not(:disabled)').forEach(input => {
        const cant = parseFloat(input.value) || 0;
        if (cant > 0) {
            hayDatos = true;
        }
    });

    if (!hayDatos) return Swal.fire('Sin datos', 'Habilite al menos un almacén e ingrese cantidad.', 'warning');

    Swal.fire({
        title: '¿Confirmar Ingreso?',
        text: "Se afectará el stock de los almacenes habilitados.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sí, registrar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/myvet/app/controllers/egresosController.php?action=procesarAjusteFaltante', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Éxito!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'No se pudo procesar la solicitud.', 'error');
                    console.error(err);
                });
        }
    });
}
</script>