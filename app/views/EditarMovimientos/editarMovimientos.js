$(document).ready(function () {
  initEditarMovimientos();
});

function initEditarMovimientos() {
  cargarTiposMovimiento();
  listarMovimientosPendientes();

  // Establecer valores por defecto de sesión
  if (
    typeof empresaSesion !== "undefined" &&
    empresaSesion &&
    empresaSesion !== ""
  ) {
    $("#filtroEmpresa").val(empresaSesion).trigger("change.select2");
  }

  if (
    typeof sucursalSesion !== "undefined" &&
    sucursalSesion &&
    sucursalSesion !== ""
  ) {
    $("#filtroSucursal").val(sucursalSesion).trigger("change.select2");
  }

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
          $("#divlistadoMovimientos").show();
          $("#divtblmovimientos").show();
          // Recargar la tabla
          $("#tblMovimientos").DataTable().ajax.reload();

          $("#divEditarMovimiento").hide();
        }
      });
    });
}

function cargarTiposMovimiento() {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status) {
        $("#filtroTipo").html(
          '<option value="">Todos los tipos</option>' + res.data.tipoMovimiento
        );
        $("#filtroTipo").select2({
          theme: "bootstrap4",
          width: "100%",
          placeholder: "Seleccionar Tipo de Movimiento",
          allowClear: true,
        });
      } else {
        console.error("Error al cargar tipos de movimiento:", res.message);
      }
    },
    error: (xhr, status, error) => {
      console.error("Error al cargar tipos de movimiento:", error);
    },
  });
}

