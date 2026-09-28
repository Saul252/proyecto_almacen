<div class="modal fade" id="modalConfirmarCancelacion" tabindex="-1" aria-labelledby="modalConfirmarCancelacionLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarCancelacionLabel">Confirmar Cancelación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalTextoDetalle" class="fw-semibold mb-2"></p>
                <p id="modalTextoSub" class="text-muted small"></p>
                <div id="wrapperMotivoSinPago" class="d-none mt-3">
                    <label class="form-label small fw-bold">Motivo de la cancelación:</label>
                    <input type="text" id="inputMotivoSinPago" class="form-field form-control"
                        placeholder="Escriba el motivo...">
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <!-- Botón Cancelar: SOLO CIERRA EL MODAL MEDIANTE BOOTSTRAP -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Regresar
                </button>
                <div id="contenedorBotonesAccion" class="d-flex gap-2">
                    <!-- Los botones dinámicos se insertan desde JS -->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let usuario = <?= intval($_SESSION['rol'] ?? 0) ?>;
    /* ==========================================================================
       RENDERIZADOR CENTRALIZADO (COMBINA TODAS LAS NOTIFICACIONES)
       ========================================================================== */



    // Almacén global para acumular los ítems de todos los módulos
    const almacenNotificaciones = {
        mantenimientos: [],
        cancelaciones: [],
        traspasos: [],
        verificaciones: []
    };

    // Estados de control para Toastify
    const estadoToasts = {
        verificacion: { primeraCarga: true, ultimoConteo: 0 },
        mantenimiento: { primeraCarga: true, ultimoConteo: 0 },
        cancelacion: { primeraCarga: true, ultimoConteo: 0 },
        traspaso: { primeraCarga: true, ultimoConteo: 0 }
    };
    function renderizarListaNotificaciones() {
        const lista = document.getElementById('lista-notificaciones');
        const badge = document.getElementById('notif-badge');

        // Concatenar todos los HTMLs generados por cada módulo
        const todosLosHTML = [
            ...almacenNotificaciones.traspasos,
            ...almacenNotificaciones.cancelaciones,
            ...almacenNotificaciones.mantenimientos
        ];

        const totalNotificaciones = todosLosHTML.length;

        // 1. Actualizar el Badge con la suma total
        if (badge) {
            if (totalNotificaciones > 0) {
                badge.innerText = totalNotificaciones;
                badge.classList.remove('d-none');
                badge.style.display = 'inline-block';
            } else {
                badge.classList.add('d-none');
                badge.style.display = 'none';
            }
        }

        // 2. Renderizar la lista consolidada sin borrar los otros tipos
        if (lista) {
            if (totalNotificaciones === 0) {
                lista.innerHTML = '<div class="p-4 text-center text-body-secondary small">Sin notificaciones pendientes</div>';
            } else {
                lista.innerHTML = todosLosHTML.join('');
            }
        }
    }

    /* ==========================================================================
       CONSULTAS AL BACKEND
       ========================================================================== */
    function mantenimientoSistema() {
        verificarCancelacionesRecientes();
        verificarNotificaciones();
        if (usuario <= 2) {

            verificarMantenimientos();
            verificarSolicitudesCancelacion();
            verificarVerificaciones();
        }
    }
    /* ==========================================================================
       CONSULTA DE CANCELACIONES/ELIMINACIONES RECIENTES (RANGO 5 MINUTOS)
       ========================================================================== */
    // Set en memoria para evitar que se repita la notificación durante los 5 minutos
    const idsCancelacionesNotificadas = new Set();

    function verificarCancelacionesRecientes() {
        fetch('/myvet/app/controllers/ventasHistorialController.php?action=obtenerCancelacionesRecientes')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {

                    res.data.forEach(item => {
                        // Si el ID de la solicitud no ha sido notificado aún en este navegador
                        if (!idsCancelacionesNotificadas.has(item.id)) {

                            // 1. Guardar en memoria local
                            idsCancelacionesNotificadas.add(item.id);

                            // 2. Disparar Toastify único para todos los usuarios
                            if (typeof Toastify === "function") {
                                Toastify({
                                    text: `🗑️ VENTA ELIMINADA\nSe canceló la venta #${item.id_venta}\nMotivo: ${item.razon || 'Sin motivo especificado'}`,
                                    duration: 500,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    stopOnFocus: true,
                                    style: {
                                        background: "#dc3545",
                                        color: "#ffffff",
                                        borderRadius: "12px",
                                        fontWeight: "600",
                                        padding: "14px 18px",
                                        boxShadow: "0 4px 12px rgba(0,0,0,0.15)"
                                    }
                                }).showToast();
                            }

                            // 3. Recargar el listado de ventas si la pantalla del usuario la contiene
                            if (typeof getVentas === 'function') getVentas();
                        }
                    });
                }
            })
            .catch(err => console.error("Error cancelaciones recientes:", err));
    }

    function verificarSolicitudesCancelacion() {
        fetch('/myvet/app/controllers/ventasHistorialController.php?action=obtenerSolicitudesPendientes')
            .then(res => res.json())
            .then(res => {
                const items = res.data || res.items || [];
                const cantidad = items.length;
                const estado = estadoToasts.cancelacion;

                // Alerta Toastify individual
                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                    if (typeof Toastify === "function") {
                        const u = items[0] || {};
                        Toastify({
                            text: `⚠️ CANCELACIÓN SOLICITADA\nVenta #${u.id_venta || ''}\nMotivo: ${u.razon || 'Sin motivo'}`,
                            duration: 500,
                            close: true,
                            gravity: "top",
                            position: "right",
                            className: "toast-cancelacion toast-cancel-close",
                            style: { background: "#ffffff", color: "#000", borderRadius: "14px", padding: "16px 20px" },
                            onClick: () => window.location.href = "/myvet/app/controllers/ventasHistorialController.php"
                        }).showToast();
                    }
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                // Guardar HTMLs en el almacén
                almacenNotificaciones.cancelaciones = items.map(item => `
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-body-tertiary hover-notif">
                    <div style="flex: 1; line-height: 1.4;">
                        <b class="text-danger d-block small text-uppercase">Venta #${item.id_venta}</b>
                        <b class="d-block text-body-secondary" style="font-size: 0.75rem;">Motivo: ${item.razon || 'Sin especificación'}</b>
                        <div class="mt-1"><small class="text-body-tertiary" style="font-size: 0.70rem;">Por: ${item.usuario_nombre || 'Usuario'}</small></div>
                    </div>
                    <div class="d-flex gap-1 ms-2">
                        <button onclick="procesarAceptarCancelacion(${item.idVenta},${item.id},${item.pagado},${item.venta_total},'${item.folio}','${item.razon}')" class="btn btn-success btn-sm rounded-circle shadow-sm" style="width:32px; height:32px;" title="Aceptar"><i class="bi bi-check-lg"></i></button>
                        <button onclick="procesarEliminarCancelacion(${item.id})" class="btn btn-danger btn-sm rounded-circle shadow-sm" style="width:32px; height:32px;" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </div>
                </div>`);

                renderizarListaNotificaciones();
            })
            .catch(err => console.error("Error cancelaciones:", err));
    }

    function verificarNotificaciones() {
        fetch('/myvet/app/backend/movimientos/get_notificaciones_traspaso.php?t=' + Date.now())
            .then(res => res.json())
            .then(data => {
                const items = data.items || [];
                const cantidad = parseInt(data.cantidad) || 0;
                const estado = estadoToasts.traspaso;

                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                    if (typeof Toastify === "function") {
                        const u = items[0] || {};
                        Toastify({
                            text: `📦 TRASPASO RECIBIDO\n${u.emisor} envió ${u.cantidad_texto || u.cantidad} de ${u.producto}`,
                            duration: 500,
                            close: true,
                            gravity: "top",
                            position: "right",
                            className: "toast-traspaso toast-red-close",
                            style: { background: "#ffffff", color: "#000", borderRadius: "14px", padding: "16px 20px" },
                            onClick: () => window.location.href = "/myvet/app/controllers/almacenes.php"
                        }).showToast();
                    }
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                // Guardar HTMLs en el almacén
                almacenNotificaciones.traspasos = items.map(item => `
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-white hover-notif">
                    <div style="flex: 1; line-height: 1.4;">
                        <b class="text-primary d-block small text-uppercase text-success">${item.producto}</b>
                        <b class="d-block text-body-secondary text-success" style="font-size: 0.75rem;">De: ${item.emisor}</b>
                        <div class="mt-1"><b class="text-primary d-block small text-uppercase text-success">${item.cantidad_texto || (item.cantidad + ' PZA')}</b></div>
                    </div>
                    <button onclick="procesarRecepcionRapida(${item.id})" class="btn btn-success btn-sm rounded-circle shadow-sm" style="width:32px; height:32px;"><i class="bi bi-check-lg"></i></button>
                </div>`);

                renderizarListaNotificaciones();
            })
            .catch(err => console.error("Error traspasos:", err));
    }

    function verificarMantenimientos() {
        fetch("/myvet/app/controllers/mantenimientosController.php?action=listarProximoMantenimiento")
            .then(r => r.json())
            .then(data => {
                const items = Array.isArray(data) ? data : [];
                const cantidad = items.length;
                const estado = estadoToasts.mantenimiento;

                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                    const item = items[0];
                    Toastify({
                        text: `🚗 PRÓXIMO MANTENIMIENTO\n${item.estado}\n\n${item.vehiculo} ${item.placas}`,
                        duration: 500,
                        gravity: "top",
                        position: "right",
                        style: { background: "#ffffff", color: "#111", borderLeft: "5px solid #ffc107", borderRadius: "15px", padding: "18px" },
                        onClick: () => window.location.href = "/myvet/app/controllers/mantenimientosController.php"
                    }).showToast();
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                // Guardar HTMLs en el almacén
                almacenNotificaciones.mantenimientos = items.map(item => {
                    const dias = parseInt(item.dias_restantes);
                    const color = dias <= 0 ? "danger" : (dias <= 3 ? "warning" : "success");
                    return `
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom hover-notif">
                        <div>
                            <div class="small"><b>${item.estado}</b></div>
                            <div class="fw-bold text-${color}">Vehículo: ${item.vehiculo}</div>
                            <div class="small text-secondary">${item.placas}</div>
                        </div>
                        <button class="btn btn-outline-primary btn-sm rounded-circle" onclick="window.location='/myvet/app/controllers/mantenimientos.php?id=${item.id_mantenimiento}'">
                            <i class="bi bi-arrow-right text-danger"></i>
                        </button>
                    </div>`;
                });

                renderizarListaNotificaciones();
            })
            .catch(err => console.error("Error mantenimientos:", err));
    }

    function verificarVerificaciones() {
        fetch("/myvet/app/controllers/verificacionesController.php?action=listarProximaVerificacion")
            .then(r => r.json())
            .then(data => {
                const items = Array.isArray(data) ? data : [];
                const cantidad = items.length;
                const estado = estadoToasts.verificacion;

                // Actualiza únicamente su badge independiente si existe
                const badgeVerif = document.getElementById("badge-verificaciones");
                if (badgeVerif) {
                    badgeVerif.innerText = cantidad;
                    badgeVerif.classList.toggle('d-none', cantidad === 0);
                }

                if (cantidad > 0 && (estado.primeraCarga || cantidad > estado.ultimoConteo)) {
                    const item = items[0];
                    Toastify({
                        text: `📋 PRÓXIMA VERIFICACIÓN\n${item.estado}\n\n${item.vehiculo} - ${item.placas}`,
                        duration: 500,
                        gravity: "top",
                        position: "right",
                        style: { background: "#fff", color: "#111", borderLeft: "5px solid #0d6efd", borderRadius: "15px", padding: "18px" },
                        onClick: () => window.location.href = "/myvet/app/controllers/verificacionesController.php"
                    }).showToast();
                    estado.primeraCarga = false;
                }
                estado.ultimoConteo = cantidad;

                const listaVerif = document.getElementById("lista-verificaciones");
                if (listaVerif) {
                    if (cantidad === 0) {
                        listaVerif.innerHTML = '<div class="p-4 text-center text-body-secondary">No hay verificaciones próximas.</div>';
                    } else {
                        listaVerif.innerHTML = items.map(item => {
                            const dias = parseInt(item.dias_restantes);
                            const color = dias <= 0 ? "danger" : (dias <= 3 ? "warning" : "success");
                            return `
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom hover-notif">
                                <div>
                                    <div class="small fw-bold">${item.estado}</div>
                                    <div class="fw-bold text-${color}">Vehículo: ${item.vehiculo}</div>
                                    <div class="small text-secondary">Placas: ${item.placas}</div>
                                </div>
                                <button class="btn btn-outline-primary btn-sm rounded-circle" onclick="window.location='/myvet/app/controllers/verificacionesController.php?action=obtenerDetalle&id=${item.id}'">
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>`;
                        }).join("");
                    }
                }
            })
            .catch(err => console.error("Error verificaciones:", err));
    }
    let ultimoIdVentaEliminadaNotificada = 0;

    function verificarVentasEliminadasGlobales() {
        fetch('/myvet/app/controllers/ventasHistorialController.php?action=obtenerUltimaVentaEliminada')
            .then(res => res.json())
            .then(data => {
                // Si hay una venta eliminada reciente y no la hemos notificado aún en esta sesión
                if (data && data.id_venta && data.id_venta !== ultimoIdVentaEliminadaNotificada) {

                    Toastify({
                        text: `🚨 ATENCIÓN GLOBAL\nSe ha cancelado/eliminado la Venta #${data.folio || data.id_venta}`,
                        duration: 500,
                        close: true,
                        gravity: "top",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background: "#dc3545",
                            color: "#ffffff",
                            borderRadius: "12px",
                            fontWeight: "600",
                            padding: "16px"
                        }
                    }).showToast();

                    ultimoIdVentaEliminadaNotificada = data.id_venta;

                    // Actualizar tablas en pantalla si el usuario está viendo el listado
                    if (typeof getVentas === 'function') getVentas();
                }
            })
            .catch(err => console.error("Error al consultar ventas eliminadas:", err));
    }

    /* ==========================================================================
       HANDLERS Y ESTILOS AUXILIARES
       ========================================================================== */
    function inyectarEstilosToast() {
        if (!document.getElementById('style-toast-custom')) {
            const style = document.createElement('style');
            style.id = 'style-toast-custom';
            style.innerHTML = `
            .toast-close { opacity: 1 !important; font-weight: bold; font-size: 20px; margin-left: 10px; }
            .toast-red-close .toast-close { color: #ff0000 !important; }
            .toast-cancel-close .toast-close { color: #dc3545 !important; }
        `;
            document.head.appendChild(style);
        }
    } function procesarAceptarCancelacion(idVenta, id, pagado, total, folio, razon) {
        const montoPagado = parseFloat(pagado) || 0;
        const modalElem = document.getElementById('modalConfirmarCancelacion');
        const modalBs = bootstrap.Modal.getOrCreateInstance(modalElem);

        const textoDetalle = document.getElementById('modalTextoDetalle');
        const textoSub = document.getElementById('modalTextoSub');
        const wrapperMotivo = document.getElementById('wrapperMotivoSinPago');
        const contenedorBotones = document.getElementById('contenedorBotonesAccion');

        textoDetalle.textContent = `¿Aceptar y Cancelar Venta ${folio || '#' + idVenta}?`;

        // Configurar estado según pago
        if (montoPagado > 0) {
            textoSub.textContent = `Motivo reportado: "${razon}". Selecciona si deseas reintegrar el dinero al saldo del cliente o solo anular la venta.`;
            wrapperMotivo.classList.add('d-none');

            contenedorBotones.innerHTML = `
            <button class="btn btn-danger" onclick="ejecutarCancelacionEncadenada(${idVenta}, ${id}, false, '${razon}')">
                <i class="bi bi-x-circle"></i> Sin Saldo
            </button>
            <button class="btn btn-success" onclick="ejecutarCancelacionEncadenada(${idVenta}, ${id}, true, '${razon}')">
                <i class="bi bi-cash-stack"></i> Con Saldo a Favor
            </button>
        `;
        } else {
            textoSub.textContent = "Esta venta no tiene pagos registrados. Se procederá a cancelarla sin saldo.";
            wrapperMotivo.classList.remove('d-none');
            document.getElementById('inputMotivoSinPago').value = razon || '';

            contenedorBotones.innerHTML = `
            <button class="btn btn-danger" onclick="confirmarSinPago(${idVenta}, ${id})">
                <i class="bi bi-check-lg"></i> Sí, cancelar venta
            </button>
        `;
        }

        modalBs.show();
    }

    // Handler auxiliar cuando no hay pago
    function confirmarSinPago(idVenta, id) {
        const motivoInput = document.getElementById('inputMotivoSinPago').value.trim();
        if (!motivoInput) {
            alert("¡El motivo es obligatorio!");
            return;
        }
        ejecutarCancelacionEncadenada(idVenta, id, false, motivoInput);
    }

    // Ejecuta las peticiones backend tras cerrar el modal
    async function ejecutarCancelacionEncadenada(idVenta, id, conSaldo, motivo) {
        // Cerrar modal Bootstrap
        const modalElem = document.getElementById('modalConfirmarCancelacion');
        const modalBs = bootstrap.Modal.getInstance(modalElem);
        if (modalBs) modalBs.hide();

        const esOscuro = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const esModoOscuroObj = {
            background: esOscuro ? '#1e293b' : '#ffffff',
            color: esOscuro ? '#f8fafc' : '#1e2022'
        };

        Swal.fire({
            title: 'Procesando cancelación...',
            text: 'Por favor espere...',
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false,
            ...esModoOscuroObj
        });

        try {
            // 1. Aceptar solicitud
            const formData = new FormData();
            formData.append('id', id);

            const respSolicitud = await fetch('/myvet/app/controllers/ventasHistorialController.php?action=aceptarSolicitudCancelacion', {
                method: 'POST',
                body: formData
            });
            const dataSolicitud = await respSolicitud.json();

            if (dataSolicitud.status !== 'success' && !dataSolicitud.success) {
                Swal.fire({ icon: 'error', title: 'Error', text: dataSolicitud.message || 'Error al aceptar solicitud.' });
                return;
            }

            // 2. Anular / Reintegrar
            const accion = conSaldo ? 'cancelarVenta' : 'cancelarVentaSinSaldo';
            const urlController = typeof URL_CONTROLLER !== 'undefined' ? URL_CONTROLLER : '/myvet/app/controllers/ventasHistorialController.php';

            const respCancelacion = await fetch(`${urlController}?action=${accion}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_venta: idVenta, motivo: motivo })
            });
            const resCancelacion = await respCancelacion.json();

            if (resCancelacion.status === 'success' || resCancelacion.success) {
                await Swal.fire({
                    title: '¡Venta Cancelada!',
                    text: resCancelacion.message || 'Procesado correctamente.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                if (typeof verificarSolicitudesCancelacion === 'function') verificarSolicitudesCancelacion();
                if (typeof getVentas === 'function') getVentas();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: resCancelacion.message });
            }

        } catch (error) {
            console.error("Error:", error);
            Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo conectar con el servidor.' });
        }
    }
    async function procesarEliminarCancelacion(id) {
        const esOscuro = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const confirm = await Swal.fire({
            title: '¿Eliminar solicitud?',
            text: 'Esta acción borrará físicamente el registro.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            background: esOscuro ? '#1e293b' : '#ffffff',
            color: esOscuro ? '#f8fafc' : '#1e2022'
        });

        if (!confirm.isConfirmed) return;

        const formData = new FormData();
        formData.append('id', id);

        try {
            const response = await fetch('/myvet/app/controllers/ventasHistorialController.php?action=eliminarSolicitudCancelacion', { method: 'POST', body: formData });
            const data = await response.json();
            if (data.status === 'success') verificarSolicitudesCancelacion();
            else Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        } catch (err) { console.error(err); }
    }

    window.procesarRecepcionRapida = function (id) {
        if (!confirm("¿Confirmar recepción de material?")) return;
        const formData = new FormData();
        formData.append('id', id);

        fetch('/myvet/app/controllers/traspasosController.php?action=recibirTraspaso', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success || data.status === 'success') location.reload();
                else alert("Error: " + (data.message || "No se pudo procesar"));
            })
            .catch(err => console.error(err));
    };

</script>