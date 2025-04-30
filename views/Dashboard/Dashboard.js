
// $(document).ready(function (e) {
//     CantidadDocumentosVentas()
//     CantidadRequerimientos()
//     CantidadDocumentosVentasxEnviar()
//       // The Calender
//     $('#calendar').datetimepicker({
//       format: 'L',
//       inline: true,
//       // language: 'es',
//       locale: 'es',
//     })
//   });
  
  
  
//   function CantidadRequerimientos() {
//     $.ajax({
//       url: "../../controller/home.php?op=CantidadRequerimientos",
//       type: "POST",
//       async: false,
//       success: (res) => {
//         res = JSON.parse(res);
//         console.log(res);
//         $("#lblcantrequerimientos").html(res.msg.Requerimientos);
//       },
//     });
//   }
  
//   function CantidadDocumentosVentas() {
//     $.ajax({
//       url: "../../controller/home.php?op=CantidadDocumentosVentas",
//       type: "POST",
//       async: false,
//       success: (res) => {
//         res = JSON.parse(res);
//         console.log(res);
//         $("#lblcantdocumentosventas").html(res.msg.TotalDocumentos);
//       },
//     });
//   }
  
//   function CantidadDocumentosVentasxEnviar() {
//     $.ajax({
//       url: "../../controller/home.php?op=CantidadDocumentosVentasxEnviar",
//       type: "POST",
//       async: false,
//       success: (res) => {
//         res = JSON.parse(res);
//         console.log(res);
//         $("#lblcantdocumentosventasxenviar").html(res.msg.TotalDocumentosxEnviar);
//       },
//     });
//   }