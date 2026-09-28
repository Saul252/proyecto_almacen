function editarProducto(productoId, almacenId) {
    console.log("Iniciando carga de producto:", productoId);

    fetch(`/myvet/app/backend/almacen/obtener_producto_individual.php?id=${productoId}&almacen_id=${almacenId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const p = data.producto;
                
                // Función optimizada para el nuevo modal
                const setVal = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) {
                        if (el.tagName === 'SPAN') el.innerText = val || '';
                        else el.value = val || '';
                    }
                };

                // 1. Identificadores y Títulos
                setVal('edit_id', p.id);
                setVal('edit_almacen_id', almacenId);
                setVal('edit_nombre_titulo', p.nombre);
                setVal('edit_almacen_nombre', p.almacen_nombre);

                // 2. Información General
                setVal('edit_sku', p.sku);
                setVal('edit_nombre', p.nombre);
                setVal('edit_descripcion', p.descripcion);
                
                // --- CATEGORÍA (Usando el nuevo ID único) ---
                const selectCat = document.getElementById('edit_categoria_idx');
                if (selectCat) {
                    const idCat = p.categoria_id ? String(p.categoria_id).trim() : "";
                    selectCat.value = idCat;
                }

                // 3. Datos Fiscales
                setVal('edit_fiscal_clave_prod', p.fiscal_clave_prod);
                setVal('edit_fiscal_clave_unit', p.fiscal_clave_unidad); // Ajustado al ID del modal
                setVal('edit_impuesto_iva', p.impuesto_iva);

                // 4. Precios (Cards)
                setVal('edit_p_min', p.precio_minorista);
                setVal('edit_p_may', p.precio_mayorista);
                setVal('edit_p_dist', p.precio_distribuidor);

                // 5. Unidades y Stocks
                setVal('edit_unidad_reporte', p.unidad_reporte);
                setVal('edit_factor_conversion', p.factor_conversion);
                setVal('edit_unidad_medida', p.unidad_medida);
                setVal('edit_stock', p.stock);
                setVal('edit_s_min', p.stock_minimo);

                // 6. Lanzamiento del Modal
                const modalEl = document.getElementById('modalEditarProducto');
                const myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                myModal.show();

                /**
                 * REFUERZO DE SELECCIÓN:
                 * Como el modal tiene animaciones, forzamos la selección de la categoría
                 * justo cuando termina de mostrarse (shown.bs.modal).
                 */
                modalEl.addEventListener('shown.bs.modal', () => {
                    if (selectCat && p.categoria_id) {
                        const target = String(p.categoria_id).trim();
                        selectCat.value = target;
                        
                        // Debug por si necesitas ver si hizo match
                        console.log("Categoría fijada en:", selectCat.value);
                    }
                }, { once: true });

            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(err => {
            console.error("Error en fetch:", err);
            Swal.fire('Error', 'No se pudieron recuperar los datos del servidor', 'error');
        });
}