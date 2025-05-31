<?php

class Auditorias {
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    //! LISTAR LOGS MULTIPLES CONSULTAS CON PROCEDIMIENTOS ALMACENADOS
    public function AuditoriasLogs($filters){
        try{
            $query = 'SELECT * FROM tLogAuditoria WHERE 1=1';
            $params = [];
            if (!empty($filters['usuario'])){
                $query .= ' AND usuario = ?';
                $params[] = $filters['usuario'];
            }
            if (!empty($filters['Accion'])){
                $query .= ' AND Accion = ?';
                $params[] = $filters['Accion'];
            }
            if (!empty($filters['Tabla'])){
                $query .= ' AND Tabla = ?';
                $params[] = $filters['Tabla'];
            }
            if (!empty($filters['FechaInicio'])){
                $query .= ' AND Fecha >= ?';
                $params[] = $filters['FechaInicio'];
            }
            if (!empty($filters['FechaFin'])){
                $query .= ' AND Fecha <= ?';
                $params[] = $filters['FechaFin'];
            }
            $query .= ' ORDER BY fecha DESC';

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e){
            error_log("Error in AuditoriasLogs: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function ListarLogs(){
        try {
            $stmt = $this->db->query('SELECT * FROM tLogAuditoria ORDER BY fecha DESC');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error in Logs::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}

?>