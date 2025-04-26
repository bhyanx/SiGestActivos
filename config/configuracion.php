<?php

session_start();

class Conectar
{

    protected $dbh;

    protected function ConexionBdPracticante()
    {
        try {
            
            $conectar = $this->dbh = new PDO("sqlsrv:Server=optimus;Database=bdActivos", "pracsistAlfa", "Calichin2025"); // CONEXION LOCAL PC PRACTICANTE
            return $conectar;
            echo "Conexion Exitosa";
        } catch (Exception $e) {
            echo "Error en cacdena de conexion Conexion(): " . $e->getMessage();
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

    public function set_names()
    {
        return $this->dbh->query("SET NAMES 'utf8'");
    }

    public static function ruta()
    {
        return 'http://192.168.1.224:8088/sigestActivos/';
    }

    public static function rutaServidor()
    {
        return $_SERVER["DOCUMENT_ROOT"] . '/SiGestActivos/';
    }
}
