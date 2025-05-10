<?php
ob_start();
require_once '../../config/configuracion.php';
require_once '../../models/Usuarios.php';

if (!isset($_SESSION['IdRol'])) {
    header('Location: /app/views/Login/');
    exit();
}

$usuario = new Usuarios();
$data = $usuario->leerMenuGrupo($_SESSION['IdRol']);
$datapermisos = $usuario->leerMenuRol($_SESSION['IdRol']);

// Función para validar la ruta actual
function isCurrentRoute($menuRuta) {
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return $currentPath === $menuRuta;
}
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4 sidebar-dark-green">
    <!-- Brand Logo -->
    <a href="/app/views/Home/" class="brand-link">
        <img src="/public/img/Page-Lubriseng.png" alt="Logo Sistema EPPS" class="brand-image elevation-3" style="opacity: .8">
        <span class="brand-text font-weight text-sm"></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fa fa-user fa-lg text-light" style="padding-left: 7px"></i>
            </div>
            <div class="info">
                <span class="d-block text-light text-sm"></span>
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
                </span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php
                if (is_array($data) && !empty($data)) {
                    foreach ($data as $menugrupo) {
                        $hasActiveChild = false;
                        if (is_array($datapermisos)) {
                            foreach ($datapermisos as $permiso) {
                                if ($menugrupo['MenuGrupo'] == $permiso['MenuGrupo'] && $permiso['Permiso'] == 1) {
                                    if (isCurrentRoute($permiso['MenuRuta'])) {
                                        $hasActiveChild = true;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        echo '<li class="nav-item'.($hasActiveChild ? ' menu-open' : '').'">
                                <a href="#" class="nav-link'.($hasActiveChild ? ' active' : '').'" id="'.$menugrupo['MenuGrupo'].'">
                                    <i class="nav-icon fas '.$menugrupo['MenuGrupoIcono'].'"></i>
                                    <p> '.$menugrupo['MenuGrupo'].' <i class="right fas fa-angle-left"></i> </p>
                                </a>
                                <ul class="nav nav-treeview">';
                        if (is_array($datapermisos)) {
                            foreach ($datapermisos as $permiso) {                            
                                if ($menugrupo['MenuGrupo'] == $permiso['MenuGrupo'] && $permiso['Permiso'] == 1) {
                                    $isActive = isCurrentRoute($permiso['MenuRuta']);
                                    echo '<li class="nav-item">
                                            <a href="'.$permiso['MenuRuta'].'" class="nav-link'.($isActive ? ' active' : '').'" id="'.$permiso['MenuIdentificador'].'">
                                                <i class="fas '.$permiso['MenuIcono'].' nav-icon"></i>
                                                <p>'.$permiso['NombreMenu'].'</p>
                                            </a>
                                        </li>';
                                }
                            }
                        }
                        echo '</ul>
                            </li>';                   
                    }
                }
                ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>