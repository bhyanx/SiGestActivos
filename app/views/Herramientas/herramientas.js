let activoFormCount = 0;
let combosActivos = null;

const CONFIGURACION = {
  URLS: {
    //CONTROLADOR: "../../controllers/AmbienteController.php",
    IDIOMA_DATATABLES: "../../../public/plugins/datatables/json/Spanish.json",
  },
  VALORES_POR_DEFECTO: {
    ESTADO_ACTIVO: 1,
    ESTADO_INACTIVO: 0,
    LONGITUD_TABLA: 10,
  },
};

$(document).ready(function () {
  init();
});

function init() {
  listarActivosTable();
  ListarCombosMov();
  ListarCombosFiltros();
  // ListarCombosModalActualizarActivo();

  // ? INICIO: SE COMENTO EL CODIGO PARA INICIALIZAR EL MODAL DE REGISTRO MANUAL
  $("#divModalActualizarActivo").on("shown.bs.modal", function () {
    $("#frmEditarActivo")[0].reset();
    cargarCombosModalActualizarActivo();
  });

  // ? FIN: SE COMENTO EL CODIGO PARA INICIALIZAR EL MODAL DE REGISTRO MANUAL
  $("#divModalRegistroManualActivo").on("shown.bs.modal", function () {
    $("#frmmantenimiento")[0].reset();
    cargarCombosModalRegistroManual();
  });

  $(document).on(
    "change",
    "#IdSucursalOrigen, #IdSucursalDestino",
    function () {
      setSucursalOrigenDestino();
    }
  );

  // Manejar cambio de tipo de documento
  $(document).on("change", "#tipoDocumento", function () {
    const tipoDoc = $(this).val();
    const labelDocumento = $("#labelDocumento");
    const inputDocumento = $("#inputDocumento");

    if (tipoDoc === "ingreso") {
      labelDocumento.text("Doc. Ingreso Almacén:");
      inputDocumento.attr("placeholder", "ID de Doc. Ingreso");
    } else if (tipoDoc === "venta") {
      labelDocumento.text("Doc. Venta:");
      inputDocumento.attr("placeholder", "ID de Doc. Venta");
    }

    // Limpiar el input cuando cambie el tipo
    inputDocumento.val("");
  });

  $(document).on("click", "#btnBuscarDocumento", function () {
    let documento = $("#inputDocumento").val().trim();
    let tipoDoc = $("#tipoDocumento").val();

    console.log("Data enviada a listarActivo:", documento, "Tipo:", tipoDoc);

    if (!documento) {
      const tipoTexto =
        tipoDoc === "ingreso" ? "Doc. Ingreso Almacén" : "Doc. Venta";
      mostrarNotificacionModalActivos(`Ingrese el ${tipoTexto}`, "danger");
      return;
    }

    $("#ModalArticulos").modal("show");
    listarActivosModal(documento, tipoDoc);
  });

  $("#btnvolverprincipal")
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
          $("#divregistroActivo").hide();
          $("#divRegistroManualActivoMultiple").hide();
          $("#divlistadoactivos").show();
          $("#divtblactivos").show();
          $("#tblRegistros").show();
        }
      });
    });

  $("#btnvolverprincipalManual")
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
          $("#divregistroActivo").hide();
          $("#divRegistroManualActivoMultiple").hide();
          $("#divlistadoactivos").show();
          $("#divtblactivos").show();
          $("#tblRegistros").show();
        }
      });
    });

  $("#btncancelarRegistroManual")
    .off("click")
    .on("click", function () {
      Swal.fire({
        title: "¿Estás seguro cerrar el formulario?",
        text: "Se perderán los datos registrados",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Aceptar",
        cancelButtonColor: "#d33",
        cancelButtonText: "No, continuar aquí",
      }).then((result) => {
        if (result.isConfirmed) {
          $("#divregistroActivo").hide();
          $("#divRegistroManualActivoMultiple").hide();
          $("#divlistadoactivos").show();
          $("#divtblactivos").show();
          $("#tblRegistros").show();
        }
      });
    });

  $("#btncancelarGuardarDetalles")
    .off("click")
    .on("click", function () {
      Swal.fire({
        title: "¿Estás seguro de cerrar los detalles?",
        text: "Se perderán los cambios realizados y los articulos seleccionados",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Aceptar",
        cancelButtonColor: "#d33",
        cancelButtonText: "No, continuar aquí",
      }).then((result) => {
        if (result.isConfirmed) {
          $("#divregistroActivo").hide();
          $("#divRegistroManualActivoMultiple").hide();
          $("#divlistadoactivos").show();
          $("#divtblactivos").show();
          $("#tblRegistros").show();
        }
      });
    });

  $("#divregistroActivo").hide();

  $("#btnnuevo")
    .off("click")
    .on("click", function () {
      $("#divregistroActivo").show();
      $("#tblRegistros").hide();
      $("#divtblRegistros").hide();
      $("#divtblactivos").hide();
      $("#divlistadoactivos").hide();
      $("#divRegistroManualActivoMultiple").hide();
      $("#tituloModalMovimiento").html(
        '<i class="fa fa-plus-circle"></i> Registrar Movimiento'
      );
      $("#frmArticulos")[0].reset();
      $("#ModalArticulos").modal("show");
    });

  $("#btnCrearActivo")
    .off("click")
    .on("click", function () {
      $("#divRegistroManualActivoMultiple").show();
      $("#divlistadoactivos").hide();
      $("#tblRegistros").hide();
      $("#divtblRegistros").hide();
      $("#divtblactivos").hide();
      $("#divregistroActivo").hide();
      $("#activosContainer").empty();
      activoFormCount = 0;
      if (!combosActivos) {
        obtenerCombosActivos(function (data) {
          combosActivos = data;
          addActivoManualForm(combosActivos);
        });
      } else {
        addActivoManualForm(combosActivos);
      }
    });

  $("#btnCancelarMovimiento")
    .off("click")
    .on("click", function () {
      $("#divregistroActivo").hide();
      $("#divtblactivos").show();
      $("#divlistadoactivos").show();
    });

  $("#frmbusqueda").on("submit", function (e) {
    e.preventDefault();
    $("#divtblactivos").show();
    $("#divregistroActivo").hide();
    $("#divlistadoactivos").show();
    $("#divRegistroManualActivoMultiple").hide();

    // ¡AQUÍ! Asegúrate de mostrar el div de la tabla
    $("#divtblRegistros").show();

    if ($.fn.DataTable.isDataTable("#tblRegistros")) {
      $("#tblRegistros").DataTable().clear().destroy();
    }

    setTimeout(() => {
      listarActivosTable();
    }, 100);
  });

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

  $("#ModalArticulos").on("shown.bs.modal", function () {
    let docIngreso = $("#inputDocIngresoAlm").val().trim();
    if (docIngreso) {
      listarActivosModal(docIngreso);
    }
  });

  $("#btnAgregarOtroActivo").on("click", function () {
    if (combosActivos) {
      addActivoManualForm(combosActivos);
    }
  });

  $(document).on("click", ".btn-remove-activo", function () {
    const formToRemove = $(this).closest(".activo-manual-form");
    if ($("#activosContainer .activo-manual-form").length > 1) {
      Swal.fire({
        title: "¿Estás seguro?",
        text: "Se eliminará este formulario de activo.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          formToRemove.remove();
          updateActivoFormNumbers();
        }
      });
    } else {
      Swal.fire({
        icon: "warning",
        title: "Advertencia",
        text: "No puedes eliminar el último formulario de activo.",
      });
    }
  });

  $(document).on("click", ".btnSeleccionarActivo", function () {
    var fila = $(this).closest("tr");
    var tipoDoc = $(this).data("tipo");

    var activo = {
      id: fila.find("td:eq(0)").text(),
      nombre: fila.find("td:eq(1)").text(),
      marca: fila.find("td:eq(2)").text(),
      empresa: fila.find("td:eq(3)").text(),
      unidadNegocio: fila.find("td:eq(4)").text(),
      nombreLocal: fila.find("td:eq(5)").text(),
    };

    // Para documentos de venta, agregar la cantidad
    if (tipoDoc === "venta") {
      activo.cantidad = parseInt(fila.find("td:eq(3)").text()) || 1; // La cantidad está en la columna 3 para doc venta
      activo.valorUnitario = parseFloat(fila.find("td:eq(4)").text()) || 0; // Si hay valor unitario
    }

    agregarActivoAlDetalle(activo);
  });

  $(document).on("click", ".btnQuitarActivo", function () {
    const filaActual = $(this).closest("tr");
    const activoId = filaActual.data("id");
    const grupoId = filaActual.data("grupo-id");

    if (filaActual.hasClass("activo-grupo-principal")) {
      // Si es la fila principal, eliminar todo el grupo
      Swal.fire({
        title: "¿Eliminar todo el grupo?",
        text: "Se eliminarán todas las unidades de este activo.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar todo",
        cancelButtonColor: "#6c757d",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          if (grupoId) {
            // Eliminar todas las filas del grupo
            $(
              `#tbldetalleactivoreg tbody tr[data-grupo-id='${grupoId}']`
            ).remove();
          } else {
            // Eliminar todas las filas con el mismo ID (método anterior)
            $(`#tbldetalleactivoreg tbody tr[data-id='${activoId}']`).remove();
          }
          NotificacionToast("success", "Grupo eliminado completamente.");
        }
      });
    } else if (grupoId) {
      // Si es una fila hija, eliminar solo esta unidad
      Swal.fire({
        title: "¿Eliminar esta unidad?",
        text: "Solo se eliminará esta unidad específica.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonColor: "#6c757d",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          filaActual.remove();
          // Actualizar los badges del grupo
          actualizarBadgesGrupo(grupoId);
          NotificacionToast("success", "Unidad eliminada.");
        }
      });
    } else {
      // Fila normal sin grupo (método anterior)
      $(`#tbldetalleactivoreg tbody tr[data-id='${activoId}']`).remove();
    }
  });

  // Manejador para el botón "Procesar Cantidad" - Abre el modal
  $(document).on("click", ".btnProcesarCantidad", function () {
    const filaActual = $(this).closest("tr");
    const activoId = filaActual.data("id");
    const activoNombre = filaActual.data("activo-nombre");
    const activoMarca = filaActual.data("activo-marca");
    const tipoDoc = filaActual.data("tipo-doc");
    const cantidad = parseInt(filaActual.find("input.cantidad").val()) || 1;

    if (cantidad <= 1) {
      NotificacionToast(
        "info",
        "La cantidad debe ser mayor a 1 para procesar."
      );
      return;
    }

    // Verificar si ya se procesó este activo
    if (
      $(
        `#tbldetalleactivoreg tbody tr[data-id='${activoId}'][data-procesado='true']`
      ).length > 0
    ) {
      NotificacionToast(
        "warning",
        "Este activo ya ha sido procesado. Elimine las filas generadas primero."
      );
      return;
    }

    // Validar que los campos principales estén llenos
    const ambienteId = filaActual.find("select.ambiente").val();
    const categoriaId = filaActual.find("select.categoria").val();
    const proveedorId = filaActual.find("select.proveedor").val();

    if (!ambienteId || !categoriaId) {
      NotificacionToast(
        "error",
        "Debe seleccionar ambiente y categoría antes de procesar."
      );
      return;
    }

    // Obtener los valores de la fila principal
    const serie = filaActual.find("input[name='serie[]']").val();
    const observaciones = filaActual
      .find("textarea[name='observaciones[]']")
      .val();

    // Configurar el modal con los datos
    $("#modalActivoNombre").text(activoNombre);
    $("#modalActivoMarca").text(activoMarca);
    $("#modalCantidadTotal").val(cantidad);
    $("#modalSerieBase").val(serie);
    $("#modalObservacionesBase").val(observaciones);
    $("#cantidadACrear").text(cantidad);

    // Mostrar información adicional si es documento de venta
    if (tipoDoc === "venta") {
      $("#modalTipoDocumento").html(`
        <div class="alert alert-info">
          <i class="fas fa-file-invoice"></i> <strong>Documento de Venta</strong><br>
          <small>Cantidad definida por el documento: ${cantidad} unidades</small>
        </div>
      `);

      // Mostrar campo de proveedor para documentos de venta
      $("#modalProveedorContainer").show();

      // Inicializar Select2 para el proveedor en el modal si no está inicializado
      if (!$("#modalProveedor").hasClass("select2-hidden-accessible")) {
        $("#modalProveedor").select2({
          dropdownParent: $("#modalProcesarCantidad"),
          minimumInputLength: 2,
          theme: "bootstrap4",
          width: "100%",
          language: {
            inputTooShort: function (args) {
              return "Ingresar más de 2 caracteres para buscar...";
            },
            noResults: function () {
              return "No se encontraron proveedores.";
            },
            searching: function () {
              return "Buscando proveedores...";
            },
          },
          ajax: {
            url: "../../controllers/GestionarActivosController.php?action=comboProveedor",
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (params) {
              return {
                filtro: params.term,
              };
            },
            processResults: function (data) {
              return {
                results: data || [],
              };
            },
            cache: true,
          },
          placeholder: "🔍 Buscar y Seleccionar Proveedor",
          allowClear: false,
        });
      }

      // Cargar el proveedor actual si existe
      if (proveedorId) {
        // Para Select2 con AJAX, necesitamos crear la opción manualmente
        const proveedorTexto = filaActual
          .find("select.proveedor option:selected")
          .text();
        if (proveedorTexto && proveedorTexto !== "") {
          const newOption = new Option(proveedorTexto, proveedorId, true, true);
          $("#modalProveedor").append(newOption).trigger("change");
        }
      }
    } else {
      $("#modalTipoDocumento").html(`
        <div class="alert alert-success">
          <i class="fas fa-file-import"></i> <strong>Documento de Ingreso</strong><br>
          <small>Cantidad personalizable</small>
        </div>
      `);

      // Ocultar campo de proveedor para documentos de ingreso
      $("#modalProveedorContainer").hide();
    }

    // Guardar referencia a la fila actual en el modal
    $("#modalProcesarCantidad").data("filaActual", filaActual);
    $("#modalProcesarCantidad").data("activoId", activoId);
    $("#modalProcesarCantidad").data("activoNombre", activoNombre);
    $("#modalProcesarCantidad").data("activoMarca", activoMarca);

    // Agregar contenedor para tipo de documento si no existe
    if ($("#modalTipoDocumento").length === 0) {
      $("#modalProcesarCantidad .modal-body").prepend(
        '<div id="modalTipoDocumento"></div>'
      );
    }

    // Mostrar el modal
    $("#modalProcesarCantidad").modal("show");
  });

  // Limpiar el modal cuando se cierre
  $("#modalProcesarCantidad").on("hidden.bs.modal", function () {
    // Limpiar el select de proveedor
    $("#modalProveedor").val(null).trigger("change");
    // Limpiar otros campos
    $("#modalSerieBase").val("");
    $("#modalObservacionesBase").val("");
    $("#modalCantidadTotal").val("");
    $("#cantidadACrear").text("0");
  });

  // Manejador para el botón "Confirmar Procesar" del modal
  $(document).on("click", "#btnConfirmarProcesar", function () {
    const filaActual = $("#modalProcesarCantidad").data("filaActual");
    const activoId = $("#modalProcesarCantidad").data("activoId");
    const activoNombre = $("#modalProcesarCantidad").data("activoNombre");
    const activoMarca = $("#modalProcesarCantidad").data("activoMarca");
    const tipoDoc = filaActual.data("tipo-doc") || "ingreso";

    const cantidad = parseInt($("#modalCantidadTotal").val()) || 1;
    const serieBase = $("#modalSerieBase").val().trim();
    const observacionesBase = $("#modalObservacionesBase").val().trim();
    const proveedorModal = $("#modalProveedor").val();

    // Validar serie base
    if (!serieBase) {
      NotificacionToast("error", "Debe ingresar una serie base.");
      return;
    }

    // Validar proveedor para documentos de venta
    if (tipoDoc === "venta" && !proveedorModal) {
      NotificacionToast(
        "error",
        "Debe seleccionar un proveedor para documentos de venta."
      );
      return;
    }

    // Validar series duplicadas
    const validacion = validarSeriesDuplicadas(serieBase);
    if (!validacion.esValida) {
      Swal.fire({
        title: "Serie Duplicada",
        html: `
          <p>La serie base "<strong>${serieBase}</strong>" ya existe en la tabla.</p>
          <p>¿Desea generar una serie única automáticamente?</p>
        `,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        confirmButtonText: "Sí, generar automáticamente",
        cancelButtonColor: "#6c757d",
        cancelButtonText: "No, cambiar manualmente",
      }).then((result) => {
        if (result.isConfirmed) {
          const serieUnica = generarSerieUnica(serieBase);
          $("#modalSerieBase").val(serieUnica);
          NotificacionToast(
            "info",
            `Serie cambiada automáticamente a: ${serieUnica}`
          );
        }
      });
      return;
    }

    // Obtener los valores de la fila principal
    const valor = filaActual.find("input[name='valor[]']").val();
    const ambienteId = filaActual.find("select.ambiente").val();
    const categoriaId = filaActual.find("select.categoria").val();
    // Para documentos de venta, usar el proveedor del modal; para ingreso, usar el de la fila
    const proveedorId =
      tipoDoc === "venta"
        ? proveedorModal
        : filaActual.find("select.proveedor").val();

    // Cerrar el modal
    $("#modalProcesarCantidad").modal("hide");

    // Mostrar loading progresivo
    Swal.fire({
      title: "Procesando Activos",
      html: `
        <div class="procesamiento-container">
          <div class="progress mb-3" style="height: 25px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                 role="progressbar" style="width: 0%" id="progressBar">
              <span class="progress-text">0%</span>
            </div>
          </div>
          <div class="procesamiento-info">
            <p class="mb-2"><i class="fas fa-cogs fa-spin text-info"></i> <span id="procesamientoTexto">Preparando activos...</span></p>
            <small class="text-muted" id="procesamientoDetalle">Configurando ${cantidad} unidades para "${activoNombre}"</small>
          </div>
        </div>
      `,
      allowOutsideClick: false,
      showConfirmButton: false,
      customClass: {
        popup: "procesamiento-popup",
      },
    });

    // Generar un ID único para el grupo
    const grupoId = `grupo_${activoId}_${Date.now()}`;

    // Marcar la fila original como procesada y agregar distintivo
    filaActual.attr("data-procesado", "true");
    filaActual.attr("data-grupo-id", grupoId);
    filaActual.addClass("activo-grupo-principal");
    filaActual.find(".btnProcesarCantidad").hide();
    filaActual.find("input.cantidad").prop("disabled", true).val(1);

    // Actualizar la serie de la fila original y agregar distintivo visual
    filaActual.find("input[name='serie[]']").val(serieBase + "-1");
    filaActual.find("textarea[name='observaciones[]']").val(observacionesBase);

    // Actualizar el proveedor en la fila original si es documento de venta
    if (tipoDoc === "venta" && proveedorModal) {
      // Mantener el select visible pero deshabilitarlo para evitar cambios
      filaActual
        .find("select.proveedor")
        .val(proveedorModal)
        .prop("disabled", true)
        .trigger("change");
    }

    // Agregar distintivo visual a la fila principal
    const distintivoPrincipal = `<span class="badge badge-primary grupo-badge">👑 Principal</span>`;
    filaActual.find("td:eq(1)").html(`${activoNombre} ${distintivoPrincipal}`);

    // Agregar botones de control del grupo a la fila principal
    const btnColapsar =
      cantidad > 2
        ? `<button type='button' class='btn btn-outline-secondary btn-sm btnColapsarGrupo me-1' data-grupo-id='${grupoId}' title="Colapsar/Expandir grupo"><i class='fa fa-chevron-down'></i></button>`
        : "";
    const btnAgregarMas = `<button type='button' class='btn btn-success btn-sm btnAgregarMasUnidades ms-1' data-grupo-id='${grupoId}' title="Agregar más unidades a este grupo"><i class='fa fa-plus'></i> +1</button>`;
    filaActual.find("td:last").html(`
      <div class="btn-group">
        ${btnColapsar}
        ${btnAgregarMas}
        <button type='button' class='btn btn-danger btn-sm btnQuitarActivo' title="Eliminar todo el grupo">
          <i class='fa fa-trash'></i>
        </button>
      </div>
    `);

    // Debug: verificar que el botón se creó
    console.log(
      `Grupo ${grupoId}: cantidad=${cantidad}, botón colapsar creado:`,
      btnColapsar !== ""
    );

    // Función para actualizar el progreso
    function actualizarProgreso(actual, total, texto) {
      const porcentaje = Math.round((actual / total) * 100);
      $("#progressBar").css("width", porcentaje + "%");
      $("#progressBar .progress-text").text(porcentaje + "%");
      $("#procesamientoTexto").text(texto);
      $("#procesamientoDetalle").text(
        `Procesando unidad ${actual} de ${total}`
      );
    }

    // Crear las filas individuales de forma progresiva
    let ultimaFilaInsertada = filaActual;

    // Función recursiva para crear filas con delay y progreso visual
    function crearFilaProgresiva(indice) {
      if (indice >= cantidad) {
        // Todas las filas creadas, finalizar
        finalizarProcesamiento();
        return;
      }

      const i = indice;
      actualizarProgreso(i, cantidad, `Creando activo ${i + 1}/${cantidad}...`);

      const numeroFilas = $("#tbldetalleactivoreg").find("tbody tr").length;
      const selectAmbiente = `<select class='form-control form-control-sm ambiente' name='ambiente[]' id="comboAmbiente${numeroFilas}"></select>`;
      const selectCategoria = `<select class='form-control form-control-sm categoria' name='categoria[]' id="comboCategoria${numeroFilas}"></select>`;
      const inputEstadoActivo = `<input type="text" class="form-control form-control-sm" name="estado_activo[]" value="Operativa" disabled>`;
      const inputCantidad = `<input type="number" class="form-control form-control-sm cantidad" name="cantidad[]" value="1" min="1" disabled>`;

      // Para activos procesados, mostrar el proveedor pero deshabilitado
      const proveedorTexto =
        filaActual.find("select.proveedor option:selected").text() ||
        "No asignado";
      const proveedorDisplay = `<input type="hidden" name="proveedor[]" value="${
        proveedorId || ""
      }"><span class="text-muted small">${proveedorTexto}</span>`;

      const distintivo = `<span class="badge badge-info grupo-badge">📦 ${
        i + 1
      }/${cantidad}</span>`;
      const indentacion = `<span class="grupo-indent">└─</span>`;

      const nuevaFila = `<tr data-id='${activoId}' class='table-info activo-procesado activo-grupo-hijo' data-procesado='true' data-grupo-id='${grupoId}' data-activo-nombre="${activoNombre}" data-activo-marca="${activoMarca}" data-tipo-doc="${tipoDoc}">
                    <td>${activoId}</td>
                    <td>${indentacion} ${activoNombre} ${distintivo}</td>
                    <td>${activoMarca}</td>
                    <td><input type="text" class="form-control form-control-sm" name="serie[]" placeholder="Serie ${
                      i + 1
                    }" value="${serieBase}-${i + 1}"></td>
                    <td>${inputEstadoActivo}</td>
                    <td>${selectAmbiente}</td>
                    <td>${selectCategoria}</td>
                    <td><input type="text" class="form-control form-control-sm" name="valor[]" placeholder="Valor" value="${valor}"></td>
                    <td>${inputCantidad}</td>
                    <td>${proveedorDisplay}</td>
                    <td><textarea class='form-control form-control-sm' name='observaciones[]' rows='1' placeholder='Observaciones'>${observacionesBase}</textarea></td>
                    <td>
                      <button type='button' class='btn btn-danger btn-sm btnQuitarActivo' title="Eliminar solo esta unidad">
                        <i class='fa fa-trash'></i>
                      </button>
                    </td>
                </tr>`;

      // Insertar la nueva fila con animación
      ultimaFilaInsertada.after(nuevaFila);
      ultimaFilaInsertada = ultimaFilaInsertada.next();

      // Efecto visual de aparición
      ultimaFilaInsertada.hide().fadeIn(300);

      // Cargar combos para la nueva fila (solo ambiente y categoría para filas procesadas)
      ListarCombosAmbiente(`comboAmbiente${numeroFilas}`);
      ListarCombosCategoria(`comboCategoria${numeroFilas}`);

      // Establecer los valores seleccionados en los combos
      setTimeout(() => {
        $(`#comboAmbiente${numeroFilas}`).val(ambienteId).trigger("change");
        $(`#comboCategoria${numeroFilas}`).val(categoriaId).trigger("change");
      }, 100);

      // Continuar con la siguiente fila después de un pequeño delay
      setTimeout(() => {
        crearFilaProgresiva(indice + 1);
      }, 300); // 300ms entre cada fila para efecto visual
    }

    // Función para finalizar el procesamiento
    function finalizarProcesamiento() {
      actualizarProgreso(cantidad, cantidad, "¡Procesamiento completado!");

      setTimeout(() => {
        Swal.close();
        NotificacionToast(
          "success",
          `Se han creado ${cantidad} filas individuales para el activo "${activoNombre}".`
        );
        // Actualizar contador total
        actualizarContadorTotal();

        // Auto-colapsar grupos grandes (más de 5 unidades)
        if (cantidad > 5) {
          console.log(
            `Intentando auto-colapsar grupo ${grupoId} con ${cantidad} unidades`
          );
          const btnColapsar = $(
            `button[data-grupo-id='${grupoId}'].btnColapsarGrupo`
          );
          console.log(`Botón colapsar encontrado: ${btnColapsar.length > 0}`);
          if (btnColapsar.length > 0) {
            setTimeout(() => {
              console.log("Ejecutando auto-colapso");
              btnColapsar.click();
              NotificacionToast(
                "info",
                `Grupo auto-colapsado por tener ${cantidad} unidades. Click en 📁 para expandir.`
              );
            }, 500);
          } else {
            console.log("No se encontró el botón colapsar para auto-colapso");
          }
        }
      }, 800);
    }

    // Iniciar el procesamiento progresivo inmediatamente
    setTimeout(() => {
      crearFilaProgresiva(1); // Empezar desde 1 porque 0 ya existe (fila principal)
    }, 200);
  });

  // Actualizar contador cuando cambie la cantidad en el modal
  $(document).on("input", "#modalCantidadTotal", function () {
    const cantidad = parseInt($(this).val()) || 0;
    $("#cantidadACrear").text(cantidad);
  });

  // Validación en tiempo real de series duplicadas
  $(document).on("input", "#modalSerieBase", function () {
    const serieBase = $(this).val().trim();
    const inputElement = $(this);

    if (serieBase.length > 0) {
      const validacion = validarSeriesDuplicadas(serieBase);

      if (!validacion.esValida) {
        inputElement.addClass("is-invalid");
        inputElement.next(".invalid-feedback").remove();
        inputElement.after(`
          <div class="invalid-feedback">
            <i class="fas fa-exclamation-triangle"></i> Esta serie ya existe en la tabla
          </div>
        `);
      } else {
        inputElement.removeClass("is-invalid");
        inputElement.next(".invalid-feedback").remove();
      }
    } else {
      inputElement.removeClass("is-invalid");
      inputElement.next(".invalid-feedback").remove();
    }
  });

  // Manejador para colapsar/expandir grupos
  $(document).on("click", ".btnColapsarGrupo", function () {
    console.log("Botón colapsar clickeado");
    const grupoId = $(this).data("grupo-id");
    const btnIcon = $(this).find("i");
    const filasHijas = $(`tr[data-grupo-id='${grupoId}']`).not(
      ".activo-grupo-principal"
    );

    console.log(
      `Grupo: ${grupoId}, Filas hijas encontradas: ${filasHijas.length}`
    );
    console.log("¿Filas hijas visibles?", filasHijas.is(":visible"));
    console.log("Filas hijas:", filasHijas);

    // Verificar si la primera fila hija está visible
    const primeraFilaHija = filasHijas.first();
    const estaVisible =
      primeraFilaHija.length > 0 ? primeraFilaHija.is(":visible") : false;
    console.log("¿Primera fila hija visible?", estaVisible);

    if (estaVisible) {
      console.log("Colapsando grupo...");
      // Colapsar grupo
      filasHijas.hide();
      btnIcon.removeClass("fa-chevron-down").addClass("fa-chevron-right");
      $(this).attr("title", "Expandir grupo");

      // Agregar indicador de grupo colapsado
      const filaPrincipal = $(
        `tr[data-grupo-id='${grupoId}'].activo-grupo-principal`
      );
      const activoNombre = filaPrincipal.data("activo-nombre");
      const totalUnidades = filasHijas.length + 1;

      const distintivoPrincipal = `<span class="badge badge-warning grupo-badge">📁 Colapsado (${totalUnidades} unidades)</span>`;
      filaPrincipal
        .find("td:eq(1)")
        .html(`${activoNombre} ${distintivoPrincipal}`);
      console.log("Grupo colapsado exitosamente");
    } else {
      console.log("Expandiendo grupo...");
      // Expandir grupo
      filasHijas.show();
      btnIcon.removeClass("fa-chevron-right").addClass("fa-chevron-down");
      $(this).attr("title", "Colapsar grupo");

      // Restaurar badges normales
      actualizarBadgesGrupo(grupoId);
      console.log("Grupo expandido exitosamente");
    }
  });

  // Manejador para agregar más unidades a un grupo existente
  $(document).on("click", ".btnAgregarMasUnidades", function () {
    const grupoId = $(this).data("grupo-id");
    const filaPrincipal = $(
      `tr[data-grupo-id='${grupoId}'].activo-grupo-principal`
    );
    const filasGrupo = $(`tr[data-grupo-id='${grupoId}']`);

    if (filaPrincipal.length === 0) {
      NotificacionToast("error", "No se pudo encontrar el grupo principal.");
      return;
    }

    // Obtener datos del grupo
    const activoId = filaPrincipal.data("id");
    const activoNombre = filaPrincipal.data("activo-nombre");
    const activoMarca = filaPrincipal.data("activo-marca");
    const serieBase = filaPrincipal
      .find("input[name='serie[]']")
      .val()
      .replace("-1", "");
    const valor = filaPrincipal.find("input[name='valor[]']").val();
    const observacionesBase = filaPrincipal
      .find("textarea[name='observaciones[]']")
      .val();
    const ambienteId = filaPrincipal.find("select.ambiente").val();
    const categoriaId = filaPrincipal.find("select.categoria").val();
    const proveedorId = filaPrincipal.find("select.proveedor").val();

    // Calcular el siguiente número de serie
    const cantidadActual = filasGrupo.length;
    const siguienteNumero = cantidadActual + 1;

    Swal.fire({
      title: "Agregar Unidad",
      text: `¿Desea agregar una unidad más al grupo "${activoNombre}"?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#28a745",
      confirmButtonText: "Sí, agregar",
      cancelButtonColor: "#6c757d",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        // Crear nueva fila
        const numeroFilas = $("#tbldetalleactivoreg").find("tbody tr").length;
        const selectAmbiente = `<select class='form-control form-control-sm ambiente' name='ambiente[]' id="comboAmbiente${numeroFilas}"></select>`;
        const selectCategoria = `<select class='form-control form-control-sm categoria' name='categoria[]' id="comboCategoria${numeroFilas}"></select>`;
        // Para unidades adicionales del grupo, mostrar el proveedor heredado
        const proveedorTexto =
          filaPrincipal.find("select.proveedor option:selected").text() ||
          "No asignado";
        const proveedorDisplay = `<input type="hidden" name="proveedor[]" value="${
          proveedorId || ""
        }"><span class="text-muted small">${proveedorTexto}</span>`;
        const inputEstadoActivo = `<input type="text" class="form-control form-control-sm" name="estado_activo[]" value="Operativa" disabled>`;
        const inputCantidad = `<input type="number" class="form-control form-control-sm cantidad" name="cantidad[]" value="1" min="1" disabled>`;

        const distintivo = `<span class="badge badge-info grupo-badge">📦 ${siguienteNumero}/${siguienteNumero}</span>`;
        const indentacion = `<span class="grupo-indent">└─</span>`;

        const nuevaFila = `<tr data-id='${activoId}' class='table-info activo-procesado activo-grupo-hijo' data-procesado='true' data-grupo-id='${grupoId}' data-activo-nombre="${activoNombre}" data-activo-marca="${activoMarca}">
                      <td>${activoId}</td>
                      <td>${indentacion} ${activoNombre} ${distintivo}</td>
                      <td>${activoMarca}</td>
                      <td><input type="text" class="form-control form-control-sm" name="serie[]" placeholder="Serie ${siguienteNumero}" value="${serieBase}-${siguienteNumero}"></td>
                      <td>${inputEstadoActivo}</td>
                      <td>${selectAmbiente}</td>
                      <td>${selectCategoria}</td>
                      <td><input type="text" class="form-control form-control-sm" name="valor[]" placeholder="Valor" value="${valor}"></td>
                      <td>${inputCantidad}</td>
                      <td>${proveedorDisplay}</td>
                      <td><textarea class='form-control form-control-sm' name='observaciones[]' rows='1' placeholder='Observaciones'>${observacionesBase}</textarea></td>
                      <td>
                        <button type='button' class='btn btn-danger btn-sm btnQuitarActivo' title="Eliminar solo esta unidad">
                          <i class='fa fa-trash'></i>
                        </button>
                      </td>
                  </tr>`;

        // Insertar la nueva fila después de la última fila del grupo
        const ultimaFilaGrupo = $(`tr[data-grupo-id='${grupoId}']`).last();
        ultimaFilaGrupo.after(nuevaFila);

        // Cargar combos para la nueva fila (solo ambiente y categoría)
        ListarCombosAmbiente(`comboAmbiente${numeroFilas}`);
        ListarCombosCategoria(`comboCategoria${numeroFilas}`);

        // Establecer los valores seleccionados en los combos
        setTimeout(() => {
          $(`#comboAmbiente${numeroFilas}`).val(ambienteId).trigger("change");
          $(`#comboCategoria${numeroFilas}`).val(categoriaId).trigger("change");
        }, 500);

        // Actualizar los badges de todas las filas del grupo
        actualizarBadgesGrupo(grupoId);

        NotificacionToast(
          "success",
          `Se agregó una unidad más al grupo "${activoNombre}".`
        );
      }
    });
  });

  // Función para actualizar los badges de numeración de un grupo
  function actualizarBadgesGrupo(grupoId) {
    const filasGrupo = $(`tr[data-grupo-id='${grupoId}']`);
    const total = filasGrupo.length;
    const filaPrincipal = filasGrupo.filter(".activo-grupo-principal");

    filasGrupo.each(function (index) {
      const fila = $(this);
      const activoNombre = fila.data("activo-nombre");

      if (fila.hasClass("activo-grupo-principal")) {
        // Fila principal - actualizar botones si es necesario
        const distintivoPrincipal = `<span class="badge badge-primary grupo-badge">👑 Principal (${total} unidades)</span>`;
        fila.find("td:eq(1)").html(`${activoNombre} ${distintivoPrincipal}`);

        // Actualizar botones de control
        const btnColapsar =
          total > 2
            ? `<button type='button' class='btn btn-outline-secondary btn-sm btnColapsarGrupo me-1' data-grupo-id='${grupoId}' title="Colapsar grupo"><i class='fa fa-chevron-down'></i></button>`
            : "";
        const btnAgregarMas = `<button type='button' class='btn btn-success btn-sm btnAgregarMasUnidades ms-1' data-grupo-id='${grupoId}' title="Agregar más unidades a este grupo"><i class='fa fa-plus'></i> +1</button>`;

        fila.find("td:last").html(`
          <div class="btn-group">
            ${btnColapsar}
            ${btnAgregarMas}
            <button type='button' class='btn btn-danger btn-sm btnQuitarActivo' title="Eliminar todo el grupo">
              <i class='fa fa-trash'></i>
            </button>
          </div>
        `);
      } else {
        // Filas hijas
        const distintivo = `<span class="badge badge-info grupo-badge">📦 ${
          index + 1
        }/${total}</span>`;
        const indentacion = `<span class="grupo-indent">└─</span>`;
        fila
          .find("td:eq(1)")
          .html(`${indentacion} ${activoNombre} ${distintivo}`);
      }
    });

    // Actualizar contador total después de cambios en grupo
    actualizarContadorTotal();
  }

  // Función para actualizar contador total en tiempo real
  function actualizarContadorTotal() {
    const totalFilas = $("#tbldetalleactivoreg tbody tr").length;
    const totalGrupos =
      $("#tbldetalleactivoreg tbody tr.activo-grupo-principal").length +
      $("#tbldetalleactivoreg tbody tr:not([data-grupo-id])").length;

    $("#CantRegistros").html(`
      <div class="contador-detalle">
        <span class="badge badge-success">${totalFilas} Activos</span>
        <span class="badge badge-info">${totalGrupos} Grupos</span>
      </div>
    `);

    // Actualizar también el título de la sección
    const tituloDetalle =
      totalFilas > 0
        ? `Detalles <small class="text-muted">(${totalFilas} activos en ${totalGrupos} grupos)</small>`
        : "Detalles";

    $("#tbldetalleactivoreg")
      .closest(".card")
      .find('h5:contains("Detalles")')
      .html(`<i class="fas fa-list"></i> ${tituloDetalle}`);
  }

  // Función para validar series duplicadas
  function validarSeriesDuplicadas(serieBase, grupoId = null) {
    const seriesExistentes = [];

    $("#tbldetalleactivoreg tbody tr").each(function () {
      const fila = $(this);
      const serie = fila.find("input[name='serie[]']").val();
      const filaGrupoId = fila.data("grupo-id");

      // Solo validar contra series de otros grupos o activos sin grupo
      if (serie && (!grupoId || filaGrupoId !== grupoId)) {
        seriesExistentes.push(serie.toLowerCase());
      }
    });

    return {
      esValida: !seriesExistentes.includes(serieBase.toLowerCase()),
      seriesExistentes: seriesExistentes,
    };
  }

  // Función para generar serie única automáticamente
  function generarSerieUnica(serieBase) {
    let contador = 1;
    let serieNueva = serieBase;

    while (!validarSeriesDuplicadas(serieNueva).esValida) {
      contador++;
      serieNueva = `${serieBase}-V${contador}`;
    }

    return serieNueva;
  }

  $("#btnGuardarActivo").on("click", function (e) {
    e.preventDefault();

    if ($("#tbldetalleactivoreg tbody tr").length === 0) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Debe agregar al menos un activo al detalle",
      });
      return;
    }

    let activos = [];
    const tipoDocumento = $("#tipoDocumento").val();
    const documento = $("#inputDocumento").val();

    $("#tbldetalleactivoreg tbody tr").each(function () {
      let row = $(this);
      let cantidad = parseInt(row.find("input.cantidad").val()) || 1;
      let tipoDocFila = row.data("tipo-doc");
      // Obtener proveedor del select o del campo hidden (para filas procesadas)
      let proveedor =
        row.find("select.proveedor").val() ||
        row.find("input[name='proveedor[]']").val();

      // Debug: log para verificar los datos
      console.log("Fila:", {
        id: row.find("td:eq(0)").text(),
        nombre: row.find("td:eq(1)").text(),
        cantidad: cantidad,
        tipoDoc: tipoDocFila,
        proveedor: proveedor,
        ambiente: row.find("select.ambiente").val(),
        categoria: row.find("select.categoria").val(),
      });

      // Validar proveedor obligatorio para documentos de venta
      if (
        (tipoDocFila === "venta" || tipoDocumento === "venta") &&
        !proveedor
      ) {
        Swal.fire({
          icon: "error",
          title: "Proveedor Requerido",
          text: `El proveedor es obligatorio para documentos de venta. Fila: ${row
            .find("td:eq(1)")
            .text()}`,
        });
        return false;
      }

      // Para documentos de venta, crear múltiples activos individuales según la cantidad
      // Para documentos de ingreso, crear múltiples activos individuales como antes
      if (tipoDocFila === "venta" || tipoDocumento === "venta") {
        // Para documentos de venta: crear múltiples activos individuales
        for (let i = 0; i < cantidad; i++) {
          let serieActual = row.find("input[name='serie[]']").val() || null;

          // Si hay cantidad > 1, agregar sufijo a la serie
          if (cantidad > 1 && serieActual) {
            serieActual = serieActual + "-" + (i + 1);
          }

          let activo = {
            IdArticulo: parseInt(row.find("td:eq(0)").text()) || null,
            Serie: serieActual,
            IdAmbiente: parseInt(row.find("select.ambiente").val()) || null,
            IdCategoria: parseInt(row.find("select.categoria").val()) || null,
            ValorAdquisicion:
              parseFloat(row.find("input[name='valor[]']").val()) || 0,
            IdProveedor: proveedor || null,
            Observaciones:
              row.find("textarea[name='observaciones[]']").val() || "",
            IdEstado: 1, // Estado por defecto: Operativo
            Garantia: 0, // Por defecto sin garantía
            UserMod: userMod,
            Accion: 1, // 1 = Insertar
            VidaUtil: 3, // Vida útil por defecto
            FechaAdquisicion: new Date().toISOString().split("T")[0], // Fecha actual
            Cantidad: 1, // Cada iteración es 1 activo individual
            IdDocVenta: parseInt(documento) || null,
            IdDocIngresoAlm: null,
          };

          activos.push(activo);
        }
      } else {
        // Para documentos de ingreso: crear múltiples activos individuales
        for (let i = 0; i < cantidad; i++) {
          let serieActual = row.find("input[name='serie[]']").val() || null;

          // Si hay cantidad > 1, agregar sufijo a la serie
          if (cantidad > 1 && serieActual) {
            serieActual = serieActual + "-" + (i + 1);
          }

          let activo = {
            IdArticulo: parseInt(row.find("td:eq(0)").text()) || null,
            Serie: serieActual,
            IdAmbiente: parseInt(row.find("select.ambiente").val()) || null,
            IdCategoria: parseInt(row.find("select.categoria").val()) || null,
            ValorAdquisicion:
              parseFloat(row.find("input[name='valor[]']").val()) || 0,
            IdProveedor: proveedor || null,
            Observaciones:
              row.find("textarea[name='observaciones[]']").val() || "",
            IdEstado: 1, // Estado por defecto: Operativo
            Garantia: 0, // Por defecto sin garantía
            UserMod: userMod,
            Accion: 1, // 1 = Insertar
            VidaUtil: 3, // Vida útil por defecto
            FechaAdquisicion: new Date().toISOString().split("T")[0], // Fecha actual
            Cantidad: 1, // Cada iteración es 1 activo
            IdDocIngresoAlm: parseInt(documento) || null,
            IdDocVenta: null,
          };

          activos.push(activo);
        }
      }
    });

    // Validar que todos los campos requeridos estén presentes
    let activosValidos = activos.every((activo) => {
      // Validación básica
      const validacionBasica =
        activo.IdArticulo &&
        activo.IdAmbiente &&
        activo.IdCategoria &&
        activo.Cantidad > 0;

      // Validación de documento (debe tener uno u otro)
      const tieneDocumento = activo.IdDocIngresoAlm || activo.IdDocVenta;

      // Validación de proveedor para documentos de venta
      const proveedorValido = activo.IdDocVenta ? activo.IdProveedor : true;

      return validacionBasica && tieneDocumento && proveedorValido;
    });

    if (!activosValidos) {
      // Identificar qué campos faltan
      let errores = [];
      activos.forEach((activo, index) => {
        if (!activo.IdArticulo)
          errores.push(`Fila ${index + 1}: Falta artículo`);
        if (!activo.IdAmbiente)
          errores.push(`Fila ${index + 1}: Falta ambiente`);
        if (!activo.IdCategoria)
          errores.push(`Fila ${index + 1}: Falta categoría`);
        if (!activo.IdDocIngresoAlm && !activo.IdDocVenta)
          errores.push(`Fila ${index + 1}: Falta documento`);
        if (activo.IdDocVenta && !activo.IdProveedor)
          errores.push(
            `Fila ${index + 1}: Falta proveedor (obligatorio para doc. venta)`
          );
      });

      Swal.fire({
        icon: "error",
        title: "Campos Requeridos",
        html:
          "Se encontraron los siguientes errores:<br><br>" +
          errores.join("<br>"),
      });
      return;
    }

    Swal.fire({
      title: "Procesando",
      text: "Registrando activos...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Determinar qué función usar según el tipo de documento
    const tipoDocActual = $("#tipoDocumento").val();
    const action =
      tipoDocActual === "venta"
        ? "GuardarActivosDesdeDocumentoVenta"
        : "GuardarActivosDesdeDocumentoIngreso";

    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=" + action,
      type: "POST",
      data: {
        action: action,
        activos: JSON.stringify(activos),
      },
      dataType: "json",
      success: function (res) {
        if (res.status) {
          Swal.fire({
            icon: "success",
            title: "Éxito",
            text: res.message,
            timer: 1500,
          }).then(() => {
            $("#tbldetalleactivoreg tbody").empty();
            $("#divregistroActivo").hide();
            $("#divlistadoactivos").show();
            $("#divtblactivos").show();
            $("#tblRegistros").show();
            listarActivosTable();
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: res.message,
          });
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("Error en la petición:", jqXHR.responseText);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error al registrar los activos: " + errorThrown,
        });
      },
    });
  });

  $("#btnGuardarActivosManuales").on("click", function (e) {
    e.preventDefault();

    const activos = [];
    let totalActivosPreview = 0;

    // Primero, recopilar activos de las tablas de preview (activos procesados)
    $("[id^='tblPreviewActivos_'] tbody tr").each(function () {
      const fila = $(this);
      const formId = fila.data("form-id");
      const form = $(`[data-form-number='${formId}']`);

      if (form.length > 0) {
        const activo = {
          Nombre: form.find("input[name='nombre[]']").val(),
          Descripcion: form.find("textarea[name='Descripcion[]']").val(),
          IdEstado: form.find("select[name='Estado[]']").val(),
          Garantia: 0,
          IdResponsable: form.find("select[name='Responsable[]']").val(),
          IdProveedor: form.find("select[name='Proveedor[]']").val(),
          IdEmpresa: null,
          IdSucursal: null,
          IdAmbiente: form.find("select[name='Ambiente[]']").val(),
          IdCategoria: form.find("select[name='Categoria[]']").val(),
          Serie: fila.find(".serie-manual").val(), // Serie única de la tabla
          Observaciones: form.find("textarea[name='Observaciones[]']").val(),
          ValorAdquisicion: parseFloat(
            form.find("input[name='ValorAdquisicion[]']").val()
          ),
          FechaAdquisicion: form.find("input[name='fechaAdquisicion[]']").val(),
          Cantidad: 1, // Cada fila de preview es 1 activo individual
        };
        activos.push(activo);
        totalActivosPreview++;
      }
    });

    // Luego, recopilar activos de formularios no procesados (cantidad = 1)
    $("#activosContainer .activo-manual-form").each(function () {
      const form = $(this);
      const formId = form.data("form-number");
      const tablaPreview = $(`#tblPreviewActivos_${formId} tbody tr`);

      // Solo agregar si no tiene tabla de preview (no fue procesado)
      if (tablaPreview.length === 0) {
        const cantidad =
          parseInt(form.find("input[name='Cantidad[]']").val()) || 1;

        if (cantidad === 1) {
          const activo = {
            Nombre: form.find("input[name='nombre[]']").val(),
            Descripcion: form.find("textarea[name='Descripcion[]']").val(),
            IdEstado: form.find("select[name='Estado[]']").val(),
            Garantia: 0,
            IdResponsable: form.find("select[name='Responsable[]']").val(),
            IdProveedor: form.find("select[name='Proveedor[]']").val(),
            IdEmpresa: null,
            IdSucursal: null,
            IdAmbiente: form.find("select[name='Ambiente[]']").val(),
            IdCategoria: form.find("select[name='Categoria[]']").val(),
            Serie: form.find("input[name='serie[]']").val(),
            Observaciones: form.find("textarea[name='Observaciones[]']").val(),
            ValorAdquisicion: parseFloat(
              form.find("input[name='ValorAdquisicion[]']").val()
            ),
            FechaAdquisicion: form
              .find("input[name='fechaAdquisicion[]']")
              .val(),
            Cantidad: 1,
          };
          activos.push(activo);
        } else {
          NotificacionToast(
            "warning",
            `El formulario #${formId} tiene cantidad > 1. Use "Procesar Activo" primero.`
          );
          return false;
        }
      }
    });

    if (activos.length === 0) {
      Swal.fire({
        icon: "warning",
        title: "Sin Activos",
        text: "No hay activos para guardar. Agregue al menos un activo o procese los formularios con cantidad > 1.",
      });
      return;
    }

    // Validar que todos los activos tengan los campos requeridos
    const activosValidos = activos.every((activo) => {
      return (
        activo.Nombre &&
        activo.Serie &&
        activo.IdEstado &&
        activo.IdCategoria &&
        activo.IdResponsable &&
        activo.ValorAdquisicion > 0
      );
    });

    if (!activosValidos) {
      Swal.fire({
        icon: "error",
        title: "Datos Incompletos",
        text: "Todos los activos deben tener nombre, serie, estado, categoría, responsable y valor de adquisición.",
      });
      return;
    }

    // Mostrar confirmación con resumen
    Swal.fire({
      title: "Confirmar Guardado",
      html: `
        <div class="text-left">
          <p><strong>Total de activos a guardar:</strong> ${activos.length}</p>
          <p><strong>Activos procesados:</strong> ${totalActivosPreview}</p>
          <p><strong>Activos simples:</strong> ${
            activos.length - totalActivosPreview
          }</p>
          <hr>
          <p class="text-info"><i class="fas fa-info-circle"></i> ¿Desea proceder con el guardado?</p>
        </div>
      `,
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#28a745",
      confirmButtonText: "Sí, guardar",
      cancelButtonColor: "#6c757d",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        guardarActivosManuales(activos);
      }
    });
  });

  // Función separada para guardar activos manuales
  function guardarActivosManuales(activos) {
    Swal.fire({
      title: "Procesando",
      text: "Registrando activos...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=GuardarActivosManual",
      type: "POST",
      data: JSON.stringify({ activos: activos }),
      contentType: "application/json",
      dataType: "json",
      success: function (res) {
        if (res.status) {
          Swal.fire({
            icon: "success",
            title: "Éxito",
            text: res.message,
            timer: 2000,
          }).then(() => {
            // Limpiar todo
            $("#activosContainer").empty();
            activoFormCount = 0;

            // Volver a la vista principal
            $("#divRegistroManualActivoMultiple").hide();
            $("#divlistadoactivos").show();
            $("#divtblactivos").show();
            $("#tblRegistros").show();

            // Recargar tabla principal
            listarActivosTable();

            NotificacionToast(
              "success",
              "Activos guardados correctamente. Regresando a la lista principal."
            );
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: res.message,
          });
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("Error en la petición:", jqXHR.responseText);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error al registrar los activos: " + errorThrown,
        });
      },
    });
  }

  $(document).on("click", ".btnEditarActivo", function () {
    const fila = $(this).closest("tr");
    const datos =
      $(fila).closest("table").attr("id") === "modalDetallesActivo"
        ? $("#modalDetallesActivo").DataTable().row(fila).data()
        : $("#tblTodosActivos").DataTable().row(fila).data();

    if (!datos) {
      Swal.fire(
        "Error",
        "No se pudo obtener la información del activo.",
        "error"
      );
      return;
    }

    $("#frmEditarActivo").data("idArticulo", datos.idArticulo);

    $("#tituloModalActualizarActivo").html(
      '<i class="fa fa-edit"></i> Editar Activo'
    );

    // Carga los combos y luego los datos del activo
    cargarCombosModalActualizarActivo(() => {
      $.ajax({
        url: "../../controllers/GestionarActivosController.php?action=obtenerActivoPorId",
        type: "POST",
        data: { idActivo: datos.idActivo },
        dataType: "json",
        success: (res) => {
          if (res.status) {
            let data = res.data;
            console.log("Datos del activo:", data);

            // Cargar datos básicos
            $("#IdActivoEditar").val(data.idActivo);
            $("#IdActivo").val(data.idActivo);
            $("#CodigoActivo").val(data.codigo);
            $("#SerieActivo").val(data.serie);
            $("#DocIngresoAlmacen").val(data.DocIngresoAlmacen);
            $("#IdArticulo").val(data.idArticulo);
            $("#nombreArticulo").val(data.NombreActivoVisible);
            $("#marca").val(data.Marca);
            $("#fechaAdquisicion").val(data.fechaAdquisicion);
            $("#Garantia").prop("checked", data.garantia == 1);
            $("#Observaciones").val(data.observaciones);
            $("#VidaUtil").val(data.vidaUtil);
            $("#ValorAdquisicion").val(data.valorAdquisicion);

            // Asignar valores a los combos
            $("#IdEstado").val(data.idEstado).trigger("change");
            $("#IdAmbiente").val(data.idAmbiente).trigger("change");
            $("#IdCategoria")
              .val(data.idCategoria)
              .trigger("change")
              .prop("disabled", true);

            // $("#Cantidad")
            //   .val(data.cantidad)
            //   .trigger("change")
            //   .prop("disabled", true);
            // Mostrar el modal
            $("#divModalActualizarActivo").modal({
              backdrop: "static",
              keyboard: false,
            });
          } else {
            Swal.fire(
              "Editar Activo",
              "No se pudo obtener el activo: " + res.message,
              "warning"
            );
          }
        },
        error: (xhr, status, error) => {
          Swal.fire(
            "Editar Activo",
            "Error al obtener activo: " + error,
            "error"
          );
        },
      });
    });
  });

  // Manejador para el botón de Asignar Responsable
  $(document).on("click", ".btnAsignarResponsable", function () {
    const idActivo = $(this).data("idActivo");

    if (!idActivo) {
      Swal.fire(
        "Error",
        "No se pudo obtener la información del activo.",
        "error"
      );
      return;
    }

    // Verificar si ya tiene un responsable
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=verificarResponsable",
      type: "POST",
      data: { idActivo: idActivo },
      dataType: "json",
      success: function (res) {
        if (res.status) {
          if (res.existe) {
            Swal.fire({
              title: "Advertencia",
              text: "Este activo ya tiene un responsable asignado. La asignación de un nuevo responsable solo debe realizarse a través de un movimiento.",
              icon: "warning",
              confirmButtonText: "Entendido",
            });
            return;
          }

          // Si no tiene responsable, continuar con el proceso normal
          $("#frmAsignarResponsable").data("idActivo", idActivo);

          $.ajax({
            url: "../../controllers/GestionarActivosController.php?action=combos",
            type: "POST",
            dataType: "json",
            success: function (res) {
              if (res.status) {
                $("#Responsable").html(res.data.responsable).trigger("change");
                $("#Responsable").select2({
                  theme: "bootstrap4",
                  dropdownParent: $("#modalAsignarResponsable .modal-body"),
                  width: "100%",
                });
                // Mostrar el modal después de cargar el combo
                $("#modalAsignarResponsable").modal("show");
              } else {
                Swal.fire("Error", res.message, "error");
              }
            },
            error: function (xhr, status, error) {
              Swal.fire(
                "Error",
                "Error al cargar los combos: " + error,
                "error"
              );
            },
          });
        } else {
          Swal.fire(
            "Error",
            res.message || "Error al verificar el responsable",
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        Swal.fire(
          "Error",
          "Error al verificar el responsable: " + error,
          "error"
        );
      },
    });
  });

  // Manejador para el formulario de asignación de responsable
  $("#frmAsignarResponsable").on("submit", function (e) {
    e.preventDefault();

    const idActivo = $(this).data("idActivo");
    const responsable = $("#Responsable").val();

    if (!responsable) {
      Swal.fire("Error", "Debe seleccionar un responsable", "error");
      return;
    }

    // Mostrar loading
    Swal.fire({
      title: "Procesando",
      text: "Asignando el responsable al activo...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    console.log("Datos a enviar:", {
      idActivo: idActivo,
      idResponsable: responsable,
      userMod: userMod,
    });

    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=asignarResponsable",
      type: "POST",
      data: {
        IdActivo: idActivo,
        IdResponsable: responsable,
        UserMod: userMod,
      },
      dataType: "json",
      success: function (res) {
        console.log("Respuesta del servidor:", res);
        if (res.status) {
          Swal.fire({
            title: "Éxito",
            text: res.message,
            timer: 1500,
          }).then(() => {
            $("#modalAsignarResponsable").modal("hide");
            $("#frmAsignarResponsable")[0].reset();
            listarActivosTable();
          });
        } else {
          Swal.fire(
            "Error",
            res.message || "Error al asignar el responsable",
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la petición:", error);
        Swal.fire("Error", "Error al procesar la solicitud: " + error, "error");
      },
    });
  });

  $("#Responsable").select2({
    theme: "bootstrap4",
    dropdownParent: $("#modalAsignarResponsable .modal-body"),
    width: "100%",
  });

  // Manejador para el botón de Ver (detalles completos del activo)
  $(document).on("click", ".btnVerDetalles", function () {
    const fila = $(this).closest("tr");
    const datos =
      $(fila).closest("table").attr("id") === "tblRegistros"
        ? $("#tblRegistros").DataTable().row(fila).data()
        : $("#tblTodosActivos").DataTable().row(fila).data();

    if (!datos) {
      Swal.fire(
        "Error",
        "No se pudo obtener la información del activo.",
        "error"
      );
      return;
    }

    // Lógica para mostrar los detalles completos del activo
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=obtenerActivoPorId",
      type: "POST",
      data: { idActivo: datos.idActivo },
      dataType: "json",
      success: function (res) {
        if (res.status && res.data) {
          let activo = res.data;
          // Eliminar modal anterior si existe
          $("#modalDetallesActivo").remove();
          // Agregar nuevo modal al body con el diseño mejorado
          let modalHtml = `
<div class="modal fade" id="modalDetallesActivo" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius: 20px; box-shadow: 0 25px 50px rgba(0,0,0,0.08);">
            
            <!-- Header del Modal -->
            <div class="modal-header position-relative overflow-hidden border-0 p-3 bg-primary" style=" border-radius: 20px 20px 0 0;">
                
                <div class="d-flex align-items-center text-white w-100 position-relative">
                    <div class="me-3 p-3 rounded-circle" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                        <i class="fas fa-box-open fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="modal-title mb-1 fw-bold" id="modalDetallesLabel">Detalles del Activo</h4>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge px-3 py-2 rounded-pill" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                                <i class="fas fa-barcode me-1"></i>
                                ${activo.codigo}
                            </span>
                            <span class="badge bg-white text-cyan-600 px-3 py-2 rounded-pill fw-semibold">
                                <i class="fas fa-check-circle me-1"></i>
                                ${activo.Estado}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Body del Modal -->
            <div class="modal-body p-0" style="background: #f8fafc;">
                
                <!-- Tarjeta Principal del Activo -->
                <div class="container-fluid p-4">
                    <div class="card border-0 shadow-sm mb-2 bg-success" style="border-radius: 16px;">
                        <div class="card-body text-white p-3">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-2 fw-bold">${activo.NombreActivo}</h5>
                                    <p class="mb-0 opacity-90">
                                        <i class="fas fa-tag me-2"></i> ${activo.Categoria}
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <div class="d-inline-block px-4 py-2 rounded-pill" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                                        <i class="fas fa-dollar-sign me-1"></i>
                                        <span class="fw-bold fs-5">${activo.valorAdquisicion}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-4">
                        
                        <!-- Columna Izquierda - Información General -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px;">
                                <div class="card-header border-0 py-2" style="background: linear-gradient( #d1fae5 100%); border-radius: 16px 16px 0 0;">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 p-2 rounded-circle">
                                            <i class="fas fa-info-circle text-emerald-600"></i>
                                        </div>
                                        <h6 class="mb-0 fw-bold text-emerald-700">Información General</h6>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row g-3">
                                        
                                        <!-- Fila 1: ID y Código -->
                                        <div class="col-sm-6">
                                            <div class="info-card p-2 rounded-3 h-100" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">ID Activo</label>
                                                <div class="fw-semibold text-slate-700">${activo.idActivo}</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="info-card p-2 rounded-3 h-100" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">Código</label>
                                                <div class="fw-bold text-slate-700">${activo.codigo}</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Fila 2: Serie y Estado -->
                                        <div class="col-sm-6">
                                            <div class="info-card p-2 rounded-3 h-100" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">Serie</label>
                                                <div class="fw-bold text-slate-700">${activo.Serie}</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="info-card p-2 rounded-3 h-100 text" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">Estado</label>
                                                <span class="badge bg-emerald-500 text-white px-3 py-2 rounded-pill">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    ${activo.Estado}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Fila 3: Nombre del Activo -->
                                        <div class="col-12">
                                            <div class="info-card p-2 rounded-3" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); border-left: 4px solid #0d9488;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">Nombre del Activo</label>
                                                <div class="fw-bold text-slate-700 fs-6">${activo.NombreActivo}</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Fila 4: Marca y Categoría -->
                                        <div class="col-sm-6">
                                            <div class="info-card p-2 rounded-3 h-100" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">Marca</label>
                                                <div class="fw-bold text-slate-700">${activo.Marca}</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="info-card p-2 rounded-3 h-100" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">Categoría</label>
                                                <div class="fw-bold text-slate-700">${activo.Categoria}</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Fila 5: Valor y Fecha -->
                                        <div class="col-sm-6">
                                            <div class="info-card p-2 rounded-3 h-100" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">Valor Adquisición</label>
                                                <div class="fw-bold text-emerald-600 fs-6">
                                                    <i class="fas fa-hand-holding-dollar me-1 text-success-500"></i>
                                                    ${activo.valorAdquisicion}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="info-card p-2 rounded-3 h-100" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">Fecha Adquisición</label>
                                                <div class="fw-bold text-slate-700">
                                                    <i class="fas fa-calendar-alt me-2 text-success-500"></i>
                                                    ${activo.fechaAdquisicion}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Fila 6: Responsable y Ambiente -->
                                        <div class="col-sm-6">
                                            <div class="info-card p-2 rounded-3 h-100" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">Responsable</label>
                                                <div class="fw-bold text-slate-700">
                                                    <i class="fas fa-user me-2 text-success-500"></i>
                                                    ${activo.Responsable}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                           <div class="info-card p-2 rounded-3 h-100" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">Ambiente</label>
                                                <div class="fw-bold text-slate-700">
                                                    <i class="fas fa-map-marker-alt me-2 text-success-500"></i>
                                                    ${activo.Ambiente}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Fila 7: Proveedor -->
                                        <div class="col-sm-6">
                                            <div class="info-card p-2 rounded-3 h-100" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label small mb-1 fw-bold text-uppercase">Proveedor</label>
                                                <div class="fw-bold text-slate-700">
                                                    <i class="fas fa-building me-2 text-success-500"></i>
                                                    ${activo.RazonSocial}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Fila 8: Observaciones -->
                                        <div class="col-12">
                                            <div class="info-card p-2 rounded-3 h-100" style="background: #f0fdfa; border-left: 4px solid #28A745;">
                                                <label class="form-label text-dark small mb-2 fw-semibold text-uppercase">
                                                    <i class="fas fa-sticky-note me-1"></i>
                                                    Observaciones
                                                </label>
                                                <div class="fw-semibold text-slate-600">${activo.Observaciones}</div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna Derecha - Componentes y Eventos -->
                        <div class="col-lg-6">
                            <!-- Últimos Eventos del Activo -->
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                                <div class="card-header border-0 py-3" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 16px 16px 0 0;">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 p-2 rounded-circle" style="background: rgba(245, 158, 11, 0.1);">
                                            <i class="fas fa-history text-amber-600"></i>
                                        </div>
                                        <h6 class="mb-0 fw-bold text-amber-700">Últimos Eventos</h6>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div id="ultimosEventosActivo">
                                        <div class="d-flex align-items-center justify-content-center py-3">
                                            <div class="text-center">
                                                <div class="spinner-border text-amber-500 mb-2" role="status" style="width: 2rem; height: 2rem;">
                                                    <span class="visually-hidden">Cargando...</span>
                                                </div>
                                                <p class="text-dark mb-0 fw-semibold small">Cargando eventos...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Componentes del Activo -->
                            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                                <div class="card-header border-0 py-3" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 16px 16px 0 0;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 p-2 rounded-circle" style="background: rgba(6, 182, 212, 0.1);">
                                                <i class="fas fa-puzzle-piece text-cyan-600"></i>
                                            </div>
                                            <h6 class="mb-0 fw-bold text-cyan-700">Componentes</h6>
                                        </div>
                                        <span class="badge bg-cyan-500 text-white px-2 py-1 rounded-pill small">
                                            <i class="fas fa-cogs me-1"></i>
                                            Activo
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="componentesActivo" class="p-3">
                                        <div class="d-flex align-items-center justify-content-center py-3">
                                            <div class="text-center">
                                                <div class="spinner-border text-cyan-500 mb-2" role="status" style="width: 2rem; height: 2rem;">
                                                    <span class="visually-hidden">Cargando...</span>
                                                </div>
                                                <p class="text-dark mb-0 fw-semibold small">Cargando componentes...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            
            <!-- Footer del Modal -->
            <div class="modal-footer border-0 p-4" style="background: #f8fafc; border-radius: 0 0 20px 20px;">
                <div class="d-flex flex-wrap gap-3 w-100 justify-content-center">
                    <button type="button" class="btn btn-outline-cyan btnEditarDesdeModal px-4 py-2 rounded-pill shadow-sm m-1" data-id-activo="${activo.idActivo}" style="min-width: 120px; border-color: #06b6d4; color: #0891b2;">
                        <i class="fas fa-edit m-2"></i>Editar
                    </button>
                    <button type="button" class="btn btn-outline-emerald btnImprimirDesdeModal px-4 py-2 rounded-pill shadow-sm m-1" data-id-activo="${activo.idActivo}" style="min-width: 120px; border-color: #10b981; color: #059669;">
                        <i class="fas fa-print m-2"></i>Imprimir
                    </button>
                    <button type="button" class="btn btn-cyan btnAsignarResponsable px-4 py-2 rounded-pill shadow-sm m-1" data-id-activo="${activo.idActivo}" style="min-width: 140px; background-color: #06b6d4; border-color: #06b6d4; color: white;">
                        <i class="fas fa-user-edit m-2"></i>Asignar
                    </button>
                    <button type="button" class="btn btn-outline-slate btnDarBajaDesdeModal px-4 py-2 rounded-pill shadow-sm m-1" data-id-activo="${activo.idActivo}" style="min-width: 120px; border-color: #64748b; color: #475569;">
                        <i class="fas fa-trash-alt m-2"></i>Dar de Baja
                    </button>
                    <button type="button" class="btn btn-outline-slate px-4 py-2 rounded-pill shadow-sm m-1 btnCerrarModal" style="min-width: 100px; border-color: #64748b; color: #475569;">
                        <i class="fas fa-times m-2"></i>Cerrar
                    </button>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
/* Paleta de colores minimalista verde/celeste */
.text-emerald-600 { color: #059669 !important; }
.text-emerald-700 { color: #047857 !important; }
.text-emerald-500 { color: #10b981 !important; }
.text-cyan-500 { color: #06b6d4 !important; }
.text-cyan-600 { color: #0891b2 !important; }
.text-cyan-700 { color: #0e7490 !important; }
.text-amber-500 { color: #f59e0b !important; }
.text-amber-600 { color: #d97706 !important; }
.text-amber-700 { color: #b45309 !important; }
.text-teal-500 { color: #14b8a6 !important; }
.text-dark { color: #64748b !important; }
.text-slate-600 { color: #475569 !important; }
.text-slate-700 { color: #334155 !important; }

.bg-emerald-500 { background-color: #10b981 !important; }
.bg-cyan-500 { background-color: #06b6d4 !important; }
.bg-amber-500 { background-color: #f59e0b !important; }

/* Animaciones suaves */
.info-card {
    transition: all 0.3s ease;
    cursor: default;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(6, 182, 212, 0.1);
}

.btn {
    transition: all 0.3s ease;
    font-weight: 600;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.btn-outline-cyan:hover {
    background-color: #06b6d4;
    border-color: #06b6d4;
    color: white;
}

.btn-outline-emerald:hover {
    background-color: #10b981;
    border-color: #10b981;
    color: white;
}

.btn-outline-slate:hover {
    background-color: #64748b;
    border-color: #64748b;
    color: white;
}

.modal-content {
    animation: modalSlideIn 0.4s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Spinner personalizado */
.spinner-border {
    border-width: 0.25em;
}

.text-cyan-500.spinner-border {
    border-color: #a7f3d0;
    border-right-color: #06b6d4;
}

.text-amber-500.spinner-border {
    border-color: #fde68a;
    border-right-color: #f59e0b;
}

.text-green-600 { color: #16a34a !important; }
.text-purple-600 { color: #9333ea !important; }
.text-red-600 { color: #dc2626 !important; }

/* Responsive */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .btn {
        min-width: auto !important;
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>`;
          $("body").append(modalHtml);

          // Cargar movimientos del activo
          $.ajax({
            url: "../../controllers/GestionarActivosController.php?action=verHistorial",
            type: "POST",
            data: { idActivo: datos.idActivo },
            dataType: "json",
            success: function (historialRes) {
              if (historialRes.status && historialRes.data.length > 0) {
                let movimientosHtml = `
                  <table id="tblMovimientosActivo" class="table table-bordered table-striped table-sm" style="width:100%;">
                    <thead>
                      <tr>
                        <th>Fecha</th>
                        <th>Acción</th>
                        <th>Usuario Mod.</th>
                        <th>Campo Modificado</th>
                        <th>Valor Anterior</th>
                        <th>Valor Nuevo</th>
                      </tr>
                    </thead>
                    <tbody>`;

                historialRes.data.forEach((item) => {
                  movimientosHtml += `
                    <tr>
                      <td>${item.FechaModificacion}</td>
                      <td>${item.Accion}</td>
                      <td>${item.UserMod}</td>
                      <td>${item.CampoModificado}</td>
                      <td>${item.ValorAnterior || ""}</td>
                      <td>${item.ValorNuevo || ""}</td>
                    </tr>`;
                });

                movimientosHtml += `</tbody></table>`;
                $("#movimientosActivo").html(movimientosHtml);

                $("#tblMovimientosActivo").DataTable({
                  language: {
                    url: CONFIGURACION.URLS.IDIOMA_DATATABLES,
                  },
                  responsive: true,
                  destroy: true,
                  order: [[0, "desc"]],
                  pageLength: 5,
                });
              } else {
                $("#movimientosActivo").html(
                  "<p>No se encontraron movimientos para este activo.</p>"
                );
              }
            },
            error: function () {
              $("#movimientosActivo").html(
                "<p>Error al cargar los movimientos del activo.</p>"
              );
            },
          });

          // Cargar últimos eventos del activo
          $.ajax({
            url: "../../controllers/GestionarActivosController.php?action=obtenerUltimosEventos",
            type: "POST",
            data: { idActivo: datos.idActivo },
            dataType: "json",
            success: function (eventosRes) {
              console.log("=== DEBUGGING ÚLTIMOS EVENTOS ===");
              console.log("Respuesta completa:", eventosRes);
              console.log("¿Tiene status?", eventosRes.status);
              console.log("¿Tiene data?", eventosRes.data);
              if (eventosRes.data) {
                console.log("Datos de eventos:", eventosRes.data);
                console.log("Todas las propiedades del objeto eventos:");
                Object.keys(eventosRes.data).forEach((key) => {
                  console.log(
                    `  ${key}:`,
                    eventosRes.data[key],
                    `(tipo: ${typeof eventosRes.data[key]})`
                  );
                });
                console.log(
                  "fechaUltimoMantenimiento específicamente:",
                  eventosRes.data.fechaUltimoMantenimiento
                );
              }

              if (eventosRes.status && eventosRes.data) {
                let eventos = eventosRes.data;
                let eventosHtml = `
                  <div class="row g-2">`;

                // Último Movimiento
                if (eventos.fechaUltimoMovimiento) {
                  eventosHtml += `
                    <div class="col-12">
                      <div class="d-flex align-items-center p-2 rounded-3" style="background: #f0fdf4; border-left: 3px solid #22c55e;">
                        <div class="me-2 ml-2 p-2">
                          <i class="fas fa-exchange-alt text-green-600"></i>
                        </div>
                        <div class="flex-grow-1">
                          <div class="fw-semibold small">Último Movimiento</div>
                          <div class="text-muted small">${moment(
                            eventos.fechaUltimoMovimiento
                          ).format("DD/MM/YYYY HH:mm")}</div>
                        </div>
                      </div>
                    </div>`;
                }

                // Último Préstamo
                if (eventos.fechaUltimoPrestamo) {
                  eventosHtml += `
                    <div class="col-12">
                      <div class="d-flex align-items-center p-2 rounded-3" style="background: #fef3c7; border-left: 3px solid #f59e0b;">
                        <div class="me-2 ml-2 p-2">
                          <i class="fas fa-hand-holding text-amber-600"></i>
                        </div>
                        <div class="flex-grow-1">
                          <div class="fw-semibold small">Último Préstamo</div>
                          <div class="text-muted small">${moment(
                            eventos.fechaUltimoPrestamo
                          ).format("DD/MM/YYYY HH:mm")}</div>
                        </div>
                      </div>
                    </div>`;
                }

                // Última Devolución
                if (eventos.fechaUltimaDevolucion) {
                  eventosHtml += `
                    <div class="col-12">
                      <div class="d-flex align-items-center p-2 rounded-3" style="background: #e0f2fe; border-left: 3px solid #06b6d4;">
                        <div class="me-2 ml-2 p-2">
                          <i class="fas fa-undo text-cyan-600"></i>
                        </div>
                        <div class="flex-grow-1">
                          <div class="fw-semibold small">Última Devolución</div>
                          <div class="text-muted small">${moment(
                            eventos.fechaUltimaDevolucion
                          ).format("DD/MM/YYYY HH:mm")}</div>
                        </div>
                      </div>
                    </div>`;
                }

                // Último Traslado
                if (eventos.fechaUltimoTraslado) {
                  eventosHtml += `
                    <div class="col-12">
                      <div class="d-flex align-items-center p-2 rounded-3" style="background: #f3e8ff; border-left: 3px solid #8b5cf6;">
                        <div class="me-2 ml-2 p-2">
                          <i class="fas fa-truck text-purple-600"></i>
                        </div>
                        <div class="flex-grow-1">
                          <div class="fw-semibold small">Último Traslado</div>
                          <div class="text-muted small">${moment(
                            eventos.fechaUltimoTraslado
                          ).format("DD/MM/YYYY HH:mm")}</div>
                        </div>
                      </div>
                    </div>`;
                }

                // Último Mantenimiento
                console.log(
                  "Verificando último mantenimiento:",
                  eventos.fechaUltimoMantenimiento
                );
                console.log(
                  "Tipo de dato:",
                  typeof eventos.fechaUltimoMantenimiento
                );
                console.log(
                  "¿Es null?",
                  eventos.fechaUltimoMantenimiento === null
                );
                console.log(
                  "¿Es undefined?",
                  eventos.fechaUltimoMantenimiento === undefined
                );
                console.log(
                  "¿Es string vacío?",
                  eventos.fechaUltimoMantenimiento === ""
                );

                if (
                  eventos.fechaUltimoMantenimiento &&
                  eventos.fechaUltimoMantenimiento !== null &&
                  eventos.fechaUltimoMantenimiento !== ""
                ) {
                  console.log("Agregando último mantenimiento al HTML");
                  eventosHtml += `
                    <div class="col-12">
                      <div class="d-flex align-items-center p-2 rounded-3" style="background: #fef2f2; border-left: 3px solid #ef4444;">
                        <div class="me-2 ml-2 p-2">
                          <i class="fas fa-tools text-red-600"></i>
                        </div>
                        <div class="flex-grow-1">
                          <div class="fw-semibold small">Último Mantenimiento</div>
                          <div class="text-muted small">${moment(
                            eventos.fechaUltimoMantenimiento
                          ).format("DD/MM/YYYY HH:mm")}</div>
                        </div>
                      </div>
                    </div>`;
                } else {
                  console.log(
                    "No se agregó último mantenimiento - valor:",
                    eventos.fechaUltimoMantenimiento
                  );
                }

                eventosHtml += `</div>`;

                if (
                  !eventos.fechaUltimoMovimiento &&
                  !eventos.fechaUltimoPrestamo &&
                  !eventos.fechaUltimaDevolucion &&
                  !eventos.fechaUltimoTraslado &&
                  !eventos.fechaUltimoMantenimiento
                ) {
                  eventosHtml = `
                    <div class="text-center py-3">
                      <i class="fas fa-info-circle text-muted mb-2" style="font-size: 2rem;"></i>
                      <p class="text-muted mb-0 small">No se encontraron eventos recientes para este activo.</p>
                    </div>`;
                }

                $("#ultimosEventosActivo").html(eventosHtml);
              } else {
                $("#ultimosEventosActivo").html(`
                  <div class="text-center py-3">
                    <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 small">Error al cargar los eventos del activo.</p>
                  </div>`);
              }
            },
            error: function () {
              $("#ultimosEventosActivo").html(`
                <div class="text-center py-3">
                  <i class="fas fa-exclamation-triangle text-danger mb-2" style="font-size: 2rem;"></i>
                  <p class="text-muted mb-0 small">Error al cargar los eventos del activo.</p>
                </div>`);
            },
          });

          // Cargar componentes del activo dinámicamente desde el servidor
          $.ajax({
            url: "../../controllers/GestionarActivosController.php?action=obtenerComponentes",
            type: "POST",
            data: { idActivoPadre: datos.idActivo },
            dataType: "json",
            success: function (componentesRes) {
              if (componentesRes.status && componentesRes.data.length > 0) {
                let componentesHtml = `
                  <div class="table-responsive">
                    <table class="table table-hover mb-0 table-sm">
                      <thead class="table-light">
                        <tr>
                          <th class="border-0 py-2 small">Código</th>
                          <th class="border-0 py-2 small">Componente</th>
                          <th class="border-0 py-2 small">Estado</th>
                        </tr>
                      </thead>
                      <tbody>`;

                componentesRes.data.forEach((item) => {
                  componentesHtml += `
                    <tr>
                      <td class="py-2">
                        <code class="text-primary small">${
                          item.CodigoComponente
                        }</code>
                      </td>
                      <td class="py-2">
                        <div class="fw-semibold small">${
                          item.NombreComponente
                        }</div>
                        <small class="text-muted">${
                          item.Descripcion || "-"
                        }</small>
                      </td>
                      <td class="py-2">
                        <span class="badge bg-success-subtle text-success small">${
                          item.Estado || "Activo"
                        }</span>
                      </td>
                    </tr>`;
                });

                componentesHtml += `
                      </tbody>
                    </table>
                  </div>`;
                $("#componentesActivo").html(componentesHtml);
              } else {
                $("#componentesActivo").html(`
                  <div class="text-center py-3">
                    <i class="fas fa-puzzle-piece text-muted mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted mb-0 small">No se encontraron componentes para este activo.</p>
                  </div>`);
              }
            },
            error: function () {
              $("#componentesActivo").html(`
                <div class="text-center py-3">
                  <i class="fas fa-exclamation-triangle text-danger mb-2" style="font-size: 2rem;"></i>
                  <p class="text-muted mb-0 small">Error al cargar los componentes del activo.</p>
                </div>`);
            },
          });

          // Mostrar el modal
          $("#modalDetallesActivo").modal("show");

          // Agregar event handler para el botón cerrar
          $(document)
            .off("click", ".btnCerrarModal")
            .on("click", ".btnCerrarModal", function () {
              $("#modalDetallesActivo").modal("hide");
            });
        } else {
          Swal.fire(
            "Detalles del Activo",
            "Error al obtener los detalles: " + res.message,
            "info"
          );
        }
      },
      error: function (xhr, status, error) {
        Swal.fire(
          "Error",
          "No se pudo obtener los detalles del activo: " + error,
          "error"
        );
      },
    });
  });

  // Manejador para el botón Editar desde el modal de detalles
  $(document).on("click", ".btnEditarDesdeModal", function () {
    const idActivo = $(this).data("idActivo");

    // Obtener datos del activo
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=obtenerActivoPorId",
      type: "POST",
      data: { idActivo: idActivo },
      dataType: "json",
      success: (res) => {
        if (res.status) {
          let data = res.data;
          $("#frmEditarActivo").data("idArticulo", data.idArticulo);

          $("#tituloModalActualizarActivo").html(
            '<i class="fa fa-edit"></i> Editar Activo'
          );

          // Carga los combos y luego los datos del activo
          cargarCombosModalActualizarActivo(() => {
            // Cargar datos básicos
            $("#IdActivoEditar").val(data.idActivo);
            $("#IdActivo").val(data.idActivo);
            $("#CodigoActivo").val(data.codigo);
            $("#SerieActivo").val(data.Serie);
            $("#DocIngresoAlmacen").val(data.DocIngresoAlmacen);
            $("#IdArticulo").val(data.idArticulo);
            $("#nombreArticulo").val(data.NombreActivo);
            $("#marca").val(data.Marca);
            $("#fechaAdquisicion").val(data.fechaAdquisicion);
            $("#Garantia").prop("checked", data.garantia == 1);
            $("#Observaciones").val(data.observaciones);
            $("#VidaUtil").val(data.vidaUtil);
            $("#ValorAdquisicion").val(data.valorAdquisicion);

            // Asignar valores a los combos
            $("#IdEstado").val(data.idEstado).trigger("change");
            $("#IdAmbiente").val(data.idAmbiente).trigger("change");
            $("#IdCategoria")
              .val(data.idCategoria)
              .trigger("change")
              .prop("disabled", true);

            // Cerrar el modal de detalles
            $("#modalDetallesActivo").modal("hide");

            // Mostrar el modal de edición
            $("#divModalActualizarActivo").modal({
              backdrop: "static",
              keyboard: false,
            });
          });
        } else {
          Swal.fire(
            "Editar Activo",
            "No se pudo obtener el activo: " + res.message,
            "warning"
          );
        }
      },
      error: (xhr, status, error) => {
        Swal.fire(
          "Editar Activo",
          "Error al obtener activo: " + error,
          "error"
        );
      },
    });
  });

  $(document).on("click", ".btnDarBajaDesdeModal", function () {
    const idActivo = $(this).data("idActivo");
    // Guardar el ID del activo en el formulario
    $("#frmBajaActivo").data("idActivo", idActivo);

    // Cargar el combo de autorizadores
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=combos",
      type: "POST",
      dataType: "json",
      success: function (res) {
        if (res.status) {
          $("#Autorizador").html(res.data.autorizador).trigger("change");
          $("#Autorizador").select2({
            theme: "bootstrap4",
            dropdownParent: $("#modalBajaActivo"),
            width: "100%",
          });
          // Mostrar el modal después de cargar el combo
          $("#modalBajaActivo").modal("show");
        } else {
          Swal.fire(
            "Error",
            "No se pudieron cargar los autorizadores",
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        Swal.fire(
          "Error",
          "Error al cargar los autorizadores: " + error,
          "error"
        );
      },
    });
  });

  // Manejador para el botón Imprimir desde el modal de detalles
  $(document).on("click", ".btnImprimirDesdeModal", function () {
    const idActivo = $(this).data("idActivo");
    window.open(
      `../../views/Reportes/reporteActivoPDF.php?idActivo=${idActivo}`,
      "_blank"
    );
  });

  // Manejador para el botón de Dar de Baja
  $(document).on("click", ".btnDarBaja", function () {
    const fila = $(this).closest("tr");
    const datos =
      $(fila).closest("table").attr("id") === "tblRegistros"
        ? $("#tblRegistros").DataTable().row(fila).data()
        : $("#tblTodosActivos").DataTable().row(fila).data();

    if (!datos) {
      Swal.fire(
        "Error",
        "No se pudo obtener la información del activo.",
        "error"
      );
      return;
    }

    // Guardar el ID del activo en el formulario
    $("#frmBajaActivo").data("idActivo", datos.idActivo);

    // Cargar el combo de autorizadores
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=combos",
      type: "POST",
      dataType: "json",
      success: function (res) {
        if (res.status) {
          $("#Autorizador").html(res.data.autorizador).trigger("change");
          $("#Autorizador").select2({
            theme: "bootstrap4",
            dropdownParent: $("#modalBajaActivo"),
            width: "100%",
          });
          // Mostrar el modal después de cargar el combo
          $("#modalBajaActivo").modal("show");
        } else {
          Swal.fire(
            "Error",
            "No se pudieron cargar los autorizadores",
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        Swal.fire(
          "Error",
          "Error al cargar los autorizadores: " + error,
          "error"
        );
      },
    });
  });

  // Manejador para el formulario de baja
  $("#frmBajaActivo").on("submit", function (e) {
    e.preventDefault();

    const idActivo = $(this).data("idActivo");
    const autorizador = $("#Autorizador").val();
    const motivoBaja = $("#motivoBaja").val();

    if (!autorizador || !motivoBaja) {
      Swal.fire("Error", "Todos los campos son obligatorios", "error");
      return;
    }

    // Mostrar loading
    Swal.fire({
      title: "Procesando",
      text: "Dando de baja el activo...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Log para depuración
    console.log("Datos a enviar:", {
      idActivo: idActivo,
      idResponsable: autorizador,
      motivoBaja: motivoBaja,
      userMod: userMod,
    });

    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=darBaja",
      type: "POST",
      data: {
        idActivo: idActivo,
        idResponsable: autorizador,
        motivoBaja: motivoBaja,
        userMod: userMod,
      },
      dataType: "json",
      success: function (res) {
        console.log("Respuesta del servidor:", res);
        if (res.status) {
          Swal.fire({
            icon: "success",
            title: "Éxito",
            text: res.message,
            timer: 1500,
          }).then(() => {
            $("#modalBajaActivo").modal("hide");
            $("#frmBajaActivo")[0].reset();
            listarActivosTable();
          });
        } else {
          Swal.fire("Error", res.message, "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la petición:", xhr.responseText);
        Swal.fire(
          "Error",
          "Error al procesar la baja del activo: " + error,
          "error"
        );
      },
    });
  });

  // Inicializar select2 para el autorizador en el modal de baja
  $("#Autorizador").select2({
    theme: "bootstrap4",
    dropdownParent: $("#modalBajaActivo"),
    width: "100%",
  });

  function ListarCombosConCallback(callback) {
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=combos",
      type: "POST",
      dataType: "json",
      success: (res) => {
        if (res.status) {
          // Limpiar y cargar los combos
          $("#Estado").empty().html(res.data.estado);
          $("#Ambiente").empty().html(res.data.ambientes);
          $("#Categoria").empty().html(res.data.categorias);

          // Inicializar select2 con configuración específica para el modal
          $("#Estado, #Ambiente, #Categoria").select2({
            theme: "bootstrap4",
            dropdownParent: $("#divModalActualizarActivo .modal-body"),
            width: "100%",
          });

          if (typeof callback === "function") {
            callback();
          }
        } else {
          Swal.fire(
            "Error",
            "No se pudieron cargar los combos: " + res.message,
            "warning"
          );
        }
      },
      error: (xhr, status, error) => {
        console.error("Error al cargar combos:", error);
        Swal.fire("Error", "Error al cargar los combos: " + error, "error");
      },
    });
  }

  // Modificar el evento submit del formulario
  $("#frmEditarActivo").on("submit", function (e) {
    e.preventDefault();

    // Solo enviamos los campos que realmente necesitamos actualizar
    const datos = {
      IdActivo: $("#IdActivoEditar").val() || null,
      Serie: $("#SerieActivo").val() || null,
      IdEstado: $("#IdEstado").val() === "" ? null : $("#IdEstado").val(),
      IdAmbiente: $("#Ambiente").val() === "" ? null : $("#Ambiente").val(),
      IdCategoria: $("#Categoria").val() === "" ? null : $("#Categoria").val(), // La categoría se mantiene pero no es editable
      Observaciones: $("#Observaciones").val() || null,
      UserMod: userMod,
      Accion: 2,
    };

    // Validar campos requeridos
    if (!datos.IdActivo) {
      Swal.fire("Error", "El ID del activo es requerido", "error");
      return;
    }

    // Convertir valores numéricos solo si tienen un valor
    datos.IdActivo = parseInt(datos.IdActivo);
    if (datos.IdEstado !== null) datos.IdEstado = parseInt(datos.IdEstado);
    if (datos.IdAmbiente !== null) {
      datos.IdAmbiente = parseInt(datos.IdAmbiente);
      if (isNaN(datos.IdAmbiente)) datos.IdAmbiente = null;
    }
    if (datos.IdCategoria !== null) {
      datos.IdCategoria = parseInt(datos.IdCategoria);
      if (isNaN(datos.IdCategoria)) datos.IdCategoria = null;
    }

    console.log("Datos a enviar:", datos);

    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=Actualizar",
      type: "POST",
      data: datos,
      dataType: "json",
      success: function (res) {
        if (res.status) {
          Swal.fire("Éxito", res.message, "success");
          $("#divModalActualizarActivo").modal("hide");
          listarActivosTable();
          const idArticulo = $("#frmEditarActivo").data("idArticulo");
          // if (idArticulo) {
          //   // listarActivosTableModal({
          //   //   IdArticulo: idArticulo,
          //   // });
          // }
        } else {
          Swal.fire(
            "Error",
            res.message || "Error al actualizar el activo",
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la petición:", xhr.responseText);
        Swal.fire("Error", "Error al actualizar el activo: " + error, "error");
      },
    });
  });

  // Eliminar los eventos que podrían estar causando la limpieza de datos
  $("#divModalActualizarActivo").off("shown.bs.modal hidden.bs.modal");

  // ? SE COMENTO PORQUE YA NO SE HARÁ UN REGISTRO MANUAL

  $("#frmmantenimiento").on("submit", function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=RegistrarManual",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (res) {
        if (res.status) {
          Swal.fire("Exito", res.message, "success");
          $("#divModalRegistroManualActivo").modal("hide");
          $("#frmmantenimiento")[0].reset();
          listarActivosTable?.();
        } else {
          Swal.fire("Error", res.message, "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la petición:", xhr.responseText);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error al registrar el activo: " + error,
        });
      },
    });
  });

  function cargarCombosModalRegistroManual() {
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=combos",
      type: "POST",
      dataType: "json",
      async: true,
      success: (res) => {
        if (res.status) {
          $("#Responsable").html(res.data.responsable).trigger("change");
          $("#Estado").html(res.data.estado).trigger("change");
          $("#Ambiente").html(res.data.ambientes).trigger("change");
          $("#Categoria").html(res.data.categorias).trigger("change");
          $("#Proveedor").html(res.data.proveedores).trigger("change");

          $("#Responsable, #Estado, #Ambiente, #Categoria, #Proveedor").select2(
            {
              theme: "bootstrap4",
              width: "100%",
            }
          );
        } else {
          Swal.fire(
            "Filtro de categorias",
            "No se pudieron cargar los combos: " + res.message,
            "warning"
          );
        }
      },
      error: (xhr, status, error) => {
        Swal.fire(
          "Filtro de categorias",
          "Error al cargar combos: " + error,
          "error"
        );
      },
    });
  }
}

