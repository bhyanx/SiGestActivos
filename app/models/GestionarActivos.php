<?php
require_once(__DIR__ . '/../config/configuracion.php');

class GestionarActivos
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    public function consultarActivos($data)
    {
        try {
            //$empresa = $_SESSION['cod_empresa'] ??  null; 
            //$sucursal = $_SESSION['cod_UnidadNeg'] ?? null;
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

    public function consultarActivosRelacionados($data)
    {
        try {
            error_log("Data recibida en modelo: " . print_r($data, true), 3, __DIR__ . '/../../logs/debug.log');
            //$empresa = $_SESSION['cod_empresa'] ??  null; 
            //$sucursal = $_SESSION['cod_UnidadNeg'] ?? null;
            // Convertir cadenas vacías a null o enteros para parámetros numéricos
            $pIdArticulo = empty($data['pIdArticulo']) ? null : $data['pIdArticulo'];
            $pCodigo = empty($data['pCodigo']) ? null : $data['pCodigo'];
            $pIdEmpresa = empty($data['pIdEmpresa']) ? null : $data['pIdEmpresa'];
            $pIdSucursal = empty($data['pIdSucursal']) ? null : (int)$data['pIdSucursal'];
            $pIdCategoria = empty($data['pIdCategoria']) ? null : (int)$data['pIdCategoria'];
            $pIdEstado = empty($data['pIdEstado']) ? null : (int)$data['pIdEstado'];
            $pAccion = 3;

            error_log("Valor de pIdCategoria después de procesamiento: " . $pIdCategoria, 3, __DIR__ . '/../../logs/debug.log');

            $stmt = $this->db->prepare('EXEC sp_ConsultarActivos @pIdArticulo = ?, @pCodigo = ?, @pIdEmpresa = ?, @pIdSucursal = ?, @pIdCategoria = ?, @pIdEstado = ?, @pAccion = ?');
            $stmt->bindParam(1, $pIdArticulo, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(2, $pCodigo, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(3, $pIdEmpresa, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(4, $pIdSucursal, \PDO::PARAM_INT | \PDO::PARAM_NULL);
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

    public function obtenerActivoPorId($idActivo)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM vActivos WHERE IdActivo = ?");
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in obtenerActivoPorId: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function obtenerInfoActivo($idActivo)
    {
        $stmt = $this->db->prepare("SELECT Codigo AS CodigoActivo, Sucursal AS SucursalActual, Ambiente AS AmbienteActual FROM tActivos WHERE IdActivo = ?");
        $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerAmbientesPorEmpresaSucursal($idEmpresa, $idSucursal)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    a.idAmbiente,
                    a.nombre,
                    a.idEmpresa,
                    a.idSucursal
                FROM tAmbiente a
                WHERE a.idEmpresa = ? 
                AND a.idSucursal = ?
                AND a.estado = 1
                ORDER BY a.nombre
            ");
            $stmt->bindParam(1, $idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(2, $idSucursal, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in obtenerAmbientesPorEmpresaSucursal: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function registrarActivos($data)
    {
        try {
            // Formatear fechas al formato SQL Server
            $fechaFinGarantia = !empty($data['FechaFinGarantia']) ? date('Y-m-d', strtotime($data['FechaFinGarantia'])) : null;
            $fechaAdquisicion = !empty($data['FechaAdquisicion']) ? date('Y-m-d', strtotime($data['FechaAdquisicion'])) : null;

            $stmt = $this->db->prepare('EXEC sp_GuardarActivo 
                @pIdActivo = ?, 
                @pIdDocIngresoAlm = ?, 
                @pIdArticulo = ?, 
                @pCodigo = ?, 
                @pSerie = ?, 
                @pIdEstado = ?, 
                @pGarantia = ?, 
                @pFechaFinGarantia = ?, 
                @pIdProveedor = ?, 
                @pObservaciones = ?, 
                @pIdSucursal = ?, 
                @pIdAmbiente = ?, 
                @pIdCategoria = 1, 
                @pVidaUtil = ?, 
                @pValorAdquisicion = ?, 
                @pFechaAdquisicion = ?, 
                @pUserMod = ?, 
                @pAccion = ?');

            $stmt->bindParam(1, $data['IdActivo'], \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(2, $data['IdDocIngresoAlm'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['IdArticulo'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['Codigo'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(5, $data['Serie'], \PDO::PARAM_STR);
            $stmt->bindParam(6, $data['IdEstado'], \PDO::PARAM_INT);
            $stmt->bindParam(7, $data['Garantia'], \PDO::PARAM_INT);
            $stmt->bindParam(8, $fechaFinGarantia, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(9, $data['IdProveedor'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(10, $data['Observaciones'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(11, $data['IdSucursal'], \PDO::PARAM_INT);
            $stmt->bindParam(12, $data['IdAmbiente'], \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(13, $data['VidaUtil'], \PDO::PARAM_INT);
            $stmt->bindParam(14, $data['ValorAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(15, $fechaAdquisicion, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(16, $data['UserMod'], \PDO::PARAM_STR);
            $stmt->bindParam(17, $data['Accion'], \PDO::PARAM_INT);

            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in registrarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function GuardarActivosDesdeDocumentoIngreso($data)
    {
        try {
            // Formatear fechas al formato SQL Server
            $fechaFinGarantia = !empty($data['FechaFinGarantia']) ? date('Y-m-d', strtotime($data['FechaFinGarantia'])) : null;
            $fechaAdquisicion = !empty($data['FechaAdquisicion']) ? date('Y-m-d', strtotime($data['FechaAdquisicion'])) : date('Y-m-d');
            $empresa = $_SESSION['cod_empresa'] ??  null;
            $sucursal = $_SESSION['cod_UnidadNeg'] ?? null;

            $stmt = $this->db->prepare('EXEC sp_RegistrarActivoDesdeDocumento 
                @pIdDocIngresoAlm = ?,
                @pIdArticulo = ?,
                @pIdEstado = ?,
                @pGarantia = ?,
                @pFechaFinGarantia = ?,
                @pIdProveedor = ?,
                @pIdEmpresa = ?,
                @pIdSucursal = ?,
                @pIdAmbiente = ?,
                @pIdCategoria = ?,
                @pVidaUtil = ?,
                @pSerie = ?,
                @pObservaciones = ?,
                @pValorAdquisicion = ?,
                @pAplicaIGV = ?,
                @pFechaAdquisicion = ?,
                @pUserMod = ?');

            $stmt->bindParam(1, $data['IdDocIngresoAlm'], \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(2, $data['IdArticulo'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['IdEstado'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['Garantia'], \PDO::PARAM_INT);
            $stmt->bindParam(5, $fechaFinGarantia, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(6, $data['IdProveedor'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(7, $empresa, \PDO::PARAM_INT);
            $stmt->bindParam(8, $sucursal, \PDO::PARAM_INT);
            $stmt->bindParam(9, $data['IdAmbiente'], \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(10, $data['IdCategoria'], \PDO::PARAM_INT);
            $stmt->bindParam(11, $data['VidaUtil'], \PDO::PARAM_INT);
            $stmt->bindParam(12, $data['Serie'], \PDO::PARAM_STR);
            $stmt->bindParam(13, $data['Observaciones'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(14, $data['ValorAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(15, $data['AplicaIGV'], \PDO::PARAM_INT);
            $stmt->bindParam(16, $fechaAdquisicion, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(17, $data['UserMod'], \PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in GuardarActivosDesdeDocumentoIngreso: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function GuardarActivosDesdeDocumentoVenta($data)
    {
        try {
            // Formatear fechas al formato SQL Server
            $fechaAdquisicion = !empty($data['FechaAdquisicion']) ? date('Y-m-d', strtotime($data['FechaAdquisicion'])) : date('Y-m-d');
            $empresa = $_SESSION['cod_empresa'] ??  null;
            $sucursal = $_SESSION['cod_UnidadNeg'] ?? null;

            $stmt = $this->db->prepare('EXEC sp_RegistrarActivoDesdeDocumentoVenta
                @pIdDocumentoVta = ?,
                @pIdArticulo = ?,
                @pIdEstado = ?,
                @pCantidad = ?,
                @pIdProveedor = ?,
                @pSerie = ?,
                @pIdEmpresa = ?,
                @pIdSucursal = ?,
                @pIdAmbiente = ?,
                @pIdCategoria = ?,
                @pVidaUtil = ?,
                @pObservaciones = ?,
                @pValorAdquisicion = ?,
                @pAplicaIGV = ?,
                @pFechaAdquisicion = ?,
                @pUserMod = ?');

            $stmt->bindParam(1, $data['IdDocVenta'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdArticulo'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['IdEstado'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['Cantidad'], \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(5, $data['IdProveedor'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(6, $data['Serie'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(7, $empresa, \PDO::PARAM_INT);
            $stmt->bindParam(8, $sucursal, \PDO::PARAM_INT);
            $stmt->bindParam(9, $data['IdAmbiente'], \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(10, $data['IdCategoria'], \PDO::PARAM_INT);
            $stmt->bindParam(11, $data['VidaUtil'], \PDO::PARAM_INT);
            $stmt->bindParam(12, $data['Observaciones'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(13, $data['ValorAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(14, $data['AplicaIGV'], \PDO::PARAM_INT);
            $stmt->bindParam(15, $fechaAdquisicion, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(16, $data['UserMod'], \PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in GuardarActivosDesdeDocumentoVenta: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function GuardarActivosManual($data)
    {
        try {
            // Formatear fechas
            $fechaAdquisicion = !empty($data['FechaAdquisicion']) ? date('Y-m-d', strtotime($data['FechaAdquisicion'])) : date('Y-m-d');
            // Convertir cadenas vacías a null para parámetros numéricos
            $idDocumentoVenta = empty($data['IdDocumentoVenta']) ? null : $data['IdDocumentoVenta'];
            $idOrdendeCompra = empty($data['IdOrdendeCompra']) ? null : $data['IdOrdendeCompra'];

            $stmt = $this->db->prepare('EXEC sp_RegistrarActivoManual
                @pNombre = ?,
                @pIdFactura = ?,
                @pCodigoAntiguo = ?,
                @pDescripcion = ?,
                @pIdEstado = ?,
                @pGarantia = ?,
                @pIdResponsable = ?,
                @pFechaFinGarantia = ?,
                @pIdProveedor = ?,
                @pIdMarca = ?,
                @pIdEmpresa = ?,
                @pIdSucursal = ?,
                @pIdAmbiente = ?,
                @pIdCategoria = ?,
                @pVidaUtil = ?,
                @pSerie = ?,
                @pObservaciones = ?,
                @pValorAdquisicion = ?,
                @pAplicaIGV = ?,
                @pFechaAdquisicion = ?,
                @pUserMod = ?,
                @pCantidad = ?,
                @pCorrelativoManual = ?');

            //$stmt->bindParam(1, $data['IdActivo'], \PDO::PARAM_INT);
            $stmt->bindParam(1, $data['Nombre'], \PDO::PARAM_STR);
            $stmt->bindParam(2, $data['IdFactura'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['CodigoAntiguo'], \PDO::PARAM_STR);
            $stmt->bindParam(4, $data['Descripcion'], \PDO::PARAM_STR);
            $stmt->bindParam(5, $data['IdEstado'], \PDO::PARAM_INT);
            $stmt->bindParam(6, $data['Garantia'], \PDO::PARAM_INT);
            $stmt->bindParam(7, $data['IdResponsable'], \PDO::PARAM_STR);
            $stmt->bindParam(8, $data['FechaFinGarantia'], \PDO::PARAM_STR);
            $stmt->bindParam(9, $data['IdProveedor'], \PDO::PARAM_STR);
            $stmt->bindParam(10, $data['IdMarca'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            //$stmt->bindParam(6, $data['Codigo'], \PDO::PARAM_STR);
            $stmt->bindParam(11, $data['IdEmpresa'], \PDO::PARAM_INT);
            $stmt->bindParam(12, $data['IdSucursal'], \PDO::PARAM_INT);
            $stmt->bindParam(13, $data['IdAmbiente'], \PDO::PARAM_INT);
            $stmt->bindParam(14, $data['IdCategoria'], \PDO::PARAM_INT);
            $stmt->bindParam(15, $data['VidaUtil'], \PDO::PARAM_INT);
            $stmt->bindParam(16, $data['Serie'], \PDO::PARAM_STR);
            $stmt->bindParam(17, $data['Observaciones'], \PDO::PARAM_STR);
            $stmt->bindParam(18, $data['ValorAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(19, $data['AplicaIGV'], \PDO::PARAM_INT);
            $stmt->bindParam(20, $data['FechaAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(21, $data['UserMod'], \PDO::PARAM_STR);
            //$stmt->bindParam(22, $data['MotivoBaja'], \PDO::PARAM_STR);
            $stmt->bindParam(21, $data['Cantidad'], \PDO::PARAM_INT);
            $stmt->bindParam(23, $data['Correlativo'], \PDO::PARAM_STR | \PDO::PARAM_NULL);

            // Debug: Log del correlativo que se envía al SP
            error_log("Correlativo enviado al SP: " . ($data['Correlativo'] ?? 'NULL'), 3, __DIR__ . '/../../logs/debug.log');

            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in registrarActivosManual: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }


    public function actualizarActivos($data)
    {
        try {
            if ($data['accion'] == 3) {
                $stmt = $this->db->prepare("EXEC sp_GuardarActivoPRUEBA @pIdActivo = ?, @pIdEstado = ?, @pIdResponsable = ?, @pMotivoBaja = ?, @pUserMod = ?, @pAccion = ?");
                $stmt->bindParam(1, $data['idActivo'], PDO::PARAM_INT);
                $stmt->bindParam(2, $data['idEstado'], PDO::PARAM_INT);
                $stmt->bindParam(3, $data['idResponsable'], PDO::PARAM_INT);
                $stmt->bindParam(4, $data['motivoBaja'], PDO::PARAM_STR);
                $stmt->bindParam(5, $data['userMod'], PDO::PARAM_STR);
                $stmt->bindParam(6, $data['accion'], PDO::PARAM_INT);
            } else {
                $stmt = $this->db->prepare("EXEC sp_GuardarActivoPRUEBA @pIdActivo = ?, @pSerie = ?, @pIdEstado = ?, @pIdAmbiente = ?, @pIdCategoria = ?, @pObservaciones = ?, @pUserMod = ?, @pAccion = ?");
                $stmt->bindParam(1, $data['IdActivo'], PDO::PARAM_INT);
                $stmt->bindParam(2, $data['Serie'], PDO::PARAM_STR);
                $stmt->bindParam(3, $data['IdEstado'], PDO::PARAM_INT | PDO::PARAM_NULL);
                $stmt->bindParam(4, $data['IdAmbiente'], PDO::PARAM_INT | PDO::PARAM_NULL);
                $stmt->bindParam(5, $data['IdCategoria'], PDO::PARAM_INT | PDO::PARAM_NULL);
                $stmt->bindParam(6, $data['Observaciones'], PDO::PARAM_STR);
                $stmt->bindParam(7, $data['UserMod'], PDO::PARAM_STR);
                $stmt->bindParam(8, $data['Accion'], PDO::PARAM_INT);
            }
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log("Error en actualizarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    // ASIGNAR RESPONSABLES A ACTIVOS
    public function asignarResponsables($data)
    {
        try {
            //code...
            $stmt = $this->db->prepare("EXEC sp_GuardarActivoPRUEBA
            @pIdActivo = ?,
            @pIdResponsable = ?,
            @pUserMod = ?,
            @pAccion = 4");

            $stmt->bindParam(1, $data['IdActivo'], PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdResponsable'], PDO::PARAM_STR);
            $stmt->bindParam(3, $data['UserMod'], PDO::PARAM_STR);
            $stmt->bindParam(4, $data['Accion'], PDO::PARAM_INT);

            $stmt->execute();
            return true;
        } catch (Exception $e) {
            //throw $th;
            error_log("Error en asignarResponsables: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function verificarArticuloExistente($idDocIngresoAlm, $idArticulo)
    {
        try {
            // Optimizar la consulta usando EXISTS en lugar de COUNT
            $stmt = $this->db->prepare("
                SELECT CAST(CASE WHEN EXISTS (
                SELECT 1 
                FROM tOrigenActivo oa
                INNER JOIN tUbicacionActivo ua ON oa.idActivo = ua.idActivo
                WHERE oa.idDocIngresoAlm = ?
                AND oa.idArticulo = ?
                AND ua.idEmpresa = ?
                AND ua.idSucursal = ?
            ) THEN 1 ELSE 0 END AS BIT) as existe
            ");
            $stmt->bindParam(1, $idDocIngresoAlm, PDO::PARAM_INT);
            $stmt->bindParam(2, $idArticulo, PDO::PARAM_INT);
            $stmt->bindParam(3, $_SESSION['cod_empresa'], PDO::PARAM_INT);
            $stmt->bindParam(4, $_SESSION['cod_UnidadNeg'], PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['existe'] == 1;
        } catch (\PDOException $e) {
            error_log("Error in verificarArticuloExistente: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function verificarArticuloExistenteDocVenta($idDocumentoVta, $idArticulo)
    {
        try {
            // Verificar si el artículo ya existe para este documento de venta

            $stmt = $this->db->prepare("
                SELECT CAST(CASE WHEN EXISTS (
                SELECT 1 
                FROM tOrigenActivo oa
                INNER JOIN tUbicacionActivo ua ON oa.idActivo = ua.idActivo
                WHERE oa.IdDocumentoVenta = ?
                AND oa.idArticulo = ?
                AND ua.idEmpresa = ?
                AND ua.idSucursal = ?
            ) THEN 1 ELSE 0 END AS BIT) as existe
            ");
            $stmt->bindParam(1, $idDocumentoVta, PDO::PARAM_INT);
            $stmt->bindParam(2, $idArticulo, PDO::PARAM_INT);
            $stmt->bindParam(3, $_SESSION['cod_empresa'], PDO::PARAM_INT);
            $stmt->bindParam(4, $_SESSION['cod_UnidadNeg'], PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['existe'] == 1;
        } catch (\PDOException $e) {
            error_log("Error in verificarArticuloExistenteDocVenta: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function verificarResponsableExistente($idActivo)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as existe
                FROM tUbicacionActivo
                WHERE idActivo = ?
                AND idResponsable IS NOT NULL
                AND esActual = 1
            ");
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['existe'] > 0;
        } catch (\PDOException $e) {
            error_log("Error in verificarResponsableExistente: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function consultarActivosIndividuales()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT a.idActivo, a.codigo AS Codigo, ad.nombre AS NombreActivo,
                ua.idEmpresa AS IdEmpresa, un.Nombre_local AS Locacion, 1 AS Cantidad,
                ad.valorAdquisicion AS ValorTotal
                FROM tActivos a
                INNER JOIN tActivoDetalle ad ON a.idActivo = ad.idActivo
                INNER JOIN tUbicacionActivo ua ON a.idActivo = ua.idActivo AND ua.esActual = 1
                LEFT JOIN vUnidadesdeNegocio un ON ua.idSucursal = un.cod_UnidadNeg
                LEFT JOIN tOrigenActivo oa ON a.idActivo = oa.idActivo
                WHERE oa.idArticulo IS NULL AND oa.idDocIngresoAlm IS NULL
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in consultarActivosIndividuales: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function obtenerHistorialActivo($idActivo)
    {
        try {
            $stmt = $this->db->prepare("EXEC sp_ConsultarHistorialActivo @pIdActivo = ?");
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in obtenerHistorialActivo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function obtenerComponente($idActivo)
    {
        try {
            $stmt = $this->db->prepare("SELECT hijo.idActivo AS IdActivoComponente,
            hijo.codigo AS CodigoComponente,
            hijo.NombreActivo AS NombreComponente, padre.idActivo AS IdActivoPadre,
            padre.codigo AS CodigoPadre, padre.NombreActivo AS NombrePadre
            FROM vActivos hijo
            INNER JOIN tActivoDetalle detalleH ON hijo.idActivo = detalleH.idActivo
            INNER JOIN vActivos padre ON hijo.idActivoPadre = padre.idActivo
            INNER JOIN tActivoDetalle detalleP ON padre.idActivo = detalleP.idActivo
            WHERE hijo.idActivoPadre = ?");
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in obtenerComponente: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function verificarConfiguracionCorrelativo($idEmpresa, $idCategoria)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.estadoActivo,
                    v.AbreEmpresa,
                    v.codigoClase,
                    c.numeroCorrelativo
                FROM tCorrelativo c
                JOIN vCorrelativoActivos v ON v.idEmpresa = c.idEmpresa AND v.idCategoria = c.idCategoria
                WHERE c.idEmpresa = ? AND c.idCategoria = ?
            ");
            $stmt->bindParam(1, $idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(2, $idCategoria, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in verificarConfiguracionCorrelativo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function obtenerMantenimientosActivo($idActivo){
        try {
            //code...
            $stmt = $this->db->prepare("SELECT m.idMantenimiento, m.codigoMantenimiento,
                    ISNULL(CONVERT(VARCHAR(10), m.fechaRegistro, 103), 'Sin Fecha') AS fechaRegistro,
                    m.descripcion, m.costoEstimado,
                    ISNULL(CAST(m.costoReal AS VARCHAR(50)), 'Se canceló o en proceso') AS CostoReal,
                    em.nombre AS estadoActual, tm.nombre AS tipoMantenimiento, m.idResponsable, e.NombreTrabajador AS responsable
                    FROM tMantenimientos m
                    INNER JOIN tDetalleMantenimiento dm ON m.idMantenimiento = dm.idMantenimiento
                    INNER JOIN tEstadoMantenimiento em ON m.estadoMantenimiento = em.idEstadoMantenimiento
                    INNER JOIN tTipoMantenimiento tm ON m.idTipoMantenimiento = tm.idTipoMantenimiento
                    LEFT JOIN vEmpleados e ON m.idResponsable = e.codTrabajador
                    WHERE dm.idActivo = ?
                    ORDER BY m.fechaRegistro DESC;");

            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in obtenerMantenimientosActivo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function obtenerUltimosEventosActivo($idActivo)
    {
        try {
            /*error_log("=== DEBUGGING MODELO EVENTOS ===", 3, __DIR__ . '/../../logs/debug.log');
            error_log("Consultando eventos para activo ID: " . $idActivo, 3, __DIR__ . '/../../logs/debug.log');*/

            // Primero verificar si la vista existe
            $checkView = $this->db->prepare("SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_NAME = 'vUltimosEventosActivo'");
            $checkView->execute();
            $vistaExiste = $checkView->fetch(PDO::FETCH_ASSOC);
            error_log("¿Vista existe? " . print_r($vistaExiste, true), 3, __DIR__ . '/../../logs/debug.log');

            if ($vistaExiste['existe'] == 0) {
                error_log("La vista vUltimosEventosActivo no existe", 3, __DIR__ . '/../../logs/debug.log');
                // Crear consulta alternativa
                $stmt = $this->db->prepare("SELECT a.idActivo, a.codigo, a.NombreActivo AS nombreActivo,
                    -- Último Movimiento (cualquier tipo)
                    (SELECT TOP 1 dm.fecha FROM tDetalleMovimiento dm
                     WHERE dm.idActivo = a.idActivo ORDER BY dm.fecha DESC) AS fechaUltimoMovimiento,
                    -- Último Préstamo
                    (SELECT TOP 1 dm.fecha
                     FROM tDetalleMovimiento dm
                     WHERE dm.idActivo = a.idActivo AND dm.idTipoMovimiento = 2
                     ORDER BY dm.fecha DESC) AS fechaUltimoPrestamo,
                    -- Última Devolución
                    (SELECT TOP 1 dm.fecha
                    FROM tDetalleMovimiento dm
                    WHERE dm.idActivo = a.idActivo AND dm.idTipoMovimiento = 3
                    ORDER BY dm.fecha DESC) AS fechaUltimaDevolucion,
                    -- Último Traslado
                    (SELECT TOP 1 dm.fecha
                    FROM tDetalleMovimiento dm
                    WHERE dm.idActivo = a.idActivo AND dm.idTipoMovimiento = 1
                    ORDER BY dm.fecha DESC) AS fechaUltimoTraslado,
                    -- Último mantenimiento
                    (SELECT TOP 1 fechaRegistro
                    FROM tDetalleMantenimiento dm
                    INNER JOIN tMantenimientos m ON m.idMantenimiento = dm.idMantenimiento
                    WHERE dm.idActivo = a.idActivo
                    ORDER BY m.fechaRegistro) AS fechaUltimoMantenimiento
                    FROM vActivos a
                    WHERE a.idActivo = ?");
            } else {
                $stmt = $this->db->prepare("SELECT 
                    idActivo,
                    codigo,
                    nombreActivo,
                    fechaUltimoMovimiento,
                    fechaUltimoPrestamo,
                    fechaUltimaDevolucion,
                    fechaUltimoTraslado,
                    fechaUltimoMantenimiento
                    FROM vUltimosEventosActivo 
                    WHERE idActivo = ?");
            }

            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            error_log("Resultado de la consulta: " . print_r($resultado, true), 3, __DIR__ . '/../../logs/debug.log');

            // Verificar si existen registros de mantenimiento para este activo
            $checkMant = $this->db->prepare("SELECT COUNT(*) as total FROM tDetalleMantenimiento WHERE idActivo = ?");
            $checkMant->bindParam(1, $idActivo, PDO::PARAM_INT);
            $checkMant->execute();
            $totalMant = $checkMant->fetch(PDO::FETCH_ASSOC);
            error_log("Total registros de mantenimiento para activo $idActivo: " . $totalMant['total'], 3, __DIR__ . '/../../logs/debug.log');

            if ($totalMant['total'] > 0) {
                // Obtener el último mantenimiento directamente
                $lastMant = $this->db->prepare("SELECT TOP 1 
                    dm.idActivo, dm.idMantenimiento, m.fechaRegistro, ISNULL(m.fechaRegistro, m.fechaMod) as fechaUltimoMantenimiento
                    FROM tDetalleMantenimiento dm
                    INNER JOIN tMantenimientos m ON m.idMantenimiento = dm.idMantenimiento
                    WHERE dm.idActivo = ?
                    ORDER BY fechaRegistro DESC
                ");
                $lastMant->bindParam(1, $idActivo, PDO::PARAM_INT);
                $lastMant->execute();
                $ultimoMant = $lastMant->fetch(PDO::FETCH_ASSOC);
                error_log("Último mantenimiento directo: " . print_r($ultimoMant, true), 3, __DIR__ . '/../../logs/debug.log');

                // Si la consulta principal no devolvió el mantenimiento pero existe, agregarlo manualmente
                if ($resultado && !$resultado['fechaUltimoMantenimiento'] && $ultimoMant) {
                    $resultado['fechaUltimoMantenimiento'] = $ultimoMant['fechaUltimoMantenimiento'];
                    error_log("Mantenimiento agregado manualmente al resultado", 3, __DIR__ . '/../../logs/debug.log');
                }
            }

            return $resultado;
        } catch (\PDOException $e) {
            error_log("Error in obtenerUltimosEventosActivo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/debug.log');
            error_log("SQL Error: " . $e->getTraceAsString(), 3, __DIR__ . '/../../logs/debug.log');
            throw $e;
        }
    }

    public function darBajaActivo($data)
    {
        try {
            $stmt = $this->db->prepare('EXEC sp_BajaActivo 
                @pIdActivo = ?, 
                @pidTipoBaja = ?, 
                @pMotivo = ?, 
                @pDocumentoSoporte = ?, 
                @pObservaciones = ?, 
                @pUserMod = ?');

            $stmt->bindParam(1, $data['IdActivo'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['pidTipoBaja'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['Motivo'], \PDO::PARAM_STR);
            $stmt->bindParam(4, $data['DocumentoSoporte'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(5, $data['Observaciones'], \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(6, $data['UserMod'], \PDO::PARAM_STR);

            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in darBajaActivo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
