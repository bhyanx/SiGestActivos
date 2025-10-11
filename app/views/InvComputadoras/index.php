<?php
session_start();
require_once("../../config/configuracion.php");
if (isset($_SESSION["IdRol"])) {
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <?php require_once("../Layouts/Header.php") ?>
        <title>Inventario Maquinas - Sistema Gestión de activos</title>
    </head>

    <body class="sidebar-mini layout-fixed sidebar-collapse">

        <div class="wrapper">

            <?php require_once("../Layouts/Head-Body.php") ?>
            <?php require_once("../Layouts/SideBar.php") ?>

            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1><i class="fas fa-tools"></i> Inventario de Computadoras</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                    <li class="breadcrumb-item active">Inventario de Computadoras</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row" id="dashboards">
                            <div class="col-lg-2 col-6">
                                <!-- small box -->
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3 id="lblnrototales">0</h3>

                                        <p>Bueno Estado</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <!-- <a href="#" onclick="ListarDashboard('0')" class="small-box-footer">Listar <i class="fas fa-arrow-circle-right"></i></a> -->
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-2 col-6">
                                <!-- small box -->
                                <div class="small-box bg-maroon">
                                    <div class="inner">
                                        <h3 id="lblnrovigentes">0</h3>

                                        <p>Roto</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <!-- <a href="#" onclick="ListarDashboard('vi')" class="small-box-footer">Listar <i class="fas fa-arrow-circle-right"></i></a> -->
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-2 col-6">
                                <!-- small box -->
                                <div class="small-box bg-lightblue">
                                    <div class="inner">
                                        <h3 id="lblnrovencidos">0</h3>

                                        <p>Reemplazar</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <!-- <a href="#" onclick="ListarDashboard('ve')" class="small-box-footer">Listar <i class="fas fa-arrow-circle-right"></i></a> -->
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3 id="lblnroporvencer">0</h3>

                                        <p>Reposición</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <!-- <a href="#" onclick="ListarDashboard('pv')" class="small-box-footer">Listar <i class="fas fa-arrow-circle-right"></i></a> -->
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3 id="lblnrovencidos">0</h3>

                                        <p>Falta</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-eye-slash"></i>
                                    </div>
                                    <!-- <a href="#" onclick="ListarDashboard('ve')" class="small-box-footer">Listar <i class="fas fa-arrow-circle-right"></i></a> -->
                                </div>
                            </div>
                            <!-- ./col -->
                        </div>
                    </div>
                </section>
                <!-- /.content -->

                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-10 offset-md-1 mb-4" id="divlistadoactivos">
                                <form action="#" method="post" id="frmbusqueda">
                                    <div class="row">
                                        <div class="col-md-12" id="divfiltros">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="filtroEmpresa">Empresa:</label>
                                                        <select class="form-control" name="filtroEmpresa" id="filtroEmpresa"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="filtroSucursal">Unidad de Neg. :</label>
                                                        <select name="filtroSucursal" id="filtroSucursal" class="form-control"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="filtroAmbiente">Ambiente:</label>
                                                        <select class="form-control" name="filtroAmbiente" id="filtroAmbiente"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="filtroEstado">Estado de activos:</label>
                                                        <select class="form-control" name="filtroEstado" id="filtroEstado"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 offset-md-6">
                                                    <div class="form-group mb-0">
                                                        <label>&nbsp;</label>
                                                        <button type="submit" class="btn btn-primary btn-sm btn-block" id="btnlistar">
                                                            <i class="fa fa-search"></i> Buscar
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-0">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-success btn-sm btn-block" id="btnnuevo">
                                                            <i class="fa fa-plus" style="margin-right: 5px;"></i> Nueva Computadora
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-12" id="divtblRegistros" style="display: none;">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fa fa-list-check"></i> Lista de Activos Registrados</h3>
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
                                                                <th>Id</th>
                                                                <th>Código</th>
                                                                <th>Código Antiguo</th>
                                                                <th>Nombre</th>
                                                                <th>Id Estado</th>
                                                                <th>Estado</th>
                                                                <th>Id Categoria</th>
                                                                <th>Id Empresa</th>
                                                                <th>Id Sucursal</th>
                                                                <th>Id Ambiente</th>
                                                                <th>Ubicación</th>
                                                                <th>Id. Responsable</th>
                                                                <th>Asignado a</th>
                                                                <th>Serie</th>
                                                                <th>Valor</th>
                                                                <th>F. Ingreso</th>
                                                                <th><i class="fas fa-cogs"></i></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                        <tfoot class="table-success">
                                                            <tr>
                                                                <th>Id</th>
                                                                <th>Código</th>
                                                                <th>Código Antiguo</th>
                                                                <th>Nombre</th>
                                                                <th>Id Estado</th>
                                                                <th>Estado</th>
                                                                <th>Id Categoria</th>
                                                                <th>Id Empresa</th>
                                                                <th>Id Sucursal</th>
                                                                <th>Id Ambiente</th>
                                                                <th>Ubicación</th>
                                                                <th>Id. Responsable</th>
                                                                <th>Asignado a</th>
                                                                <th>Serie</th>
                                                                <th>Valor</th>
                                                                <th>F. Ingreso</th>
                                                                <th><i class="fas fa-cogs"></i></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Modal Articulos -->
                <div class="modal fade" id="ModalArticulos" tabindex="-1" role="dialog" aria-labelledby="ModalArticulosTitle"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalArticulosTitulo"><i class="fas fa-box"></i> Lista de artículos</h5>
                                <button class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="tbllistariculos" class="table table-bordered table-striped w-100">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>Artículo</th>
                                                        <th>UM.</th>
                                                        <th>Marca</th>
                                                        <th>Nro. Parte</th>
                                                        <th><i class="fa fa-cogs"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>Artículo</th>
                                                        <th>UM.</th>
                                                        <th>Marca</th>
                                                        <th>Nro. Parte</th>
                                                        <th><i class="fa fa-cogs"></i></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php require_once("../Layouts/Footer.php") ?>
        </div>

        <!-- <?php //require_once("../InvComputadoras/in") 
                ?> -->
        <script type="text/javascript" src="inventariopc.js"></script>
    </body>

    </html>
<?php
} else {
    header("Location:" . Conectar::ruta());
    //header("Location: " . Conectar::rutaLocal());
    exit();
}
?>