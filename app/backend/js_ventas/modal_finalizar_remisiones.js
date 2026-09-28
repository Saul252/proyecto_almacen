/**
 * 4. FUNCIÓN PARA ABRIR EL MODAL DE FINALIZACIÓN
 */
window.abrirModalFinalizar = function () {
    if (!window.carrito || window.carrito.length === 0) {
        Swal.fire('Carrito vacío', 'Agrega productos antes de finalizar la venta.', 'warning');
        return;
    }

    const tabla = document.getElementById("tablaConfirmacion");
    if (!tabla) return;

    tabla.innerHTML = "";

    window.carrito.forEach((item, index) => {
 item.entrega_hoy = Math.round((item.cantidad) * 100) / 100;
        if (item.entrega_hoy === undefined || item.entrega_hoy === null) {
            item.entrega_hoy = Math.floor((item.cantidad) * 100) / 100;
        }
        console.log(item);

        const cantFactorVenta = Math.floor(item.cantidad / item.factor);
        const piezasRestantesVenta = Math.round((item.cantidad % item.factor) * 100) / 100;

        let leyenda = '';
        let cantidadT = item.cantidad;

        if (item.cantidad < 1) {
            cantidadT = item.unidadEquivalencia > 0
                ? (item.cantidad / (1 / item.unidadEquivalencia))
                : item.cantidad;

            leyenda =
                '1 ' + item.unidadMedidaNombre +
                ' = ' +
                (1 / item.unidadEquivalencia) +
                ' de ' +
                item.unidad_medida;
        }else {
            cantidadT = item.unidadEquivalencia > 0
                ? (item.cantidad / (1 / item.unidadEquivalencia))
                : item.cantidad;

            leyenda =
                '1 ' + item.unidadMedidaNombre +
                ' = ' +
                (1 / item.unidadEquivalencia) +
                ' de ' +
                item.unidad_medida;
        }

        let nombreuni = '';

        if (item.cantidad<1) {
            nombreuni = item.unidadMedidaNombre;
        } else {
            nombreuni = item.unidad_medida;
        }

        const tr = document.createElement("tr");
        console.log(item.unidad_medida,cantFactorVenta);

        tr.innerHTML = `
            <td>
                <div class="fw-bold" style="font-size: 0.85rem;">${item.nombre}</div>
                <small class="text-body-secondary d-block">
                    ${item.almacen_nombre} | ${item.tipo_precio.toUpperCase()}
                </small>

              
            </td>

            <td class="text-center">
                <div class="fw-bold" style="font-size: 0.9rem;">
                    ${cantFactorVenta >= 1 ? cantFactorVenta : cantidadT.toFixed(3)}
                    ${cantFactorVenta >0 ? item.unidad_reporte : item.unidadMedidaNombre}
                </div>
            

            
               
                    <input type="hidden"
                        class="form-control text-center input-entrega-modal"
                        data-index="${index}"
                        value="0"
                        min="0"
                        max="${item.cantidad}"
                        step="0.01">

                    
               
</td>
           

            <td class="text-end fw-bold">
                $${item.subtotal.toFixed(2)}
            </td>

            
        `;

        tabla.appendChild(tr);

        // YA EXISTE EN EL DOM
       
    });

    window.recalcularTotalModal();

    const modalElement = document.getElementById('modalFinalizarVenta');

    if (modalElement) {
        const myModal = new bootstrap.Modal(modalElement);
        myModal.show();
    }
};

/**
 * 5. RECALCULAR TOTALES DENTRO DEL MODAL
 */
window.recalcularTotalModal = function() {
    let total = 0;
    if (window.carrito) {
        window.carrito.forEach(i => {
            total += parseFloat(i.subtotal || 0);
        });
    }

    const totalDisplay = document.getElementById("totalFinalModal");
    if (totalDisplay) totalDisplay.innerText = total.toFixed(2);

    const inputPago = document.getElementById("monto_pagar");
    if (inputPago) {
        inputPago.value = total.toFixed(2);
        inputPago.dispatchEvent(new Event('input'));
    }
};

