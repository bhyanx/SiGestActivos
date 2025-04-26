<footer class="main-footer align-self-center">
  <div class="float-right d-none d-sm-block">
    <b>Version</b> 1.0.0
  </div>

  <strong>Copyright © <?php echo date('Y'); ?>
    <a href="https://www.lubriseng.com.pe/" target="_blank">
      <?php

      if (isset($_SESSION['vgEmpresa'])) {
        echo  $_SESSION['vgEmpresa'];
      } else {
        echo 'Bryan Sanchez Garcia';
      }
      ?>
    </a>.
  </strong> Todos los derechos reservados.
  <?php echo date('Y'); ?>
</footer>