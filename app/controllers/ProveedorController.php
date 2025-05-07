<?php
//require_once '../config/configuracion.php';
require_once '../models/Proveedores.php';

$proveedor = new Proveedores();

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

switch ($action){
    // case 'RegistrarProveedores':
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    //         try{
    //             $data = [
    //                 'IdProveedor' => null,
    //                 'Nombre' => $_POST['Nombre'],
    //                 'Ruc' => $_POST['Ruc'],
    //                 'Telefono' => $_POST['Telefono'],
    //                 'Email' => $_POST['Email'],
    //                 'Direccion' => $_POST['Direccion'],
    //                 'Observaciones' => $_POST['Observaciones'],
    //                 'UserMod' => $_SESSION['CodEmpleado'],
    //             ];
    //             $proveedor->registrarProveedores($data);
    //             echo "Proveedor registrado con éxito.";
    //         }catch(PDOException $e){
    //             echo "Error: " . $e->getMessage();
    //         }
    //     }
    //     break;

    case 'ListarProveedores':
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            try{
                $data = $proveedor->listarTodo();
                echo json_encode($data);
            }catch(PDOException $e){
                echo "Error: " . $e->getMessage();
            }
        }

}



?>