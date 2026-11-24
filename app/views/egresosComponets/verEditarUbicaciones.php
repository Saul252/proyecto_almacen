<!-- MODAL DE GESTIÓN DE UBICACIONES / DIRECCIONES -->
<div class="modal fade" id="modalGestionUbicaciones" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden bg-body text-body">

            <!-- ENCABEZADO -->
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-body">
                <h5 class="modal-title fw-semibold fs-5 text-body">
                    <i class="bi bi-geo-alt-fill me-2 text-primary"></i>Administrar Ubicaciones - <span
                        id="titulo_proveedor_ubicaciones" class="text-primary">Proveedor</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- CUERPO -->
            <div class="modal-body px-4 py-3 bg-body">

                <!-- VISTA 1: LISTADO Y BUSCADOR -->
                <div id="vistaListadoUbicaciones">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-3">
                        <!-- Buscador por nombre -->
                        <div class="input-group w-md-50">
                            <span
                                class="input-group-text bg-body border-secondary-subtle border-end-0 rounded-start-4 ps-3 text-body-secondary">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="buscadorUbicaciones"
                                class="form-control bg-body text-body border-secondary-subtle border-start-0 ps-0 rounded-end-4 shadow-none text-uppercase"
                                placeholder="Buscar sucursal por nombre...">
                        </div>
                        <!-- Botón para abrir modal de agregar nueva -->
                        <button type="button" class="btn btn-primary px-3 rounded-3 fw-semibold shadow-sm"
                            onclick="abrirModalNuevaDesdeGestion()">
                            <i class="bi bi-plus-lg me-1"></i> Nueva Ubicación
                        </button>
                    </div>

                    <!-- Tabla de Listado -->
                    <div class="table-responsive border border-secondary-subtle rounded-4 overflow-hidden">
                        <table class="table table-hover align-middle mb-0 bg-body">
                            <thead class="table-light text-uppercase fs-7 text-secondary">
                                <tr>
                                    <th class="ps-3">Nombre / Sucursal</th>
                                    <th>Dirección Completa</th>
                                    <th>Teléfono / Ext.</th>
                                    <th>Contacto</th>
                                    <th class="text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaUbicacionesProveedorBody">
                                <!-- Se llena dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- VISTA 2: FORMULARIO DE EDICIÓN (Oculto por defecto) -->
                <div id="vistaEdicionUbicacion" style="display: none;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h6 class="fw-bold text-primary m-0"><i class="bi bi-pencil-square me-2"></i>Editar Ubicación
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-3"
                            onclick="volverAListadoUbicaciones()">
                            <i class="bi bi-arrow-left me-1"></i> Volver al listado
                        </button>
                    </div>

                    <form id="formEditarUbicacion">
                        <input type="hidden" name="id" id="edit_ubicacion_id">
                        <input type="hidden" name="proveedor_id" id="edit_proveedor_id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary mb-1">Nombre / Sucursal
                                    *</label>
                                <input type="text" name="nombre" id="edit_nombre"
                                    class="form-control bg-body text-body border-secondary-subtle text-uppercase rounded-3 shadow-none"
                                    required style="min-height: 44px;">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary mb-1">Teléfono</label>
                                <input type="text" name="telefono" id="edit_telefono"
                                    class="form-control bg-body text-body border-secondary-subtle text-uppercase rounded-3 shadow-none"
                                    style="min-height: 44px;">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-body-secondary mb-1">Dirección Completa
                                    *</label>
                                <textarea name="direccion_completa" id="edit_direccion_completa"
                                    class="form-control bg-body text-body border-secondary-subtle text-uppercase rounded-3 shadow-none"
                                    rows="2" required style="min-height: 70px;"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary mb-1">Contacto /
                                    Responsable</label>
                                <input type="text" name="contacto" id="edit_contacto"
                                    class="form-control bg-body text-body border-secondary-subtle text-uppercase rounded-3 shadow-none"
                                    style="min-height: 44px;">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-body-secondary mb-1">Extensión</label>
                                <input type="text" name="extencion" id="edit_extencion"
                                    class="form-control bg-body text-body border-secondary-subtle text-uppercase rounded-3 shadow-none"
                                    style="min-height: 44px;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4 rounded-3 fw-semibold"
                                onclick="volverAListadoUbicaciones()">Cancelar</button>
                            <button type="button"
                                class="btn btn-primary px-4 rounded-3 text-white fw-semibold shadow-sm"
                                onclick="guardarCambiosUbicacion(event)">
                                <i class="bi bi-check2-circle me-1"></i>Actualizar Ubicación
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- PIE -->
            <div class="modal-footer px-4 py-3 border-0 bg-body-tertiary">
                <button type="button" class="btn btn-secondary px-4 rounded-3 fw-semibold"
                    data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>
