<?php

?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4 sidebar-dark-green">
    <!-- Brand Logo -->
    <a href="" class="brand-link">
        <img src="/public/img/Page-Lubriseng.png" alt="Logo Sistema EPPS" class="brand-image elevation-3" style="opacity: .8">
        <span class="brand-text font-weight text-sm"></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fa fa-user fa-lg text-light" style="padding-left: 7px"></i>
            </div>
            <div class="info">
                <span class="d-block text-light text-sm"></span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="../Home/" class="nav-link" id="Home">
                        <i class="nav-icon fas fa-home"></i>
                        <p> Inicio </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" id="Activos">
                        <i class="nav-icon fas fab fa-user-tie"></i>
                        <p> Activos <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="../Activos/" class="nav-link" id="MovimientoActivo">
                                <i class="fas fa-check-double nav-icon"></i>
                                <p>
                                    Movimiento Activo
                                </p>
                            </a>
                        </li>
                    </ul>
                    <!-- <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="../Activos/" class="nav-link" id="AdministrarActivo">
                                <i class="fas fa-list-check nav-icon"></i>
                                <p>
                                    Administrar Activo
                                </p>
                            </a>
                        </li>
                    </ul> -->
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fab fa-building"></i>
                        <p>
                            Sucursales
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="../Sucursal/" class="nav-link" id="Sucursal">
                                <i class="fas fa-dot-circle nav-icon"></i>
                                <p>Autocentro B-80</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="../Sucursal/" class="nav-link" id="Sucursal">
                                <i class="fas fa-dot-circle nav-icon"></i>
                                <p>Llantacentro B-90</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="../Sucursal/" class="nav-link" id="Sucursal">
                                <i class="fas fa-dot-circle nav-icon"></i>
                                <p>Taller Hyundai</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../Proveedores/" class="nav-link" id="Proveedores">
                        <i class="nav-icon fas fa-home"></i>
                        <p> Proveedores </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../Usuario/" class="nav-link" id="Usuarios">
                        <i class="nav-icon fas fa-user-gear"></i>
                        <p> Usuarios </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../Configuracion/" class="nav-link" id="Configuracion">
                        <i class="fas fa-wrench nav-icon"></i>
                        <p>
                            Configuración Sistema
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>