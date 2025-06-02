<?php

session_start();

// ? VISTA PARA REVISAR AUDITORIAS DE ACTIVOS POR SUCURSALES

// ? REVISION

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Auditoria - Sistema Gestion de Activos</title>
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
                            <h1>Revisión de Auditorias</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="#">Inicio</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Auditoria
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fa fa-list"></i>Lista de Logs</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" id="divfiltros">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="filtroCodigo">Codigo:</label>
                                                        <input class="form-control" name="filtroCodigo" id="filtroCodigo">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="filtroAccion">Accion:</label>
                                                        <select class="form-control" name="filtroAccion" id="filtroAccion"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="filtroTablas">Tablas:</label>
                                                        <select class="form-control" name="filtroTablas" id="filtroTablas"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="filtroFecha">Fecha Registro:</label>
                                                        <input type="date" class="form-control" name="filtroFecha" id="filtroFecha" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 offset-md-8">
                                                    <div class="form-group mb-0">
                                                        <label for="">&nbsp;</label>
                                                        <button type="submit" class="btn btn-primary btn-sm btn-block" id="btnlistar">
                                                            <i class="fa fa-search"></i> Buscar
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-md-2">
                                                    <div class="form-group mb-0">
                                                        <label for="">&nbsp;</label>
                                                        <button type="button" class="btn btn-success btn-sm btn-block" id="btnnuevo">
                                                            <i class="fa fa-plus"></i> Nuevo
                                                        </button>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="tblAuditorias" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th><i class="fa fa-cogs"></i></th>
                                                            <th>Id. Log</th>
                                                            <th>Cod Usuario</th>
                                                            <th>Nombre</th>
                                                            <th>Accion</th>
                                                            <th>Tabla</th>
                                                            <th>Id Ultimo Reg.</th>
                                                            <th>Fecha</th>
                                                            <th>Detalle</th>
                                                        </tr>
                                                    </thead>
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

            <div class="modal fade" id="ModalLogAuditoria" tabindex="-1" role="dialog" aria-labelledby="ModalLogAuditoriaLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header dragable_touch">
                            <h5 class="modal-title" id="ModalLogAuditoriaLabel"><i class="fa fa-clock"></i> Historial Auditoria</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="timeline timeline-inverse" id="timedata">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once("../Layouts/Footer.php"); ?>
        <script src="auditoria.js"></script>
    </div>
</body>

</html>