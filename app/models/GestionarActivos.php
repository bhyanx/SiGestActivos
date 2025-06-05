<?php
require_once '../config/configuracion.php';

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
            // Convertir cadenas vacías a null o enteros para parámetros numéricos
            $pCodigo = empty($data['pCodigo']) ? null : $data['pCodigo'];
            $pIdSucursal = empty($data['pIdSucursal']) ? null : (int)$data['pIdSucursal'];
            $pIdCategoria = empty($data['pIdCategoria']) ? null : (int)$data['pIdCategoria'];
            $pIdEstado = empty($data['pIdEstado']) ? null : (int)$data['pIdEstado'];

            $stmt = $this->db->prepare('EXEC sp_ConsultarActivos @pCodigo = ?, @pIdSucursal = ?, @pIdCategoria = ?, @pIdEstado = ?');
            $stmt->bindParam(1, $pCodigo, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(2, $pIdSucursal, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(3, $pIdCategoria, \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(4, $pIdEstado, \PDO::PARAM_INT | \PDO::PARAM_NULL);
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
            $stmt->bindParam(11, $sucursal, \PDO::PARAM_INT);
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

    public function registrarActivosPrueba($data)
    {
        try {
            // Formatear fechas al formato SQL Server
            $fechaFinGarantia = !empty($data['FechaFinGarantia']) ? date('Y-m-d', strtotime($data['FechaFinGarantia'])) : null;
            $fechaAdquisicion = !empty($data['FechaAdquisicion']) ? date('Y-m-d', strtotime($data['FechaAdquisicion'])) : null;
            $sucursal = $_SESSION['cod_UnidadNeg'] ?? null;

            $stmt = $this->db->prepare('EXEC sp_GuardarActivoPRUEBA
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
                @pIdCategoria = ?, 
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
            $stmt->bindParam(11, $sucursal, \PDO::PARAM_INT);
            $stmt->bindParam(12, $data['IdAmbiente'], \PDO::PARAM_INT | \PDO::PARAM_NULL);
            $stmt->bindParam(13, $data['IdCategoria'], \PDO::PARAM_INT);
            $stmt->bindParam(14, $data['VidaUtil'], \PDO::PARAM_INT);
            $stmt->bindParam(15, $data['ValorAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(16, $fechaAdquisicion, \PDO::PARAM_STR | \PDO::PARAM_NULL);
            $stmt->bindParam(17, $data['UserMod'], \PDO::PARAM_STR);
            $stmt->bindParam(18, $data['Accion'], \PDO::PARAM_INT);

            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in registrarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    // !COMENTADO POR DESUSO EN EL REGISTRO MANUAL DE ACTIVOS.
    // public function registrarActivosManual($data)
    // {
    //     try {

    //         $sucursal = $_SESSION['cod_UnidadNeg'] ?? null;
    //         if ($sucursal === null) {
    //             throw new Exception("No se ha encontrado la sucursal");
    //         }

    //         $nombre = !empty($data['Nombre']) ? trim($data['Nombre']) : null;
    //         $descripcion = !empty($data['Descripcion']) ? trim($data['Descripcion']) : null;
    //         $serie = !empty($data['Serie']) ? trim($data['Serie']) : null;
    //         $IdEstado = !empty($data['IdEstado']) ? (int)$data['IdEstado'] : null;
    //         $IdCategoria = !empty($data['IdCategoria']) ? (int)$data['IdCategoria'] : null;
    //         $idAmbiente = !empty($data['IdAmbiente']) ? (int)$data['IdAmbiente'] : null;
    //         $cantidad = !empty($data['Cantidad']) ? (int)$data['Cantidad'] : null;
    //         $fechaAdquisicion = !empty($data['FechaAdquisicion']) ? date('Y-m-d', strtotime($data['FechaAdquisicion'])) : null;
    //         $userMod = !empty($data['UserMod']) ? trim($data['UserMod']) : null;
    //         $accion = 1;

    //         if (empty($nombre) || empty($descripcion) || empty($serie) || empty($idEstado) || empty($IdCategoria) || empty($idAmbiente) || empty($cantidad) || empty($userMod)) {
    //             throw new Exception("Todos los campos son obligatorios.");
    //         }


    //         $stmt = $this->db->prepare('EXEC sp_GuardarActivoManual
    //         @pNombre = ?,
    //         @pDescripcion = ?,
    //         @pSerie = ?,
    //         @pIdEstado = 1,
    //         @pIdCategoria = ?,
    //         @pIdAmbiente = ?,
    //         @pIdSucursal = ?,
    //         @pCantidad = ?,
    //         @pFechaAdquisicion = ?,
    //         @pUserMod = ?,
    //         @pAccion = 1');

    //         $stmt->bindParam(1, $data['Nombre'], \PDO::PARAM_STR);
    //         $stmt->bindParam(2, $data['Descripcion'], \PDO::PARAM_STR);
    //         $stmt->bindParam(3, $data['Serie'], \PDO::PARAM_STR);
    //         $stmt->bindParam(4, $data['IdEstado'], \PDO::PARAM_INT);
    //         $stmt->bindParam(5, $data['IdCategoria'], \PDO::PARAM_INT);
    //         $stmt->bindParam(6, $data['IdAmbiente'], \PDO::PARAM_INT);
    //         $stmt->bindParam(7, $data['IdSucursal'], \PDO::PARAM_INT);
    //         $stmt->bindParam(8, $data['Cantidad'], \PDO::PARAM_INT);
    //         $stmt->bindParam(9, $data['FechaAdquisicion'], \PDO::PARAM_STR);
    //         $stmt->bindParam(10, $data['UserMod'], \PDO::PARAM_STR);

    //         $stmt->execute();
    //         return true;
    //     } catch (\Throwable $th) {
    //         error_log("Error in registrarActivosManual: " . $th->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
    //         throw $th;
    //     }
    // }

    public function actualizarActivos($data)
    {
        try {
            //$db = (new Conectar())->ConexionBdPracticante();
            $stmt = $this->db->prepare("EXEC sp_GuardarActivoPRUEBA 
                @pIdActivo = ?, 
                @pSerie = ?, 
                @pIdEstado = ?, 
                @pIdAmbiente = ?, 
                @pIdCategoria = ?, 
                @pObservaciones = ?, 
                @pUserMod = ?, 
                @pAccion = ?");

            $stmt->bindParam(1, $data['IdActivo'], PDO::PARAM_INT);
            $stmt->bindParam(2, $data['Serie'], PDO::PARAM_STR);
            $stmt->bindParam(3, $data['IdEstado'], PDO::PARAM_INT);
            $stmt->bindParam(4, $data['IdAmbiente'], PDO::PARAM_INT);
            $stmt->bindParam(5, $data['IdCategoria'], PDO::PARAM_INT);
            $stmt->bindParam(6, $data['Observaciones'], PDO::PARAM_STR);
            $stmt->bindParam(7, $data['UserMod'], PDO::PARAM_STR);
            $stmt->bindParam(8, $data['Accion'], PDO::PARAM_INT);

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
            @pAccion = ?");

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

    /*public function getCategoriaCode($IdCategoria)
    {
        $stmt = $this->db->prepare("SELECT Codigo FROM tCategoriasActivo WHERE IdCategoria = ?");
        $stmt->bindParam(1, $IdCategoria, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['Codigo'] : 'XXX'; // XXX if not found
    }

    public function getSucursalCode($IdSucursal)
    {
        $stmt = $this->db->prepare("SELECT Codigo FROM tSucursales WHERE cod_UnidadNeg = ?");
        $stmt->bindParam(1, $IdSucursal, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['Codigo'] : 'YYY'; // YYY if not found
    }

    public function getNextSequentialNumber($EMP, $CCC)
    {
        // Get the last sequential number used for this EMP and CCC
        $stmt = $this->db->prepare("SELECT MAX(Codigo) AS LastCode FROM tActivos WHERE Codigo LIKE ?");
        $like = "$EMP-$CCC-%";
        $stmt->bindParam(1, $like, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $lastCode = $result['LastCode'];

        if ($lastCode) {
            // Extract the number and increment it
            $parts = explode('-', $lastCode);
            $number = intval($parts[2]) + 1;
        } else {
            // Start from 1
            $number = 1;
        }

        // Pad the number with leading zeros
        return str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function generarCodigoActivo($IdCategoria, $IdSucursal)
    {
        try {
            // Obtener códigos de categoría y sucursal
            $codigoCategoria = $this->getCategoriaCode($IdCategoria);
            $codigoSucursal = $this->getSucursalCode($IdSucursal);

            // Generar número secuencial
            $numeroSecuencial = $this->getNextSequentialNumber($codigoCategoria, $codigoSucursal);

            // Formatear código final: CAT-SUC-NUM
            return $codigoCategoria . '-' . $codigoSucursal . '-' . $numeroSecuencial;
        } catch (\PDOException $e) {
            error_log("Error in generarCodigoActivo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }*/
}
