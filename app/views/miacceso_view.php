<!DOCTYPE html>
<html lang="es" data-bs-theme="auto">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina ?? 'Configuración de Mi Acceso') ?></title>

    <link rel="icon" type="image/png"
        href="/myvet/<?= htmlspecialchars($_SESSION['logo'] ?? 'public/assets/logo.png') ?>">
    <link rel="shortcut icon" href="/myvet/<?= htmlspecialchars($_SESSION['ico'] ?? 'public/assets/logo.ico') ?>"
        type="image/x-icon">

    <?php require_once __DIR__ . '/layout/icono.php'; ?>
    <?php if (function_exists('cargarEstilos')) {
        cargarEstilos();
    } ?>

    <!-- Estilos Frameworks y Complementos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/nano.min.css" />

    <style>
        :root {
            --header-gradient: linear-gradient(135deg, #10b981 0%, #064e3b 100%);
            --panel-gradient: linear-gradient(135deg, #0f172a 0%, #020617 100%);
        }

        [data-bs-theme="dark"] {
            --header-gradient: linear-gradient(135deg, #059669 0%, #022c22 100%);
            --panel-gradient: linear-gradient(135deg, #0a1122 0%, #020617 100%);
        }

        body {
            padding-top: 70px;
            background:
                radial-gradient(1200px 600px at 10% -10%, rgba(16, 185, 129, 0.06), transparent 60%),
                radial-gradient(1000px 500px at 100% 0%, rgba(59, 130, 246, 0.05), transparent 55%),
                var(--bs-body-bg);
        }

        /* Solo 3 clases custom — lo que Bootstrap no tiene */
        .header-grad {
            background: var(--header-gradient);
            position: relative;
            overflow: hidden;
        }

        .header-grad::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(600px 120px at 90% 0%, rgba(255, 255, 255, .15), transparent 70%),
                radial-gradient(400px 100px at 10% 100%, rgba(255, 255, 255, .08), transparent 70%);
        }

        .panel-tema {
            background: var(--panel-gradient);
            border: 1px solid rgba(255, 255, 255, .08);
            box-shadow: 0 30px 60px -30px rgba(0, 0, 0, .7), inset 0 1px 0 rgba(255, 255, 255, .05);
            position: relative;
            overflow: hidden;
        }

        .panel-tema::before {
            content: "";
            position: absolute;
            inset: -2px;
            pointer-events: none;
            background:
                radial-gradient(500px 200px at 80% -10%, rgba(56, 189, 248, .12), transparent 60%),
                radial-gradient(400px 180px at 0% 110%, rgba(16, 185, 129, .10), transparent 60%);
        }

        .glass {
            background: rgba(255, 255, 255, .05);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, .12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08);
        }

        /* Ajuste fino del focus ring a tu verde */
        .form-control:focus,
        .form-select:focus {
            border-color: rgba(16, 185, 129, .6);
            box-shadow: 0 0 0 .2rem rgba(16, 185, 129, .25);
        }
    </style>

<body>

    <?php renderizarLayout($paginaActual); ?>

    <div class="container-xxl p-3 p-md-4">
        <div class="card border-0 rounded-4 shadow-lg overflow-hidden mx-auto" style="max-width: 1100px;">

            <!-- Header -->
            <div class="header-grad text-white p-4">
                <h4 class="fw-bold mb-1 d-flex align-items-center">
                    <i class="bi bi-gear-wide-connected me-2 fs-3"></i> Configuración de Mi Acceso
                </h4>
                <p class="mb-0 opacity-75 small">Administración y parámetros generales de la sucursal actual</p>
            </div>

            <!-- Formulario -->
            <form id="formMiAcceso" enctype="multipart/form-data" class="p-4">
                <input type="hidden" id="almacen_id" name="almacen_id"
                    value="<?= htmlspecialchars($datosAlmacen['id'] ?? $almacen_id) ?>">
                <input type="hidden" id="logo_actual" name="logo_actual"
                    value="<?= htmlspecialchars($datosAlmacen['logo'] ?? '') ?>">
                <input type="hidden" id="ico_actual" name="ico_actual"
                    value="<?= htmlspecialchars($datosAlmacen['ico'] ?? '') ?>">

                <div class="row g-4">

                    <!-- Logo -->
                    <div class="col-md-2">
                        <div class="card h-100 border rounded-4 bg-body-tertiary">
                            <div
                                class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                                <label class="text-uppercase fw-bold text-body-secondary mb-3"
                                    style="font-size:.7rem; letter-spacing:.1em;">Logo Oficial</label>

                                <div class="border border-2 border-secondary-subtle rounded-4 d-flex align-items-center justify-content-center mb-3 overflow-hidden shadow-sm"
                                    style="width:140px;height:140px;border-style:dashed!important;">
                                    <?php if (!empty($datosAlmacen['logo']) && file_exists(__DIR__ . '/../../' . $datosAlmacen['logo'])): ?>
                                        <img src="/myvet/<?= htmlspecialchars($datosAlmacen['logo']) ?>" id="imgLogoPreview"
                                            class="w-100 h-100 object-fit-contain p-1" alt="Logo">
                                    <?php else: ?>
                                        <i class="bi bi-building-gear fs-1 text-secondary opacity-50"
                                            id="iconFallbackLogo"></i>
                                        <img src="" id="imgLogoPreview" class="d-none" alt="Logo">
                                    <?php endif; ?>
                                </div>

                                <input type="file" id="inputLogo" name="logo" class="d-none"
                                    accept="image/png, image/jpeg, image/webp">
                                <button type="button"
                                    class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold"
                                    onclick="$('#inputLogo').click()">
                                    <i class="bi bi-upload me-1"></i> Cambiar Logo
                                </button>
                                <small class="text-body-secondary mt-2" style="font-size:.7rem;">PNG, JPG, WEBP</small>
                            </div>
                        </div>
                    </div>

                    <!-- Favicon -->
                    <div class="col-md-2">
                        <div class="card h-100 border rounded-4 bg-body-tertiary">
                            <div
                                class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                                <label class="text-uppercase fw-bold text-body-secondary mb-3"
                                    style="font-size:.7rem; letter-spacing:.1em;">Favicon (ICO)</label>

                                <div class="border border-2 border-secondary-subtle rounded-4 d-flex align-items-center justify-content-center mb-3 overflow-hidden shadow-sm"
                                    style="width:140px;height:140px;border-style:dashed!important;">
                                    <?php if (!empty($datosAlmacen['ico']) && file_exists(__DIR__ . '/../../' . $datosAlmacen['ico'])): ?>
                                        <img src="/myvet/<?= htmlspecialchars($datosAlmacen['ico']) ?>" id="imgIcoPreview"
                                            class="w-100 h-100 object-fit-contain p-1" alt="ICO">
                                    <?php else: ?>
                                        <i class="bi bi-image-alt fs-1 text-secondary opacity-50" id="iconFallbackIco"></i>
                                        <img src="" id="imgIcoPreview" class="d-none" alt="ICO">
                                    <?php endif; ?>
                                </div>

                                <input type="file" id="inputIco" name="ico" class="d-none" accept="image/x-icon, .ico">
                                <button type="button"
                                    class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold"
                                    onclick="$('#inputIco').click()">
                                    <i class="bi bi-upload me-1"></i> Cambiar Favicon
                                </button>
                                <small class="text-body-secondary mt-2" style="font-size:.7rem;">Exclusivo .ICO</small>
                            </div>
                        </div>
                    </div>

                    <!-- Info principal -->
                    <div class="col-md-8">
                        <div class="card h-100 border rounded-4 bg-body-tertiary">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4 text-body-secondary d-flex align-items-center">
                                    <i class="bi bi-sliders me-2 text-primary"></i> Parámetros del Almacén
                                </h6>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="text-uppercase fw-bold text-body-secondary mb-1"
                                            style="font-size:.7rem; letter-spacing:.1em;">Código Identificador</label>
                                        <input type="text" class="form-control bg-body-tertiary"
                                            value="<?= htmlspecialchars($datosAlmacen['codigo'] ?? 'N/A') ?>" readonly
                                            disabled>
                                    </div>

                                    <div class="col-md-8">
                                        <label class="text-uppercase fw-bold text-body-secondary mb-1"
                                            style="font-size:.7rem; letter-spacing:.1em;">Nombre Comercial <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="nombre" name="nombre" class="form-control"
                                            value="<?= htmlspecialchars($datosAlmacen['nombre'] ?? '') ?>" required
                                            placeholder="Ej: Sucursal Central">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="text-uppercase fw-bold text-body-secondary mb-1"
                                            style="font-size:.7rem; letter-spacing:.1em;">Hora de Cierre</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary border-end-0"><i
                                                    class="bi bi-clock-history"></i></span>
                                            <input type="time" id="hora_cierre_programada" name="hora_cierre_programada"
                                                class="form-control border-start-0"
                                                value="<?= htmlspecialchars($datosAlmacen['hora_cierre_programada'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="text-uppercase fw-bold text-body-secondary mb-1"
                                            style="font-size:.7rem; letter-spacing:.1em;">Plan Contratado</label>
                                        <input type="text"
                                            class="form-control bg-body-tertiary text-uppercase fw-semibold"
                                            value="<?= htmlspecialchars($datosAlmacen['tipo_plan'] ?? 'Estándar') ?>"
                                            readonly disabled>
                                    </div>

                                    <div class="col-12">
                                        <label class="text-uppercase fw-bold text-body-secondary mb-1"
                                            style="font-size:.7rem; letter-spacing:.1em;">Ubicación / Dirección</label>
                                        <textarea id="ubicacion" name="ubicacion" class="form-control" rows="3"
                                            placeholder="Calle, colonia, ciudad y CP..."><?= htmlspecialchars($datosAlmacen['ubicacion'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de tema -->
                    <div class="col-12">
                        <div class="panel-tema rounded-4 p-4 text-white">
                            <div class="row align-items-center g-4 position-relative">

                                <div class="col-12 col-md-8">
                                    <div class="d-inline-flex align-items-center gap-2 mb-2 text-uppercase fw-bold"
                                        style="font-size:.7rem; letter-spacing:.15em; color: rgba(255,255,255,.7);">
                                        <i class="bi bi-palette-fill"></i> Personalización de Tema
                                    </div>

                                    <h5 class="fw-bold mb-2">Color de Barra Superior</h5>
                                    <p class="text-secondary small mb-3" style="max-width:520px;">
                                        Ajusta en tiempo real el color de la barra superior. Selecciona cualquier tono,
                                        modula su opacidad o pega directamente un valor HEX / RGBA.
                                    </p>

                                    <div class="d-flex flex-wrap align-items-center gap-3">
                                        <span class="d-inline-flex align-items-center gap-1 small text-secondary">
                                            <i class="bi bi-check-circle-fill text-success"></i> Vista previa en vivo
                                        </span>
                                        <span class="d-inline-flex align-items-center gap-1 small text-secondary">
                                            <i class="bi bi-check-circle-fill text-success"></i> Soporta transparencias
                                        </span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 text-center text-md-end">
                                    <div class="glass rounded-4 p-3 d-inline-flex flex-column align-items-center w-100">
                                        <span class="small font-monospace mb-2 d-flex align-items-center gap-1"
                                            style="color: rgba(255,255,255,.6);">
                                            <i class="bi bi-eyedropper text-info"></i> Haz clic para cambiar
                                        </span>

                                        <div class="d-flex justify-content-center align-items-center p-2 rounded-circle mb-3"
                                            style="background: rgba(0,0,0,.2); border: 1px solid rgba(255,255,255,.1);">
                                            <div id="color-picker"></div>
                                        </div>

                                        <button type="button" onclick="color()"
                                            class="btn btn-outline-info btn-sm rounded-pill px-3 py-1 fw-semibold w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restaurar predeterminado
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- Guardar -->
                <div class="text-end mt-4 pt-3 border-top">
                    <button type="submit" id="btnGuardar"
                        class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow d-inline-flex align-items-center gap-2">
                        <i class="bi bi-floppy-fill"></i> Guardar Configuración
                    </button>
                </div>

            </form>
        </div>
    </div>
</body>

<!-- Scripts Base -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function color() {
        colorId = 'rgba(255, 255, 255, 0.85)';
    }
    const URL_CONTROLADOR_MIACCESO = '/myvet/app/controllers/miAccesoController.php';
    let colorId = <?= json_encode($_SESSION['sidebar_bg'] ?? '') ?>;
    $(document).ready(function () {
        // Previsualización de la imagen al subirla
        $('#inputLogo').on('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (evt) {
                    $('#imgLogoPreview').attr('src', evt.target.result).removeClass('d-none');
                    $('#iconFallback').addClass('d-none');
                }
                reader.readAsDataURL(file);
            }
        });

        // Envio del Formulario vía AJAX
        $('#formMiAcceso').on('submit', async function (e) {
            e.preventDefault();

            const btn = $('#btnGuardar');
            btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...'
            );

            try {
                const formData = new FormData(this);

                // Agregar la variable de color al FormData
                // Asegúrate de que 'colorId' esté definida globalmente o accesible en este scope
                formData.append('colorId', colorId);
                console.log("Color a enviar:", 'null');

                const resp = await fetch(`${URL_CONTROLADOR_MIACCESO}?action=guardar`, {
                    method: 'POST',
                    body: formData
                });

                // Validar si la respuesta del servidor es correcta HTTP 200-299
                if (!resp.ok) {
                    throw new Error(`Error HTTP: ${resp.status}`);
                }

                const json = await resp.json();

                if (json.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: json.message || 'Información de acceso actualizada exitosamente.',
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    // CORREGIDO: Usar 'Error' en lugar de 'Exception'
                    throw new Error(json.message || 'No se pudieron guardar los cambios.');
                }
            } catch (err) {
                console.error("Error detectado:", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Procesamiento',
                    text: err.message || 'Ocurrió un problema en la conexión.',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                btn.prop('disabled', false).html(
                    '<i class="bi bi-floppy-fill me-1"></i> Guardar Configuración'
                );
            }
        });
    });
</script>

<!-- Pickr JS -->
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>

<script>
    const pickr = Pickr.create({
        el: '#color-picker',
        theme: 'nano',
        default: 'rgba(255, 255, 255, 0.85)',
        components: {
            preview: true,
            opacity: true, // Alpha transparencia
            hue: true, // Barra de color
            interaction: {
                hex: true,
                rgba: true,
                input: true, // Campo editable de texto
                save: true
            }
        },
        i18n: {
            'btn:save': 'Guardar'
        }
    });

    // Actualiza variable CSS en tiempo real
    pickr.on('change', (color) => {
        const rgbaColor = color.toRGBA().toString(2);
        colorId = rgbaColor;
        console.log(colorId);
        document.documentElement.style.setProperty('--sidebar-bg', rgbaColor);
    });
</script>
</body>

</html>