function init() {
    $("#frmmantenimiento").on('submit', (e) => {
        guardaryeditar(e);
    });

    // Evento para cargar artículos cuando cambia IdDocIngresoAlm
    $("#IdDocIngresoAlm").on('change', function () {
        let IdDocIngresoAlm = $(this).val();
        if (IdDocIngresoAlm) {
            cargarArticulosPorDocIngreso(IdDocIngresoAlm);
        } else {
            $("#IdArticulo").html('<option value="">Seleccione</option>').trigger('change');
        }
    });
}

$(document).ready(() => {
    Listar();
    ListarCombos();
});

function ListarCombos() {
    $.ajax({
        url: '../../controllers/GestionarActivosController.php?action=combos', // Ruta corregida
        type: 'POST',
        dataType: 'json',
        success: (res) => {
            console.log('Combos response:', res);
            if (res.status) {
                $("#IdDocIngresoAlm").html(res.data.docIngresoAlm);
                $("#IdEstado").html(res.data.estados);
                $("#IdProveedor").html(res.data.proveedores);
                $("#IdSucursal").html(res.data.sucursales);
                $("#IdAmbiente").html(res.data.ambientes);
                $("#IdCategoria").html(res.data.categorias);
                $("#IdArticulo").html('<option value="">Seleccione un documento primero</option>');

                $("#IdDocIngresoAlm, #IdArticulo, #IdEstado, #IdProveedor, #IdSucursal, #IdAmbiente, #IdCategoria").select2({
                    theme: "bootstrap4",
                    dropdownParent: $("#ModalMantenimiento"),
                });
            } else {
                Swal.fire('Mantenimiento Activos', 'No se pudieron cargar los combos: ' + res.message, 'warning');
            }
        },
        error: (xhr, status, error) => {
            console.log('Error en combos:', xhr.responseText, status, error);
            Swal.fire('Mantenimiento Activos', 'Error al cargar combos: ' + error, 'error');
        }
    });
}

function cargarArticulosPorDocIngreso(IdDocIngresoAlm) {
    $.ajax({
        url: '../../controllers/GestionarActivosController.php?action=articulos_por_doc',
        type: 'POST',
        data: { IdDocIngresoAlm: IdDocIngresoAlm },
        dataType: 'json',
        success: (res) => {
            console.log('Artículos response:', res);
            if (res.status) {
                $("#IdArticulo").html(res.data.articulos).trigger('change');
            } else {
                $("#IdArticulo").html('<option value="">No hay artículos disponibles</option>').trigger('change');
                Swal.fire('Mantenimiento Activos', res.message, 'warning');
            }
        },
        error: (xhr, status, error) => {
            console.log('Error en artículos:', xhr.responseText, status, error);
            Swal.fire('Mantenimiento Activos', 'Error al cargar artículos: ' + error, 'error');
        }
    });
}

function Listar() {
    tabla = $("#tblregistros").dataTable({
        dom: 'Bfrtip',
        searching: true,
        responsive: true,
        lengthChange: false,
        colReorder: true,
        autoWidth: false,
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Listado Activos',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                autoFilter: true,
                sheetName: 'Data',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            },
            "pageLength"
        ],
        ajax: {
            url: '../../controllers/GestionarActivosController.php?action=Consultar',
            type: "POST",
            dataType: "json",
            data: {
                pCodigo: '',
                pIdSucursal: null,
                pIdCategoria: null,
                pIdEstado: null
            },
            dataSrc: function(json) {
                console.log('Consultar response:', json); // Para depuración
                return json || [];
            },
            error: function (xhr, status, error) {
                console.log('Error en AJAX:', xhr.responseText, status, error);
                Swal.fire('Mantenimiento Activos', 'Error al cargar datos: ' + error, 'error');
            }
        },
        bDestroy: true,
        responsive: true,
        bInfo: true,
        iDisplayLength: 10,
        autoWidth: false,
        language: {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún dato disponible en esta tabla",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            search: "Buscar:",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        },
        columnDefs: [
            {
                targets: 0,
                data: null,
                render: function (data, type, row) {
                    return '<button class="btn btn-sm btn-primary" onclick="editar(event, ' + row.idActivo + ')"><i class="fa fa-edit"></i></button>';
                }
            },
            { targets: 1, data: 'idActivo' },
            { targets: 2, data: 'CodigoActivo' },
            { targets: 3, data: 'NumeroSerie' },
            { targets: 4, data: 'NombreArticulo' },
            { targets: 5, data: 'Sucursal' },
            { targets: 6, data: 'Estado' },
            { targets: 7, data: 'valorSoles' }
        ]
    }).DataTable();
}

$("#btnnuevo").click(() => {
    $("#tituloModalMantenimiento").html('<i class="fa fa-plus-circle"></i> Registrar Activo');
    $("#frmmantenimiento")[0].reset();
    $("#IdActivo").val('0');
    $("#IdDocIngresoAlm").val('').trigger('change');
    $("#IdArticulo").html('<option value="">Seleccione un documento primero</option>').trigger('change');
    $("#ModalMantenimiento").modal('show');
});

