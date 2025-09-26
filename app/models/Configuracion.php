<?php
require_once(__DIR__ . '/../config/configuracion.php');

class Configuracion
{
    private $db;

    public function __construct()
    {
        $this->db = (new Conectar())->ConexionBdPracticante();
    }

    /**
     * Bloquear edición de un activo
     */
    public function bloquearEdicion($idActivo)
    {
        try {
            // Obtener el estado actual antes de cambiarlo
            $estadoActual = $this->obtenerEstadoEditable($idActivo);

            $stmt = $this->db->prepare("UPDATE tActivos SET esEditable = 0 WHERE idActivo = ?");
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                // Registrar en el log de auditoría
                $this->registrarEventoAuditoria($idActivo, $estadoActual, 0);
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error en bloquearEdicion: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    /**
     * Permitir edición de un activo
     */
    public function permitirEdicion($idActivo)
    {
        try {
            // Obtener el estado actual antes de cambiarlo
            $estadoActual = $this->obtenerEstadoEditable($idActivo);

            $stmt = $this->db->prepare("UPDATE tActivos SET esEditable = 1 WHERE idActivo = ?");
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                // Registrar en el log de auditoría
                $this->registrarEventoAuditoria($idActivo, $estadoActual, 1);
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error en permitirEdicion: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    /**
     * Obtener estado editable de un activo
     */
    public function obtenerEstadoEditable($idActivo)
    {
        try {
            $stmt = $this->db->prepare("SELECT esEditable FROM tActivos WHERE idActivo = ?");
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? (int)$result['esEditable'] : null;
        } catch (Exception $e) {
            error_log("Error en obtenerEstadoEditable: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    /**
     * Obtener historial de eventos de un activo
     */
    public function obtenerHistorialEventos($idActivo)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    fecha,
                    tabla,
                    campo,
                    valorAnterior,
                    valorNuevo,
                    usuario
                FROM tLogEventos
                WHERE idRegistro = ? AND tabla = 'tActivos'
                ORDER BY fecha DESC
            ");
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerHistorialEventos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            throw $e;
        }
    }

    /**
     * Registrar evento en la auditoría
     */
    private function registrarEventoAuditoria($idActivo, $valorAnterior, $valorNuevo)
    {
        try {
            $usuario = $_SESSION['CodEmpleado'] ?? 'Sistema';

            $stmt = $this->db->prepare("
                INSERT INTO tLogEventos (tabla, idRegistro, campo, valorAnterior, valorNuevo, fecha, usuario)
                VALUES ('tActivos', ?, 'esEditable', ?, ?, GETDATE(), ?)
            ");
            $stmt->bindParam(1, $idActivo, PDO::PARAM_INT);
            $stmt->bindParam(2, $valorAnterior, PDO::PARAM_STR);
            $stmt->bindParam(3, $valorNuevo, PDO::PARAM_STR);
            $stmt->bindParam(4, $usuario, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en registrarEventoAuditoria: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            // No lanzamos excepción aquí para no interrumpir el flujo principal
        }
    }
}