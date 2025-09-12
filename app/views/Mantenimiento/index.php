<?php
session_start();

// ? VISTA PARA GESTIONAR LOS MOVIMIENTOS DE LOS ACTIVOS

//! FALTA FUNCIONAMIENTO Y PONER CAMPOS REFERENCIALES A LOS MOVIMIENTOS A NUESTRA VISTA

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Mantenimiento - Sistema Gestion de activos</title>
    <style>
        .swal2-container {
            z-index: 9999 !important;
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

        /* Asegurar que el modal de mantenimiento esté visible */
        #ModalArticulosMantenimiento {
            z-index: 10000 !important;
        }

        #ModalArticulosMantenimiento .modal-backdrop {
            z-index: 9999 !important;
        }

        .modal-backdrop.show {
            z-index: 9998 !important;
        }

        /* Forzar z-index para backdrop de mantenimiento */
        .modal-backdrop {
            z-index: 9998 !important;
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
                            <h1>Gestión de Mantenimiento de Activos</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li class="breadcrumb-item"><a href="../Movimientos/">Movimientos</a></li>
                                <li class="breadcrumb-item active">Mantenimiento</li>
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
                                                    <button type="button" class="btn btn-warning btn-sm btn-block" id="btnmantenimiento">
                                                        <i class="fa fa-tools"></i> Enviar a Mantenimiento
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
                                    <h3 class="card-title" id="tituloTablaMovimientos"><i class="fa fa-list-check"></i> Lista de Movimientos Enviados</h3>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="tblMovimientos" class="table table-bordered table-striped w-100 h-100">
                                                <thead class="table-warning">
                                                    <tr>
                                                        <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                        <th>Código</th>
                                                        <th>Tipo Movimiento</th>
                                                        <th>NombreActivo</th>
                                                        <th>Proveedor</th>
                                                        <th>Autorizador</th>
                                                        <th>Estado Mantenimiento</th>
                                                        <th>Fecha Mantenimiento</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot class="table-warning">
                                                    <tr>
                                                        <th><i class="fa fa-cogs" title="Acciones"></i></th>
                                                        <th>Código</th>
                                                        <th>Tipo Movimiento</th>
                                                        <th>NombreActivo</th>
                                                        <th>Proveedor</th>
                                                        <th>Autorizador</th>
                                                        <th>Estado Mantenimiento</th>
                                                        <th>Fecha Mantenimiento</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Mantenimiento -->
                    <div class="col-12" id="divgenerarmantenimiento" style="display: none;">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-tools"></i> Enviar Activos a Mantenimiento:</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12" id="divfiltrosmantenimiento">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="IdTipoMantenimiento">Tipo de Mantenimiento:</label>
                                                    <select name="IdTipoMantenimiento" id="IdTipoMantenimiento" class="form-control" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="IdEstadoMantenimiento">Estado:</label>
                                                    <select name="IdEstadoMantenimiento" id="IdEstadoMantenimiento" class="form-control" value="1" required disabled></select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="FechaProgramada">Fecha Programada:</label>
                                                    <input type="date" name="FechaProgramada" id="FechaProgramada" class="form-control" min="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="IdProveedor">Proveedor:</label>
                                                    <select name="IdProveedor" id="IdProveedor" class="form-control"></select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="IdResponsableMantenimiento">Responsable:</label>
                                                    <select name="IdResponsableMantenimiento" id="IdResponsableMantenimiento" class="form-control"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="CostoEstimado">Costo Estimado:</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">S/.</span>
                                                        </div>
                                                        <input type="number" name="CostoEstimado" id="CostoEstimado" class="form-control" step="0.01" min="0" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="DescripcionMantenimiento">Descripción:</label>
                                                    <input type="text" name="DescripcionMantenimiento" id="DescripcionMantenimiento" class="form-control" placeholder="Descripción breve del mantenimiento" maxlength="255">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="ObservacionesMantenimiento">Observaciones:</label>
                                                    <textarea name="ObservacionesMantenimiento" id="ObservacionesMantenimiento" class="form-control" rows="3" placeholder="Observaciones adicionales del mantenimiento..." maxlength="500"></textarea>
                                                    <small class="form-text text-muted">
                                                        <span id="contador-caracteres-mant">0</span>/500 caracteres
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="">&nbsp;</label>
                                                    <button type="button" class="btn btn-danger btn-block" id="btncancelarmantenimiento">
                                                        <i class="fas fa-reply"></i> Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="">&nbsp;</label>
                                                    <button type="button" class="btn btn-warning btn-block" id="btnprocesarmantenimiento">
                                                        <i class="fas fa-tools"></i> Procesar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Registro de Mantenimiento -->
                    <div class="col-12" id="divregistroMantenimiento" style="display: none;">
                        <div class="alert alert-warning alert-dismissible">
                            <span id="lblusuariomantenimiento">Usuario enviando a mantenimiento: <?php echo $_SESSION['PrimerNombre'] . ' ' . $_SESSION['ApellidoPaterno'] . ' ' . $_SESSION['ApellidoMaterno']; ?></span>
                            <button type="button" class="close btn" id="btnchangedatamantenimiento"><i class="fas fa-undo-alt"></i></button>
                            <input type="hidden" name="IdTipoMantenimientoHidden" id="IdTipoMantenimientoHidden">
                            <input type="hidden" name="IdEstadoMantenimientoHidden" id="IdEstadoMantenimientoHidden">
                            <input type="hidden" name="IdProveedorHidden" id="IdProveedorHidden">
                            <input type="hidden" name="IdResponsableHidden" id="IdResponsableHidden">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información del Mantenimiento:</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="tipoMantenimientoInfo">Tipo de Mantenimiento:</label>
                                                    <input type="text" class="form-control" id="tipoMantenimientoInfo" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="estadoMantenimientoInfo">Estado:</label>
                                                    <input type="text" class="form-control" id="estadoMantenimientoInfo" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="fechaProgramadaInfo">Fecha Programada:</label>
                                                    <input type="text" class="form-control" id="fechaProgramadaInfo" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="proveedorInfo">Proveedor:</label>
                                                    <input type="text" class="form-control" id="proveedorInfo" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="responsableInfo">Responsable:</label>
                                                    <input type="text" class="form-control" id="responsableInfo" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Detalles Adicionales:</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="costoEstimadoInfo">Costo Estimado:</label>
                                                    <input type="text" class="form-control" id="costoEstimadoInfo" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="descripcionInfo">Descripción:</label>
                                                    <textarea class="form-control" id="descripcionInfo" rows="3" readonly></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="observacionesInfo">Observaciones:</label>
                                                    <textarea class="form-control" id="observacionesInfo" rows="3" readonly></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Activos para Mantenimiento -->
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-list-check"></i> Activos para Mantenimiento</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="">Buscar Activo:</label>
                                                <div class="input-group">
                                                    <input type="text" name="txtbuscaractivomant" id="txtbuscaractivomant" placeholder="ID de Activo" class="form-control">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary" type="button" id="btnBuscarActivoMant"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="">Listar Activos</label>
                                                <button class="btn btn-primary btn-block btnagregaractivomant" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="tblactivosmantenimiento" class="table table-hover table-bordered table-striped table-sm w-100">
                                            <thead class="table-warning">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Código</th>
                                                    <th>Nombre</th>
                                                    <th>Sucursal</th>
                                                    <th>Ambiente</th>
                                                    <th>Estado Actual</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot class="table-warning">
                                                <tr>
                                                    <th colspan="6" style="text-align: right;">TOTAL ACTIVOS:</th>
                                                    <th class="text-center">
                                                        <span id="TotalActivosMantenimiento">0</span>
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="col-md-12 mt-3">
                            <div class="card">
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                                            <button type="button" class="btn btn-danger btn-sm btn-block" id="btnsalirmantenimiento">
                                                <i class="fa fa-times"></i> Cerrar
                                            </button>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <button type="button" class="btn btn-warning btn-sm btn-block" id="btnGuardarMantenimiento">
                                                <i class="fa fa-tools"></i> Enviar a Mantenimiento
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Articulos para Mantenimiento -->
                    <div class="modal fade" id="ModalArticulosMantenimiento" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="ModalArticulosMantenimientoTitle">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
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
                                                    <thead class="table-warning">
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Código</th>
                                                            <th>Nombre</th>
                                                            <th>Sucursal</th>
                                                            <th>Ambiente</th>
                                                            <th><i class="fa fa-screwdriver-wrench"></i></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Aquí se llenarán los datos dinámicamente -->
                                                    </tbody>
                                                    <tfoot class="table-warning">
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Código</th>
                                                            <th>Nombre</th>
                                                            <th>Sucursal</th>
                                                            <th>Ambiente</th>
                                                            <th><i class="fa fa-screwdriver-wrench"></i></th>
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
    <script src="mantenimiento.js"></script>
    </div>
</body>

</html>