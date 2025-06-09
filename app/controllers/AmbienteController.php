<?php
session_start();
require_once("../config/configuracion.php");
require_once("../models/Ambientes.php");

$ambiente = new Ambientes();
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
                    'idSucursal' => $_POST['idSucursal'],
                    'estado' => $_POST['estado'],
                    'fechaRegistro' => $fechaActual,
                    'fechaMod' => $fechaActual,
                    'userMod' => $_SESSION['CodEmpleado']
                ];
                $ambiente->crear($data);
                echo "Ambiente registrado con éxito.";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
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
                    'userMod' => $_SESSION['CodEmpleado']
                ];
                $ambiente->actualizar($data['IdAmbiente'], $data);
                echo "Ambiente actualizado con éxito.";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
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

    default:
        echo "Acción no válida.";
        break;
}