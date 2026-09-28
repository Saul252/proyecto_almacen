<!-- Modal de Almacenes e Inventario Inicial (Estilo iOS / Modo Oscuro) -->
<div class="modal fade" id="modalInventarioInicialAlmacenes" tabindex="-1" aria-labelledby="modalInventarioInicialLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- Header limpio estilo iOS -->
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold fs-5 text-body" id="modalInventarioInicialLabel">
                    Configuración de Inventario y Precios
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">

                <!-- Buscador de Producto -->
                <div class="mb-4">
                    <label class="form-label text-secondary fw-semibold small mb-1">
                        <i class="bi bi-search me-1 text-primary"></i> Añadir Producto
                    </label>
                    <div class="input-group input-group-sm rounded-3 overflow-hidden shadow-sm">
                        <select id="buscadorProductosEditar" class="form-select border-0 bg-body-secondary text-body"
                            onchange="cambio()">
                            <option value="">Escribe SKU o nombre...</option>
                        </select>
                        <button type="button" class="btn btn-primary d-flex align-items-center px-3 border-0"
                            onclick="abrirModalProducto()" title="Agregar nuevo producto">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Costo Total de la Mercancía -->
                <div class="mb-4 p-3 rounded-4 bg-body-secondary border border-secondary-subtle bg-opacity-50">
                    <label for="costoTotal" class="form-label small fw-bold text-secondary mb-1">Costo Total de la
                        mercancía por introducir</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text border-0 bg-transparent text-primary fw-bold">$</span>
                        <input type="number" id="costoTotal" name="costoTotal" value="0" step="0.01"
                            class="form-control form-control-sm border-0 bg-transparent text-body fw-bold fs-6 shadow-none">
                    </div>
                </div>

                <!-- 🔹 PRECIOS GLOBALES (Tarjeta flotante estilo iOS) -->
                <div class="card border-0 bg-body-secondary mb-4 p-3 rounded-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-primary fw-bold mb-0 fs-6">
                            <i class="bi bi-tags me-1"></i> Precios Globales <span
                                class="text-secondary fw-normal small">(Aplica para almacenes seleccionados)</span>
                        </h6>
                        <!-- Indicador visual elegante de la unidad -->
                        <span
                            class="badge bg-primary bg-opacity-15 text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold">
                            Unidad: <span id="unidadIngreso">--</span>
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-secondary small fw-bold">Precio Minorista</label>
                            <input type="number" step="0.01" id="global_precio_minorista"
                                class="form-control form-control-sm rounded-3 border-0 bg-body text-body shadow-none"
                                placeholder="0.00" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small fw-bold">Precio Mayorista</label>
                            <input type="number" step="0.01" id="global_precio_mayorista"
                                class="form-control form-control-sm rounded-3 border-0 bg-body text-body shadow-none"
                                placeholder="0.00" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small fw-bold">Precio Distribuidor</label>
                            <input type="number" step="0.01" id="global_precio_distribuidor"
                                class="form-control form-control-sm rounded-3 border-0 bg-body text-body shadow-none"
                                placeholder="0.00" value="0">
                        </div>
                    </div>

                    <hr class="border-secondary opacity-25 my-4">

                    <!-- 🔹 TABLA DE ALMACENES CON CABECERA FIJA Y SCROLL -->
                    <!-- 🔹 TABLA DE ALMACENES CON CABECERA FIJA Y SCROLL -->
                    <div class="table-responsive rounded-4 border border-secondary-subtle"
                        style="max-height: 250px; overflow-y: auto;">


                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Seleccionar</th>
                                    <th>Nombre del Almacén</th>
                                    <th>Stock Inicial</th>
                                    <th>Stock Mínimo</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-almacenes-body">
                                <!-- Aquí se insertarán las filas dinámicamente con JavaScript -->
                            </tbody>
                        </table>
                        </tbody>
                        </table>
                    </div>

                </div>

                <!-- Footer minimalista -->
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-light btn-sm px-4 rounded-pill text-secondary fw-semibold"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold shadow-sm"
                        onclick="enviarFormularioProducto()">Guardar Producto</button>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    // Función centralizada para cargar los almacenes del usuario de forma segura
    async function cargarAlmacenesModal() {
        const tbody = document.getElementById('tabla-almacenes-body');

        try {
            const response = await fetch('/myvet/app/controllers/accesoController.php?action=getAlmacenesUsuario');
            const almacenes = await response.json();

            tbody.innerHTML = ''; // Limpiamos contenedor

            if (!almacenes || almacenes.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">No hay almacenes disponibles</td></tr>`;
                return;
            }

            almacenes.forEach(a => {
                const nombreSeguro = escapeHtml(a.nombre);

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center align-middle">
                        <div class="form-check d-flex justify-content-center m-0">
                            <input type="checkbox" class="form-check-input check-almacen shadow-none"
                                style="transform: scale(1.1); cursor: pointer;"
                                data-id="${a.id}" checked
                                onchange="toggleAlmacen(${a.id})">
                        </div>
                    </td>
                    <td class="align-middle">
                        <span class="fw-semibold text-body">${nombreSeguro}</span>
                    </td>
                    <td class="align-middle">
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01"
                                class="form-control bg-body text-body input-stock-${a.id}"
                                data-almacen-id="${a.id}" value="0">
                            <span class="input-group-text border-0 bg-body-secondary text-secondary fw-bold text-unidad-dinamica">--</span>
                        </div>
                    </td>
                    <td class="align-middle">
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01"
                                class="form-control form-control-sm bg-body text-body input-min-${a.id}"
                                data-almacen-id="${a.id}" value="0">
                            <span class="input-group-text border-0 bg-body-secondary text-secondary fw-bold text-unidad-dinamica">--</span>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } catch (error) {
            console.error('Error al cargar los almacenes:', error);
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-3">Error al cargar almacenes</td></tr>`;
        }
    }

    // Función auxiliar para escapar texto plano en JS
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function (m) { return map[m]; });
    }

    async function recargarProductosEditar() {
        const $select = $('#buscadorProductosEditar');
        const url = `/myvet/app/controllers/accesoController.php?action=obtenerProductosAlmacen`;

        try {
            const resp = await fetch(url);
            const textoServidor = await resp.text();
            let res = JSON.parse(textoServidor);

            if (!res.success) return;

            $select.empty(); $select.append(new Option("Escribe SKU o nombre...", "", true, true));

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
                        'data-stock': pr.stock || 1
                    });

                    $select.append(option);
                });
            }

            $select.trigger('change.select2');
        } catch (errJson) {
            console.error("❌ Error al cargar productos del modal:", errJson);
        }
    }

    function toggleAlmacen(almacenId) {
        let checkbox = document.querySelector(`.check-almacen[data-id="${almacenId}"]`);
        let stockInput = document.querySelector(`.input-stock-${almacenId}`);
        let minInput = document.querySelector(`.input-min-${almacenId}`);

        if (!checkbox.checked) {
            stockInput.setAttribute('disabled', 'true');
            minInput.setAttribute('disabled', 'true');
            stockInput.dataset.prevVal = stockInput.value;
            minInput.dataset.prevVal = minInput.value;
            stockInput.value = '';
            minInput.value = '';
        } else {
            stockInput.removeAttribute('disabled');
            minInput.removeAttribute('disabled');
            if (stockInput.dataset.prevVal) stockInput.value = stockInput.dataset.prevVal;
            if (minInput.dataset.prevVal) minInput.value = minInput.dataset.prevVal;
        }
    }

    function cambio() {
        let $opcion = $('#buscadorProductosEditar').find(':selected');

        let precioMinorista = $opcion.attr('data-premin') || 0;
        let precioMayorista = $opcion.attr('data-premat') || 0;
        let precioDistribuidor = $opcion.attr('data-predis') || 0;
        let unidadReporte = $opcion.attr('data-ur') || '--';

        document.getElementById('global_precio_minorista').value = precioMinorista;
        document.getElementById('global_precio_mayorista').value = precioMayorista;
        document.getElementById('global_precio_distribuidor').value = precioDistribuidor;

        $('#unidadIngreso').text(unidadReporte);
        $('.text-unidad-dinamica').text(unidadReporte);
    }

    function enviarFormularioProducto() {
        let pMinorista = document.getElementById('global_precio_minorista').value;
        let pMayorista = document.getElementById('global_precio_mayorista').value;
        let pDistribuidor = document.getElementById('global_precio_distribuidor').value;

        let formData = new FormData();
        let $select = $('#buscadorProductosEditar');
        let opcionSeleccionada = $select.find(':selected');

        let productoId = opcionSeleccionada.val();
        let sku = opcionSeleccionada.attr('data-sku') || '';
        let factorConversion = opcionSeleccionada.attr('data-factor') || 1;

        if (!productoId) {
            alert('Por favor selecciona un producto.');
            return;
        }

        formData.append('producto_id', productoId);
        formData.append('sku', sku);
        formData.append('factor_conversion', factorConversion);

        let checkboxes = document.querySelectorAll('.check-almacen');
        let seleccionados = 0;

        checkboxes.forEach(cb => {
            let id = cb.getAttribute('data-id');

            if (cb.checked) {
                seleccionados++;
                let stockInput = document.querySelector(`.input-stock-${id}`).value || 0;
                let stockMinimo = document.querySelector(`.input-min-${id}`).value || 0;
                let stockFinal = parseFloat(stockInput) * parseFloat(factorConversion);

                formData.append(`almacenes[${id}][stock]`, stockFinal);
                formData.append(`almacenes[${id}][stock_minimo]`, stockMinimo);
                formData.append(`almacenes[${id}][precio_minorista]`, pMinorista);
                formData.append(`almacenes[${id}][precio_mayorista]`, pMayorista);
                formData.append(`almacenes[${id}][precio_distribuidor]`, pDistribuidor);
            }
        });

        if (seleccionados === 0) {
            alert('Debes seleccionar al menos un almacén.');
            return;
        }

        fetch('/myvet/app/controllers/almacenes.php?action=guardarCantidadesIniciales', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        confirmButtonColor: '#0d6efd', // Color acorde a Bootstrap primaria
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: data.message || 'Ocurrió un error inesperado.',
                        confirmButtonColor: '#0d6efd',
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                console.error('Error en la petición:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo completar la solicitud. Inténtalo de nuevo.',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Cerrar'
                });
            });
    }

    // Función principal para abrir el modal de forma síncrona y limpia
    async function abrirModalInventarioInicial() {
        let modalElement = document.getElementById('modalInventarioInicialAlmacenes');
        let modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);

        // 1. Carga los almacenes frescos al abrir el modal
        await cargarAlmacenesModal();
        // 2. Carga los productos correspondientes
        await recargarProductosEditar();

        modal.show();
    }
</script>