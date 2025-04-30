<?php 

require_once("config/configuracion.php");

if (!empty($_SESSION['CodUsuario']) && !empty($_SESSION['ClaveAcceso'])){
    header("Location: views/Dashboard");
}else{
    header("Location: views/Login");
} ?>






