<?php

class CategoriasActivos{

    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    //* LISTAR TODO 

    public function listarTodo(){
        try{
            $stmt = $this->db->query('SELECT * FROM tCategoriasActivos ORDER BY nombre');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in CategoriasActivos::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* LISTAR POR ID 

    public function listarPorId($idCategoria){
        try{
            $stmt = $this->db->prepare('SELECT * FROM tCategoriasActivos WHERE idCategoria = ?');
            $stmt->execute([$idCategoria]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in CategoriasActivos::listarPorId: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FIN LISTAR POR ID

    //* LISTAR POR NOMBRE

    public function listarPorNombre($nombre){
        try{
            $stmt = $this->db->prepare('SELECT * FROM tCategoriasActivos WHERE nombre = ?');
            $stmt->execute([$nombre]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in CategoriasActivos::listarPorNombre: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FIN LISTAR POR NOMBRE

    //* FIN LISTAR TODO

    //? FUNCIONES PARA EL CRUD CATEGORIAS DE ACTIVOS

    //* CREAR CATEGORIAS DE ACTIVOS

    public function crear($data){
        try{
            $stmt = $this->db->prepare('INSERT INTO tCategoriasActivos (nombre, descripcion, vidaUtilEstandar, estado, fechaRegistro, fechaMod, userMod) VALUES (?,?,?,?, GETDATE(), GETDATE(), ?)');
            $stmt->execute([$data['nombre'], $data['descripcion'], $data['vidaUtilEstandar'], $data['estado'], $data['userMod']]);
            return $this->db->lastInsertId();
        }catch(\PDOException $e){
            error_log("Error in CategoriasActivos::crear: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;

        }
    }

    //* FIN CREAR CATEGORIAS DE ACTIVOS

    //* ACTUALIZAR CATEGORIAS DE ACTIVOS

    public function actualizar($idCategoria, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tCategoriasActivos SET nombre = ?, descripcion = ?, vidaUtilEstandar = ?, estado = ?, fechaMod= GETDATE(), userMod = ? WHERE idCategoria = ?');
            $stmt->execute([$data['nombre'], $data['descripcion'], $data['vidaUtilEstandar'], $data['estado'], $data['userMod'], $idCategoria]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in CategoriasActivos::actualizar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* DESACTIVAR CATEGORIAS DE ACTIVOS

    public function desactivar($idCategoria, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tCategoriasActivos SET estado = 0, fechaMod = GETDATE(), userMod = ? WHERE idCategoria = ?');
            $stmt->execute([$data['userMod'], $idCategoria]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in CategoriasActivos::desactivar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function activar($idCategoria, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tCategoriasActivos SET estado = 1, fechaMod = GETDATE(), userMod = ? WHERE idCategoria = ?');
            $stmt->execute([$data['userMod'], $idCategoria]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in CategoriasActivos::activar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;

        }
    }

    //* FIN DESACTIVAR CATEGORIAS DE ACTIVOS

    //* FIN ACTUALIZAR CATEGORIAS DE ACTIVOS
}

?>