// ? FIN CODIGO PARA GUARDAR MANUAL SIN UTILIZAR.

function listarActivosModal(documento, tipoDoc = "ingreso") {
  if ($.fn.DataTable.isDataTable("#tbllistarActivos")) {
    $("#tbllistarActivos").DataTable().clear().destroy();
  }

  let ajaxConfig = {};
  let columns = [];

  if (tipoDoc === "ingreso") {
    ajaxConfig = {
      url: "../../controllers/GestionarActivosController.php?action=articulos_por_doc",
      type: "POST",
      dataType: "json",
      data: { IdDocIngresoAlm: documento },
      dataSrc: function (json) {
        console.log("Respuesta del backend (ingreso): ", json);
        return json.data || [];
      },
    };

    columns = [
      { data: "IdArticulo" },
      { data: "Nombre" },
      { data: "Marca" },
      { data: "Proveedor" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            '<button class="btn btn-success align-self-center btn-sm btnSeleccionarActivo" data-id="' +
            row.IdArticulo +
            '" data-tipo="ingreso"><i class="fa fa-check"></i></button>'
          );
        },
      },
    ];
  } else if (tipoDoc === "venta") {
    ajaxConfig = {
      url: "../../controllers/GestionarActivosController.php?action=articulos_por_doc_venta",
      type: "POST",
      dataType: "json",
      data: { IdDocVenta: documento },
      dataSrc: function (json) {
        console.log("Respuesta del backend (venta): ", json);
        return json.data || [];
      },
    };

    columns = [
      { data: "IdArticulo" },
      { data: "Nombre" },
      { data: "Marca" },
      { data: "Cantidad", title: "Cantidad" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            '<button class="btn btn-success align-self-center btn-sm btnSeleccionarActivo" data-id="' +
            row.IdArticulo +
            '" data-tipo="venta" title="Seleccionar (Cantidad: ' +
            row.Cantidad +
            ')"><i class="fa fa-check"></i> Seleccionar</button>'
          );
        },
      },
    ];
  }

  $("#tbllistarActivos").DataTable({
    dom: "Bfrtip",
    responsive: false,
    destroy: true,
    ajax: ajaxConfig,
    columns: columns,
    language: {
      url: CONFIGURACION.URLS.IDIOMA_DATATABLES,
    },
  });
}

