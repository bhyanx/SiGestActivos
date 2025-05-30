$(document).ready(function () {
  init();
});

function init() {
  listarActivosTable();
  ListarCombosMov();
  ListarCombosFiltros();

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

    $("#ModalArticulos").modal("show"); // 👈 Abre el modal aquí
    listarActivosModal(docIngreso);
  });

  // ...existing code...

  $("#btnvolverprincipal")
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
          $("#divregistroActivo").hide();
          $("#divlistadoactivos").show();
          $("#divtblactivos").show();
          $("#tblRegistros").show();
        }
      });
    });

  // Ocultar secciones al cargar
  $("#divgenerarmov").hide();
  $("#divregistroActivo").hide(); //uno

  // Botón para abrir el panel de generación de movimiento
  $("#btnnuevo")
    .off("click")
    .on("click", function () {
      $("#divregistroActivo").show(); //dos
      $("#tblRegistros").hide();
      $("#divtblactivos").hide();
      $("#divlistadoactivos").hide(); // Oculta el formulario de búsqueda
      $("#tituloModalMovimiento").html(
        '<i class="fa fa-plus-circle"></i> Registrar Movimiento'
      );
      $("#frmMovimiento")[0].reset();
      $("#ModalArticulos").modal("show");
    });

  // Botón procesar en generarmov
  $("#btnprocesarempresa")
    .off("click")
    .on("click", function () {
      $("#divregistroActivo").show(); //tres
      $("#divgenerarmov").hide();
      // Opcional: limpiar el formulario
      $("#frmMovimiento")[0].reset();
    });

  // Botón cancelar en generarmov
  // Botón cancelar en generarmov
  $("#btncancelarempresa")
    .off("click")
    .on("click", function () {
      $("#divgenerarmov").hide();
      $("#divtblactivos").show();
      $("#divlistadoactivos").show(); // Muestra el formulario de búsqueda
    });

  // Botón cancelar en registro de movimiento
  $("#btnCancelarMovimiento")
    .off("click")
    .on("click", function () {
      $("#divregistroActivo").hide(); //cuatro
      $("#divtblactivos").show();
      $("#divlistadoactivos").show();
    });

  // Al buscar, mostrar la tabla y ocultar formularios
  $("#frmbusqueda").on("submit", function (e) {
    e.preventDefault();

    $("#divtblactivos").show(); // Asegura que el contenedor esté visible
    $("#divgenerarmov").hide();
    $("#divregistroActivo").hide();
    $("#divlistadoactivos").show();

    if ($.fn.DataTable.isDataTable("#tblRegistros")) {
      $("#tblRegistros").DataTable().clear().destroy();
    }

    setTimeout(() => {
      listarActivosTable();
    }, 100); // Espera breve para asegurar visibilidad
  });

  // Botón para abrir modal de nuevo movimiento
  // $("#btnnuevo").click(() => {
  //   $("#tituloModalMovimiento").html(
  //     '<i class="fa fa-plus-circle"></i> Registrar Movimiento'
  //   );
  //   $("#frmMovimiento")[0].reset();
  //   $("#ModalMovimiento").modal("show");
  // });

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
          console.log("Server response:", res); // Add this line
          Swal.fire("Éxito", "Activo agregado al movimiento", "success");
          // Limpia solo el select de activo y los campos visuales
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
  });

  $("#ModalArticulos").on("shown.bs.modal", function () {
    let docIngreso = $("#inputDocIngresoAlm").val().trim();
    if (docIngreso) {
      listarActivosModal(docIngreso);
    }
  });

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
      //codigo: fila.find("td:eq(0)").text(),
      nombre: fila.find("td:eq(1)").text(),
      marca: fila.find("td:eq(2)").text(),
      sucursal: fila.find("td:eq(3)").text(),
      ambiente: fila.find("td:eq(4)").text(),
    };
    agregarActivoAlDetalle(activo);
  });

  $(document).on("click", ".btnQuitarActivo", function () {
    $(this).closest("tr").remove();
  });

  // Guardar Activo
  $("#btnGuardarActivo").on("click", function (e) {
    e.preventDefault();
    
    // Validar que haya al menos un activo en la tabla
    if ($("#tbldetalleactivoreg tbody tr").length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Debe agregar al menos un activo al detalle'
        });
        return;
    }

    // Recolectar datos de la tabla
    let activos = [];
    $("#tbldetalleactivoreg tbody tr").each(function() {
        let row = $(this);
        activos.push({
            IdDocIngresoAlm: $("#inputDocIngresoAlm").val(),
            IdArticulo: row.find("td:eq(0)").text(),
            Serie: row.find("input[name='serie[]']").val(),
            IdEstado: 1, // Estado por defecto
            IdSucursal: 1, // Sucursal por defecto
            IdAmbiente: row.find("select.ambiente-destino").val(),
            IdCategoria: row.find("select.categoria").val(),
            VidaUtil: 3, // Valor por defecto
            ValorAdquisicion: 0, // Valor por defecto
            FechaAdquisicion: new Date().toISOString().split('T')[0], // Fecha actual
            Garantia: 0, // Valor por defecto
            FechaFinGarantia: '1900-01-01', // Fecha por defecto
            Observaciones: row.find("textarea[name='observaciones[]']").val() || ''
        });
    });

    // Enviar datos al servidor
    $.ajax({
        url: "../../controllers/GestionarActivosController.php?action=Registrar",
        type: "POST",
        data: {
            action: "Registrar",
            activos: JSON.stringify(activos)
        },
        dataType: "json",
        success: function (res) {
            if (res.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: res.message,
                    timer: 1500
                }).then(() => {
                    // Limpiar tabla y volver a la vista principal
                    $("#tbldetalleactivoreg tbody").empty();
                    $("#divregistroActivo").hide();
                    $("#divlistadoactivos").show();
                    $("#divtblactivos").show();
                    listarActivosTable();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la petición:', jqXHR.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al registrar los activos: ' + errorThrown
            });
        }
    });
  });
}

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
            row.IdArticulo +
            '"><i class="fa fa-check"></i></button>'
          );
        },
      },
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
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
  if ($(`#tbldetalleactivoreg tbody tr[data-id='${activo.id}']`).length > 0) {
    NotificacionToast(
      "error",
      `El activo <b>${activo.nombre}</b> ya está en el detalle.`
    );
    return false;
  }
  var numeroFilas = $("#tbldetalleactivoreg").find("tbody tr").length;

  var selectAmbienteDestino = `<select class='form-control form-control-sm ambiente-destino' name='ambiente_destino[]' id="comboAmbiente${numeroFilas}"></select>`;

  var selectCategoria = `<select class='form-control form-control-sm categoria' name='categoria[] id="comboCategoria${numeroFilas}"></select>`;

  var inputEstadoActivo = `<input type="text" class="form-control form-control-sm" name="estado_activo[]" value="Operativa" disabled>`;

  var nuevaFila = `<tr data-id='${activo.id}' class='table-success agregado-temp'>
    <td>${activo.id}</td>
    <td>${activo.nombre}</td>
    <td>${activo.marca}</td>
    <td><input type="text" class="form-control form-control-sm" name="codigo[]" placeholder="Codigo"></td>
    <td><input type="text" class="form-control form-control-sm" name="serie[]" placeholder="Serie"></td>
    <td>${inputEstadoActivo}</td>
    <td>${selectAmbienteDestino}</td>
    <td><textarea class='form-control form-control-sm' name='observaciones[]' rows='1' placeholder='Observaciones'></textarea>
</td>
    
    <td><button type='button' class='btn btn-danger btn-sm btnQuitarActivo'><i class='fa fa-trash'></i></button></td>
  </tr>`;
  $("#tbldetalleactivoreg tbody").append(nuevaFila);
  console.log(`comboAmbiente${numeroFilas}`);
  ListarCombosAmbiente(`comboAmbiente${numeroFilas}`);
  // console.log(`comboCategoria${numeroFilas}`)
  // ListarCombosCategoria(`comboCategoria${numeroFilas}`);

  // ListarCombosResponsable(`comboResponsable${numeroFilas}`);

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
}

