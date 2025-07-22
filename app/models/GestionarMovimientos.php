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

    public function crearMovimientoConCodigo($data)
    {
        try {
            $sql = "DECLARE @nuevoIdMovimiento INT;
                DECLARE @nuevoCodMovimiento VARCHAR(20);

                EXEC sp_CrearMovimiento 
                    @idTipoMovimiento = :idTipoMovimiento,
                    @idAutorizador = :idAutorizador,
                    @idEmpresa = :idEmpresa,
                    @observaciones = :observaciones,
                    @userMod = :userMod,
                    @nuevoIdMovimiento = @nuevoIdMovimiento OUTPUT,
                    @nuevoCodMovimiento = @nuevoCodMovimiento OUTPUT;

                SELECT @nuevoIdMovimiento AS idMovimiento, @nuevoCodMovimiento AS codMovimiento;";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':idTipoMovimiento', $data['idTipoMovimiento'], PDO::PARAM_INT);
            $stmt->bindParam(':idAutorizador', $data['idAutorizador'], PDO::PARAM_STR);
            $stmt->bindParam(':idEmpresa', $data['idEmpresa'], PDO::PARAM_INT);
            $stmt->bindParam(':observaciones', $data['observaciones'], PDO::PARAM_STR);
            $stmt->bindParam(':userMod', $data['userMod'], PDO::PARAM_STR);

            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al crear movimiento: " . $e->getMessage());
        }
    }


    public function crearDetalleMovimiento($detalle)
    {
        try {
            $sql = "EXEC sp_RegistrarDetalleMovimiento 
                    @IdMovimiento = :idMovimiento,
                    @IdActivo = :idActivo,
                    @IdTipo_Movimiento = :idTipoMovimiento,
                    @IdAmbiente_Nuevo = :idAmbienteNuevo,
                    @IdResponsable_Nuevo = :idResponsableNuevo,
                    @IdAutorizador = :idAutorizador,
                    @UserMod = :userMod";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':idMovimiento', $detalle['idMovimiento'], PDO::PARAM_INT);
            $stmt->bindParam(':idActivo', $detalle['idActivo'], PDO::PARAM_INT);
            $stmt->bindParam(':idTipoMovimiento', $detalle['idTipoMovimiento'], PDO::PARAM_INT);
            $stmt->bindParam(':idAmbienteNuevo', $detalle['idAmbienteNuevo'], PDO::PARAM_INT);
            $stmt->bindParam(':idResponsableNuevo', $detalle['idResponsableNuevo'], PDO::PARAM_STR);
            $stmt->bindParam(':idAutorizador', $detalle['idAutorizador'], PDO::PARAM_STR);
            $stmt->bindParam(':userMod', $detalle['userMod'], PDO::PARAM_STR);

            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al registrar detalle de movimiento: " . $e->getMessage());
        }
    }


    public function listarDetalleMovimientos($filtros = [])
    {
        try {
            $sql = "SELECT * FROM vDetalleMovimiento WHERE 1=1";
            $params = [];

            // Filtro por empresa desde sesión
            if (!empty($filtros['idEmpresa'])) {
                $sql .= " AND idEmpresa = ?";
                $params[] = $filtros['idEmpresa'];
            }

            // Filtro por sucursal desde sesión (origen o destino)
            if (!empty($filtros['idSucursal'])) {
                $sql .= " AND (SucursalOrigen = (SELECT Nombre_local FROM vUnidadesdeNegocio WHERE cod_UnidadNeg = ?) OR SucursalDestino = (SELECT Nombre_local FROM vUnidadesdeNegocio WHERE cod_UnidadNeg = ?))";
                $params[] = $filtros['idSucursal'];
                $params[] = $filtros['idSucursal'];
            }

            if (!empty($filtros['tipo'])) {
                $sql .= " AND TipoMovimiento = ?";
                $params[] = $filtros['tipo'];
            }
            if (!empty($filtros['sucursal_origen'])) {
                $sql .= " AND SucursalOrigen = ?";
                $params[] = $filtros['sucursal_origen'];
            }
            if (!empty($filtros['sucursal_destino'])) {
                $sql .= " AND SucursalDestino = ?";
                $params[] = $filtros['sucursal_destino'];
            }
            if (!empty($filtros['fecha'])) {
                $sql .= " AND CONVERT(date, FechaMovimiento) = ?";
                $params[] = $filtros['fecha'];
            }
            if (!empty($filtros['ambiente'])) {
                $sql .= " AND (AmbienteOrigen = ? OR AmbienteDestino = ?)";
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

    public function listarActivosParaMovimiento($idEmpresa, $idSucursal)
    {
        try {
            $sql = "
            SELECT a.IdActivo, a.Serie, a.codigo, a.NombreActivo, s.Nombre_local AS Sucursal
            ,amb.nombre AS Ambiente
            FROM vActivos a
            INNER JOIN vUnidadesdeNegocio s ON a.IdSucursal = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON a.IdAmbiente = amb.idAmbiente
            WHERE a.IdEmpresa = ?
            AND a.IdSucursal = ?
            AND a.idEstado <> 3
            ORDER BY a.NombreActivo";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(2, $idSucursal, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in listarActivosParaMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function obtenerAmbientesPorSucursal($idEmpresa, $idSucursal)
    {
        try {
            $sql = "SELECT DISTINCT a.idAmbiente, a.nombre 
                    FROM tAmbiente a 
                    WHERE a.idEmpresa = :idEmpresa 
                    AND a.idSucursal = :idSucursal 
                    ORDER BY a.nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(':idSucursal', $idSucursal, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener ambientes: " . $e->getMessage());
        }
    }

    public function anularMovimiento($idMovimiento)
    {
        try {
            // Primero verificamos si el movimiento existe y no está anulado
            $sql = "SELECT estado FROM tMovimientos WHERE idMovimiento = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();
            $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$movimiento) {
                throw new Exception("El movimiento no existe");
            }

            if ($movimiento['estado'] === 'A') {
                throw new Exception("El movimiento ya está anulado");
            }

            // Iniciamos la transacción
            $this->db->beginTransaction();

            // Actualizamos el estado del movimiento a anulado
            $sql = "UPDATE tMovimientos SET estado = 'A', fechaAnulacion = GETDATE() WHERE idMovimiento = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();

            // Actualizamos el estado de los detalles del movimiento
            $sql = "UPDATE tDetalleMovimiento SET estado = 'A' WHERE idMovimiento = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();

            // Confirmamos la transacción
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            // Si hay error, revertimos la transacción
            $this->db->rollBack();
            error_log("Error in anularMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function obtenerHistorialMovimiento($idMovimiento)
    {
        try {
            $sql = "
            SELECT 
                m.idMovimiento,
                m.FechaMovimiento,
                m.estado,
                m.fechaAnulacion,
                tm.nombre as tipoMovimiento,
                a.NombreActivoVisible,
                a.CodigoActivo,
                so.Nombre_local as sucursalOrigen,
                sd.Nombre_local as sucursalDestino,
                ao.nombre as ambienteOrigen,
                ad.nombre as ambienteDestino,
                CONCAT(tor.NombreTrabajador, ' ', tor.ApellidoPaterno) as responsableOrigen,
                CONCAT(tdr.NombreTrabajador, ' ', tdr.ApellidoPaterno) as responsableDestino,
                CONCAT(ta.NombreTrabajador, ' ', ta.ApellidoPaterno) as autorizador
            FROM tMovimientos m
            INNER JOIN tTipoMovimiento tm ON m.idTipoMovimiento = tm.idTipoMovimiento
            INNER JOIN tDetalleMovimiento dm ON m.idMovimiento = dm.idMovimiento
            INNER JOIN vActivos a ON dm.idActivo = a.IdActivo
            INNER JOIN vUnidadesdeNegocio so ON m.idSucursalOrigen = so.cod_UnidadNeg
            INNER JOIN vUnidadesdeNegocio sd ON m.idSucursalDestino = sd.cod_UnidadNeg
            INNER JOIN tAmbiente ao ON dm.idAmbiente_Origen = ao.idAmbiente
            INNER JOIN tAmbiente ad ON dm.idAmbiente_Nueva = ad.idAmbiente
            INNER JOIN tTrabajador tor ON dm.idResponsable_Origen = tor.codTrabajador
            INNER JOIN tTrabajador tdr ON dm.idResponsable_Nueva = tdr.codTrabajador
            INNER JOIN tTrabajador ta ON m.idAutorizador = ta.codTrabajador
            WHERE m.idMovimiento = ?
            ORDER BY m.FechaMovimiento DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in obtenerHistorialMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    // public function crearMovimientoConCodigo($data)
    // {
    //     try {
    //         $sql = "DECLARE @nuevoIdMovimiento INT;
    //                 DECLARE @nuevoCodMovimiento VARCHAR(20);

    //                 EXEC sp_CrearMovimiento 
    //                     @idTipoMovimiento = :idTipoMovimiento,
    //                     @idAutorizador = :idAutorizador,
    //                     @idSucursalOrigen = :idSucursalOrigen,
    //                     @idSucursalDestino = :idSucursalDestino,
    //                     @idEmpresaOrigen = :idEmpresaOrigen,
    //                     @idEmpresaDestino = :idEmpresaDestino,
    //                     @observaciones = :observaciones,
    //                     @nuevoIdMovimiento = @nuevoIdMovimiento OUTPUT,
    //                     @nuevoCodMovimiento = @nuevoCodMovimiento OUTPUT;

    //                 SELECT @nuevoIdMovimiento as idMovimiento, @nuevoCodMovimiento as codMovimiento;";

    //         $stmt = $this->db->prepare($sql);

    //         // Asegurar que los valores sean del tipo correcto
    //         $idTipoMovimiento = (int)$data['idTipoMovimiento'];
    //         $idAutorizador = (int)$data['idAutorizador'];
    //         $idSucursalOrigen = (int)$data['idSucursalOrigen'];
    //         $idSucursalDestino = (int)$data['idSucursalDestino'];
    //         $idEmpresaOrigen = (int)$data['idEmpresaOrigen'];
    //         $idEmpresaDestino = (int)$data['idEmpresaDestino'];
    //         $observaciones = (string)$data['observaciones'];

    //         $stmt->bindParam(':idTipoMovimiento', $idTipoMovimiento, PDO::PARAM_INT);
    //         $stmt->bindParam(':idAutorizador', $idAutorizador, PDO::PARAM_INT);
    //         $stmt->bindParam(':idSucursalOrigen', $idSucursalOrigen, PDO::PARAM_INT);
    //         $stmt->bindParam(':idSucursalDestino', $idSucursalDestino, PDO::PARAM_INT);
    //         $stmt->bindParam(':idEmpresaOrigen', $idEmpresaOrigen, PDO::PARAM_INT);
    //         $stmt->bindParam(':idEmpresaDestino', $idEmpresaDestino, PDO::PARAM_INT);
    //         $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);

    //         if (!$stmt->execute()) {
    //             throw new PDOException("Error al ejecutar la consulta: " . implode(" ", $stmt->errorInfo()));
    //         }

    //         $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    //         return [
    //             'idMovimiento' => $resultado['idMovimiento'],
    //             'codMovimiento' => $resultado['codMovimiento']
    //         ];
    //     } catch (PDOException $e) {
    //         throw new Exception("Error al crear el movimiento: " . $e->getMessage());
    //     }
    // }

    public function registrarMovimientoActivos($idsActivos, $idAmbienteDestino, $idResponsableDestino, $motivo, $userMod, $idEmpresaDestino, $idSucursalDestino)
    {
        try {
            // Construir el XML de activos
            $xml = "<Activos>";
            foreach ($idsActivos as $id) {
                $xml .= "<Id>$id</Id>";
            }
            $xml .= "</Activos>";

            // Consulta SQL
            $sql = "DECLARE @nuevoCodMovimiento VARCHAR(20);
                EXEC sp_RegistrarMovimientoActivo
                    @pXmlActivos = :xmlActivos,
                    @pIdAmbienteDestino = :idAmbienteDestino,
                    @pIdResponsableDestino = :idResponsableDestino,
                    @pMotivo = :motivo,
                    @pUserMod = :userMod,
                    @pIdEmpresaDestino = :idEmpresaDestino,
                    @pIdSucursalDestino = :idSucursalDestino,
                    @nuevoCodMovimiento = @nuevoCodMovimiento OUTPUT;
                SELECT @nuevoCodMovimiento AS codMovimiento;";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':xmlActivos', $xml, PDO::PARAM_STR);
            $stmt->bindParam(':idAmbienteDestino', $idAmbienteDestino, PDO::PARAM_INT);
            $stmt->bindParam(':idResponsableDestino', $idResponsableDestino, PDO::PARAM_STR);
            $stmt->bindParam(':motivo', $motivo, PDO::PARAM_STR);
            $stmt->bindParam(':userMod', $userMod, PDO::PARAM_STR);
            $stmt->bindParam(':idEmpresaDestino', $idEmpresaDestino, PDO::PARAM_INT);
            $stmt->bindParam(':idSucursalDestino', $idSucursalDestino, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['codMovimiento'];
        } catch (PDOException $e) {
            throw new Exception("Error al registrar movimiento de activos: " . $e->getMessage());
        }
    }



    public function listarMovimientos($filtros = [])
    {
        try {
            $sql = "SELECT DISTINCT
                    m.idMovimiento,
                    m.codigoMovimiento,
                    tm.nombre AS tipoMovimiento,
                    m.fechaMovimiento,
                    m.userMod AS usuarioRegistro,
                    MAX(e.Razon_empresa) AS empresaDestino,
                    MAX(s.Nombre_local) AS sucursalDestino,
                    MAX(u.NombreTrabajador) AS autorizador,
                    m.fechaMovimiento
                FROM tMovimientos m
                INNER JOIN tTipoMovimiento tm ON m.idTipoMovimiento = tm.idTipoMovimiento
                INNER JOIN tDetalleMovimiento dm ON dm.idMovimiento = m.idMovimiento
                LEFT JOIN vEmpleados au ON dm.idAutorizador = au.codTrabajador
                LEFT JOIN vEmpleados u ON au.codTrabajador = u.codTrabajador
                LEFT JOIN tUbicacionActivo ua ON ua.idActivo = dm.idActivo AND ua.esActual = 1
                LEFT JOIN vEmpresas e ON ua.idEmpresa = e.cod_empresa
                LEFT JOIN vUnidadesdeNegocio s ON ua.idSucursal = s.cod_UnidadNeg
                WHERE 1=1";

            $params = [];

            if (!empty($filtros['idEmpresa'])) {
                $sql .= " AND ua.idEmpresa = ?";
                $params[] = $filtros['idEmpresa'];
            }

            if (!empty($filtros['idSucursal'])) {
                $sql .= " AND ua.idSucursal = ?";
                $params[] = $filtros['idSucursal'];
            }

            if (!empty($filtros['tipo'])) {
                $sql .= " AND m.idTipoMovimiento = ?";
                $params[] = $filtros['tipo'];
            }

            if (!empty($filtros['fecha'])) {
                $sql .= " AND CONVERT(date, m.fechaMovimiento) = ?";
                $params[] = $filtros['fecha'];
            }

            $sql .= " GROUP BY m.idMovimiento, m.codigoMovimiento, tm.nombre, m.fechaMovimiento, m.userMod
                  ORDER BY m.fechaMovimiento DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al listar movimientos: " . $e->getMessage());
        }
    }


    public function obtenerDetallesMovimiento($idMovimiento)
    {
        try {
            $sql = "SELECT a.CodigoActivo as Codigo, a.NombreActivoVisible as nombreActivo, 
                    amb1.nombre as ambienteOrigen, 
                    amb2.nombre as ambienteDestino, 
                    u1.NombreTrabajador as responsableOrigen, 
                    u2.NombreTrabajador as responsableDestino
                FROM tDetalleMovimiento dm
                INNER JOIN vActivos a ON dm.idActivo = a.IdActivo
                LEFT JOIN tAmbiente amb1 ON dm.IdAmbiente_Anterior = amb1.idAmbiente
                LEFT JOIN tAmbiente amb2 ON dm.IdAmbiente_Nuevo = amb2.idAmbiente
                LEFT JOIN vEmpleados u1 ON dm.IdResponsable_Anterior = u1.codTrabajador
                LEFT JOIN vEmpleados u2 ON dm.IdResponsable_Nuevo = u2.codTrabajador
                WHERE dm.idMovimiento = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener detalles del movimiento: " . $e->getMessage());
        }
    }

    public function obtenerCabeceraMovimiento($idMovimiento)
    {
        try {
            $sql = "SELECT m.idMovimiento, m.CodMovimiento, tm.nombre as tipoMovimiento, s1.Nombre_local as sucursalOrigen,
	   eo.Razon_empresa as empresaOrigen, eo.Ruc_empresa as RucOrigen,
	   e.Razon_empresa as empresaDestino, e.Ruc_empresa as RucDestino,
	   s1.Direccion_local as DireccionOrigen, s2.Nombre_local as sucursalDestino, s2.Direccion_local as DireccionDestino,
	   dm.idResponsable_Anterior as responsableOrigen, dm.IdResponsable_Nuevo as responsableDestino,
	   dm.idAutorizador as dniAutorizador,
       aut.NombreTrabajador as nombreAutorizador,
	   m.observaciones, m.fechaMovimiento
FROM tMovimientos m
INNER JOIN tTipoMovimiento tm ON m.idTipoMovimiento = tm.idTipoMovimiento
INNER JOIN vUnidadesdeNegocio s1 ON m.idSucursalOrigen = s1.cod_UnidadNeg
INNER JOIN vUnidadesdeNegocio s2 ON m.idSucursalDestino = s2.cod_UnidadNeg
LEFT JOIN vEmpresas eo ON m.idEmpresaOrigen = eo.cod_empresa
INNER JOIN vEmpresas e ON m.idEmpresaDestino = e.cod_empresa
INNER JOIN vEmpleados aut ON m.idAutorizador = aut.codTrabajador
LEFT JOIN tDetalleMovimiento dm ON m.idMovimiento = dm.idMovimiento
WHERE m.idMovimiento = ?
ORDER BY m.fechaMovimiento DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener cabecera del movimiento: " . $e->getMessage());
        }
    }

    public function obtenerEmpleado($idEmpleado)
    {
        try {
            $sql = "SELECT codTrabajador, NombreTrabajador 
                    FROM vEmpleados 
                    WHERE codTrabajador = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idEmpleado, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener datos del empleado: " . $e->getMessage());
        }
    }
    public function obtenerEmpresa($idEmpresa)
    {
        try {
            $sql = "SELECT * FROM vEmpresas WHERE cod_empresa = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idEmpresa, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener datos de la empresa: " . $e->getMessage());
        }
    }

    public function obtenerTiposMovimiento()
    {
        try {
            $sql = "SELECT idTipoMovimiento, nombre FROM tTipoMovimiento ORDER BY nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener tipos de movimiento: " . $e->getMessage());
        }
    }
}
