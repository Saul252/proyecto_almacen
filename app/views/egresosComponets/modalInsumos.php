<div class="modal fade" id="modalGasto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 24px; ">
            <form id="formNuevoGasto" enctype="multipart/form-data">
                
                <div class="modal-header  pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-primary d-flex align-items-center" style="letter-spacing: -0.5px;">
                        <i class="bi bi-cash-stack text-primary me-2 fs-4"></i> Registrar Nuevo Gasto
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Folio/Factura</label>
                            <input type="text" id="folio_gasto" name="folio" class="form-control   text-center fw-bold"
                                style="border-radius: 12px; height: 42px; color: #555;" placeholder="Cargando..." readonly required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Almacén Destino</label>
                            <select name="almacen_id" id="almacen_guardar_gasto" class="form-select  " style="border-radius: 12px; height: 42px;"
                                <?= ($_SESSION['rol_id'] != 1) ? 'readonly style="pointer-events: none;"' : '' ?> required>
                                <?php foreach($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>" <?= ($_SESSION['almacen_id'] == $alm['id']) ? 'selected' : '' ?>>
                                    <?= $alm['nombre'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">Categoría de Gasto</label>
                            <div class="input-group">
                                <select id="select_categoria_gasto" name="categoria_id" class="form-select  " style="border-radius: 12px 0 0 12px; height: 42px;" required>
                                    <option value="">Seleccione categoría...</option>
                                </select>
                                <!-- <button type="button" class="btn btn-primary " style="border-radius: 0 12px 12px 0; padding: 0 15px;" onclick="abrirModalNuevaCategoria()">
                                    <i class="bi bi-plus-lg"></i>
                                </button> -->
                            </div>
                        </div>

                       <div class="col-md-6 d-none" id="wrapper-beneficiario"> 
                            <label class="form-label small fw-semibold text-secondary">Beneficiario / Razón Social</label>
                            <input type="text" id="beneficiario" name="beneficiario" class="form-control   text-uppercase"
                                style="border-radius: 12px; height: 42px;" placeholder="Ej: CFE, Gasolinera..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Vincular Proveedor Existente</label>
                            <div class="input-group">
                                <select class="form-select  " id="select-proveedor" name="proveedor_id" style="border-radius: 12px 0 0 12px; height: 42px;" onchange="actualizar()">
                                    <option value="">Seleccione...</option>
                                    <option value="OTRO" class="fw-bold text-primary">➕ OTRO (ESCRIBIR NUEVO)</option>
                                </select>
                                <button class="btn btn-outline-light   text-success" type="button" style="border-radius: 0 12px 12px 0; padding: 0 15px;" onclick="abrirModalNuevoProveedor()">
                                    <i class="bi bi-plus-lg fw-bold"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Método de Pago</label>
                            <select name="metodo_pago" class="form-select  " style="border-radius: 12px; height: 42px;">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Comprobante (Evidencia)</label>
                            <input type="file" name="documento" class="form-control  " style="border-radius: 12px; height: 42px; pt-2;" accept=".jpg,.png,.pdf">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pt-2">
                        <h6 class="fw-bold mb-0 text-primary" style="letter-spacing: -0.3px;">Conceptos del Gasto</h6>
                        <button type="button" class="btn btn-sm text-darck fw-bold bg-transparent  d-flex align-items-center" onclick="abrirModalNuevoInsumo()">
                            <i class="bi bi-plus-circle-fill me-1 fs-6"></i> Agregar Insumo
                        </button>
                        <button type="button" class="btn btn-sm text-primary fw-bold bg-transparent  d-flex align-items-center" onclick="agregarFilaGasto()">
                            <i class="bi bi-plus-circle-fill me-1 fs-6"></i> Agregar Concepto
                        </button>
                   
                        
                    </div>

                    <div class="table-responsive mb-4" style="border-radius: 16px; padding: 10px;">
                        <table class="table table-borderless align-middle mb-0" id="tablaConceptosGasto">
                            <thead>
                                <tr class="text-secondary small fw-bold" style="font-size: 0.75rem;">
                                    <th>DESCRIPCIÓN</th>
                                    <th width="90" class="text-center">CANT.</th>
                                    <th width="150">INSUMO VINCULADO</th>
                                    <th width="120">PRECIO</th>
                                    <th width="120" class="text-end pe-3">SUBTOTAL</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="" style="border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                    <td>
                                        <input type="text" name="desc[]" class="form-control form-control-sm   text-uppercase" style="border-radius: 8px; height: 36px;" required>
                                    </td>
                                    <td>
                                        <input type="number" name="cant[]" class="form-control form-control-sm   cant text-center" style="border-radius: 8px; height: 36px;" value="1" step="any" oninput="calcularGasto()">
                                    </td>
                                    <td>
                                        <select name="items[]" class="form-select form-select-sm   items" style="border-radius: 8px; height: 36px;">
                                            <option value=""></option>
                                            <?php foreach($insumos as $ve): ?>
                                            <option value="<?= $ve['id'] ?>"><?= $ve['nombre'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="precio[]" class="form-control form-control-sm   precio" style="border-radius: 8px; height: 36px;" value="0.00" step="any" oninput="calcularGasto()">
                                    </td>
                                    <td class="text-end fw-bold text-primary subtotal_fila pe-3" style="font-size: 0.9rem;">$0.00</td>
                                    <td class="text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row align-items-end g-3 pt-2">
                        <div class="col-md-7">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-secondary">Fecha Emisión</label>
                                    <input type="date" id="fecha" name="fecha" value="<?= date("Y-m-d") ?>" class="form-control  " style="border-radius: 12px; height: 40px;">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-secondary">Observaciones Internas</label>
                                    <textarea name="observaciones" class="form-control text-uppercase  " style="border-radius: 12px;" rows="2" placeholder="Notas aclaratorias..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 text-end pb-1">
                            <div class="p-3" style=" border-radius: 16px;">
                                <span class="text-secondary small fw-bold text-uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Monto Total</span>
                                <h1 class="fw-bolder text-primary mb-0" id="txtTotalGasto" style="font-size: 2.2rem; letter-spacing: -1px;">$ 0.00</h1>
                                <input type="hidden" name="total_final" id="inputTotalGasto" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer  px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-light fw-semibold text-secondary  me-2" data-bs-dismiss="modal" style="border-radius: 12px; padding: 10px 20px;">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" style="border-radius: 12px; padding: 10px 24px;">Guardar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevaCategoriaGasto" tabindex="-1" aria-hidden="true"
    style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 20px;">
            <div class="modal-header ">
                <h6 class="modal-title fw-bold">Nueva Categoría</h6>
                <button type="button" class="btn-close" onclick="$('#modalNuevaCategoriaGasto').modal('hide')"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="mb-3">
                    <label class="small fw-bold text-body-secondary">Nombre</label>
                    <input type="text" id="nuevo_nombre_cat" class="form-control  "
                        style="border-radius: 10px;" placeholder="Ej: Servicios">
                </div>

            </div>
            <div class="modal-footer  pt-0">
                <button type="button" class="btn btn-primary w-100 fw-bold" onclick="guardarNuevaCategoria()"
                    style="border-radius: 10px;">Agregar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalNuevoInsumo" tabindex="-1" aria-hidden="true"
    style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 20px;">
            <div class="modal-header ">
                <h6 class="modal-title fw-bold">Nuevo Insumo</h6>
                <button type="button" class="btn-close" onclick="$('#modalNuevoInsumo').modal('hide')"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="mb-3">
                    <label class="small fw-bold text-body-secondary">Nombre y Marca</label>
                    <input type="text" id="nuevo_nombre_insumo" class="form-control  "
                        style="border-radius: 10px;" placeholder="Ej: Servicios">
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-body-secondary">Unidad maxima</label>
                    <input type="text" id="nuevo_maximo_insumo" class="form-control  "
                        style="border-radius: 10px;" placeholder="Ej: cubeta, tambo">
                </div>

          
                <div class="mb-3">
                    <label class="small fw-bold text-body-secondary">Unidad minima</label>
                    <input type="text" id="nuevo_minimo_insumo" class="form-control  "
                        style="border-radius: 10px;" placeholder="Ej:  litro ">
                </div> 
                <div class="mb-3">
                    <label class="small fw-bold text-body-secondary">FACTOR </label>
                    <input type="number" id="factor" class="form-control  "
                        style="border-radius: 10px;" placeholder="1">
                </div>
                 <div class="mb-3">
                    <label class="small fw-bold text-body-secondary">Descripcion y modelo</label>
                    <input type="text" id="nuevo_descripcion_insumo" class="form-control  "
                        style="border-radius: 10px;" placeholder="Ej: BARDAL MOD 1234">
                </div> 

            </div>
            <div class="modal-footer  pt-0">
                <button type="button" class="btn btn-primary w-100 fw-bold" onclick="guardarNuevoInsumo()"
                    style="border-radius: 10px;">Agregar</button>
            </div>
        </div>
    </div>
</div>
<script>
cargarProveedoresSelect();
async function cargarProveedoresSelect() {
    const select = document.getElementById('select-proveedor');
    if (!select) return; // Seguridad por si el select no está en la vista actual

    try {
        // 1. Realizar la petición a tu controlador de Cf System
        const url = '/myvet/app/controllers/egresosController.php?action=obtenerProveedores';
        const respuesta = await fetch(url);


        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

        const resultado = await respuesta.json();

        // 2. Verificar que la respuesta sea exitosa y contenga los datos
        if (resultado.success && Array.isArray(resultado.data)) {

            // Limpiamos el select y dejamos una opción inicial neutra


            // 3. Recorrer los usuarios y crear las opciones
            resultado.data.forEach(proveedor => {
                console.log(proveedor);
                const opcion = document.createElement('option');
                opcion.value = proveedor.nombre_comercial; // El ID que se enviará en el formulario

                // Formateamos el texto: "Nombre (Almacén - Rol)" para que sea súper descriptivo
                const almacen = proveedor.almacen_nombre || 'Sin Almacén';
                opcion.textContent = `${proveedor.nombre_comercial}`;

                // Agregamos la opción al select
                select.appendChild(opcion);
            });

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los proveedores </option>';
            console.error('El backend no devolvió success:true o la estructura cambió');
        }

    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarproveedores Select:', error);
    }
}
// VARIABLES GLOBALES (FUERA del DOMContentLoaded)
const modalGastoEl = document.getElementById('modalGasto');
const formGasto = document.getElementById('formNuevoGasto');

function actualizar() {
    const valorSeleccionado = $('#select-proveedor').val();
    const $beneficiario = $('#beneficiario');
    const $wrapperBeneficiario = $('#wrapper-beneficiario');
    const $wrapperSelect = $('#wrapper-select-proveedor');

    console.log('Proveedor seleccionado:', valorSeleccionado);

    if (valorSeleccionado === "OTRO") {
        // 1. Mostramos el input y ocultamos el select de la fila
        $wrapperSelect.addClass('d-none');
        $wrapperBeneficiario.removeClass('d-none');
        
        // 2. Habilitamos el input para que sea requerido y viaje en el formulario
        $beneficiario.prop('disabled', false).val('').focus();
    } else {
        $wrapperBeneficiario.addClass('d-none');
          $beneficiario.prop('disabled', false).val('').focus();
        // Si elige un proveedor de la lista, ese valor se asigna directamente al beneficiario
        $beneficiario.val(valorSeleccionado);
        
        // Si el valor es vacío (Seleccione...), nos aseguramos de mantener las propiedades limpias
        if (valorSeleccionado === "") {
            $beneficiario.val('');
        }
    }
}

// Función adicional para que el usuario pueda arrepentirse y volver al select rápido
function regresarASelect() {
    $('#select-proveedor').val('').trigger('change');
    $('#wrapper-beneficiario').addClass('d-none');
    $('#wrapper-select-proveedor').removeClass('d-none');
    $('#beneficiario').prop('disabled', true).val('');
}
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema Gastos INICIADO');

    if (!modalGastoEl || !formGasto) {
        console.error('❌ Modal o Form no encontrados');
        return;
    }

    // 1. CARGAR CATEGORÍAS
    cargarCategorias();

    // 2. AL ABRIR MODAL
    modalGastoEl.addEventListener('show.bs.modal', function() {
        console.log('🟢 Modal ABIERTO');
        formGasto.reset();
        limpiarTabla();
        cargarFolio();
        calcularGasto();
    });

    // 3. SUBMIT FORM
    formGasto.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('📤 GUARDANDO...');
        guardarGasto();
    });

    // 4. CALCULAR TOTAL
    document.addEventListener('input', function(e) {
        if (e.target.matches('.cant, .precio')) calcularGasto();
    });
});

