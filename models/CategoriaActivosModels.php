<?php
class CategoriaActivosModels extends Conectar{
    public function get_CategoriaActivos(){
        $conectar = parent::ConexionBdPracticante();

        $sql = "SELECT * FROM tCategoriasActivos";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_CategoriaActivos_id($IdCategoria){
        $conectar = parent::ConexionBdPracticante();

        $sql = "SELECT * FROM tCategeoriasActivo WHERE IdCategoria = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdCategoria);
        $sql->execute();
        return $resultado = $sql->fetchObject();
    }

    public function insert($Nombre, $Descripcion, $VidaUtil, $Estado, $UserMod){
        $conectar = parent::ConexionBdPracticante();

        $sql = "INSERT INTO tCategoriasActivo (nombre, descripcion, vidaUtilEstandar, estado, fechaRegistro, fechaMod, userMod) VALUES (?, ?, ?, ?, GETDATE(), GETDATE(), ?)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $Nombre);
        $sql->bindValue(2, $Descripcion);
        $sql->bindValue(3, $VidaUtil);
        $sql->bindValue(4, $Estado);
        $sql->bindValue(5, $UserMod);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function update($IdCategoria, $Nombre, $Descripcion, $VidaUtil, $Estado, $UserMod){
        $conectar = parent::ConexionBdPracticante();

        $sql = "UPDATE tCategoriasActivo SET nombre = ?, descripcion = ?, vidaUtilEstandar = ?, estado = ?, fechaMod = GETDATE(), userMod = ? WHERE IdCategoria = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $Nombre);
        $sql->bindValue(2, $Descripcion);
        $sql->bindValue(3, $VidaUtil);
        $sql->bindValue(4, $Estado);
        $sql->bindValue(5, $UserMod);
        $sql->bindValue(6, $IdCategoria);
        return $resultado = $sql->execute();

    }

    public function delete($IdCategoria){
        $conectar = parent::ConexionBdPracticante();

        $sql = "UPDATE tCategoriasActivo SET estado = 0 WHERE IdCategoria = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdCategoria);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function activar($IdCategoria){
        $conectar = parent::ConexionBdPracticante();

        $sql = "UPDATE tCategoriasActivo SET estado = 1 WHERE IdCategoria = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdCategoria);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

}
?>