function listarMovimientosPendientes() {
  $("#tblMovimientos").DataTable({
    aProcessing: true,
    aServerSide: true,
    layout: {
      topStart: {
        buttons: [
          {
            extend: "excelHtml5",
            title: "Movimientos Pendientes",
            text: "<i class='fas fa-file-excel'></i> Exportar",
            autoFilter: true,
            sheetName: "MovimientosPendientes",
            exportOptions: {
              columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            },
          },
          "pageLength",
          "colvis",
        ],
      },
      bottomEnd: {
        paging: {
          firstLast: false,
        },
      },
    },
    lengthChange: false,
    colReorder: true,
    autoWidth: false,
    destroy: true,
    ajax: {
      url: "../../controllers/EdicionesMovController.php?action=listarMovimientosPendientes",
      type: "POST",
      data: function (d) {
        return {
          filtroTipo: $("#filtroTipo").val() || null,
          filtroFechaInicio: $("#filtroFechaInicio").val() || null,
          filtroFechaFin: $("#filtroFechaFin").val() || null,
        };
      },
      dataType: "json",
      dataSrc: function (json) {
        if (json.status && json.data) {
          // Actualizar contador
          $("#contadorMovimientos").text(json.data.length);
          return json.data;
        } else {
          $("#contadorMovimientos").text("0");
          return [];
        }
      },
    },
    columns: [
      {
        data: null,
        render: function (data, type, row) {
          return `
                        <div class="btn-group">
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item btnVerMovimiento" type="button" data-id="${row.idMovimiento}">
                                    <i class="fas fa-edit text-warning"></i> Editar
                                </button>
                                <button class="dropdown-item btnVerDetalles" type="button" data-id="${row.idMovimiento}">
                                    <i class="fas fa-eye text-info"></i> Ver Detalles
                                </button>
                                <button class="dropdown-item btnAprobarMovimiento" type="button" data-id="${row.idMovimiento}">
                                    <i class="fas fa-check text-success"></i> Aprobar
                                </button>
                                <button class="dropdown-item btnRechazarMovimiento" type="button" data-id="${row.idMovimiento}">
                                    <i class="fas fa-times text-danger"></i> Rechazar
                                </button>
                            </div>
                        </div>`;
        },
      },
      { data: "codigoMovimiento" },
      { data: "tipoMovimiento" },
      { data: "sucursalOrigen" },
      { data: "empresaDestino" },
      { data: "sucursalDestino" },
      { data: "autorizador" },
      { data: "receptor" },
      {
        data: "estadoMovimiento",
        render: function (data, type, row) {
          return `<span class="badge badge-warning"><i class="fas fa-clock"></i> ${data}</span>`;
        },
      },
      {
        data: "fechaMovimiento",
        render: function (data, type, row) {
          return new Date(data).toLocaleDateString("es-ES");
        },
      },
      { data: "observaciones" },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
    order: [[9, "desc"]], // Ordenar por fecha descendente
    pageLength: 10,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
  });
}

// Función para notificaciones toast
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

// Función para actualizar contador de cambios pendientes
function actualizarContadorCambios() {
  const agregados = $(
    "#tbldetalleactivomov tbody tr[data-nuevo='true']"
  ).length;
  const modificados = $(
    "#tbldetalleactivomov tbody tr[data-estado='modificado']"
  ).length;
  const eliminados = $(
    "#tbldetalleactivomov tbody tr[data-eliminar='true']"
  ).length;

  const totalCambios = agregados + modificados + eliminados;

  // Actualizar contador visual
  let contadorHtml = "";
  if (totalCambios > 0) {
    contadorHtml = `
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle"></i>
                <strong>Cambios pendientes:</strong>
                ${
                  agregados > 0
                    ? `<span class="badge badge-warning ml-1">${agregados} agregados</span>`
                    : ""
                }
                ${
                  modificados > 0
                    ? `<span class="badge badge-info ml-1">${modificados} modificados</span>`
                    : ""
                }
                ${
                  eliminados > 0
                    ? `<span class="badge badge-danger ml-1">${eliminados} eliminados</span>`
                    : ""
                }
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
  }

  // Mostrar/ocultar contador
  if ($("#contadorCambios").length === 0) {
    $("#divEditarMovimiento .card-header").after(
      `<div id="contadorCambios"></div>`
    );
  }
  $("#contadorCambios").html(contadorHtml);

  // Actualizar estado del botón guardar
  const btnGuardar = $("#btnGuardarEdicion");
  if (totalCambios > 0) {
    btnGuardar.removeClass("btn-secondary").addClass("btn-success");
    btnGuardar.html(
      `<i class="fas fa-save"></i> Guardar Cambios (${totalCambios})`
    );
  } else {
    btnGuardar.removeClass("btn-success").addClass("btn-secondary");
    btnGuardar.html(`<i class="fas fa-save"></i> Guardar Cambios`);
  }
}

// Event handler para el formulario de búsqueda
$("#frmbusqueda").on("submit", function (e) {
  e.preventDefault();

  $("#divtblmovimientos").show();

  if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
    $("#tblMovimientos").DataTable().clear().destroy();
  }

  setTimeout(() => {
    listarMovimientosPendientes();
  }, 100);
});

// Event listener for Ver Detalles button
$(document).on("click", ".btnVerDetalles", function () {
  const idMovimiento = $(this).data("id");

  if (!idMovimiento) {
    NotificacionToast("error", "No se pudo obtener el ID del movimiento.");
    return;
  }

  // Obtener detalles del movimiento
  $.ajax({
    url: "../../controllers/EdicionesMovController.php?action=obtenerDetallesMovimiento",
    type: "POST",
    data: { idMovimiento: idMovimiento },
    dataType: "json",
    success: function (res) {
      if (res.status && res.data) {
        const tbody = $("#tblDetallesMovimiento tbody");
        tbody.empty();

        res.data.forEach(function (detalle) {
          const fila = `
                        <tr>
                            <td>${detalle.codigoActivo}</td>
                            <td>${detalle.nombreActivo}</td>
                            <td>${detalle.ambienteOrigen}</td>
                            <td>${detalle.ambienteDestino}</td>
                            <td>${detalle.responsableOrigen}</td>
                            <td>${detalle.responsableDestino}</td>
                            <td>${detalle.tipoMovimiento}</td>
                            <td>${detalle.fechaMovimiento}</td>
                            <td>${detalle.usuarioRegistro}</td>
                        </tr>
                    `;
          tbody.append(fila);
        });

        // Mostrar el modal
        $("#modalVerDetallesMovimiento").modal("show");
      } else {
        NotificacionToast(
          "error",
          res.message || "Error al obtener detalles del movimiento"
        );
      }
    },
    error: function () {
      NotificacionToast(
        "error",
        "Error al cargar los detalles del movimiento."
      );
    },
  });
});

// Event listener for Aprobar Movimiento button
$(document).on("click", ".btnAprobarMovimiento", function () {
  const idMovimiento = $(this).data("id");

  if (!idMovimiento) {
    NotificacionToast("error", "No se pudo obtener el ID del movimiento.");
    return;
  }

  Swal.fire({
    title: "¿Aprobar movimiento?",
    text: "Esta acción aprobará el movimiento y lo ejecutará físicamente.",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, aprobar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../../controllers/EdicionesMovController.php?action=aprobarMovimiento",
        type: "POST",
        data: { idMovimiento: idMovimiento },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            NotificacionToast("success", res.message);
            // Recargar la tabla
            $("#tblMovimientos").DataTable().ajax.reload();
          } else {
            NotificacionToast("error", res.message);
          }
        },
        error: function () {
          NotificacionToast("error", "Error al aprobar el movimiento.");
        },
      });
    }
  });
});

// Event listener for Rechazar Movimiento button
$(document).on("click", ".btnRechazarMovimiento", function () {
  const idMovimiento = $(this).data("id");

  if (!idMovimiento) {
    NotificacionToast("error", "No se pudo obtener el ID del movimiento.");
    return;
  }

  Swal.fire({
    title: "¿Rechazar movimiento?",
    text: "Esta acción rechazará el movimiento permanentemente.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, rechazar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../../controllers/EdicionesMovController.php?action=rechazarMovimiento",
        type: "POST",
        data: { idMovimiento: idMovimiento },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            NotificacionToast("success", res.message);
            // Recargar la tabla
            $("#tblMovimientos").DataTable().ajax.reload();
          } else {
            NotificacionToast("error", res.message);
          }
        },
        error: function () {
          NotificacionToast("error", "Error al rechazar el movimiento.");
        },
      });
    }
  });
});

// Event handler para el botón "Agregar Activo"
$(document).on("click", "#btnAgregarActivo", function () {
  $("#modalAgregarActivo").modal("show");

  // Esperar a que el modal se abra completamente antes de inicializar la tabla
  $("#modalAgregarActivo").on("shown.bs.modal.listarActivos", function () {
    console.log("Modal de activos se abrió completamente, inicializando tabla");
    listarActivosParaSeleccion();
    // Remover el evento para evitar múltiples inicializaciones
    $(this).off("shown.bs.modal.listarActivos");
  });
});

// Función para listar activos disponibles para selección
function listarActivosParaSeleccion() {
  console.log("Inicializando tabla de activos para selección");

  // Obtener la sucursal de origen del movimiento actual
  const idSucursalOrigen = $("#editIdMovimiento").data("sucursalOrigen");

  // Destruir la instancia existente si existe
  if ($.fn.DataTable.isDataTable("#tblSeleccionarActivos")) {
    $("#tblSeleccionarActivos").DataTable().destroy();
  }

  $("#tblSeleccionarActivos").DataTable({
    dom: "Bfrtip",
    responsive: false,
    processing: true,
    ajax: {
      url: "../../controllers/EdicionesMovController.php?action=buscarActivosDisponibles",
      type: "POST",
      data: function (d) {
        // Agregar filtro por sucursal de origen
        d.idSucursal = idSucursalOrigen;
        return d;
      },
      dataType: "json",
      dataSrc: function (json) {
        console.log("Datos recibidos para selección de activos:", json);
        if (!json.status) {
          NotificacionToast("error", json.message);
          return [];
        }
        return json.data || [];
      },
      error: function (xhr, status, error) {
        console.error("Error al cargar activos:", error);
        NotificacionToast("error", "Error al cargar los activos: " + error);
        return [];
      },
    },
    columns: [
      { data: "IdActivo", title: "ID" },
      { data: "codigo", title: "Código" },
      { data: "NombreActivo", title: "Nombre" },
      { data: "sucursal", title: "Sucursal" },
      { data: "ambiente", title: "Ambiente" },
      {
        data: null,
        title: "Acción",
        orderable: false,
        render: function (data, type, row) {
          return `<button class="btn btn-success btn-sm btnSeleccionarActivoParaMovimiento" data-id="${
            row.IdActivo
          }" data-codigo="${row.codigo}" data-nombre="${
            row.NombreActivo
          }" data-sucursal="${row.sucursal || "Sin sucursal"}" data-ambiente="${
            row.ambiente || "Sin ambiente"
          }" title="Seleccionar activo">
                            <i class="fa fa-check"></i> Seleccionar
                        </button>`;
        },
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
    order: [[2, "asc"]], // Ordenar por NombreActivo
    pageLength: 10,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    drawCallback: function () {
      console.log("Tabla de selección de activos renderizada correctamente");
    },
  });
}

// Event handler para seleccionar activo de la tabla
$(document).on("click", ".btnSeleccionarActivoParaMovimiento", function () {
  const activo = {
    id: $(this).data("id"),
    codigo: $(this).data("codigo"),
    nombre: $(this).data("nombre"),
    Sucursal: $(this).data("sucursal") || "Sin sucursal",
    Ambiente: $(this).data("ambiente") || "Sin ambiente",
  };

  const idMovimiento = $("#editIdMovimiento").val();
  const modo = $("#modalAgregarActivo").data("modo");

  const titulo =
    modo === "cambiar"
      ? "¿Cambiar activo en el movimiento?"
      : "¿Agregar activo al movimiento?";
  const textoBoton = modo === "cambiar" ? "Sí, cambiar" : "Sí, agregar";

  Swal.fire({
    title: titulo,
    html: `
            <div class="text-left">
                <p><strong>Activo:</strong> ${activo.nombre} (${activo.codigo})</p>
                <p><strong>Sucursal actual:</strong> ${activo.Sucursal}</p>
                <p><strong>Ambiente actual:</strong> ${activo.Ambiente}</p>
            </div>
        `,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#6c757d",
    confirmButtonText: textoBoton,
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Guardar datos del activo en el modal para uso posterior
      $("#modalAgregarActivo").data("activo-codigo", activo.codigo);
      $("#modalAgregarActivo").data("activo-nombre", activo.nombre);
      $("#modalAgregarActivo").data("activo-sucursal", activo.Sucursal);
      $("#modalAgregarActivo").data("activo-ambiente", activo.Ambiente);

      if (modo === "cambiar") {
        const idActivoActual = $("#modalAgregarActivo").data("idActivoActual");
        cambiarActivoEnMovimiento(idMovimiento, idActivoActual, activo.id);
      } else {
        agregarActivoAlMovimiento(idMovimiento, activo.id);
      }
    }
  });
});

// Función para agregar activo al movimiento (solo en memoria, no guarda en BD)
function agregarActivoAlMovimiento(idMovimiento, idActivo) {
  // Obtener datos del activo seleccionado
  const activo = {
    id: idActivo,
    codigo: $("#modalAgregarActivo").data("activo-codigo"),
    nombre: $("#modalAgregarActivo").data("activo-nombre"),
    Sucursal: $("#modalAgregarActivo").data("activo-sucursal"),
    Ambiente: $("#modalAgregarActivo").data("activo-ambiente"),
  };

  // Agregar a la tabla visual
  if (agregarActivoAlDetalleEdicion(activo)) {
    $("#modalAgregarActivo").modal("hide");
  }
}

// Función para agregar activo al detalle de edición (solo en memoria)
function agregarActivoAlDetalleEdicion(activo) {
  if ($(`#tbldetalleactivomov tbody tr[data-id='${activo.id}']`).length > 0) {
    NotificacionToast(
      "error",
      `El activo <b>${activo.nombre}</b> ya está en el detalle.`
    );
    return false;
  }

  // Validar que el activo tenga todos los datos necesarios
  if (!activo.id || !activo.nombre) {
    NotificacionToast("error", "El activo no tiene todos los datos necesarios");
    return false;
  }

  var numeroFilas = $("#tbldetalleactivomov").find("tbody tr").length;

  var selectAmbienteDestino = `<select class='form-control form-control-sm ambiente-destino' name='ambiente_destino[]' id="comboAmbiente${numeroFilas}" required>
        <option value="">Seleccione ambiente...</option>
    </select>`;
  var selectResponsableDestino = `<select class='form-control form-control-sm responsable-destino' name='responsable_destino[]' id="comboResponsable${numeroFilas}" required>
        <option value="">Seleccione responsable...</option>
    </select>`;

  // Indicadores visuales mejorados
  var badgeOrigen = `<span class="badge badge-info"><i class="fas fa-map-marker-alt"></i> Origen</span>`;
  var badgeDestino = `<span class="badge badge-success"><i class="fas fa-arrow-right"></i> Destino</span>`;
  var badgeNuevo = `<span class="badge badge-warning"><i class="fas fa-plus"></i> Nuevo</span>`;

  var nuevaFila = `<tr data-id='${
    activo.id
  }' class='table-light border-left border-warning border-3' data-nuevo='true' data-estado='agregado'>
        <td class="text-center">
            ${activo.id}
            <br><small class="text-muted">${badgeNuevo}</small>
        </td>
        <td><strong>${activo.codigo}</strong></td>
        <td>${activo.nombre}</td>
        <td>${badgeOrigen} ${activo.Sucursal || "Sin sucursal"}</td>
        <td>${badgeOrigen} ${activo.Ambiente || "Sin ambiente"}</td>
        <td>${badgeDestino} ${
    $("#editSucursalDestino option:selected").text() || "Sin destino"
  }</td>
        <td>${selectAmbienteDestino}</td>
        <td>${selectResponsableDestino}</td>
        <td class="text-center">
            <div class="btn-group">
                <button type='button' class='btn btn-danger btn-sm btnEliminarActivo' data-activo="${
                  activo.id
                }" title='Quitar activo'>
                    <i class='fa fa-trash'></i>
                </button>
            </div>
        </td>
    </tr>`;
  $("#tbldetalleactivomov tbody").append(nuevaFila);

//   <button type='button' class='btn btn-warning btn-sm btnCambiarActivo' data-activo="${
//                   activo.id
//                 }" title='Cambiar activo'>
//                     <i class='fas fa-exchange-alt'></i>
//                 </button>

  // Inicializar los combos con la sucursal destino actual
  ListarCombosAmbiente(`comboAmbiente${numeroFilas}`);
  ListarCombosResponsable(`comboResponsable${numeroFilas}`);

  // Agregar validación mejorada al cambiar los combos
  $(`#comboAmbiente${numeroFilas}, #comboResponsable${numeroFilas}`).on(
    "change",
    function () {
      const $this = $(this);
      const fila = $this.closest("tr");

      // Validación individual
      if (!$this.val()) {
        $this.addClass("is-invalid").removeClass("is-valid");
      } else {
        $this.removeClass("is-invalid").addClass("is-valid");
        // Marcar fila como modificada
        fila.addClass("table-info");
        fila.data("estado", "modificado");
      }

      // Marcar que hay cambios pendientes
      $("#frmEditarMovimiento").data("cambios-pendientes", true);
      actualizarContadorCambios();
    }
  );

  // Animación de entrada mejorada
  setTimeout(function () {
    $("#tbldetalleactivomov tbody tr[data-id='${activo.id}']")
      .addClass("table-success")
      .css("animation", "fadeIn 0.5s ease-in");

    setTimeout(function () {
      $("#tbldetalleactivomov tbody tr[data-id='${activo.id}']").removeClass(
        "table-success"
      );
    }, 1000);
  }, 100);

  // Marcar que hay cambios pendientes
  $("#frmEditarMovimiento").data("cambios-pendientes", true);
  actualizarContadorCambios();

  NotificacionToast(
    "success",
    `✅ Activo <b>${activo.nombre}</b> agregado. Los cambios se guardarán al confirmar.`
  );

  // Cerrar modal automáticamente
  $("#modalAgregarActivo").modal("hide");

  return true;
}

