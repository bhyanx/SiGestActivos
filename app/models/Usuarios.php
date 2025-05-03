<?php

class Usuarios {
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    public function login($codUsuario, $ClaveAcceso){
        try{
            $stmt = $this->db->prepare('SELECT * FROM tUsuarios WHERE codUsuario = ?');
            $stmt->execute([$codUsuario]);
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($usuario && password_verify($ClaveAcceso, $usuario[$ClaveAcceso])){
                return $usuario;
            }
            return false;
        }catch(\PDOException $e){
            error_log("Error in Usuarios::login: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function registrar($data){
        try{
            $stmt = $this->db->prepare('INSERT INTO tUsuarios(CodUsuario, CodEmpleado, idRol, ClaveAcceso, UrlUltimaSession, userUpdate, fechaUpdate) VALUES(?,?,?,?,?,GETDATE())');
            $stmt->execute([$data['CodUsuario'], $data['CodEmpleado'], $data['IdRol'],  $data['ClaveAcceso'], $data['UrlUltimaSession'], $data['userUpdate'], $data['fechaUpdate']]);
            return $this->db->lastInsertId();
        } catch(\PDOException $e){
            error_log("Error in Usuarios::registrar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}

?>