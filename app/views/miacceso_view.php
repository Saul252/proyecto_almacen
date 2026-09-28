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
    <?php if (function_exists('cargarEstilos')) { cargarEstilos(); } ?>

    <!-- Estilos Frameworks y Complementos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/nano.min.css" />

    <style>
    :root {
        --bg-main: #f8fafc;
        --card-bg: #ffffff;
        --card-shadow: rgba(15, 23, 42, 0.08);
        --header-gradient: linear-gradient(135deg, #09b009 0%, #0f2a13 100%);
        --filter-box-bg: #f8fafc;
        --filter-box-border: #e2e8f0;
        --form-label-color: #64748b;
    }

    [data-bs-theme="dark"] {
        --bg-main: #0f172a;
        --card-bg: #1e293b;
        --card-shadow: rgba(0, 0, 0, 0.35);
        --header-gradient: linear-gradient(135deg, #059669 0%, #022c22 100%);
        --filter-box-bg: #0f172a;
        --filter-box-border: #334155;
        --form-label-color: #94a3b8;
    }

    body {
        padding-top: 70px;
        background-color: var(--bg-main);
        transition: background-color 0.3s ease;
    }

    .main-content {
        background-color: var(--bg-main) !important;
    }

    .main-card {
        background: var(--card-bg);
        border-radius: 20px;
        box-shadow: 0 10px 30px var(--card-shadow);
        overflow: hidden;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .page-header-gradient {
        background: var(--header-gradient);
        color: #ffffff;
        padding: 1.5rem 2rem;
    }

    .card-custom-box {
        background-color: var(--filter-box-bg);
        border: 1px solid var(--filter-box-border);
        border-radius: 18px;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .form-label-custom {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--form-label-color);
        text-transform: uppercase;
    }

    .logo-preview-box {
        width: 140px;
        height: 140px;
        border: 2px dashed var(--filter-box-border);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: var(--card-bg);
        position: relative;
    }

    .logo-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 5px;
    }
    </style>
</head>

<body>

    <?php renderizarLayout($paginaActual); ?>
    <!-- Pickr CSS (Tema Nano - Estilo VS Code) -->

    <!-- Contenedor del selector -->


    <div class="main-content p-3 p-md-4">
        <div class="main-card max-w-5xl mx-auto">

            <!-- Encabezado -->
            <div class="page-header-gradient d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1 text-white d-flex align-items-center">
                        <i class="bi bi-gear-wide-connected me-2 fs-3"></i> Configuración de Mi Acceso
                    </h4>
                    <p class="mb-0 opacity-75 small">Administración y parámetros generales de la sucursal actual</p>
                </div>
            </div>

            <!-- Formulario de Edición -->
            <form id="formMiAcceso" enctype="multipart/form-data" class="p-4">
                <input type="hidden" id="almacen_id" name="almacen_id"
                    value="<?= htmlspecialchars($datosAlmacen['id'] ?? $almacen_id) ?>">
                <input type="hidden" id="logo_actual" name="logo_actual"
                    value="<?= htmlspecialchars($datosAlmacen['logo'] ?? '') ?>">
                <input type="hidden" id="ico_actual" name="ico_actual"
                    value="<?= htmlspecialchars($datosAlmacen['ico'] ?? '') ?>">

                <div class="row g-4">
                    <!-- Columna Izquierda: Logo -->
                    <!-- Bloque 1: Logo Oficial (PNG, JPG, WEBP) -->
                    <div class="col-md-2 col-lg-2">
                        <div
                            class="card-custom-box p-3 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                            <label class="form-label-custom mb-3 d-block">Logo Oficial</label>

                            <div class="logo-preview-box mb-3 shadow-sm" id="boxPreviewLogo">
                                <?php if (!empty($datosAlmacen['logo']) && file_exists(__DIR__ . '/../../' . $datosAlmacen['logo'])): ?>
                                <img src="/myvet/<?= htmlspecialchars($datosAlmacen['logo']) ?>" id="imgLogoPreview"
                                    alt="Logo">
                                <?php else: ?>
                                <i class="bi bi-building-gear fs-1 text-secondary opacity-50" id="iconFallbackLogo"></i>
                                <img src="" id="imgLogoPreview" class="d-none" alt="Logo">
                                <?php endif; ?>
                            </div>

                            <input type="file" id="inputLogo" name="logo" class="d-none"
                                accept="image/png, image/jpeg, image/webp">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold"
                                onclick="$('#inputLogo').click()">
                                <i class="bi bi-upload me-1"></i> Cambiar Logo
                            </button>
                            <small class="text-body-secondary mt-2 d-block" style="font-size: 0.7rem;">Formatos: PNG,
                                JPG, WEBP</small>
                        </div>
                    </div>

                    <!-- Bloque 2: Favicon / ICO Oficial (.ICO) -->
                    <div class="col-md-2 col-lg-2">
                        <div
                            class="card-custom-box p-3 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                            <label class="form-label-custom mb-3 d-block">Favicon (ICO)</label>

                            <div class="logo-preview-box mb-3 shadow-sm" id="boxPreviewIco">
                                <?php if (!empty($datosAlmacen['ico']) && file_exists(__DIR__ . '/../../' . $datosAlmacen['ico'])): ?>
                                <img src="/myvet/<?= htmlspecialchars($datosAlmacen['ico']) ?>" id="imgIcoPreview"
                                    alt="ICO">
                                <?php else: ?>
                                <i class="bi bi-image-alt fs-1 text-secondary opacity-50" id="iconFallbackIco"></i>
                                <img src="" id="imgIcoPreview" class="d-none" alt="ICO">
                                <?php endif; ?>
                            </div>

                            <input type="file" id="inputIco" name="ico" class="d-none" accept="image/x-icon, .ico">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold"
                                onclick="$('#inputIco').click()">
                                <i class="bi bi-upload me-1"></i> Cambiar Favicon
                            </button>
                            <small class="text-body-secondary mt-2 d-block" style="font-size: 0.7rem;">Formatos:
                                Exclusivo .ICO</small>
                        </div>
                    </div>

                    <!-- Columna Derecha: Información Principal -->
                    <div class="col-md-8 col-lg-8">
                        <div class="card-custom-box p-4 h-100">
                            <h6 class="fw-bold mb-4 text-body-secondary d-flex align-items-center">
                                <i class="bi bi-sliders me-2 text-primary"></i> Parámetros del Almacén
                            </h6>

                            <div class="row g-3">
                                <!-- Código de Almacén (Solo lectura) -->
                                <div class="col-md-4">
                                    <label class="form-label-custom mb-1">Código Identificador</label>
                                    <input type="text" class="form-control rounded-3 shadow-sm bg-body-tertiary"
                                        value="<?= htmlspecialchars($datosAlmacen['codigo'] ?? 'N/A') ?>" readonly
                                        disabled>
                                </div>

                                <!-- Nombre del Almacén -->
                                <div class="col-md-8">
                                    <label class="form-label-custom mb-1">Nombre Comercial / Sucursal <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="nombre" name="nombre"
                                        class="form-control rounded-3 shadow-sm"
                                        value="<?= htmlspecialchars($datosAlmacen['nombre'] ?? '') ?>" required
                                        placeholder="Ej: Sucursal Central">
                                </div>

                                <!-- Hora Cierre Programada -->
                                <div class="col-md-6">
                                    <label class="form-label-custom mb-1">Hora de Cierre Programada</label>
                                    <div class="input-group shadow-sm rounded-3">
                                        <span class="input-group-text bg-body-tertiary border-end-0"><i
                                                class="bi bi-clock-history"></i></span>
                                        <input type="time" id="hora_cierre_programada" name="hora_cierre_programada"
                                            class="form-control border-start-0"
                                            value="<?= htmlspecialchars($datosAlmacen['hora_cierre_programada'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- Tipo de Plan / Estado (Informativos) -->
                                <div class="col-md-6">
                                    <label class="form-label-custom mb-1">Plan Contratado</label>
                                    <input type="text"
                                        class="form-control rounded-3 shadow-sm bg-body-tertiary text-uppercase fw-semibold"
                                        value="<?= htmlspecialchars($datosAlmacen['tipo_plan'] ?? 'Estándar') ?>"
                                        readonly disabled>
                                </div>

                                <!-- Ubicación / Dirección -->
                                <div class="col-12 mb-2">
                                    <label class="form-label-custom mb-1">Ubicación / Dirección Físicas</label>
                                    <textarea id="ubicacion" name="ubicacion" class="form-control rounded-3 shadow-sm"
                                        rows="3"
                                        placeholder="Ingresa la calle, colonia, ciudad y código postal..."><?= htmlspecialchars($datosAlmacen['ubicacion'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="col-12 card border-0 rounded-4 p-4 text-white shadow-lg" style="background: linear-gradient(135deg, rgba(0, 0, 0, 1), rgba(2, 2, 2, 1)); 
            backdrop-filter: blur(25px); 
            ">

                                <div class="row align-items-center g-4">
                                    <!-- Columna Izquierda: Información -->
                                    <div class="col-12 col-md-8">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="">
                                                <i class="bi bi-palette-fill me-1"></i> Personalización de Tema
                                            </span>
                                        </div>

                                        <h5 class="fw-bold mb-2 text-white">Color de Barra superior </h5>

                                        <p class="text-secondary small mb-3">
                                            Esta herramienta te permite personalizar en tiempo real la tonalidad y
                                            transparencia del menú . Selecciona cualquier color, ajusta su opacidad o
                                            pega directamente una clave Hexadecimal / RGBA para adaptar el sistema a tu
                                            gusto.
                                        </p>

                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center gap-1 text-secondary fs-7">
                                                <i class="bi bi-check-circle-fill text-success"></i> Vista previa en
                                                vivo
                                            </div>
                                            <div class="d-flex align-items-center gap-1 text-secondary fs-7">
                                                <i class="bi bi-check-circle-fill text-success"></i> Soporta
                                                transparencias
                                            </div>
                                        </div>
                                    </div>

                                <!-- Columna Derecha: Selector de Color -->
<div class="col-12 col-md-5 col-lg-4 text-center text-md-end">
    <div class="p-3 rounded-4 d-inline-flex flex-column align-items-center w-100 shadow-sm"
         style="background: rgba(255, 255, 255, 0.05); 
                backdrop-filter: blur(12px); 
                border: 1px solid rgba(255, 255, 255, 0.15); 
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);">

        <span class="text-secondary small mb-2 font-monospace d-flex align-items-center gap-1">
            <i class="bi bi-eyedropper text-info"></i> Haz clic para cambiar
        </span>

        <!-- Contenedor del selector con realce -->
        <div class="d-flex justify-content-center align-items-center p-2 rounded-circle mb-3"
             style="background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.1);">
            <div id="color-picker"></div>
        </div>

        <!-- Botón Restaurar Estilizado -->
        <button type="button" onclick="color()"
                class="btn btn-outline-info btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 transition-all w-100"
                style="border-color: rgba(56, 189, 248, 0.4); background: rgba(56, 189, 248, 0.08);">
            <i class="bi bi-arrow-counterclockwise fs-6"></i> Restaurar predeterminado
        </button>
    </div>
</div>   
                            </div>
                            <!-- Botón Guardar -->
                            <div class="text-end mt-4 pt-3 border-top">
                                <button type="submit" id="btnGuardar"
                                    class="btn btn-success  rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-floppy-fill me-1"></i> Guardar Configuración
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>

        </div>
        <!-- SECCIÓN SELECTOR DE COLOR EN SU CARDA CRISTAL -->

    </div>

    <!-- Scripts Base -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function color()
        {
            colorId='rgba(255, 255, 255, 0.85)';
        }
    const URL_CONTROLADOR_MIACCESO = '/myvet/app/controllers/miAccesoController.php';
let colorId = <?= json_encode($_SESSION['sidebar_bg'] ?? '') ?>;
    $(document).ready(function() {
        // Previsualización de la imagen al subirla
        $('#inputLogo').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    $('#imgLogoPreview').attr('src', evt.target.result).removeClass('d-none');
                    $('#iconFallback').addClass('d-none');
                }
                reader.readAsDataURL(file);
            }
        });

        // Envio del Formulario vía AJAX
       $('#formMiAcceso').on('submit', async function(e) {
    e.preventDefault();

    const btn = $('#btnGuardar');
    btn.prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...'
    );

    try {
        const formData = new FormData(this);
        
        // Agregar la variable de color al FormData
        // Asegúrate de que 'colorId' esté definida globalmente o accesible en este scope
        formData.append('colorId',  colorId);
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
        colorId=rgbaColor;
        console.log(colorId);
        document.documentElement.style.setProperty('--sidebar-bg', rgbaColor);
    });
    </script>
</body>

</html>