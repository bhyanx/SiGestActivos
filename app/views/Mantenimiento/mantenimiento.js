$(document).ready(function () {
  initMantenimiento();
});

function initMantenimiento() {
  // Inicializar la tabla una sola vez
  if (!$.fn.DataTable.isDataTable("#tblMovimientos")) {
    ListarMantenimientos();
  }

  ListarCombosMantenimiento();
  ListarCombosFiltrosMantenimiento();

  // Ocultar secciones al cargar
  $("#divgenerarmantenimiento").hide();
  $("#divregistroMantenimiento").hide();

  // Manejar el evento submit del formulario de búsqueda para evitar recarga
  $("#frmbusqueda")
    .off("submit")
    .on("submit", function (e) {
      e.preventDefault();
      $("#divtblmovimientos").show();
      $("#divgenerarmantenimiento").hide();
      $("#divregistroMantenimiento").hide();
      $("#divlistadomovimientos").show();

      // Recargar la tabla
      if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
        $("#tblMovimientos").DataTable().ajax.reload(null, false);
      } else {
        ListarMantenimientos();
      }
    });

  // Botón para abrir el panel de mantenimiento
  $("#btnmantenimiento, #btnnuevomantenimiento")
    .off("click")
    .on("click", function () {
      $("#divgenerarmantenimiento").show();
      $("#divregistroMantenimiento").hide();
      $("#divtblmovimientos").hide();
      $("#divlistadomovimientos").hide();

      // Cargar combos de mantenimiento cuando se abra el formulario
      ListarCombosMantenimiento();
    });

  // Contador de caracteres para observaciones de mantenimiento
  $(document).on("input", "#ObservacionesMantenimiento", function () {
    const maxLength = 500;
    const currentLength = $(this).val().length;
    $("#contador-caracteres-mant").text(currentLength);

    // Cambiar color según proximidad al límite
    if (currentLength > maxLength * 0.9) {
      $("#contador-caracteres-mant")
        .removeClass("text-muted text-warning")
        .addClass("text-danger");
    } else if (currentLength > maxLength * 0.7) {
      $("#contador-caracteres-mant")
        .removeClass("text-muted text-danger")
        .addClass("text-warning");
    } else {
      $("#contador-caracteres-mant")
        .removeClass("text-warning text-danger")
        .addClass("text-muted");
    }
  });

  // Eventos para mantenimiento
  $(document).on(
    "click",
    "#btnBuscarActivoMant, .btnagregaractivomant",
    function () {
      console.log("Abriendo modal para mantenimiento");
      console.log(
        "Modal mantenimiento existe:",
        $("#ModalArticulosMantenimiento").length
      );

      // Limpiar cualquier backdrop previo
      $(".modal-backdrop").remove();

      // Configurar z-index antes de abrir
      $("#ModalArticulosMantenimiento").css("z-index", "10000");

      $("#ModalArticulosMantenimiento").modal({
        backdrop: "static",
        keyboard: false,
        show: true,
      });

      listarActivosModalMantenimiento();
    }
  );

  // Eventos específicos del modal de mantenimiento
  $("#ModalArticulosMantenimiento").on("show.bs.modal", function () {
    console.log("Modal de mantenimiento se está abriendo");
    $(this).css("z-index", "10000");
    $(".modal-backdrop").css("z-index", "9999");
  });

  $("#ModalArticulosMantenimiento").on("shown.bs.modal", function () {
    console.log("Modal de mantenimiento se abrió completamente");
    // Asegurar que el modal esté por encima
    $(this).css("z-index", "10000");
    $(".modal-backdrop").css("z-index", "9999");
  });

  $("#ModalArticulosMantenimiento").on("hide.bs.modal", function () {
    console.log("Modal de mantenimiento se está cerrando");
  });

  // Botón procesar mantenimiento
  $("#btnprocesarmantenimiento")
    .off("click")
    .on("click", function () {
      // Validar campos obligatorios
      const tipoMantenimiento = $("#IdTipoMantenimiento").val();
      const estadoMantenimiento = $("#IdEstadoMantenimiento").val();
      const descripcion = $("#DescripcionMantenimiento").val().trim();
      const fechaProgramada = $("#FechaProgramada").val();
      const observaciones = $("#ObservacionesMantenimiento").val().trim();

      if (!tipoMantenimiento) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar un tipo de mantenimiento",
          icon: "warning",
        });
        $("#IdTipoMantenimiento").focus();
        return;
      }

      if (!estadoMantenimiento) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar un estado de mantenimiento",
          icon: "warning",
        });
        $("#IdEstadoMantenimiento").focus();
        return;
      }

      if (!descripcion) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe ingresar una descripción del mantenimiento",
          icon: "warning",
        });
        $("#DescripcionMantenimiento").focus();
        return;
      }

      if (descripcion.length < 10) {
        Swal.fire({
          title: "Descripción muy corta",
          text: "La descripción debe tener al menos 10 caracteres",
          icon: "warning",
        });
        $("#DescripcionMantenimiento").focus();
        return;
      }

      if (!fechaProgramada) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar una fecha programada para el mantenimiento",
          icon: "warning",
        });
        $("#FechaProgramada").focus();
        return;
      }

      if (!observaciones) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe ingresar observaciones para el mantenimiento",
          icon: "warning",
        });
        $("#ObservacionesMantenimiento").focus();
        return;
      }

      // Validar que la fecha no sea anterior a hoy
      const fechaSeleccionada = new Date(fechaProgramada);
      const hoy = new Date();
      hoy.setHours(0, 0, 0, 0);

      if (fechaSeleccionada < hoy) {
        Swal.fire({
          title: "Fecha inválida",
          text: "La fecha programada no puede ser anterior a la fecha actual",
          icon: "warning",
        });
        $("#FechaProgramada").focus();
        return;
      }

      // Validar costo estimado (requerido)
      const costoEstimado = $("#CostoEstimado").val().trim();
      if (!costoEstimado) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe ingresar un costo estimado para el mantenimiento",
          icon: "warning",
        });
        $("#CostoEstimado").focus();
        return;
      }

      const costoNum = parseFloat(costoEstimado);
      if (isNaN(costoNum) || costoNum <= 0) {
        Swal.fire({
          title: "Costo inválido",
          text: "El costo estimado debe ser un número mayor a cero",
          icon: "warning",
        });
        $("#CostoEstimado").focus();
        return;
      }

      if (costoNum > 999999.99) {
        Swal.fire({
          title: "Costo muy alto",
          text: "El costo estimado no puede exceder S/. 999,999.99",
          icon: "warning",
        });
        $("#CostoEstimado").focus();
        return;
      }

      // Validar proveedor (requerido)
      const proveedorSeleccionado = $("#IdProveedor").val();
      if (!proveedorSeleccionado || proveedorSeleccionado.trim() === "") {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar un proveedor para el mantenimiento",
          icon: "warning",
        });
        $("#IdProveedor").focus();
        return;
      }

      // Si todo está correcto, proceder con el procesamiento
      $("#divregistroMantenimiento").show();
      $("#divgenerarmantenimiento").hide();

      // Transferir valores de los combos a campos ocultos
      $("#IdTipoMantenimientoHidden").val($("#IdTipoMantenimiento").val());
      $("#IdEstadoMantenimientoHidden").val($("#IdEstadoMantenimiento").val());
      $("#IdProveedorHidden").val($("#IdProveedor").val());
      $("#IdResponsableHidden").val($("#IdResponsableMantenimiento").val());

      // Llenar campos informativos
      $("#tipoMantenimientoInfo").val(
        $("#IdTipoMantenimiento option:selected").text()
      );
      $("#estadoMantenimientoInfo").val(
        $("#IdEstadoMantenimiento option:selected").text()
      );
      $("#fechaProgramadaInfo").val(
        $("#FechaProgramada").val() || "No programada"
      );
      $("#proveedorInfo").val(
        $("#IdProveedor option:selected").text() || "No asignado"
      );
      $("#responsableInfo").val(
        $("#IdResponsableMantenimiento option:selected").text() || "No asignado"
      );
      $("#costoEstimadoInfo").val(
        $("#CostoEstimado").val()
          ? "S/. " + $("#CostoEstimado").val()
          : "No estimado"
      );
      $("#descripcionInfo").val(
        $("#DescripcionMantenimiento").val() || "Sin descripción"
      );
      $("#observacionesInfo").val(
        $("#ObservacionesMantenimiento").val() || "Sin observaciones"
      );

      // Limpiar tabla de activos
      $("#tblactivosmantenimiento tbody").empty();
      $("#TotalActivosMantenimiento").text("0");
    });

  // Botón cancelar mantenimiento
  $("#btncancelarmantenimiento")
    .off("click")
    .on("click", function () {
      // Limpiar modales antes de cambiar de sección
      limpiarModalesProblematicos();

      $("#divgenerarmantenimiento").hide();
      $("#divtblmovimientos").show();
      $("#divlistadomovimientos").show();
    });

  // Botón salir mantenimiento
  $("#btnsalirmantenimiento")
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
          // Limpiar modales antes de cambiar de sección
          limpiarModalesProblematicos();

          $("#divregistroMantenimiento").hide();
          $("#divgenerarmantenimiento").hide();
          $("#divtblmovimientos").show();
          $("#divlistadomovimientos").show();
        }
      });
    });

  // Botón cambiar datos mantenimiento
  $("#btnchangedatamantenimiento")
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
          $("#divregistroMantenimiento").hide();
          $("#divgenerarmantenimiento").show();
        }
      });
    });

  // Evento específico para seleccionar activos desde el modal de mantenimiento
  $(document).on("click", ".btnSeleccionarActivoMantenimiento", function () {
    var fila = $(this).closest("tr");
    var activo = {
      id: $(this).data("id"),
      codigo: fila.find("td:eq(1)").text(),
      nombre: fila.find("td:eq(2)").text(),
      Sucursal: fila.find("td:eq(3)").text(),
      Ambiente: fila.find("td:eq(4)").text(),
    };

    // Verificar si el activo tiene hijos antes de agregar
    $.ajax({
      url: "../../controllers/MantenimientosController.php?action=ActivoTieneHijos",
      type: "POST",
      dataType: "json",
      data: { idActivo: activo.id },
      success: function (res) {
        if (res.status && res.data && res.data.tieneHijos) {
          // Preguntar si desea incluir los hijos
          Swal.fire({
            title: "Activo con componentes",
            html:
              `<p>El activo <strong>${activo.nombre}</strong> tiene activos hijos asociados.</p>` +
              `<p>¿Desea enviar a mantenimiento solo el activo padre o incluir también los hijos?</p>`,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Padre + Hijos",
            cancelButtonText: "Solo Padre",
            reverseButtons: true,
          }).then((result) => {
            if (result.isConfirmed) {
              // cargar hijos y agregarlos como filas regulares
              $.ajax({
                url: "../../controllers/MantenimientosController.php?action=ListarHijosActivo",
                type: "POST",
                dataType: "json",
                data: { idActivo: activo.id },
                success: function (rsHijos) {
                  agregarActivoAMantenimiento(activo);
                  if (rsHijos.status && Array.isArray(rsHijos.data)) {
                    rsHijos.data.forEach(function (hijo) {
                      var hijoActivo = {
                        id: hijo.IdActivo,
                        codigo: hijo.codigo,
                        nombre: hijo.NombreActivo,
                        Sucursal: hijo.Sucursal,
                        Ambiente: hijo.Ambiente,
                        incluirHijos: 0
                      };
                      agregarActivoAMantenimiento(hijoActivo);
                    });
                  }
                },
                error: function () {
                  agregarActivoAMantenimiento(activo);
                },
              });
            } else {
              agregarActivoAMantenimiento(activo);
            }
          });
        } else {
          agregarActivoAMantenimiento(activo);
        }
      },
      error: function () {
        // En caso de error, continuar como solo padre
        agregarActivoAMantenimiento(activo);
      },
    });
  });

  // Función para guardar el mantenimiento completo usando el enfoque cabecera-detalle
  $("#btnGuardarMantenimiento").on("click", function () {
    // Verificar si hay activos en la tabla
    if ($("#tblactivosmantenimiento tbody tr").length === 0) {
      Swal.fire(
        "Error",
        "Debe agregar al menos un activo al mantenimiento",
        "error"
      );
      return;
    }

    // Mostrar loading
    Swal.fire({
      title: "Procesando Mantenimiento",
      html: `
        <div class="text-center">
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="sr-only">Creando mantenimiento...</span>
          </div>
          <p>Paso 1: Creando cabecera del mantenimiento...</p>
        </div>
      `,
      allowOutsideClick: false,
      showConfirmButton: false,
    });

    // PASO 1: Crear la cabecera del mantenimiento
    const formDataMantenimiento = new FormData();
    formDataMantenimiento.append(
      "fechaProgramada",
      $("#FechaProgramada").val() || null
    );
    formDataMantenimiento.append(
      "idTipoMantenimiento",
      $("#IdTipoMantenimiento").val() || null
    );
    formDataMantenimiento.append(
      "descripcion",
      $("#DescripcionMantenimiento").val() || null
    );
    formDataMantenimiento.append(
      "observaciones",
      $("#ObservacionesMantenimiento").val() || null
    );
    formDataMantenimiento.append(
      "costoEstimado",
      $("#CostoEstimado").val() || null
    );
    formDataMantenimiento.append(
      "idProveedor",
      $("#IdProveedorHidden").val() || null
    );
    formDataMantenimiento.append(
      "idResponsable",
      $("#IdResponsableHidden").val() || null
    );
    formDataMantenimiento.append(
      "estadoMantenimiento",
      $("#IdEstadoMantenimientoHidden").val()
    );

    $.ajax({
      url: "../../controllers/MantenimientosController.php?action=RegistrarMantenimiento",
      type: "POST",
      data: formDataMantenimiento,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (resMantenimiento) {
        if (resMantenimiento.status) {
          // PASO 2: Agregar todos los activos al mantenimiento creado
          Swal.update({
            html: `
              <div class="alert alert-success mb-3">
                <h5><i class="fas fa-check-circle"></i> Mantenimiento Creado</h5>
                <h4 class="text-primary"><strong>${
                  resMantenimiento.codigoMantenimiento
                }</strong></h4>
              </div>
              <div class="progress mb-3">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                     style="width: 0%" id="progressBar">0/${
                       $("#tblactivosmantenimiento tbody tr").length
                     }</div>
              </div>
              <div id="progressText">Paso 2: Agregando activos al mantenimiento...</div>
            `,
          });

          agregarActivosAlMantenimiento(
            resMantenimiento.idMantenimiento,
            resMantenimiento.codigoMantenimiento
          );
        } else {
          Swal.fire({
            title: "Error al Crear Mantenimiento",
            html: `
              <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> ${resMantenimiento.message}
              </div>
            `,
            icon: "error",
            confirmButtonText: "Intentar de nuevo",
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la petición:", error);
        Swal.fire({
          title: "Error de Comunicación",
          html: `
            <div class="alert alert-danger">
              <i class="fas fa-wifi"></i> No se pudo comunicar con el servidor.<br>
              <small>Error: ${error}</small>
            </div>
          `,
          icon: "error",
          confirmButtonText: "Reintentar",
        });
      },
    });
  });
}

// ==================== FUNCIONES DE MANTENIMIENTO ====================
function verHistorialEstados(idMantenimiento) {
  $.ajax({
    url: "../../controllers/MantenimientosController.php?action=obtenerHistorialEstadoMantenimiento",
    type: "POST",
    data: { idMantenimiento: idMantenimiento },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        let historialHtml = `
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="table-warning">
                <tr>
                  <th>Estado Nuevo</th>
                  <th>Fecha Cambio</th>
                  <th>Usuario</th>
                </tr>
              </thead>
              <tbody>`;

        res.data.forEach(function (item) {
          //const estadoAnterior = item.EstadoAnterior;
          const estadoNuevo = item.estadoNuevo;
          const fecha = moment(item.fechaCambio).format("DD/MM/YYYY HH:mm:ss");
          const usuario = item.nombreUsuario || item.userMod;

          historialHtml += `
            <tr>
              <td><strong>${estadoNuevo}</strong></td>
              <td>${fecha}</td>
              <td>${usuario}</td>
            </tr>`;
        });

        historialHtml += `
              </tbody>
            </table>
          </div>`;

        Swal.fire({
          title: "Historial de Estados del Mantenimiento",
          html: historialHtml,
          width: "80%",
          showCloseButton: true,
          showConfirmButton: false,
        });
      } else {
        NotificacionToast(
          "error",
          res.message || "Error al cargar el historial"
        );
      }
    },
    error: function () {
      NotificacionToast("error", "Error al comunicarse con el servidor");
    },
  });
}

