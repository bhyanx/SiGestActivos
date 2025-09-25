$(document).ready(function () {
  init();
});

function init() {
  // Verificar e inicializar estados automáticamente
  verificarEInicializarEstados();

  // Inicializar la tabla una sola vez
  if (!$.fn.DataTable.isDataTable("#tblMovimientos")) {
    ListarMovimientos();
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
        ListarMovimientos();
      }
    });

  $(document).on(
    "change",
    "#IdSucursalOrigen, #IdSucursalDestino",
    function () {
      setSucursalOrigenDestino();
    }
  );

  // Evento para cambiar entre movimientos enviados y recibidos
  $(document).on("change", "#filtroTipoListado", function () {
    // Recargar la tabla con el nuevo tipo
    if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
      ListarMovimientos();
    }
  });

  $("#btnvolver")
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

  $(document).on("click", "#btnBuscarIdItem, .btnagregardet", function () {
    console.log("Abriendo modal para movimientos");

    // Asegurar que el modal esté limpio antes de abrirlo
    $("#ModalArticulos").modal("show");

    // Esperar a que el modal se abra completamente antes de inicializar la tabla
    $("#ModalArticulos").on("shown.bs.modal.listarActivos", function () {
      console.log("Modal completamente abierto, inicializando tabla");
      listarActivosModal();
      // Remover el evento para evitar múltiples inicializaciones
      $(this).off("shown.bs.modal.listarActivos");
    });
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

  $("#btnsalirmov")
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
          $("#divgenerarmov").hide();
          $("#divtblmovimientos").show();
          $("#divlistadomovimientos").show();
        }
      });
    });

  // Ocultar secciones al cargar
  $("#divgenerarmov").hide();
  $("#divregistroMovimiento").hide();

  // Contador de caracteres para observaciones
  $(document).on("input", "#observaciones", function () {
    const maxLength = 500;
    const currentLength = $(this).val().length;
    $("#contador-caracteres").text(currentLength);

    // Cambiar color según proximidad al límite
    if (currentLength > maxLength * 0.9) {
      $("#contador-caracteres")
        .removeClass("text-muted text-warning")
        .addClass("text-danger");
    } else if (currentLength > maxLength * 0.7) {
      $("#contador-caracteres")
        .removeClass("text-muted text-danger")
        .addClass("text-warning");
    } else {
      $("#contador-caracteres")
        .removeClass("text-warning text-danger")
        .addClass("text-muted");
    }
  });

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

  // Botón para abrir el panel de mantenimiento
  $("#btnmantenimiento")
    .off("click")
    .on("click", function () {
      window.location.href = "../Mantenimiento/";
    });

  // Botón procesar en generarmov
  $("#btnprocesarempresa")
    .off("click")
    .on("click", function () {
      // Validar campos obligatorios
      const tipoMovimiento = $("#IdTipoMovimientoMov").val();
      const autorizador = $("#CodAutorizador").val();
      const sucursalDestino = $("#IdSucursalDestino").val();
      const empresaDestino = $("#IdEmpresaDestino").val();
      const receptor = $("#CodReceptor").val();

      if (!tipoMovimiento) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar un tipo de movimiento",
          icon: "warning",
        });
        return;
      }

      if (!autorizador) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar un autorizador",
          icon: "warning",
        });
        return;
      }

      if (!empresaDestino) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar una empresa destino",
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

      if (!receptor) {
        Swal.fire({
          title: "Campo Requerido",
          text: "Debe seleccionar un Receptor",
          icon: "warning",
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

      var receptorNombre = $("#CodReceptor option:selected").text();
      $("#IdReceptor").val($("#CodReceptor").val());

      // Llenar los campos de usuario origen (autorizador) y destino (receptor)
      $("#usuario_origen").val(autorizadorNombre);
      $("#usuario_destino").val(receptorNombre);

      // Llenar sucursal destino
      var sucursalDestinoNombre = $(
        "#IdSucursalDestino option:selected"
      ).text();
      $("#sucursal_destino").val(sucursalDestinoNombre);

      // La sucursal origen ya está establecida desde PHP, no la modificamos

      // Transferir valores de empresa y sucursal destino para usar en el detalle
      window.idempresaDestino = $("#IdEmpresaDestino").val();
      window.idsucursalDestino = $("#IdSucursalDestino").val();

      // Limpiar observaciones para el nuevo movimiento
      $("#observaciones").val("");
      $("#contador-caracteres")
        .text("0")
        .removeClass("text-warning text-danger")
        .addClass("text-muted");

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
          // Si se guardó el movimiento principal, proceder a guardar los detalles
          Swal.fire({
            title: "Movimiento Creado",
            text: `Se ha creado el movimiento con código: ${res.codigoMovimiento}`,
            icon: "success",
          }).then(() => {
            guardarDetallesMovimiento(res.idMovimiento);
          });
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

  // Al abrir el modal de detalle, autocompleta los campos de destino
  $("#ModalDetalleMovimiento").on("show.bs.modal", function () {
    setSucursalOrigenDestino();
    setDestinoDetalle();
  });

  // Debug para el modal de artículos
  $("#ModalArticulos").on("show.bs.modal", function () {
    console.log("Modal de artículos se está abriendo");

    // Si estamos en mantenimiento, asegurar que el modal esté visible
    if (
      $("#divgenerarmantenimiento").is(":visible") ||
      $("#divregistroMantenimiento").is(":visible")
    ) {
      console.log("Abriendo desde mantenimiento - ajustando z-index");
      $(this).css("z-index", "9999");
      $(".modal-backdrop").css("z-index", "9998");
    }
  });

  $("#ModalArticulos").on("shown.bs.modal", function () {
    console.log("Modal de artículos se abrió completamente");
  });

  $("#ModalArticulos").on("hide.bs.modal", function () {
    console.log("Modal de artículos se está cerrando");
  });

  // Manejar el cierre del modal con el botón X
  $(document).on("click", "#ModalArticulos .close", function () {
    console.log("Cerrando modal con botón X");
    $("#ModalArticulos").modal("hide");
  });

  // Manejar el cierre del modal con Escape
  $(document).on("keydown", function (e) {
    if (e.key === "Escape" && $("#ModalArticulos").hasClass("show")) {
      console.log("Cerrando modal con Escape");
      $("#ModalArticulos").modal("hide");
    }
  });

  // Evento adicional para asegurar el cierre del modal
  $("#ModalArticulos").on("hidden.bs.modal", function () {
    console.log("Modal cerrado completamente");
    // Limpiar cualquier tabla DataTable si existe
    if ($.fn.DataTable.isDataTable("#tbllistarActivos")) {
      $("#tbllistarActivos").DataTable().destroy();
    }
    // Remover cualquier backdrop residual
    $(".modal-backdrop").remove();
    $("body").removeClass("modal-open");
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
            $("#SucursalActual").val(res.data.Sucursal || "");
            $("#AmbienteActual").val(res.data.Ambiente || "");
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
    $("#tbldetalleactivomov tbody tr").each(function () {
      const ambienteDestino = $(this).find(".ambiente-destino").val();
      const responsableDestino = $(this).find(".responsable-destino").val();
      const idActivo = $(this).data("id");
      const nombreActivo = $(this).find("td:eq(1)").text();

      if (!ambienteDestino) {
        camposFaltantes.push(`Ambiente destino para el activo ${nombreActivo}`);
      }
      if (!responsableDestino) {
        camposFaltantes.push(
          `Responsable destino para el activo ${nombreActivo}`
        );
      }
    });

    if (camposFaltantes.length > 0) {
      Swal.fire({
        title: "Campos Faltantes",
        html:
          "Por favor complete los siguientes campos:<br><br>" +
          camposFaltantes.join("<br>"),
        icon: "warning",
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
      },
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
            icon: "success",
          }).then(() => {
            // Limpia solo el select de activo y los campos visuales
            $("#IdActivo").val("").trigger("change");
            $("#CodigoActivo, #Sucursal, #Ambiente").val("");
            // Limpiar la tabla de detalle
            $("#tbldetalleactivomov tbody").empty();
          });
        } else {
          Swal.fire(
            "Error",
            res.message || "No se pudo agregar el activo",
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la petición:", error);
        Swal.fire(
          "Error",
          "Ocurrió un error al procesar la solicitud",
          "error"
        );
      },
    });
  });

  // Función para verificar componentes y agregar activo
  function verificarComponentesYAgregar(activo) {
    $.ajax({
      url: "../../controllers/GestionarMovimientoController.php?action=verificarComponentesActivo",
      type: "POST",
      data: { idActivo: activo.id },
      dataType: "json",
      success: function (res) {
        if (res.status && res.data.tieneComponentes) {
          // Mostrar alerta con información de componentes
          Swal.fire({
            title: "¡Activo con Componentes Detectado!",
            html: `
              <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> ${activo.nombre}</h5>
                <p><strong>Este activo tiene ${res.data.totalComponentes} componente(s) anidado(s):</strong></p>
                <div class="text-left" style="max-height: 200px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0;">
                  <small>${res.data.listaComponentes}</small>
                </div>
                <div class="alert alert-warning mt-3 mb-3">
                  <h6><i class="fas fa-exclamation-triangle"></i> Comportamiento Automático:</h6>
                  <ul class="mb-0 text-left">
                    <li><strong>Mismo destino:</strong> Los componentes se moverán junto con el activo principal</li>
                    <li><strong>Destino diferente:</strong> Los componentes se separarán automáticamente del activo principal</li>
                  </ul>
                </div>
                <p class="mb-0"><strong>¿Desea continuar con el movimiento?</strong></p>
              </div>
            `,
            icon: "info",
            showCancelButton: true,
            confirmButtonText:
              '<i class="fas fa-arrow-right"></i> Continuar con el Movimiento',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            width: "650px",
          }).then((result) => {
            if (result.isConfirmed) {
              // El SP maneja automáticamente la lógica de separación
              agregarActivoAlDetalle(activo, true);
            }
          });
        } else {
          // No tiene componentes, agregar normalmente
          agregarActivoAlDetalle(activo, true);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al verificar componentes:", error);
        // En caso de error, agregar el activo normalmente
        agregarActivoAlDetalle(activo, true);
      },
    });
  }

  // Función para agregar activo al detalle
  function agregarActivoAlDetalle(activo, incluirComponentes = true) {
    if ($(`#tbldetalleactivomov tbody tr[data-id='${activo.id}']`).length > 0) {
      NotificacionToast(
        "error",
        `El activo <b>${activo.nombre}</b> ya está en el detalle.`
      );
      return false;
    }

    // Validar que el activo tenga todos los datos necesarios
    if (!activo.id || !activo.nombre || !activo.Ambiente || !activo.Sucursal) {
      NotificacionToast(
        "error",
        "El activo no tiene todos los datos necesarios"
      );
      return false;
    }

    var numeroFilas = $("#tbldetalleactivomov").find("tbody tr").length;

    var selectAmbienteDestino = `<select class='form-control form-control-sm ambiente-destino' name='ambiente_destino[]' id="comboAmbiente${numeroFilas}" required>
      <option value="">Seleccione ambiente...</option>
    </select>`;
    var selectResponsableDestino = `<select class='form-control form-control-sm responsable-destino' name='responsable_destino[]' id="comboResponsable${numeroFilas}" required>
      <option value="">Seleccione responsable...</option>
    </select>`;

    // Indicador visual de origen vs destino
    var badgeOrigen = `<span class="badge badge-info">Origen</span>`;
    var badgeDestino = `<span class="badge badge-success">Destino</span>`;

    var nuevaFila = `<tr data-id='${activo.id}' class='table-light border-left border-success border-3 agregado-temp'>
      <td class="text-center">${activo.id}</td>
      <td><strong>${activo.codigo}</strong></td>
      <td>${activo.nombre}</td>
      <td>${badgeOrigen} ${activo.Sucursal}</td>
      <td>${badgeOrigen} ${activo.Ambiente}</td>
      <td>${badgeDestino} ${selectAmbienteDestino}</td>
      <td>${badgeDestino} ${selectResponsableDestino}</td>
      <td class="text-center">
        <button type='button' class='btn btn-danger btn-sm btnQuitarActivo' title='Quitar activo'>
          <i class='fa fa-trash'></i>
        </button>
      </td>
    </tr>`;
    $("#tbldetalleactivomov tbody").append(nuevaFila);

    // Inicializar los combos con la sucursal destino actual
    ListarCombosAmbiente(`comboAmbiente${numeroFilas}`);
    ListarCombosResponsable(`comboResponsable${numeroFilas}`);

    // Agregar validación mejorada al cambiar los combos
    $(`#comboAmbiente${numeroFilas}, #comboResponsable${numeroFilas}`).on(
      "change",
      function () {
        const $this = $(this);
        const ambienteVal = $(`#comboAmbiente${numeroFilas}`).val();
        const responsableVal = $(`#comboResponsable${numeroFilas}`).val();

        // Validación individual
        if (!$this.val()) {
          $this.addClass("is-invalid").removeClass("is-valid");
        } else {
          $this.removeClass("is-invalid").addClass("is-valid");
        }

        // Actualizar contador de activos listos
        actualizarContadorActivosListos();
      }
    );

    // Animación de entrada
    setTimeout(function () {
      $("#tbldetalleactivomov tbody tr.agregado-temp")
        .removeClass("table-light agregado-temp")
        .addClass("table-active");

      setTimeout(function () {
        $("#tbldetalleactivomov tbody tr.table-active").removeClass(
          "table-active"
        );
      }, 1000);
    }, 100);

    NotificacionToast(
      "success",
      `Activo <b>${activo.nombre}</b> agregado al detalle.`
    );

    // Cerrar modal automáticamente
    $("#ModalArticulos").modal("hide");

    // Actualizar contador
    actualizarContadorActivosListos();

    return true;
  }

  // Función para actualizar el contador de activos listos para procesar
  function actualizarContadorActivosListos() {
    const totalActivos = $("#tbldetalleactivomov tbody tr").length;
    let activosListos = 0;

    $("#tbldetalleactivomov tbody tr").each(function () {
      const ambiente = $(this).find(".ambiente-destino").val();
      const responsable = $(this).find(".responsable-destino").val();

      if (ambiente && responsable) {
        activosListos++;
      }
    });

    // Actualizar badge en el botón guardar
    const $btnGuardar = $("#btnGuardarMov");

    if (totalActivos === 0) {
      $btnGuardar
        .prop("disabled", true)
        .removeClass("btn-success")
        .addClass("btn-secondary")
        .html('<i class="fa fa-save"></i> Guardar Movimiento');
    } else if (activosListos === totalActivos) {
      $btnGuardar
        .prop("disabled", false)
        .removeClass("btn-secondary")
        .addClass("btn-success")
        .html(
          `<i class="fa fa-save"></i> Registrar Movimiento (${totalActivos} activo${
            totalActivos > 1 ? "s" : ""
          })`
        );
    } else {
      $btnGuardar
        .prop("disabled", true)
        .removeClass("btn-success")
        .addClass("btn-secondary")
        .html(
          `<i class="fa fa-save"></i> Completar datos (${activosListos}/${totalActivos})`
        );
    }

    // Remover cualquier mensaje de advertencia previo
    $("#mensaje-destinos-inconsistentes").remove();
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
      Sucursal: fila.find("td:eq(3)").text(),
      Ambiente: fila.find("td:eq(4)").text(),
    };

    // Verificar si el activo tiene componentes anidados
    verificarComponentesYAgregar(activo);
  });

  $(document).on("click", ".btnQuitarActivo", function () {
    const $fila = $(this).closest("tr");
    const nombreActivo = $fila.find("td:eq(2)").text();

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
        $fila.fadeOut(300, function () {
          $(this).remove();
          actualizarContadorActivosListos();
          NotificacionToast(
            "info",
            `Activo <b>${nombreActivo}</b> removido del detalle.`
          );
        });
      }
    });
  });

  // Función para guardar el movimiento completo usando el enfoque cabecera-detalle
  $("#btnGuardarMov").on("click", function () {
    // Verificar si hay detalles en la tabla
    if ($("#tbldetalleactivomov tbody tr").length === 0) {
      Swal.fire("Error", "Debe agregar al menos un activo al detalle", "error");
      return;
    }

    // Validar que todos los campos requeridos estén llenos
    let camposFaltantes = [];
    $("#tbldetalleactivomov tbody tr").each(function () {
      const ambienteDestino = $(this).find(".ambiente-destino").val();
      const responsableDestino = $(this).find(".responsable-destino").val();
      const nombreActivo = $(this).find("td:eq(2)").text();

      if (!ambienteDestino) {
        camposFaltantes.push(`Ambiente destino para el activo ${nombreActivo}`);
      }
      if (!responsableDestino) {
        camposFaltantes.push(
          `Responsable destino para el activo ${nombreActivo}`
        );
      }
    });

    if (camposFaltantes.length > 0) {
      Swal.fire({
        title: "Campos Faltantes",
        html:
          "Por favor complete los siguientes campos:<br><br>" +
          camposFaltantes.join("<br>"),
        icon: "warning",
      });
      return;
    }

    // Mostrar loading
    Swal.fire({
      title: "Procesando Movimiento",
      html: `
        <div class="text-center">
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="sr-only">Creando movimiento...</span>
          </div>
          <p>Paso 1: Creando cabecera del movimiento...</p>
        </div>
      `,
      allowOutsideClick: false,
      showConfirmButton: false,
    });

    // PASO 1: Crear la cabecera del movimiento
    const formDataMovimiento = new FormData();
    formDataMovimiento.append("idTipoMovimiento", $("#IdTipoMovimiento").val());
    formDataMovimiento.append("idAutorizador", $("#IdAutorizador").val());
    formDataMovimiento.append("idReceptor", $("#IdReceptor").val());
    formDataMovimiento.append("idEmpresaDestino", window.idempresaDestino);
    formDataMovimiento.append("idSucursalDestino", window.idsucursalDestino);

    // Usar las observaciones ingresadas por el usuario
    const observaciones = $("#observaciones").val().trim();
    formDataMovimiento.append(
      "observaciones",
      observaciones || "Sin observaciones adicionales"
    );

    // Depuración: mostrar datos que se enviarán
    console.log("Datos a enviar cabecera:", {
      idTipoMovimiento: $("#IdTipoMovimiento").val(),
      idAutorizador: $("#IdAutorizador").val(),
      idReceptor: $("#IdReceptor").val(),
      idEmpresaDestino: window.idempresaDestino,
      idSucursalDestino: window.idsucursalDestino,
      observaciones: observaciones || "Sin observaciones adicionales",
    });

    $.ajax({
      url: "../../controllers/GestionarMovimientoController.php?action=RegistrarMovimiento",
      type: "POST",
      data: formDataMovimiento,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (resMovimiento) {
        if (resMovimiento.status) {
          // PASO 2: Agregar todos los activos al movimiento creado
          Swal.update({
            html: `
              <div class="alert alert-success mb-3">
                <h5><i class="fas fa-check-circle"></i> Movimiento Creado</h5>
                <h4 class="text-primary"><strong>${
                  resMovimiento.codMovimiento
                }</strong></h4>
              </div>
              <div class="progress mb-3">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                     style="width: 0%" id="progressBar">0/${
                       $("#tbldetalleactivomov tbody tr").length
                     }</div>
              </div>
              <div id="progressText">Paso 2: Agregando activos al movimiento...</div>
            `,
          });

          agregarActivosAlMovimiento(
            resMovimiento.idMovimiento,
            resMovimiento.codMovimiento
          );
        } else {
          Swal.fire({
            title: "Error al Crear Movimiento",
            html: `
              <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> ${resMovimiento.message}
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

  // Función para agregar todos los activos al movimiento creado
  function agregarActivosAlMovimiento(idMovimiento, codMovimiento) {
    let activosProcesados = 0;
    let totalActivos = $("#tbldetalleactivomov tbody tr").length;
    let errores = [];
    let activosExitosos = [];

    $("#tbldetalleactivomov tbody tr").each(function (index) {
      const fila = $(this);
      const nombreActivo = fila.find("td:eq(2)").text();
      const detalleData = new FormData();

      detalleData.append("IdMovimiento", idMovimiento);
      detalleData.append("IdActivo", fila.find("td:eq(0)").text());
      detalleData.append("IdTipo_Movimiento", $("#IdTipoMovimiento").val());
      detalleData.append(
        "IdAmbiente_Nueva",
        fila.find(".ambiente-destino").val()
      );
      detalleData.append(
        "IdResponsable_Nueva",
        fila.find(".responsable-destino").val()
      );
      detalleData.append("IdAutorizador", $("#IdAutorizador").val());
      detalleData.append("IdReceptor", $("#IdReceptor").val());
      detalleData.append("idSucursalDestino", window.idsucursalDestino || "");
      detalleData.append("idEmpresaDestino", window.idempresaDestino || "");
      detalleData.append("IdActivoPadre_Nuevo", null);

      $.ajax({
        url: "../../controllers/GestionarMovimientoController.php?action=AgregarDetalle",
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
                  title: "¡Movimiento Completado Exitosamente!",
                  html: `
                    <div class="alert alert-success">
                      <h5><i class="fas fa-check-circle text-success"></i> Código de Movimiento</h5>
                      <h3 class="text-primary"><strong>${codMovimiento}</strong></h3>
                      <p class="mb-0"><strong>${
                        activosExitosos.length
                      } activos</strong> procesados correctamente</p>
                    </div>
                    
                    <div class="row mt-4">
                      <div class="col-md-6">
                        <div class="card border-info">
                          <div class="card-header bg-info text-white">
                            <i class="fas fa-info-circle"></i> Información del Movimiento
                          </div>
                          <div class="card-body">
                            <p><strong>Código:</strong> ${codMovimiento}</p>
                            <p><strong>Total de activos:</strong> ${
                              activosExitosos.length
                            }</p>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-6">
                        <div class="card border-success">
                          <div class="card-header bg-success text-white">
                            <i class="fas fa-boxes"></i> Activos Procesados
                          </div>
                          <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                            ${activosExitosos
                              .map(
                                (nombre) =>
                                  `<div class="d-flex justify-content-between align-items-center border-bottom py-1">
                                <span><i class="fas fa-box text-success"></i> ${nombre}</span>
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
                        <i class="fas fa-search"></i> Para consultar este movimiento, busque por el código: <strong>${codMovimiento}</strong>
                      </small>
                    </div>
                  `,
                  icon: "success",
                  width: "900px",
                  confirmButtonText: "Continuar",
                  confirmButtonColor: "#28a745",
                }).then(() => {
                  limpiarYRecargar();
                });
              } else {
                let mensajeExito =
                  activosExitosos.length > 0
                    ? `<div class="alert alert-success mb-3">
                    <h5><i class="fas fa-check-circle"></i> Movimiento: <strong>${codMovimiento}</strong></h5>
                    <strong>Exitosos (${activosExitosos.length}):</strong><br>
                    ${activosExitosos
                      .map(
                        (activo) =>
                          `• <i class="fas fa-box text-success"></i> ${activo}`
                      )
                      .join("<br>")}
                  </div>`
                    : "";

                Swal.fire({
                  title: "Movimiento Completado con Advertencias",
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
                      <small><i class="fas fa-info-circle"></i> Los activos exitosos están bajo el código: <strong>${codMovimiento}</strong></small>
                    </div>
                  `,
                  icon: "warning",
                  width: "700px",
                }).then(() => {
                  limpiarYRecargar();
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
                    <h5>Movimiento: <strong>${codMovimiento}</strong></h5>
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

  // Función para limpiar y recargar
  function limpiarYRecargar() {
    $("#divregistroMovimiento").hide();
    $("#divtblmovimientos").show();
    $("#divlistadomovimientos").show();
    $("#tbldetalleactivomov tbody").empty();

    // Limpiar observaciones y resetear contador
    $("#observaciones").val("");
    $("#contador-caracteres")
      .text("0")
      .removeClass("text-warning text-danger")
      .addClass("text-muted");

    ListarMovimientos();
  }

  // Función legacy para compatibilidad - ya no se usa con el nuevo procedimiento
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
      detalleData.append("IdTipo_Movimiento", $("#IdTipoMovimiento").val());
      detalleData.append("IdSucursal_Nueva", $("#IdSucursalDestino").val());
      detalleData.append("IdEmpresaDestino", $("#IdEmpresaDestino").val());
      detalleData.append(
        "IdAmbiente_Nueva",
        fila.find(".ambiente-destino").val()
      );
      detalleData.append(
        "IdResponsable_Nueva",
        fila.find(".responsable-destino").val()
      );
      detalleData.append("IdAutorizador", $("#IdAutorizador").val());
      detalleData.append("IdReceptor", $("#IdReceptor").val());
      detalleData.append("IdActivoPadre_Nuevo", null);

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
                $("#tbldetalleactivomov tbody").empty();
                ListarMovimientos();
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
                $("#tbldetalleactivomov tbody").empty();
                ListarMovimientos();
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
  // La sucursal origen ya está establecida desde PHP (sucursal del usuario actual)
  // Solo actualizamos la sucursal destino si está disponible
  var sucursalDestinoText = $("#IdSucursalDestino option:selected").text();
  if (sucursalDestinoText && sucursalDestinoText !== "") {
    $("#sucursal_destino").val(sucursalDestinoText);
  }
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
    data: {
      idSucursal: idSucursal || $("#IdSucursalDestino").val(),
      idEmpresa: $("#IdEmpresaDestino").val(),
    },
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

// Agregar evento para actualizar ambientes cuando cambie la sucursal destino o la empresa
$(document).on("change", "#IdSucursalDestino, #IdEmpresaDestino", function () {
  // Actualizar todos los combos de ambiente en la tabla
  $("#tbldetalleactivomov tbody tr").each(function () {
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

        $(
          "#filtroTipoMovimiento, #filtroSucursal, #filtroAmbiente, #filtroTipoListado"
        ).select2({
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
          .html(res.data.tipoMovimientov1)
          .trigger("change");

        // Cargar autorizador
        $("#CodAutorizador").html(res.data.autorizador).trigger("change");

        // Cargar empresas
        $("#IdEmpresaDestino").html(res.data.empresas).trigger("change");

        // Actualizar el campo de sucursal origen
        $("#IdSucursalOrigen").val(res.data.sucursalOrigen);
        $("#IdSucursalOrigenValor").val(res.data.sucursalOrigenId);

        // Cargar sucursales para el destino
        $("#IdSucursalDestino").html(res.data.sucursales);

        // Cargar receptor
        $("#CodReceptor").html(res.data.receptor);

        // Inicializar select2 una sola vez para los combos
        if (!$("#IdTipoMovimientoMov").hasClass("select2-hidden-accessible")) {
          $(
            "#IdTipoMovimientoMov, #CodAutorizador, #IdSucursalDestino, #IdEmpresaDestino, #CodReceptor"
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

// Agregar evento para actualizar sucursales cuando cambie la empresa
$(document).on("change", "#IdEmpresaDestino", function () {
  const idEmpresa = $(this).val();
  if (idEmpresa) {
    $.ajax({
      url: "../../controllers/GestionarMovimientoController.php?action=obtenerSucursalesPorEmpresa",
      type: "POST",
      data: { idEmpresa: idEmpresa },
      dataType: "json",
      success: function (res) {
        if (res.status) {
          $("#IdSucursalDestino").html(res.data).trigger("change");
        } else {
          NotificacionToast(
            "error",
            "Error al cargar sucursales: " + res.message
          );
        }
      },
      error: function (xhr, status, error) {
        NotificacionToast("error", "Error al cargar sucursales: " + error);
      },
    });
  } else {
    $("#IdSucursalDestino")
      .html('<option value="">Seleccione</option>')
      .trigger("change");
  }
});

// Función principal que decide qué tipo de movimientos listar
function ListarMovimientos() {
  const tipoListado = $("#filtroTipoListado").val();

  if (tipoListado === "enviados") {
    ListarMovimientosEnviados();
  } else if (tipoListado === "recibidos") {
    ListarMovimientosRecibidos();
  }
}

// Listar movimientos enviados (desde mi sucursal)
function ListarMovimientosEnviados() {
  if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
    $("#tblMovimientos").DataTable().destroy();
  }

  // Actualizar título
  $("#tituloTablaMovimientos").html(
    '<i class="fa fa-paper-plane"></i> Lista de Movimientos Enviados'
  );

  $("#tblMovimientos").DataTable({
    aProcessing: true,
    aServerSide: false,
    destroy: true,
    //responsive: true,
    bInfo: true,
    iDisplayLength: 10,
    order: [[7, "desc"]], // Ordenar por fecha descendente (columna 7 = fecha)
    ajax: {
      url: "../../controllers/GestionarMovimientoController.php?action=listarMovimientosEnviados",
      type: "POST",
      data: function (d) {
        return {
          tipo: $("#filtroTipoMovimiento").val(),
          sucursal: $("#filtroSucursal").val(),
          fechaInicio: $("#filtroFechaInicio").val(),
          fechaFin: $("#filtroFechaFin").val(),
          //fecha: $("#filtroFecha").val(),
        };
      },
      dataSrc: function (json) {
        if (!json.status) {
          NotificacionToast(
            "error",
            json.message || "Error al cargar los movimientos"
          );
          return [];
        }
        return json.data || [];
      },
      error: function (xhr, status, error) {
        console.error("Error en la petición:", error);
        NotificacionToast("error", "Error al cargar los movimientos: " + error);
        return [];
      },
    },
    columns: [
      {
        data: null,
        render: function (data, type, row) {
          let acciones = `
            <div class="btn-group">
              <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cogs"></i>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="#" onclick="verDetallesMovimiento(${row.idMovimiento})">
                  <i class="fas fa-list"></i> Ver Detalles
                </a>
                <a class="dropdown-item" href="#" onclick="verHistorialEstados(${row.idMovimiento})">
                  <i class="fas fa-history"></i> Historial Estados
                </a>
                <a class="dropdown-item" href="#" onclick="imprimirReporte(${row.idMovimiento})">
                  <i class="fas fa-print"></i> Imprimir Reporte
                </a>`;

          // Agregar acciones según el estado

          if (row.idEstadoMovimiento == 1 || row.idEstadoMovimiento === "1") {
            // Pendiente - Solo el autorizador puede aprobar/rechazar
            if (row.idAutorizador == window.currentUserId) {
              acciones += `
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-success" href="#" onclick="aprobarMovimiento(${row.idMovimiento})">
                  <i class="fas fa-check"></i> Aprobar
                </a>
                <a class="dropdown-item text-danger" href="#" onclick="rechazarMovimiento(${row.idMovimiento})">
                  <i class="fas fa-times"></i> Rechazar
                </a>`;
            }
          }

          acciones += `
              </div>
            </div>`;

          return acciones;
        },
      },
      { data: "codigoMovimiento" },
      { data: "tipoMovimiento" },
      { data: "sucursalDestino" },
      { data: "empresaDestino" },
      { data: "autorizador" },
      {
        data: "estadoMovimiento",
        render: function (data, type, row) {
          const estados = {
            1: '<span class="badge badge-warning">Pendiente</span>',
            2: '<span class="badge badge-info">Aprobado</span>',
            3: '<span class="badge badge-danger">Rechazado</span>',
            4: '<span class="badge badge-success">Aceptado</span>',
          };
          return (
            estados[row.idEstadoMovimiento] ||
            '<span class="badge badge-secondary">Sin estado</span>'
          );
        },
      },
      {
        data: "fechaMovimiento",
        render: function (data) {
          return moment(data).format("DD/MM/YYYY HH:mm");
        },
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
  });
}

function ListarMovimientosRecibidos() {
  if ($.fn.DataTable.isDataTable("#tblMovimientos")) {
    $("#tblMovimientos").DataTable().destroy();
  }

  // Actualizar título
  $("#tituloTablaMovimientos").html(
    '<i class="fa fa-inbox"></i> Lista de Movimientos Recibidos'
  );

  $("#tblMovimientos").DataTable({
    aProcessing: true,
    aServerSide: false,
    destroy: true,
    //responsive: true,
    bInfo: true,
    iDisplayLength: 10,
    order: [[7, "desc"]], // Ordenar por fecha descendente (columna 7 = fecha)
    ajax: {
      url: "../../controllers/GestionarMovimientoController.php?action=listarMovimientosRecibidos",
      type: "POST",
      data: function (d) {
        return {
          tipo: $("#filtroTipoMovimiento").val(),
          sucursal: $("#filtroSucursal").val(),
          fechaInicio: $("#filtroFechaInicio").val(),
          fechaFin: $("#filtroFechaFin").val(),
          //fecha: $("#filtroFecha").val(),
        };
      },
      dataSrc: function (json) {
        if (!json.status) {
          NotificacionToast(
            "error",
            json.message || "Error al cargar los movimientos"
          );
          return [];
        }
        return json.data || [];
      },
      error: function (xhr, status, error) {
        console.error("Error en la petición:", error);
        NotificacionToast("error", "Error al cargar los movimientos: " + error);
        return [];
      },
    },
    columns: [
      {
        data: null,
        render: function (data, type, row) {
          let acciones = `
            <div class="btn-group">
              <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cogs"></i>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="#" onclick="verDetallesMovimiento(${row.idMovimiento})">
                  <i class="fas fa-list"></i> Ver Detalles
                </a>
                <a class="dropdown-item" href="#" onclick="verHistorialEstados(${row.idMovimiento})">
                  <i class="fas fa-history"></i> Historial Estados
                </a>
                <a class="dropdown-item" href="#" onclick="imprimirReporte(${row.idMovimiento})">
                  <i class="fas fa-print"></i> Imprimir Reporte
                </a>`;

          // Agregar acciones según el estado para movimientos recibidos

          if (row.idEstadoMovimiento == 2 || row.idEstadoMovimiento === "2") {
            // Aprobado - Solo el receptor puede aceptar
            if (row.idReceptor == window.currentUserId) {
              acciones += `
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-success" href="#" onclick="aceptarMovimiento(${row.idMovimiento})">
                  <i class="fas fa-check-double"></i> Aceptar Movimiento
                </a>`;
            }
          }

          acciones += `
              </div>
            </div>`;

          return acciones;
        },
      },
      { data: "codigoMovimiento" },
      { data: "tipoMovimiento" },
      { data: "sucursalDestino" },
      { data: "empresaDestino" },
      { data: "autorizador" },
      {
        data: "estadoMovimiento",
        render: function (data, type, row) {
          const estados = {
            1: '<span class="badge badge-warning">Pendiente</span>',
            2: '<span class="badge badge-info">Aprobado</span>',
            3: '<span class="badge badge-danger">Rechazado</span>',
            4: '<span class="badge badge-success">Aceptado</span>',
          };
          return (
            estados[row.idEstadoMovimiento] ||
            '<span class="badge badge-secondary">Sin estado</span>'
          );
        },
      },
      {
        data: "fechaMovimiento",
        render: function (data) {
          return moment(data).format("DD/MM/YYYY HH:mm");
        },
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
  });
}

function verDetallesMovimiento(idMovimiento) {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=obtenerDetallesMovimiento",
    type: "POST",
    data: { idMovimiento: idMovimiento },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        let detallesHtml =
          '<div class="table-responsive"><table class="table table-bordered table-striped">';
        detallesHtml += "<thead><tr>";
        detallesHtml += "<th>Activo</th>";
        detallesHtml += "<th>Ambiente Anterior</th>";
        detallesHtml += "<th>Ambiente Nuevo</th>";
        detallesHtml += "<th>Responsable Anterior</th>";
        detallesHtml += "<th>Responsable Nuevo</th>";
        detallesHtml += "</tr></thead><tbody>";

        res.data.forEach(function (detalle) {
          detallesHtml += "<tr>";
          detallesHtml += `<td>${detalle.nombreActivo}</td>`;
          detallesHtml += `<td>${detalle.ambienteOrigen}</td>`;
          detallesHtml += `<td>${detalle.ambienteDestino}</td>`;
          detallesHtml += `<td>${detalle.responsableOrigen}</td>`;
          detallesHtml += `<td>${detalle.responsableDestino}</td>`;
          detallesHtml += "</tr>";
        });

        detallesHtml += "</tbody></table></div>";

        Swal.fire({
          title: "Detalles del Movimiento",
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

function listarActivosModal() {
  console.log("Inicializando tabla de activos en modal");

  // Destruir la instancia existente si existe
  if ($.fn.DataTable.isDataTable("#tbllistarActivos")) {
    $("#tbllistarActivos").DataTable().destroy();
  }

  $("#tbllistarActivos").DataTable({
    dom: "Bfrtip",
    responsive: false,
    processing: true,
    ajax: {
      url: "../../controllers/GestionarMovimientoController.php?action=ListarParaMovimiento",
      type: "POST",
      dataType: "json",
      dataSrc: function (json) {
        console.log("Datos recibidos para modal:", json);
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
      { data: "Sucursal", title: "Sucursal" },
      { data: "Ambiente", title: "Ambiente" },
      {
        data: null,
        title: "Acción",
        orderable: false,
        render: function (data, type, row) {
          return `<button class="btn btn-success btn-sm btnSeleccionarActivo" data-id="${row.IdActivo}" title="Seleccionar activo">
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
      console.log("Tabla de activos renderizada correctamente");
    },
  });
}

// Agregar los manejadores de eventos para los nuevos botones
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
//               // Recargar la tabla
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

  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=obtenerHistorialMovimiento",
    type: "POST",
    data: { idMovimiento: datos.IdDetalleMovimiento },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        let historialHtml =
          '<div class="table-responsive"><table class="table table-bordered">';
        historialHtml +=
          "<thead><tr><th>Fecha</th><th>Estado</th><th>Tipo</th><th>Activo</th><th>Origen</th><th>Destino</th></tr></thead>";
        historialHtml += "<tbody>";

        res.data.forEach((item) => {
          historialHtml += `<tr>
            <td>${moment(item.FechaMovimiento).format("DD/MM/YYYY HH:mm")}</td>
            <td>${
              item.estado === "A"
                ? '<span class="badge badge-danger">Anulado</span>'
                : '<span class="badge badge-success">Activo</span>'
            }</td>
            <td>${item.tipoMovimiento}</td>
            <td>${item.CodigoActivo} - ${item.NombreArticulo}</td>
            <td>${item.ambienteOrigen}</td>
            <td>${item.sucursalDestino} - ${item.ambienteDestino}</td>
          </tr>`;
        });

        historialHtml += "</tbody></table></div>";

        Swal.fire({
          title: "Historial del Movimiento",
          html: historialHtml,
          width: "800px",
        });
      } else {
        Swal.fire("Error", res.message, "error");
      }
    },
    error: function (xhr, status, error) {
      Swal.fire("Error", "Error al obtener el historial: " + error, "error");
    },
  });
});

