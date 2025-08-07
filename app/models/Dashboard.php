<?php
require_once '../config/configuracion.php';

class Dashboard
{

    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    public function consultarResumenActivos($data)
    {
        try {
            error_log("Data recibida en modelo: " . print_r($data, true), 3, __DIR__ . '/../../logs/debug.log');
            //$empresa = $_SESSION['cod_empresa'] ??  null; 
            //$sucursal = $_SESSION['cod_UnidadNeg'] ?? null;
            // Convertir cadenas vacías a null o enteros para parámetros numéricos
            $pIdArticulo = empty($data['pIdArticulo']) ? null : $data['pIdArticulo'];
            $pCodigo = empty($data['pCodigo']) ? null : $data['pCodigo'];
            $pIdEmpresa = empty($data['pIdEmpresa']) ? null : $data['pIdEmpresa'];
            $pIdSucursal = empty($data['pIdSucursal']) ? null : (int)$data['pIdSucursal'];
            $pIdCategoria = empty($data['pIdCategoria']) ? null : (int)$data['pIdCategoria'];
            $pIdEstado = empty($data['pIdEstado']) ? null : (int)$data['pIdEstado'];
            $pAccion = 1;

            error_log("Valor de pIdCategoria después de procesamiento: " . $pIdCategoria, 3, __DIR__ . '/../../logs/debug.log');

            $stmt = $this->db->prepare('EXEC sp_ListadoDashboard @pIdArticulo = ?, @pCodigo = ?, @pIdEmpresa = ?, @pIdSucursal = ?, @pIdCategoria = ?, @pIdEstado = ?, @pAccion = ?');
            $stmt->bindParam(1, $pIdArticulo, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(2, $pCodigo, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(3, $pIdEmpresa, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(4, $pIdSucursal, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(5, $pIdCategoria, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(6, $pIdEstado, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(7, $pAccion, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in consultarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }


    public function dashboardConteo($data)
    {
        try {
            $pIdArticulo = empty($data['pIdArticulo']) ? null : $data['pIdArticulo'];
            $pCodigo = empty($data['pCodigo']) ? null : $data['pCodigo'];
            $pIdEmpresa = empty($data['pIdEmpresa']) ? null : $data['pIdEmpresa'];
            $pIdSucursal = empty($data['pIdSucursal']) ? null : (int)$data['pIdSucursal'];
            $pIdCategoria = empty($data['pIdCategoria']) ? null : (int)$data['pIdCategoria'];
            $pIdEstado = empty($data['pIdEstado']) ? null : (int)$data['pIdEstado'];
            $pAccion = 2;
        
            $stmt = $this->db->prepare('EXEC sp_ListadoDashboard @pIdArticulo = ?, @pCodigo = ?, @pIdEmpresa = ?, @pIdSucursal = ?, @pIdCategoria = ?, @pIdEstado = ?, @pAccion = ?');
            $stmt->bindParam(1, $pIdArticulo, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(2, $pCodigo, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(3, $pIdEmpresa, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(4, $pIdSucursal, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(5, $pIdCategoria, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(6, $pIdEstado, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(7, $pAccion, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Error in consultarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
