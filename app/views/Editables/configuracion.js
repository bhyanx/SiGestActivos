$(document).ready(function () {
  initConfiguracion();
});

function initConfiguracion() {
  ListarCombosFiltros();
  listarActivosTableConfiguracion();

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
}

function ListarCombosFiltros() {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status) {
        $("#filtroEmpresa")
          .html(res.data.empresas)
          .trigger("change");
        $("#filtroCategoria")
          .html(res.data.categorias)
          .trigger("change");
        $("#filtroEstado").html(res.data.estado).trigger("change");
        $("#filtroAmbiente")
          .html(
            '<option value="">Seleccionar Ambiente</option><option value="TODOS">🌍 Todos los Ambientes</option>' +
              res.data.ambientes
          )
          .trigger("change");

        // 5. Inicializar todos los Select2 de una vez
        $(
          "#filtroCategoria, #filtroEmpresa, #filtroSucursal, #filtroAmbiente, #filtroEstado"
        ).select2({
          theme: "bootstrap4",
          width: "100%",
          allowClear: true,
        });

        // 6. Establecer placeholders específicos
        $("#filtroCategoria").select2("destroy").select2({
          theme: "bootstrap4",
          width: "100%",
          placeholder: "Filtrar por Categoría",
          allowClear: true,
        });

        $("#filtroEstado").select2("destroy").select2({
          theme: "bootstrap4",
          width: "100%",
          placeholder: "Filtrar por Estado",
          allowClear: true,
        });

        $("#filtroEmpresa").select2("destroy").select2({
          theme: "bootstrap4",
          width: "100%",
          placeholder: "Seleccionar Empresa (Obligatorio)",
          allowClear: false, // Empresa es obligatoria
        });

        $("#filtroSucursal").select2("destroy").select2({
          theme: "bootstrap4",
          width: "100%",
          placeholder: "Filtrar por Sucursal",
          allowClear: true,
        });

        $("#filtroAmbiente").select2("destroy").select2({
          theme: "bootstrap4",
          width: "100%",
          placeholder: "Filtrar por Ambiente",
          allowClear: true,
        });

        // 8. Configurar event listeners optimizados
        configurarEventListenersFiltros();
      } else {
        Swal.fire(
          "Configuración",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      Swal.fire(
        "Configuración",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

function configurarEventListenersFiltros() {
  // Event listener para empresa (copiado exacto del registro manual)
  $("#filtroEmpresa")
    .off("change.filtros")
    .on("change.filtros", function () {
      const codEmpresa = $(this).val();
      const unidadNegocioSelect = $("#filtroSucursal");
      const ambienteSelect = $("#filtroAmbiente");

      if (codEmpresa) {
        // Cargar unidades de negocio para la empresa seleccionada
        $.ajax({
          url: "../../controllers/GestionarActivosController.php?action=comboUnidadNegocio",
          type: "POST",
          data: { codEmpresa: codEmpresa },
          dataType: "json",
          success: function (res) {
            if (res.status) {
              unidadNegocioSelect
                .html(
                  '<option value="">Seleccionar Sucursal</option>' + res.data
                )
                .trigger("change");

              // Mantener la selección de sucursal de sesión si corresponde a la empresa
              if (
                typeof sucursalSesion !== "undefined" &&
                sucursalSesion &&
                sucursalSesion !== ""
              ) {
                unidadNegocioSelect.val(sucursalSesion).trigger("change");
              }
            } else {
              unidadNegocioSelect.html(
                '<option value="">Error al cargar</option>'
              );
              NotificacionToast("error", res.message);
            }
          },
          error: function () {
            unidadNegocioSelect.html(
              '<option value="">Error al cargar</option>'
            );
            NotificacionToast("error", "Error al cargar unidades de negocio");
          },
        });
      } else {
        unidadNegocioSelect.html(
          '<option value="">Seleccionar Sucursal</option>'
        );
        ambienteSelect.html(
          '<option value="">Seleccionar Ambiente</option><option value="TODOS">🌍 Todos los Ambientes</option>'
        );
      }
    });

  // Event listener para sucursal (copiado exacto del registro manual)
  $("#filtroSucursal")
    .off("change.filtros")
    .on("change.filtros", function () {
      const codEmpresa = $("#filtroEmpresa").val();
      const codUnidadNegocio = $(this).val();
      const ambienteSelect = $("#filtroAmbiente");

      if (codEmpresa && codUnidadNegocio) {
        // Cargar ambientes para la empresa y unidad de negocio seleccionadas
        $.ajax({
          url: "../../controllers/GestionarActivosController.php?action=comboAmbiente",
          type: "POST",
          data: {
            idEmpresa: codEmpresa,
            idSucursal: codUnidadNegocio,
          },
          dataType: "json",
          success: function (res) {
            if (res.status) {
              ambienteSelect
                .html(
                  '<option value="">Seleccionar Ambiente</option><option value="TODOS">🌍 Todos los Ambientes</option>' +
                    res.data
                )
                .trigger("change");
            } else {
              ambienteSelect.html(
                '<option value="">Error al cargar ambientes</option>'
              );
              NotificacionToast("error", res.message);
            }
          },
          error: function () {
            ambienteSelect.html(
              '<option value="">Error al cargar ambientes</option>'
            );
            NotificacionToast("error", "Error al cargar ambientes");
          },
        });
      } else {
        ambienteSelect.html(
          '<option value="">Seleccionar Ambiente</option><option value="TODOS">🌍 Todos los Ambientes</option>'
        );
      }
    });

}

function listarActivosTableConfiguracion() {
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
              columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
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
      url: "../../controllers/GestionarActivosController.php?action=ConsultarActivos",
      type: "POST",
      data: function (d) {
        // Obtener valores de los filtros
        return {
          filtroEmpresa: $("#filtroEmpresa").val() || null,
          filtroSucursal: $("#filtroSucursal").val() || null,
          filtroAmbiente: $("#filtroAmbiente").val() || null,
          filtroCategoria: $("#filtroCategoria").val() || null,
          filtroEstado: $("#filtroEstado").val() || null,
          //filtroFecha: $("#filtroFecha").val() || null,
        };
      },
      dataType: "json",
      dataSrc: function (json) {
        return json || [];
      },
    },
    columns: [
      { data: "idActivo" },
      { data: "codigo" },
      { data: "codigoAntiguo", visible: false },
      { data: "NombreActivo" },
      { data: "idEstadoActivo", visible: false, searchable: false },
      {
        data: "Estado",
        render: function (data, type, row) {
          switch (data) {
            case "Operativa":
              return '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Operativo</span>';
            case "Reparación":
              return '<span class="badge bg-warning"><i class="fas fa-wrench me-1"></i> Reparación</span>';
            case "Baja":
              return '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Baja</span>';
            case "Vendido":
              return '<span class="badge bg-secondary"><i class="fas fa-dollar-sign me-1"></i> Vendido</span>';
            case "Regular":
              return '<span class="badge bg-info"><i class="fas fa-exclamation-circle me-1"></i> Regular</span>';
            case "Malo":
              return '<span class="badge bg-dark"><i class="fas fa-skull-crossbones me-1"></i> Malo</span>';
            default:
              return '<span class="badge bg-dark"><i class="fas fa-question-circle me-1"></i> Desconocido</span>';
          }
        },
      },
      { data: "idCategoria", visible: false, searchable: false },
      { data: "idEmpresa", visible: false, searchable: false },
      { data: "idSucursal", visible: false, searchable: false },
      { data: "idAmbiente", visible: false, searchable: false },
      { data: "NombreAmbiente" },
      { data: "idResponsable", visible: false, searchable: false },
      {
        data: "Responsable",
        render: function (data, type, row) {
          if (data === "No Asignado" || data === null || data === "") {
            return '<span class="badge bg-dark">No Asignado</span>';
          } else {
            return data;
          }
        },
      },
      { data: "Serie" },
      { data: "valorAdquisicion" },
      { data: "fechaRegistro" },
      {
        data: 'esEditable',
        render: function (data, type, row) {
          if (data == 1) {
            return '<span class="badge badge-success"><i class="fas fa-unlock"></i> Editable</span>';
          } else {
            return '<span class="badge badge-danger"><i class="fas fa-lock"></i> Bloqueado</span>';
          }
        },
        visible: true,
        searchable: false,
        orderable: false
      },
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
              <button class="dropdown-item btnVerEventos" type="button" data-id="${row.idActivo}" data-nombre="${row.NombreActivo}">
                <i class="fas fa-calendar-alt text-warning"></i> Ver Eventos
              </button>
            </div>
          </div>`;
        },
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
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

// Event handler para el formulario de búsqueda
$("#frmbusqueda").on("submit", function (e) {
  e.preventDefault();

  // Validar que se haya seleccionado una empresa
  const empresaSeleccionada = $("#filtroEmpresa").val();
  if (!empresaSeleccionada || empresaSeleccionada === "") {
    Swal.fire({
      icon: "warning",
      title: "Empresa Requerida",
      text: "Debe seleccionar una empresa para realizar la búsqueda.",
      confirmButtonText: "Entendido",
    });
    return;
  }

  $("#divtblRegistros").show();

  if ($.fn.DataTable.isDataTable("#tblRegistros")) {
    $("#tblRegistros").DataTable().clear().destroy();
  }

  setTimeout(() => {
    listarActivosTableConfiguracion();
  }, 100);
});

// Event listener for Ver Detalles button
$(document).on("click", ".btnVerDetalles", function () {
  const fila = $(this).closest("tr");
  const datos = $("#tblRegistros").DataTable().row(fila).data();

  if (!datos) {
    Swal.fire("Error", "No se pudo obtener el IdArticulo.", "error");
    return;
  }

  // Aquí puedes agregar la lógica para mostrar detalles del activo
  // Similar a la función en activosp.js
});

// Event listener for Ver Mantenimientos button
$(document).on("click", ".btnVerMantenimientos", function () {
  const fila = $(this).closest("tr");
  const datos = $("#tblRegistros").DataTable().row(fila).data();

  console.log("Datos de la fila seleccionada:", datos);

  if (!datos || !datos.idActivo) {
    NotificacionToast("error", "No se pudo obtener el ID del activo.");
    return;
  }

  // Aquí puedes agregar la lógica para mostrar mantenimientos
  // Similar a la función en activosp.js
});

// Event listener for Ver Eventos button
$(document).on("click", ".btnVerEventos", function () {
  const idActivo = $(this).data("id");
  const nombreActivo = $(this).data("nombre");

  if (!idActivo) {
    NotificacionToast("error", "No se pudo obtener el ID del activo.");
    return;
  }

  // Llenar información básica del modal
  $("#idActivoEvento").val(idActivo);
  $("#nombreActivoEvento").val(nombreActivo);

  // Obtener estado editable del activo
  $.ajax({
    url: "../../controllers/ConfiguracionController.php?action=obtenerEstadoEditable",
    type: "POST",
    data: { idActivo: idActivo },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        const estadoEditable = res.esEditable;
        const badgeHtml = estadoEditable == 1
          ? '<span class="badge badge-success"><i class="fas fa-unlock"></i> Editable</span>'
          : '<span class="badge badge-danger"><i class="fas fa-lock"></i> Bloqueado</span>';
        $("#estadoEditableBadge").html(badgeHtml);

        // Cambiar texto del botón según el estado
        $("#btnToggleEditable").html(
          estadoEditable == 1
            ? '<i class="fas fa-lock"></i> Bloquear Edición'
            : '<i class="fas fa-unlock"></i> Permitir Edición'
        );
      } else {
        $("#estadoEditableBadge").html('<span class="badge badge-warning">Desconocido</span>');
      }
    },
    error: function () {
      $("#estadoEditableBadge").html('<span class="badge badge-danger">Error</span>');
    }
  });

  // Obtener historial de eventos
  $.ajax({
    url: "../../controllers/ConfiguracionController.php?action=obtenerHistorialEventos",
    type: "POST",
    data: { idActivo: idActivo },
    dataType: "json",
    success: function (res) {
      const tbody = $("#tblHistorialEventos tbody");
      tbody.empty();

      if (res.status && res.data && res.data.length > 0) {
        res.data.forEach(function (evento) {
          const fila = `
            <tr>
              <td>${new Date(evento.fecha).toLocaleString()}</td>
              <td>Cambio en ${evento.campo}</td>
              <td>${evento.usuario}</td>
              <td>De "${evento.valorAnterior}" a "${evento.valorNuevo}"</td>
            </tr>
          `;
          tbody.append(fila);
        });
      } else {
        tbody.html('<tr><td colspan="4" class="text-center">No hay eventos registrados</td></tr>');
      }
    },
    error: function () {
      $("#tblHistorialEventos tbody").html('<tr><td colspan="4" class="text-center text-danger">Error al cargar eventos</td></tr>');
    }
  });

  // Mostrar el modal
  $("#modalVerEventos").modal("show");
});

// Event listener for toggle editable button
$(document).on("click", "#btnToggleEditable", function () {
  const idActivo = $("#idActivoEvento").val();
  const nombreActivo = $("#nombreActivoEvento").val();

  if (!idActivo) {
    NotificacionToast("error", "No se pudo obtener el ID del activo.");
    return;
  }

  Swal.fire({
    title: "¿Cambiar estado de edición?",
    html: `¿Está seguro de cambiar el estado de edición del activo <strong>${nombreActivo}</strong>?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, cambiar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../../controllers/ConfiguracionController.php?action=toggleEdicion",
        type: "POST",
        data: { idActivo: idActivo },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            NotificacionToast("success", res.message);

            // Actualizar el badge en el modal
            const badgeHtml = res.esEditable == 1
              ? '<span class="badge badge-success"><i class="fas fa-unlock"></i> Editable</span>'
              : '<span class="badge badge-danger"><i class="fas fa-lock"></i> Bloqueado</span>';
            $("#estadoEditableBadge").html(badgeHtml);

            // Cambiar texto del botón
            $("#btnToggleEditable").html(
              res.esEditable == 1
                ? '<i class="fas fa-lock"></i> Bloquear Edición'
                : '<i class="fas fa-unlock"></i> Permitir Edición'
            );

            // Recargar la tabla para mostrar el cambio
            $("#tblRegistros").DataTable().ajax.reload();
          } else {
            NotificacionToast("error", res.message);
          }
        },
        error: function () {
          NotificacionToast("error", "Error al cambiar el estado de edición.");
        }
      });
    }
  });
});