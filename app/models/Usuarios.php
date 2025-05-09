<?php

require_once(__DIR__ . "/../config/configuracion.php");

class Usuarios
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    public function login($codUsuario, $ClaveAcceso)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM tUsuarios WHERE CodUsuario = ? AND ClaveAcceso = ?');
            $stmt->execute([$codUsuario, $ClaveAcceso]);
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($usuario) {
                return [$usuario];
            }
            return false;
        } catch (\PDOException $e) {
            error_log("Error in Usuarios::login: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function listarTodo()
    {
        try {
            $stmt = $this->db->query('SELECT * FROM tUsuarios ORDER BY CodEmpleado');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in Usuarios::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function listarPorCodUsuario($IdCodUsuario){
        try {
            $stmt = $this->db->prepare('SELECT * FROM tUsuarios WHERE CodUsuario = ?');
            $stmt->execute([$IdCodUsuario]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in listarPorCodUsuario: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function registrar($data)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO tUsuarios(CodUsuario, CodEmpleado, idRol, ClaveAcceso, UrlUltimaSession, userUpdate, fechaUpdate) VALUES(?,?,?,?,?,GETDATE())');
            $stmt->execute([$data['CodUsuario'], $data['CodEmpleado'], $data['IdRol'],  $data['ClaveAcceso'], $data['UrlUltimaSession'], $data['userUpdate'], $data['fechaUpdate']]);
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Error in Usuarios::registrar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }


    //! GESTIONAR USUARIOS CON PROCEDIMIENTOS ALMACENADOS

    //* ACTUALIZAR ROL DE UN USUARIO USANDO sp_GestionUsuarios

    public function actualizarRol($data)
    {
        try {
            $stmt = $this->db->prepare('EXEC sp_GestionUsuarios @pAccion = ?, @CodUsuario = ?, @IdRol = ?, @UserMod = ?');
            $stmt->bindParam(1, $data['pAccion'], \PDO::PARAM_STR); // 'ACTUALIZAR'
            $stmt->bindParam(2, $data['CodUsuario'], \PDO::PARAM_STR);
            $stmt->bindParam(3, $data['IdRol'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['UserMod'], \PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in actualizarRol: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function gestionarPermiso($data)
    {
        try {
            $stmt = $this->db->prepare('EXEC sp_GestionPermisos @pAccion = ?, @CodPermiso = ?, @CodMenu = ?, @IdRol = ?, @Permiso = ?, @UserMod = ?');
            $stmt->bindParam(1, $data['pAccion'], \PDO::PARAM_STR); // 'INSERTAR', 'ACTUALIZAR', 'ELIMINAR', 'CONSULTAR'
            $stmt->bindParam(2, $data['CodPermiso'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['CodMenu'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['IdRol'], \PDO::PARAM_INT);
            $stmt->bindParam(5, $data['Permiso'], \PDO::PARAM_BOOL);
            $stmt->bindParam(6, $data['UserMod'], \PDO::PARAM_STR);
            $stmt->execute();
            if ($data['pAccion'] === 'CONSULTAR') {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            return true;
        } catch (\PDOException $e) {
            error_log("Error in gestionarPermiso: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function listarMenusPorUsuario($codUsuario)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM vMenusPorUsuario WHERE CodUsuario = ?');
            $stmt->execute([$codUsuario]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in listarMenusPorUsuario: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //! GESTIONAR SESSIONES DE USUARIOS

    //* ACTUALIZAR LA URL DE LA ULTIMA SESSION DEL USUARIO

    public function actualizarUrlUltimaSession($data)
    {
        try {
            $stmt = $this->db->prepare('UPDATE tUsuarios SET UrlUltimaSession = ?, fechaUpdate = GETDATE() WHERE CodUsuario = ?');
            $stmt->bindParam(1, $data['UrlUltimaSession'], \PDO::PARAM_STR);
            $stmt->bindParam(2, $data['CodUsuario'], \PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            error_log("Error in actualizarUrlUltimaSession: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FUNCION PARA PODER OBTENER EL 
    public function leerMenuGrupo($idRol){
        try{
            $stmt = $this->db->prepare("SELECT DISTINCT m.MenuGrupo, m.MenuGrupoIcono, p.Permiso 
                                        FROM tmenu m
                                        inner join tpermisos p on tmenu.CodMenu = tpermisos.CodMenu
                                        where tpermisos.Permiso = '1' and tpermisos.IdRol = 1 ");
            $stmt->bindParam(1, $idRol, \PDO::PARAM_INT); //$stmt->bindParam(1, $data['IdRol'], \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            error_log("Error in leerMenuGrupo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
    

    //* FUNCION PARA PODER OBTENER EL ID DEL ROL DEL USUARIO QUE SE ENCUENTRA LOGUEADO
    public function leerMenuRol($idRol)
    {
        try {
            $stmt = $this->db->prepare("SELECT p.CodPermiso, p.CodMenu, p.IdRol, p.Permiso, m.NombreMenu, m.MenuRuta, m.MenuIdentificador, 
                                        m.MenuIcono, m.MenuGrupo, m.MenuGrupoIcono, m.Estado as EstadoMenu, r.NombreRol 
                                        FROM tpermisos p
                                        inner join tmenu  m on p.CodMenu = m.CodMenu
                                        inner join troles r on p.IdRol = r.IdRol
                                        where p.idRol = ? and p.Permiso = '1' and m.Estado = '1'");
            $stmt->bindParam(1, $idRol, \PDO::PARAM_INT); //$stmt->bindParam(1, $data['IdRol'], \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in leerMenuRol: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
