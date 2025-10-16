<?php

require_once '../config/configuracion.php';

/**
 * Class Ambientes
 *
 * Handles all database operations related to Ambientes (Environments)
 * Provides CRUD operations for managing environment records
 */
class Ambientes
{
    /**
     * @var PDO Database connection instance
     */
    private $db;

    /**
     * Constructor - Initializes database connection
     */
    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        // Alternative connection for testing
        // $this->db = (new Conectar())->ConexionBdPruebas();
    }

    /**
     * Retrieves all active environments with optional filtering
     *
     * @param int|null $cod_empresa Company code for filtering
     * @param int|null $cod_UnidadNeg Business unit code for filtering
     * @return array List of environments with branch information
     * @throws PDOException If database operation fails
     */
    public function listarTodo($cod_empresa = null, $cod_UnidadNeg = null)
    {
        try {
            $sql = 'SELECT a.*, s.Nombre_local AS NombreSucursal
                    FROM vAmbientes a
                    INNER JOIN vUnidadesdeNegocio s ON a.cod_UnidadNeg = s.cod_UnidadNeg
                    WHERE a.estado = 1';
            $params = [];
            
            // Add company filter if provided
            if ($cod_empresa !== null) {
                $sql .= ' AND s.Cod_Empresa = ?';
                $params[] = $cod_empresa;
            }
            
            // Add business unit filter if provided
            if ($cod_UnidadNeg !== null) {
                $sql .= ' AND a.cod_UnidadNeg = ?';
                $params[] = $cod_UnidadNeg;
            }
            
            $sql .= ' ORDER BY a.nombre';
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            $this->logError("listarTodo", $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieves environments for a specific branch
     *
     * @param int $idSucursal Branch ID
     * @return array List of environments for the specified branch
     * @throws PDOException If database operation fails
     */
    public function listarAmbienteSucursal($idSucursal)
    {
        try {
            // Validate input parameter
            if (!is_numeric($idSucursal) || $idSucursal <= 0) {
                throw new InvalidArgumentException("Invalid branch ID provided");
            }

            $stmt = $this->db->prepare('SELECT * FROM tAmbiente WHERE idSucursal = ? ORDER BY nombre');
            $stmt->execute([$idSucursal]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            $this->logError("listarAmbienteSucursal", $e->getMessage());
            throw $e;
        }
    }

    /**
     * Creates a new environment record
     *
     * @param array $data Environment data containing required fields
     * @return string Last inserted ID
     * @throws PDOException If database operation fails
     * @throws InvalidArgumentException If required data is missing
     */
    public function crear($data)
    {
        try {
            // Validate required fields
            $this->validateEnvironmentData($data, ['nombre', 'descripcion', 'idEmpresa', 'idSucursal', 'estado', 'userMod']);
            
            $sql = 'INSERT INTO tAmbiente (nombre, descripcion, idEmpresa, idSucursal, estado, fechaRegistro, fechaMod, userMod, codigoAmbiente)
                    VALUES (?, ?, ?, ?, ?, GETDATE(), GETDATE(), ?, ?)';
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['nombre'],
                $data['descripcion'],
                $data['idEmpresa'],
                $data['idSucursal'],
                $data['estado'],
                $data['userMod'],
                $data['codigoAmbiente'] ?? null
            ]);
            
            return $this->db->lastInsertId();
            
        } catch (\PDOException $e) {
            $this->logError("crear", $e->getMessage());
            throw $e;
        }
    }

    /**
     * Updates an existing environment record
     *
     * @param int $idAmbiente Environment ID to update
     * @param array $data Updated environment data
     * @return int Number of affected rows
     * @throws PDOException If database operation fails
     * @throws InvalidArgumentException If required data is missing
     */
    public function actualizar($idAmbiente, $data)
    {
        try {
            // Validate required fields
            $this->validateEnvironmentData($data, ['nombre', 'descripcion', 'idSucursal', 'estado', 'userMod']);
            
            if (!is_numeric($idAmbiente) || $idAmbiente <= 0) {
                throw new InvalidArgumentException("Invalid environment ID provided");
            }
            
            $sql = 'UPDATE tAmbiente
                    SET nombre = ?, descripcion = ?, idSucursal = ?, estado = ?, fechaMod = GETDATE(), userMod = ?
                    WHERE idAmbiente = ?';
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['nombre'],
                $data['descripcion'],
                $data['idSucursal'],
                $data['estado'],
                $data['userMod'],
                $idAmbiente
            ]);
            
            return $stmt->rowCount();
            
        } catch (\PDOException $e) {
            $this->logError("actualizar", $e->getMessage());
            throw $e;
        }
    }

    /**
     * Deactivates an environment by setting its status to inactive
     *
     * @param int $idAmbiente Environment ID to deactivate
     * @param array $data Data containing estado and userMod
     * @return int Number of affected rows
     * @throws PDOException If database operation fails
     * @throws InvalidArgumentException If required data is missing
     */
    public function desactivar($idAmbiente, $data)
    {
        try {
            // Validate required fields
            $this->validateEnvironmentData($data, ['estado', 'userMod']);
            
            if (!is_numeric($idAmbiente) || $idAmbiente <= 0) {
                throw new InvalidArgumentException("Invalid environment ID provided");
            }
            
            $sql = 'UPDATE tAmbiente
                    SET estado = ?, fechaMod = GETDATE(), userMod = ?
                    WHERE idAmbiente = ?';
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$data['estado'], $data['userMod'], $idAmbiente]);
            
            return $stmt->rowCount();
            
        } catch (\PDOException $e) {
            $this->logError("desactivar", $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validates that required fields are present in the data array
     *
     * @param array $data Data to validate
     * @param array $requiredFields List of required field names
     * @throws InvalidArgumentException If any required field is missing
     */
    private function validateEnvironmentData($data, $requiredFields)
    {
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                throw new InvalidArgumentException("Required field '{$field}' is missing or empty");
            }
        }
    }

    /**
     * Logs errors to the error log file
     *
     * @param string $method Method name where error occurred
     * @param string $message Error message
     */
    private function logError($method, $message)
    {
        $logMessage = "Error in Ambientes::{$method}: {$message}";
        error_log($logMessage, 3, __DIR__ . '/../../logs/errors.log');
    }
}
