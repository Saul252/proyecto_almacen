<style>
    .ios-micro-card {
        background: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 14px;
        padding: 10px 16px;
        min-width: 120px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    }

    .ios-micro-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--bs-secondary-color);
    }

    .ios-micro-value {
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .ios-micro-footer {
        font-size: 0.7rem;
        margin-top: 2px;
    }

    .tabla-scroll {
        max-height: 420px;
        overflow-y: auto;
    }

    .fs-7 {
        font-size: 0.7rem !important;
    }

    /* Z-index para modales anidados */
    #modalInsumos {
        z-index: 1060 !important;
    }

    #modalEditarInsumo {
        z-index: 1110 !important;
    }

    .modal-backdrop:nth-of-type(1) {
        z-index: 1055 !important;
    }

    .modal-backdrop:nth-of-type(2) {
        z-index: 1105 !important;
    }
</style>








<!-- ═══════════════════════════════════════════════════════
         MODAL PRINCIPAL: INSUMOS
         ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalInsumos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pt-4 px-4 pb-2">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-3 p-2 me-3 shadow-sm">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Módulo de Insumos</h5>
                        <p class="text-body-secondary small mb-0">Gestión de insumos del sistema</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4">
                <!-- KPIs -->
                <div class="d-flex align-items-center flex-wrap mb-4" style="gap: 10px;">
                    <div class="ios-micro-card">
                        <span class="ios-micro-label">Total Insumos</span>
                        <div class="ios-micro-value text-primary" id="kpiTotal">0</div>
                    </div>
                    <div class="ios-micro-card">
                        <span class="ios-micro-label">Con Stock</span>
                        <div class="ios-micro-value text-success" id="kpiConStock">0</div>
                    </div>
                    <div class="ios-micro-card">
                        <span class="ios-micro-label">Sin Stock</span>
                        <div class="ios-micro-value text-danger" id="kpiSinStock">0</div>
                    </div>
                    <div class="ios-micro-card">
                        <span class="ios-micro-label">Cobertura</span>
                        <div class="d-flex align-items-baseline">
                            <span class="ios-micro-value text-success" id="kpiCobertura">0</span>
                            <span style="font-size: 0.65rem; font-weight: 700; margin-left: 1px;"
                                class="text-success">%</span>
                        </div>
                        <div class="progress"
                            style="height: 4px; background-color: var(--bs-tertiary-bg); border-radius: 10px; margin-top: 6px; width: 100%;">
                            <div class="progress-bar bg-success rounded-pill" id="kpiBarra" style="width: 0%;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="row mb-4 g-3 align-items-center">
                    <div class="col-xl-3 col-md-4">
                        <select id="filtroCategoria" class="form-select form-select-sm py-2 rounded-3 shadow-none">
                            <option value="">Todas las Categorías</option>
                            <option value="1">Insumos Médicos</option>
                            <option value="2">Limpieza</option>
                            <option value="3">Papelería</option>
                            <option value="4">Otros</option>
                        </select>
                    </div>

                    <div class="col-xl-9 col-md-8">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent border-end-0 text-muted rounded-start-3">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="buscador"
                                class="form-control border-start-0 ps-0 py-2 shadow-none rounded-end-3"
                                placeholder="Buscar insumo...">
                        </div>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="table-responsive tabla-scroll border rounded-4">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead class="text-muted text-uppercase fs-7 border-bottom"
                            style="font-size: 0.7rem; letter-spacing: 0.05em;">
                            <tr>
                                <th class="ps-3 py-3">ID</th>
                                <th class="py-3">Nombre</th>
                                <th class="py-3">Descripción</th>
                                <th class="py-3">U. Mayor</th>
                                <th class="py-3">U. Menor</th>
                                <th class="py-3">Factor</th>
                                <th class="text-center pe-3 py-3" width="60">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyInsumos">
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <div class="spinner-border spinner-border-sm me-2"></div> Cargando insumos...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                    data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════
         MODAL ANIDADO: EDITAR INSUMO
         ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEditarInsumo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="formEditarInsumo">
                <input type="hidden" id="edit_insumo_id" name="id" value="">

                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-white rounded-3 p-2 me-3 shadow-sm">
                            <i class="bi bi-pencil-square fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Editar Insumo</h5>
                            <p class="text-body-secondary small mb-0">Modifica los datos del insumo</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Nombre</label>
                            <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Descripción</label>
                            <input type="text" id="edit_descripcion" name="descripcion" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">U. Mayor</label>
                            <input type="text" id="edit_uma" name="uma" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">U. Menor</label>
                            <input type="text" id="edit_umi" name="umi" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Factor</label>
                            <input type="number" id="edit_factor" name="factor" class="form-control" step="0.01"
                                min="0.01" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 py-2 fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-2"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    (function () {
        'use strict';

        function esc(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function badgeFactor(factor) {
            const f = parseFloat(factor) || 1;
            return `<span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 rounded-pill fw-semibold" style="font-size:.75rem;">${f}</span>`;
        }

        /* ---------- Render tabla ---------- */
        function renderTabla(insumos) {
            const tbody = document.getElementById('tbodyInsumos');
            if (!tbody) return;

            if (!Array.isArray(insumos) || insumos.length === 0) {
                tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                No hay insumos para mostrar.
                            </td>
                        </tr>`;
                actualizarKPIs([]);
                return;
            }

            const filas = insumos.map(m => {
                const search = `${m.nombre || ''} ${m.descripcion || ''} ${m.u_ma || ''} ${m.u_mi || ''}`.toLowerCase();

                return `
                        <tr data-insumo-id="${esc(m.id)}"
                            data-search="${esc(search)}"
                            data-nombre="${esc(m.nombre || '')}"
                            data-descripcion="${esc(m.descripcion || '')}"
                            data-uma="${esc(m.u_ma || '')}"
                            data-umi="${esc(m.u_mi || '')}"
                            data-factor="${esc(m.factor ?? 1)}"
                            style="border-bottom:1px solid var(--bs-border-color-translucent);">
                            <td class="ps-3 fw-bold text-body">${esc(m.id)}</td>
                            <td class="fw-semibold text-body">${esc(m.nombre)}</td>
                            <td class="text-muted small">${esc(m.descripcion || 'Sin descripción')}</td>
                            <td class="text-muted small">${esc(m.u_ma || 'N/A')}</td>
                            <td class="text-muted small">${esc(m.u_mi || 'N/A')}</td>
                            <td>${badgeFactor(m.factor)}</td>
                            <td class="text-center pe-3">
                                <button class="btn btn-sm rounded-circle p-1 d-inline-flex align-items-center justify-content-center shadow-none text-warning border"
                                    style="width:32px;height:32px;"
                                    onclick="editarInsumo(${Number(m.id)})"
                                    title="Editar">
                                    <i class="bi bi-pencil fs-6"></i>
                                </button>
                            </td>
                        </tr>`;
            });

            tbody.innerHTML = filas.join('');
            actualizarKPIs(insumos);
        }

        /* ---------- KPIs ---------- */
        function actualizarKPIs(insumos) {
            const total = insumos.length;
            const conStock = insumos.filter(m => (parseFloat(m.stock) || 0) > 0).length;
            const sinStock = total - conStock;
            const cobertura = total > 0 ? ((conStock / total) * 100).toFixed(1) : 0;

            document.getElementById('kpiTotal').textContent = total;
            document.getElementById('kpiConStock').textContent = conStock;
            document.getElementById('kpiSinStock').textContent = sinStock;
            document.getElementById('kpiCobertura').textContent = cobertura;
            document.getElementById('kpiBarra').style.width = cobertura + '%';
        }

        /* ---------- Cargar insumos ---------- */
        let peticionActual = null;
        let ultimoRequestId = 0;
        let loaderTimer = null;

        async function cargarInsumos() {
            const tbody = document.getElementById('tbodyInsumos');
            if (!tbody) return;

            if (peticionActual) peticionActual.abort();
            peticionActual = new AbortController();
            const signal = peticionActual.signal;
            const requestId = ++ultimoRequestId;

            if (loaderTimer) clearTimeout(loaderTimer);
            loaderTimer = setTimeout(() => {
                if (requestId !== ultimoRequestId) return;
                tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <div class="spinner-border spinner-border-sm me-2"></div> Cargando insumos...
                            </td>
                        </tr>`;
            }, 150);

            const url = `/myvet/app/controllers/egresosController.php?action=obtenerInsumosSelect`;

            try {
                const resp = await fetch(url, { signal });
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                const texto = await resp.text();

                if (requestId !== ultimoRequestId) return;

                let res;
                try {
                    res = JSON.parse(texto);
                } catch (e) {
                    console.error('❌ JSON inválido:\n', texto);
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Respuesta inválida del servidor.</td></tr>`;
                    return;
                }

                if (!res.success) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${esc(res.message || 'Error al cargar insumos')}</td></tr>`;
                    return;
                }

                const insumos = Array.isArray(res.data) ? res.data : [];
                renderTabla(insumos);
                aplicarFiltros();

            } catch (error) {
                if (error.name === 'AbortError') return;
                console.error('Error en cargarInsumos:', error);
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Error de conexión con el servidor.</td></tr>`;
            } finally {
                if (requestId === ultimoRequestId) {
                    if (loaderTimer) { clearTimeout(loaderTimer); loaderTimer = null; }
                    peticionActual = null;
                }
            }
        }

        /* ---------- Filtros ---------- */
        function aplicarFiltros() {
            const texto = (document.getElementById('buscador')?.value || '').toLowerCase().trim();
            const categoria = document.getElementById('filtroCategoria')?.value || '';

            const filas = document.querySelectorAll('#tbodyInsumos tr[data-insumo-id]');
            for (let i = 0; i < filas.length; i++) {
                const fila = filas[i];
                const d = fila.dataset;

                const okTexto = !texto || (d.search || '').includes(texto);
                const okCat = !categoria;

                fila.style.display = (okTexto && okCat) ? '' : 'none';
            }
        }

        /* ---------- Abrir modal principal ---------- */
        window.abrirModalEditarInsumos = function () {
            new bootstrap.Modal(document.getElementById('modalInsumos')).show();
        };

        /* ---------- Editar insumo (lee data-* de la fila) ---------- */
        window.editarInsumo = function (id) {
            const fila = document.querySelector(`#tbodyInsumos tr[data-insumo-id="${id}"]`);
            if (!fila) {
                Swal.fire('Error', 'No se encontró el insumo en la tabla', 'error');
                return;
            }

            const d = fila.dataset;

            document.getElementById('edit_insumo_id').value = id;
            document.getElementById('edit_nombre').value = d.nombre || '';
            document.getElementById('edit_descripcion').value = d.descripcion || '';
            document.getElementById('edit_uma').value = d.uma || '';
            document.getElementById('edit_umi').value = d.umi || '';
            document.getElementById('edit_factor').value = d.factor || 1;

            new bootstrap.Modal(document.getElementById('modalEditarInsumo')).show();
        };

        /* ---------- Guardar cambios ---------- */
        document.addEventListener('submit', async function (e) {
            if (!e.target.matches('#formEditarInsumo')) return;
            e.preventDefault();

            const id = document.getElementById('edit_insumo_id').value;
            if (!id || Number(id) <= 0) {
                Swal.fire('Error', 'ID de insumo inválido', 'error');
                return;
            }

            const formData = new FormData(e.target);

            Swal.fire({
                title: 'Actualizando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const resp = await fetch('/myvet/app/controllers/egresosController.php?action=editar_insumo', {
                    method: 'POST',
                    body: formData
                });

                // 🔥 Leer como texto primero para detectar errores de PHP/HTML
                const texto = await resp.text();

                // 🔥 Si el HTTP no es 200, mostrar el status y el texto
                if (!resp.ok) {
                    console.error(`❌ HTTP ${resp.status} - ${resp.statusText}`);
                    console.error('Respuesta del servidor:\n', texto);
                    throw new Error(`HTTP ${resp.status}: ${resp.statusText} — ${texto.slice(0, 200)}`);
                }

                // 🔥 Intentar parsear JSON
                let res;
                try {
                    res = JSON.parse(texto);
                } catch (errJson) {
                    console.error('❌ JSON inválido. Respuesta del servidor:\n', texto);
                    throw new Error('El servidor no devolvió JSON válido. Revisa la consola (F12).');
                }

                // 🔥 Si el servidor devuelve success: false, mostrar el message exacto
                if (!res.success) {
                    console.error('❌ El servidor devolvió success: false →', res.message);
                    throw new Error(res.message || 'Error al actualizar (sin mensaje del servidor)');
                }

                // ✅ Éxito
                const modalEl = document.getElementById('modalEditarInsumo');
                const modalInst = bootstrap.Modal.getInstance(modalEl);
                if (modalInst) modalInst.hide();

                await cargarInsumos();

                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: res.message || 'Insumo actualizado correctamente',
                    timer: 1500,
                    showConfirmButton: false
                });

            } catch (error) {
                console.error('❌ Error al editar insumo:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar',
                    html: `
                <p class="mb-2">${esc(error.message || 'Fallo de conexión')}</p>
                <small class="text-muted">Revisa la consola (F12) para más detalles.</small>
            `,
                    confirmButtonText: 'Cerrar'
                });
            }
        }); /* ---------- Funciones globales ---------- */
        window.cargarInsumos = cargarInsumos;
        window.aplicarFiltros = aplicarFiltros;

        /* ---------- Boot ---------- */
        function boot() {
            let filtroTimer;
            const buscador = document.getElementById('buscador');
            if (buscador) {
                buscador.addEventListener('input', () => {
                    clearTimeout(filtroTimer);
                    filtroTimer = setTimeout(aplicarFiltros, 120);
                });
            }

            document.getElementById('filtroCategoria')?.addEventListener('change', aplicarFiltros);
            cargarInsumos();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', boot);
        } else {
            boot();
        }
    })();
</script>