<?php

class GestionarMovimientos
{

    private $db;
    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    //! REGISTRAR MOVIMIENTO CON PROCEDIMIENTOS ALMACENADOS (escritura diferente)

    public function crearMovimiento($data)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO tMovimientos (FechaMovimiento, idTipoMovimiento, idAutorizador, idSucursalOrigen, idSucursalDestino, observaciones) VALUES (GETDATE(), ?, ?, ?, ?, ?)');

            $stmt->bindParam(1, $data['idTipoMovimiento'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['idAutorizador'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['idSucursalOrigen'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['idSucursalDestino'], \PDO::PARAM_INT);
            $stmt->bindParam(5, $data['observaciones'], \PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId(); // Devuelve el ID del último movimiento insertado
        } catch (\PDOException $e) {
            error_log("Error in registrarMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function crearDetalleMovimiento($data)
    {
        try {
            $stmt = $this->db->prepare('EXEC sp_RegistrarDetalleMovimiento @IdMovimiento = ?, @IdActivo = ?, @IdSucursal_Nueva = ?,@IdAmbiente_Nueva = ?, @IdResponsable_Nueva = ?, @IdActivoPadreOrigen = ?');

            $stmt->bindParam(1, $data['IdMovimiento'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdActivo'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['IdSucursal_Nueva'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['IdAmbiente_Nueva'], \PDO::PARAM_INT);
            $stmt->bindParam(5, $data['IdResponsable_Nueva'], \PDO::PARAM_INT);
            $stmt->bindParam(6, $data['IdActivoPadreOrigen'], \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error in crearDetalleMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function listarDetalleMovimientos($filtros = [])
    {
        try {
            $sql = "SELECT * FROM vDetalleMovimiento WHERE 1=1";
            $params = [];
            if (!empty($filtros['tipo'])) {
                $sql .= " AND TipoMovimiento = ?";
                $params[] = $filtros['tipo'];
            }
            if (!empty($filtros['sucursal_origen'])) {
                $sql .= " AND SucursalAnterior = ?";
                $params[] = $filtros['sucursal_origen'];
            }
            if (!empty($filtros['sucursal_destino'])) {
                $sql .= " AND SucursalNueva = ?";
                $params[] = $filtros['sucursal_destino'];
            }
            if (!empty($filtros['fecha'])) {
                $sql .= " AND CONVERT(date, FechaMovimiento) = ?";
                $params[] = $filtros['fecha'];
            }
            if (!empty($filtros['ambiente'])) {
                $sql .= " AND (AmbienteAnterior = ? OR AmbienteNuevo = ?)";
                $params[] = $filtros['ambiente'];
                $params[] = $filtros['ambiente'];
            }
            $sql .= " ORDER BY FechaMovimiento DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in listarDetalleMovimientos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //! CODIGO SIN UTILIZAR (NO FUNCIONA) DADO A QUE SE MODIFICÓ LA LOGICA DE USO
    //     SELECT 
    //     dm.IdDetalleMovimiento,
    //     dm.IdActivo,
    //     a.Nombre,

    //     dm.IdSucursal_Anterior,
    //     sa.Nombre AS SucursalAnterior,
    //     dm.IdSucursal_Nueva,
    //     sn.Nombre AS SucursalNueva,

    //     dm.IdAmbiente_Anterior,
    //     aa.Nombre AS AmbienteAnterior,
    //     dm.IdAmbiente_Nuevo,
    //     an.Nombre AS AmbienteNuevo,

    //     dm.IdResponsable_Anterior,
    //     ra.Nombre AS ResponsableAnterior,
    //     dm.IdResponsable_Nuevo,
    //     rn.Nombre AS ResponsableNuevo,

    //     dm.IdActivoPadre_Anterior
    // FROM DetalleMovimiento dm
    // INNER JOIN Activo a ON a.IdActivo = dm.IdActivo
    // LEFT JOIN Sucursal sa ON sa.IdSucursal = dm.IdSucursal_Anterior
    // LEFT JOIN Sucursal sn ON sn.IdSucursal = dm.IdSucursal_Nueva
    // LEFT JOIN Ambiente aa ON aa.IdAmbiente = dm.IdAmbiente_Anterior
    // LEFT JOIN Ambiente an ON an.IdAmbiente = dm.IdAmbiente_Nuevo
    // LEFT JOIN Responsable ra ON ra.IdResponsable = dm.IdResponsable_Anterior
    // LEFT JOIN Responsable rn ON rn.IdResponsable

    // public function registrarMovimiento($data){
    //     try{
    //         $stmt = $this->db->prepare('EXEC sp_GestionMovimientos @pTipoRegistro = ?, @idMovimiento = ?, @idFicha = ?, @idActivo = ?, @idTipoMovimiento = ?, @idAmbienteOrigen = ?, @idAmbienteDestino = ?, @idResponsableNuevo = ?, @idAutorizador = ?, @observaciones = ?, @userMod = ?, @idGenerado = ?');

    //         // idFicha, idActivo, idTipoMovimiento, idAmbienteOrigen, idAmbienteDestino, idActivoPadreOrigen,
    //         // idActivoPadreDestino, idResponsableAnterior, idResponsableNuevo, idEstadoMovimiento, observaciones,
    //         // fechaRegistro, userMod

    //         $stmt->bindParam(1, $data['pTipoRegistro'], \PDO::PARAM_STR); // 'INDIVIDUAL' o 'MASIVO'
    //         $stmt->bindParam(2, $data['idMovimiento'], \PDO::PARAM_INT);
    //         $stmt->bindParam(3, $data['idFicha'], \PDO::PARAM_INT);
    //         $stmt->bindParam(4, $data['idActivo'], \PDO::PARAM_INT);
    //         $stmt->bindParam(5, $data['idTipoMovimiento'], \PDO::PARAM_INT);
    //         $stmt->bindParam(6, $data['idAmbienteOrigen'], \PDO::PARAM_INT);
    //         $stmt->bindParam(7, $data['idAmbienteDestino'], \PDO::PARAM_INT);
    //         $stmt->bindParam(8, $data['idResponsableNuevo'], \PDO::PARAM_STR);
    //         $stmt->bindParam(9, $data['idAutorizador'], \PDO::PARAM_STR);
    //         $stmt->bindParam(10, $data['observaciones'], \PDO::PARAM_STR);
    //         $stmt->bindParam(11, $data['userMod'], \PDO::PARAM_STR);
    //         $stmt->bindParam(12, $data['idGenerado'], \PDO::PARAM_INT);
    //         $stmt->execute();
    //         return true;
    //     }catch (\PDOException $e){
    //         error_log("Error in registrarMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
    //         throw $e;
    //     }
    // }

    //* LISTAR MOVIMIENTOS

    // public function listarMovimientos($idActivo){
    //     try{
    //         $stmt = $this->db->prepare('SELECT * FROM vHistorialMovimientosPorActivo WHERE idActivo = ? ORDER BY fechaMovimiento DESC');
    //         $stmt->execute([$idActivo]);
    //         return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    //     }catch(\PDOException $e){
    //         error_log("Error in listarMovimientos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
    //         throw $e;
    //     }
    // }

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
