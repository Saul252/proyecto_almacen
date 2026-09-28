<?php
// Recibimos el folio por URL.
$folio_viaje = $_GET['folio'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cf System - Gestión de Ruta</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-bg: #F2F2F7;
            --card-shadow: 0 8px 20px rgba(0,0,0,0.08);
            --ios-green: #34C759;
        }

        body {
            background-color: var(--ios-bg);
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
            padding-bottom: 30px;
            color: #1c1c1e;
        }

        /* --- HEADER ESTILO IOS --- */
        .header-ios {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 12px 16px;
        }

        /* --- TARJETAS DE ENTREGA --- */
        .card-entrega {
            background: #fff;
            border-radius: 20px;
            
            box-shadow: var(--card-shadow);
            margin-bottom: 15px;
            transition: transform 0.2s;
        }

        .card-entrega.visitado {
            border-left: 8px solid var(--ios-green);
            opacity: 0.9;
        }

        /* --- BOTONES Y CÁMARA --- */
        .btn-camera {
            background-color: #f8f9fa;
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
        }

        .btn-camera:hover {
            background-color: #f0f0f5;
            border-color: var(--ios-blue);
        }

        .preview-img {
            width: 100%;
            border-radius: 18px;
            margin-top: 10px;
            display: none;
            height: 160px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .btn-primary-ios {
            background: var(--ios-blue);
            
            border-radius: 16px;
            padding: 16px;
            font-weight: 600;
            color: white;
            transition: background 0.2s;
        }

        .btn-primary-ios:hover {
            background: #0066d6;
        }

        .info-label {
            font-size: 0.72rem;
            color: #8e8e93;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 4px;
            display: block;
        }

        /* --- AJUSTES DEL MODAL PARA PC --- */
        .modal-ios-style {
            border-radius: 28px !important;
            border: none !important;
        }

        @media (min-width: 768px) {
            .modal-dialog {
                max-width: 750px; /* Ancho cómodo en computadora */
            }
        }
    </style>
</head>
<body>

<div class="header-ios d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <a href="javascript:history.back()" class="text-decoration-none me-3">
            <i class="bi bi-chevron-left fs-4" style="color: var(--ios-blue);"></i>
        </a>
        <div>
            <h1 class="h6 mb-0 fw-bold">Ruta de Reparto</h1>
            <small class="text-body-secondary"><?php echo htmlspecialchars($folio_viaje); ?></small>
        </div>
    </div>
    <button class="btn btn-light rounded-circle shadow-sm" onclick="cargarEntregas()">
        <i class="bi bi-arrow-clockwise text-primary"></i>
    </button>
</div>

<div class="container mt-3" id="contenedor-entregas">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
</div>

<div class="modal fade" id="modalEvidencia" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg modal-ios-style">
            <div class="modal-header  pb-0 px-4 pt-4">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <h5 class="modal-title fw-bold" id="tituloModal">Finalizar Entrega</h5>
                    <span class="badge bg-light text-body-secondary border rounded-pill px-3 py-2" style="font-size: 0.7rem;">
                        ID MOV: <span id="m_id_visible">0</span>
                    </span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="formEvidencia">
                <input type="text" name="id_movimiento" id="id_movimiento">
                <input type="hidden" name="id_venta" id="m_venta_id">
                <input type="hidden" name="vehiculo_id" id="m_vehiculo_id">
                <input type="hidden" name="action" value="subir_evidencia_reparto">
 <input type="text" name="folio" id="m_mov_id" value="<?php echo htmlspecialchars($folio_viaje); ?>">
                <div class="modal-body px-4">
                    <div id="alertaEdicion" class="alert alert-warning py-2 small mb-3 rounded-4 d-none">
                        <i class="bi bi-pencil-square me-2"></i> Estás editando una entrega existente.
                    </div>

                    <div class="mb-3 p-3 rounded-4" style="background-color: #f2f2f7; border: 1px solid #e5e5ea;">
                        <div class="info-label">Cliente / Folio de Venta</div>
                        <div id="m_cliente_full" class="fw-bold mb-2"></div>
                        <div class="info-label">Dirección de Entrega</div>
                        <div id="m_direccion_full" class="small text-body-secondary"></div>
                    </div>

                    <div class="mb-3" style="display:none;">
                        <label class="info-label">Estado de la Visita</label>
                        <select name="estatus_entrega" id="m_estatus_select" class="form-select  bg-light rounded-3 shadow-none">
                            <option value="Entregado">Entregado Total</option>
                            <option value="Parcial">Entrega Parcial</option>
                            <option value="Rechazado">Rechazado por Cliente</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="info-label">1. Foto del Material</label>
                            <button type="button" class="btn-camera" onclick="document.getElementById('input-foto').click()">
                                <i class="bi bi-box-seam fs-2"></i>
                                <span class="fw-bold small">Material en Obra</span>
                            </button>
                            <input type="file" name="evidencia_foto" id="input-foto" accept="image/*" capture="environment" class="d-none" onchange="previewImagen(this, 'img-preview')">
                            <img id="img-preview" class="preview-img">
                            <div id="txt-material-actual" class="small text-primary mt-1 d-none text-center"><i class="bi bi-check-all"></i> Imagen guardada</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="info-label">2. Foto de Nota Firmada</label>
                            <button type="button" class="btn-camera" onclick="document.getElementById('input-foto-nota').click()">
                                <i class="bi bi-file-earmark-text fs-2"></i>
                                <span class="fw-bold small">Capturar Nota</span>
                            </button>
                            <input type="file" name="evidencia_nota" id="input-foto-nota" accept="image/*" capture="environment" class="d-none" onchange="previewImagen(this, 'img-preview-nota')">
                            <img id="img-preview-nota" class="preview-img">
                            <div id="txt-nota-actual" class="small text-primary mt-1 d-none text-center"><i class="bi bi-check-all"></i> Imagen guardada</div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="info-label">Observaciones</label>
                        <textarea name="comentario" id="m_comentario" class="form-control text-uppercase  bg-light rounded-3 shadow-none" rows="2" placeholder="Notas opcionales..."></textarea>
                    </div>
                </div>

                <div class="modal-footer  px-4 pb-4">
                    <button type="submit" class="btn btn-primary-ios w-100 py-3" id="btnGuardar">Guardar y Finalizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const FOLIO = "<?php echo $folio_viaje; ?>";
const API_URL = "/myvet/app/controllers/gestionarRepartoController.php";
let datosTemporales = []; 

$(document).ready(() => {
    cargarEntregas();
});
function cargarEntregas() {
    const container = document.getElementById('contenedor-entregas');
    
    // Usamos ruta relativa por si el slash inicial '/' distorsiona la ruta base
    fetch(`/myvet/app/controllers/gestionarRepartoController.php?action=get_entregas_folio&folio=${FOLIO}`)
        .then(async res => {
            // Si el servidor devolvió un HTTP 404, 500, etc.
            if (!res.ok) {
                const text = await res.text();
                throw new Error(`Error en el servidor (${res.status}): ${text}`);
            }
            return res.json();
        })
        .then(res => {
            container.innerHTML = '';
            
            // Verificamos si PHP envió un error en formato JSON { success: false, error: "..." }
            if (res.success === false) {
                container.innerHTML = `<div class="alert alert-warning mx-3">Error: ${res.error || 'No se pudieron obtener los datos.'}</div>`;
                return;
            }

            console.log(res.data);
            datosTemporales = res.data || [];

            if (datosTemporales.length === 0) {
                container.innerHTML = '<div class="text-center py-5 text-body-secondary">No hay paradas pendientes.</div>';
                return;
            }

            datosTemporales.forEach((item, index) => {
                const entregadoReal = parseInt(item.ya_entregado) === 1;
                const estado = (item.estado_punto || 'pendiente').toLowerCase();
                const esVisitado = estado === 'visitado' || entregadoReal;
                
                container.innerHTML += `
                    <div class="card card-entrega ${esVisitado ? 'visitado' : ''} animate__animated animate__fadeIn">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge rounded-pill ${esVisitado ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary'}">
                                    ${esVisitado ? 'ENTREGADO' : estado.toUpperCase()}
                                </span>
                                <span class="small text-body-secondary">Venta: ${item.folio_venta || 'S/F'} (Entrega ${item.entrega_id})</span>
                            </div>
                            <h6 class="fw-bold mb-1">${item.cliente || 'Cliente'}</h6>
                            <p class="small text-body-secondary mb-3"><i class="bi bi-geo-alt me-1"></i>${item.direccion_entrega || 'Sin dirección'}</p>
                            
                            <div class="p-3 rounded-4 mb-3 bg-light" style="font-size: 0.85rem; border: 1px solid #f2f2f7;">
                            </div>

                            ${!esVisitado ? 
                                `<button class="btn btn-primary-ios w-100" onclick="abrirModalPorIndex(${index})">
                                    Reportar Entrega
                                 </button>` : 
                                `<button class="btn btn-outline-success w-100 border-2 py-2" onclick="abrirModalPorIndex(${index})" style="border-radius: 14px; font-weight: 600;">
                                    <i class="bi bi-pencil-square me-1"></i> Modificar Evidencia
                                 </button>`
                            }
                        </div>
                    </div>`;
            });
        })
        .catch(err => {
            console.error("Detalle del error:", err);
            container.innerHTML = `<div class="alert alert-danger mx-3">Error de conexión o respuesta no válida.<br><small>${err.message}</small></div>`;
        });
}
function abrirModalPorIndex(index) {
    const data = datosTemporales[index];
    console.log(data);
    if (!data) return;

    const modalEl      = document.getElementById('modalEvidencia');
    const form         = document.getElementById('formEvidencia');
    const titulo       = document.getElementById('tituloModal');
    const btnGuardar   = document.getElementById('btnGuardar');
    const alertaEdicion = document.getElementById('alertaEdicion');
    
    const previewMat   = document.getElementById('img-preview');
    const previewNota  = document.getElementById('img-preview-nota');
    const txtMat       = document.getElementById('txt-material-actual');
    const txtNota      = document.getElementById('txt-nota-actual');

    form.reset();
    previewMat.style.display = 'none';
    previewNota.style.display = 'none';
    txtMat.classList.add('d-none');
    txtNota.classList.add('d-none');

    const idMovimiento = data.entrega_id || 0;
    document.getElementById('id_movimiento').value = data.entrega_id;
    document.getElementById('m_mov_id').value = data.viaje_folio;
    document.getElementById('m_id_visible').innerText = data.entrega_id;
  document.getElementById('m_venta_id').value = (data.venta_id );    document.getElementById('m_vehiculo_id').value = data.vehiculo_id || 0; 
    document.getElementById('m_cliente_full').innerText = `${data.cliente || 'S/N'} (${data.folio_venta || 'S/F'})`;
    document.getElementById('m_direccion_full').innerText = data.direccion_entrega || 'Sin dirección';

    const esEdicion = parseInt(data.ya_entregado) === 1;

    if (esEdicion) {
        titulo.innerText = "Modificar Entrega";
        alertaEdicion.classList.remove('d-none');
        btnGuardar.innerText = "Actualizar Cambios";
        btnGuardar.className = "btn btn-success w-100 py-3 rounded-4 fw-bold";

        document.getElementById('m_estatus_select').value = data.estatus_evidencia || "Entregado";
        document.getElementById('m_comentario').value = data.comentario_evidencia || "";

        if (data.foto_registrada && data.foto_registrada.length > 5) {
            previewMat.src =`/myvet/${data.foto_registrada}` + "?t=" + new Date().getTime();
            previewMat.style.display = 'block';
            txtMat.classList.remove('d-none');
        }
        if (data.nota_registrada && data.nota_registrada.length > 5) {
            previewNota.src = `/myvet/${data.nota_registrada}` + "?t=" + new Date().getTime();
            previewNota.style.display = 'block';
            txtNota.classList.remove('d-none');
        }
    } else {
        titulo.innerText = "Finalizar Entrega";
        alertaEdicion.classList.add('d-none');
        btnGuardar.innerText = "Guardar y Finalizar";
        btnGuardar.className = "btn btn-primary-ios w-100 py-3";
        document.getElementById('m_estatus_select').value = "Entregado";
    }

    btnGuardar.disabled = (idMovimiento === 0);
    const myModal = new bootstrap.Modal(modalEl);
    myModal.show();
}

function previewImagen(input, idDestino) {
    const preview = document.getElementById(idDestino);
    const labelOk = idDestino === 'img-preview' ? 'txt-material-actual' : 'txt-nota-actual';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            document.getElementById(labelOk).classList.add('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('formEvidencia').onsubmit = function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnGuardar');
    const formData = new FormData(this);
    const esEdicion = document.getElementById('tituloModal').innerText.includes("Modificar");

   

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

    fetch(API_URL, { method: 'POST', body: formData })
    .then(async res => {
        const data = await res.json();
        if(!res.ok) throw new Error(data.message || "Error en el servidor");
        return data;
    })
    .then(res => {
        Swal.fire({ icon: 'success', title: 'Éxito', text: res.message, timer: 1500, showConfirmButton: false });
        bootstrap.Modal.getInstance(document.getElementById('modalEvidencia')).hide();
        cargarEntregas();
    })
    .catch(err => Swal.fire('Error', err.message, 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerText = esEdicion ? "Actualizar Cambios" : "Guardar y Finalizar";
    });
};
</script>
</body>
</html>