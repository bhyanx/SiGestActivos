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

  $("#btnGuardarActivosManuales").on("click", function (e) {
    e.preventDefault();

    const activos = [];
    $("#activosContainer .activo-manual-form").each(function () {
      const form = $(this);
      const activo = {
        IdActivo: null,
        IdDocumentoVenta: form.find("input[name='idDocumentoVenta[]']").val(),
        IdOrdendeCompra: form.find("input[name='idOrdendeCompra[]']").val(),
        Nombre: form.find("input[name='nombre[]']").val(),
        Descripcion: form.find("textarea[name='Descripcion[]']").val(),
        Serie: form.find("input[name='serie[]']").val(),
        IdEstado: form.find("select[name='Estado[]']").val(),
        Garantia: 0,
        IdEmpresa: null,
        IdSucursal: null,
        IdResponsable: form.find("select[name='Responsable[]']").val(),
        IdProveedor: form.find("select[name='Proveedor[]']").val(),
        Observaciones: form.find("textarea[name='Observaciones[]']").val(),
        IdAmbiente: form.find("select[name='Ambiente[]']").val(),
        IdCategoria: form.find("select[name='Categoria[]']").val(),
        ValorAdquisicion: parseFloat(
          form.find("input[name='ValorAdquisicion[]']").val()
        ),
        FechaAdquisicion: form.find("input[name='fechaAdquisicion[]']").val(),
        Cantidad: parseInt(form.find("input[name='Cantidad[]']").val()) || 1,
        Accion: 1,
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
        url: "../../controllers/GestionarActivosController.php?action=GuardarActivosManuales",
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
            $("#CodigoActivo").val(data.CodigoActivo);
            $("#SerieActivo").val(data.NumeroSerie);
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
          // let detallesHtml = `
          //   <div class="modal fade" id="modalDetallesActivo" tabindex="-1" role="dialog" aria-labelledby="modalDetallesLabel" aria-hidden="true">
          //     <div class="modal-dialog modal-xl" role="document">
          //       <div class="modal-content">
          //         <div class="modal-header bg-primary text-white">
          //           <h5 class="modal-title" id="modalDetallesLabel">Detalles del Activo: ${
          //             datos.CodigoActivo
          //           }</h5>
          //           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          //             <span aria-hidden="true">&times;</span>
          //           </button>
          //         </div>
          //         <div class="modal-body">
          //           <div class="row">
          //             <div class="col-md-6">
          //               <h6><strong>Información General</strong></h6>
          //               <table class="table table-bordered table-sm">
          //                 <tr><th>ID Activo</th><td>${activo.idActivo}</td></tr>
          //                 <tr><th>Código</th><td>${
          //                   activo.CodigoActivo
          //                 }</td></tr>
          //                 <tr><th>Serie</th><td>${
          //                   activo.NumeroSerie || "-"
          //                 }</td></tr>
          //                 <tr><th>Nombre</th><td>${
          //                   activo.NombreActivoVisible
          //                 }</td></tr>
          //                 <tr><th>Marca</th><td>${activo.Marca || "-"}</td></tr>
          //                 <tr><th>Estado</th><td>${
          //                   activo.Estado || "-"
          //                 }</td></tr>
          //                 <tr><th>Categoría</th><td>${
          //                   activo.Categoria || "-"
          //                 }</td></tr>
          //                 <tr><th>Valor Adquisición</th><td>${
          //                   activo.valorAdquisicion
          //                 }</td></tr>
          //                 <tr><th>Fecha Adquisición</th><td>${
          //                   activo.fechaAdquisicion || "-"
          //                 }</td></tr>
          //                 <tr><th>Responsable</th><td>${
          //                   activo.Responsable || "-"
          //                 }</td></tr>
          //                 <tr><th>Ambiente</th><td>${
          //                   activo.Ambiente || "-"
          //                 }</td></tr>
          //                 <tr><th>Sucursal</th><td>${
          //                   activo.Sucursal || "-"
          //                 }</td></tr>
          //                 <tr><th>Proveedor</th><td>${
          //                   activo.Proveedor || "-"
          //                 }</td></tr>
          //                 <tr><th>Observaciones</th><td>${
          //                   activo.observaciones || "-"
          //                 }</td></tr>
          //               </table>
          //             </div>
          //             <div class="col-md-6">
          //               <h6><strong>Movimientos del Activo</strong></h6>
          //               <div id="movimientosActivo"></div>
          //               <h6 class="mt-3"><strong>Componentes del Activo</strong></h6>
          //               <div id="componentesActivo"></div>
          //             </div>
          //           </div>
          //         </div>
          //         <div class="modal-footer">
          //           <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          //         </div>
          //       </div>
          //     </div>
          //   </div>`;

          // Eliminar modal anterior si existe
          $("#modalDetallesActivo").remove();
          // Agregar nuevo modal al body con el diseño mejorado
          let modalHtml = `

    <!-- Modal Mejorado -->
    <div class="modal fade" id="modalDetallesActivo" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content shadow-lg">
                <!-- Header del Modal -->
                <div class="modal-header bg-gradient-primary text-white border-0">
                    <div class="d-flex align-items-center">
                        <i class="fab fa-dropbox m-3 fs-4"></i>
                        <div>
                            <h5 class="modal-title mb-0" id="modalDetallesLabel">Detalles del Activo</h5>
                            <small class="opacity-75">Código: ${activo.CodigoActivo}</small>
                        </div>
                    </div>
                </div>

                <!-- Body del Modal -->
                <div class="modal-body p-0">
                    <div class="container-fluid p-4">
                        <div class="row g-4">
                            <!-- Información General -->
                            <div class="col-lg-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light border-0 py-3">
                                        <h6 class="card-title mb-0 text-primary">
                                            <i class="fas fa-info-circle me-2"></i>Información General
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">ID Activo</label>
                                                    <div class="fw-semibold"> ${activo.idActivo}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Código</label>
                                                    <div class="fw-semibold"> ${activo.CodigoActivo}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Serie</label>
                                                    <div class="fw-semibold"> ${activo.NumeroSerie}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Estado</label>
                                                    <span class="badge bg-success"> ${activo.Estado}</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Nombre</label>
                                                    <div class="fw-semibold"> ${activo.NombreActivoVisible}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Marca</label>
                                                    <div class="fw-semibold"> ${activo.Marca}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Categoría</label>
                                                    <div class="fw-semibold">${activo.Categoria}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Valor Adquisición</label>
                                                    <div class="fw-semibold text-success">${activo.valorAdquisicion}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Fecha Adquisición</label>
                                                    <div class="fw-semibold">${activo.fechaAdquisicion}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Responsable</label>
                                                    <div class="fw-semibold">${activo.NombreResponsable}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Ambiente</label>
                                                    <div class="fw-semibold">${activo.Ambiente}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Sucursal</label>
                                                    <div class="fw-semibold">${activo.Sucursal}</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Proveedor</label>
                                                    <div class="fw-semibold">${activo.Proveedor}</div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="info-item">
                                                    <label class="form-label text-muted small mb-1">Observaciones</label>
                                                    <div class="fw-semibold">${activo.observaciones}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Componentes -->
                            <div class="col-lg-6">
                                <!-- Componentes -->
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light border-0 py-3">
                                        <h6 class="card-title mb-0 text-primary">
                                            <i class="fas fa-puzzle-piece me-2"></i>Componentes del Activo
                                        </h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div id="componentesActivo">
                                            <p>Cargando componentes...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer del Modal -->
                <div class="modal-footer border-0 bg-light">
                    <div class="d-flex">
                        <button type="button" class="btn btn-outline-primary btnEditarDesdeModal m-2" data-id-activo="${activo.idActivo}">
                            <i class="fas fa-edit me-2"></i>Editar
                        </button>
                        <button type="button" class="btn btn-outline-success btnImprimirDesdeModal m-2" data-id-activo="${activo.idActivo}">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </button>
                        <button type="button" class="btn btn-outline-danger btnDarBajaDesdeModal m-2" data-id-activo="${activo.idActivo}">
                            <i class="fas fa-trash-alt me-2"></i>Baja
                        </button>
                        <button type="button" class="btn btn-outline-info btnAsignarResponsable m-2" data-id-activo="${activo.idActivo}">
                            <i class="fas fa-user-edit me-2"></i>Asignar Responsable
                        </button> 
                        <button type="button" class="btn btn-danger m-2" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>`;
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
                          <th class="border-0 py-3">Fecha</th>
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
                      <td class="py-3">${item.FechaAsignacion || "-"}</td>
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
            $("#CodigoActivo").val(data.CodigoActivo);
            $("#SerieActivo").val(data.NumeroSerie);
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
          dropdownParent: $("#divtblactivos .modal-body"),
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
      url: "../../controllers/GestionarActivosController.php?action=ConsultarActivosRelacionados",
      type: "POST",
      data: {
        IdArticulo: "",
        IdActivo: "",
        pIdCategoria: 6,
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
              <button class="btn btn-primary btnVerDetalles" type="button">
                <i class="fas fa-eye text-white"></i>
              </button>
        </div>`,
      },
      { data: "idActivo", visible: false, searchable: false },
      { data: "CodigoActivo" },
      { data: "NumeroSerie" },
      { data: "NombreActivoVisible" },
      { data: "Marca" },
      { data: "Sucursal" },
      { data: "Proveedor" },
      { data: "Estado" },
      { data: "valorAdquisicion" },
      { data: "idResponsable" },
      { data: "TotalRelacionadosPorArticulo" },
      { data: "TotalRelacionadosPorPadre" },
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
                <input type="number" name="Cantidad[]" id="Cantidad_${activoFormCount}" class="form-control" placeholder="Ej. 1" value="1" min="1" required readonly disabled>
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
  newForm
    .find(`[name='Responsable[]']`)
    .select2({ dropdownParent: newForm, theme: "bootstrap4", width: "100%" });
  newForm
    .find(`[name='Estado[]']`)
    .select2({ dropdownParent: newForm, theme: "bootstrap4", width: "100%" });
  newForm
    .find(`[name='Categoria[]']`)
    .select2({ dropdownParent: newForm, theme: "bootstrap4", width: "100%" });
  newForm
    .find(`[name='Ambiente[]']`)
    .select2({ dropdownParent: newForm, theme: "bootstrap4", width: "100%" });

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
