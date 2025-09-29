function init() {
  $("#frmUsuarios").on("submit", function (e) {
    e.preventDefault();
    guardaryeditarUsuarios(e);
  });

  $("#frmEditarRol").on("submit", function (e) {
    e.preventDefault();
    actualizarRolUsuario(e);
  });

  $("#idUsuario").on("change", function () {
    let idUsuario = $(this).val();
    if (idUsuario) {
      cargarUsuarios(idUsuario);
    } else {
      $("#detallesUsuario").html(
        "<p>Seleccione un usuario para ver sus detalles.</p>"
      );
    }
  });
  
  // Configurar el modal para que reinicie el select2 cuando se cierre
  $("#ModalEditarRol").on("hidden.bs.modal", function () {
    $("#IdRol").val(null).trigger("change");
  });
}

//* INICIALIZACIÓN
$(document).ready(() => {
  listarUsuarios();
  ListarCombos();
});

// Botón nuevo usuarios

$("#btnnuevo").click(() => {
  $("#tituloModalUsuarios").html(
    '<i class="fa fa-plus-circle"></i> Registrar Usuario'
  );
  $("#frmUsuarios")[0].reset();
  $("#usuarios").val("").trigger("change");
  $("#ModalUsuarios").modal("show");
});

function ListarCombos() {
  $.ajax({
    url: "../../controllers/GestionarMovimientoController.php?action=combos",
    type: "POST",
    dataType: "json",

    success: (res) => {
      console.log("Combos response:", res);
      if (res.status) {
        $("#IdUsuario").html(res.data.usuarios).trigger("change");

        $("#IdUsuario").select2({
          theme: "bootstrap4",
          dropdownParent: $("#ModalUsuarios .modal-body"),
          width: "100%",
        });
      } else {
        Swal.fire(
          "Crear Usuario",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      console.log("Error en combos:", xhr.responseText, status, error);
      Swal.fire("Crear Usuario", "Error al cargar combos: " + error, "error");
    },
  });
}

function ListarCombosRoles(elemento) {
  $.ajax({
    url: "../../controllers/UsuarioController.php?action=combos",
    type: "POST",
    dataType: "text", // Cambiar a text para manejar la respuesta mixta
    async: false,

    success: (res) => {
      console.log("Roles response:", res);
      
      // Verificar si la respuesta es un string (puede contener datos de usuarios + combos)
      if (typeof res === 'string') {
        try {
          // Intentar extraer la parte JSON que contiene los combos
          const jsonStartIndex = res.lastIndexOf('{"status":');
          if (jsonStartIndex !== -1) {
            const jsonPart = res.substring(jsonStartIndex);
            res = JSON.parse(jsonPart);
            console.log("Respuesta parseada:", res);
          }
        } catch (e) {
          console.error("Error al parsear respuesta:", e);
        }
      }
      
      if (res && res.status) {
        $(`#${elemento}`).html(res.data.roles).trigger("change");

        // Configurar el select2 con el parent correcto según el elemento
        let dropdownParent = null;
        if (elemento === "IdRol") {
          dropdownParent = $("#ModalEditarRol .modal-body");
        }

        $(`#${elemento}`).select2({
          theme: "bootstrap4",
          dropdownParent: dropdownParent,
          width: "100%",
        });
      } else {
        Swal.fire(
          "Filtro de roles",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      console.log("Error en roles:", xhr.responseText, status, error);
      Swal.fire(
        "Filtros de roles",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

function listarUsuarios() {
  $("#tblUsuarios").DataTable({
    aProcessing: true,
    aServerSide: false,
    layout: {
      topStart: {
        buttons: [
          {
            extend: "excelHtml5",
            title: "Listado Usuarios",
            text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
            autoFilter: true,
            sheetName: "Data",
            exportOptions: {
              columns: [1, 2, 3],
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
    ajax: {
      url: "../../controllers/UsuarioController.php?action=LeerUsuarios",
      type: "POST",
      dataType: "json",
      data: {
        CodUsuario: "",
        IdRol: "",
        ClaveAcceso: "",
      },
      dataSrc: function (json) {
        console.log("Consultar response:", json);
        return json || [];
      },
      error: function (xhr, status, error) {
        console.log("Error en AJAX:", xhr.responseText, status, error);
        Swal.fire(
          "Gestionar Usuarios",
          "Error al cargar datos: " + error,
          "error"
        );
      },
    },
    bDestroy: true,
    bInfo: true,
    iDisplayLength: 10,
    language: {
      processing: "Procesando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron resultados",
      emptyTable: "Ningún dato disponible en esta tabla",
      infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      infoFiltered: "(filtrado de un total de _MAX_ registros)",
      search: "Buscar:",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior",
      },
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "CodUsuario" },
      { data: "Nombres" },
      { data: "Apellidos" },
      { data: "NombreRol" },
      { data: "ClaveAcceso" },
      {
        data: "Activo",
        render: function (data, type, row) {
          return data == 1
            ? '<span class="badge badge-success text-sm border border-success">Activo</span>'
            : '<span class="badge badge-danger text-sm border border-danger">Inactivo</span>';
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return (
            '<button class="btn btn-sm btn-info" onclick="editarRol(' +
            row.CodUsuario +
            ')"><i class="fa fa-user-tag"></i></button>'
          );
        },
      },
    ],
  });
}

function cargarUsuarios(idUsuario) {
  $.ajax({
    url: "../../controllers/UsuarioController.php?action=listar_detalle",
    type: "POST",
    data: { idUsuario: idUsuario },
    dataType: "json",
    success: (res) => {
      console.log("Detalles recibidos:", res);
      if (res.status && res.data.length > 0) {
        let html = "<ul>";
        res.data.forEach((detalle) => {
          html += `
                        <li>
                            Usuario: ${detalle.NombreUsuario} <br>
                            Rol: ${detalle.Rol}
                        </li>
                    `;
        });
        html += "</ul>";
        $("#detallesUsuario").html(html);
      } else {
        $("#detallesUsuario").html("<p>No hay detalles disponibles.</p>");
      }
    },
    error: (xhr, status, error) => {
      console.error("Error al cargar detalles:", xhr.responseText);
      $("#detallesUsuario").html("<p>Error al cargar detalles.</p>");
    },
  });
}

function verDetalles(idUsuario) {
  $("#idUsuarioDetalle").val(idUsuario).trigger("change");
  $("#usuariosDetalleModal").modal("show");
}

// Función para editar el rol del usuario
function editarRol(codUsuario) {
  // Limpiar el formulario
  $("#frmEditarRol")[0].reset();
  
  // Establecer el código de usuario
  $("#CodUsuario").val(codUsuario);
  
  // Mostrar el modal
  $("#ModalEditarRol").modal("show");
  
  // Cargar roles después de mostrar el modal para asegurar que el select2 funcione correctamente
  setTimeout(function() {
    // Destruir el select2 existente si existe
    if ($("#IdRol").hasClass("select2-hidden-accessible")) {
      $("#IdRol").select2("destroy");
    }
    
    // Cargar los roles
    ListarCombosRoles("IdRol");
  }, 100);
  
  console.log("Modal de edición de rol abierto para usuario:", codUsuario);
}

// Función para actualizar el rol del usuario
function actualizarRolUsuario(e) {
  e.preventDefault();
  
  // Obtener los valores directamente
  let codUsuario = $("#CodUsuario").val();
  const idRol = $("#IdRol").val();
  const userMod = "<?php echo $_SESSION['CodEmpleado'] ?? 'usuario_desconocido'; ?>";
  
  // Verificar si el código de usuario necesita un formato específico (añadir cero inicial si es necesario)
  if (codUsuario && codUsuario.length === 7) {
    codUsuario = "0" + codUsuario; // Añadir cero inicial si tiene 7 dígitos
    console.log("CodUsuario formateado con cero inicial:", codUsuario);
  }
  
  console.log("Valores a enviar:", {
    CodUsuario: codUsuario,
    IdRol: idRol,
    UserMod: userMod
  });
  
  // Crear un nuevo FormData
  const formData = new FormData();
  
  // Agregar los parámetros exactos que espera el controlador
  formData.append("CodUsuario", codUsuario);
  formData.append("IdRol", idRol);
  formData.append("UserMod", userMod);
  
  
  $.ajax({
    url: "../../controllers/UsuarioController.php?action=ActualizarRol",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    
    // Mostrar datos que se están enviando para depuración
    beforeSend: function(xhr) {
      console.log("Enviando datos:", {
        CodUsuario: $("#CodUsuario").val(),
        IdRol: $("#IdRol").val(),
        UserMod: "<?php echo $_SESSION['CodEmpleado'] ?? 'usuario_desconocido'; ?>"
      });
    },
    
    success: (res) => {
      console.log("Respuesta servidor (raw):", res);
      
      try {
        let response;
        
        // Verificar si la respuesta es un string (puede contener datos adicionales)
        if (typeof res === 'string') {
          // Intentar extraer la parte JSON que contiene la respuesta
          const jsonStartIndex = res.lastIndexOf('{"status":');
          if (jsonStartIndex !== -1) {
            const jsonPart = res.substring(jsonStartIndex);
            response = JSON.parse(jsonPart);
            console.log("Respuesta parseada:", response);
          } else {
            response = JSON.parse(res);
          }
        } else {
          response = res;
        }
        
        if (response && response.status) {
          Swal.fire("Rol de Usuario", response.message || "Rol actualizado correctamente", "success");
          $("#frmEditarRol")[0].reset();
          $("#tblUsuarios").DataTable().ajax.reload();
          $("#ModalEditarRol").modal("hide");
        } else {
          Swal.fire("Error", (response && response.message) || "No se pudo actualizar el rol", "error");
        }
      } catch (e) {
        console.error("Error al procesar respuesta:", e);
        console.error("Respuesta que causó el error:", res);
        Swal.fire(
          "Error",
          "Hubo un problema procesando la respuesta del servidor.",
          "error"
        );
      }
    },
    
    error: (xhr, status, error) => {
      console.error("Error AJAX:", xhr.responseText, status, error);
      Swal.fire("Error", "No se pudo actualizar el rol del usuario.", "error");
    },
  });
}

init();

// function guardaryeditarUsuarios(e) {
//   e.preventDefault();

//   const formData = new FormData($("#frmUsuarios")[0]);

//   formData.append(
//     "userMod",
//     "<?php echo $_SESSION['CodEmpleado'] ?? 'usuario_desconocido'; ?>"
//   );

//   $.ajax({
//     url: "../../controllers/GestionarUsuariosController.php",
//     type: "POST",
//     data: formData,
//     contentType: false,
//     processData: false,

//     success: (res) => {
//       console.log("Respuesta servidor:", res);
//       try {
//         const response = JSON.parse(res);
//         if (response.status) {
//           Swal.fire("Usuario", response.message, "success");
//           $("#frmUsuarios")[0].reset();
//           $("#tblUsuarios").DataTable().ajax.reload();
//           $("#ModalUsuarios").modal("hide");
//         } else {
//           Swal.fire("Error", response.message, "error");
//         }
//       } catch (e) {
//         Swal.fire(
//           "Error",
//           "Hubo un problema procesando la respuesta.",
//           "error"
//         );
//         console.error("No se pudo parsear la respuesta:", res);
//       }
//     },

//     error: (xhr, status, error) => {
//       console.error("Error AJAX:", xhr.responseText, status, error);
//       Swal.fire("Error", "No se pudo registrar el usuario.", "error");
//     },
//   });
// }
