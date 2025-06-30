let tabla;

function init() {
  ListarCombosEmpresa("CodEmpresas");
  $("#login_form").on("submit", function (e) {
    Login(e);
  });

  // Cuando cambia la empresa, cargar las unidades de negocio correspondientes
  $("#CodEmpresas").on("change", function () {
    let codEmpresa = $(this).val();
    $.ajax({
      url: "../../controllers/UsuarioController.php?action=unidadnegocio",
      type: "POST",
      data: { cod_empresa: codEmpresa },
      dataType: "json",
      success: function (res) {
        if (res.status) {
          $("#CodUnidadNegocio").html(res.data).trigger("change");
        } else {
          $("#CodUnidadNegocio").html('<option value="">Seleccione</option>');
        }
      },
      error: function () {
        $("#CodUnidadNegocio").html('<option value="">Seleccione</option>');
      },
    });
  });
}

function ListarCombosEmpresa(elemento) {
  $.ajax({
    url: "../../controllers/UsuarioController.php?action=combos",
    type: "POST",
    dataType: "json",
    async: false,

    success: (res) => {
      if (res.status) {
        $(`#${elemento}`).html(res.data.empresas).trigger("change");
      } else {
        Swal.fire(
          "Filtro de empresas",
          "No se pudieron cargar los combos: " + res.message,
          "warning"
        );
      }
    },
    error: (xhr, status, error) => {
      Swal.fire(
        "Filtros de empresas",
        "Error al cargar combos: " + error,
        "error"
      );
    },
  });
}

function Login(e) {
  e.preventDefault();

  let usuario = $("#CodUsuario").val();
  let clave = $("#ClaveAcceso").val();
  let codEmpresa = $("#CodEmpresas").val();
  let codUnidadNegocio = $("#CodUnidadNegocio").val();

  if (!usuario || !clave) {
    Swal.fire({
      icon: "warning",
      title: "Atención",
      text: "Por favor complete todos los campos",
    });
    return;
  }

  Swal.fire({
    title: "Validando sus datos",
    timerProgressBar: true,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  $.ajax({
    url: "../../controllers/UsuarioController.php?action=AccesoUsuario",
    type: "POST",
    data: {
      CodUsuario: usuario,
      ClaveAcceso: clave,
      CodEmpresa: codEmpresa,
      CodUnidadNegocio: codUnidadNegocio
    },
    dataType: "json",
    success: function (data) {
      if (data.status) {
        window.location.href = data.msg;
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: data.msg,
        });
      }
    },
    error: function (xhr, status, error) {
      let errorMessage = "Ocurrió un error al procesar la solicitud";
      try {
        // Try to get a meaningful error message if possible
        const response = xhr.responseText;
        if (response.includes("<br />")) {
          // If it's an HTML error, show a generic message
          errorMessage =
            "Error en el servidor. Por favor contacte al administrador.";
        }
      } catch (e) {
        console.error("Error parsing response:", e);
      }

      Swal.fire({
        icon: "error",
        title: "Error",
        text: errorMessage,
      });
      console.error("Server Response:", xhr.responseText);
    },
  });
}
init();
