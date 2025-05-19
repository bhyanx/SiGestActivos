// Cambia el evento submit para decidir si es guardar o editar
function init() {
  $("#frmmantenimiento").on("submit", (e) => {
    if ($("#idActivo").val() == "0" || $("#idActivo").val() == "") {
      guardarActivo(e);
    } else {
      editarActivo(e);
    }
  });

  // Evento para cargar artículos cuando cambia IdDocIngresoAlm
  $("#IdDocIngresoAlm").on("change", function () {
    let IdDocIngresoAlm = $(this).val();
    if (IdDocIngresoAlm) {
      cargarArticulosPorDocIngreso(IdDocIngresoAlm);
    } else {
      $("#IdArticulo")
        .html('<option value="">Seleccione</option>')
        .trigger("change");
    }
  });
}

$(document).ready(() => {
  Listar();
  ListarCombos();
});

function ListarCombos() {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      console.log("Combos response:", res);
      if (res.status) {
        $("#IdDocIngresoAlm").html(res.data.docIngresoAlm).trigger("change");
        $("#IdEstado").html(res.data.estados).trigger("change");
        $("#IdProveedor").html(res.data.proveedores).trigger("change");
        $("#IdSucursal").html(res.data.sucursales).trigger("change");
        $("#IdAmbiente").html(res.data.ambientes).trigger("change");
        $("#IdCategoria").html(res.data.categorias).trigger("change");
        $("#IdArticulo").html(
          '<option value="">Seleccione un documento primero</option>'
        );
        $(
          "#IdDocIngresoAlm, #IdArticulo, #IdEstado, #IdProveedor, #IdSucursal, #IdAmbiente, #IdCategoria"
        ).select2({
          theme: "bootstrap4",
          dropdownParent: $("#ModalMantenimiento .modal-content"),
          width: "100%",
        });
      } else {
        Swal.fire(
          "Mantenimiento Activos",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      console.log("Error en combos:", xhr.responseText, status, error);
      Swal.fire(
        "Mantenimiento Activos",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

function Listar() {
  tabla = $("#tblregistros")
    .dataTable({
      aProcessing: true,
      aServerSide: true,
      dom: "Bfrtip",
      searching: true,
      responsive: false,
      lengthChange: false,
      colReorder: false,
      autoWidth: true,
      buttons: [
        {
          extend: "copyHtml5",
          title: "Prueba de listado",
          text: '<i class="fas fa-copy"></i> Copiar',
          title: "Copiar",
          sheetName: "Data",
          exportOptions: {
            columns: [1, 2, 3, 4, 5, 6, 7],
          },
        },
        {
          extend: "excelHtml5",
          title: "Listado Activos",
          text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
          autoFilter: true,
          sheetName: "Data",
          exportOptions: {
            columns: [1, 2, 3, 4, 5, 6, 7],
          },
        },
        "pageLength",
      ],
      ajax: {
        url: "../../controllers/GestionarActivosController.php?action=Consultar",
        type: "POST",
        dataType: "json",
        data: {
          pCodigo: "",
          pIdSucursal: null,
          pIdCategoria: null,
          pIdEstado: null,
        },
        dataSrc: function (json) {
          console.log("Consultar response:", json); // Para depuración
          return json || [];
        },
        error: function (xhr, status, error) {
          console.log("Error en AJAX:", xhr.responseText, status, error);
          Swal.fire(
            "Mantenimiento Activos",
            "Error al cargar datos: " + error,
            "error"
          );
        },
      },
      bDestroy: true,
      responsive: false,
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
            return `
      <div class="btn-group">
        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-cogs"></i>
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="#" onclick="editar(event, ${row.idActivo})"><i class="fa fa-edit"></i> Editar</a>
          <a class="dropdown-item" href="#" onclick="detalleActivo(${row.idActivo})"><i class="fa fa-eye"></i> Detalle</a>
          <a class="dropdown-item" href="#" onclick="fichaActivo(${row.idActivo})"><i class="fa fa-file"></i> Ficha</a>
        </div>
      </div>
    `;
          },
        },
        { targets: 1, data: "idActivo" },
        { targets: 2, data: "CodigoActivo" },
        { targets: 3, data: "NumeroSerie" },
        { targets: 4, data: "NombreArticulo" },
        { targets: 5, data: "MarcaArticulo" },
        { targets: 6, data: "Sucursal" },
        { targets: 7, data: "Proveedor" },
        {
          targets: 8,
          data: "Estado",
          render: function (data, type, row) {
            let clase = "";
            switch (data) {
              case "Operativa":
                clase = "badge-success";
                break;
              case "Reparación":
                clase = "bg-warning text-dark";
                break;
              case "Baja":
                clase = "bg-danger";
                break;
              case "Vendido":
                clase = "bg-secondary";
                break;
              case "Regular":
                clase = "bg-info text-dark";
                break;
              case "Malo":
                clase = "bg-danger";
                break;

              default:
                clase = "bg-light text-dark";
            }
            return `<span class="badge ${clase}">${data}</span>`;
          },
        },
        { targets: 9, data: "valorSoles" },
      ],
    })
    .DataTable();
}

$("#btnnuevo").click(() => {
  $("#tituloModalMantenimiento").html(
    '<i class="fa fa-plus-circle"></i> Registrar Activo'
  );
  $("#frmmantenimiento")[0].reset();
  $("#IdActivo").val("0");
  $("#IdDocIngresoAlm").val("").trigger("change");
  $("#IdArticulo")
    .html('<option value="">Seleccione un documento primero</option>')
    .trigger("change");
  $("#ModalMantenimiento").modal("show");
});

function guardarActivo(e) {
  e.preventDefault();
  let frmmantenimiento = new FormData($("#frmmantenimiento")[0]);
  //frmmantenimiento.append("UserMod", '<?php echo $_SESSION["CodEmpleado"]; ?>');
  frmmantenimiento.append("UserMod", userMod);
  frmmantenimiento.append("action", "Registrar");

  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=Registrar",
    type: "POST",
    data: frmmantenimiento,
    contentType: false,
    processData: false,
    success: (res) => {
      console.log("Guardar response:", res);
      res = JSON.parse(JSON.stringify(res));
      if (res.status) {
        $("#frmmantenimiento")[0].reset();
        $("#tblregistros").DataTable().ajax.reload();
        $("#ModalMantenimiento").modal("hide");
        Swal.fire("Mantenimiento Activos", res.message, "success");
      } else {
        Swal.fire("Mantenimiento Activos", res.message, "error");
      }
    },
    error: (xhr, status, error) => {
      console.log("Error en guardar:", xhr.responseText, status, error);
      Swal.fire("Mantenimiento Activos", "Error al guardar: " + error, "error");
    },
  });
}

