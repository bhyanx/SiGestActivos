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
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function ConexionBdPruebas(){
        try {
            $conectar = $this->dbh = new PDO("sqlsrv:Server=DESKTOP-5QKJ7QK;Database=SiGestActivos", "", "");
            return $conectar;
        } catch (Exception $e){
            echo "Error en cadena de conexión Conexion(): " . $e->getMessage();
            die();
        }
    }

    // protected function ConexionPhpMyadmin()
    // {
    //     try {
    //         $conectarPHP = $this->dbh = new PDO("mysql:host=localhost;dbname=sis_tareo", 'root', '12345678');  // AMBIENTE QAQC
    //         return $conectarPHP;
    //     } catch (Exception $e) {
    //         print "Error BD MYSQL: " . $e->getMessage() . "</br>";
    //         die();
    //     }
    // }

    // public function set_names()
    // {
    //     return $this->dbh->query("SET NAMES 'utf8'");
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
ini_set('display_errors', 1);
?>
