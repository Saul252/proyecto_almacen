
<!DOCTYPE html>
<html lang="es">
<head>
  
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Movimientos | Sistema</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    
      <?php require_once __DIR__ . '/layout/icono.php' ?>
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    <style>
        :root { --glass-bg: rgba(255, 255, 255, 0.9); }
        body { 
           
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            padding-top: 75px; 
        }
        .main-content { padding: 1.5rem; min-height: calc(100vh - 75px); }
        .card-custom { 
             border-radius: 16px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.03); 
            backdrop-filter: blur(10px);
        }
        .table thead th { 
           color: #64748b; font-weight: 700;
            text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em;
            padding: 1.25rem; border-bottom: 2px solid #f1f5f9;
        }
        .badge-mov { 
            min-width: 90px; padding: 6px 12px; border-radius: 8px; 
            font-weight: 700; font-size: 0.65rem; text-transform: uppercase;
        }
        .ruta-pill {
            display: inline-flex; align-items: center; 
            border: 1px solid #e2e8f0; padding: 4px 12px; border-radius: 20px;
            font-size: 0.8rem; color: #475569;
        }
        .ruta-arrow { color: #94a3b8; margin: 0 8px; font-size: 0.9rem; }
        
        
        .btn-recibir {
            background: #10b981;
            color: white;
            
            font-weight: 700;
            font-size: 0.75rem;
            padding: 5px 15px;
            transition: all 0.2s;
        }
        .btn-recibir:hover { background: #059669; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2); }
    </style>
</head>
<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold m-0 card-title-text">Movimientos de Stock</h2>
                    <p class="text-body-secondary small">Consulta de entradas, salidas y traspasos</p>
                </div>
                <div id="loader" class="spinner-border text-primary d-none" role="status"></div>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <form id="formFiltros" class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-body-secondary">PERIODO</label>
                            <select id="selectorPeriodo" class="form-select  border border-subtle">
                                <option value="hoy">Hoy</option>
                                <option value="ayer">Ayer</option>
                                <option value="semana">Últimos 7 días</option>
                                <option value="mes">Este Mes</option>
                                <option value="personalizado">📅 Rango Manual</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-body-secondary">DESDE</label>
                            <input type="date" id="f_inicio" class="form-control input-disabled" disabled>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-body-secondary">HASTA</label>
                            <input type="date" id="f_fin" class="form-control input-disabled" disabled>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-body-secondary">ALMACÉN</label>
                            <select id="filtroAlmacen" class="form-select  border border-subtle" <?= ($almacen_usuario > 0) ? 'disabled' : '' ?>>
                                <?php if($almacen_usuario == 0): ?>
                                    <option value="0">-- Ver Todos --</option>
                                    <?php 
                                    $q_alm = $conexion->query("SELECT id, nombre FROM almacenes WHERE activo = 1 ORDER BY nombre");
                                    while($a = $q_alm->fetch_assoc()): ?>
                                        <option value="<?= $a['id'] ?>"><?= $a['nombre'] ?></option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <?php 
                                    $res_mio = $conexion->query("SELECT nombre FROM almacenes WHERE id = $almacen_usuario LIMIT 1")->fetch_assoc();
                                    ?>
                                    <option value="<?= $almacen_usuario ?>" selected>📍 <?= $res_mio['nombre'] ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-body-secondary">TIPO</label>
                            <select id="filtroTipo" class="form-select  border border-subtle">
                                <option value="">Todos</option>
                                <option value="entrada">Entradas</option>
                                <option value="salida">Salidas</option>
                                <option value="traspaso">Traspasos</option>
                                <option value="ajuste">Ajustes</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <button type="button" id="btnReset" class="btn btn-dark w-100 rounded-pill fw-bold  shadow-sm">
                                <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-custom overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="tablaHistorial" class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Fecha / Hora</th>
                                    <th>Producto</th>
                                    <th class="text-center">Operación</th>
                                    <th class="text-center">Cant.</th>
                                    <th>Trayectoria</th>
                                    <th>Usuarios (Reg/Rec)</th>
                                    <th class="no-export pe-4 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-secondary" style="font-size: 0.85rem;"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php cargarScripts(); ?>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
    $(document).ready(function() {
        const almacenUsuarioSesion = <?= intval($almacen_usuario) ?>;

        const tabla = $('#tablaHistorial').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            dom: '<"d-flex justify-content-between p-3 border-bottom"Bf>rt<"p-3"ip>',
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="bi bi-file-pdf"></i> PDF',
                className: 'btn btn-outline-danger btn-sm px-4 rounded-pill',
                orientation: 'landscape',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
            }],
            order: [[0, 'desc']],
            pageLength: 20
        });

        function formatQty(m) {
            const cant = parseFloat(m.cantidad);
            const factor = parseFloat(m.factor_conversion);
            const unidad =m.unidad_medida;
            if(factor > 1 && cant >= factor) {
                const uReporte = Math.floor(cant / factor);
                const resto = Math.round((cant % factor) * 100) / 100;
                return `<div class="fw-bold card-title-text">${uReporte} ${m.unidad_reporte}</div>` +
                       (resto > 0 ? `<small class="text-body-secondary">+ ${resto} ${m.unidad_medida}</small>` : '');
            }
            return `<div class="fw-bold card-title-text">${cant} <small class="fw-normal">${unidad} </small></div>`;
        }

        function cargarHistorial() {
            const idAlmacenBusqueda = (almacenUsuarioSesion > 0) ? almacenUsuarioSesion : $('#filtroAlmacen').val();
            $('#loader').removeClass('d-none');

            $.ajax({
                url: '/myvet/app/controllers/movimientosController.php',
                data: {
                    ajax: 1,
                    periodo: $('#selectorPeriodo').val(),
                    f_inicio: $('#f_inicio').val(),
                    f_fin: $('#f_fin').val(),
                    tipo: $('#filtroTipo').val(),
                    almacen_id: idAlmacenBusqueda
                },
                dataType: 'json',
                success: function(res) {
                    tabla.clear();
                    if(res.data) {
                        res.data.forEach(m => {
                            const labelOri = (m.almacen_origen_id == idAlmacenBusqueda) ? `<strong>${m.origen}</strong>` : m.origen;
                            const labelDes = (m.almacen_destino_id == idAlmacenBusqueda) ? `<strong>${m.destino}</strong>` : m.destino;

                            let btnAccion = '';
                            // Usamos la bandera es_pendiente calculada por el modelo
                            if (m.es_pendiente && m.almacen_destino_id == almacenUsuarioSesion) {
                                btnAccion = `<button onclick="aceptarTraspaso(${m.id})" class="btn btn-recibir rounded-pill shadow-sm">
                                                <i class="bi bi-box-seam me-1"></i> Recibir
                                             </button>`;
                            } else {
                                btnAccion = `<a href="/myvet/app/backend/movimientos/imprimir_movimiento.php?id=${m.id}" target="_blank" class="btn btn-sm btn-white border shadow-xs">
                                                <i class="bi bi-printer text-danger"></i>
                                             </a>`;
                            }

                            tabla.row.add([
                                `<span class="ps-3 card-title-text fw-bold">${m.fecha_format}</span>`,
                                `<div><div class="card-title-text fw-bold">${m.producto}</div><small class="text-primary">${m.sku}</small></div>`,
                                `<div class="text-center"><span class="badge badge-mov bg-${m.color} bg-opacity-10 text-${m.color} border border-${m.color} border-opacity-25">${m.tipo}</span></div>`,
                                `<div class="text-center">${formatQty(m)}</div>`,
                                `<div><div class="ruta-pill">${labelOri} <i class="bi bi-arrow-right ruta-arrow"></i> ${labelDes}</div></div>`,
                                `<div><small class="d-block"><b>R:</b> ${m.u_reg}</small><small class="text-body-secondary"><b>A:</b> ${m.u_rec}</small></div>`,
                                `<div class="pe-3 text-end">${btnAccion}</div>`
                            ]);
                        });
                    }
                    tabla.draw();
                },
                error: (e) => console.error("Error cargando historial", e),
                complete: () => $('#loader').addClass('d-none')
            });
        }

       window.aceptarTraspaso = function(id) {
    // 1. Confirmación con el mensaje específico para traspasos
    if (!confirm("¿Confirmas que has recibido físicamente esta mercancía? El stock se sumará a este almacén de inmediato.")) return;
    
    // 2. Preparación de FormData (id y acción)
    const formData = new FormData();
    formData.append('id', id);
    formData.append('accion', 'aceptar_traspaso'); // Enviamos la acción para que el archivo sepa qué hacer
    
    // 3. Fetch a la URL exacta que solicitaste
    fetch('/myvet/app/backend/movimientos/procesar_transaccion_rapida.php', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error('Error en la respuesta del servidor');
        return res.json();
    })
    .then(data => {
        // 4. Lógica de éxito idéntica
        if (data.success || data.status === 'success') {
            // Recarga la página para actualizar inventarios visibles
            location.reload();
        } else {
            // Error con mensaje dinámico
            alert("Error: " + (data.message || "No se pudo procesar la recepción"));
        }
    })
    .catch(err => {
        console.error("Error en fetch:", err);
        alert("Error de conexión al servidor.");
    });
}
        $('#selectorPeriodo').on('change', function() {
            const isPerso = $(this).val() === 'personalizado';
            $('#f_inicio, #f_fin').prop('disabled', !isPerso).toggleClass('input-disabled', !isPerso);
            if(!isPerso) cargarHistorial();
        });

        $('#f_inicio, #f_fin, #filtroTipo, #filtroAlmacen').on('change', cargarHistorial);
        
        $('#btnReset').on('click', () => { 
            $('#formFiltros')[0].reset(); 
            $('#f_inicio, #f_fin').prop('disabled', true).addClass('input-disabled');
            cargarHistorial(); 
        });

        cargarHistorial();
    });
    </script>
</body>
</html>