<?php

class GestionarMovimientos
{

    public $db;
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
                    @idReceptor = :idReceptor,
                    @idEmpresaOrigen = :idEmpresaOrigen,
                    @idSucursalOrigen = :idSucursalOrigen,
                    @idEmpresaDestino = :idEmpresaDestino,
                    @idSucursalDestino = :idSucursalDestino,
                    @observaciones = :observaciones,
                    @userMod = :userMod,
                    @nuevoIdMovimiento = @nuevoIdMovimiento OUTPUT,
                    @nuevoCodMovimiento = @nuevoCodMovimiento OUTPUT;

                SELECT @nuevoIdMovimiento AS idMovimiento, @nuevoCodMovimiento AS codMovimiento;";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':idTipoMovimiento', $data['idTipoMovimiento'], PDO::PARAM_INT);
            $stmt->bindParam(':idReceptor', $data['idReceptor'], PDO::PARAM_STR);
            $stmt->bindParam(':idAutorizador', $data['idAutorizador'], PDO::PARAM_STR);
            $stmt->bindParam(':idEmpresaOrigen', $data['idEmpresaOrigen'], PDO::PARAM_INT);
            $stmt->bindParam(':idSucursalOrigen', $data['idSucursalOrigen'], PDO::PARAM_INT);
            $stmt->bindParam(':idEmpresaDestino', $data['idEmpresaDestino'], PDO::PARAM_INT);
            $stmt->bindParam(':idSucursalDestino', $data['idSucursalDestino'], PDO::PARAM_INT);
            $stmt->bindParam(':observaciones', $data['observaciones'], PDO::PARAM_STR);
            $stmt->bindParam(':userMod', $data['userMod'], PDO::PARAM_STR);

            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al crear movimiento: " . $e->getMessage());
        }
    }

    public function crearDetalleMovimiento($data)
    {
        try {
            // Obtener datos del movimiento y del activo para completar la información
            $sqlMovimiento = "SELECT idEmpresaDestino, idSucursalDestino FROM tMovimientos WHERE idMovimiento = ?";
            $stmtMov = $this->db->prepare($sqlMovimiento);
            $stmtMov->bindParam(1, $data['idMovimiento'], PDO::PARAM_INT);
            $stmtMov->execute();
            $movimiento = $stmtMov->fetch(PDO::FETCH_ASSOC);

            $sqlActivo = "SELECT ua.idAmbiente as idAmbienteAnterior, ua.idResponsable as idResponsableAnterior,
                                 ua.idEmpresa as idEmpresaOrigen, ua.idSucursal as idSucursalOrigen
                          FROM tUbicacionActivo ua 
                          WHERE ua.idActivo = ? AND ua.esActual = 1";
            $stmtActivo = $this->db->prepare($sqlActivo);
            $stmtActivo->bindParam(1, $data['idActivo'], PDO::PARAM_INT);
            $stmtActivo->execute();
            $activo = $stmtActivo->fetch(PDO::FETCH_ASSOC);

            if (!$activo) {
                throw new Exception("No se encontró la ubicación actual del activo");
            }

            // Insertar detalle del movimiento (solo registro, sin ejecución física)
            // Esto es necesario porque el SP sp_RegistrarMovimientoActivov2 ejecuta inmediatamente
            // y en el nuevo flujo necesitamos separar el registro de la ejecución
            $sql = "INSERT INTO tDetalleMovimiento 
                    (idMovimiento, idActivo, idTipoMovimiento, idAmbienteAnterior, idAmbienteNuevo, 
                     idResponsableAnterior, idResponsableNuevo, fecha, userMod, 
                     idEmpresaDestino, idSucursalDestino, idEmpresaOrigen, idSucursalOrigen)
                    VALUES 
                    (:idMovimiento, :idActivo, :idTipoMovimiento, :idAmbienteAnterior, :idAmbienteNuevo,
                     :idResponsableAnterior, :idResponsableNuevo, GETDATE(), :userMod,
                     :idEmpresaDestino, :idSucursalDestino, :idEmpresaOrigen, :idSucursalOrigen)";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':idMovimiento', $data['idMovimiento'], PDO::PARAM_INT);
            $stmt->bindParam(':idActivo', $data['idActivo'], PDO::PARAM_INT);
            $stmt->bindParam(':idTipoMovimiento', $data['idTipoMovimiento'], PDO::PARAM_INT);
            $stmt->bindValue(':idAmbienteAnterior', $activo['idAmbienteAnterior'], PDO::PARAM_INT);
            $stmt->bindValue(':idAmbienteNuevo', $data['idAmbienteNuevo'], $data['idAmbienteNuevo'] !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(':idResponsableAnterior', $activo['idResponsableAnterior'], PDO::PARAM_STR);
            $stmt->bindValue(':idResponsableNuevo', $data['idResponsableNuevo'], $data['idResponsableNuevo'] !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':userMod', $data['userMod'], PDO::PARAM_STR);
            $stmt->bindValue(':idEmpresaDestino', $movimiento['idEmpresaDestino'], PDO::PARAM_INT);
            $stmt->bindValue(':idSucursalDestino', $movimiento['idSucursalDestino'], PDO::PARAM_INT);
            $stmt->bindValue(':idEmpresaOrigen', $activo['idEmpresaOrigen'], PDO::PARAM_INT);
            $stmt->bindValue(':idSucursalOrigen', $activo['idSucursalOrigen'], PDO::PARAM_INT);

            $stmt->execute();
            return true;
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
            AND a.idEstado NOT IN(2,3,4)
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
            // Verificar si el movimiento existe y su estado actual
            $sql = "SELECT idEstadoMovimiento FROM tMovimientos WHERE idMovimiento = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();
            $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$movimiento) {
                throw new Exception("El movimiento no existe");
            }

            if ($movimiento['idEstadoMovimiento'] == 4) {
                throw new Exception("No se puede anular un movimiento que ya ha sido aceptado y ejecutado");
            }

            if ($movimiento['idEstadoMovimiento'] == 3) {
                throw new Exception("El movimiento ya está rechazado");
            }

            // Para anular, usamos el método rechazar
            return $this->rechazarMovimiento($idMovimiento, $_SESSION['usuario'] ?? 'SYSTEM');
        } catch (Exception $e) {
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





    public function listarMovimientosEnviados($filtros = [])
    {
        try {
            $sql = "SELECT m.idMovimiento,
            m.codigoMovimiento,
            tm.nombre AS tipoMovimiento,
            m.fechaMovimiento,
            m.userMod AS usuarioRegistro,
            eo.Razon_empresa AS empresaOrigen,
            so.Nombre_local AS sucursalOrigen,
            ed.Razon_empresa AS empresaDestino,
            sd.Nombre_local AS sucursalDestino,
            u.NombreTrabajador AS autorizador,
            em.nombre AS estadoMovimiento,
            m.idEstadoMovimiento

            FROM tMovimientos m
            INNER JOIN tTipoMovimiento tm ON m.idTipoMovimiento = tm.idTipoMovimiento
            LEFT JOIN tEstadoMovimiento em ON m.idEstadoMovimiento = em.idEstadoMovimiento

            -- Origen
            LEFT JOIN vEmpresas eo ON m.idEmpresaOrigen = eo.cod_empresa
            LEFT JOIN vUnidadesdeNegocio so ON m.idSucursalOrigen = so.cod_UnidadNeg

            -- Destino
            LEFT JOIN vEmpresas ed ON m.idEmpresaDestino= ed.cod_empresa
            LEFT JOIN vUnidadesdeNegocio sd ON m.idSucursalDestino = sd.cod_UnidadNeg

            -- Autorizador
            LEFT JOIN vEmpleados u ON m.idAutorizador = u.codTrabajador
            WHERE 1=1";

            $params = [];

            // Empresa: puede ser origen o destino
            if (!empty($filtros['idEmpresa'])) {
                $sql .= " AND (m.idEmpresaOrigen = ? )";
                $params[] = $filtros['idEmpresa'];
            }

            // Sucursal: puede ser origen o destino
            if (!empty($filtros['idSucursalOrigen'])) {
                $sql .= " AND (m.idSucursalOrigen = ? )";
                $params[] = $filtros['idSucursalOrigen'];
            }

            if (!empty($filtros['tipo'])) {
                $sql .= " AND m.idTipoMovimiento = ?";
                $params[] = $filtros['tipo'];
            }

            if (!empty($filtros['fecha'])) {
                $fecha = date('Y-m-d', strtotime($filtros['fecha']));
                $sql .= " AND CONVERT(date, m.fechaMovimiento) = ?";
                $params[] = $fecha;
            }

            $sql .= " ORDER BY m.fechaMovimiento DESC;";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al listar movimientos: " . $e->getMessage());
        }
    }

    public function listarMovimientosRecibidos($filtros = [])
    {
        try {
            $sql = "SELECT m.idMovimiento,
            m.codigoMovimiento,
            tm.nombre AS tipoMovimiento,
            m.fechaMovimiento,
            m.userMod AS usuarioRegistro,
            eo.Razon_empresa AS empresaOrigen,
            so.Nombre_local AS sucursalOrigen,
            ed.Razon_empresa AS empresaDestino,
            sd.Nombre_local AS sucursalDestino,
            u.NombreTrabajador AS autorizador,
            em.nombre AS estadoMovimiento,
            m.idEstadoMovimiento

            FROM tMovimientos m
            INNER JOIN tTipoMovimiento tm ON m.idTipoMovimiento = tm.idTipoMovimiento
            LEFT JOIN tEstadoMovimiento em ON m.idEstadoMovimiento = em.idEstadoMovimiento

            -- Origen
            LEFT JOIN vEmpresas eo ON m.idEmpresaOrigen = eo.cod_empresa
            LEFT JOIN vUnidadesdeNegocio so ON m.idSucursalOrigen = so.cod_UnidadNeg

            -- Destino
            LEFT JOIN vEmpresas ed ON m.idEmpresaDestino= ed.cod_empresa
            LEFT JOIN vUnidadesdeNegocio sd ON m.idSucursalDestino = sd.cod_UnidadNeg

            -- Autorizador
            LEFT JOIN vEmpleados u ON m.idAutorizador = u.codTrabajador
            WHERE 1=1";

            $params = [];

            // Empresa: puede ser origen o destino
            if (!empty($filtros['idEmpresa'])) {
                $sql .= " AND (m.idEmpresaDestino = ? )";
                $params[] = $filtros['idEmpresa'];
            }

            // Sucursal: puede ser origen o destino
            if (!empty($filtros['idSucursalDestino'])) {
                $sql .= " AND (m.idSucursalDestino = ? )";
                $params[] = $filtros['idSucursalDestino'];
            }

            if (!empty($filtros['tipo'])) {
                $sql .= " AND m.idTipoMovimiento = ?";
                $params[] = $filtros['tipo'];
            }

            if (!empty($filtros['fecha'])) {
                $fecha = date('Y-m-d', strtotime($filtros['fecha']));
                $sql .= " AND CONVERT(date, m.fechaMovimiento) = ?";
                $params[] = $fecha;
            }

            $sql .= " ORDER BY m.fechaMovimiento DESC;";

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
            $sql = "SELECT 
            a.codigo AS codigoActivo,
            a.NombreActivo AS nombreActivo,

            ISNULL(amb1.nombre, 'Sin origen') AS ambienteOrigen,
            ISNULL(amb2.nombre, 'Sin destino') AS ambienteDestino,

            ISNULL(u1.NombreTrabajador, 'Sin responsable origen') AS responsableOrigen,
            ISNULL(u2.NombreTrabajador, 'Sin responsable destino') AS responsableDestino,

            dm.idTipoMovimiento,
            tm.nombre AS tipoMovimiento,
            FORMAT(dm.fecha, 'yyyy-MM-dd HH:mm') AS fechaMovimiento,
            dm.userMod AS usuarioRegistro

            FROM tDetalleMovimiento dm
            INNER JOIN vActivos a ON dm.idActivo = a.IdActivo
            LEFT JOIN tAmbiente amb1 ON dm.idAmbienteAnterior = amb1.idAmbiente
            LEFT JOIN tAmbiente amb2 ON dm.idAmbienteNuevo = amb2.idAmbiente
            LEFT JOIN vEmpleados u1 ON dm.idResponsableAnterior = u1.codTrabajador
            LEFT JOIN vEmpleados u2 ON dm.idResponsableNuevo = u2.codTrabajador
            LEFT JOIN tTipoMovimiento tm ON dm.idTipoMovimiento = tm.idTipoMovimiento
                    
            WHERE dm.idMovimiento = ?
            ORDER BY a.codigo;";

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
            $sql = "SELECT m.idMovimiento, m.codigoMovimiento, tm.nombre as tipoMovimiento, s1.Nombre_local as sucursalOrigen,
	        eo.Razon_empresa as empresaOrigen, eo.Ruc_empresa as RucOrigen,
	        e.Razon_empresa as empresaDestino, e.Ruc_empresa as RucDestino,
	        s1.Direccion_local as DireccionOrigen, s2.Nombre_local as sucursalDestino, s2.Direccion_local as DireccionDestino,
	        dm.idResponsableAnterior as responsableOrigen, dm.idResponsableNuevo as responsableDestino,
	        m.idAutorizador as dniAutorizador,
            aut.NombreTrabajador as nombreAutorizador,
	        m.idReceptor AS dniReceptor,
	        rec.NombreTrabajador As nombreReceptor,
	        m.observaciones, m.fechaMovimiento
            FROM tMovimientos m
            INNER JOIN tTipoMovimiento tm ON m.idTipoMovimiento = tm.idTipoMovimiento
            INNER JOIN vUnidadesdeNegocio s1 ON m.idSucursalOrigen = s1.cod_UnidadNeg
            INNER JOIN vUnidadesdeNegocio s2 ON m.idSucursalDestino = s2.cod_UnidadNeg
            LEFT JOIN vEmpresas eo ON m.idEmpresaOrigen = eo.cod_empresa
            INNER JOIN vEmpresas e ON m.idEmpresaDestino = e.cod_empresa
            INNER JOIN vEmpleados aut ON m.idAutorizador = aut.codTrabajador
            LEFT JOIN vEmpleados rec ON m.idReceptor = rec.codTrabajador
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

    public function verificarActivoConComponentes($idActivo)
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as totalComponentes,
                        STRING_AGG(CONCAT(codigo, ' - ', NombreActivo), ', ') as listaComponentes
                    FROM vActivos 
                    WHERE idActivoPadre = ? AND idEstado <> 3";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'tieneComponentes' => $resultado['totalComponentes'] > 0,
                'totalComponentes' => $resultado['totalComponentes'],
                'listaComponentes' => $resultado['listaComponentes'] ?? ''
            ];
        } catch (PDOException $e) {
            throw new Exception("Error al verificar componentes del activo: " . $e->getMessage());
        }
    }

    public function aprobarMovimiento($idMovimiento, $userMod)
    {
        try {
            $sql = "EXEC sp_AprobarMovimiento 
                    @idMovimiento = :idMovimiento,
                    @userMod = :userMod";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idMovimiento', $idMovimiento, PDO::PARAM_INT);
            $stmt->bindParam(':userMod', $userMod, PDO::PARAM_STR);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al aprobar movimiento: " . $e->getMessage());
        }
    }

    public function rechazarMovimiento($idMovimiento, $userMod)
    {
        try {
            $sql = "EXEC sp_RechazarMovimiento 
                    @idMovimiento = :idMovimiento,
                    @userMod = :userMod";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idMovimiento', $idMovimiento, PDO::PARAM_INT);
            $stmt->bindParam(':userMod', $userMod, PDO::PARAM_STR);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al rechazar movimiento: " . $e->getMessage());
        }
    }

    public function aceptarMovimiento($idMovimiento, $userMod)
    {
        try {
            $sql = "EXEC sp_AceptarMovimiento 
                    @idMovimiento = :idMovimiento,
                    @userMod = :userMod";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idMovimiento', $idMovimiento, PDO::PARAM_INT);
            $stmt->bindParam(':userMod', $userMod, PDO::PARAM_STR);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al aceptar movimiento: " . $e->getMessage());
        }
    }

    public function obtenerHistorialEstadoMovimiento($idMovimiento)
    {
        try {
            $sql = "SELECT 
                        h.idHistorialEstadoMovimiento,
                        h.idMovimiento,
                        ea.nombre AS estadoAnterior,
                        en.nombre AS estadoNuevo,
                        h.fechaCambio,
                        h.userMod,
                        e.NombreTrabajador AS nombreUsuario
                    FROM tHistorialEstadoMovimiento h
                    LEFT JOIN tEstadoMovimiento ea ON h.idEstadoAnterior = ea.idEstadoMovimiento
                    INNER JOIN tEstadoMovimiento en ON h.idEstadoNuevo = en.idEstadoMovimiento
                    LEFT JOIN vEmpleados e ON h.userMod = e.codTrabajador
                    WHERE h.idMovimiento = ?
                    ORDER BY h.fechaCambio DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener historial de estados: " . $e->getMessage());
        }
    }

    public function obtenerEstadosMovimiento()
    {
        try {
            $sql = "SELECT idEstadoMovimiento, nombre, descripcion FROM tEstadoMovimiento ORDER BY idEstadoMovimiento";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener estados de movimiento: " . $e->getMessage());
        }
    }
}
