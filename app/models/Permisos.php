<?php

class Permisos {

    private $db;

    public function __construct() {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    //* LISTAR TODO

    public function listarTodo() {
        try {
            $stmt = $this->db->query('SELECT * FROM tPermisos ORDER BY nombre');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in Permisos::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
    
    //* LISTAR POR ID

    public function listarPorId($CodPermiso) {
        try {
            $stmt = $this->db->prepare('SELECT * FROM tPermisos WHERE CodPermiso = ?');
            $stmt->execute([$CodPermiso]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in Permisos::listarPorId: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FIN LISTAR POR ID

    //* FIN LISTAR TODO

    //? FUNCIONES CRUD PARA ADMINISTRAR PERMISOS DE USUARIOS

    //* CREAR PERMISOS

    public function crear($data) {
        try {
            $stmt = $this->db->prepare('INSERT INTO tPermisos (CodMenu, IdRol, Permiso, UserUpdate, FechaUpdate) VALUES (?, ?, ?, ?, GETDATE())');
            $stmt->execute([$data['IdMenu'], $data['IdRol'], $data['Permiso'], $data['UserUpdate']]);
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Error in Permisos::crear: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FIN CREAR PERMISOS

    //* ACTUALIZAR PERMISOS

    public function actualizar($CodPermiso, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tPermisos SET CodMenu = ?, IdRol = ?, Permiso = ?, UserUpdate = ?, FechaUpdate = GETDATE() WHERE CodPermiso = ?');
            $stmt->execute([$data['IdMenu'], $data['IdRol'], $data['Permiso'], $data['UserUpdate'], $CodPermiso]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in Permisos::actualizar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FIN ACTUALIZAR PERMISOS

}

?>