<!-- Botón para disparar el modal (Ejemplo) -->

<style>
    /* Fondos suaves y elegantes para las tarjetas superiores */
.bg-primary-soft { background-color: rgba(13, 110, 253, 0.08); }
.bg-success-soft { background-color: rgba(25, 135, 84, 0.08); }

.card-total {
    border-radius: 12px;
    transition: transform 0.2s;
}

/* Renglones de la lista de selección */
.tarjeta-seleccion-venta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 16px;
    border-bottom: 1px solid #f1f1f4;
    transition: background-color 0.2s;
}
.tarjeta-seleccion-venta:last-child { border-bottom: none; }
.tarjeta-seleccion-venta:hover { background-color: #f8f9fa; }

/* Botones adaptados y estilizados */
.btn-sm-custom {
    font-size: 13px;
    padding: 6px 14px;
    border-radius: 8px;
}

/* Estilo limpio para el input de la tabla */
.input-tabla-abono {
    width: 100%;
    padding: 6px 12px;
    border: 2px solid #e5e5ea;
    border-radius: 8px;
    text-align: right;
    font-weight: 600;
    color: #1c1c1e;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.input-tabla-abono:focus {
    outline: none;
    border-color: #007aff;
    box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
}

/* Scrollbars delgadas estilo moderno */
#contenedor-disponibles::-webkit-scrollbar { width: 6px; }
#contenedor-disponibles::-webkit-scrollbar-track { background: #f1f1f4; }
#contenedor-disponibles::-webkit-scrollbar-thumb { background: #c1c1c4; border-radius: 3px; }
</style>
<!-- Modal de Bootstrap 5 -->
<div class="modal fade" id="modalDispersión" tabindex="-1" aria-labelledby="modalDispersiónLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius: 16px;">
            
            <!-- Encabezado -->
            <div class="modal-header  bg-light py-3 px-4" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h4>Comprobante de pago 
                <span class="fs-5 fw-bold text-dark"><span id="idComprobante">0.00</span></span>

                </h4>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          
            <input type="hidden" id="idC">
            
            <!-- Cuerpo del Modal -->
          <div class="modal-body p-4">
    
    <!-- Indicadores de Totales (Estilo Cards iOS) -->
    <div class="row g-3 mb-4">
          <h5 class="modal-title fw-bold text-dark" id="modalDispersiónLabel">Distribución y Asignación de Saldos Comprobante </h5> 
               
        <div class="col-4">
            <div class="card card-total text-center  bg-light p-2">
                <span class="text-body-secondary small fw-medium">Monto Inicial</span>
                <span class="fs-5 fw-bold text-dark">$<span id="txt-monto-inicial">0.00</span></span>
            </div>
        </div>
        <div class="col-4">
            <div class="card card-total text-center  bg-primary-soft p-2">
                <span class="text-primary small fw-medium">Total Asignado</span>
                <span class="fs-5 fw-bold text-primary">$<span id="txt-total-asignado">0.00</span></span>
            </div>
        </div>
        <div class="col-4">
            <div class="card card-total text-center  bg-success-soft p-2">
                <span class="text-success small fw-medium">Sobrante</span>
                <span class="fs-5 fw-bold text-success">$<span id="txt-sobrante">0.00</span></span>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 1: Tabla de Ventas Disponibles (Con Scrollbar) -->
    <div class="mb-4">
        <h6 class="fw-bold text-secondary mb-2 small text-uppercase tracking-wider">1. Ventas Disponibles del Cliente</h6>
        <div class="table-responsive border rounded-3 bg-white shadow-sm" style="max-height: 180px; overflow-y: auto;">
            <table class="table align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light sticky-top">
                    <tr>
                        <th class="ps-3">Folio</th>
                        <th>Cliente</th>
                        <th>Pendiente / Total</th>
                        <th class="text-center" style="width: 100px;">Acción</th>
                    </tr>
                </thead>
                <tbody id="contenedor-disponibles">
                    <!-- Inserción dinámica por JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECCIÓN 2: Tabla de Renglones Activos -->
    <div>
        <h6 class="fw-bold text-secondary mb-2 small text-uppercase tracking-wider">2. Renglones a Aplicar Abono</h6>
        <div class="table-responsive border rounded-3 bg-white shadow-sm">
            <table class="table align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Folio</th>
                        <th>Cliente</th>
                        <th>Saldo Pendiente</th>
                        <th style="width: 160px;">Cantidad a Abonar</th>
                        <th class="text-center" style="width: 100px;">Acción</th>
                    </tr>
                </thead>
                <tbody id="contenedor-renglones">
                    <!-- Inserción dinámica por JS -->
                </tbody>
            </table>
        </div>
    </div>

</div>
            
            <!-- Pie del Modal -->
            <div class="modal-footer  bg-light py-3 px-4" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <button type="button" class="btn btn-secondary px-4 rounded-3 btn-sm-custom" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary px-4 rounded-3 btn-sm-custom fw-semibold" onclick="guardarDispersiónPagos()">Procesar Pago</button>
            </div>

        </div>
    </div>
</div>
<script>
    let montoInicialTotal =0;
    let ventasDisponibles = [];
    let aplicado_inicial=0;
   async function getDeuda(id, monto,idComprobante,aplicado) {
    aplicado_inicial=aplicado;
    console.log(aplicado_inicial);
    console.log(id, monto);
    ventasDisponibles = [];
    
    const params = new URLSearchParams({
        action: 'listarClientesDeuda',
        f_search: '',
        f_rango: 'todos',
        f_inicio: '',
        f_fin:'',
        f_almacen: 0,
        f_status: '',
        f_pago: 'deuda',
        f_cliente: id,
        f_factura: ''
    });

    try {
        const res = await fetch(`/myvet/app/controllers/ventasHistorialController.php?${params.toString()}`);
        const data = await res.json();
        $('#idC').val(idComprobante);     
        $('#idComprobante').text(idComprobante);        
        // 1. Guardamos la respuesta del servidor
        ventasDisponibles = data;
        console.log("Ventas cargadas desde el controlador:", ventasDisponibles);
        
        // 2. Ejecutamos la inicialización que actualiza los totales y RENDERIZA las tablas
        inicializarModal(monto);

    } catch (error) {
        console.error("Error al obtener la deuda del cliente:", error);
    }
}


let ventasAplicadas = [];

 

function inicializarModal(montoExterno) {
    if(montoExterno) montoInicialTotal = parseFloat(montoExterno);
    renderizarListas();
}

function aplicarVenta(idVenta) {
    const index = ventasDisponibles.findIndex(v => v.id === idVenta);
    if (index !== -1) {
        const venta = ventasDisponibles[index];
        const saldoPendiente = parseFloat(venta.total) - parseFloat(venta.pagado);

        ventasAplicadas.push({
            ...venta,
            saldo_pendiente: saldoPendiente,
            monto_abono: 0.00 
        });

        ventasDisponibles.splice(index, 1);
        renderizarListas();
        
        setTimeout(() => {
            const input = document.getElementById(`input-abono-${idVenta}`);
            if(input) { input.focus(); input.select(); }
        }, 50);
    }
}

function quitarVenta(idVenta) {
    const index = ventasAplicadas.findIndex(v => v.id === idVenta);
    if (index !== -1) {
        const venta = ventasAplicadas[index];
        delete venta.saldo_pendiente;
        delete venta.monto_abono;

        ventasDisponibles.push(venta);
        ventasAplicadas.splice(index, 1);
        renderizarListas();
    }
}

function calcularRenglon(idVenta, valorInput) {
    const venta = ventasAplicadas.find(v => v.id === idVenta);
    if (!venta) return;

    let nuevoAbono = parseFloat(valorInput) || 0;

    const sumOtrosRenglones = ventasAplicadas
        .filter(v => v.id !== idVenta)
        .reduce((sum, v) => sum + v.monto_abono, 0);

    const disponibleGlobal = montoInicialTotal - sumOtrosRenglones;
    const maximoPermitido = Math.min(venta.saldo_pendiente, disponibleGlobal);

    if (nuevoAbono > maximoPermitido) nuevoAbono = maximoPermitido;
    if (nuevoAbono < 0) nuevoAbono = 0;

    venta.monto_abono = nuevoAbono;
    
    document.getElementById(`input-abono-${idVenta}`).value = nuevoAbono > 0 ? nuevoAbono : '';
    actualizarResumenTotales();
}

function actualizarResumenTotales() {
    const totalAsignado = ventasAplicadas.reduce((sum, v) => sum + v.monto_abono, 0)+aplicado_inicial;

    const sobrante = montoInicialTotal - totalAsignado;

    document.getElementById('txt-monto-inicial').textContent = montoInicialTotal.toFixed(2);
    document.getElementById('txt-total-asignado').textContent = totalAsignado.toFixed(2);
    document.getElementById('txt-sobrante').textContent = sobrante.toFixed(2);
}

function renderizarListas() {
    // 1. Renderizar lista superior (Ventas disponibles)
    const contenedorDisponibles = document.getElementById('contenedor-disponibles');
    contenedorDisponibles.innerHTML = '';
    
    if (ventasDisponibles.length === 0) {
        contenedorDisponibles.innerHTML = `<div class="p-3 text-center text-body-secondary small">No hay más ventas pendientes disponibles.</div>`;
    } else {
        ventasDisponibles.forEach(v => {
            const saldo = parseFloat(v.total) - parseFloat(v.pagado);
            contenedorDisponibles.innerHTML += `
               <tr>
                    <td class="ps-3"><strong>${v.folio}</strong></td>
                    <td class="text-secondary">${v.cliente}</td>
                    <td>
                        <span class="text-danger fw-medium">$${saldo.toFixed(2)}</span>
                        <span class="text-body-secondary small d-block" style="font-size: 11px;">Total: $${v.total}</span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-primary btn-sm rounded-2 px-3 fw-medium" style="font-size: 12px;" onclick="aplicarVenta('${v.id}')">
                            Aplicar
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    // 2. Renderizar tabla inferior (Renglones agregados)
    const contenedorRenglones = document.getElementById('contenedor-renglones');
    contenedorRenglones.innerHTML = '';

    if (ventasAplicadas.length === 0) {
        contenedorRenglones.innerHTML = `<tr><td colspan="5" class="text-center text-body-secondary py-4">Ninguna venta seleccionada para abono.</td></tr>`;
    } else {
        ventasAplicadas.forEach(v => {
            contenedorRenglones.innerHTML += `
                <tr>
                    <td class="ps-3"><strong>${v.folio}</strong></td>
                    <td class="text-secondary small">${v.cliente}</td>
                    <td class="fw-medium text-dark">$${v.saldo_pendiente.toFixed(2)}</td>
                    <td>
                        <input 
                            type="number" 
                            id="input-abono-${v.id}" 
                            class="input-tabla-abono"
                            value="${v.monto_abono > 0 ? v.monto_abono : ''}" 
                            placeholder="0.00"
                            step="0.01"
                            oninput="calcularRenglon('${v.id}', this.value)"
                        />
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger  rounded-2" onclick="quitarVenta('${v.id}')">
                            ✕ Quitar
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    actualizarResumenTotales();
}
/**
 * 1. RECOLECTAR Y VALIDAR DATOS con advertencias estéticas
 */
function procesarEstructuraPagos() {
    // Validar si el usuario agregó renglones a la tabla inferior
    if (ventasAplicadas.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Sin ventas seleccionadas',
            text: 'Por favor, selecciona al menos una venta de la lista superior para aplicar un abono.',
            confirmButtonColor: '#0d6efd',
            customClass: { popup: 'rounded-4' } // Bordes suaves tipo iOS
        });
        return null;
    }

    // Filtrar únicamente las ventas que tengan un monto de abono real (mayor a 0)
    const ventasConAbono = ventasAplicadas.filter(v => v.monto_abono > 0);

    if (ventasConAbono.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Montos en cero',
            text: 'Ninguna de las ventas seleccionadas tiene un monto asignado mayor a $0.00',
            confirmButtonColor: '#0d6efd',
            customClass: { popup: 'rounded-4' }
        });
        return null;
    }

    // Mapeamos para separar en los dos arreglos paralelos
    const arregloIds = ventasConAbono.map(v => v.id);
    const arregloPagos = ventasConAbono.map(v => v.monto_abono);

    return {
        ids: arregloIds,
        pagos: arregloPagos
    };
}

