function init() {}


//? INICIALIZAR MIS FUNCIONES
$(document).ready(function () {
    listarProveedores();
})

function listarProveedores() {
  $("#tblProveedores").DataTable({
    dom: "Bfrtip",
    responsive: true,
    lengthChange: false,
    colReorder: true,
    autoWidth: false,
    buttons: [
      {
        extend: "excelHtml5",
        title: "Listado Proveedores",
        text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
        autoFilter: true,
        sheetName: "Data",
        exportOptions: {
          columns: [1, 2, 3],
        },
      },
      "pageLength",
    ],
    ajax: {
      url: "../../controllers/ProveedorController.php?action=ListarProveedores",
      type: "POST",
      dataType: "json",
      data: {
        Documento: "",
        RazonSocial: "",
        DescTipoEntExt: "",
      },
      dataSrc: function (json) {
        console.log("Consultar response:", json); // Para depuración
        return json || [];
      },
      error: function (xhr, status, error) {
        console.log("Error en AJAX:", xhr.responseText, status, error);
        Swal.fire(
          "Gestionar Proveedores",
          "Error al cargar datos: " + error,
          "error"
        );
      },
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
        previous: "Anterior",
      },
    },
    columnDefs: [
      {
        targets: 0,
        data: null,
        render: function (data, type, row) {
          return (
            '<button class="btn btn-sm btn-primary" onclick="editar(event, ' +
            row.Documento +
            ')"><i class="fa fa-edit"></i></button>'
          );
        },
      },
      { targets: 1, data: "Documento"},
      { targets: 2, data: "RazonSocial"},
      { targets: 3, data: "DescTipoEntExt"},
    ],
  });
}

init();