// ==================== FUNCIONES (GLOBALES) ====================
function cargarCategorias() {
    console.log('📂 Categorías...');
    fetch('/myvet/app/controllers/egresosController.php?action=get_categorias_egresos')
        .then(res => {
            if (!res.ok) throw new Error('HTTP: ' + res.status);
            return res.json();
        })
        .then(data => {
            const select = document.getElementById('select_categoria_gasto');
            if (data.success && select) {
                let html = '<option value="">Seleccione...</option>';
                data.data.forEach(cat => {
                    html += `<option value="${cat.id}">${cat.nombre}</option>`;
                });
                select.innerHTML = html;
                console.log('✅ Categorías:', data.data.length);
            }
        })
        .catch(err => {
            console.error('❌ Categorías:', err);
            mostrarError('Error cargando categorías');
        });
}

function cargarFolio() {
    let almacen=document.getElementById("almacen_guardar_gasto");
    console.log('📄 Folio...');
    fetch(`/myvet/app/controllers/egresosController.php?action=getSiguienteFolioGasto&almacen=${almacen}`)
          .then(res => {
            if (!res.ok) throw new Error('HTTP: ' + res.status);
            return res.json();
        })
        .then(data => {
            const input = document.getElementById('folio_gasto');
            if (data.success && input) {
                input.value = data.folio;
                console.log('✅ Folio:', data.folio);
            }
        })
        .catch(err => {
            console.error('❌ Folio:', err);
            document.getElementById('folio_gasto').value = 'ERR-' + Date.now();
        });
}

