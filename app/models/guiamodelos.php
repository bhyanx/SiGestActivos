<?php
// Clases PHP para interactuar con la base de datos usando procedimientos almacenados
// Asumen una clase Conectar con método ConexionBdPracticante() que retorna PDO
// Guardar en directorio 'models/' o similar

// models/GestionarUsuarios.php

// //! GESTIONAR USUARIOS
// class GestionarUsuarios {
//     private $db;

//     public function __construct() {
//         $this->db = (new Conectar())->ConexionBdPracticante();
//     }

//     // Actualizar rol de un usuario usando sp_GestionUsuarios
//     public function actualizarRol($data) {
//         try {
//             $stmt = $this->db->prepare('EXEC sp_GestionUsuarios @pAccion = ?, @CodUsuario = ?, @IdRol = ?, @UserMod = ?');
//             $stmt->bindParam(1, $data['pAccion'], \PDO::PARAM_STR); // 'ACTUALIZAR'
//             $stmt->bindParam(2, $data['CodUsuario'], \PDO::PARAM_STR);
//             $stmt->bindParam(3, $data['IdRol'], \PDO::PARAM_INT);
//             $stmt->bindParam(4, $data['UserMod'], \PDO::PARAM_STR);
//             $stmt->execute();
//             return true;
//         } catch (\PDOException $e) {
//             error_log("Error in actualizarRol: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
//             throw $e;
//         }
//     }

//     // Gestionar permisos usando sp_GestionPermisos
//     public function gestionarPermiso($data) {
//         try {
//             $stmt = $this->db->prepare('EXEC sp_GestionPermisos @pAccion = ?, @CodPermiso = ?, @CodMenu = ?, @IdRol = ?, @Permiso = ?, @UserMod = ?');
//             $stmt->bindParam(1, $data['pAccion'], \PDO::PARAM_STR); // 'INSERTAR', 'ACTUALIZAR', 'ELIMINAR', 'CONSULTAR'
//             $stmt->bindParam(2, $data['CodPermiso'], \PDO::PARAM_INT);
//             $stmt->bindParam(3, $data['CodMenu'], \PDO::PARAM_INT);
//             $stmt->bindParam(4, $data['IdRol'], \PDO::PARAM_INT);
//             $stmt->bindParam(5, $data['Permiso'], \PDO::PARAM_BOOL);
//             $stmt->bindParam(6, $data['UserMod'], \PDO::PARAM_STR);
//             $stmt->execute();
//             if ($data['pAccion'] === 'CONSULTAR') {
//                 return $stmt->fetchAll(\PDO::FETCH_ASSOC);
//             }
//             return true;
//         } catch (\PDOException $e) {
//             error_log("Error in gestionarPermiso: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
//             throw $e;
//         }
//     }

//     // Listar menús por usuario usando vMenusPorUsuario
//     public function listarMenusPorUsuario($codUsuario) {
//         try {
//             $stmt = $this->db->prepare('SELECT * FROM vMenusPorUsuario WHERE CodUsuario = ?');
//             $stmt->execute([$codUsuario]);
//             return $stmt->fetchAll(\PDO::FETCH_ASSOC);
//         } catch (\PDOException $e) {
//             error_log("Error in listarMenusPorUsuario: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
//             throw $e;
//         }
//     }
// }

//! FIN GESTIONAR USUARIOS

//! GESTIONAR ACTIVOS

// models/GestionarActivos.php
// class GestionarActivos {
//     private $db;

//     public function __construct() {
//         $this->db = (new Conectar())->ConexionBdPracticante();
//     }

//     // Registrar activo usando sp_RegistrarActivos
//     public function registrarActivo($data) {
//         try {
//             $stmt = $this->db->prepare('EXEC sp_RegistrarActivos @CodigoActivo = ?, @IdEstado = ?, @IdSucursal = ?, @IdAmbiente = ?, @IdCategoria = ?, @IdProveedor = ?, @NombreArticulo = ?, @ValorAdquisicion = ?, @VidaUtil = ?, @FechaAdquisicion = ?, @UserMod = ?');
//             $stmt->bindParam(1, $data['CodigoActivo'], \PDO::PARAM_STR);
//             $stmt->bindParam(2, $data['IdEstado'], \PDO::PARAM_INT);
//             $stmt->bindParam(3, $data['IdSucursal'], \PDO::PARAM_INT);
//             $stmt->bindParam(4, $data['IdAmbiente'], \PDO::PARAM_INT);
//             $stmt->bindParam(5, $data['IdCategoria'], \PDO::PARAM_INT);
//             $stmt->bindParam(6, $data['IdProveedor'], \PDO::PARAM_INT);
//             $stmt->bindParam(7, $data['NombreArticulo'], \PDO::PARAM_STR);
//             $stmt->bindParam(8, $data['ValorAdquisicion'], \PDO::PARAM_STR); // DECIMAL as string
//             $stmt->bindParam(9, $data['VidaUtil'], \PDO::PARAM_INT);
//             $stmt->bindParam(10, $data['FechaAdquisicion'], \PDO::PARAM_STR);
//             $stmt->bindParam(11, $data['UserMod'], \PDO::PARAM_STR);
//             $stmt->execute();
//             return true;
//         } catch (\PDOException $e) {
//             error_log("Error in registrarActivo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
//             throw $e;
//         }
//     }

