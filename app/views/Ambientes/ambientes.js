$(document).ready(() => {
  init();
});

function init() {
  ListarAmbientes();
}

function ListarAmbientes() {
  $("#tblAmbientes").DataTable({
    aProcessing: true,
    aServerSide: false,
    layout: {
      topStart: {
        buttons: [
          {
            extend: "excelHtml5",
            title: "Listado Ambientes",
            text: "<i class='fas fa-file-excel'></i> Exportar",
            autoFilter: true,
            sheetName: "Data",
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5],
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
    autoWidth: true,
    ajax: {
      url: "../../controllers/AmbienteController.php?action=ListarAmbientes",
      type: "POST",
      dataType: "json",
      data: {
        idAmbiente: "",
        nombre: "",
        descripcion: "",
        NombreSucursal: "",
        estado: "",
      },
      dataSrc: function (json) {
        console.log("Consultar response:", json);
        return json || [];
      },
      error: function (xhr, status, error) {
        console.log("Error en AJAX:", xhr.responseText, status, error);
        Swal.fire(
          "Listar Ambientes",
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
      info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
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
      { data: "idAmbiente", visible: false, searchable: false },
      { data: "nombre" },
      { data: "descripcion" },
      { data: "NombreSucursal" },
      {
        data: "estado",
        render: function (data, type, row) {
          return data == 1
            ? '<span class="badge badge-success">Activo</span>'
            : '<span class="badge badge-danger">Inactivo</span>';
        },
      },
      {
        data: null,
        orderable: false,
        render: function (data, type, row) {
          return `
            <button class="btn btn-sm btn-primary" onclick="editar(event, ${row.idAmbiente})">
              <i class="fa fa-cogs"></i>
            </button>`;
        },
      },
    ],
  });
}
