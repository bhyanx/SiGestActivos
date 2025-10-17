function init() {
  listarRoles();
}

$(document).ready(function () {
  init();
});

function listarRoles() {
  $("#tblRoles").DataTable({
    aProcessing: true,
    aServerSide: false,
    layout: {
      topStart: {
        buttons: [
          {
            extend: "excelHtml5",
            title: "Listado Roles",
            text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
            autoFilter: true,
            sheetName: "Data",
            exportOptions: {
              columns: [1, 2, 3],
            },
          },
          "pageLength",
          "colvis",
        ],
      },
      bottom: "paging",
      bottomStart: null,
      bottomEnd: null,
    },
    responsive: true,
    lengthChange: false,
    colReorder: true,
    autoWidth: false,
    ajax: {
      url: "../../controllers/RolController.php?action=ListarRoles",
      type: "POST",
      dataType: "json",
      data: {
        IdRol: "",
        NombreRol: "",
        Estado: "",
      },
      dataSrc: function (json) {
        console.log("Consultar response:", json);
        return json || [];
      },
      error: function (xhr, status, error) {
        console.log("Error en AJAX:", xhr.responseText, status, error);
        Swal.fire("Listar Roles", "Error al cargar datos: " + error, "error");
      },
    },
    bDestroy: true,
    responsive: true,
    bInfo: true,
    iDisplayLength: 10,
    autoWidth: false,
    language: {
      url: CONFIGURACION.URLS.IDIOMA_DATATABLES,
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "IdRol", visible: false, searchable: false },
      { data: "NombreRol" },
      {
        data: "Estado",
        render: function (data, type, row) {
          return data == 1
            ? '<span class="badge badge-success text-sm border border-success">Activo</span>'
            : '<span class="badge badge-danger text-sm border border-danger">Inactivo</span>';
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          let botones = '';
          
          if (row.Estado == 1) {
            // Rol activo - mostrar opciones de desactivar
            botones = `
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cogs"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="verPermisos(${row.IdRol})">
                                    <i class="fas fa-user-shield"></i> Ver permisos
                                </a>
                                <a class="dropdown-item" href="#" onclick="desactivar(${row.IdRol})">
                                    <i class="fas fa-ban"></i> Desactivar
                                </a>
                            </div>
                        </div>`;
          } else {
            // Rol inactivo - mostrar opción de activar
            botones = `
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cogs"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="activar(${row.IdRol})">
                                    <i class="fas fa-check"></i> Activar
                                </a>
                            </div>
                        </div>`;
          }
          
          return botones;
        },
      },
    ],
  });
}

/**
 * Función para ver los permisos de un rol específico
 */
function verPermisos(idRol) {
  $.ajax({
    url: "../../controllers/RolController.php?action=ListarPermisosRoles",
    type: "POST",
    dataType: "json",
    data: {
      IdRol: idRol
    },
    success: function(response) {
      if (response && response.length > 0) {
        mostrarPermisosModal(response, idRol);
      } else {
        Swal.fire('Información', 'Este rol no tiene permisos asignados', 'info');
      }
    },
    error: function(xhr, status, error) {
      console.log("Error al obtener permisos:", error);
      Swal.fire('Error', 'Error al obtener los permisos del rol', 'error');
    }
  });
}

/**
 * Función para mostrar el modal de permisos
 */
