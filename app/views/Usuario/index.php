<?php

session_start();

// ? VISTA PARA EL CONTROL DE USUARIOS 

// ? MANTENIMIENTO DE USUARIOS
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Usuarios - Sistema Gestion de Activos</title>
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
                            <h1>Administración de Usuarios</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="#">Inicio</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Usuarios
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
                                    <h3 class="card-title"><i class="fa fa-list"></i>Lista de Usuarios</h3>
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
                                                <table id="tblUsuarios" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th><i class="fa fa-cogs"></i></th>
                                                            <th>DNI</th>
                                                            <th>Nombres</th>
                                                            <th>Apellidos</th>
                                                            <th>Rol</th>
                                                            <th>Clave</th>
                                                            <th>Estado</th>
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

                    <!-- MODAL PARA REGISTRAR USUARIOS -->
                     <div class="modal fade" id="ModalUsuarios" tabindex="-1" role="dialog" aria-labelledby="ModalUsuariosLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form id="frmUsuarios" name="frmUsuario" method="POST">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="tituloModalUsuario"><i class="fa fa-plus-circle"></i> Nuevo Movimiento</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="idUsuario" id="idUsuario" value="0">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="IdUsuario">Codigo de Usuario:</label>
                                                    <select class="form-control select2" id="IdUsuario" name="IdUsuario" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="rol">Rol:</label>
                                                    <select class="form-control select2" id="idrol" name="idrol" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="clave">Clave de Acceso:</label>
                                                    <select class="form-control select2" id="clave" name="clave" required></select>
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
        <script src="usuarios.js"></script>
    </div>
</body>

</html>