document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("btnPrint");
  btn.addEventListener("click", () => {
    const zpl = btn.dataset.zpl;

    BrowserPrint.getDefaultDevice("printer", function (printer) {
      if (!printer) {
        alert("No se encontró impresora Zebra. Verifica Browser Print.");
        return;
      }

      printer.send(zpl, 
        () => alert("Etiqueta enviada a la Zebra ✅"), 
        err => alert("Error al imprimir: " + err)
      );
    });
  });
});
