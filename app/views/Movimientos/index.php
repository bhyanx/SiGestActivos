<?php
session_start();

// ? VISTA PARA GESTIONAR LOS MOVIMIENTOS DE LOS ACTIVOS

//! FALTA FUNCIONAMIENTO Y PONER CAMPOS REFERENCIALES A LOS MOVIMIENTOS A NUESTRA VISTA

?>
<!DOCTYPE html>
<html lang="es">

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
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Gestion de Movimientos en Activos</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li
                                    class="breadcrumb-item active">Movimientos</li>
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
                                    <h3 class="card-title"><i class="fa fa-list"></i>
                                        Lista de Movimientos Realizados
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" id="divfiltros">
                                            <div class="row">
                                                <div class="col-md-3 offset-md-9">
                                                    <div class="form-group">
                                                        <button class="btn btn-primary btn-block" id="btnnuevo"><i class="fa fa-plus"></i> Crear Movimiento</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="tblMovimientos" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th><i class="fa fa-cogs"></i></th>
                                                            <th>Id DetalleMovimiento</th>
                                                            <th>Id Activo</th>
                                                            <!-- <th>Código</th>
                                                            <th>Serie</th> -->
                                                            <th>Nombre Activo</th>
                                                            <th>Tipo Movimiento</th>
                                                            <th>Sucursal Anterior</th>
                                                            <th>Sucursal Nueva</th>
                                                            <th>Ambiente Anterior</th>
                                                            <th>Ambiente Nueva</th>
                                                            <th>Autorizador</th>
                                                            <th>Responsable Anterior</th>
                                                            <th>Responsable Nueva</th>
                                                            <th>Fecha Movimiento</th>
                                                            <th>Responsable Origen</th>
                                                            <th>Responsable Destino</th>
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
                    <!-- Modal para Registrar/Editar Movimiento -->
                    <div class="modal fade" id="ModalMovimiento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form id="frmMovimiento" name="frmMovimiento" method="POST">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="tituloModalMovimiento"><i class="fa fa-plus-circle"></i> Nuevo Movimiento</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="idMovimiento" id="idMovimiento" value="0">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="IdTipo">Tipo de Movimiento:</label>
                                                    <select class="form-control select2" id="IdTipo" name="IdTipo" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="autorizador">Autorizador:</label>
                                                    <select class="form-control select2" id="autorizador" name="autorizador" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sucursal_origen">Sucursal Origen:</label>
                                                    <select class="form-control select2" id="sucursal_origen" name="sucursal_origen" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sucursal_destino">Sucursal Destino:</label>
                                                    <select class="form-control select2" id="sucursal_destino" name="sucursal_destino" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="observacion">Observaciones:</label>
                                                    <textarea class="form-control" id="observacion" name="observacion" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Activos a Mover:</label><br>
                                                    <select class="form-control select2" id="activos" name="activos[]" multiple="multiple" required></select>
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
        <script src="movimiento.js"></script>
    </div>
</body>

</html>