<?php
session_start();
require_once '../config/configuracion.php';
require_once '../models/GestionarActivos.php';

$activos = new GestionarActivos();
$action = $_GET['action'] ?? $_POST['action'] ?? 'Consultar';

// Desactivar display_errors para evitar HTML en respuestas JSON
ini_set('display_errors', 0);

header('Content-Type: application/json');

switch ($action) {
    case 'Consultar':
        try {
            $filtros = [
                'pCodigo' => $_POST['pCodigo'] ?? null,
                'pIdSucursal' => $_POST['pIdSucursal'] ?? null,
                'pIdCategoria' => $_POST['pIdCategoria'] ?? null,
                'pIdEstado' => $_POST['pIdEstado'] ?? null
            ];
            $resultados = $activos->consultarActivos($filtros);
            error_log("Consultar resultados: " . print_r($resultados, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode($resultados ?: []);
        } catch (Exception $e) {
            error_log("Error Consultar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar activos: ' . $e->getMessage()]);
        }
        break;

    case 'Registrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'IdActivo' => null,
                    'IdDocIngresoAlm' => $_POST['IdDocIngresoAlm'],
                    'IdArticulo' => $_POST['IdArticulo'],
                    'Codigo' => $_POST['Codigo'],
                    'Serie' => $_POST['Serie'],
                    'IdEstado' => $_POST['IdEstado'],
                    'Garantia' => $_POST['Garantia'],
                    'FechaFinGarantia' => $_POST['FechaFinGarantia'],
                    'IdProveedor' => $_POST['IdProveedor'],
                    'Observaciones' => $_POST['Observaciones'],
                    'IdSucursal' => $_POST['IdSucursal'],
                    'IdAmbiente' => $_POST['IdAmbiente'],
                    'IdCategoria' => $_POST['IdCategoria'],
                    'VidaUtil' => $_POST['VidaUtil'],
                    'ValorAdquisicion' => $_POST['ValorAdquisicion'],
                    'FechaAdquisicion' => $_POST['FechaAdquisicion'],
                    'UserMod' => $_SESSION['CodEmpleado']
                ];
                $activos->registrarActivos($data);
                echo json_encode(['status' => true, 'message' => 'Activo registrado con éxito.']);
            } catch (Exception $e) {
                error_log("Error Registrar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode(['status' => false, 'message' => 'Error al registrar activo: ' . $e->getMessage()]);
            }
        }
        break;

    case 'Actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'IdActivo' => $_POST['IdActivo'],
                    'IdDocIngresoAlm' => $_POST['IdDocIngresoAlm'],
                    'IdArticulo' => $_POST['IdArticulo'],
                    'Codigo' => $_POST['Codigo'],
                    'Serie' => $_POST['Serie'],
                    'IdEstado' => $_POST['IdEstado'],
                    'Garantia' => $_POST['Garantia'],
                    'FechaFinGarantia' => $_POST['FechaFinGarantia'],
                    'IdProveedor' => $_POST['IdProveedor'],
                    'Observaciones' => $_POST['Observaciones'],
                    'IdSucursal' => $_POST['IdSucursal'],
                    'IdAmbiente' => $_POST['IdAmbiente'],
                    'IdCategoria' => $_POST['IdCategoria'],
                    'VidaUtil' => $_POST['VidaUtil'],
                    'ValorAdquisicion' => $_POST['ValorAdquisicion'],
                    'FechaAdquisicion' => $_POST['FechaAdquisicion'],
                    'UserMod' => $_SESSION['CodEmpleado']
                ];
                $activos->actualizarActivos($data);
                echo json_encode(['status' => true, 'message' => 'Activo actualizado con éxito.']);
            } catch (Exception $e) {
                error_log("Error Actualizar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode(['status' => false, 'message' => 'Error al actualizar activo: ' . $e->getMessage()]);
            }
        }
        break;

    case 'get_activo':
        try {
            $filtros = [
                'pCodigo' => null,
                'pIdSucursal' => null,
                'pIdCategoria' => null,
                'pIdEstado' => null
            ];
            $resultados = $activos->consultarActivos($filtros);
            $activo = array_filter($resultados, function ($item) {
                return $item['IdActivo'] == $_POST['IdActivo'];
            });
            $activo = array_values($activo)[0] ?? null;
            error_log("Get Activo: " . print_r($activo, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode(['status' => !!$activo, 'data' => $activo, 'message' => $activo ? 'Activo encontrado.' : 'Activo no encontrado.']);
        } catch (Exception $e) {
            error_log("Error Get Activo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al obtener activo: ' . $e->getMessage()]);
        }
        break;

    case 'combos':
        try {
            $db = (new Conectar())->ConexionBdPracticante(); // Usar bdActivos
            $combos = [];

            // Documentos de ingreso al almacén
            $stmt = $db->query("SELECT idDocIngAlmacen AS IdDocIngresoAlm FROM vListadoDeArticulosPorDocIngresoAlmacen GROUP BY idDocIngAlmacen");
            $combos['docIngresoAlm'] = '<option value="">Seleccione</option>';
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $combos['docIngresoAlm'] .= "<option value='{$row['IdDocIngresoAlm']}'></option>";
            }

            // Estados
            $stmt = $db->query("SELECT IdEstadoActivo, Nombre FROM tEstadoActivo ORDER BY Nombre");
            $combos['estados'] = '<option value="">Seleccione</option>';
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $combos['estados'] .= "<option value='{$row['IdEstadoActivo']}'>{$row['Nombre']}</option>";
            }

            // Proveedores
            $stmt = $db->query("SELECT IdProveedor, Nombre FROM tProveedor ORDER BY Nombre");
            $combos['proveedores'] = '<option value="">Seleccione</option>';
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $combos['proveedores'] .= "<option value='{$row['IdProveedor']}'>{$row['Nombre']}</option>";
            }

            // Sucursales
            $stmt = $db->query("SELECT cod_UnidadNeg, Nombre FROM tSucursales ORDER BY Nombre");
            $combos['sucursales'] = '<option value="">Seleccione</option>';
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $combos['sucursales'] .= "<option value='{$row['cod_UnidadNeg']}'>{$row['Nombre']}</option>";
            }

            // Ambientes
            $stmt = $db->query("SELECT IdAmbiente, Nombre FROM tAmbiente ORDER BY Nombre");
            $combos['ambientes'] = '<option value="">Seleccione</option>';
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $combos['ambientes'] .= "<option value='{$row['IdAmbiente']}'>{$row['Nombre']}</option>";
            }

            // Categorías
            $stmt = $db->query("SELECT IdCategoria, Nombre FROM tCategoriasActivo ORDER BY Nombre");
            $combos['categorias'] = '<option value="">Seleccione</option>';
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $combos['categorias'] .= "<option value='{$row['IdCategoria']}'>{$row['Nombre']}</option>";
            }

            error_log("Combos generados: " . print_r($combos, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode(['status' => true, 'data' => $combos, 'message' => 'Combos cargados correctamente.']);
        } catch (Exception $e) {
            error_log("Error Combos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar combos: ' . $e->getMessage()]);
        }
        break;

    case 'articulos_por_doc':
        try {
            $db = (new Conectar())->ConexionBdPracticante(); // Usar bdActivos
            $IdDocIngresoAlm = $_POST['IdDocIngresoAlm'] ?? null;
            if (!$IdDocIngresoAlm) {
                throw new Exception("IdDocIngresoAlm no proporcionado.");
            }

            $stmt = $db->prepare("
                SELECT ing.idDocIngAlmacen AS IdDocIngresoAlm, ing.IdArticulo, a.Descripcion_articulo AS Nombre
                FROM vListadoDeArticulosPorDocIngresoAlmacen ing
                INNER JOIN vArticulos a ON ing.IdArticulo = a.IdArticulo
                WHERE ing.idDocIngAlmacen = ?
                ORDER BY a.Descripcion_articulo
            ");
            $stmt->execute([$IdDocIngresoAlm]);
            $articulos = '<option value="">Seleccione</option>';
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $articulos .= "<option value='{$row['IdArticulo']}'>{$row['Nombre']}</option>";
            }

            error_log("Artículos por doc: IdDocIngresoAlm=$IdDocIngresoAlm, Resultados=" . substr($articulos, 0, 100), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode(['status' => true, 'data' => ['articulos' => $articulos], 'message' => 'Artículos cargados correctamente.']);
        } catch (Exception $e) {
            error_log("Error Artículos por doc: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar artículos: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Acción no válida.']);
        break;
}
?>