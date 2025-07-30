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

        case 'listarActivosPadresv2':
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
    
                $activosPadres = $movimientosComponentes->listarActivosPadresv2($sucursal);
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

            $idComponente = $_POST['IdActivoComponente'] ?? null;
            $idPadreNuevo = $_POST['IdActivoPadreNuevo'] ?? null;

            // Validaciones básicas
            if (!$idComponente || !$idPadreNuevo) {
                throw new Exception("Faltan parámetros requeridos: IdActivoComponente e IdActivoPadreNuevo");
            }

            // Validar que el componente no sea su propio padre
            if ($idComponente == $idPadreNuevo) {
                throw new Exception("Un activo no puede ser su propio padre. Seleccione un activo padre diferente.");
            }

            $data = [
                'IdActivoComponente' => $idComponente,
                'IdActivoPadreNuevo' => $idPadreNuevo,
                'UserMod' => $_SESSION['usuario'] ?? 'usuario_default'
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

            $idPadre = $_POST['IdActivoPadre'] ?? null;
            $idComponente = $_POST['IdActivoComponente'] ?? null;

            // Validaciones básicas
            if (!$idPadre || !$idComponente) {
                throw new Exception("Faltan parámetros requeridos: IdActivoPadre e IdActivoComponente");
            }

            // Validar que el componente no sea su propio padre
            if ($idComponente == $idPadre) {
                throw new Exception("Un activo no puede ser su propio padre. Seleccione un activo padre diferente.");
            }

            $data = [
                'IdActivoPadre' => $idPadre,
                'IdActivoComponente' => $idComponente,
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

    case 'listarComponentesSinPadre':
        try {
            $sucursal = $_POST['sucursal'] ?? $_SESSION['cod_UnidadNeg'] ?? null;
            
            $componentes = $movimientosComponentes->listarComponentesSinPadre($sucursal);
            echo json_encode([
                'status' => true,
                'data' => $componentes,
                'message' => 'Componentes sin padre listados correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error al listar componentes sin padre: ' . $e->getMessage()
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
