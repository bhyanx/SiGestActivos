<?php

class EdicionesMov
{
    public $db;
    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    public function listarMovimientosPendientes($filtros = [])
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
            u.codTrabajador AS idAutorizador,
            rec.NombreTrabajador AS receptor,
            rec.codTrabajador AS idReceptor,
            em.nombre AS estadoMovimiento,
            m.idEstadoMovimiento,
            m.observaciones

            FROM tMovimientos m
            INNER JOIN tTipoMovimiento tm ON m.idTipoMovimiento = tm.idTipoMovimiento
            LEFT JOIN tEstadoMovimiento em ON m.idEstadoMovimiento = em.idEstadoMovimiento

            -- Origen
            LEFT JOIN vEmpresas eo ON m.idEmpresaOrigen = eo.cod_empresa
            LEFT JOIN vUnidadesdeNegocio so ON m.idSucursalOrigen = so.cod_UnidadNeg

            -- Destino
            LEFT JOIN vEmpresas ed ON m.idEmpresaDestino= ed.cod_empresa
            LEFT JOIN vUnidadesdeNegocio sd ON m.idSucursalDestino = sd.cod_UnidadNeg

            -- Autorizador y Receptor
            LEFT JOIN vEmpleados u ON m.idAutorizador = u.codTrabajador
            LEFT JOIN vEmpleados rec ON m.idReceptor = rec.codTrabajador
            WHERE m.idEstadoMovimiento = 1"; // Estado pendiente

            $params = [];

            if (!empty($filtros['tipo'])) {
                $sql .= " AND m.idTipoMovimiento = ?";
                $params[] = $filtros['tipo'];
            }

            // Filtros de fecha más flexibles
            if (!empty($filtros['fechaInicio']) && !empty($filtros['fechaFin'])) {
                // Ambas fechas presentes - rango completo
                $fechaInicio = date('Y-m-d', strtotime($filtros['fechaInicio']));
                $fechaFin = date('Y-m-d', strtotime($filtros['fechaFin']));
                $sql .= " AND CONVERT(date, m.fechaMovimiento) BETWEEN ? AND ?";
                $params[] = $fechaInicio;
                $params[] = $fechaFin;
            } elseif (!empty($filtros['fechaInicio'])) {
                // Solo fecha inicio - desde esa fecha en adelante
                $fechaInicio = date('Y-m-d', strtotime($filtros['fechaInicio']));
                $sql .= " AND CONVERT(date, m.fechaMovimiento) >= ?";
                $params[] = $fechaInicio;
            } elseif (!empty($filtros['fechaFin'])) {
                // Solo fecha fin - hasta esa fecha
                $fechaFin = date('Y-m-d', strtotime($filtros['fechaFin']));
                $sql .= " AND CONVERT(date, m.fechaMovimiento) <= ?";
                $params[] = $fechaFin;
            }

            $sql .= " ORDER BY m.fechaMovimiento DESC;";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al listar movimientos pendientes: " . $e->getMessage());
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

