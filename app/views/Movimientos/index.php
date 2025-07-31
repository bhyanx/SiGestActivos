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
            z-index: 10000 !important;
        }

        .dropdown-menu .show {
            position: fixed !important;
        }

        /* Asegurar que el modal de artículos siempre esté visible */
        #ModalArticulos {
            z-index: 9999 !important;
        }

        #ModalArticulos .modal-backdrop {
            z-index: 9998 !important;
        }

        .modal-backdrop.show {
            z-index: 9998 !important;
        }

        /* Mejorar la apariencia del botón de cerrar */
        #ModalArticulos .close {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            cursor: pointer;
        }

        #ModalArticulos .close:hover {
            opacity: .75;
        }

        /* Asegurar que los botones del modal sean clickeables */
        #ModalArticulos .modal-header .close,
        #ModalArticulos .modal-footer .btn {
            z-index: 10001;
            position: relative;
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
                    <!-- Contenedor principal -->
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
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="filtroTipoListado">Tipo de Listado:</label>
                                                    <select class="form-control" name="filtroTipoListado" id="filtroTipoListado">
                                                        <option value="enviados">Movimientos Enviados</option>
                                                        <option value="recibidos">Movimientos Recibidos</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
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
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="filtroFecha">Fecha Movimiento:</label>
                                                    <input type="date" class="form-control" name="filtroFecha" id="filtroFecha" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-2 offset-md-2">
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
                                            <!-- <div class="col-md-2">
                                                <div class="form-group mb-0">
                                                    <lable>&nbsp;</lable>
                                                    <button class="btn btn-success btn-sm btn-block" id="btnCompuesto">
                                                        <i class="fa fa-plus"></i> Nuevo Movimiento Compuesto
                                                    </button>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-12" id="divtblmovimientos" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title" id="tituloTablaMovimientos"><i class="fa fa-list"></i> Lista de Movimientos Enviados</h3>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="tblMovimientos" class="table table-bordered table-striped w-100 h-100">
                                                <thead>
                                                    <tr>
                                                        <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                        <th>Código</th>
                                                        <th>Tipo Movimiento</th>
                                                        <th>Sucursal Destino</th>
                                                        <th>Empresa Destino</th>
                                                        <th>Autorizador</th>
                                                        <th>Fecha Movimiento</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                        <th>Código</th>
                                                        <th>Tipo Movimiento</th>
                                                        <th>Sucursal Destino</th>
                                                        <th>Empresa Destino</th>
                                                        <th>Autorizador</th>
                                                        <th>Fecha Movimiento</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12" id="divgenerarmov">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-building"></i> Generar Movimiento:</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" id="divfiltros">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="IdTipoMovimientoMov">Tipo Mov.:</label>
                                                        <select name="IdTipoMovimientoMov" id="IdTipoMovimientoMov" class="form-control" required></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group"><label for="CodAutorizador">Autorizador:</label><select name="CodAutorizador" id="CodAutorizador" class="form-control" required></select></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="IdEmpresaDestino">Empresa Destino:</label>
                                                        <select name="IdEmpresaDestino" id="IdEmpresaDestino" class="form-control" required></select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="IdSucursalDestino">Sucursal Destino:</label>
                                                        <select name="IdSucursalDestino" id="IdSucursalDestino" class="form-control" required></select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="CodReceptor">Receptor:</label>
                                                        <select name="CodReceptor" id="CodReceptor" class="form-control" require></select>
                                                    </div>
                                                </div>

                                                <!-- <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="codAlmacen">Almacen:</label>
                                                        <select name="codAlmacen" id="codAlmacen" class="form-control">
                                                        </select>
                                                    </div>
                                                </div> -->
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="">&nbsp;</label>
                                                        <button type="button" class="btn btn-danger btn-block" id="btncancelarempresa">
                                                            <i class="fas fa-reply"></i> Cancelar
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="">&nbsp;</label>
                                                        <button type="button" class="btn btn-primary btn-block" id="btnprocesarempresa" onclick="$('#IdTipoMovimiento').val($('#IdTipoMovimientoMov').val()); $('#IdAutorizador').val($('#CodAutorizador').val()); $('#IdReceptor').val($('#CodReceptor').val());">
                                                            <i class="fas fa-sync"></i> Procesar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>

                    <div class="col-12" id="divregistroMovimiento">
                        <div class=" alert alert-info alert-dismissible">
                            <span id="lbldatossucmovimiento"></span>
                            <span id="lblusuariorealizando">Usuario realizando el movimiento: <?php echo $_SESSION['PrimerNombre'] . ' ' . $_SESSION['ApellidoPaterno'] . ' ' . $_SESSION['ApellidoMaterno']; ?></span>
                            <button type="button" class="close btn" id="btnchangedatasucmovimiento"><i class="fas fa-undo-alt"></i></button>
                            <input type="hidden" name="IdTipoMovimiento" id="IdTipoMovimiento">
                            <input type="hidden" name="IdAutorizador" id="IdAutorizador">
                            <input type="hidden" name="IdReceptor" id="IdReceptor">
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
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="usuario_origen">Autorizador:</label>
                                                            <input type="text" class="form-control" name="usuario_origen" id="usuario_origen" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="sucursal_origen">Sucursal origen:</label>
                                                            <input class="form-control" name="sucursal_origen" id="sucursal_origen" value="<?php echo $_SESSION['Nombre_local'] ?? $_SESSION['NombreSucursal'] ?? 'No definida'; ?>" readonly>
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
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="usuario_destino">Receptor:</label>
                                                            <input type="text" class="form-control" name="usuario_destino" id="usuario_destino" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="sucursal_destino">Sucursal destino:</label>
                                                            <input class="form-control" name="sucursal_destino" id="sucursal_destino" readonly>
                                                        </div>
                                                    </div>
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
                                                            <th>Código</th>
                                                            <th>Nombre</th>
                                                            <!-- <th>Marca</th> -->
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
                                                            <th colspan="9" style="text-align: right;">TOTAL DETALLE &nbsp;&nbsp;</th>
                                                            <!-- <th style="text-align: right;"></th> -->
                                                            <th class="text-center">
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
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <!-- /.card-header -->
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Observaciones del Movimiento</h3>
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
                                            <div class="form-group">
                                                <label for="observaciones">
                                                    <i class="fas fa-edit"></i> Observaciones:
                                                    <small class="text-muted">(Opcional)</small>
                                                </label>
                                                <textarea
                                                    class="form-control"
                                                    name="observaciones"
                                                    id="observaciones"
                                                    rows="4"
                                                    placeholder="Ingrese observaciones adicionales sobre este movimiento de activos..."
                                                    maxlength="500"
                                                    style="resize: vertical; min-height: 100px;"></textarea>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle"></i>
                                                        Describa detalles importantes del movimiento, motivos, condiciones especiales, etc.
                                                    </small>
                                                    <small class="form-text text-muted">
                                                        <span id="contador-caracteres">0</span>/500 caracteres
                                                    </small>
                                                </div>
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
                                            <button type="button" class="btn btn-danger btn-sm btn-block" id="btnsalirmov">
                                                <i class="fa fa-times"></i> Cerrar
                                            </button>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <button type="button" class="btn btn-primary btn-sm btn-block" id="btnGuardarMov">
                                                <i class="fa fa-check"></i> Guardar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Modal Articulos -->
                        <div class="modal fade" id="ModalArticulos" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalArticulosTitle" style="z-index: 9999 !important;">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header dragable_touch">
                                    <h5 class="modal-title" id="ModalArticulosTitulo"><i class="fas fa-box"></i> Lista de artículos</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
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
                                                            <th>Código</th>
                                                            <th>Nombre</th>
                                                            <!-- <th>Marca</th> -->
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
                                                            <th>Código</th>
                                                            <th>Nombre</th>
                                                            <!-- <th>Marca</th> -->
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
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fas fa-times"></i> Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Articulos para Mantenimiento -->
                        <div class="modal fade" id="ModalArticulosMantenimiento" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalArticulosMantenimientoTitle">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header dragable_touch">
                                        <h5 class="modal-title" id="ModalArticulosMantenimientoTitulo"><i class="fas fa-tools"></i> Lista de activos para mantenimiento</h5>
                                        <button class="close" data-dismiss="modal" aria-label="Close">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table id="tbllistarActivosMantenimiento" class="table table-bordered table-striped display nowrap" style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Id</th>
                                                                <th>Código</th>
                                                                <th>Nombre</th>
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
                                                                <th>Código</th>
                                                                <th>Nombre</th>
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
        </div>
        </section>
    </div>
    <?php require_once("../Layouts/Footer.php"); ?>
    <script src="movimiento.js"></script>
    </div>
</body>

</html>