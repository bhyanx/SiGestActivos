<?php

require_once '../config/configuracion.php';
require_once '../models/EdicionesMov.php';

// Desactivar display_errors para evitar HTML en respuestas JSON
ini_set('display_errors', 0);

header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    $model = new EdicionesMov();

    switch ($action) {
        case 'listarMovimientosPendientes':
            $filtros = [];

            // Obtener filtros adicionales del POST
            if (isset($_POST['filtroTipo']) && !empty($_POST['filtroTipo'])) {
                $filtros['tipo'] = $_POST['filtroTipo'];
            }

            // Filtros de fecha más flexibles - se pueden usar individualmente
            if (isset($_POST['filtroFechaInicio']) && !empty($_POST['filtroFechaInicio'])) {
                $filtros['fechaInicio'] = $_POST['filtroFechaInicio'];
            }

            if (isset($_POST['filtroFechaFin']) && !empty($_POST['filtroFechaFin'])) {
                $filtros['fechaFin'] = $_POST['filtroFechaFin'];
            }

            $movimientos = $model->listarMovimientosPendientes($filtros);
            echo json_encode(['status' => true, 'data' => $movimientos]);
            break;

        case 'obtenerDetallesMovimiento':
            if (!isset($_POST['idMovimiento']) || empty($_POST['idMovimiento'])) {
                throw new Exception('ID de movimiento requerido');
            }

            $idMovimiento = $_POST['idMovimiento'];
            $detalles = $model->obtenerDetallesMovimiento($idMovimiento);
            echo json_encode(['status' => true, 'data' => $detalles]);
            break;

        case 'aprobarMovimiento':
            if (!isset($_POST['idMovimiento']) || empty($_POST['idMovimiento'])) {
                throw new Exception('ID de movimiento requerido');
            }

            $idMovimiento = $_POST['idMovimiento'];
            $userMod = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'SYSTEM';

            $model->aprobarMovimiento($idMovimiento, $userMod);
            echo json_encode(['status' => true, 'message' => 'Movimiento aprobado correctamente']);
            break;

        case 'rechazarMovimiento':
            if (!isset($_POST['idMovimiento']) || empty($_POST['idMovimiento'])) {
                throw new Exception('ID de movimiento requerido');
            }

            $idMovimiento = $_POST['idMovimiento'];
            $userMod = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'SYSTEM';

            $model->rechazarMovimiento($idMovimiento, $userMod);
            echo json_encode(['status' => true, 'message' => 'Movimiento rechazado correctamente']);
            break;

        case 'obtenerMovimientoParaEditar':
            if (!isset($_POST['idMovimiento']) || empty($_POST['idMovimiento'])) {
                throw new Exception('ID de movimiento requerido');
            }

            $idMovimiento = $_POST['idMovimiento'];
            $movimiento = $model->obtenerMovimientoParaEditar($idMovimiento);
            echo json_encode(['status' => true, 'data' => $movimiento]);
            break;

        case 'obtenerDetallesMovimientoParaEditar':
            if (!isset($_POST['idMovimiento']) || empty($_POST['idMovimiento'])) {
                throw new Exception('ID de movimiento requerido');
            }

            $idMovimiento = $_POST['idMovimiento'];
            $detalles = $model->obtenerDetallesMovimientoParaEditar($idMovimiento);
            echo json_encode(['status' => true, 'data' => $detalles]);
            break;

        case 'modificarMovimientoPendiente':
            if (!isset($_POST['data'])) {
                throw new Exception('Datos requeridos');
            }

            $requestData = json_decode($_POST['data'], true);
            if (!$requestData) {
                throw new Exception('Datos inválidos');
            }

            // Modificar cabecera del movimiento
            $headerData = [
                'idMovimiento' => $requestData['idMovimiento'],
                'idSucursalDestino' => isset($requestData['idSucursalDestino']) && !empty($requestData['idSucursalDestino']) ? $requestData['idSucursalDestino'] : null,
                'idEmpresaDestino' => isset($requestData['idEmpresaDestino']) && !empty($requestData['idEmpresaDestino']) ? $requestData['idEmpresaDestino'] : null,
                'idReceptor' => isset($requestData['idReceptor']) && !empty($requestData['idReceptor']) ? $requestData['idReceptor'] : null,
                'idActivo' => null, // No se modifica activo en la cabecera
                'idAmbienteNuevo' => null, // No se modifica ambiente en la cabecera
                'idResponsableNuevo' => null, // No se modifica responsable en la cabecera
                'userMod' => isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'SYSTEM'
            ];

            $model->modificarMovimientoPendiente($headerData);

            // Modificar activos si hay cambios
            if (isset($requestData['activos']) && is_array($requestData['activos'])) {
                foreach ($requestData['activos'] as $activo) {
                    if (isset($activo['idActivo'])) {
                        // Determinar la acción
                        if (isset($activo['accion']) && $activo['accion'] === 'eliminar') {
                            $accion = 2; // Eliminar
                        } elseif (isset($activo['esNuevo']) && $activo['esNuevo']) {
                            $accion = 3; // Agregar
                        } else {
                            $accion = 1; // Actualizar
                        }
                        
                        $assetData = [
                            'accion' => $accion,
                            'idMovimiento' => $requestData['idMovimiento'],
                            'idActivo' => in_array($accion, [1, 2]) ? $activo['idActivo'] : 0, // Para actualizar y eliminar (0 en lugar de null)
                            'nuevoIdActivo' => $accion === 3 ? $activo['idActivo'] : 0, // Solo para agregar (0 en lugar de null)
                            'idAmbienteNuevo' => isset($activo['idAmbienteNuevo']) ? $activo['idAmbienteNuevo'] : null,
                            'idResponsableNuevo' => isset($activo['idResponsableNuevo']) ? $activo['idResponsableNuevo'] : null,
                            'userMod' => isset($_SESSION['CodEmpleado']) ? $_SESSION['CodEmpleado'] : 'SYSTEM'
                        ];

                        $model->gestionarDetalleMovimientoPendiente($assetData);
                    }
                }
            }

            echo json_encode(['status' => true, 'message' => 'Movimiento y activos modificados correctamente']);
            break;

        case 'gestionarDetalleMovimientoPendiente':
            if (!isset($_POST['accion']) || !isset($_POST['idMovimiento'])) {
                throw new Exception('Acción e ID de movimiento requeridos');
            }

            $data = [
                'accion' => $_POST['accion'],
                'idMovimiento' => $_POST['idMovimiento'],
                'idActivo' => isset($_POST['idActivo']) && !empty($_POST['idActivo']) ? $_POST['idActivo'] : null,
                'nuevoIdActivo' => isset($_POST['nuevoIdActivo']) && !empty($_POST['nuevoIdActivo']) ? $_POST['nuevoIdActivo'] : null,
                'idAmbienteNuevo' => isset($_POST['idAmbienteNuevo']) && !empty($_POST['idAmbienteNuevo']) ? $_POST['idAmbienteNuevo'] : null,
                'idResponsableNuevo' => isset($_POST['idResponsableNuevo']) && !empty($_POST['idResponsableNuevo']) ? $_POST['idResponsableNuevo'] : null,
                'userMod' => isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'SYSTEM'
            ];

            $model->gestionarDetalleMovimientoPendiente($data);

            $mensaje = '';
            switch ($data['accion']) {
                case 1: $mensaje = 'Activo actualizado correctamente'; break;
                case 2: $mensaje = 'Activo eliminado correctamente'; break;
                case 3: $mensaje = 'Activo agregado correctamente'; break;
                default: $mensaje = 'Operación completada correctamente';
            }

            echo json_encode(['status' => true, 'message' => $mensaje]);
            break;

        case 'buscarActivosDisponibles':
            $filtros = [];

            if (isset($_POST['codigo']) && !empty($_POST['codigo'])) {
                $filtros['codigo'] = $_POST['codigo'];
            }

            if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
                $filtros['nombre'] = $_POST['nombre'];
            }

            if (isset($_POST['idEmpresa']) && !empty($_POST['idEmpresa'])) {
                $filtros['idEmpresa'] = $_POST['idEmpresa'];
            }

            if (isset($_POST['idSucursal']) && !empty($_POST['idSucursal'])) {
                $filtros['idSucursal'] = $_POST['idSucursal'];
            }

            $activos = $model->buscarActivosDisponibles($filtros);
            echo json_encode(['status' => true, 'data' => $activos]);
            break;

        default:
            throw new Exception('Acción no válida');
    }

} catch (Exception $e) {
    error_log("Error in EdicionesMovController: " . $e->getMessage(), 3, '../logs/errors.log');
    echo json_encode(['status' => false, 'message' => $e->getMessage()]);
}