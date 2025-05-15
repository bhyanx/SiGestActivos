function init() {
  // Registrar movimiento
  $("#frmMovimiento").on("submit", function (e) {
    e.preventDefault();
    guardaryeditarMovimiento(e);
  });

  // Cargar detalles de movimiento si se selecciona uno
  $("#idMovimientoDetalle").on("change", function () {
    let idMovimiento = $(this).val();
    if (idMovimiento) {
      cargarDetallesMovimiento(idMovimiento);
    } else {
      $("#detallesMovimiento").html(
        "<p>Seleccione un movimiento para ver detalles.</p>"
      );
    }
  });
}

$(document).ready(() => {
  listarMovimientos(); // Cargar listado inicial
  ListarCombos(); // Cargar combos
});

// Botón nuevo movimiento
$("#btnnuevo").click(() => {
  $("#tituloModalMovimiento").html(
    '<i class="fa fa-plus-circle"></i> Registrar Movimiento'
  );
  $("#frmMovimiento")[0].reset(); // Usamos el mismo ID que en el form
  $("#activos").val("").trigger("change"); // Limpiar select múltiple
  $("#ModalMovimiento").modal("show");
});

function ListarCombos() {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=combos",
    type: "POST",
    dataType: "json",

    success: (res) => {
      console.log("Combos response:", res);
      if (res.status) {
        $("#IdTipo").html(res.data.tipoMovimiento).trigger("change");
        $("#sucursal_origen").html(res.data.sucursales).trigger("change");
        $("#sucursal_destino").html(res.data.sucursales).trigger("change");
        $("#autorizador").html(res.data.autorizador).trigger("change");


        $("#sucursal_origen, #sucursal_destino, #IdTipo, #autorizador").select2({
            theme: "bootstrap4",
            dropdownParent: $("#ModalMovimiento .modal-body"),
            width: "100%"
        });
        
      } else {
        Swal.fire(
          "Movimiento de activos",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      console.log("Error en combos:", xhr.responseText, status, error);
      Swal.fire(
        "Movimiento de activos",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

// Función para guardar o editar movimiento
function guardaryeditarMovimiento(e) {
  e.preventDefault();

  const formData = new FormData($("#frmMovimiento")[0]);

  // Añadir UserMod desde PHP (si usas sesión)
  formData.append(
    "userMod",
    "<?php echo $_SESSION['CodEmpleado'] ?? 'usuario_desconocido'; ?>"
  );

  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: (res) => {
      console.log("Respuesta servidor:", res);
      try {
        const response = JSON.parse(res);
        if (response.status) {
          Swal.fire("Movimiento", response.message, "success");
          $("#frmMovimiento")[0].reset(); // Limpiar formulario
          $("#tblMovimientos").DataTable().ajax.reload(); // Recargar tabla
          $("#ModalMovimiento").modal("hide"); // Cerrar modal
        } else {
          Swal.fire("Error", response.message, "error");
        }
      } catch (e) {
        Swal.fire(
          "Error",
          "Hubo un problema procesando la respuesta.",
          "error"
        );
        console.error("No se pudo parsear la respuesta:", res);
      }
    },
    error: (xhr, status, error) => {
      console.error("Error AJAX:", xhr.responseText, status, error);
      Swal.fire("Error", "No se pudo registrar el movimiento.", "error");
    },
  });
}

// Listar movimientos en una tabla DataTable
function listarMovimientos() {
  $("#tblregistros").DataTable({
    // Cambiado a #tblregistros (como en tu HTML)
    dom: "Bfrtip",
    responsive: true,
    destroy: true,
    ajax: {
      url: "../../controllers/GestionarMovimientoController.php?action=listar",
      type: "POST",
      dataType: "json",
      dataSrc: function (json) {
        console.log("Datos recibidos:", json);
        return json || [];
      },
    },
    columns: [
      {
        data: null,
        render: () =>
          '<button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>',
      },
      { data: "IdDetalleMovimiento" },
      { data: "IdActivo" },
      { data: "NombreActivo" },
      { data: "TipoMovimiento" },
      { data: "SucursalAnterior" },
      { data: "SucursalNueva" },
      { data: "AmbienteAnterior" },
      { data: "AmbienteNuevo" },
      { data: "Autorizador" },
      { data: "ResponsableAnterior" },
      { data: "ResponsableNuevo" },
      { data: "FechaMovimiento" },
      { data: "TipoMovimiento" },
      { data: "ResponsableOrigen" },
      { data: "ResponsableDestino" },
      { data: "Estado" },
    ],
    language: {
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

// Cargar detalles de un movimiento específico
function cargarDetallesMovimiento(idMovimiento) {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=listar_detalle",
    type: "POST",
    data: { idMovimiento: idMovimiento },
    dataType: "json",
    success: (res) => {
      console.log("Detalles recibidos:", res);
      if (res.status && res.data.length > 0) {
        let html = "<ul>";
        res.data.forEach((detalle) => {
          html += `
                        <li>
                            Activo: ${detalle.NombreActivo} <br>
                            Sucursal Anterior: ${detalle.SucursalAnterior} → Nueva: ${detalle.SucursalNueva} <br>
                            Responsable: ${detalle.ResponsableNuevo}
                        </li>
                    `;
        });
        html += "</ul>";
        $("#detallesMovimiento").html(html);
      } else {
        $("#detallesMovimiento").html("<p>No hay detalles disponibles.</p>");
      }
    },
    error: (xhr, status, error) => {
      console.error("Error al cargar detalles:", xhr.responseText);
      $("#detallesMovimiento").html("<p>Error al cargar detalles.</p>");
    },
  });
}

// function ListarCombos() {
//   $.ajax({
//     url: "../../controllers/",
//   });
// }

// Mostrar detalles desde tabla
function verDetalles(idMovimiento) {
  $("#idMovimientoDetalle").val(idMovimiento).trigger("change");
  $("#movimientoDetalleModal").modal("show");
}

init();