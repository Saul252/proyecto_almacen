<?php
require_once __DIR__ . '/../../includes/auth.php';
protegerPagina('Configuracion');
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/conexion.php';

$paginaActual = 'Configuracion';

// 1. Obtener roles de la BD
$resRoles = $conexion->query("SELECT * FROM roles ORDER BY id ASC");
$roles = $resRoles->fetch_all(MYSQLI_ASSOC);

// 2. CARGAR MÓDULOS DESDE LA BASE DE DATOS
// Consultamos la tabla que creamos para llenar el array dinámicamente
$resModulos = $conexion->query("SELECT * FROM modulos WHERE activo = 1 ORDER BY orden ASC");
$modulosData = $resModulos->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Accesos | Sistema</title>
      <link rel="icon" type="image/png" href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">

    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --sidebar-width: 0px; --navbar-height: 20px; }
      
        .main-content {  padding: 40px; margin-top: var(--navbar-height); transition: all 0.3s; }
        .card-custom {  border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; overflow: hidden; }
        .table thead th { background-color: #f8fafc; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #64748b; padding: 15px 20px; border-bottom: 2px solid #edf2f7; }
        .module-name { font-weight: 700; color: #1e293b; font-size: 0.9rem; }
        .role-header { font-weight: 800; color: #4338ca; }
        .form-check-input:checked { background-color: #4338ca; border-color: #4338ca; }
        .permiso-row:hover { background-color: #f1f5f9 !important; }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; } }
    </style>
</head>
<body>

    <?php renderSidebar($paginaActual); ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0 text-dark">Matriz de Seguridad</h2>
                <p class="text-body-secondary">Configura qué módulos puede ver cada rol de usuario</p>
            </div>
            <button type="button" id="btnGuardarPermisos" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-shield-check me-2"></i> Guardar Cambios
            </button>
        </div>

        <div class="card card-custom">
            <div class="card-body p-0">
                <form id="formPermisos">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Módulo / Sección</th>
                                    <?php foreach($roles as $r): ?>
                                        <th class="text-center role-header"><?= htmlspecialchars($r['nombre']) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($modulosData as $m): ?>
                                <tr class="permiso-row">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded-3 me-3 text-primary">
                                                <i class="<?= $m['icono'] ?>"></i>
                                            </div>
                                            <span class="module-name"><?= $m['nombre'] ?></span>
                                        </div>
                                    </td>
                                    <?php foreach($roles as $r): 
                                        // Comparamos contra el identificador (ej: 'corteCaja')
                                        $sqlCheck = "SELECT id FROM permisos_roles WHERE rol_id = ? AND modulo = ?";
                                        $stmt = $conexion->prepare($sqlCheck);
                                        $stmt->bind_param("is", $r['id'], $m['identificador']);
                                        $stmt->execute();
                                        $hasPerm = ($stmt->get_result()->num_rows > 0) ? 'checked' : '';
                                    ?>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                       name="permisos[<?= $r['id'] ?>][]" 
                                                       value="<?= $m['identificador'] ?>" <?= $hasPerm ?>>
                                            </div>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
        </main>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ... (el JS que ya tienes funciona perfectamente aquí)
    </script>
</body>
</html>


 <script>
    $(document).ready(function() {
        $('#btnGuardarPermisos').on('click', function() {
            
            Swal.fire({
                title: '¿Confirmar cambios?',
                text: "Se actualizarán los privilegios de acceso para los roles seleccionados.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4338ca',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // Loader de procesamiento
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Guardando configuración de seguridad',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    const form = document.getElementById('formPermisos');
                    const formData = new FormData(form);

                    $.ajax({
                        url: '/myvet/app/backend/permisos/guardar_permisos.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: res.message,
                                    confirmButtonColor: '#4338ca'
                                }).then(() => {
                                    location.reload(); 
                                });
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            Swal.fire('Error', 'No se pudo conectar con el servidor para guardar los cambios.', 'error');
                        }
                    });
                }
            });
        });
    });
    </script>





