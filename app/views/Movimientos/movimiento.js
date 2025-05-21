$(document).ready(function () {
  init();
});

function init() {
  listarMovimientos();
  ListarCombos();
  ListarCombosFiltros();

  // Botón para abrir modal de nuevo movimiento
  $("#btnnuevo").click(() => {
    $("#tituloModalMovimiento").html('<i class="fa fa-plus-circle"></i> Registrar Movimiento');
    $("#frmMovimiento")[0].reset();
    $("#ModalMovimiento").modal("show");
  });

  // Guardar movimiento principal
  $("#frmMovimiento").on("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
      url: "../../controllers/GestionarMovimientoController.php?action=RegistrarMovimientoSolo",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (res) {
        if (res.status) {
          // Guardar el ID del movimiento para el detalle
          $("#IdMovimientoDetalle").val(res.idMovimiento);
          // Autocompletar campos de destino en el modal de detalle
          setDestinoDetalle();
          $("#ModalMovimiento").modal("hide");
          $("#frmDetalleMovimiento")[0].reset();
          $("#ModalDetalleMovimiento").modal("show");
        } else {
          Swal.fire("Error", res.message, "error");
        }
      },
      error: function () {
        Swal.fire("Error", "No se pudo registrar el movimiento.", "error");
      },
    });
  });

  // Al abrir el modal de detalle, autocompleta los campos de destino
  $("#ModalDetalleMovimiento").on("show.bs.modal", function () {
    setDestinoDetalle();
  });

  // Al seleccionar un activo, autocompleta los datos de ese activo
  $("#IdActivo").on("change", function () {
    let idActivo = $(this).val();
    if (idActivo) {
      $.ajax({
        url: "../../controllers/GestionarActivosController.php?action=obtenerInfoActivo",
        type: "POST",
        data: { idActivo: idActivo },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            $("#CodigoActivo").val(res.data.CodigoActivo || "");
            $("#SucursalActual").val(res.data.SucursalActual || "");
            $("#AmbienteActual").val(res.data.AmbienteActual || "");
          } else {
            $("#CodigoActivo, #SucursalActual, #AmbienteActual").val("");
          }
        },
        error: function () {
          $("#CodigoActivo, #SucursalActual, #AmbienteActual").val("");
        },
      });
    } else {
      $("#CodigoActivo, #SucursalActual, #AmbienteActual").val("");
    }
  });

  // Guardar detalle (agregar activo al movimiento)
  $("#frmDetalleMovimiento").on("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
      url: "../../controllers/GestionarMovimientoController.php?action=AgregarDetalle",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (res) {
        if (res.status) {
          Swal.fire("Éxito", "Activo agregado al movimiento", "success");
          // Limpia solo el select de activo y los campos visuales
          $("#IdActivo").val("").trigger("change");
          $("#CodigoActivo, #SucursalActual, #AmbienteActual").val("");
        } else {
          Swal.fire("Error", res.message, "error");
        }
      },
      error: function () {
        Swal.fire("Error", "No se pudo agregar el activo.", "error");
      },
    });
  });

  // Botón para agregar otro activo (limpia el formulario de detalle)
  $("#btnAgregarOtroActivo").on("click", function () {
    $("#frmDetalleMovimiento")[0].reset();
    setDestinoDetalle();
    $("#CodigoActivo, #SucursalActual, #AmbienteActual").val("");
  });
}

// Autocompleta los campos de destino en el modal de detalle
function setDestinoDetalle() {
  // Toma el texto seleccionado en el formulario principal
  $("#SucursalDestino").val($("#sucursal_destino option:selected").text());
  $("#AmbienteDestino").val($("#ambiente_destino option:selected").text());
}

function ListarCombosFiltros() {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status){
        $("#filtroTipoMovimiento").html(res.data.tipoMovimiento).trigger("change");
        $("#filtroSucursal").html(res.data.sucursales).trigger("change");
        $("#filtroAmbiente").html(res.data.ambientes).trigger("change");
        // $("#filtroSucursalDestino").html(res.data.sucursales).trigger("change");

        $("#filtroTipoMovimiento, #filtroSucursal, #filtroAmbiente").select2({
          theme: "bootstrap4",
          //dropdownParent: $("#ModalFiltros .modal-body"),
          width: "100%",
        });
      } else {
        Swal.fire("Filtro de movimientos", "No se pudieron cargar los combos: " + res.message, "warning");
      }
    },
    error: (xhr, status, error) => {
      Swal.fire("Filtros de movimientos", "Error al cargar combos: " + error, "error");
    }
  })
}

// Cargar combos principales
function ListarCombos() {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status) {
        $("#IdTipo").html(res.data.tipoMovimiento).trigger("change");
        $("#sucursal_origen").html(res.data.sucursales).trigger("change");
        $("#sucursal_destino").html(res.data.sucursales).trigger("change");
        $("#autorizador").html(res.data.autorizador).trigger("change");

        $("#sucursal_origen, #sucursal_destino, #IdTipo, #autorizador").select2({
          theme: "bootstrap4",
          dropdownParent: $("#ModalMovimiento .modal-body"),
          width: "100%",
        });
      } else {
        Swal.fire("Movimiento de activos", "No se pudieron cargar los combos: " + res.message, "warning");
      }
    },
    error: (xhr, status, error) => {
      Swal.fire("Movimiento de activos", "Error al cargar combos: " + error, "error");
    },
  });
}

// Listar movimientos en una tabla DataTable
function listarMovimientos() {
  $("#tblMovimientos").DataTable({
    dom: "Bfrtip",
    responsive: true,
    destroy: true,
    ajax: {
      url: "../../controllers/GestionarMovimientoController.php?action=Consultar",
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