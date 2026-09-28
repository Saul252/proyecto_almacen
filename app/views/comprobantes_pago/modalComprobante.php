<div class="modal fade" id="modalCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content"
            style="border-radius: 20px;  width: 95%; max-width: 1140px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
            <form id="formSolicitud">

                <div class="modal-header   pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-3 p-2 me-3 shadow-sm d-flex align-items-center justify-content-center"
                            style="width: 45px; height: 45px;">
                            <i class="bi bi-file-earmark-plus fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 ">Nuevo Comprobante / Depósito</h4>
                            <p class="text-body-secondary small mb-0">Complete los datos para registrar el movimiento en el
                                sistema</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pt-3">
                    <div class="row g-3 p-4 rounded-4  border align-items-end mb-2">

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-box-seam me-1"></i> Almacén de Cargo
                            </label>
                            <div class="input-group">
                                <span class="input-group-text  border-end-0 text-body-secondary"><i
                                        class="bi bi-geo-alt"></i></span>
                                <select name="almacen_id" id="almacen_id" class="form-select border-start-0 ps-0"
                                    required>
                                  
                                    <?php foreach($almacenes as $a): ?>
                                    <option value="<?= $a['id'] ?>">
                                        <?= htmlspecialchars($a['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-person me-1"></i> Cliente
                            </label>
                            <div class="input-group">
                                <select name="cliente_id" id="cliente_id" class="form-select select2-modal" required>
                                   
                                   
                                    
                                </select>
                                <button class="btn btn-primary" type="button" onclick="abrirModalNuevoCliente()"
                                    title="Registrar nuevo cliente">
                                    <i class="bi bi-person-plus-fill"></i>
                                </button>
                            </div>
                        </div>
                           <div class="col-md-8 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-bookmark me-1"></i> Numero(s) de ordenes
                            </label>
                            <input type="text" placeholder="Ej. Pago compra orden #123...." 
                                 id="numero_venta"
                                name="numero_venta" class="form-control">
                        </div>

<?php if ($rolAct==1): ?>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-calendar3 me-1"></i> Fecha de Depósito
                            </label>
                            <input type="date" id="fecha_deposito" value="<?=date('Y-m-d')?>" name="fecha_deposito" class="form-control" required>
                        </div>
<?php endif; ?>
                        <div class="col-md-4 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-currency-dollar me-1"></i> Monto
                            </label>
                            <div class="input-group">
                                <span class="input-group-text  text-success fw-bold">$</span>
                                <input type="number" step="0.01" placeholder="0.00" id="monto_depositado"
                                    name="monto_depositado" class="form-control fw-bold " required>
                            </div>
                        </div>
                        <div class="col-5">
                            <select id="metodo_pago_m" name="metodo_pago_m" class="form-select fw-bold">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta</option>

                            </select>
                        </div>

                        <div class="col-md-8 mt-3">
                            <label class="form-label small fw-bold text-secondary mb-2">
                                <i class="bi bi-bookmark me-1"></i> Referencia / Concepto
                            </label>
                            <input type="text" placeholder="Ej. Pago compra orden #123...."id="referencia"
                                name="referencia" class="form-control">
                        </div>

                    </div>
                </div>

                <div class="modal-footer  p-4 pt-2">
                    <button type="button" class="btn btn-light text-body-secondary fw-bold rounded-pill px-4 me-2"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-2"></i> Crear Comprobante
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<script>
const URL_CONTROLADOR = '/myvet/app/controllers/comprobantesPagoController.php';

// =====================================================
// SELECT2
// =====================================================

$('.select2-modal').select2({
    theme: 'bootstrap-5',
    dropdownParent: $('#modalCotizacion')
});
 document.addEventListener('DOMContentLoaded', function() {
        const selectAlmacen = document.getElementById('almacen_id');

        if (selectAlmacen) {
            selectAlmacen.addEventListener('change', function(e) {
                const almacenId = this.value; // ID del almacén seleccionado
                const textoSeleccionado = this.options[this.selectedIndex].text; // Nombre del almacén

                if (almacenId) {
                    console.log(`Almacén cambiado a ID: ${almacenId} - ${textoSeleccionado}`);
                   
                    const id = $('#almacen_id').val();
                   

                    cargarClientes();

                    // 🚀 Coloca aquí la función o lógica que deseas ejecutar
                    // Ejemplo: cargarProductosPorAlmacen(almacenId);
                } else {
                    console.log('Se deseleccionó el almacén');
                     cargarClientes();
                }
            });
        }
    });
 
    
async function cargarClientes() {
    console.log("cargo clientes");
    
    // Obtenemos el ID del almacén actual
    const almacenId = $('#almacen_id').val();
    const select = document.getElementById('cliente_id');
    console.log('almacen',almacenId);
    if (!select) return;

    // Limpiamos el select antes de poblarlo
    select.innerHTML = '<option value="">-- Seleccione un cliente --</option>';

    try {
        const url = '/myvet/app/controllers/accesoController.php?action=obtenerClientes';
        const respuesta = await fetch(url);

        if (!respuesta.ok) throw new Error('Error en la respuesta del servidor');

        const resultado = await respuesta.json();
        console.log(resultado);

        if (resultado.success && Array.isArray(resultado.data)) {
            
            // FILTRADO: 
            // 1. Conserva clientes cuyo nombre NO contenga "público en general" (clientes normales).
            // 2. Para "público en general", solo conserva el que coincida con el almacenId actual.
            const clientesFiltrados = resultado.data.filter(cliente => {
                const nombreNorm = cliente.nombre_comercial.toLowerCase().trim();
                const esPublicoGeneral = nombreNorm.includes('publico en general') || nombreNorm.includes('público en general');

                if (esPublicoGeneral) {
                    // Revisa que coincida el ID del almacén (compara tanto número como string)
                    return cliente.almacen_id == almacenId;
                }

                // Si es un cliente regular, se muestra siempre
                return true;
            });

            // Llenamos el select únicamente con la lista filtrada
            (resultado.data).forEach(cliente => {
                const opcion = document.createElement('option');
                opcion.value = cliente.id;
                opcion.textContent = `${cliente.nombre_comercial}`;
                select.appendChild(opcion);
            });

        } else {
            select.innerHTML = '<option value="">No se pudieron cargar los usuarios</option>';
        }
    } catch (error) {
        select.innerHTML = '<option value="">Error al cargar la lista</option>';
        console.error('Error al ejecutar cargarClientes:', error);
    }
}
 document.addEventListener('DOMContentLoaded', () => {
       
        cargarClientes();
    });

// =====================================================
// CALCULAR TOTAL
// =====================================================

// 🔥 EVITAR LOOPS
let recalculandoFila = false;
let totaLCompra;


// =====================================================
// AGREGAR PRODUCTO
// =====================================================

// =====================================================
// GUARDAR SOLICITUD
// =====================================================
// // =====================================================
// CONVERTIR A COMPRA
// =====================================================
$('#formSolicitud').on('submit', async function(e) {
    e.preventDefault();

    const payload = {
        almacen_id: $('#almacen_id').val(),
        cliente_id: $('#cliente_id').val(),
        monto_depositado: $('#monto_depositado').val(),
        referencia: $('#referencia').val(),
        fecha: $('#fecha_deposito').val(),
        metodo: $('#metodo_pago_m').val(),
        numeroventa: $('#numero_venta').val(),


    };

    console.log('JSON ENVIADO:', payload);

    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const resp = await fetch(`${URL_CONTROLADOR}?action=guardar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const res = await resp.json();
        console.log('RESPUESTA:', res);

        if (res.status === 'success') {
            await Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
            });
            location.reload();
        } else {
            Swal.fire('Error', res.message, 'error');
        }

    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Fallo de conexión o error en el servidor', 'error');
    }
});
// $('#formConvertirCompra').on('submit', async function(e) {

//     e.preventDefault();

//     Swal.fire({
//         title: 'Procesando ingreso...',
//         allowOutsideClick: false,
//         didOpen: () => Swal.showLoading()
//     });

//     try {

//         const resp = await fetch(
//             `${URL_CONTROLADOR}?action=convertirACompra`, {
//                 method: 'POST',
//                 body: new FormData(this)
//             }
//         );

//         const res = await resp.json();

//         if (res.status === 'success') {

//             await Swal.fire({
//                 icon: 'success',
//                 title: 'Ingresado',
//                 text: res.message
//             });

//             location.reload();

//         } else {

//             Swal.fire(
//                 'Error',
//                 res.message,
//                 'error'
//             );
//         }

//     } catch (e) {

//         Swal.fire(
//             'Error',
//             'Fallo de conexión',
//             'error'
//         );
//     }
// });

// =====================================================
// ELIMINAR FILA
// =====================================================

function quitarFila(id) {

    $(`#fila-${id}`).remove();

    if (!$('#tablaDetalle tbody tr').length) {

        $('#emptyState').removeClass('d-none');
    }
}

// =====================================================
// NUEVA SOLICITUD
// =====================================================

function nuevaCotizacion() {

    $('#formSolicitud')[0].reset();

    $('#tablaDetalle tbody').empty();

    $('#emptyState').removeClass('d-none');

    $('#modalCotizacion').modal('show');

}
</script>
