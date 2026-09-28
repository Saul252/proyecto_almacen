
    function verDetalle(tipo, id) {
        $('#contenidoDetalle').html('<div class="text-center p-4"><div class="spinner-border text-primary"></div><p>Cargando datos...</p></div>');
        modalVer.show();

        // Asegúrate de que esta ruta sea correcta según tu servidor
        $.get('/myvet/app/backend/compras/obtener_detalle.php', { tipo: tipo, id: id }, function(html) {
            $('#contenidoDetalle').html(html);
        }).fail(function() {
            $('#contenidoDetalle').html('<div class="alert alert-danger">Error al cargar los detalles. Verifique que el archivo obtener_detalle.php existe.</div>');
        });
    }

   