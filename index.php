<?php 

require_once("app/config/configuracion.php");

if (!empty($_SESSION['CodUsuario']) && !empty($_SESSION['ClaveAcceso'])){
    header("Location: app/views/Dashboard");
}else{
    header("Location: app/views/Login");
} 

?>






