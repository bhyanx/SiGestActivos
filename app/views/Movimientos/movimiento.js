$(document).ready(function () {
  init();
});

function init() {
  listarMovimientos();
  ListarCombosMov();
  ListarCombosFiltros();

  $(document).on("click", "#btnBuscarIdItem, .btnagregardet", function () {
    $("#ModalArticulos").modal("show");
    listarActivosModal();
  });

  $("#btnchangedatasucmovimiento")
    .off("click")
    .on("click", function () {
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Se perderán los cambios realizados",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6 ",
        confirmButtonText: "Aceptar",
        cancelButtonColor: "#d33",
        cancelButtonText: "No, continuar aquí",
      }).then((result) => {
        if (result.isConfirmed) {
          $("#divregistroMovimiento").hide();
          $("#divgenerarmov").show();
        }
      });
    });

  // Ocultar secciones al cargar
  $("#divgenerarmov").hide();
  $("#divregistroMovimiento").hide();

  // Botón para abrir el panel de generación de movimiento
  $("#btnnuevo")
    .off("click")
    .on("click", function () {
      $("#divgenerarmov").show();
      $("#divregistroMovimiento").hide();
      $("#divtblmovimientos").hide();
      $("#divlistadomovimientos").hide(); // Oculta el formulario de búsqueda
      $("#tituloModalMovimiento").html(
        '<i class="fa fa-plus-circle"></i> Registrar Movimiento'
      );
      $("#frmMovimiento")[0].reset();
      $("#ModalMovimiento").modal("show");

      var sucursalOrigen = $("#IdSucursalOrigen").val();
      var sucursalDestino = $("#IdSucursalDestino").val();

      if (sucursalOrigen) {
        $("#sucursal_origen").val(sucursalOrigen).trigger("change");
      }
      if (sucursalDestino) {
        $("#sucursal_destino").val(sucursalDestino).trigger("change");
      }
    });

  // Botón procesar en generarmov
  $("#btnprocesarempresa")
    .off("click")
    .on("click", function () {
      $("#divregistroMovimiento").show();
      $("#divgenerarmov").hide();
      // Opcional: limpiar el formulario
      $("#frmMovimiento")[0].reset();
    });

  // Botón cancelar en generarmov
  // Botón cancelar en generarmov
  $("#btncancelarempresa")
    .off("click")
    .on("click", function () {
      $("#divgenerarmov").hide();
      $("#divtblmovimientos").show();
      $("#divlistadomovimientos").show(); // Muestra el formulario de búsqueda
    });

  // Botón cancelar en registro de movimiento
  $("#btnCancelarMovimiento")
    .off("click")
    .on("click", function () {
      $("#divregistroMovimiento").hide();
      $("#divtblmovimientos").show();
      $("#divlistadomovimientos").show();
    });

  // Al buscar, mostrar la tabla y ocultar formularios
  $("#frmbusqueda").on("submit", function (e) {
    e.preventDefault();
    $("#divtblmovimientos").show();
    $("#divgenerarmov").hide();
    $("#divregistroMovimiento").hide();
    $("#divlistadomovimientos").show();
    if (typeof listarMovimientos === "function") listarMovimientos();
  });

  // Botón para abrir modal de nuevo movimiento
  // $("#btnnuevo").click(() => {
  //   $("#tituloModalMovimiento").html(
  //     '<i class="fa fa-plus-circle"></i> Registrar Movimiento'
  //   );
  //   $("#frmMovimiento")[0].reset();
  //   $("#ModalMovimiento").modal("show");
  // });

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
          setSucursalOrigenDestino();
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
    setSucursalOrigenDestino();
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

  $(document).on("click", ".btnSeleccionarActivo", function () {
    var fila = $(this).closest("tr");
    var activo = {
      id: $(this).data("id"),
      //codigo: fila.find("td:eq(0)").text(),
      nombre: fila.find("td:eq(1)").text(),
      marca: fila.find("td:eq(2)").text(),
      sucursal: fila.find("td:eq(3)").text(),
      ambiente: fila.find("td:eq(4)").text(),
    };
    agregarActivoAlDetalle(activo);
  });

  $(document).on("click", ".btnQuitarActivo", function () {
    $(this).closest("tr").remove();
  });
}

function setSucursalOrigenDestino() {
  var sucursalOrigenText = $("#IdSucursalOrigen option:selected").text();
  var sucursalDestinoText = $("#IdSucursalDestino option:selected").text();
  $("#sucursal_origen").val(sucursalOrigenText);
  $("#sucursal_destino").val(sucursalDestinoText);
}