//     // Consultar activos usando sp_ConsultarActivos
//     public function consultarActivos($data) {
//         try {
//             $stmt = $this->db->prepare('EXEC sp_ConsultarActivos @pCodigo = ?, @pIdSucursal = ?, @pIdCategoria = ?, @pIdEstado = ?');
//             $stmt->bindParam(1, $data['pCodigo'], \PDO::PARAM_STR);
//             $stmt->bindParam(2, $data['pIdSucursal'], \PDO::PARAM_INT);
//             $stmt->bindParam(3, $data['pIdCategoria'], \PDO::PARAM_INT);
//             $stmt->bindParam(4, $data['pIdEstado'], \PDO::PARAM_INT);
//             $stmt->execute();
//             return $stmt->fetchAll(\PDO::FETCH_ASSOC);
//         } catch (\PDOException $e) {
//             error_log("Error in consultarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
//             throw $e;
//         }
//     }

//     // Actualizar activo (usando sp_GuardarActivo, asumiendo acción de actualización)
//     public function actualizarActivo($data) {
//         try {
//             $stmt = $this->db->prepare('EXEC sp_GuardarActivo @pAccion = 2, @idActivo = ?, @CodigoActivo = ?, @IdEstado = ?, @IdSucursal = ?, @IdAmbiente = ?, @IdCategoria = ?, @IdProveedor = ?, @NombreArticulo = ?, @ValorAdquisicion = ?, @VidaUtil = ?, @FechaAdquisicion = ?, @UserMod = ?');
//             $stmt->bindParam(1, $data['IdActivo'], \PDO::PARAM_INT);
//             $stmt->bindParam(2, $data['CodigoActivo'], \PDO::PARAM_STR);
//             $stmt->bindParam(3, $data['IdEstado'], \PDO::PARAM_INT);
//             $stmt->bindParam(4, $data['IdSucursal'], \PDO::PARAM_INT);
//             $stmt->bindParam(5, $data['IdAmbiente'], \PDO::PARAM_INT);
//             $stmt->bindParam(6, $data['IdCategoria'], \PDO::PARAM_INT);
//             $stmt->bindParam(7, $data['IdProveedor'], \PDO::PARAM_INT);
//             $stmt->bindParam(8, $data['NombreArticulo'], \PDO::PARAM_STR);
//             $stmt->bindParam(9, $data['ValorAdquisicion'], \PDO::PARAM_STR);
//             $stmt->bindParam(10, $data['VidaUtil'], \PDO::PARAM_INT);
//             $stmt->bindParam(11, $data['FechaAdquisicion'], \PDO::PARAM_STR);
//             $stmt->bindParam(12, $data['UserMod'], \PDO::PARAM_STR);
//             $stmt->execute();
//             return true;
//         } catch (\PDOException $e) {
//             error_log("Error in actualizarActivo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
//             throw $e;
//         }
//     }
// }

//! FIN GESTIONAR ACTIVOS

//! GESTIONAR MOVIMIENTOS

// // models/GestionarMovimientos.php
// class GestionarMovimientos {
//     private $db;

//     public function __construct() {
//         $this->db = (new Conectar())->ConexionBdPracticante();
//     }

//     // Registrar movimiento (individual o masivo) usando sp_RegistrarMovimiento
//     public function registrarMovimiento($data) {
//         try {
//             $stmt = $this->db->prepare('EXEC sp_RegistrarMovimiento @pIdFicha = ?, @pIdActivo = ?, @pIdTipoMovimiento = ?, @pIdAmbienteOrigen = ?, @pIdAmbienteDestino = ?, @pIdActivoPadreOrigen = ?, @pIdActivoPadreDestino = ?, @pIdResponsableAnterior = ?, @pIdResponsableNuevo = ?, @pIdAutorizador = ?, @pIdEstadoMovimiento = ?, @pObservaciones = ?, @pUserMod = ?, @pIdRazonMovimiento = ?, @pAccion = ?');
//             $stmt->bindParam(1, $data['IdFicha'], \PDO::PARAM_INT);
//             $stmt->bindParam(2, $data['IdActivo'], \PDO::PARAM_INT);
//             $stmt->bindParam(3, $data['IdTipoMovimiento'], \PDO::PARAM_INT);
//             $stmt->bindParam(4, $data['IdAmbienteOrigen'], \PDO::PARAM_INT);
//             $stmt->bindParam(5, $data['IdAmbienteDestino'], \PDO::PARAM_INT);
//             $stmt->bindParam(6, $data['IdActivoPadreOrigen'], \PDO::PARAM_INT);
//             $stmt->bindParam(7, $data['IdActivoPadreDestino'], \PDO::PARAM_INT);
//             $stmt->bindParam(8, $data['IdResponsableAnterior'], \PDO::PARAM_STR);
//             $stmt->bindParam(9, $data['IdResponsableNuevo'], \PDO::PARAM_STR);
//             $stmt->bindParam(10, $data['IdAutorizador'], \PDO::PARAM_STR);
//             $stmt->bindParam(11, $data['IdEstadoMovimiento'], \PDO::PARAM_INT);
//             $stmt->bindParam(12, $data['Observaciones'], \PDO::PARAM_STR);
//             $stmt->bindParam(13, $data['UserMod'], \PDO::PARAM_STR);
//             $stmt->bindParam(14, $data['IdRazonMovimiento'], \PDO::PARAM_INT);
//             $stmt->bindParam(15, $data['pAccion'], \PDO::PARAM_STR); // 'INDIVIDUAL' o 'MASIVO'
//             $stmt->execute();
//             return true;
//         } catch (\PDOException $e) {
//             error_log("Error in registrarMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
//             throw $e;
//         }
//     }

