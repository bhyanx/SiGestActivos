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
                //'pCodigo' => $_POST['pCodigo'] ?? null,
                'pIdEmpresa' => $_SESSION['cod_empresa'] ?? null,
                'pIdSucursal' => $_SESSION['cod_UnidadNeg'] ?? null,
                //'pIdCategoria' => $_POST['pIdCategoria'] ?? null,
                //'pIdEstado' => $_POST['pIdEstado'] ?? null
            ];
            $resultados = $activos->consultarActivos($filtros);
            error_log("Consultar resultados: " . print_r($resultados, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode($resultados ?: []);
        } catch (Exception $e) {
            error_log("Error Consultar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar activos: ' . $e->getMessage()]);
        }
        break;

    case 'ConsultarActivos':
        try {
            $filtros = [
                'pCodigo' => $_POST['pCodigo'] ?? null,
                'pIdEmpresa' => $_SESSION['cod_empresa'] ?? null,
                'pIdSucursal' => $_SESSION['cod_UnidadNeg'] ?? null,
                'pIdCategoria' => $_POST['pIdCategoria'] ?? null,
                'pIdEstado' => $_POST['pIdEstado'] ?? null
            ];
            $resultados = $activos->consultarActivosModal($filtros);
            error_log("Consultar resultados: " . print_r($resultados, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode($resultados ?: []);
        } catch (Exception $e) {
            error_log("Error Consultar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar activos: ' . $e->getMessage()]);
        }
        break;

    case 'ConsultarActivosRelacionados':
        try {
            $filtros = [
                'pIdArticulo' => $_POST['IdArticulo'] ?? null,
                'pCodigo' => $_POST['pCodigo'] ?? null,
                'pIdEmpresa' => $_SESSION['cod_empresa'] ?? null,
                'pIdSucursal' => $_SESSION['cod_UnidadNeg'] ?? null,
                'pIdCategoria' => $_POST['pIdCategoria'] ?? null,
                'pIdEstado' => $_POST['pIdEstado'] ?? null
            ];
            $resultados = $activos->consultarActivosRelacionados($filtros);
            error_log("Consultar resultados: " . print_r($resultados, true), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode($resultados ?: []);
        } catch (Exception $e) {
            error_log("Error Consultar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al consultar activos: ' . $e->getMessage()]);
        }
        break;

    // case 'Consultar':
    //     try {
    //         $filtros = [
    //             'pCodigo' => $_POST['pCodigo'] ?? null,
    //             'pIdEmpresa' => $_SESSION['cod_empresa'] ?? null,
    //             'pIdSucursal' => $_SESSION['cod_UnidadNeg'] ?? null
    //         ];
    //         $resultados = $activos->consultarActivosCabecera($filtros);
    //         error_log("Consultar resultados: " . print_r($resultados, true), 3, __DIR__ . '/../../logs/debug.log');
    //         echo json_encode($resultados ?: []);
    //     } catch (Exception $e) {
    //         error_log("Error Consultar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
    //         echo json_encode(['status' => false, 'message' => 'Error al consultar activos: ' . $e->getMessage()]);
    //     }

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

    /*case 'RegistrarPruebaVenta':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {

                error_log("POST recibido en RegistrarPruebaVenta: " . print_r($_POST, true), 3, __DIR__ . '/../../logs/debug.log');


                $activosArray = json_decode($_POST['activos'], true);

                error_log("Array de activos decodificado: " . print_r($activosArray, true), 3, __DIR__ . '/../../logs/debug.log');

                if (!$activosArray) {
                    error_log("Error en json_decode: " . json_last_error_msg(), 3, __DIR__ . '/../../logs/errors.log');
                    throw new Exception("No se recibieron datos de activos válidos");
                }

                $resultados = [];
                foreach ($activosArray as $activo) {
                    
                    error_log("Procesando activo: " . print_r($activo, true), 3, __DIR__ . '/../../logs/debug.log');

                    // Formatear fechas
                    $fechaFinGarantia = !empty($activo['FechaFinGarantia']) ? date('Y-m-d', strtotime($activo['FechaFinGarantia'])) : null;
                    $fechaAdquisicion = !empty($activo['FechaAdquisicion']) ? date('Y-m-d', strtotime($activo['FechaAdquisicion'])) : date('Y-m-d');

                    $data = [
                        'IdActivo' => null,
                        'IdDocVenta' => $activo['IdDocVenta'],
                        'IdArticulo' => $activo['IdArticulo'],
                        'Codigo' => null,
                        'Serie' => $activo['Serie'],
                        'IdEstado' => $activo['IdEstado'],
                        'Garantia' => $activo['Garantia'] ?? 0,
                        'FechaFinGarantia' => $fechaFinGarantia,
                        'IdProveedor' => $activo['IdProveedor'] ?? null,
                        'Observaciones' => $activo['Observaciones'] ?? '',
                        'IdEmpresa' => $_SESSION['IdEmpresa'] ?? '',
                        'IdSucursal' => $_SESSION['IdSucursal'],
                        'IdAmbiente' => $activo['IdAmbiente'],
                        'IdCategoria' => $activo['IdCategoria'] ?? 2,
                        'VidaUtil' => $activo['VidaUtil'] ?? 3,
                        'ValorAdquisicion' => $activo['ValorAdquisicion'] ?? 0,
                        'FechaAdquisicion' => $fechaAdquisicion,
                        'UserMod' => $_SESSION['CodEmpleado'],
                        'Accion' => 1
                    ];

                    // Log de los datos preparados para el SP
                    error_log("Datos preparados para SP: " . print_r($data, true), 3, __DIR__ . '/../../logs/debug.log');

                    $activos->registrarActivosVentaPrueba($data);
                    $resultados[] = [
                        'status' => true,
                        'message' => 'Activo registrado correctamente'
                    ];
                }

                $response = [
                    'status' => true,
                    'message' => 'Activos registrados con éxito.',
                    'data' => $resultados
                ];

                // Log de la respuesta antes de enviarla
                error_log("Respuesta a enviar: " . print_r($response, true), 3, __DIR__ . '/../../logs/debug.log');

                echo json_encode($response);
            } catch (Exception $e) {
                error_log("Error RegistrarPruebaVenta: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al registrar activos: ' . $e->getMessage()
                ]);
            }
        }
        break;*/

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
                        'IdEmpresa' => $_SESSION['IdEmpresa'] ?? '',
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
    case 'RegistrarManual':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'Nombre' => $_POST['nombre'],
                    'Descripcion' => $_POST['descripcion'],
                    'Serie' => $_POST['serie'],
                    'IdEstado' => $_POST['idEstado'],
                    'IdCategoria' => $_POST['idCategoria'],
                    'IdAmbiente' => $_POST['idAmbiente'],
                    'IdSucursal' => $_SESSION['cod_UnidadNeg'],
                    'Cantidad' => $_POST['cantidad'],
                    'AnioCompra' => $_POST['anioCompra'],
                    'UserMod' => $_SESSION['CodEmpleado'],
                    'Accion' => 1
                ];

                $activos->registrarActivosManual($data);
                echo json_encode([
                    'status' => true,
                    'message' => 'Activos registrados con éxito.'
                ]);
            } catch (Exception $e) {
                error_log("Error RegistrarManual: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al registrar activos manualmente: ' . $e->getMessage()
                ]);
            }
        }
        break;

    case 'Actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'IdActivo' => isset($_POST['IdActivo']) ? (int)$_POST['IdActivo'] : null,
                    'Serie' => $_POST['Serie'] ?? null,
                    'IdEstado' => isset($_POST['IdEstado']) ? (int)$_POST['IdEstado'] : null,
                    'IdAmbiente' => isset($_POST['IdAmbiente']) ? (int)$_POST['IdAmbiente'] : null,
                    'IdCategoria' => isset($_POST['IdCategoria']) ? (int)$_POST['IdCategoria'] : null,
                    'Observaciones' => $_POST['Observaciones'] ?? null,
                    'UserMod' => $_SESSION['CodEmpleado'] ?? null,
                    'Accion' => 2
                ];

                // Validar campos requeridos
                if (!$data['IdActivo']) {
                    throw new Exception("El ID del activo es requerido");
                }
                if (!$data['IdEstado']) {
                    throw new Exception("El estado es requerido");
                }
                if (!$data['IdAmbiente']) {
                    throw new Exception("El ambiente es requerido");
                }
                if (!$data['IdCategoria']) {
                    throw new Exception("La categoría es requerida");
                }

                $activos->actualizarActivos($data);
                echo json_encode(['status' => true, 'message' => 'Activo actualizado con éxito.']);
            } catch (Exception $e) {
                error_log("Error Actualizar: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode(['status' => false, 'message' => 'Error al actualizar activo: ' . $e->getMessage()]);
            }
        }
        break;

    case 'asignarResponsable':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'IdActivo' => $_POST['IdActivo'],
                    'IdResponsable' => $_POST['IdResponsable'],
                    'UserMod' => $_SESSION['CodEmpleado'],
                    'Accion' => 4
                ];

                $activos->asignarResponsables($data);
                echo json_encode(['status' => true, 'message' => 'Responsable asignado con éxito.']);
            } catch (Exception $e) {
                error_log("Error AsignarResponsable: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode(['status' => false, 'message' => 'Error al asignar responsable: ' . $e->getMessage()]);
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

    case 'obtenerActivoPorId':
        try {
            $idActivo = $_POST['idActivo'];
            $info = $activos->obtenerActivoPorId($idActivo);
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

            // Documentos de venta
            // $stmt = $db->query("SELECT idDocumentoVta AS IdDocVenta FROM vListadoDeArticulosPorDocumentoDeVenta GROUP BY idDocumentoVta");
            // $combos['docVenta'] = '<option value="">Seleccione</option>';
            // foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            //     $combos['docVenta'] .= "<option value='{$row['IdDocVenta']}'>{$row['IdDocVenta']}</option>";
            // }

            // Proveedores
            $stmt = $db->query("SELECT Documento, RazonSocial FROM vEntidadExternaGeneralProveedor ORDER BY RazonSocial");
            $combos['proveedores'] = '<option value="">Seleccione</option>';
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $combos['proveedores'] .= "<option value='{$row['Documento']}'>{$row['RazonSocial']}</option>";
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

            // Obtener ambientes filtrados por empresa y sucursal
            $idEmpresa = $_SESSION['cod_empresa'] ?? null;
            $idSucursal = $_SESSION['cod_UnidadNeg'] ?? null;

            if ($idEmpresa && $idSucursal) {
                $ambientes = $activos->obtenerAmbientesPorEmpresaSucursal($idEmpresa, $idSucursal);
                $combos['ambientes'] = '<option value="">Seleccione</option>';
                foreach ($ambientes as $row) {
                    $combos['ambientes'] .= "<option value='{$row['idAmbiente']}'>{$row['nombre']}</option>";
                }
            } else {
                $combos['ambientes'] = '<option value="">No hay ambientes disponibles</option>';
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

    /*case 'articulos_por_doc_venta':
        try {
            $db = (new Conectar())->ConexionBdPracticante();

            $IdDocVenta = $_POST['IdDocVenta'] ?? null;
            if (!$IdDocVenta) {
                throw new Exception("IdDocVenta no proporcionado.");
            }

            $stmt = $db->prepare("
            SELECT ing.idDocumentoVta AS IdDocVenta, ing.idArtServDetDocVta AS IdArticulo, a.Descripcion_articulo AS Nombre,
	   a.DescripcionMarca AS Marca, e.Razon_empresa AS Empresa, ing.cod_UnidadNeg AS IdUnidadNegocio,
	   ing.Nombre_local AS NombreLocal,
	   ing.Cantidad AS Cantidad
FROM vListadoDeArticulosPorDocumentoDeVenta ing
INNER JOIN vArticulos a ON ing.idArtServDetDocVta = a.IdArticulo
LEFT JOIN vEmpresas e ON ing.Cod_Empresa = e.cod_empresa 
WHERE  ing.IdTipoComp = 9 AND ing.idDocumentoVta = ? 
ORDER BY a.Descripcion_articulo;
");
            $stmt->execute([$IdDocVenta]);
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

            error_log("Artículos por doc: IdDocVenta=$IdDocVenta, Resultados=" . json_encode($articulos), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode(['status' => true, 'data' => $articulos, 'message' => 'Artículos cargados correctamente.']);
        } catch (Exception $e) {
            error_log("Error articulos_por_doc_venta: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar artículos: ' . $e->getMessage()]);
        }
        break;*/

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

    case 'verificarArticuloExistente':
        try {
            $idDocIngresoAlm = $_POST['IdDocIngresoAlm'] ?? null;
            $idArticulo = $_POST['IdArticulo'] ?? null;
            $idEmpresa = $_SESSION['cod_empresa'] ?? null;
            $idSucursal = $_SESSION['cod_UnidadNeg'] ?? null;

            if (!$idDocIngresoAlm || !$idArticulo) {
                throw new Exception("Se requiere el documento de ingreso y el artículo");
            }

            $existe = $activos->verificarArticuloExistente($idDocIngresoAlm, $idArticulo, $idEmpresa, $idSucursal);
            echo json_encode([
                'status' => true,
                'existe' => $existe,
                'message' => $existe ? 'El artículo ya ha sido registrado con este documento de ingreso' : 'El artículo no ha sido registrado'
            ]);
        } catch (Exception $e) {
            error_log("Error verificarArticuloExistente: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode([
                'status' => false,
                'message' => 'Error al verificar artículo: ' . $e->getMessage()
            ]);
        }
        break;

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

    case 'obtenerAmbientes':
        try {
            $idEmpresa = $_POST['idEmpresa'] ?? null;
            $idSucursal = $_POST['idSucursal'] ?? null;

            if (!$idEmpresa || !$idSucursal) {
                throw new Exception("Se requiere empresa y sucursal");
            }

            $ambientes = $activos->obtenerAmbientesPorEmpresaSucursal($idEmpresa, $idSucursal);
            echo json_encode([
                'status' => true,
                'data' => $ambientes,
                'message' => 'Ambientes cargados correctamente'
            ]);
        } catch (Exception $e) {
            error_log("Error obtenerAmbientes: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode([
                'status' => false,
                'message' => 'Error al obtener ambientes: ' . $e->getMessage()
            ]);
        }
        break;

    case 'darBaja':
        try {
            // Validar campos requeridos
            if (!isset($_POST['idActivo']) || !isset($_POST['idResponsable']) || !isset($_POST['motivoBaja']) || !isset($_POST['userMod'])) {
                throw new Exception("Faltan campos requeridos");
            }
            // Log para depuración
            error_log("Datos recibidos en darBaja: " . print_r($_POST, true));

            $data = [
                'idActivo' => $_POST['idActivo'],
                'idResponsable' => $_POST['idResponsable'],
                'motivoBaja' => $_POST['motivoBaja'],
                'userMod' => $_POST['userMod'],
                'idEstado' => 3, // Estado de baja
                'accion' => 3    // Acción de cambio de estado
            ];

            // Log para depuración
            error_log("Datos preparados para SP: " . print_r($data, true));

            $resultado = $activos->actualizarActivos($data);

            if ($resultado) {
                echo json_encode(['status' => true, 'message' => 'Activo dado de baja correctamente']);
            } else {
                throw new Exception("Error al dar de baja el activo");
            }
        } catch (Exception $e) {
            error_log("Error en darBaja: " . $e->getMessage());
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'verificarResponsable':
        try {
            $idActivo = $_POST['idActivo'] ?? null;
            if (!$idActivo) {
                throw new Exception("ID del activo no proporcionado");
            }

            $existe = $activos->verificarResponsableExistente($idActivo);
            echo json_encode([
                'status' => true,
                'existe' => $existe
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Acción no válida.']);
        break;
}
