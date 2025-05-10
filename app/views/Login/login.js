let tabla;

function init() {
    $("#login_form").on("submit", function(e){
        Login(e);
    })
}

function Login(e){
    e.preventDefault();

    let usuario = $("#CodUsuario").val();
    let clave = $("#ClaveAcceso").val();

    if (!usuario || !clave){
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Por favor complete todos los campos'
        });
        return;
    }

    Swal.fire({
        title: 'Validando sus datos',
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        url: "/app/controllers/UsuarioController.php?action=AccesoUsuario",
        type: "POST",
        data: {
            CodUsuario: usuario,
            ClaveAcceso: clave
        },
        dataType: 'json',
        success: function(data){
            if(data.status){
                // Asegurarse de que la URL sea absoluta
                if(data.msg.startsWith('/')){
                    window.location.href = data.msg;
                } else {
                    window.location.href = '/' + data.msg;
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.msg
                });
            }
        },
        error: function(xhr, status, error){
            let errorMessage = 'Ocurrió un error al procesar la solicitud';
            try {
                // Try to get a meaningful error message if possible
                const response = xhr.responseText;
                if (response.includes('<br />')) {
                    // If it's an HTML error, show a generic message
                    errorMessage = 'Error en el servidor. Por favor contacte al administrador.';
                }
            } catch (e) {
                console.error('Error parsing response:', e);
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
            console.error('Server Response:', xhr.responseText);
        }
    });
}
init();