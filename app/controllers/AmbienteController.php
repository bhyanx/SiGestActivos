<?php
/**
 * Ambiente Controller
 *
 * Handles all HTTP requests related to environment management
 * Provides endpoints for CRUD operations on environments
 */

session_start();
require_once("../config/configuracion.php");
require_once("../models/Ambientes.php");
require_once("../models/Combos.php");

/**
 * Controller class for handling environment-related requests
 */
class AmbienteController
{
    private $ambiente;
    private $combos;
    private $currentDate;

    public function __construct()
    {
        $this->ambiente = new Ambientes();
        $this->combos = new Combos();
        $this->currentDate = date("Y-m-d H:i:s");
    }

    /**
     * Main request handler - routes requests to appropriate methods
     */
    public function handleRequest()
    {
        $action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';
        
        try {
            switch ($action) {
                case 'RegistrarAmbiente':
                    $this->registerEnvironment();
                    break;
                    
                case 'ActualizarAmbiente':
                    $this->updateEnvironment();
                    break;
                    
                case 'Desactivar':
                    $this->deactivateEnvironment();
                    break;
                    
                case 'ListarAmbientes':
                    $this->listEnvironments();
                    break;
                    
                case 'combos':
                    $this->getComboData();
                    break;
                    
                default:
                    $this->sendErrorResponse("Acción no válida", 400);
                    break;
            }
        } catch (Exception $e) {
            $this->logError($action, $e->getMessage());
            $this->sendErrorResponse("Error interno del servidor", 500);
        }
    }

