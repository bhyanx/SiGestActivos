<?php
  require_once("../../config/configuracion.php");
  session_destroy();
  header("Location:".Conectar::ruta());
  exit();
?>