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

  // Ocultar secciones al cargar
  $("#divgenerarmov").hide();
  $("#divregistroMovimiento").hide();
  $("#divformularioasignacion").hide();

  // Botón para abrir el panel de generación de movimiento
  $("#btnnuevo")
    .off("click")
    .on("click", function () {
      $("#divgenerarmov").show();
      $("#divregistroMovimiento").hide();
      $("#divtblmovimientos").hide();
      $("#divlistadomovimientos").hide();
    });

  // Botón para abrir el formulario de asignación de componentes
  $("#btnasignacion")
    .off("click")
    .on("click", function () {
      $("#divformularioasignacion").show();
      $("#divlistadomovimientos").hide();
      $("#divtblmovimientos").hide();
      $("#tbldetalleactivos tbody").empty();
      cargarActivosParaAsignacion();
    });

  // Botón para cancelar la asignación de componentes
  $("#btnCancelarAsignacion")
    .off("click")
    .on("click", function () {
      $("#divformularioasignacion").hide();
      $("#divlistadomovimientos").show();
      $("#divtblmovimientos").show();
    });

  // Manejar el envío del formulario de asignación de componentes
  $("#formAsignarComponente")
    .off("submit")
    .on("submit", function (e) {
      e.preventDefault();
      const formData = $(this).serialize();
      
      Swal.fire({
        title: "Procesando",
        text: "Guardando la asignación...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      $.ajax({
        url: "../../controllers/GestionarMovimientosComponentesController.php?action=asignarComponente",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (res) {
          Swal.close();
          if (res.success) {
            Swal.fire({
              title: "Éxito",
              text: res.message,
              icon: "success"
            }).then(() => {
              $("#divformularioasignacion").hide();
              $("#divlistadomovimientos").show();
              $("#divtblmovimientos").show();
              $("#formAsignarComponente")[0].reset();
              if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
                $("#tblMovimientos").DataTable().ajax.reload(null, false);
              }
            });
          } else {
            Swal.fire({
              title: "Error",
              text: res.message,
              icon: "error"
            });
          }
        },
        error: function (xhr, status, error) {
          Swal.close();
          Swal.fire({
            title: "Error",
            text: "Error al comunicarse con el servidor",
            icon: "error"
          });
        }
      });
    });

  // Botón procesar en generarmov
  $("#btnprocesarempresa")
    .off("click")
    .on("click", function () {
      // Validar campos obligatorios
      const autorizador = $("#CodAutorizador").val();
      const sucursalDestino = $("#IdSucursalDestino").val();

      if (!autorizador) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar un autorizador",
          icon: "warning",
        });
        return;
      }

      if (!sucursalDestino) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar una sucursal destino",
          icon: "warning",
        });
        return;
      }

      // Si todo está correcto, proceder con el procesamiento
      $("#divregistroMovimiento").show();
      $("#divgenerarmov").hide();

      // Transferir valores de los combos
      var autorizadorNombre = $("#CodAutorizador option:selected").text();
      $("#IdAutorizador").val($("#CodAutorizador").val());
      $("#lblautorizador").text("Autorizador: " + autorizadorNombre);

      // Cargar activos padres disponibles
      cargarActivosPadres();
    });

  // Botón cancelar en generarmov
  $("#btncancelarempresa")
    .off("click")
    .on("click", function () {
      $("#divgenerarmov").hide();
      $("#divtblmovimientos").show();
      $("#divlistadomovimientos").show();
    });

  // Botón cancelar en registro de movimiento
  $("#btnCancelarMovimiento")
    .off("click")
    .on("click", function () {
      $("#divregistroMovimiento").hide();
      $("#divtblmovimientos").show();
      $("#divlistadomovimientos").show();
    });

  $("#btnVolver")
    .off("click")
    .on("click", function () {
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Se perderán los cambios realizados",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Aceptar",
        cancelButtonColor: "#d33",
        cancelButtonText: "No, continuar aquí",
      }).then((result) => {
        if (result.isConfirmed) {
          $("#divregistroMovimiento").hide();
          // $("#divgenerarmov").show();
          $("#divtblmovimientos").show();
          $("#divlistadomovimientos").show();
        }
      });
    });

    $("#btnCancelarMovComp")
    .off("click")
    .on("click", function () {
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Se perderán los cambios realizados",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Aceptar",
        cancelButtonColor: "#d33",
        cancelButtonText: "No, continuar aquí",
      }).then((result) => {
        if (result.isConfirmed) {
          $("#divregistroMovimiento").hide();
          //$("#divgenerarmov").show();
          $("#divtblmovimientos").show();
          $("#divlistadomovimientos").show();
        }
      });
    });
    
  // Al seleccionar un activo padre origen, cargar sus componentes
  $("#IdActivoPadreOrigen").on("change", function () {
    const idActivoPadre = $(this).val();
    if (idActivoPadre) {
      cargarComponentesActivo(idActivoPadre);
    } else {
      $("#tbldetallecomponentes tbody").empty();
    }
  });

  // Botón para abrir el modal de búsqueda de componentes
  $("#btnBuscarComponentes").on("click", function() {
    const idActivoPadre = $("#IdActivoPadreOrigen").val();
    if (!idActivoPadre) {
      Swal.fire("Error", "Debe seleccionar un activo padre origen", "error");
      return;
    }
    $("#modalBuscarComponentes").modal("show");
    listarComponentesModal(idActivoPadre);
  });

  // Función para guardar el movimiento completo
  $("#btnGuardarMov").on("click", function () {
    // Verificar si el activo padre destino es diferente al origen
    const idActivoPadreOrigen = $("#IdActivoPadreOrigen").val();
    const idActivoPadreDestino = $("#IdActivoPadreDestino").val();

    if (idActivoPadreOrigen === idActivoPadreDestino) {
      Swal.fire({
        title: "Error",
        text: "No se puede mover un componente al mismo activo padre",
        icon: "error",
        confirmButtonText: "Aceptar"
      });
      return;
    }

    // Verificar si hay componentes habilitados (los que se enviarán)
    const componentesAEnviar = $("#tbldetallecomponentes tbody tr:not(.componente-deshabilitado)");
    if (componentesAEnviar.length === 0) {
      NotificacionToast("error", "Debe seleccionar al menos un componente para mover");
      return;
    }

    // Verificar que se haya seleccionado un activo padre destino
    if (!$("#IdActivoPadreDestino").val()) {
      NotificacionToast("error", "Debe seleccionar un activo padre destino");
      return;
    }

    // Mostrar loading
    Swal.fire({
      title: "Procesando",
      text: "Guardando el movimiento...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    // Obtener los datos del formulario principal
    const formData = new FormData();
    formData.append("IdTipo", 8); // Tipo 8 = Movimiento entre activos
    formData.append("autorizador", $("#IdAutorizador").val());
    formData.append("sucursal_destino", $("#IdSucursalDestino").val());
    formData.append("observacion", "");

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
          Swal.close();
          NotificacionToast("error", res.message || "Error al registrar el movimiento");
        }
      },
      error: function () {
        Swal.close();
        NotificacionToast("error", "Error al comunicarse con el servidor");
      },
    });
  });

  // Agregar evento para actualizar activos padres cuando cambie la sucursal destino
  $("#IdSucursalDestino").on("change", function() {
    cargarActivosPadres();
    // Limpiar los componentes cuando cambie la sucursal
    $("#tbldetallecomponentes tbody").empty();
    $("#IdActivoPadreOrigen").val("").trigger("change");
    $("#IdActivoPadreDestino").val("").trigger("change");
  });

  // Evento para abrir el modal de búsqueda de activos
  $(document).on("click", "#btnBuscarActivos", function () {
    $("#modalBuscarActivos").modal("show");
    listarActivosModalBusqueda();
  });

  // Evento para guardar la asignación de componentes
  $(document).on("click", "#btnGuardarAsignacion", function () {
    const idPadre = $("#IdAsignacionPadre").val();
    if (!idPadre) {
      Swal.fire("Error", "Debe seleccionar un activo padre.", "error");
      return;
    }
    const filas = $("#tbldetalleactivos tbody tr");
    if (filas.length === 0) {
      Swal.fire("Error", "Debe agregar al menos un componente.", "error");
      return;
    }

    let total = filas.length;
    let exitos = 0;
    let errores = 0;
    let erroresMsg = [];

    Swal.fire({
      title: "Procesando",
      text: "Asignando componentes...",
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    filas.each(function () {
      const idComponente = $(this).data("id");
      const observacion = $(this).find(".observacion-componente").val() || "";
      $.ajax({
        url: "../../controllers/GestionarMovimientosComponentesController.php?action=asignarComponente",
        type: "POST",
        data: {
          IdActivoPadre: idPadre,
          IdActivoComponente: idComponente,
          Observaciones: observacion
        },
        dataType: "json",
        success: function (res) {
          if (res.success) {
            exitos++;
          } else {
            errores++;
            erroresMsg.push(res.message || "Error al asignar componente " + idComponente);
          }
          if (exitos + errores === total) {
            Swal.close();
            if (errores === 0) {
              Swal.fire({
                icon: "success",
                title: "Éxito",
                text: "Todos los componentes fueron asignados correctamente.",
                timer: 1800
              });
              $("#tbldetalleactivos tbody").empty();
            } else {
              Swal.fire({
                icon: "warning",
                title: "Algunos errores",
                html: erroresMsg.join("<br>")
              });
            }
          }
        },
        error: function (xhr, status, error) {
          errores++;
          erroresMsg.push("Error de red al asignar componente " + idComponente);
          if (exitos + errores === total) {
            Swal.close();
            Swal.fire({
              icon: "warning",
              title: "Algunos errores",
              html: erroresMsg.join("<br>")
            });
          }
        }
      });
    });
  });
}

function cargarActivosPadres() {
  // Para origen usamos la sucursal de la sesión
  $.ajax({
    url: "../../controllers/GestionarMovimientosComponentesController.php?action=listarActivosPadres",
    type: "POST",
    data: { tipo: 'origen' },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        // Limpiar y cargar el select de activos padres origen
        $("#IdActivoPadreOrigen").empty();
        $("#IdActivoPadreOrigen").append('<option value="">Seleccione un activo padre</option>');
        
        res.data.forEach(function (activo) {
          const option = `<option value="${activo.IdActivo}">${activo.CodigoActivo} - ${activo.NombreArticulo}</option>`;
          $("#IdActivoPadreOrigen").append(option);
        });

        // Inicializar select2 para origen
        $("#IdActivoPadreOrigen").select2({
          theme: "bootstrap4",
          width: "100%",
        });
      } else {
        Swal.fire("Error", res.message, "error");
      }
    },
    error: function (xhr, status, error) {
      Swal.fire("Error", "Error al cargar los activos padres origen: " + error, "error");
    },
  });

  // Para destino usamos la sucursal seleccionada
  const sucursalDestino = $("#IdSucursalDestino").val();
  if (sucursalDestino) {
    $.ajax({
      url: "../../controllers/GestionarMovimientosComponentesController.php?action=listarActivosPadres",
      type: "POST",
      data: { tipo: 'destino', sucursal: sucursalDestino },
      dataType: "json",
      success: function (res) {
        if (res.status) {
          // Limpiar y cargar el select de activos padres destino
          $("#IdActivoPadreDestino").empty();
          $("#IdActivoPadreDestino").append('<option value="">Seleccione un activo padre</option>');
          
          res.data.forEach(function (activo) {
            const option = `<option value="${activo.IdActivo}">${activo.CodigoActivo} - ${activo.NombreArticulo}</option>`;
            $("#IdActivoPadreDestino").append(option);
          });

          // Inicializar select2 para destino
          $("#IdActivoPadreDestino").select2({
            theme: "bootstrap4",
            width: "100%",
          });
        } else {
          Swal.fire("Error", res.message, "error");
        }
      },
      error: function (xhr, status, error) {
        Swal.fire("Error", "Error al cargar los activos padres destino: " + error, "error");
      },
    });
  } else {
    // Si no hay sucursal destino seleccionada, limpiar el selector
    $("#IdActivoPadreDestino").empty().append('<option value="">Seleccione un activo padre</option>').trigger('change');
  }
}