// Función para cambiar activo en el movimiento (solo en memoria, no guarda en BD)
function cambiarActivoEnMovimiento(
  idMovimiento,
  idActivoActual,
  idNuevoActivo
) {
  // Solo cambiar en la tabla visual, no guardar en BD
  $("#modalAgregarActivo").modal("hide");

  // Marcar que hay cambios pendientes
  $("#frmEditarMovimiento").data("cambios-pendientes", true);

  NotificacionToast(
    "success",
    "Activo cambiado. Los cambios se guardarán al confirmar."
  );
}

// Event handler para cambiar activo
$(document).on("click", ".btnCambiarActivo", function () {
  const idActivoActual = $(this).data("activo");
  const idMovimiento = $("#editIdMovimiento").val();

  // Abrir modal de selección para cambiar el activo
  $("#modalAgregarActivo").data("modo", "cambiar");
  $("#modalAgregarActivo").data("idActivoActual", idActivoActual);
  $("#modalAgregarActivo").modal("show");
});

// Event handler para eliminar activo
$(document).on("click", ".btnEliminarActivo", function () {
  const idActivo = $(this).data("activo");
  const idMovimiento = $("#editIdMovimiento").val();
  const fila = $(this).closest("tr");
  const nombreActivo = fila.find("td:eq(2)").text();

  Swal.fire({
    title: "¿Quitar activo?",
    text: `¿Está seguro de quitar "${nombreActivo}" del movimiento?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, quitar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      eliminarActivoDelMovimiento(idMovimiento, idActivo);
    }
  });
});

// Función para eliminar activo del movimiento (solo en memoria, no guarda en BD)
function eliminarActivoDelMovimiento(idMovimiento, idActivo) {
  const fila = $(`#tbldetalleactivomov tbody tr[data-id='${idActivo}']`);
  const nombreActivo = fila.find("td:eq(2)").text();

  // Si es un activo existente (no nuevo), marcarlo para eliminación
  if (!fila.data("nuevo")) {
    fila.data("eliminar", true);
    fila.data("estado", "eliminado");
    fila.addClass("table-danger");
    fila.find("td").css("opacity", "0.6");
    fila.find("button").prop("disabled", true);

    // Agregar badge de eliminación
    const badgeEliminado = `<span class="badge badge-danger"><i class="fas fa-trash"></i> Eliminar</span>`;
    fila.find("td:first").append(`<br><small>${badgeEliminado}</small>`);

    NotificacionToast(
      "warning",
      `🗑️ Activo <b>${nombreActivo}</b> marcado para eliminación. Los cambios se guardarán al confirmar.`
    );
  } else {
    // Si es un activo nuevo, eliminarlo completamente con animación
    fila.addClass("table-danger");
    fila.fadeOut(300, function () {
      $(this).remove();
      actualizarContadorCambios();
    });
    NotificacionToast(
      "info",
      `❌ Activo <b>${nombreActivo}</b> eliminado. Los cambios se guardarán al confirmar.`
    );
  }

  // Marcar que hay cambios pendientes
  $("#frmEditarMovimiento").data("cambios-pendientes", true);
  actualizarContadorCambios();
}

// Event handler para cambios en ambiente o responsable (solo validación visual)
$(document).on(
  "change",
  ".ambiente-destino, .responsable-destino",
  function () {
    const $this = $(this);
    const fila = $this.closest("tr");
    const ambienteVal = fila.find(".ambiente-destino").val();
    const responsableVal = fila.find(".responsable-destino").val();

    // Validación visual - marcar como válido si tiene valor
    if ($this.hasClass("ambiente-destino") && ambienteVal) {
      $this.removeClass("is-invalid").addClass("is-valid");
    } else if ($this.hasClass("responsable-destino") && responsableVal) {
      $this.removeClass("is-invalid").addClass("is-valid");
    } else if (!$this.val()) {
      $this.removeClass("is-valid").addClass("is-invalid");
    }

    // Marcar fila como modificada si no es nueva
    if (!fila.data("nuevo") && !fila.data("eliminar")) {
      fila.addClass("table-info");
      fila.data("estado", "modificado");

      // Agregar badge de modificado si no existe
      if (!fila.find(".badge-warning").length) {
        const badgeModificado = `<span class="badge badge-warning"><i class="fas fa-edit"></i> Modificado</span>`;
        fila.find("td:first").append(`<br><small>${badgeModificado}</small>`);
      }
    }

    // Marcar que hay cambios pendientes
    $("#frmEditarMovimiento").data("cambios-pendientes", true);
    actualizarContadorCambios();
  }
);

// Función para actualizar activo en movimiento
function actualizarActivoEnMovimiento(
  idMovimiento,
  idActivo,
  idAmbienteNuevo,
  idResponsableNuevo
) {
  $.ajax({
    url: "../../controllers/EdicionesMovController.php?action=gestionarDetalleMovimientoPendiente",
    type: "POST",
    data: {
      accion: 1, // Actualizar
      idMovimiento: idMovimiento,
      idActivo: idActivo,
      idAmbienteNuevo: idAmbienteNuevo || null,
      idResponsableNuevo: idResponsableNuevo || null,
    },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        NotificacionToast("success", "Activo actualizado correctamente");
      } else {
        NotificacionToast("error", res.message);
      }
    },
    error: function () {
      NotificacionToast("error", "Error al actualizar activo");
    },
  });
}

// Limpiar modal de agregar activo cuando se cierre
$("#modalAgregarActivo").on("hidden.bs.modal", function () {
  // Limpiar la tabla DataTable si existe
  if ($.fn.DataTable.isDataTable("#tblSeleccionarActivos")) {
    $("#tblSeleccionarActivos").DataTable().destroy();
  }
  $(this).removeData("modo");
  $(this).removeData("idActivoActual");
});

// Función para cargar ambientes disponibles
function ListarCombosAmbiente(elemento) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=comboAmbiente",
    type: "POST",
    data: {
      idEmpresa: $("#editEmpresaDestino").val(),
      idSucursal: $("#editSucursalDestino").val(),
    },
    dataType: "json",
    async: false,
    success: (res) => {
      if (res.status) {
        $(`#${elemento}`).html(
          '<option value="">Seleccionar Ambiente</option>' + res.data
        );
        $(`#${elemento}`).select2({
          theme: "bootstrap4",
          width: "100%",
          placeholder: "Seleccionar Ambiente",
          allowClear: true,
        });
      } else {
        NotificacionToast(
          "error",
          "No se pudieron cargar los ambientes: " + res.message
        );
      }
    },
    error: (xhr, status, error) => {
      NotificacionToast("error", "Error al cargar ambientes: " + error);
    },
  });
}