function guardarGasto() {
    // 1. Obtener los elementos de forma segura
    const inputTotal = document.getElementById('inputTotalGasto');
    const selectCat = document.getElementById('select_categoria_gasto');

    // 2. Validaciones previas
    if (!selectCat || !selectCat.value) {
        return mostrarError('Por favor, seleccione una categoría');
    }

    const total = parseFloat(inputTotal ? inputTotal.value : 0);
    if (total <= 0) {
        return mostrarError('El total del gasto debe ser mayor a 0');
    }

    // 3. Preparar el envío
    const formData = new FormData(formGasto);

    // Debug para que veas en consola qué se está yendo realmente
    console.log('ID Categoría detectado:', formData.get('categoria_id'));

    const btn = formGasto.querySelector('button[type="submit"]');
    const textoOriginal = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    fetch('/myvet/app/controllers/egresosController.php?action=guardarGastoInsumo', {
            method: 'POST',
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error(`Error servidor: ${res.status}`);
            return res.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    const inst = bootstrap.Modal.getInstance(modalGastoEl);
                    if (inst) inst.hide();
                    location.reload();
                });
            } else {
                throw new Error(data.message || 'Error al procesar el gasto');
            }
        })
        .catch(err => {
            console.error('❌ Error en guardado:', err);
            mostrarError(err.message);
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
        });
}

