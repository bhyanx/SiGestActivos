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
                a.codigo as CodigoActivo,
                ISNULL(a.NombreActivo, '') as NombreArticulo,
                ISNULL(a.Marca, '') as MarcaArticulo,
                ISNULL(s.Nombre_local, '') AS Sucursal,
                ISNULL(amb.nombre, '') AS Ambiente,
                ISNULL(a.Serie, '') as NumeroSerie
            FROM vActivos a
            INNER JOIN vUnidadesdeNegocio s ON a.IdSucursal = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON a.IdAmbiente = amb.idAmbiente
            WHERE a.IdEmpresa = 1 
            AND a.idEstado = 1
            AND a.esPadre = 1";

            if ($sucursal) {
                $sql .= " AND a.IdSucursal = :sucursal";
            }

            $sql .= " ORDER BY a.NombreActivo";


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
                a.codigo as CodigoActivo,
                ISNULL(a.NombreActivo, '') as NombreArticulo,
                ISNULL(a.Marca, '') as MarcaArticulo,
                ISNULL(a.Serie, '') as NumeroSerie,
                ISNULL(s.Nombre_local, '') AS Sucursal,
                ISNULL(amb.nombre, '') AS Ambiente
            FROM vActivos a
            INNER JOIN vUnidadesdeNegocio s ON a.IdSucursal = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON a.IdAmbiente = amb.idAmbiente
            WHERE a.idActivoPadre = ?
            AND a.idEstado = 1
            ORDER BY a.NombreActivo";

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

            $sql = "EXEC sp_MoverComponenteActivo 
                @pIdActivoComponente = ?, 
                @pNuevoPadre = ?, 
                @pUserMod = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $data['IdActivoComponente'], PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdActivoPadreNuevo'], PDO::PARAM_INT);
            $stmt->bindParam(3, $data['UserMod'], PDO::PARAM_STR);
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
    hc.idHistorialComponente as IdDetalleMovimiento,
    c.IdActivo AS IdComponente,
    c.codigo AS CodigoComponente,
    ISNULL(c.NombreActivo, '') AS NombreComponente,
    'Movimiento de Componente' AS TipoMovimiento,
    ISNULL(ap_origen.codigo + ' - ' + ap_origen.NombreActivo, 'Sin padre anterior') AS ActivoPadreOrigen,
    ISNULL(ap_destino.codigo + ' - ' + ap_destino.NombreActivo, '') AS ActivoPadreDestino,
    ISNULL(s.Nombre_local, '') AS Sucursal,
    ISNULL(amb.nombre, '') AS Ambiente,
    ISNULL(hc.userMod, '') AS Autorizador,
    ISNULL(hc.userMod, '') AS Responsable,
    hc.fechaCambio as FechaMovimiento
FROM tHistorialComponente hc
INNER JOIN vActivos c ON hc.idActivoComponente = c.IdActivo
LEFT JOIN vActivos ap_origen ON hc.idPadreAnterior = ap_origen.IdActivo
LEFT JOIN vActivos ap_destino ON hc.idPadreNuevo = ap_destino.IdActivo
LEFT JOIN vUnidadesdeNegocio s ON ap_destino.IdSucursal = s.cod_UnidadNeg
LEFT JOIN tAmbiente amb ON ap_destino.IdAmbiente = amb.idAmbiente
WHERE 1=1";

            $params = [];

            if (!empty($filtros['sucursal'])) {
                $sql .= " AND s.cod_UnidadNeg = ?";
                $params[] = $filtros['sucursal'];
            }

            if (!empty($filtros['fecha'])) {
                $sql .= " AND CONVERT(date, hc.fechaCambio) = ?";
                $params[] = $filtros['fecha'];
            }

            $sql .= " ORDER BY hc.fechaCambio DESC";

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
            $stmt = $this->db->prepare('EXEC sp_AsignarComponenteActivo @pIdActivoComponente = ?, @pIdActivoPadre = ?, @pUserMod = ?');
            $stmt->bindParam(1, $data['IdActivoComponente'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdActivoPadre'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['UserMod'], \PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in asignarComponenteActivo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
