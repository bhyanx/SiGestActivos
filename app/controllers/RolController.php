<?php
session_start();
require_once '../config/configuracion.php';
require_once '../models/Roles.php';

$objetoRol = new Roles();
// $CodEmpleado = $_SESSION["CodEmpleado"];;
// $UserUpdate = $_SESSION["UserUpdate"];

$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

switch ($action) {
    case "combo":
        $datos = $objetoRol->listarTodo();
        if (is_array($datos) and count($datos) > 0) {
            $html = "<option value=''>Seleccione</option>";
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['CodRol'] . "'>" . $row['NombreRol'] . "</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>Selecione</option>";
        }
        break;

    case 'ListarRoles':
        try {
            $data = $objetoRol->listarTodo();
            error_log("ListarRoles: " . json_encode($data), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode($data ?: []);
        } catch (\Throwable $th) {
            error_log("Error ListarRoles: " . $th->getMessage(), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode(['status' => false, 'message' => 'Error al listar roles: ' . $th->getMessage()]);
        }
        break;

    case 'ListarPermisosRoles':
        try {
            $idRol = $_POST['IdRol'] ?? null;
            if (!$idRol) {
                echo json_encode(['status' => false, 'message' => 'ID de rol requerido']);
                break;
            }
            
            $data = $objetoRol->listarPermisosRoles($idRol);
            echo json_encode($data ?: []);
        } catch (\Throwable $th) {
            error_log("Error ListarPermisosRoles: " . $th->getMessage(), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode(['status' => false, 'message' => 'Error al listar permisos: ' . $th->getMessage()]);
        }
        break;

    case 'DesactivarRol':
        try {
            $idRol = $_POST['IdRol'] ?? null;
            if (!$idRol) {
                echo json_encode(['status' => false, 'message' => 'ID de rol requerido']);
                break;
            }
            
            $data = [
                'Estado' => 0,
                'UserUpdate' => $_SESSION['CodEmpleado'] ?? 'admin'
            ];
            
            $resultado = $objetoRol->desactivar($idRol, $data);
            if ($resultado > 0) {
                echo json_encode(['status' => true, 'message' => 'Rol desactivado correctamente']);
            } else {
                echo json_encode(['status' => false, 'message' => 'No se pudo desactivar el rol']);
            }
        } catch (\Throwable $th) {
            error_log("Error DesactivarRol: " . $th->getMessage(), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode(['status' => false, 'message' => 'Error al desactivar rol: ' . $th->getMessage()]);
        }
        break;

    case 'ActivarRol':
        try {
            $idRol = $_POST['IdRol'] ?? null;
            if (!$idRol) {
                echo json_encode(['status' => false, 'message' => 'ID de rol requerido']);
                break;
            }
            
            $data = [
                'UserUpdate' => $_SESSION['CodEmpleado'] ?? 'admin'
            ];
            
            $resultado = $objetoRol->activar($idRol, $data);
            if ($resultado > 0) {
                echo json_encode(['status' => true, 'message' => 'Rol activado correctamente']);
            } else {
                echo json_encode(['status' => false, 'message' => 'No se pudo activar el rol']);
            }
        } catch (\Throwable $th) {
            error_log("Error ActivarRol: " . $th->getMessage(), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode(['status' => false, 'message' => 'Error al activar rol: ' . $th->getMessage()]);
        }
        break;

    case 'CambiarEstadoPermiso':
        try {
            $idRol = $_POST['IdRol'] ?? null;
            $codPermiso = $_POST['IdPermiso'] ?? null; // Mantenemos el nombre del parámetro por compatibilidad
            $nuevoEstado = $_POST['NuevoEstado'] ?? null;
            
            if (!$idRol || !$codPermiso || $nuevoEstado === null) {
                echo json_encode(['status' => false, 'message' => 'Parámetros requeridos: IdRol, IdPermiso, NuevoEstado']);
                break;
            }
            
            $resultado = $objetoRol->cambiarEstadoPermiso($idRol, $codPermiso, $nuevoEstado);
            if ($resultado > 0) {
                echo json_encode(['status' => true, 'message' => 'Estado del permiso actualizado correctamente']);
            } else {
                echo json_encode(['status' => false, 'message' => 'No se pudo actualizar el estado del permiso']);
            }
        } catch (\Throwable $th) {
            error_log("Error CambiarEstadoPermiso: " . $th->getMessage(), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode(['status' => false, 'message' => 'Error al cambiar estado del permiso: ' . $th->getMessage()]);
        }
        break;

    case 'GuardarCambiosPermisos':
        try {
            $cambios = $_POST['cambios'] ?? null;
            
            if (!$cambios) {
                echo json_encode(['status' => false, 'message' => 'No se recibieron cambios para guardar']);
                break;
            }
            
            $cambiosArray = json_decode($cambios, true);
            if (!is_array($cambiosArray)) {
                echo json_encode(['status' => false, 'message' => 'Formato de cambios inválido']);
                break;
            }
            
            $resultados = [];
            $exitosos = 0;
            $fallidos = 0;
            
            foreach ($cambiosArray as $cambio) {
                try {
                    $resultado = $objetoRol->cambiarEstadoPermiso(
                        $cambio['IdRol'], 
                        $cambio['CodPermiso'], 
                        $cambio['NuevoEstado']
                    );
                    
                    if ($resultado > 0) {
                        $exitosos++;
                        $resultados[] = [
                            'CodPermiso' => $cambio['CodPermiso'],
                            'status' => true,
                            'message' => 'Actualizado correctamente'
                        ];
                    } else {
                        $fallidos++;
                        $resultados[] = [
                            'CodPermiso' => $cambio['CodPermiso'],
                            'status' => false,
                            'message' => 'No se pudo actualizar'
                        ];
                    }
                } catch (\Exception $e) {
                    $fallidos++;
                    $resultados[] = [
                        'CodPermiso' => $cambio['CodPermiso'],
                        'status' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ];
                }
            }
            
            if ($fallidos === 0) {
                echo json_encode([
                    'status' => true, 
                    'message' => "Se guardaron {$exitosos} cambio(s) correctamente",
                    'resultados' => $resultados
                ]);
            } else {
                echo json_encode([
                    'status' => false, 
                    'message' => "Se guardaron {$exitosos} cambio(s), {$fallidos} fallaron",
                    'resultados' => $resultados
                ]);
            }
            
        } catch (\Throwable $th) {
            error_log("Error GuardarCambiosPermisos: " . $th->getMessage(), 3, __DIR__ . '/../../logs/acciones.log');
            echo json_encode(['status' => false, 'message' => 'Error al guardar cambios: ' . $th->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Acción no válida.']);
        break;
}
