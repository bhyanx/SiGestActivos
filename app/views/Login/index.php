<?php
require_once("../../config/configuracion.php");

if(!isset($_SESSION["IdRol"])){
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php //require_once '../Layouts/Header.php' ?>
    <link href="https://cdn.jsdelivr.net/npm/mdb-ui-kit@9.0.0/css/mdb.min.css" rel="stylesheet" />
    <title>Login | Sistema Activos</title>
</head>
<style>
    .divider:after,
    .divider:before {
        content: "";
        flex: 1;
        height: 3px;
        background: rgb(2, 141, 0);
    }

    .btn-login {
        transition: all 0.3s ease-in-out !important;
        /* &:hover{
        background-color: #02732A !important;
        color: white !important;
        } */
    }

    .bg-form {
        background-color: rgba(8, 166, 3, 0.36) !important;
        border-radius: 20px 10px 10px 20px !important;
        opacity: 0.9 !important;
        border: 2px solid #02732A;
    }

    .bg-login {
        background-image: url('/public/img/Login-Background.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .bg-lubriseng {
        background-color: #09A603 !important;
    }

    .h-custom {
        height: calc(100% - 73px);
    }

    @media (max-width: 450px) {
        .h-custom {
            height: 100%;
        }
    }

    input[type="text"],
    input[type="password"] {
        background-color: #343A40 !important;
        border: 1px solid #02732A !important;
    }
</style>

<body>
    <section class="vh-100 bg-login">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark text-black" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">

                            <div class="mb-md-5 mt-md-4 pb-5">
                                <div class="text-center">
                                    <img src="/public/img/Logo-Lubriseng.png"
                                        style="width: 200px;" alt="logo">
                                    <h2 class="mt-3 mb-2 pb-1">Iniciar Sesión</h2>
                                    <p class="text-white-50 mb-5">Por favor ingrese correctamente su código de usuario y contraseña</p>
                                </div>

                                <form id="login_form">
                                    <div data-mdb-input-init class="form-outline form-white mb-4">
                                        <input type="text" id="CodUsuario" name="CodUsuario" class="form-control form-control-lg" required />
                                        <label class="form-label" for="CodUsuario">Codigo de Usuario</label>
                                    </div>

                                    <div data-mdb-input-init class="form-outline form-white mb-4">
                                        <input type="password" id="ClaveAcceso" name="ClaveAcceso" class="form-control form-control-lg" />
                                        <label class="form-label" for="ClaveAcceso">Contraseña</label>
                                    </div>

                                    <div class="divider d-flex align-items-center my-4">
                                        <!-- <p class="text-center fw-bold mx-3 mb-0 text-muted">OR</p> -->
                                        <i class="fa-solid fa-gear mx-3" style="color: #09A603;"></i>
                                    </div>

                                    <button data-mdb-button-init data-mdb-ripple-init class="btn mt-5 btn-outline-light btn-lg px-5 btn-login" type="submit">Ingresar</button>

                                </form>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php require_once '../Layouts/Footer.php' ?>
    <script src="/app/views/Login/login.js"></script>
</body>

</html>
<?php
} else {
    header("Location: ../views/Home/");
    exit();
}