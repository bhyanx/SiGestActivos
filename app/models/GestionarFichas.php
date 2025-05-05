<?php 

class GestionarFichas
{
    private $db;
    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    public function generarGuiaRemision($data){
        try{
            $stmt = $this->db->prepare('EXEC sp_GenerarGuiaRemision @pIdFicha = ?, @pSerie = ?, @pNumero = ?, @pFechaEmision = ?, @pPuntoPartida = ?, @pPuntoLlegada = ?, @pRucTransportista = ?, @pRazonSocialTransportista = ?, @pPlaca = ?, @pUserMod = ?');

            $stmt->bindParam(1, $data['IdFicha'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['Serie'], \PDO::PARAM_STR);
            $stmt->bindParam(3, $data['Numero'], \PDO::PARAM_STR);
            $stmt->bindParam(4, $data['FechaEmision'], \PDO::PARAM_STR);
            $stmt->bindParam(5, $data['PuntoPartida'], \PDO::PARAM_STR);
            $stmt->bindParam(6, $data['PuntoLlegada'], \PDO::PARAM_STR);
            $stmt->bindParam(7, $data['RucTransportista'], \PDO::PARAM_STR);
            $stmt->bindParam(8, $data['RazonSocialTransportista'], \PDO::PARAM_STR);
            $stmt->bindParam(9, $data['Placa'], \PDO::PARAM_STR);
            $stmt->bindParam(10, $data['UserMod'], \PDO::PARAM_STR);
            $stmt->execute();
            return true;
        }catch(\PDOException $e){
            error_log("Error in generarGuiaRemision: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function listarFichasPorSucursal($idSucursalDestino){
        try{
            $stmt = $this->db->prepare('SELECT * FROM tFichaMovimiento WHERE idSucursalDestino = ? ORDER BY fechaFicha DESC');
            $stmt->execute([$idSucursalDestino]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in listarFichasPorSucursal: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}

?>