function editarActivo(e) {
  e.preventDefault();
  let frmmantenimiento = new FormData($("#frmmantenimiento")[0]);
  frmmantenimiento.append("UserMod", userMod);
  frmmantenimiento.append("action", "Actualizar");

  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=Actualizar",
    type: "POST",
    data: frmmantenimiento,
    contentType: false,
    processData: false,
    success: (res) => {
      console.log("Editar response:", res);
      res = JSON.parse(JSON.stringify(res));
      if (res.status) {
        $("#frmmantenimiento")[0].reset();
        $("#tblregistros").DataTable().ajax.reload();
        $("#ModalMantenimiento").modal("hide");
        Swal.fire("Mantenimiento Activos", res.message, "success");
      } else {
        Swal.fire("Mantenimiento Activos", res.message, "error");
      }
    },
    error: (xhr, status, error) => {
      console.log("Error en editar:", xhr.responseText, status, error);
      Swal.fire("Mantenimiento Activos", "Error al editar: " + error, "error");
    },
  });
}

function ListarCombosConCallback(callback) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status) {
        $("#IdDocIngresoAlm").html(res.data.docIngresoAlm);
        $("#IdEstado").html(res.data.estados);
        $("#IdProveedor").html(res.data.proveedores);
        $("#IdSucursal").html(res.data.sucursales);
        $("#IdAmbiente").html(res.data.ambientes);
        $("#IdCategoria").html(res.data.categorias);
        $("#IdArticulo").html(
          '<option value="">Seleccione un documento primero</option>'
        );

        // Inicializamos Select2 después de cargar los HTML
        $(
          "#IdDocIngresoAlm, #IdArticulo, #IdEstado, #IdProveedor, #IdSucursal, #IdAmbiente, #IdCategoria"
        ).select2({
          theme: "bootstrap4",
          dropdownParent: $("#ModalMantenimiento .modal-content"),
          width: "100%",
        });

        if (typeof callback === "function") callback();
      } else {
        Swal.fire(
          "Mantenimiento Activos",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      Swal.fire(
        "Mantenimiento Activos",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

function cargarArticulosPorDocIngreso(IdDocIngresoAlm, callback = null) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=articulos_por_doc",
    type: "POST",
    data: { IdDocIngresoAlm },
    dataType: "json",
    success: (res) => {
      console.log("Artículos response:", res);
      if (res.status) {
        $("#IdArticulo").html(res.data.articulos).trigger("change");
        if (typeof callback === "function") callback();
      } else {
        $("#IdArticulo")
          .html('<option value="">No hay artículos disponibles</option>')
          .trigger("change");
        Swal.fire("Mantenimiento Activos", res.message, "warning");
      }
    },
    error: (xhr, status, error) => {
      console.log("Error en artículos:", xhr.responseText, status, error);
      Swal.fire(
        "Mantenimiento Activos",
        "Error al cargar artículos: " + error,
        "error"
      );
    },
  });
}

