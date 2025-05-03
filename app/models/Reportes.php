<?php

class Reportes {
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    public function ListarReportes($filters){
        try{
            $stmt = $this->db->prepare('EXEC sp_GetReporteActivos @idSucursal = ?, @idEstado = ?, @idCategoria = ?, @idProveedor = ?');

            $stmt->bindParam(1, $filters['idSucursal'], \PDO::PARAM_STR);
            $stmt->bindParam(2, $filters['idEstado'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $filters['idCategoria'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $filters['idProveedor'], \PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch (\PDOException $e){
            error_log("Error in ListarReportes: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}

?>