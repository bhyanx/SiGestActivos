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
<!--
<style>
    /* Cambia el color de las filas impares (stripe) */
    table.table-striped tbody tr:nth-of-type(odd) {
        background-color: #6cf169 !important;
    }

    /* Hover personalizado */
    table.dataTable tbody tr:hover {
        background-color: #37fa33 !important;
        /* verde un poco más fuerte para hover */
    }
</style>


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
                        <div class="col-md-10 offset-md-1 mb-4" id="divlistadoactivos">
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

                                            <!-- !!!COMENTADO POR DESUSO!!! -->
<!-- <div class="col-md-2">
                                                <div class="form-group mb-0">
                                                    <label for="">&nbsp;</label>
                                                    <button type="button" class="btn btn-info btn-sm btn-block" id="btnCrearActivo">
                                                        <i class="fa fa-plus"></i> Crear Activo
                                                    </button>
                                                </div>
                                            </div> -->
</div>
</div>
</div>
</form>
</div>
<div class="col-12" id="divtblactivos" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-list"></i> Lista de Activos Registrados</h3>
        </div>
        <div class="dataTables_wrapper dt-bootstrap4">
            <div class="card-body">
                <div class="row">
                    <div class=""></div>
                </div>
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="tblRegistros" class="table table-bordered table-striped mt-4 table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-cogs"></i></th>
                                    <th style="display: none;">Id Activo</th>
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

<div class="col-12" id="divregistroActivo">
    <div class=" alert alert-info alert-dismissible">
        <span id="lbldatosactivo"> Guardar activos</span>
        <button type="button" class="close btn" id="btnvolverprincipal"><i class="fas fa-undo-alt"></i></button>
    </div>

    <div class="col-md-12">
        <div class="card card-success">
            <!-- /.card-header -->
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Detalle Registro de activos</h3>
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
                        <!-- ...existing code... -->
                        <div class="row mb-2">
                            <div class="col-md-5 col-lg-4 col-xl-4">
                                <div class="form-group">
                                    <label for="inputDocIngresoAlm">
                                        Doc. Ingreso Almacén:&nbsp;
                                    </label>
                                    <div class="input-group">
                                        <input type="text" id="inputDocIngresoAlm" placeholder="ID de Doc. Ingreso" class="form-control">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" id="btnBuscarDocIngreso"><i class="fa fa-search"></i> Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ...la tabla aquí... -->
                    </div>
                    <div class="col-md-12" id="divdetalle">
                        <hr>
                        <div class="row">
                            <div class="col-md-2">
                                <h5>Detalles</h5>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="tbldetalleactivoreg" class="table table-hover table-bordered table-striped table-sm w-100">
                                <thead class="table-success">
                                    <tr>
                                        <th>Id. Articulo</th>
                                        <th>Nombre</th>
                                        <th>Marca</th>
                                        <th>Código</th>
                                        <th>Serie</th>
                                        <th>Estado</th>
                                        <th>Ambiente</th>
                                        <th>Categoria</th>
                                        <th>Observaciones</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="9" style="text-align: right;" class="">TOTAL DETALLE &nbsp;&nbsp;</th>
                                        <!-- <th style="text-align: right;"></th> -->
                                        <th class="text-center">
                                            <span id="CantRegistros">0</span>
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
                        <button type="button" class="btn btn-danger btn-sm btn-block" id="btncancelarGuardarDetalles">
                            <i class="fa fa-times"></i> Cerrar
                        </button>
                    </div>
                    <div class="col-12 col-md-6">
                        <button type="button" class="btn btn-primary btn-sm btn-block" id="btnGuardarActivo">
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
                                            <th>Nombre</th>
                                            <th>Marca</th>
                                            <th>Empresa</th>
                                            <th>Id.Unidad Negocio</th>
                                            <th>Nombre Local</th>
                                            <th><i class="fa fa-cogs"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Aquí se llenarán los datos dinámicamente -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Id</th>
                                            <th>Nombre</th>
                                            <th>Marca</th>
                                            <th>Empresa</th>
                                            <th>Id.Unidad Negocio</th>
                                            <th>Nombre Local</th>
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

