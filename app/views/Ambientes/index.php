<?php

session_start();

// ? VISTA PARA REVISAR, AGREGAR Y EDITAR AMBIENTES DE ACTIVOS POR SUCURSALES

// ? REVISION

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Ambiente - Sistema Gestion de Activos</title>
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
                            <h1>Revisión de Ambientes</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="#">Inicio</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Ambientes
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
                                    <h3 class="card-title"><i class="fa fa-list"></i>Listado de Ambientes en el sistema</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" id="divfiltros">
                                            <div class="row">
                                                <div class="col-md-3 offset-md-9">
                                                    <div class="form-group">
                                                        <input type="hidden" id="cod_empresa" value="<?php echo $_SESSION['cod_empresa'] ?? ''; ?>">
                                                        <input type="hidden" id="cod_UnidadNeg" value="<?php echo $_SESSION['cod_UnidadNeg'] ?? ''; ?>">
                                                        <button class="btn btn-primary btn-block" id="btnnuevo"><i class="fa fa-plus"></i>Nuevo</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="tblAmbientes" class="table table-bordered table-striped mt-4">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>IdAmbiente</th>
                                                            <th>Nombre</th>
                                                            <th>Descripcion</th>
                                                            <th>Sucursal</th>
                                                            <th>Estado</th>
                                                            <th><i class="fas fa-gears"></i></th>
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

                    <!-- MODAL PARA REGISTRAR AMBIENTES -->
                    <div class="modal fade" id="ModalAmbiente" tabindex="-1" role="dialog" aria-labelledby="ModalAmbienteLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form id="frmAmbiente" name="frmAmbiente" method="POST">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="tituloModalAmbiente"><i class="fa fa-plus-circle"></i> Registrar nuevo ambiente</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="idAmbiente" id="idAmbiente" value="0">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nombre">Nombre</label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="descripcion">Descripcion:</label>
                                                    <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="idEmpresa">Empresa:</label>
                                                    <select name="idEmpresa" id="idEmpresa" class="form-control select2"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="idSucursal">Sucursal:</label>
                                                    <select name="idSucursal" id="idSucursal" class="form-control select2"></select>
                                                </div>
                                            </div> -->
                                            <!-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="estado">Estado:</label>
                                                    <select class="form-control select2" id="estado" name="estado" required></select>
                                                </div>
                                            </div> -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="CodAmbiente">Abreviación:</label>
                                                    <input type="text" class="form-control" id="CodAmbiente" name="CodAmbiente">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" id="btnGuardarAmbiente" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="modal fade" id="ModalAmbiente" tabindex="-1" role="dialog" aria-labelledby="ModalLogAuditoriaLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header dragable_touch">
                            <h5 class="modal-title" id="ModalAmbienteLabel"><i class="fa fa-clock"></i> Historial Auditoria</h5>
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
        <script src="ambientes.js"></script>
    </div>
</body>

</html>