    /**
     * Registers a new environment
     */
    private function registerEnvironment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendErrorResponse("Método no permitido", 405);
            return;
        }

        try {
            // Validate session data
            $this->validateSession(['cod_empresa', 'cod_UnidadNeg', 'CodEmpleado']);
            
            // Validate input data
            $validatedData = $this->validateEnvironmentInput($_POST);
            
            $environmentData = [
                'IdAmbiente' => null,
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                'idEmpresa' => $_SESSION['cod_empresa'],
                'idSucursal' => $_SESSION['cod_UnidadNeg'],
                'estado' => $validatedData['estado'] ?? 1,
                'fechaRegistro' => $this->currentDate,
                'fechaMod' => $this->currentDate,
                'userMod' => $_SESSION['CodEmpleado'],
                'codigoAmbiente' => $validatedData['codigoAmbiente'] ?? null
            ];

            $newId = $this->ambiente->crear($environmentData);
            
            $this->sendSuccessResponse([
                'message' => 'Ambiente registrado con éxito',
                'data' => ['id' => $newId]
            ]);

        } catch (InvalidArgumentException $e) {
            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->logError("registerEnvironment", $e->getMessage());
            $this->sendErrorResponse("Error al registrar el ambiente", 500);
        }
    }

    /**
     * Updates an existing environment
     */
    private function updateEnvironment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendErrorResponse("Método no permitido", 405);
            return;
        }

        try {
            // Validate session data
            $this->validateSession(['CodEmpleado']);
            
            // Validate input data
            $validatedData = $this->validateEnvironmentInput($_POST, true);
            
            $environmentData = [
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'],
                'idSucursal' => $validatedData['idSucursal'],
                'estado' => $validatedData['estado'],
                'fechaMod' => $this->currentDate,
                'userMod' => $_SESSION['CodEmpleado'],
                'codigoAmbiente' => $validatedData['codigoAmbiente'] ?? null
            ];

            $affectedRows = $this->ambiente->actualizar($validatedData['IdAmbiente'], $environmentData);
            
            if ($affectedRows > 0) {
                $this->sendSuccessResponse(['message' => 'Ambiente actualizado con éxito']);
            } else {
                $this->sendErrorResponse("No se encontró el ambiente o no se realizaron cambios", 404);
            }

        } catch (InvalidArgumentException $e) {
            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->logError("updateEnvironment", $e->getMessage());
            $this->sendErrorResponse("Error al actualizar el ambiente", 500);
        }
    }

    /**
     * Deactivates an environment
     */
    private function deactivateEnvironment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendErrorResponse("Método no permitido", 405);
            return;
        }

        try {
            // Validate session data
            $this->validateSession(['CodEmpleado']);
            
            // Validate input data
            $idAmbiente = $this->validateRequired($_POST, 'IdAmbiente');
            
            $deactivationData = [
                'estado' => 0,
                'fechaMod' => $this->currentDate,
                'userMod' => $_SESSION['CodEmpleado']
            ];

            $affectedRows = $this->ambiente->desactivar($idAmbiente, $deactivationData);
            
            if ($affectedRows > 0) {
                $this->sendSuccessResponse(['message' => 'Ambiente desactivado con éxito']);
            } else {
                $this->sendErrorResponse("No se encontró el ambiente", 404);
            }

        } catch (InvalidArgumentException $e) {
            $this->sendErrorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            $this->logError("deactivateEnvironment", $e->getMessage());
            $this->sendErrorResponse("Error al desactivar el ambiente", 500);
        }
    }

    /**
     * Lists all environments with optional filtering
     */
    private function listEnvironments()
    {
        try {
            $cod_empresa = $_POST['cod_empresa'] ?? null;
            $cod_UnidadNeg = $_POST['cod_UnidadNeg'] ?? null;
            
            $environments = $this->ambiente->listarTodo($cod_empresa, $cod_UnidadNeg);
            
            // Log successful operation
            $this->logAction("listEnvironments", "Listed " . count($environments) . " environments");
            
            // Send response with proper content type
            $this->sendJsonResponse($environments);

        } catch (Exception $e) {
            $this->logError("listEnvironments", $e->getMessage());
            $this->sendErrorResponse("Error al listar ambientes", 500);
        }
    }

    /**
     * Gets combo box data for forms
     */
    private function getComboData()
    {
        try {
            $comboData = [];
            
            // Get companies combo data
            $empresas = $this->combos->comboEmpresa();
            $comboData['empresas'] = '<option value="">Seleccione</option>';
            foreach ($empresas as $row) {
                $empresa = htmlspecialchars($row['Razon_empresa']);
                $codEmpresa = htmlspecialchars($row['cod_empresa']);
                $comboData['empresas'] .= "<option value='{$codEmpresa}'>{$empresa}</option>";
            }

            // Get branches combo data
            $sucursales = $this->combos->comboSucursal();
            $comboData['sucursales'] = '<option value="">Seleccione</option>';
            foreach ($sucursales as $row) {
                $nombre = htmlspecialchars($row['nombre']);
                $idSucursal = htmlspecialchars($row['idSucursal']);
                $comboData['sucursales'] .= "<option value='{$idSucursal}'>{$nombre}</option>";
            }

            $this->sendJsonResponse($comboData);

        } catch (Exception $e) {
            $this->logError("getComboData", $e->getMessage());
            $this->sendErrorResponse("Error al cargar combos", 500);
        }
    }

    /**
     * Validates environment input data
     */
    private function validateEnvironmentInput($data, $isUpdate = false)
    {
        $validated = [];
        
        // Required fields for creation and update
        $validated['nombre'] = $this->validateRequired($data, 'nombre');
        $validated['descripcion'] = $this->validateRequired($data, 'descripcion');
        
        // Additional fields for update
        if ($isUpdate) {
            $validated['IdAmbiente'] = $this->validateRequired($data, 'IdAmbiente');
            $validated['idSucursal'] = $this->validateRequired($data, 'idSucursal');
            $validated['estado'] = $this->validateRequired($data, 'estado');
        }
        
        // Optional fields
        $validated['codigoAmbiente'] = $data['codigoAmbiente'] ?? null;
        
        return $validated;
    }

    /**
     * Validates that required field exists and is not empty
     */
    private function validateRequired($data, $field)
    {
        if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
            throw new InvalidArgumentException("Campo requerido faltante: {$field}");
        }
        
        return is_string($data[$field]) ? trim($data[$field]) : $data[$field];
    }

    /**
     * Validates that required session variables exist
     */
    private function validateSession($requiredFields)
    {
        foreach ($requiredFields as $field) {
            if (!isset($_SESSION[$field]) || empty($_SESSION[$field])) {
                throw new InvalidArgumentException("Sesión inválida: campo {$field} requerido");
            }
        }
    }

    /**
     * Sends a JSON success response
     */
    private function sendSuccessResponse($data)
    {
        $response = ['status' => true] + $data;
        $this->sendJsonResponse($response);
    }

    /**
     * Sends a JSON error response
     */
    private function sendErrorResponse($message, $httpCode = 400)
    {
        http_response_code($httpCode);
        $this->sendJsonResponse(['status' => false, 'message' => $message]);
    }

    /**
     * Sends a JSON response with proper headers
     */
    private function sendJsonResponse($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Logs error messages
     */
    private function logError($method, $message)
    {
        $logMessage = "Error in AmbienteController::{$method}: {$message}";
        error_log($logMessage, 3, __DIR__ . '/../../logs/errors.log');
    }

    /**
     * Logs action messages
     */
    private function logAction($method, $message)
    {
        $logMessage = "AmbienteController::{$method}: {$message}";
        error_log($logMessage, 3, __DIR__ . '/../../logs/acciones.log');
    }
}

// Initialize and handle the request
$controller = new AmbienteController();
$controller->handleRequest();
