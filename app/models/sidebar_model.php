<?php
class SidebarModel {
    public static function obtenerMenu() {
        // Definimos todos los módulos posibles
        $modulos = [
            'inicio'   => ['label' => 'Inicio', 'icon' => 'bi-house-door', 'url' => 'inicio.php'],
            'ventas'   => ['label' => 'Ventas', 'icon' => 'bi-cart-check', 'url' => 'ventasController.php'],
            'almacenes'=> ['label' => 'Almacén', 'icon' => 'bi-box-seam', 'url' => 'almacenes.php'],
            'movimientos'=> ['label' => 'Movimientos', 'icon' => 'bi-arrow-left-right', 'url' => 'movimientos.php'],
            'usuarios' => ['label' => 'Usuarios', 'icon' => 'bi-people', 'url' => 'usuarios.php'],
            'compras'  => ['label' => 'Compras', 'icon' => 'bi-bag-check', 'url' => 'compras.php'],
            'clientes' => ['label' => 'Clientes', 'icon' => 'bi-person-lines-fill', 'url' => 'clientes.php'],
            'finanzas' => ['label' => 'Finanzas', 'icon' => 'bi-graph-up-arrow', 'url' => 'finanzas.php']
        ];

        $permitidos = [];
        foreach ($modulos as $key => $info) {
            if (puedeVerModulo($key)) { // Tu función de permisos.php
                $permitidos[] = $info;
            }
        }
        return $permitidos;
    }
}