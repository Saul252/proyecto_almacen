<!-- MODAL PRINCIPAL -->
<div class="modal fade" id="modalAjusteFaltante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-danger text-white px-4 py-3 border-0">
                <h5 class="modal-title fw-semibold d-flex align-items-center gap-2">
                    <i class="bi bi-diagram-3-fill fs-5"></i>
                    <span>Distribución de Faltantes</span>
                    <span id="ajF_folio" class="badge bg-white text-danger fw-bold ms-2 px-3 py-1"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal"></button>
            </div>

            <form id="ajF_form">
                <div class="modal-body px-4 py-4">

                    <input type="hidden" name="compra_id" id="ajF_compra_id">

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
                    <div id="ajF_lista_productos" class="row g-4">
                        <!-- contenido dinámico -->
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer px-4 py-3 border-0 d-flex justify-content-between">

                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-semibold shadow-sm"
                            onclick="ajF_aplicarFaltantesCompra()">
                            <i class="bi bi-arrow-repeat me-1"></i> Ajustar compra
                        </button>

                        <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold shadow"
                            onclick="ajF_procesarAjuste()">
                            <i class="bi bi-check-circle-fill me-1"></i> Registrar entrada
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
    .ajF-pointer {
        cursor: pointer;
    }

    #modalAjusteFaltante .form-switch .form-check-input:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
</style>

