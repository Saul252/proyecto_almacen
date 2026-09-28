<!-- =========================================================
MODAL CREAR MEDIDA ADICIONAL
========================================================= -->

<style>
/* =========================================
Z-INDEX & OVERLAYS
========================================= */
#modalMedidaAdicional {
    z-index: 1065 !important;
}

#modalMedidaAdicional + .modal-backdrop {
    z-index: 1060 !important;
}

.miSwalZ,
.swal2-container {
    z-index: 10000 !important;
}

/* =========================================
MODAL CONTENT & STRUCTURE
========================================= */
#modalMedidaAdicional .modal-content {
    
    border-radius: 1.25rem;
    overflow: hidden;
    background-color: var(--bs-modal-bg);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
}

#modalMedidaAdicional .modal-header {
    background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-text-emphasis, #0a58ca) 100%);
    
}

/* =========================================
INPUTS & CONTROLS
========================================= */
#modalMedidaAdicional .form-control {
    height: 48px;
    border-radius: 0.75rem;
    background-color: var(--bs-body-bg);
    border: 1px solid var(--bs-border-color);
    color: var(--bs-body-color);
    transition: all 0.2s ease;
}

#modalMedidaAdicional .form-control:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
}

/* =========================================
FORMULA BOX & EQUIVALENCIA (MODO OSCURO COMPATIBLE)
========================================= */
#modalMedidaAdicional .formula-box {
    background-color: var(--bs-tertiary-bg);
    border: 1px dashed var(--bs-border-color-translucent);
    border-radius: 1rem;
    padding: 1.25rem;
}

/* Resultado calculado resaltado */
#equivalencia {
    background-color: var(--bs-warning-bg-subtle) !important;
    color: var(--bs-warning-text-emphasis) !important;
    border: 1px solid var(--bs-warning-border-subtle) !important;
    font-size: 1.15rem;
    font-weight: 700;
    text-align: center;
}

/* =========================================
TIPO CARD (TARJETAS RADIO BUTTON)
========================================= */
.tipo-card {
    border: 1px solid var(--bs-border-color);
    border-radius: 0.875rem;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    background-color: var(--bs-body-bg);
    display: block;
}

