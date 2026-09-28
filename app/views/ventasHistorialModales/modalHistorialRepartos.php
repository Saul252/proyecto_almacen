 <div class="modal fade" id="modalSimulacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content  shadow-lg">
                <div class="modal-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="modal-title m-0"><i class="bi bi-file-earmark-ruled me-2"></i>Orden de Despacho</h5>
                    <div>
                        <button type="button" class="btn btn-outline-light btn-sm btn-print-action me-2" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Imprimir
                        </button>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body p-4" id="documentoPatio"></div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnConfirmarFinal" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Generar Entrega
                    </button>
                </div>
            </div>
        </div>
    </div>
<script>
window.verDetalleDespachoAlmacen = function(idVenta) {

    $('#loader').removeClass('d-none');

    $.getJSON('/myvet/app/controllers/entregasController.php', {
        ajax: 'obtenerAuditoriaVenta',
        id_venta: idVenta
    }, function(res) {

        if (res.success) {

            const r = res.data;

            let htmlProductos = r.productos.map((p, index) => {
                let totalEntregado=0;

                let filasLotes =`
                   <style>

@media print {
#btnConfirmarFinal {
display:none !important}



    body * {
        visibility: hidden !important;
    }

    #modalSimulacion,
    #modalSimulacion * {
        visibility: visible !important;
    }

    #modalSimulacion {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: auto !important;
        background: white !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }

    #modalSimulacion .modal-dialog {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #modalSimulacion .modal-content {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        background: #fff !important;
    }

    #modalSimulacion .modal-header,
    #modalSimulacion .modal-footer,
    #btnImprimirModal,
    #btnConfirmarFinal,
    .btn-close {
        display: none !important;
    }

    #documentoPatio {
        padding: 20px !important;
        font-size: 12px !important;
        color: #000 !important;
    }

    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    table th,
    table td {
        border: 1px solid #000 !important;
        padding: 6px !important;
    }
        .bi-check-circle{
        display:hidden;
                                }

}
</style> ;`

                if (p.detalle_financiero) {

                    p.detalle_financiero.split('___').forEach(reg => {

                        const c = reg.split('|');
                      



                        if (c.length >= 2) {
                            document.getElementById('btnConfirmarFinal').style.display = 'none !important';
                             totalEntregado=totalEntregado+ Number(c[1])??0;
                            filasLotes += `
                                      
                                <tr>
                                
                                    <td class="py-1 ps-2">
                                        ${c[0]}
                                    </td>

                                    <td class="text-end py-1 pe-2 fw-semibold">
                                      ${(c[1]/p.factor_conversion)>=1?(c[1]/p.factor_conversion):c[1]} ${(c[1]/p.factor_conversion)>=1?p.unidad_reporte:p.unidad_medida || ''}
                                     
                                        
                                    </td>
                                    <td class="text-end py-1 pe-2 fw-semibold">
                                     ${c[4]}
                                        
                                    </td>
                                </tr>
                            `;
                        }
                    });
                }

                return `
                    <div class="mb-3 pb-2"
                        style="
                            border-bottom:1px dashed #999;
                        ">

                        <!-- PRODUCTO -->
                        <div class="d-flex justify-content-between align-items-start mb-2">

                            <div>
                                <div class="fw-bold text-uppercase"
                                    style="font-size:14px;">
                                    ${index + 1}. ${p.producto}
                                </div>

                                <div class="text-body-secondary"
                                    style="font-size:11px;">
                                    SKU: ${p.sku}
                                </div>
                            </div>

                            <div class="text-end">

                                <div class="fw-bold"
                                    style="
                                        font-size:15px;
                                        line-height:1;
                                    ">
                                  Venta Total:   ${(p.cantidad_total/p.factor_conversion)>1?(p.cantidad_total/p.factor_conversion):p.cantidad_total} ${(p.cantidad_total/p.factor_conversion)>1?p.unidad_reporte:p.unidad_medida || ''}
                                     
                                   
                                </div>


                            </div>

                        </div>

                        <!-- LOTES -->
                        <table class="table table-sm table-borderless mb-0"
                            style="font-size:12px;">

                            <thead>
                                <tr style="
                                    border-bottom:1px solid #ddd;
                                ">
                                    <th class="fw-semibold text-body-secondary">
                                        LOTE / UBICACIÓN
                                    </th>

                                    <th class="fw-semibold text-body-secondary text-end">
                                        CANTIDAD
                                    </th>
                                     <th class="fw-semibold text-body-secondary text-end">
                                        FECHA DE SALIDA
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                ${filasLotes}
                                <td></td>
                                <td>Total Entregado:</td>
                                <td> ${(totalEntregado/p.factor_conversion)>=1?(totalEntregado/p.factor_conversion)+' '+ p.unidad_reporte:totalEntregado+ ' '+ p.unidad_medida}</td>
                            </tbody>


                        </table>

                    </div>
                `;

            }).join('');

            let htmlFinal = `

                <div style="
                    background:#fff;
                    color:#000;
                    padding:20px;
                    font-family:'Segoe UI',sans-serif;
                    border:1px solid #ccc;
                ">

                    <!-- HEADER -->
                    <div class="text-center mb-4">

                        <div style="
                            font-size:22px;
                            font-weight:700;
                            letter-spacing:1px;
                        ">
                            ORDEN DE DESPACHO
                        </div>

                        <div style="
                            font-size:12px;
                            color:#666;
                        ">
                            REPORTE DE SALIDA DE ALMACÉN
                        </div>

                    </div>

                    <!-- INFO -->
                    <table class="w-100 mb-4"
                        style="font-size:12px;">

                        <tr>
                            <td>
                                <strong>Folio:</strong>
                                ${r.productos[0].folio || 'N/A'}
                            </td>

                            <td class="text-end">
                                <strong>Fecha:</strong>
                                ${new Date().toLocaleDateString()}
                            </td>
                        </tr>

                    </table>

                    <!-- PRODUCTOS -->
                    ${htmlProductos}

                    <!-- FIRMAS -->
                    <div class="row mt-5 text-center">

                        <div class="col-6">

                            <div style="
                                border-top:1px solid #000;
                                width:80%;
                                margin:auto;
                                padding-top:4px;
                                font-size:11px;
                            ">
                                ALMACENISTA
                            </div>

                        </div>

                        <div class="col-6">

                            <div style="
                                border-top:1px solid #000;
                                width:80%;
                                margin:auto;
                                padding-top:4px;
                                font-size:11px;
                            ">
                                CHOFER / CLIENTE
                            </div>

                        </div>

                    </div>

                </div>
            `;

            $('#documentoPatio').html(htmlFinal);
  $('#btnConfirmarFinal').prop('disabled', true).addClass('d-none');
            $('#modalSimulacion').modal('show');

        } else {

            Swal.fire('Atención', res.message, 'warning');

        }

    }).always(() => {

        $('#loader').addClass('d-none');

    });
};
</script>
