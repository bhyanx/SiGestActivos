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
    });
  
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
        id: $(this).data("id"),
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
        activos.push({
          IdDocIngresoAlm: $("#inputDocIngresoAlm").val(),
          IdArticulo: row.find("td:eq(0)").text(),
          Serie: row.find("input[name='serie[]']").val(),
          IdAmbiente: row.find("select.ambiente").val() || null,
          IdCategoria: row.find("select.categoria").val() || null,
          Observaciones: row.find("textarea[name='observaciones[]']").val() || "",
        });
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
  
    var selectAmbiente = `<select class='form-control form-control-sm ambiente' name='ambiente[]' id="comboAmbiente${numeroFilas}"></select>`;
  
    var selectCategoria = `<select class='form-control form-control-sm categoria' name='categoria[]' id="comboCategoria${numeroFilas}"></select>`;
  
    var inputEstadoActivo = `<input type="text" class="form-control form-control-sm" name="estado_activo[]" value="Operativa" disabled>`;
  
    var nuevaFila = `<tr data-id='${activo.id}' class='table-success agregado-temp'>
      <td>${activo.id}</td>
      <td>${activo.nombre}</td>
      <td>${activo.marca}</td>
      <td><input type="text" class="form-control form-control-sm" name="codigo[]" placeholder="Codigo"></td>
      <td><input type="text" class="form-control form-control-sm" name="serie[]" placeholder="Serie"></td>
      <td>${inputEstadoActivo}</td>
      <td>${selectAmbiente}</td>
      <td>${selectCategoria}</td>
      <td><textarea class='form-control form-control-sm' name='observaciones[]' rows='1' placeholder='Observaciones'></textarea></td>
      <td><button type='button' class='btn btn-danger btn-sm btnQuitarActivo'><i class='fa fa-trash'></i></button></td>
    </tr>`;
    $("#tbldetalleactivoreg tbody").append(nuevaFila);
    console.log(`comboAmbiente${numeroFilas}`);
    ListarCombosAmbiente(`comboAmbiente${numeroFilas}`);
    console.log(`comboCategoria${numeroFilas}`);
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
          $("#IdTipoMovimientoMov, #CodAutorizador, #IdSucursalOrigen, #IdSucursalDestino").select2({
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
      aServerSide: false,
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
      responsive: true,
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
    });
  }