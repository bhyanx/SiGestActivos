<?php

class Mantenimientos
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    // * REGISTRAR MANTENIMIENTO CON PROCEDIMIENTOS ALMACENADOS

    public function crearMantenimientoConCodigo($data)
    {
        try {
            $sql = "DECLARE @nuevoIdMantenimiento INT;
                DECLARE @nuevoCodigoMantenimiento VARCHAR(20);

                EXEC sp_CrearMantenimiento 
                    @fechaProgramada = :fechaProgramada,
                    @idTipoMantenimiento = :idTipoMantenimiento,
                    @descripcion = :descripcion,
                    @observaciones = :observaciones,
                    @costoEstimado = :costoEstimado,
                    @idProveedor = :idProveedor,
                    @idResponsable = :idResponsable,
                    @estadoMantenimiento = :estadoMantenimiento,
                    @userMod = :userMod,
                    @idEmpresa = :idEmpresa,
                    @idSucursal = :idSucursal,
                    @nuevoIdMantenimiento = @nuevoIdMantenimiento OUTPUT,
                    @nuevoCodigoMantenimiento = @nuevoCodigoMantenimiento OUTPUT;

                SELECT @nuevoIdMantenimiento AS idMantenimiento, @nuevoCodigoMantenimiento AS codigoMantenimiento;";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(':fechaProgramada', $data['fechaProgramada'], $data['fechaProgramada'] !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':idTipoMantenimiento', $data['idTipoMantenimiento'], PDO::PARAM_INT);
            $stmt->bindValue(':descripcion', $data['descripcion'], $data['descripcion'] !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':observaciones', $data['observaciones'], $data['observaciones'] !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':costoEstimado', $data['costoEstimado'], $data['costoEstimado'] !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':idProveedor', $data['idProveedor'], $data['idProveedor'] !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(':idResponsable', $data['idResponsable'], $data['idResponsable'] !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':estadoMantenimiento', $data['estadoMantenimiento'], PDO::PARAM_INT);
            $stmt->bindParam(':userMod', $data['userMod'], PDO::PARAM_STR);
            $stmt->bindValue(':idEmpresa', $data['idEmpresa'], $data['idEmpresa'] !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(':idSucursal', $data['idSucursal'], $data['idSucursal'] !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);

            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al crear mantenimiento: " . $e->getMessage());
        }
    }

    public function crearDetalleMantenimiento($data)
    {
        try {
            $sql = "EXEC sp_RegistrarDetalleMantenimiento 
                    @idMantenimiento = :idMantenimiento,
                    @idActivo = :idActivo,
                    @observaciones = :observaciones,
                    @userMod = :userMod,
                    @incluirHijos = :incluirHijos";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':idMantenimiento', $data['idMantenimiento'], PDO::PARAM_INT);
            $stmt->bindParam(':idActivo', $data['idActivo'], PDO::PARAM_INT);
            //$stmt->bindParam(':tipoMantenimiento', $data['tipoMantenimiento'], PDO::PARAM_INT);
            $stmt->bindValue(':observaciones', $data['observaciones'], $data['observaciones'] !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindParam(':userMod', $data['userMod'], PDO::PARAM_STR);
            $incluirHijos = isset($data['incluirHijos']) && (int)$data['incluirHijos'] === 1 ? 1 : 0;
            $stmt->bindValue(':incluirHijos', $incluirHijos, PDO::PARAM_INT);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al registrar detalle de mantenimiento: " . $e->getMessage());
        }
    }

    public function activoTieneHijos($idActivo)
    {
        try {
            $sql = "SELECT TOP 1 1 FROM tActivos WHERE idActivoPadre = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->execute();
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Error al verificar hijos del activo: " . $e->getMessage());
        }
    }

    public function listarHijosActivos($idActivoPadre)
    {
        try {
            $sql = "
            ;WITH CTE AS (
                SELECT a.idActivo
                FROM tActivos a
                WHERE a.idActivoPadre = :idPadre
                UNION ALL
                SELECT h.idActivo
                FROM tActivos h
                INNER JOIN CTE p ON h.idActivoPadre = p.idActivo
            )
            SELECT va.IdActivo, va.codigo, va.NombreActivo,
                   s.Nombre_local AS Sucursal,
                   amb.nombre AS Ambiente,
                   va.idEstado,
                   ea.nombre AS EstadoActual
            FROM CTE c
            INNER JOIN vActivos va ON va.IdActivo = c.idActivo
            INNER JOIN vUnidadesdeNegocio s ON va.IdSucursal = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON va.IdAmbiente = amb.idAmbiente
            LEFT JOIN tEstadoActivo ea ON va.idEstado = ea.idEstadoActivo
            ORDER BY va.NombreActivo";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idPadre', $idActivoPadre, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al listar hijos del activo: " . $e->getMessage());
        }
    }

    public function obtenerEstadoActualActivo($idActivo)
    {
        try {
            $sql = "SELECT ea.nombre AS EstadoActual, a.idEstado, a.idActivoPadre , a.esPadre
                    FROM vActivos a
                    LEFT JOIN tEstadoActivo ea ON a.idEstado = ea.idEstadoActivo
                    WHERE a.IdActivo = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener estado del activo: " . $e->getMessage());
        }
    }



    public function obtenerTiposMantenimiento()
    {
        try {
            $sql = "SELECT idTipoMantenimiento, nombre 
                    FROM tTipoMantenimiento 
                    ORDER BY nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener tipos de mantenimiento: " . $e->getMessage());
        }
    }

    public function obtenerEstadosMantenimiento()
    {
        try {
            $sql = "SELECT idEstadoMantenimiento, nombre 
                    FROM tEstadoMantenimiento
                    ORDER BY nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener estados de mantenimiento: " . $e->getMessage());
        }
    }

    public function listarActivosParaMantenimiento($idEmpresa, $idSucursal)
    {
        try {
            $sql = "
            SELECT a.IdActivo, a.Serie, a.codigo, a.NombreActivo, s.Nombre_local AS Sucursal
            ,amb.nombre AS Ambiente, a.idEstado, ea.nombre AS EstadoActual
            , a.esPadre, a.idActivoPadre
            FROM vActivos a
            INNER JOIN vUnidadesdeNegocio s ON a.IdSucursal = s.cod_UnidadNeg
            INNER JOIN tAmbiente amb ON a.IdAmbiente = amb.idAmbiente
            LEFT JOIN tEstadoActivo ea ON a.idEstado = ea.idEstadoActivo
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
            error_log("Error in listarActivosParaMantenimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function consultarMantenimientos($data)
    {
        try {
            $pIdEmpresa = empty($data['pIdEmpresa']) ? null : $data['pIdEmpresa'];
            $pIdSucursal = empty($data['pIdSucursal']) ? null : (int)$data['pIdSucursal'];
            $pFechaInicio = empty($data['pFechaInicio']) ? null : $data['pFechaInicio'];
            $pFechaFin = empty($data['pFechaFin']) ? null : $data['pFechaFin'];

            $stmt = $this->db->prepare('EXEC sp_ConsultarMantenimientosCabecera @pIdEmpresa = ?,
                                                                        @pIdSucursal = ?,
                                                                        @pFechaInicio = ?,
                                                                        @pFechaFin = ?');
            $stmt->bindParam(1, $pIdEmpresa, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(2, $pIdSucursal, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(3, $pFechaInicio, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(4, $pFechaFin, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in consultarMantenimientos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function obtenerDetallesMantenimiento($idMantenimiento)
    {
        try {
            $sql = "EXEC sp_ConsultarMantenimientos @pIdMantenimiento = :idMantenimiento";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idMantenimiento', $idMantenimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener detalles del mantenimiento: " . $e->getMessage());
        }
    }

    public function obtenerCabeceraMantenimiento($idMantenimiento)
    {
        try {
            $sql = "SELECT m.idMantenimiento, m.codigoMantenimiento, m.fechaRegistro,
	            m.descripcion, m.observaciones, m.costoEstimado, m.costoReal,
	            tm.nombre as tipoMantenimiento,
	            em.nombre as estadoMantenimiento,
	            emp.NombreTrabajador as responsable,
	            m.userMod as usuarioRegistro,
	            --m.fechaMod as fechaUltimaModificacion,
	            m.idProveedor, prov.RazonSocial as Proveedor
                    FROM tMantenimientos m
                    LEFT JOIN tTipoMantenimiento tm ON m.estadoMantenimiento = tm.idTipoMantenimiento
                    LEFT JOIN tEstadoMantenimiento em ON m.estadoMantenimiento = em.idEstadoMantenimiento
                    LEFT JOIN vEmpleados emp ON m.idResponsable = emp.codTrabajador
                    LEFT JOIN vEntidadExternaGeneralProveedor prov ON m.idProveedor = prov.Documento
                    WHERE m.idMantenimiento = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMantenimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener cabecera del mantenimiento: " . $e->getMessage());
        }
    }

    public function obtenerEmpresaInfo($idEmpresa)
    {
        try {
            $sql = "SELECT 
                e.Razon_empresa as nombre,
                e.Ruc_empresa as ruc,
                e.Direccion_empresa as direccion
                FROM vEmpresas e 
                WHERE e.cod_empresa = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idEmpresa, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener información de la empresa: " . $e->getMessage());
        }
    }

    public function obtenerSucursalInfo($idSucursal)
    {
        try {
            $sql = "SELECT 
                s.Nombre_local as nombre,
                s.Direccion_local as direccion
                FROM vUnidadesdeNegocio s 
                WHERE s.cod_UnidadNeg = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idSucursal, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener información de la sucursal: " . $e->getMessage());
        }
    }

    public function obtenerMantenimientoParaAprobar($idMantenimiento)
    {
       try {
            $sql = "SELECT 
                m.idMantenimiento,
                m.codigoMantenimiento,
                m.descripcion,
                m.fechaRegistro,
                m.costoEstimado,
                m.estadoMantenimiento,
                em.nombre as estadoActual,
                COUNT(dm.idActivo) as totalActivos
                FROM tMantenimientos m
                LEFT JOIN tEstadoMantenimiento em ON m.estadoMantenimiento = em.idEstadoMantenimiento
                LEFT JOIN tDetalleMantenimiento dm ON m.idMantenimiento = dm.idMantenimiento
                WHERE m.idMantenimiento = ?
                GROUP BY m.idMantenimiento, m.codigoMantenimiento, m.descripcion, 
                         m.fechaRegistro, m.costoEstimado, m.estadoMantenimiento, em.nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMantenimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener datos del mantenimiento: " . $e->getMessage());
        }
    }

    public function finalizarMantenimiento($data)
    {
        try {
            // Construir XML para el procedimiento almacenado
            $xml = '<Mantenimientos>';
            $xml .= '<Mantenimiento>';
            $xml .= '<idMantenimiento>' . $data['idMantenimiento'] . '</idMantenimiento>';
            $xml .= '<fechaMod>' . $data['fechaMod'] . '</fechaMod>';
            $xml .= '<costoReal>' . ($data['costoReal'] ?? 0) . '</costoReal>';
            $xml .= '<observaciones>' . htmlspecialchars($data['observaciones'] ?? '') . '</observaciones>';
            $xml .= '<idEstadoMantenimiento>' . $data['idEstadoMantenimiento'] . '</idEstadoMantenimiento>';
            $xml .= '</Mantenimiento>';
            $xml .= '</Mantenimientos>';

            $sql = "EXEC sp_FinalizarMantenimiento @XmlMantenimientos = :xml, @pUserMod = :userMod";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':xml', $xml, PDO::PARAM_STR);
            $stmt->bindParam(':userMod', $data['userMod'], PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al finalizar mantenimiento: " . $e->getMessage());
        }
    }

    public function obtenerMantenimientoParaFinalizar($idMantenimiento)
    {
        try {
            $sql = "SELECT 
                m.idMantenimiento,
                m.codigoMantenimiento,
                m.descripcion,
                m.fechaRegistro,
                m.costoEstimado,
                m.estadoMantenimiento,
                em.nombre as estadoActual,
                COUNT(dm.idActivo) as totalActivos
                FROM tMantenimientos m
                LEFT JOIN tEstadoMantenimiento em ON m.estadoMantenimiento = em.idEstadoMantenimiento
                LEFT JOIN tDetalleMantenimiento dm ON m.idMantenimiento = dm.idMantenimiento
                WHERE m.idMantenimiento = ?
                GROUP BY m.idMantenimiento, m.codigoMantenimiento, m.descripcion, 
                         m.fechaRegistro, m.costoEstimado, m.estadoMantenimiento, em.nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMantenimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener datos del mantenimiento: " . $e->getMessage());
        }
    }

    public function cancelarMantenimiento($data)
    {
        try {
            // Construir XML para el procedimiento almacenado
            $xml = '<Mantenimientos>';
            $xml .= '<Mantenimiento>';
            $xml .= '<idMantenimiento>' . $data['idMantenimiento'] . '</idMantenimiento>';
            $xml .= '<motivo>' . htmlspecialchars($data['motivo'] ?? '') . '</motivo>';
            $xml .= '<idEstadoMantenimiento>' . $data['idEstadoMantenimiento'] . '</idEstadoMantenimiento>';
            $xml .= '</Mantenimiento>';
            $xml .= '</Mantenimientos>';

            $sql = "EXEC sp_CancelarMantenimiento @XmlMantenimientos = :xml, @pUserMod = :userMod";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':xml', $xml, PDO::PARAM_STR);
            $stmt->bindParam(':userMod', $data['userMod'], PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al cancelar mantenimiento: " . $e->getMessage());
        }
    }

    public function obtenerMantenimientoParaCancelar($idMantenimiento)
    {
        try {
            $sql = "SELECT 
                m.idMantenimiento,
                m.codigoMantenimiento,
                m.descripcion,
                m.fechaRegistro,
                m.costoEstimado,
                m.estadoMantenimiento,
                em.nombre as estadoActual,
                COUNT(dm.idActivo) as totalActivos
                FROM tMantenimientos m
                LEFT JOIN tEstadoMantenimiento em ON m.estadoMantenimiento = em.idEstadoMantenimiento
                LEFT JOIN tDetalleMantenimiento dm ON m.idMantenimiento = dm.idMantenimiento
                WHERE m.idMantenimiento = ?
                AND m.estadoMantenimiento NOT IN (3, 4) -- No finalizado ni cancelado
                GROUP BY m.idMantenimiento, m.codigoMantenimiento, m.descripcion, 
                         m.fechaRegistro, m.costoEstimado, m.estadoMantenimiento, em.nombre";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMantenimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener datos del mantenimiento: " . $e->getMessage());
        }
    }

    public function obtenerHistorialMantenimientos($idMantenimiento)
    {
        try {
            $sql = "SELECT h.idHistorialEstadoMantenimiento, h.idMantenimiento, ea.nombre as EstadoAnterior,
            en.nombre AS estadoNuevo, h.fechaCambio, h.userMod, e.NombreTrabajador as nombreUsuario, h.observacion
            FROM tHistorialEstadoMantenimiento h
            LEFT JOIN tEstadoMantenimiento ea ON h.idEstadoAnterior = ea.idEstadoMantenimiento
            INNER JOIN tEstadoMantenimiento en ON h.idEstadoNuevo = en.idEstadoMantenimiento
            LEFT JOIN vEmpleados e ON h.userMod = e.codTrabajador
            WHERE h.idMantenimiento = ?
            ORDER BY h.fechaCambio ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $idMantenimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener datos del mantenimiento: " . $e->getMessage());
        }
    }

    public function aprobarMantenimiento($data)
    {
        try {
            $sql = "EXEC sp_AprobarMantenimiento @idMantenimiento = :idMantenimiento, @observacionAprobar = :observacionAprobar, @userMod = :userMod";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idMantenimiento', $data['idMantenimiento'], PDO::PARAM_INT);
            $stmt->bindValue(':observacionAprobar', $data['observacionAprobar'] ?? null, PDO::PARAM_STR);
            $stmt->bindParam(':userMod', $data['userMod'], PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al aprobar mantenimiento: " . $e->getMessage());
        }
    }
}
