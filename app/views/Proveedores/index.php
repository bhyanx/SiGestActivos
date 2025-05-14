<?php

session_start();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php") ?>

    <title>Proveedores - Sistema Gestion de Activos</title>
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
                            <h1>Administración de Proveedores</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li class="breadcrumb-item active">Proveedores</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12" id="divlistadofichas">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fa fa-list"></i> Lista de Registros</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" id="divfiltros">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="">F. Inicio</label>
                                                        <input type="date" class="form-control" value="<?php echo date('Y-m-01'); ?>" id="pFechaInicial" name="pFechaInicial">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="">F. Fin</label>
                                                        <input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" id="pFechaFinal" name="pFechaFinal">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="">&nbsp;</label>
                                                        <button type="button" class="btn btn-primary btn-block" id="btnbuscar">
                                                            <i class="fa fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="divtblarticulos">
                                            <div class="table-responsive">
                                                <table id="tblrequerimientos" class="table table-bordered table-striped w-100">
                                                    <thead>
                                                        <tr>
                                                            <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                            <th>#</th>
                                                            <th>F. Registro</th>
                                                            <th>Cod.</th>
                                                            <th>Tipo</th>
                                                            <th>Doc. Desp</th>
                                                            <th>Empresa</th>
                                                            <th>Und. Neg.</th>
                                                            <th>Solicitante</th>
                                                            <th>Observaciones</th>
                                                            <th>Estado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                            <th>#</th>
                                                            <th>F. Registro</th>
                                                            <th>Cod.</th>
                                                            <th>Tipo</th>
                                                            <th>Doc. Desp</th>
                                                            <th>Empresa</th>
                                                            <th>Und. Neg.</th>
                                                            <th>Solicitante</th>
                                                            <th>Observaciones</th>
                                                            <th>Estado</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
            </section>
        </div> <!-- Cierre de content-wrapper -->
    </div> <!-- Cierre de wrapper -->
    <?php require_once "../Layouts/Footer.php"; ?>

</body>

</html>