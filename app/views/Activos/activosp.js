let activoFormCount = 0;
let combosActivos = null;

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

  $(document).on("click", "#btnBuscarDocIngreso", function () {
    let docIngreso = $("#inputDocIngresoAlm").val().trim();
    console.log("Data enviada a listarActivo:", docIngreso);

    if (!docIngreso) {
      mostrarNotificacionModalActivos(
        "Ingrese el Doc. Ingreso Almacén",
        "danger"
      );
      return;
    }

    $("#ModalArticulos").modal("show");
    listarActivosModal(docIngreso);
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
      $("#frmMovimiento")[0].reset();
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

  /*$("#frmDetalleMovimiento").on("submit", function (e) {
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
          console.log("Server response:", res);
          Swal.fire("Éxito", "Activo agregado al movimiento", "success");
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
  });*/

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
    var activo = {
      id: fila.find("td:eq(0)").text(),
      nombre: fila.find("td:eq(1)").text(),
      marca: fila.find("td:eq(2)").text(),
      empresa: fila.find("td:eq(3)").text(),
      unidadNegocio: fila.find("td:eq(4)").text(),
      nombreLocal: fila.find("td:eq(5)").text(),
    };
    agregarActivoAlDetalle(activo);
  });

  $(document).on("click", ".btnQuitarActivo", function () {
    const filaActual = $(this).closest("tr");
    const activoId = filaActual.data("id");

    // Remover la fila principal y todas las filas generadas por procesamiento
    $(`#tbldetalleactivoreg tbody tr[data-id='${activoId}']`).remove();
  });

  // Manejador para el botón "Procesar Cantidad" - Abre el modal
  $(document).on("click", ".btnProcesarCantidad", function () {
    const filaActual = $(this).closest("tr");
    const activoId = filaActual.data("id");
    const activoNombre = filaActual.data("activo-nombre");
    const activoMarca = filaActual.data("activo-marca");
    const cantidad = parseInt(filaActual.find("input.cantidad").val()) || 1;

    if (cantidad <= 1) {
      NotificacionToast("info", "La cantidad debe ser mayor a 1 para procesar.");
      return;
    }

    // Verificar si ya se procesó este activo
    if ($(`#tbldetalleactivoreg tbody tr[data-id='${activoId}'][data-procesado='true']`).length > 0) {
      NotificacionToast("warning", "Este activo ya ha sido procesado. Elimine las filas generadas primero.");
      return;
    }

    // Validar que los campos principales estén llenos
    const ambienteId = filaActual.find("select.ambiente").val();
    const categoriaId = filaActual.find("select.categoria").val();
    
    if (!ambienteId || !categoriaId) {
      NotificacionToast("error", "Debe seleccionar ambiente y categoría antes de procesar.");
      return;
    }

    // Obtener los valores de la fila principal
    const serie = filaActual.find("input[name='serie[]']").val();
    const observaciones = filaActual.find("textarea[name='observaciones[]']").val();

    // Configurar el modal con los datos
    $("#modalActivoNombre").text(activoNombre);
    $("#modalActivoMarca").text(activoMarca);
    $("#modalCantidadTotal").val(cantidad);
    $("#modalSerieBase").val(serie);
    $("#modalObservacionesBase").val(observaciones);
    $("#cantidadACrear").text(cantidad);

    // Guardar referencia a la fila actual en el modal
    $("#modalProcesarCantidad").data("filaActual", filaActual);
    $("#modalProcesarCantidad").data("activoId", activoId);
    $("#modalProcesarCantidad").data("activoNombre", activoNombre);
    $("#modalProcesarCantidad").data("activoMarca", activoMarca);

    // Mostrar el modal
    $("#modalProcesarCantidad").modal("show");
  });

  // Manejador para el botón "Confirmar Procesar" del modal
  $(document).on("click", "#btnConfirmarProcesar", function () {
    const filaActual = $("#modalProcesarCantidad").data("filaActual");
    const activoId = $("#modalProcesarCantidad").data("activoId");
    const activoNombre = $("#modalProcesarCantidad").data("activoNombre");
    const activoMarca = $("#modalProcesarCantidad").data("activoMarca");
    
    const cantidad = parseInt($("#modalCantidadTotal").val()) || 1;
    const serieBase = $("#modalSerieBase").val().trim();
    const observacionesBase = $("#modalObservacionesBase").val().trim();

    // Validar serie base
    if (!serieBase) {
      NotificacionToast("error", "Debe ingresar una serie base.");
      return;
    }

    // Obtener los valores de la fila principal
    const valor = filaActual.find("input[name='valor[]']").val();
    const ambienteId = filaActual.find("select.ambiente").val();
    const categoriaId = filaActual.find("select.categoria").val();

    // Cerrar el modal
    $("#modalProcesarCantidad").modal("hide");

    // Mostrar loading
    Swal.fire({
      title: "Procesando",
      text: "Creando activos individuales...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Marcar la fila original como procesada y ocultar el botón procesar
    filaActual.attr("data-procesado", "true");
    filaActual.find(".btnProcesarCantidad").hide();
    filaActual.find("input.cantidad").prop("disabled", true).val(1);
    
    // Actualizar la serie de la fila original
    filaActual.find("input[name='serie[]']").val(serieBase + "-1");
    filaActual.find("textarea[name='observaciones[]']").val(observacionesBase);

    // Crear las filas individuales
    for (let i = 1; i < cantidad; i++) {
      const numeroFilas = $("#tbldetalleactivoreg").find("tbody tr").length;
      const selectAmbiente = `<select class='form-control form-control-sm ambiente' name='ambiente[]' id="comboAmbiente${numeroFilas}"></select>`;
      const selectCategoria = `<select class='form-control form-control-sm categoria' name='categoria[]' id="comboCategoria${numeroFilas}"></select>`;
      const inputEstadoActivo = `<input type="text" class="form-control form-control-sm" name="estado_activo[]" value="Operativa" disabled>`;
      const inputCantidad = `<input type="number" class="form-control form-control-sm cantidad" name="cantidad[]" value="1" min="1" disabled>`;

      const nuevaFila = `<tr data-id='${activoId}' class='table-info activo-procesado' data-procesado='true' data-activo-nombre="${activoNombre}" data-activo-marca="${activoMarca}">
                    <td>${activoId}</td>
                    <td>${activoNombre} <small class="text-muted">(${i + 1}/${cantidad})</small></td>
                    <td>${activoMarca}</td>
                    
                    <td><input type="text" class="form-control form-control-sm" name="serie[]" placeholder="Serie ${i + 1}" value="${serieBase}-${i + 1}"></td>
                    <td>${inputEstadoActivo}</td>
                    <td>${selectAmbiente}</td>
                    <td>${selectCategoria}</td>
                    <td><input type="text" class="form-control form-control-sm" name="valor[]" placeholder="Valor" value="${valor}"></td>
                    <td>${inputCantidad}</td>
                    <td><textarea class='form-control form-control-sm' name='observaciones[]' rows='1' placeholder='Observaciones'>${observacionesBase}</textarea></td>
                    <td>
                      <button type='button' class='btn btn-danger btn-sm btnQuitarActivo' title="Eliminar esta fila">
                        <i class='fa fa-trash'></i>
                      </button>
                    </td>
                </tr>`;

      $("#tbldetalleactivoreg tbody").append(nuevaFila);

      // Cargar combos para la nueva fila
      ListarCombosAmbiente(`comboAmbiente${numeroFilas}`);
      ListarCombosCategoria(`comboCategoria${numeroFilas}`);

      // Establecer los valores seleccionados en los combos
      setTimeout(() => {
        $(`#comboAmbiente${numeroFilas}`).val(ambienteId).trigger("change");
        $(`#comboCategoria${numeroFilas}`).val(categoriaId).trigger("change");
      }, 500);
    }

    // Cerrar loading y mostrar éxito
    setTimeout(() => {
      Swal.close();
      NotificacionToast("success", `Se han creado ${cantidad} filas individuales para el activo "${activoNombre}".`);
    }, 1000);
  });

  // Actualizar contador cuando cambie la cantidad en el modal
  $(document).on("input", "#modalCantidadTotal", function () {
    const cantidad = parseInt($(this).val()) || 0;
    $("#cantidadACrear").text(cantidad);
  });

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
    $("#tbldetalleactivoreg tbody tr").each(function () {
      let row = $(this);
      /*activos.push({
        IdDocIngresoAlm: parseInt($("#inputDocIngresoAlm").val()) || null,
        IdArticulo: parseInt(row.find("td:eq(0)").text()) || null,
        Serie: row.find("input[name='serie[]']").val() || null,
        IdAmbiente: parseInt(row.find("select.ambiente").val()) || null,
        IdCategoria: parseInt(row.find("select.categoria").val()) || null,
        Observaciones: row.find("textarea[name='observaciones[]']").val() || "",
        IdEstado: 1, // Estado por defecto: Operativo
        Garantia: 0, // Por defecto sin garantía
        IdSucursal: null, // Se obtiene de la sesión en el backend
        UserMod: userMod,
        Accion: 1, // 1 = Insertar
      });*/

      let cantidad = parseInt(row.find("input.cantidad").val()) || 1;

      for (let i = 0; i < cantidad; i++) {
        activos.push({
          IdDocIngresoAlm: parseInt($("#inputDocIngresoAlm").val()) || null,
          IdArticulo: parseInt(row.find("td:eq(0)").text()) || null,
          Serie: row.find("input[name='serie[]']").val() || null,
          IdAmbiente: parseInt(row.find("select.ambiente").val()) || null,
          IdCategoria: parseInt(row.find("select.categoria").val()) || null,
          ValorAdquisicion:
            parseFloat(row.find("input[name='valor[]']").val()) || 0,
          Observaciones:
            row.find("textarea[name='observaciones[]']").val() || "",
          IdEstado: 1, // Estado por defecto: Operativo
          Garantia: 0, // Por defecto sin garantía
          IdSucursal: null, // Se obtiene de la sesión en el backend
          UserMod: userMod,
          Accion: 1, // 1 = Insertar
          Cantidad: 1, // Agregar la cantidad al objeto
        });
      }
    });

    // Validar que todos los campos requeridos estén presentes
    let activosValidos = activos.every((activo) => {
      return (
        activo.IdDocIngresoAlm &&
        activo.IdArticulo &&
        activo.IdAmbiente &&
        activo.IdCategoria
      );
    });

    if (!activosValidos) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Todos los campos son requeridos para cada activo",
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

    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=GuardarActivos",
      type: "POST",
      data: {
        action: "GuardarActivos",
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
    $("#activosContainer .activo-manual-form").each(function () {
      const form = $(this);
      const activo = {
        //IdDocumentoVenta: form.find("input[name='idDocumentoVenta[]']").val(),
        //IdOrdendeCompra: form.find("input[name='idOrdendeCompra[]']").val(),
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
        FechaAdquisicion: form.find("input[name='fechaAdquisicion[]']").val(),
        Cantidad: parseInt(form.find("input[name='Cantidad[]']").val()) || 1,
      };
      activos.push(activo);
    });

    if (activos.length > 0) {
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
              timer: 1500,
            }).then(() => {
              $("#activosContainer").empty();
              activoFormCount = 0;
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
  });

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
                        
                        <!-- Columna Derecha - Componentes -->
                        <div class="col-lg-6">
                            <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px;">
                                <div class="card-header border-0 py-4" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 16px 16px 0 0;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 p-2 rounded-circle" style="background: rgba(6, 182, 212, 0.1);">
                                                <i class="fas fa-puzzle-piece text-cyan-600"></i>
                                            </div>
                                            <h6 class="mb-0 fw-bold text-cyan-700">Componentes del Activo</h6>
                                        </div>
                                        <span class="badge bg-cyan-500 text-white px-3 py-2 rounded-pill">
                                            <i class="fas fa-cogs me-1"></i>
                                            Activo
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="componentesActivo" class="p-4">
                                        <div class="d-flex align-items-center justify-content-center py-5">
                                            <div class="text-center">
                                                <div class="spinner-border text-cyan-500 mb-3" role="status" style="width: 3rem; height: 3rem;">
                                                    <span class="visually-hidden">Cargando...</span>
                                                </div>
                                                <p class="text-dark mb-0 fw-semibold">Cargando componentes...</p>
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
                    <button type="button" class="btn btn-outline-cyan btnEditarDesdeModal px-4 py-2 rounded-pill shadow-sm" data-id-activo="${activo.idActivo}" style="min-width: 120px; border-color: #06b6d4; color: #0891b2;">
                        <i class="fas fa-edit me-2"></i>Editar
                    </button>
                    <button type="button" class="btn btn-outline-emerald btnImprimirDesdeModal px-4 py-2 rounded-pill shadow-sm" data-id-activo="${activo.idActivo}" style="min-width: 120px; border-color: #10b981; color: #059669;">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </button>
                    <button type="button" class="btn btn-cyan btnAsignarResponsable px-4 py-2 rounded-pill shadow-sm" data-id-activo="${activo.idActivo}" style="min-width: 140px; background-color: #06b6d4; border-color: #06b6d4; color: white;">
                        <i class="fas fa-user-edit me-2"></i>Asignar
                    </button>
                    <button type="button" class="btn btn-outline-slate btnDarBajaDesdeModal px-4 py-2 rounded-pill shadow-sm" data-id-activo="${activo.idActivo}" style="min-width: 120px; border-color: #64748b; color: #475569;">
                        <i class="fas fa-trash-alt me-2"></i>Dar de Baja
                    </button>
                    <button type="button" class="btn btn-outline-slate px-4 py-2 rounded-pill shadow-sm" data-bs-dismiss="modal" style="min-width: 100px; border-color: #64748b; color: #475569;">
                        <i class="fas fa-times me-2"></i>Cerrar
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
.text-teal-500 { color: #14b8a6 !important; }
.text-dark { color: #64748b !important; }
.text-slate-600 { color: #475569 !important; }
.text-slate-700 { color: #334155 !important; }

.bg-emerald-500 { background-color: #10b981 !important; }
.bg-cyan-500 { background-color: #06b6d4 !important; }

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
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
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
                    <table class="table table-hover mb-0">
                      <thead class="table-light">
                        <tr>
                          <th class="border-0 py-3">Código</th>
                          <th class="border-0 py-3">Componente</th>
                          <th class="border-0 py-3">Estado</th>
                        </tr>
                      </thead>
                      <tbody>`;

                componentesRes.data.forEach((item) => {
                  componentesHtml += `
                    <tr>
                      <td class="py-3">
                        <code class="text-primary">${
                          item.CodigoComponente
                        }</code>
                      </td>
                      <td class="py-3">
                        <div class="fw-semibold">${item.NombreComponente}</div>
                        <small class="text-muted">${
                          item.Descripcion || "-"
                        }</small>
                      </td>
                      <td class="py-3">
                        <span class="badge bg-success-subtle text-success">${
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
                $("#componentesActivo").html(
                  "<p>No se encontraron componentes para este activo.</p>"
                );
              }
            },
            error: function () {
              $("#componentesActivo").html(
                "<p>Error al cargar los componentes del activo.</p>"
              );
            },
          });

          // Mostrar el modal
          $("#modalDetallesActivo").modal("show");
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
      `../../views/Reportes/reporteActivo.php?idActivo=${idActivo}`,
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

  // function cargarArticulosPorDocIngreso(idDoc, callback) {
  //   $.ajax({
  //     url: "../../controllers/GestionarActivosController.php?action=articulos_por_doc",
  //     type: "POST",
  //     data: { IdDocIngresoAlm: idDoc },
  //     dataType: "json",
  //     success: (res) => {
  //       if (res.status) {
  //         let options = '<option value="">Seleccione un artículo</option>';
  //         res.data.forEach((item) => {
  //           options += `<option value="${item.IdArticulo}">${item.Nombre}</option>`;
  //         });
  //         $("#IdArticulo").html(options);
  //         if (typeof callback === "function") {
  //           callback();
  //         }
  //       }
  //     },
  //   });
  // }

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

function listarActivosModal(docIngresoAlm) {
  if ($.fn.DataTable.isDataTable("#tbllistarActivos")) {
    $("#tbllistarActivos").DataTable().clear().destroy();
  }
  $("#tbllistarActivos").DataTable({
    dom: "Bfrtip",
    responsive: false,
    destroy: true,
    ajax: {
      url: "../../controllers/GestionarActivosController.php?action=articulos_por_doc",
      type: "POST",
      dataType: "json",
      data: { IdDocIngresoAlm: docIngresoAlm },
      dataSrc: function (json) {
        console.log("Respuesta del backend: ", json);
        return json.data || [];
      },
    },
    columns: [
      { data: "IdArticulo" },
      { data: "Nombre" },
      { data: "Marca" },
      { data: "Empresa" },
      { data: "IdUnidadNegocio" },
      { data: "NombreLocal" },
      {
        data: null,
        render: function (data, type, row) {
          return (
            '<button class="btn btn-success btn-sm btnSeleccionarActivo" data-id="' +
            row.idArticulo +
            '"><i class="fa fa-check"></i></button>'
          );
        },
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
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
  // Primero verificar si el artículo ya existe
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=verificarArticuloExistente",
    type: "POST",
    data: {
      IdDocIngresoAlm: $("#inputDocIngresoAlm").val(),
      IdArticulo: activo.id,
      IdEmpresa: activo.empresa,
      IdSucursal: activo.sucursal,
    },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        if (res.existe) {
          NotificacionToast(
            "error",
            `El artículo <b>${activo.nombre}</b> ya ha sido registrado con este documento de ingreso.`
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
        var inputCantidad = `<input type="number" class="form-control form-control-sm cantidad" name="cantidad[]" value="1" min="1" data-activo-id="${activo.id}">`;
        var btnProcesar = `<button type="button" class="btn btn-warning btn-sm btnProcesarCantidad me-1" data-activo-id="${activo.id}" title="Procesar cantidad múltiple"><i class="fa fa-cogs"></i> Procesar</button>`;

        var nuevaFila = `<tr data-id='${activo.id}' class='table-success agregado-temp activo-principal' data-activo-nombre="${activo.nombre}" data-activo-marca="${activo.marca}">
                    <td>${activo.id}</td>
                    <td>${activo.nombre}</td>
                    <td>${activo.marca}</td>
                    
                    <td><input type="text" class="form-control form-control-sm" name="serie[]" placeholder="Serie"></td>
                    <td>${inputEstadoActivo}</td>
                    <td>${selectAmbiente}</td>
                    <td>${selectCategoria}</td>
                    <td><input type="text" class="form-control form-control-sm" name="valor[]" placeholder="Valor"></td>
                    <td>${inputCantidad}</td>
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

        setTimeout(function () {
          $("#tbldetalleactivoreg tbody tr.agregado-temp").removeClass(
            "table-success agregado-temp"
          );
        }, 1000);

        NotificacionToast(
          "success",
          `Activo <b>${activo.nombre}</b> agregado al detalle.`
        );
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
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
  });
}

// function listarActivosTableModal(articulo) {
//   $("#tblTodosActivos").DataTable({
//     aProcessing: true,
//     aServerSide: true,
//     layout: {
//       topStart: {
//         buttons: [
//           {
//             extend: "excelHtml5",
//             title: "Listado Activos",
//             text: "<i class='fas fa-file-excel'></i> Exportar",
//             autoFilter: true,
//             sheetName: "Data",
//             exportOptions: {
//               columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
//             },
//           },
//           "pageLength",
//           "colvis",
//         ],
//       },
//       bottom: "paging",
//       bottomStart: null,
//       bottomEnd: null,
//     },
//     lengthChange: false,
//     colReorder: true,
//     autoWidth: false,
//     destroy: true,
//     ajax: {
//       url: "../../controllers/GestionarActivosController.php?action=ConsultarActivosRelacionados",
//       type: "POST",
//       data: {
//         IdArticulo: articulo.IdArticulo,
//         IdActivo: articulo.IdActivo,
//       },
//       dataType: "json",
//       dataSrc: function (json) {
//         return json || [];
//       },
//     },
//     columns: [
//       {
//         data: null,
//         render: (data, type, row) =>
//           `<div class="btn-group">
//             <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
//             <i class="fas fa-cog"></i>
//             </button>
//             <div class="dropdown-menu">
//               <button class="dropdown-item btnEditarActivo" type="button">
//                 <i class="fas fa-edit text-warning"></i> Editar
//               </button>
//               <button class="dropdown-item btnAsignarResponsable" type="button">
//                 <i class="fas fa-user-plus text-info"></i> Asignar Responsable
//               </button>
//               <button class="dropdown-item btnVerHistorial" type="button">
//                 <i class="fas fa-history text-primary"></i> Ver Historial
//               </button>
//               <button class="dropdown-item btnImprimirActivo" type="button">
//                 <i class="fas fa-print text-secondary"></i> Imprimir Activo
//               </button>
//               <button class="dropdown-item btnDarBaja" type="button">
//                 <i class="fas fa-ban text-danger"></i> Dar de Baja
//               </button>
//             </div>
//         </div>`,
//       },
//       { data: "idActivo", visible: false, searchable: false },
//       { data: "CodigoActivo" },
//       { data: "NumeroSerie" },
//       { data: "NombreArticulo" },
//       { data: "MarcaArticulo" },
//       { data: "Sucursal" },
//       { data: "Proveedor" },
//       { data: "Estado" },
//       { data: "valorAdquisicion" },
//       { data: "idResponsable" },
//       { data: "idArticulo", visible: false },
//       { data: "idAmbiente", visible: false },
//       { data: "idCategoria", visible: false },
//       { data: "DocIngresoAlmacen", visible: false },
//       { data: "fechaAdquisicion", visible: false },
//       { data: "observaciones", visible: false },
//     ],
//     language: {
//       url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
//     },
//   });
// }

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

  // Abrir el reporte en una nueva ventanaMore actions
  // window.open(
  //   `/app/views/Reportes/index.php?idActivo=${datos.idActivo}`,
  //   "_blank"
  // );
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
                <label for="idDocumentoVenta_${activoFormCount}">Documento de Venta</label>
                <input type="text" name="idDocumentoVenta[]" id="idDocumentoVenta_${activoFormCount}" class="form-control" placeholder="Doc. Venta"/>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="idOrdendeCompra_${activoFormCount}">Orden de Compra</label>
                <input type="text" name="idOrdendeCompra[]" id="idOrdendeCompra_${activoFormCount}" class="form-control" placeholder="Orden Compra"/>
              </div>
            </div>
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
          </div>
        </form>
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
$(document).ready(function() {
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
    </style>
  `;
  
  // Agregar los estilos al head del documento
  $('head').append(modalStyles);
});