.tipo-card:hover {
    border-color: var(--bs-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.tipo-card:has(input:checked) {
    border-color: var(--bs-primary) !important;
    background-color: var(--bs-primary-bg-subtle) !important;
}

.tipo-card input[type="radio"] {
    width: 1.15em;
    height: 1.15em;
    cursor: pointer;
}

.tracking-wide {
    letter-spacing: 0.04em;
}
</style>

<div class="modal fade" id="modalMedidaAdicional" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header text-white p-4 position-relative">

                <div class="pe-4">
                    <h5 class="modal-title fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-rulers fs-4"></i>
                        Nueva Medida
                    </h5>

                    <small id="infoProductoModal" class="text-white-50 fw-medium">
                        Configura equivalencia de unidades
                    </small>
                </div>

                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>

            </div>

            <!-- FORM -->
            <form id="formMedidaAdicional">

                <input type="hidden" name="producto_id" id="id_producto_crear">
                <input type="hidden" name="almacen_id" id="id_almacen_crear">

                <!-- BODY -->
                <div class="modal-body p-4 bg-body">

                    <!-- NOMBRE -->
                    <div class="mb-4">
                        <label for="nombreNuevaUnidad"
                            class="form-label fw-bold small text-uppercase text-body-secondary tracking-wide">
                            Nombre de la nueva unidad
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary text-body-secondary border-end-0">
                                <i class="bi bi-tag-fill"></i>
                            </span>
                            <input type="text" name="nombre" id="nombreNuevaUnidad"
                                class="form-control border-start-0 ps-0" placeholder="Ej: Caja, Gramo, Tonelada"
                                required>
                        </div>
                    </div>

                    <!-- TIPO CONVERSIÓN -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-body-secondary mb-2 tracking-wide">
                            Tipo de conversión
                        </label>

                        <div class="row g-3">

                            <!-- MÁS GRANDE -->
                           <div class="row g-3">

    <!-- MÁS GRANDE (MAYOR) -->
    <div class="col-md-6">
        <label class="tipo-card h-100 p-3 rounded-4 d-flex align-items-center">
            <div class="d-flex align-items-center gap-3 w-100">
                <input type="radio" 
                       name="tipoConversion" 
                       value="grande"
                       class="form-check-input flex-shrink-0 mt-0" 
                       checked>
                       
                <div class="lh-sm">
                    
                    <small class="text-body-secondary d-block fs-7">
                        MAS GRANDE QUE <span id="masg" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 ms-1">Unidad</span>
                    </small>
                </div>
            </div>
        </label>
    </div>

    <!-- MÁS PEQUEÑA (MENOR) -->
    <div class="col-md-6">
        <label class="tipo-card h-100 p-3 rounded-4 d-flex align-items-center">
            <div class="d-flex align-items-center gap-3 w-100">
                <input type="radio" 
                       name="tipoConversion" 
                       value="pequena"
                       class="form-check-input flex-shrink-0 mt-0">
                       
                <div class="lh-sm">
                    
                    <small class="text-body-secondary d-block fs-7">
                        MAS PEQUEÑA QUE <span id="masp" class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1 ms-1">Unidad</span>
                    </small>
                </div>
            </div>
        </label>
    </div>

</div>

                        </div>
                    </div>

                    <!-- FORMULA BOX -->
                    <div class="formula-box mb-4">

                        <div class="mb-3">
                            <label for="cantidadConversion"
                                class="form-label fw-bold small text-uppercase text-body-secondary tracking-wide">
                                Conversión
                            </label>

                            <input type="number" id="cantidadConversion" class="form-control text-center fw-bold fs-4"
                                step="0.00000001" min="0" placeholder="0.00">
                        </div>

                        <!-- TEXTO FÓRMULA -->
                        <div class="alert bg-body border text-body text-center py-2 px-3 mb-3 shadow-sm rounded-3">
                            <span id="textoFormula" class="fw-medium small">
                                <i class="bi bi-calculator me-1 text-primary"></i> Fórmula de conversión
                            </span>
                        </div>

                        <!-- RESULTADO CALCULADO -->
                        <div>
                            <label for="equivalencia"
                                class="form-label fw-bold small text-uppercase text-body-secondary tracking-wide">
                                Equivalencia calculada
                            </label>

                            <input type="number" id="equivalencia" name="equivalencia" class="form-control"
                                step="0.000000001" readonly>
                        </div>

                    </div>

                    <!-- EJEMPLO EN ALERTA ADAPTATIVA -->
                    <div
                        class="alert alert-info  bg-info-subtle text-info-emphasis d-flex align-items-start gap-2 m-0 p-3 rounded-3">
                        <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 mt-n1"></i>
                        <small id="ejemploConversion" class="fw-medium">
                            Esperando datos...
                        </small>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer  bg-body px-4 pb-4 pt-0 gap-2">

                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i>
                        Guardar
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>
// =====================================================
// 🔥 VARIABLES
// =====================================================

let unidadBaseActual = 'Unidad';

// =====================================================
// 🔥 ABRIR MODAL
// =====================================================

window.prepararNuevaMedida = function(
    idProducto,
    idAlmacen,
    nombreProducto,
    unidadBase
) {

    unidadBaseActual = unidadBase || 'Unidad';

    document.getElementById('id_producto_crear').value = idProducto;
    document.getElementById('id_almacen_crear').value = idAlmacen;

    // Corrección crítica: Usar innerText en lugar de .text
    document.getElementById('masg').innerText = unidadBaseActual;
    document.getElementById('masp').innerText = unidadBaseActual;

    document.getElementById('infoProductoModal').innerText = `Producto: ${nombreProducto}`;

    document.getElementById('cantidadConversion').value = '';
    document.getElementById('equivalencia').value = '';
    document.getElementById('nombreNuevaUnidad').value = '';

    actualizarFormula();

    const modal = bootstrap.Modal.getOrCreateInstance(
        document.getElementById('modalMedidaAdicional')
    );

    modal.show();
};

// =====================================================
// 🔥 ACTUALIZAR FORMULA
// =====================================================

function actualizarFormula() {

    const tipoRadio = document.querySelector('input[name="tipoConversion"]:checked');
    const tipo = tipoRadio ? tipoRadio.value : 'grande';

    const cantidad = parseFloat(document.getElementById('cantidadConversion').value) || 0;
    const nuevaUnidad = document.getElementById('nombreNuevaUnidad').value.trim() || 'Nueva Unidad';

    const texto = document.getElementById('textoFormula');
    const equivalencia = document.getElementById('equivalencia');
    const ejemplo = document.getElementById('ejemploConversion');

    // =================================================
    // 🔥 MÁS GRANDE
    // Ej: 1000 KG caben en 1 TONELADA
    // equivalencia = 0.001
    // =================================================
    if (tipo === 'grande') {

        texto.innerHTML = `<i class="bi bi-calculator me-1 text-primary"></i> <strong>${cantidad || '?'}</strong> ${unidadBaseActual} caben en <strong>1 ${nuevaUnidad}</strong>`;

        if (cantidad > 0) {

            const equivVal = (1 / cantidad).toFixed(8);
            equivalencia.value = equivVal;

            ejemplo.innerHTML = `
                <strong>${cantidad} ${unidadBaseActual}</strong> = <strong>1 ${nuevaUnidad}</strong>
                <br>
                <span class="text-body-secondary">Entonces: 1 ${unidadBaseActual} = ${equivVal} ${nuevaUnidad}</span>
            `;
        } else {
            ejemplo.innerText = 'Ingresa una cantidad válida para calcular la equivalencia.';
        }
    }

    // =================================================
    // 🔥 MÁS PEQUEÑA
    // Ej: 1 KG contiene 1000 GRAMOS
    // equivalencia = 1000
    // =================================================
    else {

        texto.innerHTML = `<i class="bi bi-calculator me-1 text-primary"></i> <strong>1 ${unidadBaseActual}</strong> contiene <strong>${cantidad || '?'} ${nuevaUnidad}</strong>`;

        if (cantidad > 0) {

            const equivVal = cantidad.toFixed(8);
            equivalencia.value = equivVal;

            ejemplo.innerHTML = `
                <strong>1 ${unidadBaseActual}</strong> = <strong>${cantidad} ${nuevaUnidad}</strong>
            `;
        } else {
            ejemplo.innerText = 'Ingresa una cantidad válida para calcular la equivalencia.';
        }
    }
}

// =====================================================
// 🔥 EVENTOS
// =====================================================

document.getElementById('cantidadConversion').addEventListener('input', actualizarFormula);
document.getElementById('nombreNuevaUnidad').addEventListener('input', actualizarFormula);

document.querySelectorAll('input[name="tipoConversion"]').forEach(radio => {
    radio.addEventListener('change', actualizarFormula);
});

// =====================================================
// 🔥 GUARDAR FORMULARIO
// =====================================================

document.getElementById('formMedidaAdicional').addEventListener('submit', async function(e) {

    e.preventDefault();

    try {

        Swal.fire({
            title: 'Guardando...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            customClass: {
                container: 'miSwalZ'
            }
        });

        const formData = new FormData(this);

        const resp = await fetch(
            '/myvet/app/controllers/productosController.php?action=guardarOpcionMedida',
            {
                method: 'POST',
                body: formData
            }
        );

        const data = await resp.json();

        Swal.close();

        if (data.success || data.status === 'success') {

            await Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: 'Medida agregada correctamente',
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                    container: 'miSwalZ'
                }
            });

            const modalEl = document.getElementById('modalMedidaAdicional');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }

            document.getElementById('formMedidaAdicional').reset();

            if (typeof recargarModalMedidas === 'function') {
                recargarModalMedidas();
            }

        } else {

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'No se pudo guardar',
                customClass: {
                    container: 'miSwalZ'
                }
            });
        }

    } catch (error) {

        console.error(error);

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Falló la comunicación con el servidor',
            customClass: {
                container: 'miSwalZ'
            }
        });
    }
});
</script>