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
        $sql = "SELECT cod_UnidadNeg, nombre FROM tSucursales WHERE estado = 1";
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
        $sql = "SELECT codTrabajador, NombreTrabajador FROM vEmpleados WHERE Estado = 1";
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
        $sql = "SELECT codTrabajador, NombreTrabajador FROM vEmpleados 
WHERE estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function comboRol(){
        $sql = "SELECT IdRol, NombreRol FROM tRoles WHERE Estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}


// $stmt = $db->query("SELECT IdCategoria, Nombre FROM tCategoriasActivo ORDER BY Nombre");
//             $combos['categorias'] = '<option value="">Seleccione</option>';
//             foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
//                 $combos['categorias'] .= "<option value='{$row['IdCategoria']}'>{$row['Nombre']}</option>";
//             }

// $stmt = $db->query("SELECT IdProveedor, Nombre FROM tProveedor ORDER BY Nombre");
//             $combos['proveedores'] = '<option value="">Seleccione</option>';
//             foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
//                 $combos['proveedores'] .= "<option value='{$row['IdProveedor']}'>{$row['Nombre']}</option>";
//             }
