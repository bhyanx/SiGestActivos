$(document).ready(() => {
  init();
});

/**
 * Inicializa las funcionalidades de la página al cargar.
 */
function init() {
  ListarAmbientes();
}

/**
 * Abre el modal para crear un nuevo ambiente y limpia los campos del formulario.
 */
$("#btnnuevo").click(() => {
  $("#ModalAmbiente").modal("show");
  // Limpiar campos del formulario
  $("#nombre").val("");
  $("#descripcion").val("");
  $("#CodAmbiente").val("");
  $("#empresaModal").val("").trigger("change");
  $("#sucursalModal").val("").trigger("change");
  $("#estadoAmbiente").val("1");
});

/**
 * Vincula el evento de guardar al botón correspondiente en el modal.
 * Previene el comportamiento por defecto para evitar recarga de la página.
 */
$("#btnGuardarAmbiente").click((event) => {
  event.preventDefault();
  CrearAmbientes();
});

/**
 * Lista los ambientes en una tabla DataTable con datos obtenidos del servidor.
 */
function ListarAmbientes() {
  $("#tblAmbientes").DataTable({
    aProcessing: true,
    aServerSide: false,
    layout: {
      topStart: {
        buttons: [
          {
            extend: "excelHtml5",
            title: "Listado Ambientes",
            text: "<i class='fas fa-file-excel'></i> Exportar",
            autoFilter: true,
            sheetName: "Data",
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5],
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
    autoWidth: true,
    ajax: {
      url: "../../controllers/AmbienteController.php?action=ListarAmbientes",
      type: "POST",
      dataType: "json",
      data: function (d) {
        return {
          cod_empresa: $("#cod_empresa").val() || null,
          cod_UnidadNeg: $("#cod_UnidadNeg").val() || null,
          idAmbiente: "",
          nombre: "",
          descripcion: "",
          estado: "",
        };
      },
      dataSrc: function (json) {
        console.log("Consultar response:", json);
        return json || [];
      },
      error: function (xhr, status, error) {
        console.log("Error en AJAX:", xhr.responseText, status, error);
        Swal.fire(
          "Listar Ambientes",
          "Error al cargar datos: " + error,
          "error"
        );
      },
    },
    bDestroy: true,
    responsive: true,
    bInfo: true,
    iDisplayLength: 10,
    autoWidth: false,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "idAmbiente", visible: false, searchable: false },
      { data: "nombre" },
      { data: "descripcion" },
      { data: "NombreSucursal" },
      {
        data: "estado",
        render: function (data, type, row) {
          return data == 1
            ? '<span class="badge badge-success">Activo</span>'
            : '<span class="badge badge-danger">Inactivo</span>';
        },
      },
      {
        data: null,
        orderable: false,
        render: function (data, type, row) {
          return `
            <button class="btn btn-sm btn-danger" onclick="desactivarAmbiente(${row.idAmbiente})">
              <i class="fas fa-ban"></i>
            </button>`;
        },
      },
    ],
  });
}

/**
 * Registra un nuevo ambiente con los datos ingresados en el formulario del modal.
 */
function CrearAmbientes() {
  let nombre = $("#nombre").val();
  let descripcion = $("#descripcion").val();
  let codAmbiente = $("#CodAmbiente").val(); // This should be the ID from the form, it will be empty for new records
  let idEmpresa = $("#empresaModal").val() || null;
  let idSucursal = $("#sucursalModal").val() || null;
  let estado = $("#estadoAmbiente").val() || 1;
  let userMod = $("#userMod").val() || null;
  let codigoAmbiente = $("#codigoAmbiente").val() || null; // This is the new field for the unique code

  if (!nombre) {
    Swal.fire(
      "Crear Ambiente",
      "Por favor, complete el campo obligatorio (Nombre).",
      "warning"
    );
    return;
  }

  $.ajax({
    url: "../../controllers/AmbienteController.php?action=RegistrarAmbiente",
    type: "POST",
    dataType: "json",
    data: {
      nombre: nombre,
      descripcion: descripcion,
      //codAmbiente: codAmbiente === '' ? null : codAmbiente, // Send null if empty for new records
      idEmpresa: idEmpresa,
      idSucursal: idSucursal,
      estado: estado,
      userMod: userMod,
      codigoAmbiente: codigoAmbiente, // This is the new field for the unique code
    },
    success: (res) => {
      console.log("Respuesta del servidor:", res);
      if (res && res.status) {
        Swal.fire(
          "Crear Ambiente",
          "Ambiente registrado con éxito.",
          "success"
        ).then(() => {
          $("#ModalAmbiente").modal("hide");
          ListarAmbientes();
          // Actualizar los campos de empresa y sucursal si están disponibles en la respuesta
          if (res.data && res.data.idEmpresa) {
            $("#empresaModal").val(res.data.idEmpresa).trigger("change");
          }
          if (res.data && res.data.idSucursal) {
            $("#sucursalModal").val(res.data.idSucursal).trigger("change");
          }
        });
      } else {
        Swal.fire(
          "Crear Ambiente",
          "Error al registrar el ambiente: " + (res ? res.message : "Respuesta inválida del servidor"),
          "error"
        );
      }
    },
    error: (xhr, status, error) => {
      Swal.fire(
        "Crear Ambiente",
        "Error al registrar el ambiente: " + error,
        "error"
      );
    },
  });
}

/**
 * Desactiva un ambiente cambiando su estado a 0.
 */
function desactivarAmbiente(idAmbiente) {
  Swal.fire({
    title: '¿Está seguro de desactivar este ambiente?',
    text: "¡No podrá revertir esto!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, desactivar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      let userMod = $("#userMod").val() || null;
      $.ajax({
        url: "../../controllers/AmbienteController.php?action=Desactivar",
        type: "POST",
        dataType: "json",
        data: {
          IdAmbiente: idAmbiente,
          userMod: userMod
        },
        success: (res) => {
          if (res && res.status) {
            Swal.fire(
              'Desactivado!',
              'El ambiente ha sido desactivado.',
              'success'
            ).then(() => {
              ListarAmbientes();
            });
          } else {
            Swal.fire(
              'Error',
              'Hubo un problema al desactivar el ambiente: ' + (res ? res.message : 'Respuesta inválida del servidor'),
              'error'
            );
          }
        },
        error: (xhr, status, error) => {
          Swal.fire(
            'Error',
            'Error en la solicitud AJAX: ' + error,
            'error'
          );
        }
      });
    }
  });
}
