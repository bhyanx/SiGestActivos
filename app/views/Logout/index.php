<?php 
ob_start();
session_start();

require_once('../../config/configuracion.php');
session_destroy();
header('Location: ' . Conectar::ruta() . 'app/views/Login/');
exit();
?>