<?php 
    // TODO: VALIDAR MENU Y PERSMISO DE USUSARIO
    $tienepermiso = false;

    $rutaactual = basename(getcwd());

    if(!isset($_SESSION['permisos'])){
        header('Location: '.Conectar::ruta().'Logout/logout.php');
        die();
    }else{
        foreach ($_SESSION['permisos'] as $permiso) {
            if ($permiso['MenuIdentificador'] == $rutaactual) {
                $tienepermiso = true;
            }
        }
    }

  
?>
<input type="hidden" name="vgcodEmpresa" id="vgcodEmpresa" value="<?= $_SESSION['vgcodEmpresa'] ?>">
<input type="hidden" name="vgcodUnidadNeg" id="vgcodUnidadNeg" value="<?= $_SESSION['vgcodUnidadNeg'] ?>">
<input type="hidden" name="vgUnidadNeg" id="vgUnidadNeg" value="<?= $_SESSION['vgUnidadNeg'] ?>">
<input type="hidden" name="vgTasaIGVCV" id="vgTasaIGVCV" value="<?= $_SESSION['vgTasaIGVCV'] ?>">
<input type="hidden" name="vgtccompra" id="vgtccompra" value="<?= $_SESSION['vgtccompra'] ?>">
<input type="hidden" name="vgtcventa" id="vgtcventa" value="<?= $_SESSION['vgtcventa'] ?>">
<input type="hidden" name="vgCodCaja" id="vgCodCaja" value="<?= $_SESSION['vgCodCaja'] ?>">
<input type="hidden" name="vgcodTurnoCaja" id="vgcodTurnoCaja" value="<?= $_SESSION['vgcodTurnoCaja'] ?>">
<input type="hidden" name="vgcodAlmacen" id="vgcodAlmacen" value="<?= $_SESSION['vgcodAlmacen'] ?>">
<input type="hidden" name="vgAlmacen" id="vgAlmacen" value="<?= $_SESSION['vgAlmacen'] ?>">

<!-- Preloader -->
<?php if(empty($_SESSION['vgcodEmpresa'])){ ?>  
    <div class="preloader flex-column justify-content-center align-items-center">
        <!-- <img class="animation__shake" src="../../public/img/logo-<?= $_SESSION['vgcodEmpresa'] ?>.png" alt="AdminLTELogo" style="width: 50%"> -->
        <i class="fas fa-5x fa-sync-alt"></i>
        
    </div>
<?php }else{ ?>
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="../../public/img/logo-<?= $_SESSION['vgcodEmpresa'] ?>.png" alt="AdminLTELogo" style="width: 50%">
        <h3 id="textpreloader"></h3>
        <i class="fas fa-2x fa-sync-alt iconloader" id="iconloader"></i> 
    </div>
<?php } ?>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark <?= $_SESSION['TemaEmpresa'] ?> border-bottom-0">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <span class="nav-link" id="tcambiolabel">T.C. Compra: <?php echo $_SESSION['vgtccompra']; ?>, Venta: <?php echo $_SESSION["vgtcventa"]; ?> - SUNAT</span>
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
                <input type="hidden" name="CodUsuario" id="CodUsuario" value="<?= $_SESSION["cod_user"] ?>">
                <input type="hidden" name="CodEmpleado" id="CodEmpleado" value="<?= $_SESSION["CodEmpleado"] ?>">
                <input type="hidden" name="NomEmpleado" id="NomEmpleado" value="<?= $_SESSION["Empleado"] ?>">

                <div class="dropdown-divider"></div>
                <!-- <a href="#" class="dropdown-item">
                    <i class="fas fa-address-card"></i> Perfil
                </a> -->
                <div class="dropdown-divider"></div>
                <a href="../Logout/logout.php" class="dropdown-item dropdown-footer"><i class="fas fa-sign-out-alt"></i> Cerrar Sessión</a>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->