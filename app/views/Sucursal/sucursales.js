function init() {}

$(document).ready(() => {
  listarSucursales();
});

function listarSucursales() {
  $("#tblSucursales").DataTable({
    aProcessing: true,
    aServerSide: false,
    layout: {
      topStart: {
        buttons: [
          {
            extend: "excelHtml5",
            title: "Listado Sucursales",
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
      url: "../../controllers/SucursalesController.php?action=Listar",
      type: "POST",
      dataType: "json",
      data: function(d) {
        return {
          cod_empresa: $("#cod_empresa").val() || null,
          cod_UnidadNeg: "",
          Nombre_local: "",
          direccion: "",
          estado: ""
        };
      },
      dataSrc: function (json) {
        console.log("Consultar response:", json); // Para depuración
        return json || [];
      },
      error: function (xhr, status, error) {
        console.log("Error en AJAX:", xhr.responseText, status, error);
        Swal.fire(
          "Gestionar Sucursales",
          "Error al cargar datos: " + error,
          "error"
        );
      },
    },
    bDestroy: true,
    bInfo: true,
    iDisplayLength: 10,
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
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "cod_UnidadNeg", visible: false, searchable: false },
      { data: "Nombre_local" },
      { data: "Direccion_local" },
      {
        data: "estadoFuncionamiento",
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
            row.cod_UnidadNeg +
            ')"><i class="fa fa-cogs"></i></button>'
          );
        },
      },
    ],
  });
}

init();
