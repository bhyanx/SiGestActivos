<?php
session_start();
require_once '../config/configuracion.php';
require_once '../models/Auditorias.php';
require_once '../models/Combos.php';

$auditoria = new Auditorias();
$combos = new Combos();

$action = $_GET['action'] ?? ['action'] ?? 'Consultar';

ini_set('display_errors', 0);

header('Content-Type: application/json');

switch ($action) {
    case 'Consultar':
        try {
            $data = $auditoria->ListarLogs();
            error_log("ListarLogs resultados: " . print_r($data, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode($data ?: []);
        } catch (Exception $e) {
            error_log("Error ListarLogs: " . $e->getMessage(), 3, __DIR__ . '/../../logs/error.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar los datos: ' . $e->getMessage()]);
        }
        break;

    case 'combos':
        try {
            //code...
            $accionesAuditoria = $combos->comboAccionesAuditoria();
            $combos['accionesAuditoria'] = '<option value="">Seleccione</option>';
            foreach ($estadoActivo as $row) {
                $combos['accionesAuditoria'] .= "<option value='{$row['accion']}'>{$row['accion']}</option>";
            }
        } catch (Exception $e) {
            //Exception $e;
            error_log("Error Combos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/error.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar combos: ' . $e->getMessage()]);
        }
        break;

    default:
        # code...
        echo json_encode(['status' => false, 'message' => 'Acción no encontrada']);
        break;
}
