$(document).ready(function () {
  init();
});

function init() {
  //listarActivosTable();
  Dashboard();
  cargarGraficoActivosAsignados();
}

/*function listarActivosTable() {
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
      { data: "codigo" },
      { data: "Serie" },
      { data: "NombreActivo" },
      //{ data: "Marca" },
      { data: "idSucursal", visible: false, searchable: false },
      { data: "idProveedor", visible: false, searchable: false },
      { data: "Estado", visible: false, searchable: false },
      { data: "valorAdquisicion" },
      { data: "Responsable" },
      { data: "idArticulo", visible: false, searchable: false },
      { data: "idAmbiente", visible: false, searchable: false },
      { data: "idCategoria", visible: false, searchable: false },
      //{ data: "DocIngresoAlmacen", visible: false, searchable: false },
      { data: "fechaAdquisicion", visible: false, searchable: false },
      { data: "Observaciones", visible: false, searchable: false },
    ],
  });
}*/

function Dashboard() {
  $.ajax({
    url: "../../controllers/DashboardController.php?action=ConteoDashboard",
    type: "POST",
    async: false,
    success: (res) => {
      res = JSON.parse(res);

      // recorrer cada fila del array
      res.forEach((element) => {
        if (element["Estado"] == "Operativa") {
          $("#lblcantidadoperativos").html(parseInt(element["Cantidad"] || 0));
        } else if (element["Estado"] == "Reparación") {
          $("#lblcantidadactivosmantenimiento").html(
            parseInt(element["Cantidad"] || 0)
          );
        } else if (element["Estado"] == "Baja") {
          $("#lblcantidadactivosbaja").html(parseInt(element["Cantidad"] || 0));
        } else if (element["Estado"] == "Total") {
          $("#lblcantidadactivos").html(parseInt(element["Cantidad"] || 0));
        } else if (element["Estado"] == "Valorizacion") {
          $("#lblvalordeactivos").html(
            parseFloat(element["Valor"] || 0).toLocaleString("es-PE", {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })
          );
        }
      });
    },
  });
}

function cargarGraficoActivosAsignados() {
  // Obtener datos de activos asignados
  $.ajax({
    url: "../../controllers/DashboardController.php?action=TotalActivosAsignados",
    type: "POST",
    success: function (response) {
      console.log("Respuesta de activos asignados:", response);
      let dataAsignados;
      try {
        // Intentar parsear la respuesta si viene como string
        dataAsignados =
          typeof response === "string" ? JSON.parse(response) : response;
        console.log("Datos de activos asignados procesados:", dataAsignados);
      } catch (e) {
        console.error("Error al parsear datos de activos asignados:", e);
        dataAsignados = { cantidad: 0 };
      }

      // Obtener datos de activos no asignados
      $.ajax({
        url: "../../controllers/DashboardController.php?action=TotalActivosNoAsignados",
        type: "POST",
        success: function (response) {
          console.log("Respuesta de activos no asignados:", response);
          let dataNoAsignados;
          try {
            // Intentar parsear la respuesta si viene como string
            dataNoAsignados =
              typeof response === "string" ? JSON.parse(response) : response;
            console.log(
              "Datos de activos no asignados procesados:",
              dataNoAsignados
            );
          } catch (e) {
            console.error("Error al parsear datos de activos no asignados:", e);
            dataNoAsignados = { cantidad: 0 };
          }

          // Procesar datos para el gráfico
          const cantidadAsignados =
            dataAsignados && dataAsignados.cantidad !== undefined
              ? dataAsignados.cantidad
              : 0;
          const cantidadNoAsignados =
            dataNoAsignados && dataNoAsignados.cantidad !== undefined
              ? dataNoAsignados.cantidad
              : 0;

          console.log("Cantidad de activos asignados:", cantidadAsignados);
          console.log("Cantidad de activos no asignados:", cantidadNoAsignados);

          // Crear el gráfico
          crearGraficoDistribucion(cantidadAsignados, cantidadNoAsignados);
        },
        error: function (error) {
          console.error("Error al obtener activos no asignados:", error);
        },
      });
    },
    error: function (error) {
      console.error("Error al obtener activos asignados:", error);
    },
  });
}

function crearGraficoDistribucion(cantidadAsignados, cantidadNoAsignados) {
  // Obtener el contexto del canvas
  const ctx = document
    .getElementById("graficoActivosAsignados")
    .getContext("2d");

  // Destruir el gráfico si ya existe
  if (window.graficoActivos) {
    window.graficoActivos.destroy();
  }

  // Crear nuevo gráfico
  window.graficoActivos = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: ["Activos Asignados", "Activos No Asignados"],
      datasets: [
        {
          data: [cantidadAsignados, cantidadNoAsignados],
          backgroundColor: [
            "#28a745", // Verde para asignados
            "#dc3545", // Rojo para no asignados
          ],
          borderColor: ["#ffffff", "#ffffff"],
          borderWidth: 2,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            font: {
              size: 14,
            },
          },
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const label = context.label || "";
              const value = context.raw || 0;
              const total = context.dataset.data.reduce(
                (acc, val) => acc + val,
                0
              );
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            },
          },
        },
      },
    },
  });
}
