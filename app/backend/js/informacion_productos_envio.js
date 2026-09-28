
function filtrarProductosPorOrigen() {
    const origenId = document.getElementById('origen_id').value;
    const selectProd = document.getElementById('traspaso_producto');
    const infoStock = document.getElementById('info_stock');
    
    selectProd.innerHTML = '<option value="">Seleccione producto...</option>';
    if (infoStock) infoStock.innerText = '';
    
    if (!origenId) {
        selectProd.disabled = true;
        return;
    }

    // Buscamos en el array global que viene del PHP
    const productosDisponibles = productosInventario.filter(p => p.almacen_id == origenId && p.stock > 0);
    

    if (productosDisponibles.length > 0) {
        productosDisponibles.forEach(p => {
            console.log(p);
            const option = document.createElement('option');
            option.value = p.id;
            option.text = `${p.sku} - ${p.nombre}`;
            
            // ATRIBUTOS CRUCIALES PARA LA CONVERSIÓN
            option.dataset.stock = p.stock;
            option.dataset.factor = p.factor_conversion || 1;
            option.dataset.unidad = p.unidad_reporte || 'Unid.';
            option.dataset.medida= p.unidad_medida || 'Unid.';
            
            selectProd.appendChild(option);
        });
        selectProd.disabled = false;
    } else {
        selectProd.innerHTML = '<option value="">No hay productos disponibles</option>';
        selectProd.disabled = true;
    }
}

// Variables globales para controlar el límite en este traspaso
let factorTraspasoActual = 1;
let stockMaximoTraspaso = 0;
let unidadNombre = 0;
        let unidadMedida = 0;
async function lotes(producto_id,almacenId) {
    const resp = await fetch(`/myvet/app/controllers/entregasController.php?ajax=despachar&prodId=${producto_id}&almacen=${almacenId}`
        );
// 1. Seleccionamos el <select> directamente desde el div creado, es más seguro y no requiere que ya esté en el DOM principal
    const select = document.getElementById("lote_id");

    // // 2. Extraemos la data con encadenamiento opcional para evitar errores si viene vacío o undefined
    

    // // 3. Agregamos las opciones al select
    let lotes=await resp.json();
    lotes.data.forEach(lote => {
        const option = document.createElement('option');
        option.value = lote.id;
        option.textContent = `${lote.codigo_lote} (${lote.cantidad_actual})`;
        select.appendChild(option);
    });
    console.log( producto_id,almacenId);
    console.log( await resp.json());

        //return await resp.json();

    
}
function actualizarMaximo() {
    const selectProd = document.getElementById('traspaso_producto');
    const infoStock = document.getElementById('info_stock');
    const labelUnidad = document.getElementById('label_unidad_reporte');
      const labelmenor = document.getElementById('label_unidad_medida');
    const almacen = document.getElementById('origen_id');
    
    const selectedOption = selectProd.options[selectProd.selectedIndex];
    
    if (selectedOption && selectedOption.value !== "") {
        stockMaximoTraspaso = parseFloat(selectedOption.dataset.stock) || 0;
        factorTraspasoActual = parseFloat(selectedOption.dataset.factor) || 1;
         unidadNombre = selectedOption.dataset.unidad;
         unidadMedida = selectedOption.dataset.medida;
        let cantidadReal=stockMaximoTraspaso/factorTraspasoActual;
        lotes(selectedOption.value,almacen.value);
      

const bloque = document.getElementById('bloque_traspaso');

if (unidadMedida === unidadNombre) {
    bloque.style.display = 'none';
} else {
    bloque.style.display = 'block'; // o 'block' según tu layout
}

        if (infoStock) infoStock.innerText = `Stock disponible: ${cantidadReal>=1?cantidadReal +' '+ unidadNombre:stockMaximoTraspaso +' '+ unidadMedida}`;
        if (labelUnidad) labelUnidad.innerText = unidadNombre;
         if (labelmenor) labelmenor.innerText = unidadMedida;
        // Limpiar inputs al cambiar de producto
        document.getElementById('traspaso_factor_input').value = '';
        document.getElementById('traspaso_piezas_input').value = '';
        calcularTotalTraspaso();
    }
}

function calcularTotalTraspaso() {
    const cantMayor = parseFloat(document.getElementById('traspaso_factor_input').value) || 0;
    const cantSueltas = parseFloat(document.getElementById('traspaso_piezas_input').value) || 0;
    
    // LA FÓRMULA: (Toneladas * Factor) + Piezas
    const totalPiezas = (cantMayor * factorTraspasoActual) + cantSueltas;

    // Actualizamos el input oculto que se va al PHP
    document.getElementById('cantidad_traspaso_final').value = totalPiezas;

    // UI: Mostrar resumen y validar
    const txtTotal = document.getElementById('txt_total_pzas');
     const unidadF = document.getElementById('unidadF');
    const resumen = document.getElementById('resumen_conversion');
    const btn = document.getElementById('btnGuardarTraspaso');

    if (totalPiezas > 0) {
        resumen.style.display = 'block';
        let totalReal=totalPiezas/factorTraspasoActual;
        totalReal>=1?
        txtTotal.innerText = totalReal.toFixed(2)
        
        :
        txtTotal.innerText = totalPiezas.toFixed(2);
 totalReal>=1?
        unidadF.innerText = unidadNombre
        
        :
        unidadF.innerText =unidadMedida;


        // Validar contra el stock real
        if (totalPiezas > stockMaximoTraspaso) {
            txtTotal.classList.add('text-danger');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-x-circle"></i> Stock Insuficiente';
        } else {

            txtTotal.classList.remove('text-danger');
            btn.disabled = false;
            btn.innerHTML = 'Solicitar Movimiento';
        }
    } else {
        resumen.style.display = 'none';
        btn.disabled = true;
    }
}

// Vincular los eventos de escritura
document.getElementById('traspaso_factor_input').addEventListener('input', calcularTotalTraspaso);
document.getElementById('traspaso_piezas_input').addEventListener('input', calcularTotalTraspaso);

// Validación final antes de enviar
document.getElementById('formTraspaso').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const destino = document.getElementById('destino_id').value;
    const origen = document.getElementById('origen_id').value;

    if(origen === destino) {
        Swal.fire('Error', 'El destino no puede ser igual al origen', 'error');
        return;
    }

    Swal.fire({
        title: '¿Confirmar envío?',
        text: "La mercancía se descontará de tu stock actual.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, enviar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/myvet/app/backend/almacen/procesar_traspaso.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire('¡Enviado!', data.message, 'success')
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
});
    