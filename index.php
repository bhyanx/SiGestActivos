<?php 
require_once("config/configuracion.php");
if (isset($_SESSION['idUsuario']) AND isset($_SESSION['contrasenaUsuario']) AND isset($_['rolUsuario'])){
    header("Location: views/Dashboard");
}else{
    header("Location: views/Login");
} ?>






