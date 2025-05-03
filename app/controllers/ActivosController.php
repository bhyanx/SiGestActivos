<?php
require_once '../config/configuracion.php';
require_once '../models/ActivosModels.php';

$objetoActivos = new ActivosModels();
$CodEmpleado = $_SESSION["CodEmpleado"];
$UserUpdate = $_SESSION["UserUpdate"];

switch ($_GET['op']) {
    case "combo":
        $datos = $objetoActivos->get_Activos($idSucursal);
        if (is_array($datos) and count($datos) > 0) {
            $html = "<option value=''>Seleccione</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['idActivo'] . "'>" . $row['NombreArticulo'] . "</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>Seleccione</option>";
        }
        break;

    case "listar":
        $datos = $objetoActivos->get_Activos($idSucursal);
        $sub_array = array();
        $sub_array[] = $i;
        $sub_array[] = $row['NombreArticulo'];
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $i;
            $sub_array[] = $row['NombreArticulo'];
            if ($row['IdEstado'] == 1) {
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
                                        <button type="button" onClick="activar(' . $row['idActivo'] . ')" id="' . $row['idActivo'] . '" class="btn btn-sm dropdown-item"><i class="fa fa-check text-info"></i> Activar</button>
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

    case "listarActivos":
        $datos = $objetoActivos->get_Activos($idSucursal);
        $data = array();
        $i = 1;
        foreach ($datos as $row) {
            $sub_array = array();
            $objetoActivos[] = $i;
            $sub_array[] = $row['NombreArticulo'];
            if ($row['idCategoria'] == 1) {
                $sub_array[] = '<div class="custom-control custom-switch custom-switch-lg custom-switch-off-danger custom-switch-on-success">
                              <input type="checkbox" checked class="custom-control-input" id="customSwitch' . $i . '" value="' . $row['idactivo'] . '" onchange="DesactivarPermiso(event,' . $row['idactivo'] . ')">
                              <label class="custom-control-label" for="customSwitch' . $i . '">Activado</label>
                            </div>';
            } else {
                $sub_array[] = '<div class="custom-control custom-switch custom-switch-lg custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="customSwitch' . $i . '" value="' . $row['idactivo'] . '" onchange="ActivarPermiso(event,' . $row['idactivo'] . ')">
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
            $datos = $objetoActivos->get_Activos_id($_POST['idactivo']);
            if ($datos){
                $results = array('msg' => true,
                'datos' => $datos);
            } else {
                $results = array('msg' => false, 'datos' => 0);
            }
            echo json_encode($results);
            break;

        case 'guardaryeditar':
            if(empty($_POST['idActivos']) or $_POST['idActivos'] == 0){
                $res = $objetoActivos->insert(
                    $_POST['idDocIngresoAlm'], 
                    $_POST['idArticulo'], 
                    $_POST['codigo'], 
                    $_POST['serie'], 
                    $_POST['idEstado'], 
                    $_POST['enUso'], 
                    $_POST['esCompuesto'], 
                    $_POST['idActivoPadre'], 
                    $_POST['idSucursal'], 
                    $_POST['idAmbiente'], 
                    $_POST['idCategoria'], 
                    $_POST['vidaUtil'], 
                    $_POST['fechaAdquisicion'], 
                    $_POST['garantia'], 
                    $_POST['fechaFinGarantia'], 
                    $_POST['idProveedor'], 
                    $_POST['observaciones']
                );
            } else {
                empty($_POST['idEstado']) ? $_POST['idEstado'] = 0 : $_POST['idEstado'] = 1;
            }

            echo json_encode($res);
            break;
    
        case 'eliminar':
            $res = $objetoActivos->delete($_POST['idActivos']);
            break;

        case 'activar':
            $res = $objetoActivos->activar($_POST['idActivos']);
            break;
    
        default:
            # code...
            break;
}

?>
