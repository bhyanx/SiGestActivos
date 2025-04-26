var tabla;
// var user_id = $("#user_idx").val();
// var rol_id = $("#rol_idx").val();

function init() {
  $("#login_form").on("submit", function (e) {
    Login(e);
  })
}

$(document).ready(function (e) {
  ListarEmpresas(e)
  ListarUnidadNegocio(e)
  $(".main-footer").hide()
});

function ListarEmpresas() {
  Swal.fire({
    title: 'Iniciando Sistema',
    timerProgressBar: true,
    didOpen: () => {
      Swal.showLoading()
      const b = Swal.getHtmlContainer().querySelector('b')
    }
  })
  $.ajax({
    url: '../../controllers/Configuracion.php?op=combo_empresa',
    type: 'POST',
    async: false,
    success: (res) => {
      // console.log(res)
      Swal.close()
      $("#cod_empresa").html(res).change()
    }
  })
}

function ListarUnidadNegocio() {
  $.ajax({
    url: '../../controllers/Configuracion.php?op=combo_unidades_negocio',
    type: 'POST',
    data:{
      cod_empresa: $("#cod_empresa option:selected").val()
    },
    success: (res) => {
      $("#cod_UnidadNeg").html(res).change()
    }
  })
}

function Login(e) {
  e.preventDefault();
  Swal.fire({
    title: 'Iniciando Sesión',
    timerProgressBar: true,
    didOpen: () => {
      Swal.showLoading()
      const b = Swal.getHtmlContainer().querySelector('b')
    }
  })
  $.post("../../controllers/usuario.php?op=acceso",
    {
      cod_user: $("#cod_user").val(),
      clave_acceso: $("#clave_acceso").val(),
      cod_empresa: $("#cod_empresa option:selected").val(),
      empresa: $("#cod_empresa option:selected").text(),
      cod_UnidadNeg: $("#cod_UnidadNeg option:selected").val(),
      UnidadNegocio: $("#cod_UnidadNeg option:selected").text()
    } ,
    function (data) {
      data = JSON.parse(data);
      if (data.status){
        window.location.href = data.msg;
      }else {
        Swal.fire("Upps!", data.msg, "error");
      }
      console.log(data);
    })
}

$(document).on('change', "#cod_empresa", ()=>{
  var cod_empresa = $("#cod_empresa option:selected").val()
  $("#logoempresa").attr("src", '../../public/img/logo-' + cod_empresa +'.png')
  ListarUnidadNegocio()
})

$(document).on('click','#btnmostrarocultarpass', (e)=>{
  var tipoinput = $("#clave_acceso").prop('type');
  if(tipoinput === 'password'){
    $('#clave_acceso').prop('type', 'text');
    $("#iconinputpass").removeClass().addClass("fas fa-eye-slash")
  }else{
    $('#clave_acceso').prop('type', 'password');
    $("#iconinputpass").removeClass().addClass("fas fa-eye")
  }
})

document.addEventListener('DOMContentLoaded', function() {
  const inputs = document.querySelectorAll('input, textarea, select, button[type="button"], button[type="submit"]');
  for (let i = 0; i < inputs.length; i++) {
    // console.log(inputs[i].type)
    if(inputs[i].type != 'submit'){
      inputs[i].addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          inputs[(i+1)%inputs.length].focus();
        }
      });
    }
  }
});

init();