function mostrarPermisosModal(permisos, idRol) {
  // Variable global para almacenar los cambios temporales
  window.cambiosTemporales = {};
  // Variable global para almacenar los estados originales
  window.estadosOriginales = {};
  
  let contenidoHTML = '<div class="table-responsive"><table class="table table-bordered table-hover">';
  contenidoHTML += '<thead class="thead-dark"><tr><th>Módulo</th><th>Estado</th><th>Acción</th></tr></thead><tbody>';
  
  permisos.forEach(function(permiso, index) {
    const estado = permiso.Permiso == 1 ? 
      '<span class="badge badge-success"> Activo</span>' : 
      '<span class="badge badge-danger"> Inactivo</span>';
    
    const switchId = `switch_${idRol}_${index}`;
    const switchChecked = permiso.Permiso == 1 ? 'checked' : '';
    
    // Inicializar cambios temporales y estados originales
    window.cambiosTemporales[permiso.CodPermiso] = permiso.Permiso;
    window.estadosOriginales[permiso.CodPermiso] = permiso.Permiso;
    
    contenidoHTML += `<tr>
      <td><strong>${permiso.NombreMenu || 'N/A'}</strong></td>
      <td class="estado-cell-${index}">${estado}</td>
      <td class="text-center">
        <div class="custom-control custom-switch d-inline-block">
          <input type="checkbox" class="custom-control-input" id="${switchId}" ${switchChecked} 
                 onchange="cambiarEstadoTemporal(${idRol}, '${permiso.CodPermiso}', this.checked, ${index})">
          <label class="custom-control-label" for="${switchId}">
            <span class="switch-label-${index}">${permiso.Permiso == 1 ? ' Activo' : ' Inactivo'}</span>
          </label>
        </div>
      </td>
    </tr>`;
  });
  
  contenidoHTML += '</tbody></table></div>';
  
  // Agregar botones de acción
  contenidoHTML += `
    <div class="row mt-3">
      <div class="col-12 text-center">
        <button type="button" class="btn btn-success btn-lg" onclick="guardarCambiosPermisos(${idRol})">
          <i class="fas fa-save"></i> Guardar Cambios
        </button>
        <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="revertirCambiosPermisos(${idRol})">
          <i class="fas fa-undo"></i> Revertir Cambios
        </button>
      </div>
    </div>
  `;
  
  Swal.fire({
    title: `<i class="fas fa-user-shield text-primary"></i> Permisos del Rol`,
    html: contenidoHTML,
    width: '1000px',
    confirmButtonText: 'Cerrar',
    confirmButtonColor: '#3085d6',
    showCloseButton: true,
    showConfirmButton: true,
    customClass: {
      container: 'permisos-modal-container'
    },
    didOpen: () => {
      // Agregar estilos CSS personalizados para los switches y la tabla
      const style = document.createElement('style');
      style.textContent = `
        .permisos-modal-container .swal2-popup {
          font-size: 0.9rem;
        }
        .permisos-modal-container .table {
          margin-bottom: 0;
        }
        .permisos-modal-container .table th {
          background-color: #343a40;
          color: white;
          border-color: #454d55;
          font-size: 0.85rem;
          padding: 0.5rem;
        }
        .permisos-modal-container .table td {
          padding: 0.5rem;
          vertical-align: middle;
        }
        .permisos-modal-container .custom-switch {
          margin: 0;
        }
        .permisos-modal-container .custom-switch .custom-control-label::before {
          width: 2.5rem;
          height: 1.25rem;
          border-radius: 0.625rem;
          background-color: #6c757d;
          border-color: #6c757d;
        }
        .permisos-modal-container .custom-switch .custom-control-label::after {
          width: calc(1.25rem - 4px);
          height: calc(1.25rem - 4px);
          border-radius: calc(0.625rem - 2px);
          background-color: white;
        }
        .permisos-modal-container .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
          background-color: #28a745;
          border-color: #28a745;
        }
        .permisos-modal-container .custom-switch .custom-control-input:checked ~ .custom-control-label::after {
          transform: translateX(1.25rem);
        }
        .permisos-modal-container .badge {
          font-size: 0.75rem;
          padding: 0.25rem 0.5rem;
        }
        .permisos-modal-container .custom-control-label span {
          font-size: 0.8rem;
          font-weight: 500;
        }
        .permisos-modal-container .btn-lg {
          padding: 0.75rem 1.5rem;
          font-size: 1rem;
        }
      `;
      document.head.appendChild(style);
    }
  });
}

/**
 * Función para desactivar un rol
 */
function desactivar(idRol) {
  Swal.fire({
    title: '¿Está seguro de desactivar este rol?',
    text: "¡No podrá revertir esto!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, desactivar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../../controllers/RolController.php?action=DesactivarRol",
        type: "POST",
        dataType: "json",
        data: {
          IdRol: idRol
        },
        success: function(response) {
          if (response && response.status) {
            Swal.fire(
              'Desactivado!',
              'El rol ha sido desactivado correctamente.',
              'success'
            ).then(() => {
              listarRoles(); // Recargar la tabla
            });
          } else {
            Swal.fire(
              'Error',
              'Hubo un problema al desactivar el rol: ' + (response ? response.message : 'Respuesta inválida del servidor'),
              'error'
            );
          }
        },
        error: function(xhr, status, error) {
          Swal.fire(
            'Error',
            'Error en la solicitud AJAX: ' + error,
            'error'
          );
        }
      });
    }
  });
}