function limpiarTabla() {
    document.querySelectorAll('#tablaConceptosGasto tbody tr:not(:first-child)')
        .forEach(f => f.remove());

    const primera = document.querySelector('#tablaConceptosGasto tbody tr');
    if (primera) {
        primera.querySelector('input[name="desc[]"]').value = '';
        primera.querySelector('.cant').value = '1';
        primera.querySelector('.items').value = '';
        primera.querySelector('.precio').value = '0.00';
    }
}

function calcularGasto() {
    let total = 0;
    document.querySelectorAll('#tablaConceptosGasto tbody tr').forEach(fila => {
        const cant = parseFloat(fila.querySelector('.cant').value) || 0;
        const precio = parseFloat(fila.querySelector('.precio').value) || 0;
        const subtotal = cant * precio;
        fila.querySelector('.subtotal_fila').textContent = '$' + subtotal.toFixed(2);
        total += subtotal;
    });

    const txtTotal = document.getElementById('txtTotalGasto');
    const inputTotal = document.getElementById('inputTotalGasto');
    txtTotal.textContent = '$' + total.toLocaleString('es-MX', {
        minimumFractionDigits: 2
    });
    inputTotal.value = total;
}

function mostrarError(msg) {
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: msg,
        toast: true,
        position: 'top-end'
    });
}

