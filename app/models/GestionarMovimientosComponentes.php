<?php

class GestionarMovimientosComponentes
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    public function listarActivosPadres($sucursal = null)
    {
        try {
            $sql = "
            SELECT 
                a.IdActivo,
                a.CodigoActivo,
                ISNULL(a.NombreActivoVisible, '') as NombreArticulo,
                ISNULL(a.Marca, '') as MarcaArticulo,
                ISNULL(s.Nombre_local, '') AS Sucursal,
                ISNULL(amb.nombre, '') AS Ambiente,
                ISNULL(a.NumeroSerie, '') as NumeroSerie
            FROM vActivos a
            INNER JOIN vUnidadesdeNegocio s ON a.IdSucursal = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON a.IdAmbiente = amb.idAmbiente
            WHERE a.IdEmpresa = 1 
            AND a.idEstado = 1
            AND a.EsPadre = 1";

            if ($sucursal) {
                $sql .= " AND a.IdSucursal = :sucursal";
            }

            $sql .= " ORDER BY a.NombreActivoVisible";


            $stmt = $this->db->prepare($sql);

            if ($sucursal) {
                $stmt->bindParam(':sucursal', $sucursal, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in listarActivosPadres: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function listarComponentesActivo($idActivoPadre)
    {
        try {
            $sql = "
            SELECT 
                a.IdActivo,
                a.CodigoActivo,
                ISNULL(a.NombreActivoVisible, '') as NombreArticulo,
                ISNULL(a.Marca, '') as MarcaArticulo,
                ISNULL(a.NumeroSerie, '') as NumeroSerie,
                ISNULL(s.Nombre_local, '') AS Sucursal,
                ISNULL(amb.nombre, '') AS Ambiente
            FROM vActivos a
            INNER JOIN vUnidadesdeNegocio s ON a.IdSucursal = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON a.IdAmbiente = amb.idAmbiente
            WHERE a.idActivoPadre = ?
            AND a.idEstado = 1
            ORDER BY a.NombreActivoVisible";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idActivoPadre, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in listarComponentesActivo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function moverComponenteEntreActivos($data)
    {
        try {
            $this->db->beginTransaction();

            $sql = "EXEC sp_MoverComponenteEntreActivos 
                @IdMovimiento = ?, 
                @IdActivoComponente = ?, 
                @IdActivoPadreNuevo = ?, 
                @IdTipo_Movimiento = ?, 
                @IdAutorizador = ?,
                @Observaciones = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $data['IdMovimiento'], PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdActivoComponente'], PDO::PARAM_INT);
            $stmt->bindParam(3, $data['IdActivoPadreNuevo'], PDO::PARAM_INT);
            $stmt->bindParam(4, $data['IdTipo_Movimiento'], PDO::PARAM_INT);
            $stmt->bindParam(5, $data['IdAutorizador'], PDO::PARAM_INT);
            $stmt->bindParam(6, $data['Observaciones'], PDO::PARAM_STR);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error in moverComponenteEntreActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function consultarMovimientosEntreActivos($filtros = [])
    {
        try {
            $sql = "
            SELECT 
    dm.IdDetalleMovimiento,
    c.IdActivo AS IdComponente,
    c.CodigoActivo AS CodigoComponente,
    ISNULL(c.NombreActivoVisible, '') AS NombreComponente,
	tm.nombre AS TipoMovimiento,
    ISNULL(ap_origen.CodigoActivo + ' - ' + ap_origen.NombreActivoVisible, '') AS ActivoPadreOrigen,
    ISNULL(ap_destino.CodigoActivo + ' - ' + ap_destino.NombreActivoVisible, '') AS ActivoPadreDestino,
    ISNULL(s.Nombre_local, '') AS Sucursal,
    ISNULL(amb.nombre, '') AS Ambiente,
    ISNULL(t.NombreTrabajador, ' ') AS Autorizador,
    ISNULL(r.NombreTrabajador, ' ') AS Responsable,
    m.FechaMovimiento
FROM tDetalleMovimiento dm
INNER JOIN tMovimientos m ON dm.IdMovimiento = m.IdMovimiento
INNER JOIN vActivos c ON dm.IdActivo = c.IdActivo
LEFT JOIN tTipoMovimiento tm ON dm.IdTipo_Movimiento = tm.idTipoMovimiento
LEFT JOIN vActivos ap_origen ON dm.IdActivoPadre_Anterior = ap_origen.IdActivo
LEFT JOIN vActivos ap_destino ON dm.IdActivoPadre_Nuevo = ap_destino.IdActivo
LEFT JOIN vUnidadesdeNegocio s ON m.IdSucursalDestino = s.cod_UnidadNeg
LEFT JOIN tAmbiente amb ON dm.IdAmbiente_Nuevo = amb.idAmbiente
LEFT JOIN vEmpleados t ON m.IdAutorizador = t.codTrabajador
LEFT JOIN vEmpleados r ON dm.IdResponsable_Nuevo = r.codTrabajador
WHERE m.idTipoMovimiento = 8";

            $params = [];

            if (!empty($filtros['sucursal'])) {
                $sql .= " AND s.cod_UnidadNeg = ?";
                $params[] = $filtros['sucursal'];
            }

            if (!empty($filtros['fecha'])) {
                $sql .= " AND CONVERT(date, m.FechaMovimiento) = ?";
                $params[] = $filtros['fecha'];
            }

            $sql .= " ORDER BY m.FechaMovimiento DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in consultarMovimientosEntreActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
    public function asignarComponenteActivo($data)
    {
        try {
            $stmt = $this->db->prepare('EXEC sp_AsignarComponenteActivo @pIdActivoPadre = ?, @pIdActivoComponente = ?, @pObservaciones = ?, @pFechaAsignacion = ?, @pUserMod = ?');
            $stmt->bindParam(1, $data['IdActivoPadre'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdActivoComponente'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['Observaciones'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(4, $data['FechaAsignacion'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(5, $data['UserMod'], \PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in asignarComponenteActivo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
