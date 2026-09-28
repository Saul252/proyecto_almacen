window.procesarVenta = function() {
    // 1. Validar carrito
    if (!window.carrito || window.carrito.length === 0) {
        Swal.fire('Carrito vacío', 'Debes agregar al menos un producto.', 'warning');
        return;
    }

    // 2. Validar Cliente
    const idCliente = document.getElementById('selectCliente').value;
    if (!idCliente) {
        Swal.fire('Falta Cliente', 'Por favor selecciona un cliente para la venta.', 'warning');
        return;
    }

    // 3. Capturar valores de pago y totales
    const totalTexto = document.getElementById('totalFinalModal').innerText.replace(/[$,]/g, '');
    const totalVenta = parseFloat(totalTexto) || 0;
    const montoPagado = parseFloat(document.getElementById('monto_pagar').value) || 0;
    const metodoPago = document.getElementById('metodo_pago').value;
    const observaciones = document.getElementById('obsVenta').value;

    // 4. Confirmación visual con estética limpia
    Swal.fire({
        title: '¿Finalizar Venta?',
        html: `Total: <b>$${totalVenta.toFixed(2)}</b><br>Recibido: <b>$${montoPagado.toFixed(2)}</b>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007aff', // Azul iOS
        cancelButtonColor: '#8e8e93',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar',
        customClass: { popup: 'rounded-4' }
    }).then((result) => {
        if (result.isConfirmed) {
            
            const btnFinalizar = document.querySelector('#modalFinalizarVenta .btn-primary');
            if(btnFinalizar) btnFinalizar.disabled = true;
            
            Swal.fire({
                title: 'Procesando...',
                text: 'Validando saldos y actualizando stock...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // 5. Mapeo del carrito (Manteniendo tu lógica de entrega parcial)
            const carritoFinal = window.carrito.map((item, index) => {
                const inputEntrega = document.querySelector(`.input-entrega-modal[data-index="${index}"]`);
                let entregado = item.entrega_hoy; 
                if (inputEntrega) {
                    entregado = parseFloat(inputEntrega.value);
                }

                return {
                    producto_id: parseInt(item.producto_id),
                    almacen_id: parseInt(item.almacen_id),
                    cantidad: parseFloat(item.cantidad),
                    entrega_hoy: isNaN(entregado) ? 0 : entregado, 
                    precio_unitario: parseFloat(item.precio_unitario),
                    subtotal: parseFloat(item.subtotal),
                    tipo_precio: item.tipo_precio
                };
            });

            // 6. Preparar objeto de envío (Añadimos 'accion' para el controlador)
            const datos = {
                accion: 'guardar_venta', // <--- IMPORTANTE para tu controlador
                id_cliente: parseInt(idCliente),
                descuento: 0,
                monto_pagado: montoPagado,
                metodo_pago: metodoPago,
                total_venta: totalVenta,
                observaciones: observaciones,
                carrito: carritoFinal
            };

            // 7. Envío al Controlador Central
            fetch('/myvet/app/controllers/ventasController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            })
            .then(res => {
                if (!res.ok) throw new Error('Error en la respuesta del servidor');
                return res.json();
            })
            .then(res => {
                if (res.status === 'success') {
                    console.log("entro al controller")
                    // Si el monto pagado fue menor al total, mostramos aviso de deuda en el éxito
                    const tieneDeuda = montoPagado < totalVenta;
                    const iconoFinal = res.total_entregado >= res.total_pedido ? 'success' : 'warning';
                    
                    let htmlExtra = `<p class="mb-1">Folio generado: <b>${res.folio}</b></p>`;
                    if(tieneDeuda) {
                        htmlExtra += `<div class="badge rounded-pill bg-danger-subtle text-danger border border-danger-child mb-2 px-3 py-2" style="font-size:0.75rem">
                                        ⚠️ Saldo pendiente registrado en cuenta
                                      </div>`;
                    }

                    Swal.fire({
                        title: res.total_entregado >= res.total_pedido ? '¡Venta Exitosa!' : 'Entrega Parcial',
                        html: `
                            <div class="alert bg-body-tertiary text-body  small shadow-sm text-start py-2">
                                ${res.message || 'Operación realizada correctamente.'}
                            </div>
                            ${htmlExtra}
                            <p class="text-body-secondary small">¿Deseas imprimir el ticket?</p>
                        `,
                        icon: iconoFinal,
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: '<i class="bi bi-receipt"></i> Con Precios',
                        denyButtonText: '<i class="bi bi-receipt"></i> Sin Precios',
                        cancelButtonText: 'Cerrar',
                        confirmButtonColor: '#198754',
                        denyButtonColor: '#0dcaf0',
                        customClass: { popup: 'rounded-4' }
                    }).then((result) => {
                        let url = '';
                        if (result.isConfirmed) {
                            url = `/myvet/app/backend/ventas/ticket_venta.php?id=${res.id_venta}`;
                        } else if (result.isDenied) {
                            url = `/myvet/app/backend/ventas/ticket_sin_precio.php?id=${res.id_venta}`;
                        }

                        if (url !== '') {
                            window.open(url, '_blank');
                        }
                        location.reload(); 
                    });
                } else {
                    Swal.fire('Error al procesar', res.message || 'Error desconocido', 'error');
                    if(btnFinalizar) btnFinalizar.disabled = false;
                }
            })
            .catch(err => {
                console.error("Error en Fetch:", err);
                Swal.fire('Error Crítico', 'No se pudo conectar con el controlador.', 'error');
                if(btnFinalizar) btnFinalizar.disabled = false;
            });
        }
    });
}