/**
 * 2. ENVIAR DATOS POR POST con SweetAlert de carga y éxito
 */
/**
 * Ejecuta un ciclo asíncrono para guardar cada abono de forma independiente
 */
async function guardarDispersiónPagos() {
    // 1. Recolectamos los datos validados del modal (extrae ids y pagos de ventas con monto > 0)
    const datosPagos = procesarEstructuraPagos();
    if (!datosPagos) return; 

    // 2. Obtener la fecha de hoy en formato YYYY-MM-DD
   const ahora = new Date();

const yyyy = ahora.getFullYear();
const mm = String(ahora.getMonth() + 1).padStart(2, '0');
const dd = String(ahora.getDate()).padStart(2, '0');

const hh = String(ahora.getHours()).padStart(2, '0');
const min = String(ahora.getMinutes()).padStart(2, '0');
const ss = String(ahora.getSeconds()).padStart(2, '0');

// Formato AAAA-MM-DD HH:MM:SS (Ideal para Bases de Datos / MySQL)
const fechaHoy = `${yyyy}-${mm}-${dd} ${hh}:${min}:${ss}`;

    // Marcadores para el método de pago y la nota base
    const metodo = "Efectivo"; // Cambia este valor por el que use tu vista (ej. un select) o déjalo fijo
    
    // Bloqueamos la pantalla con SweetAlert antes de iniciar la ráfaga de peticiones
    Swal.fire({
        title: 'Aplicando abonos...',
        text: 'Procesando cada venta de forma individual.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
        customClass: { popup: 'rounded-4' }
    });

    try {
        // Usamos for...of en lugar de forEach para que el 'await' frene y espere 
        // a que termine una venta antes de pasar a la siguiente de manera ordenada
        for (let i = 0; i < datosPagos.ids.length; i++) {
            const Ventaid = datosPagos.ids[i];
            const monto = datosPagos.pagos[i];
            let idCom=  document.getElementById('idC').value;
            // Aquí puedes personalizar dinámicamente tu nota/referencia por cada iteración
            const referencia = `Abono desde Comprobante #${idCom}`;

            // Creamos el contenedor idéntico al que requiere tu función original
            const fd = new FormData();
            fd.append('venta_id', Ventaid);
            fd.append('monto', monto);
            fd.append('metodo_pago', metodo);
            fd.append('fecha_pago', fechaHoy); // <--- Inyectamos la fecha actual de hoy
            fd.append('referencia', referencia);

            console.log(`Enviando abono de $${monto} a la Venta ID: ${Ventaid}. Nota: ${referencia}`);

            // Disparamos la petición individual hacia tu controlador existente
            const res = await fetch(`/myvet/app/controllers/registrarPagosController.php?action=guardarAbono`, { 
                method: 'POST', 
                body: fd 
            });
             const fd2 = new FormData();
             let idC=$('#idC').val();
            
            
        fd2.append('id', idC);
        fd2.append('cantidadAplicada',monto)
       
          const resp = await fetch(`/myvet/app/controllers/comprobantesPagoController.php?action=actualizarAplicado`, {
                method: 'POST',
                body: fd2
            });
        
            
            const data = await res.json();

            // Si uno de los abonos falla en el servidor, interrumpimos el flujo para evitar descuadres
            if (data.status !== 'success') {
                throw new Error(data.message || `Error al procesar el abono para la venta ID: ${Ventaid}`);
            }
             
        }
       

        // Si el ciclo terminó de ejecutar todos los 'await' sin lanzar errores:
        Swal.fire({
            icon: 'success',
            title: '¡Todo Guardado!',
            text: 'Todos los abonos individuales se registraron con éxito.',
            confirmButtonColor: '#198754',
            customClass: { popup: 'rounded-4' }
        }).then(() => {
            // Ocultamos el modal de dispersión actual
            const modalElement = document.getElementById('modalDispersión');
            const modalInstancia = bootstrap.Modal.getInstance(modalElement);
            if (modalInstancia) modalInstancia.hide();

            // Recargamos la lista general de la aplicación
            location.reload();
        });

    } catch (error) {
        console.error("Fallo durante la dispersión secuencial:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error al procesar',
            text: error.message || 'Ocurrió un problema en el servidor al guardar un abono.',
            confirmButtonColor: '#dc3545',
            customClass: { popup: 'rounded-4' }
        });
    }
}
document.addEventListener("DOMContentLoaded", () => {
    
    // Ejemplo de inicialización con 500 pesos. 
    // Puedes invocar esta función cuando se abra el modal pasando la cifra que venga de tu caja o backend.
   
});
</script>