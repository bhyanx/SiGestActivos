<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//require_once("../config/configuracion.php");
require_once("../models/Usuarios.php");

$usuario = new Usuarios();

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

//* USO DE SWITCH CASE PARA REALIZAR ACCIONES EN BASE A LA PETICION DEL USUARIO

switch ($action) {

    //* CASE PARA PODER INICIAR SESSIÓN EN EL SISTEMA, DEPENDIENDO DE SUS ROLES Y PERMISOS

    case 'AccesoUsuario':
        header('Content-Type: application/json');
        $datos = $usuario->login($_POST["CodUsuario"], $_POST["ClaveAcceso"]);
        if ($datos && is_array($datos)) {
            $row = $datos[0];
            
            // Limpiar sesión anterior
            session_unset();
            session_destroy();
            session_start();
            
            // Debug - Verificar datos antes de guardar en sesión
            error_log("Datos a guardar en sesión: " . print_r($row, true));
            
            // Guardar datos en la sesión
            $_SESSION["CodUsuario"] = $row["CodUsuario"] ?? '';
            $_SESSION["CodEmpleado"] = $row["CodEmpleado"] ?? '';
            $_SESSION["IdRol"] = $row["IdRol"] ?? '';
            $_SESSION["UrlUltimaSession"] = $row["UrlUltimaSession"] ?? '';
            $_SESSION["ClaveAcceso"] = $row["ClaveAcceso"] ?? '';
            $_SESSION["NombreTrabajador"] = $row["NombreTrabajador"] ?? '';
            $_SESSION["PrimerNombre"] = $row["PrimerNombre"] ?? '';
            $_SESSION["SegundoNombre"] = $row["SegundoNombre"] ?? '';
            $_SESSION["ApellidoPaterno"] = $row["ApellidoPaterno"] ?? '';
            $_SESSION["ApellidoMaterno"] = $row["ApellidoMaterno"] ?? '';
            
            // Debug - Verificar datos guardados en sesión
            error_log("Datos guardados en sesión: " . print_r($_SESSION, true));
            
            // Cargar los permisos del usuario
            $datapermisos = $usuario->leerMenuRol($_SESSION['IdRol']);
            if (is_array($datapermisos) && count($datapermisos) > 0) {
                $_SESSION['Permisos'] = $datapermisos;
                
                // Verificar si el usuario tiene permisos para acceder
                $tienePermisos = false;
                foreach ($datapermisos as $permiso) {
                    if ($permiso['Permiso'] == 1) {
                        $tienePermisos = true;
                        break;
                    }
                }

                if ($tienePermisos) {
                    if (empty($row["UrlUltimaSession"])) {
                        $result = array('status' => true, 'msg' => '/app/views/Home/');
                    } else {
                        $result = array('status' => true, 'msg' => $row["UrlUltimaSession"]);
                    }
                } else {
                    $result = array('status' => false, 'msg' => 'No tiene permisos para acceder al sistema');
                }
            } else {
                $result = array('status' => false, 'msg' => 'No se encontraron permisos para el usuario');
            }
        } else {
            $result = array('status' => false, 'msg' => 'Usuario o contraseña incorrectos');
        }
        echo json_encode($result);
        exit();
        break;


    //* CASE PARA PODER LEER CUALQUIER USUARIO SOLO INGRESANDO SU CODIGO DE USUARIO(DNI)

    case 'LeerUsuarioId':
        $data = $usuario->listarPorCodUsuario($_POST["CodUsuario"]);
        if ($data) {
            $response = array('status' => true, 'data' => $data);
        } else {
            $response = array('status' => false, 'data' => $data);
        }
        echo json_encode($response);
        break;


    //* CASE PARA PODER LEER TODOS LOS USUARIOS QUE SE ENCUENTRAN EN LA BASE DE DATOS
    case 'LeerUsuarios':
        try {
            $data = $usuario->listarTodo();
            error_log("LeerUsuarios resultados: " . print_r($data, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode($data ?: []);
        } catch (Exception $e) {
            error_log("Error LeerUsuarios: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar usuarios: ' . $e->getMessage()]);
        }
        break;

    default:
        $result = array('status' => false, 'msg' => 'No se encontraron permisos para el usuario');
        echo json_encode($result);
        break;
}

?>



USE [bdActivos]
GO

SELECT u.CodUsuario, e.PrimerNombre + ' ' + e.SegundoNombre AS Nombres,
	   e.ApellidoPaterno + ' ' +e.ApellidoMaterno AS Apellidos
FROM tUsuarios u
INNER JOIN vEmpleados e ON u.CodUsuario = e.codTrabajador