function abrirModalGasto() {
    if (modalGastoEl) {
        new bootstrap.Modal(modalGastoEl).show();
    }
}

async function agregarFilaGasto() {
    const resp = await fetch(
        `/myvet/app/controllers/egresosController.php?action=obtenerInsumosSelect`
    );

    const res = await resp.json();
    let data = res.data;



    let insumos = ``;
    console.log(res.data);
    data.forEach(m => {

        insumos += `
   
    <option 
        value="${m.id}"
       
        data-nombre="${m.nombre}"
    >
        ${m.nombre}
    </option>

    `;
    });
    const tbody = document.querySelector('#tablaConceptosGasto tbody');
    const fila = document.createElement('tr');
    fila.innerHTML = `
        <td><input type="text" name="desc[]" class="form-control form-control-sm  " style="border-radius: 8px;" required></td>
        <td><input type="number" name="cant[]" class="form-control form-control-sm   cant text-center" style="border-radius: 8px;" value="1" min="0" step="any"></td>
           <td>
                <select 
                    name="items[]" 
                    class="form-select items"
                   
                >
                   

                    <option value="">
                        
                    </option>
                    ${insumos}
                </select>
            </td>
        <td><input type="number" name="precio[]" class="form-control form-control-sm   precio" style="border-radius: 8px;" value="0.00" min="0" step="0.01"></td>
        <td class="text-end fw-bold subtotal_fila pe-3">$0.00</td>
        <td><button type="button" class="btn btn-sm text-danger" onclick="this.closest('tr').remove(); calcularGasto();">
            <i class="bi bi-trash"></i>
        </button></td>`;
    tbody.appendChild(fila);
    calcularGasto();
}
// ==================== FUNCIONES DE CATEGORÍA ====================

/**
 * Abre el modal pequeño para registrar una nueva categoría de gasto
 */
function abrirModalNuevaCategoria() {
    // Usamos jQuery para asegurar compatibilidad con tu estructura anterior
    // o Bootstrap nativo si prefieres
    const modalCat = new bootstrap.Modal(document.getElementById('modalNuevaCategoriaGasto'));
    modalCat.show();
}
function abrirModalNuevoInsumo() {
    // Usamos jQuery para asegurar compatibilidad con tu estructura anterior
    // o Bootstrap nativo si prefieres
    const modalInsumo = new bootstrap.Modal(document.getElementById('modalNuevoInsumo'));
    modalInsumo.show();
}

/**
 * Envía la nueva categoría al controlador y actualiza el select principal
 */async function guardarNuevoInsumo() {
    // ✅ CORRECCIÓN: Obtener los elementos correctos usando jQuery para mantener tu consistencia
    const $nombreInput = $('#nuevo_nombre_insumo');
    const $descInput = $('#nuevo_descripcion_insumo');
    const $unidadmaxima = $('#nuevo_maximo_insumo');
    const $unidadminima = $('#nuevo_minimo_insumo');
    const $factor = $('#factor');
    
    const nombre = $nombreInput.val() ? $nombreInput.val().trim() : '';
    const descripcion = $descInput.val() ? $descInput.val().trim() : '';
const uma = $unidadmaxima.val() ? $unidadmaxima.val().trim() : '';
const umi = $unidadminima.val() ? $unidadminima.val().trim() : '';
const factor = $factor.val() ? $factor.val() : 1;

    if (!nombre) {
        return Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'Debes escribir el nombre del insumo', // ✅ Mensaje corregido
            toast: true,
            position: 'top-end',
            timer: 3000
        });
    }

    // Preparar datos para el controlador
    const datos = new FormData();
    datos.append('ajax', 'guardar_insumo'); 
    datos.append('nombre', nombre);
    datos.append('descripcion', descripcion);
 datos.append('uma', uma);
 datos.append('umi', umi);
 datos.append('factor', factor);

    // Botón de carga seguro
    const btn = document.querySelector('#modalNuevoInsumo .btn-primary');
    if (!btn) return;
    const textoOriginal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    // Petición al controlador de egresos
    fetch('/myvet/app/controllers/egresosController.php', {
            method: 'POST',
            body: datos
        })
        .then(res => {
            if (!res.ok) throw new Error('Respuesta del servidor no válida');
         return res.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Insumo Creado',
                    timer: 1500,
                    showConfirmButton: false
                });

                // 1. Limpiar los campos reales del insumo
                $nombreInput.val('');
                $descInput.val('');

                // 2. Cerrar el modal de insumo de forma segura con Bootstrap nativo
                const modalEl = document.getElementById('modalNuevoInsumo');
                if (modalEl) {
                    const modalInst = bootstrap.Modal.getInstance(modalEl);
                    if (modalInst) modalInst.hide();
                }

                // 3. ✅ SOLUCIÓN: Actualizar dinámicamente los selects de insumos en la tabla de conceptos
                // Como tu función agregarFilaGasto() ya hace un fetch al backend buscando los insumos actualizados,
                // lo ideal es mandar llamar esa misma lógica o refrescar el selector de la primera fila.
                actualizarTodosLosSelectsInsumos();

            } else {
                throw new Error(data.message || 'Error al guardar');
            }
        })
        .catch(err => {
            console.error('❌ Error Insumo:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.message,
                confirmButtonColor: '#0d6efd'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
        });
}

