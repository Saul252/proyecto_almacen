<?php
// terminos-y-condiciones.php
// Puedes incluir aquí tus variables de sesión o header general
// include_once 'header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones de Uso | CF System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .legal-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            padding: 3rem 0;
            border-bottom: 4px solid #0d6efd;
        }
        .legal-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
        }
        .sticky-sidebar {
            position: -webkit-sticky;
            position: sticky;
            top: 2rem;
        }
        .nav-legal .nav-link {
            color: #475569;
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            margin-bottom: 0.2rem;
            transition: all 0.2s ease;
        }
        .nav-legal .nav-link:hover, .nav-legal .nav-link.active {
            color: #0d6efd;
            background-color: #eef2ff;
            font-weight: 600;
        }
        .clause-title {
            color: #1e293b;
            font-weight: 700;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
            margin-top: 2.5rem;
            margin-bottom: 1.25rem;
        }
        .clause-title:first-of-type {
            margin-top: 0;
        }
        .badge-legal {
            background-color: #e0e7ff;
            color: #3730a3;
            font-weight: 600;
        }
        .legal-footer {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 1.5rem 0;
        }
    </style>
</head>
<body data-bs-spy="scroll" data-bs-target="#legal-nav" data-bs-offset="100" tabindex="0">

    <!-- Encabezado Principal -->
    <header class="legal-header mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge badge-legal mb-2 px-3 py-2">Documento Legal Vinculante</span>
                    <h1 class="fw-bold mb-2">Términos y Condiciones de Servicio</h1>
                    <p class="text-white-50 mb-0">Contrato de Adhesión para el Licenciamiento de Software SaaS (CF System)</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <p class="small text-white-50 mb-1">Última actualización: <?php echo date('d/m/Y'); ?></p>
                    <button onclick="window.print();" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-printer me-1"></i> Imprimir Contrato
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido Legal -->
    <main class="container mb-5">
        <div class="row g-4">
            
            <!-- Barra Lateral de Navegación -->
            <nav class="col-lg-3 d-none d-lg-block">
                <div class="sticky-sidebar p-3 legal-card">
                    <h6 class="fw-bold text-uppercase text-muted mb-3 style="font-size: 0.75rem;">Índice del Contrato</h6>
                    <div id="legal-nav" class="nav flex-column nav-legal">
                        <a class="nav-link" href="#preambulo">1. Declaración Preliminar</a>
                        <a class="nav-link" href="#clausula-1">2. Objeto y Esquema</a>
                        <a class="nav-link" href="#clausula-2">3. Facturación y Pagos</a>
                        <a class="nav-link" href="#clausula-3">4. Desarrollo a Medida</a>
                        <a class="nav-link" href="#clausula-4">5. Tutorías y Viáticos</a>
                        <a class="nav-link" href="#clausula-5">6. Niveles de Servicio (SLA)</a>
                        <a class="nav-link" href="#clausula-6">7. Uso y Datos Internos</a>
                        <a class="nav-link" href="#clausula-7">8. Protección de Datos</a>
                        <a class="nav-link" href="#clausula-8">9. Multialmacén y Planes</a>
                        <a class="nav-link" href="#clausula-9">10. Cancelación y Suspensión</a>
                        <a class="nav-link" href="#clausula-10">11. Propiedad Intelectual</a>
                        <a class="nav-link" href="#clausula-11">12. Caso Fortuito / Fuerza Mayor</a>
                        <a class="nav-link" href="#clausula-12">13. Confidencialidad (NDA)</a>
                        <a class="nav-link" href="#clausula-13">14. Tarifas e Incrementos</a>
                        <a class="nav-link" href="#clausula-14">15. Mantenimientos</a>
                        <a class="nav-link" href="#clausula-15">16. Ciberseguridad y Respaldos</a>
                        <a class="nav-link" href="#clausula-16">17. Cesión y Reventa</a>
                        <a class="nav-link" href="#clausula-17">18. Divisibilidad</a>
                        <a class="nav-link" href="#clausula-18">19. Jurisdicción Aplicable</a>
                    </div>
                </div>
            </nav>

            <!-- Cuerpo del Documento -->
            <div class="col-lg-9">
                <div class="card legal-card p-4 p-md-5">

                    <!-- Preámbulo -->
                    <section id="preambulo" class="mb-4">
                        <div class="alert alert-primary d-flex align-items-top mb-4" role="alert">
                            <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                            <div>
                                <strong>Aviso Importante:</strong> Al registrarse, ingresar o hacer uso de la plataforma, usted acepta formalmente el cumplimiento de estas cláusulas bajo el marco de los artículos 1803 y 1805 del Código Civil Federal y el artículo 80 del Código de Comercio de los Estados Unidos Mexicanos.
                            </div>
                        </div>
                        <h4 class="fw-bold text-primary mb-3">DECLARACIÓN PRELIMINAR Y ACEPTACIÓN DEL USUARIO</h4>
                        <p class="text-justify">
                            El presente instrumento regula el acceso, uso, licenciamiento y operación del sistema informático de gestión empresarial (en adelante, <strong>"El Sistema"</strong>). Al presionar el botón de aceptación, registrarse, contratar, ingresar o hacer uso de la plataforma, la persona física o moral (en adelante, <strong>"El Cliente"</strong>) manifiesta su consentimiento expreso, libre e informado, reconociendo que los medios electrónicos constituyen una manifestación válida de la voluntad con pleno valor probatorio.
                        </p>
                    </section>

                    <!-- Cláusula 1 -->
                    <section id="clausula-1">
                        <h5 class="clause-title">CLÁUSULA PRIMERA: DEL OBJETO Y ESQUEMA DE CONTRATACIÓN</h5>
                        <ol>
                            <li class="mb-2"><strong>Modelos de Cobro y Módulos Adicionales:</strong> La renta o contraprestación por el uso de El Sistema se calculará de manera individual por cada almacén, nodo o sucursal contratada. Dicho costo base se incrementará conforme a los módulos adicionales, integraciones o licencias de usuarios extra habilitados por El Cliente.</li>
                            <li class="mb-2"><strong>Exclusión de Servicios Gratuitos:</strong> En términos del artículo 1796 del Código Civil Federal, las partes quedan obligadas al tenor de lo pactado. El Sistema no otorgará bajo ninguna circunstancia módulos, funciones o servicios gratuitos, salvo convenio especial por escrito firmado por ambas partes.</li>
                            <li class="mb-2"><strong>Periodos de Prueba y Suspensión:</strong> Los periodos de prueba tendrán una duración improrrogable de siete (7) días naturales a un (1) mes calendario como máximo, según la promoción vigente. Vencido dicho plazo sin que se verifique la contratación formal, la cuenta pasará a estado suspendido en los términos fijados en la Cláusula Novena.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 2 -->
                    <section id="clausula-2">
                        <h5 class="clause-title">CLÁUSULA SEGUNDA: CICLO DE FACTURACIÓN, PRORRATEO, SUSPENSIÓN Y PAGOS</h5>
                        <ol>
                            <li class="mb-2"><strong>Periodicidad Mensual y Fecha de Corte:</strong> El esquema de contratación de El Sistema es de carácter estrictamente mensual, fijando como fecha única de vencimiento y renovación el día <strong>primero (1) de cada mes calendario</strong>.</li>
                            <li class="mb-2"><strong>Periodos de Gracia y Prorrateos:</strong>
                                <ul>
                                    <li>Si la contratación se realiza a partir del día 28 del mes, La Empresa otorgará un periodo de gracia no cobrable hasta el fin de dicho mes. La primera mensualidad completa se cobrará el día 1 del mes subsiguiente.</li>
                                    <li>Para contrataciones realizadas en días previos dentro del mes, el cobro de los días proporcionales restantes quedará sujeto a la política de evaluación de La Empresa.</li>
                                </ul>
                            </li>
                            <li class="mb-2"><strong>Mora y Suspensión del Servicio:</strong> Si El Cliente no liquida su mensualidad el día 1, el acceso a El Sistema se suspenderá automáticamente. El Cliente dispondrá de un periodo de tolerancia de quince (15) días naturales para regularizar su adeudo acumulado. Durante dicho periodo el servicio permanecerá inactivo; una vez liquidado el pago, la cuenta será reactivada.</li>
                            <li class="mb-2"><strong>Validación de Transferencias Bancarias:</strong> En pagos por transferencia electrónica o depósito, la reactivación o liberación del servicio se procesará tras la verificación manual del ingreso de fondos en las cuentas bancarias de La Empresa. El Cliente deberá notificar y enviar la ficha de pago correspondiente por los canales oficiales para gestionar la validación.</li>
                            <li class="mb-2"><strong>Imputabilidad por Errores de Transferencia:</strong> En términos de los artículos 2062 y 2095 del Código Civil Federal, la carga de realizar el pago correctamente recae sobre el pagador. La Empresa no se hace responsable por transferencias a cuentas equivocadas, montos erróneos o fallas bancarias originadas por El Cliente.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 3 -->
                    <section id="clausula-3">
                        <h5 class="clause-title">CLÁUSULA TERCERA: DESARROLLO PERSONALIZADO, COSTOS, PRUEBAS Y PENALIZACIONES</h5>
                        <ol>
                            <li class="mb-2"><strong>Cotización y Anticipo Inicial:</strong> Cualquier requerimiento de desarrollo a medida fuera de la versión base de El Sistema se cotizará según su complejidad técnica. Dicho desarrollo tendrá un costo base mínimo de <strong>$3,000.00 MXN</strong> (tres mil pesos 00/100 M.N.). Si la complejidad del módulo es mayor, la tarifa final se negociará previamente con El Cliente. Para iniciar los trabajos, El Cliente deberá cubrir obligatoriamente un <strong>pago inicial del 50% del costo total del módulo</strong>, pudiendo diferir el 50% restante en las mensualidades posteriores.</li>
                            <li class="mb-2"><strong>Obligación de Continuidad y Suspensión por Falta de Pago:</strong> El Cliente está obligado a mantener activa la contratación mensual de El Sistema durante todo el periodo de desarrollo del módulo personalizado y posterior a su entrega. Si El Cliente incurre en mora o suspende el pago del software durante la fase de desarrollo:
                                <ul>
                                    <li>El desarrollo del módulo personalizado quedará pausado de forma inmediata.</li>
                                    <li>Si El Cliente reactiva su cuenta con posterioridad solicitando la entrega del módulo, acepta expresamente que el proyecto sufrirá un retraso e incremento en los tiempos de entrega debido a la reprogramación del equipo de ingeniería.</li>
                                </ul>
                            </li>
                            <li class="mb-2"><strong>Pena Convencional Proporcional por Cancelación Anticipada:</strong> Si El Cliente cancela el servicio o incurre en falta de pago durante el primer mes de un periodo de desarrollo estimado (ejemplo: un proyecto con duración prevista de tres meses), El Cliente cubrirá como pena convencional la liquidación de hasta el <strong>50% del costo total del módulo</strong> contratado por concepto de amortización de gastos de desarrollo e ingeniería consumidos. El 50% restante del costo del módulo quedará cancelado de pleno derecho y el proyecto se dará por concluido definitivamente.</li>
                            <li class="mb-2"><strong>Proceso de Pruebas y Reporte de Bugs (QA):</strong> El Cliente acepta que el desarrollo de software a medida implica fases de ajuste y reconoce la presencia inherente de errores temporales (<em>bugs</em>). El Cliente asume la obligación de actuar como usuario probador final (<em>Beta Tester</em>), realizando las pruebas de campo e informando de manera inmediata a La Empresa sobre cualquier falla detectada para su oportuna corrección.</li>
                            <li class="mb-2"><strong>Propiedad Intelectual sobre Desarrollos:</strong> Salvo pacto expreso en contrario por escrito, los desarrollos a medida o módulos adicionales continúan siendo propiedad intelectual exclusiva de La Empresa en términos de los artículos 83 y 84 de la Ley Federal del Derecho de Autor, otorgando a El Cliente únicamente una licencia de uso no exclusiva e intransferible.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 4 -->
                    <section id="clausula-4">
                        <h5 class="clause-title">CLÁUSULA CUARTA: TUTORÍAS, CAPACITACIONES Y VIÁTICOS</h5>
                        <ol>
                            <li class="mb-2"><strong>Tutoría Virtual Incluida:</strong> Al contratar el servicio, El Cliente tiene derecho a una (1) sesión inicial de capacitación en línea con una duración máxima de tres (3) horas continuas.</li>
                            <li class="mb-2"><strong>Sesiones Adicionales:</strong> Cualquier sesión de capacitación o soporte técnico personalizado requerido con posterioridad a la sesión inicial se cobrará conforme a las siguientes tarifas:
                                <ul>
                                    <li><strong>Modalidad Virtual:</strong> $400.00 MXN por hora o fracción.</li>
                                    <li><strong>Modalidad Presencial:</strong> $600.00 MXN por hora o fracción.</li>
                                </ul>
                            </li>
                            <li class="mb-2"><strong>Comprobación de Viáticos:</strong> En tutorías presenciales, El Cliente reembolsará el 100% de los gastos de viáticos derivados de traslados, hospedaje y alimentación del personal técnico asignado. La Empresa presentará las evidencias y comprobantes correspondientes: boletos de avión, boletos de autobús y recibos o tarifas emitidos por plataformas de transporte privado (tales como Uber u homogéneas) para traslados locales dentro de la Ciudad de México u otras localidades donde no se emitan comprobantes tradicionales, sirviendo dichas constancias digitales como base para la comprobación del gasto.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 5 -->
                    <section id="clausula-5">
                        <h5 class="clause-title">CLÁUSULA QUINTA: CONTINUIDAD DEL SERVICIO (SLA), CAÍDAS Y DESLINDES DE HARDWARE</h5>
                        <ol>
                            <li class="mb-2"><strong>Compensación por Caídas en la Nube:</strong> Si El Sistema hosted en la nube sufre una interrupción técnica imprevista atribuible a La Empresa, los días de inactividad serán compensados mediante un descuento proporcional aplicado directamente en la siguiente mensualidad de El Cliente.</li>
                            <li class="mb-2"><strong>Falla Estructural Masiva:</strong> En el evento extraordinario de que la infraestructura sufra una falla estructural masiva que imposibilite la operación del sistema de forma definitiva durante el mes en curso, La Empresa reembolsará el <strong>100% de la mensualidad pagada</strong> correspondiente a ese mes, constituyendo dicho monto el límite y tope máximo de responsabilidad financiera de La Empresa.</li>
                            <li class="mb-2"><strong>Exención por Infraestructura del Cliente o Hosting Local:</strong> La Empresa no asume responsabilidad alguna por fallas, obsolescencia, sobrecalentamiento, ataques de virus, interrupciones de internet o desperfectos físicos en las computadoras, redes o servidores de El Cliente. En instalaciones desplegadas en servidores locales (<em>On-Premise</em>) o hosting contratado directamente por El Cliente, La Empresa queda liberada de cualquier responsabilidad por caídas, lentitud o pérdida de información derivadas de configuraciones deficientes, falta de respaldos o brechas de seguridad en la infraestructura del cliente.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 6 -->
                    <section id="clausula-6">
                        <h5 class="clause-title">CLÁUSULA SEXTA: RESPONSABILIDAD SOBRE LA INFORMACIÓN Y USO INTERNO</h5>
                        <ol>
                            <li class="mb-2"><strong>Imputabilidad de Operaciones:</strong> En apego al principio de neutralidad tecnológica y a los artículos 89 y 90 del Código de Comercio, El Cliente es el único responsable por la creación, modificación, alta o eliminación de registros (clientes, proveedores, inventarios, precios y ventas) realizados desde sus cuentas de usuario.</li>
                            <li class="mb-2"><strong>Descargas y Exportación de Datos:</strong> El Sistema actúa únicamente como un intermediario tecnológico. La extracción, descarga o exportación de reportes o bases de datos por parte de los usuarios asignados por El Cliente será de su entera responsabilidad, eximiendo a La Empresa de cualquier responsabilidad civil, mercantil o penal por la fuga de información o uso inadecuado de dichos materiales.</li>
                            <li class="mb-2"><strong>Credenciales de Acceso:</strong> Conforme al artículo 91 del Código de Comercio, la custodia y confidencialidad de los nombres de usuario y contraseñas corresponde exclusivamente a El Cliente y a sus usuarios finales.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 7 -->
                    <section id="clausula-7">
                        <h5 class="clause-title">CLÁUSULA SÉPTIMA: TRATAMIENTO DE DATOS PERSONALES DE TERCEROS (LFPDPPP)</h5>
                        <ol>
                            <li class="mb-2"><strong>Distribución de Roles Jurídicos:</strong> En cumplimiento de la Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP), El Cliente manifiesta y reconoce que actúa como único <strong>Responsable</strong> del tratamiento de los datos personales de sus clientes, proveedores y empleados ingresados a El Sistema. La Empresa actúa única y exclusivamente en calidad de <strong>Encargado</strong> del tratamiento tecnológico.</li>
                            <li class="mb-2"><strong>Ausencia de Control y Deslinde ante el INAI:</strong> La Empresa no audita, comercializa, transfiere ni gestiona para fines propios la información de terceros ingresada por El Cliente. Cualquier queja, reclamación o procedimiento sancionador iniciado por titulares de datos o por el INAI derivado del mal uso, filtración o falta de consentimiento en la recolección de datos por parte de El Cliente será responsabilidad directa y exclusiva de este último.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 8 -->
                    <section id="clausula-8">
                        <h5 class="clause-title">CLÁUSULA OCTAVA: ARQUITECTURA DE ALMACENES Y REGLA DE COMPATIBILIDAD</h5>
                        <ol>
                            <li class="mb-2"><strong>Almacenes Independientes:</strong> En contrataciones bajo el esquema de almacenes individuales, no existirá vinculación, intercambio ni sincronización de datos entre los distintos almacenes, operando como instancias aisladas.</li>
                            <li class="mb-2"><strong>Modalidad Multialmacén:</strong> En el esquema multialmacén, las sucursales compartirán los catálogos de clientes, proveedores y vendedores, manteniendo la independencia estricta respecto del inventario y existencias físicas de cada almacén.</li>
                            <li class="mb-2"><strong>Regla Estricta de Compatibilidad de Planes:</strong> Por arquitectura de base de datos e integridad del sistema, la vinculación entre almacenes en la modalidad multialmacén requiere obligatoriamente que todos los almacenes pertenezcan exactamente a la misma categoría o identificador de plan. De tal manera, la vinculación solo es técnicamente válida e integrable entre planes idénticos (N con N, es decir: Plan 1 exclusivamente con Plan 1, Plan 2 exclusivamente con Plan 2, Plan 3 exclusivamente con Plan 3, y así sucesivamente). Se prohíbe explícitamente la combinación o interconexión de planes con numeración o tipología distinta.</li>
                            <li class="mb-2"><strong>Ventana de Migración:</strong> La conversión de un esquema de <em>Almacén Individual</em> a una estructura <em>Multialmacén</em> requerirá de una ventana de mantenimiento técnico e ingeniería de datos de hasta siete (7) días hábiles.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 9 -->
                    <section id="clausula-9">
                        <h5 class="clause-title">CLÁUSULA NOVENA: NOTIFICACIÓN DE RESCISIÓN Y RÉGIMEN DE DATOS SUSPENDIDOS</h5>
                        <ol>
                            <li class="mb-2"><strong>Notificación y Perfeccionamiento del Aviso:</strong> La rescisión o cancelación del servicio se notificará a El Cliente mediante comunicación digital enviada al correo electrónico registrado o vía WhatsApp. Transcurrido un periodo de <strong>dos (2) días hábiles</strong> contados a partir de su emisión, La Empresa dará por perfeccionada y efectivamente recibida dicha notificación para todos los efectos legales a que haya lugar.</li>
                            <li class="mb-2"><strong>Derecho de Extracción de Datos en Cuentas Suspendidas:</strong> Tras la rescisión o suspensión de la cuenta, La Empresa no eliminará la base de datos de El Cliente. La información permanecerá resguardada en estado suspendido, inhabilitando el acceso de El Cliente a los módulos operativos y funcionales de El Sistema.</li>
                            <li class="mb-2"><strong>Módulo Exclusivo de Exportación:</strong> El Cliente mantendrá habilitada únicamente la facultad de acceder a un módulo restringido para <strong>descargar y exportar su información comercial primaria</strong> (catálogos de proveedores, clientes e inventarios). Queda estrictamente prohibido el uso de la interfaz operativa o la ejecución de procesos transaccionales dentro de El Sistema durante el periodo de suspensión.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 10 -->
                    <section id="clausula-10">
                        <h5 class="clause-title">CLÁUSULA DÉCIMA: PROPIEDAD INTELECTUAL, INGENIERÍA INVERSA Y SANCIONES</h5>
                        <ol>
                            <li class="mb-2"><strong>Protección Legal:</strong> El Sistema, su código fuente, código objeto, estructuras de base de datos, marcas, diagramas e interfaces gráficas están protegidos por la Ley Federal del Derecho de Autor (artículos 101, 102 y conexos) y la Ley Federal de Protección a la Propiedad Industrial (artículos 163 y 386).</li>
                            <li class="mb-2"><strong>Prohibición de Manipulación:</strong> Queda estrictamente prohibido a El Cliente, sus dependientes o terceros:
                                <ul>
                                    <li>Practicar ingeniería inversa, descompilación, desmontaje o descifrado sobre el código fuente de El Sistema.</li>
                                    <li>Alterar, inyectar código o modificar las estructuras de la base de datos sin autorización por escrito.</li>
                                    <li>Sublicenciar, revender o duplicar la plataforma.</li>
                                </ul>
                            </li>
                            <li class="mb-2"><strong>Sanciones Penalidades y Civiles:</strong> La infracción a esta cláusula facultará a La Empresa a rescindir el servicio inmediatamente y a ejercitar acciones civiles por daños y perjuicios, así como las denuncias penales correspondientes en términos de los artículos 424 bis y 427 del Código Penal Federal.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 11 -->
                    <section id="clausula-11">
                        <h5 class="clause-title">CLÁUSULA DÉCIMA PRIMERA: CASO FORTUITO, FUERZA MAYOR Y EVENTOS FUERA DE CONTROL</h5>
                        <ol>
                            <li class="mb-2"><strong>Exención Absoluta de Responsabilidad:</strong> En términos del artículo 2111 del Código Civil Federal, La Empresa no será responsable por la interrupción total o parcial de El Sistema, lentitud, fallas de conectividad o pérdida de datos causadas por eventos de caso fortuito o fuerza mayor.</li>
                            <li class="mb-2"><strong>Catálogo de Eventos Exclusivos:</strong> Se entienden fuera del control directo de La Empresa: desastres naturales (sismos, inundaciones, tormentas), cortes masivos de energía eléctrica o fibra óptica, actos de autoridad, huelgas, fallas en la infraestructura global de proveedores de nube o telecomunicaciones, así como ciberataques masivos (incluyendo ataques DDoS, malware o exploits de día cero) que superen los estándares de seguridad comerciales aplicables.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 12 -->
                    <section id="clausula-12">
                        <h5 class="clause-title">CLÁUSULA DÉCIMA SEGUNDA: CONFIDENCIALIDAD Y SECRETO INDUSTRIAL (LFPPI)</h5>
                        <ol>
                            <li class="mb-2"><strong>Protección Recíproca de Datos:</strong> En estricto apego a los artículos 163, 164 y conexos de la Ley Federal de Protección a la Propiedad Industrial (LFPPI), ambas partes se obligan a guardar estricta confidencialidad respecto a la información técnica, comercial, financiera y operativa compartida durante la relación contractual.</li>
                            <li class="mb-2"><strong>Resguardo de Algoritmos e Información Comercial:</strong> El Cliente se obliga a no divulgar, mostrar, copiar o revelar las pantallas, lógica de negocio, arquitectura o metodologías de El Sistema a terceros. Por su parte, La Empresa mantendrá la confidencialidad de las bases de datos de El Cliente (proveedores, inventarios y márgenes de venta), no pudiendo explotarla ni revelarla a competidores bajo ninguna circunstancia.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 13 -->
                    <section id="clausula-13">
                        <h5 class="clause-title">CLÁUSULA DÉCIMA TERCERA: AJUSTES TARIFARIOS E INCREMENTO DE COSTOS</h5>
                        <ol>
                            <li class="mb-2"><strong>Reserva de Derecho de Ajuste:</strong> La Empresa se reserva el derecho de modificar y actualizar el costo de la renta mensual de El Sistema, así como de los módulos adicionales y tarifas de soporte.</li>
                            <li class="mb-2"><strong>Notificación Anticipada y Aplicación:</strong> Todo incremento tarifario será notificado a El Cliente con al menos <strong>treinta (30) días naturales de anticipación</strong> mediante aviso dentro de la plataforma o al correo electrónico registrado. Si El Cliente no manifiesta su disconformidad y continúa utilizando el servicio transcurrido dicho plazo, se entenderán aceptadas de pleno derecho las nuevas tarifas para los ciclos de facturación subsecuentes.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 14 -->
                    <section id="clausula-14">
                        <h5 class="clause-title">CLÁUSULA DÉCIMA CUARTA: MANTENIMIENTOS PROGRAMADOS Y VENTANAS DE SERVICIO</h5>
                        <ol>
                            <li class="mb-2"><strong>Interrupciones Técnicas Necesarias:</strong> La Empresa podrá realizar mantenimientos preventivos, correctivos, despliegue de parches de seguridad y actualizaciones de servidor para garantizar la estabilidad de El Sistema.</li>
                            <li class="mb-2"><strong>Exclusión de Compensación:</strong> Dichas ventanas de mantenimiento podrán generar suspensiones temporales del servicio o lentitud en la plataforma. Siempre que las labores sean programadas o ejecutadas en horarios de bajo tráfico, <strong>no darán lugar al cobro de compensaciones, descuentos ni reclamaciones por caída de servicio</strong> estipuladas en la Cláusula Quinta.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 15 -->
                    <section id="clausula-15">
                        <h5 class="clause-title">CLÁUSULA DÉCIMA QUINTA: INCIDENTES DE CIBERSEGURIDAD, COPIAS DE RESPALDO Y LATENCIA</h5>
                        <ol>
                            <li class="mb-2"><strong>Protocolo de Suspensión de Emergencia:</strong> Ante la detección de una vulneración, intento de intrusión o incidente crítico de ciberseguridad, La Empresa queda facultada para <strong>suspender de forma inmediata y preventiva el acceso a El Sistema</strong> con el fin de contener el ataque y salvaguardar la integridad de la base de datos.</li>
                            <li class="mb-2"><strong>Respaldos y Margen de Latencia:</strong> La Empresa mantiene esquemas automatizados de copias de seguridad (<em>backups</em>). Sin embargo, El Cliente reconoce y acepta que, debido a la latencia técnica inherente al procesamiento y réplica de datos entre servidores, las copias de seguridad <strong>pueden no estar 100% actualizadas al último segundo previo al incidente</strong>. La Empresa queda eximida de cualquier responsabilidad por la pérdida del diferencial del margen de datos no reflejado dentro de dicha latencia durante la restauración.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 16 -->
                    <section id="clausula-16">
                        <h5 class="clause-title">CLÁUSULA DÉCIMA SEXTA: CESIÓN DEL SOFTWARE, PROHIBICIÓN DE REVENTA Y MULTA MERCANTIL</h5>
                        <ol>
                            <li class="mb-2"><strong>Derecho de Cesión de La Empresa:</strong> La Empresa mantiene el derecho irrestricto de ceder, vender, licenciar o transferir la titularidad de El Sistema, su código fuente o la cartera de clientes a cualquier otra entidad o tercero, sin que dicha transmisión genere responsabilidad legal, reclamo o indemnización alguna en favor de El Cliente.</li>
                            <li class="mb-2"><strong>Prohibición de Subarrendamiento y Reventa por El Cliente:</strong> Queda estrictamente prohibido a El Cliente sublicenciar, revender, arrendar, prestar, ceder, comercializar o hacer copartícipes a otras empresas o terceros del uso de sus accesos o instancias de El Sistema.</li>
                            <li class="mb-2"><strong>Sanciones y Multa Convencional:</strong> En caso de que se detecte que El Cliente actúa como distribuidor, revendedor no autorizado o compartidor de licencias, La Empresa procederá a la <strong>suspensión inmediata y definitiva de la cuenta</strong>, reservándose el derecho de interponer la acción legal penal por violación a los derechos de autor y exigir en la vía mercantil una <strong>multa convencional ejecutiva</strong>, además del reclamo por daños y perjuicios.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 17 -->
                    <section id="clausula-17">
                        <h5 class="clause-title">CLÁUSULA DÉCIMA SÉPTIMA: DIVISIBILIDAD E INTEGRIDAD DEL ACUERDO</h5>
                        <ol>
                            <li class="mb-2"><strong>Nulidad Parcial (<em>Severability</em>):</strong> Si cualquier disposición o cláusula del presente contrato es declarada nula, inválida o inejecutable por un tribunal competente, las cláusulas restantes mantendrán su plena validez, fuerza y efecto legal.</li>
                            <li class="mb-2"><strong>Integridad Contractual:</strong> El presente instrumento constituye la manifestación completa de la voluntad entre las partes respecto al uso de El Sistema, dejando sin efecto cualquier negociación, correo, chat o acuerdo verbal previo.</li>
                        </ol>
                    </section>

                    <!-- Cláusula 18 -->
                    <section id="clausula-18">
                        <h5 class="clause-title">CLÁUSULA DÉCIMA OCTAVA: JURISDICCIÓN Y LEGISLACIÓN APLICABLE</h5>
                        <p>
                            Para la interpretación, cumplimiento y resolución de controversias derivadas del presente contrato, las partes se someten expresamente a las leyes aplicables de los Estados Unidos Mexicanos y a la jurisdicción de los tribunales competentes de la Ciudad de México, renunciando a cualquier otro fuero que pudiera corresponderles por razón de sus domicilios presentes o futuros.
                        </p>
                    </section>

                    <!-- Acción / Botón de Aceptación opcional para tu formulario -->
                    <div class="mt-5 p-4 bg-light rounded-3 text-center border">
                        <h5 class="fw-bold mb-2">Conformidad Legal</h5>
                        <p class="text-muted small mb-3">Al presionar el botón inferior, confirmas que has leído, comprendido y aceptado la totalidad de las cláusulas contractuales expuestas.</p>
                        <form action="procesar-aceptacion.php" method="POST">
                            <input type="hidden" name="fecha_aceptacion" value="<?php echo date('Y-m-d H:i:s'); ?>">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-check-circle-fill me-2"></i> Aceptar Términos y Condiciones
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <!-- Footer de la Página -->
    <footer class="legal-footer text-center text-muted small">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> CF System. Todos los derechos reservados. Módulo de Términos y Condiciones Legales.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JavaScript Bundle con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>