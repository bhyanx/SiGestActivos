<?php

class GestionarActivos{
    
    private $db;

    public function __construct() {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }
    
    public function gestionActivos($accion,$data){
        try{
            $stmt = $this->db->prepare('EXEC sp_GestionActivos @pAccion = ?, @idActivo = ?, @idDocIngresoAlm = ?, @idArticulo = ?, @codigo = ?, @serie = ?, @idEstado = ?, @enUso = ?, @idSucursal = ?, @idAmbiente = ?, @idCategoria = ?, @vidaUtil = ?, @valorAdquisicion = ?, @fechaAdquisicion = ?, @garantia = ?, @fechaFinGarantia = ?, @idProveedor = ?, @observaciones = ?, @fechaInicio = ?, @fechaFin = ?, @userMod = ?, @idGenerado = ? OUTPUT');

            $idGenerado = 0;
            $stmt->bindParam(1, $accion);
            $stmt->bindParam(2, $data['idActivo'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['idDocIngresoAlm'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['idArticulo'], \PDO::PARAM_INT);
            $stmt->bindParam(5, $data['codigo']);
            $stmt->bindParam(6, $data['serie']);
            $stmt->bindParam(7, $data['idEstado'], \PDO::PARAM_INT);
            $stmt->bindParam(8, $data['enUso'], \PDO::PARAM_BOOL);
            $stmt->bindParam(9, $data['idSucursal'], \PDO::PARAM_STR);
            $stmt->bindParam(10, $data['idAmbiente'], \PDO::PARAM_INT);
            $stmt->bindParam(11, $data['idCategoria'], \PDO::PARAM_INT);
            $stmt->bindParam(12, $data['vidaUtil'], \PDO::PARAM_INT);
            $stmt->bindParam(13, $data['valorAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(14, $data['fechaAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(15, $data['garantia'], \PDO::PARAM_BOOL);
            $stmt->bindParam(16, $data['fechaFinGarantia'], \PDO::PARAM_STR);
            $stmt->bindParam(17, $data['idProveedor'], \PDO::PARAM_INT);
            $stmt->bindParam(18, $data['observaciones'], \PDO::PARAM_STR);
            $stmt->bindParam(19, $data['fechaInicio']);
            $stmt->bindParam(20, $data['fechaFin']);
            $stmt->bindParam(21, $data['userMod']);
            $stmt->bindParam(22, $data['idGenerado'], \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT);

            $stmt->execute();

            if ($accion === 'CONSULTAR' ){
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            return $idGenerado;
        } catch(\PDOException $e){
            error_log("Error in gestionActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;

        }
    }

    public function getById($id){
        $data = [
            'idActivo' => $id,
            'idDocIngresoAlm' => null,
            'idArticulo' => null,
            'codigo' => null,
            'idSucursal' => null,
            'idEstado' => null,
            'enUso' => null,
            'fechaInicio' => null,
            'fechaFin' => null,
            'userMod' => null
        ];
        $result = $this->gestionActivos('CONSULTAR', $data);
        return $result ? $result[0] : null;
    }
}

?>

