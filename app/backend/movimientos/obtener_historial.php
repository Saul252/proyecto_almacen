<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

// 1. IMPORTANTE: Iniciar sesión para validar el rol y el almacén del usuario
session_start(); 
require_once $_SERVER['DOCUMENT_ROOT'] . '/myvet/config/conexion.php';

// Validamos que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['data' => [], 'error' => 'No autorizado']);
    exit;
}

$almacen_usuario = $_SESSION['almacen_id'] ?? 0; // 0 significa Admin
$periodo = $_GET['periodo'] ?? 'hoy';
$tipo = $_GET['tipo'] ?? '';
$f_inicio_user = $_GET['f_inicio'] ?? '';
$f_fin_user = $_GET['f_fin'] ?? '';

// Lógica de fechas automatizada
$hoy = date('Y-m-d');
$inicio = $hoy;
$fin = $hoy;

if ($periodo !== 'personalizado') {
    switch ($periodo) {
        case 'ayer': 
            $inicio = date('Y-m-d', strtotime('-1 day')); 
            $fin = $inicio; 
            break;
        case 'semana': 
            $inicio = date('Y-m-d', strtotime('-7 days')); 
            break;
        case 'mes': 
            $inicio = date('Y-m-01'); 
            break;
    }
} else {
    $inicio = !empty($f_inicio_user) ? $f_inicio_user : $hoy;
    $fin = !empty($f_fin_user) ? $f_fin_user : $hoy;
}

// 2. CONSTRUCCIÓN DE LA CONSULTA CON FILTRO DE SEGURIDAD
$where = "WHERE DATE(m.fecha) BETWEEN '$inicio' AND '$fin'";

if ($tipo) {
    $where .= " AND m.tipo = '$tipo'";
}

// SEGURIDAD: Si no es admin, filtramos forzosamente por su almacén
if ($almacen_usuario > 0) {
    // El usuario solo ve movimientos donde su almacén fue Origen O Destino
    $where .= " AND (m.almacen_origen_id = $almacen_usuario OR m.almacen_destino_id = $almacen_usuario)";
}

$sql = "SELECT m.*, p.nombre as prod, p.sku, p.factor_conversion, p.unidad_reporte,
               a1.nombre as ori_nom, a2.nombre as des_nom, 
               u1.nombre as reg_user, u2.nombre as env_user, u3.nombre as rec_user
        FROM movimientos m 
        INNER JOIN productos p ON m.producto_id = p.id
        LEFT JOIN almacenes a1 ON m.almacen_origen_id = a1.id
        LEFT JOIN almacenes a2 ON m.almacen_destino_id = a2.id
        LEFT JOIN usuarios u1 ON m.usuario_registra_id = u1.id
        LEFT JOIN usuarios u2 ON m.usuario_envia_id = u2.id
        LEFT JOIN usuarios u3 ON m.usuario_recibe_id = u3.id
        $where 
        ORDER BY m.fecha DESC";

$res = $conexion->query($sql);
$data = [];

$badges = [
    'entrada' => 'success',
    'salida' => 'danger',
    'traspaso' => 'primary',
    'ajuste' => 'warning text-dark'
];
// ... (todo tu código anterior igual hasta el while)

while ($row = $res->fetch_assoc()) {
    $data[] = [
        'id' => $row['id'],
        'fecha_format' => date('d/m/Y H:i', strtotime($row['fecha'])),
        'producto' => $row['prod'],
        'sku' => $row['sku'],
        'tipo' => $row['tipo'],
        'color' => $badges[$row['tipo']] ?? 'secondary',
        // ENVIAR EL NÚMERO PURO PARA CÁLCULOS
        'cantidad' => $row['cantidad'], 
        'factor_conversion' => $row['factor_conversion'],
        'unidad_reporte' => $row['unidad_reporte'],
        // ------------------------------------------
        'origen' => $row['ori_nom'] ?? '---',
        'destino' => $row['des_nom'] ?? '---',
        'u_reg' => $row['reg_user'],
        'u_env' => $row['env_user'],
        'u_rec' => $row['rec_user'],
        'obs' => $row['observaciones']
    ];
}


echo json_encode(['data' => $data]);