//     // Listar movimientos por activo usando vHistorialMovimientosPorActivo
//     public function listarMovimientosId($idActivo) {
//         try {
//             $stmt = $this->db->prepare('SELECT * FROM vHistorialMovimientosPorActivo WHERE idActivo = ? ORDER BY fechaMovimiento DESC');
//             $stmt->execute([$idActivo]);
//             return $stmt->fetchAll(\PDO::FETCH_ASSOC);
//         } catch (\PDOException $e) {
//             error_log("Error in listarMovimientosId: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
//             throw $e;
//         }
//     }
// }

// // //! FIN GESTIONAR MOVIMIENTOS

// //! GESTIONAR FICHAS

// // models/GestionarFichas.php
// class GestionarFichas {
//     private $db;

//     public function __construct() {
//         $this->db = (new Conectar())->ConexionBdPracticante();
//     }

//     // Generar guía de remisión usando sp_GenerarGuiaRemision
//     public function generarGuiaRemision($data) {
//         try {
//             $stmt = $this->db->prepare('EXEC sp_GenerarGuiaRemision @pIdFicha = ?, @pSerie = ?, @pNumero = ?, @pFechaEmision = ?, @pPuntoPartida = ?, @pPuntoLlegada = ?, @pRucTransportista = ?, @pRazonSocialTransportista = ?, @pPlaca = ?, @pUserMod = ?');
//             $stmt->bindParam(1, $data['IdFicha'], \PDO::PARAM_INT);
//             $stmt->bindParam(2, $data['Serie'], \PDO::PARAM_STR);
//             $stmt->bindParam(3, $data['Numero'], \PDO::PARAM_STR);
//             $stmt->bindParam(4, $data['FechaEmision'], \PDO::PARAM_STR);
//             $stmt->bindParam(5, $data['PuntoPartida'], \PDO::PARAM_STR);
//             $stmt->bindParam(6, $data['PuntoLlegada'], \PDO::PARAM_STR);
//             $stmt->bindParam(7, $data['RucTransportista'], \PDO::PARAM_STR);
//             $stmt->bindParam(8, $data['RazonSocialTransportista'], \PDO::PARAM_STR);
//             $stmt->bindParam(9, $data['Placa'], \PDO::PARAM_STR);
//             $stmt->bindParam(10, $data['UserMod'], \PDO::PARAM_STR);
//             $stmt->execute();
//             return true;
//         } catch (\PDOException $e) {
//             error_log("Error in generarGuiaRemision: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
//             throw $e;
//         }
//     }

//     // Listar fichas por sucursal destino
//     public function listarFichasPorSucursal($idSucursalDestino) {
//         try {
//             $stmt = $this->db->prepare('SELECT * FROM tFichaMovimiento WHERE idSucursalDestino = ? ORDER BY fechaFicha DESC');
//             $stmt->execute([$idSucursalDestino]);
//             return $stmt->fetchAll(\PDO::FETCH_ASSOC);
//         } catch (\PDOException $e) {
//             error_log("Error in listarFichasPorSucursal: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
//             throw $e;
//         }
//     }
// }

// //! FIN GESTIONAR FICHAS

//! GESTIONAR REPORTES

// models/GestionarReportes.php
class GestionarReportes {
    private $db;

    public function __construct() {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    // Generar reporte de movimientos usando sp_ReporteMovimientos
    public function reporteMovimientos($data) {
        try {
            $stmt = $this->db->prepare('EXEC sp_ReporteMovimientos @pIdActivo = ?, @pFechaInicio = ?, @pFechaFin = ?, @pIdResponsable = ?');
            $stmt->bindParam(1, $data['IdActivo'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['FechaInicio'], \PDO::PARAM_STR);
            $stmt->bindParam(3, $data['FechaFin'], \PDO::PARAM_STR);
            $stmt->bindParam(4, $data['IdResponsable'], \PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in reporteMovimientos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}

//! FIN GESTIONAR REPORTES

//! GESTIONAR DEPRECIACIÓN

// models/GestionarDepreciacion.php
class GestionarDepreciacion {
    private $db;

    public function __construct() {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    // Registrar depreciación usando sp_RegistrarDepreciacion
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

    // Listar depreciaciones por activo
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

//! FIN GESTIONAR DEPRECIACIÓN
?>