function editar(event, idActivo) {
  event.preventDefault();
  $("#tituloModalMantenimiento").html(
    '<i class="fa fa-edit"></i> Editar Activo'
  );
  // Carga los combos y luego los datos del activo
  ListarCombosConCallback(() => {
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=get_activo",
      type: "POST",
      data: { idActivo: idActivo },
      dataType: "json",
      success: (res) => {
        if (res.status) {
          let data = res.data;
          console.log("Datos del activo:", data);
          $("#idActivo").val(data.idActivo);
          $("#Codigo").val(data.CodigoActivo);
          $("#Serie").val(data.NumeroSerie);
          $("#Garantia").val(data.Garantia);
          $("#FechaFinGarantia").val(data.FechaFinGarantia);
          $("#Observaciones").val(data.observaciones);
          $("#VidaUtil").val(data.vidaUtil);
          $("#ValorAdquisicion").val(data.valorSoles);
          $("#FechaAdquisicion").val(data.fechaAdquisicion);

          // Asignar combos dependientes
          $("#IdDocIngresoAlm").val(data.DocIngresoAlmacen).trigger("change");
          // Espera a que los artículos se carguen antes de seleccionar el artículo
          setTimeout(() => {
            let idDoc = $("#IdDocIngresoAlm").val();
            if (idDoc) {
              cargarArticulosPorDocIngreso(idDoc, () => {
                $("#IdArticulo").val(data.idArticulo).trigger("change");
              });
            }
          }, 300);

          $("#IdEstado").val(data.idEstado).trigger("change");
          $("#IdProveedor").val(data.Documento).trigger("change");
          $("#IdSucursal").val(data.idSucursal).trigger("change");
          $("#IdAmbiente").val(data.idAmbiente).trigger("change");
          $("#IdCategoria").val(data.idCategoria).trigger("change");

          $("#ModalMantenimiento").modal("show");
        } else {
          Swal.fire(
            "Mantenimiento Activos",
            "No se pudo obtener el activo: " + res.message,
            "warning"
          );
        }
      },
      error: (xhr, status, error) => {
        Swal.fire(
          "Mantenimiento Activos",
          "Error al obtener activo: " + error,
          "error"
        );
      },
    });
  });
}

// Hacer global la función para el onclick
window.editar = editar;

init();
