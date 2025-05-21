<?php
session_start();

// ? VISTA PARA GESTIONAR LOS MOVIMIENTOS DE LOS ACTIVOS

//! FALTA FUNCIONAMIENTO Y PONER CAMPOS REFERENCIALES A LOS MOVIMIENTOS A NUESTRA VISTA

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Movimientos - Sistema Gestion de activos</title>
    <style>
        .swal2-container {
            z-index: 9999 !important;
        }

        .dropdown-menu .show {
            position: fixed !important;
        }
    </style>
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
                            <h1>Gestion de Movimientos en Activos</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li
                                    class="breadcrumb-item active">Movimientos</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-10 offset-md-1 mb-4" id="divlistadomovimientos">
                            <form action="#" method="post" id="frmbusqueda">
                                <div class="row">
                                    <div class="col-md-12" id="divfiltros">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filtroTipoMovimiento">Tipo Movimiento:</label>
                                                    <select class="form-control" name="filtroTipoMovimiento" id="filtroTipoMovimiento"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filtroSucursal">Sucursal:</label>
                                                    <select class="form-control" name="filtroSucursal" id="filtroSucursal"></select>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filtroAmbiente">Ambiente Destino:</label>
                                                    <select class="form-control" name="filtroAmbiente"id="filtroAmbiente"></select>
                                                </div>
                                            </div> -->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filtroFecha">Fecha Movimiento:</label>
                                                    <input type="date" class="form-control" name="filtroFecha" id="filtroFecha" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-2 offset-md-8">
                                                <div class="form-group mb-0">
                                                    <label for="">&nbsp;</label>
                                                    <button type="submit" class="btn btn-primary btn-sm btn-block" id="btnlistar">
                                                        <i class="fa fa-search"></i> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group mb-0">
                                                    <label for="">&nbsp;</label>
                                                    <button type="button" class="btn btn-success btn-sm btn-block" id="btnnuevo">
                                                        <i class="fa fa-plus"></i> Nuevo Movimiento
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-12" id="divtblmovimientos" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fa fa-list"></i> Lista de Movimientos Realizados</h3>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="tblMovimientos" class="table table-bordered table-striped w-100 h-100">
                                                <thead>
                                                    <tr>
                                                        <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                        <th>Id DetalleMovimiento</th>
                                                        <th>Id Activo</th>
                                                        <th>Nombre Activo</th>
                                                        <th>Tipo Movimiento</th>
                                                        <th>Sucursal Anterior</th>
                                                        <th>Sucursal Nueva</th>
                                                        <th>Ambiente Anterior</th>
                                                        <th>Ambiente Nueva</th>
                                                        <th>Autorizador</th>
                                                        <th>Responsable Anterior</th>
                                                        <th>Responsable Nueva</th>
                                                        <th>Fecha Movimiento</th>
                                                        <th>Responsable Origen</th>
                                                        <th>Responsable Destino</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                        <th>Id DetalleMovimiento</th>
                                                        <th>Id Activo</th>
                                                        <th>Nombre Activo</th>
                                                        <th>Tipo Movimiento</th>
                                                        <th>Sucursal Anterior</th>
                                                        <th>Sucursal Nueva</th>
                                                        <th>Ambiente Anterior</th>
                                                        <th>Ambiente Nueva</th>
                                                        <th>Autorizador</th>
                                                        <th>Responsable Anterior</th>
                                                        <th>Responsable Nueva</th>
                                                        <th>Fecha Movimiento</th>
                                                        <th>Responsable Origen</th>
                                                        <th>Responsable Destino</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Aquí puedes agregar los modales para registrar/editar movimientos y detalles, igual que en tu estructura actual -->
                        <?php //require_once("modals_movimientos.php"); 
                        ?>
                    </div>
                </div>
            </section>
        </div>
        <?php require_once("../Layouts/Footer.php"); ?>
        <script src="movimiento.js"></script>
    </div>
    <script>
        $(document).ready(function() {
            $('#frmbusqueda').on('submit', function(e) {
                e.preventDefault();
                $('#divtblmovimientos').show();
                // Si usas DataTable, aquí puedes recargarla:
                if (typeof listarMovimientos === 'function') listarMovimientos();
            });
        });
    </script>
</body>

</html>