// Función para cargar responsables disponibles
function ListarCombosResponsable(elemento) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=comboReceptor",
    type: "POST",
    data: {
      idEmpresa: $("#editEmpresaDestino").val(),
      idSucursal: $("#editSucursalDestino").val(),
    },
    dataType: "json",
    async: false,
    success: (res) => {
      if (res.status) {
        $(`#${elemento}`).html(
          '<option value="">Seleccionar Responsable</option>' + res.data
        );
        $(`#${elemento}`).select2({
          theme: "bootstrap4",
          width: "100%",
          placeholder: "Seleccionar Responsable",
          allowClear: true,
        });
      } else {
        NotificacionToast(
          "error",
          "No se pudieron cargar los responsables: " + res.message
        );
      }
    },
    error: (xhr, status, error) => {
      NotificacionToast("error", "Error al cargar responsables: " + error);
    },
  });
}

// Event listener for Editar Movimiento button
$(document).on("click", ".btnVerMovimiento", function () {
  const idMovimiento = $(this).data("id");

  if (!idMovimiento) {
    NotificacionToast("error", "No se pudo obtener el ID del movimiento.");
    return;
  }

  // Mostrar sección de edición y ocultar lista
  $("#divtblmovimientos").hide();
  $("#divlistadoMovimientos").hide();
  $("#divEditarMovimiento").show();

  // Cargar datos del movimiento
  cargarDatosMovimientoParaEditar(idMovimiento);
});

