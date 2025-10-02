<?php
// index.php (VERSIÓN FINAL CORREGIDA)

// Habilitar la visualización de errores (esto está bien)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Cargar configuración de BD (Solo una vez)
$config = require_once 'config/config.php'; 

// 2. Cargar el controlador
require_once 'app/Controllers/CalculoController.php';

// 3. POO: Instancia el objeto Controlador una sola vez
$controller = new CalculoController($config);

// 4. Enrutamiento: determina qué acción del controlador ejecutar
$action = $_GET['action'] ?? 'index';

if ($action === 'calcular' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // DEBUG: Ejecutando acción CALCULAR (Método POST correcto).
    
    // Si se envió el formulario, llama al método calcular() del Controlador
    $controller->calcular();
    
} else {
    
    // DEBUG: Ejecutando acción INDEX (Método GET o POST de recarga de tabla).
    
    // Por defecto, llama al método index() para mostrar el formulario
    $controller->index();
}
