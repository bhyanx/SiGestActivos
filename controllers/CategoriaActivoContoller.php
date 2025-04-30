<?php
require_once '../config/configuracion.php';
require_once '../models/CategoriaActivosModels.php';

$objetoCategoriaActivos = new CategoriaActivosModels();
$CodEmpleado = $_SESSION["CodEmpleado"];
$UserUpdate = $_SESSION["UserUpdate"];

switch ($_GET['op']) {
    case "combo":
        $datos = $objetoCategoriaActivos->get_CategoriaActivos();
        if (is_array($datos) and count($datos) > 0) {
            $html = "<option value=''>Seleccione</option>";
            foreach ($datos as $row) {
                $html .= "<option  value='" . $row['idCategoria'] . "'>" . $row['nombre'] . "</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>Seleccione</option>";
        }
        break;

    case "listar":
        $datos = $objetoCategoriaActivos->get_CategoriaActivos();
        $sub_array = array();
        $sub_array[] = $i;
        $sub_array[] = $row['nombre'];
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $i;
            $sub_array[] = $row['nombre'];
            if ($row['estado'] == 1) {
                $sub_array[] = '<span class="badge badge-pill badge-success">ACTIVO</span>';
                $sub_array[] = '<div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cogs"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <button type="button" onClick="editar(' . $row['CodRol'] . ')" id="' . $row['CodRol'] . '" class="btn btn-sm dropdown-item"><i class="fa fa-edit text-warning"></i> Editar</button>
                                        <button type="button" onClick="eliminar(' . $row['CodRol'] . ')" id="' . $row['CodRol'] . '" class="btn btn-sm dropdown-item"><i class="fa fa-trash text-danger"></i> Desactivar</button>
                                        <button type="button" onClick="viewpermisos(' . $row['CodRol'] . ')" id="' . $row['CodRol'] . '" class="btn btn-sm dropdown-item"><i class="fa fa-unlock text-primary"></i> Permisos</button>
                                    </div>
                                </div>';
            } else {
                $sub_array[] = '<span class="badge badge-pill badge-danger">DESACTIVADO</span>';
                $sub_array[] = '<div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-cogs"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <button type="button" onClick="activar(' . $row['IdCategoria'] . ')" id="' . $row['IdCategoria'] . '" class="btn btn-sm dropdown-item"><i class="fa fa-check text-info"></i> Activar</button>
                                    </div>
                                </div>';
            }

            $data[] = $sub_array;
            $i++;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case "listar_permisos":
        $datos = $objetoCategoriaActivos->get_CategoriaActivos();
        $data = array();
        $i = 1;
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $i;
            $sub_array[] = $row['nombre'];
            if ($row['Categoria'] == 1) {
                $sub_array[] = '<div class="custom-control custom-switch custom-switch-lg custom-switch-off-danger custom-switch-on-success">
                              <input type="checkbox" checked class="custom-control-input" id="customSwitch' . $i . '" value="' . $row['IdCategoria'] . '" onchange="DesactivarPermiso(event,' . $row['IdCategoria'] . ')">
                              <label class="custom-control-label" for="customSwitch' . $i . '">Activado</label>
                            </div>';
            } else {
                $sub_array[] = '<div class="custom-control custom-switch custom-switch-lg custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="customSwitch' . $i . '" value="' . $row['IdCategoria'] . '" onchange="ActivarPermiso(event,' . $row['IdCategoria'] . ')">
                            <label class="custom-control-label" for="customSwitch' . $i . '">Desactivado</label>
                          </div>';
            }
            $data[] = $sub_array;
            $i++;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'obtener':
        $datos = $objetoCategoriaActivos->get_CategoriaActivos_id($_POST['IdCategoria']);
        if ($datos) {
            $results = array('msg' => true, 'datos' => $datos);
        } else {
            $results = array('msg' => false, 'datos' => 0);
        }
        echo json_encode($results);
        break;

    case 'guardaryeditar':
        if (empty($_POST['IdCategoria']) or $_POST['IdCategoria'] == 0) {
            $res = $objetoCategoriaActivos->insert($_POST['nombre'], $_POST['descripcion'], $_POST['vidaUtilEstandar'], $_POST['estado'], $CodEmpleado);
        } else {
            empty($_POST['estado']) ? $_POST['estado'] = 0 : $_POST['estado'] = 1;
        }

        echo json_encode($res);
        break;

    case 'eliminar':
        $res = $objetoCategoriaActivos->delete($_POST['IdCategoria']);
        break;

    case 'activar':
        $res = $objetoCategoriaActivos->activar($_POST['IdCategoria']);
        break;

    default:

        break;
}
?>
[idCategoria]
,[nombre]
,[descripcion]
,[vidaUtilEstandar]
,[estado]
,[fechaRegistro]
,[fechaMod]
,[userMod]