function imprimirReporte(idMovimiento) {
  window.open(
    `../../views/Reportes/reporteMovimientoPDF.php?id=${idMovimiento}`,
    "_blank"
  );
}

// Función para verificar e inicializar estados automáticamente
function verificarEInicializarEstados() {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=obtenerEstadosMovimiento",
    type: "POST",
    dataType: "json",
    success: function (res) {
      if (res.status && res.data && res.data.length >= 4) {
        // Estados ya existen, no hacer nada
        console.log("✅ Estados de movimiento ya configurados");
      } else {
        // Estados no existen o están incompletos, inicializar automáticamente
        console.log("⚠️ Estados incompletos, inicializando automáticamente...");
        inicializarEstadosAutomatico();
      }
    },
    error: function (xhr, status, error) {
      console.log(
        "⚠️ Error al verificar estados, inicializando automáticamente..."
      );
      inicializarEstadosAutomatico();
    },
  });
}

// Función para inicializar estados automáticamente (sin notificaciones)
function inicializarEstadosAutomatico() {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=inicializarEstados",
    type: "POST",
    dataType: "json",
    success: function (res) {
      if (res.status) {
        console.log("✅ Estados inicializados automáticamente");
      } else {
        console.error("❌ Error al inicializar estados:", res.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("❌ Error de comunicación al inicializar estados:", error);
    },
  });
}