<script>
    // Variable global para almacenar las ubicaciones actuales del proveedor en memoria (para el filtro de búsqueda)
    let ubicacionesGlobales = [];
    let proveedorActualId = 0;
    let proveedorActualNombre = '';

    /**
     * Abre el modal de administración de ubicaciones y carga sus registros
     */
    function abrirModalGestionUbicaciones(proveedorId, nombreProveedor) {
        if (!proveedorId || proveedorId <= 0) {
            Swal.fire('Atención', 'Seleccione un proveedor válido', 'warning');
            return;
        }

        proveedorActualId = proveedorId;
        proveedorActualNombre = nombreProveedor;

        document.getElementById('titulo_proveedor_ubicaciones').textContent = nombreProveedor;

        // Mostrar siempre la vista de listado por defecto
        volverAListadoUbicaciones();

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('modalGestionUbicaciones'));
        modal.show();

        // Cargar datos vía fetch
        cargarListadoUbicaciones(proveedorId);
    }

    /**
     * Petición para obtener todas las ubicaciones del proveedor
     */
    function cargarListadoUbicaciones(proveedorId) {
        console.log('data');
        const tbody = document.getElementById('tablaUbicacionesProveedorBody');
        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Cargando ubicaciones...</td></tr>`;

        fetch(`/myvet/app/controllers/proveedoresController.php?action=listarDireccionesProveedor&proveedor_id=${proveedorId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    ubicacionesGlobales = data.data;
                    renderizarTablaUbicaciones(ubicacionesGlobales);
                } else {
                    ubicacionesGlobales = [];
                    renderizarTablaUbicaciones([]);
                }
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-3">Error al conectar con el servidor</td></tr>`;
            });
    }

    /**
     * Renderiza el arreglo de ubicaciones en la tabla HTML
     */
    function renderizarTablaUbicaciones(ubicaciones) {
        const tbody = document.getElementById('tablaUbicacionesProveedorBody');

        if (ubicaciones.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No se encontraron ubicaciones registradas.</td></tr>`;
            return;
        }

        let html = '';
        ubicaciones.forEach(u => {
            let telefonoInfo = u.telefono ? u.telefono + (u.extencion ? ' Ext. ' + u.extencion : '') : 'N/D';
            html += `
            <tr>
                <td class="fw-semibold text-uppercase">${u.nombre}</td>
                <td class="text-uppercase">${u.direccion_completa}</td>
                <td>${telefonoInfo}</td>
                <td class="text-uppercase">${u.contacto || 'N/D'}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="cargarUbicacionParaEditar(${u.id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarUbicacion(${u.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        });
        tbody.innerHTML = html;
    }

    /**
     * Filtro de búsqueda en tiempo real por el campo "nombre"
     */
    document.getElementById('buscadorUbicaciones').addEventListener('input', function () {
        const texto = this.value.toLowerCase().trim();
        const filtradas = ubicacionesGlobales.filter(u => u.nombre.toLowerCase().includes(texto));
        renderizarTablaUbicaciones(filtradas);
    });

    /**
     * REUTILIZA TU ACCIÓN: obtenerUbicacion para cargar los datos en el formulario de edición
     */
    function cargarUbicacionParaEditar(idUbicacion) {
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch(`/myvet/app/controllers/proveedoresController.php?action=obtenerUbicacion&id=${idUbicacion}`)
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    const u = data.data;

                    // Rellenar formulario de edición
                    document.getElementById('edit_ubicacion_id').value = u.id;
                    document.getElementById('edit_proveedor_id').value = u.id_proveedor;
                    document.getElementById('edit_nombre').value = u.nombre;
                    document.getElementById('edit_telefono').value = u.telefono;
                    document.getElementById('edit_direccion_completa').value = u.direccion_completa;
                    document.getElementById('edit_contacto').value = u.contacto;
                    document.getElementById('edit_extencion').value = u.extencion;

                    // Cambiar de vista a edición dentro del modal
                    document.getElementById('vistaListadoUbicaciones').style.display = 'none';
                    document.getElementById('vistaEdicionUbicacion').style.display = 'block';
                } else {
                    Swal.fire('Error', data.message || 'No se pudo obtener la información', 'error');
                }
            })
            .catch(err => {
                Swal.close();
                console.error(err);
                Swal.fire('Error', 'Fallo de conexión al buscar ubicación', 'error');
            });
    }

    /**
     * Envía los cambios actualizados al backend
     */
    function guardarCambiosUbicacion(e) {
        const form = document.getElementById('formEditarUbicacion');
        const btn = e.target;
        const formData = new FormData(form);

        if (!form.nombre.value.trim() || !form.direccion_completa.value.trim()) {
            Swal.fire('Atención', 'El nombre y la dirección completa son obligatorios', 'warning');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Actualizando...';

        fetch('/myvet/app/controllers/proveedoresController.php?action=actualizarUbicacion', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Actualizado',
                        text: 'La ubicación se modificó correctamente',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Regresar al listado y refrescar datos
                    volverAListadoUbicaciones();
                    cargarListadoUbicaciones(proveedorActualId);
                } else {
                    Swal.fire('Error', data.message || 'No se pudo actualizar', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Error de conexión', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Actualizar Ubicación';
            });
    }

    /**
     * Alterna la vista dentro del modal de vuelta a la tabla
     */
    function volverAListadoUbicaciones() {
        document.getElementById('vistaEdicionUbicacion').style.display = 'none';
        document.getElementById('vistaListadoUbicaciones').style.display = 'block';
        document.getElementById('buscadorUbicaciones').value = '';
    }

    /**
     * Abre el modal independiente para registrar una nueva dirección (puedes enlazarlo a tu función anterior)
     */
    function abrirModalNuevaDesdeGestion() {
        // Si tienes separado tu modal de nueva ubicación, puedes cerrarlo o llamarlo así:
        const modalGestion = bootstrap.Modal.getInstance(document.getElementById('modalGestionUbicaciones'));


        // Llamar a tu función anterior de nueva dirección pasando el ID y nombre actual
        abrirModalNuevaDireccion(proveedorActualId, proveedorActualNombre);
    }
</script>