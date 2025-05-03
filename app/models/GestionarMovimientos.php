<?php
class GestionarMovimientos{
    
    private $db;
    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    public function registrarMovimiento($data){
        try{
            $stmt = $this->db->prepare('EXEC sp_RegistrarMovimientoActivo @idActivo = ?, @idTipoMovimiento = ?, @idAmbienteOrigen = ?, @idAmbienteDestino = ?, @idResponsableNuevo = ?, @idAutorizador = ?, @observaciones = ?, @userMod = ?, @idMovimiento = ? OUTPUT');

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


class Branch {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getAll() {
        try {
            $stmt = $this->db->query('SELECT * FROM tSucursales ORDER BY nombre');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in Branch::getAll: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function create($data) {
        try {
            $stmt = $this->db->prepare('INSERT INTO tSucursales (cod_UnidadNeg, nombre, direccion, fechaRegistro, userMod) VALUES (?, ?, ?, GETDATE(), ?)');
            $stmt->execute([$data['cod_UnidadNeg'], $data['nombre'], $data['direccion'], $data['userMod']]);
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Error in Branch::create: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function update($cod_UnidadNeg, $data) {
        try {
            $stmt = $this->db->prepare('UPDATE tSucursales SET nombre = ?, direccion = ?, fechaMod = GETDATE(), userMod = ? WHERE cod_UnidadNeg = ?');
            $stmt->execute([$data['nombre'], $data['direccion'], $data['userMod'], $cod_UnidadNeg]);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            error_log("Error in Branch::update: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function getById($cod_UnidadNeg) {
        try {
            $stmt = $this->db->prepare('SELECT * FROM tSucursales WHERE cod_UnidadNeg = ?');
            $stmt->execute([$cod_UnidadNeg]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in Branch::getById: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
?>