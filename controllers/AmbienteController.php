<?php
require_once("../config/configuracion.php");
require_once("../models/AmbienteModels.php");

$ambiente = new AmbienteModels();
$config = new Conectar();
$fechaActual = date("Y-m-d");

switch ($_GET['op']) {
    case 'get_ambiente_id':
        $data = $usuario->get_ambiente_id($_POST["IdAmbiente"]);
        if ($data) {
            $response = array('status' => true, 'data' => $data);
        } else {
            $response = array('status' => false, 'data' => $data);
        }
        echo json_encode($response);
        break;

    case 'get_ambientes':
        $datos = $ambiente->get_Ambiente($idSucursal);
        if ($datos) {
            $data = array();
            $i = 1;

            foreach ($datos as $row) {
                $sub_array = array();
                $sub_array[] = $i;
                $sub_array[] = '<div class="btn-group" role="group">                          
                            <div class="btn-group" role="group">
                              <button id="btnGroupDrop1" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-cogs"></i> 
                              </button>
                              <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">   
                                <button class="btn dropdown-item" type="button" onclick="editar(event,\'' . $row['IdAmbiente'] . '\')"><i class="fa fa-edit"></i> Editar</button>   
                              </div>
                            </div>
                          </div>';
                $sub_array[] = $row['IdAmbiente'];
                $sub_array[] = $row['nombre'];
                $sub_array[] = $row['descripcion'];
                $sub_array[] = $row['idSucursal'];
                if ($row['Activo'] == 1) {
                    $sub_array[] = '<div class="custom-control custom-switch custom-switch-lg custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" checked class="custom-control-input" id="customSwitch' . $i . '" value="' . $row['IdAmbiente'] . '" onclick="eliminar(event,\'' . $row['IdAmbiente'] . '\')">
                                <label class="custom-control-label" for="customSwitch' . $i . '">Activado</label>
                              </div>';
                } else {
                    $sub_array[] = '<div class="custom-control custom-switch custom-switch-lg custom-switch-off-danger custom-switch-on-success">
                              <input type="checkbox" class="custom-control-input" id="customSwitch' . $i . '" value="' . $row['IdAmbiente'] . '" onclick="activar(event,\'' . $row['IdAmbiente'] . '\')">
                              <label class="custom-control-label" for="customSwitch' . $i . '">Desactivado</label>
                            </div>';
                }

                $data[] = $sub_array();
                $i++;
            }

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
            );
        }

        echo json_encode($results);
        break;

    case 'guardaryeditar':
        if (empty($_POST['IdAmbiente']) or $_POST['IdAmbiente'] == 0) {
            $res = $ambiente->insert($_POST['nombre'], $_POST['descripcion'], $_POST['idSucursal'], $_POST['estado'], $CodEmpleado);
        } else {
            empty($_POST['estado']) ? $_POST['estado'] = 0 : $_POST['estado'] = 1;
            $res = $ambiente->update($_POST['IdAmbiente'], $_POST['nombre'], $_POST['descripcion'], $_POST['idSucursal'], $_POST['estado'], $CodEmpleado);
        }

        echo json_encode($res);
        break;

    case 'eliminar':
        $res = $ambiente->delete($_POST['IdAmbiente']);
        echo json_encode($res);
        break;

    case 'activar':
        $res = $ambiente->activar($_POST['IdAmbiente']);
        break;

    default:

        break;
}
?>

[idAmbiente]
,[nombre]
,[descripcion]
,[idSucursal]
,[estado]
,[fechaRegistro]
,[fechaMod]
,[userMod]