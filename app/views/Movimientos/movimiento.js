$(document).ready(function () {
  init();
});

function init() {
  // Inicializar la tabla una sola vez
  if (!$.fn.DataTable.isDataTable("#tblMovimientos")) {
    listarMovimientos();
  }

  ListarCombosMov();
  ListarCombosFiltros();

  // Manejar el evento submit del formulario de búsqueda
  $("#frmbusqueda")
    .off("submit")
    .on("submit", function (e) {
      e.preventDefault();
      $("#divtblmovimientos").show();
      $("#divgenerarmov").hide();
      $("#divregistroMovimiento").hide();
      $("#divlistadomovimientos").show();
      
      // Recargar la tabla
      if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
        $("#tblMovimientos").DataTable().ajax.reload(null, false);
      } else {
        listarMovimientos();
      }
    });

  $(document).on(
    "change",
    "#IdSucursalOrigen, #IdSucursalDestino",
    function () {
      setSucursalOrigenDestino();
    }
  );

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
    });

  // Botón procesar en generarmov
  $("#btnprocesarempresa")
    .off("click")
    .on("click", function () {
      // Validar campos obligatorios
      const tipoMovimiento = $("#IdTipoMovimientoMov").val();
      const autorizador = $("#CodAutorizador").val();
      const sucursalDestino = $("#IdSucursalDestino").val();

      if (!tipoMovimiento) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar un tipo de movimiento",
          icon: "warning"
        });
        return;
      }

      if (!autorizador) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar un autorizador",
          icon: "warning"
        });
        return;
      }

      if (!sucursalDestino) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar una sucursal destino",
          icon: "warning"
        });
        return;
      }

      // Si todo está correcto, proceder con el procesamiento
      $("#divregistroMovimiento").show();
      $("#divgenerarmov").hide();

      // Transferir valores de los combos
      $("#IdTipoMovimiento").val($("#IdTipoMovimientoMov").val());
      var autorizadorNombre = $("#CodAutorizador option:selected").text();
      $("#IdAutorizador").val($("#CodAutorizador").val());
      $("#lblautorizador").text("Autorizador: " + autorizadorNombre);

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
    
    // Validar que haya al menos un activo en la tabla
    if ($("#tbldetalleactivomov tbody tr").length === 0) {
      Swal.fire("Error", "Debe agregar al menos un activo al detalle", "error");
      return false;
    }

    // Validar que todos los campos requeridos estén llenos
    let camposFaltantes = [];
    $("#tbldetalleactivomov tbody tr").each(function() {
      const ambienteDestino = $(this).find(".ambiente-destino").val();
      const responsableDestino = $(this).find(".responsable-destino").val();
      const idActivo = $(this).data("id");
      const nombreActivo = $(this).find("td:eq(1)").text();

      if (!ambienteDestino) {
        camposFaltantes.push(`Ambiente destino para el activo ${nombreActivo}`);
      }
      if (!responsableDestino) {
        camposFaltantes.push(`Responsable destino para el activo ${nombreActivo}`);
      }
    });

    if (camposFaltantes.length > 0) {
      Swal.fire({
        title: "Campos Faltantes",
        html: "Por favor complete los siguientes campos:<br><br>" + 
              camposFaltantes.join("<br>"),
        icon: "warning"
      });
      return false;
    }

    const formData = new FormData(this);

    // Agregar el responsable seleccionado
    const responsableDestino = $(this).find(".responsable-destino").val();
    formData.append("IdResponsableDestino", responsableDestino);

    // Mostrar loading
    Swal.fire({
      title: "Procesando",
      text: "Guardando el detalle del movimiento...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    $.ajax({
      url: "../../controllers/GestionarMovimientoController.php?action=AgregarDetalle",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (res) {
        if (res.status) {
          Swal.fire({
            title: "Éxito",
            text: "Activo agregado al movimiento",
            icon: "success"
          }).then(() => {
            // Limpia solo el select de activo y los campos visuales
            $("#IdActivo").val("").trigger("change");
            $("#CodigoActivo, #SucursalActual, #AmbienteActual").val("");
            // Limpiar la tabla de detalle
            $("#tbldetalleactivomov tbody").empty();
          });
        } else {
          Swal.fire("Error", res.message || "No se pudo agregar el activo", "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la petición:", error);
        Swal.fire("Error", "Ocurrió un error al procesar la solicitud", "error");
      }
    });
  });

  // Función para agregar activo al detalle
  function agregarActivoAlDetalle(activo) {
    if ($(`#tbldetalleactivomov tbody tr[data-id='${activo.id}']`).length > 0) {
      NotificacionToast(
        "error",
        `El activo <b>${activo.nombre}</b> ya está en el detalle.`
      );
      return false;
    }

    // Validar que el activo tenga todos los datos necesarios
    if (!activo.id || !activo.nombre || !activo.marca || !activo.sucursal || !activo.ambiente) {
      NotificacionToast("error", "El activo no tiene todos los datos necesarios");
      return false;
    }

    var numeroFilas = $("#tbldetalleactivomov").find("tbody tr").length;

    var selectAmbienteDestino = `<select class='form-control form-control-sm ambiente-destino' name='ambiente_destino[]' id="comboAmbiente${numeroFilas}" required></select>`;
    var selectResponsableDestino = `<select class='form-control form-control-sm responsable-destino' name='responsable_destino[]' id="comboResponsable${numeroFilas}" required></select>`;

    var nuevaFila = `<tr data-id='${activo.id}' class='table-success agregado-temp'>
      <td>${activo.id}</td>
      <td>${activo.codigo}</td>
      <td>${activo.nombre}</td>
      <td>${activo.marca}</td>
      <td>${activo.sucursal}</td>
      <td>${activo.ambiente}</td>
      <td>${selectAmbienteDestino}</td>
      <td>${selectResponsableDestino}</td>
      <td><button type='button' class='btn btn-danger btn-sm btnQuitarActivo'><i class='fa fa-trash'></i></button></td>
    </tr>`;
    $("#tbldetalleactivomov tbody").append(nuevaFila);
    
    // Inicializar los combos con la sucursal destino actual
    ListarCombosAmbiente(`comboAmbiente${numeroFilas}`);
    ListarCombosResponsable(`comboResponsable${numeroFilas}`);

    // Agregar validación al cambiar los combos
    $(`#comboAmbiente${numeroFilas}, #comboResponsable${numeroFilas}`).on('change', function() {
      const ambienteVal = $(`#comboAmbiente${numeroFilas}`).val();
      const responsableVal = $(`#comboResponsable${numeroFilas}`).val();
      
      if (!ambienteVal || !responsableVal) {
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
      }
    });

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
        codigo: fila.find("td:eq(1)").text(),
        nombre: fila.find("td:eq(2)").text(),
        marca: fila.find("td:eq(3)").text(),
        sucursal: fila.find("td:eq(4)").text(),
        ambiente: fila.find("td:eq(5)").text()
    };
    agregarActivoAlDetalle(activo);
  });

  $(document).on("click", ".btnQuitarActivo", function () {
    $(this).closest("tr").remove();
  });

  // Función para guardar el movimiento completo
  $("#btnGuardarMov").on("click", function () {
    // Verificar si hay detalles en la tabla
    if ($("#tbldetalleactivomov tbody tr").length === 0) {
      Swal.fire("Error", "Debe agregar al menos un activo al detalle", "error");
      return;
    }

    // Obtener los datos del formulario principal
    const formData = new FormData();
    formData.append("IdTipo", $("#IdTipoMovimiento").val());
    formData.append("autorizador", $("#IdAutorizador").val());
    formData.append("sucursal_destino", $("#IdSucursalDestino").val());
    formData.append("observacion", ""); // Si tienes campo de observaciones, agrégalo aquí

    // Primero guardar el movimiento principal
    $.ajax({
      url: "../../controllers/GestionarMovimientoController.php?action=RegistrarMovimientoSolo",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (res) {
        if (res.status) {
          // Si se guardó el movimiento principal, proceder a guardar los detalles
          guardarDetallesMovimiento(res.idMovimiento);
        } else {
          Swal.fire(
            "Error",
            res.message || "Error al registrar el movimiento",
            "error"
          );
        }
      },
      error: function () {
        Swal.fire("Error", "Error al comunicarse con el servidor", "error");
      },
    });
  });

  function guardarDetallesMovimiento(idMovimiento) {
    let detallesGuardados = 0;
    let totalDetalles = $("#tbldetalleactivomov tbody tr").length;
    let errores = [];

    // Iterar sobre cada fila de la tabla de detalles
    $("#tbldetalleactivomov tbody tr").each(function () {
      const fila = $(this);
      const detalleData = new FormData();

      detalleData.append("IdMovimiento", idMovimiento);
      detalleData.append("IdActivo", fila.find("td:eq(0)").text());
      detalleData.append("IdTipoMovimiento", $("#IdTipoMovimiento").val());
      detalleData.append("IdSucursalDestino", $("#IdSucursalDestino").val());
      detalleData.append(
        "IdAmbienteDestino",
        fila.find(".ambiente-destino").val()
      );
      detalleData.append(
        "IdResponsableDestino",
        fila.find(".responsable-destino").val()
      );
      detalleData.append("IdAutorizador", $("#IdAutorizador").val());

      // Guardar cada detalle
      $.ajax({
        url: "../../controllers/GestionarMovimientoController.php?action=AgregarDetalle",
        type: "POST",
        data: detalleData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (res) {
          detallesGuardados++;
          if (!res.status) {
            errores.push(
              `Error en activo ${fila.find("td:eq(0)").text()}: ${res.message}`
            );
          }

          // Cuando todos los detalles se hayan procesado
          if (detallesGuardados === totalDetalles) {
            if (errores.length === 0) {
              Swal.fire({
                title: "Éxito",
                text: "Movimiento registrado correctamente",
                icon: "success",
              }).then(() => {
                // Limpiar y recargar
                $("#divregistroMovimiento").hide();
                $("#divtblmovimientos").show();
                $("#divlistadomovimientos").show();
                listarMovimientos();
              });
            } else {
              Swal.fire({
                title: "Advertencia",
                html:
                  "El movimiento se registró pero hubo errores en algunos detalles:<br>" +
                  errores.join("<br>"),
                icon: "warning",
              }).then(() => {
                $("#divregistroMovimiento").hide();
                $("#divtblmovimientos").show();
                $("#divlistadomovimientos").show();
                listarMovimientos();
              });
            }
          }
        },
        error: function () {
          detallesGuardados++;
          errores.push(
            `Error de comunicación con el servidor para el activo ${fila
              .find("td:eq(0)")
              .text()}`
          );

          if (detallesGuardados === totalDetalles) {
            Swal.fire({
              title: "Error",
              html:
                "Hubo errores al guardar los detalles:<br>" +
                errores.join("<br>"),
              icon: "error",
            });
          }
        },
      });
    });
  }
}

