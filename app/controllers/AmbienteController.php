<?php
session_start();
require_once("../config/configuracion.php");
require_once("../models/Ambientes.php");
require_once("../models/Combos.php");

$ambiente = new Ambientes();
$combos = new Combos();

$fechaActual = date("Y-m-d");

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

switch ($action) {
    case 'RegistrarAmbiente':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'IdAmbiente' => null,
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'],
                    'idEmpresa' => $_SESSION['cod_empresa'],
                    'idSucursal' => $_SESSION['cod_UnidadNeg'],
                    'estado' => $_POST['estado'],
                    'fechaRegistro' => $fechaActual,
                    'fechaMod' => $fechaActual,
                    'userMod' => $_SESSION['CodEmpleado'],
                    'codigoAmbiente' => $_POST['codigoAmbiente']
                ];
                $ambiente->crear($data);
                echo json_encode(['status' => true, 'message' => 'Ambiente registrado con éxito.']);
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        break;

    case 'ActualizarAmbiente':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'IdAmbiente' => $_POST['IdAmbiente'],
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'],
                    'idSucursal' => $_POST['idSucursal'],
                    'estado' => $_POST['estado'],
                    'fechaMod' => $fechaActual,
                    'userMod' => $_SESSION['CodEmpleado'],
                    'codigoAmbiente' => $_POST['codigoAmbiente'] // Add this line
                ];
                $ambiente->actualizar($data['IdAmbiente'], $data);
                echo "Ambiente actualizado con éxito.";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }

        break;
    
    case 'Desactivar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'IdAmbiente' => $_POST['IdAmbiente'],
                    'estado' => 0,
                    'fechaMod' => $fechaActual,
                    'userMod' => $_SESSION['CodEmpleado'],
                ];
                $ambiente->desactivar($data['IdAmbiente'], $data);
                echo json_encode(['status' => true, 'message' => 'Ambiente desactivado con éxito.']);
            } catch (PDOException $e) {
                echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
        break;

    case 'ListarAmbientes':
        try {
            $cod_empresa = $_POST['cod_empresa'] ?? null;
            $cod_UnidadNeg = $_POST['cod_UnidadNeg'] ?? null;
            $data = $ambiente->listarTodo($cod_empresa, $cod_UnidadNeg);
            error_log("Listar Ambientes: " . json_encode($data), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode($data ?: []);
        } catch (\Throwable $th) {
            error_log("Error Listar Ambientes: " . $th->getMessage(), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode(['status' => false, 'message' => 'Error al listar ambientes: ' . $th->getMessage()]);
        }

        break;
    
    case 'combos':
        try {
            //code...
            $empresas = $combos->comboEmpresa();
            $combos['empresas'] = '<option value="">Seleccione</option>';
            foreach ($empresas as $row) {
                $combos['empresas'] .= "<option value='{$row['cod_empresa']}'>{$row['Razon_empresa']}</option>";
            }

            $sucursales = $combo->comboSucursal();
            $combos['sucursales'] = '<option value="">Seleccione</option>';
            foreach ($sucursales as $row) {
                $combos['sucursales'] .= "<option value='{$row['idSucursal']}'>{$row['nombre']}</option>";
            }

        } catch (Exception $e) {
            error_log("Error Combos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar combos: ' . $e->getMessage()]);
        }
        break;

    default:
        echo "Acción no válida.";
        break;
}
