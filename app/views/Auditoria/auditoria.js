$(document).ready(() => {
  init();

  
});

function init() {
  // ListarCombosAcciones("AccionesAuditoria");
  // SeguimientoAuditoria();
  listarLogsAuditoria();
}

function SeguimientoAuditoria(usuario) {
  console.log('Consultando historial para usuario:', usuario);
  
  // Verificar si el modal existe
  if (!$("#ModalLogAuditoria").length) {
    console.error('El modal ModalLogAuditoria no existe en el DOM');
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se encontró el modal de historial'
    });
    return;
  }

  $.ajax({
    url: "../../controllers/AuditoriaController.php?action=obtenerHistorialAuditoria",
    type: "POST",
    data: {
      usuario: usuario,
    },
    success: (res) => {
      console.log('Respuesta del servidor:', res);
      
      try {
        // Intentar parsear la respuesta JSON si es una cadena
        let response = typeof res === 'string' ? JSON.parse(res) : res;
        
        // Verificar si la respuesta tiene el formato esperado
        if (response && response.status && response.data) {
          let historial = response.data;
          var html = "";
          
          if (historial.length === 0) {
            html = '<div class="alert alert-info">No hay historial disponible para este registro.</div>';
          } else {
            $.each(historial, function (index, registro) {
              const fecha = registro.fecha || 'Sin fecha';
              const detalle = registro.detalle || 'Sin detalle';
              const observaciones = registro.observaciones || '';
              const accion = registro.accion ? registro.accion.toLowerCase() : '';
              
              // Determinar la clase de estilo según la acción
              let timelineClass = 'timeline-item';
              if (accion.includes('crear') || accion.includes('insertar') || accion.includes('nuevo')) {
                timelineClass += ' bg-success';
              } else if (accion.includes('editar') || accion.includes('actualizar') || accion.includes('modificar')) {
                timelineClass += ' bg-warning';
              } else if (accion.includes('eliminar') || accion.includes('borrar')) {
                timelineClass += ' bg-danger';
              }
              
              html += `
                <div class="${timelineClass}">
                  <div class="timeline-date">
                    <i class="fas fa-calendar-alt"></i> ${fecha}
                  </div>
                  <div class="timeline-content">
                    <p class="mb-0"><strong>${detalle}</strong></p>
                    ${observaciones ? `<p class="text-muted mt-1">${observaciones}</p>` : ''}
                  </div>
                </div>
              `;
            });
          }
          
          // Limpiar el contenido anterior y agregar el nuevo
          $("#timedata").empty().html(html);
          
          // Abrir el modal
          $("#ModalLogAuditoria").modal('show');
        } else {
          console.error('Formato de respuesta inesperado:', response);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: response.message || 'Formato de respuesta del servidor inesperado.'
          });
        }
      } catch (error) {
        console.error('Error al procesar la respuesta:', error);
        console.error('Respuesta original:', res);
        
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al procesar la respuesta del servidor. Por favor, revise la consola para más detalles.'
        });
      }
    },
    error: function(xhr, status, error) {
      console.error('Error en la petición AJAX:', error);
      console.error('Status:', status);
      console.error('Response:', xhr.responseText);
      
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'Error al comunicarse con el servidor. Por favor, revise la consola para más detalles.'
      });
    }
  });
}

function ListarAcciones() {
  $("#tblAuditorias").DataTable({
    dom: "Bfrtip",
    responsive: true,
    lengthChange: false,
    colReorder: true,
    autoWidth: false,
    buttons: [
      {
        extends: "excelHtml5",
        title: "Listado de auditorias",
        text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
        autoFilter: true,
        sheetName: "data",
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6, 7, 8],
        },
      },
      "pageLength",
    ],
    ajax: {
      url: "app/controllers/AuditoriaController.php?action=Consultar",
      type: "POST",
      dataType: "json",
      data: {
        idLog: "",
        usuario: "",
        Nombre: "",
        accion: "",
        Tabla: "",
        IdRegistro: "",
        Fecha: "",
        Detalle: "",
      },
      dataSrc: function (json) {
        console.log("Consultar Response:", json);
        return json || [];
      },
      error: function (xhr, status, error) {
        console.log("Error en AJAX:", xhr.responseText, status, error);
        Swal.fire(
          "Listar auditorias",
          "Error al cargar datos: " + error,
          "error"
        );
      },
    },
    bDestroy: true,
    responsive: true,
    bInfo: true,
    iDisplayLength: 10,
    autoWidth: false,
    languaje: {
      prosessing: "Procesando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron resultados",
      emptyTable: "Ningún dato disponible en esta tabla",
      infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      infoFiltered: "(filtrado de un total de _MAX_ registros)",
      search: "Buscar:",
      info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      paginate: {
        first: "Primero",
        previous: "Anterior",
        next: "Siguiente",
        last: "Ultimo",
      },
    },
    columnDefs: [
      {
        targets: 0,
        data: null,
        render: function (data, type, row) {
          return (
            '<div class="text-center"><button class="btn btn-primary btn-sm" onclick="verDetalles(' +
            data.IdLog +
            ');"><i class="fas fa-eye"></i></button></div>'
          );
        },
      },
    ],
  });
}

// function ListarCombosAcciones(elemento) {
//     $.ajax({
//         url: "/app/controllers/AuditoriaController.php?action=combos",
//         type: "POST",
//         dataType: "json",
//         async: false,

//         success: (res) => {
//             if (res.status) {
//                     $(`#${elemento}`).html(res.data.accionesAuditoria).trigger("change");
//             } else {
//                 Swal.fire(
//                     "Filtro de acciones de auditoria",
//                     "No se pudieron cargar los combos: " + res.message,
//                     "warning"
//                 );
//             }
//         },
//         error: (xhr, status, error) => {
//             Swal.fire(
//                 "Filtro de acciones de auditoria",
//                 "Error al cargar combos: " + error,
//                 "error"
//             );
//         },
//     });
// }

function listarLogsAuditoria() {
  $("#tblAuditorias").DataTable({
    dom: "Bfrtip",
    responsive: true,
    destroy: true,
    ajax: {
      url: "../../controllers/AuditoriaController.php?action=Consultar",
      type: "POST",
      dataType: "json",
      dataSrc: function (json) {
        return json || [];
      },
    },
    columns: [
      {
        data: null,
        render: (data, type, row) =>
          `<button class="btn btn-sm btn-info btn-voir" data-usuario="${row.usuario}"><i class="fas fa-eye"></i></button>`,
      },
      { data: "idLog" },
      { data: "usuario" },
      { data: "NombreTrabajador" },
      { data: "accion" },
      { data: "tabla" },
      { data: "idRegistro" },
      { data: "fecha" },
      { data: "detalle" },
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> Exportar',
      },
    ],
  });

  // Agregar evento click al botón de ver historial
  $(document).on('click', '.btn-voir', function() {
    const usuario = $(this).data('usuario');
    SeguimientoAuditoria(usuario);
  });
}
