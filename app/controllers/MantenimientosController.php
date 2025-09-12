<?php
session_start();

require_once '../config/configuracion.php';
require_once '../models/Mantenimientos.php';
require_once '../models/Combos.php';

$mantenimientos = new Mantenimientos();
$combo = new Combos();
$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

ini_set('display_errors', 0);
header('Content-Type: application/json');

switch ($action) {
    // Listar mantenimientos para DataTables
    case 'Consultar':
        try {
            error_log("=== DEBUGGING CONSULTAR ===", 3, __DIR__ . '/../../logs/debug.log');
            error_log("SESSION cod_empresa: " . ($_SESSION['cod_empresa'] ?? 'null'), 3, __DIR__ . '/../../logs/debug.log');
            error_log("SESSION cod_UnidadNeg: " . ($_SESSION['cod_UnidadNeg'] ?? 'null'), 3, __DIR__ . '/../../logs/debug.log');
            
            $filtros = [
                'pIdEmpresa' => $_SESSION['cod_empresa'] ?? null,
                'pIdSucursal' => $_SESSION['cod_UnidadNeg'] ?? null,
            ];
            
            error_log("Filtros enviados: " . print_r($filtros, true), 3, __DIR__ . '/../../logs/debug.log');
            
            $resultados = $mantenimientos->consultarMantenimientos($filtros);
            
            error_log("Resultados obtenidos: " . print_r($resultados, true), 3, __DIR__ . '/../../logs/debug.log');
            error_log("Cantidad de resultados: " . count($resultados), 3, __DIR__ . '/../../logs/debug.log');
            
            echo json_encode(['data' => $resultados ?: []]);
        } catch (Exception $e) {
            error_log("ERROR en Consultar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/debug.log');
            error_log("Stack trace: " . $e->getTraceAsString(), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode(['error' => 'Error al consultar los mantenimientos: ' . $e->getMessage()]);
        }
        break;

    // Registrar solo el mantenimiento principal (sin detalles)
    case 'RegistrarMantenimiento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'idTipoMantenimiento' => $_POST['idTipoMantenimiento'] ?? null,
                    'fechaProgramada' => $_POST['fechaProgramada'] ?? null,
                    'descripcion' => $_POST['descripcion'] ?? null,
                    'observaciones' => $_POST['observaciones'] ?? null,
                    'costoEstimado' => $_POST['costoEstimado'] ?? null,
                    'idProveedor' => $_POST['idProveedor'] ?? null,
                    'idResponsable' => $_POST['idResponsable'] ?? null,
                    'estadoMantenimiento' => $_POST['estadoMantenimiento'] ?? 1,
                    'userMod' => $_SESSION['CodEmpleado'] ?? null,
                    'idEmpresa' => $_SESSION['cod_empresa'] ?? null,
                    'idSucursal' => $_SESSION['cod_UnidadNeg'] ?? null
                ];

                $resultado = $mantenimientos->crearMantenimientoConCodigo($data);

                echo json_encode([
                    'status' => true,
                    'idMantenimiento' => $resultado['idMantenimiento'],
                    'codigoMantenimiento' => $resultado['codigoMantenimiento']
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        break;

    case 'AgregarDetalle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Recibe los datos de un solo activo
                $data = [
                    'idMantenimiento' => $_POST['idMantenimiento'],
                    'idActivo' => $_POST['idActivo'],
                    'observaciones' => $_POST['observaciones'] ?? null,
                    'userMod' => $_SESSION['usuario'] ?? null,
                ];

                $mantenimientos->crearDetalleMantenimiento($data);

                echo json_encode(['status' => true]);
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            }
        }
        break;

    // Listar activos para mantenimiento
    case 'ListarParaMantenimiento':
        try {
            $idEmpresa = $_SESSION['cod_empresa'] ?? null;
            $idSucursal = $_SESSION['cod_UnidadNeg'] ?? null;

            if (!$idEmpresa || !$idSucursal) {
                throw new Exception("No se encontró la información de empresa o sucursal en la sesión");
            }

            $activos = $mantenimientos->listarActivosParaMantenimiento($idEmpresa, $idSucursal);
            echo json_encode([
                'status' => true,
                'data' => $activos,
                'message' => 'Activos listados correctamente'
            ]);
        } catch (Exception $e) {
            error_log("Error en ListarParaMantenimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode([
                'status' => false,
                'message' => 'Error al listar activos: ' . $e->getMessage()
            ]);
        }
        break;

    case 'obtenerTiposMantenimiento':
        try {
            $tipos = $mantenimientos->obtenerTiposMantenimiento();
            echo json_encode([
                'status' => true,
                'data' => $tipos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'obtenerEstadosMantenimiento':
        try {
            $estados = $mantenimientos->obtenerEstadosMantenimiento();
            echo json_encode([
                'status' => true,
                'data' => $estados
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    // Cargar combos para selects
    case 'combos':
        try {
            // Obtener tipos de mantenimiento
            $tiposMantenimiento = $mantenimientos->obtenerTiposMantenimiento();
            $combos['tiposMantenimiento'] = '<option value="">Seleccione tipo de mantenimiento</option>';
            foreach ($tiposMantenimiento as $row) {
                $combos['tiposMantenimiento'] .= "<option value='{$row['idTipoMantenimiento']}'>{$row['nombre']}</option>";
            }

            // Obtener estados de mantenimiento
            $estadosMantenimiento = $mantenimientos->obtenerEstadosMantenimiento();
            $combos['estadosMantenimiento'] = '<option value="">Seleccione estado</option>';
            foreach ($estadosMantenimiento as $row) {
                $combos['estadosMantenimiento'] .= "<option value='{$row['idEstadoMantenimiento']}'>{$row['nombre']}</option>";
            }

            // Obtener responsables (reutilizamos el combo existente)
            $responsables = $combo->comboResponsable();
            $combos['responsables'] = '<option value="">Seleccione responsable</option>';
            foreach ($responsables as $row) {
                $combos['responsables'] .= "<option value='{$row['codTrabajador']}'>{$row['NombreTrabajador']}</option>";
            }

            echo json_encode([
                'status' => true,
                'data' => $combos,
                'message' => 'Combos de mantenimiento cargados correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error al cargar combos de mantenimiento: ' . $e->getMessage()
            ]);
        }
        break;

    case 'obtenerDetallesMantenimiento':
        try {
            $idMantenimiento = $_POST['idMantenimiento'] ?? null;
            if (!$idMantenimiento) {
                throw new Exception("ID de mantenimiento no proporcionado");
            }

            $detalles = $mantenimientos->obtenerDetallesMantenimiento($idMantenimiento);
            echo json_encode([
                'status' => true,
                'data' => $detalles
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'obtenerCabeceraMantenimiento':
        try {
            $idMantenimiento = $_POST['idMantenimiento'] ?? null;
            if (!$idMantenimiento) {
                throw new Exception("ID de mantenimiento no proporcionado");
            }

            $cabecera = $mantenimientos->obtenerCabeceraMantenimiento($idMantenimiento);
            echo json_encode([
                'status' => true,
                'data' => $cabecera
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'obtenerMantenimientoParaFinalizar':
        try {
            $idMantenimiento = $_POST['idMantenimiento'] ?? null;
            if (!$idMantenimiento) {
                throw new Exception("ID de mantenimiento no proporcionado");
            }

            $mantenimiento = $mantenimientos->obtenerMantenimientoParaFinalizar($idMantenimiento);
            $estados = $mantenimientos->obtenerEstadosMantenimiento();
            
            echo json_encode([
                'status' => true,
                'data' => [
                    'mantenimiento' => $mantenimiento,
                    'estados' => $estados
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'FinalizarMantenimiento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'idMantenimiento' => $_POST['idMantenimiento'],
                    'fechaMod' => $_POST['fechaMod'],
                    'costoReal' => $_POST['costoReal'] ?? null,
                    'observaciones' => $_POST['observaciones'] ?? null,
                    'idEstadoMantenimiento' => $_POST['idEstadoMantenimiento'],
                    'userMod' => $_SESSION['CodEmpleado']
                ];

                $mantenimientos->finalizarMantenimiento($data);

                echo json_encode([
                    'status' => true,
                    'message' => 'Mantenimiento finalizado correctamente'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        break;

    case 'obtenerMantenimientoParaCancelar':
        try {
            $idMantenimiento = $_POST['idMantenimiento'] ?? null;
            if (!$idMantenimiento) {
                throw new Exception("ID de mantenimiento no proporcionado");
            }

            $mantenimiento = $mantenimientos->obtenerMantenimientoParaCancelar($idMantenimiento);
            if (!$mantenimiento) {
                throw new Exception("El mantenimiento no se puede cancelar o no existe");
            }

            $estados = $mantenimientos->obtenerEstadosMantenimiento();
            
            echo json_encode([
                'status' => true,
                'data' => [
                    'mantenimiento' => $mantenimiento,
                    'estados' => $estados
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'CancelarMantenimiento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'idMantenimiento' => $_POST['idMantenimiento'],
                    'motivo' => $_POST['motivo'] ?? '',
                    'idEstadoMantenimiento' => $_POST['idEstadoMantenimiento'] ?? 4, // Asumiendo que 4 es "Cancelado"
                    'userMod' => $_SESSION['CodEmpleado']
                ];

                $mantenimientos->cancelarMantenimiento($data);

                echo json_encode([
                    'status' => true,
                    'message' => 'Mantenimiento cancelado correctamente'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        break;

    case 'obtenerHistorialEstadoMantenimiento':
        try {
            $idMantenimiento = $_POST['idMantenimiento'] ?? null;
            if (!$idMantenimiento) {
                throw new Exception("ID de mantenimiento no proporcionado");
            }

            $historial = $mantenimientos->obtenerHistorialMantenimientos($idMantenimiento);
            echo json_encode([
                'status' => true,
                'data' => $historial
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode([
            'status' => false,
            'message' => 'Acción no válida'
        ]);
        break;
}