// Función para cargar datos del movimiento para editar
function cargarDatosMovimientoParaEditar(idMovimiento) {
  $.ajax({
    url: "../../controllers/EdicionesMovController.php?action=obtenerMovimientoParaEditar",
    type: "POST",
    data: { idMovimiento: idMovimiento },
    dataType: "json",
    success: function (res) {
      if (res.status && res.data) {
        const movimiento = res.data;

        // Llenar información básica del movimiento
        $("#editIdMovimiento").val(movimiento.idMovimiento);
        $("#editIdMovimiento").data(
          "sucursalOrigen",
          movimiento.idSucursalOrigen
        ); // Guardar ID de sucursal origen
        $("#editCodigoMovimiento").val(movimiento.codigoMovimiento);
        $("#editTipoMovimiento").val(movimiento.tipoMovimiento);
        $("#editEmpresaOrigen").val(movimiento.empresaOrigen);
        $("#editSucursalOrigen").val(movimiento.sucursalOrigen);
        $("#editAutorizador").val(movimiento.autorizador);

        // Limpiar flag de cambios pendientes
        $("#frmEditarMovimiento").removeData("cambios-pendientes");
        actualizarContadorCambios();

        // Cargar empresas y seleccionar la actual
        cargarEmpresasParaEditar(
          movimiento.idEmpresaDestino,
          movimiento.idSucursalDestino,
          movimiento.idReceptor,
          function () {
            // Cargar detalles de activos después de que los selects del header estén listos
            cargarDetallesMovimientoParaEditar(idMovimiento);
          }
        );
      } else {
        NotificacionToast(
          "error",
          res.message || "Error al obtener datos del movimiento"
        );
      }
    },
    error: function () {
      NotificacionToast("error", "Error al cargar datos del movimiento.");
    },
  });
}

