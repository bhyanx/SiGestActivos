<?php
require_once '../config/configuracion.php';
require_once '../models/ProveedorModels.php';

$objetoProveedor = new ProveedorModels();
$CodEmpleado = $_SESSION["CodEmpleado"];
$UserUpdate = $_SESSION["UserUpdate"];

switch ($_GET['op']){
    case "combo":
        $datos = $objetoProveedor->get_Proveedor();
}

?>