    public function modificarMovimientoPendiente($data)
    {
        try {
            $sql = "EXEC sp_ModificarMovimientoPendiente
                    @idMovimiento = :idMovimiento,
                    @idSucursalDestino = :idSucursalDestino,
                    @idEmpresaDestino = :idEmpresaDestino,
                    @idReceptor = :idReceptor,
                    @idActivo = :idActivo,
                    @idAmbienteNuevo = :idAmbienteNuevo,
                    @idResponsableNuevo = :idResponsableNuevo,
                    @userMod = :userMod";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':idMovimiento', $data['idMovimiento'], PDO::PARAM_INT);
            $stmt->bindParam(':idSucursalDestino', $data['idSucursalDestino'], $data['idSucursalDestino'] !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindParam(':idEmpresaDestino', $data['idEmpresaDestino'], $data['idEmpresaDestino'] !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindParam(':idReceptor', $data['idReceptor'], $data['idReceptor'] !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':idActivo', $data['idActivo'], $data['idActivo'] !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindParam(':idAmbienteNuevo', $data['idAmbienteNuevo'], $data['idAmbienteNuevo'] !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindParam(':idResponsableNuevo', $data['idResponsableNuevo'], $data['idResponsableNuevo'] !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':userMod', $data['userMod'], PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al modificar movimiento pendiente: " . $e->getMessage());
        }
    }

    public function obtenerMovimientoParaEditar($idMovimiento)
    {
        try {
            $sql = "SELECT m.idMovimiento,
            m.codigoMovimiento,
            m.idTipoMovimiento,
            tm.nombre AS tipoMovimiento,
            m.idEmpresaOrigen,
            eo.Razon_empresa AS empresaOrigen,
            m.idSucursalOrigen,
            so.Nombre_local AS sucursalOrigen,
            m.idEmpresaDestino,
            ed.Razon_empresa AS empresaDestino,
            m.idSucursalDestino,
            sd.Nombre_local AS sucursalDestino,
            m.idAutorizador,
            u.NombreTrabajador AS autorizador,
            m.idReceptor,
            rec.NombreTrabajador AS receptor,
            m.observaciones,
            m.fechaMovimiento

            FROM tMovimientos m
            INNER JOIN tTipoMovimiento tm ON m.idTipoMovimiento = tm.idTipoMovimiento
            LEFT JOIN vEmpresas eo ON m.idEmpresaOrigen = eo.cod_empresa
            LEFT JOIN vUnidadesdeNegocio so ON m.idSucursalOrigen = so.cod_UnidadNeg
            LEFT JOIN vEmpresas ed ON m.idEmpresaDestino = ed.cod_empresa
            LEFT JOIN vUnidadesdeNegocio sd ON m.idSucursalDestino = sd.cod_UnidadNeg
            LEFT JOIN vEmpleados u ON m.idAutorizador = u.codTrabajador
            LEFT JOIN vEmpleados rec ON m.idReceptor = rec.codTrabajador
            WHERE m.idMovimiento = ? AND m.idEstadoMovimiento = 1";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener movimiento para editar: " . $e->getMessage());
        }
    }

    public function obtenerDetallesMovimientoParaEditar($idMovimiento)
    {
        try {
            $sql = "SELECT dm.idMovimiento,
            dm.idActivo,
            a.codigo AS codigoActivo,
            a.NombreActivo AS nombreActivo,
            dm.idAmbienteAnterior,
            ISNULL(amb1.nombre, 'Sin ambiente') AS ambienteOrigen,
            dm.idAmbienteNuevo,
            ISNULL(amb2.nombre, 'Sin ambiente') AS ambienteDestino,
            dm.idResponsableAnterior,
            ISNULL(u1.NombreTrabajador, 'Sin responsable') AS responsableOrigen,
            dm.idResponsableNuevo,
            ISNULL(u2.NombreTrabajador, 'Sin responsable') AS responsableDestino,
            -- Datos de ubicación actual del activo
            ua.idEmpresa AS empresaOrigen,
            ISNULL(emp.Razon_empresa, 'Sin empresa') AS nombreEmpresaOrigen,
            ua.idSucursal AS sucursalOrigen,
            ISNULL(suc.Nombre_local, 'Sin sucursal') AS nombreSucursalOrigen

            FROM tDetalleMovimiento dm
            INNER JOIN vActivos a ON dm.idActivo = a.IdActivo
            LEFT JOIN tAmbiente amb1 ON dm.idAmbienteAnterior = amb1.idAmbiente
            LEFT JOIN tAmbiente amb2 ON dm.idAmbienteNuevo = amb2.idAmbiente
            LEFT JOIN vEmpleados u1 ON dm.idResponsableAnterior = u1.codTrabajador
            LEFT JOIN vEmpleados u2 ON dm.idResponsableNuevo = u2.codTrabajador
            -- Obtener ubicación actual del activo
            LEFT JOIN tUbicacionActivo ua ON a.IdActivo = ua.idActivo AND ua.esActual = 1
            LEFT JOIN vEmpresas emp ON ua.idEmpresa = emp.cod_empresa
            LEFT JOIN vUnidadesdeNegocio suc ON ua.idSucursal = suc.cod_UnidadNeg
            WHERE dm.idMovimiento = ?
            ORDER BY a.codigo";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMovimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener detalles del movimiento para editar: " . $e->getMessage());
        }
    }

    public function gestionarDetalleMovimientoPendiente($data)
    {
        try {
            $sql = "EXEC sp_GestionarDetalleMovimientoPendiente
                    @accion = :accion,
                    @idMovimiento = :idMovimiento,
                    @idActivo = :idActivo,
                    @nuevoIdActivo = :nuevoIdActivo,
                    @idAmbienteNuevo = :idAmbienteNuevo,
                    @idResponsableNuevo = :idResponsableNuevo,
                    @userMod = :userMod";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':accion', $data['accion'], PDO::PARAM_INT);
            $stmt->bindParam(':idMovimiento', $data['idMovimiento'], PDO::PARAM_INT);
            $stmt->bindParam(':idActivo', $data['idActivo'], ($data['idActivo'] !== null && $data['idActivo'] !== 0) ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindParam(':nuevoIdActivo', $data['nuevoIdActivo'], ($data['nuevoIdActivo'] !== null && $data['nuevoIdActivo'] !== 0) ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindParam(':idAmbienteNuevo', $data['idAmbienteNuevo'], $data['idAmbienteNuevo'] !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindParam(':idResponsableNuevo', $data['idResponsableNuevo'], $data['idResponsableNuevo'] !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':userMod', $data['userMod'], PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al gestionar detalle del movimiento pendiente: " . $e->getMessage());
        }
    }

    public function buscarActivosDisponibles($filtros = [])
    {
        try {
            $sql = "SELECT 
            a.IdActivo,
            a.codigo,
            a.NombreActivo,
            a.IdEstado,
            e.nombre AS estado,
            ua.idAmbiente,
            amb.nombre AS ambiente,
            ua.idResponsable,
            emp.NombreTrabajador AS responsable,
            ua.idEmpresa,
            emp2.Razon_empresa AS empresa,
            ua.idSucursal,
            suc.Nombre_local AS sucursal

            FROM vActivos a
            INNER JOIN tUbicacionActivo ua ON a.IdActivo = ua.idActivo AND ua.esActual = 1
            LEFT JOIN tEstadoActivo e ON a.IdEstado = e.idEstadoActivo
            LEFT JOIN tAmbiente amb ON ua.idAmbiente = amb.idAmbiente
            LEFT JOIN vEmpleados emp ON ua.idResponsable = emp.codTrabajador
            LEFT JOIN vEmpresas emp2 ON ua.idEmpresa = emp2.cod_empresa
            LEFT JOIN vUnidadesdeNegocio suc ON ua.idSucursal = suc.cod_UnidadNeg

            WHERE a.IdEstado = 1"; // Solo activos operativos

            $params = [];

            // Filtros opcionales
            if (!empty($filtros['codigo'])) {
                $sql .= " AND a.codigo LIKE ?";
                $params[] = '%' . $filtros['codigo'] . '%';
            }

            if (!empty($filtros['nombre'])) {
                $sql .= " AND a.NombreActivo LIKE ?";
                $params[] = '%' . $filtros['nombre'] . '%';
            }

            if (!empty($filtros['idEmpresa'])) {
                $sql .= " AND ua.idEmpresa = ?";
                $params[] = $filtros['idEmpresa'];
            }

            if (!empty($filtros['idSucursal'])) {
                $sql .= " AND ua.idSucursal = ?";
                $params[] = $filtros['idSucursal'];
            }

            // Excluir activos que ya están en movimientos pendientes
            $sql .= " AND a.IdActivo NOT IN (
                SELECT dm.idActivo
                FROM tDetalleMovimiento dm
                INNER JOIN tMovimientos m ON dm.idMovimiento = m.idMovimiento
                WHERE m.idEstadoMovimiento = 1
            )";

            $sql .= " ORDER BY a.codigo";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al buscar activos disponibles: " . $e->getMessage());
        }
    }
}