<?php
//require_once '../config/configuracion.php';
class ActivosModels extends Conectar
{
    public function get_Activos($idSucursal)
    {
        if ($idSucursal == 0) {
            $idSucursal = '';
        }

        $conectar = parent::ConexionBdPracticante();
        //parent::set_names('utf8');
        $sql = "SELECT a.idActivo, a.idDocIngresoAlm, a.idArticulo, a.codigo, a.serie, a.idEstado, a.enUso, a.idSucursal, a.idAmbiente, a.idCategoria, a.vidaUtil, a.valorAdquisicion, a.fechaAdquisicion, a.garantia, a.fechaFinGarantia, a.idProveedor, a.observaciones, a.fechaRegistro, a.fechaMod, a.userMod FROM tActivo AS A INNER JOIN vUnidadesEmpresa v ON A.IdSucursal = v.cod_empresa";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_Activos_id($idActivo){
        $conectar = parent::ConexionBdPracticante();

        $sql = "SELECT a.idActivo, a.idDocIngresoAlm, a.idArticulo, a.codigo, a.serie, a.idEstado, a.enUso, a.idSucursal, a.idAmbiente, a.idCategoria, a.vidaUtil, a.valorAdquisicion, a.fechaAdquisicion, a.garantia, a.fechaFinGarantia, a.idProveedor, a.observaciones, a.fechaRegistro, a.fechaMod, a.userMod FROM tActivo AS A INNER JOIN vUnidadesEmpresa v ON A.IdSucursal = v.cod_empresa WHERE idActivo = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $idActivo);
        $sql->execute();
        return $resultado = $sql->fetchObject();
    }

    public function insert($idDocIngresoAlm, $idArticulo, $codigo, $serie, $idEstado, $enUso, $idSucursal, $idAmbiente, $idCategoria, $vidaUtil, $valorAdquisicion, $fechaAdquisicion, $garantia, $fechaFinGarantia, $idProveedor, $observaciones, $userMod)
    {
        $conectar = parent::ConexionBdPracticante();
        //parent::set_names('utf8');
        $sql = "INSERT INTO tActivo (idDocIngresoAlm,idArticulo,codigo,serie,idEstado,enUso,idSucursal,idAmbiente,idCategoria,vidaUtil,valorAdquisicion,fechaAdquisicion,garantia,fechaFinGarantia,idProveedor,observaciones,userMod) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $idDocIngresoAlm);
        $sql->bindValue(2, $idArticulo);
        $sql->bindValue(3, $codigo);
        $sql->bindValue(4, $serie);
        $sql->bindValue(5, $idEstado);
        $sql->bindValue(6, $enUso);
        $sql->bindValue(7, $idSucursal);
        $sql->bindValue(8, $idAmbiente);
        $sql->bindValue(9, $idCategoria);
        $sql->bindValue(10, $vidaUtil);
        $sql->bindValue(11, $valorAdquisicion);
        $sql->bindValue(12, $fechaAdquisicion);
        $sql->bindValue(13, $garantia);
        $sql->bindValue(14, $fechaFinGarantia);
        $sql->bindValue(15, $idProveedor);
        $sql->bindValue(16, $observaciones);
        $sql->bindValue(17, $userMod);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function update($idActivo, $idDocIngresoAlm, $idArticulo, $codigo, $serie, $idEstado, $enUso, $idSucursal, $idAmbiente, $idCategoria, $vidaUtil, $valorAdquisicion, $fechaAdquisicion, $garantia, $fechaFinGarantia, $idProveedor, $observaciones, $userMod)
    {
        $conectar = parent::ConexionBdPracticante();
        //parent::set_names('utf8');
        $sql = "UPDATE tActivo SET idDocIngresoAlm = ?, idArticulo = ?, codigo = ?, serie = ?, idEstado = ?, enUso = ?, idSucursal = ?, idAmbiente = ?, idCategoria = ?, vidaUtil = ?, valorAdquisicion = ?, fechaAdquisicion = ?, garantia = ?, fechaFinGarantia = ?, idProveedor = ?, observaciones = ?, userMod = ? WHERE idActivo = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $idDocIngresoAlm);
        $sql->bindValue(2, $idArticulo);
        $sql->bindValue(3, $codigo);
        $sql->bindValue(4, $serie);
        $sql->bindValue(5, $idEstado);
        $sql->bindValue(6, $enUso);
        $sql->bindValue(7, $idSucursal);
        $sql->bindValue(8, $idAmbiente);
        $sql->bindValue(9, $idCategoria);
        $sql->bindValue(10, $vidaUtil);
        $sql->bindValue(11, $valorAdquisicion);
        $sql->bindValue(12, $fechaAdquisicion);
        $sql->bindValue(13, $garantia);
        $sql->bindValue(14, $fechaFinGarantia);
        $sql->bindValue(15, $idProveedor);
        $sql->bindValue(16, $observaciones);
        $sql->bindValue(17, $userMod);
        $sql->bindValue(18, $idActivo);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function delete($idActivo)
    {
        $conectar = parent::ConexionBdPracticante();
        //parent::set_names('utf8');
        $sql = "UPDATE tActivo SET idEstado = 0 WHERE idActivo = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $idActivo);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function activar($idActivo)
    {
        $conectar = parent::ConexionBdPracticante();
        //parent::set_names('utf8');
        $sql = "UPDATE tActivo SET idEstado = 1 WHERE idActivo = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $idActivo);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}
?>
