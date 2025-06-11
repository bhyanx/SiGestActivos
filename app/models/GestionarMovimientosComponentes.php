<?php

class GestionarMovimientosComponentes
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    public function listarActivosPadres()
    {
        try {
            $sql = "
            SELECT 
                a.IdActivo,
                a.CodigoActivo,
                ISNULL(a.NombreArticulo, '') as NombreArticulo,
                ISNULL(a.MarcaArticulo, '') as MarcaArticulo,
                ISNULL(s.Nombre_local, '') AS Sucursal,
                ISNULL(amb.nombre, '') AS Ambiente,
                ISNULL(a.NumeroSerie, '') as NumeroSerie
            FROM vActivos a
            INNER JOIN vUnidadesdeNegocio s ON a.IdSucursal = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON a.IdAmbiente = amb.idAmbiente
            WHERE a.IdEmpresa = 1 
            AND a.idEstado = 1
            AND a.EsPadre = 1
            ORDER BY a.NombreArticulo";
            
            $stmt = $this->db->prepare($sql);
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
                ISNULL(a.NombreArticulo, '') as NombreArticulo,
                ISNULL(a.MarcaArticulo, '') as MarcaArticulo,
                ISNULL(a.NumeroSerie, '') as NumeroSerie,
                ISNULL(s.Nombre_local, '') AS Sucursal,
                ISNULL(amb.nombre, '') AS Ambiente
            FROM vActivos a
            INNER JOIN vUnidadesdeNegocio s ON a.IdSucursal = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON a.IdAmbiente = amb.idAmbiente
            WHERE a.idActivoPadre = ?
            AND a.idEstado = 1
            ORDER BY a.NombreArticulo";
            
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

            // Actualizar el activo padre del componente
            $sql = "UPDATE vActivos SET IdActivoPadre = ? WHERE IdActivo = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $data['IdActivoPadreNuevo'], PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdActivoComponente'], PDO::PARAM_INT);
            $stmt->execute();

            // Registrar el detalle del movimiento
            $sql = "EXEC sp_RegistrarDetalleMovimiento @IdMovimiento = ?, @IdActivo = ?, @IdTipo_Movimiento = ?, @IdSucursal_Nueva = ?, @IdAmbiente_Nueva = ?, @IdResponsable_Nueva = ?, @IdActivoPadre_Nuevo = ?, @IdAutorizador = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $data['IdMovimiento'], PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdActivoComponente'], PDO::PARAM_INT);
            $stmt->bindParam(3, $data['IdTipo_Movimiento'], PDO::PARAM_INT);
            $stmt->bindParam(4, $data['IdSucursal_Nueva'], PDO::PARAM_INT);
            $stmt->bindParam(5, $data['IdAmbiente_Nueva'], PDO::PARAM_INT);
            $stmt->bindParam(6, $data['IdResponsable_Nueva'], PDO::PARAM_INT);
            $stmt->bindParam(7, $data['IdActivoPadreNuevo'], PDO::PARAM_INT);
            $stmt->bindParam(8, $data['IdAutorizador'], PDO::PARAM_INT);
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
                c.IdActivo as IdComponente,
                c.CodigoActivo as CodigoComponente,
                ISNULL(c.NombreArticulo, '') as NombreComponente,
                ISNULL(ap_origen.CodigoActivo + ' - ' + ap_origen.NombreArticulo, '') as ActivoPadreOrigen,
                ISNULL(ap_destino.CodigoActivo + ' - ' + ap_destino.NombreArticulo, '') as ActivoPadreDestino,
                ISNULL(s.Nombre_local, '') as Sucursal,
                ISNULL(amb.nombre, '') as Ambiente,
                ISNULL(CONCAT(t.NombreTrabajador, ' ', t.ApellidoPaterno), '') as Autorizador,
                ISNULL(CONCAT(r.NombreTrabajador, ' ', r.ApellidoPaterno), '') as Responsable,
                m.FechaMovimiento
            FROM tDetalleMovimiento dm
            INNER JOIN tMovimientos m ON dm.IdMovimiento = m.IdMovimiento
            INNER JOIN vActivos c ON dm.IdActivo = c.IdActivo
            INNER JOIN vActivos ap_origen ON dm.IdActivoPadre_Origen = ap_origen.IdActivo
            INNER JOIN vActivos ap_destino ON dm.IdActivoPadre_Nuevo = ap_destino.IdActivo
            INNER JOIN vUnidadesdeNegocio s ON m.IdSucursalDestino = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON dm.IdAmbiente_Nueva = amb.idAmbiente
            INNER JOIN tTrabajador t ON m.IdAutorizador = t.codTrabajador
            INNER JOIN tTrabajador r ON dm.IdResponsable_Nueva = r.codTrabajador
            WHERE m.IdTipoMovimiento = 8
            AND m.estado = 'A'";

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
}