/**
 * Helper para actualizar todos los selects ".items" de tu tabla sin romper las filas agregadas
 */
async function actualizarTodosLosSelectsInsumos() {
    try {
        const resp = await fetch('/myvet/app/controllers/egresosController.php?action=obtenerInsumosSelect');
        if (!resp.ok) throw new Error();
        const res = await resp.json();
        
        if (res.success && Array.isArray(res.data)) {
            // Buscamos todos los selectores de insumos en el DOM actual de la tabla
            document.querySelectorAll('#tablaConceptosGasto .items').forEach(select => {
                const valorActual = select.value; // Guardamos lo que el usuario ya tenía seleccionado para no borrárselo
                
                let html = '<option value=""></option>';
                res.data.forEach(m => {
                    html += `<option value="${m.id}" data-nombre="${m.nombre}" ${m.id == valorActual ? 'selected' : ''}>${m.nombre}</option>`;
                });
                
                select.innerHTML = html;
            });
            console.log('🔄 Selects de insumos actualizados con éxito.');
        }
    } catch (e) {
        console.error('No se pudieron refrescar los selectores de insumos', e);
    }
}
function guardarNuevaCategoria() {
    const nombreInput = document.getElementById('nuevo_nombre_cat');
    const nombre = nombreInput.value.trim();

    if (!nombre) {
        return Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'Debes escribir el nombre de la categoría',
            toast: true,
            position: 'top-end',
            timer: 3000
        });
    }

    // Preparar datos para el controlador (Coincidiendo con el Modelo)
    const datos = new FormData();
    datos.append('ajax', 'guardar_categoria_egreso'); // Acción para el controlador
    datos.append('nombre', nombre);

    // Botón de carga
    const btn = document.querySelector('#modalNuevaCategoriaGasto .btn-primary');
    const textoOriginal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    // Petición al controlador de egresos
    fetch('/myvet/app/controllers/egresosController.php', {
            method: 'POST',
            body: datos
        })
        .then(res => {
            // Validación de seguridad para el JSON
            if (!res.ok) throw new Error('Respuesta del servidor no válida');
            return res.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Categoría creada',
                    timer: 1500,
                    showConfirmButton: false
                });

                // 1. Limpiar campos
                nombreInput.value = '';
                const descInput = document.getElementById('nuevo_desc_cat');
                if (descInput) descInput.value = '';

                // 2. Cerrar el modal de categoría usando la instancia de Bootstrap
                const modalEl = document.getElementById('modalNuevaCategoriaGasto');
                const modalInst = bootstrap.Modal.getInstance(modalEl);
                if (modalInst) modalInst.hide();

                // 3. Actualizar el select principal de Gastos
                cargarCategorias();

            } else {
                throw new Error(data.message || 'Error al guardar');
            }
        })
        .catch(err => {
            console.error('❌ Error Cat:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.message,
                confirmButtonColor: '#0d6efd'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
        });
}
</script>
<script>
// Selecciona todos los inputs de texto y también los textareas
document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
    elemento.addEventListener('input', function() {
        // Convierte el valor a mayúsculas en tiempo real
        this.value = this.value.toUpperCase();
    });
});
</script>