function setSucursalOrigenDestino() {
  var sucursalOrigenText = $("#IdSucursalOrigen option:selected").text();
  var sucursalDestinoText = $("#IdSucursalDestino option:selected").text();
  $("#sucursal_origen").val(sucursalOrigenText);
  $("#sucursal_destino").val(sucursalDestinoText);
}

function agregarActivoAlDetalle(activo) {
  const tipoDoc = $("#tipoDocumento").val();
  const documento = $("#inputDocumento").val();

  // Configurar la verificación según el tipo de documento
  let verificacionConfig = {};
  if (tipoDoc === "ingreso") {
    verificacionConfig = {
      url: "../../controllers/GestionarActivosController.php?action=verificarArticuloExistente",
      data: {
        IdDocIngresoAlm: documento,
        IdArticulo: activo.id,
        IdEmpresa: activo.empresa,
        IdSucursal: activo.sucursal,
      },
      mensajeError: "documento de ingreso",
    };
  } else if (tipoDoc === "venta") {
    verificacionConfig = {
      url: "../../controllers/GestionarActivosController.php?action=verificarArticuloExistenteDocVenta",
      data: {
        IdDocVenta: documento,
        IdArticulo: activo.id,
        IdEmpresa: activo.empresa,
        IdSucursal: activo.sucursal,
      },
      mensajeError: "documento de venta",
    };
  }

  // Primero verificar si el artículo ya existe
  $.ajax({
    url: verificacionConfig.url,
    type: "POST",
    data: verificacionConfig.data,
    dataType: "json",
    success: function (res) {
      if (res.status) {
        if (res.existe) {
          NotificacionToast(
            "error",
            `El artículo <b>${activo.nombre}</b> ya ha sido registrado con este ${verificacionConfig.mensajeError}.`
          );
          return false;
        }

        // Si no existe, continuar con el proceso de agregar al detalle
        if (
          $(`#tbldetalleactivoreg tbody tr[data-id='${activo.id}']`).length > 0
        ) {
          NotificacionToast(
            "error",
            `El activo <b>${activo.nombre}</b> ya está en el detalle.`
          );
          return false;
        }

        var numeroFilas = $("#tbldetalleactivoreg").find("tbody tr").length;
        var selectAmbiente = `<select class='form-control form-control-sm ambiente' name='ambiente[]' id="comboAmbiente${numeroFilas}"></select>`;
        var selectCategoria = `<select class='form-control form-control-sm categoria' name='categoria[]' id="comboCategoria${numeroFilas}"></select>`;
        var inputEstadoActivo = `<input type="text" class="form-control form-control-sm" name="estado_activo[]" value="Operativa" disabled>`;

        // Manejar la cantidad según el tipo de documento
        let inputCantidad, btnProcesar;
        const cantidadInicial = activo.cantidad || 1;

        if (tipoDoc === "venta" && activo.cantidad && activo.cantidad > 1) {
          // Para documentos de venta con cantidad > 1, permitir procesamiento
          inputCantidad = `<input type="number" class="form-control form-control-sm cantidad" name="cantidad[]" value="${cantidadInicial}" min="1" data-activo-id="${activo.id}">`;
          btnProcesar = `<button type="button" class="btn btn-warning btn-sm btnProcesarCantidad me-1" data-activo-id="${activo.id}" title="Procesar cantidad múltiple"><i class="fa fa-cogs"></i> Procesar (${cantidadInicial})</button>`;
        } else if (tipoDoc === "venta" && activo.cantidad === 1) {
          // Para documentos de venta con cantidad = 1, solo mostrar info
          inputCantidad = `<input type="number" class="form-control form-control-sm cantidad" name="cantidad[]" value="1" min="1" readonly data-activo-id="${activo.id}">`;
          btnProcesar = `<span class="badge badge-success">Cantidad: 1</span>`;
        } else {
          // Para documentos de ingreso (comportamiento normal)
          inputCantidad = `<input type="number" class="form-control form-control-sm cantidad" name="cantidad[]" value="1" min="1" data-activo-id="${activo.id}">`;
          btnProcesar = `<button type="button" class="btn btn-warning btn-sm btnProcesarCantidad me-1" data-activo-id="${activo.id}" title="Procesar cantidad múltiple"><i class="fa fa-cogs"></i> Procesar</button>`;
        }

        // Para documentos de venta, prellenar el valor si está disponible
        const valorPrellenado =
          tipoDoc === "venta" && activo.valorUnitario
            ? activo.valorUnitario
            : "";

        // Crear select de proveedor
        var selectProveedor = `<select class='form-control form-control-sm proveedor' name='proveedor[]' id="comboProveedor${numeroFilas}"></select>`;

        var nuevaFila = `<tr data-id='${activo.id}' class='table-success agregado-temp activo-principal' data-activo-nombre="${activo.nombre}" data-activo-marca="${activo.marca}" data-tipo-doc="${tipoDoc}">
                    <td>${activo.id}</td>
                    <td>${activo.nombre}</td>
                    <td>${activo.marca}</td>
                    <td>
                      <input type="text" class="form-control form-control-sm" name="serie[]" placeholder="Serie">
                    </td>
                    <td>${inputEstadoActivo}</td>
                    <td>${selectAmbiente}</td>
                    <td>${selectCategoria}</td>
                    <td><input type="text" class="form-control form-control-sm" name="valor[]" placeholder="Valor" value="${valorPrellenado}"></td>
                    <td>${inputCantidad}</td>
                    <td>${selectProveedor}</td>
                    <td><textarea class='form-control form-control-sm' name='observaciones[]' rows='1' placeholder='Observaciones'></textarea></td>
                    <td>
                      <div class="btn-group">
                        ${btnProcesar}
                        <button type='button' class='btn btn-danger btn-sm btnQuitarActivo me-4'><i class='fa fa-trash'></i></button>
                      </div>
                    </td>
                </tr>`;
        $("#tbldetalleactivoreg tbody").append(nuevaFila);

        ListarCombosAmbiente(`comboAmbiente${numeroFilas}`);
        ListarCombosCategoria(`comboCategoria${numeroFilas}`);

        // Determinar si el proveedor es obligatorio según el tipo de documento
        const esProveedorObligatorio = tipoDoc === "venta";
        ListarCombosProveedor(
          `comboProveedor${numeroFilas}`,
          esProveedorObligatorio
        );

        setTimeout(function () {
          $("#tbldetalleactivoreg tbody tr.agregado-temp").removeClass(
            "table-success agregado-temp"
          );
        }, 1000);

        NotificacionToast(
          "success",
          `Activo <b>${activo.nombre}</b> agregado al detalle.`
        );
        // Actualizar contador total
        actualizarContadorTotal();
        return true;
      } else {
        NotificacionToast("error", res.message);
        return false;
      }
    },
    error: function () {
      NotificacionToast("error", "Error al verificar el artículo");
      return false;
    },
  });
}

