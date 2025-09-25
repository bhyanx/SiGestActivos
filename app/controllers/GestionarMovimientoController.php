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
                    'idReceptor' => $_POST['idReceptor'],
                    'idEmpresaOrigen' => $_SESSION['cod_empresa'],
                    'idSucursalOrigen' => $_SESSION['cod_UnidadNeg'],
                    'idEmpresaDestino' => $_POST['idEmpresaDestino'],
                    'idSucursalDestino' => $_POST['idSucursalDestino'],
                    'observaciones' => $_POST['observaciones'] ?? '',
                    'userMod' => $_SESSION['CodEmpleado']
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

    case 'AgregarDetalle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'idMovimiento' => $_POST['IdMovimiento'],
                    'idActivo' => $_POST['IdActivo'],
                    'idTipoMovimiento' => $_POST['IdTipo_Movimiento'],
                    'idAmbienteNuevo' => $_POST['IdAmbiente_Nueva'] ?? null,
                    'idResponsableNuevo' => $_POST['IdResponsable_Nueva'] ?? null,
                    'userMod' => $_SESSION['CodEmpleado']
                ];

                $resultado = $movimientos->crearDetalleMovimiento($data);

                echo json_encode([
                    'status' => true,
                    'message' => 'Detalle agregado correctamente'
                ]);
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

            $tipoMovimientov1 = $combo->comboTipoMovimientov1();
            $combos['tipoMovimientov1'] = '<option value="">Seleccione</option>';
            foreach ($tipoMovimientov1 as $row) {
                $combos['tipoMovimientov1'] .= "<option value='{$row['idTipoMovimiento']}'>{$row['nombre']}</option>";
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

            $receptor = $combo->comboReceptor();
            $combos['receptor'] = '<option value="">Seleccione</option>';
            foreach ($receptor as $row) {
                $combos['receptor'] .= "<option value='{$row['codTrabajador']}'>{$row['NombreTrabajador']}</option>";
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

    case 'listarMovimientosEnviados':
        try {
            $filtros = [
                'tipo' => $_POST['tipo'] ?? null,
                'fechaInicio' => $_POST['fechaInicio'] ?? null,
                'fechaFin' => $_POST['fechaFin'] ?? null,
                //'fecha' => $_POST['fecha'] ?? null,
                'idEmpresa' => $_SESSION['cod_empresa'] ?? null,
                'idSucursalOrigen' => $_SESSION['cod_UnidadNeg'] ?? null,  // corregido aquí
                //'idSucursalDestino' => $_POST['cod_UnidadNeg'] ?? null
            ];

            $resultado = $movimientos->listarMovimientosEnviados($filtros);
            echo json_encode([
                'status' => true,
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'listarMovimientosRecibidos':
        try {
            $filtros = [
                'tipo' => $_POST['tipo'] ?? null,
                'fechaInicio' => $_POST['fechaInicio'] ?? null,
                'fechaFin' => $_POST['fechaFin'] ?? null,
                //'fecha' => $_POST['fecha'] ?? null,
                'idEmpresa' => $_SESSION['cod_empresa'] ?? null,
                'idSucursalDestino' => $_SESSION['cod_UnidadNeg'] ?? null,  // corregido aquí
                //'idSucursalDestino' => $_POST['cod_UnidadNeg'] ?? null
            ];

            $resultado = $movimientos->listarMovimientosRecibidos($filtros);
            echo json_encode([
                'status' => true,
                'data' => $resultado
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

    case 'verificarComponentesActivo':
        try {
            $idActivo = $_POST['idActivo'] ?? null;
            if (!$idActivo) {
                throw new Exception("ID de activo no proporcionado");
            }

            $resultado = $movimientos->verificarActivoConComponentes($idActivo);
            echo json_encode([
                'status' => true,
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'aprobarMovimiento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $idMovimiento = $_POST['idMovimiento'] ?? null;
                if (!$idMovimiento) {
                    throw new Exception("ID de movimiento no proporcionado");
                }

                // Validar que el usuario actual sea el autorizador del movimiento
                $sql = "SELECT idAutorizador FROM tMovimientos WHERE idMovimiento = ?";
                $stmt = $movimientos->db->prepare($sql);
                $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
                $stmt->execute();
                $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$movimiento) {
                    throw new Exception("Movimiento no encontrado");
                }

                if ($movimiento['idAutorizador'] !== $_SESSION['CodEmpleado']) {
                    throw new Exception("Solo el autorizador del movimiento puede aprobarlo");
                }

                $userMod = $_SESSION['CodEmpleado'];
                $resultado = $movimientos->aprobarMovimiento($idMovimiento, $userMod);

                echo json_encode([
                    'status' => true,
                    'message' => 'Movimiento aprobado correctamente'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al aprobar el movimiento: ' . $e->getMessage()
                ]);
            }
        }
        break;

    case 'rechazarMovimiento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $idMovimiento = $_POST['idMovimiento'] ?? null;
                if (!$idMovimiento) {
                    throw new Exception("ID de movimiento no proporcionado");
                }

                $userMod = $_SESSION['CodEmpleado'];
                $resultado = $movimientos->rechazarMovimiento($idMovimiento, $userMod);

                echo json_encode([
                    'status' => true,
                    'message' => 'Movimiento rechazado correctamente'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al rechazar el movimiento: ' . $e->getMessage()
                ]);
            }
        }
        break;

    case 'aceptarMovimiento':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $idMovimiento = $_POST['idMovimiento'] ?? null;
                if (!$idMovimiento) {
                    throw new Exception("ID de movimiento no proporcionado");
                }

                // Validar que el usuario actual sea el receptor del movimiento
                $sql = "SELECT idReceptor FROM tMovimientos WHERE idMovimiento = ?";
                $stmt = $movimientos->db->prepare($sql);
                $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
                $stmt->execute();
                $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$movimiento) {
                    throw new Exception("Movimiento no encontrado");
                }

                if ($movimiento['idReceptor'] !== $_SESSION['CodEmpleado']) {
                    throw new Exception("Solo el receptor origen puede aceptar el movimiento");
                }

                $userMod = $_SESSION['CodEmpleado'];
                $resultado = $movimientos->aceptarMovimiento($idMovimiento, $userMod);

                echo json_encode([
                    'status' => true,
                    'message' => 'Movimiento aceptado y ejecutado correctamente'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al aceptar el movimiento: ' . $e->getMessage()
                ]);
            }
        }
        break;

    case 'obtenerHistorialEstadoMovimiento':
        try {
            $idMovimiento = $_POST['idMovimiento'] ?? null;
            if (!$idMovimiento) {
                throw new Exception("ID de movimiento no proporcionado");
            }

            $historial = $movimientos->obtenerHistorialEstadoMovimiento($idMovimiento);
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

    case 'obtenerEstadosMovimiento':
        try {
            $estados = $movimientos->obtenerEstadosMovimiento();
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

    case 'inicializarEstados':
        try {
            // Verificar si existen los estados básicos
            $sql = "IF NOT EXISTS (SELECT 1 FROM tEstadoMovimiento WHERE idEstadoMovimiento = 1)
                    INSERT INTO tEstadoMovimiento (idEstadoMovimiento, nombre, descripcion) VALUES (1, 'Pendiente', 'Movimiento creado, esperando aprobación');
                    
                    IF NOT EXISTS (SELECT 1 FROM tEstadoMovimiento WHERE idEstadoMovimiento = 2)
                    INSERT INTO tEstadoMovimiento (idEstadoMovimiento, nombre, descripcion) VALUES (2, 'Aprobado', 'Movimiento aprobado, listo para ser aceptado');
                    
                    IF NOT EXISTS (SELECT 1 FROM tEstadoMovimiento WHERE idEstadoMovimiento = 3)
                    INSERT INTO tEstadoMovimiento (idEstadoMovimiento, nombre, descripcion) VALUES (3, 'Rechazado', 'Movimiento rechazado');
                    
                    IF NOT EXISTS (SELECT 1 FROM tEstadoMovimiento WHERE idEstadoMovimiento = 4)
                    INSERT INTO tEstadoMovimiento (idEstadoMovimiento, nombre, descripcion) VALUES (4, 'Aceptado', 'Movimiento ejecutado físicamente');";

            $stmt = $movimientos->db->prepare($sql);
            $stmt->execute();

            echo json_encode([
                'status' => true,
                'message' => 'Estados inicializados correctamente'
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
