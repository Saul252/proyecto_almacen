<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function protegerPagina($modulo = null) {
    // 1. Verificar si está logueado (Lo que ya tenías)
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        $host = $_SERVER['HTTP_HOST'];
        header("Location: http://$host/myvet/index.php"); 
        exit;
    }

    // 2. Verificar permiso del módulo (La parte que falta)
    if ($modulo !== null) {
        // Importamos la función de permisos si no está disponible
        if (!function_exists('puedeVerModulo')) {
            require_once __DIR__ . '/permisos.php'; 
        }

        if (!puedeVerModulo($modulo)) {
    $host = $_SERVER['HTTP_HOST'];
    // Al estar en la raíz, la ruta es /myvet/403.php
    header("Location: http://$host/myvet/403.php");
    exit;
}
    }
}