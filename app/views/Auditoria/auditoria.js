$(document).ready(() => {
  init();
});

function init() {
  //   ListarCombosAcciones("AccionesAuditoria");
  listarLogsAuditoria();
}

function ListarAcciones() {
  $("#tblAuditorias").DataTable({
    dom: "Bfrtip",
    responsive: true,
    lengthChange: false,
    colReorder: true,
    autoWidth: false,
    buttons: [
      {
        extends: "excelHtml5",
        title: "Listado de auditorias",
        text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
        autoFilter: true,
        sheetName: "data",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6, 7, 8],
        },
      },
      "pageLength",
    ],
    ajax: {
      url: "app/controllers/AuditoriaController.php?action=Consultar",
      type: "POST",
      dataType: "json",
      data: {
        idLog: "",
        usuario: "",
        Nombre: "",
        accion: "",
        Tabla: "",
        IdRegistro: "",
        Fecha: "",
        Detalle: "",
      },
      dataSrc: function (json) {
        console.log("Consultar Response:", json);
        return json || [];
      },
      error: function (xhr, status, error) {
        console.log("Error en AJAX:", xhr.responseText, status, error);
        Swal.fire(
          "Listar auditorias",
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
    languaje: {
      prosessing: "Procesando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron resultados",
      emptyTable: "Ningún dato disponible en esta tabla",
      infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      infoFiltered: "(filtrado de un total de _MAX_ registros)",
      search: "Buscar:",
      info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      paginate: {
        first: "Primero",
        previous: "Anterior",
        next: "Siguiente",
        last: "Ultimo",
      },
    },
    columnDefs: [
      {
        targets: 0,
        data: null,
        render: function (data, type, row) {
          return (
            '<div class="text-center"><button class="btn btn-primary btn-sm" onclick="verDetalles(' +
            data.IdLog +
            ');"><i class="fas fa-eye"></i></button></div>'
          );
        },
      },
    ],
  });
}

// function ListarCombosAcciones(elemento) {
//     $.ajax({
//         url: "/app/controllers/AuditoriaController.php?action=combos",
//         type: "POST",
//         dataType: "json",
//         async: false,

//         success: (res) => {
//             if (res.status) {
//                     $(`#${elemento}`).html(res.data.accionesAuditoria).trigger("change");
//             } else {
//                 Swal.fire(
//                     "Filtro de acciones de auditoria",
//                     "No se pudieron cargar los combos: " + res.message,
//                     "warning"
//                 );
//             }
//         },
//         error: (xhr, status, error) => {
//             Swal.fire(
//                 "Filtro de acciones de auditoria",
//                 "Error al cargar combos: " + error,
//                 "error"
//             );
//         },
//     });
// }

function listarLogsAuditoria() {
  $("#tblAuditorias").DataTable({
    dom: "Bfrtip",
    responsive: true,
    destroy: true,
    ajax: {
      url: "../../controllers/AuditoriaController.php?action=Consultar",
      type: "POST",
      dataType: "json",
      dataSrc: function (json) {
        return json || [];
      },
    },
    columns: [
      {
        data: null,
        render: () =>
          '<button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>',
      },

      { data: "idLog" },
      { data: "usuario" },
      { data: "NombreTrabajador" },
      { data: "accion" },
      { data: "tabla" },
      { data: "idRegistro" },
      { data: "fecha" },
      { data: "detalle" },
    ],
    languaje: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> Exportar',
      },
    ],
  });
}