function agregarActivoAlDetalle(activo) {
  if ($(`#tbldetalleactivomov tbody tr[data-id='${activo.id}']`).length > 0) {
    NotificacionToast(
      "error",
      `El activo <b>${activo.nombre}</b> ya está en el detalle.`
    );
    return false;
  }
  var numeroFilas = $("#tbldetalleactivomov").find("tbody tr").length;

  // Usa el combo correcto que ya está poblado en el DOM
  var opcionesAmbiente = $("#ambiente_destino").html(); // o el id correcto
  var opcionesResponsable = $("#usuario_destino").html(); // o el id correcto

  var selectAmbienteDestino = `<select class='form-control form-control-sm ambiente-destino' name='ambiente_destino[]' id="comboAmbiente${numeroFilas}"></select>`;
  var selectResponsableDestino = `<select class='form-control form-control-sm responsable-destino' name='responsable_destino[]' id="comboResponsable${numeroFilas}"></select>`;

  var nuevaFila = `<tr data-id='${activo.id}' class='table-success agregado-temp'>
    <td>${activo.id}</td>
    <td>${activo.nombre}</td>
    <td>${activo.marca}</td>
    <td>${activo.sucursal}</td>
    <td>${activo.ambiente}</td>
    <td>${selectAmbienteDestino}</td>
    <td>${selectResponsableDestino}</td>
    <td><button type='button' class='btn btn-danger btn-sm btnQuitarActivo'><i class='fa fa-trash'></i></button></td>
  </tr>`;
  $("#tbldetalleactivomov tbody").append(nuevaFila);
  console.log(`comboAmbiente${numeroFilas}`);
  ListarCombosAmbiente(`comboAmbiente${numeroFilas}`);
  ListarCombosResponsable(`comboResponsable${numeroFilas}`);

  setTimeout(function () {
    $("#tbldetalleactivomov tbody tr.agregado-temp").removeClass(
      "table-success agregado-temp"
    );
  }, 1000);

  NotificacionToast(
    "success",
    `Activo <b>${activo.nombre}</b> agregado al detalle.`
  );
  return true;
}

function ListarCombosResponsable(elemento) {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=combos",
    type: "POST",
    dataType: "json",
    async: false,

    success: (res) => {
      if (res.status) {
        $(`#${elemento}`).html(res.data.responsable).trigger("change");

        $(`#${elemento}`).select2({
          theme: "bootstrap4",
          //dropdownParent: $("#ModalFiltros .modal-body"),
          width: "100%",
        });
      } else {
        Swal.fire(
          "Filtro de movimientos",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      Swal.fire(
        "Filtros de movimientos",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

function ListarCombosAmbiente(elemento) {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=combos",
    type: "POST",
    dataType: "json",
    async: false,
    success: (res) => {
      if (res.status) {
        $(`#${elemento}`).html(res.data.ambientes).trigger("change");

        $(`#${elemento}`).select2({
          theme: "bootstrap4",
          //dropdownParent: $("#ModalFiltros .modal-body"),
          width: "100%",
        });
      } else {
        Swal.fire(
          "Filtro de movimientos",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      Swal.fire(
        "Filtros de movimientos",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

function NotificacionToast(tipo, mensaje) {
  toastr.options = {
    closeButton: false,
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: "toast-bottom-left",
    preventDuplicates: true,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "3000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
  };
  toastr[tipo](mensaje);
}

function mostrarNotificacionModalActivos(mensaje, tipo = "success") {
  let $noti = $("#noti-modal-activos");
  if ($noti.length === 0) {
    $("#ModalArticulos .modal-body").append(
      '<div id="noti-modal-activos" style="position:fixed;left:0;right:0;bottom:10px;z-index:1051;text-align:center;width:100%;"></div>'
    );
    $noti = $("#noti-modal-activos");
  }
  $noti
    .html(
      `<span class='badge badge-${
        tipo === "success" ? "success" : "danger"
      }' style='font-size:1.1em;padding:10px 20px;'>${mensaje}</span>`
    )
    .fadeIn();
  setTimeout(function () {
    $noti.fadeOut();
  }, 1800);
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
      if (res.status) {
        $("#filtroTipoMovimiento")
          .html(res.data.tipoMovimiento)
          .trigger("change");
        $("#filtroSucursal").html(res.data.sucursales).trigger("change");
        $("#filtroAmbiente").html(res.data.ambientes).trigger("change");
        // $("#filtroSucursalDestino").html(res.data.sucursales).trigger("change");

        $("#filtroTipoMovimiento, #filtroSucursal, #filtroAmbiente").select2({
          theme: "bootstrap4",
          //dropdownParent: $("#ModalFiltros .modal-body"),
          width: "100%",
        });
      } else {
        Swal.fire(
          "Filtro de movimientos",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      Swal.fire(
        "Filtros de movimientos",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

// Cargar combos principales
function ListarCombosMov() {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status) {
        $("#IdTipoMovimientoMov")
          .html(res.data.tipoMovimiento)
          .trigger("change");
        $("#CodAutorizador").html(res.data.autorizador).trigger("change");
        $("#IdSucursalOrigen").html(res.data.sucursales).trigger("change");
        $("#IdSucursalDestino").html(res.data.sucursales).trigger("change");
        // $("#ambiente_destino").html(res.data.ambientes).trigger("change");
        // $("#usuario_destino").html(res.data.responsable).trigger("change");

        $(
          "#IdTipoMovimientoMov, #CodAutorizador, #IdSucursalOrigen, #IdSucursalDestino, #ambiente_destino, #usuario_destino"
        ).select2({
          theme: "bootstrap4",
          //dropdownParent: $("#ModalMovimiento .modal-body"),
          width: "100%",
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
      Swal.fire(
        "Movimiento de activos",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

// Function lis

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

function listarActivosModal() {
  $("#tbllistarActivos").DataTable({
    dom: "Bfrtip",
    responsive: false,
    destroy: true,
    ajax: {
      url: "../../controllers/GestionarActivosController.php?action=ListarParaMovimiento",
      type: "POST",
      dataType: "json",
      dataSrc: function (json) {
        return json.data || [];
      },
    },
    columns: [
      { data: "IdActivo" },
      //{ data: "Codigo" },
      { data: "Nombre" },
      { data: "Marca" },
      { data: "Sucursal" },
      { data: "Ambiente" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            '<button class="btn btn-success btn-sm btnSeleccionarActivo" data-id="' +
            row.IdActivo +
            '"><i class="fa fa-check"></i></button>'
          );
        },
      },
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
  });
}
