<?php

require_once '../config/configuracion.php';

class Ambientes
{

    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    //* LISTAR TODO 
    public function listarTodo($cod_empresa = null, $cod_UnidadNeg = null)
    {
        try {
            $sql = 'SELECT a.*, s.Nombre_local as NombreSucursal 
                    FROM vAmbientes a 
                    INNER JOIN vUnidadesdeNegocio s ON a.cod_UnidadNeg = s.cod_UnidadNeg 
                    WHERE a.estado = 1';
            $params = [];
            
            if ($cod_empresa !== null) {
                $sql .= ' AND s.Cod_Empresa = ?';
                $params[] = $cod_empresa;
            }
            
            if ($cod_UnidadNeg !== null) {
                $sql .= ' AND a.cod_UnidadNeg = ?';
                $params[] = $cod_UnidadNeg;
            }
            
            $sql .= ' ORDER BY a.nombre';
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in Ambientes::listarTodo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* LISTAR POR ID DE SUCURSAL
    public function listarAmbienteSucursal($idSucursal)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM tAmbiente WHERE idSucursal = ? ORDER BY nombre');
            $stmt->execute([$idSucursal]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in Ambientes::listarAmbienteSucursal: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //? FUNCIONES PARA REALIZAR EL RESTO DEL CRUD PARA AMBIENTES

    //* CREAR AMBIENTE

    public function crear($data){
        try{
            $stmt = $this->db->prepare('INSERT INTO tAmbiente (nombre, descripcion, idSucursal, estado, fechaRegistro, fechaMod, userMod) VALUES (?,?,?,?, GETDATE(), GETDATE(), ?)');
            $stmt->execute([$data['nombre'], $data['descripcion'], $data['idSucursal'], $data['estado'], $data['userMod']]);
            return $this->db->lastInsertId();
        }catch(\PDOException $e){
            error_log("Error in Ambientes::crear: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FIN CREAR AMBIENTE

    //* ACTUALIZAR AMBIENTE

    public function actualizar($idAmbiente, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tAmbiente SET nombre = ?, descripcion = ?, idSucursal = ?, estado = ?, fechaMod = GETDATE(), userMod = ? WHERE idAmbiente = ?');
            $stmt->execute([$data['nombre'], $data['descripcion'], $data['idSucursal'], $data['estado'], $data['userMod'], $idAmbiente]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in Ambientes::actualizar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function desactivar($idAmbiente, $data){
        try{
            $stmt = $this->db->prepare('UPDATE tAmbiente SET estado = ?, fechaMod = GETDATE(), userMod = ? WHERE idAmbiente = ?');
            $stmt->execute([$data['estado'], $data['userMod'], $idAmbiente]);
            return $stmt->rowCount();
        }catch(\PDOException $e){
            error_log("Error in Ambientes::desactivar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    //* FIN ACTUALIZAR AMBIENTE
}
