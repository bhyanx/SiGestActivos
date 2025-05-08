<?php

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once '../Layouts/Header.php' ?>
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
                                    <img src="/public//img/Logo-Lubriseng.png"
                                        style="width: 200px;" alt="logo">
                                    <h2 class="mt-3 mb-2 pb-1">Iniciar Sesión</h2>
                                    <p class="text-white-50 mb-5">Por favor ingrese correctamente su código de usuario y contraseña</p>
                                </div>

                                <!-- <hr class="my-4"> -->

                                <!-- <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                                <p class="text-white-50 mb-5">Please enter your login and password!</p> -->

                                <div data-mdb-input-init class="form-outline form-white mb-4">
                                    <input type="text" id="typeEmailX" class="form-control form-control-lg" />
                                    <label class="form-label" for="typeEmailX">Codigo de Usuario</label>
                                </div>

                                <div data-mdb-input-init class="form-outline form-white mb-4">
                                    <input type="password" id="typePasswordX" class="form-control form-control-lg" />
                                    <label class="form-label" for="typePasswordX">Contraseña</label>
                                </div>

                                <div class="divider d-flex align-items-center my-4">
                                    <!-- <p class="text-center fw-bold mx-3 mb-0 text-muted">OR</p> -->
                                    <i class="fa-solid fa-gear mx-3" style="color: #09A603;"></i>
                                </div>

                                <!-- <p class="small mb-5 pb-lg-2"><a class="text-white-50" href="#!">Forgot password?</a></p> -->

                                <a href="./Home/">
                                    <button data-mdb-button-init data-mdb-ripple-init class="btn mt-5 btn-outline-light btn-lg px-5 btn-login" type="submit">Ingresar</button>
                                </a>

                                <!-- <div class="d-flex justify-content-center text-center mt-4 pt-1">
                                    <a href="#!" class="text-white"><i class="fab fa-facebook-f fa-lg"></i></a>
                                    <a href="#!" class="text-white"><i class="fab fa-twitter fa-lg mx-4 px-2"></i></a>
                                    <a href="#!" class="text-white"><i class="fab fa-google fa-lg"></i></a>
                                </div> -->

                            </div>

                            <!-- <div>
                                <p class="mb-0">Don't have an account? <a href="#!" class="text-white-50 fw-bold">Sign Up</a>
                                </p>
                            </div> -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>

</html>