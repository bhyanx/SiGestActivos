<?php 

require_once("app/config/configuracion.php");

if (!empty($_SESSION['CodUsuario']) && !empty($_SESSION['ClaveAcceso'])){
    header("Location: " . Conectar::ruta() . "app/views/Home/");
    exit();
}else{
    header("Location: " . Conectar::ruta() . "app/views/Login/");
    exit();
} 
ob_end_flush();
?>






