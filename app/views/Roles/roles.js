function init() {
  listarRoles();
}

$(document).ready(function () {
  init();
});

function listarRoles() {
  $("#tblRoles").DataTable({
    aProcessing: true,
    aServerSide: false,
    layout: {
      topStart: {
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
          "colvis",
        ],
      },
      bottom: "paging",
      bottomStart: null,
      bottomEnd: null,
    },
    responsive: true,
    lengthChange: false,
    colReorder: true,
    autoWidth: false,
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
    columns: [
      { data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        }
      },
      { data: "IdRol", visible: false, searchable: false },
      { data: "NombreRol" },
      {
        data: "Estado",
        render: function (data, type, row) {
          return data == 1  
            ? '<span class="badge badge-success text-sm border border-success">Activo</span>'
            : '<span class="badge badge-danger text-sm border border-danger">Inactivo</span>';
        },
      },
      {
        data: null,
        orderable: false,
        render: function (data, type, row) {
          return (
            '<button class="btn btn-sm btn-primary" onclick="editar(event, ' +
            row.IdRol +
            ')"><i class="fa fa-cogs"></i></button>'
          );
        },
      },
    ],
  });
}