function setSucursalOrigenDestino() {
  var sucursalOrigenText = $("#IdSucursalOrigen").val();
  var sucursalDestinoText = $("#IdSucursalDestino option:selected").text();
  $("#sucursal_origen").val(sucursalOrigenText);
  $("#sucursal_destino").val(sucursalDestinoText);
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

function ListarCombosAmbiente(elemento, idSucursal = null) {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=obtenerAmbientesPorSucursal",
    type: "POST",
    data: { idSucursal: idSucursal || $("#IdSucursalDestino").val() },
    dataType: "json",
    async: false,
    success: (res) => {
      if (res.status) {
        $(`#${elemento}`).html(res.data).trigger("change");

        $(`#${elemento}`).select2({
          theme: "bootstrap4",
          width: "100%",
        });
      } else {
        NotificacionToast("error", "No se pudieron cargar los ambientes: " + res.message);
      }
    },
    error: (xhr, status, error) => {
      NotificacionToast("error", "Error al cargar ambientes: " + error);
    }
  });
}

// Agregar evento para actualizar ambientes cuando cambie la sucursal destino
$(document).on("change", "#IdSucursalDestino", function() {
  // Actualizar todos los combos de ambiente en la tabla
  $("#tbldetalleactivomov tbody tr").each(function() {
    const row = $(this);
    const comboAmbiente = row.find(".ambiente-destino");
    if (comboAmbiente.length > 0) {
      const idCombo = comboAmbiente.attr("id");
      ListarCombosAmbiente(idCombo);
    }
  });
});

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
    async: false,
    success: (res) => {
      if (res.status) {
        // Cargar tipo de movimiento
        $("#IdTipoMovimientoMov")
          .html(res.data.tipoMovimiento)
          .trigger("change");

        // Cargar autorizador
        $("#CodAutorizador").html(res.data.autorizador).trigger("change");

        // Actualizar el campo de sucursal origen
        $("#IdSucursalOrigen").val(res.data.sucursalOrigen);
        $("#IdSucursalOrigenValor").val(res.data.sucursalOrigenId);

        // Cargar sucursales para el destino
        $("#IdSucursalDestino").html(res.data.sucursales);

        // Inicializar select2 una sola vez para los combos
        if (!$("#IdTipoMovimientoMov").hasClass("select2-hidden-accessible")) {
          $(
            "#IdTipoMovimientoMov, #CodAutorizador, #IdSucursalDestino"
          ).select2({
            theme: "bootstrap4",
            width: "100%",
          });
        }

        // Actualizar el texto de la sucursal origen
        setSucursalOrigenDestino();
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
  // Destruir la instancia existente si existe
  if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
    $("#tblMovimientos").DataTable().destroy();
  }

  $("#tblMovimientos").DataTable({
    aProcessing: true,
    aServerSide: false,
    layout: {
      topStart: {
        buttons: [
          {
            extend: "excelHtml5",
            title: "Listado Movimientos",
            text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
            autoFilter: true,
            sheetName: "Data",
            exportOptions: {
              columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
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
    //responsive: true,
    lengthChange: false,
    colReorder: true,
    autoWidth: false,
    ajax: {
      url: "../../controllers/GestionarMovimientoController.php?action=Consultar",
      type: "POST",
      dataType: "json",
      data: function (d) {
        // Agregar los filtros del formulario
        d.filtroTipoMovimiento = $("#filtroTipoMovimiento").val();
        d.filtroSucursal = $("#filtroSucursal").val();
        d.filtroFecha = $("#filtroFecha").val();
      },
      dataSrc: function (json) {
        console.log("Datos recibidos:", json);
        if (json && json.data) {
          return json.data;
        }
        return [];
      },
    },
    columns: [
      {
        data: null,
        render: () =>
          `<div class="btn-group">
            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-cog"></i>
            </button>
            <div class="dropdown-menu">
              <button class="dropdown-item btnVerDetalle" type="button">
                <i class="fas fa-eye text-info"></i> Ver Detalle
              </button>
              <button class="dropdown-item btnAnularMovimiento" type="button">
                <i class="fas fa-ban text-danger"></i> Anular Movimiento
              </button>
              <button class="dropdown-item btnVerHistorial" type="button">
                <i class="fas fa-history text-primary"></i> Ver Historial
              </button>
            </div>
          </div>`,
      },
      { data: "IdDetalleMovimiento", visible: false, searchable: false },
      { data: "IdActivo" },
      { data: "NombreArticulo" },
      { data: "TipoMovimiento" },
      { data: "SucursalOrigen", visible: false, searchable: false },
      { data: "SucursalDestino" },
      { data: "AmbienteOrigen" },
      { data: "AmbienteDestino" },
      { data: "Autorizador" },
      { data: "ResponsableOrigen" },
      { data: "ResponsableDestino" },
      {
        data: "FechaMovimiento",
        render: function (data) {
          if (data) {
            return moment(data).format("DD/MM/YYYY HH:mm");
          }
          return "";
        },
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
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
  // Destruir la instancia existente si existe
  if ($.fn.DataTable.isDataTable("#tbllistarActivos")) {
    $("#tbllistarActivos").DataTable().destroy();
  }

  $("#tbllistarActivos").DataTable({
    dom: "Bfrtip",
    responsive: false,
    ajax: {
      url: "../../controllers/GestionarMovimientoController.php?action=ListarParaMovimiento",
      type: "POST",
      dataType: "json",
      dataSrc: function (json) {
        if (!json.status) {
          NotificacionToast("error", json.message);
          return [];
        }
        return json.data || [];
      },
      error: function(xhr, status, error) {
        NotificacionToast("error", "Error al cargar los activos: " + error);
        return [];
      }
    },
    columns: [
      { data: "IdActivo" },
      { data: "CodigoActivo" },
      { data: "NombreArticulo" },
      { data: "MarcaArticulo" },
      { data: "Sucursal" },
      { data: "Ambiente" },
      {
        data: null,
        render: function (data, type, row) {
          return '<button class="btn btn-success btn-sm btnSeleccionarActivo" data-id="' + 
                 row.IdActivo + '"><i class="fa fa-check"></i></button>';
        }
      }
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
    order: [[2, 'asc']], // Ordenar por NombreArticulo
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
  });
}

// Agregar los manejadores de eventos para los nuevos botones
$(document).on("click", ".btnVerDetalle", function () {
  const fila = $(this).closest("tr");
  const datos = $("#tblMovimientos").DataTable().row(fila).data();
  
  if (!datos) {
    Swal.fire("Error", "No se pudo obtener la información del movimiento.", "error");
    return;
  }

  // Aquí puedes implementar la lógica para ver el detalle
  Swal.fire({
    title: "Detalle del Movimiento",
    html: `
      <div class="text-left">
        <p><strong>ID Activo:</strong> ${datos.IdActivo}</p>
        <p><strong>Nombre:</strong> ${datos.NombreArticulo}</p>
        <p><strong>Tipo Movimiento:</strong> ${datos.TipoMovimiento}</p>
        <p><strong>Sucursal Origen:</strong> ${datos.SucursalOrigen}</p>
        <p><strong>Sucursal Destino:</strong> ${datos.SucursalDestino}</p>
        <p><strong>Ambiente Origen:</strong> ${datos.AmbienteOrigen}</p>
        <p><strong>Ambiente Destino:</strong> ${datos.AmbienteDestino}</p>
        <p><strong>Autorizador:</strong> ${datos.Autorizador}</p>
        <p><strong>Responsable Origen:</strong> ${datos.ResponsableOrigen}</p>
        <p><strong>Responsable Destino:</strong> ${datos.ResponsableDestino}</p>
        <p><strong>Fecha:</strong> ${datos.FechaMovimiento}</p>
      </div>
    `,
    width: '600px'
  });
});

$(document).on("click", ".btnAnularMovimiento", function () {
  const fila = $(this).closest("tr");
  const datos = $("#tblMovimientos").DataTable().row(fila).data();
  
  if (!datos) {
    Swal.fire("Error", "No se pudo obtener la información del movimiento.", "error");
    return;
  }

  Swal.fire({
    title: "¿Estás seguro?",
    text: "¿Deseas anular este movimiento?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, anular",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../../controllers/GestionarMovimientoController.php?action=anularMovimiento",
        type: "POST",
        data: { idMovimiento: datos.IdDetalleMovimiento },
        dataType: "json",
        success: function(res) {
          if (res.status) {
            Swal.fire({
              title: "Éxito",
              text: res.message,
              icon: "success"
            }).then(() => {
              // Recargar la tabla
              $("#tblMovimientos").DataTable().ajax.reload();
            });
          } else {
            Swal.fire("Error", res.message, "error");
          }
        },
        error: function(xhr, status, error) {
          Swal.fire("Error", "Error al procesar la solicitud: " + error, "error");
        }
      });
    }
  });
});

$(document).on("click", ".btnVerHistorial", function () {
  const fila = $(this).closest("tr");
  const datos = $("#tblMovimientos").DataTable().row(fila).data();
  
  if (!datos) {
    Swal.fire("Error", "No se pudo obtener la información del movimiento.", "error");
    return;
  }

  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=obtenerHistorialMovimiento",
    type: "POST",
    data: { idMovimiento: datos.IdDetalleMovimiento },
    dataType: "json",
    success: function(res) {
      if (res.status) {
        let historialHtml = '<div class="table-responsive"><table class="table table-bordered">';
        historialHtml += '<thead><tr><th>Fecha</th><th>Estado</th><th>Tipo</th><th>Activo</th><th>Origen</th><th>Destino</th></tr></thead>';
        historialHtml += '<tbody>';
        
        res.data.forEach(item => {
          historialHtml += `<tr>
            <td>${moment(item.FechaMovimiento).format('DD/MM/YYYY HH:mm')}</td>
            <td>${item.estado === 'A' ? '<span class="badge badge-danger">Anulado</span>' : '<span class="badge badge-success">Activo</span>'}</td>
            <td>${item.tipoMovimiento}</td>
            <td>${item.CodigoActivo} - ${item.NombreArticulo}</td>
            <td>${item.sucursalOrigen} - ${item.ambienteOrigen}</td>
            <td>${item.sucursalDestino} - ${item.ambienteDestino}</td>
          </tr>`;
        });
        
        historialHtml += '</tbody></table></div>';

        Swal.fire({
          title: "Historial del Movimiento",
          html: historialHtml,
          width: '800px'
        });
      } else {
        Swal.fire("Error", res.message, "error");
      }
    },
    error: function(xhr, status, error) {
      Swal.fire("Error", "Error al obtener el historial: " + error, "error");
    }
  });
});
