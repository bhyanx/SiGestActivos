<?php
//require_once '../config/configuracion.php';
require_once '../models/CategoriasActivos.php';

$categoria = new CategoriasActivos();
$fechaActual = date("Y-m-d");

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

switch ($action) {
    case 'RegistrarCategoria':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'IdCategoria' => null,
                    'nombre' => $_POST['nombre'],
                    'descripcion' => $_POST['descripcion'],
                    'vidaUtilEstandar' => $_POST['vidaUtilEstandar'],
                    'estado' => $_POST['estado'],
                    'fechaRegistro' => $fechaActual,
                    'fechaMod' => $fechaActual,
                    'userMod' => $_SESSION['CodEmpleado'] ?? 'admin',
                    'codigoClase' => $_POST['codigoClase'],
                ];
                $resultado = $categoria->crear($data);
                echo json_encode(['status' => true, 'message' => 'Categoría registrada con éxito', 'data' => ['id' => $resultado]]);
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
        break;

    case 'DesactivarCategoria':
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            try {
                $data = [
                    'IdCategoria' => $_POST['IdCategoria'],
                    'estado' => 0,
                    'fechaMod' => $fechaActual,
                    'userMod' => $_SESSION['CodEmpleado'] ?? 'admin'
                ];
                $resultado = $categoria->desactivar($data['IdCategoria'], $data);
                if ($resultado > 0) {
                    echo json_encode(['status' => true, 'message' => 'Categoría desactivada con éxito']);
                } else {
                    echo json_encode(['status' => false, 'message' => 'No se pudo desactivar la categoría']);
                }
            } catch (PDOException $e) {
                echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
        break;

    case 'ListarCategorias':
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            try {
                $data = $categoria->listarTodo();
                echo json_encode($data);
            } catch(PDOException $e) {
                echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
            }
        }
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Acción no válida']);
        break;
}
?>