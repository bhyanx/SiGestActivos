<?php
session_start();
require_once("../../config/configuracion.php");
if (isset($_SESSION["IdRol"])) {
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
                                <h1>Gestion de Activos - Edificaciones</h1>
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
                                                            <i class="fa fa-plus"></i> Nuevo
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-0">
                                                        <label>&nbsp;</label>
                                                        <button type="button" class="btn btn-info btn-sm btn-block" id="btnCrearActivo">
                                                            <i class="fa fa-plus"></i> Crear Activo
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
                                                        <thead class="table-success">
                                                            <tr>
                                                                <th>Id</th>
                                                                <th>Código</th>
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
                                                        <tfoot>
                                                            <tr>
                                                                <th>Id</th>
                                                                <th>Código</th>
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

                            <div class="col-12" id="divregistroActivo" style="display: none;">
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
                                                <button type="button" class="btn btn-tool btn-remove-activo" style="display:none;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <!-- ...existing code... -->
                                                    <div class="row mb-2">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="tipoDocumento">Tipo de Documento:</label>
                                                                <select class="form-control" id="tipoDocumento">
                                                                    <option value="ingreso">Doc. Ingreso Almacén</option>
                                                                    <option value="venta">Doc. Venta</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5 col-lg-4 col-xl-4">
                                                            <div class="form-group">
                                                                <label for="inputDocumento" id="labelDocumento">
                                                                    Doc. Ingreso Almacén:&nbsp;
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="text" id="inputDocumento" placeholder="ID de Documento" class="form-control">
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary" type="button" id="btnBuscarDocumento"><i class="fa fa-search"></i> Buscar</button>
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
                                                                    <th>Id</th>
                                                                    <th>Nombre</th>
                                                                    <th>Marca</th>
                                                                    <!--<th>Código</th>-->
                                                                    <th>Serie</th>
                                                                    <th>Estado</th>
                                                                    <th>Ambiente</th>
                                                                    <th>Categoría</th>
                                                                    <th>Valor Unitario</th>
                                                                    <th>Cantidad</th>
                                                                    <th>Proveedor</th>
                                                                    <th>Observaciones</th>
                                                                    <th>Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th colspan="11" style="text-align: right;" class="">TOTAL DETALLE &nbsp;&nbsp;</th>
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
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document" id="frmArticulos">
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
                                                                        <th>Proveedor</th>
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
                                                                        <th>Proveedor</th>
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

                            <!-- Nuevo div para registro manual de multiples activos -->
                            <div class="col-12" id="divRegistroManualActivoMultiple" style="display: none;">
                                <div class="alert alert-info alert-dismissible">
                                    <span>Registro Manual de Activos</span>
                                    <button type="button" class="close btn" id="btnvolverprincipalManual"><i class="fas fa-undo-alt"></i></button>
                                </div>

                                <div id="activosContainer">
                                    <div class="card card-success activo-manual-form" data-form-number="1">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-plus-circle"></i> Activo Nuevo <span class="activo-num">#1</span></h3>
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
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Nombre</label>
                                                        <input type="text" name="nombre[]" class="form-control" placeholder="Ej. Mouse Logitech" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Serie</label>
                                                        <input type="text" name="serie[]" class="form-control" placeholder="Ej. ML-123" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Estado</label>
                                                        <select name="Estado[]" class="form-control select-2" required></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Descripción</label>
                                                        <textarea name="Descripcion[]" class="form-control" placeholder="Ej. Mouse Logitech color negro"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Empresa</label>
                                                        <input type="text" class="form-control" name="empresa[]" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Unidad de Negocio</label>
                                                        <input type="text" class="form-control" name="unidadNegocio[]" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Responsable</label>
                                                        <select name="Responsable[]" class="form-control select-2" required></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label>Categoria</label>
                                                        <select name="Categoria[]" class="form-control select-2" required></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label>Ambiente:</label>
                                                        <select name="Ambiente[]" class="form-control select-2"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label>Proveedor</label>
                                                        <select name="Proveedor[]" class="form-control select-2"></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label> Cantidad: </label>
                                                        <input type="text" name="Cantidad[]" class="form-control" placeholder="Ej. 1" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Valor Adquisición:</label>
                                                        <input type="text" name="ValorAdquisicion[]" class="form-control" placeholder="Ej. 10.00" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Fecha Adquisición: </label>
                                                        <input type="date" name="fechaAdquisicion[]" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Observaciones: </label>
                                                        <textarea name="Observaciones[]" class="form-control" rows="3" placeholder="Ingrese las observaciones según el activo..."></textarea>
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
                                                    <button type="button" class="btn btn-danger btn-sm btn-block" id="btncancelarRegistroManual">
                                                        <i class="fa fa-times"></i> Cerrar
                                                    </button>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <button type="button" class="btn btn-success btn-sm btn-block" id="btnAgregarOtroActivo">
                                                        <i class="fa fa-plus"></i> Agregar Otro Activo
                                                    </button>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <button type="button" class="btn btn-primary btn-sm btn-block" id="btnGuardarActivosManuales">
                                                        <i class="fa fa-check"></i> Guardar Todos los Activos
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- COMENTADO PORQUE YA NO SE USARÁ EL REGISTRO MANUAL -->
                            <!--<div class="modal fade" id="divModalRegistroManualActivo" tabindex="-1" role="dialog" aria-labelledby="ModalRegistroManualLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <h5 class="modal-title" id="tituloModalRegistroManual"><i class="fa fa-plus-circle"></i> Crear Activo</h5>
                                        </div>
                                        <form id="frmmantenimiento">
                                            <div class="modal-body">
                                                <div class="row">
                                                    <input type="hidden" name="IdActivo" id="IdActivo" value="0">
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
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="">Responsable</label>
                                                            <select name="Responsable" id="Responsable" class="form-control select-2" required></select>
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
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label for="">Proveedor</label>
                                                            <select name="Proveedor" id="Proveedor" class="form-control select-2"></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="Cantidad"> Cantidad: </label>
                                                            <input type="text" name="Cantidad" id="Cantidad" class="form-control" placeholder="Ej. 1" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="">Valor Adquisición:</label>
                                                            <input type="text" name="ValorAdquisicion" id="ValorAdquisicion" class="form-control" placeholder="Ej. 10.00" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="fechaAdquisicion">Fecha Adquisición: </label>
                                                            <input type="date" name="fechaAdquisicion" id="fechaAdquisicion" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="">Observaciones: </label>
                                                            <textarea name="Observaciones" id="Observaciones" class="form-control" rows="3" placeholder="Ingrese las observaciones según el activo..."></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                                                <button type="submit" class="btn btn-primary" id="btnGuardarManual"><i class="fa fa-save"></i> Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>-->

                            <!-- MODAL PARA PODER ACTUALIZAR EL ACTIVO. -->
                            <div class="modal fade" id="divModalActualizarActivo" style="z-index: 9999 !important;" role="dialog" aria-labelledby="ModalActualizarActivoLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <h5 class="modal-title" id="tituloModalActualizarActivo"><i class="fa fa-plus-circle"></i> Actualizar activo</h5>
                                        </div>
                                        <form id="frmEditarActivo">
                                            <input type="hidden" name="IdActivo" id="IdActivoEditar" value="0">
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="IdActivo">Id Activo</label>
                                                            <input type="text" id="IdActivo" name="IdActivo" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="CodigoActivo">Codigo Activo</label>
                                                            <input type="text" id="CodigoActivo" name="CodigoActivo" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="SerieActivo">Serie Activo</label>
                                                            <input type="text" id="SerieActivo" name="SerieActivo" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="DocIngresoAlmacen">Doc. Ingreso Almacén</label>
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
                                                            <label for="IdEstado">Estado</label>
                                                            <select name="IdEstado" id="IdEstado" class="form-control select-2"></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label for="IdAmbiente">Ambiente:</label>
                                                            <select name="IdAmbiente" id="IdAmbiente" class="form-control select-2"></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="IdCategoria">Categoria</label>
                                                            <select name="IdCategoria" id="IdCategoria" class="form-control select-2"></select>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="Cantidad">Cantidad: </label>
                                                            <input type="text" name="Cantidad" id="Cantidad" class="form-control" disabled>
                                                        </div>
                                                    </div> -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="fechaAdquisicion">Fecha Adquisición: </label>
                                                            <input type="date" name="fechaAdquisicion" id="fechaAdquisicion" class="form-control" disabled>
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

                            <div id="modalBajaActivo" class="modal fade" style="z-index: 9999 !important;" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title" id="tituloModalBajaActivo"><i class="fa fa-minus-circle"></i> Dar de Baja Activo</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form id="frmBajaActivo">
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label for="Autorizador">Autorizador</label>
                                                            <select name="Autorizador" id="Autorizador" class="form-control select-2"></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="motivoBaja">Motivo de Baja:</label>
                                                            <textarea name="motivoBaja" id="motivoBaja" class="form-control" rows="3" placeholder="Ingrese el motivo de la baja del activo..."></textarea>
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

                            <div id="modalAsignarResponsable" class="modal fade" role="dialog" style="z-index: 9999 !important;">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-alert">
                                            <h5 class="modal-title" id="tituloModalAsignarResponsable"><i class="fa fa-users"></i> Asignar Responsable</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form id="frmAsignarResponsable">
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label for="Responsable">Responsable</label>
                                                            <select name="Responsable" id="Responsable" class="form-control select-2"></select>
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

                            <!-- MODAL PARA LISTAR TODOS LOS ACTIVOS -->
                            <div class="modal fade" id="modalListarTodosActivos" tabindex="-1" role="dialog" aria-labelledby="modalListarTodosActivosLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info">
                                            <h5 class="modal-title" id="modalListarTodosActivosLabel"><i class="fa fa-list"></i> Lista de Todos los Activos</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table id="tblTodosActivos" class="table table-bordered table-striped display nowrap" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th><i class="fa fa-cogs"></i></th>
                                                            <th>Id</th>
                                                            <th>Código</th>
                                                            <th>Serie</th>
                                                            <th>Nombre</th>
                                                            <th>Marca</th>
                                                            <th>Empresa</th>
                                                            <th>Locación</th>
                                                            <th>Categoría</th>
                                                            <th>Estado</th>
                                                            <th>Valor</th>
                                                            <th>Responsable</th>
                                                            <!-- <th>Articulos Relacionados</th>
                                                            <th>Activos Relacionados</th> -->
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Aquí se llenarán los datos dinámicamente -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th><i class="fa fa-cogs"></i></th>
                                                            <th>Id</th>
                                                            <th>Código</th>
                                                            <th>Serie</th>
                                                            <th>Nombre</th>
                                                            <th>Marca</th>
                                                            <th>Empresa</th>
                                                            <th>Locación</th>
                                                            <th>Categoría</th>
                                                            <th>Estado</th>
                                                            <th>Valor</th>
                                                            <th>Responsable</th>
                                                            <!-- <th>Articulos Relacionados</th>
                                                            <th>Activos Relacionados</th> -->
                                                            <!-- <th><i class="fa fa-cogs"></i></th> -->
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL PARA PROCESAR CANTIDAD DE ACTIVOS -->
                            <div class="modal fade" id="modalProcesarCantidad" tabindex="-1" role="dialog" aria-labelledby="modalProcesarCantidadLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info">
                                            <h5 class="modal-title" id="modalProcesarCantidadLabel">
                                                <i class="fas fa-cogs"></i> Procesar Cantidad de Activos
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>Información:</strong> Se crearán filas individuales para cada unidad del activo.
                                                Cada una tendrá su propia serie y podrá ser gestionada independientemente.
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-header bg-light">
                                                            <h6 class="mb-0"><i class="fas fa-box"></i> Información del Activo</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <strong>Nombre:</strong> <span id="modalActivoNombre">-</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <strong>Marca:</strong> <span id="modalActivoMarca">-</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="modalCantidadTotal">
                                                            <i class="fas fa-hashtag"></i> Cantidad Total:
                                                        </label>
                                                        <input type="number" class="form-control" id="modalCantidadTotal" min="2" max="100" readonly>
                                                        <small class="form-text text-muted">Cantidad actual del activo</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="modalSerieBase">
                                                            <i class="fas fa-barcode"></i> Serie Base:
                                                        </label>
                                                        <input type="text" class="form-control" id="modalSerieBase" placeholder="Ej: ABC123">
                                                        <small class="form-text text-muted">Se agregará -1, -2, -3... a cada serie</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Campo de proveedor para documentos de venta -->
                                            <div class="row" id="modalProveedorContainer" style="display: none;">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="modalProveedor">
                                                            <i class="fas fa-building"></i> Proveedor: <span class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-control" id="modalProveedor">
                                                            <option value="">Seleccione un proveedor...</option>
                                                        </select>
                                                        <small class="form-text text-muted">Obligatorio para documentos de venta</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="modalObservacionesBase">
                                                            <i class="fas fa-sticky-note"></i> Observaciones Base:
                                                        </label>
                                                        <textarea class="form-control" id="modalObservacionesBase" rows="2" placeholder="Observaciones que se aplicarán a todos los activos"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        <strong>Importante:</strong>
                                                        <ul class="mb-0 mt-2">
                                                            <li>Se crearán <strong><span id="cantidadACrear">0</span></strong> filas individuales</li>
                                                            <li>Cada activo tendrá su propia serie única</li>
                                                            <li>Los valores de ambiente, categoría y valor se copiarán automáticamente</li>
                                                            <li>Podrás editar cada activo individualmente después del procesamiento</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                <i class="fas fa-times"></i> Cancelar
                                            </button>
                                            <button type="button" class="btn btn-info" id="btnConfirmarProcesar">
                                                <i class="fas fa-cogs"></i> Procesar Activos
                                            </button>
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
            <script src="edificaciones.js"></script>
        </div>
    </body>

    </html>
<?php
} else {
    header("Location: " . Conectar::ruta());
    exit();
}
?>