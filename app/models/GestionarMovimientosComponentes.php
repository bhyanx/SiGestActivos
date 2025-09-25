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
                ISNULL(s.Nombre_local, '') AS Sucursal,
                ISNULL(amb.nombre, '') AS Ambiente,
                ISNULL(a.Serie, '') as NumeroSerie
            FROM vActivos a
            INNER JOIN vUnidadesdeNegocio s ON a.IdSucursal = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON a.IdAmbiente = amb.idAmbiente
            WHERE a.IdEmpresa = 1 
            AND a.idEstado NOT IN(2,3,4)
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
    public function listarActivosPadresDestino($sucursal = null)
    {
        try {
            $sql = "
            SELECT 
                a.IdActivo,
                a.codigo as CodigoActivo,
                ISNULL(a.NombreActivo, '') as NombreArticulo,
                ISNULL(s.Nombre_local, '') AS Sucursal,
                ISNULL(amb.nombre, '') AS Ambiente,
                ISNULL(a.Serie, '') as NumeroSerie
            FROM vActivos a
            INNER JOIN vUnidadesdeNegocio s ON a.IdSucursal = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON a.IdAmbiente = amb.idAmbiente
            WHERE a.IdEmpresa = 1 
            AND a.idEstado NOT IN(2,3,4)
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
            SELECT  a.IdActivo, a.codigo as CodigoActivo,
        ISNULL(a.NombreActivo, '') as NombreArticulo,
        ISNULL( a.Marca, '') as Marca,
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
            // El procedimiento almacenado maneja todo: validaciones, actualización, historial y transacciones
            $sql = "EXEC sp_MoverComponenteActivo 
                @pIdActivoComponente = ?, 
                @pNuevoPadre = ?, 
                @pUserMod = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $data['IdActivoComponente'], PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdActivoPadreNuevo'], PDO::PARAM_INT);
            $stmt->bindParam(3, $data['UserMod'], PDO::PARAM_STR);
            $stmt->execute();

            return true;
        } catch (\PDOException $e) {
            error_log("Error in moverComponenteEntreActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function consultarMovimientosEntreActivos($filtros = [])
    {
        try {
            $sql = "SELECT 
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

            if (!empty($filtros['fechaInicio']) && !empty($filtros['fechaFin'])) {
                $fechaInicio = date('Y-m-d', strtotime($filtros['fechaInicio']));
                $fechaFin = date('Y-m-d', strtotime($filtros['fechaFin']));
                $sql .= " AND CONVERT(date, hc.fechaCambio) BETWEEN ? AND ?";
                $params[] = $fechaInicio;
                $params[] = $fechaFin;
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
            // El procedimiento almacenado maneja todo: validaciones, actualización, historial y transacciones
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

    public function listarComponentesSinPadre($sucursal = null)
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
            WHERE a.idActivoPadre IS NULL
            AND a.idEstado = 1
            AND a.esPadre = 0";

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
            error_log("Error in listarComponentesSinPadre: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function consultarActivosModal($data)
    {
        try {
            // Convertir cadenas vacías a null o enteros para parámetros numéricos
            $pCodigo = empty($data['pCodigo']) ? null : $data['pCodigo'];
            $pIdEmpresa = empty($data['pIdEmpresa']) ? null : $data['pIdEmpresa'];
            $pIdSucursal = empty($data['pIdSucursal']) ? null : (int)$data['pIdSucursal'];
            $pIdAmbiente = empty($data['pIdAmbiente']) ? null : (int)$data['pIdAmbiente'];
            $pIdCategoria = empty($data['pIdCategoria']) ? null : (int)$data['pIdCategoria'];
            $pIdEstado = empty($data['pIdEstado']) ? null : (int)$data['pIdEstado'];
            $pAccion = 1;

            $stmt = $this->db->prepare('EXEC sp_ConsultarActivos @pCodigo = ?, @pIdEmpresa = ?, @pIdSucursal = ?, @pIdAmbiente = ?, @pIdCategoria = ?, @pIdEstado = ?, @pAccion = ?');
            $stmt->bindParam(1, $pCodigo, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(2, $pIdEmpresa, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(3, $pIdSucursal, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(4, $pIdAmbiente, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(5, $pIdCategoria, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(6, $pIdEstado, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(7, $pAccion, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in consultarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
