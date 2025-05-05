<?php
class GestionarMovimientos{
    
    private $db;
    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    //! REGISTRAR MOVIMIENTO CON PROCEDIMIENTOS ALMACENADOS (escritura diferente)
    public function registrarMovimiento($data){
        try{
            $stmt = $this->db->prepare('EXEC sp_GestionMovimientos @pTipoRegistro = ?, @idMovimiento = ?, @idFicha = ?, @idActivo = ?, @idTipoMovimiento = ?, @idAmbienteOrigen = ?, @idAmbienteDestino = ?, @idResponsableNuevo = ?, @idAutorizador = ?, @observaciones = ?, @userMod = ?, @idGenerado = ?');
            
            // idFicha, idActivo, idTipoMovimiento, idAmbienteOrigen, idAmbienteDestino, idActivoPadreOrigen,
            // idActivoPadreDestino, idResponsableAnterior, idResponsableNuevo, idEstadoMovimiento, observaciones,
            // fechaRegistro, userMod

            $stmt->bindParam(1, $data['pTipoRegistro'], \PDO::PARAM_STR); // 'INDIVIDUAL' o 'MASIVO'
            $stmt->bindParam(2, $data['idMovimiento'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['idFicha'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['idActivo'], \PDO::PARAM_INT);
            $stmt->bindParam(5, $data['idTipoMovimiento'], \PDO::PARAM_INT);
            $stmt->bindParam(6, $data['idAmbienteOrigen'], \PDO::PARAM_INT);
            $stmt->bindParam(7, $data['idAmbienteDestino'], \PDO::PARAM_INT);
            $stmt->bindParam(8, $data['idResponsableNuevo'], \PDO::PARAM_STR);
            $stmt->bindParam(9, $data['idAutorizador'], \PDO::PARAM_STR);
            $stmt->bindParam(10, $data['observaciones'], \PDO::PARAM_STR);
            $stmt->bindParam(11, $data['userMod'], \PDO::PARAM_STR);
            $stmt->bindParam(12, $data['idGenerado'], \PDO::PARAM_INT);
            $stmt->execute();
            return true;
        }catch (\PDOException $e){
            error_log("Error in registrarMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* LISTAR MOVIMIENTOS

    public function listarMovimientos($idActivo){
        try{
            $stmt = $this->db->prepare('SELECT * FROM vHistorialMovimientosPorActivo WHERE idActivo = ? ORDER BY fechaMovimiento DESC');
            $stmt->execute([$idActivo]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in listarMovimientos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* LISTAR MOVIMIENTOS POR ID

    // public function listarMovimientosId($idActivo){
    //     try{
    //         $stmt = $this->db->prepare('SELECT * FROM tMovimientos WHERE idActivo = ? ORDER BY fechaRegistro DESC');
    //         $stmt->execute([$idActivo]);
    //         return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    //     }catch (\PDOException $e){
    //         error_log("Error in listarMovimientosId: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
    //         throw $e;
    //     }
    // }
}

?>

