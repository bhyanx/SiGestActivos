<?php
session_start();
require_once '../config/configuracion.php';
require_once '../models/Menu.php';

$objetoMenu = new Menu();

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

switch ($action) {
    case 'ListarMenu':
        # code...
        try {
            //code...
            $data = $objetoMenu->listarTodo();
            error_log("ListarMenu: " . json_encode($data), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode($data ?: []);
        } catch (\Throwable $th) {
            //throw $th;
            error_log("Error ListarMenu: " . $th->getMessage(), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode(['status' => false, 'message' => 'Error al listar menu: ' . $th->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Acción no válida.']);
        break;
}
