<?php

session_start();

// ? VISTA PARA LA CONFIGURACIÓN DEL SISTEMA, CREACIÓN DE NUEVAS COLUMNAS EN LAS TABLAS

// ? MANTENIMIENTO
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Movimientos - Sistema Gestion de activos</title>
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
                            <h1>Configuración del sistema</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="#">Inicio</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Configuración
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Modal para Ver Eventos -->
                <div class="modal fade" id="modalVerEventos" tabindex="-1" role="dialog" aria-labelledby="modalVerEventosLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalVerEventosLabel">
                                    <i class="fas fa-calendar-alt"></i> Eventos del Activo
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="idActivoEvento">ID Activo:</label>
                                            <input type="text" class="form-control" id="idActivoEvento" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombreActivoEvento">Nombre del Activo:</label>
                                            <input type="text" class="form-control" id="nombreActivoEvento" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="estadoEditable">Estado Editable:</label>
                                            <div class="d-flex align-items-center">
                                                <span id="estadoEditableBadge" class="badge mr-2"></span>
                                                <button type="button" class="btn btn-sm btn-warning" id="btnToggleEditable">
                                                    <i class="fas fa-lock"></i> Cambiar Estado
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Usuario que modificó:</label>
                                            <input type="text" class="form-control" id="usuarioModifico" readonly>
                                        </div>
                                    </div> -->
                                </div>
                                <hr>
                                <!-- <h6><i class="fas fa-history"></i> Historial de Eventos</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered" id="tblHistorialEventos">
                                        <thead class="table-info">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Evento</th>
                                                <th>Usuario</th>
                                                <th>Detalles</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div> -->
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
                        <div class="col-md-10 offset-md-1 mb-4" id="divlistadoactivos">
                            <form action="#" method="post" id="frmbusqueda">
                                <div class="row">
                                    <div class="col-md-12" id="divfiltros">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filtroEmpresa">Empresa:</label>
                                                    <select class="form-control" name="filtroEmpresa" id="filtroEmpresa"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filtroSucursal">Unidad de Neg. :</label>
                                                    <select name="filtroSucursal" id="filtroSucursal" class="form-control"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="filtroAmbiente">Ambiente:</label>
                                                    <select class="form-control" name="filtroAmbiente" id="filtroAmbiente"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="filtroCategoria">Categoria de Activo:</label>
                                                    <select class="form-control" name="filtroCategoria" id="filtroCategoria"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="filtroEstado">Estado de activos:</label>
                                                    <select class="form-control" name="filtroEstado" id="filtroEstado"></select>
                                                </div>
                                            </div>
                                            <!--<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="filtroFecha">Fecha Registro:</label>
                                                        <input type="date" class="form-control" name="filtroFecha" id="filtroFecha" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>
                                                </div>-->
                                            <div class="col-md-2 offset-md-6">
                                                <div class="form-group mb-0">
                                                    <label>&nbsp;</label>
                                                    <button type="submit" class="btn btn-primary btn-sm btn-block" id="btnlistar">
                                                        <i class="fa fa-search"></i> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-12" id="divtblRegistros" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fa fa-list-check"></i> Lista de Activos Registrados</h3>
                                </div>
                                <div class="dataTables_wrapper dt-bootstrap4">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class=""></div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="tblRegistros" class="table table-bordered table-striped mt-4 table-hover">
                                                    <thead class="table-success">
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Código</th>
                                                            <th>Código Antiguo</th>
                                                            <th>Nombre</th>
                                                            <th>Id Estado</th>
                                                            <th>Estado</th>
                                                            <th>Id Categoria</th>
                                                            <th>Id Empresa</th>
                                                            <th>Id Sucursal</th>
                                                            <th>Id Ambiente</th>
                                                            <th>Ubicación</th>
                                                            <th>Id. Responsable</th>
                                                            <th>Asignado a</th>
                                                            <th>Serie</th>
                                                            <th>Valor</th>
                                                            <th>F. Ingreso</th>
                                                            <th>Editable</th>
                                                            <th><i class="fas fa-cogs"></i></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot class="table-success">
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Código</th>
                                                            <th>Código Antiguo</th>
                                                            <th>Nombre</th>
                                                            <th>Id Estado</th>
                                                            <th>Estado</th>
                                                            <th>Id Categoria</th>
                                                            <th>Id Empresa</th>
                                                            <th>Id Sucursal</th>
                                                            <th>Id Ambiente</th>
                                                            <th>Ubicación</th>
                                                            <th>Id. Responsable</th>
                                                            <th>Asignado a</th>
                                                            <th>Serie</th>
                                                            <th>Valor</th>
                                                            <th>F. Ingreso</th>
                                                            <th>Editable</th>
                                                            <th><i class="fas fa-cogs"></i></th>
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
                </div>
            </section>
            <?php require_once("../Layouts/Footer.php"); ?>
            <script src="configuracion.js"></script>
        </div>
</body>

</html>