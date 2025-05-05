<?php 

class GestionarDepreciacion{
    private $db;

    public function __construct(){
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    public function registrarDepreciacion($data) {
        try {
            $stmt = $this->db->prepare('EXEC sp_RegistrarDepreciacion @pIdActivo = ?, @pFechaDepreciacion = ?, @pMetodoDepreciacion = ?, @pUserMod = ?');
            $stmt->bindParam(1, $data['IdActivo'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['FechaDepreciacion'], \PDO::PARAM_STR);
            $stmt->bindParam(3, $data['MetodoDepreciacion'], \PDO::PARAM_STR);
            $stmt->bindParam(4, $data['UserMod'], \PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in registrarDepreciacion: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function listarDepreciacionesPorActivo($idActivo) {
        try {
            $stmt = $this->db->prepare('SELECT * FROM tDepreciacion WHERE idActivo = ? ORDER BY fechaDepreciacion DESC');
            $stmt->execute([$idActivo]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in listarDepreciacionesPorActivo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}

?>