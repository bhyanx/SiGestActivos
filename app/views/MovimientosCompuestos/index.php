<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Movimientos entre Activos - Sistema Gestion de activos</title>
    <style>
        .swal2-container {
            z-index: 9999 !important;
        }

        .dropdown-menu .show {
            position: fixed !important;
        }
    </style>
</head>

<body class="sidebar-mini control-sidebar-slide-open layout-navbar-fixed layout-fixed sidebar-mini-xs sidebar-mini-md sidebar-collapse">
    <div class="wrapper">
        <?php require_once("../Layouts/Head-Body.php"); ?>
        <?php require_once("../Layouts/SideBar.php"); ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Gestion de Movimientos entre Activos</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li class="breadcrumb-item active">Movimientos entre Activos</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <!-- Contenedor principal -->
                    <div class="row">
                        <div class="col-md-10 offset-md-1 mb-4" id="divlistadomovimientos">
                            <form action="#" method="post" id="frmbusqueda">
                                <div class="row">
                                    <div class="col-md-12" id="divfiltros">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filtroSucursal">Sucursal:</label>
                                                    <select class="form-control" name="filtroSucursal" id="filtroSucursal" disabled></select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="filtroFechaInicio">Fecha Inicio Mov:</label>
                                                    <input type="date" class="form-control" name="filtroFechaInicio" id="filtroFechaInicio" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="filtroFechaFin">Fecha Fin Mov:</label>
                                                    <input type="date" class="form-control" name="filtroFechaFin" id="filtroFechaFin" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-2 offset-md-6">
                                                <div class="form-group mb-0">
                                                    <label for="">&nbsp;</label>
                                                    <button type="submit" class="btn btn-primary btn-sm btn-block" id="btnlistar">
                                                        <i class="fa fa-search"></i> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group mb-0">
                                                    <label for="">&nbsp;</label>
                                                    <button type="button" class="btn btn-success btn-sm btn-block" id="btnnuevo">
                                                        <i class="fas fa-route"></i> Nuevo Movimiento
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group mb-0">
                                                    <label for="">&nbsp;</label>
                                                    <button type="button" class="btn btn-info btn-sm btn-block" id="btnasignacion">
                                                        <i class="fas fa-boxes-stacked"></i> Asignar Componente
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-12" id="divformularioasignacion">
                            <div class="alert alert-info alert-dismissible">
                                <span id="lblSucursal"><?php echo $_SESSION['Nombre_local'] ?></span>
                                <button type="button" class="close btn" id="btnVolver">
                                    <i class="fas fa-undo-alt"></i>
                                </button>
                                <!-- <input type="hidden" name="IdAutorizador" id="IdAutorizador"> -->
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-file-alt"></i> Asignar Activo Padre :</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                    <i class="fas fa-expand"></i>
                                                </button>
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <div class="form-group">
                                                        <label for="IdAsignacionPadre">Seleccionar Activo Padre:</label>
                                                        <select class="form-control" name="IdAsignacionPadre" id="IdAsignacionPadre" required></select>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="SerieActivo">Serie Activo Padre:</label>
                                                        <input type="text" class="form-control" name="SerieActivo" id="SerieActivo" placeholder="Serie Activo Padre" required>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Activos a asignar</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-primary btn-sm" id="btnBuscarActivos">
                                                <i class="fas fa-search"></i> Buscar Activos
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- <div class="col-md-12 mb-3">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="buscarActivo" placeholder="Buscar activo por código, nombre o serie...">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" id="limpiarBusqueda">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div> -->
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table id="tbldetalleactivos" class="table table-hover table-bordered table-striped table-sm w-100">
                                                        <thead>
                                                            <tr>
                                                                <th>Código</th>
                                                                <th>Nombre</th>
                                                                <th>Marca</th>
                                                                <th>Serie</th>
                                                                <th>Observaciones</th>
                                                                <th>Acción</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <div class="card">
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                                <button type="button" class="btn btn-danger btn-sm btn-block" id="btnCancelarAsignacion">
                                                    <i class="fa fa-times"></i> Cerrar
                                                </button>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <button type="button" class="btn btn-primary btn-sm btn-block" id="btnGuardarAsignacion">
                                                    <i class="fa fa-check"></i> Guardar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12" id="divtblmovimientos" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fa fa-list-check"></i> Lista de Movimientos entre Activos</h3>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="tblMovimientos" class="table table-bordered table-striped w-100 h-100">
                                                <thead class="table-success">
                                                    <tr>
                                                        <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                        <th>Id DetalleMovimiento</th>
                                                        <th>Id Componente</th>
                                                        <th>Codigo Componente</th>
                                                        <th>Nombre Componente</th>
                                                        <th>Tipo de Mov.</th>
                                                        <th>Activo Padre Origen</th>
                                                        <th>Activo Padre Destino</th>
                                                        <th>Sucursal</th>
                                                        <th>Ambiente</th>
                                                        <th>Responsable</th>
                                                        <th>Fecha Movimiento</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot class="table-success">
                                                    <tr>
                                                        <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                        <th>Id DetalleMovimiento</th>
                                                        <th>Id Componente</th>
                                                        <th>Codigo Componente</th>
                                                        <th>Nombre Componente</th>
                                                        <th>Tipo de Mov.</th>
                                                        <th>Activo Padre Origen</th>
                                                        <th>Activo Padre Destino</th>
                                                        <th>Sucursal</th>
                                                        <th>Ambiente</th>
                                                        <th>Responsable</th>
                                                        <th>Fecha Movimiento</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12" id="divgenerarmov">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-building"></i> Generar Movimiento entre Activos:</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" id="divfiltros">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="CodAutorizador">Autorizador:</label>
                                                        <select name="CodAutorizador" id="CodAutorizador" class="form-control" required></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="IdSucursalOrigen">Sucursal Origen:</label>
                                                        <input type="text" class="form-control" name="IdSucursalOrigen" id="IdSucursalOrigen" readonly>
                                                        <input type="hidden" name="IdSucursalOrigenValor" id="IdSucursalOrigenValor">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="IdSucursalDestino">Sucursal Destino:</label>
                                                        <select name="IdSucursalDestino" id="IdSucursalDestino" class="form-control" required></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 offset-md-8">
                                                    <div class="form-group">
                                                        <label for="">&nbsp;</label>
                                                        <button type="button" class="btn btn-danger btn-block" id="btncancelarempresa">
                                                            <i class="fas fa-reply"></i> Cancelar
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="">&nbsp;</label>
                                                        <button type="button" class="btn btn-primary btn-block" id="btnprocesarempresa">
                                                            <i class="fas fa-sync"></i> Procesar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12" id="divregistroMovimiento">
                        <div class="alert alert-info alert-dismissible">
                            <span id="lbldatossucmovimiento"></span>
                            <span id="lblautorizador"></span>
                            <button type="button" class="close btn" id="btnVolver">
                                <i class="fas fa-undo-alt"></i>
                            </button>
                            <input type="hidden" name="IdAutorizador" id="IdAutorizador">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-file-alt"></i> Activo Padre Origen:</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="IdActivoPadreOrigen">Seleccionar Activo Padre:</label>
                                                    <select class="form-control" name="IdActivoPadreOrigen" id="IdActivoPadreOrigen" required></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-file-alt"></i> Activo Padre Destino:</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="IdActivoPadreDestino">Seleccionar Activo Padre:</label>
                                                    <select class="form-control" name="IdActivoPadreDestino" id="IdActivoPadreDestino" required></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Componentes a Mover</h3>
                                    <div class="card-tools">
                                        <!-- <button type="button" class="btn btn-primary btn-sm" id="btnBuscarComponentes">
                                            <i class="fas fa-search"></i> Buscar Componentes
                                        </button> -->
                                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                            <i class="fas fa-expand"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="tbldetallecomponentes" class="table table-hover table-bordered table-striped table-sm">
                                                    <thead class="text-center table-success">
                                                        <tr>
                                                            <th>Código</th>
                                                            <th>Nombre</th>
                                                            <th>Marca</th>
                                                            <th>Serie</th>
                                                            <th>Observaciones</th>
                                                            <th>Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="card">
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                                            <button type="button" class="btn btn-danger btn-sm btn-block" id="btnCancelarMovComp">
                                                <i class="fa fa-times"></i> Cerrar
                                            </button>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <button type="button" class="btn btn-primary btn-sm btn-block" id="btnGuardarMov">
                                                <i class="fa fa-check"></i> Guardar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php require_once("../Layouts/Footer.php"); ?>
        <script src="movimientoscomponentes.js"></script>
    </div>

    <!-- Modal de Búsqueda de Componentes -->
    <!-- <div class="modal fade" id="modalBuscarComponentes" tabindex="-1" role="dialog" aria-labelledby="modalBuscarComponentesLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBuscarComponentesLabel">Buscar Componentes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tblComponentes" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Marca</th>
                                            <th>Serie</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Modal de Búsqueda de Activos -->
    <div class="modal fade" id="modalBuscarActivos" tabindex="-1" role="dialog" aria-labelledby="modalBuscarActivosLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="modalBuscarActivosLabel">Buscar Activos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tblActivos" class="table table-bordered table-striped">
                                    <thead class="text-center table-success">
                                        <tr>
                                            <th>ID</th>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Estado</th>
                                            <th>Serie</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <?php require_once("../Layouts/Footer.php"); ?>
    <script src="movimientoscomponentes.js"></script>
</body>

</html>
