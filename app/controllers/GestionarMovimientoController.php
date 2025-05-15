<?php
session_start();

require_once '../config/configuracion.php';
require_once '../models/GestionarMovimientos.php';
require_once '../models/Combos.php';

$movimientos = new GestionarMovimientos();
$combo = new Combos();
$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

// Desactivar display_errors para evitar HTML en respuestas JSON
ini_set('display_errors', 0);

header('Content-Type: application/json');

switch ($action) {
    case 'Consultar':
        try {
            $filtros = [
                'SucursalOrigen' => $_POST['sucursal_origen'] ?? null,
            ];
            $resultados = $movimientos->listarDetalleMovimientos($filtros);
            error_log("Consultar movimientos: " . print_r($resultados, true), 3, __DIR__ . '/logs/movimientos.log');
            echo json_encode($resultados ?: []);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error al consultar los movimientos: ' . $e->getMessage()]);
        }
        break;

    case 'Registrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'idTipoMovimiento' => $_POST['tipo'],
                    'idAutorizador' => $_POST['autorizador'],
                    'idSucursalOrigen' => $_POST['sucursal_origen'],
                    'idSucursalDestino' => $_POST['sucursal_destino'],
                    'observaciones' => $_POST['observacion']
                ];

                $idMovimiento = $movimientos->crearMovimiento($data);

                foreach ($_POST['activos'] as $activo) {
                    $detalle = [
                        'IdMovimiento' => $idMovimiento,
                        'IdActivo' => $activo,
                        'IdSucursal_Nueva' => $_POST['sucursal_destino'],
                        'IdAmbiente_Nueva' => $_POST['ambiente_destino'],
                        'IdResponsable_Nueva' => $_POST['responsable_destino'],
                        'IdActivoPadreOrigen' => $_POST['activo_padre']
                    ];
                    $this->$movimientos->crearDetalleMovimiento($detalle);
                }
                // Redirección después del registro
                header('Location: ../views/movimiento_exito.php');
                exit;

                echo json_encode(array('status' => true, 'message' => 'Movimiento registrado con Exito.'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
                echo json_encode(['error' => 'Error al registrar el movimiento: ' . $e->getMessage()]); {
                }
            }
        }
        break;

    case 'combos':
        try {
            $db = (new Conectar())->ConexionBdPracticante();

            $tipoMovimiento = $combo->comboTipoMovimiento();
            $combos['tipoMovimiento'] = '<option value="">Seleccione</option>';
            foreach ($tipoMovimiento as $row) {
                $combos['tipoMovimiento'] .= "<option value='{$row['idTipoMovimiento']}'>{$row['nombre']}</option>";
            }

            $sucursales = $combo->comboSucursal();
            $combos['sucursales'] = '<option value="">Seleccione</option>';
            foreach ($sucursales as $row){
                $combos['sucursales'] .= "<option value='{$row['idSucursal']}'>{$row['nombre']}</option>";
            }

            $autorizador = $combo->comboAutorizador();
            $combos['autorizador'] = '<option value="">Seleccione</option>';
            foreach ($autorizador as $row){
                $combos['autorizador'] .= "<option value='{$row['codTrabajador']}'>{$row['NombreTrabajador']}</option>";
            }

            // $estados = $combo->comboEstadoActivo();
            // $combos['estados'] = '<option value="">Seleccione</option>';
            // foreach ($estados as $row) {
            //     $combos['estados'] .= "<option value='{$row['idEstadoActivo']}'>{$row['nombre']}</option>";
            // }

            error_log("Combos generados: " . print_r($combos, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode(['status' => true, 'data' => $combos, 'message' => 'Combos cargados correctamente.']);
        } catch (Exception $e) {
            error_log("Error Combos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar combos: ' . $e->getMessage()]);
        }
}

//? SE USAN FUNCIONES Y NO UN CASE, YA QUE TIENE UNA SOLA FUNCIONALIDAD EN DONDE LA VAMOS A LLAMAR DIRECTAMENTE DESDE EL FORMULARIO
//? Y NO SE VA A HACER UNA PETICION DESDE UN BOTON DE ACCION, POR LO QUE NO SE NECESITA UN SWITCH CASE

// class GestionarMovimientoController
// {

//     private $movimientoModel;

//     public function __construct()
//     {
//         $this->movimientoModel = new GestionarMovimientos();
//     }

//     //* FUNCION DEL CONTROLADOR QUE SE ENCARGA DE REGISTRAR UN MOVIMIENTO EN LA BASE DE DATOS 

//     public function registrarMovimiento()
//     {
//         //? EN ESTA FUNCIÓN SE CREA PRINCIPALMENTE EL MOVIMIENTO Y LUEGO DE ESO, PASAMOS A CREAR LOS DETALLES QUE VIENE A SER LOS ATRIBUTOS DEL MOVIMIENTO Y SE ACTUALIZA AUTOMÁTICAMENTE EL ACTIVO EN LA BASE DE DATOS
//         //? SE DEBE ENVIAR EL ID DEL MOVIMIENTO A REGISTRAR

//         $dataMovimiento = [
//             'idTipoMovimiento' => $_POST['tipo'],
//             'idAutorizador' => $_POST['autorizador'],
//             'idSucursalOrigen' => $_POST['sucursal_origen'],
//             'idSucursalDestino' => $_POST['sucursal_destino'],
//             'observaciones' => $_POST['observacion']
//         ];

//         $idMovimiento = $this->movimientoModel->crearMovimiento($dataMovimiento);

//         foreach ($_POST['activos'] as $activo) {
//             $detalle = [
//                 'IdMovimiento' => $idMovimiento,
//                 'IdActivo' => $activo,
//                 'IdSucursal_Nueva' => $_POST['sucursal_destino'],
//                 'IdAmbiente_Nueva' => $_POST['ambiente_destino'],
//                 'IdResponsable_Nueva' => $_POST['responsable_destino'],
//                 'IdActivoPadreOrigen' => $_POST['activo_padre']
//             ];
//             $this->movimientoModel->crearDetalleMovimiento($detalle);
//         }

//         header('Location: ../views/movimiento_exito.php'); // Redirección después del registro
//         exit;
//     }

//     //* FUNCION DEL CONTROLADOR QUE SE ENCARGA DE LISTAR LOS DETALLES DE UN MOVIMIENTO ENCONTRADO EN LA BASE DE DATOS
//     public function listarMovimientos()
//     {
//         $idMovimiento = $_GET['idMovimiento'];
//         $detalles = $this->movimientoModel->listarDetalleMovimientos($idMovimiento);
//         return $detalles;
//     }
// }
