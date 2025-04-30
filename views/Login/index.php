<?php

require_once("../../config/configuracion.php");
//require_once("../../models/Configuracion.php");
//$config = new Configuracion();
// *$fechaactual = date("Y-m-d");//'2024-08-24';
//$datatcr = $config->get_TipoCambio_MonedaRomina($fechaactual);
//var_dump($_SESSION);
// if (count($datatcr) > 0){
//   foreach ($datatcr as $row) {
//     $_SESSION['vgfechaactual'] = $fechaactual;
//     $_SESSION['vgtccompra'] = $row['Compra']; 
//     $_SESSION['vgtcventa'] = $row['Venta'];
//     $_SESSION['tipocambioservidor'] = 1;
//   }
// }else{
//   $datatc = $config->get_TipoCambio_Moneda($fechaactual);
//   if (is_array($datatc) || is_object($datatc)){
//     $_SESSION['vgfechaactual'] = $fechaactual;
//     $_SESSION['vgtccompra'] = $datatc->precioCompra;
//     $_SESSION['vgtcventa'] = $datatc->precioVenta;
//     $_SESSION['tipocambioservidor'] = 0;
//   }
// }

if(!isset($_SESSION["idRol"])){
?>
<!DOCTYPE html>
<html>

<head lang="es">
  <?php
  require_once("../MainHead/head.php");  
  ?>
    <title>Login</title>
</head>

<body class="hold-transition login-page bg-login">

  <div class="login-box">
    <div class="login-logo">
      <a href="../Dashboard/" class="h6">
        <!-- <b id="diaactual"></b> -->
        <img src="../../public/img/logo-1.png" alt="Logo Empresa" class="img-fluid" id="logoempresa">
      </a>
    </div>
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="../Login/" class="h3">SISTEMA GESTIÓN</a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Iniciar Sesión</p>
        <form id="login_form">
          <!-- <div class="input-group mb-3">
            <select name="cod_empresa" id="cod_empresa" class="form-control"></select>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-building"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <select name="cod_UnidadNeg" id="cod_UnidadNeg" class="form-control"></select>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-map-marked-alt"></span>
              </div>
            </div>
          </div> -->
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Usuario" value="" id="CodUsuario" name="CodUsuario" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Password" id="ClaveAcceso" name="ClaveAcceso" required>
            <div class="input-group-append">
              <button type="button" class="input-group-text" id="btnmostrarocultarpass">
                <span class="fas fa-eye" id="iconinputpass"></span>
              </button>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Acceder</button>
              <hr>
            </div>
          </div>
          <!-- <div class="row">
            <div class="col-12">
              <div class="card shadow">
                <div class="card-header bg-gradient-primary">
                  <h5 class="card-title">Tipo Cambio Sunat</h5>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-8"> -->
                      <!-- <h6>Fecha: <?php // echo date('d/m/Y', strtotime($fechaactual));?></h6> -->
                      <!-- <h5><span class="badge badge-success">Compra: <?php // echo $_SESSION['vgtccompra'];?></span></h1> -->
                    <!-- </div>
                    <div class="col-4 text-center">
                        <img class="img-fluid" src="../../public/img/sunat.ico" alt="">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
        </form>
      </div>
    </div>
  </div>

  <?php require_once("../MainFooter/mainjs.php") ?>
  <script src="Login/login.js"></script>
</body>
</html>
<?php
}else {
  header("Location:".Conectar::ruta());
  exit();
}
?>



<!-- // RECICLED -->

<!-- <h5><span class="badge badge-info">Venta: CODIGO PHP</span></h5=> -->
<?php // echo $_SESSION['vgtcventa'];?>