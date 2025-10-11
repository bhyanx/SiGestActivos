<?php

session_start();
require_once("../../config/configuracion.php");

if (!isset($_SESSION["IdRol"])) {
    header("Location: " . Conectar::ruta());
    //header("Location: " . Conectar::rutaLocal());
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Menu - Sistema Gestion de Activos</title>
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
                            <h1>Menu</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="#">Inicio</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Menu
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
                                    <h3 class="card-title"><i class="fa fa-list"></i>Listado de Menu en el sistema</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" id="divfiltros">
                                            <div class="row">
                                                <div class="col-md-3 offset-md-9">
                                                    <div class="form-group">
                                                        <button class="btn btn-primary btn-block" id="btnnuevo"><i class="fa fa-plus"></i>Nuevo</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="tblMenu" class="table table-bordered table-striped mt-4">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Cod. Menu</th>
                                                            <th>Nombre Menu</th>
                                                            <th>Menu Ruta</th>
                                                            <th>Menu Identificador</th>
                                                            <th>Menu Icono</th>
                                                            <th>Menu Grupo</th>
                                                            <th>Menu Grupo Icono</th>
                                                            <th>Estado</th>
                                                            <th><i class="fa fa-cogs"></i></th>
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

                    <!-- MODAL PARA REGISTRAR ROLES -->
                    <div class="modal fade" id="ModalMenu" tabindex="-1" role="dialog" aria-labelledby="ModalMenuLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form id="frmMenu" name="frmMenu" method="POST">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="tituloModalMenu"><i class="fa fa-plus-circle"></i> Registrar nuevo menu</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="idRol" id="idRol" value="0">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="idMenu">Codigo de menu</label>
                                                    <select class="form-control select2" id="idMenu" name="idMenu" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="menu">Nombre Menu:</label>
                                                    <select class="form-control select2" id="menu" name="menu" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="estado">Estado:</label>
                                                    <select class="form-control select2" id="estado" name="estado" required></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php require_once("../Layouts/Footer.php"); ?>
        <script src="menu.js"></script>
    </div>
</body>

</html>