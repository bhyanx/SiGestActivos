<?php

class Roles{

    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    //* LISTAR ROLES DE USUARIOS
    public function listarTodo(){
        try{
            $stmt = $this->db->query('SELECT * FROM tRoles ORDER BY NombreRol');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in Roles::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* LISTAR ROLES POR ID
    public function listarPorId($IdRol){
        try{
            $stmt = $this->db->prepare('SELECT * FROM tRoles WHERE IdRol = ?');
            $stmt->execute([$IdRol]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in Roles::listarPorId: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //? FUNCIONES CRUD OPCIONALES PARA ADMINISTRAR ROLES

    //* CREAR ROLES
    public function crear($data){
        try{
            $stmt = $this->db->prepare('INSERT INTO tRoles (NombreRol, Estado, UserUpdate, FechaUpdate) VALUES (?, ?, ?, GETDATE())');
            $stmt->execute([$data['NombreRol'], $data['Estado'], $data['UserUpdate']]);
            return $this->db->lastInsertId();
        }catch(\PDOException $e){
            error_log("Error in Roles::crear: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FIN ELIMINAR ROLES

    //* ACTUALIZAR ROLES
    public function actualizar($IdRol, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tRoles SET NombreRol = ?, Estado = ?, UserUpdate = ?, FechaUpdate = GETDATE() WHERE IdRol = ?');
            $stmt->execute([$data['NombreRol'], $data['Estado'], $data['UserUpdate'], $IdRol]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in Roles::actualizar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* ACTIVAR ROLES 
    public function activar($IdRol, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tRoles SET Estado = 1, UserUpdate = ?, FechaUpdate = GETDATE() WHERE IdRol = ?');
            $stmt->execute([$data['UserUpdate'], $IdRol]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in Roles::activar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
    
    //* DESACTIVAR ROLES
    public function desactivar($IdRol, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tRoles SET Estado = 0, UserUpdate = ?, FechaUpdate = GETDATE() WHERE IdRol = ?');
            $stmt->execute([$data['UserUpdate'], $IdRol]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in Roles::desactivar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}

?>