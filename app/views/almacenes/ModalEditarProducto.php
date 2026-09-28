<div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 20px; overflow: hidden;">

            <div class="modal-header  bg-warning bg-gradient p-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="bi bi-pencil-square fs-4 me-2"></i>
                    <span>Editar Producto:</span>
                    <span id="edit_nombre_titulo" class="ms-2 opacity-75 fw-normal"></span>
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditarProducto">
                <input type="hidden" name="producto_id" id="edit_id">
                <input type="hidden" name="almacen_actual_id" id="edit_almacen_id">

                <div class="modal-body p-4 border border-subtle">
                    <div class="row g-4">

                        <div class="col-md-4">
                            <div class="p-3 rounded-4 border border-subtle border border-opacity-10 h-100">
                                <h6 class="fw-bold text-primary mb-3 d-flex align-items-center">
                                    <i class="bi bi-info-circle me-2"></i> Datos Generales
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary">SKU / Código</label>
                                    <input type="text" name="sku" id="edit_sku" class="form-control  shadow-sm"
                                        style="border-radius: 10px;" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary">Nombre del Producto</label>
                                    <input type="text" name="nombre" id="edit_nombre"
                                        class="form-control  shadow-sm" style="border-radius: 10px;" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary">Categoría</label>
                                    <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                        <select name="categoria_id" id="edit_categoria_idx"
                                            class="form-select ">
                                            <option value="">Seleccione...</option>
                                            <?php foreach($categorias as $cat): ?>
                                            <option value="<?= trim($cat['id']) ?>">
                                                <?= htmlspecialchars($cat['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn btn-white  text-success" type="button"
                                            onclick="abrirSubModalCategoria()">
                                            <i class="bi bi-plus-circle-fill"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary">Descripción</label>
                                    <textarea name="descripcion" id="edit_descripcion"
                                        class="form-control text-uppercase  shadow-sm" style="border-radius: 10px;"
                                        rows="3"></textarea>
                                </div>

                                <h6 class="fw-bold text-info mt-4 mb-3 d-flex align-items-center">
                                    <i class="bi bi-shield-check me-2"></i> Datos SAT
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="small text-body-secondary">Clave Prod.</label>
                                        <input type="text" name="fiscal_clave_prod" id="edit_fiscal_clave_prod"
                                            class="form-control form-control-sm  shadow-sm">
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-body-secondary">Clave Unidad</label>
                                        <input type="text" name="fiscal_clave_unidad" id="edit_fiscal_clave_unidad"
                                            class="form-control form-control-sm  shadow-sm">
                                    </div>
                                    <div class="col-12 mt-2">
                                        <label class="small text-body-secondary">IVA (%)</label>
                                        <input type="number" step="0.01" name="impuesto_iva" id="edit_impuesto_iva"
                                            class="form-control form-control-sm  shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">

                            <div
                                class="d-flex justify-content-between align-items-center p-3 mb-4 rounded-4 bg-dark text-white shadow-sm">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt-fill text-warning me-2"></i>
                                    <span class="small opacity-75 me-2">Editando en:</span>
                                    <span id="edit_almacen_nombre" class="fw-bold"></span>
                                </div>
                                <?php if($almacen_usuario == 0): ?>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="check_todos_almacenes"
                                        name="aplicar_global">
                                    <label class="form-check-label small fw-bold" for="check_todos_almacenes">¿Aplicar a
                                        todos los almacenes?</label>
                                </div>
                                <?php endif; ?>

                            </div>

                            <h6 class="fw-bold text-success mb-3">Gestión de Precios</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="card  shadow-sm p-3 rounded-4">
                                        <label class="small fw-bold text-body-secondary mb-2">P. Publico</label>
                                        <div class="input-group">
                                            <span
                                                class="input-group-text bg-transparent  text-success fw-bold">$</span>
                                            <input type="number" step="0.01" name="precio_minorista" id="edit_p_min"
                                                class="form-control  fw-bold fs-5 p-0 shadow-none">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card  shadow-sm p-3 rounded-4">
                                        <label class="small fw-bold text-body-secondary mb-2">P. Constructora</label>
                                        <div class="input-group">
                                            <span
                                                class="input-group-text bg-transparent  text-success fw-bold">$</span>
                                            <input type="number" step="0.01" name="precio_mayorista" id="edit_p_may"
                                                class="form-control  fw-bold fs-5 p-0 shadow-none">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card  shadow-sm p-3 rounded-4">
                                        <label class="small fw-bold text-body-secondary mb-2">P. Distribuidor</label>
                                        <div class="input-group">
                                            <span
                                                class="input-group-text bg-transparent  text-success fw-bold">$</span>
                                            <input type="number" step="0.01" name="precio_distribuidor" id="edit_p_dist"
                                                class="form-control  fw-bold fs-5 p-0 shadow-none">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark mb-3">Configuración de Unidades</h6>
                            <div class="row g-3 p-3 rounded-4 border border-subtle border mb-4">
                                <div class="col-md-4">
                                    <label class="small fw-bold text-secondary">Unidad Compra</label>

                                    <select name="unidad_reporte" id="edit_unidad_reporte"
                                        class="form-select  shadow-sm text-center fw-bold">
                                        <option value="">Seleccione...</option>
                                        <?php foreach($unidadesMedida as $u): ?>
                                        <option value="<?= trim($u['clave']) ?>">
                                            <?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['clave']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 text-center">
                                    <label class="small fw-bold text-primary">Unidades que componen Unidad de
                                        Compra</label>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="bi bi-arrow-repeat fs-4 text-primary me-2"></i>
                                        <input type="number" step="0.01" name="factor_conversion"
                                            id="edit_factor_conversion"
                                            class="form-control  shadow-sm text-center fw-bold fs-5 text-primary"
                                            style="width: 100px;">
                                    </div>
                                </div>
                                <div class="col-md-4">

                                    <label class="small fw-bold text-secondary">Unidad Venta (Base)</label>

                                    <select name="unidad_medida" id="edit_unidad_medida"
                                        class="form-select  shadow-sm text-center fw-bold">

                                        <option value="">Seleccione...</option>

                                        <?php foreach($unidadesMedida as $u): ?>
                                        <option value="<?= trim($u['clave']) ?>">
                                            <?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['clave']) ?>)
                                        </option>
                                        <?php endforeach; ?>

                                    </select>
                                </div>
                            </div>

                            <h6 class="fw-bold text-secondary mb-3">Control de Inventario</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-3 rounded-4 border-start border-4 border-success border border-subtle shadow-sm">
                                        <label class="small fw-bold text-body-secondary">Stock Actual</label>
                                        <div class="d-flex align-items-center mt-1">
                                            <i class="bi bi-box-fill text-success me-2"></i>
                                            <input type="number" step="0.01" name="stock" id="edit_stock"
                                                class="form-control  fw-bold fs-4 p-0 shadow-none" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-4 border-start border-4 border-danger border border-subtle shadow-sm">
                                        <label class="small fw-bold text-body-secondary">Mínimo Permitido</label>
                                        <div class="d-flex align-items-center mt-1">
                                            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                                            <input type="number" step="0.01" name="stock_minimo" id="edit_s_min"
                                                class="form-control  fw-bold fs-4 p-0 shadow-none">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
 
