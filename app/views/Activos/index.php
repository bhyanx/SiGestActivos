<?php
session_start();
// require_once("../../config/configuracion.php");
// if (isset($_SESSION["CodEmpleado"])) {
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Activos - Sistema Gestion de activos</title>
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
                            <h1>Gestion de Activos</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li class="breadcrumb-item active">Gestion de activos</li>
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
                                                    <label for="filtroAmbiente">Ambiente:</label>
                                                    <select class="form-control" name="filtroAmbiente" id="filtroAmbiente"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="filtroCategoria">Categoria de Activo:</label>
                                                    <select class="form-control" name="filtroCategoria" id="filtroCategoria"></select>
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
                                                    <label for="filtroFecha">Fecha Registro:</label>
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
                                                        <i class="fa fa-plus"></i> Nuevo
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
                                <div class="dataTables_wrapper dt-bootstrap4">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-6"></div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="tblRegistros" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <!-- <th><i class="fa fa-cogs"></i></th> -->
                                                            <!-- <th>#</th> -->
                                                            <th><i class="fa fa-cogs"></i></th>
                                                            <th>Id Activo</th>
                                                            <th>Código</th>
                                                            <th>Serie</th>
                                                            <th>Nombre Activo</th>
                                                            <th>Marca</th>
                                                            <th>Sucursal</th>
                                                            <th>Proveedor</th>
                                                            <th>Estado</th>
                                                            <th>Valor Adquisición</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <!-- <th><i class="fa fa-cogs"></i></th> -->
                                                            <th><i class="fa fa-cogs"></i></th>
                                                            <th>Id Activo</th>
                                                            <th>Código</th>
                                                            <th>Serie</th>
                                                            <th>Nombre Activo</th>
                                                            <th>Marca</th>
                                                            <th>Sucursal</th>
                                                            <th>Proveedor</th>
                                                            <th>Estado</th>
                                                            <th>Valor Adquisición</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-12" id="divregistroMovimiento">
                            <div class=" alert alert-info alert-dismissible">
                                <span id="lbldatossucmovimiento"></span>
                                <button type="button" class="close btn" id="btnchangedatasucmovimiento"><i class="fas fa-undo-alt"></i></button>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-file-alt"></i> Datos de Origen:</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                    <i class="fas fa-expand"></i>
                                                </button>
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <div class="form-group">
                                                                <label for="sucursal_origen">Sucursal origen:</label>
                                                                <input class="form-control" name="sucursal_origen" id="sucursal_origen" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <div class="form-group">
                                                                <label for="usuario_origen">Usuario realizando el movimiento:</label>
                                                                <input type="text" class="form-control" name="usuario_origen" id="usuario_origen" value="<?php echo $_SESSION['PrimerNombre'] . ' ' . $_SESSION['ApellidoPaterno'] . ' ' . $_SESSION['ApellidoMaterno']; ?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="fecha_salida">Fecha de salida:</label>
                                                                <input type="date" class="form-control" name="fecha_salida" id="fecha_salida" value="<?php echo date('Y-m-d'); ?>" required readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="hora_salida">Hora de salida:</label>
                                                                <input type="time" class="form-control" name="hora_salida" id="hora_salida" value="<?php echo date('H:i'); ?>" required readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="overlay dark" id="overlay" style="display: none;">
                                            <i class="fas fa-2x fa-sync-alt"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-file-alt"></i> Datos de Destino:</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                    <i class="fas fa-expand"></i>
                                                </button>
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="sucursal_destino">Sucursal destino:</label>
                                                                <input class="form-control" name="sucursal_destino" id="sucursal_destino" readonly>
                                                            </div>
                                                        </div>

                                                        <!-- <div class="form-group">
                                                            <label for="usuario_destino">Usuario que recibe:</label>
                                                            <select class="form-control" name="usuario_destino" id="usuario_destino" required></select>
                                                        </div> -->
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="fecha_ingreso">Fecha de ingreso:</label>
                                                                <input type="date" class="form-control" name="fecha_ingreso" id="fecha_ingreso" value="<?php echo date('Y-m-d'); ?>" required readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="hora_ingreso">Hora de ingreso:</label>
                                                                <input type="time" class="form-control" name="hora_ingreso" id="hora_ingreso" value="<?php echo date('H:i'); ?>" required readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="overlay dark" id="overlay" style="display: none;">
                                            <i class="fas fa-2x fa-sync-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card card-warning">
                                    <!-- /.card-header -->
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Detalle Movimiento de activos</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-5 col-lg-4 col-xl-4">
                                                        <div class="form-group">
                                                            <label for="">
                                                                Buscar Activo:&nbsp;
                                                                <!-- <div class="custom-control custom-radio custom-control-inline">
                                                                    <input type="radio" id="radioproducto" name="radiotipodetalle" class="custom-control-input" value="P" checked>
                                                                    <label class="custom-control-label" for="radioproducto">Producto</label>
                                                                </div> -->
                                                                <!-- <div class="custom-control custom-radio custom-control-inline d-none">
                                                                    <input type="radio" id="radioservicio" name="radiotipodetalle" class="custom-control-input" value="S" disabled>
                                                                    <label class="custom-control-label" for="radioservicio">Servicio</label>
                                                                </div> -->
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="text" name="txtbuscarartid" id="txtbuscarartid" placeholder="ID de Activo" class="form-control">
                                                                <div class="input-group-append">
                                                                    <button class="btn" type="button" id="btnBuscarIdItem"><i class="fa fa-plus"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 col-lg-1 col-xl-1">
                                                        <div class="form-group">
                                                            <label for="">Listar Art.</label>
                                                            <button class="btn btn-primary btn-block btnagregardet" type="button">
                                                                <i class="fa fa-search"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12" id="divdetalle">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <h5>Detalles</h5>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table id="tbldetalleactivomov" class="table table-hover table-bordered table-striped table-sm w-100">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <!-- <th>Código</th> -->
                                                                <th>Nombre</th>
                                                                <th>Marca</th>
                                                                <th>Sucursal</th>
                                                                <th>Ambiente</th>
                                                                <th>Ambiente Destino</th>
                                                                <th>Responsable Destino</th>
                                                                <th>Acción</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="9" style="text-align: right;" class="bg-<?= $_SESSION['TemaColor'] ?>">TOTAL DETALLE &nbsp;&nbsp;</th>
                                                                <!-- <th style="text-align: right;"></th> -->
                                                                <th class="text-center bg-<?= $_SESSION['TemaColor'] ?>">
                                                                    <span id="TotalSinIgV">0.00</span>
                                                                </th>

                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <div class="card">
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                                <button type="button" class="btn btn-danger btn-sm btn-block" id="btncancelarficha">
                                                    <i class="fa fa-times"></i> Cerrar
                                                </button>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <button type="button" class="btn btn-primary btn-sm btn-block" id="btnGuardarOC">
                                                    <i class="fa fa-check"></i> Guardar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Modal Articulos -->
                            <div class="modal fade" id="ModalArticulos" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalArticulosTitle">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header dragable_touch">
                                            <h5 class="modal-title" id="ModalArticulosTitulo"><i class="fas fa-box"></i> Lista de artículos</h5>
                                            <button class="close" data-dismiss="modal" aria-label="Close">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">

                                                        <table id="tbllistarActivos" class="table table-bordered table-striped display nowrap" style="width:100%">
                                                            <thead>
                                                                <tr>

                                                                    <th>Id</th>
                                                                    <!-- <th>Código</th> -->
                                                                    <th>Nombre</th>
                                                                    <th>Marca</th>
                                                                    <th>Sucursal</th>
                                                                    <th>Ambiente</th>
                                                                    <th><i class="fa fa-cogs"></i></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <!-- Aquí se llenarán los datos dinámicamente -->
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th>Id</th>
                                                                    <!-- <th>Código</th> -->
                                                                    <th>Nombre</th>
                                                                    <th>Marca</th>
                                                                    <th>Sucursal</th>
                                                                    <th>Ambiente</th>
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
                    </div>
            </section>
        </div>

        <?php require_once("../Layouts/Footer.php"); ?>
        <script>
            var userMod = "<?php echo isset($_SESSION['CodEmpleado']) ? $_SESSION['CodEmpleado'] : ''; ?>";
        </script>
        <script src="activos.js"></script>
    </div>
</body>

</html>
<?php
// } else {
//     header("Location: " . Conectar::ruta());
//     exit();
// }
?>