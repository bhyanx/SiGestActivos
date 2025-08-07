<?php
session_start();
require_once '../config/configuracion.php';
require_once '../models/Roles.php';

$objetoRol = new Roles();
// $CodEmpleado = $_SESSION["CodEmpleado"];;
// $UserUpdate = $_SESSION["UserUpdate"];

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

switch ($action) {
    case "combo":
        $datos = $objetoRol->listarTodo();
        if (is_array($datos) and count($datos) > 0) {
            $html = "<option value=''>Seleccione</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['CodRol'] . "'>" . $row['NombreRol'] . "</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>Selecione</option>";
        }
        break;

    case 'ListarRoles':
        try {
            $data = $objetoRol->listarTodo();
            error_log("ListarRoles: " . json_encode($data), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode($data ?: []);
        } catch (\Throwable $th) {
            error_log("Error ListarRoles: " . $th->getMessage(), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode(['status' => false, 'message' => 'Error al listar roles: ' . $th->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Acción no válida.']);
        break;
}