// ? INICIO: SE COMENTO LA CARGA DE COMBOS EN EL MODAL YA QUE NO SE UTILIZARÁ
function cargarCombosModalActualizarActivo(callback) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status) {
        // Limpiar y cargar los combos
        $("#IdEstado").html(res.data.estado).trigger("change");
        $("#IdAmbiente").html(res.data.ambientes).trigger("change");
        $("#IdCategoria").html(res.data.categorias).trigger("change");

        $("#IdEstado, #IdAmbiente, #IdCategoria").select2({
          theme: "bootstrap4",
          dropdownParent: $("#divModalActualizarActivo .modal-body"),
          width: "100%",
        });

        // Llamar al callback después de cargar los combos
        if (typeof callback === "function") {
          callback();
        }
      }
    },
    error: (xhr, status, error) => {
      console.error("Error al cargar combos:", error);
      if (typeof callback === "function") {
        callback();
      }
    },
  });
}

function ListarCombosCategoria(elemento) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=combos",
    type: "POST",
    dataType: "json",
    async: false,
    success: (res) => {
      if (res.status) {
        $(`#${elemento}`).html(res.data.categorias).trigger("change");
        $(`#${elemento}`).select2({
          theme: "bootstrap4",
          width: "100%",
        });
      } else {
        Swal.fire(
          "Filtro de categorias",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      Swal.fire(
        "Filtro de categorias",
        "No se pudieron cargar los combos: " + res.message,
        "warning"
      );
    },
  });
}

