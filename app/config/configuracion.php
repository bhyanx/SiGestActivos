<?php
// Verificar si la sesión ya está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la base de datos
class Conectar
{
    protected $dbh;

    public function ConexionBdPracticante()
    {
        try {
            $conexion = new PDO("sqlsrv:Server=192.168.1.35;Database=bdActivos", "practsistAlfa", "Calichin2025");
            // $conexion = new PDO("sqlsrv:Server=BHYANX;Database=bdActivos", "", "");
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            error_log("Error de conexión a bdActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    // public function ConexionBdLocal(){
    //    $conexion = new PDO("sqlsrv:Server=BHYANX;Database=bdActivos", "", ""); 
    // }

    public static function ruta()
    {
        return 'http://192.168.1.35:8088/sigestActivos/';
    }

    public static function rutaServidor()
    {
        return $_SERVER["DOCUMENT_ROOT"] . '/SiGestActivos/';
    }
}

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 0); // Desactivado para evitar HTML en respuestas JSON
?>