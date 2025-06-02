function init() {
  listarRoles();
}

$(document).ready(function () {
  init();
});

function listarRoles() {
  $("#tblRoles").DataTable({
    dom: "Bfrtip",
    responsive: true,
    lengthChange: false,
    colReorder: true,
    autoWidth: false,
    buttons: [
      {
        extend: "excelHtml5",
        title: "Listado Roles",
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
      url: "../../controllers/RolController.php?action=ListarRoles",
      type: "POST",
      dataType: "json",
      data: {
        IdRol: "",
        NombreRol: "",
        Estado: "",
      },
      dataSrc: function (json) {
        console.log("Consultar response:", json);
        return json || [];
      },
      error: function (xhr, status, error) {
        console.log("Error en AJAX:", xhr.responseText, status, error);
        Swal.fire("Listar Roles", "Error al cargar datos: " + error, "error");
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
            row.IdRol +
            ')"><i class="fa fa-edit"></i></button>'
          );
        },
      },
      { targets: 1, data: "IdRol", visible: false, searchable: false },
      { targets: 2, data: "NombreRol" },
      {
        targets: 3,
        data: "Estado",
        render: function (data, type, row) {
          if (data == 1 || data === 1) {
            return '<span class="badge badge-success">Activo</span>';
          } else {
            return '<span class="badge badge-danger">Inactivo</span>';
          }
        },
      },
    ],
  });
}
