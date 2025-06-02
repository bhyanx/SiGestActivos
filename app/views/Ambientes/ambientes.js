$(document).ready(() => {
  init();
});

function init() {
  ListarAmbientes();
}

function ListarAmbientes() {
  $("#tblAmbientes").DataTable({
    dom: "Bfrtip",
    responsive: true,
    lengthChange: false,
    colReorder: true,
    autoWidth: true,
    buttons: [
      {
        extend: "excelHtml5",
        title: "Listado Ambientes",
        text: "<i class='fas fa-file-excel'></i> Exportar",
        autoFilter: true,
        sheetName: "Data",
        exportOptions: {
          columns: [1, 2, 3, 4],
        },
      },
      "pageLength",
    ],
    ajax: {
      url: "../../controllers/AmbienteController.php?action=ListarAmbientes",
      type: "POST",
      dataType: "json",
      data: {
        Nombre: "",
        Descripcion: "",
        NombreSucursal: "",
        Estado: "",
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
    columnDefs: [
        {
            targets: 0,
            data: null,
            render: function (data, type, row) {
                return (
                    '<button class="btn btn-sm btn-primary" onclick="editar(event, ' +
                    row.idAmbiente +
                    ')"><i class="fa fa-edit"></i></button>'
                );
            },
        },
        { targets: 1, data: "nombre"},
        { targets: 2, data: "descripcion"},
        { targets: 3, data: "NombreSucursal"},
        { targets: 4, data: "estado"}
    ],
    // ajax: {
    //   url: "../../controllers/AmbienteController.php?action=ListarAmbientes",
    //   type: "POST",
    //   dataType: "json",
    //   dataSrc: function (json) {
    //     return json || [];
    //   },
    // },
    // columns: [
    //   {
    //     data: null,
    //     render: () =>
    //       "<button class='btn btn-sm btn-info'><i class='fas fa-eye'></i></button>",
    //   },
    //   //   { data: "idAmbiente" },
    //   { data: "nombre" },
    //   { data: "descripcion" },
    //   { data: "NombreSucursal" },
    //   {
    //     data: "estado",
    //     render: function (data, type, row) {
    //       let clase = "";
    //       let texto = "";
    //       switch (data) {
    //         case 1:
    //           clase = "btn btn-success";
    //           texto = "Activo";
    //           break;
    //         case 0:
    //           clase = "btn btn-danger";
    //           texto = "Inactivo";
    //           break;

    //         default:
    //           clase = "bg-light text-dark";
    //       }
    //       return `<span class="badge ${clase}">${texto}</span>`;
    //     },
    //   },
    // ],
    // language: {
    //   url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    // },
    // buttons: [
    //   {
    //     extend: "excelHtml5",
    //     text: "<i class='fas fa-file-excel'></i> Exportar",
    //   },
    // ],
  });
}