// Función para verificar que los estados existen (solo para debug)
function verificarEstadosMovimiento() {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=obtenerEstadosMovimiento",
    type: "POST",
    dataType: "json",
    success: function (res) {
      if (res.status) {
        console.log("✅ Estados disponibles:", res.data);
        console.table(res.data);
      } else {
        console.error("❌ Error al verificar estados:", res.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("❌ Error al verificar estados:", error);
    },
  });
}

// Función de diagnóstico completo del sistema (para debug)
function diagnosticoSistemaMovimientos() {
  console.log("🔍 INICIANDO DIAGNÓSTICO DEL SISTEMA DE MOVIMIENTOS");
  console.log("================================================");

  // 1. Verificar estados
  console.log("1️⃣ Verificando estados...");
  verificarEstadosMovimiento();

  // 2. Verificar datos de sesión
  console.log("2️⃣ Datos de sesión disponibles:");
  console.log("- Empresa:", window.idempresaDestino || "No definida");
  console.log("- Sucursal:", window.idsucursalDestino || "No definida");

  // 3. Verificar funciones críticas
  console.log("3️⃣ Verificando funciones críticas:");
  const funcionesCriticas = [
    "aprobarMovimiento",
    "rechazarMovimiento",
    "aceptarMovimiento",
    "verHistorialEstados",
    "ListarMovimientosEnviados",
    "ListarMovimientosRecibidos",
  ];

  funcionesCriticas.forEach((func) => {
    if (typeof window[func] === "function") {
      console.log(`✅ ${func} - OK`);
    } else {
      console.log(`❌ ${func} - NO ENCONTRADA`);
    }
  });

  console.log("================================================");
  console.log("🏁 DIAGNÓSTICO COMPLETADO - Revisa la consola");
}