// function ListarCombosResponsable(elemento) {
//   $.ajax({
//     url: "../../controllers/GestionarMovimientoController.php?action=combos",
//     type: "POST",
//     dataType: "json",
//     async: false,

//     success: (res) => {
//       if (res.status) {
//         $(`#${elemento}`).html(res.data.responsable).trigger("change");

//         $(`#${elemento}`).select2({
//           theme: "bootstrap4",
//           //dropdownParent: $("#ModalFiltros .modal-body"),
//           width: "100%",
//         });
//       } else {
//         Swal.fire(
//           "Filtro de movimientos",
//           "No se pudieron cargar los combos: " + res.message,
//           "warning"
//         );
//       }
//     },
//     error: (xhr, status, error) => {
//       Swal.fire(
//         "Filtros de movimientos",
//         "Error al cargar combos: " + error,
//         "error"
//       );
//     },
//   });
// }
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

function ListarCombosEstado(elemento) {
  $.ajax({
    url: "../../controllers/GestionarActivosController.php?action=combos",
    type: "POST",
    dataType: "json",
    async: false,
    success: (res) => {
      if (res.status) {
        $(`#${elemento}`).html(res.data.estado).trigger("change");
        $(`#${elemento}`).select2({
          theme: "bootstrap4",
          width: "100%",
        });
      } else {
        Swal.fire(
          "Filtro de estados",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      Swal.fire(
        "Filtros de estados",
        "Error al cargar combos: " + error,
        "error"
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
    url: "../../controllers/GestionarActivosController.php?action=combos",
    type: "POST",
    dataType: "json",
    success: (res) => {
      if (res.status) {
        // $("#filtroTipoMovimiento")
        //   .html(res.data.tipoMovimiento)
        //   .trigger("change");
        $("#filtroCategoria").html(res.data.categorias).trigger("change");
        $("#filtroSucursal").html(res.data.sucursales).trigger("change");
        $("#filtroAmbiente").html(res.data.ambientes).trigger("change");
        // $("#filtroSucursalDestino").html(res.data.sucursales).trigger("change");

        $(
          "#filtroTipoMovimiento, #filtroSucursal, #filtroAmbiente , #filtroCategoria"
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
    success: (res) => {
      if (res.status) {
        $("#IdTipoMovimientoMov")
          .html(res.data.tipoMovimiento)
          .trigger("change");
        $("#CodAutorizador").html(res.data.autorizador).trigger("change");
        $("#IdSucursalOrigen").html(res.data.sucursales).trigger("change");
        $("#IdSucursalDestino").html(res.data.sucursales).trigger("change");
        // $("#ambiente_destino").html(res.data.ambientes).trigger("change");
        // $("#usuario_destino").html(res.data.responsable).trigger("change");

        $(
          "#IdTipoMovimientoMov, #CodAutorizador, #IdSucursalOrigen, #IdSucursalDestino, #ambiente_destino, #usuario_destino"
        ).select2({
          theme: "bootstrap4",
          //dropdownParent: $("#ModalMovimiento .modal-body"),
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

// Function lis

// Listar movimientos en una tabla DataTable
function listarActivosTable() {
  $("#tblRegistros").DataTable({
    dom: "Bfrtip",
    responsive: true,
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
        render: () =>
          '<button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>',
      },

      { data: "idActivo" },
      { data: "CodigoActivo" },
      { data: "NumeroSerie" },
      { data: "NombreArticulo" },
      { data: "MarcaArticulo" },
      { data: "Sucursal" },
      { data: "Proveedor" },
      { data: "Estado" },
      { data: "valorSoles" },
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> Exportar',
      },
    ],
  });
}

function guardarMantenimiento(e) {
    e.preventDefault();
    
    // Validar campos requeridos
    const requiredFields = [
        'IdDocIngresoAlm', 'IdArticulo', 'Codigo', 'IdEstado', 
        'EnUso', 'IdSucursal', 'IdCategoria', 'VidaUtil', 
        'ValorAdquisicion', 'FechaAdquisicion'
    ];
    
    let isValid = true;
    requiredFields.forEach(field => {
        const element = document.getElementById(field);
        if (!element.value) {
            element.classList.add('is-invalid');
            isValid = false;
        } else {
            element.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor complete todos los campos requeridos'
        });
        return;
    }
    
    var formData = new FormData($("#frmmantenimiento")[0]);
    formData.append("action", "Registrar");
    
    // Asegurarse de que los campos numéricos tengan valores válidos
    const numericFields = ['VidaUtil', 'ValorAdquisicion', 'Garantia'];
    numericFields.forEach(field => {
        if (!formData.get(field)) {
            formData.set(field, '0');
        }
    });
    
    // Asegurarse de que las fechas tengan valores válidos
    const dateFields = ['FechaAdquisicion', 'FechaFinGarantia'];
    dateFields.forEach(field => {
        if (!formData.get(field)) {
            formData.set(field, '1900-01-01');
        }
    });
    
    // Asegurarse de que los campos de ID tengan valores válidos
    const idFields = ['IdDocIngresoAlm', 'IdArticulo', 'IdEstado', 'IdSucursal', 'IdAmbiente', 'IdCategoria', 'IdProveedor'];
    idFields.forEach(field => {
        if (!formData.get(field)) {
            formData.set(field, 'NULL');
        }
    });

    $.ajax({
        url: "/app/controllers/GestionarActivosController.php",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            try {
                var data = JSON.parse(datos);
                if (data.status) {
                    $("#ModalMantenimiento").modal("hide");
                    ListarActivos();
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.msg,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.msg
                    });
                }
            } catch (e) {
                console.error('Error al procesar la respuesta:', e);
                console.log('Respuesta del servidor:', datos);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al procesar la respuesta del servidor'
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('Error en la petición AJAX:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al comunicarse con el servidor'
            });
        }
    });
}