// Función para cargar empresas en el modal de edición
function cargarEmpresasParaEditar(
  idEmpresaSeleccionada = null,
  idSucursalSeleccionada = null,
  idReceptorSeleccionado = null,
  callback = null
) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: function (res) {
      if (res.status) {
        $("#editEmpresaDestino").html(
          '<option value="">Seleccionar Empresa</option>' + res.data.empresas
        );
        if (idEmpresaSeleccionada) {
          $("#editEmpresaDestino").val(idEmpresaSeleccionada).trigger("change");
        }
        $("#editEmpresaDestino").select2({
          theme: "bootstrap4",
          width: "100%",
          placeholder: "Seleccionar Empresa Destino",
          allowClear: true,
        });

        // Si hay empresa seleccionada, cargar sucursales y receptores
        if (idEmpresaSeleccionada) {
          setTimeout(() => {
            cargarSucursalesParaEditar(
              idEmpresaSeleccionada,
              idSucursalSeleccionada,
              idReceptorSeleccionado,
              callback
            );
          }, 200);
        } else if (callback) {
          callback();
        }
      } else {
        NotificacionToast(
          "error",
          "No se pudieron cargar las empresas: " + res.message
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al cargar empresas:", error);
      NotificacionToast("error", "Error al cargar empresas: " + error);
    },
  });
}

// Función para cargar sucursales para edición
function cargarSucursalesParaEditar(
  idEmpresa,
  idSucursalSeleccionada = null,
  idReceptorSeleccionado = null,
  callback = null
) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=comboUnidadNegocio",
    type: "POST",
    data: { codEmpresa: idEmpresa },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        $("#editSucursalDestino").html(
          '<option value="">Seleccionar Sucursal</option>' + res.data
        );
        if (idSucursalSeleccionada) {
          $("#editSucursalDestino")
            .val(idSucursalSeleccionada)
            .trigger("change");
        }
        $("#editSucursalDestino").select2({
          theme: "bootstrap4",
          width: "100%",
          placeholder: "Seleccionar Sucursal Destino",
          allowClear: true,
        });

        // Si hay sucursal seleccionada, cargar receptores
        if (idSucursalSeleccionada) {
          setTimeout(() => {
            cargarReceptoresParaEditar(
              idEmpresa,
              idSucursalSeleccionada,
              idReceptorSeleccionado,
              callback
            );
          }, 300);
        } else if (callback) {
          callback();
        }
      } else {
        NotificacionToast(
          "error",
          "No se pudieron cargar las sucursales: " + res.message
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al cargar sucursales:", error);
      NotificacionToast("error", "Error al cargar sucursales: " + error);
    },
  });
}

// Función para cargar receptores para edición
function cargarReceptoresParaEditar(
  idEmpresa,
  idSucursal,
  idReceptorSeleccionado = null,
  callback = null
) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=comboReceptor",
    type: "POST",
    data: { idEmpresa: idEmpresa, idSucursal: idSucursal },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        $("#editReceptor").html(
          '<option value="">Seleccionar Receptor</option>' + res.data
        );
        if (idReceptorSeleccionado) {
          $("#editReceptor").val(idReceptorSeleccionado).trigger("change");
        }
        $("#editReceptor").select2({
          theme: "bootstrap4",
          width: "100%",
          placeholder: "Seleccionar Receptor",
          allowClear: true,
        });
        if (callback) callback();
      } else {
        NotificacionToast(
          "error",
          "No se pudieron cargar los receptores: " + res.message
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("Error al cargar receptores:", error);
      NotificacionToast("error", "Error al cargar receptores: " + error);
    },
  });
}

// Event listener para cambio de empresa destino
$(document).on("change", "#editEmpresaDestino", function () {
  const idEmpresa = $(this).val();

  if (idEmpresa) {
    // Cargar sucursales de la empresa seleccionada
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=comboUnidadNegocio",
      type: "POST",
      data: { codEmpresa: idEmpresa },
      dataType: "json",
      success: function (res) {
        if (res.status) {
          $("#editSucursalDestino").html(
            '<option value="">Seleccionar Sucursal</option>' + res.data
          );
          $("#editSucursalDestino").select2({
            theme: "bootstrap4",
            width: "100%",
            placeholder: "Seleccionar Sucursal Destino",
            allowClear: true,
          });

          // Limpiar receptor cuando cambia la empresa
          $("#editReceptor").html(
            '<option value="">Seleccionar Receptor</option>'
          );
          $("#editReceptor").select2({
            theme: "bootstrap4",
            width: "100%",
            placeholder: "Seleccionar Receptor",
            allowClear: true,
          });

          // Recargar ambientes y responsables para los activos
          setTimeout(() => {
            cargarAmbientesParaActivos();
            cargarResponsablesParaActivos();
          }, 200);
        } else {
          NotificacionToast(
            "error",
            "Error al cargar sucursales: " + res.message
          );
        }
      },
      error: function () {
        NotificacionToast("error", "Error al cargar sucursales");
      },
    });
  } else {
    $("#editSucursalDestino").html(
      '<option value="">Seleccionar Sucursal</option>'
    );
    $("#editReceptor").html('<option value="">Seleccionar Receptor</option>');
  }
});

