<?php
session_start();
require_once '../config/configuracion.php';
require_once '../models/Sucursales.php';
require_once '../models/Combos.php';

$sucursal = new Sucursales();

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

// Desactivar display_errors para evitar HTML en respuestas JSON
ini_set('display_errors', 0);

header('Content-Type: application/json');

switch ($action) {
    case 'Listar':
        try {
            $data = $sucursal->listarTodo();
            error_log("LeerUsuarios resultados: " . print_r($data, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode($data ?: []);
        }catch(Exception $e) {
            error_log("Error Consultar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar sucursales: ' . $e->getMessage()]);
        }
        break;

    default:
        $result = array('status' => false, 'msg' => 'No se encontraron permisos para el usuario');
        echo json_encode($result);
        break;
}



// switch ($_GET['op']) {
//     case "combo":
//         $datos = $objetoUnidadNegocio->get_UnidadNegocio($_POST['idSucursal']);
//         if (is_array($datos) and count($datos) > 0) {
//             $html = "<option value=''>Seleccione</option>";
//             foreach ($datos as $row) {
//                 $html .= "<option value='" . $row['CodUnidadNeg'] . "'>" . $row['nombre'] . "</option>";
//             }
//             echo $html;
//         } else {
//             echo "<option value=''>Seleccione</option>";
//         }
//         break;

//     case "listar":
//         $datos = $objetoUnidadNegocio->get_UnidadNegocio_id($_POST['cod_empresa']);
//         $data = array();
//         $i = 1;
//         foreach ($datos as $row) {
//             $sub_array = array();
//             $sub_array[] = $i;
//             $sub_array[] = $row['cod_empresa'];
//             $sub_array[] = $row['Razon_empresa'];
//             $sub_array[] = $row['cod_UnidadNeg'];
//             $sub_array[] = $row['Nombre_local'];
//             if ($row['estadoFuncionamiento'] == 1) {
//                 $sub_array[] = '<span class="badge badge-pill badge-success">Activo</span>';
//                 $sub_array[] = '<div class="btn-group" role="group">
//                                 <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
//                                     <i class="fa fa-cogs"></i>
//                                 </button>
//                                 <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
//                                     <button type="button" onClick="editar(' . $row['cod_UnidadNeg'] . ')" id="' . $row['cod_UnidadNeg'] . '" class="btn btn-sm dropdown-item"><i class="fa fa-edit text-warning"></i> Editar</button>
//                                     <button type="button" onClick="eliminar(' . $row['cod_UnidadNeg'] . ')" id="' . $row['cod_UnidadNeg'] . '" class="btn btn-sm dropdown-item"><i class="fa fa-trash text-danger"></i> Eliminar</button>
//                                 </div>
//                             </div>';
//             } else {
//                 $sub_array[] = '<span class="badge badge-pill badge-danger">DESACTIVADO</span>';
//                 $sub_array[] = '<div class="btn-group" role="group">
//                                 <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
//                                     <i class="fa fa-cogs"></i>
//                                 </button>
//                                 <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
//                                     <button type="button" onClick="activar(' . $row['cod_UnidadNeg'] . ')" id="' . $row['cod_UnidadNeg'] . '" class="btn btn-sm dropdown-item"><i class="fa fa-check text-info"></i> Activar</button>
//                                 </div>
//                             </div>';
//             }
//             $data[] = $sub_array;
//             $i++;
//         }
//         $results = array(
//             "sEcho" => 1,
//             "iTotalRecords" => count($data),
//             "iTotalDisplayRecords" => count($data),
//             "aaData" => $data
//         );
//         echo json_encode($results);
//         break;

//     default:
//         # code...
//         break;
// }


// ?>
