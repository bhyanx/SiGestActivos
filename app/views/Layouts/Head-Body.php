<?php
ob_start();
// Validar menu y permisos de usuario
if (!isset($_SESSION['Permisos'])) {
    ob_end_clean();
    header('Location: /app/views/Logout/');
    exit();
}

$tienePermiso = false;
$rutaActual = basename(getcwd());

foreach ($_SESSION['Permisos'] as $permiso) {
    if ($permiso['MenuIdentificador'] == $rutaActual) {
        $tienePermiso = true;
        break;
    }
}

if (!$tienePermiso) {
    ob_end_clean();
    header('Location: /Home/');
    exit();
}
?>
<!-- Preloader -->

<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="/public/img/Logo-Lubriseng.png" alt="AdminLTELogo" style="width: 50%">
    <i class="fas fa-5x fa-sync-alt"></i>

</div>

<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="/public/img/Logo-Lubriseng.png" alt="AdminLTELogo" style="width: 50%">
    <h3 id="textpreloader"></h3>
    <i class="fas fa-2x fa-sync-alt iconloader" id="iconloader"></i>
</div>


<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark border-bottom-0">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fas fa-user-cog"></i>
            <!-- <span class="badge badge-warning navbar-badge">15</span> -->
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

            <!-- CODEMPLEADO/CODUSUARIO CREADO POR SESSION -->
            <span class="dropdown-item dropdown-header"><?php echo $_SESSION["CodUsuario"] ?></span>

            <div class="dropdown-divider"></div>

            <input type="hidden" name="CodUsuario" id="CodUsuario" value="<?php echo $_SESSION["CodUsuario"]; ?>">


            <div class="dropdown-divider"></div>
            <a href="../Logout/" class="dropdown-item dropdown-footer"><i class="fas fa-sign-out-alt"></i> Cerrar Sessión</a>
        </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
<?php
ob_end_flush();
?>