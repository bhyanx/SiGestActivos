<?php

// Al inicio de UsuarioController.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../config/configuracion.php';
require_once __DIR__ . '/../models/Usuarios.php';
require_once __DIR__ . '/../models/Combos.php';

$usuario = new Usuarios();
$combo = new Combos();

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
            // $_SESSION["UrlUltimaSession"] = $row["UrlUltimaSession"] ?? '';
            $_SESSION["ClaveAcceso"] = $row["ClaveAcceso"] ?? '';
            $_SESSION["NombreTrabajador"] = $row["NombreTrabajador"] ?? '';
            $_SESSION["PrimerNombre"] = $row["PrimerNombre"] ?? '';
            $_SESSION["SegundoNombre"] = $row["SegundoNombre"] ?? '';
            $_SESSION["ApellidoPaterno"] = $row["ApellidoPaterno"] ?? '';
            $_SESSION["ApellidoMaterno"] = $row["ApellidoMaterno"] ?? '';


            // En tu controlador, después del login exitoso:
            $_SESSION['cod_empresa'] = $_POST['CodEmpresa'] ?? '';
            $_SESSION['cod_UnidadNeg'] = $_POST['CodUnidadNegocio'] ?? '';

            // Luego usa el modelo para obtener los nombres:
            $_SESSION['Razon_empresa'] = !empty($_SESSION['cod_empresa'])
                ? $usuario->obtenerNombreEmpresa($_SESSION['cod_empresa'])
                : '';

            $_SESSION['Nombre_local'] = !empty($_SESSION['cod_UnidadNeg'])
                ? $usuario->obtenerNombreUnidadNegocio($_SESSION['cod_UnidadNeg'])
                : '';

            // Debug - Verificar datos guardados en sesión
            error_log("Datos guardados en sesión: " . print_r($_SESSION, true));
            // error_log("Datos de login: " . print_r($row, true));

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
                    $redirect_url = '';
                    if (!empty($row["UrlUltimaSession"])) {
                        // Limpiar la ruta de la BD y construir una URL absoluta
                        // $cleaned_url_ultima_session = ltrim($row["UrlUltimaSession"], './');
                        $redirect_url = Conectar::ruta() . 'app/views/' . $cleaned_url_ultima_session;
                    } else {
                        // Redirigir a la ruta base de Home si no hay UrlUltimaSession
                        $redirect_url = Conectar::ruta() . 'app/views/Home/';
                    }
                    $result = array('status' => true, 'msg' => $redirect_url);
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

    case "combos":
        try {
            // Empresas
            $empresas = $combo->comboEmpresa();
            $combos['empresas'] = '<option value="">Seleccione</option>';
            foreach ($empresas as $row) {
                $combos['empresas'] .= "<option value='{$row['cod_empresa']}'>{$row['Razon_empresa']}</option>";
            }

            $rol = $combo->comboRol();
            $combos['roles'] = '<option value="">Seleccione</option>';
            foreach ($rol as $row) {
                $combos['roles'] .= "<option value='{$row['IdRol']}'>{$row['NombreRol']}</option>";
            }


            error_log("Combos generados: " . print_r($combos, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode(['status' => true, 'data' => $combos, 'message' => 'Combos cargados correctamente.']);
        } catch (\PDOException $e) {
            error_log("Error Combos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar combos: ' . $e->getMessage()]);
        }
        break;

    case "unidadnegocio":
        try {
            $codEmpresa = $_POST['cod_empresa'] ?? '';
            $unidadNegocio = $combo->comboUnidadNegocio($codEmpresa);
            $comboUnidad = '<option value="">Seleccione</option>';
            foreach ($unidadNegocio as $row) {
                $comboUnidad .= "<option value='{$row['cod_UnidadNeg']}'>{$row['Nombre_local']}</option>";
            }
            echo json_encode(['status' => true, 'data' => $comboUnidad]);
        } catch (\PDOException $e) {
            echo json_encode(['status' => false, 'message' => 'Error al cargar unidades de negocio: ' . $e->getMessage()]);
        }
        break;

    default:
        $result = array('status' => false, 'msg' => 'No se encontraron permisos para el usuario');
        echo json_encode($result);
        break;
}
