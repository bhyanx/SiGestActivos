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
function isCurrentRoute($menuRuta)
{
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return $currentPath === $menuRuta;
}
?>
<style>
    .nav-sidebar .nav-item .nav-link {
        color: #155724;
        transition: all 0.2s ease-in-out;
        border-radius: 6px;
        margin: 2px 6px;
    }

    .nav-sidebar .nav-item .nav-link:hover {
        background-color: #d4edda !important;
        color: #155724 !important;
        /* font-weight: 500; */
    }

    .nav-sidebar .nav-item .nav-link.active {
        background-color: #28a745 !important;
        color: white !important;
        font-weight: bold;
    }

    .nav-sidebar .nav-item .nav-link.active i,
    .nav-sidebar .nav-item .nav-link:hover i {
        color: inherit !important;
    }

    .nav-sidebar .nav-treeview .nav-link {
        padding-left: 2rem;
        font-size: 14px;
    }

    .nav-sidebar .nav-treeview .nav-link:hover {
        background-color: #c3e6cb !important;
        color: #155724 !important;
    }

    .nav-icon {
        min-width: 1.5rem;
        text-align: center;
    }
</style>

<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4 sidebar-light-success">
    <!-- Brand Logo -->
    <a href="http://192.168.1.54:8088/SiGestActivosV2/" class="brand-link">
        <b class="ml-3 mr-0">SIS</b>
        <span class="brand-text font-weight-light">- GESTIÓN ACTIVOS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fa fa-user fa-lg text-light" style="padding-left: 7px"></i>
            </div>
            <div class="info">
                <span class="d-block text-light text-sm">
                    <?php
                    /*if (isset($_SESSION["NombreTrabajador"]) && !empty($_SESSION["NombreTrabajador"])) {
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
                    }*/
                    ?>
                </span>
            </div>
        </div> -->

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

                        echo '<li class="nav-item' . ($hasActiveChild ? ' menu-open' : '') . '">
                                <a href="#" class="nav-link' . ($hasActiveChild ? ' active' : '') . '" id="' . $menugrupo['MenuGrupo'] . '">
                                    <i class="nav-icon fas ' . $menugrupo['MenuGrupoIcono'] . '"></i>
                                    <p> ' . $menugrupo['MenuGrupo'] . ' <i class="right fas fa-angle-left"></i> </p>
                                </a>
                                <ul class="nav nav-treeview">';
                        if (is_array($datapermisos)) {
                            foreach ($datapermisos as $permiso) {
                                if ($menugrupo['MenuGrupo'] == $permiso['MenuGrupo'] && $permiso['Permiso'] == 1) {
                                    $isActive = isCurrentRoute($permiso['MenuRuta']);
                                    echo '<li class="nav-item ml-1">
                                            <a href="' . $permiso['MenuRuta'] . '" class="nav-link' . ($isActive ? ' active' : '') . '" id="' . $permiso['MenuIdentificador'] . '">
                                                <i class="fas ' . $permiso['MenuIcono'] . ' nav-icon"></i>
                                                <p>' . $permiso['NombreMenu'] . '</p>
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