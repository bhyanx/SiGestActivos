<?php 

require_once("app/config/configuracion.php");

if (!empty($_SESSION['CodUsuario']) && !empty($_SESSION['ClaveAcceso'])){
    header("Location: " . Conectar::ruta() . "app/views/Home/");
    //header("Location: " . Conectar::rutaLocal() . "app/views/Home/");
    exit();
}else{
    header("Location: " . Conectar::ruta() . "app/views/Login/");
    //header("Location: " . Conectar::rutaLocal() . "app/views/Login/");
    exit();
} 
ob_end_flush();
?>






