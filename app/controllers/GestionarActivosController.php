<?php
session_start();
require_once '../config/configuracion.php';
require_once '../models/GestionarActivos.php';
require_once '../models/Combos.php';

$activos = new GestionarActivos();
$combo = new Combos();

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
                echo json_encode(array('status' => true, 'message' => 'Activo registrado con Exito.'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
                error_log("Error Registrar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode(['status' => false, 'message' => 'Error al registrar activo: ' . $e->getMessage()]);
            }
        }
        break;

    case 'Actualizar':

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Normaliza el idActivo para que siempre llegue como int y con el nombre correcto
                $data['IdActivo'] = isset($_POST['IdActivo']) ? (int)$_POST['idActivo'] : null;
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

    case 'obtenerInfoActivo':
        try {
            $idActivo = $_POST['idActivo'];
            $info = $activos->obtenerInfoActivo($idActivo); // Este método debe existir en tu modelo
            echo json_encode([
                'status' => true,
                'data' => $info
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
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
                return $item['idActivo'] == $_POST['idActivo'];
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

            // Documentos de ingreso al almacén
            $stmt = $db->query("SELECT idDocIngAlmacen AS IdDocIngresoAlm FROM vListadoDeArticulosPorDocIngresoAlmacen GROUP BY idDocIngAlmacen");
            $combos['docIngresoAlm'] = '<option value="">Seleccione</option>';
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $combos['docIngresoAlm'] .= "<option value='{$row['IdDocIngresoAlm']}'>{$row['IdDocIngresoAlm']}</option>";
            }

            // Estados
            $estados = $combo->comboEstadoActivo();
            $combos['estados'] = '<option value="">Seleccione</option>';
            foreach ($estados as $row) {
                $combos['estados'] .= "<option value='{$row['idEstadoActivo']}'>{$row['nombre']}</option>";
            }

            // Proveedores
            $proveedores = $combo->comboProveedor();
            $combos['proveedores'] = '<option value="">Seleccione</option>';
            foreach ($proveedores as $row) {
                $combos['proveedores'] .= "<option value='{$row['Documento']}'>{$row['RazonSocial']}</option>";
            }

            // Sucursales
            $sucursales = $combo->comboSucursal();
            $combos['sucursales'] = '<option value="">Seleccione</option>';
            foreach ($sucursales as $row) {
                $combos['sucursales'] .= "<option value='{$row['cod_UnidadNeg']}'>{$row['nombre']}</option>";
            }

            // Ambientes
            $ambientes = $combo->comboAmbiente();
            $combos['ambientes'] = '<option value="">Seleccione</option>';
            foreach ($ambientes as $row) {
                $combos['ambientes'] .= "<option value='{$row['idAmbiente']}'>{$row['nombre']}</option>";
            }

            // Categorías
            $categorias = $combo->comboCategoria();
            $combos['categorias'] = '<option value="">Seleccione</option>';
            foreach ($categorias as $row) {
                $combos['categorias'] .= "<option value='{$row['IdCategoria']}'>{$row['Nombre']}</option>";
            }

            // ANTERIOR MANEJO DE COMBOS
            // $stmt = $db->query("SELECT IdAmbiente, Nombre FROM tAmbiente ORDER BY Nombre");
            // $combos['ambientes'] = '<option value="">Seleccione</option>';
            // foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            //     $combos['ambientes'] .= "<option value='{$row['IdAmbiente']}'>{$row['Nombre']}</option>";
            // }

            // $stmt = $db->query("SELECT IdEstadoActivo, Nombre FROM tEstadoActivo ORDER BY Nombre");
            // $combos['estados'] = '<option value="">Seleccione</option>';
            // foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            //     $combos['estados'] .= "<option value='{$row['IdEstadoActivo']}'>{$row['Nombre']}</option>";
            // }

            // $stmt = $db->query("SELECT IdCategoria, Nombre FROM tCategoriasActivo ORDER BY Nombre");
            // $combos['categorias'] = '<option value="">Seleccione</option>';
            // foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            //     $combos['categorias'] .= "<option value='{$row['IdCategoria']}'>{$row['Nombre']}</option>";
            // }

            // $stmt = $db->query("SELECT IdProveedor, Nombre FROM tProveedor ORDER BY Nombre");
            // $combos['proveedores'] = '<option value="">Seleccione</option>';
            // foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            //     $combos['proveedores'] .= "<option value='{$row['IdProveedor']}'>{$row['Nombre']}</option>";
            // }


            error_log("Combos generados: " . print_r($combos, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode(['status' => true, 'data' => $combos, 'message' => 'Combos cargados correctamente.']);
        } catch (Exception $e) {
            error_log("Error Combos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar combos: ' . $e->getMessage()]);
        }
        break;

    //Case para el modal y agregar 
    case 'articulos_por_doc':
        try {
            $db = (new Conectar())->ConexionBdPracticante(); // Usar bdActivos
            $IdDocIngresoAlm = $_POST['IdDocIngresoAlm'] ?? null;
            if (!$IdDocIngresoAlm) {
                throw new Exception("IdDocIngresoAlm no proporcionado.");
            }

            $stmt = $db->prepare("
            SELECT 
        ing.idDocIngAlmacen AS IdDocIngresoAlm,
        ing.idarticulo AS IdArticulo, -- <-- Cambia aquí el alias
        a.Descripcion_articulo AS Nombre,
        a.DescripcionMarca AS Marca,
        ing.Cod_Empresa AS IdEmpresa,
        ing.cod_UnidadNeg AS IdUnidadNegocio,
        ing.Nombre_local AS NombreLocal
    FROM vListadoDeArticulosPorDocIngresoAlmacen ing
    INNER JOIN vArticulos a ON ing.IdArticulo = a.IdArticulo
    WHERE ing.idDocIngAlmacen = ?
    ORDER BY a.Descripcion_articulo;
        ");
            $stmt->execute([$IdDocIngresoAlm]);
            $articulos = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $articulos[] = [
                    'IdArticulo' => $row['IdArticulo'],
                    'Nombre'     => $row['Nombre'],
                    'Marca'      => $row['Marca'] ?? '',
                    'IdEmpresa'  => $row['IdEmpresa'] ?? '',
                    'IdUnidadNegocio' => $row['IdUnidadNegocio'],
                    'NombreLocal' => $row['NombreLocal'],
                ];
            }

            error_log("Artículos por doc: IdDocIngresoAlm=$IdDocIngresoAlm, Resultados=" . json_encode($articulos), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode(['status' => true, 'data' => $articulos, 'message' => 'Artículos cargados correctamente.']);
        } catch (Exception $e) {
            error_log("Error Artículos por doc: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar artículos: ' . $e->getMessage()]);
        }
        break;
    // ...existing code...


    // Listar activos para el modal de movimientos

    case 'ListarParaMovimiento':
        try {
            $resultados = $activos->consultarActivos([]);
            // Mapeo flexible para los nombres de campos
            $data = array_map(function ($row) {
                return [
                    'IdActivo'  => $row['IdActivo'] ?? $row['idActivo'] ?? null,
                    'Codigo'    => $row['Codigo'] ?? $row['CodigoActivo'] ?? null,
                    'Nombre'    => $row['Nombre'] ?? $row['NombreArticulo'] ?? null,
                    'Marca'     => $row['Marca'] ?? $row['MarcaArticulo'] ?? null,
                    'Sucursal'  => $row['Sucursal'] ?? null,
                    'Ambiente'  => $row['Ambiente'] ?? null
                ];
            }, $resultados ?: []);
            echo json_encode(['data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['data' => [], 'error' => $e->getMessage()]);
        }
        break;
    default:
        echo json_encode(['status' => false, 'message' => 'Acción no válida.']);
        break;
}
