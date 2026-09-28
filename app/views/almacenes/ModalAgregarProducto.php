<div class="modal fade" id="modalAgregarProducto" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content shadow-lg ">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-box-seam me-2"></i> Nuevo Producto y Entrada de Almacén</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formAgregarProducto">
                <div class="modal-body p-4">

                    <h6 class="fw-bold mb-3 text-success border-bottom pb-2">Información General</h6>
                   <div class="row g-4 align-items-start">
    <!-- Bloque de Identificación Principal -->
    <div class="col-lg-8">
        <div class="card  border border-subtle shadow-sm">
            <div class="card-body p-4">
                <h6 class="text-uppercase text-body-secondary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 1px;">
                    Información General
                </h6>
                <div class="row g-3">
                    <!-- SKU -->
                 <div class="col-md-3">
    <label class="form-label small fw-bold text-dark">SKU</label>
    <input type="text" id="input_sku" name="sku"
        class="form-control  shadow-sm"
        placeholder="Ej: AL-25" required>
</div>

<div class="col-md-9">
    <label class="form-label small fw-bold text-dark">Nombre del Producto</label>
    <input type="text" id="input_nombre" name="nombre"
        class="form-control  shadow-sm"
        placeholder="Ej: Alambre calibre 25" required>
</div>

                    <!-- Categoría con botón integrado -->
                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-dark">Categoría</label>
                        <div class="input-group shadow-sm">
                            <select name="categoria_id" id="edit_categoria" class="form-select ">
                                <option value="" selected disabled>Seleccionar categoría...</option>
                                <?php foreach($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-success" type="button" onclick="abrirSubModalCategoria()" title="Nueva Categoría">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark">Descripción Corta</label>
                        <textarea name="description" class="form-control text-uppercase  shadow-sm" rows="2" 
                                  placeholder="Detalles adicionales del producto..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bloque de Costos (Sidebar) -->
    <div class="col-lg-4">
        <div class="card  bg-danger bg-opacity-10 shadow-sm h-100">
            <div class="card-body p-4">
                <h6 class="text-uppercase text-danger fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 1px;">
                    Análisis de Costo
                </h6>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-danger">PRECIO DE COMPRA (LOTE)</label>
                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text bg-danger text-white ">$</span>
                        <input type="number" name="precio_adquisicion"
                            class="form-control  fw-bold text-danger" value="0"step="0.01" placeholder="0.00" required>
                    </div>
                </div>

                <div class="p-3 border border-subtlee rounded-3 border-start border-danger border-4 shadow-sm">
                    <p class="mb-0 text-body-secondary" style="font-size: 0.8rem; line-height: 1.4;">
                        <i class="bi bi-info-circle-fill text-danger me-1"></i>
                        Este valor define el <strong>costo real del lote</strong>. Es fundamental para el cálculo automático de tus márgenes de ganancia.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>



                    <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Información Fiscal (SAT)</h6>
                    <div class="row mb-4 g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Clave SAT (Producto/Servicio)</label>
                            <input type="text" name="fiscal_clave_prod" class="form-control" placeholder="Ej: 43231500">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Clave Unidad SAT</label>
                            <input type="text" name="fiscal_clave_unit" class="form-control" placeholder="Ej: H87">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">IVA %</label>
                            <select name="impuesto_iva" class="form-select">
                                <option value="16.00">16%</option>
                                <option value="8.00">8%</option>
                                <option value="0.00">0%</option>
                                <option value="exento">Exento</option>
                            </select>
                        </div>
                    </div>

                    
<!-- Card Principal -->
<div class="card  shadow-sm mb-4">
    <!-- Encabezado con sutil contraste -->
    <div class="card-header border border-subtlee  pt-4 pb-0">
        <div class="d-flex align-items-center">
            <div class="p-2 bg-warning bg-opacity-10 rounded-3 me-3">
                <i class="bi bi-calculator-fill text-warning fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-dark text-uppercase" style="letter-spacing: 1px;">Control de Entrada</h6>
                <small class="text-body-secondary">Conversión de unidades y stock</small>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        <div class="row g-4">
            
            <!-- Bloque de Selección de Unidades -->
            <div class="col-lg-7">
                <div class="p-3 border border-subtle rounded-3 border">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">UNIDAD DE COMPRA</label>
                            <select name="unidad_reporte" id="unidad_medida_compra"
                                class="form-select  shadow-sm fw-bold">
                                <option value="">Seleccione...</option>
                                <?php foreach($unidadesMedida as $ur): ?>
                                <option value="<?= trim($ur['clave']) ?>">
                                    <?= htmlspecialchars($ur['nombre']) ?> (<?= htmlspecialchars($ur['clave']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">UNIDAD BASE (VENTA)</label>
                            <select name="unidad_medida" id="unidad_medida"
                                class="form-select  shadow-sm fw-bold">
                                <option value="">Seleccione...</option>
                                <?php foreach($unidadesMedida as $j): ?>
                                <option value="<?= trim($j['clave']) ?>">
                                    <?= htmlspecialchars($j['nombre']) ?> (<?= htmlspecialchars($j['clave']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bloque de Datos Numéricos -->
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label small fw-bold text-primary">FACTOR X UNIDAD</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-primary text-white border-primary small">×</span>
                            <input type="number" id="inputFactor" name="factor_conversion"
                                class="form-control border-primary fw-bold text-center" value="1" step="0.01"
                                oninput="actualizarLimiteMaestro()">
                        </div>
                        <div class="form-text text-body-secondary" style="font-size: 0.7rem;">Ej: 1 Ton es igual a 40 bultos .</div>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label small fw-bold text-danger">CANT. RECIBIDA</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-danger text-white border-danger small">#</span>
                            <input type="number" id="inputLlegadaMaestra"
                                class="form-control border-danger fw-bold text-center" step="0.01" placeholder="0.00"
                                oninput="actualizarLimiteMaestro()">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de Resultado (Full Width) -->
            <div class="col-12 mt-4">
                <div class="card bg-dark text-white  shadow">
                    <div class="card-body py-2 px-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="vr me-3 opacity-50" style="height: 40px; width: 3px; background: #ffc107;"></div>
                            <div>
                                <p class="mb-0 small text-uppercase opacity-75 fw-bold">Total Unidades a Repartir</p>
                                <small class="opacity-50">Cálculo en base a factor de equivalencia</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <h2 id="displayLimiteBultos" class="mb-0 fw-light text-warning">0.00</h2>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 text-success"><i class="bi bi-houses-fill"></i> Distribución por
                            Almacén</h6>
                        <div class="badge bg-secondary p-2 shadow-sm" style="font-size: 0.9rem;">
                            Asignado: <span id="displayAsignado" class="fw-bold">0.00</span> |
                            Restante: <span id="displayRestante" class="fw-bold">0.00</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-dark small text-center">
                                <tr>
                                    <th width="40">Act.</th>
                                    <th>Almacén</th>
                                    <th width="130">Stock Inicial</th>
                                    <th width="100">Stock Mín.</th>
                                    <th width="110">P. Publico</th>
                                    <th width="110">P. Constructora</th>
                                    <th width="110">P. Distribuidor</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                                <?php foreach($almacenes as $a): ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="almacenes[<?= $a['id'] ?>][activo]" value="1"
                                            class="form-check-input" checked>
                                    </td>
                                    <td class="small fw-bold"><?= htmlspecialchars($a['nombre']) ?></td>
                                    <td>
                                        
                                        <input type="number" step="0.01" name="almacenes[<?= $a['id'] ?>][stock]"
                                            class="form-control form-control-sm input-calculo border-primary fw-bold text-center"
                                            oninput="validarReparto()" value="0">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="almacenes[<?= $a['id'] ?>][stock_minimo]"
                                            class="form-control form-control-sm text-center" placeholder="0">
                                    </td>
                                    <td><input type="number" step="0.01"
                                            name="almacenes[<?= $a['id'] ?>][precio_minorista]"
                                            class="form-control form-control-sm" placeholder="$"></td>
                                    <td><input type="number" step="0.01"
                                            name="almacenes[<?= $a['id'] ?>][precio_mayorista]"
                                            class="form-control form-control-sm" placeholder="$"></td>
                                    <td><input type="number" step="0.01"
                                            name="almacenes[<?= $a['id'] ?>][precio_distribuidor]"
                                            class="form-control form-control-sm" placeholder="$"></td>
                                </tr>
                                <?php endforeach?>
                            </tbody>
                        </table>
                    </div>
                </div>
<!-- Botón para Crear -->

     <div class="modal-footer border border-subtle">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" id="btnGuardarProducto" class="btn btn-success px-5 fw-bold shadow">
                        <i class="bi bi-save me-2"></i> GUARDAR PRODUCTO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function generarSKU(nombre) {
    if (!nombre) return '';

    let limpio = nombre
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .toUpperCase();

    const palabras = limpio.split(' ').filter(p => p.length > 0);

    // Prefijo (2 letras)
    let prefijo = palabras.length > 0
        ? palabras[0].substring(0, 2)
        : '';

    // Número detectado
    const matchNumero = limpio.match(/\d+/);
    let numero = matchNumero ? matchNumero[0] :1;
     numerorandom= Math.floor(Math.random() * 10000); // 0 - 9999

    return numero ? `${prefijo}-${numero}-${numerorandom}` : prefijo;
}
document.addEventListener('DOMContentLoaded', () => {
    const nombre = document.getElementById('input_nombre');
    const sku = document.getElementById('input_sku');

    nombre.addEventListener('input', () => {

        // 🛑 No sobreescribir si el usuario ya escribió manualmente
        if (sku.dataset.editado === "true") return;

        sku.value = generarSKU(nombre.value);
    });

    // Detectar si el usuario edita el SKU manualmente
    sku.addEventListener('input', () => {
        sku.dataset.editado = "true";
    });
});
document.getElementById('formAgregarProducto').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const btn = document.getElementById('btnGuardarProducto');
    const formData = new FormData(form);

    // bloquear botón
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

    fetch('/myvet/app/controllers/almacenes.php?action=guardarCompleto', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text()) // 🔥 primero como texto
        .then(text => {

            console.log("🔎 RESPUESTA RAW:", text); // 👈 aquí ves el error real

            let data;

            try {
                data = JSON.parse(text); // intentar convertir a JSON
            } catch (e) {
                throw new Error("Respuesta no válida del servidor");
            }

            if (data.status === 'success') {

                Swal.fire({
                    icon: 'success',
                    title: 'Producto guardado',
                    text: data.message,
                    timer: 1800,
                    showConfirmButton: false
                });

                // cerrar modal correctamente
                const modalEl = document.getElementById('modalAgregarProducto');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();

                form.reset();

                setTimeout(() => location.reload(), 1500);

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Error desconocido'
                });
            }

        })
        .catch(err => {

            console.error("❌ ERROR:", err);

            Swal.fire({
                icon: 'error',
                title: 'Error del servidor',
                text: err.message
            });

        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-save me-2"></i> GUARDAR PRODUCTO';
        });
});
</script>