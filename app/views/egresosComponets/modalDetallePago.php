<div class="modal fade" style="z-index:1999" id="modalDetallePago" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content  shadow-lg" style="border-radius:16px; overflow:hidden;">

            <!-- HEADER -->
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-receipt-cutoff me-2"></i>
                    Detalle de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <!-- INFO GENERAL -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-body-secondary">Proveedor</small>
                        <div id="dp_proveedor" class="fw-bold"></div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-body-secondary">Almacén</small>
                        <div id="dp_almacen" class="fw-bold"></div>
                    </div>

                    <div class="col-md-6 mt-2">
                        <small class="text-body-secondary">Usuario</small>
                        <div id="dp_usuario" class="fw-bold"></div>
                    </div>

                    <div class="col-md-6 mt-2">
                        <small class="text-body-secondary">Fecha</small>
                        <div id="dp_fecha" class="fw-bold"></div>
                    </div>
                </div>

                <!-- PAGO -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-body-secondary">Monto Pagado</small>
                        <div id="dp_monto" class="fw-bold text-success fs-5"></div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-body-secondary">Método de Pago</small>
                        <div id="dp_metodo" class="fw-bold"></div>
                    </div>
                </div>

                <!-- PRODUCTO / EXCEDENTE -->
                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-body-secondary">Producto</small>
                            <div id="dp_producto" class="fw-bold"></div>
                        </div>

                        <div class="col-md-3">
                            <small class="text-body-secondary">Excedente</small>
                            <div id="dp_excedente" class="fw-bold text-danger"></div>
                        </div>

                        <div class="col-md-3">
                            <small class="text-body-secondary">Precio Unitario</small>
                            <div id="dp_precio" class="fw-bold"></div>
                        </div>
                    </div>
                </div>

                <!-- OBSERVACIONES -->
                <div>
                    <small class="text-body-secondary">Observaciones</small>
                    <div id="dp_obs" class="p-2 border rounded bg-light"></div>
                </div>

            </div>

        </div>
    </div>
</div>
<script>
    function abrirDetallePago(id) {
    $.ajax({
        url: '/myvet/app/controllers/egresosController.php',
        method: 'GET',
        data: { action: 'obtenerDetallePago', id: id },
        dataType: 'json',
        success: function(res) {

            if (!res.success) {
                alert(res.message);
                return;
            }

            const d = res.data; // 🔥 CORRECTO

            console.log(d);

            $('#dp_proveedor').text(d.proveedorNombre);
            $('#dp_almacen').text(d.almacen_nombre);
            $('#dp_usuario').text(d.usuario_nombre);
            $('#dp_fecha').text(d.fecha);

            $('#dp_monto').text('$ ' + parseFloat(d.monto_pagado || 0).toFixed(2));
            $('#dp_metodo').text(d.metodo_pago);

            $('#dp_producto').text((d.producto_nombre || '') + ' (' + (d.unidad_medida || '') + ')');
            $('#dp_excedente').text(d.cantidad_excedente || 0);
            $('#dp_precio').text('$ ' + parseFloat(d.precio_unitario || 0).toFixed(2));

            $('#dp_obs').text(d.observaciones || 'Sin observaciones');

            $('#modalDetallePago').modal('show');
        },
        error: function() {
            alert('Error al obtener detalle');
        }
    });
}
</script>