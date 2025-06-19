<?php

class Usuarios
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    public function login($CodUsuario, $ClaveAcceso)
    {
        try {
            $sql = "SELECT * FROM vAccesoLogin WHERE CodUsuario = ? AND ClaveAcceso = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $CodUsuario);
            $stmt->bindParam(2, $ClaveAcceso);
            $stmt->execute();
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Debug - Verificar datos retornados
            error_log("Datos retornados por login: " . print_r($resultado, true));

            if (empty($resultado)) {
                error_log("No se encontraron resultados para el usuario: " . $CodUsuario);
                return false;
            }

            // Verificar campos específicos
            $row = $resultado[0];
            error_log("NombreTrabajador: " . ($row['NombreTrabajador'] ?? 'no definido'));
            error_log("PrimerNombre: " . ($row['PrimerNombre'] ?? 'no definido'));
            error_log("ApellidoPaterno: " . ($row['ApellidoPaterno'] ?? 'no definido'));

            return $resultado;
        } catch (\PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            throw $e;
        }
    }

    public function listarTodo()
    {
        try {
            $stmt = $this->db->query("SELECT u.CodUsuario, e.PrimerNombre + ' ' + e.SegundoNombre AS Nombres,
	   e.ApellidoPaterno + ' ' +e.ApellidoMaterno AS Apellidos, r.NombreRol, u.ClaveAcceso, Activo
FROM tUsuarios u
INNER JOIN vEmpleados e ON u.CodUsuario = e.codTrabajador
LEFT JOIN tRoles r ON u.IdRol = r.IdRol");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in Usuarios::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function obtenerNombreEmpresa($cod_empresa)
    {
        $sql = "SELECT Razon_empresa FROM vEmpresas WHERE cod_empresa = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cod_empresa]);
        $empresa = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $empresa['Razon_empresa'] ?? '';
    }

    public function obtenerNombreUnidadNegocio($cod_UnidadNeg)
    {
        $sql = "SELECT Nombre_local FROM vUnidadesdeNegocio WHERE cod_UnidadNeg = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cod_UnidadNeg]);
        $unidad = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $unidad['Nombre_local'] ?? '';
    }

    public function listarPorCodUsuario($IdCodUsuario)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM tUsuarios WHERE CodUsuario = ?');
            $stmt->execute([$IdCodUsuario]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
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
    public function leerMenuGrupo($idRol)
    {
        try {
            $stmt = $this->db->prepare("SELECT DISTINCT m.MenuGrupo, m.MenuGrupoIcono 
                                        FROM tmenu m
                                        INNER JOIN tpermisos p ON m.CodMenu = p.CodMenu
                                        WHERE p.IdRol = ? AND m.Estado = 1 AND p.Permiso = 1
                                        ORDER BY m.MenuGrupo");
            $stmt->execute([$idRol]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in leerMenuGrupo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }


    //* FUNCION PARA PODER OBTENER EL ID DEL ROL DEL USUARIO QUE SE ENCUENTRA LOGUEADO
    public function leerMenuRol($idRol)
    {
        try {
            $stmt = $this->db->prepare("SELECT p.CodPermiso, p.CodMenu, p.IdRol, p.Permiso, 
                                       m.NombreMenu, m.MenuRuta, m.MenuIdentificador, 
                                       m.MenuIcono, m.MenuGrupo, m.MenuGrupoIcono, m.Estado as EstadoMenu, 
                                       r.NombreRol 
                                       FROM tpermisos p
                                       INNER JOIN tmenu m ON p.CodMenu = m.CodMenu
                                       INNER JOIN troles r ON p.IdRol = r.IdRol
                                       WHERE p.IdRol = ? AND m.Estado = 1 AND p.Permiso = 1
                                       ORDER BY m.MenuGrupo, m.NombreMenu");
            $stmt->execute([$idRol]);
            $menus = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Procesar las rutas
            foreach ($menus as &$menu) {
                // Eliminar el '../' inicial y cualquier './' o '/' duplicado del inicio de MenuRuta
                $cleaned_ruta = ltrim($menu['MenuRuta'], './');
                // Prepend the full public path including app/views/
                // Esto asume que MenuRuta en la BD es relativa a 'app/views/'
                $menu['MenuRuta'] = Conectar::ruta() . 'app/views/' . $cleaned_ruta;
            }

            return $menus;
        } catch (\PDOException $e) {
            error_log("Error in leerMenuRol: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }
}