// Event listener para cambio de sucursal destino
$(document).on("change", "#editSucursalDestino", function () {
  const idEmpresa = $("#editEmpresaDestino").val();
  const idSucursal = $(this).val();

  if (idEmpresa && idSucursal) {
    // Cargar empleados/receptores de la sucursal seleccionada
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=comboReceptor",
      type: "POST",
      data: { idEmpresa: idEmpresa, idSucursal: idSucursal },
      dataType: "json",
      success: function (res) {
        if (res.status) {
          $("#editReceptor").html(
            '<option value="">Seleccionar Receptor</option>' + res.data
          );
          $("#editReceptor").select2({
            theme: "bootstrap4",
            width: "100%",
            placeholder: "Seleccionar Receptor",
            allowClear: true,
          });

          // Recargar ambientes y responsables para los activos
          setTimeout(() => {
            cargarAmbientesParaActivos();
            cargarResponsablesParaActivos();
          }, 200);
        }
      },
    });
  } else {
    $("#editReceptor").html('<option value="">Seleccionar Receptor</option>');
  }
});

// Función para cargar detalles de activos para editar
function cargarDetallesMovimientoParaEditar(idMovimiento) {
  $.ajax({
    url: "../../controllers/EdicionesMovController.php?action=obtenerDetallesMovimientoParaEditar",
    type: "POST",
    data: { idMovimiento: idMovimiento },
    dataType: "json",
    success: function (res) {
      if (res.status && res.data) {
        const tbody = $("#tbldetalleactivomov tbody");
        tbody.empty();

        res.data.forEach(function (detalle, index) {
          const selectAmbienteDestino = `<select class='form-control form-control-sm ambiente-destino' name='ambiente_destino[]' id="comboAmbiente${index}" required>
                        <option value="">Seleccione ambiente...</option>
                    </select>`;
          const selectResponsableDestino = `<select class='form-control form-control-sm responsable-destino' name='responsable_destino[]' id="comboResponsable${index}" required>
                        <option value="">Seleccione responsable...</option>
                    </select>`;

          // Indicadores visuales para activos existentes
          var badgeOrigen = `<span class="badge badge-info"><i class="fas fa-map-marker-alt"></i> Origen</span>`;
          var badgeDestino = `<span class="badge badge-success"><i class="fas fa-arrow-right"></i> Destino</span>`;
          var badgeExistente = `<span class="badge badge-secondary"><i class="fas fa-check"></i> Existente</span>`;

          const filaHtml = `
                        <tr data-id='${
                          detalle.idActivo
                        }' class='table-light border-left border-primary border-3' data-nuevo='false' data-estado='original'>
                            <td class="text-center">
                                ${detalle.idActivo}
                                <br><small class="text-muted">${badgeExistente}</small>
                            </td>
                            <td><strong>${detalle.codigoActivo}</strong></td>
                            <td>${detalle.nombreActivo}</td>
                            <td>${badgeOrigen} ${
            detalle.nombreSucursalOrigen || "Sin sucursal"
          }</td>
                            <td>${badgeOrigen} ${
            detalle.ambienteOrigen || "Sin ambiente"
          }</td>
                            <td>${badgeDestino} ${
            $("#editSucursalDestino option:selected").text() || "Sin destino"
          }</td>
                            <td>${selectAmbienteDestino}</td>
                            <td>${selectResponsableDestino}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type='button' class='btn btn-danger btn-sm btnEliminarActivo' data-activo="${
                                      detalle.idActivo
                                    }" title='Quitar activo'>
                                        <i class='fa fa-trash'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
          tbody.append(filaHtml);
        });

        // Cargar ambientes y responsables para cada activo después de un pequeño delay
        setTimeout(() => {
          cargarAmbientesParaActivos();
          cargarResponsablesParaActivos();
        }, 500);

        // Agregar validación mejorada al cambiar los combos
        $("#tbldetalleactivomov tbody tr").each(function (index) {
          const row = $(this);
          const comboAmbiente = row.find(".ambiente-destino");
          const comboResponsable = row.find(".responsable-destino");

          $(`#comboAmbiente${index}, #comboResponsable${index}`).on(
            "change",
            function () {
              const $this = $(this);
              const fila = $this.closest("tr");
              const ambienteVal = $(`#comboAmbiente${index}`).val();
              const responsableVal = $(`#comboResponsable${index}`).val();

              // Validación individual
              if (!$this.val()) {
                $this.addClass("is-invalid").removeClass("is-valid");
              } else {
                $this.removeClass("is-invalid").addClass("is-valid");

                // Marcar fila como modificada
                if (!fila.data("nuevo") && !fila.data("eliminar")) {
                  fila.addClass("table-info");
                  fila.data("estado", "modificado");

                  // Agregar badge de modificado si no existe
                  if (!fila.find(".badge-warning").length) {
                    const badgeModificado = `<span class="badge badge-warning"><i class="fas fa-edit"></i> Modificado</span>`;
                    fila
                      .find("td:first")
                      .append(`<br><small>${badgeModificado}</small>`);
                  }
                }
              }

              // Marcar que hay cambios pendientes
              $("#frmEditarMovimiento").data("cambios-pendientes", true);
              actualizarContadorCambios();
            }
          );
        });
      }
    },
    error: function () {
      NotificacionToast("error", "Error al cargar detalles de activos.");
    },
  });
}

// <button type='button' class='btn btn-warning btn-sm btnCambiarActivo' data-activo="${
//                                       detalle.idActivo
//                                     }" title='Cambiar activo'>
//                                         <i class='fas fa-exchange-alt'></i>
//                                     </button>

