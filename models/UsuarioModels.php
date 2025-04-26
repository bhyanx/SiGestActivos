<?php

class UsuarioModels extends Conectar {

    //DECLARACION DE VARIABLES GLOBALES

    // ? VARIABLE PARA MI TABLA PRINCIPAL DEL MODELO USUARIOS
    private $table = "tUsuarios";

    // ? VARIABLE PARA MI VISTAS PRINCIPALES DEL MODELO USUARIOS
    private $viewLogin = "vAccesoLogin";
    private $viewMenuGrupo = "vMenuGrupo";
    private $viewMenuRol = "vMenuRol";

    // TODO: FUNCIONES ADMINISTRATIVAS PARA ACCESOS AL SISTEMA
    //FUNCION PARA VERIFICAR LOS DATOS ENVIADOS DESDE EL LOGIN
    public function get_login($codUsuario, $contrasena){
        $conectar = parent::ConexionBdPracticante();
        $sql = "SELECT * FROM " . $this->viewLogin . " WHERE codUsuario = ? AND contrasena = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codUsuario);
        $sql->bindValue(2, $contrasena);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    //FUNCION PARA CREAR UN NUEVO USUARIO EN EL SISTEMA
    public function insert_usuario($codUsuario,$nombre, $contrasena,$idRol, $userMod){
        $conectar = parent::ConexionBdPracticante();
        $sql = "INSERT INTO " . $this->table . " (codUsuario, nombre, contrasena, idRol, estado, userMod, fechaRegistro) VALUES (?,?,?,?,?,?,GETDATE())";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codUsuario);
        $sql->bindValue(2, $nombre);
        $sql->bindValue(3, $contrasena);
        $sql->bindValue(4, $idRol);
        $sql->bindValue(5, 1);
        $sql->bindValue(6, $userMod);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    //FUNCION PARA QUE EL USUARIO PUEDA MODIFICAR SU CONTRASEÑA
    public function update_login_usuario($codUsuario, $contrasena, $userMod){
        $conectar = parent::ConexionBdPracticante();
        $sql = "UPDATE " . $this->table . " SET contrasena = ?, userMod = ?, fechaMod = GETDATE() WHERE codUsuario = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $contrasena);
        $sql->bindValue(2, $userMod);
        $sql->bindValue(3, $codUsuario);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    //FUNCION PARA QUE EL USUARIO PUEDA MODIFICAR SU NOMBRE
    public function update_usuario($codUsuario, $nombre, $userMod){
        $conectar = parent::ConexionBdPracticante();
        $sql = "UPDATE " . $this->table . " SET nombre = ?, userMod, userMod = ?, fechaMod = GETDATE() WHERE codUsuario = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $nombre);
        $sql->bindValue(2, $userMod);
        $sql->bindValue(3, $codUsuario);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    //FUNCION PARA ELIMINAR UN USUARIO DEL SISTEMA CAMBIANDO SU ESTADO
    public function delete_usuario($codUsuario, $estado, $userMod){
        $conectar = parent::ConexionBdPracticante();
        $sql = "UPDATE " . $this->table . " SET estado = '0', userMod = ?, fechaMod = GETDATE() WHERE codUsuario = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $userMod);
        $sql->bindValue(2, $codUsuario);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    //FUNCION PARA VOLVER A ACTIVAR UN USUARIO DEL SISTEMA CAMBIANDO SU ESTADO
    public function activar_usuario($codUsuario, $userMod){
        $conectar = parent::ConexionBdPracticante();
        $sql = "UPDATE " . $this->table . " SET estado = '1', userMod = ?, fechaMod = GETDATE() WHERE codUsuario = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $userMod);
        $sql->bindValue(2, $codUsuario);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    //FUNCION PARA ACTUALIZAR LA ULTIMA SESION DE UN USUARIO EN EL SISTEMA
    public function update_last_session($codUsuario, $UrlUltimaSession){
        $conectar = parent::ConexionBdPracticante();
        $sql = "UPDATE " . $this->table . " SET UrlUltimaSession = ? WHERE codUsuario = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $UrlUltimaSession);
        $sql->bindValue(2, $codUsuario);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    //FUNCION PARA ACTUALIZAR EL ROL DE UN USUARIO EN EL SISTEMA
    public function update_rol_usuario($codUsuario, $idRol){
        $conectar = parent::ConexionBdPracticante();
        $sql = "UPDATE " . $this->table . " SET idRol = ?
        WHERE codUsuario = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $idRol);
        $sql->bindValue(2, $codUsuario);
        $sql->execute();
        return $resultado=$sql->rowCount();
    }

    //FUNCION PARA LLAMAR EL MENU GRUPO
    public function get_menu_grupo($IdRol){
        $conectar = parent::ConexionBdPracticante();
        $sql = "SELECT * FROM " . $this->viewMenuGrupo . " WHERE idRol = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdRol);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    //FUNCION PARA LLAMAR EL MENU DE ROL
    public function get_menu_rol($idRol){
        $conectar = parent::ConexionBdPracticante();
        $sql = "SELECT * FROM " . $this->viewMenuRol . " WHERE IdRol = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $idRol);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    // TODO: FIN FUNCIONES ADMINISTRATIVAS PARA ACCESOS AL SISTEMA

    // *? FUNCIONES DE CONSULTAS DE DATOS DE USUARIOS

    //FUNCION PARA OBTENER TODOS LOS USUARIOS
    public function get_usuarios(){
        $conectar = parent::ConexionBdPracticante();
        $sql = "SELECT * FROM " . $this->table . " WHERE estado = 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    //FUNCION PARA OBTENER UN USUARIO ESPEFICO POR SU CODIGO(DNI)
    public function get_usuarios_id($codUsuario){
        $conectar = parent::ConexionBdPracticante();
        $sql = "SELECT * FROM " . $this->table . "WHERE codUsuario = ? AND estado = 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchObject();
    }





}

?>