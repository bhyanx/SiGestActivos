<?php
session_start();

require_once '../config/configuracion.php';
require_once '../models/GestionarMovimientos.php';
require_once '../models/Combos.php';

$movimientos = new GestionarMovimientos();
$combo = new Combos();
$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

ini_set('display_errors', 0);
header('Content-Type: application/json');

switch ($action) {
    // Listar movimientos para DataTables
    case 'Consultar':
        try {
            $filtros = [
                'tipo' => $_POST['filtroTipoMovimiento'] ?? null,
                'sucursal_origen' => $_POST['filtroSucursalOrigen'] ?? null,
                'sucursal_destino' => $_POST['filtroSucursalDestino'] ?? null,
                'fecha' => $_POST['filtroFecha'] ?? null,
                'ambiente' => $_POST['filtroAmbiente'] ?? null,
                'idEmpresa' => $_SESSION['cod_empresa'] ?? null,
                'idSucursal' => $_SESSION['cod_UnidadNeg'] ?? null
            ];
            $resultados = $movimientos->listarDetalleMovimientos($filtros);
            echo json_encode(['data' => $resultados ?: []]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error al consultar los movimientos: ' . $e->getMessage()]);
        }
        break;

    // Registrar solo el movimiento principal (sin detalles)
    case 'RegistrarMovimiento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'idTipoMovimiento' => $_POST['idTipoMovimiento'],
                    'idAutorizador' => $_POST['idAutorizador'],
                    'idEmpresa' => $_SESSION['cod_empresa'],
                    'observaciones' => $_POST['observaciones'] ?? '',
                    'userMod' => $_SESSION['usuario']
                ];

                $resultado = $movimientos->crearMovimientoConCodigo($data);

                echo json_encode([
                    'status' => true,
                    'idMovimiento' => $resultado['idMovimiento'],
                    'codMovimiento' => $resultado['codMovimiento']
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        break;

    case 'AgregarDetalleMovimiento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $detalle = [
                    'idMovimiento' => $_POST['idMovimiento'],
                    'idActivo' => $_POST['idActivo'],
                    'idTipoMovimiento' => $_POST['idTipoMovimiento'],
                    'idAmbienteNuevo' => $_POST['idAmbienteNuevo'],
                    'idResponsableNuevo' => $_POST['idResponsableNuevo'],
                    'idAutorizador' => $_POST['idAutorizador'],
                    'userMod' => $_SESSION['usuario']
                ];

                $movimientos->crearDetalleMovimiento($detalle);

                echo json_encode(['status' => true]);
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            }
        }
        break;



    case 'RegistrarMovimientoActivos':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $ids = isset($_POST['activos']) ? json_decode($_POST['activos'], true) : [];
                if (!is_array($ids) || empty($ids)) {
                    throw new Exception("No se recibieron activos válidos.");
                }

                $codMovimiento = $movimientos->registrarMovimientoActivos(
                    $ids,
                    $_POST['idAmbienteDestino'],
                    $_POST['idResponsableDestino'],
                    $_POST['motivo'],
                    $_SESSION['usuario'], // usuario logueado
                    $_SESSION['cod_empresa'],
                    $_SESSION['cod_UnidadNeg']
                );

                echo json_encode([
                    'status' => true,
                    'mensaje' => 'Movimiento registrado correctamente.',
                    'codigoMovimiento' => $codMovimiento
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => false,
                    'mensaje' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
        break;


    // Agregar detalle (activo) al movimiento
    case 'AgregarDetalle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $detalle = [
                    'IdMovimiento' => $_POST['IdMovimiento'],
                    'IdActivo' => $_POST['IdActivo'],
                    'IdSucursal_Nueva' => $_POST['IdSucursal_Nueva'],
                    'IdAmbiente_Nueva' => $_POST['IdAmbiente_Nueva'],
                    'IdTipo_Movimiento' => $_POST['IdTipo_Movimiento'],
                    'IdAutorizador' => $_POST['IdAutorizador'],
                    'IdResponsable_Nueva' => $_POST['IdResponsable_Nueva'],
                    'IdActivoPadre_Nuevo' => $_POST['IdActivoPadre_Nuevo'] ?? null,
                    'IdEmpresaDestino' => $_POST['IdEmpresaDestino']
                ];
                $movimientos->crearDetalleMovimiento($detalle);
                echo json_encode(['status' => true]);
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            }
        }
        break;

    // Listar activos para movimiento
    case 'ListarParaMovimiento':
        try {
            $idEmpresa = $_SESSION['cod_empresa'] ?? null;
            $idSucursal = $_SESSION['cod_UnidadNeg'] ?? null;

            if (!$idEmpresa || !$idSucursal) {
                throw new Exception("No se encontró la información de empresa o sucursal en la sesión");
            }

            $activos = $movimientos->listarActivosParaMovimiento($idEmpresa, $idSucursal);
            echo json_encode([
                'status' => true,
                'data' => $activos,
                'message' => 'Activos listados correctamente'
            ]);
        } catch (Exception $e) {
            error_log("Error en ListarParaMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode([
                'status' => false,
                'message' => 'Error al listar activos: ' . $e->getMessage()
            ]);
        }
        break;

    case 'obtenerAmbientesPorSucursal':
        try {
            $idEmpresa = $_POST['idEmpresa'] ?? null;
            $idSucursal = $_POST['idSucursal'] ?? null;

            if (!$idEmpresa || !$idSucursal) {
                throw new Exception("Se requiere la empresa y la sucursal");
            }

            $ambientes = $movimientos->obtenerAmbientesPorSucursal($idEmpresa, $idSucursal);
            $html = '<option value="">Seleccione</option>';
            foreach ($ambientes as $ambiente) {
                $html .= "<option value='{$ambiente['idAmbiente']}'>{$ambiente['nombre']}</option>";
            }

            echo json_encode([
                'status' => true,
                'data' => $html,
                'message' => 'Ambientes cargados correctamente'
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerAmbientesPorSucursal: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode([
                'status' => false,
                'message' => 'Error al cargar ambientes: ' . $e->getMessage()
            ]);
        }
        break;

    // Cargar combos para selects
    case 'combos':
        try {
            $tipoMovimiento = $combo->comboTipoMovimiento();
            $combos['tipoMovimiento'] = '<option value="">Seleccione</option>';
            foreach ($tipoMovimiento as $row) {
                $combos['tipoMovimiento'] .= "<option value='{$row['idTipoMovimiento']}'>{$row['nombre']}</option>";
            }

            // Obtener empresas
            $empresas = $combo->comboEmpresa();
            $combos['empresas'] = '<option value="">Seleccione</option>';
            foreach ($empresas as $row) {
                $combos['empresas'] .= "<option value='{$row['cod_empresa']}'>{$row['Razon_empresa']}</option>";
            }

            // Obtener sucursales incluyendo la unidad de negocio actual
            $sucursales = $combo->comboUnidadNegocio($_SESSION['cod_empresa']);
            $combos['sucursales'] = '<option value="">Seleccione</option>';
            $unidadNegocioActual = $_SESSION['cod_UnidadNeg'];
            $nombreSucursalActual = '';

            foreach ($sucursales as $row) {
                if ($row['cod_UnidadNeg'] == $unidadNegocioActual) {
                    $combos['sucursales'] .= "<option value='{$row['cod_UnidadNeg']}' selected>{$row['Nombre_local']}</option>";
                    $nombreSucursalActual = $row['Nombre_local'];
                } else {
                    $combos['sucursales'] .= "<option value='{$row['cod_UnidadNeg']}'>{$row['Nombre_local']}</option>";
                }
            }

            // Agregar el nombre y ID de la unidad de negocio de la sesión
            $combos['sucursalOrigen'] = $nombreSucursalActual;
            $combos['sucursalOrigenId'] = $unidadNegocioActual;
            $combos['sucursalOrigenSelect'] = "<option value='{$unidadNegocioActual}' selected>{$nombreSucursalActual}</option>";

            $autorizador = $combo->comboAutorizador();
            $combos['autorizador'] = '<option value="">Seleccione</option>';
            foreach ($autorizador as $row) {
                $combos['autorizador'] .= "<option value='{$row['codTrabajador']}'>{$row['NombreTrabajador']}</option>";
            }

            $responsable = $combo->comboResponsable();
            $combos['responsable'] = '<option value="">Seleccione</option>';
            foreach ($responsable as $row) {
                $combos['responsable'] .= "<option value='{$row['codTrabajador']}'>{$row['NombreTrabajador']}</option>";
            }

            $ambientes = $combo->comboAmbiente();
            $combos['ambientes'] = '<option value="">Seleccione</option>';
            foreach ($ambientes as $row) {
                $combos['ambientes'] .= "<option value='{$row['idAmbiente']}'>{$row['nombre']}</option>";
            }

            $estadoActivo = $combo->comboEstadoActivo();
            $combos['estado'] = '<option value="">Seleccione</option>';
            foreach ($estadoActivo as $row) {
                $combos['estado'] .= "<option value='{$row['idEstadoActivo']}'>{$row['nombre']}</option>";
            }

            echo json_encode(['status' => true, 'data' => $combos, 'message' => 'Combos cargados correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'Error al cargar combos: ' . $e->getMessage()]);
        }
        break;

    // Nuevo caso para anular movimiento
    case 'anularMovimiento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $idMovimiento = $_POST['idMovimiento'] ?? null;
                if (!$idMovimiento) {
                    throw new Exception("ID de movimiento no proporcionado");
                }

                $resultado = $movimientos->anularMovimiento($idMovimiento);
                echo json_encode([
                    'status' => true,
                    'message' => 'Movimiento anulado correctamente'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al anular el movimiento: ' . $e->getMessage()
                ]);
            }
        }
        break;

    // Nuevo caso para obtener historial de movimiento
    case 'obtenerHistorialMovimiento':
        try {
            $idMovimiento = $_POST['idMovimiento'] ?? null;
            if (!$idMovimiento) {
                throw new Exception("ID de movimiento no proporcionado");
            }

            $historial = $movimientos->obtenerHistorialMovimiento($idMovimiento);
            echo json_encode([
                'status' => true,
                'data' => $historial
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error al obtener el historial: ' . $e->getMessage()
            ]);
        }
        break;

    case 'obtenerSucursalesPorEmpresa':
        try {
            $idEmpresa = $_POST['idEmpresa'] ?? null;
            if (!$idEmpresa) {
                throw new Exception("Se requiere el ID de la empresa");
            }

            $sucursales = $combo->comboUnidadNegocio($idEmpresa);
            $html = '<option value="">Seleccione</option>';
            foreach ($sucursales as $row) {
                $html .= "<option value='{$row['cod_UnidadNeg']}'>{$row['Nombre_local']}</option>";
            }

            echo json_encode([
                'status' => true,
                'data' => $html,
                'message' => 'Sucursales cargadas correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error al cargar sucursales: ' . $e->getMessage()
            ]);
        }
        break;

    case 'listarMovimientos':
        try {
            $filtros = [
                'tipo' => $_POST['tipo'] ?? null,
                'sucursal' => $_POST['sucursal'] ?? null,
                'fecha' => $_POST['fecha'] ?? null,
                'idEmpresa' => $_SESSION['cod_empresa'] ?? null,
                'idSucursal' => $_SESSION['cod_UnidadNeg'] ?? null
            ];

            $movimientos = $movimientos->listarMovimientos($filtros);
            echo json_encode([
                'status' => true,
                'data' => $movimientos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'obtenerDetallesMovimiento':
        try {
            $idMovimiento = $_POST['idMovimiento'] ?? null;
            if (!$idMovimiento) {
                throw new Exception("ID de movimiento no proporcionado");
            }

            $detalles = $movimientos->obtenerDetallesMovimiento($idMovimiento);
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
}