/**
 * Función para activar un rol
 */
function activar(idRol) {
  Swal.fire({
    title: '¿Está seguro de activar este rol?',
    text: "El rol estará disponible para su uso",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, activar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../../controllers/RolController.php?action=ActivarRol",
        type: "POST",
        dataType: "json",
        data: {
          IdRol: idRol
        },
        success: function(response) {
          if (response && response.status) {
            Swal.fire(
              'Activado!',
              'El rol ha sido activado correctamente.',
              'success'
            ).then(() => {
              listarRoles(); // Recargar la tabla
            });
          } else {
            Swal.fire(
              'Error',
              'Hubo un problema al activar el rol: ' + (response ? response.message : 'Respuesta inválida del servidor'),
              'error'
            );
          }
        },
        error: function(xhr, status, error) {
          Swal.fire(
            'Error',
            'Error en la solicitud AJAX: ' + error,
            'error'
          );
        }
      });
    }
  });
}

/**
 * Función para cambiar el estado de un permiso específico
 */
function cambiarEstadoPermiso(idRol, codPermiso, nuevoEstado, index) {
  const estadoTexto = nuevoEstado ? 'activar' : 'desactivar';
  
  Swal.fire({
    title: 'Confirmar cambio',
    text: `¿Está seguro de ${estadoTexto} este permiso?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, confirmar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "../../controllers/RolController.php?action=CambiarEstadoPermiso",
        type: "POST",
        dataType: "json",
        data: {
          IdRol: idRol,
          IdPermiso: codPermiso, // Mantenemos el nombre del parámetro por compatibilidad
          NuevoEstado: nuevoEstado ? 1 : 0
        },
        success: function(response) {
          if (response && response.status) {
            // Actualizar la etiqueta del switch
            const labelElement = document.querySelector(`.switch-label-${index}`);
            if (labelElement) {
              labelElement.textContent = nuevoEstado ? 'Activo' : 'Inactivo';
            }
            
            // Actualizar el badge de estado en la tabla
            const estadoCell = document.querySelector(`.estado-cell-${index}`);
            if (estadoCell) {
              estadoCell.innerHTML = nuevoEstado ? 
                '<span class="badge badge-success">Activo</span>' : 
                '<span class="badge badge-danger">Inactivo</span>';
            }
            
            // Mostrar mensaje de éxito
            Swal.fire({
              icon: 'success',
              title: 'Permiso actualizado',
              text: `El permiso ha sido ${nuevoEstado ? 'activado' : 'desactivado'} correctamente`,
              timer: 1500,
              showConfirmButton: false,
              toast: true,
              position: 'top-end'
            });
          } else {
            // Revertir el switch si hay error
            const switchElement = document.querySelector(`#switch_${idRol}_${index}`);
            if (switchElement) {
              switchElement.checked = !nuevoEstado;
            }
            
            Swal.fire(
              'Error',
              'Hubo un problema al actualizar el permiso: ' + (response ? response.message : 'Respuesta inválida del servidor'),
              'error'
            );
          }
        },
        error: function(xhr, status, error) {
          // Revertir el switch si hay error
          const switchElement = document.querySelector(`#switch_${idRol}_${index}`);
          if (switchElement) {
            switchElement.checked = !nuevoEstado;
          }
          
          console.log("Error en AJAX:", xhr.responseText, status, error);
          Swal.fire(
            'Error',
            'Error en la solicitud AJAX: ' + error,
            'error'
          );
        }
      });
    } else {
      // Revertir el switch si se cancela
      const switchElement = document.querySelector(`#switch_${idRol}_${index}`);
      if (switchElement) {
        switchElement.checked = !nuevoEstado;
      }
    }
  });
}

/**
 * Función para cambiar el estado temporal de un permiso (sin guardar en BD)
 */
