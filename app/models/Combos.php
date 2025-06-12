<?php

require_once '../config/configuracion.php';

class Combos
{

    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    public function comboSucursal()
    {
        $sql = "SELECT cod_UnidadNeg, Nombre_local FROM vUnidadesdeNegocio WHERE estadoFuncionamiento = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboTipoMovimiento()
    {
        $sql = "SELECT idTipoMovimiento, nombre FROM tTipoMovimiento";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboEstadoActivo()
    {
        $sql = "SELECT idEstadoActivo, nombre FROM tEstadoActivo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboResponsable()
    {
        $sql = "SELECT codTrabajador, NombreTrabajador FROM vEmpleados";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboAmbiente()
    {
        $sql = "SELECT idAmbiente, nombre FROM tAmbiente WHERE estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboCategoria()
    {
        $sql = "SELECT IdCategoria, Nombre FROM tCategoriasActivo WHERE Estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboProveedor()
    {
        $sql = "SELECT TOP (100) Documento, RazonSocial FROM vEntidadExternaGeneralProveedor";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboAutorizador()
    {
        $sql = "SELECT codTrabajador, NombreTrabajador FROM vEmpleados";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboRol()
    {
        $sql = "SELECT IdRol, NombreRol FROM tRoles WHERE Estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboEmpresa()
    {
        $sql = "SELECT cod_empresa, Razon_empresa FROM vEmpresas";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboUnidadNegocio($codEmpresa)
    {
        $sql = "SELECT cod_UnidadNeg, Nombre_local FROM vUnidadesdeNegocio WHERE Cod_Empresa = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$codEmpresa]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboAccionesAuditoria(){
        $sql = "SELECT DISTINCT accion FROM tLogAuditoria";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboEmpresas()
    {
        try {
            $sql = "SELECT cod_empresa, nombre FROM tEmpresa WHERE estado = 1 ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in comboEmpresas: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
