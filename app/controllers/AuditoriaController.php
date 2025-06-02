<?php
session_start();
require_once '../config/configuracion.php';
require_once '../models/Auditorias.php';
require_once '../models/Combos.php';

$auditoria = new Auditorias();
$combos = new Combos();

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

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

    case 'obtenerHistorialAuditoria':
        try {
            $usuario = $_POST['usuario'];
            $info = $auditoria->ListarHistorialLogs($usuario);
            if ($info === false) {
                echo json_encode([
                    'status' => false,
                    'message' => 'No se encontró historial para el usuario especificado',
                    'data' => []
                ]);
            } else {
                echo json_encode([
                    'status' => true,
                    'message' => 'Historial obtenido correctamente',
                    'data' => $info
                ]);
            }
        } catch (Exception $e) {
            error_log("Error en obtenerHistorialAuditoria: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode([
                'status' => false,
                'message' => 'Error al obtener el historial: ' . $e->getMessage(),
                'data' => []
            ]);
        }
        break;

    case 'combos':
        try {
            $accionesAuditoria_data = $combos->comboAccionesAuditoria();
            $html_accionesAuditoria = '<option value="">Seleccione</option>';
            foreach ($accionesAuditoria_data as $row) {
                if (isset($row['accion'])) {
                    $html_accionesAuditoria .= "<option value='{$row['accion']}'>{$row['accion']}</option>";
                }
            }
            echo json_encode(['status' => true, 'data' => ['accionesAuditoria' => $html_accionesAuditoria]]);
        } catch (Exception $e) {
            error_log("Error Combos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/error.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar combos: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Acción no encontrada']);
        break;
}
