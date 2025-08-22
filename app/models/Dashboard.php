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

    public function TotalActivosAsignados(){
        try {
            // Consulta directa para contar activos asignados
            $sql = "SELECT COUNT(a.idActivo) AS total 
                    FROM vActivos AS a 
                    INNER JOIN vEmpleados AS e 
                    ON a.idResponsable = e.codTrabajador 
                    WHERE a.idResponsable IS NOT NULL 
                    AND e.codTrabajador IS NOT NULL 
                    AND (idEstado NOT IN(3,4))";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            error_log("TotalActivosAsignados - Resultado: " . print_r($result, true), 3, __DIR__ . '/../../logs/debug.log');
            
            // Devolver un objeto con la cantidad
            return ['cantidad' => (int)$result['total']];
        } catch (\PDOException $e) {
            error_log("Error en TotalActivosAsignados: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function TotalActivosNoAsignados(){
        try {
            // Consulta directa para contar activos no asignados
            $sql = "SELECT COUNT(a.idActivo) AS total 
                    FROM vActivos AS a 
                    LEFT JOIN vEmpleados AS e 
                    ON a.idResponsable = e.codTrabajador 
                    WHERE a.idResponsable IS NULL 
                    OR e.codTrabajador IS NULL AND (idEstado NOT IN(3,4))";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            error_log("TotalActivosNoAsignados - Resultado: " . print_r($result, true), 3, __DIR__ . '/../../logs/debug.log');
            
            // Devolver un objeto con la cantidad
            return ['cantidad' => (int)$result['total']];
        } catch (\PDOException $e) {
            error_log("Error en TotalActivosNoAsignados: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
