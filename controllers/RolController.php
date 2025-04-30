<?php
require_once '../config/configuracion.php';
require_once '../models/RolModels.php';

$objetoRol = new RolModels();
$CodEmpleado = $_SESSION["CodEmpleado"];;
$UserUpdate = $_SESSION["UserUpdate"];

switch ($_GET['op']){
    case "combo":
        $datos = $objetoRol->get_Rol();
        if (is_array($datos) AND count($datos)>0){
            $html = "<option value=''>Seleccione</option>";
            foreach ($datos as $row){
                $html .= "<option value='".$row['CodRol']."'>".$row['NombreRol']."</option>";
            }
            echo $html;
        }else {
            echo "<option value=''>Selecione</option>";
        }
        break;

    case "listar" :
        $datos = $rol->get_Rol();
        $data = Array();
        $i = 1;
        foreach ($datos as $row){
            $sub_array = array();
            $sub_array[] = $i;
            $sub_array[] = $row['NombreRol'];
            if($row['Estado'] == 1){
                $sub_array[] = '<span class="badge badge-pill badge-success">ACTIVO</span>';
                $sub_array[] = '<div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-cogs"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" onClick="editar('.$row['CodRol'].')" id="'.$row['CodRol'].'" class="btn btn-sm dropdown-item"><i class="fa fa-edit text-warning"></i> Editar</button>
                                    <button type="button" onClick="eliminar('.$row['CodRol'].')" id="'.$row['CodRol'].'" class="btn btn-sm dropdown-item"><i class="fa fa-trash text-danger"></i> Desactivar</button>
                                    <button type="button" onClick="viewpermisos('.$row['CodRol'].')" id="'.$row['CodRol'].'" class="btn btn-sm dropdown-item"><i class="fa fa-unlock text-primary"></i> Permisos</button>
                                </div>
                            </div>';
            }else {
                $sub_array[] = '<span class="badge badge-pill badge-danger">DESACTIVADO</span>';
                $sub_array[] = '<div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-cogs"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <button type="button" onClick="activar('.$row['CodRol'].')" id="'.$row['CodRol'].'" class="btn btn-sm dropdown-item"><i class="fa fa-check text-info"></i> Activar</button>
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
            "aaData"=>$data);
        echo json_encode($results);
        break;

        case "listar_permisos" :
            $datos = $objetoRol->get_Permisos_Rol($_POST['IdRol']);
            $data = Array();
            $i = 1;
            foreach ($datos as $row){
                $sub_array = array();
                $sub_array[] = $i;
                $sub_array[] = $row['NombrePermiso'];
                if($row['Permiso'] == 1){
                    $sub_array[] = '<div class="custom-control custom-switch custom-switch-lg custom-switch-off-danger custom-switch-on-success">
                              <input type="checkbox" checked class="custom-control-input" id="customSwitch'.$i.'" value="'.$row['CodPermiso'].'" onchange="DesactivarPermiso(event,'.$row['CodPermiso'].')">
                              <label class="custom-control-label" for="customSwitch'.$i.'">Activado</label>
                            </div>';
                }else {
                    $sub_array[] = '<div class="custom-control custom-switch custom-switch-lg custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="customSwitch'.$i.'" value="'.$row['CodPermiso'].'" onchange="ActivarPermiso(event,'.$row['CodPermiso'].')">
                            <label class="custom-control-label" for="customSwitch'.$i.'">Desactivado</label>
                          </div>';
                }
                $data[] = $sub_array;
                $i++;
            }

            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData"=>$data);
            echo json_encode($results);
            break;

            case 'obtener' : 
                $datos = $objetoRol->get_Rol_id($_POST['IdRol']);
                if ($datos){
                    $results = array('msg' => true, 'datos' => $datos);
                } else {
                    $results = array('msg' => false, 'datos' => 0);
                }
                echo json_encode($results);
                break;

            case 'guardaryeditar' :
                if(empty($_POST['IdRol']) OR $_POST['IdRol'] == 0){
                    $res = $rol->insert($_POST['NombreRol'], $_POST['Estado'], $CodEmpleado);
                }else{
                    empty($_POST['Estado']) ? $_POST['Estado'] = 0 : $_POST['Estado'] = 1;
                    $res = $rol->update($_POST['IdRol'], $_POST['NombreRol'], $_POST['Estado'], $CodEmpleado);
                }

                echo json_encode($res);
                break;

            
            case 'eliminar' :
                $res = $rol->delete($_POST['IdRol']);
                break;

            case 'activar' :
                $res = $rol->activar($_POST['IdRol']);
                break;

            default:
                # code...
            break;
}   

?>
 [IdRol]
      ,[NombreRol]
      ,[Estado]
      ,[UserUpdate]
      ,[FechaUpdate]