<?php

class GestionarActivos{
    
    private $db;

    public function __construct() {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }
    
    //! CLASES PARA GESTIONAR ACTIVOS CON PROCEDIMIENTOS ALMACENADOS(escritura diferente)


    //* FUNCION PARA REGISTRAR ACTIVOS CON PROCEDIMIENTOS ALMACENADOS
    public function registrarActivos($data){
        try{
            $stmt = $this->db->prepare('EXEC sp_RegistrarActivos @idDocIngresoAlm = ?, idArticulo = ?, @codigo = ?, serie = ?, @idEstado = ?, @enUso = ?, @idSucursal = ?, @idAmbiente = ?, @idCategoria = ?, vidaUtil = ?, @valorAdquisicion = ?, @fechaAdquisicion = ?, garantia = ?, @fechaFinGarantia = ?, @idProveedor = ?, observaciones = ?, @fechaRegistro = GETDATE(), @userMod = ?');

            //? PARAMETROS QUE REQUIERE MI TABLA PARA INSERTAR COMPLETAMENTE LOS DATOS
            //     idDocIngresoAlm, idArticulo, codigo, serie, idEstado, enUso, idSucursal, idAmbiente,
            //     idCategoria, vidaUtil, valorAdquisicion, fechaAdquisicion, garantia, fechaFinGarantia,
            //     idProveedor, observaciones, fechaRegistro, userMod

            $stmt->bindParam(1, $data['idDocIngresoAlm'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['idArticulo'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['codigo'], \PDO::PARAM_STR);
            $stmt->bindParam(4, $data['serie'], \PDO::PARAM_STR);
            $stmt->bindParam(5, $data['idEstado'], \PDO::PARAM_INT);
            $stmt->bindParam(6, $data['enUso'], \PDO::PARAM_INT);
            $stmt->bindParam(7, $data['idSucursal'], \PDO::PARAM_INT);
            $stmt->bindParam(8, $data['idAmbiente'], \PDO::PARAM_INT);
            $stmt->bindParam(9, $data['idCategoria'], \PDO::PARAM_INT);
            $stmt->bindParam(10, $data['vidaUtil'], \PDO::PARAM_INT);
            $stmt->bindParam(11, $data['valorAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(12, $data['fechaAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(13, $data['garantia'], \PDO::PARAM_INT);
            $stmt->bindParam(14, $data['fechaFinGarantia'], \PDO::PARAM_STR);
            $stmt->bindParam(15, $data['idProveedor'], \PDO::PARAM_INT);
            $stmt->bindParam(16, $data['observaciones'], \PDO::PARAM_STR);
            $stmt->bindParam(17, $data['userMod'], \PDO::PARAM_STR);
            $stmt->execute();
            return true;

        } catch(\PDOException $e){
            error_log("Error in registrarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;

        }
    }

    //* FUNCION PARA CONSULTAR ACTIVOS CON PROCEDIMIENTOS ALMACENADOS

    public function consultarActivos($data){
        try{
            $stmt = $this->db->prepare('EXEC sp_ConsultarActivos @pCodigo = ?, @pIdSucursal = ?, @pIdCategoria = ?, @pIdEstado = ?');
            $stmt->bindParam(1, $data['pCodigo'], \PDO::PARAM_STR);
            $stmt->bindParam(2, $data['pIdSucursal'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['pIdCategoria'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['pIdEstado'], \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch(\PDOException $e){
            error_log("Error in consultarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FUNCION PARA ACTUALIZAR ACTIVOS CON PROCEDIMIENTOS ALMACENADOS

    public function actualizarActivos($data){
        try{
            $stmt = $this->db->prepare('EXEC sp_GuardarActivo @pAccion = 2, @idActivo = ?, @CodigoActivo = ?, @IdEstado = ?, @IdSucursal = ?, @IdAmbiente = ?, @IdCategoria = ?, @IdProveedor = ?, @NombreArticulo = ?, @ValorAdquisicion = ?, @VidaUtil = ?, @FechaAdquisicion = ?, @UserMod = ?');

            $stmt->bindParam(1, $data['IdActivo'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['CodigoActivo'], \PDO::PARAM_STR);
            $stmt->bindParam(3, $data['IdEstado'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['IdSucursal'], \PDO::PARAM_INT);
            $stmt->bindParam(5, $data['IdAmbiente'], \PDO::PARAM_INT);
            $stmt->bindParam(6, $data['IdCategoria'], \PDO::PARAM_INT);
            $stmt->bindParam(7, $data['IdProveedor'], \PDO::PARAM_INT);
            $stmt->bindParam(8, $data['NombreArticulo'], \PDO::PARAM_STR);
            $stmt->bindParam(9, $data['ValorAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(10, $data['VidaUtil'], \PDO::PARAM_INT);
            $stmt->bindParam(11, $data['FechaAdquisicion'], \PDO::PARAM_STR);
            $stmt->bindParam(12, $data['UserMod'], \PDO::PARAM_STR);
            $stmt->execute();
            return true;

        }catch(\PDOException $e){
            error_log("Error in actualizarActivos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}

?>

