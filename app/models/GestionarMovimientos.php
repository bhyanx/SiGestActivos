<?php
class GestionarMovimientos{
    
    private $db;
    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    //! REGISTRAR MOVIMIENTO CON PROCEDIMIENTOS ALMACENADOS (escritura diferente)
    public function registrarMovimientoIndividual($data){
        try{
            $stmt = $this->db->prepare('EXEC sp_GestionMovimientos @pTipoRegistro = ?, @idMovimiento = ?, ');

            
// <!-- @pAccion NVARCHAR(20),
// @pTipoRegistro NVARCHAR(20) = 'INDIVIDUAL',
// @idMovimiento INT = NULL,
// @idFicha INT = NULL,
// @idActivo INT = NULL,
// @idTipoMovimiento INT = NULL,
// @idAmbienteOrigen INT = NULL,
// @idAmbienteDestino INT = NULL,
// @idActivoPadreOrigen INT = NULL,
// @idActivoPadreDestino INT = NULL,
// @idResponsableAnterior NVARCHAR(50) = NULL,
// @idResponsableNuevo NVARCHAR(50) = NULL,
// @idEstadoMovimiento INT = NULL,
// @observaciones NVARCHAR(MAX) = NULL,
// @userMod NVARCHAR(50) = NULL,
// @idGenerado INT OUTPUT -->

            $idMovimiento = 0;
            $stmt->bindParam(1, $data['idActivo'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['idTipoMovimiento'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['idAmbienteOrigen'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['idAmbienteDestino'], \PDO::PARAM_INT);
            $stmt->bindParam(5, $data['idResponsableNuevo'], \PDO::PARAM_INT);
            $stmt->bindParam(6, $data['idAutorizador'], \PDO::PARAM_INT);
            $stmt->bindParam(7, $data['observaciones'], \PDO::PARAM_STR);
            $stmt->bindParam(8, $data['userMod'], \PDO::PARAM_STR);
            $stmt->bindParam(9, $data['idMovimiento'], \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT);

            $stmt->execute();
            return $idMovimiento;

        }catch (\PDOException $e){
            error_log("Error in registrarMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function listarMovimientosId($idActivo){
        try{
            $stmt = $this->db->prepare('SELECT * FROM tMovimientos WHERE idActivo = ? ORDER BY fechaRegistro DESC');
            $stmt->execute([$idActivo]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch (\PDOException $e){
            error_log("Error in listarMovimientosId: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}

?>

