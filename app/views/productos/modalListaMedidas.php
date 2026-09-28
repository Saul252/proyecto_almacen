<!-- =========================================
MODAL LISTA DE MEDIDAS
========================================= -->
<div class="modal fade" id="modalListaMedidas" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content  shadow-lg overflow-hidden">

            <!-- HEADER: Gradiente adaptativo -->
            <div class="modal-header bg-primary bg-gradient text-white  p-4 position-relative">

                <div class="pe-4">
                    <h5 class="modal-title fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-rulers fs-4"></i>
                        Medidas Disponibles
                    </h5>

                    <small id="subtituloListaMedidas" class="text-white-50 fw-medium">
                        Cargando detalles...
                    </small>
                </div>

                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>

            </div>

            <!-- BODY -->
            <div class="modal-body p-0 bg-body">

                <!-- Barra superior de acciones -->
                <div class="p-3 border-bottom bg-body-tertiary d-flex justify-content-between align-items-center">
                    <span class="text-body-secondary small fw-semibold text-uppercase tracking-wide">
                        Lista de Equivalencias
                    </span>
                    <button type="button" id="agregarMedida"
                        class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Agregar Medida</span>
                    </button>
                </div>

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="bg-body-tertiary border-bottom">

                            <tr>
                                <th class="ps-4 text-body-secondary small text-uppercase">Nombre Medida</th>
                                <th class="text-body-secondary small text-uppercase">Equivalencia</th>
                                <th class="text-end pe-4 text-body-secondary small text-uppercase">Acciones</th>
                            </tr>

                        </thead>

                        <tbody id="tablaCuerpoMedidas">

                            <!-- JS Populate -->

                        </tbody>

                    </table>

                </div>

                <!-- EMPTY STATE -->
                <div id="listaVacia" class="text-center py-5 d-none">

                    <i class="bi bi-inbox fs-1 text-body-tertiary d-block mb-2"></i>

                    <p class="text-body-secondary fw-medium mb-0">
                        No hay medidas adicionales configuradas para este producto.
                    </p>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-top bg-body-tertiary px-4 py-3">

                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                    data-bs-dismiss="modal">
                    Cerrar
                </button>

            </div>

        </div>

    </div>

</div>


