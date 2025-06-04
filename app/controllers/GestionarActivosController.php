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
                // Obtener los datos del array de activos
                $activosArray = json_decode($_POST['activos'], true);
                if (!$activosArray) {
                    throw new Exception("No se recibieron datos de activos válidos");
                }

                $resultados = [];
                foreach ($activosArray as $activo) {
                    // Formatear fechas
                    $fechaFinGarantia = !empty($activo['FechaFinGarantia']) ? date('Y-m-d', strtotime($activo['FechaFinGarantia'])) : null;
                    $fechaAdquisicion = !empty($activo['FechaAdquisicion']) ? date('Y-m-d', strtotime($activo['FechaAdquisicion'])) : date('Y-m-d');

                    $data = [
                        'IdActivo' => null,
                        'IdDocIngresoAlm' => $activo['IdDocIngresoAlm'],
                        'IdArticulo' => $activo['IdArticulo'],
                        'Codigo' => null, // El SP generará el código automáticamente
                        'Serie' => $activo['Serie'],
                        'IdEstado' => $activo['IdEstado'],
                        'Garantia' => $activo['Garantia'] ?? 0,
                        'FechaFinGarantia' => $fechaFinGarantia,
                        'IdProveedor' => $activo['IdProveedor'] ?? null,
                        'Observaciones' => $activo['Observaciones'] ?? '',
                        'IdSucursal' => $activo['IdSucursal'],
                        'IdAmbiente' => $activo['IdAmbiente'],
                        'IdCategoria' => $activo['IdCategoria'],
                        'VidaUtil' => $activo['VidaUtil'] ?? 3,
                        'ValorAdquisicion' => $activo['ValorAdquisicion'] ?? 0,
                        'FechaAdquisicion' => $fechaAdquisicion,
                        'UserMod' => $_SESSION['CodEmpleado'],
                        'Accion' => 1 // 1 = Insertar
                    ];

                    $activos->registrarActivos($data);
                    $resultados[] = [
                        'status' => true,
                        'message' => 'Activo registrado correctamente'
                    ];
                }

                echo json_encode([
                    'status' => true,
                    'message' => 'Activos registrados con éxito.',
                    'data' => $resultados
                ]);
            } catch (Exception $e) {
                error_log("Error Registrar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al registrar activos: ' . $e->getMessage()
                ]);
            }
        }
        break;

    case 'RegistrarPrueba':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Obtener los datos del array de activos
                $activosArray = json_decode($_POST['activos'], true);
                if (!$activosArray) {
                    throw new Exception("No se recibieron datos de activos válidos");
                }

                $resultados = [];
                foreach ($activosArray as $activo) {
                    // Formatear fechas
                    $fechaFinGarantia = !empty($activo['FechaFinGarantia']) ? date('Y-m-d', strtotime($activo['FechaFinGarantia'])) : null;
                    $fechaAdquisicion = !empty($activo['FechaAdquisicion']) ? date('Y-m-d', strtotime($activo['FechaAdquisicion'])) : date('Y-m-d');

                    $data = [
                        'IdActivo' => null,
                        'IdDocIngresoAlm' => $activo['IdDocIngresoAlm'],
                        'IdArticulo' => $activo['IdArticulo'],
                        'Codigo' => null, // El SP generará el código automáticamente
                        'Serie' => $activo['Serie'],
                        'IdEstado' => $activo['IdEstado'],
                        'Garantia' => $activo['Garantia'] ?? 0,
                        'FechaFinGarantia' => $fechaFinGarantia,
                        'IdProveedor' => $activo['IdProveedor'] ?? null,
                        'Observaciones' => $activo['Observaciones'] ?? '',
                        'IdSucursal' => $_SESSION['IdSucursal'],
                        'IdAmbiente' => $activo['IdAmbiente'],
                        'IdCategoria' => $activo['IdCategoria'] ?? 2,
                        'VidaUtil' => $activo['VidaUtil'] ?? 3,
                        'ValorAdquisicion' => $activo['ValorAdquisicion'] ?? 0,
                        'FechaAdquisicion' => $fechaAdquisicion,
                        'UserMod' => $_SESSION['CodEmpleado'],
                        'Accion' => 1 // 1 = Insertar
                    ];

                    $activos->registrarActivosPrueba($data);
                    $resultados[] = [
                        'status' => true,
                        'message' => 'Activo registrado correctamente'
                    ];
                }

                echo json_encode([
                    'status' => true,
                    'message' => 'Activos registrados con éxito.',
                    'data' => $resultados
                ]);
            } catch (Exception $e) {
                error_log("Error Registrar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al registrar activos: ' . $e->getMessage()
                ]);
            }
        }
        break;

    // !COMENTADO POR DESUSO EN EL REGISTRO MANUAL DE ACTIVOS.
    // case 'RegistrarManual':
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         try {
    //             $data = [
    //                 'Nombre' => $_POST['nombre'],
    //                 'Descripcion' => $_POST['descripcion'],
    //                 'Serie' => $_POST['serie'],
    //                 'IdEstado' => $_POST['idEstado'],
    //                 'IdCategoria' => $_POST['idCategoria'],
    //                 'IdAmbiente' => $_POST['idAmbiente'],
    //                 'IdSucursal' => $_SESSION['cod_UnidadNeg'],
    //                 'Cantidad' => $_POST['cantidad'],
    //                 'AnioCompra' => $_POST['anioCompra'],
    //                 'UserMod' => $_SESSION['CodEmpleado'],
    //                 'Accion' => 1
    //             ];

    //             $activos->registrarActivosManual($data);
    //             echo json_encode([
    //                 'status' => true,
    //                 'message' => 'Activos registrados con éxito.'
    //             ]);
    //         } catch (Exception $e) {
    //             error_log("Error RegistrarManual: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
    //             echo json_encode([
    //                 'status' => false,
    //                 'message' => 'Error al registrar activos manualmente: ' . $e->getMessage()
    //             ]);
    //         }
    //     }
    //     break;

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

            $tipoMovimiento = $combo->comboTipoMovimiento();
            $combos['tipoMovimiento'] = '<option value="">Seleccione</option>';
            foreach ($tipoMovimiento as $row) {
                $combos['tipoMovimiento'] .= "<option value='{$row['idTipoMovimiento']}'>{$row['nombre']}</option>";
            }

            $sucursales = $combo->comboSucursal();
            $combos['sucursales'] = '<option value="">Seleccione</option>';
            foreach ($sucursales as $row) {
                $combos['sucursales'] .= "<option value='{$row['idSucursal']}'>{$row['nombre']}</option>";
            }

            $autorizador = $combo->comboAutorizador();
            $combos['autorizador'] = '<option value="">Seleccione</option>';
            foreach ($autorizador as $row) {
                $combos['autorizador'] .= "<option value='{$row['codTrabajador']}'>{$row['NombreTrabajador']}</option>";
            }

            $responsable = $combo->comboResponsable();
            $combos['responsable'] = '<option value="">Seleccione</option>';
            foreach ($responsable as $row) {
                $combos['responsable'] .= "<option value='{$row['codTrabajador']}'>{$row['NombreTrabajador']}</option>";
            }

            $categorias = $combo->comboCategoria();
            $combos['categorias'] = '<option value="">Seleccione</option>';
            foreach ($categorias as $row) {
                $combos['categorias'] .= "<option value='{$row['IdCategoria']}'>{$row['Nombre']}</option>";
            }

            $ambientes = $combo->comboAmbiente();
            $combos['ambientes'] = '<option value="">Seleccione</option>';
            foreach ($ambientes as $row) {
                $combos['ambientes'] .= "<option value='{$row['idAmbiente']}'>{$row['nombre']}</option>";
            }

            $estadoActivo = $combo->comboEstadoActivo();
            $combos['estado'] = '<option value="">Seleccione</option>';
            foreach ($estadoActivo as $row) {
                $combos['estado'] .= "<option value='{$row['idEstadoActivo']}'>{$row['nombre']}</option>";
            }

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
        ing.idarticulo AS IdArticulo, 
        a.Descripcion_articulo AS Nombre,
        a.DescripcionMarca AS Marca,
		e.Razon_empresa AS Empresa,
        ing.cod_UnidadNeg AS IdUnidadNegocio,
        ing.Nombre_local AS NombreLocal
    FROM vListadoDeArticulosPorDocIngresoAlmacen ing
    INNER JOIN vArticulos a ON ing.IdArticulo = a.IdArticulo
	LEFT JOIN vEmpresas e ON ing.Cod_Empresa = e.cod_empresa 
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
                    'Empresa'  => $row['Empresa'] ?? '',
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
