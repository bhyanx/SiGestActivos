<?php
/**
 * Environments Management View
 *
 * This view provides interface for reviewing, adding, and editing
 * asset environments by branch locations
 */

session_start();

// Security check - ensure user is authenticated
if (!isset($_SESSION['cod_empresa']) || !isset($_SESSION['cod_UnidadNeg'])) {
    header('Location: ../../index.php');
    exit;
}

// Sanitize session data for output
$cod_empresa = htmlspecialchars($_SESSION['cod_empresa'] ?? '');
$cod_UnidadNeg = htmlspecialchars($_SESSION['cod_UnidadNeg'] ?? '');
$codEmpleado = htmlspecialchars($_SESSION['CodEmpleado'] ?? '');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Ambientes - Sistema Gestión de Activos</title>
    <meta name="description" content="Gestión de ambientes para activos del sistema">
</head>

<body class="sidebar-mini control-sidebar-slide-open layout-navbar-fixed layout-fixed sidebar-mini-xs sidebar-mini-md sidebar-collapse">
    <div class="wrapper">
        <?php require_once("../Layouts/Head-Body.php"); ?>
        <?php require_once("../Layouts/SideBar.php"); ?>

        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-building"></i> Gestión de Ambientes</h1>
                            <p class="text-muted">Administre los ambientes de activos por sucursales</p>
                        </div>
                        <div class="col-sm-6">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item">
                                        <a href="../../views/Dashboard/" title="Ir al inicio">
                                            <i class="fas fa-home"></i> Inicio
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Ambientes
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="card-title mb-0">
                                        <i class="fas fa-list"></i> Listado de Ambientes en el Sistema
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <!-- Filter and Action Section -->
                                    <div class="row mb-3">
                                        <div class="col-md-12" id="divfiltros">
                                            <div class="row">
                                                <div class="col-md-3 offset-md-9">
                                                    <div class="form-group">
                                                        <!-- Hidden form fields for session data and user info -->
                                                        <input type="hidden" id="cod_empresa" value="<?php echo $cod_empresa; ?>">
                                                        <input type="hidden" id="cod_UnidadNeg" value="<?php echo $cod_UnidadNeg; ?>">
                                                        <input type="hidden" id="userMod" value="<?php echo $codEmpleado; ?>">
                                                        
                                                        <button type="button" class="btn btn-primary btn-block" id="btnnuevo"
                                                                title="Crear nuevo ambiente">
                                                            <i class="fas fa-plus"></i> Nuevo Ambiente
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Table Section -->
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="tblAmbientes" class="table table-bordered table-striped table-hover mt-2"
                                                       role="table" aria-label="Tabla de ambientes">
                                                    <thead class="thead-primary">
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col" class="d-none">IdAmbiente</th>
                                                            <th scope="col">Nombre</th>
                                                            <th scope="col">Descripción</th>
                                                            <th scope="col">Sucursal</th>
                                                            <th scope="col">Estado</th>
                                                            <th scope="col" class="text-center">
                                                                <i class="fas fa-cogs" title="Acciones"></i>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Data will be loaded via AJAX -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th scope="row">#</th>
                                                            <th scope="row" class="d-none">IdAmbiente</th>
                                                            <th scope="row">Nombre</th>
                                                            <th scope="row">Descripción</th>
                                                            <th scope="row">Sucursal</th>
                                                            <th scope="row">Estado</th>
                                                            <th scope="row" class="text-center">
                                                                <i class="fas fa-cogs" title="Acciones"></i>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Environment Registration Modal -->
                    <div class="modal fade" id="ModalAmbiente" tabindex="-1" role="dialog"
                         aria-labelledby="ModalAmbienteLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form id="frmAmbiente" name="frmAmbiente" method="POST" novalidate>
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="ModalAmbienteLabel">
                                            <i class="fas fa-plus-circle"></i> Registrar Nuevo Ambiente
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal"
                                                aria-label="Cerrar modal">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    
                                    <div class="modal-body">
                                        <!-- Hidden fields -->
                                        <input type="hidden" name="idAmbiente" id="idAmbiente" value="0">
                                        <input type="hidden" id="estadoAmbiente" value="1">

                                        <div class="row">
                                            <!-- Environment Name -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nombre" class="required">
                                                        <i class="fas fa-tag"></i> Nombre del Ambiente
                                                    </label>
                                                    <input type="text"
                                                           class="form-control"
                                                           id="nombre"
                                                           name="nombre"
                                                           placeholder="Ingrese el nombre del ambiente"
                                                           maxlength="100"
                                                           required
                                                           autocomplete="off">
                                                    <div class="invalid-feedback">
                                                        Por favor ingrese un nombre válido
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Environment Description -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="descripcion" class="required">
                                                        <i class="fas fa-align-left"></i> Descripción
                                                    </label>
                                                    <input type="text"
                                                           class="form-control"
                                                           id="descripcion"
                                                           name="descripcion"
                                                           placeholder="Descripción del ambiente"
                                                           maxlength="255"
                                                           required
                                                           autocomplete="off">
                                                    <div class="invalid-feedback">
                                                        Por favor ingrese una descripción válida
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Environment Abbreviation -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="CodAmbiente">
                                                        <i class="fas fa-code"></i> Abreviación
                                                    </label>
                                                    <input type="text"
                                                           class="form-control text-uppercase"
                                                           id="CodAmbiente"
                                                           name="CodAmbiente"
                                                           placeholder="Ej: LAB, OF1, ALM"
                                                           maxlength="10"
                                                           autocomplete="off">
                                                    <small class="form-text text-muted">
                                                        Código corto para identificar el ambiente (opcional)
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <!-- Company and Branch info (read-only display) -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>
                                                        <i class="fas fa-building"></i> Información de Asignación
                                                    </label>
                                                    <div class="alert alert-info mb-0">
                                                        <small>
                                                            <strong>Empresa:</strong> <?php echo $_SESSION['nombre_empresa'] ?? 'Sistema'; ?><br>
                                                            <strong>Sucursal:</strong> <?php echo $_SESSION['nombre_sucursal'] ?? 'Principal'; ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Additional fields for future use (currently commented but structured) -->
                                        <div class="d-none">
                                            <!-- Company Selection (hidden for now) -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="empresaModal">
                                                        <i class="fas fa-building"></i> Empresa
                                                    </label>
                                                    <select name="empresaModal" id="empresaModal" class="form-control select2">
                                                        <option value="">Seleccione una empresa</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <!-- Branch Selection (hidden for now) -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sucursalModal">
                                                        <i class="fas fa-map-marker-alt"></i> Sucursal
                                                    </label>
                                                    <select name="sucursalModal" id="sucursalModal" class="form-control select2">
                                                        <option value="">Seleccione una sucursal</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="submit" id="btnGuardarAmbiente" class="btn btn-success">
                                            <i class="fas fa-save"></i> Guardar Ambiente
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Audit History Modal -->
                    <div class="modal fade" id="ModalAuditoria" tabindex="-1" role="dialog"
                         aria-labelledby="ModalAuditoriaLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title" id="ModalAuditoriaLabel">
                                        <i class="fas fa-history"></i> Historial de Auditoría
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="timeline timeline-inverse" id="timedata" role="log"
                                         aria-label="Cronología de cambios">
                                        <!-- Timeline data will be loaded here -->
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fas fa-times"></i> Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <?php require_once("../Layouts/Footer.php"); ?>
        
        <!-- Load JavaScript -->
        <script src="ambientes.js" defer></script>
        
        <!-- Additional styles for this page -->
        <style>
            .required::after {
                content: " *";
                color: red;
            }
            
            .text-uppercase {
                text-transform: uppercase;
            }
            
            .table th {
                background-color: #f8f9fa;
                font-weight: 600;
            }
            
            .card {
                border-radius: 10px;
            }
            
            .btn {
                border-radius: 5px;
            }
            
            .modal-header {
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
            }
            
            .form-control:focus {
                border-color: #007bff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }
        </style>
    </div>
</body>

</html>