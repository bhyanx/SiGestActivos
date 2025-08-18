<?php

session_start();

// ? VISTA PARA REVISAR, AGREGAR Y EDITAR AMBIENTES DE ACTIVOS POR SUCURSALES

// ? REVISION

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once("../Layouts/Header.php"); ?>
    <title>Categorias - Sistema Gestion de Activos</title>
</head>

<body class="sidebar-mini control-sidebar-slide-open layout-navbar-fixed layout-fixed sidebar-mini-xs sidebar-mini-md sidebar-collapse">
    <div class="wrapper">
        <?php require_once("../Layouts/Head-Body.php"); ?>
        <?php require_once("../Layouts/SideBar.php"); ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="content-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Revisión de Categorias</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="#">Inicio</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Categorias
                                </li>
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
                                    <h3 class="card-title"><i class="fa fa-list"></i>Listado de Categorias en el sistema</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12" id="divfiltros">
                                            <div class="row">
                                                <div class="col-md-3 offset-md-9">
                                                    <div class="form-group">
                                                        <!--<input type="hidden" id="cod_empresa" value="<?php //echo $_SESSION['cod_empresa'] ?? ''; ?>">
                                                        <input type="hidden" id="cod_UnidadNeg" value="<?php //echo $_SESSION['cod_UnidadNeg'] ?? ''; ?>"-->
                                                        <button class="btn btn-primary btn-block" id="btnnuevo"><i class="fa fa-plus"></i>Nuevo</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="tblCategorias" class="table table-bordered table-striped mt-4">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>IdCategorias</th>
                                                            <th>Nombre</th>
                                                            <th>Descripcion</th>
                                                            <th>Vida Util Estandar</th>
                                                            <th>Estado</th>
                                                            <th>Código Clase</th>
                                                            <th><i class="fas fa-gears"></i></th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL PARA REGISTRAR CATEGORIAS -->
                    <div class="modal fade" id="ModalCategorias" tabindex="-1" role="dialog" aria-labelledby="ModalCategoriasLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form id="frmCategorias" name="frmCategorias" method="POST">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="tituloModalCategorias"><i class="fa fa-plus-circle"></i> Registrar nueva categoria</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="idCategoria" id="idCategoria" value="0">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nombre">Nombre</label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Herramientas" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="descripcion">Descripcion:</label>
                                                    <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Ej. Llaves, Taladros" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="vidaUtilEstandar">Vida Útil Estándar (años):</label>
                                                    <input type="number" class="form-control" id="vidaUtilEstandar" name="vidaUtilEstandar" min="1" max="100">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="estado">Estado:</label>
                                                    <select class="form-control" id="estado" name="estado" required>
                                                        <option value="1">Activo</option>
                                                        <option value="0">Inactivo</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="codigoClase">Abreviación:</label>
                                                    <input type="text" class="form-control" id="codigoClase" name="codigoClase" placeholder="HER" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" id="btnGuardarCategoria" class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php require_once("../Layouts/Footer.php"); ?>
        <script src="categorias.js"></script>
    </div>
</body>

</html>