/**
 * 6. LISTENER PARA ACTUALIZAR DESGLOSE Y ENTREGA EN TIEMPO REAL
 */document.addEventListener('input', function(e) {
    if (e.target.classList.contains('input-entrega-modal')) {
        const index = e.target.dataset.index;
        const item = window.carrito[index];
        
        // CORRECCIÓN: Usa parseFloat para permitir decimales o cantidades enteras mayores a 1
        let valor = parseFloat(e.target.value);
        
        if (isNaN(valor)) valor = 0;

        // Validar que no entregue más de lo vendido
        if (valor > item.cantidad) {
            valor = item.cantidad;
            e.target.value = valor;
        }
        
        // Guardamos el valor real (ej. 2, 5, 10...)
        item.entrega_hoy = valor;

        // Actualizar el texto informativo (Aquí sí usamos floor solo para mostrar el texto de "bultos")
        const f = Math.floor(valor / item.factor);
        const p = Math.round((valor % item.factor) * 100) / 100;
        
        const elDesglose = document.getElementById(`desglose-entrega-${index}`);
        if (elDesglose) {
            elDesglose.innerHTML = `Entregando: ${f} ${item.unidad_reporte} + ${p}${item.unidad_medida}.`;
        }
    }
});
// document.addEventListener('change', function (e) {

//     if (e.target.matches('select[name^="merma_lote_"]')) {

//         const index = e.target.name.split('_').pop();
//         const item = window.carrito[index];

//         const loteId = e.target.value;

//         if (!loteId) return;

//         const optionSeleccionada = e.target.options[e.target.selectedIndex];
//         const stockLote = parseFloat(optionSeleccionada.dataset.stock || 0);

//         console.log('Índice:', index);
//         console.log('Producto:', item.nombre);
//         console.log('Lote seleccionado:', loteId);
//         console.log('Stock lote:', stockLote);

//         // Guardar lote en carrito
//         item.lote_id = loteId;
       

//         // Validar entrega vs stock del lote
//         if (item.entrega_hoy > stockLote) {
//             Swal.fire(
//                 'Stock insuficiente',
//                 `El lote solo tiene ${stockLote}`,
//                 'warning'
//             );

//             item.entrega_hoy = stockLote;

//             const inputEntrega = document.querySelector(
//                 `.input-entrega-modal[data-index="${index}"]`
//             );

//             if (inputEntrega) {
//                 inputEntrega.value = stockLote;
//             }
//         }
//     }
// });
// async function lotesporPropducto(producto_id, almacen_id, index) {
//     const loteSelect = document.querySelector(
//         `[name="merma_lote_${index}"]`
//     );

//     if (!loteSelect) {
//         console.error(`No existe merma_lote_${index}`);
//         return;
//     }

//     try {
//         const response = await fetch(
//             `/myvet/app/controllers/mermasController.php?action=obtenerLotes&producto_id=${producto_id}&almacen_id=${almacen_id}`
//         );

//         if (!response.ok) {
//             throw new Error('Error HTTP: ' + response.status);
//         }

//         const lotes = await response.json();

//         console.log('Lotes:', lotes);

//         loteSelect.innerHTML = '<option value="">Seleccione lote</option>';

//         if (!Array.isArray(lotes)) {
//             throw new Error('La respuesta no es un array');
//         }

//         lotes.forEach(l => {
//             const option = document.createElement('option');
//             option.value = l.id;
//             option.textContent = `${l.codigo_lote} (Disp: ${l.cantidad_actual})`;
//             option.dataset.stock = l.cantidad_actual;

//             loteSelect.appendChild(option);
//         });

//         loteSelect.disabled = false;

//     } catch (e) {
//         console.error('Error cargando lotes:', e);
//         loteSelect.innerHTML = '<option value="">Error al cargar</option>';
//     }
// }                   
                              
