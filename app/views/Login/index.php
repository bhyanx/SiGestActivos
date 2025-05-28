<?php
require_once("../../config/configuracion.php");

if (!isset($_SESSION["IdRol"])) {
?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login | Sistema Activos</title>
        <link rel="stylesheet" href="/public/css/variables.css">
        <link rel="stylesheet" href="/public/css/login.css">
        <link rel="stylesheet" href="/public/css/animations.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>

    <body>
        <div class="login-container">
            <div class="background-overlay"></div>

            <div class="login-card">
                <div class="logo-container">
                    <div>
                        <img src="/public/img/Logo-Lubriseng.png" alt="Lubriseng Logo" class="logo">
                    </div>
                </div>

                <div class="login-content">
                    <h1 class="login-title">Iniciar Sesión</h1>
                    <p class="login-subtitle">Por favor ingrese correctamente su código de usuario y contraseña</p>

                    <form id="login_form" class="login-form">

                        <div class="form-group">
                            <div class="input-container">
                                <i class="fa-solid fa-building input-icon"></i>
                                <select name="CodEmpresas" id="CodEmpresas" class="form-input" placeholder="Empresa" require></select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-container">
                                <i class="fa-solid fa-city input-icon"></i>
                                <select name="CodUnidadNegocio" id="CodUnidadNegocio" class="form-input" placeholder="Unidad de Negocio" require></select>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="input-container">
                                <i class="fa-solid fa-user input-icon"></i>
                                <input type="text" id="CodUsuario" name="CodUsuario" class="form-input" placeholder="Código de Usuario" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-container">
                                <i class="fa-solid fa-lock input-icon"></i>
                                <input type="password" id="ClaveAcceso" name="ClaveAcceso" class="form-input" placeholder="Contraseña" required>
                            </div>
                        </div>

                        <div class="divider">
                            <i class="fa-solid fa-gear divider-icon"></i>
                        </div>

                        <button type="submit" class="login-button">
                            <span class="button-text">Ingresar</span>
                            <span class="button-icon"><i class="fa-solid fa-arrow-right"></i></span>
                        </button>
                    </form>
                </div>

                <div class="system-info">
                    <p>Sistema Activos &copy; 2025</p>
                </div>
            </div>
        </div>

        <?php require_once '../Layouts/Footer.php'; ?>
        <script src="/app/views/Login/login.js"></script>
    </body>

    </html>
<?php
} else {
    header("Location: ../views/Home/");
    exit();
}
