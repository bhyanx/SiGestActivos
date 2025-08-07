<?php
session_start();
require_once '../config/configuracion.php';
require_once '../models/Ambientes.php';

$ambiente = new Ambientes();

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

// Desactivar display_errors para evitar HTML en respuestas JSON
ini_set('display_errors', 0);

header('Content-Type: application/json');

switch ($action) {
    case 'Listar':
        try {
            $cod_empresa = $_POST['cod_empresa'] ?? null;
            $cod_UnidadNeg = $_POST['cod_UnidadNeg'] ?? null;
            $data = $ambiente->listarTodo($cod_empresa, $cod_UnidadNeg);
            error_log("ListarAmbientes resultados: " . print_r($data, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode($data ?: []);
        } catch (Exception $e) {
            error_log("Error Consultar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar ambientes: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Acción no válida.']);
} 