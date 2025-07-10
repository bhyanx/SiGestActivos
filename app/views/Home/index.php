<?php
require_once("../../config/configuracion.php");
if (!isset($_SESSION["IdRol"])) {
    header("Location:" . Conectar::ruta());
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Inicio | Sistema Activos</title>
</head>

<body class="sidebar-mini control-sidebar-slide-open layout-navbar-fixed layout-fixed sidebar-mini-xs sidebar-mini-md sidebar-collapse">

    <div class="wrapper">

        <?php require_once("../Layouts/Head-Body.php") ?>

        <?php require_once("../Layouts/SideBar.php") ?>4

        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Inicio</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li class="breadcrumb-item active">Inicio</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-6 col-lg-3 col-xl-2">
                        <div class="card shadow">
                            <div class="card-body">
                                <img class='img-fluid w-100' src="<?php echo Conectar::ruta(); ?>public/img/Logo-Lubriseng.png" alt="" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 col-xl-2">
                        <div class="small-box bg-success shadow">
                            <div class="inner">
                                <h3 id="lblcantidadactivos">0</h3>
                                <p>Total Activos</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 col-xl-2">
                        <div class="small-box bg-primary shadow">
                            <div class="inner">
                                <h3 id="lblcantidadoperativos">0</h3>
                                <p>Activos Operativos</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 col-xl-2">
                        <div class="small-box bg-warning shadow">
                            <div class="inner">
                                <h3 id="lblcantidadactivosmantenimiento">0</h3>
                                <p>Activos Mantenimiento</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-gears"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 col-xl-2">
                        <div class="small-box bg-danger shadow">
                            <div class="inner">
                                <h3 id="lblcantidadactivosbaja">0</h3>
                                <p>Activos Baja</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-ban"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6" id="divtblRegistros">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> Lista de Activos Registrados</h3>
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
                                                    <th>#</th>
                                                    <th>Id Activo</th>
                                                    <th>Código</th>
                                                    <th>Serie</th>
                                                    <th>Descripción</th>
                                                    <th>Marca</th>
                                                    <th>Sucursal</th>
                                                    <th>Proveedor</th>
                                                    <th>Estado</th>
                                                    <th>Valor</th>
                                                    <th>Responsable</th>
                                                    <th>Nombre Res.</th>
                                                    <th>Art. Relacionados</th>
                                                    <th>Act. Relacionados</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Id Activo</th>
                                                    <th>Código</th>
                                                    <th>Serie</th>
                                                    <th>Descripción</th>
                                                    <th>Marca</th>
                                                    <th>Sucursal</th>
                                                    <th>Proveedor</th>
                                                    <th>Estado</th>
                                                    <th>Valor</th>
                                                    <th>Responsable</th>
                                                    <th>Nombre Res.</th>
                                                    <th>Art. Relacionados</th>
                                                    <th>Act. Relacionados</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </section>
            <!-- /.content -->

        </div>

    </div>

    <?php require_once "../Layouts/Footer.php"; ?>
    <script src="home.js"></script>

</body>

</html>