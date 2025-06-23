<?php
session_start();

require_once '../config/configuracion.php';
require_once '../models/GestionarMovimientosComponentes.php';
require_once '../models/GestionarMovimientos.php';

$movimientosComponentes = new GestionarMovimientosComponentes();
$movimientos = new GestionarMovimientos();
$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

ini_set('display_errors', 0);
header('Content-Type: application/json');

switch ($action) {
    case 'listarActivosPadres':
        try {
            $tipo = $_POST['tipo'] ?? '';
            $sucursal = null;

            if ($tipo === 'origen') {
                // Para origen usamos la sucursal de la sesión
                $sucursal = $_SESSION['cod_UnidadNeg'] ?? null;
                if (!$sucursal) {
                    throw new Exception("No se encontró la sucursal de origen en la sesión");
                }
            } else if ($tipo === 'destino') {
                // Para destino usamos la sucursal seleccionada
                $sucursal = $_POST['sucursal'] ?? null;
                if (!$sucursal) {
                    throw new Exception("Debe seleccionar una sucursal destino");
                }
            } else {
                throw new Exception("Tipo de consulta no válido");
            }

            $activosPadres = $movimientosComponentes->listarActivosPadres($sucursal);
            echo json_encode([
                'status' => true,
                'data' => $activosPadres,
                'message' => 'Activos padres listados correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error al listar activos padres: ' . $e->getMessage()
            ]);
        }
        break;

    case 'listarComponentesActivo':
        try {
            $idActivoPadre = $_POST['idActivoPadre'] ?? null;
            if (!$idActivoPadre) {
                throw new Exception("ID de activo padre no proporcionado");
            }

            $componentes = $movimientosComponentes->listarComponentesActivo($idActivoPadre);
            echo json_encode([
                'status' => true,
                'data' => $componentes,
                'message' => 'Componentes listados correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error al listar componentes: ' . $e->getMessage()
            ]);
        }
        break;

    case 'MoverComponenteEntreActivos':
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            $data = [
                'IdMovimiento' => $_POST['IdMovimiento'],
                'IdActivoComponente' => $_POST['IdActivoComponente'],
                'IdTipo_Movimiento' => $_POST['IdTipo_Movimiento'],
                'IdActivoPadreNuevo' => $_POST['IdActivoPadreNuevo'],
                'IdAutorizador' => $_POST['IdAutorizador'],
                'Observaciones' => $_POST['Observaciones'] ?? null
            ];

            $resultado = $movimientosComponentes->moverComponenteEntreActivos($data);
            echo json_encode([
                'status' => true,
                'message' => 'Componente movido correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error al mover el componente: ' . $e->getMessage()
            ]);
        }
        break;

    case 'ConsultarMovimientosEntreActivos':
        try {
            $filtros = [
                'sucursal' => $_POST['filtroSucursal'] ?? null,
                'fecha' => $_POST['filtroFecha'] ?? null
            ];

            $resultados = $movimientosComponentes->consultarMovimientosEntreActivos($filtros);
            echo json_encode([
                'status' => true,
                'data' => $resultados,
                'message' => 'Movimientos consultados correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error al consultar movimientos: ' . $e->getMessage()
            ]);
        }
        break;

    case 'asignarComponente':
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método no permitido");
            }

            $data = [
                'IdActivoPadre' => $_POST['IdActivoPadre'] ?? null,
                'IdActivoComponente' => $_POST['IdActivoComponente'] ?? null,
                'Observaciones' => $_POST['Observaciones'] ?? null,
                'FechaAsignacion' => date('Y-m-d H:i:s'),
                'UserMod' => $_SESSION['usuario'] ?? 'usuario_default'
            ];

            $resultado = $movimientosComponentes->asignarComponenteActivo($data);
            echo json_encode([
                'success' => true,
                'message' => 'Componente asignado correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al asignar el componente: ' . $e->getMessage()
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
