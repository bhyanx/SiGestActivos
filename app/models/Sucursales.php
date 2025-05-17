<?php

class Sucursales{

    private $db;
    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    //* LISTAR TODO CON MI TABLA SUSUCURSALES
    public function tlistarTodo(){
        try {
            $stmt = $this->db->query('SELECT * FROM tSucursales ORDER BY nombre');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in Sucursales::tlistarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* LISTAR POR ID DE MI TABLA SUSUCURSALES
    public function tlistarId($cod_UnidadNeg){
        try{
            $stmt = $this->db->prepare('SELECT * FROM tSucursales WHERE cod_UnidadNeg = ?');
            $stmt->execute([$cod_UnidadNeg]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in Sucursales::tlistarId: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* LISTAR TODO CON MI VISTA VUNIDADESEMPRESA
    public function vlistarTodo(){
        try{
            $stmt = $this->db->query('SELECT * FROM vUnidadesEmpresa ORDER BY nombreLocal');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in Sucursales::vlistarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* LISTAR POR ID DE MI VISTA VUNIDADESEMPRESA
    public function vListarId($cod_UnidadNeg){
        try{
            $stmt = $this->db->prepare('SELECT * FROM vUnidadesEmpresa WHERE cod_UnidadNeg = ?');
            $stmt->execute([$cod_UnidadNeg]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in Sucursales::vListarId: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //? FUNCIONES ALTERNATIVAS PARA REALIZAR CRUD DE SUCURSALES Y EMPRESAS
    
    //*CREAR SUCURSAL
    public function crear($data){
        try{
            $stmt = $this->db->prepare('INSERT INTO tSucursales(cod_UnidadNeg, nombre, direccion, estado, fechaRegistro, fechaMod, userMod) VALUES (?,?,?, GETDATE(), GETDATE(), ?');
            $stmt->execute([$data['cod_UnidadNeg'], $data['nombre'], $data['direccion'], $data['estado'], $data['userMod']]);
            return $this->db->lastInsertId();
        }catch(\PDOException $e){
            error_log("Error in Sucursales::crear: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;

        }
    }
    //*FIN CREAR SUCURSALES

    //*ACTUALIZAR SUCURSALES

    public function actualizar($cod_UnidadNeg, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tSucursales SET nombre = ?, direccion = ?, fechaMod =  GETDATE(), userMod = ? WHERE cod_UnidadNeg = ?');
            $stmt->execute([$data['nombre'], $data['direccion'], $data['estado'], $data['userMod'], $cod_UnidadNeg]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in Sucursales::actualizar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //*FIN ACTUALIZAR SUCURSALES

    //*MODIFICAR ESTADO SUCURSALES

    public function desactivar($cod_UnidadNeg, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tSucursales SET estado = ?, fechaMod = GETDATE(), userMod = ? WHERE cod_UnidadNeg = ?');
            $stmt->execute([$data['estado'], $data['userMod'], $cod_UnidadNeg]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in Sucursales::desactivar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //*FIN MODIFICAR ESTADO SUCURSALES


}

?>
