<?php

session_start();

// ? VISTA PARA EDITAR MOVIMIENTOS PENDIENTES

// ? MANTENIMIENTO
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Editar Movimientos - Sistema Gestion de activos</title>
</head>

<body class="sidebar-mini control-sidebar-slide-open layout-navbar-fixed layout-fixed sidebar-mini-xs sidebar-mini-md sidebar-collapse">
    <div class="wrapper">
        <?php require_once("../Layouts/Head-Body.php"); ?>
        <?php require_once("../Layouts/SideBar.php"); ?>
        <div class="content-wrapper">
            <section class="content-header">
                <div class="content-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Editar Movimientos Pendientes</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="#">Inicio</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Editar Movimientos
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Modal para Ver Detalles del Movimiento -->
                <div class="modal fade" id="modalVerDetallesMovimiento" tabindex="-1" role="dialog" aria-labelledby="modalVerDetallesLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalVerDetallesLabel">
                                    <i class="fas fa-list"></i> Detalles del Movimiento
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered" id="tblDetallesMovimiento">
                                                <thead class="table-info">
                                                    <tr>
                                                        <th>Código Activo</th>
                                                        <th>Nombre Activo</th>
                                                        <th>Ambiente Origen</th>
                                                        <th>Ambiente Destino</th>
                                                        <th>Responsable Origen</th>
                                                        <th>Responsable Destino</th>
                                                        <th>Tipo Movimiento</th>
                                                        <th>Fecha</th>
                                                        <th>Usuario</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
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

                <!-- Sección de Edición de Movimiento -->
                <div id="divEditarMovimiento" style="display: none;" class="col-12">
                    <!-- Botón de Regreso -->
                    <!-- <div class="mb-3">
                        <button type="button" class="btn btn-secondary" id="btnVolverLista">
                            <i class="fas fa-arrow-left"></i> Volver a la Lista
                        </button>
                    </div> -->

                    <!-- Información del Movimiento -->
                    <div class="card">
                        <div class="alert alert-info alert-dismissible">
                            <span id="lbldatossucmovimiento"></span>
                            <h5 class="card-title mb-0"><i class="fas fa-edit"></i> Editar Movimiento | </h5>
                            <span id="lblusuariorealizando"> Usuario realizando el movimiento: <?php echo $_SESSION['PrimerNombre'] . ' ' . $_SESSION['ApellidoPaterno'] . ' ' . $_SESSION['ApellidoMaterno']; ?></span>
                            <button type="button" class="close btn" id="btnchangedatasucmovimiento"><i class="fas fa-undo-alt"></i></button>
                            <!-- <h5 class="card-title mb-0">
                                <i class="fas fa-edit"></i> Editar Movimiento
                            </h5> -->
                        </div>
                        <div class="card-body">
                            <form id="frmEditarMovimiento">
                                <input type="hidden" id="editIdMovimiento" name="idMovimiento">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="editCodigoMovimiento">Código:</label>
                                            <input type="text" class="form-control" id="editCodigoMovimiento" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="editTipoMovimiento">Tipo:</label>
                                            <input type="text" class="form-control" id="editTipoMovimiento" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="editEmpresaOrigen">Empresa Origen:</label>
                                            <input type="text" class="form-control" id="editEmpresaOrigen" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="editSucursalOrigen">Sucursal Origen:</label>
                                            <input type="text" class="form-control" id="editSucursalOrigen" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="editAutorizador">Autorizador:</label>
                                            <input type="text" class="form-control" id="editAutorizador" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="editEmpresaDestino">Empresa Destino:</label>
                                            <select class="form-control" id="editEmpresaDestino" name="idEmpresaDestino">
                                                <option value="">Seleccionar Empresa</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="editSucursalDestino">Sucursal Destino:</label>
                                            <select class="form-control" id="editSucursalDestino" name="idSucursalDestino">
                                                <option value="">Seleccionar Sucursal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="editReceptor">Receptor:</label>
                                            <select class="form-control" id="editReceptor" name="idReceptor">
                                                <option value="">Seleccionar Receptor</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Detalles de Activos -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-boxes"></i> Activos del Movimiento
                                <button type="button" class="btn btn-success btn-sm float-right" id="btnAgregarActivo">
                                    <i class="fas fa-plus"></i> Agregar Activo
                                </button>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="tbldetalleactivomov">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>ID</th>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Sucursal Origen</th>
                                            <th>Ambiente Origen</th>
                                            <th>Sucursal Destino</th>
                                            <th>Ambiente Destino</th>
                                            <th>Responsable Destino</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="card mt-3">
                        <div class="card-body text-center">
                            <button type="button" class="btn btn-secondary mr-2" id="btnCancelarEdicion">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" id="btnGuardarEdicion">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal para Agregar Activo -->
                <div class="modal fade" id="modalAgregarActivo" tabindex="-1" role="dialog" aria-labelledby="modalAgregarLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAgregarLabel">
                                    <i class="fas fa-search"></i> Seleccionar Activos Disponibles
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table id="tblSeleccionarActivos" class="table table-bordered table-striped w-100">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th>Sucursal</th>
                                                <th>Ambiente</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
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
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 mb-4" id="divlistadoMovimientos">
                            <form action="#" method="post" id="frmbusqueda">
                                <div class="row">
                                    <div class="col-md-12" id="divfiltros">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filtroTipo">Tipo Movimiento:</label>
                                                    <select class="form-control" name="filtroTipo" id="filtroTipo">
                                                        <option value="">Todos los tipos</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filtroFechaInicio">Fecha Inicio:</label>
                                                    <input type="date" class="form-control" name="filtroFechaInicio" id="filtroFechaInicio" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filtroFechaFin">Fecha Fin:</label>
                                                    <input type="date" class="form-control" name="filtroFechaFin" id="filtroFechaFin" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <button type="submit" class="btn btn-primary btn-block" id="btnlistar">
                                                        <i class="fa fa-search"></i> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-12" id="divtblmovimientos" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title" id="tituloTablaMovimientos">
                                        <i class="fa fa-clock"></i> Movimientos Pendientes
                                        <span class="badge badge-warning" id="contadorMovimientos">0</span>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="tblMovimientos" class="table table-bordered table-striped w-100 h-100">
                                                <thead class="table-warning">
                                                    <tr>
                                                        <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                        <th>Código</th>
                                                        <th>Tipo Movimiento</th>
                                                        <th>Sucursal Origen</th>
                                                        <th>Empresa Destino</th>
                                                        <th>Sucursal Destino</th>
                                                        <th>Autorizador</th>
                                                        <th>Receptor</th>
                                                        <th>Estado</th>
                                                        <th>Fecha Movimiento</th>
                                                        <th>Observaciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot class="table-warning">
                                                    <tr>
                                                        <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                        <th>Código</th>
                                                        <th>Tipo Movimiento</th>
                                                        <th>Sucursal Origen</th>
                                                        <th>Empresa Destino</th>
                                                        <th>Sucursal Destino</th>
                                                        <th>Autorizador</th>
                                                        <th>Receptor</th>
                                                        <th>Estado</th>
                                                        <th>Fecha Movimiento</th>
                                                        <th>Observaciones</th>
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
            </section>
            <?php require_once("../Layouts/Footer.php"); ?>
            <script src="editarMovimientos.js"></script>
        </div>
</body>

</html>