<script>
    // ═══════════════════════════════════════════════════════════
    // Estado global (namespace para evitar colisiones)
    // ═══════════════════════════════════════════════════════════
    const ajF_State = {
        almacenes: [],
        idCompra: 0
    };

    // Set para no repetir el toast en cada tecla mientras el usuario sigue en exceso
    const ajF_productosConExcedenteNotificado = new Set();

    // ═══════════════════════════════════════════════════════════
    // Abrir modal principal
    // ═══════════════════════════════════════════════════════════
    function abrirModalAjuste(id, folio) {
        const modalElem = document.getElementById('modalAjusteFaltante');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElem);

        document.getElementById('ajF_folio').innerText = folio;
        document.getElementById('ajF_compra_id').value = id;
        ajF_State.idCompra = id;

        // Limpiar set de notificaciones al abrir
        ajF_productosConExcedenteNotificado.clear();

        const contenedor = document.getElementById('ajF_lista_productos');
        contenedor.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-danger"></div></div>';

        modal.show();

        ajF_State.almacenes = <?= json_encode($almacenes ?? 0) ?>;

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

                    ajF_State.almacenes.forEach(alm => {
                        tablaAlmacenes += `
                        <tr>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input ajF-pointer" type="checkbox"
                                           id="ajF_chk_${p.producto_id}_${alm.id}"
                                           data-prod-id="${p.producto_id}"
                                           data-alm-id="${alm.id}"
                                           onchange="ajF_toggleAlmacen(this, ${p.producto_id}, ${alm.id})">
                                </div>
                            </td>
                            <td class="small fw-semibold text-secondary">${alm.nombre}</td>
                            <td>
                                <input type="number"
                                       id="ajF_in_vis_${p.producto_id}_${alm.id}"
                                       name="distribucion[${p.producto_id}][${alm.id}]1"
                                       class="form-control form-control-sm border-danger ajF-input-visible"
                                       data-prod-id="${p.producto_id}"
                                       data-alm-id="${alm.id}"
                                       data-max="${p.cantidad_pendiente}"
                                       data-factor="${p.factor_conversion}"
                                       disabled
                                       placeholder="0.00"
                                       step=".01"
                                       min="0"
                                       oninput="ajF_recalcularRestante(${p.producto_id}, ${p.factor_conversion}, this)">

                                <input type="hidden"
                                       id="ajF_in_hid_${p.producto_id}_${alm.id}"
                                       name="distribucion[${p.producto_id}][${alm.id}]"
                                       class="ajF-input-hidden"
                                       data-prod-id="${p.producto_id}"
                                       data-alm-id="${alm.id}"
                                       data-max="${p.cantidad_pendiente}"
                                       disabled>
                            </td>
                        </tr>`;
                    });

                    tablaAlmacenes += `</tbody></table>`;

                    const restanteInicial = (parseFloat(p.cantidad_pendiente) / parseFloat(p.factor_conversion)).toFixed(2);

                    contenedor.innerHTML += `
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
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
                                <div class="small text-danger fw-bold mt-2 ajF-restante-prod" data-restante="${p.producto_id}">
                                    Restante por asignar: <b>${restanteInicial}</b>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            })
            .catch(err => {
                contenedor.innerHTML = '<div class="col-12 text-center text-danger py-4">Error al cargar los productos faltantes.</div>';
                console.error(err);
            });
    }

    // ═══════════════════════════════════════════════════════════
    // Toggle de almacén (checkbox) — todo por ID
    // ═══════════════════════════════════════════════════════════
    function ajF_toggleAlmacen(check, prodId, almId) {
        const inputVisible = document.getElementById(`ajF_in_vis_${prodId}_${almId}`);
        const inputHidden = document.getElementById(`ajF_in_hid_${prodId}_${almId}`);

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
            ajF_recalcularRestante(prodId, factor, inputVisible);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Recalcular restante + TOAST de excedente (SIN modal)
    // ═══════════════════════════════════════════════════════════
    function ajF_recalcularRestante(prodId, factor = 1, inputModificado = null) {
        const inputs = document.querySelectorAll(
            `input.ajF-input-visible[data-prod-id="${prodId}"]`
        );

        let suma = 0;
        let maximo = 0;

        inputs.forEach(input => {
            if (!input.disabled) {
                const valor = parseFloat(input.value) || 0;
                suma += valor * factor;
                maximo = parseFloat(input.dataset.max) || 0;

                const almId = input.dataset.almId;
                const hidden = document.getElementById(`ajF_in_hid_${prodId}_${almId}`);
                if (hidden) {
                    hidden.value = (valor * factor).toFixed(2);
                }
            }
        });

        suma = Math.round(suma * 1000) / 1000;

        const restanteRaw = (maximo - suma) / factor;
        const restante = Math.max(0, restanteRaw);
        const excede = suma > maximo;
        const exceso = excede ? Math.abs(restanteRaw) : 0;

        // Texto "Restante por asignar" con badge amarillo si hay excedente
        const textos = document.querySelectorAll(`.ajF-restante-prod[data-restante="${prodId}"]`);
        textos.forEach(texto => {
            if (excede) {
                texto.innerHTML =
                    `Restante por asignar: <b>0.00</b>
                     <span class="badge bg-warning text-dark ms-2">
                         <i class="bi bi-exclamation-triangle-fill"></i>
                         Excedente: ${exceso.toFixed(2)}
                     </span>`;
            } else {
                texto.innerHTML = `Restante por asignar: <b>${restante.toFixed(2)}</b>`;
            }
        });

        // SweetAlert toast: solo una vez por producto hasta que vuelva a estar dentro del límite
        if (excede && !ajF_productosConExcedenteNotificado.has(prodId)) {
            ajF_productosConExcedenteNotificado.add(prodId);

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Material excedente detectado',
                html: `Ingresaste más de lo pendiente.<br><b>Exceso: ${exceso.toFixed(2)}</b>`,
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
            });
        } else if (!excede) {
            ajF_productosConExcedenteNotificado.delete(prodId);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // Aplicar faltantes (resetea compra)
    // ═══════════════════════════════════════════════════════════
    function ajF_aplicarFaltantesCompra() {
        const compra_id = document.getElementById('ajF_compra_id').value;

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

                fetch(`/myvet/app/controllers/egresosController.php?action=aplicarFaltantesCompras&compra_id=${compra_id}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Actualizado',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
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

    // ═══════════════════════════════════════════════════════════
    // Procesar ajuste → envía TODO al backend (con o sin exceso)
    // ═══════════════════════════════════════════════════════════
    function ajF_procesarAjuste() {
        const formData = new FormData();

        formData.append('compra_id', document.getElementById('ajF_compra_id').value);

        // Distribución: iterar SOLO los hidden habilitados (fuente de verdad)
        let hayDatos = false;

        document.querySelectorAll('input.ajF-input-hidden').forEach(hidden => {
            if (hidden.disabled) return;

            const cant = parseFloat(hidden.value) || 0;
            if (cant <= 0) return;

            const prodId = hidden.dataset.prodId;
            const almId = hidden.dataset.almId;

            // Cantidad base (valor × factor) — se envía aunque exceda el pendiente
            formData.append(`distribucion[${prodId}][${almId}]`, hidden.value);

            // Cantidad visible con sufijo "1" (compatibilidad con backend actual)
            const vis = document.getElementById(`ajF_in_vis_${prodId}_${almId}`);
            if (vis && vis.value !== '') {
                formData.append(`distribucion[${prodId}][${almId}]1`, vis.value);
            }

            hayDatos = true;
        });

        if (!hayDatos) {
            return Swal.fire('Sin datos', 'Habilite al menos un almacén e ingrese cantidad.', 'warning');
        }

        Swal.fire({
            title: '¿Confirmar Ingreso?',
            text: 'Se afectará el stock de los almacenes habilitados.',
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