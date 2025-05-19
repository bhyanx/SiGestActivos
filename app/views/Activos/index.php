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
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fa fa-list"></i> Lista de Activos</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" id="divfiltros">
                                            <div class="row">
                                                <div class="col-md-3 offset-md-9">
                                                    <div class="form-group">
                                                        <button class="btn btn-primary btn-block" id="btnnuevo">
                                                            <i class="fa fa-plus"></i> Nuevo
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="tblregistros" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <!-- <th><i class="fa fa-cogs"></i></th> -->
                                                            <!-- <th>#</th> -->
                                                            <th><i class="fa fa-cogs"></i></th>
                                                            <th>Id Activo</th>
                                                            <th>Código</th>
                                                            <th>Serie</th>
                                                            <th>Nombre</th>
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
                                                            <th>Nombre</th>
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
                    </div>

                    <div class="modal fade" id="ModalMantenimiento" tabindex="-1" role="dialog" aria-labelledby="ModalMantenimientoLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title" id="tituloModalMantenimiento"><i class="fa fa-plus-circle"></i> Registrar Activo</h5>
                                </div>
                                <form id="frmmantenimiento">
                                    <div class="modal-body">
                                        <div class="row">
                                            <input type="hidden" name="IdActivo" id="idActivo" value="0">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="IdDocIngresoAlm">Doc. Ingreso Almacén:</label>
                                                    <select name="IdDocIngresoAlm" id="IdDocIngresoAlm" class="form-control select-2" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="IdArticulo">Artículo:</label>
                                                    <select name="IdArticulo" id="IdArticulo" class="form-select" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="Codigo">Código:</label>
                                                    <input type="text" name="Codigo" id="Codigo" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="Serie">Serie:</label>
                                                    <input type="text" name="Serie" id="Serie" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="IdEstado">Estado:</label>
                                                    <select name="IdEstado" id="IdEstado" class="form-control" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="Garantia">Garantía:</label>
                                                    <input type="text" name="Garantia" id="Garantia" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="FechaFinGarantia">Fin Garantía:</label>
                                                    <input type="date" name="FechaFinGarantia" id="FechaFinGarantia" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="IdProveedor">Proveedor:</label>
                                                    <select name="IdProveedor" id="IdProveedor" class="form-control"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="Observaciones">Observaciones:</label>
                                                    <textarea name="Observaciones" id="Observaciones" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="IdSucursal">Sucursal:</label>
                                                    <select name="IdSucursal" id="IdSucursal" class="form-control" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="IdAmbiente">Ambiente:</label>
                                                    <select name="IdAmbiente" id="IdAmbiente" class="form-control" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="IdCategoria">Categoría:</label>
                                                    <select name="IdCategoria" id="IdCategoria" class="form-control" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="VidaUtil">Vida Útil (meses):</label>
                                                    <input type="number" name="VidaUtil" id="VidaUtil" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ValorAdquisicion">Valor Adquisición:</label>
                                                    <input type="number" step="0.01" name="ValorAdquisicion" id="ValorAdquisicion" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="FechaAdquisicion">Fecha Adquisición:</label>
                                                    <input type="date" name="FechaAdquisicion" id="FechaAdquisicion" class="form-control" required>
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