function guardaryeditar(e) {
    e.preventDefault();
    var frmmantenimiento = new FormData($("#frmmantenimiento")[0]);
    frmmantenimiento.append('UserMod', '<?php echo $_SESSION["CodEmpleado"]; ?>');
    frmmantenimiento.append('action', $("#IdActivo").val() == '0' ? 'Registrar' : 'Actualizar');

    $.ajax({
        url: '../../controllers/GestionarActivosController.php',
        type: 'POST',
        data: frmmantenimiento,
        contentType: false,
        processData: false,
        success: (res) => {
            console.log('Guardar response:', res);
            res = JSON.parse(res);
            if (res.status) {
                $("#frmmantenimiento")[0].reset();
                $("#tblregistros").DataTable().ajax.reload();
                $("#ModalMantenimiento").modal('hide');
                Swal.fire('Mantenimiento Activos', res.message, 'success');
            } else {
                Swal.fire('Mantenimiento Activos', res.message, 'error');
            }
        },
        error: (xhr, status, error) => {
            console.log('Error en guardar:', xhr.responseText, status, error);
            Swal.fire('Mantenimiento Activos', 'Error al guardar: ' + error, 'error');
        }
    });
}

function editar(event, IdActivo) {
    event.preventDefault();
    $("#tituloModalMantenimiento").html('<i class="fa fa-edit"></i> Editar Activo');
    $.ajax({
        url: '../../controllers/GestionarActivosController.php?action=get_activo',
        type: 'POST',
        data: { IdActivo: IdActivo },
        dataType: 'json',
        success: (res) => {
            console.log('Editar response:', res);
            if (res.status) {
                let data = res.data;
                $("#IdActivo").val(data.idActivo); // Cambiar a data.IdActivo si es necesario
                $("#IdDocIngresoAlm").val(data.IdDocIngresoAlm).trigger('change');
                if (data.IdDocIngresoAlm) {
                    cargarArticulosPorDocIngreso(data.IdDocIngresoAlm, () => {
                        $("#IdArticulo").val(data.IdArticulo).trigger('change');
                    });
                }
                $("#Codigo").val(data.CodigoActivo); // Cambiar a data.Codigo si es necesario
                $("#Serie").val(data.NumeroSerie); // Cambiar a data.Serie si es necesario
                $("#IdEstado").val(data.idEstado).trigger('change'); // Cambiar a data.IdEstado
                $("#Garantia").val(data.garantia); // Cambiar a data.Garantia
                $("#FechaFinGarantia").val(data.fechaFinGarantia); // Cambiar a data.FechaFinGarantia
                $("#IdProveedor").val(data.IdProveedor).trigger('change');
                $("#Observaciones").val(data.observaciones); // Cambiar a data.Observaciones
                $("#IdSucursal").val(data.idSucursal).trigger('change'); // Cambiar a data.IdSucursal
                $("#IdAmbiente").val(data.idAmbiente).trigger('change'); // Cambiar a data.IdAmbiente
                $("#IdCategoria").val(data.idCategoria).trigger('change'); // Cambiar a data.IdCategoria
                $("#VidaUtil").val(data.vidaUtil); // Cambiar a data.VidaUtil
                $("#ValorAdquisicion").val(data.valorSoles); // Cambiar a data.ValorAdquisicion
                $("#FechaAdquisicion").val(data.fechaAdquisicion); // Cambiar a data.FechaAdquisicion
                $("#ModalMantenimiento").modal('show');
            } else {
                Swal.fire('Mantenimiento Activos', 'No se pudo obtener el activo: ' + res.message, 'warning');
            }
        },
        error: (xhr, status, error) => {
            console.log('Error en editar:', xhr.responseText, status, error);
            Swal.fire('Mantenimiento Activos', 'Error al obtener activo: ' + error, 'error');
        }
    });
}

function cargarArticulosPorDocIngreso(IdDocIngresoAlm, callback) {
    $.ajax({
        url: '../../controllers/GestionarActivosController.php?action=articulos_por_doc',
        type: 'POST',
        data: { IdDocIngresoAlm: IdDocIngresoAlm },
        dataType: 'json',
        success: (res) => {
            console.log('Artículos response:', res);
            if (res.status) {
                $("#IdArticulo").html(res.data.articulos).trigger('change');
                if (callback) callback();
            } else {
                $("#IdArticulo").html('<option value="">No hay artículos disponibles</option>').trigger('change');
                Swal.fire('Mantenimiento Activos', res.message, 'warning');
            }
        },
        error: (xhr, status, error) => {
            console.log('Error en artículos:', xhr.responseText, status, error);
            Swal.fire('Mantenimiento Activos', 'Error al cargar artículos: ' + error, 'error');
        }
    });
}

init();