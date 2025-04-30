<?php
class RolModels extends Conectar {
    public function get_Rol(){
        $conectar = parent::ConexionBdPracticante();

        $sql = "SELECT * FROM tRoles";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_Permisos_Rol($IdRol){
        $conectar = parent::ConexionBdPracticante();

        $sql = "SELECT * FROM tPermisos WHERE (? IS NULL) or IdRol = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdRol);
        $sql->bindValue(2, $IdRol);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_Rol_id($IdRol){
        $conectar = parent::ConexionBdPracticante();

        $sql = "SELECT * FROM tRoles WHERE IdRol = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdRol);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function insert($NombreRol, $UserUpdate){
        $conectar = parent::ConexionBdPracticante();

        $sql = "INSERT INTO tRoles (NombreRol, Estado) VALUES (?, ?)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $NombreRol);
        $sql->bindValue(2, 1);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function update($IdRol, $NombreRol, $Estado, $UserUpdate){
        $conectar = parent::ConexionBdPracticante();

        $sql = "UPDATE tRoles SET NombreRol = ?, Estado = ?, UserUpdate = ? WHERE IdRol = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $NombreRol);
        $sql->bindValue(2, $Estado);
        $sql->bindValue(3, $UserUpdate);
        $sql->bindValue(4, $IdRol);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function delete($IdRol){
        $conectar = parent::ConexionBdPracticante();

        $sql = "UPDATE tRoles SET Estado = '0' WHERE IdRol = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdRol);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function activar($IdRol){
        $conectar = parent::ConexionBdPracticante();

        $sql = "UPDATE tRoles SET Estado = '1' WHERE IdRol = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdRol);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

}
?>