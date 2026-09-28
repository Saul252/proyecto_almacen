// GUARDADO EGRESO COMPLETO ACTUALIZADO
$('#formEgreso').on('submit', function(e) {
    e.preventDefault();

    // 1. EXTRAER LOS DATOS DE LAS FILAS DINÁMICAMENTE
    let itemsParaEnviar = [];
    let hayFaltantesGlobal = 0;

    $('.item-row').each(function() {
        let row = $(this);
        let p_id = row.find('.select-prod').val() || 0;
        let desc = row.find('.desc').val() || ''; 
        let cant = parseFloat(row.find('.cant').val()) || 0;
        
        // --- NUEVOS CAMPOS ---
        let unidad = row.find('.select-unidad').val() || 'PZA'; // Captura TON o PZA
        let factor = parseFloat(row.find('.input-factor').val()) || 1; // Captura el factor manual
        // ---------------------

        let faltante = parseFloat(row.find('.input-faltante').val()) || 0;
        let precio = parseFloat(row.find('.precio_u').val()) || 0;
        let sub = parseFloat(row.find('.subtotal').val()) || 0;
        let dist = row.attr('data-dist') ? JSON.parse(row.attr('data-dist')) : [];

        if(faltante > 0) hayFaltantesGlobal = 1;

        itemsParaEnviar.push({
            producto_id: p_id,
            descripcion: desc,
            cantidad: cant,
            unidad_compra: unidad,      // Enviamos la unidad
            factor_conversion: factor,  // Enviamos el factor manual
            cantidad_faltante: faltante,
            precio: precio,
            subtotal: sub,
            distribucion: dist
        });
    });

    // ... (resto del código de validación y AJAX se mantiene igual)
    // 3. PREPARAR EL ENVÍO
    var fd = new FormData(this); 
    fd.append('items_json', JSON.stringify(itemsParaEnviar)); 
    fd.append('tiene_faltantes', hayFaltantesGlobal);
    
    let totalFinal = itemsParaEnviar.reduce((acc, item) => acc + item.subtotal, 0);
    fd.append('total_final', totalFinal);

    $.ajax({
        url: '/myvet/app/backend/compras/compras.php',
        type: 'POST',
        data: fd,
        contentType: false,
        processData: false,
        dataType: 'json', 
        beforeSend: function() {
            $('#btnGuardarEgreso').prop('disabled', true).text('Procesando...');
        },
        success: function(response) {
            if (response.status === 'success') {
                $('.modal').modal('hide'); 
                Swal.fire({
                    icon: 'success',
                    title: '¡Logrado!',
                    text: response.message
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire("Error", response.message, "error");
                $('#btnGuardarEgreso').prop('disabled', false).text('GUARDAR REGISTRO');
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            Swal.fire("Error Crítico", "El servidor no respondió correctamente.", "error");
            $('#btnGuardarEgreso').prop('disabled', false).text('GUARDAR REGISTRO');
        }
    });
});