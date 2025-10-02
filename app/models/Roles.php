<?php

class Roles
{

    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    //* LISTAR ROLES DE USUARIOS
    public function listarTodo()
    {
        try {
            $stmt = $this->db->query('SELECT * FROM vRoles ORDER BY NombreRol');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in Roles::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function listarPermisosRoles($IdRol)
    {
        try {
            $stmt = $this->db->prepare('SELECT p.CodPermiso,m.CodMenu, p.IdRol, COALESCE(p.Permiso, 0) AS Permiso, m.NombreMenu
                    FROM tMenu m
                    LEFT JOIN tPermisos p ON p.CodMenu = m.CodMenu AND p.IdRol = ?
                    ORDER BY m.NombreMenu ASC');
            $stmt->execute([$IdRol]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in permisos::listarPermisosRoles: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
    //* LISTAR ROLES POR ID
    public function listarPorId($IdRol)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM tRoles WHERE IdRol = ?');
            $stmt->execute([$IdRol]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in Roles::listarPorId: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //? FUNCIONES CRUD OPCIONALES PARA ADMINISTRAR ROLES

    //* CREAR ROLES
    public function crear($data)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO tRoles (NombreRol, Estado, UserUpdate, FechaUpdate) VALUES (?, ?, ?, GETDATE())');
            $stmt->execute([$data['NombreRol'], $data['Estado'], $data['UserUpdate']]);
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Error in Roles::crear: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FIN ELIMINAR ROLES

    //* ACTUALIZAR ROLES
    public function actualizar($IdRol, $data)
    {
        try {
            $stmt = $this->db->prepare('UPDATE tRoles SET NombreRol = ?, Estado = ?, UserUpdate = ?, FechaUpdate = GETDATE() WHERE IdRol = ?');
            $stmt->execute([$data['NombreRol'], $data['Estado'], $data['UserUpdate'], $IdRol]);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            error_log("Error in Roles::actualizar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* ACTIVAR ROLES 
    public function activar($IdRol, $data)
    {
        try {
            $stmt = $this->db->prepare('UPDATE tRoles SET Estado = 1, UserUpdate = ?, FechaUpdate = GETDATE() WHERE IdRol = ?');
            $stmt->execute([$data['UserUpdate'], $IdRol]);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            error_log("Error in Roles::activar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* DESACTIVAR ROLES
    public function desactivar($IdRol, $data)
    {
        try {
            $stmt = $this->db->prepare('UPDATE tRoles SET Estado = 0, UserUpdate = ?, FechaUpdate = GETDATE() WHERE IdRol = ?');
            $stmt->execute([$data['UserUpdate'], $IdRol]);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            error_log("Error in Roles::desactivar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* CAMBIAR ESTADO DE PERMISO
    public function cambiarEstadoPermiso($IdRol, $IdPermiso, $nuevoEstado)
    {
        try {
            $stmt = $this->db->prepare('UPDATE tPermisos SET Permiso = ?, FechaUpdate = GETDATE() WHERE IdRol = ? AND CodPermiso = ?');
            $stmt->execute([$nuevoEstado, $IdRol, $IdPermiso]);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            error_log("Error in Roles::cambiarEstadoPermiso: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
