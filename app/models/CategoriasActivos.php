<?php

require_once '../config/configuracion.php';

class CategoriasActivos
{

    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    //* LISTAR TODO 
    public function listarTodo()
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM vCategorias WHERE estado = 1');
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in Categorias::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* CREAR CATEGORIA
    public function crear($data){
        try{
            $stmt = $this->db->prepare('INSERT INTO tCategoriasActivo (nombre, descripcion, vidaUtilEstandar, estado, fechaRegistro, fechaMod, userMod, codigoClase) VALUES (?,?,?,?,GETDATE(), GETDATE(), ?, ?)');
            $stmt->execute([ 
                $data['nombre'],
                $data['descripcion'],
                $data['vidaUtilEstandar'],
                $data['estado'],
                $data['userMod'],
                $data['codigoClase'],
            ]);
            return $this->db->lastInsertId();
        }catch(\PDOException $e){
            error_log("Error in Categorias::crear: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* DESACTIVAR CATEGORIA
    public function desactivar($idCategoria, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tCategoriasActivo SET estado = ?, fechaMod = ?, userMod = ? WHERE idCategoria = ?');
            $stmt->execute([$data['estado'], $data['fechaMod'], $data['userMod'], $idCategoria]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in Categorias::desactivar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
