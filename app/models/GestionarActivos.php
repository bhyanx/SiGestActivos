<?php
require_once '../config/configuracion.php';

class GestionarActivos
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    public function consultarActivos($data)
    {
        try {
            // Convertir cadenas vacías a null o enteros para parámetros numéricos
            $pCodigo = empty($data['pCodigo']) ? null : $data['pCodigo'];
            $pIdSucursal = empty($data['pIdSucursal']) ? null : (int)$data['pIdSucursal'];
            $pIdCategoria = empty($data['pIdCategoria']) ? null : (int)$data['pIdCategoria'];
            $pIdEstado = empty($data['pIdEstado']) ? null : (int)$data['pIdEstado'];

            $stmt = $this->db->prepare('EXEC sp_ConsultarActivos @pCodigo = ?, @pIdSucursal = ?, @pIdCategoria = ?, @pIdEstado = ?');
            $stmt->bindParam(1, $pCodigo, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(2, $pIdSucursal, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(3, $pIdCategoria, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(4, $pIdEstado, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in consultarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function registrarActivos($data)
    {
        try {
            $stmt = $this->db->prepare('EXEC sp_GuardarActivo 
                @pIdActivo = ?, 
                @pIdDocIngresoAlm = ?, 
                @pIdArticulo = ?, 
                @pCodigo = ?, 
                @pSerie = ?, 
                @pIdEstado = ?, 
                @pGarantia = ?, 
                @pFechaFinGarantia = ?, 
                @pIdProveedor = ?, 
                @pObservaciones = ?, 
                @pIdSucursal = ?, 
                @pIdAmbiente = ?, 
                @pIdCategoria = ?, 
                @pVidaUtil = ?, 
                @pValorAdquisicion = ?, 
                @pFechaAdquisicion = ?, 
                @pUserMod = ?, 
                @pAccion = 1');

            $stmt->bindParam(1, $data['IdActivo'], \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(2, $data['IdDocIngresoAlm'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['IdArticulo'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['Codigo'], \PDO::PARAM_STR);
            $stmt->bindParam(5, $data['Serie'], \PDO::PARAM_STR);
            $stmt->bindParam(6, $data['IdEstado'], \PDO::PARAM_INT);
            $stmt->bindParam(7, $data['Garantia'], \PDO::PARAM_STR);
            $stmt->bindParam(8, $data['FechaFinGarantia'], \PDO::PARAM_STR);
            $stmt->bindParam(9, $data['IdProveedor'], \PDO::PARAM_INT);
            $stmt->bindParam(10, $data['Observaciones'], \PDO::PARAM_STR);
            $stmt->bindParam(11, $data['IdSucursal'], \PDO::PARAM_INT);
            $stmt->bindParam(12, $data['IdAmbiente'], \PDO::PARAM_INT);
            $stmt->bindParam(13, $data['IdCategoria'], \PDO::PARAM_INT);
            $stmt->bindParam(14, $data['VidaUtil'], \PDO::PARAM_INT);
            $stmt->bindParam(15, $data['ValorAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(16, $data['FechaAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(17, $data['UserMod'], \PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in registrarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function actualizarActivos($data)
    {
        try {
            $stmt = $this->db->prepare('EXEC sp_GuardarActivo 
                @pIdActivo = ?, 
                @pIdDocIngresoAlm = ?, 
                @pIdArticulo = ?, 
                @pCodigo = ?, 
                @pSerie = ?, 
                @pIdEstado = ?, 
                @pGarantia = ?, 
                @pFechaFinGarantia = ?, 
                @pIdProveedor = ?, 
                @pObservaciones = ?, 
                @pIdSucursal = ?, 
                @pIdAmbiente = ?, 
                @pIdCategoria = ?, 
                @pVidaUtil = ?, 
                @pValorAdquisicion = ?, 
                @pFechaAdquisicion = ?, 
                @pUserMod = ?, 
                @pAccion = 2');

            $stmt->bindParam(1, $data['IdActivo'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdDocIngresoAlm'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['IdArticulo'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['Codigo'], \PDO::PARAM_STR);
            $stmt->bindParam(5, $data['Serie'], \PDO::PARAM_STR);
            $stmt->bindParam(6, $data['IdEstado'], \PDO::PARAM_INT);
            $stmt->bindParam(7, $data['Garantia'], \PDO::PARAM_STR);
            $stmt->bindParam(8, $data['FechaFinGarantia'], \PDO::PARAM_STR);
            $stmt->bindParam(9, $data['IdProveedor'], \PDO::PARAM_INT);
            $stmt->bindParam(10, $data['Observaciones'], \PDO::PARAM_STR);
            $stmt->bindParam(11, $data['IdSucursal'], \PDO::PARAM_INT);
            $stmt->bindParam(12, $data['IdAmbiente'], \PDO::PARAM_INT);
            $stmt->bindParam(13, $data['IdCategoria'], \PDO::PARAM_INT);
            $stmt->bindParam(14, $data['VidaUtil'], \PDO::PARAM_INT);
            $stmt->bindParam(15, $data['ValorAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(16, $data['FechaAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(17, $data['UserMod'], \PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in actualizarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}