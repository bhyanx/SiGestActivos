<?php

class GestionarMovimientos
{

    private $db;
    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
        //$this->db = (new Conectar())->ConexionBdPruebas();
    }

    //! REGISTRAR MOVIMIENTO CON PROCEDIMIENTOS ALMACENADOS (escritura diferente)

    public function crearMovimiento($data)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO tMovimientos (FechaMovimiento, idTipoMovimiento, idAutorizador, idSucursalOrigen, idSucursalDestino, observaciones) VALUES (GETDATE(), ?, ?, ?, ?, ?)');

            $stmt->bindParam(1, $data['idTipoMovimiento'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['idAutorizador'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['idSucursalOrigen'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['idSucursalDestino'], \PDO::PARAM_INT);
            $stmt->bindParam(5, $data['observaciones'], \PDO::PARAM_STR);
            $stmt->execute();
            return $this->db->lastInsertId(); // Devuelve el ID del último movimiento insertado
        } catch (\PDOException $e) {
            error_log("Error in registrarMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function crearDetalleMovimiento($data)
    {
        try {
            $stmt = $this->db->prepare('EXEC sp_RegistrarDetalleMovimiento @IdMovimiento = ?, @IdActivo = ?, @IdTipo_Movimiento = ?, @IdSucursal_Nueva = ?, @IdAmbiente_Nueva = ?, @IdResponsable_Nueva = ?, @IdActivoPadre_Nuevo = ?, @IdAutorizador = ?');

            $stmt->bindParam(1, $data['IdMovimiento'], \PDO::PARAM_INT);
            $stmt->bindParam(2, $data['IdActivo'], \PDO::PARAM_INT);
            $stmt->bindParam(3, $data['IdTipo_Movimiento'], \PDO::PARAM_INT);
            $stmt->bindParam(4, $data['IdSucursal_Nueva'], \PDO::PARAM_INT);
            $stmt->bindParam(5, $data['IdAmbiente_Nueva'], \PDO::PARAM_INT);
            $stmt->bindParam(6, $data['IdResponsable_Nueva'], \PDO::PARAM_INT);
            $stmt->bindParam(7, $data['IdActivoPadre_Nuevo'], \PDO::PARAM_INT);
            $stmt->bindParam(8, $data['IdAutorizador'], \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error in crearDetalleMovimiento: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    public function listarDetalleMovimientos($filtros = [])
    {
        try {
            $sql = "SELECT * FROM vDetalleMovimiento WHERE 1=1";
            $params = [];
            if (!empty($filtros['tipo'])) {
                $sql .= " AND TipoMovimiento = ?";
                $params[] = $filtros['tipo'];
            }
            if (!empty($filtros['sucursal_origen'])) {
                $sql .= " AND SucursalAnterior = ?";
                $params[] = $filtros['sucursal_origen'];
            }
            if (!empty($filtros['sucursal_destino'])) {
                $sql .= " AND SucursalNueva = ?";
                $params[] = $filtros['sucursal_destino'];
            }
            if (!empty($filtros['fecha'])) {
                $sql .= " AND CONVERT(date, FechaMovimiento) = ?";
                $params[] = $filtros['fecha'];
            }
            if (!empty($filtros['ambiente'])) {
                $sql .= " AND (AmbienteAnterior = ? OR AmbienteNuevo = ?)";
                $params[] = $filtros['ambiente'];
                $params[] = $filtros['ambiente'];
            }
            $sql .= " ORDER BY FechaMovimiento DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in listarDetalleMovimientos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }


}
