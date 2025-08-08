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

    public function comboTipoMovimientov1()
    {
        $sql = "SELECT idTipoMovimiento, nombre FROM tTipoMovimiento 
        WHERE idTipoMovimiento NOT IN ( 5, 7, 8)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboEstadoActivo()
    {
        $sql = "SELECT idEstadoActivo, nombre FROM tEstadoActivo WHERE idEstadoActivo NOT IN(3,2,4)";
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

    public function comboReceptor()
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

    public function comboAccionesAuditoria()
    {
        $sql = "SELECT DISTINCT accion FROM tLogAuditoria";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboEmpresas()
    {
        try {
            $sql = "SELECT cod_empresa, Razon_empresa FROM vEmpresas";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in comboEmpresas: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function comboProveedores()
    {
        try {
            $sql = "SELECT Documento, RazonSocial FROM vEntidadExternaGeneralProveedor ORDER BY RazonSocial";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in comboProveedores: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function comboMarcas()
    {
        try{
            $sql = "SELECT codMarca, DescripcionMarca FROM vMarcas";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in comboMarca: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }


    // Nueva función get_Proveedor para búsqueda dinámica con filtro
    public function get_Proveedor($filtro)
    {
        try {
            // Asegurarse de que el filtro siempre sea una cadena
            $filtro = (string) $filtro;

            $sql = "SELECT TOP 20 p.Documento, p.RazonSocial 
FROM vEntidadExternaGeneralProveedor  p
WHERE (p.RazonSocial LIKE ? OR p.Documento LIKE ?) 
ORDER BY p.RazonSocial ";

            $stmt = $this->db->prepare($sql);
            $filtroParam = "%" . $filtro . "%";
            $stmt->execute([$filtroParam, $filtroParam]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in get_Proveedor: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
