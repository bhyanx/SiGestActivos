<?php
// =========================
// CONFIGURACIÓN DE DATOS
// =========================
$codigo = "ACT-0001";
$nombre = "Laptop Dell XPS 13";
$ubicacion = "Oficina Principal";

// =========================
// GENERAR CÓDIGO ZPL
// =========================
$zpl = "^XA
^PW400
^CF0,30
^FO30,30^FDActivo:^FS
^FO150,30^FD$codigo^FS
^FO30,70^FDNombre:^FS
^FO150,70^FD$nombre^FS
^FO30,110^FDUbicacion:^FS
^FO150,110^FD$ubicacion^FS
^FO30,150^BY2
^BCN,80,Y,N,N
^FD$codigo^FS
^XZ";

// =========================
// GENERAR VISTA PREVIA CON LABELARY API
// =========================
$url = "http://api.labelary.com/v1/printers/8dpmm/labels/4x2/0/";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: image/png"]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $zpl);
$image = curl_exec($ch);
curl_close($ch);

// Guardar temporalmente
file_put_contents("etiqueta.png", $image);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Etiqueta Activo <?= $codigo ?></title>
  <script src="BrowserPrint.min.js"></script>
  <script src="imprimir.js"></script>
</head>
<body>
  <h3>Vista previa de etiqueta</h3>
  <img src="etiqueta.png" alt="Etiqueta" style="border:1px solid #ccc"><br><br>

  <button id="btnPrint" 
          data-zpl="<?= htmlspecialchars($zpl) ?>">
    🖨️ Imprimir en Zebra
  </button>
</body>
</html>