function cargarActivosParaAsignacion() {
  // Cargar todos los activos disponibles para asignar como padre
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=ConsultarActivosRelacionados",
    type: "POST",
    data: { IdArticulo: "", IdActivo: "" },
    dataType: "json",
    success: function (res) {
      let data = res && res.length ? res : (res.data || []);
      if (data.length > 0) {
        $("#IdAsignacionPadre").empty();
        $("#IdAsignacionPadre").append('<option value="">Seleccione un activo padre</option>');
        data.forEach(function (activo) {
          const id = activo.idActivo || activo.IdActivo;
          const nombre = activo.NombreActivoVisible || activo.Nombre || activo.nombreActivoVisible;
          const codigo = activo.CodigoActivo || activo.Codigo;
          const serie = activo.NumeroSerie || activo.Serie || "";
          $("#IdAsignacionPadre").append(`<option value="${id}">${codigo} - ${nombre} (${serie})</option>`);
        });
        if (!$("#IdAsignacionPadre").hasClass("select2-hidden-accessible")) {
          $("#IdAsignacionPadre").select2({ theme: "bootstrap4", width: "100%" });
        }
      } else {
        Swal.fire("Error", "No se encontraron activos disponibles", "error");
      }
    },
    error: function (xhr, status, error) {
      Swal.fire("Error", "Error al cargar los activos: " + error, "error");
    }
  });
  // No llenar la tabla de detalle aquí
}

