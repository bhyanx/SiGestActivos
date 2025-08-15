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

    case 'ConsultarActivos':
        try {
            // Procesar filtros especiales: convertir "TODOS" a null para no aplicar filtro
            $filtroSucursal = $_POST['filtroSucursal'] ?? $_SESSION['cod_UnidadNeg'] ?? null;
            $filtroAmbiente = $_POST['filtroAmbiente'] ?? null;
            $filtroCategoria = $_POST['filtroCategoria'] ?? null;
            
            // Si el valor es "TODOS", convertir a null para mostrar todos los registros
            if ($filtroSucursal === 'TODOS') $filtroSucursal = null;
            if ($filtroAmbiente === 'TODOS') $filtroAmbiente = null;
            if ($filtroCategoria === 'TODOS') $filtroCategoria = null;
            
            $filtros = [
                'pCodigo' => $_POST['pCodigo'] ?? null,
                'pIdEmpresa' => $_POST['filtroEmpresa'] ?? $_SESSION['cod_empresa'] ?? null,
                'pIdSucursal' => $filtroSucursal,
                'pIdAmbiente' => $filtroAmbiente,
                'pIdCategoria' => $filtroCategoria,
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
            error_log("POST data recibida: " . print_r($_POST, true), 3, __DIR__ . '/../../logs/debug.log');
            $filtros = [
                'pIdArticulo' => $_POST['IdArticulo'] ?? null,
                'pCodigo' => $_POST['pCodigo'] ?? null,
                'pIdEmpresa' => $_SESSION['cod_empresa'] ?? null,
                'pIdSucursal' => $_SESSION['cod_UnidadNeg'] ?? null,
                'pIdCategoria' => $_POST['pIdCategoria'] ?? null,
                'pIdEstado' => $_POST['pIdEstado'] ?? null
            ];
            error_log("Filtros procesados: " . print_r($filtros, true), 3, __DIR__ . '/../../logs/debug.log');
            $resultados = $activos->consultarActivosRelacionados($filtros);
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



    case 'Actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $idActivo = isset($_POST['IdActivo']) ? (int)$_POST['IdActivo'] : null;
                if (!$idActivo) {
                    throw new Exception("El ID del activo es requerido.");
                }

                $original_activo = $activos->obtenerActivoPorId($idActivo);
                if (!$original_activo) {
                    throw new Exception("Activo no encontrado con ID " . $idActivo);
                }

                $data = [
                    'IdActivo' => $idActivo,
                    'Serie' => isset($_POST['Serie']) ? $_POST['Serie'] : $original_activo['NumeroSerie'],
                    'IdEstado' => (isset($_POST['IdEstado']) && $_POST['IdEstado'] !== '') ? (int)$_POST['IdEstado'] : $original_activo['idEstado'],
                    'IdAmbiente' => (isset($_POST['IdAmbiente']) && $_POST['IdAmbiente'] !== '') ? (int)$_POST['IdAmbiente'] : $original_activo['idAmbiente'],
                    'IdCategoria' => (isset($_POST['IdCategoria']) && $_POST['IdCategoria'] !== '') ? (int)$_POST['IdCategoria'] : $original_activo['idCategoria'],
                    'Observaciones' => isset($_POST['Observaciones']) ? $_POST['Observaciones'] : $original_activo['observaciones'],
                    'UserMod' => $_SESSION['CodEmpleado'] ?? null,
                    'Accion' => 2
                ];

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

    case 'obtenerComponentes':
        try {
            $idActivoPadre = $_POST['idActivoPadre'];
            $info = $activos->obtenerComponente($idActivoPadre);
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

    case 'obtenerUltimosEventos':
        try {
            $idActivo = $_POST['idActivo'];
            error_log("=== DEBUGGING ÚLTIMOS EVENTOS ===", 3, __DIR__ . '/../../logs/debug.log');
            error_log("ID Activo: " . $idActivo, 3, __DIR__ . '/../../logs/debug.log');

            $eventos = $activos->obtenerUltimosEventosActivo($idActivo);
            error_log("Eventos obtenidos: " . print_r($eventos, true), 3, __DIR__ . '/../../logs/debug.log');

            echo json_encode([
                'status' => true,
                'data' => $eventos
            ]);
        } catch (Exception $e) {
            error_log("Error en obtenerUltimosEventos: " . $e->getMessage(), 3, __DIR__ . '/../../logs/debug.log');
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
                'pIdEmpresa' => null,
                'pIdSucursal' => null,
                'pIdAmbiente' => null,
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
            $stmt = $db->query("SELECT idDocumentoVta AS IdDocVenta FROM bdGestionLubriseng.dbo.vmListadoDeArticulosPorDocumentoDeVenta GROUP BY idDocumentoVta");
            $combos['docVenta'] = '<option value="">Seleccione</option>';
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $combos['docVenta'] .= "<option value='{$row['IdDocVenta']}'>{$row['IdDocVenta']}</option>";
            }

            // Proveedores
            $stmt = $db->query("SELECT Documento, RazonSocial FROM vEntidadExternaGeneralProveedor ORDER BY RazonSocial");
            $combos['proveedores'] = '<option value="">Seleccione</option>';
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $combos['proveedores'] .= "<option value='{$row['Documento']}'>{$row['RazonSocial']}</option>";
            }

            // Empresas
            $empresas = $combo->comboEmpresa();
            $combos['empresas'] = '<option value="">Seleccione Empresa</option>';
            foreach ($empresas as $row) {
                $selected = ($row['cod_empresa'] == ($_SESSION['cod_empresa'] ?? '')) ? 'selected' : '';
                $combos['empresas'] .= "<option value='{$row['cod_empresa']}' {$selected}>{$row['Razon_empresa']}</option>";
            }

            // Unidades de Negocio (inicialmente vacío, se carga dinámicamente)
            $combos['unidadesNegocio'] = '<option value="">Seleccione Empresa primero</option>';

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

            // Ambientes (inicialmente vacío, se carga dinámicamente)
            $combos['ambientes'] = '<option value="">Seleccione Empresa y Unidad de Negocio primero</option>';

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

    case 'articulos_por_doc_venta':
        try {
            $dbv = (new Conectar())->ConexionBdProgSistemas();

            $IdDocVenta = $_POST['IdDocVenta'] ?? null;
            if (!$IdDocVenta) {
                throw new Exception("IdDocVenta no proporcionado.");
            }

            $stmt = $dbv->prepare("SELECT ing.idDocumentoVta AS IdDocVenta, ing.idArtServDetDocVta AS IdArticulo, 
            a.Descripcion_articulo AS Nombre, a.DescripcionMarca AS Marca, e.Razon_empresa AS Empresa,
            ing.cod_UnidadNeg AS IdUnidadNegocio, ing.Nombre_local AS NombreLocal, ing.CantidadSalidaEquivalente AS Cantidad
            FROM vmListadoDeArticulosPorDocumentoDeVenta ing
            INNER JOIN vArticulosMaestro a ON ing.idArtServDetDocVta = a.IdArticulo
            LEFT JOIN vEmpresas e ON ing.Cod_Empresa = e.cod_empresa 
            WHERE ing.idDocumentoVta = ? AND a.idLineaProd = 11
            ORDER BY a.Descripcion_articulo
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
                    'Cantidad' => $row['Cantidad'],
                ];
            }

            error_log("Artículos por doc venta: IdDocVenta=$IdDocVenta, Resultados=" . json_encode($articulos), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode(['status' => true, 'data' => $articulos, 'message' => 'Artículos cargados correctamente.']);
        } catch (Exception $e) {
            error_log("Error articulos_por_doc_venta: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar artículos: ' . $e->getMessage()]);
        }
        break;

    //Case para el modal y agregar 
    case 'articulos_por_doc':
        try {
            $dbi = (new Conectar())->ConexionBdProgSistemas(); // Usar bdActivos
            $IdDocIngresoAlm = $_POST['IdDocIngresoAlm'] ?? null;
            if (!$IdDocIngresoAlm) {
                throw new Exception("IdDocIngresoAlm no proporcionado.");
            }

            $stmt = $dbi->prepare("
            SELECT ing.idDocIngAlmacen AS IdDocIngresoAlm,
            ing.idarticulo AS IdArticulo, 
            a.Descripcion_articulo AS Nombre,
            a.DescripcionMarca AS Marca,
		    e.Razon_empresa AS Empresa,
            ing.cod_UnidadNeg AS IdUnidadNegocio,
            ing.Nombre_local AS NombreLocal,
            p.RazonSocial AS Proveedor
            FROM vmListadoDeArticulosPorDocumentoDeIngresoAAlmacen ing
            INNER JOIN vArticulos a ON ing.IdArticulo = a.IdArticulo
            LEFT JOIN vEmpresas e ON ing.Cod_Empresa = e.cod_empresa 
            LEFT JOIN vEntidadExternaGeneralProveedor p ON p.Documento = ing.Documento
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
                    'Proveedor' => $row['Proveedor'],
                ];
            }

            error_log("Artículos por doc: IdDocIngresoAlm=$IdDocIngresoAlm, Resultados=" . json_encode($articulos), 3, __DIR__ . '/../../logs/debug.log');
            echo json_encode(['status' => true, 'data' => $articulos, 'message' => 'Artículos cargados correctamente.']);
        } catch (Exception $e) {
            error_log("Error Artículos por doc: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['status' => false, 'message' => 'Error al cargar artículos: ' . $e->getMessage()]);
        }
        break;

    case 'GuardarActivosDesdeDocumentoIngreso':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Obtener los datos del array de activos
                $activosArray = json_decode($_POST['activos'], true);
                if (!$activosArray) {
                    throw new Exception("No se recibieron datos de activos válidos");
                }

                // Debug: Log de los datos recibidos
                error_log("=== GUARDANDO ACTIVOS DESDE DOCUMENTO INGRESO ===", 3, __DIR__ . '/../../logs/debug.log');
                error_log("Total activos a guardar: " . count($activosArray), 3, __DIR__ . '/../../logs/debug.log');
                error_log("Datos recibidos: " . print_r($activosArray, true), 3, __DIR__ . '/../../logs/debug.log');

                $resultados = [];
                foreach ($activosArray as $index => $activo) {
                    // Formatear fechas
                    $fechaFinGarantia = !empty($activo['FechaFinGarantia']) ? date('Y-m-d', strtotime($activo['FechaFinGarantia'])) : null;
                    $fechaAdquisicion = !empty($activo['FechaAdquisicion']) ? date('Y-m-d', strtotime($activo['FechaAdquisicion'])) : date('Y-m-d');

                    $data = [
                        'IdArticulo' => $activo['IdArticulo'],
                        'IdEstado' => $activo['IdEstado'],
                        'Garantia' => isset($activo['Garantia']) ? (int)$activo['Garantia'] : 0,
                        'FechaFinGarantia' => $fechaFinGarantia,
                        'IdProveedor' => $activo['IdProveedor'] ?? null,
                        'IdEmpresa' => $_SESSION['cod_empresa'] ?? null,
                        'IdSucursal' => $_SESSION['cod_UnidadNeg'] ?? null,
                        'IdAmbiente' => $activo['IdAmbiente'],
                        'IdCategoria' => $activo['IdCategoria'] ?? 2,
                        'VidaUtil' => $activo['VidaUtil'] ?? 3,
                        'Serie' => $activo['Serie'],
                        'Observaciones' => $activo['Observaciones'] ?? '',
                        'ValorAdquisicion' => $activo['ValorAdquisicion'] ?? 0,
                        'AplicaIGV' => $activo['AplicaIGV'] ?? 0,
                        'FechaAdquisicion' => $fechaAdquisicion,
                        'UserMod' => $_SESSION['CodEmpleado'],
                        'IdDocIngresoAlm' => $activo['IdDocIngresoAlm'] ?? null
                    ];

                    $activos->GuardarActivosDesdeDocumentoIngreso($data);
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
                error_log("Error GuardarActivosDesdeDocumentoIngreso: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al registrar activos: ' . $e->getMessage()
                ]);
            }
        }
        break;

    case 'GuardarActivosDesdeDocumentoVenta':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Obtener los datos del array de activos
                $activosArray = json_decode($_POST['activos'], true);
                if (!$activosArray) {
                    throw new Exception("No se recibieron datos de activos válidos");
                }

                // Debug: Log de los datos recibidos
                error_log("=== GUARDANDO ACTIVOS DESDE DOCUMENTO VENTA ===", 3, __DIR__ . '/../../logs/debug.log');
                error_log("Total activos a guardar: " . count($activosArray), 3, __DIR__ . '/../../logs/debug.log');
                error_log("Datos recibidos: " . print_r($activosArray, true), 3, __DIR__ . '/../../logs/debug.log');

                $resultados = [];
                foreach ($activosArray as $index => $activo) {
                    // Formatear fechas
                    $fechaAdquisicion = !empty($activo['FechaAdquisicion']) ? date('Y-m-d', strtotime($activo['FechaAdquisicion'])) : date('Y-m-d');

                    $data = [
                        'IdArticulo' => $activo['IdArticulo'],
                        'IdEstado' => $activo['IdEstado'],
                        'IdProveedor' => $activo['IdProveedor'] ?? null,
                        'IdEmpresa' => $_SESSION['cod_empresa'] ?? null,
                        'IdSucursal' => $_SESSION['cod_UnidadNeg'] ?? null,
                        'IdAmbiente' => $activo['IdAmbiente'],
                        'IdCategoria' => $activo['IdCategoria'] ?? 2,
                        'VidaUtil' => $activo['VidaUtil'] ?? 3,
                        'Serie' => $activo['Serie'],
                        'Observaciones' => $activo['Observaciones'] ?? '',
                        'ValorAdquisicion' => $activo['ValorAdquisicion'] ?? 0,
                        'AplicaIGV' => $activo['AplicaIGV'] ?? 0,
                        'FechaAdquisicion' => $fechaAdquisicion,
                        'UserMod' => $_SESSION['CodEmpleado'],
                        'Cantidad' => isset($activo['Cantidad']) ? (int)$activo['Cantidad'] : 1,
                        'IdDocVenta' => $activo['IdDocVenta'] ?? null
                    ];

                    $activos->GuardarActivosDesdeDocumentoVenta($data);
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
                error_log("Error GuardarActivosDesdeDocumentoVenta: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al registrar activos: ' . $e->getMessage()
                ]);
            }
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

    case 'verificarArticuloExistenteDocVenta':
        try {
            $IdDocVenta = $_POST['IdDocVenta'] ?? null;
            $idArticulo = $_POST['IdArticulo'] ?? null;
            $idEmpresa = $_SESSION['cod_empresa'] ?? null;
            $idSucursal = $_SESSION['cod_UnidadNeg'] ?? null;

            if (!$IdDocVenta || !$idArticulo) {
                throw new Exception("Se requiere el documento de venta y el artículo");
            }

            $existe = $activos->verificarArticuloExistenteDocVenta($IdDocVenta, $idArticulo, $idEmpresa, $idSucursal);
            echo json_encode([
                'status' => true,
                'existe' => $existe,
                'message' => $existe ? 'El artículo ya ha sido registrado con este documento de venta' : 'El artículo no ha sido registrado'
            ]);
        } catch (Exception $e) {
            error_log("Error verificarArticuloExistenteDocVenta: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
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

    case 'verHistorial':
        try {
            $idActivo = $_POST['idActivo'] ?? null;
            if (!$idActivo) {
                throw new Exception("ID del activo no proporcionado");
            }
            $historial = $activos->obtenerHistorialActivo($idActivo);
            echo json_encode(['status' => true, 'data' => $historial]);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'GuardarActivosManual':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $input = file_get_contents("php://input");
                $requestData = json_decode($input, true);

                if (!isset($requestData['activos']) || !is_array($requestData['activos'])) {
                    throw new Exception("No se recibieron datos de activos válidos.");
                }

                // Debug: Log de los datos recibidos
                error_log("=== DATOS RECIBIDOS EN CONTROLADOR ===", 3, __DIR__ . '/../../logs/debug.log');
                error_log("Total activos: " . count($requestData['activos']), 3, __DIR__ . '/../../logs/debug.log');
                error_log("Datos completos: " . print_r($requestData, true), 3, __DIR__ . '/../../logs/debug.log');

                $activosGuardados = [];
                foreach ($requestData['activos'] as $activo) {
                    // Preparar los datos para el modelo
                    $data = [
                        //'IdActivo' => $activo['IdActivo'] ?? null,
                        //'IdDocumentoVenta' => $activo['IdDocumentoVenta'] ?? null,
                        //'IdOrdendeCompra' => $activo['IdOrdendeCompra'] ?? null,
                        'Nombre' => $activo['Nombre'] ?? null,
                        'Descripcion' => $activo['Descripcion'] ?? null,
                        'IdEstado' => $activo['IdEstado'] ?? null,
                        'Garantia' => $activo['Garantia'] ?? 0,
                        'IdResponsable' => $activo['IdResponsable'] ?? null,
                        'FechaFinGarantia' => $activo['FechaFinGarantia'] ?? null,
                        'IdProveedor' => $activo['IdProveedor'] ?? null,
                        'IdMarca' => $activo['IdMarca'] ?? null,
                        'IdEmpresa' => $activo['IdEmpresa'] ?? $_SESSION['cod_empresa'] ?? null,
                        'IdSucursal' => $activo['IdSucursal'] ?? $_SESSION['cod_UnidadNeg'] ?? null,
                        'IdAmbiente' => $activo['IdAmbiente'] ?? null,
                        'IdCategoria' => $activo['IdCategoria'] ?? null,
                        'VidaUtil' => $activo['VidaUtil'] ?? 3,
                        'Serie' => $activo['Serie'] ?? null,
                        'Observaciones' => $activo['Observaciones'] ?? null,
                        'ValorAdquisicion' => $activo['ValorAdquisicion'] ?? 0,
                        'AplicaIGV' => $activo['AplicaIGV'] ?? 0,
                        'FechaAdquisicion' => $activo['FechaAdquisicion'] ?? date('Y-m-d'),
                        'UserMod' => $_SESSION['CodEmpleado'] ?? 'SYSTEM',
                        //'Codigo' => $activo['Codigo'] ?? null,
                        'Cantidad' => $activo['Cantidad'] ?? 1,
                        'Correlativo' => !empty($activo['Correlativo']) ? $activo['Correlativo'] : null,
                    ];

                    // Debug: Log de los datos que se envían al modelo
                    error_log("Datos enviados al modelo: " . print_r($data, true), 3, __DIR__ . '/../../logs/debug.log');
                    error_log("Campo Correlativo específico: " . ($data['Correlativo'] ?? 'NULL'), 3, __DIR__ . '/../../logs/debug.log');
                    
                    $activos->GuardarActivosManual($data);
                    $activosGuardados[] = $data['Nombre']; // Guardar el nombre del activo para la respuesta
                }

                echo json_encode([
                    'status' => true,
                    'message' => 'Activos (' . implode(', ', $activosGuardados) . ') registrados con éxito.',
                    'data' => $activosGuardados
                ]);
            } catch (Exception $e) {
                error_log("Error GuardarActivosManuales: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
                echo json_encode([
                    'status' => false,
                    'message' => 'Error al registrar activos manualmente: ' . $e->getMessage()
                ]);
            }
        }
        break;

    case 'comboProveedor':
        try {
            // Asegúrate de que $combo esté instanciado (viene de Combos.php)
            if (empty($combo) || !($combo instanceof Combos)) {
                $combo = new Combos();
            }

            $filtro = $_GET['filtro'] ?? null;
            $datos = $combo->get_Proveedor($filtro);
            $arrayProv = [];

            if (is_array($datos) && count($datos) > 0) {
                foreach ($datos as $row) {
                    $arrayProv[] = [
                        'id' => $row['Documento'], // Asumiendo que Documento es el ID
                        'text' => $row['RazonSocial'] . " - " . $row['Documento'] // O el formato que necesites
                    ];
                }
            }
            echo json_encode($arrayProv);
        } catch (Exception $e) {
            error_log("Error en comboProveedor: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['results' => [], 'error' => $e->getMessage()]);
        }
        break;

    case 'comboMarcas':
        try {
            // Asegúrate de que $combo esté instanciado (viene de Combos.php)
            if (empty($combo) || !($combo instanceof Combos)) {
                $combo = new Combos();
            }

            $filtro = $_GET['filtro'] ?? null;
            $datos = $combo->get_Marca($filtro);
            $arrayMarca = [];

            if (is_array($datos) && count($datos) > 0) {
                foreach ($datos as $row) {
                    $arrayMarca[] = [
                        'id' => $row['codMarca'], // Asumiendo que Documento es el ID
                        'text' => $row['DescripcionMarca'] . " - " . $row['codMarca'] // O el formato que necesites
                    ];
                }
            }
            echo json_encode($arrayMarca);
        } catch (Exception $e) {
            error_log("Error en comboMarca: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode(['results' => [], 'error' => $e->getMessage()]);
        }
        break;

    case 'comboUnidadNegocio':
        try {
            $codEmpresa = $_POST['codEmpresa'] ?? null;
            if (!$codEmpresa) {
                throw new Exception("Código de empresa no proporcionado");
            }

            $unidadesNegocio = $combo->comboUnidadNegocio($codEmpresa);
            $html = '<option value="">Seleccione Unidad de Negocio</option>';
            foreach ($unidadesNegocio as $row) {
                $selected = ($row['cod_UnidadNeg'] == ($_SESSION['cod_UnidadNeg'] ?? '')) ? 'selected' : '';
                $html .= "<option value='{$row['cod_UnidadNeg']}' {$selected}>{$row['Nombre_local']}</option>";
            }

            echo json_encode([
                'status' => true,
                'data' => $html,
                'message' => 'Unidades de negocio cargadas correctamente'
            ]);
        } catch (Exception $e) {
            error_log("Error comboUnidadNegocio: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode([
                'status' => false,
                'message' => 'Error al cargar unidades de negocio: ' . $e->getMessage()
            ]);
        }
        break;

    case 'comboAmbiente':
        try {
            $idEmpresa = $_POST['idEmpresa'] ?? null;
            $idSucursal = $_POST['idSucursal'] ?? null;
            
            if (!$idEmpresa || !$idSucursal) {
                throw new Exception("Se requiere empresa y unidad de negocio");
            }

            $ambientes = $activos->obtenerAmbientesPorEmpresaSucursal($idEmpresa, $idSucursal);
            $html = '<option value="">Seleccione Ambiente</option>';
            foreach ($ambientes as $row) {
                $html .= "<option value='{$row['idAmbiente']}'>{$row['nombre']}</option>";
            }

            echo json_encode([
                'status' => true,
                'data' => $html,
                'message' => 'Ambientes cargados correctamente'
            ]);
        } catch (Exception $e) {
            error_log("Error comboAmbiente: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode([
                'status' => false,
                'message' => 'Error al cargar ambientes: ' . $e->getMessage()
            ]);
        }
        break;

    case 'verificarCorrelativo':
        try {
            $idEmpresa = $_POST['idEmpresa'] ?? null;
            $idCategoria = $_POST['idCategoria'] ?? null;
            
            if (!$idEmpresa || !$idCategoria) {
                throw new Exception("Se requiere empresa y categoría");
            }

            $configuracion = $activos->verificarConfiguracionCorrelativo($idEmpresa, $idCategoria);
            echo json_encode([
                'status' => true,
                'data' => $configuracion,
                'message' => 'Configuración obtenida correctamente'
            ]);
        } catch (Exception $e) {
            error_log("Error verificarCorrelativo: " . $e->getMessage(), 3, __DIR__ . '/../../logs/errors.log');
            echo json_encode([
                'status' => false,
                'message' => 'Error al verificar correlativo: ' . $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode(['status' => false, 'message' => 'Acción no válida.']);
        break;
}
