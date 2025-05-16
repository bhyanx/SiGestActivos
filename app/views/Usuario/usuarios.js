function init() {
  $("#frmUsuarios").on("submit", function (e) {
    e.preventDefault();
    guardaryeditarUsuarios(e);
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

function guardaryeditarUsuarios(e) {
  e.preventDefault();

  const formData = new FormData($("#frmUsuarios")[0]);

  formData.append(
    "userMod",
    "<?php echo $_SESSION['CodEmpleado'] ?? 'usuario_desconocido'; ?>"
  );

  $.ajax({
    url: "../../controllers/GestionarUsuariosController.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: (res) => {
      console.log("Respuesta servidor:", res);
      try {
        const response = JSON.parse(res);
        if (response.status) {
          Swal.fire("Usuario", response.message, "success");
          $("#frmUsuarios")[0].reset();
          $("#tblUsuarios").DataTable().ajax.reload();
          $("#ModalUsuarios").modal("hide");
        } else {
          Swal.fire("Error", response.message, "error");
        }
      } catch (e) {
        Swal.fire(
          "Error",
          "Hubo un problema procesando la respuesta.",
          "error"
        );
        console.error("No se pudo parsear la respuesta:", res);
      }
    },

    error: (xhr, status, error) => {
      console.error("Error AJAX:", xhr.responseText, status, error);
      Swal.fire("Error", "No se pudo registrar el usuario.", "error");
    },
  });
}

function listarUsuarios() {
  $("#tblUsuarios").DataTable({
      dom: "Bfrtip",
      responsive: true,
      lengthChange: false,
      colReorder: true,
      autoWidth: false,
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
      ],
      ajax: {
        url: "../../controllers/UsuarioController.php?action=LeerUsuarios",
        type: "POST",
        dataType: "json",
        data: {
          CodUsuario: "",
          IdRol: "",
          ClaveAcceso: ""
        },
        dataSrc: function (json) {
          console.log("Consultar response:", json); // Para depuración
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
      responsive: true,
      bInfo: true,
      iDisplayLength: 10,
      autoWidth: false,
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
      columnDefs: [
        {
          targets: 0,
          data: null,
          render: function (data, type, row) {
            return (
              '<button class="btn btn-sm btn-primary" onclick="editar(event, ' +
              row.CodUsuario +
              ')"><i class="fa fa-edit"></i></button>'
            );
          },
        },
        { targets: 1, data: "CodUsuario" },
        { targets: 2, data: "IdRol" },
        { targets: 3, data: "ClaveAcceso" }
      ],
    })
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

init();
