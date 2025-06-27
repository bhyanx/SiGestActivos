<?php
ob_start();
// Validar menu y permisos de usuario
if (!isset($_SESSION['Permisos'])) {
    ob_end_clean();
    header('Location: ../views/Logout/');
    exit();
}

$tienePermiso = false;
$rutaActual = basename(getcwd());

// Obtener el identificador del menú actual
$identificadorActual = '';
foreach ($_SESSION['Permisos'] as $permiso) {
    if (strpos($permiso['MenuRuta'], $rutaActual) !== false) {
        $identificadorActual = $permiso['MenuIdentificador'];
        break;
    }
}

// Verificar si el usuario tiene permiso para el identificador actual
foreach ($_SESSION['Permisos'] as $permiso) {
    if ($permiso['MenuIdentificador'] === $identificadorActual && $permiso['Permiso'] == 1) {
        $tienePermiso = true;
        break;
    }
}

if (!$tienePermiso) {
    ob_end_clean();
    header('Location: /app/views/Home/');
    exit();
}
?>
<!-- Preloader -->

<div class="preloader flex-column justify-content-center align-items-center">
    <img class="rotate-loader" src="../../../public/img/Logo-Lubriseng.png" alt="AdminLTELogo" style="width: 50%">
    <i class="fas fa-5x fa-sync-alt"></i>
</div>

<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="../../../public/img/Logo-Lubriseng.png" alt="AdminLTELogo" style="width: 50%">
    <h3 id="textpreloader"></h3>
    <i class="fas fa-2x fa-sync-alt iconloader" id="iconloader"></i>
</div>


<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light"> <!-- SI EL COLOR NO SE LEE BIEN, USAR CLASES -->
    <!-- Left navbar links -->

    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-success font-weight-bold" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>


    <div class="user-panel d-flex">
        <div class="info">
            <span class="text-success text-sm font-weight-bold">
                <i class="fa fa-user"></i>
                <?php
                if (isset($_SESSION["NombreTrabajador"]) && !empty($_SESSION["NombreTrabajador"])) {
                    echo $_SESSION["NombreTrabajador"];
                } else if (isset($_SESSION["PrimerNombre"]) && isset($_SESSION["ApellidoPaterno"])) {
                    $nombre = $_SESSION["PrimerNombre"];
                    if (!empty($_SESSION["SegundoNombre"])) {
                        $nombre .= " " . $_SESSION["SegundoNombre"];
                    }
                    $nombre .= " " . $_SESSION["ApellidoPaterno"];
                    if (!empty($_SESSION["ApellidoMaterno"])) {
                        $nombre .= " " . $_SESSION["ApellidoMaterno"];
                    }
                    echo $nombre;
                } else {
                    echo $_SESSION["CodUsuario"];
                }
                ?>
                <?php
                $empresa = $_SESSION["Razon_empresa"] ?? "";
                $unidad = $_SESSION["Nombre_local"] ?? "";
                if ($empresa) {
                    echo "  |  <i class='fa fa-building'>  </i> <span class='text-sm'>" . $empresa . "</span>";
                }
                if ($unidad) {
                    echo "  |  <i class='fa fa-city'>  </i> <span class='text-sm'>" . $unidad . "</span>";
                }
                ?>
            </span>
        </div>
    </div>


    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown text-light">
        <li class="nav-item">
            <a class="nav-link text-success" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <a class="nav-link text-success" data-toggle="dropdown" href="#">
            <i class="fas fa-user-cog"></i>
            <!-- <span class="badge badge-warning navbar-badge">15</span> -->
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

            <!-- CODEMPLEADO/CODUSUARIO CREADO POR SESSION -->
            <span class="dropdown-item dropdown-header"><?php echo $_SESSION["CodUsuario"] ?></span>

            <div class="dropdown-divider"></div>

            <input type="hidden" name="CodUsuario" id="CodUsuario" value="<?php echo $_SESSION["CodUsuario"]; ?>">


            <div class="dropdown-divider"></div>
            <a href="../../../app/views/Logout/" class="dropdown-item dropdown-footer"><i class="fas fa-sign-out-alt"></i> Cerrar Sessión</a>
        </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
<?php
ob_end_flush();
?>