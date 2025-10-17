function init() {}


const CONFIGURACION = {
  URLS: {
    //CONTROLADOR: "../../controllers/AmbienteController.php",
    IDIOMA_DATATABLES: "../../../public/plugins/datatables/json/Spanish.json",
  },
  VALORES_POR_DEFECTO: {
    ESTADO_ACTIVO: 1,
    ESTADO_INACTIVO: 0,
    LONGITUD_TABLA: 10,
  },
};
//? INICIALIZAR MIS FUNCIONES
$(document).ready(function () {
    listarProveedores();
})

function listarProveedores() {
  $("#tblProveedores").DataTable({
    aProcessing: true,
    aServerSide: false,
    layout: {
      topStart: {
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
      url: CONFIGURACION.URLS.IDIOMA_DATATABLES,
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "Documento"},
      { data: "RazonSocial"},
      { data: "DescTipoEntExt"},
      {
        data: null,
        render: function (data, type, row) {
          return (
            '<button class="btn btn-sm btn-primary" onclick="editar(event, ' +
            row.Documento +
            ')"><i class="fa fa-cogs"></i></button>'
          );
        },
      },
    ],
  });
}

init();