<!-- MODAL PARA PODER ACTUALIZAR EL ACTIVO. -->
<div class="modal fade" id="divModalActualizarActivo" tabindex="-1" role="dialog" aria-labelledby="ModalActualizarActivoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="tituloModalActualizarActivo"><i class="fa fa-plus-circle"></i> Actualizar activo</h5>
            </div>
            <form id="frmEditarActivo">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Id Activo</label>
                                <input type="text" id="idActivo" name="idActivo" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Codigo Activo</label>
                                <input type="text" id="CodigoActivo" name="CodigoActivo" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Serie Activo</label>
                                <input type="text" id="SerieActivo" name="SerieActivo" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Doc. Ingreso Almacén</label>
                                <input type="text" id="DocIngresoAlmacen" name="DocIngresoAlmacen" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="IdArticulo">Id Articulo</label>
                                <input type="text" id="IdArticulo" name="IdArticulo" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nombreArticulo">Nombre</label>
                                <input type="text" name="nombreArticulo" id="nombreArticulo" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="marca">Marca</label>
                                <input type="text" name="marca" id="marca" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select name="Estado" id="Estado" class="form-control select-2"></select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="ambiente">Ambiente:</label>
                                <select name="Ambiente" id="Ambiente" class="form-control select-2"></select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="categoria">Categoria</label>
                                <select name="Categoria" id="Categoria" class="form-control select-2"></select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="Cantidad">Cantidad: </label>
                                <input type="text" name="Cantidad" id="Cantidad" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fechaAdquisicion">Fecha Adquisición: </label>
                                <input type="date" name="fechaAdquisicion" id="fechaAdquisicion" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="Garantia">¿Tiene Garantía? </label>
                                <input type="checkbox" name="Garantia" id="Garantia" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="btnguardar"><i class="fa fa-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- COMENTADO PORQUE YA NO SE USARÁ EL REGISTRO MANUAL -->
<!-- <div class="modal fade" id="divModalRegistroManualActivo" tabindex="-1" role="dialog" aria-labelledby="ModalRegistroManualLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title" id="tituloModalRegistroManual"><i class="fa fa-plus-circle"></i> Crear Activo</h5>
                                    </div>
                                    <form id="frmmantenimiento">
                                        <div class="modal-body">
                                            <div class="row">
                                                <input type="hidden" name="idActivo" id="dActivo" value="0">
                                                <input type="hidden" name="IdActivo" id="idActivo" value="0">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="nombre">Nombre</label>
                                                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej. Mouse Logitech" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="serie">Serie</label>
                                                        <input type="text" name="serie" id="serie" class="form-control" placeholder="Ej. ML-123" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="descripcion">Descripción</label>
                                                        <textarea name="Descripcion" id="Descripcion" class="form-control" placeholder="Ej. Mouse Logitech color negro"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="empresa">Empresa</label>
                                                        <input type="text" class="form-control" id="empresa" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="unidadNegocio">Unidad de Negocio</label>
                                                        <input type="text" class="form-control" id="unidadNegocio" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="estado">Estado</label>
                                                        <select name="Estado" id="Estado" class="form-control select-2" required></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="categoria">Categoria</label>
                                                        <select name="Categoria" id="Categoria" class="form-control select-2" required></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="ambiente">Ambiente:</label>
                                                        <select name="Ambiente" id="Ambiente" class="form-control select-2"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="Cantidad">Cantidad: </label>
                                                        <input type="text" name="Cantidad" id="Cantidad" class="form-control" placeholder="Ej. 1" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="fechaAdquisicion">Fecha Adquisición: </label>
                                                        <input type="date" name="fechaAdquisicion" id="fechaAdquisicion" class="form-control" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                                            <button type="submit" class="btn btn-primary" id="btnguardar"><i class="fa fa-save"></i> Guardar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div> -->
</div>
</section>
</div>

<?php require_once("../Layouts/Footer.php"); ?>
<script>
    var userMod = "<?php echo isset($_SESSION['CodEmpleado']) ? $_SESSION['CodEmpleado'] : ''; ?>";
</script>
<script src="activosp.js"></script>
</div>
</body>

</html>
<?php
// } else {
//     header("Location: " . Conectar::ruta());
//     exit();
// }
?>