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
            $conexion = new PDO("sqlsrv:Server=192.168.1.35;Database=bdActivosV1", "practsistAlfa", "Calichin2025");
            //$conexion = new PDO("sqlsrv:Server=192.168.1.37;Database=bdActivos", "","");
            //$conexion = new PDO("sqlsrv:Server=BHYANX;Database=bdActivos", "", "");
            //$conexion = new PDO("sqlsrv:Server=localhost;Database=bdActivos", "sa", "Bryan260904");
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            error_log("Error de conexión a bdActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function ConexionBdProgSistemas()
    {
        try {
            $conexion = new PDO("sqlsrv:Server=192.168.1.52;Database=bdGestionLubriseng", "Mastlub", "Popeye**2025**");
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            error_log("Error de conexión a bdActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function ConexionBdGestionLubriseng()
    {
        try {
            $conexion = new PDO("sqlsrv:Server=zeus;Database=bdGestionLubriseng", "MastLub", "Popeye**2025**");
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            error_log("Error de conexión a bdGestionLubriseng: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    // public function ConexionBdLocal(){
    //    $conexion = new PDO("sqlsrv:Server=BHYANX;Database=bdActivos", "", ""); 
    // }

    public static function ruta()
    {
        return 'http://192.168.1.54:8088/SiGestActivos/';
        //return 'http://192.168.1.14/';
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
ini_set('display_errors', 1); // Desactivado para evitar HTML en respuestas JSON
?>