// Función para aprobar movimiento
function aprobarMovimiento(idMovimiento) {
  Swal.fire({
    title: "¿Aprobar Movimiento?",
    text: "¿Está seguro de aprobar este movimiento?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, aprobar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../../controllers/GestionarMovimientoController.php?action=aprobarMovimiento",
        type: "POST",
        data: { idMovimiento: idMovimiento },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            Swal.fire({
              title: "¡Aprobado!",
              text: res.message,
              icon: "success",
            }).then(() => {
              // Recargar la tabla
              $("#tblMovimientos").DataTable().ajax.reload();
            });
          } else {
            Swal.fire("Error", res.message, "error");
          }
        },
        error: function (xhr, status, error) {
          Swal.fire(
            "Error",
            "Error al procesar la solicitud: " + error,
            "error"
          );
        },
      });
    }
  });
}

// Función para rechazar movimiento
function rechazarMovimiento(idMovimiento) {
  Swal.fire({
    title: "¿Rechazar Movimiento?",
    text: "¿Está seguro de rechazar este movimiento?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, rechazar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../../controllers/GestionarMovimientoController.php?action=rechazarMovimiento",
        type: "POST",
        data: { idMovimiento: idMovimiento },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            Swal.fire({
              title: "¡Rechazado!",
              text: res.message,
              icon: "success",
            }).then(() => {
              // Recargar la tabla
              $("#tblMovimientos").DataTable().ajax.reload();
            });
          } else {
            Swal.fire("Error", res.message, "error");
          }
        },
        error: function (xhr, status, error) {
          Swal.fire(
            "Error",
            "Error al procesar la solicitud: " + error,
            "error"
          );
        },
      });
    }
  });
}

