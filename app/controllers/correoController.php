<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../helpers/Mailer.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Mostrar errores SOLO para depurar (quitar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        // ============================================
        // 1. DECODIFICAR ENTRADA (JSON o form-data)
        // ============================================
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $raw = file_get_contents('php://input');

        if (stripos($contentType, 'application/json') !== false) {
            $datos = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON inválido: ' . json_last_error_msg());
            }
        } else {
            $datos = $_POST;
        }

        if (empty($datos)) {
            throw new Exception('No se recibieron datos.');
        }

        // ============================================
        // 2. DETECTAR MODO
        // ============================================
        $modo = $datos['modo'] ?? 'simple';

        $para = $datos['para'] ?? null;
        if (empty($para)) {
            throw new Exception('Falta el campo "para".');
        }
        $destinatarios = is_array($para) ? $para : [$para];

        // ============================================
        // 3. NORMALIZAR CAMPOS
        // ============================================
        foreach (['copia', 'copia_oculta'] as $campo) {
            if (isset($datos[$campo]) && is_string($datos[$campo])) {
                $decoded = json_decode($datos[$campo], true);
                $datos[$campo] = is_array($decoded) ? $decoded : [$datos[$campo]];
            }
        }

        // ============================================
        // 4. PREPARAR ADJUNTOS
        // ============================================
        $adjuntos = $datos['adjuntos'] ?? [];

        if (is_string($adjuntos)) {
            $decoded = json_decode($adjuntos, true);
            $adjuntos = is_array($decoded) ? $decoded : [$adjuntos];
        }

        $rutasAdjuntos = [];

        foreach ($adjuntos as $item) {
            // Caso A: HTML → PDF
            if (is_array($item) && !empty($item['html'])) {
                $nombrePdf = $item['nombre'] ?? ('documento_' . uniqid() . '.pdf');
                $rutaPdf = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nombrePdf;

                // Asegurar UTF-8 en el HTML
                $html = $item['html'];
                if (!mb_check_encoding($html, 'UTF-8')) {
                    $html = mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
                }

                // Asegurar que el HTML tenga meta charset
                if (stripos($html, '<meta charset') === false) {
                    $html = str_replace('<head>', '<head><meta charset="UTF-8">', $html);
                }

                $dompdf = new Dompdf(new Options([
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'DejaVu Sans',
                ]));
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $pdfOutput = $dompdf->output();
                if (empty($pdfOutput)) {
                    throw new Exception("Dompdf generó un PDF vacío para: $nombrePdf");
                }

                if (file_put_contents($rutaPdf, $pdfOutput) === false) {
                    throw new Exception("No se pudo escribir el PDF en: $rutaPdf");
                }

                if (!is_file($rutaPdf) || filesize($rutaPdf) === 0) {
                    throw new Exception("El PDF generado está vacío: $rutaPdf");
                }

                $rutasAdjuntos[] = $rutaPdf;
            }
            // Caso B: ruta a archivo existente
            elseif (is_string($item) && is_file($item)) {
                $rutasAdjuntos[] = $item;
            }
        }

        // ============================================
        // 5. ENVIAR
        // ============================================
        $mailer = new Mailer();

        $opciones = [
            'para' => $destinatarios,
            'asunto' => $datos['asunto'] ?? 'Sin asunto',
            'contenido' => $datos['contenido'] ?? '',
            'adjuntos' => $rutasAdjuntos,
            'copia' => $datos['copia'] ?? [],
            'copia_oculta' => $datos['copia_oculta'] ?? [],
        ];

        $resultado = $mailer->enviar($opciones);

        // ============================================
        // 6. LIMPIAR PDFs TEMPORALES
        // ============================================
        foreach ($rutasAdjuntos as $ruta) {
            if (strpos($ruta, sys_get_temp_dir()) === 0 && is_file($ruta)) {
                @unlink($ruta);
            }
        }

        // ============================================
        // 7. RESPUESTA JSON SEGURA
        // ============================================
        $json = json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        if ($json === false) {
            echo json_encode([
                'ok' => false,
                'error' => 'Error al codificar JSON: ' . json_last_error_msg()
            ]);
        } else {
            echo $json;
        }

    } catch (Throwable $e) {
        // Throwable captura Exception y Error
        $json = json_encode([
            'ok' => false,
            'error' => $e->getMessage(),
            'archivo' => $e->getFile(),
            'linea' => $e->getLine()
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        echo $json !== false ? $json : '{"ok":false,"error":"Error fatal no codificable"}';
    }
    exit;
}

http_response_code(405);
echo json_encode(['ok' => false, 'error' => 'Método no permitido. Usa POST.']);
exit;