function verHistorialEstados2(idMantenimiento) {
  // Destruir la instancia existente si existe
  if ($.fn.DataTable.isDataTable("#tbllistarEstadosMantenimiento")) {
    $("#tbllistarEstadosMantenimiento").DataTable().destroy();
  }

  // Limpiar cualquier backdrop previo
  // $(".modal-backdrop").remove();

  // Configurar z-index antes de abrir
  $("#ModalEstadosMantenimiento").css("z-index", "99999");

  // Abrir el modal
  $("#ModalEstadosMantenimiento").modal("show");

  // Inicializar la DataTable después de que el modal se abra
  $("#ModalEstadosMantenimiento").on("shown.bs.modal", function () {
    $("#tbllistarEstadosMantenimiento").DataTable({
      dom: "Bfrtip",
      responsive: false,
      ajax: {
        url: "../../controllers/MantenimientosController.php?action=obtenerHistorialEstadoMantenimiento",
        type: "POST",
        data: { idMantenimiento: idMantenimiento },
        dataType: "json",
        dataSrc: function (json) {
          if (!json.status) {
            NotificacionToast("error", json.message);
            return [];
          }
          return json.data || [];
        },
        error: function (xhr, status, error) {
          NotificacionToast("error", "Error al cargar el historial: " + error);
          return [];
        },
      },
      columns: [
        {
          data: "estadoNuevo",
          title: "Estado",
        },
        {
          data: "fechaCambio",
          title: "Fecha Cambio",
          render: function (data) {
            if (!data) return "N/A";
            try {
              return moment(data).format("DD/MM/YYYY HH:mm:ss");
            } catch (e) {
              return data;
            }
          },
        },
        {
          data: "nombreUsuario",
          title: "Usuario",
          render: function (data, type, row) {
            return data || row.userMod || "N/A";
          },
        },
      ],
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
      },
      order: [[1, "asc"]], // Ordenar por fecha ascendente (más antiguo primero)
      pageLength: 10,
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "Todos"],
      ],
    });
  });

  // Limpiar el evento cuando se cierre el modal
  $("#ModalEstadosMantenimiento").on("hidden.bs.modal", function () {
    $(this).off("shown.bs.modal");
    if ($.fn.DataTable.isDataTable("#tbllistarEstadosMantenimiento")) {
      $("#tbllistarEstadosMantenimiento").DataTable().destroy();
    }
  });
}