// Función para aceptar movimiento (ejecutar físicamente)
function aceptarMovimiento(idMovimiento) {
  Swal.fire({
    title: "¿Aceptar y Ejecutar Movimiento?",
    html: `
      <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> 
        <strong>¡Atención!</strong> Esta acción ejecutará físicamente el movimiento de activos.
        <br><br>
        Los activos serán movidos a sus nuevas ubicaciones y responsables.
        <br><br>
        <strong>Esta acción no se puede deshacer.</strong>
      </div>
    `,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, ejecutar movimiento",
    cancelButtonText: "Cancelar",
    width: "600px",
  }).then((result) => {
    if (result.isConfirmed) {
      // Mostrar loading
      Swal.fire({
        title: "Ejecutando Movimiento",
        html: `
          <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status">
              <span class="sr-only">Ejecutando...</span>
            </div>
            <p>Procesando el movimiento físico de activos...</p>
          </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false,
      });

      $.ajax({
        url: "../../controllers/GestionarMovimientoController.php?action=aceptarMovimiento",
        type: "POST",
        data: { idMovimiento: idMovimiento },
        dataType: "json",
        success: function (res) {
          if (res.status) {
            Swal.fire({
              title: "¡Movimiento Ejecutado!",
              html: `
                <div class="alert alert-success">
                  <i class="fas fa-check-circle"></i> 
                  ${res.message}
                </div>
                <p>Los activos han sido movidos exitosamente a sus nuevas ubicaciones.</p>
              `,
              icon: "success",
              width: "500px",
            }).then(() => {
              // Recargar la tabla
              $("#tblMovimientos").DataTable().ajax.reload();
            });
          } else {
            Swal.fire("Error", res.message, "error");
          }
        },
        error: function (xhr, status, error) {
          Swal.fire(
            "Error",
            "Error al procesar la solicitud: " + error,
            "error"
          );
        },
      });
    }
  });
}

// Función para ver historial de estados
function verHistorialEstados(idMovimiento) {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=obtenerHistorialEstadoMovimiento",
    type: "POST",
    data: { idMovimiento: idMovimiento },
    dataType: "json",
    success: function (res) {
      if (res.status) {
        let historialHtml = `
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="thead-dark">
                <tr>
                  <th>Estado Anterior</th>
                  <th>Estado Nuevo</th>
                  <th>Fecha Cambio</th>
                  <th>Usuario</th>
                </tr>
              </thead>
              <tbody>`;

        res.data.forEach(function (item) {
          const estadoAnterior =
            item.estadoAnterior || '<span class="text-muted">Inicial</span>';
          const estadoNuevo = item.estadoNuevo;
          const fecha = moment(item.fechaCambio).format("DD/MM/YYYY HH:mm:ss");
          const usuario = item.nombreUsuario || item.userMod;

          historialHtml += `
            <tr>
              <td>${estadoAnterior}</td>
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
          title: "Historial de Estados del Movimiento",
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