// Evento para seleccionar/deseleccionar componentes en la tabla
$(document).off("click", ".btnSeleccionarComponente").on("click", ".btnSeleccionarComponente", function () {
  const $btn = $(this);
  const $tr = $btn.closest("tr");
  if ($tr.hasClass("table-success")) {
    $tr.removeClass("table-success");
    $btn.removeClass("btn-danger").addClass("btn-success").html('<i class="fa fa-check"></i> Seleccionar');
  } else {
    $tr.addClass("table-success");
    $btn.removeClass("btn-success").addClass("btn-danger").html('<i class="fa fa-times"></i> Quitar');
  }
});

function cargarComponentesActivo(idActivoPadre) {
  $.ajax({
    url: "../../controllers/GestionarMovimientosComponentesController.php?action=listarComponentesActivo",
    type: "POST",
    data: { idActivoPadre: idActivoPadre },
    dataType: "json",
    success: function (response) {
      if (response.status) {
        $("#tbldetallecomponentes tbody").empty();
        response.data.forEach(function (componente) {
          $("#tbldetallecomponentes tbody").append(`
            <tr data-id="${componente.IdActivo}" class="componente-deshabilitado" style="opacity: 0.6; background-color: #f8f9fa;">
              <td>${componente.CodigoActivo}</td>
              <td>${componente.NombreArticulo}</td>
              <td>${componente.MarcaArticulo}</td>
              <td>${componente.NumeroSerie}</td>
              <td>
                <input type="text" class="form-control componente-observacion" placeholder="Ingrese observaciones para este componente">
              </td>
              <td>
                <button type="button" class="btn btn-primary btn-sm btn-seleccionar">
                  <i class="fas fa-check"></i> Seleccionar
                </button>
              </td>
            </tr>
          `);
        });

        // Agregar el evento click solo al botón de selección
        $(".btn-seleccionar").on("click", function() {
          const fila = $(this).closest("tr");
          const nombreComponente = fila.find("td:eq(1)").text(); // Obtener el nombre del componente
          
          if (fila.hasClass("componente-deshabilitado")) {
            fila.removeClass("componente-deshabilitado").css({
              opacity: 1,
              backgroundColor: ""
            });
            $(this).html('<i class="fas fa-times"></i> Deseleccionar');
            NotificacionToast("success", `Componente <b>${nombreComponente}</b> incluido en el envío`);
          } else {
            fila.addClass("componente-deshabilitado").css({
              opacity: 0.6,
              backgroundColor: "#f8f9fa"
            });
            $(this).html('<i class="fas fa-check"></i> Seleccionar');
            NotificacionToast("warning", `Componente <b>${nombreComponente}</b> excluido del envío`);
          }
        });
      } else {
        NotificacionToast("error", "Error al cargar componentes: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      NotificacionToast("error", "Error al cargar componentes: " + error);
    },
  });
}

function guardarDetallesMovimiento(idMovimiento) {
  let detallesGuardados = 0;
  let totalDetalles = $("#tbldetallecomponentes tbody tr:not(.componente-deshabilitado)").length;
  let errores = [];

  if (totalDetalles === 0) {
    Swal.close();
    NotificacionToast("error", "No hay componentes seleccionados para mover");
    return;
  }

  // Iterar solo sobre los componentes habilitados (los que se enviarán)
  $("#tbldetallecomponentes tbody tr:not(.componente-deshabilitado)").each(function () {
    const fila = $(this);
    const detalleData = new FormData();

    detalleData.append("IdMovimiento", idMovimiento);
    detalleData.append("IdActivoComponente", fila.data("id"));
    detalleData.append("IdActivoPadreNuevo", $("#IdActivoPadreDestino").val());
    detalleData.append("IdTipo_Movimiento", 8); // Tipo 8 = Movimiento entre activos
    detalleData.append("IdAutorizador", $("#IdAutorizador").val());
    detalleData.append("Observaciones", fila.find('.componente-observacion').val() || $("#txtObservaciones").val());

    // Guardar cada detalle
    $.ajax({
      url: "../../controllers/GestionarMovimientosComponentesController.php?action=MoverComponenteEntreActivos",
      type: "POST",
      data: detalleData,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (res) {
        detallesGuardados++;
        if (!res.status) {
          errores.push(
            `Error en componente ${fila.find("td:eq(0)").text()}: ${
              res.message
            }`
          );
        }

        // Cuando todos los detalles se hayan procesado
        if (detallesGuardados === totalDetalles) {
          Swal.close();
          if (errores.length === 0) {
            Swal.fire({
              title: "Éxito",
              text: "Movimiento registrado correctamente",
              icon: "success"
            }).then(() => {
              // Limpiar y recargar
              $("#divregistroMovimiento").hide();
              $("#divtblmovimientos").show();
              $("#divlistadomovimientos").show();
              listarMovimientos();
            });
          } else {
            NotificacionToast("warning", "El movimiento se registró pero hubo errores en algunos detalles");
            // Limpiar y recargar
            $("#divregistroMovimiento").hide();
            $("#divtblmovimientos").show();
            $("#divlistadomovimientos").show();
            listarMovimientos();
          }
        }
      },
      error: function () {
        detallesGuardados++;
        errores.push(
          `Error de comunicación con el servidor para el componente ${fila
            .find("td:eq(0)")
            .text()}`
        );

        if (detallesGuardados === totalDetalles) {
          Swal.close();
          NotificacionToast("error", "Hubo errores al guardar los detalles");
        }
      },
    });
  });
}

function ListarCombosMov() {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=combos",
    type: "POST",
    dataType: "json",
    async: false,
    success: (res) => {
      if (res.status) {
        // Cargar autorizador
        $("#CodAutorizador").html(res.data.autorizador).trigger("change");

        // Actualizar el campo de sucursal origen
        $("#IdSucursalOrigen").val(res.data.sucursalOrigen);
        $("#IdSucursalOrigenValor").val(res.data.sucursalOrigenId);

        // Cargar sucursales para el destino
        $("#IdSucursalDestino").html(res.data.sucursales);

        // Inicializar select2
        if (!$("#CodAutorizador").hasClass("select2-hidden-accessible")) {
          $("#CodAutorizador, #IdSucursalDestino").select2({
            theme: "bootstrap4",
            width: "100%",
          });
        }
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

function ListarCombosFiltros() {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status) {
        $("#filtroSucursal").html(res.data.sucursales).trigger("change");

        $("#filtroSucursal").select2({
          theme: "bootstrap4",
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
    lengthChange: true,
    colReorder: true,
    autoWidth: false,
    ajax: {
      url: "../../controllers/GestionarMovimientosComponentesController.php?action=ConsultarMovimientosEntreActivos",
      type: "POST",
      dataType: "json",
      data: function (d) {
        // Agregar los filtros del formulario
        d.filtroTipoMovimiento = $("#filtroTipoMovimiento").val();
        d.filtroSucursal = $("#filtroSucursal").val();
        d.filtroFecha = $("#filtroFecha").val();
      },
      dataSrc: function (json) {
        if (!json.status) {
          NotificacionToast("error", json.message || "Error al cargar los movimientos");
          return [];
        }
        return json.data || [];
      },
      error: function(xhr, status, error) {
        NotificacionToast("error", "Error al cargar los movimientos: " + error);
        return [];
      }
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
            </div>
          </div>`,
      },
      { data: "IdDetalleMovimiento", visible: false, searchable: false },
      { data: "IdComponente" },
      { data: "CodigoComponente", },
      { data: "NombreComponente" },
      { data: "TipoMovimiento" },
      { data: "ActivoPadreOrigen" },
      { data: "ActivoPadreDestino" },
      //{ data: "SucursalOrigen", visible: false, searchable: false },
      { data: "Sucursal" },
      //{ data: "AmbienteOrigen" },
      { data: "Ambiente" },
      //{ data: "Autorizador" },
      { data: "Responsable" },
      //{ data: "ResponsableDestino" },
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

// Agregar los manejadores de eventos para los botones de acción
$(document).on("click", ".btnVerDetalle", function () {
  const fila = $(this).closest("tr");
  const datos = $("#tblMovimientos").DataTable().row(fila).data();

  if (!datos) {
    Swal.fire(
      "Error",
      "No se pudo obtener la información del movimiento.",
      "error"
    );
    return;
  }

  Swal.fire({
    title: "Detalle del Movimiento",
    html: `
      <div class="text-left">
        <p><strong>ID Componente:</strong> ${datos.IdComponente}</p>
        <p><strong>Nombre Componente:</strong> ${datos.NombreComponente}</p>
        <p><strong>Activo Padre Origen:</strong> ${datos.ActivoPadreOrigen}</p>
        <p><strong>Activo Padre Destino:</strong> ${datos.ActivoPadreDestino}</p>
        <p><strong>Sucursal:</strong> ${datos.Sucursal}</p>
        <p><strong>Ambiente:</strong> ${datos.Ambiente}</p>
        <p><strong>Autorizador:</strong> ${datos.Autorizador}</p>
        <p><strong>Responsable:</strong> ${datos.Responsable}</p>
        <p><strong>Fecha:</strong> ${datos.FechaMovimiento}</p>
      </div>
    `,
    width: "600px",
  });
});

// $(document).on("click", ".btnAnularMovimiento", function () {
//   const fila = $(this).closest("tr");
//   const datos = $("#tblMovimientos").DataTable().row(fila).data();

//   if (!datos) {
//     Swal.fire(
//       "Error",
//       "No se pudo obtener la información del movimiento.",
//       "error"
//     );
//     return;
//   }

//   Swal.fire({
//     title: "¿Estás seguro?",
//     text: "¿Deseas anular este movimiento?",
//     icon: "warning",
//     showCancelButton: true,
//     confirmButtonColor: "#3085d6",
//     cancelButtonColor: "#d33",
//     confirmButtonText: "Sí, anular",
//     cancelButtonText: "Cancelar",
//   }).then((result) => {
//     if (result.isConfirmed) {
//       $.ajax({
//         url: "../../controllers/GestionarMovimientoController.php?action=anularMovimiento",
//         type: "POST",
//         data: { idMovimiento: datos.IdDetalleMovimiento },
//         dataType: "json",
//         success: function (res) {
//           if (res.status) {
//             Swal.fire({
//               title: "Éxito",
//               text: res.message,
//               icon: "success",
//             }).then(() => {
//               $("#tblMovimientos").DataTable().ajax.reload();
//             });
//           } else {
//             Swal.fire("Error", res.message, "error");
//           }
//         },
//         error: function (xhr, status, error) {
//           Swal.fire(
//             "Error",
//             "Error al procesar la solicitud: " + error,
//             "error"
//           );
//         },
//       });
//     }
//   });
// });

$(document).on("click", ".btnVerHistorial", function () {
  const fila = $(this).closest("tr");
  const datos = $("#tblMovimientos").DataTable().row(fila).data();

  if (!datos) {
    Swal.fire(
      "Error",
      "No se pudo obtener la información del movimiento.",
      "error"
    );
    return;
  }

  Swal.fire({
    title: "Historial de Movimientos",
    html: `
      <div class="text-left">
        <p><strong>ID Componente:</strong> ${datos.IdComponente}</p>
        <p><strong>Nombre Componente:</strong> ${datos.NombreComponente}</p>
        <p><strong>Activo Padre Origen:</strong> ${datos.ActivoPadreOrigen}</p>
        <p><strong>Activo Padre Destino:</strong> ${datos.ActivoPadreDestino}</p>
        <p><strong>Sucursal:</strong> ${datos.Sucursal}</p>
        <p><strong>Ambiente:</strong> ${datos.Ambiente}</p>
        <p><strong>Autorizador:</strong> ${datos.Autorizador}</p>
        <p><strong>Responsable:</strong> ${datos.Responsable}</p>
        <p><strong>Fecha:</strong> ${datos.FechaMovimiento}</p>
      </div>
    `,
    width: "600px",
  });
});

function listarComponentesModal(idActivoPadre) {
  if ($.fn.DataTable.isDataTable("#tblComponentes")) {
    $("#tblComponentes").DataTable().destroy();
  }

  $("#tblComponentes").DataTable({
    aProcessing: true,
    aServerSide: false,
    ajax: {
      url: "../../controllers/GestionarMovimientosComponentesController.php?action=listarComponentesActivo",
      type: "POST",
      data: { idActivoPadre: idActivoPadre },
      dataType: "json",
      dataSrc: function (json) {
        if (json && json.data) {
          return json.data;
        }
        return [];
      },
    },
    columns: [
      { data: "IdActivo" },
      { data: "CodigoActivo" },
      { data: "NombreArticulo" },
      { data: "MarcaArticulo" },
      { data: "NumeroSerie" },
      {
        data: null,
        render: function (data) {
          return `<button type="button" class="btn btn-success btn-sm btnSeleccionarComponente" data-id="${data.IdActivo}">
                    <i class="fa fa-check"></i>
                  </button>`;
        },
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
  });
}

// Agregar botón para agregar componentes seleccionados al detalle
$("#modalBuscarComponentes .modal-footer").prepend(`
  <button type="button" class="btn btn-primary" id="btnAgregarSeleccionados">
    <i class="fa fa-plus"></i> Agregar Seleccionados
  </button>
`);

// Agregar componentes seleccionados al detalle
$(document).on("click", "#btnAgregarSeleccionados", function() {
  const componentesSeleccionados = $("#tblComponentes tbody tr.selected");
  
  if (componentesSeleccionados.length === 0) {
    Swal.fire("Advertencia", "Debe seleccionar al menos un componente", "warning");
    return;
  }

  componentesSeleccionados.each(function() {
    const data = $("#tblComponentes").DataTable().row($(this)).data();
    
    // Verificar si el componente ya está en el detalle
    if ($(`#tbldetallecomponentes tbody tr[data-id="${data.IdActivo}"]`).length > 0) {
      return; // Saltar este componente si ya está en el detalle
    }

    // Agregar al detalle
    const nuevaFila = `
      <tr data-id="${data.IdActivo}">
        <td>${data.IdActivo}</td>
        <td>${data.CodigoActivo}</td>
        <td>${data.NombreArticulo}</td>
        <td>${data.MarcaArticulo}</td>
        <td>${data.NumeroSerie}</td>
        <td>
          <button type="button" class="btn btn-danger btn-sm btnEliminarComponente">
            <i class="fa fa-trash"></i>
          </button>
        </td>
      </tr>
    `;
    $("#tbldetallecomponentes tbody").append(nuevaFila);
  });

  // Cerrar el modal
  $("#modalBuscarComponentes").modal("hide");
});

// Eliminar componente del detalle
$(document).on("click", ".btnEliminarComponente", function() {
  $(this).closest("tr").remove();
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

// Función para listar activos en el modal de búsqueda
function listarActivosModalBusqueda() {
  if ($.fn.DataTable.isDataTable("#tblActivos")) {
    $("#tblActivos").DataTable().destroy();
  }
  $("#tblActivos").DataTable({
    ajax: {
      url: "../../controllers/GestionarActivosController.php?action=ConsultarActivosRelacionados",
      type: "POST",
      data: { IdArticulo: "", IdActivo: "" },
      dataType: "json",
      dataSrc: function (json) {
        return json && json.length ? json : (json.data || []);
      },
    },
    columns: [
      { data: "idActivo" },
      { data: "CodigoActivo" },
      { data: "NombreActivoVisible" },
      { data: "Marca" },
      { data: "NumeroSerie" },
      {
        data: null,
        render: function (data, type, row) {
          return `<button type="button" class="btn btn-success btn-sm btnAgregarActivoDetalle"><i class="fa fa-plus"></i> Agregar</button>`;
        },
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
    destroy: true,
    responsive: true,
    autoWidth: false,
  });
}

// Evento para agregar un activo seleccionado al detalle
$(document).on("click", ".btnAgregarActivoDetalle", function () {
  const data = $("#tblActivos").DataTable().row($(this).closest("tr")).data();
  if (!data) return;
  // Verificar si ya está en el detalle
  if ($(`#tbldetalleactivos tbody tr[data-id='${data.idActivo}']`).length > 0) {
    NotificacionToast("warning", "El activo ya está en el detalle.");
    return;
  }
  // Agregar al detalle
  $("#tbldetalleactivos tbody").append(`
    <tr data-id="${data.idActivo}">
      <td>${data.CodigoActivo}</td>
      <td>${data.NombreActivoVisible}</td>
      <td>${data.Marca || ""}</td>
      <td>${data.NumeroSerie || ""}</td>
      <td><input type="text" class="form-control form-control-sm observacion-componente" placeholder="Observaciones"></td>
      <td><button type="button" class="btn btn-danger btn-sm btnQuitarActivoDetalle"><i class="fa fa-trash"></i> Quitar</button></td>
    </tr>
  `);
  NotificacionToast("success", "Activo agregado al detalle.");
});

// Evento para quitar un activo del detalle
$(document).on("click", ".btnQuitarActivoDetalle", function () {
  $(this).closest("tr").remove();
});
