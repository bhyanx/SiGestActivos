<?php
    require_once("../config/configuracion.php");
    if(!isset($_SESSION)){
?>
<!DOCTYPE html>
<html>
<head lang="es">
    <?php
        require_once("MainHead/head.php")
    ?>
  <title>SIS Soporte::Page Not Found</title>
</head>
<body class="sidebar-mini sidebar-collapse sidebar-closed">

    <div class="wrapper">

      <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="../public/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="margin-left: 0 !important;">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <!-- <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li> -->
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="Home/" class="nav-link">Inicio</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="Login/" class="nav-link">Login</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
            </ul>
        </nav>
        <!-- /.navbar -->


        <div class="content-wrapper" style="margin-left: 0 !important;">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Página no encontrada</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="Home/">Home</a></li>
                        <li class="breadcrumb-item active">Página no encontrada</li>
                        </ol>
                    </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="error-page">
                    <h2 class="headline text-warning"> 404</h2>

                    <div class="error-content">
                        <h3>
                            <i class="fas fa-exclamation-triangle text-warning"></i> ¡Ups! Página no encontrada.
                        </h3>
                        <p>
                            No pudimos encontrar la página que estabas buscando. Mientras tanto, puedes
                            <a href="Home/">retornar al inicio</a>.
                        </p>
                    </div>
                    <!-- /.error-content -->
                </div>
                <!-- /.error-page -->
            </section>
            <!-- /.content -->
        </div>

        <footer class="main-footer" style="margin-left: 0 !important;">
            <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2022 <a href="#">Julem Cabanillas</a>.</strong> Todos los derechos reservados.
        </footer>
    </div>

<?php require_once("../javascript/mainjs.php") ?>
</body>
</html>
<?php
  }else {
    header("Location:".Conectar::ruta());
    exit();
  }
?>