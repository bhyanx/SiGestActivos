<?php
session_start();

require_once '../config/configuracion.php';
require_once '../models/Dashboard.php';

$Dashboard = new Dashboard();

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

switch ($action) {
    case 'ConsultarResumenActivos':
        try {
            $filtros = [
                'pIdArticulo' => $_POST['IdArticulo'] ?? null,
                'pCodigo' => $_POST['pCodigo'] ?? null,
                'pIdEmpresa' => $_SESSION['cod_empresa'] ?? null,
                'pIdSucursal' => $_SESSION['cod_UnidadNeg'] ?? null,
                'pIdCategoria' => $_POST['pIdCategoria'] ?? null,
                'pIdEstado' => $_POST['pIdEstado'] ?? null
            ];
            error_log("Filtros procesados: " . print_r($filtros, true), 3, __DIR__ . '/../../logs/debug.log');
            $resultados = $Dashboard->consultarResumenActivos($filtros);
            error_log("Consultar resultados: " . print_r($resultados, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode($resultados ?: []);
        } catch (Exception $e) {
            error_log("Error Consultar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar activos: ' . $e->getMessage()]);
        }
        break;

    case 'ConteoDashboard':
        try {
            $filtros = [
                'pIdArticulo' => $_POST['IdArticulo'] ?? null,
                'pCodigo' => $_POST['pCodigo'] ?? null,
                'pIdEmpresa' => $_SESSION['cod_empresa'] ?? null,
                'pIdSucursal' => $_SESSION['cod_UnidadNeg'] ?? null,
                'pIdCategoria' => $_POST['pIdCategoria'] ?? null,
                'pIdEstado' => $_POST['pIdEstado'] ?? null
            ];
            error_log("Filtros procesados: " . print_r($filtros, true), 3, __DIR__ . '/../../logs/debug.log');
            $resultados = $Dashboard->dashboardConteo($filtros);
            error_log("Consultar resultados: " . print_r($resultados, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode($resultados ?: []);
        } catch (Exception $e) {
            error_log("Error Consultar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar activos: ' . $e->getMessage()]);
        }
        break;

    case 'TotalActivosAsignados':
        try {
            $data = $Dashboard->TotalActivosAsignados();
            echo json_encode($data);
        } catch (Exception $e) {
            error_log("Error TotalActivosAsignados: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');  
            echo json_encode(['status' => false, 'message' => 'Error al consultar activos: ' . $e->getMessage()]);
        }
        break;

    case 'TotalActivosNoAsignados':
        try {
            $data = $Dashboard->TotalActivosNoAsignados();
            echo json_encode($data);
        } catch (Exception $e) {
            error_log("Error TotalActivosNoAsignados: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar activos: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Acción no válida.']);
        break;
}