function ListarCombosAmbiente(elemento) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=combos",
    type: "POST",
    dataType: "json",
    async: false,
    success: (res) => {
      if (res.status) {
        $(`#${elemento}`).html(res.data.ambientes).trigger("change");
        $(`#${elemento}`).select2({
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

function ListarCombosProveedor(elemento, esObligatorio = false) {
  // Inicializar Select2 con AJAX para búsqueda dinámica
  $(`#${elemento}`).select2({
    dropdownParent: $(`#${elemento}`).closest("tr"),
    minimumInputLength: 2,
    theme: "bootstrap4",
    width: "100%",
    language: {
      inputTooShort: function (args) {
        return "Ingresar más de 2 caracteres para buscar...";
      },
      noResults: function () {
        return "No se encontraron proveedores.";
      },
      searching: function () {
        return "Buscando proveedores...";
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
    placeholder: esObligatorio
      ? "🔍 Buscar y Seleccionar Proveedor (OBLIGATORIO)"
      : "🔍 Buscar Proveedor (Opcional)",
    allowClear: !esObligatorio,
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

function ListarCombosFiltros() {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status) {
        $("#filtroCategoria").html(res.data.categorias).trigger("change");
        //$("#filtroSucursal").html(res.data.sucursales).trigger("change");
        $("#filtroAmbiente").html(res.data.ambientes).trigger("change");
        $("#filtroCategoria, #filtroAmbiente").select2({
          theme: "bootstrap4",
          dropdownParent: $("#divtblRegistros .modal-body"),
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
        $(
          "#IdTipoMovimientoMov, #CodAutorizador, #IdSucursalOrigen, #IdSucursalDestino"
        ).select2({
          theme: "bootstrap4",
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

function listarActivosTable() {
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
      url: "../../controllers/GestionarActivosController.php?action=ConsultarActivos",
      type: "POST",
      data: {
        IdArticulo: "",
        IdActivo: "",
        pIdCategoria: "6",
      },
      dataType: "json",
      dataSrc: function (json) {
        return json || [];
      },
    },
    columns: [
      { data: "idActivo" },
      { data: "codigo" },
      { data: "NombreActivo" },
      { data: "idEstadoActivo", visible: false, searchable: false },
      {
        data: "Estado",
        render: function (data, type, row) {
          switch (data) {
            case "Operativa":
              return '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Operativo</span>';
            case "Reparación":
              return '<span class="badge bg-danger"><i class="fas fa-wrench me-1"></i> Reparación</span>';
            case "Baja":
              return '<span class="badge bg-warning text-dark"><i class="fas fa-times-circle me-1"></i> Baja</span>';
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
      { data: "idCategoria", visible: false },
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
        data: null,
        render: (data, type, row) =>
          `<div class="btn-group">
              <button class="btn btn-primary btnVerDetalles" type="button">
                <i class="fas fa-eye text-white"></i>
              </button>
        </div>`,
      },
    ],
    language: {
      url: CONFIGURACION.URLS.IDIOMA_DATATABLES,
    },
  });
}

$(document).on("click", ".btnVerDetalle", function () {
  // Abrir el modal
  $("#modalListarTodosActivos").modal("show");
  // Obtener el Codigo de la fila seleccionada (que es el IdArticulo)
  const fila = $(this).closest("tr");
  const datos = $("#tblRegistros").DataTable().row(fila).data();
  if (!datos || !datos.Codigo) {
    Swal.fire("Error", "No se pudo obtener el IdArticulo.", "error");
    return;
  }
  // Listar los activos en el modal, enviando el IdArticulo
  //listarActivosTableModal({ IdArticulo: datos.Codigo });
});

// Add the event handler for the print button
$(document).on("click", ".btnImprimirActivo", function () {
  const fila = $(this).closest("tr");
  const datos =
    $(fila).closest("table").attr("id") === "tblRegistros"
      ? $("#tblRegistros").DataTable().row(fila).data()
      : $("#tblTodosActivos").DataTable().row(fila).data();

  if (!datos) {
    Swal.fire(
      "Error",
      "No se pudo obtener la información del activo.",
      "error"
    );
    return;
  }

  window.open(
    `/app/views/Reportes/reporteActivo.php?idActivo=${datos.idActivo}`,
    "_blank"
  );
});

function obtenerCombosActivos(callback) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: function (res) {
      if (res.status) {
        callback(res.data);
      } else {
        Swal.fire(
          "Error",
          "No se pudieron cargar los combos: " + res.message,
          "error"
        );
      }
    },
    error: function (xhr, status, error) {
      Swal.fire("Error", "Error al cargar combos: " + error, "error");
    },
  });
}

function addActivoManualForm(combos) {
  activoFormCount++;
  const formHtml = `
    <div class="card card-success activo-manual-form" data-form-number="${activoFormCount}">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-plus-circle"></i> Activo Nuevo <span class="activo-num">#${activoFormCount}</span></h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool btn-remove-activo" style="display:none;">
                <i class="fas fa-times"></i>
            </button>
        </div>
      </div>
      <div class="card-body">
        <form class="frmmantenimientoManual">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="nombre_${activoFormCount}">Nombre</label>
                <input type="text" name="nombre[]" id="nombre_${activoFormCount}" class="form-control" placeholder="Ej. Mouse Logitech" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="serie_${activoFormCount}">Serie</label>
                <input type="text" name="serie[]" id="serie_${activoFormCount}" class="form-control" placeholder="Ej. ML-123" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="Estado_${activoFormCount}">Estado</label>
                <select name="Estado[]" id="Estado_${activoFormCount}" class="form-control select-2" required></select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="Categoria_${activoFormCount}">Categoria</label>
                <select name="Categoria[]" id="Categoria_${activoFormCount}" class="form-control select-2" required></select>
              </div>
            </div>
             <div class="col-md-4">
              <div class="form-group">
                <label for="Responsable_${activoFormCount}">Responsable</label>
                <select name="Responsable[]" id="Responsable_${activoFormCount}" class="form-control select-2" required></select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="Proveedor_${activoFormCount}">Proveedor</label>
                <select name="Proveedor[]" id="Proveedor_${activoFormCount}" class="form-control select-2" required></select>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="descripcion_${activoFormCount}">Descripción</label>
                <textarea name="Descripcion[]" id="descripcion_${activoFormCount}" class="form-control" placeholder="Ej. Mouse Logitech color negro"></textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="empresa_${activoFormCount}">Empresa</label>
                <input type="text" class="form-control" name="empresa[]" id="empresa_${activoFormCount}" disabled>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="unidadNegocio_${activoFormCount}">Unidad de Negocio</label>
                <input type="text" class="form-control" name="unidadNegocio[]" id="unidadNegocio_${activoFormCount}" disabled>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="Ambiente_${activoFormCount}">Ambiente:</label>
                <select name="Ambiente[]" id="Ambiente_${activoFormCount}" class="form-control select-2"></select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="Cantidad_${activoFormCount}"> Cantidad: </label>
                <input type="number" name="Cantidad[]" id="Cantidad_${activoFormCount}" class="form-control" placeholder="Ej. 1" value="1" min="1" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="ValorAdquisicion_${activoFormCount}">Valor Adquisición:</label>
                <input type="number" step="0.01" name="ValorAdquisicion[]" id="ValorAdquisicion_${activoFormCount}" class="form-control" placeholder="Ej. 10.00" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="fechaAdquisicion_${activoFormCount}">Fecha Adquisición: </label>
                <input type="date" name="fechaAdquisicion[]" id="fechaAdquisicion_${activoFormCount}" class="form-control" value="${new Date()
    .toISOString()
    .slice(0, 10)}" required>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="Observaciones_${activoFormCount}">Observaciones: </label>
                <textarea name="Observaciones[]" id="Observaciones_${activoFormCount}" class="form-control" rows="3" placeholder="Ingrese las observaciones según el activo..."></textarea>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group text-center">
                <button type="button" class="btn btn-info btnProcesarActivoManual" data-form-id="${activoFormCount}">
                  <i class="fas fa-cogs"></i> Procesar Activo
                </button>
              </div>
            </div>
          </div>
        </form>
        
        <!-- Tabla de previsualización de activos procesados -->
        <div class="tabla-preview-activos" id="tablaPreview_${activoFormCount}" style="display: none;">
          <hr>
          <h6><i class="fas fa-eye"></i> Previsualización de Activos a Crear</h6>
          <div class="table-responsive">
            <table class="table table-sm table-bordered" id="tblPreviewActivos_${activoFormCount}">
              <thead class="table-info">
                <tr>
                  <th>Serie</th>
                  <th>Nombre</th>
                  <th>Estado</th>
                  <th>Categoría</th>
                  <th>Responsable</th>
                  <th>Ambiente</th>
                  <th>Valor</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
              <tfoot>
                <tr>
                  <th colspan="7" class="text-right">Total Activos:</th>
                  <th class="text-center"><span class="badge badge-info total-activos-preview">0</span></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  `;
  $("#activosContainer").append(formHtml);

  // Solo actualiza el número y atributos del nuevo formulario
  const newForm = $(`[data-form-number='${activoFormCount}']`);
  newForm.find(".activo-num").text(`#${activoFormCount}`);
  // No es necesario reasignar for/id salvo que cambie el orden

  // Initialize Select2 for static combos after they are populated
  newForm.find(`[name='Responsable[]']`).select2({
    dropdownParent: newForm,
    theme: "bootstrap4",
    width: "100%",
    placeholder: "Seleccionar Responsable",
  });
  newForm.find(`[name='Estado[]']`).select2({
    dropdownParent: newForm,
    theme: "bootstrap4",
    width: "100%",
    placeholder: "Seleccionar Estado",
  });
  newForm.find(`[name='Categoria[]']`).select2({
    dropdownParent: newForm,
    theme: "bootstrap4",
    width: "100%",
    placeholder: "Seleccionar Categoria",
  });
  newForm.find(`[name='Ambiente[]']`).select2({
    dropdownParent: newForm,
    theme: "bootstrap4",
    width: "100%",
    placeholder: "Seleccionar Ambiente",
    allowClear: true,
  });

  // Proveedor: inicializar Select2 con AJAX para búsqueda dinámica
  newForm.find('[name="Proveedor[]"]').select2({
    dropdownParent: newForm,
    minimumInputLength: 2,
    theme: "bootstrap4",
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

  // Cargar combos normales
  if (combos) {
    newForm
      .find(`[name='Responsable[]']`)
      .html(combos.responsable)
      .trigger("change");
    newForm.find(`[name='Estado[]']`).html(combos.estado).trigger("change");
    newForm
      .find(`[name='Categoria[]']`)
      .html(combos.categorias)
      .trigger("change");
    newForm
      .find(`[name='Ambiente[]']`)
      .html(combos.ambientes)
      .trigger("change");
    // No cargar combos.proveedores aquí
  }
  if (typeof $.fn.CardWidget === "function") {
    newForm.CardWidget();
  }
  // Mostrar/ocultar botón de eliminar solo en el nuevo formulario
  if ($("#activosContainer .activo-manual-form").length > 1) {
    $(".btn-remove-activo").show();
  } else {
    $(".btn-remove-activo").hide();
  }
}

function updateActivoFormNumbers() {
  // Solo actualiza el número de formularios y el atributo data-form-number
  $("#activosContainer .activo-manual-form").each(function (index) {
    $(this)
      .find(".activo-num")
      .text(`#${index + 1}`);
    $(this).attr("data-form-number", index + 1);
    // No reasignes for/id salvo que sea estrictamente necesario
  });
  activoFormCount = $("#activosContainer .activo-manual-form").length;
  // Mostrar/ocultar botón de eliminar solo si hay más de uno
  if (activoFormCount > 1) {
    $(".btn-remove-activo").show();
  } else {
    $(".btn-remove-activo").hide();
  }
}

$(document).on("click", ".btn-remove-activo", function () {
  const formToRemove = $(this).closest(".activo-manual-form");
  if ($("#activosContainer .activo-manual-form").length > 1) {
    Swal.fire({
      title: "¿Estás seguro?",
      text: "Se eliminará este formulario de activo.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      confirmButtonText: "Sí, eliminar",
      cancelButtonColor: "#d33",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        formToRemove.remove();
        updateActivoFormNumbers();
      }
    });
  } else {
    Swal.fire({
      icon: "warning",
      title: "Advertencia",
      text: "No puedes eliminar el último formulario de activo.",
    });
  }
});

$("#activosContainer .card").each(function () {
  if (typeof $(this).CardWidget === "function") {
    $(this).CardWidget();
  }
});
// Estilos CSS para el modal de procesamiento
$(document).ready(function () {
  // Agregar estilos CSS dinámicamente
  const modalStyles = `
    <style>
      #modalProcesarCantidad .modal-header {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
      }
      
      #modalProcesarCantidad .alert-info {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border-color: #b8daff;
        color: #0c5460;
      }
      
      #modalProcesarCantidad .alert-warning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-color: #ffeaa7;
        color: #856404;
      }
      
      #modalProcesarCantidad .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #dee2e6;
      }
      
      #modalProcesarCantidad .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border-color: #17a2b8;
        transition: all 0.3s ease;
      }
      
      #modalProcesarCantidad .btn-info:hover {
        background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      }
      
      .activo-procesado {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3;
      }
      
      .activo-procesado:hover {
        background-color: #bbdefb !important;
      }
      
      .btnProcesarCantidad {
        transition: all 0.3s ease;
      }
      
      .btnProcesarCantidad:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      }
      
      /* Estilos para los grupos de activos */
      .activo-grupo-principal {
        background-color: #e8f5e8 !important;
        border-left: 4px solid #28a745;
        font-weight: bold;
      }
      
      .activo-grupo-hijo {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3;
      }
      
      .grupo-indent {
        color: #6c757d;
        font-weight: bold;
        margin-right: 8px;
        font-family: monospace;
      }
      
      .grupo-badge {
        font-size: 0.75em;
        margin-left: 8px;
        padding: 4px 8px;
        border-radius: 12px;
      }
      
      .badge-primary.grupo-badge {
        background-color: #28a745;
        color: white;
      }
      
      .badge-info.grupo-badge {
        background-color: #17a2b8;
        color: white;
      }
      
      /* Hover effects para las filas de grupo */
      .activo-grupo-principal:hover {
        background-color: #d4edda !important;
      }
      
      .activo-grupo-hijo:hover {
        background-color: #bbdefb !important;
      }
      
      /* Botón agregar más unidades */
      .btnAgregarMasUnidades {
        transition: all 0.3s ease;
      }
      
      .btnAgregarMasUnidades:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        background-color: #218838;
      }
      
      /* Botón colapsar grupos */
      .btnColapsarGrupo {
        transition: all 0.3s ease;
      }
      
      .btnColapsarGrupo:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        background-color: #5a6268;
        color: white;
      }
      
      /* Badge para grupos colapsados */
      .badge-warning.grupo-badge {
        background-color: #ffc107;
        color: #212529;
      }
      
      /* Contador detalle mejorado */
      .contador-detalle {
        display: flex;
        gap: 8px;
        justify-content: center;
        align-items: center;
      }
      
      .contador-detalle .badge {
        font-size: 0.85em;
        padding: 6px 12px;
      }
      
      /* Estilos para el loader de procesamiento */
      .procesamiento-popup {
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      }
      
      .procesamiento-container {
        padding: 20px;
        text-align: center;
      }
      
      .progress {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
      }
      
      .progress-bar {
        transition: width 0.3s ease;
        border-radius: 15px;
        position: relative;
      }
      
      .progress-text {
        position: absolute;
        width: 100%;
        text-align: center;
        line-height: 25px;
        color: white;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
      }
      
      .procesamiento-info {
        margin-top: 15px;
      }
      
      .procesamiento-info p {
        font-size: 1.1em;
        color: #495057;
      }
      
      .procesamiento-info small {
        font-size: 0.9em;
        color: #6c757d;
      }
    </style>
  `;

  // Agregar los estilos al head del documento
  $("head").append(modalStyles);
});

// Manejador para procesar activo manual
$(document).on("click", ".btnProcesarActivoManual", function () {
  const formId = $(this).data("form-id");
  const form = $(`[data-form-number='${formId}']`);

  // Obtener datos del formulario
  const nombre = form.find("input[name='nombre[]']").val().trim();
  const serie = form.find("input[name='serie[]']").val().trim();
  const cantidad = parseInt(form.find("input[name='Cantidad[]']").val()) || 1;
  const estado = form.find("select[name='Estado[]']").val();
  const categoria = form.find("select[name='Categoria[]']").val();
  const responsable = form.find("select[name='Responsable[]']").val();
  const ambiente = form.find("select[name='Ambiente[]']").val();
  const valor =
    parseFloat(form.find("input[name='ValorAdquisicion[]']").val()) || 0;
  const observaciones = form
    .find("textarea[name='Observaciones[]']")
    .val()
    .trim();

  // Validaciones
  if (!nombre) {
    NotificacionToast("error", "El nombre del activo es requerido");
    return;
  }

  if (!serie) {
    NotificacionToast("error", "La serie del activo es requerida");
    return;
  }

  if (cantidad <= 1) {
    NotificacionToast("info", "La cantidad debe ser mayor a 1 para procesar");
    return;
  }

  if (!estado || !categoria || !responsable) {
    NotificacionToast(
      "error",
      "Estado, categoría y responsable son requeridos"
    );
    return;
  }

  if (valor <= 0) {
    NotificacionToast("error", "El valor de adquisición debe ser mayor a 0");
    return;
  }

  // Validar series duplicadas en todas las tablas de preview
  const validacion = validarSeriesDuplicadasManual(serie);
  if (!validacion.esValida) {
    Swal.fire({
      title: "Serie Duplicada",
      html: `
        <p>La serie base "<strong>${serie}</strong>" ya existe en las previsualizaciones.</p>
        <p>¿Desea generar una serie única automáticamente?</p>
      `,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#28a745",
      confirmButtonText: "Sí, generar automáticamente",
      cancelButtonColor: "#6c757d",
      cancelButtonText: "No, cambiar manualmente",
    }).then((result) => {
      if (result.isConfirmed) {
        const serieUnica = generarSerieUnicaManual(serie);
        form.find("input[name='serie[]']").val(serieUnica);
        NotificacionToast(
          "info",
          `Serie cambiada automáticamente a: ${serieUnica}`
        );
      }
    });
    return;
  }

  // Mostrar modal de confirmación con detalles
  Swal.fire({
    title: "Procesar Activo Manual",
    html: `
      <div class="text-left">
        <p><strong>Activo:</strong> ${nombre}</p>
        <p><strong>Serie Base:</strong> ${serie}</p>
        <p><strong>Cantidad:</strong> ${cantidad} unidades</p>
        <p><strong>Valor Unitario:</strong> S/${valor}</p>
        <hr>
        <p class="text-info"><i class="fas fa-info-circle"></i> Se crearán S/{cantidad} activos individuales con series únicas.</p>
      </div>
    `,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#17a2b8",
    confirmButtonText: "Sí, procesar",
    cancelButtonColor: "#6c757d",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      procesarActivoManual(formId, {
        nombre,
        serie,
        cantidad,
        estado,
        categoria,
        responsable,
        ambiente,
        valor,
        observaciones,
      });
    }
  });
});

// Función para procesar activo manual
function procesarActivoManual(formId, datos) {
  const form = $(`[data-form-number='${formId}']`);
  const tablaPreview = $(`#tblPreviewActivos_${formId} tbody`);
  const divTabla = $(`#tablaPreview_${formId}`);

  // Mostrar loading
  Swal.fire({
    title: "Procesando Activo Manual",
    html: `
      <div class="procesamiento-container">
        <div class="progress mb-3" style="height: 25px;">
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
               role="progressbar" style="width: 0%" id="progressBarManual">
            <span class="progress-text">0%</span>
          </div>
        </div>
        <div class="procesamiento-info">
          <p class="mb-2"><i class="fas fa-cogs fa-spin text-info"></i> <span id="procesamientoTextoManual">Preparando activos...</span></p>
          <small class="text-muted" id="procesamientoDetalleManual">Configurando ${datos.cantidad} unidades para "${datos.nombre}"</small>
        </div>
      </div>
    `,
    allowOutsideClick: false,
    showConfirmButton: false,
    customClass: {
      popup: "procesamiento-popup",
    },
  });

  // Función para actualizar progreso
  function actualizarProgresoManual(actual, total, texto) {
    const porcentaje = Math.round((actual / total) * 100);
    $("#progressBarManual").css("width", porcentaje + "%");
    $("#progressBarManual .progress-text").text(porcentaje + "%");
    $("#procesamientoTextoManual").text(texto);
    $("#procesamientoDetalleManual").text(
      `Procesando unidad ${actual} de ${total}`
    );
  }

  // Obtener textos de los selects
  const estadoTexto = form
    .find("select[name='Estado[]'] option:selected")
    .text();
  const categoriaTexto = form
    .find("select[name='Categoria[]'] option:selected")
    .text();
  const responsableTexto = form
    .find("select[name='Responsable[]'] option:selected")
    .text();
  const ambienteTexto = form
    .find("select[name='Ambiente[]'] option:selected")
    .text();

  // Crear filas progresivamente
  let activosCreados = 0;

  function crearActivoManualProgresivo(indice) {
    if (indice >= datos.cantidad) {
      finalizarProcesamientoManual();
      return;
    }

    actualizarProgresoManual(
      indice + 1,
      datos.cantidad,
      `Creando activo ${indice + 1}/${datos.cantidad}...`
    );

    const serieActual = `${datos.serie}-${indice + 1}`;
    const grupoId = `manual_${formId}_${Date.now()}`;

    const nuevaFila = `
      <tr data-grupo-manual="${grupoId}" data-form-id="${formId}">
        <td>
          <input type="text" class="form-control form-control-sm serie-manual" value="${serieActual}" readonly>
        </td>
        <td>${datos.nombre}</td>
        <td>${estadoTexto}</td>
        <td>${categoriaTexto}</td>
        <td>${responsableTexto}</td>
        <td>${ambienteTexto || "No asignado"}</td>
        <td>S/.${datos.valor.toFixed(2)}</td>
        <td>
          <button type="button" class="btn btn-danger btn-sm btnEliminarActivoManual" title="Eliminar este activo">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>
    `;

    tablaPreview.append(nuevaFila);

    // Efecto visual
    const ultimaFila = tablaPreview.find("tr:last");
    ultimaFila.hide().fadeIn(300);

    activosCreados++;

    // Continuar con el siguiente
    setTimeout(() => {
      crearActivoManualProgresivo(indice + 1);
    }, 200);
  }

  function finalizarProcesamientoManual() {
    actualizarProgresoManual(
      datos.cantidad,
      datos.cantidad,
      "¡Procesamiento completado!"
    );

    setTimeout(() => {
      Swal.close();

      // Mostrar tabla de preview
      divTabla.show();

      // Actualizar contador
      actualizarContadorPreview(formId);

      // Deshabilitar campos del formulario
      form.find("input, select, textarea").prop("disabled", true);
      form.find(".btnProcesarActivoManual").hide();

      // Mostrar botón de reset
      form.find(".btnProcesarActivoManual").after(`
        <button type="button" class="btn btn-warning btnResetActivoManual ms-2" data-form-id="${formId}">
          <i class="fas fa-undo"></i> Resetear
        </button>
      `);

      NotificacionToast(
        "success",
        `Se han creado ${datos.cantidad} activos en la previsualización.`
      );
    }, 800);
  }

  // Iniciar procesamiento
  setTimeout(() => {
    crearActivoManualProgresivo(0);
  }, 500);
}

// Función para validar series duplicadas en formularios manuales
function validarSeriesDuplicadasManual(serieBase) {
  const seriesExistentes = [];

  // Revisar todas las tablas de preview
  $("[id^='tblPreviewActivos_'] tbody tr").each(function () {
    const serie = $(this).find(".serie-manual").val();
    if (serie) {
      seriesExistentes.push(serie.toLowerCase());
    }
  });

  // También revisar la tabla principal si existe
  $("#tbldetalleactivoreg tbody tr").each(function () {
    const serie = $(this).find("input[name='serie[]']").val();
    if (serie) {
      seriesExistentes.push(serie.toLowerCase());
    }
  });

  return {
    esValida: !seriesExistentes.includes(serieBase.toLowerCase()),
    seriesExistentes: seriesExistentes,
  };
}

// Función para generar serie única en formularios manuales
function generarSerieUnicaManual(serieBase) {
  let contador = 1;
  let serieNueva = serieBase;

  while (!validarSeriesDuplicadasManual(serieNueva).esValida) {
    contador++;
    serieNueva = `${serieBase}-V${contador}`;
  }

  return serieNueva;
}

// Función para actualizar contador de preview
function actualizarContadorPreview(formId) {
  const totalActivos = $(`#tblPreviewActivos_${formId} tbody tr`).length;
  $(`#tblPreviewActivos_${formId} .total-activos-preview`).text(totalActivos);
}

// Manejador para eliminar activo individual de preview
$(document).on("click", ".btnEliminarActivoManual", function () {
  const fila = $(this).closest("tr");
  const formId = fila.data("form-id");

  Swal.fire({
    title: "¿Eliminar este activo?",
    text: "Se eliminará solo esta unidad de la previsualización.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar",
    cancelButtonColor: "#6c757d",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      fila.remove();
      actualizarContadorPreview(formId);
      NotificacionToast("success", "Activo eliminado de la previsualización.");
    }
  });
});

// Manejador para resetear formulario manual
$(document).on("click", ".btnResetActivoManual", function () {
  const formId = $(this).data("form-id");
  const form = $(`[data-form-number='${formId}']`);

  Swal.fire({
    title: "¿Resetear formulario?",
    text: "Se eliminarán todos los activos de la previsualización y se habilitará el formulario.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#ffc107",
    confirmButtonText: "Sí, resetear",
    cancelButtonColor: "#6c757d",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Limpiar tabla de preview
      $(`#tblPreviewActivos_${formId} tbody`).empty();
      $(`#tablaPreview_${formId}`).hide();

      // Habilitar formulario
      form.find("input, select, textarea").prop("disabled", false);
      form.find(".btnProcesarActivoManual").show();
      form.find(".btnResetActivoManual").remove();

      NotificacionToast("success", "Formulario reseteado correctamente.");
    }
  });
});