<!-- Cambiamos id por class -->
<button type="button" 
        class="btn-ver-medidas btn btn-outline-success rounded-pill px-3">
    <i class="bi bi-list-ul me-2"></i>
    Ver Medidas
</button>
                <div class="modal-footer  p-4 border border-subtle d-flex justify-content-between">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-bold"
                        data-bs-dismiss="modal">Descartar</button>
                    <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm" style="border-radius: 12px;">
                        Actualizar Producto <i class="bi bi-check-lg ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
   
<script src="/myvet/app/backend/js/calculo_de_conversion.js"></script>
<script>
function editarProducto(productoId, almacenId) {
    console.log("Iniciando carga de producto:", productoId);

    fetch(`/myvet/app/controllers/almacenes.php?action=getProducto&id=${productoId}&almacen_id=${almacenId}`)
        .then(res => res.text())
        .then(text => {

            console.log("🔎 RAW:", text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error("Respuesta no válida del servidor");
            }

            if (data.status === 'success') {
                const p = data.producto;

                const setVal = (id, val) => {
                    const el = document.getElementById(id);
                    if (!el) return;

                    if (el.tagName === 'SPAN') {
                        el.innerText = val ?? '';
                    } else {
                        el.value = val ?? '';
                    }
                };
                
// Seleccionamos por clase y aplicamos el atributo
$('.btn-ver-medidas').last() // Selecciona el último botón agregado en el bucle
    .attr('onclick', `verListaMedidas(${p.id}, ${almacenId}, '${p.nombre}','${p.unidad_medida}')`);


                // 1. Identificadores
                setVal('edit_id', p.id);
                setVal('edit_almacen_id', almacenId);
                setVal('edit_nombre_titulo', p.nombre);
                setVal('edit_almacen_nombre', p.almacen_nombre);

                // 2. General
                setVal('edit_sku', p.sku);
                setVal('edit_nombre', p.nombre);
                setVal('edit_descripcion', p.descripcion);

                // 3. Categoría
                const selectCat = document.getElementById('edit_categoria_idx');
                if (selectCat) {
                    selectCat.value = p.categoria_id ?? '';
                }

                // 4. Fiscal
                setVal('edit_fiscal_clave_prod', p.fiscal_clave_prod);
                setVal('edit_fiscal_clave_unidad', p.fiscal_clave_unidad);
                setVal('edit_impuesto_iva', p.impuesto_iva);
                

                // 5. Precios
              setVal(
  'edit_p_min',
  (Math.round((p.precio_minorista * p.factor_conversion) * 100) / 100).toFixed(1)
);

setVal(
  'edit_p_may',
  (Math.round((p.precio_mayorista * p.factor_conversion) * 100) / 100).toFixed(1)
);

setVal(
  'edit_p_dist',
  (Math.round((p.precio_distribuidor * p.factor_conversion) * 100) / 100).toFixed(1)
);

                // 6. Stock
                const selectUnidad = document.getElementById('edit_unidad_reporte');
                console.log(selectUnidad);
                console.log(p.unidad_reporte);

                if (selectUnidad && p.unidad_reporte) {
                    const valor = String(p.unidad_reporte).trim();

                    // intenta asignar
                    selectUnidad.value = valor;

                    // 🔥 si no existe opción, lo agrega dinámicamente
                    if (selectUnidad.value !== valor) {
                        const option = document.createElement('option');
                        option.value = valor;
                        option.text = valor + ' (actual)';
                        option.selected = true;
                        selectUnidad.appendChild(option);
                    }
                }
                setVal('edit_factor_conversion', p.factor_conversion);
             const selectMedida = document.getElementById('edit_unidad_medida');

if (selectMedida && p.unidad_medida) {
    const valor = String(p.unidad_medida).trim();

    // Damos 100ms para que el DOM se asiente
    setTimeout(() => {
        selectMedida.value = valor;

        // Si después de asignar sigue sin marcarse, forzamos la creación
        if (selectMedida.value !== valor) {
            console.log("Forzando creación de opción para:", valor);
            const option = new Option(valor, valor, true, true);
            selectMedida.add(option);
            selectMedida.value = valor;
        }
    }, 100);
}
                setVal('edit_stock', p.stock);
                setVal('edit_s_min', p.stock_minimo);

                // Mostrar modal
                const modalEl = document.getElementById('modalEditarProducto');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();

                modalEl.addEventListener('shown.bs.modal', () => {
                    if (selectCat && p.categoria_id) {
                        selectCat.value = p.categoria_id;
                    }
                }, {
                    once: true
                });

            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(err => {
            console.error("❌ ERROR:", err);
            Swal.fire('Error', err.message, 'error');
        });
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const formEditar = document.getElementById('formEditarProducto');
    if (!formEditar) return;

    formEditar.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        Swal.fire({
            title: 'Guardando cambios...',
            text: 'Actualizando información del producto',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('/myvet/app/controllers/almacenes.php?action=actualizarProducto', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text()) // 🔥 importante para debug
            .then(text => {

                console.log("🔎 RAW:", text);

                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error("Respuesta inválida del servidor");
                }

                if (data.status === 'success') {

                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });

                } else {
                    Swal.fire('Error', data.message || 'Error desconocido', 'error');
                }

            })
            .catch(error => {
                console.error('❌ ERROR:', error);

                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    text: error.message
                });
            });
    });

});
</script>
