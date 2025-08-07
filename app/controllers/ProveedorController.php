<?php
session_start();
require_once '../config/configuracion.php';
require_once '../models/Proveedores.php';
require_once '../models/Combos.php';

$proveedor = new Proveedores();

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

ini_set('display_errors', 0);

header('Content-Type: application/json');

switch ($action) {
    case 'ListarProveedores':
        try {
            $data = $proveedor->listarTodo();
            error_log("ListarProveedores: " . json_encode($data), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode($data ?: []);
        } catch (Exception $e) {
            error_log("Error Consultar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar proveedores: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Acción no válida.']);
}
