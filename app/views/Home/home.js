$(document).ready(function () {
  init();
});

function init() {
  listarActivosTable();
  Dashboard();
}

function listarActivosTable() {
  $("#tblRegistros").DataTable({
    aProcessing: true,
    //responsive: true,
    aServerSide: true,
    layout: {
      topStart: {
        buttons: [
          {
            extend: "excelHtml5",
            title: "Listado Activos",
            text: "<i class='fas fa-file-excel'></i> Exportar",
            autoFilter: true,
            sheetName: "Data",
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
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
    lengthChange: false,
    colReorder: true,
    autoWidth: false,
    destroy: true,
    ajax: {
      url: "../../controllers/DashboardController.php?action=ConsultarResumenActivos",
      type: "POST",
      data: {
        IdArticulo: "",
        IdActivo: "",
      },
      dataType: "json",
      dataSrc: function (json) {
        return json || [];
      },
    },
    columns: [
      {
        data: null,
        render: (data, type, row, meta) => meta.row + 1, // Números desde 1
        className: "text-center",
        title: "#",
      },
      { data: "idActivo", visible: false, searchable: false },
      { data: "CodigoActivo" },
      { data: "NumeroSerie" },
      { data: "NombreActivoVisible" },
      { data: "Marca" },
      { data: "Sucursal" },
      { data: "Proveedor" },
      { data: "Estado" },
      { data: "valorAdquisicion" },
      { data: "idResponsable", visible: false, searchable: false },
      { data: "NombreResponsable" },
      {
        data: "TotalRelacionadosPorArticulo",
        visible: false,
        searchable: false,
      },
      { data: "TotalRelacionadosPorPadre", visible: false, searchable: false },
      { data: "idArticulo", visible: false, searchable: false },
      { data: "idAmbiente", visible: false, searchable: false },
      { data: "idCategoria", visible: false, searchable: false },
      { data: "DocIngresoAlmacen", visible: false, searchable: false },
      { data: "fechaAdquisicion", visible: false, searchable: false },
      { data: "observaciones", visible: false, searchable: false },
    ],
  });
}

function Dashboard() {
  $.ajax({
    url: "../../controllers/DashboardController.php?action=ConteoDashboard",
    type: "POST",
    async: false,
    success: (res) => {
      res = JSON.parse(res);
      if (res.length > 0) {
        for (let i = 0; i < res.length; i++) {
          const element = res[i];
          if (res[i]["Estado"] == "Operativa") {
            $("#lblcantidadoperativos").html(res[i]["Cantidad"]);
          } else if (res[i]["Estado"] == "Reparacion") {
            $("#lblcantidadactivosmantenimiento").html(res[i]["Cantidad"]);
          } else if (res[i]["Estado"] == "Baja") {
            $("#lblcantidadactivosbaja").html(res[i]["Cantidad"]);
          } else {
            $("#lblcantidadactivos").html(res[i]["Cantidad"]);
          }
        }
      } else {
        $("#lblcantidadoperativos").html(0);
        $("#lblcantidadactivosmantenimiento").html(0);
        $("#lblcantidadactivosbaja").html(0);
        $("#lblcantidadactivos").html(0);
      }
    },
  });
}