<!-- =========================================
MODAL EDITAR MEDIDA
========================================= -->
<div class="modal fade" id="modalEditarMedida" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white px-4 py-3 ">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-25 text-primary p-2 rounded-3 d-flex align-items-center justify-content-center"
                        style="width: 42px; height: 42px;">
                        <i class="bi bi-pencil-square fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white">Editar Medida</h5>
                        <p class="text-white-50 small mb-0">Actualiza los parámetros de conversión</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- FORM -->
            <form id="formEditarMedida">
                <input type="hidden" id="edit_medida_id" name="id">
                <input type="hidden" id="edit_producto_id" name="producto_id">

                <div class="modal-body p-4 ">

                    <!-- NOMBRE -->
                    <div class="mb-3">
                        <label for="edit_nombre_medida"
                            class="form-label fw-bold small text-muted text-uppercase tracking-wider">
                            Nombre de la medida
                        </label>
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text  border-end-0 text-muted ps-3">
                                <i class="bi bi-tag"></i>
                            </span>
                            <input type="text" id="edit_nombre_medida" class="form-control border-start-0 ps-2 fs-6 "
                                name="nombre_edit" placeholder="Ej. Caja, Gramo, Pieza" required>
                        </div>
                    </div>

                    <!-- EQUIVALENCIA (DISEÑO TARJETA MEJORADO) -->


                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-uppercase text-secondary mb-2">
                            Factor de equivalencia
                        </label>

                        <div class="border rounded-3 p-3  shadow-sm">
                            <div class="row g-2 align-items-center">

                                <!-- Valor / Unidad Adicional -->
                                <div class="col-5">
                                    <div class="input-group input-group-sm">
                                        <input type="number"
                                            class="form-control fw-bold text-primary text-center  border-end-0"
                                            id="edit_equivalencia" name="equivalencia" step="0.000000001" min="0.00001"
                                            required>
                                        <span
                                            class="input-group-text  text-muted fw-normal px-2 border-start-0 text-truncate"
                                            id="edit_unidad_text" style="max-width: 80px; font-size: 0.75rem;">
                                            Unidades
                                        </span>
                                    </div>
                                </div>

                                <!-- Conector Central -->
                                <div class="col-2 text-center text-muted fw-semibold" style="font-size: 0.8rem;">
                                    <span class="text-uppercase tracking-wider text-muted opacity-75"
                                        style="font-size: 0.65rem;">EQUIV. A</span>
                                </div>

                                <!-- Valor / Unidad Base -->
                                <div class="col-5">
                                    <div class="input-group input-group-sm">
                                        <input type="number"
                                            class="form-control fw-bold text-success text-center  border-end-0"
                                            id="edit_base" name="base" step="0.000000001" min="0.00001" required>
                                        <span
                                            class="input-group-text  text-muted fw-normal px-2 border-start-0 text-truncate"
                                            id="unidadMedidaMostrar" style="max-width: 80px; font-size: 0.75rem;">
                                            Base
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                </div>

                <!-- FOOTER -->
                <div class="modal-footer   px-4 py-3 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-secondary"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm">
                        <i class="bi bi-check2 me-1"></i> Guardar Cambios
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<!-- =========================================
ESTILOS ADICIONALES (CSS)
========================================= -->
<style>
    /* Z-Index para modales anidados */
    #modalEditarMedida {
        z-index: 1065 !important;
    }

    #modalEditarMedida+.modal-backdrop {
        z-index: 1060 !important;
    }

    .miSwalZ {
        z-index: 10000 !important;
    }

    .tracking-wide {
        letter-spacing: 0.05em;
    }
</style>


