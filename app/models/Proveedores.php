<?php

class Proveedores{
    
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    //* LISTAR TODO 

    public function listarTodo(){
        try{
            $stmt = $this->db->query('SELECT * FROM vEntidadExternaGeneralProveedor ORDER BY Nombre');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in Proveedores::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* LISTAR MIS PROVEEDORES POR NUMERO DE DOCUMENTO(RUC, DNI) 
    public function listarPorDocumento($Documento){
        try{
            $stmt = $this->db->prepare('SELECT * FROM vEntidadExternaGeneralProveedor WHERE Documento = ?');
            $stmt->execute([$Documento]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in Proveedores::listarPorDocumento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
    public function listarPorNombre($RazonSocial){
        try{
            $stmt = $this->db->prepare('SELECT * FROM vEntidadExternaGeneralProveedor WHERE RazonSocial = ?');
            $stmt->execute([$RazonSocial]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in Proveedores::listarPorNombre: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FIN LISTAR MIS PROVEEDORES POR NUMERO DE DOCUMENTO(RUC, DNI)

    //* FIN LISTAR TODO

}

?>