// Función para cargar ambientes disponibles
function cargarAmbientesParaActivos() {
  const idEmpresa = $("#editEmpresaDestino").val();
  const idSucursal = $("#editSucursalDestino").val();

  if (idEmpresa && idSucursal) {
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=comboAmbiente",
      type: "POST",
      data: { idEmpresa: idEmpresa, idSucursal: idSucursal },
      dataType: "json",
      success: function (res) {
        if (res.status) {
          $(".ambiente-destino").each(function () {
            $(this).html(
              '<option value="">Seleccionar Ambiente</option>' + res.data
            );
            $(this).select2({
              theme: "bootstrap4",
              width: "100%",
              placeholder: "Seleccionar Ambiente",
              allowClear: true,
            });
          });
        } else {
          NotificacionToast(
            "error",
            "No se pudieron cargar los ambientes: " + res.message
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al cargar ambientes:", error);
        NotificacionToast("error", "Error al cargar ambientes: " + error);
      },
    });
  }
}

// Función para cargar responsables disponibles
function cargarResponsablesParaActivos() {
  const idEmpresa = $("#editEmpresaDestino").val();
  const idSucursal = $("#editSucursalDestino").val();

  if (idEmpresa && idSucursal) {
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=comboReceptor",
      type: "POST",
      data: { idEmpresa: idEmpresa, idSucursal: idSucursal },
      dataType: "json",
      success: function (res) {
        if (res.status) {
          $(".responsable-destino").each(function () {
            $(this).html(
              '<option value="">Seleccionar Responsable</option>' + res.data
            );
            $(this).select2({
              theme: "bootstrap4",
              width: "100%",
              placeholder: "Seleccionar Responsable",
              allowClear: true,
            });
          });
        } else {
          NotificacionToast(
            "error",
            "No se pudieron cargar los responsables: " + res.message
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al cargar responsables:", error);
        NotificacionToast("error", "Error al cargar responsables: " + error);
      },
    });
  }
}

// Event handler para el formulario de editar movimiento
$("#frmEditarMovimiento").on("submit", function (e) {
  e.preventDefault();

  Swal.fire({
    title: "¿Guardar cambios?",
    text: "Se modificarán los datos del movimiento pendiente y la ubicación de los activos.",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, guardar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Recopilar datos del formulario
      const data = {
        idMovimiento: $("#editIdMovimiento").val(),
        idEmpresaDestino: $("#editEmpresaDestino").val(),
        idSucursalDestino: $("#editSucursalDestino").val(),
        idReceptor: $("#editReceptor").val(),
        activos: [],
      };

      // Recopilar cambios en los activos
      $("#tbldetalleactivomov tbody tr").each(function () {
        const fila = $(this);
        const idActivo = fila.data("id");
        const idAmbienteNuevo = fila.find(".ambiente-destino").val();
        const idResponsableNuevo = fila.find(".responsable-destino").val();
        const esNuevo = fila.data("nuevo") === true;
        const eliminar = fila.data("eliminar") === true;

        // Si está marcado para eliminar y no es nuevo, agregarlo a la lista de eliminación
        if (eliminar && !esNuevo) {
          data.activos.push({
            idActivo: idActivo,
            accion: "eliminar",
          });
        }
        // Si no está marcado para eliminar y tiene cambios, procesarlo
        else if (
          !eliminar &&
          idActivo &&
          (idAmbienteNuevo || idResponsableNuevo)
        ) {
          data.activos.push({
            idActivo: idActivo,
            idAmbienteNuevo: idAmbienteNuevo || null,
            idResponsableNuevo: idResponsableNuevo || null,
            esNuevo: esNuevo,
          });
        }
      });

      // Enviar datos de modificación
      $.ajax({
        url: "../../controllers/EdicionesMovController.php?action=modificarMovimientoPendiente",
        type: "POST",
        data: {
          action: "modificarMovimientoPendiente",
          data: JSON.stringify(data),
        },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            NotificacionToast("success", res.message);
            // Limpiar flag de cambios pendientes
            $("#frmEditarMovimiento").removeData("cambios-pendientes");
            actualizarContadorCambios();
            // Volver a la lista
            $("#divEditarMovimiento").hide();
            $("#divlistadoMovimientos").show();
            $("#divtblmovimientos").show();
            // Recargar la tabla
            $("#tblMovimientos").DataTable().ajax.reload();
          } else {
            NotificacionToast("error", res.message);
          }
        },
        error: function () {
          NotificacionToast("error", "Error al modificar el movimiento.");
        },
      });
    }
  });
});

// Event handler para botón "Volver a la Lista"
$(document).on("click", "#btnVolverLista", function () {
  const hayCambiosPendientes = $("#frmEditarMovimiento").data(
    "cambios-pendientes"
  );

  if (hayCambiosPendientes) {
    Swal.fire({
      title: "¿Volver a la lista?",
      text: "Se perderán los cambios no guardados.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Sí, volver",
      cancelButtonText: "Continuar editando",
    }).then((result) => {
      if (result.isConfirmed) {
        $("#divEditarMovimiento").hide();
        $("#divlistadomovimientos").show();
        $("#divtblmovimientos").show();
      }
    });
  } else {
    $("#divEditarMovimiento").hide();
    $("#divlistadomovimientos").show();
    $("#divtblmovimientos").show();
  }
});

// Event handler para botón "Cancelar Edición"
$(document).on("click", "#btnCancelarEdicion", function () {
  const hayCambiosPendientes = $("#frmEditarMovimiento").data(
    "cambios-pendientes"
  );

  if (hayCambiosPendientes) {
    Swal.fire({
      title: "¿Cancelar edición?",
      text: "Se perderán todos los cambios realizados.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Sí, cancelar",
      cancelButtonText: "Continuar editando",
    }).then((result) => {
      if (result.isConfirmed) {
        $("#divEditarMovimiento").hide();
        $("#divlistadoMovimientos").show();
        $("#divtblmovimientos").show();
      }
    });
  } else {
    $("#divEditarMovimiento").hide();
    $("#divlistadoMovimientos").show();
    $("#divtblmovimientos").show();
  }
});

// Event handler para botón "Guardar Cambios"
$(document).on("click", "#btnGuardarEdicion", function () {
  $("#frmEditarMovimiento").trigger("submit");
});
