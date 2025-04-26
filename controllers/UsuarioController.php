<?php
require_once("../config/configuracion.php");
require_once("../models/UsuarioModels.php");

$usuario = new UsuarioModels();
$config = new Conectar();
$fechaActual = date("Y-m-d");

switch ($_GET['op']) {
    case 'login':
        $datos = $usuario->get_login($_POST["codUsuario"], $_POST["Contrasena"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $_SESSION["CodUsuario"] = $row["CodUsuario"];
                $_SESSION["CodEmpleado"] = $row["CodEmpleado"];
                $_SESSION["ClaveAcceso"] = $row["ClaveAcceso"];
                $_SESSION["IdRol"] = $row["IdRol"];
                $_SESSION["ApellidoPaterno"] = $row["ApellidoPaterno"];
                $_SESSION["ApellidoMaterno"] = $row["ApellidoMaterno"];
                $_SESSION["PrimerNombre"] = $row["PrimerNombre"];
                $_SESSION["SegundoNombre"] = $row["SegundoNombre"];
                $_SESSION["NombreTrabajador"] = $row["NombreTrabajador"];
                $_SESSION["CorreoPersonal"] = $row["CorreoPersonal"];
                $_SESSION["FechaNacimiento"] = date("Y-m-d", strtotime($row["fechaNacimiento"]));
                $_SESSION["CodGenero"] = $row["CodGenero"];
                $_SESSION["Celular"] = $row["Celular"];
                $_SESSION["UrlUltimaSession"] = $row["UrlUltimaSession"];
                $_SESSION["Foto"] = $row["Foto"];
                $_SESSION["Firma"] = $row["Firma"];
            }

            if (empty($row["UrlUltimaSession"])) {
                $result = array('status' => true, 'msg' => '../Home/');
            } else {
                $result = array('status' => true, 'msg' => $row["UrlUltimaSession"]);
            }

            $datapermisos = $usuario->get_menu_rol($_SESSION['IdRol']);
            if (is_array($datapermisos) == true and count($datapermisos) > 0) {
                $_SESSION['Permisos'] = $datapermisos;
            } else {
                $result = array('status' => false, 'msg' => 'No se encontraron permisos para el usuario');
            }
        } else {
            $result = array('status' => false, 'msg' => 'Usuario o contraseña incorrectos');
        }
        echo json_encode($result);
        break;

    case 'get_usuario_id':
        $data = $usuario->get_usuarios_id($_POST["codUsuario"]);
        if ($data) {
            $response = array('status' => true, 'data' => $data);
        } else {
            $response = array('status' => false, 'data' => $data);
        }
        echo json_encode($response);
        break;


    case 'get_usuarios':
        $datos = $usuario->get_usuarios();
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
                                <button class="btn dropdown-item" type="button" onclick="editar(event,\'' . $row['CodUsuario'] . '\')"><i class="fa fa-edit"></i> Editar</button>   
                              </div>
                            </div>
                          </div>';
                $sub_array[] = $row['NombreTrabajador'];
                $sub_array[] = $row['CodUsuario'];
                $sub_array[] = $row['NombreRol'];
                $sub_array[] = $row['CorreoPersonal'];
                if ($row['Activo'] == 1) {
                    $subArray[] = '<div class="custom-control custom-switch custom-switch-lg custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" checked class="custom-control-input" id="customSwitch' . $i . '" value="' . $row['CodUsuario'] . '" onclick="eliminar(event,\'' . $row['CodUsuario'] . '\')">
                                <label class="custom-control-label" for="customSwitch' . $i . '">Activado</label>
                              </div>';
                } else {
                    $sub_array[] = '<div class="custom-control custom-switch custom-switch-lg custom-switch-off-danger custom-switch-on-success">
                              <input type="checkbox" class="custom-control-input" id="customSwitch' . $i . '" value="' . $row['CodUsuario'] . '" onclick="activar(event,\'' . $row['CodUsuario'] . '\')">
                              <label class="custom-control-label" for="customSwitch' . $i . '">Desactivado</label>
                            </div>';
                }

                $data[] = $sub_array;
                $i++;
            }

            $result = array();
        }
        echo json_encode($result);
        break;

    default:
        break;
}
