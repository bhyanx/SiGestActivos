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
                'ambiente' => $_POST['filtroAmbiente'] ?? null // si tienes este campo
            ];
            $resultados = $movimientos->listarDetalleMovimientos($filtros);
            echo json_encode(['data' => $resultados ?: []]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error al consultar los movimientos: ' . $e->getMessage()]);
        }
        break;

    // Registrar solo el movimiento principal (sin detalles)
    case 'RegistrarMovimientoSolo':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'idTipoMovimiento' => $_POST['IdTipo'],
                    'idAutorizador' => $_POST['autorizador'],
                    'idSucursalOrigen' => $_POST['sucursal_origen'],
                    'idSucursalDestino' => $_POST['sucursal_destino'],
                    'observaciones' => $_POST['observacion']
                ];
                $idMovimiento = $movimientos->crearMovimiento($data);
                echo json_encode([
                    'status' => true,
                    'idMovimiento' => $idMovimiento
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al registrar el movimiento: ' . $e->getMessage()
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
                    'IdSucursal_Nueva' => $_POST['IdSucursalDestino'],
                    'IdAmbiente_Nueva' => $_POST['IdAmbienteDestino'],
                    'IdTipo_Movimiento' => $_POST['IdTipoMovimiento'],
                    'IdAutorizador' => $_POST['IdAutorizador'],
                    // Agrega aquí otros campos si los necesitas
                ];
                $movimientos->crearDetalleMovimiento($detalle);
                echo json_encode(['status' => true]);
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => $e->getMessage()]);
            }
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

            $sucursales = $combo->comboSucursal();
            $combos['sucursales'] = '<option value="">Seleccione</option>';
            foreach ($sucursales as $row) {
                $combos['sucursales'] .= "<option value='{$row['idSucursal']}'>{$row['nombre']}</option>";
            }

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
}