function ListarCombosMantenimiento() {
  $.ajax({
    url: "../../controllers/MantenimientosController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status) {
        // Cargar tipos de mantenimiento
        $("#IdTipoMantenimiento")
          .html(res.data.tiposMantenimiento)
          .trigger("change");

        // Cargar estados de mantenimiento y dejar por defecto 'Pendiente' (id = 1)
        $("#IdEstadoMantenimiento")
          .html(res.data.estadosMantenimiento)
          .val("1")
          .trigger("change")
          .prop("disabled", true);

        // Cargar responsables de mantenimiento
        $("#IdResponsableMantenimiento")
          .html(res.data.responsables)
          .trigger("change");

        // Inicializar select2 para los combos básicos
        $(
          "#IdTipoMantenimiento, #IdEstadoMantenimiento, #IdResponsableMantenimiento"
        ).select2({
          theme: "bootstrap4",
          width: "100%",
        });

        // Configurar el combo de proveedores con AJAX
        $("#IdProveedor").select2({
          minimumInputLength: 2,
          theme: "bootstrap4",
          width: "100%",
          language: {
            inputTooShort: function (args) {
              return "Ingresar mas de 2 caracteres.";
            },
            noResults: function () {
              return "Datos no encontrados.";
            },
            searching: function () {
              return "Buscando...";
            },
          },
          ajax: {
            url: "../../controllers/GestionarActivosController.php?action=comboProveedor",
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (params) {
              return {
                filtro: params.term, // término de búsqueda
              };
            },
            processResults: function (data) {
              // data ya debe ser un array de objetos {id, text}
              return {
                results: data || [],
              };
            },
            cache: true,
          },
          placeholder: "Ingresar/Seleccionar Proveedor",
          allowClear: true,
        });
      } else {
        Swal.fire(
          "Mantenimiento de activos",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      Swal.fire(
        "Mantenimiento de activos",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

function listarActivosModalMantenimiento() {
  // Destruir la instancia existente si existe
  if ($.fn.DataTable.isDataTable("#tbllistarActivosMantenimiento")) {
    $("#tbllistarActivosMantenimiento").DataTable().destroy();
  }

  $("#tbllistarActivosMantenimiento").DataTable({
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
      error: function (xhr, status, error) {
        NotificacionToast("error", "Error al cargar los activos: " + error);
        return [];
      },
    },
    columns: [
      { data: "IdActivo" },
      { data: "codigo" },
      { data: "NombreActivo" },
      { data: "Sucursal" },
      { data: "Ambiente" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            '<button class="btn btn-success btn-sm btnSeleccionarActivoMantenimiento" data-id="' +
            row.IdActivo +
            '"><i class="fa fa-check"></i></button>'
          );
        },
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
    order: [[2, "asc"]], // Ordenar por NombreArticulo
    pageLength: 10,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
  });
}

function agregarActivoAMantenimiento(activo) {
  if (
    $(`#tblactivosmantenimiento tbody tr[data-id='${activo.id}']`).length > 0
  ) {
    NotificacionToast(
      "error",
      `El activo <b>${activo.nombre}</b> ya está en el mantenimiento.`
    );
    return false;
  }

  // Validar que el activo tenga todos los datos necesarios
  if (!activo.id || !activo.nombre || !activo.Ambiente || !activo.Sucursal) {
    NotificacionToast("error", "El activo no tiene todos los datos necesarios");
    return false;
  }

  var estadoTexto = 'Cargando...';
  var nuevaFila = `<tr data-id='${activo.id}' class='table-light border-left border-info border-3 agregado-temp'>
    <td class="text-center">${activo.id}</td>
    <td><strong>${activo.codigo}</strong></td>
    <td>${activo.nombre}</td>
    <td>${activo.Sucursal}</td>
    <td>${activo.Ambiente}</td>
    <td><span class='estado-actual'>${estadoTexto}</span></td>
    <td class="text-center">
      <button type='button' class='btn btn-danger btn-sm btnQuitarActivoMant' title='Quitar activo'>
        <i class='fa fa-trash'></i>
      </button>
    </td>
  </tr>`;

  $("#tblactivosmantenimiento tbody").append(nuevaFila);

  // Cargar estado actual del activo y pintar
  $.ajax({
    url: "../../controllers/MantenimientosController.php?action=ObtenerEstadoActivo",
    type: "POST",
    dataType: "json",
    data: { idActivo: activo.id },
    success: function (r) {
      if (r.status && r.data && r.data.EstadoActual) {
        $("#tblactivosmantenimiento tbody tr[data-id='" + activo.id + "'] .estado-actual").text(r.data.EstadoActual);
      } else {
        $("#tblactivosmantenimiento tbody tr[data-id='" + activo.id + "'] .estado-actual").text('N/D');
      }
    },
    error: function () {
      $("#tblactivosmantenimiento tbody tr[data-id='" + activo.id + "'] .estado-actual").text('N/D');
    }
  });

  // Animación de entrada
  setTimeout(function () {
    $("#tblactivosmantenimiento tbody tr.agregado-temp")
      .removeClass("table-light agregado-temp")
      .addClass("table-active");

    setTimeout(function () {
      $("#tblactivosmantenimiento tbody tr.table-active").removeClass(
        "table-active"
      );
    }, 1000);
  }, 100);

  NotificacionToast(
    "success",
    `Activo <b>${activo.nombre}</b> agregado al mantenimiento.`
  );

  // Cerrar modal automáticamente
  $("#ModalArticulosMantenimiento").modal("hide");

  // Limpiar backdrop si queda residual
  setTimeout(() => {
    if ($(".modal-backdrop").length > 0) {
      $(".modal-backdrop").remove();
      $("body").removeClass("modal-open");
    }
  }, 300);

  // Actualizar contador
  actualizarContadorActivosMantenimiento();

  return true;
}


function actualizarContadorActivosMantenimiento() {
  const totalActivos = $("#tblactivosmantenimiento tbody tr").length;
  $("#TotalActivosMantenimiento").text(totalActivos);

  // Actualizar botón de guardar mantenimiento
  const $btnGuardar = $("#btnGuardarMantenimiento");

  if (totalActivos === 0) {
    $btnGuardar
      .prop("disabled", true)
      .removeClass("btn-success")
      .addClass("btn-secondary")
      .html('<i class="fa fa-save"></i> Guardar Mantenimiento');
  } else {
    $btnGuardar
      .prop("disabled", false)
      .removeClass("btn-secondary")
      .addClass("btn-success")
      .html(
        `<i class="fa fa-save"></i> Registrar Mantenimiento (${totalActivos} activo${
          totalActivos > 1 ? "s" : ""
        })`
      );
  }
}

// Evento para quitar activo del mantenimiento
$(document).on("click", ".btnQuitarActivoMant", function () {
  const $fila = $(this).closest("tr");
  const nombreActivo = $fila.find("td:eq(2)").text();

  Swal.fire({
    title: "¿Quitar activo?",
    text: `¿Está seguro de quitar "${nombreActivo}" del mantenimiento?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, quitar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $fila.fadeOut(300, function () {
        $(this).remove();
        actualizarContadorActivosMantenimiento();
        NotificacionToast(
          "info",
          `Activo <b>${nombreActivo}</b> removido del mantenimiento.`
        );
      });
    }
  });
});

// Función para limpiar y recargar después del mantenimiento
function limpiarYRecargarMantenimiento() {
  // Limpiar modales problemáticos primero
  limpiarModalesProblematicos();

  $("#divregistroMantenimiento").hide();
  $("#divgenerarmantenimiento").hide();
  $("#divtblmovimientos").show();
  $("#divlistadomovimientos").show();
  $("#tblactivosmantenimiento tbody").empty();

  // Limpiar observaciones y resetear contador
  $("#ObservacionesMantenimiento").val("");
  $("#contador-caracteres-mant")
    .text("0")
    .removeClass("text-warning text-danger")
    .addClass("text-muted");

  // Limpiar formulario
  $("#IdTipoMantenimiento").val("").trigger("change");
  $("#IdEstadoMantenimiento").val("1").trigger("change").prop("disabled", true);
  $("#IdProveedor").val("").trigger("change");
  $("#IdResponsableMantenimiento").val("").trigger("change");
  $("#FechaMod").val("");
  $("#CostoEstimado").val("");
  $("#DescripcionMantenimiento").val("");

  // Actualizar contador
  actualizarContadorActivosMantenimiento();
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

// Función para limpiar modales problemáticos
function limpiarModalesProblematicos() {
  // Cerrar cualquier modal abierto
  $(".modal").modal("hide");

  // Remover backdrops residuales
  $(".modal-backdrop").remove();

  // Remover clase modal-open del body
  $("body").removeClass("modal-open");

  // Restaurar scroll del body
  $("body").css("overflow", "");
  $("body").css("padding-right", "");
}
//Función para agregar todos los activos al mantenimiento creado
function agregarActivosAlMantenimiento(idMantenimiento, codigoMantenimiento) {
  let activosProcesados = 0;
  let totalActivos = $("#tblactivosmantenimiento tbody tr").length;
  let errores = [];
  let activosExitosos = [];

  $("#tblactivosmantenimiento tbody tr").each(function (index) {
    const fila = $(this);
    const nombreActivo = fila.find("td:eq(2)").text();
    const detalleData = new FormData();

    detalleData.append("idMantenimiento", idMantenimiento);
    detalleData.append("idActivo", fila.find("td:eq(0)").text());
    detalleData.append(
      "observaciones",
      $("#ObservacionesMantenimiento").val() || null
    );
    // Siempre enviar incluirHijos=0 para procesar solo el activo seleccionado
    detalleData.append("incluirHijos", 0);

    $.ajax({
      url: "../../controllers/MantenimientosController.php?action=AgregarDetalle",
      type: "POST",
      data: detalleData,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (res) {
        activosProcesados++;

        // Actualizar progreso
        const porcentaje = (activosProcesados / totalActivos) * 100;
        $("#progressBar")
          .css("width", porcentaje + "%")
          .text(`${activosProcesados}/${totalActivos}`);
        $("#progressText").text(
          `Procesando: ${nombreActivo} - ${res.status ? "Éxito" : "Error"}`
        );

        if (res.status) {
          activosExitosos.push(nombreActivo);
        } else {
          errores.push(`${nombreActivo}: ${res.message}`);
        }

        // Cuando todos los activos se hayan procesado
        if (activosProcesados === totalActivos) {
          setTimeout(() => {
            if (errores.length === 0) {
              Swal.fire({
                title: "¡Mantenimiento Completado Exitosamente!",
                html: `
                  <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle text-success"></i> Código de Mantenimiento</h5>
                    <h3 class="text-primary"><strong>${codigoMantenimiento}</strong></h3>
                    <p class="mb-0"><strong>${
                      activosExitosos.length
                    } activos</strong> procesados correctamente</p>
                  </div>

                  <div class="row mt-4">
                    <div class="col-md-6">
                      <div class="card border-info">
                        <div class="card-header bg-info text-white">
                          <i class="fas fa-info-circle"></i> Información del Mantenimiento
                        </div>
                        <div class="card-body">
                          <p><strong>Código:</strong> ${codigoMantenimiento}</p>
                          <p><strong>Total de activos:</strong> ${
                            activosExitosos.length
                          }</p>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="card border-success">
                        <div class="card-header bg-success text-white">
                          <i class="fas fa-tools"></i> Activos en Mantenimiento
                        </div>
                        <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                          ${activosExitosos
                            .map(
                              (nombre) =>
                                `<div class="d-flex justify-content-between align-items-center border-bottom py-1">
                              <span><i class="fas fa-wrench text-success"></i> ${nombre}</span>
                              <span class="badge badge-success">✓</span>
                            </div>`
                            )
                            .join("")}
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="mt-3 p-3 bg-light rounded">
                    <small class="text-muted">
                      <i class="fas fa-search"></i> Para consultar este mantenimiento, busque por el código: <strong>${codigoMantenimiento}</strong>
                    </small>
                  </div>
                `,
                icon: "success",
                width: "900px",
                confirmButtonText: "Continuar",
                confirmButtonColor: "#28a745",
              }).then(() => {
                limpiarYRecargarMantenimiento();
              });
            } else {
              let mensajeExito =
                activosExitosos.length > 0
                  ? `<div class="alert alert-success mb-3">
                  <h5><i class="fas fa-check-circle"></i> Mantenimiento: <strong>${codigoMantenimiento}</strong></h5>
                  <strong>Exitosos (${activosExitosos.length}):</strong><br>
                  ${activosExitosos
                    .map(
                      (activo) =>
                        `• <i class="fas fa-wrench text-success"></i> ${activo}`
                    )
                    .join("<br>")}
                </div>`
                  : "";

              Swal.fire({
                title: "Mantenimiento Completado con Advertencias",
                html: `
                  ${mensajeExito}
                  <div class="alert alert-warning">
                    <strong>Errores (${errores.length}):</strong><br>
                    ${errores
                      .map(
                        (error) =>
                          `• <i class="fas fa-exclamation-triangle text-warning"></i> ${error}`
                      )
                      .join("<br>")}
                  </div>
                  <div class="mt-3 text-muted">
                    <small><i class="fas fa-info-circle"></i> Los activos exitosos están bajo el código: <strong>${codigoMantenimiento}</strong></small>
                  </div>
                `,
                icon: "warning",
                width: "700px",
              }).then(() => {
                limpiarYRecargarMantenimiento();
              });
            }
          }, 500);
        }
      },
      error: function (xhr, status, error) {
        activosProcesados++;

        // Actualizar progreso
        const porcentaje = (activosProcesados / totalActivos) * 100;
        $("#progressBar")
          .css("width", porcentaje + "%")
          .text(`${activosProcesados}/${totalActivos}`);
        $("#progressText").text(`Error en: ${nombreActivo}`);

        errores.push(`${nombreActivo}: Error de comunicación - ${error}`);

        if (activosProcesados === totalActivos) {
          setTimeout(() => {
            Swal.fire({
              title: "Error en el Proceso",
              html: `
                <div class="alert alert-danger">
                  <h5>Mantenimiento: <strong>${codigoMantenimiento}</strong></h5>
                  <strong>Errores encontrados:</strong><br>
                  ${errores
                    .map(
                      (error) =>
                        `• <i class="fas fa-times text-danger"></i> ${error}`
                    )
                    .join("<br>")}
                </div>
              `,
              icon: "error",
              width: "600px",
            });
          }, 500);
        }
      },
    });
  });
}

// Función para listar mantenimientos
function ListarMantenimientos() {
  console.log("Iniciando ListarMantenimientos...");

  if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
    $("#tblMovimientos").DataTable().destroy();
  }

  $("#tblMovimientos").DataTable({
    processing: true,
    serverSide: false,
    destroy: true,
    info: true,
    pageLength: 10,
    order: [[1, "desc"]], // Ordenar por segunda columna descendente
    ajax: {
      url: "../../controllers/MantenimientosController.php?action=Consultar",
      type: "POST",
      data: function (d) {
          d.fechaInicio = $("#filtroFechaInicio").val();
          d.fechaFin = $("#filtroFechaFin").val();
      },
      dataSrc: "data",
      error: function (xhr, status, error) {
        console.error("=== ERROR AJAX ===");
        console.error("Status:", status);
        console.error("Error:", error);
        console.error("Response Text:", xhr.responseText);
        console.error("Status Code:", xhr.status);

        NotificacionToast(
          "error",
          "Error al cargar los mantenimientos: " + error
        );
        return [];
      },
    },
    columns: [
      {
        data: null,
        render: function (data, type, row) {
          // Determinar el estado del mantenimiento para mostrar botones apropiados
          const estadoId = row.estadoMantenimiento || row.estadoMant;
          const estado = row.estadoMant ? row.estadoMant.toLowerCase() : "";
          const esFinalizado =
            estado.includes("finalizado") ||
            estado.includes("completado") ||
            estado.includes("terminado");
          const esCancelado =
            estado.includes("cancelado") || estado.includes("anulado");

          // Botones base que siempre se muestran
          let dropdownItems = `
            <a class="dropdown-item" href="#" onclick="verDetallesMantenimiento(${row.idMantenimiento})">
              <i class="fas fa-list"></i> Ver Detalles
            </a>
            <a class="dropdown-item" href="#" onclick="imprimirReporteMantenimiento(${row.idMantenimiento})">
              <i class="fas fa-print"></i> Imprimir Reporte
            </a>
            <a class="dropdown-item" href="#" onclick="verHistorialEstados2(${row.idMantenimiento})">
              <i class="fas fa-history"></i> Ver Historial
            </a>
            `;

          // Agregar botones según el estado del mantenimiento
          if (!esFinalizado && !esCancelado) {
            dropdownItems += `<div class="dropdown-divider"></div>`;

            // Si está pendiente (estado = 1), mostrar solo aprobar y cancelar
            if (estadoId == 1) {
              dropdownItems += `
                <a class="dropdown-item" href="#" onclick="aprobarMantenimiento(${row.idMantenimiento})">
                  <i class="fas fa-check text-success"></i> Aprobar Mantenimiento
                </a>
                <a class="dropdown-item" href="#" onclick="cancelarMantenimiento(${row.idMantenimiento})">
                  <i class="fas fa-times-circle text-danger"></i> Cancelar Mantenimiento
                </a>`;
            }
            // Si está aprobado/en proceso (estado = 2), mostrar finalizar y cancelar
            else if (estadoId == 2) {
              dropdownItems += `
                <a class="dropdown-item" href="#" onclick="finalizarMantenimiento(${row.idMantenimiento})">
                  <i class="fas fa-check-circle text-success"></i> Finalizar Mantenimiento
                </a>
                <a class="dropdown-item" href="#" onclick="cancelarMantenimiento(${row.idMantenimiento})">
                  <i class="fas fa-times-circle text-danger"></i> Cancelar Mantenimiento
                </a>`;
            }
            // Para otros estados activos, mostrar finalizar y cancelar por defecto
            else {
              dropdownItems += `
                <a class="dropdown-item" href="#" onclick="finalizarMantenimiento(${row.idMantenimiento})">
                  <i class="fas fa-check-circle text-success"></i> Finalizar Mantenimiento
                </a>
                <a class="dropdown-item" href="#" onclick="cancelarMantenimiento(${row.idMantenimiento})">
                  <i class="fas fa-times-circle text-danger"></i> Cancelar Mantenimiento
                </a>`;
            }
          } else {
            // Mostrar estado actual si está finalizado o cancelado
            const estadoIcon = esFinalizado
              ? "fas fa-check-circle text-success"
              : "fas fa-times-circle text-danger";
            const estadoTexto = esFinalizado ? "Finalizado" : "Cancelado";
            dropdownItems += `
              <div class="dropdown-divider"></div>
              <a class="dropdown-item disabled" href="#" style="cursor: not-allowed; opacity: 0.6;">
                <i class="${estadoIcon}"></i> ${estadoTexto}
              </a>`;
          }

          return `
            <div class="btn-group">
              <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cogs"></i>
              </button>
              <div class="dropdown-menu">
                ${dropdownItems}
              </div>
            </div>`;
        },
      },
      {
        data: "codigoMantenimiento",
        defaultContent: "N/A",
      },
      {
        data: "tipoMant",
        defaultContent: "N/A",
      },
      { data: "proveedor", defaultContent: "N/A" },
      {
        data: "responsable",
        defaultContent: "N/A",
      },
      {
        data: "estadoMant",
        render: function (data, type, row) {
          switch (data) {
            case "Pendiente":
              return '<span class="badge bg-warning"><i class="fas fa-circle-info me-1"></i> Pendiente</span>';
            case "En Proceso":
              return '<span class="badge bg-info"><i class="fas fa-spinner me-1"></i> En Proceso</span>';
            case "Completado":
              return '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Completado</span>';
            case "Cancelado":
              return '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Cancelado</span>';

            default:
              break;
          }
        },
      },
      {
        data: "fechaRegistro",
        render: function (data) {
          if (!data) return "No programada";
          try {
            return moment(data).format("DD/MM/YYYY");
          } catch (e) {
            return data;
          }
        },
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
  });
}

// Función para cargar combos de filtros
function ListarCombosFiltrosMantenimiento() {
  $.ajax({
    url: "../../controllers/MantenimientosController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status) {
        $("#filtroTipoMovimiento")
          .html(res.data.tiposMantenimiento)
          .trigger("change");

        $("#filtroTipoMovimiento").select2({
          theme: "bootstrap4",
          width: "100%",
        });
      } else {
        Swal.fire(
          "Filtro de mantenimientos",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      Swal.fire(
        "Filtros de mantenimientos",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

function listarActivosModalMantenimiento() {
  // Destruir la instancia existente si existe
  if ($.fn.DataTable.isDataTable("#tbllistarActivosMantenimiento")) {
    $("#tbllistarActivosMantenimiento").DataTable().destroy();
  }

  $("#tbllistarActivosMantenimiento").DataTable({
    dom: "Bfrtip",
    responsive: false,
    ajax: {
      url: "../../controllers/MantenimientosController.php?action=ListarParaMantenimiento",
      type: "POST",
      dataType: "json",
      dataSrc: function (json) {
        if (!json.status) {
          NotificacionToast("error", json.message);
          return [];
        }
        return json.data || [];
      },
      error: function (xhr, status, error) {
        NotificacionToast("error", "Error al cargar los activos: " + error);
        return [];
      },
    },
    columns: [
      { data: "IdActivo" },
      { data: "codigo" },
      { data: "NombreActivo" },
      { data: "Sucursal" },
      { data: "Ambiente" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            '<button class="btn btn-warning btn-sm btnSeleccionarActivoMantenimiento" data-id="' +
            row.IdActivo +
            '"><i class="fas fa-screwdriver-wrench"></i></button>'
          );
        },
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
    order: [[2, "asc"]], // Ordenar por NombreArticulo
    pageLength: 10,
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
  });
}

function verDetallesMantenimiento(idMantenimiento) {
  $.ajax({
    url: "../../controllers/MantenimientosController.php?action=obtenerDetallesMantenimiento",
    type: "POST",
    data: { idMantenimiento: idMantenimiento },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        let detallesHtml =
          '<div class="table-responsive"><table class="table table-bordered table-striped">';
        detallesHtml += "<thead><tr>";
        detallesHtml += "<th>Código Activo</th>";
        detallesHtml += "<th>Nombre Activo</th>";
        // detallesHtml += "<th>Tipo Mantenimiento</th>";
        detallesHtml += "<th>Observaciones</th>";
        detallesHtml += "</tr></thead><tbody>";

        res.data.forEach(function (detalle) {
          detallesHtml += "<tr>";
          detallesHtml += `<td>${detalle.codigoActivo}</td>`;
          detallesHtml += `<td>${detalle.NombreActivo}</td>`;
          // detallesHtml += `<td>${detalle.tipoMantenimiento || "N/A"}</td>`;
          detallesHtml += `<td>${
            detalle.observaciones || "Sin observaciones"
          }</td>`;
          detallesHtml += "</tr>";
        });

        detallesHtml += "</tbody></table></div>";

        Swal.fire({
          title: "Detalles del Mantenimiento",
          html: detallesHtml,
          width: "80%",
          showCloseButton: true,
          showConfirmButton: false,
        });
      } else {
        NotificacionToast(
          "error",
          res.message || "Error al cargar los detalles"
        );
      }
    },
    error: function () {
      NotificacionToast("error", "Error al comunicarse con el servidor");
    },
  });
}

function imprimirReporteMantenimiento(idMantenimiento) {
  window.open(
    `../../views/Reportes/reporteMantenimientoPDF.php?id=${idMantenimiento}`,
    "_blank"
  );
}

function finalizarMantenimiento(idMantenimiento) {
  // Primero obtener los datos del mantenimiento
  $.ajax({
    url: "../../controllers/MantenimientosController.php?action=obtenerMantenimientoParaFinalizar",
    type: "POST",
    data: { idMantenimiento: idMantenimiento },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        const mantenimiento = res.data.mantenimiento;
        const estados = res.data.estados;

        // Crear opciones para el select de estados
        let estadosOptions = "";
        estados.forEach((estado) => {
          const selected = estado.idEstadoMantenimiento == 3 ? "selected" : ""; // Asumiendo que 3 es "Finalizado"
          estadosOptions += `<option value="${estado.idEstadoMantenimiento}" ${selected}>${estado.nombre}</option>`;
        });

        // Mostrar modal de finalización
        Swal.fire({
          title: "Finalizar Mantenimiento",
          html: `
            <div class="container-fluid">
              <div class="row mb-3">
                <div class="col-12">
                  <div class="alert alert-info">
                    <h5><i class="fas fa-tools"></i> ${
                      mantenimiento.codigoMantenimiento
                    }</h5>
                    <p class="mb-1"><strong>Descripción:</strong> ${
                      mantenimiento.descripcion || "Sin descripción"
                    }</p>
                    <p class="mb-1"><strong>Fecha Programada:</strong> ${
                      mantenimiento.fechaMod
                        ? moment(mantenimiento.fechaMod).format("DD/MM/YYYY")
                        : "No programada"
                    }</p>
                    <p class="mb-1"><strong>Costo Estimado:</strong> S/. ${
                      mantenimiento.costoEstimado || "0.00"
                    }</p>
                    <p class="mb-0"><strong>Total de Activos:</strong> ${
                      mantenimiento.totalActivos
                    }</p>
                  </div>
                </div>
              </div>

              <form id="formFinalizarMantenimiento">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="fechaCancelada" class="form-label"><strong>Fecha de Realización *</strong></label>
                    <input type="date" class="form-control" id="fechaCancelada" name="fechaCancelada"
                           value="${moment().format("YYYY-MM-DD")}" required>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="costoReal" class="form-label"><strong>Costo Real</strong></label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">S/.</span>
                      </div>
                      <input type="number" class="form-control" id="costoReal" name="costoReal"
                             step="0.01" min="0" placeholder="0.00">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12 mb-3">
                    <label for="estadoFinal" class="form-label"><strong>Estado Final *</strong></label>
                    <select class="form-control" id="estadoFinal" name="estadoFinal" required disabled>
                      ${estadosOptions}
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12 mb-3">
                    <label for="observacionesFinales" class="form-label"><strong>Observaciones Finales</strong></label>
                    <textarea class="form-control" id="observacionesFinales" name="observacionesFinales"
                              rows="4" maxlength="500" placeholder="Ingrese observaciones sobre la finalización del mantenimiento..."></textarea>
                    <small class="form-text text-muted">
                      <span id="contadorObservaciones">0</span>/500 caracteres
                    </small>
                  </div>
                </div>
              </form>
            </div>
          `,
          width: "800px",
          showCancelButton: true,
          confirmButtonText:
            '<i class="fas fa-check-circle"></i> Finalizar Mantenimiento',
          cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
          confirmButtonColor: "#28a745",
          cancelButtonColor: "#6c757d",
          preConfirm: () => {
            const fechaRealizada =
              document.getElementById("fechaCancelada").value;
            const estadoFinal = document.getElementById("estadoFinal").value;

            if (!fechaRealizada) {
              Swal.showValidationMessage(
                "La fecha de realización es obligatoria"
              );
              return false;
            }

            if (!estadoFinal) {
              Swal.showValidationMessage("Debe seleccionar un estado final");
              return false;
            }

            return {
              fechaRealizada: fechaRealizada,
              costoReal: document.getElementById("costoReal").value || null,
              estadoFinal: estadoFinal,
              observaciones:
                document.getElementById("observacionesFinales").value || null,
            };
          },
        }).then((result) => {
          if (result.isConfirmed) {
            // Procesar la finalización
            procesarFinalizacionMantenimiento(idMantenimiento, result.value);
          }
        });

        // Contador de caracteres para observaciones
        $(document).on("input", "#observacionesFinales", function () {
          const currentLength = $(this).val().length;
          $("#contadorObservaciones").text(currentLength);

          if (currentLength > 450) {
            $("#contadorObservaciones")
              .removeClass("text-muted")
              .addClass("text-danger");
          } else if (currentLength > 350) {
            $("#contadorObservaciones")
              .removeClass("text-muted text-danger")
              .addClass("text-warning");
          } else {
            $("#contadorObservaciones")
              .removeClass("text-warning text-danger")
              .addClass("text-muted");
          }
        });
      } else {
        NotificacionToast(
          "error",
          res.message || "Error al cargar los datos del mantenimiento"
        );
      }
    },
    error: function () {
      NotificacionToast("error", "Error al comunicarse con el servidor");
    },
  });
}

function procesarFinalizacionMantenimiento(idMantenimiento, datos) {
  // Mostrar loading
  Swal.fire({
    title: "Finalizando Mantenimiento",
    html: `
      <div class="text-center">
        <div class="spinner-border text-success mb-3" role="status">
          <span class="sr-only">Procesando...</span>
        </div>
        <p>Finalizando mantenimiento y actualizando estados de activos...</p>
      </div>
    `,
    allowOutsideClick: false,
    showConfirmButton: false,
  });

  // Enviar datos al servidor
  const formData = new FormData();
  formData.append("idMantenimiento", idMantenimiento);
  formData.append("fechaRealizada", datos.fechaRealizada);
  formData.append("costoReal", datos.costoReal);
  formData.append("observaciones", datos.observaciones);
  formData.append("idEstadoMantenimiento", datos.estadoFinal);

  $.ajax({
    url: "../../controllers/MantenimientosController.php?action=FinalizarMantenimiento",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (res) {
      if (res.status) {
        Swal.fire({
          title: "¡Mantenimiento Finalizado!",
          html: `
            <div class="alert alert-success">
              <h5><i class="fas fa-check-circle text-success"></i> Proceso Completado</h5>
              <p class="mb-2">El mantenimiento ha sido finalizado correctamente.</p>
              <hr>
              <div class="row text-left">
                <div class="col-6"><strong>Fecha de Realización:</strong></div>
                <div class="col-6">${moment(datos.fechaRealizada).format(
                  "DD/MM/YYYY"
                )}</div>

                <div class="col-6"><strong>Costo Real:</strong></div>
                <div class="col-6">S/. ${datos.costoReal || "0.00"}</div>

                <div class="col-6"><strong>Estado Final:</strong></div>
                <div class="col-6">${$(
                  "#estadoFinal option:selected"
                ).text()}</div>
              </div>
            </div>

            <div class="mt-3 p-3 bg-light rounded">
              <small class="text-muted">
                <i class="fas fa-info-circle"></i> Los activos han sido actualizados automáticamente a estado operativo.
              </small>
            </div>
          `,
          icon: "success",
          width: "600px",
          confirmButtonText: "Continuar",
          confirmButtonColor: "#28a745",
        }).then(() => {
          // Recargar la tabla de mantenimientos
          if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
            $("#tblMovimientos").DataTable().ajax.reload(null, false);
          }
        });
      } else {
        Swal.fire({
          title: "Error al Finalizar",
          html: `
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-triangle"></i> ${res.message}
            </div>
          `,
          icon: "error",
          confirmButtonText: "Intentar de nuevo",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error en la petición:", error);
      Swal.fire({
        title: "Error de Comunicación",
        html: `
          <div class="alert alert-danger">
            <i class="fas fa-wifi"></i> No se pudo comunicar con el servidor.<br>
            <small>Error: ${error}</small>
          </div>
        `,
        icon: "error",
        confirmButtonText: "Reintentar",
      });
    },
  });
}

function cancelarMantenimiento(idMantenimiento) {
  // Primero obtener los datos del mantenimiento
  $.ajax({
    url: "../../controllers/MantenimientosController.php?action=obtenerMantenimientoParaCancelar",
    type: "POST",
    data: { idMantenimiento: idMantenimiento },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        const mantenimiento = res.data.mantenimiento;
        const estados = res.data.estados;

        // Crear opciones para el select de estados (solo mostrar "Cancelado")
        let estadosOptions = "";
        estados.forEach((estado) => {
          if (
            estado.nombre &&
            estado.nombre.toLowerCase().includes("cancelado")
          ) {
            estadosOptions += `<option value="${estado.idEstadoMantenimiento}" selected>${estado.nombre}</option>`;
          }
        });

        // Si no se encuentra un estado "Cancelado", usar el ID 4 por defecto
        if (!estadosOptions) {
          estadosOptions = '<option value="4" selected>Cancelado</option>';
        }

        // Mostrar modal de cancelación
        Swal.fire({
          title: "Cancelar Mantenimiento",
          html: `
            <div class="container-fluid">
              <div class="row mb-3">
                <div class="col-12">
                  <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> ${
                      mantenimiento.codigoMantenimiento
                    }</h5>
                    <p class="mb-1"><strong>Descripción:</strong> ${
                      mantenimiento.descripcion || "Sin descripción"
                    }</p>
                    <p class="mb-1"><strong>Estado Actual:</strong> ${
                      mantenimiento.estadoActual
                    }</p>
                    <p class="mb-1"><strong>Fecha Programada:</strong> ${
                      mantenimiento.fechaMod
                        ? moment(mantenimiento.fechaMod).format("DD/MM/YYYY")
                        : "No programada"
                    }</p>
                    <p class="mb-0"><strong>Total de Activos:</strong> ${
                      mantenimiento.totalActivos
                    }</p>
                  </div>
                </div>
              </div>

              <form id="formCancelarMantenimiento">
                <div class="row">
                  <div class="col-12 mb-3">
                    <label for="estadoCancelacion" class="form-label"><strong>Estado</strong></label>
                    <select class="form-control" id="estadoCancelacion" name="estadoCancelacion" readonly>
                      ${estadosOptions}
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12 mb-3">
                    <label for="motivoCancelacion" class="form-label"><strong>Motivo de Cancelación *</strong></label>
                    <textarea class="form-control" id="motivoCancelacion" name="motivoCancelacion"
                              rows="4" maxlength="500" placeholder="Ingrese el motivo detallado de la cancelación..." required></textarea>
                    <small class="form-text text-muted">
                      <span id="contadorMotivo">0</span>/500 caracteres
                    </small>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12">
                    <div class="alert alert-info">
                      <h6><i class="fas fa-info-circle"></i> Información Importante</h6>
                      <ul class="mb-0">
                        <li>Los activos asociados volverán automáticamente al estado operativo</li>
                        <li>Esta acción no se puede deshacer</li>
                        <li>Se registrará el historial de cambios de estado</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          `,
          width: "700px",
          showCancelButton: true,
          confirmButtonText:
            '<i class="fas fa-times-circle"></i> Cancelar Mantenimiento',
          cancelButtonText: '<i class="fas fa-arrow-left"></i> Volver',
          confirmButtonColor: "#dc3545",
          cancelButtonColor: "#6c757d",
          preConfirm: () => {
            const motivo = document
              .getElementById("motivoCancelacion")
              .value.trim();
            const estado = document.getElementById("estadoCancelacion").value;

            if (!motivo) {
              Swal.showValidationMessage(
                "El motivo de cancelación es obligatorio"
              );
              return false;
            }

            if (motivo.length < 10) {
              Swal.showValidationMessage(
                "El motivo debe tener al menos 10 caracteres"
              );
              return false;
            }

            return {
              motivo: motivo,
              estadoCancelacion: estado,
            };
          },
        }).then((result) => {
          if (result.isConfirmed) {
            // Confirmar la cancelación
            Swal.fire({
              title: "¿Está completamente seguro?",
              html: `
                <div class="alert alert-danger">
                  <h6><i class="fas fa-exclamation-triangle"></i> Confirmación Final</h6>
                  <p>Está a punto de cancelar el mantenimiento <strong>${mantenimiento.codigoMantenimiento}</strong></p>
                  <p class="mb-0">Esta acción es <strong>irreversible</strong></p>
                </div>
              `,
              icon: "warning",
              showCancelButton: true,
              confirmButtonText: "Sí, cancelar mantenimiento",
              cancelButtonText: "No, mantener activo",
              confirmButtonColor: "#dc3545",
              cancelButtonColor: "#28a745",
            }).then((confirmResult) => {
              if (confirmResult.isConfirmed) {
                // Procesar la cancelación
                procesarCancelacionMantenimiento(idMantenimiento, result.value);
              }
            });
          }
        });

        // Contador de caracteres para motivo
        $(document).on("input", "#motivoCancelacion", function () {
          const currentLength = $(this).val().length;
          $("#contadorMotivo").text(currentLength);

          if (currentLength > 450) {
            $("#contadorMotivo")
              .removeClass("text-muted")
              .addClass("text-danger");
          } else if (currentLength > 350) {
            $("#contadorMotivo")
              .removeClass("text-muted text-danger")
              .addClass("text-warning");
          } else {
            $("#contadorMotivo")
              .removeClass("text-warning text-danger")
              .addClass("text-muted");
          }
        });
      } else {
        NotificacionToast(
          "error",
          res.message || "Error al cargar los datos del mantenimiento"
        );
      }
    },
    error: function () {
      NotificacionToast("error", "Error al comunicarse con el servidor");
    },
  });
}

function procesarCancelacionMantenimiento(idMantenimiento, datos) {
  // Mostrar loading
  Swal.fire({
    title: "Cancelando Mantenimiento",
    html: `
      <div class="text-center">
        <div class="spinner-border text-danger mb-3" role="status">
          <span class="sr-only">Procesando...</span>
        </div>
        <p>Cancelando mantenimiento y actualizando estados de activos...</p>
      </div>
    `,
    allowOutsideClick: false,
    showConfirmButton: false,
  });

  // Enviar datos al servidor
  const formData = new FormData();
  formData.append("idMantenimiento", idMantenimiento);
  formData.append("motivo", datos.motivo);
  formData.append("idEstadoMantenimiento", datos.estadoCancelacion);

  $.ajax({
    url: "../../controllers/MantenimientosController.php?action=CancelarMantenimiento",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (res) {
      if (res.status) {
        Swal.fire({
          title: "¡Mantenimiento Cancelado!",
          html: `
            <div class="alert alert-warning">
              <h5><i class="fas fa-times-circle text-danger"></i> Cancelación Completada</h5>
              <p class="mb-2">El mantenimiento ha sido cancelado correctamente.</p>
              <hr>
              <div class="row text-left">
                <div class="col-12 mb-2">
                  <strong>Motivo de Cancelación:</strong>
                </div>
                <div class="col-12">
                  <div class="bg-light p-2 rounded">
                    <em>"${datos.motivo}"</em>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-3 p-3 bg-light rounded">
              <small class="text-muted">
                <i class="fas fa-info-circle"></i> Los activos han sido liberados automáticamente y están disponibles para otros mantenimientos.
              </small>
            </div>
          `,
          icon: "success",
          width: "600px",
          confirmButtonText: "Continuar",
          confirmButtonColor: "#28a745",
        }).then(() => {
          // Recargar la tabla de mantenimientos
          if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
            $("#tblMovimientos").DataTable().ajax.reload(null, false);
          }
        });
      } else {
        Swal.fire({
          title: "Error al Cancelar",
          html: `
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-triangle"></i> ${res.message}
            </div>
          `,
          icon: "error",
          confirmButtonText: "Intentar de nuevo",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error en la petición:", error);
      Swal.fire({
        title: "Error de Comunicación",
        html: `
          <div class="alert alert-danger">
            <i class="fas fa-wifi"></i> No se pudo comunicar con el servidor.<br>
            <small>Error: ${error}</small>
          </div>
        `,
        icon: "error",
        confirmButtonText: "Reintentar",
      });
    },
  });
}

function aprobarMantenimiento(idMantenimiento) {
  // Primero obtener los datos del mantenimiento
  $.ajax({
    url: "../../controllers/MantenimientosController.php?action=obtenerMantenimientoParaFinalizar",
    type: "POST",
    data: { idMantenimiento: idMantenimiento },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        const mantenimiento = res.data.mantenimiento;

        // Mostrar modal de aprobación
        Swal.fire({
          title: "Aprobar Mantenimiento",
          html: `
            <div class="container-fluid">
              <div class="row mb-3">
                <div class="col-12">
                  <div class="alert alert-info">
                    <h5><i class="fas fa-tools"></i> ${
                      mantenimiento.codigoMantenimiento
                    }</h5>
                    <p class="mb-1"><strong>Descripción:</strong> ${
                      mantenimiento.descripcion || "Sin descripción"
                    }</p>
                    <p class="mb-1"><strong>Estado Actual:</strong> ${
                      mantenimiento.estadoActual
                    }</p>
                    <p class="mb-1"><strong>Fecha Programada:</strong> ${
                      mantenimiento.fechaProgramada
                        ? moment(mantenimiento.fechaProgramada).format(
                            "DD/MM/YYYY"
                          )
                        : "No programada"
                    }</p>
                    <p class="mb-1"><strong>Costo Estimado:</strong> S/. ${
                      mantenimiento.costoEstimado || "0.00"
                    }</p>
                    <p class="mb-0"><strong>Total de Activos:</strong> ${
                      mantenimiento.totalActivos
                    }</p>
                  </div>
                </div>
              </div>

              <form id="formAprobarMantenimiento">
                <div class="row">
                  <div class="col-12 mb-3">
                    <label for="observacionesAprobacion" class="form-label"><strong>Observaciones de Aprobación</strong></label>
                    <textarea class="form-control" id="observacionesAprobacion" name="observacionesAprobacion"
                              rows="4" maxlength="500" placeholder="Ingrese observaciones sobre la aprobación del mantenimiento..."></textarea>
                    <small class="form-text text-muted">
                      <span id="contadorAprobacion">0</span>/500 caracteres
                    </small>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12">
                    <div class="alert alert-success">
                      <h6><i class="fas fa-info-circle"></i> Información Importante</h6>
                      <ul class="mb-0">
                        <li>Los activos asociados cambiarán automáticamente a estado "En Mantenimiento"</li>
                        <li>El mantenimiento pasará a estado "En Proceso"</li>
                        <li>Se registrará el historial de cambios de estado</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          `,
          width: "700px",
          showCancelButton: true,
          confirmButtonText:
            '<i class="fas fa-check"></i> Aprobar Mantenimiento',
          cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
          confirmButtonColor: "#28a745",
          cancelButtonColor: "#6c757d",
          preConfirm: () => {
            return {
              observaciones:
                document.getElementById("observacionesAprobacion").value ||
                null,
            };
          },
        }).then((result) => {
          if (result.isConfirmed) {
            // Confirmar la aprobación
            Swal.fire({
              title: "¿Está completamente seguro?",
              html: `
                <div class="alert alert-success">
                  <h6><i class="fas fa-check-circle"></i> Confirmación Final</h6>
                  <p>Está a punto de aprobar el mantenimiento <strong>${mantenimiento.codigoMantenimiento}</strong></p>
                  <p class="mb-0">Los activos cambiarán a estado "En Mantenimiento"</p>
                </div>
              `,
              icon: "question",
              showCancelButton: true,
              confirmButtonText: "Sí, aprobar mantenimiento",
              cancelButtonText: "No, mantener pendiente",
              confirmButtonColor: "#28a745",
              cancelButtonColor: "#6c757d",
            }).then((confirmResult) => {
              if (confirmResult.isConfirmed) {
                // Procesar la aprobación
                procesarAprobacionMantenimiento(idMantenimiento, result.value);
              }
            });
          }
        });

        // Contador de caracteres para observaciones
        $(document).on("input", "#observacionesAprobacion", function () {
          const currentLength = $(this).val().length;
          $("#contadorAprobacion").text(currentLength);

          if (currentLength > 450) {
            $("#contadorAprobacion")
              .removeClass("text-muted")
              .addClass("text-danger");
          } else if (currentLength > 350) {
            $("#contadorAprobacion")
              .removeClass("text-muted text-danger")
              .addClass("text-warning");
          } else {
            $("#contadorAprobacion")
              .removeClass("text-warning text-danger")
              .addClass("text-muted");
          }
        });
      } else {
        NotificacionToast(
          "error",
          res.message || "Error al cargar los datos del mantenimiento"
        );
      }
    },
    error: function () {
      NotificacionToast("error", "Error al comunicarse con el servidor");
    },
  });
}

function procesarAprobacionMantenimiento(idMantenimiento, datos) {
  // Mostrar loading
  Swal.fire({
    title: "Aprobando Mantenimiento",
    html: `
      <div class="text-center">
        <div class="spinner-border text-success mb-3" role="status">
          <span class="sr-only">Procesando...</span>
        </div>
        <p>Aprobando mantenimiento y actualizando estados de activos...</p>
      </div>
    `,
    allowOutsideClick: false,
    showConfirmButton: false,
  });

  // Enviar datos al servidor
  const formData = new FormData();
  formData.append("idMantenimiento", idMantenimiento);
  formData.append("observaciones", datos.observaciones || null);

  $.ajax({
    url: "../../controllers/MantenimientosController.php?action=AprobarMantenimiento",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (res) {
      if (res.status) {
        Swal.fire({
          title: "¡Mantenimiento Aprobado!",
          html: `
            <div class="alert alert-success">
              <h5><i class="fas fa-check-circle text-success"></i> Aprobación Completada</h5>
              <p class="mb-2">El mantenimiento ha sido aprobado correctamente.</p>
              <hr>
              <div class="row text-left">
                <div class="col-6"><strong>Estado Anterior:</strong></div>
                <div class="col-6">Pendiente</div>

                <div class="col-6"><strong>Estado Nuevo:</strong></div>
                <div class="col-6">En Proceso</div>
              </div>
            </div>

            <div class="mt-3 p-3 bg-light rounded">
              <small class="text-muted">
                <i class="fas fa-info-circle"></i> Los activos han sido actualizados automáticamente a estado "En Mantenimiento".
              </small>
            </div>
          `,
          icon: "success",
          width: "600px",
          confirmButtonText: "Continuar",
          confirmButtonColor: "#28a745",
        }).then(() => {
          // Recargar la tabla de mantenimientos
          if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
            $("#tblMovimientos").DataTable().ajax.reload(null, false);
          }
        });
      } else {
        Swal.fire({
          title: "Error al Aprobar",
          html: `
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-triangle"></i> ${res.message}
            </div>
          `,
          icon: "error",
          confirmButtonText: "Intentar de nuevo",
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error en la petición:", error);
      Swal.fire({
        title: "Error de Comunicación",
        html: `
          <div class="alert alert-danger">
            <i class="fas fa-wifi"></i> No se pudo comunicar con el servidor.<br>
            <small>Error: ${error}</small>
          </div>
        `,
        icon: "error",
        confirmButtonText: "Reintentar",
      });
    },
  });
}
