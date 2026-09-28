<style>
    /* --- VARIABLES DE DISEÑO --- */
    :root {
        --ios-blue: #007AFF;
        --ios-bg: #F2F2F7;
        --ios-gray: #8e8e93;
        --ios-separator: #e5e5ea;
        --card-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    /* --- ESTILO DEL CONTENEDOR DEL MODAL --- */
    .modal-ios-style {
        border-radius: 28px !important;
        border: none !important;
        overflow: hidden;

    }

    /* --- ETIQUETAS DE INFORMACIÓN (Labels pequeñas) --- */
    .info-label {
        font-size: 0.72rem;
        color: var(--ios-gray);
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
    }

    /* --- BOTÓN DE CAPTURA (Cámara/Archivo) --- */
    .btn-camera {

        color: var(--ios-blue);
        border: 2px dashed #d1d1d6;
        border-radius: 18px;
        padding: 20px;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        gap: 8px;
    }

    .btn-camera:hover {

        border-color: var(--ios-blue);
        color: #0056b3;
    }

    /* --- PREVISUALIZACIÓN DE IMÁGENES --- */
    .preview-img {
        width: 100%;
        border-radius: 18px;
        margin-top: 10px;
        display: none;
        /* Se activa por JS */
        height: 160px;
        object-fit: cover;
        border: 1px solid #ddd;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    /* --- BOTÓN PRINCIPAL ESTILO APPLE --- */
    .btn-primary-ios {
        background: var(--ios-blue);

        border-radius: 16px;
        padding: 16px;
        font-weight: 600;
        color: white;
        transition: opacity 0.2s;
    }

    .btn-primary-ios:hover {
        opacity: 0.9;
        color: white;
    }

    .btn-primary-ios:active {
        opacity: 0.7;
    }

    /* --- ADAPTACIÓN PARA ESCRITORIO (PC) --- */
    @media (min-width: 768px) {
        #modalEvidencia .modal-dialog {
            max-width: 750px;
        }
    }
</style>
<div class="modal fade" id="modalEvidencia" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg modal-ios-style">
            <div class="modal-header  pb-0 px-4 pt-4">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <h5 class="modal-title fw-bold" id="tituloModal">Finalizar Entrega</h5>
                    <span class="badge  text-body-secondary border rounded-pill px-3 py-2" style="font-size: 0.7rem;">
                        ID MOV: <span id="m_id_visible">0</span>
                    </span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEvidencia">
                <input type="hidden" name="id_movimiento" id="m_mov_id">
                <input type="hidden" name="id_venta" id="m_venta_id">
                <input type="hidden" name="vehiculo_id" id="m_vehiculo_id">
                <input type="text" name="folio" id="folio">
                <input type="hidden" name="action" value="subir_evidencia_reparto">

                <div class="modal-body px-4">
                    <div id="alertaEdicion" class="alert alert-warning py-2 small mb-3 rounded-4 d-none">
                        <i class="bi bi-pencil-square me-2"></i> Estás editando una entrega existente.
                    </div>

                    <div class="mb-3 p-3 rounded-4">
                        <div class="info-label">Cliente / Folio de Venta</div>
                        <div id="m_cliente_full" class="fw-bold mb-2"></div>
                        <div class="info-label">Dirección de Entrega</div>
                        <div id="m_direccion_full" class="small text-body-secondary"></div>
                    </div>

                    <div class="mb-3" style="display:none;">
                        <label class="info-label">Estado de la Visita</label>
                        <select name="estatus_entrega" id="m_estatus_select"
                            class="form-select   rounded-3 shadow-none">
                            <option value="Entregado">Entregado Total</option>
                            <option value="Parcial">Entrega Parcial</option>
                            <option value="Rechazado">Rechazado por Cliente</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="info-label">1. Foto del Material</label>
                            <button type="button" class="btn-camera"
                                onclick="document.getElementById('input-foto').click()">
                                <i class="bi bi-box-seam fs-2"></i>
                                <span class="fw-bold small">Material en Obra</span>
                            </button>
                            <input type="file" name="evidencia_foto" id="input-foto" accept="image/*"
                                capture="environment" class="d-none" onchange="previewImagen(this, 'img-preview')">
                            <img id="img-preview" class="preview-img">
                            <div id="txt-material-actual" class="small text-primary mt-1 d-none text-center"><i
                                    class="bi bi-check-all"></i> Imagen guardada</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="info-label">2. Foto de Nota Firmada</label>
                            <button type="button" class="btn-camera"
                                onclick="document.getElementById('input-foto-nota').click()">
                                <i class="bi bi-file-earmark-text fs-2"></i>
                                <span class="fw-bold small">Capturar Nota</span>
                            </button>
                            <input type="file" name="evidencia_nota" id="input-foto-nota" accept="image/*"
                                capture="environment" class="d-none" onchange="previewImagen(this, 'img-preview-nota')">
                            <img id="img-preview-nota" class="preview-img">
                            <div id="txt-nota-actual" class="small text-primary mt-1 d-none text-center"><i
                                    class="bi bi-check-all"></i> Imagen guardada</div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="info-label">Observaciones</label>
                        <textarea name="comentario" id="m_comentario"
                            class="form-control text-uppercase   rounded-3 shadow-none" rows="2"
                            placeholder="Notas opcionales..."></textarea>
                    </div>
                </div>

                <div class="modal-footer  px-4 pb-4">
                    <button type="submit" class="btn btn-primary-ios w-100 py-3" id="btnGuardar">Guardar y
                        Finalizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const API_URL = "/myvet/app/controllers/misRepartosController.php";

    /**
     * Recibe el índice y la cadena JSON serializada desde el onclick
     */
    function abrirModalPorIndex(index, entregaJsonRaw, viajeFolio) {
        console.log(viajeFolio);
        // 1. Convertir el texto JSON de nuevo a Objeto
        let data;
        try {
            data = JSON.parse(entregaJsonRaw);
        } catch (e) {
            console.error("Error al parsear JSON:", e);
            return;
        }
        // 4. Mapeo de datos
        // Hacemos un log para ver exactamente qué tiene el objeto 'data' cuando falla
        console.log("datos:", index, entregaJsonRaw, viajeFolio);
        console.log("Objeto recibido en modal :", data);

        // Intentamos obtener el ID de la confirmación (evidencia_id) 
        // Si no existe, tomamos el del movimiento (id_movimiento)
        const idFinal = data.id_venta || 0;
        const entregaIfinal = data.entrega_id || 0;

        console.log("ID Final asignado:", entregaIfinal);

        // Asignación al input hidden
        document.getElementById('m_mov_id').value = idFinal;

        // Asignación al texto visible (para que el supervisor lo vea)
        if (document.getElementById('m_id_visible')) {
            document.getElementById('m_id_visible').innerText = entregaIfinal;
        }
        // Guardamos el índice por si lo necesitas para otro proceso
        const currentIndex = index;

        // 2. Referencias a elementos de la interfaz
        const modalEl = document.getElementById('modalEvidencia');
        const form = document.getElementById('formEvidencia');
        const titulo = document.getElementById('tituloModal');
        const btnGuardar = document.getElementById('btnGuardar');
        const alertaEdicion = document.getElementById('alertaEdicion');

        const previewMat = document.getElementById('img-preview');
        const previewNota = document.getElementById('img-preview-nota');
        const txtMat = document.getElementById('txt-material-actual');
        const txtNota = document.getElementById('txt-nota-actual');

        // 3. Resetear estado visual del modal
        form.reset();
        previewMat.style.display = 'none';
        previewNota.style.display = 'none';
        if (txtMat) txtMat.classList.add('d-none');
        if (txtNota) txtNota.classList.add('d-none');
        document.getElementById('folio').value = viajeFolio;

        // 4. Mapeo de datos (Ajustado a los nombres que vienen de tu SQL)
        // Usamos evidencia_id o id_movimiento según lo que envíe tu controlador
        const idMovimiento = entregaIfinal || 0;

        document.getElementById('m_mov_id').value = idMovimiento;
        if (document.getElementById('m_id_visible')) {
            document.getElementById('m_id_visible').innerText = idMovimiento;
        }

        document.getElementById('m_venta_id').value = data.id_venta ?? 0;

        // Si vehiculo_id no viene en el JSON de evidencias, podrías necesitarlo en el objeto
        if (document.getElementById('m_vehiculo_id')) {
            document.getElementById('m_vehiculo_id').value = data.vehiculo_id || 0;
        }

        document.getElementById('m_cliente_full').innerText = `${data.cliente || 'S/N'} (${data.folio_venta || 'S/F'})`;
        document.getElementById('m_direccion_full').innerText = data.direccion_entrega || 'Sin dirección';

        // 5. Lógica de "Modificar" (Siempre será edición si entramos desde el monitor de evidencias)
        titulo.innerText = "Modificar Entrega";
        if (alertaEdicion) alertaEdicion.classList.remove('d-none');

        btnGuardar.innerText = "Actualizar Cambios";
        btnGuardar.className = "btn btn-success w-100 py-3 rounded-4 fw-bold";
        btnGuardar.disabled = false;

        // Llenar campos de texto
        document.getElementById('m_estatus_select').value = data.estatus_logistico || "Entregado";
        document.getElementById('m_comentario').value = data.comentario || "";

        // 6. Previsualización de fotos cargadas (foto_1 y foto_2)
        const timestamp = new Date().getTime();

        if (data.foto_registrada && data.foto_registrada.length > 5) {
            console.log(data.foto_registrada);
            // 💡 Concatenación correcta usando Template Literals (comillas invertidas)
            previewMat.src = `${data.foto_registrada}?t=${timestamp}`;
            previewMat.style.display = 'block';
            if (txtMat) txtMat.classList.remove('d-none');
        }

        if (data.nota_registrada && data.nota_registrada.length > 5) {
            previewNota.src = `${data.nota_registrada}?t=${timestamp}`;
            previewNota.style.display = 'block';
            if (txtNota) txtNota.classList.remove('d-none');
        }
        // 7. Abrir Modal
        const myModal = new bootstrap.Modal(modalEl);

        myModal.show();
    }

    /**
     * Previsualización de imágenes al seleccionar archivo
     */
    function previewImagen(input, idDestino) {
        const preview = document.getElementById(idDestino);
        const labelOk = idDestino === 'img-preview' ? 'txt-material-actual' : 'txt-nota-actual';

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = `/myvet/${e.target.result}`;
                console.log(`/myvet/${e.target.result}`);
                preview.style.display = 'block';
                const lbl = document.getElementById(labelOk);
                if (lbl) lbl.classList.add('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    /**
     * Envío del formulario vía AJAX
     */

    document.getElementById('formEvidencia').onsubmit = function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnGuardar');

        // 1. Crear el FormData
        const formData = new FormData(this);

        // 2. IMPORTANTE: Eliminar la acción que viene del HTML y poner la correcta para el monitor
        formData.delete('action');
        formData.append('action', 'subir_evidencia_reparto'); // O 'editar_evidencia' según tu controlador

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        fetch(API_URL, {
            method: 'POST',
            body: formData
        })
            .then(async res => {
                // Debug preventivo: Si la respuesta no es OK, leemos el texto para ver el error real de PHP
                if (!res.ok) {
                    const errorTexto = await res.text();
                    console.error("Error del servidor (Texto):", errorTexto);
                    throw new Error("Error en el servidor: " + res.status);
                }
                return res.json();
            })
            .then(res => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Éxito', text: res.message, timer: 1500, showConfirmButton: false });
                    bootstrap.Modal.getInstance(document.getElementById('modalEvidencia')).hide();

                    // Refrescar el monitor
                    const folioPadre = document.getElementById('txtFolioRuta').innerText;
                    if (typeof verEvidenciasPorFolio === 'function') verEvidenciasPorFolio(folioPadre);
                } else {
                    throw new Error(res.message || "Error desconocido");
                }
            })
            .catch(err => {
                console.error("Error completo:", err);
                Swal.fire('Error', err.message, 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = "Actualizar Cambios";
            });
    };
</script>