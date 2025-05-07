<?php

require_once '../models/GestionarActivos.php';

//* CREACION DE VARIABLES QUE OBTIENEN LOS VALORES DE LOS INPUTS DEL FORMULARIO

$activos = new GestionarActivos();

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

//* USO DE SWITCH CASE PARA REALIZAR ACCIONES EN BASE A LA PETICION DEL USUARIO

switch ($action){

    //* CASE PARA PODER REGISTRAR UNA ACTIVO EN LA BASE DE DATOS
    
    case 'Registrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            try{
                $data = [
                    'IdActivo' => null,
                    'IdDocIngresoAlm' => $_POST['IdDocIngresoAlm'],
                    'IdArticulo' => $_POST['IdArticulo'],
                    'Codigo' => $_POST['Codigo'],
                    'Serie' => $_POST['Serie'],
                    'IdEstado' => $_POST['IdEstado'],
                    'Garantia' => $_POST['Garantia'],
                    'FechaFinGarantia' => $_POST['FechaFinGarantia'],
                    'IdProveedor' => $_POST['IdProveedor'],
                    'Observaciones' => $_POST['Observaciones'],
                    'IdSucursal' => $_POST['IdSucursal'],
                    'IdAmbiente' => $_POST['IdAmbiente'],
                    'IdCategoria' => $_POST['IdCategoria'],
                    'VidaUtil' => $_POST['VidaUtil'],
                    'ValorAdquisicion' => $_POST['ValorAdquisicion'],
                    'FechaAdquisicion' => $_POST['FechaAdquisicion'],
                    'UserMod' => 'admin'
                ];
                $activos->registrarActivos($data);
                echo "Activo registrado con éxito.";
            }catch (Exception $e){
                echo "Error: " . $e->getMessage();
            }
        }

        break;
    
    //* CASE PARA PODER ACTUALIZAR UN ACTIVO EN LA BASE DE DATOS
    //* SE DEBE ENVIAR EL ID DEL ACTIVO A ACTUALIZAR

    case 'Actualizar':
        if ($_SESSION['REQUEST_METHOD'] === 'POST'){
            try{
                $data = [
                    'IdActivo' => $_POST['IdActivo'],
                    'IdDocIngresoAlm' => $_POST['IdDocIngresoAlm'],
                    'IdArticulo' => $_POST['IdArticulo'],
                    'Codigo' => $_POST['Codigo'],
                    'Serie' => $_POST['Serie'],
                    'IdEstado' => $_POST['IdEstado'],
                    'Garantia' => $_POST['Garantia'],
                    'FechaFinGarantia' => $_POST['FechaFinGarantia'],
                    'IdProveedor' => $_POST['IdProveedor'],
                    'Observaciones' => $_POST['Observaciones'],
                    'IdSucursal' => $_POST['IdSucursal'],
                    'IdAmbiente' => $_POST['IdAmbiente'],
                    'IdCategoria' => $_POST['IdCategoria'],
                    'VidaUtil' => $_POST['VidaUtil'],
                    'ValorAdquisicion' => $_POST['ValorAdquisicion'],
                    'FechaAdquisicion' => $_POST['FechaAdquisicion'],
                    'UserMod' => 'admin'
                ];

                $activos->actualizarActivos($data);
                echo "Activo actualizado.";
            }catch (Exception $e){
                echo "Error: " . $e->getMessage();
            }
        }
        break;
    
    //* CASE PARA PODER CONSULTAR UN ACTIVO EN LA BASE DE DATOS
    //* SE DEBE ENVIAR EL ID DEL ACTIVO A CONSULTAR

    case 'Consultar': 
            try{
                $filtros = [
                    'pCodigo' => $_POST['pCodigo'] ?? null,
                    'pIdSucursal' => $_POST['pIdSucursal'] ?? null,
                    'pIdCategoria' => $_POST['pIdCategoria'] ?? null,
                    'pIdEstado' => $_POST['pIdEstado'] ?? null
                ];
                $resultados = $activos->consultarActivos($filtros);
                header('Content-Type: application/json');
                echo json_encode($resultados);
            }catch( Exception $e){
                echo "Error: " . $e->getMessage();
            }
            break;

    default:
        echo "Acción no válida.";
        break;
}

?>
