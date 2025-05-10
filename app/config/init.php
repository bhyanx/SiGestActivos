<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir archivos de configuración necesarios
require_once __DIR__ . '/configuracion.php';

?> 