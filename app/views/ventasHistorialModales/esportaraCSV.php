<script>
    async function exportarVentasCSV() {
        $('#loader').removeClass('d-none');

        // Mismo objeto de parámetros que getVentas() para mantener los filtros aplicados
        const params = new URLSearchParams({
            action: 'listar',
            f_search: $('#f_search').val(),
            f_rango: $('#f_rango').val(),
            f_inicio: $('#f_ini').val(),
            f_fin: $('#f_fin').val(),
            f_almacen: $('#f_almacen').val(),
            f_status: $('#f_status').val(),
            f_pago: $('#f_pago').val(),
            f_vendedor: $('#select-usuarios').val() ?? '',
            f_factura: $('#estado_factura').val() ?? ''
        });

        try {
            const res = await fetch(`${URL_CONTROLLER}?${params.toString()}`);
            const data = await res.json();

            if (!data || data.length === 0) {
                alert("No hay registros de ventas para exportar con los filtros seleccionados.");
                return;
            }

            // Encabezados del CSV (1 fila por venta)
            const headers = [
                "ID Venta",
                "Fecha",
                "Folio",
                "Almacén",
                "Vendedor",
                "Cliente",
                "Monto Total ($)",
                "Monto Pagado ($)",
                "Saldo Pendiente ($)",
                "Estado Cobro",
                "Estado Entrega",
                "Estado General",
                "Factura"
            ];

            // Construir filas sanitizando comas y comillas para compatibilidad CSV
            const rows = data.map(v => {
                let total = 0;
                let pagado = 0;

                if (v.estado_general !== 'cancelada') {
                    total = parseFloat(v.total) || 0;
                    pagado = parseFloat(v.pagado) || 0;
                }

                let saldo = total - pagado;
                let estadoCobro = v.estado_general === 'cancelada'
                    ? 'Cancelado'
                    : (saldo <= 0 ? 'Pagado' : `Debe ($${saldo.toFixed(2)})`);

                // Función auxiliar para sanitizar cadenas en formato CSV
                const escapeCSV = (str) => {
                    if (str === null || str === undefined) return '""';
                    let val = String(str).replace(/"/g, '""'); // Escapar comillas dobles
                    return `"${val}"`;
                };

                return [
                    escapeCSV(v.id),
                    escapeCSV(v.fecha),
                    escapeCSV(v.folio),
                    escapeCSV(v.almacen_nombre),
                    escapeCSV(v.vendedor),
                    escapeCSV(v.cliente),
                    total.toFixed(2),
                    pagado.toFixed(2),
                    saldo.toFixed(2),
                    escapeCSV(estadoCobro),
                    escapeCSV(v.estado_entrega ? v.estado_entrega.toUpperCase() : 'N/A'),
                    escapeCSV(v.estado_general ? v.estado_general.toUpperCase() : 'N/A'),
                    escapeCSV(v.factura || 'Sin Factura')
                ].join(',');
            });

            // Ensamblar contenido del archivo CSV con BOM de UTF-8
            const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\n');

            // Generar Blob y descargar
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');

            // Formatear nombre con la fecha actual de descarga
            const hoy = new Date().toISOString().split('T')[0];
            link.setAttribute('href', url);
            link.setAttribute('download', `Reporte_Ventas_MYVET_${hoy}.csv`);
            document.body.appendChild(link);

            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);

        } catch (e) {
            console.error("Error al exportar ventas a CSV:", e);
            alert("Ocurrió un error al generar la exportación.");
        } finally {
            $('#loader').addClass('d-none');
        }
    }
</script>