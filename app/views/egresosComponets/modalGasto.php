<div class="modal fade" id="modalGasto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 22px;">
            <form id="formNuevoGasto" enctype="multipart/form-data">
                <div class="modal-header bg-warning text-dark" style="border-radius: 22px 22px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i> Registrar Nuevo Gasto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Folio/Factura</label>
                            <input type="text" id="folio_gasto" name="folio" class="form-control border border-subtle" style="border-radius: 12px;" placeholder="Cargando..." readonly required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Almacén Destino</label>
                            <select name="almacen_id"id="almacen_guardar_gasto" class="form-select border border-subtle" style="border-radius: 12px;" <?= ($_SESSION['rol_id'] != 1) ? 'readonly style="pointer-events: none;"' : '' ?> required>
                                <?php foreach($almacenes as $alm): ?>
                                <option value="<?= $alm['id'] ?>" <?= ($_SESSION['almacen_id'] == $alm['id']) ? 'selected' : '' ?>>
                                    <?= $alm['nombre'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary">Categoría de Gasto</label>
                            <div class="input-group">
                                <select id="select_categoria_gasto" name="categoria_id" class="form-select border border-subtle" style="border-radius: 12px 0 0 12px;" required>
                                    <option value="">Seleccione categoría...</option>
                                    </select>
                                <!-- <button type="button" class="btn btn-primary" style="border-radius: 0 12px 12px 0;" onclick="abrirModalNuevaCategoria()">
                                    <i class="bi bi-plus-lg"></i>
                                </button> -->
                            </div>
                        </div>

                        <div class="col-md-6">
                             <label class="form-label small fw-bold">Escriba el Proveedor ó elija uno</label>

                            <div class="input-group">
                                                       <input type="text"id="beneficiario" name="beneficiario" class="form-control border border-subtle" style="border-radius: 12px;" placeholder="Ej: CFE, Gasolinera..." required>
                     
     
   

                         
                           
                             <select class="form-select " id="select-proveedor" name="proveedor_id" onchange="actualizar()">
   <option value="">Seleccione...</option>
                            </select>
                            <button class="btn btn-outline-success" type="button"
                                            onclick="abrirModalNuevoProveedor()">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                        </div>   </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Método de Pago</label>
                            <select name="metodo_pago" class="form-select border border-subtle" style="border-radius: 12px;">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Comprobante (Evidencia)</label>
                            <input type="file" name="documento" class="form-control border border-subtle" style="border-radius: 12px;" accept=".jpg,.png,.pdf">
                        </div>
                    </div>

                    <hr class="text-body-secondary">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 card-title-text">Conceptos del Gasto</h6>
                        <button type="button" class="btn btn-sm fw-bold border border-subtle card-title-text" onclick="agregarFilaGasto()">
                            <i class="bi bi-plus-circle-fill"></i> Agregar Concepto
                        </button>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-borderless align-middle" id="tablaConceptosGasto">
                            <thead class="border border-subtle">
                                <tr class="small text-uppercase">
                                    <th>Descripción</th>
                                    <th width="100">Cant.</th>
                                    <th width="130">Precio</th>
                                    <th width="120" class="text-end pe-3">Subtotal</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-bottom">
                                    <td><input type="text" name="desc[]" class="form-control form-control-sm border border-subtle" style="border-radius: 8px;" required></td>
                                    <td><input type="number" name="cant[]" class="form-control form-control-sm border border-subtle cant text-center" style="border-radius: 8px;" value="1" step="any" oninput="calcularGasto()"></td>
                                    <td><input type="number" name="precio[]" class="form-control form-control-sm border border-subtle precio" style="border-radius: 8px;" value="0.00" step="any" oninput="calcularGasto()"></td>
                                    <td class="text-end fw-bold subtotal_fila pe-3">$0.00</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Observaciones</label>
                            <textarea name="observaciones" class="form-control text-uppercase border border-subtle" style="border-radius: 12px;" rows="2" placeholder="Notas internas..."></textarea>
                       <input type="date" id="fecha"name="fecha" value="<?= date("Y-m-d") ?>" class="form-control border border-subtle" style="border-radius: 12px;">
                        </div>
                        <div class="col-md-5 text-end">
                            <h4 class="text-body-secondary small fw-bold mb-0">TOTAL</h4>
                            <h2 class="fw-bold text-dark" id="txtTotalGasto">$ 0.00</h2>
                            <input type="hidden" name="total_final" id="inputTotalGasto" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 12px;">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm" style="border-radius: 12px;">Guardar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevaCategoriaGasto" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.4);">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 20px;">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Nueva Categoría</h6>
                <button type="button" class="btn-close" onclick="$('#modalNuevaCategoriaGasto').modal('hide')"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="mb-3">
                    <label class="small fw-bold text-body-secondary">Nombre</label>
                    <input type="text" id="nuevo_nombre_cat" class="form-control border border-subtle" style="border-radius: 10px;" placeholder="Ej: Servicios">
                </div>
               
            </div>
            <div class="modal-footer pt-0">
                <button type="button" class="btn btn-primary w-100 fw-bold" onclick="guardarNuevaCategoria()" style="border-radius: 10px;">Agregar</button>
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

function actualizar()
{
     console.log($('#select-proveedor').val());
    $('#beneficiario').val($('#select-proveedor').val());
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
    
    fetch('/myvet/app/controllers/egresosController.php?action=guardarGasto', {
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
                console.log(data.id);
                if(inst) inst.hide();
                
                 gastoDetalle_cargarVista('gasto', data.id);
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
    txtTotal.textContent = '$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2});
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

function agregarFilaGasto() {
    const tbody = document.querySelector('#tablaConceptosGasto tbody');
    const fila = document.createElement('tr');
    fila.innerHTML = `
        <td><input type="text" name="desc[]" class="form-control form-control-sm border border-subtle" style="border-radius: 8px;" required></td>
        <td><input type="number" name="cant[]" class="form-control form-control-sm border border-subtle cant text-center" style="border-radius: 8px;" value="1" min="0" step="any"></td>
        <td><input type="number" name="precio[]" class="form-control form-control-sm border border-subtle precio" style="border-radius: 8px;" value="0.00" min="0" step="0.01"></td>
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

/**
 * Envía la nueva categoría al controlador y actualiza el select principal
 */function guardarNuevaCategoria() {
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
            if(descInput) descInput.value = '';

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
}</script>
<script>
    // Selecciona todos los inputs de texto y también los textareas
    document.querySelectorAll('input[type="text"], textarea').forEach(elemento => {
        elemento.addEventListener('input', function() {
            // Convierte el valor a mayúsculas en tiempo real
            this.value = this.value.toUpperCase();
        });
    });
</script>