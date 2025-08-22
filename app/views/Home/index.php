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
                <!-- Gráfico de activos asignados y no asignados -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-header bg-primary">
                                <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Distribución de Activos</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoActivosAsignados" height="300"></canvas>
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