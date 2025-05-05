<?php

class Documentos{

    private $db;

    public function __construct() {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    //* LISTAR TODO

    public function listarTodo() {
        try{
            $stmt = $this->db->query('SELECT * FROM vDocumentosPorActivo ORDER BY fechaRegistro DESC');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in Documentos::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* LISTAR POR ID

    //* FIN LISTAR POR ID
    
    //* FIN LISTAR TODO
}

?>