function cambiarEstadoTemporal(idRol, codPermiso, nuevoEstado, index) {
  // Almacenar el cambio temporalmente
  window.cambiosTemporales[codPermiso] = nuevoEstado ? 1 : 0;
  
  // Actualizar la etiqueta del switch
  const labelElement = document.querySelector(`.switch-label-${index}`);
  if (labelElement) {
    labelElement.textContent = nuevoEstado ? 'Activo' : 'Inactivo';
  }
  
  // Obtener el estado original del permiso
  const estadoOriginal = window.estadosOriginales ? window.estadosOriginales[codPermiso] : null;
  const nuevoEstadoNum = nuevoEstado ? 1 : 0;
  
  // Solo marcar como pendiente si realmente cambió
  if (estadoOriginal !== null && estadoOriginal !== nuevoEstadoNum) {
    const estadoCell = document.querySelector(`.estado-cell-${index}`);
    if (estadoCell) {
      estadoCell.innerHTML = nuevoEstado ? 
        '<span class="badge badge-warning">Activo (Pendiente)</span>' : 
        '<span class="badge badge-warning">Inactivo (Pendiente)</span>';
    }
  } else {
    // Si no cambió, mostrar estado normal
    const estadoCell = document.querySelector(`.estado-cell-${index}`);
    if (estadoCell) {
      estadoCell.innerHTML = nuevoEstado ? 
        '<span class="badge badge-success">Activo</span>' : 
        '<span class="badge badge-danger">Inactivo</span>';
    }
  }
}

/**
 * Función para guardar todos los cambios de permisos de una vez
 */
function guardarCambiosPermisos(idRol) {
  // Verificar si hay cambios para guardar (solo los que realmente cambiaron)
  const cambios = Object.keys(window.cambiosTemporales).filter(codPermiso => {
    const estadoOriginal = window.estadosOriginales[codPermiso];
    const estadoActual = window.cambiosTemporales[codPermiso];
    return estadoOriginal !== estadoActual;
  });
  
  if (cambios.length === 0) {
    Swal.fire('Información', 'No hay cambios para guardar', 'info');
    return;
  }
  
  // Mostrar confirmación antes de guardar
  Swal.fire({
    title: 'Confirmar cambios',
    text: `¿Está seguro de guardar ${cambios.length} cambio(s) en los permisos?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, guardar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      // Crear array de cambios para enviar al servidor (solo los que cambiaron)
      const cambiosParaGuardar = cambios.map(codPermiso => ({
        IdRol: idRol,
        CodPermiso: codPermiso,
        NuevoEstado: window.cambiosTemporales[codPermiso]
      }));
      
      // Enviar todos los cambios al servidor
      $.ajax({
        url: "../../controllers/RolController.php?action=GuardarCambiosPermisos",
        type: "POST",
        dataType: "json",
        data: {
          cambios: JSON.stringify(cambiosParaGuardar)
        },
        success: function(response) {
          if (response && response.status) {
            // Actualizar todos los badges a su estado final
            cambios.forEach(codPermiso => {
              const nuevoEstado = window.cambiosTemporales[codPermiso];
              // Buscar el índice del permiso para actualizar el badge
              const index = Object.keys(window.cambiosTemporales).indexOf(codPermiso);
              if (index !== -1) {
                const estadoCell = document.querySelector(`.estado-cell-${index}`);
                if (estadoCell) {
                  estadoCell.innerHTML = nuevoEstado ? 
                    '<span class="badge badge-success">Activo</span>' : 
                    '<span class="badge badge-danger">Inactivo</span>';
                }
              }
            });
            
            Swal.fire({
              icon: 'success',
              title: 'Cambios guardados',
              text: `Se han guardado ${cambios.length} cambio(s) correctamente`,
              timer: 2000,
              showConfirmButton: false
            });
            
            // Actualizar estados originales y limpiar cambios temporales
            cambios.forEach(codPermiso => {
              window.estadosOriginales[codPermiso] = window.cambiosTemporales[codPermiso];
            });
            window.cambiosTemporales = {};
          } else {
            Swal.fire(
              'Error',
              'Hubo un problema al guardar los cambios: ' + (response ? response.message : 'Respuesta inválida del servidor'),
              'error'
            );
          }
        },
        error: function(xhr, status, error) {
          console.log("Error en AJAX:", xhr.responseText, status, error);
          Swal.fire(
            'Error',
            'Error en la solicitud AJAX: ' + error,
            'error'
          );
        }
      });
    }
  });
}

/**
 * Función para revertir todos los cambios temporales
 */
function revertirCambiosPermisos(idRol) {
  Swal.fire({
    title: 'Confirmar reversión',
    text: '¿Está seguro de revertir todos los cambios? Se perderán las modificaciones no guardadas.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, revertir',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      // Recargar el modal con los datos originales
      verPermisos(idRol);
    }
  });
}
