<?php
session_start();
require_once '../config/configuracion.php';
require_once '../models/Configuracion.php';

// Desactivar display_errors para evitar HTML en respuestas JSON
ini_set('display_errors', 0);

header('Content-Type: application/json');

class ConfiguracionController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Configuracion();
    }

    public function bloquearEdicion()
    {
        try {
            if (!isset($_POST['idActivo']) || empty($_POST['idActivo'])) {
                echo json_encode(['status' => false, 'message' => 'ID de activo requerido']);
                return;
            }

            $idActivo = (int)$_POST['idActivo'];

            $resultado = $this->modelo->bloquearEdicion($idActivo);

            if ($resultado) {
                echo json_encode([
                    'status' => true,
                    'message' => 'Activo bloqueado para edición exitosamente',
                    'esEditable' => 0
                ]);
            } else {
                echo json_encode(['status' => false, 'message' => 'Error al bloquear el activo']);
            }
        } catch (Exception $e) {
            error_log("Error en bloquearEdicion: " . $e->getMessage());
            echo json_encode(['status' => false, 'message' => 'Error interno del servidor']);
        }
    }

    public function permitirEdicion()
    {
        try {
            if (!isset($_POST['idActivo']) || empty($_POST['idActivo'])) {
                echo json_encode(['status' => false, 'message' => 'ID de activo requerido']);
                return;
            }

            $idActivo = (int)$_POST['idActivo'];

            $resultado = $this->modelo->permitirEdicion($idActivo);

            if ($resultado) {
                echo json_encode([
                    'status' => true,
                    'message' => 'Activo desbloqueado para edición exitosamente',
                    'esEditable' => 1
                ]);
            } else {
                echo json_encode(['status' => false, 'message' => 'Error al desbloquear el activo']);
            }
        } catch (Exception $e) {
            error_log("Error en permitirEdicion: " . $e->getMessage());
            echo json_encode(['status' => false, 'message' => 'Error interno del servidor']);
        }
    }

    public function obtenerEstadoEditable()
    {
        try {
            if (!isset($_POST['idActivo']) || empty($_POST['idActivo'])) {
                echo json_encode(['status' => false, 'message' => 'ID de activo requerido']);
                return;
            }

            $idActivo = (int)$_POST['idActivo'];

            $estadoEditable = $this->modelo->obtenerEstadoEditable($idActivo);

            echo json_encode([
                'status' => true,
                'esEditable' => $estadoEditable
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerEstadoEditable: " . $e->getMessage());
            echo json_encode(['status' => false, 'message' => 'Error interno del servidor']);
        }
    }

    public function obtenerHistorialEventos()
    {
        try {
            if (!isset($_POST['idActivo']) || empty($_POST['idActivo'])) {
                echo json_encode(['status' => false, 'message' => 'ID de activo requerido']);
                return;
            }

            $idActivo = (int)$_POST['idActivo'];

            $historial = $this->modelo->obtenerHistorialEventos($idActivo);

            echo json_encode([
                'status' => true,
                'data' => $historial
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerHistorialEventos: " . $e->getMessage());
            echo json_encode(['status' => false, 'message' => 'Error interno del servidor']);
        }
    }

    public function toggleEdicion()
    {
        try {
            if (!isset($_POST['idActivo']) || empty($_POST['idActivo'])) {
                echo json_encode(['status' => false, 'message' => 'ID de activo requerido']);
                return;
            }

            $idActivo = (int)$_POST['idActivo'];

            // Obtener el estado actual
            $estadoActual = $this->modelo->obtenerEstadoEditable($idActivo);

            if ($estadoActual === 1) {
                // Está editable, bloquearlo
                $resultado = $this->modelo->bloquearEdicion($idActivo);
                $nuevoEstado = 0;
                $mensaje = 'Activo bloqueado para edición';
            } else {
                // Está bloqueado, permitir edición
                $resultado = $this->modelo->permitirEdicion($idActivo);
                $nuevoEstado = 1;
                $mensaje = 'Activo desbloqueado para edición';
            }

            if ($resultado) {
                echo json_encode([
                    'status' => true,
                    'message' => $mensaje,
                    'esEditable' => $nuevoEstado
                ]);
            } else {
                echo json_encode(['status' => false, 'message' => 'Error al cambiar el estado del activo']);
            }
        } catch (Exception $e) {
            error_log("Error en toggleEdicion: " . $e->getMessage());
            echo json_encode(['status' => false, 'message' => 'Error interno del servidor']);
        }
    }
}

// Manejo de las acciones
if (isset($_GET['action'])) {
    $controller = new ConfiguracionController();

    switch ($_GET['action']) {
        case 'bloquearEdicion':
            $controller->bloquearEdicion();
            break;
        case 'permitirEdicion':
            $controller->permitirEdicion();
            break;
        case 'obtenerEstadoEditable':
            $controller->obtenerEstadoEditable();
            break;
        case 'obtenerHistorialEventos':
            $controller->obtenerHistorialEventos();
            break;
        case 'toggleEdicion':
            $controller->toggleEdicion();
            break;
        default:
            echo json_encode(['status' => false, 'message' => 'Acción no válida']);
            break;
    }
}