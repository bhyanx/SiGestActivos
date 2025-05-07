<?php

class EstadoActivo{

    private $db;

    public function __construct(){
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    //* FUNCIÓN PARA LISTAR ESTADOS DE ACTIVOS
    public function listarTodo(){
        try{
            $stmt = $this->db->query('SELECT * FROM tEstadoActivo ORDER BY nombre');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in EstadoActivo::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FIN FUNCIÓN PARA LISTAR ESTADOS DE ACTIVOS

    //! FUNCIONES PARA EL CRUD ESTADOS DE ACTIVOS

    //* CREAR ESTADOS DE ACTIVOS
    public function crear($data){
        try{
            $stmt = $this->db->prepare('INSERT INTO tEstadoActivo (nombre, descripcion, estado) VALUES (?, ?, ?)');
            $stmt->execute([$data['nombre'], $data['descripcion'], $data['estado']]);
            return $this->db->lastInsertId();
        }catch(\PDOException $e){
            error_log("Error in EstadoActivo::crear: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function actualizar($data){
        try{
            $stmt = $this->db->prepare('UPDATE tEstadoActivo SET nombre = ?, descripcion = ?, estado = ? WHERE idEstadoActivo = ?');
            $stmt->execute([$data['nombre'], $data['descripcion'], $data['estado'], $data['idEstadoActivo']]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in EstadoActivo::actualizar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}

?>

<!--   [idEstadoActivo]
      ,[nombre]
      ,[descripcion]
      ,[esFinal] -->