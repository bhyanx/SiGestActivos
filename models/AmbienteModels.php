<?php
//require_once '../config/configuracion.php';
class AmbienteModels extends Conectar
{
    public function get_Ambiente($idSucursal)
    {
        if ($idSucursal == 0) {
            $idSucursal = '';
        }

        $conectar = parent::ConexionBdPracticante();
        //parent::set_names('utf8');
        $sql = "SELECT DISTINCT a.idAmbiente, a.nombre ,a.descripcion,a.idSucursal, v.Nombre_local ,estado ,fechaRegistro ,fechaMod,userMod FROM tAmbiente a INNER JOIN vUnidadesEmpresa v ON A.IdSucursal = v.cod_empresa";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_Ambiente_id($IdAmbiente)
    {
        $conectar = parent::ConexionBdPracticante();
        //parent::set_names('utf8');
        $sql = "SELECT * FROM tAmbiente WHERE IdAmbiente = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdAmbiente);
        $sql->execute();
        return $resultado = $sql->fetchObject();
    }

    public function insert($Nombre, $Descripcion, $IdSucursal, $Estado, $UserMod)
    {
        $conectar = parent::ConexionBdPracticante();
        //parent::set_names('utf8');
        $sql = "INSERT INTO tAmbiente (nombre, descripcion, idSucursal, estado, fechaRegistro, fechaMod, userMod) VALUES (?, ?, ?, ?, GETDATE(), GETDATE(), ?)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $Nombre);
        $sql->bindValue(2, $Descripcion);
        $sql->bindValue(3, $IdSucursal);
        $sql->bindValue(4, $Estado);
        $sql->bindValue(5, $UserMod);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function update($IdAmbiente, $Nombre, $Descripcion, $IdSucursal, $Estado, $UserMod)
    {
        $conectar = parent::ConexionBdPracticante();
        //parent::set_names('utf8');
        $sql = "UPDATE tAmbiente SET nombre = ?, descripcion = ?, idSucursal = ?, estado = ?, fechaMod = GETDATE(), userMod = ? WHERE IdAmbiente = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $Nombre);
        $sql->bindValue(2, $Descripcion);
        $sql->bindValue(3, $IdSucursal);
        $sql->bindValue(4, $Estado);
        $sql->bindValue(5, $UserMod);
        $sql->bindValue(6, $IdAmbiente);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function delete($IdAmbiente)
    {
        $conectar = parent::ConexionBdPracticante();
        //parent::set_names('utf8');
        $sql = "UPDATE tAmbiente SET estado = 0 WHERE IdAmbiente = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdAmbiente);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    public function activar($IdAmbiente)
    {
        $conectar = parent::ConexionBdPracticante();
        //parent::set_names('utf8');
        $sql = "UPDATE tAmbiente SET estado = 1 WHERE IdAmbiente = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdAmbiente);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}