<!-- =========================================
JAVASCRIPT
========================================= -->
<script>
    const URL_MEDIDAS = '/myvet/app/controllers/productosController.php';

    let ultimaMedidaProductoId = 0;
    let ultimaMedidaAlmacenId = 0;
    let ultimaMedidaNombreProducto = '';
    let ultimaUnidadMedida = '';

    // =========================================
    // VER LISTA MEDIDAS
    // =========================================
    async function verListaMedidas(idProducto, idAlmacen, nombreProducto, unidad_medida) {
        ultimaMedidaProductoId = idProducto;
        ultimaMedidaAlmacenId = idAlmacen;
        ultimaMedidaNombreProducto = nombreProducto;
        ultimaUnidadMedida = unidad_medida;

        const tbody = document.getElementById('tablaCuerpoMedidas');
        const subtitulo = document.getElementById('subtituloListaMedidas');
        const emptyState = document.getElementById('listaVacia');

        subtitulo.innerText = `Producto: ${nombreProducto}`;
        emptyState.classList.add('d-none');

        tbody.innerHTML = `
        <tr>
            <td colspan="3" class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <span class="ms-2 text-body-secondary fw-medium">Cargando medidas...</span>
            </td>
        </tr>
    `;

        const modalEl = document.getElementById('modalListaMedidas');
        let myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        myModal.show();

        try {
            const resp = await fetch(`${URL_MEDIDAS}?action=obtnerMedidas&id=${idProducto}`);

            if (!resp.ok) throw new Error('Error en la red');

            const data = await resp.json();
            tbody.innerHTML = '';

            if (data.status && data.producto.medidas && data.producto.medidas.length > 0) {

                $('#agregarMedida').attr(
                    'onclick',
                    `prepararNuevaMedida(${idProducto}, ${idAlmacen}, '${nombreProducto}', '${unidad_medida}')`
                );

                // =========================================================
                // 1. BUSCAR LA MEDIDA BASE (EQUIVALENCIA = 1)
                // =========================================================
                const medidaBase = data.producto.medidas.find(m => (parseFloat(m.equivalencia) || 0) === 1);
                const idBase = medidaBase ? Number(medidaBase.id) : null;

                data.producto.medidas.forEach(m => {
                    const medidaData = encodeURIComponent(JSON.stringify(m));

                    // Convertir a número por seguridad
                    const equiv = parseFloat(m.equivalencia) || 0;
                    const idActual = Number(m.id);

                    // Cálculo de la relación inversa (1 / equivalencia)
                    const inversa = equiv > 0 ? (1 / equiv) : 0;

                    // Formateo inteligente para limpiar ceros innecesarios a la derecha
                    const equivFormateada = Number(equiv.toFixed(6));
                    const inversaFormateada = Number(inversa.toFixed(2));

                    // =========================================================
                    // 2. REGLA DE PROTECCIÓN DE ELIMINACIÓN
                    // =========================================================
                    // Se protege si:
                    // - Su equivalencia es 1
                    // - Su nombre coincide con la unidad de medida principal
                    // - Su ID es menor o igual al ID de la medida base (idActual <= idBase)
                    const esEquivBase = (equivFormateada === 1);
                    const esUnidadBase = (m.nombre === unidad_medida);
                    const esMenorOIgualQueBase = (idBase !== null && idActual <= idBase);

                    const esProtegido = esEquivBase || esUnidadBase || esMenorOIgualQueBase;

                    let boton = esProtegido
                        ? ''
                        : `<button class="btn btn-sm btn-light-hover text-danger rounded-circle  p-2 d-inline-flex align-items-center justify-content-center"
                                title="Eliminar medida"
                                style="width: 34px; height: 34px;"
                                onclick="eliminarMedida(${m.id})">
                            <i class="bi bi-trash-fill fs-6"></i>
                        </button>`;

                    // Determinar la frase según el tipo de equivalencia
                    let textoRelacion = '';

                    if (equiv >= 1) {
                        textoRelacion = `
                        <span class="badge bg-body-tertiary text-body border border-translucent rounded-pill px-3 py-2 fw-normal d-inline-flex align-items-center gap-2 shadow-sm">
                            <span><strong class="text-primary">${equivFormateada}</strong> ${m.nombre}(s)= <strong>1</strong> ${unidad_medida} </span>
                        </span>
                    `;
                    } else {
                        textoRelacion = `
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-normal d-inline-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-arrow-left-right opacity-75"></i>
                            <span> <strong>1</strong> ${m.nombre} = <strong>${inversaFormateada}</strong> ${unidad_medida}(s) </span>
                        </span>
                    `;
                    }

                    const fila = `
                    <tr class="align-middle">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 bg-body-tertiary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-rulers text-primary fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-body mb-0">${m.nombre}</div>
                                    <small class="text-body-secondary fs-7">
                                        Valor base: ${equivFormateada}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            ${textoRelacion}
                        </td>

                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <button class="btn btn-sm btn-light-hover text-primary rounded-circle  p-2 d-inline-flex align-items-center justify-content-center"
                                        title="Editar medida"
                                        style="width: 34px; height: 34px;"
                                        onclick="abrirEditarMedida('${medidaData}','${unidad_medida}')">
                                    <i class="bi bi-pencil-fill fs-6"></i>
                                </button>
                                ${boton}
                            </div>
                        </td>
                    </tr>
                `;

                    tbody.insertAdjacentHTML('beforeend', fila);
                });
            } else {
                emptyState.classList.remove('d-none');
                $('#agregarMedida').attr(
                    'onclick',
                    `prepararNuevaMedida(${idProducto}, ${idAlmacen}, '${nombreProducto}', '${unidad_medida}')`
                );
            }

        } catch (error) {
            console.error("Error:", error);
            tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center py-4 text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    No se pudo cargar la información
                </td>
            </tr>
        `;
        }
    }
    function recargarModalMedidas() {
        verListaMedidas(
            ultimaMedidaProductoId,
            ultimaMedidaAlmacenId,
            ultimaMedidaNombreProducto,
            ultimaUnidadMedida
        );
    }

    // =========================================
    // ABRIR EDITAR
    // =========================================
    function abrirEditarMedida(data, unidadM) {
        // 1. Primero parseamos los datos
        const medida = JSON.parse(decodeURIComponent(data));
        console.log(medida);

        // 2. Calculamos la equivalencia de forma segura
        let equi = 0;
        let mayor = 1;
        let menor = 1;
        if (medida.equivalencia && Number(medida.equivalencia) !== 0) {
            let calculo = 1 / parseFloat(medida.equivalencia);

            // 1. Limpiamos el ruido de coma flotante si está muy cerca de un entero (ej: 5.9999 -> 6, 21.999912 -> 22)
            const tolerancia = 0.001;
            if (Math.abs(calculo - Math.round(calculo)) < tolerancia) {
                calculo = Math.round(calculo);
            }

            if (calculo < 1) {
                equi = Math.round(1 / calculo);
                mayor = 1;
            } else {
                equi = 1;
                mayor = calculo;
            }
        }

        // 3. Obtenemos las referencias del DOM
        const inputId = document.getElementById('edit_medida_id');
        const inputProdId = document.getElementById('edit_producto_id');
        const inputNombre = document.getElementById('edit_nombre_medida');
        const inputBase = document.getElementById('edit_base');
        const inputEquiv = document.getElementById('edit_equivalencia');
        const textMOstrarMedida = document.getElementById('unidadMedidaMostrar');
        const textUnidad = document.getElementById('edit_unidad_text');

        // 4. Asignamos valores
        if (inputId) inputId.value = medida.id;
        if (inputProdId) inputProdId.value = medida.producto_id;
        if (inputEquiv) inputEquiv.value = equi;
        if (inputBase) inputBase.value = mayor;
        if (textUnidad) textUnidad.innerText = medida.nombre;
        if (textMOstrarMedida) textMOstrarMedida.innerText = unidadM;
        if (inputNombre) inputNombre.value = medida.nombre;

        // 5. Mostramos el modal de Bootstrap
        const modalEl = document.getElementById('modalEditarMedida');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }

    // =========================================
    // GUARDAR CAMBIOS
    // =========================================
    document.getElementById('formEditarMedida').addEventListener('submit', async function (e) {
        e.preventDefault();

        try {
            Swal.fire({
                title: 'Actualizando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                customClass: { popup: 'miSwalZ' }
            });

            const formData = new FormData(this);

            const resp = await fetch(`${URL_MEDIDAS}?action=actualizarMedidaAdicional`, {
                method: 'POST',
                body: formData
            });

            const data = await resp.json();
            Swal.close();

            if (data.status || data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Actualizado',
                    text: 'La medida fue actualizada correctamente',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: { popup: 'miSwalZ' }
                });

                const modalEditar = bootstrap.Modal.getInstance(document.getElementById('modalEditarMedida'));
                if (modalEditar) modalEditar.hide();

                recargarModalMedidas();

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo actualizar',
                    customClass: { popup: 'miSwalZ' }
                });
            }

        } catch (error) {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Falló la comunicación con el servidor',
                customClass: { popup: 'miSwalZ' }
            });
        }
    });

    // =========================================
    // ELIMINAR MEDIDA
    // =========================================
    async function eliminarMedida(id) {
        const swalConfig = {
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { container: 'miSwalZ' }
        };

        const confirmacion = await Swal.fire(swalConfig);

        if (confirmacion.isConfirmed) {
            try {
                Swal.fire({
                    title: 'Eliminando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    customClass: { container: 'miSwalZ' }
                });

                const formData = new FormData();
                formData.append('id', id);

                const resp = await fetch(`${URL_MEDIDAS}?action=eliminarMedidaAdicional`, {
                    method: 'POST',
                    body: formData
                });

                const data = await resp.json();

                if (data.status || data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: 'La medida ha sido removida.',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { container: 'miSwalZ' }
                    });
                    recargarModalMedidas();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar',
                        customClass: { container: 'miSwalZ' }
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Fallo de comunicación con el servidor',
                    customClass: { container: 'miSwalZ' }
                });
            }
        }
    }
</script>