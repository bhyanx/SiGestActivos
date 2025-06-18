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
      $("#divtblactivos").hide();
      $("#divlistadoactivos").hide();
      $("#tituloModalMovimiento").html(
        '<i class="fa fa-plus-circle"></i> Registrar Movimiento'
      );
      $("#frmMovimiento")[0].reset();
      $("#ModalArticulos").modal("show");
    });

  // ? INICIO: SE COMENTO LA CARGA DE COMBOS EN EL MODAL YA QUE NO SE UTILIZARÁ
  $("#btnCrearActivo").click(() => {
    $("#divModalRegistroManualActivo").modal("show");
  });
  // ? FIN: SE COMENTO LA CARGA DE COMBOS EN EL MODAL YA QUE NO SE UTILIZARÁ

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
    $("#frmDetalleMovimiento")[0].reset();
    $("#CodigoActivo, #SucursalActual, #AmbienteActual").val("");
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
    $(this).closest("tr").remove();
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
      url: "../../controllers/GestionarActivosController.php?action=RegistrarPrueba",
      type: "POST",
      data: {
        action: "RegistrarPrueba",
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

  $(document).on("click", ".btnEditarActivo", function () {
    const fila = $(this).closest("tr");
    const datos = $("#tblTodosActivos").DataTable().row(fila).data();

    if (!datos) {
      Swal.fire(
        "Error",
        "No se pudo obtener la información del activo.",
        "error"
      );
      return;
    }

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
            $("#IdActivo").val(data.idActivo);
            $("#CodigoActivo").val(data.CodigoActivo);
            $("#SerieActivo").val(data.NumeroSerie);
            $("#DocIngresoAlmacen").val(data.DocIngresoAlmacen);
            $("#IdArticulo").val(data.idArticulo);
            $("#nombreArticulo").val(data.NombreArticulo);
            $("#marca").val(data.MarcaArticulo);
            $("#fechaAdquisicion").val(data.fechaAdquisicion);
            $("#Garantia").prop("checked", data.garantia == 1);
            $("#Observaciones").val(data.observaciones);
            $("#VidaUtil").val(data.vidaUtil);
            $("#ValorAdquisicion").val(data.valorAdquisicion);

            // Asignar valores a los combos
            $("#IdEstado").val(data.idEstado).trigger("change");
            $("#Ambiente").val(data.idAmbiente).trigger("change");
            $("#Categoria")
              .val(data.idCategoria)
              .trigger("change")
              .prop("disabled", true);

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
    const fila = $(this).closest("tr");
    const datos = $("#tblTodosActivos").DataTable().row(fila).data();

    if (!datos) {
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
      data: { idActivo: datos.idActivo },
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
          $("#frmAsignarResponsable").data("idActivo", datos.idActivo);

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

  // Manejador para el botón de Ver Historial
  $(document).on("click", ".btnVerHistorial", function () {
    const fila = $(this).closest("tr");
    const datos = $("#tblTodosActivos").DataTable().row(fila).data();

    if (!datos) {
      Swal.fire(
        "Error",
        "No se pudo obtener la información del activo.",
        "error"
      );
      return;
    }

    // Aquí puedes implementar la lógica para mostrar el historial
    Swal.fire({
      title: "Historial del Activo",
      text: "Funcionalidad en desarrollo",
      icon: "info",
    });
  });

  // Manejador para el botón de Dar de Baja
  $(document).on("click", ".btnDarBaja", function () {
    const fila = $(this).closest("tr");
    const datos = $("#tblTodosActivos").DataTable().row(fila).data();

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

  function cargarArticulosPorDocIngreso(idDoc, callback) {
    $.ajax({
      url: "../../controllers/GestionarActivosController.php?action=articulos_por_doc",
      type: "POST",
      data: { IdDocIngresoAlm: idDoc },
      dataType: "json",
      success: (res) => {
        if (res.status) {
          let options = '<option value="">Seleccione un artículo</option>';
          res.data.forEach((item) => {
            options += `<option value="${item.IdArticulo}">${item.Nombre}</option>`;
          });
          $("#IdArticulo").html(options);
          if (typeof callback === "function") {
            callback();
          }
        }
      },
    });
  }

  // Modificar el evento submit del formulario
  $("#frmEditarActivo").on("submit", function (e) {
    e.preventDefault();

    // Solo enviamos los campos que realmente necesitamos actualizar
    const datos = {
      IdActivo: $("#idActivo").val() || null,
      Serie: $("#SerieActivo").val() || null,
      IdEstado: $("#Estado").val() || null,
      IdAmbiente: $("#Ambiente").val() || null,
      IdCategoria: $("#Categoria").val() || null, // La categoría se mantiene pero no es editable
      Observaciones: $("#Observaciones").val() || null,
      UserMod: userMod,
      Accion: 2,
    };

    // Validar campos requeridos
    if (!datos.IdActivo) {
      Swal.fire("Error", "El ID del activo es requerido", "error");
      return;
    }

    if (!datos.IdEstado) {
      Swal.fire("Error", "El estado es requerido", "error");
      return;
    }

    if (!datos.IdAmbiente) {
      Swal.fire("Error", "El ambiente es requerido", "error");
      return;
    }

    // Convertir valores numéricos
    datos.IdActivo = parseInt(datos.IdActivo);
    datos.IdEstado = parseInt(datos.IdEstado);
    datos.IdAmbiente = parseInt(datos.IdAmbiente);
    datos.IdCategoria = parseInt(datos.IdCategoria);

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
      // async: false,
      success: (res) => {
        if (res.status) {
          $("#Estado").html(res.data.estado).trigger("change");
          $("#Estado").select2({
            theme: "bootstrap4",
            width: "100%",
          });
          $("#Ambiente").html(res.data.ambientes).trigger("change");
          $("#Ambiente").select2({
            theme: "bootstrap4",
            width: "100%",
          });
          $("#Categoria").html(res.data.categorias).trigger("change");
          $("#Categoria").select2({
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
        var inputCantidad = `<input type="number" class="form-control form-control-sm cantidad" name="cantidad[]" value="1" min="1">`;

        var nuevaFila = `<tr data-id='${activo.id}' class='table-success agregado-temp'>
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
                    <td><button type='button' class='btn btn-danger btn-sm btnQuitarActivo'><i class='fa fa-trash'></i></button></td>
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
        $("#filtroSucursal").html(res.data.sucursales).trigger("change");
        $("#filtroAmbiente").html(res.data.ambientes).trigger("change");
        $("#filtroCategoria, #filtroSucursal, #filtroAmbiente").select2({
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
      url: "../../controllers/GestionarActivosController.php?action=Consultar",
      type: "POST",
      dataType: "json",
      dataSrc: function (json) {
        return json || [];
      },
    },
    columns: [
      {
        data: null,
        render: (data, type, row) =>
          `<div class="btn-group">
            <button type="button" class="btn btn-info btn-sm dropdown-toggle align-content-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-cog"></i>
            </button>
            <div class="dropdown-menu">
              <button class="dropdown-item btnVerDetalle" type="button">
                <i class="fas fa-bars text-success"></i> Detalle
              </button>
              <button class="dropdown-item btnImprimirActivo" type="button">
                <i class="fas fa-print text-warning"></i> Imprimir Ingreso
              </button>
            </div>
          </div>`,
      },
      { data: "Codigo" },
      { data: "NombreActivo" },
      { data: "IdEmpresa" },
      { data: "Locacion" },
      { data: "Cantidad" },
      { data: "ValorTotal" },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
  });
}

function listarActivosTableModal(articulo) {
  $("#tblTodosActivos").DataTable({
    aProcessing: true,
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
      url: "../../controllers/GestionarActivosController.php?action=ConsultarActivosRelacionados",
      type: "POST",
      data: {
        IdArticulo: articulo.IdArticulo,
      },
      dataType: "json",
      dataSrc: function (json) {
        return json || [];
      },
    },
    columns: [
      {
        data: null,
        render: (data, type, row) =>
          `<div class="btn-group">
            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-cog"></i>
            </button>
            <div class="dropdown-menu">
              <button class="dropdown-item btnEditarActivo" type="button">
                <i class="fas fa-edit text-warning"></i> Editar
              </button>
              <button class="dropdown-item btnAsignarResponsable" type="button">
                <i class="fas fa-user-plus text-info"></i> Asignar Responsable
              </button>
              <button class="dropdown-item btnVerHistorial" type="button">
                <i class="fas fa-history text-primary"></i> Ver Historial
              </button>
              <button class="dropdown-item btnImprimirActivo" type="button">
                <i class="fas fa-print text-secondary"></i> Imprimir Activo
              </button>
              <button class="dropdown-item btnDarBaja" type="button">
                <i class="fas fa-ban text-danger"></i> Dar de Baja
              </button>
            </div>
        </div>`,
      },
      { data: "idActivo", visible: false, searchable: false },
      { data: "CodigoActivo" },
      { data: "NumeroSerie" },
      { data: "NombreArticulo" },
      { data: "MarcaArticulo" },
      { data: "Sucursal" },
      { data: "Proveedor" },
      { data: "Estado" },
      { data: "valorAdquisicion" },
      { data: "idResponsable" },
      { data: "idArticulo", visible: false },
      { data: "idAmbiente", visible: false },
      { data: "idCategoria", visible: false },
      { data: "DocIngresoAlmacen", visible: false },
      { data: "fechaAdquisicion", visible: false },
      { data: "observaciones", visible: false },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
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
  listarActivosTableModal({ IdArticulo: datos.Codigo });
});

// Add the event handler for the print button
$(document).on("click", ".btnImprimirActivo", function () {
  const fila = $(this).closest("tr");
  const datos = $("#tblTodosActivos").DataTable().row(fila).data();

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
