/**
 * Módulo JavaScript para Gestión de Ambientes
 * Maneja todas las operaciones del frontend para la gestión de ambientes
 */

// Constantes de configuración
const CONFIGURACION = {
  URLS: {
    CONTROLADOR: "../../controllers/AmbienteController.php",
    IDIOMA_DATATABLES:
      "../../../public/plugins/datatables/json/Spanish.json",
  },
  VALORES_POR_DEFECTO: {
    ESTADO_ACTIVO: 1,
    ESTADO_INACTIVO: 0,
    LONGITUD_TABLA: 10,
  },
};

// Inicializar cuando el documento esté listo
$(document).ready(() => {
  inicializarModuloAmbientes();
});

/**
 * Inicializa todas las funcionalidades cuando se carga la página
 */
function inicializarModuloAmbientes() {
  inicializarTablaDeAmbientes();
  asignarEventos();
}

/**
 * Asigna todos los eventos del módulo
 */
function asignarEventos() {
  // Botón para nuevo ambiente
  $("#btnnuevo").on("click", manejarClickNuevoAmbiente);

  // Botón para guardar ambiente
  $("#btnGuardarAmbiente").on("click", manejarClickGuardarAmbiente);
}

/**
 * Maneja el evento click para crear un nuevo ambiente
 * Abre el modal y reinicia todos los campos del formulario
 */
function manejarClickNuevoAmbiente() {
  abrirModalAmbiente();
  limpiarCamposFormulario();
}

/**
 * Abre el modal de ambiente
 */
function abrirModalAmbiente() {
  $("#ModalAmbiente").modal("show");
}

/**
 * Reinicia todos los campos del formulario a sus valores por defecto
 */
function limpiarCamposFormulario() {
  const campos = [
    { id: "#nombre", valor: "" },
    { id: "#descripcion", valor: "" },
    { id: "#CodAmbiente", valor: "" },
    {
      id: "#estadoAmbiente",
      valor: CONFIGURACION.VALORES_POR_DEFECTO.ESTADO_ACTIVO.toString(),
    },
  ];

  campos.forEach((campo) => {
    $(campo.id).val(campo.valor);
  });

  // Reiniciar campos select2 si existen
  const camposSelect2 = ["#empresaModal", "#sucursalModal"];
  camposSelect2.forEach((campo) => {
    if ($(campo).length) {
      $(campo).val("").trigger("change");
    }
  });
}

/**
 * Maneja el evento click del botón guardar ambiente
 * Previene el envío por defecto del formulario y valida los datos
 */
function manejarClickGuardarAmbiente(event) {
  event.preventDefault();

  if (validarFormularioAmbiente()) {
    crearAmbiente();
  }
}

/**
 * Inicializa la tabla DataTable para listar ambientes
 * Configura la comunicación con el servidor, opciones de exportación y configuración de columnas
 */
function inicializarTablaDeAmbientes() {
  const configuracionTabla = {
    processing: true,
    serverSide: false,
    layout: {
      topStart: {
        buttons: [
          {
            extend: "excelHtml5",
            title: "Listado Ambientes",
            text: "<i class='fas fa-file-excel bg-success'></i> Exportar",
            className: "btn btn-success",
            autoFilter: true,
            sheetName: "Datos",
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5],
            },
          },
          {
            extend: "pdfHtml5",
            title: "Listado Ambientes",
            text: "<i class='fas fa-file-pdf bg-danger'></i> Exportar PDF",
            className: "btn btn-danger",
            autoFilter: true,
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5],
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
    destroy: true,
    info: true,
    displayLength: CONFIGURACION.VALORES_POR_DEFECTO.LONGITUD_TABLA,
    ajax: {
      url: `${CONFIGURACION.URLS.CONTROLADOR}?action=ListarAmbientes`,
      type: "POST",
      dataType: "json",
      data: obtenerDatosAjax,
      dataSrc: procesarRespuestaAjax,
      error: manejarErrorAjax,
    },
    language: {
      url: CONFIGURACION.URLS.IDIOMA_DATATABLES,
    },
    columns: obtenerColumnasTabla(),
  };

  $("#tblAmbientes").DataTable(configuracionTabla);
}

/**
 * Prepara los datos para las peticiones AJAX al servidor
 * @param {Object} d Objeto de datos de DataTables
 * @returns {Object} Datos formateados para la petición al servidor
 */
function obtenerDatosAjax(d) {
  return {
    cod_empresa: $("#cod_empresa").val() || null,
    cod_UnidadNeg: $("#cod_UnidadNeg").val() || null,
    idAmbiente: "",
    nombre: "",
    descripcion: "",
    estado: "",
  };
}

/**
 * Procesa la respuesta AJAX del servidor
 * @param {Object} json Respuesta del servidor
 * @returns {Array} Array de datos procesados
 */
function procesarRespuestaAjax(json) {
  console.log("Respuesta del servidor:", json);
  return Array.isArray(json) ? json : [];
}

/**
 * Maneja los errores AJAX de las peticiones al servidor
 * @param {Object} xhr Objeto XMLHttpRequest
 * @param {string} status Estado del error
 * @param {string} error Mensaje de error
 */
function manejarErrorAjax(xhr, status, error) {
  console.error("Error AJAX:", {
    respuesta: xhr.responseText,
    estado: status,
    error: error,
  });

  mostrarAlertaError("Listar Ambientes", `Error al cargar datos: ${error}`);
}

/**
 * Define la configuración de columnas para la tabla DataTable
 * @returns {Array} Array de configuración de columnas
 */
function obtenerColumnasTabla() {
  return [
    {
      data: null,
      title: "#",
      render: (data, type, row, meta) => meta.row + 1,
    },
    {
      data: "idAmbiente",
      title: "ID",
      visible: false,
      searchable: false,
    },
    {
      data: "nombre",
      title: "Nombre",
    },
    {
      data: "descripcion",
      title: "Descripción",
    },
    {
      data: "NombreSucursal",
      title: "Sucursal",
    },
    {
      data: "estado",
      title: "Estado",
      render: mostrarEtiquetaEstado,
    },
    {
      data: null,
      title: "Acciones",
      orderable: false,
      render: mostrarBotonesAccion,
    },
  ];
}

/**
 * Muestra la etiqueta de estado basada en el estado del ambiente
 * @param {number} data Valor de estado (1 = activo, 0 = inactivo)
 * @returns {string} HTML para la etiqueta de estado
 */
function mostrarEtiquetaEstado(data) {
  const esActivo = data == CONFIGURACION.VALORES_POR_DEFECTO.ESTADO_ACTIVO;
  const claseEtiqueta = esActivo ? "badge-success" : "badge-danger";
  const textoEstado = esActivo ? "Activo" : "Inactivo";

  return `<span class="badge ${claseEtiqueta}">${textoEstado}</span>`;
}

/**
 * Muestra los botones de acción para cada fila de la tabla
 * @param {Object} data Datos de la fila
 * @param {string} type Tipo de renderizado
 * @param {Object} row Datos completos de la fila
 * @returns {string} HTML para los botones de acción
 */
function mostrarBotonesAccion(data, type, row) {
  return `
    <button class="btn btn-sm btn-danger" onclick="desactivarAmbiente(${row.idAmbiente})" title="Desactivar ambiente">
      <i class="fas fa-ban"></i>
    </button>`;
}

/**
 * Función heredada para mantener compatibilidad
 * @deprecated Usar inicializarTablaDeAmbientes() en su lugar
 */
function ListarAmbientes() {
  $("#tblAmbientes").DataTable().destroy();
  inicializarTablaDeAmbientes();
}

/**
 * Valida los datos del formulario de ambiente
 * @returns {boolean} Verdadero si el formulario es válido, falso en caso contrario
 */
function validarFormularioAmbiente() {
  const nombre = $("#nombre").val()?.trim();

  if (!nombre) {
    mostrarAlertaAdvertencia(
      "Crear Ambiente",
      "Por favor, complete el campo obligatorio (Nombre)."
    );
    $("#nombre").focus();
    return false;
  }

  return true;
}

/**
 * Crea un nuevo ambiente con los datos ingresados en el formulario del modal
 */
function crearAmbiente() {
  const datosAmbiente = recopilarDatosAmbiente();

  if (!datosAmbiente) {
    return;
  }

  const configuracionAjax = {
    url: `${CONFIGURACION.URLS.CONTROLADOR}?action=RegistrarAmbiente`,
    type: "POST",
    dataType: "json",
    data: datosAmbiente,
    success: manejarExitoCreacion,
    error: manejarErrorCreacion,
  };

  $.ajax(configuracionAjax);
}

/**
 * Recopila los datos del ambiente desde el formulario
 * @returns {Object|null} Objeto de datos del ambiente o null si la validación falla
 */
function recopilarDatosAmbiente() {
  return {
    nombre: $("#nombre").val()?.trim(),
    descripcion: $("#descripcion").val()?.trim(),
    idEmpresa: $("#empresaModal").val() || null,
    idSucursal: $("#sucursalModal").val() || null,
    estado:
      $("#estadoAmbiente").val() ||
      CONFIGURACION.VALORES_POR_DEFECTO.ESTADO_ACTIVO,
    userMod: $("#userMod").val() || null,
    codigoAmbiente: $("#CodAmbiente").val()?.trim() || null,
  };
}

/**
 * Maneja la creación exitosa del ambiente
 * @param {Object} respuesta Respuesta del servidor
 */
function manejarExitoCreacion(respuesta) {
  console.log("Respuesta del servidor:", respuesta);

  if (respuesta && respuesta.status) {
    mostrarAlertaExito("Crear Ambiente", "Ambiente registrado con éxito.").then(
      () => {
        cerrarModalAmbiente();
        actualizarTablaAmbientes();
        actualizarCamposModalDesdeRespuesta(respuesta);
      }
    );
  } else {
    const mensajeError =
      respuesta?.message || "Respuesta inválida del servidor";
    mostrarAlertaError(
      "Crear Ambiente",
      `Error al registrar el ambiente: ${mensajeError}`
    );
  }
}

/**
 * Maneja los errores de creación de ambiente
 * @param {Object} xhr Objeto XMLHttpRequest
 * @param {string} status Estado del error
 * @param {string} error Mensaje de error
 */
function manejarErrorCreacion(xhr, status, error) {
  console.error("Error al crear ambiente:", { xhr, status, error });
  mostrarAlertaError(
    "Crear Ambiente",
    `Error al registrar el ambiente: ${error}`
  );
}

/**
 * Actualiza los campos del modal basado en la respuesta del servidor
 * @param {Object} respuesta Respuesta del servidor que contiene datos
 */
function actualizarCamposModalDesdeRespuesta(respuesta) {
  if (respuesta.data) {
    if (respuesta.data.idEmpresa) {
      $("#empresaModal").val(respuesta.data.idEmpresa).trigger("change");
    }
    if (respuesta.data.idSucursal) {
      $("#sucursalModal").val(respuesta.data.idSucursal).trigger("change");
    }
  }
}

/**
 * Cierra el modal de ambiente
 */
function cerrarModalAmbiente() {
  $("#ModalAmbiente").modal("hide");
}

/**
 * Actualiza los datos de la tabla de ambientes
 */
function actualizarTablaAmbientes() {
  const tabla = $("#tblAmbientes").DataTable();
  tabla.ajax.reload(null, false); // Mantener la página actual
}

/**
 * Desactiva un ambiente cambiando su estado a inactivo
 * @param {number} idAmbiente ID del ambiente a desactivar
 */
function desactivarAmbiente(idAmbiente) {
  if (!idAmbiente || idAmbiente <= 0) {
    mostrarAlertaError("Error", "ID de ambiente inválido");
    return;
  }

  const configuracionConfirmacion = {
    title: "¿Está seguro de desactivar este ambiente?",
    text: "¡No podrá revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, desactivar",
    cancelButtonText: "Cancelar",
  };

  Swal.fire(configuracionConfirmacion).then((resultado) => {
    if (resultado.isConfirmed) {
      ejecutarDesactivacion(idAmbiente);
    }
  });
}

/**
 * Ejecuta la petición real de desactivación
 * @param {number} idAmbiente ID del ambiente a desactivar
 */
function ejecutarDesactivacion(idAmbiente) {
  const datosDesactivacion = {
    IdAmbiente: idAmbiente,
    userMod: $("#userMod").val() || null,
  };

  const configuracionAjax = {
    url: `${CONFIGURACION.URLS.CONTROLADOR}?action=Desactivar`,
    type: "POST",
    dataType: "json",
    data: datosDesactivacion,
    success: manejarExitoDesactivacion,
    error: manejarErrorDesactivacion,
  };

  $.ajax(configuracionAjax);
}

/**
 * Maneja la desactivación exitosa del ambiente
 * @param {Object} respuesta Respuesta del servidor
 */
function manejarExitoDesactivacion(respuesta) {
  if (respuesta && respuesta.status) {
    mostrarAlertaExito(
      "¡Desactivado!",
      "El ambiente ha sido desactivado."
    ).then(() => {
      actualizarTablaAmbientes();
    });
  } else {
    const mensajeError =
      respuesta?.message || "Respuesta inválida del servidor";
    mostrarAlertaError(
      "Error",
      `Hubo un problema al desactivar el ambiente: ${mensajeError}`
    );
  }
}

/**
 * Maneja los errores de desactivación de ambiente
 * @param {Object} xhr Objeto XMLHttpRequest
 * @param {string} status Estado del error
 * @param {string} error Mensaje de error
 */
function manejarErrorDesactivacion(xhr, status, error) {
  console.error("Error al desactivar ambiente:", { xhr, status, error });
  mostrarAlertaError("Error", `Error en la solicitud AJAX: ${error}`);
}

// Funciones auxiliares de alertas para mensajes consistentes en la interfaz
/**
 * Muestra una alerta de éxito
 * @param {string} titulo Título de la alerta
 * @param {string} mensaje Mensaje de la alerta
 * @returns {Promise} Promesa de SweetAlert2
 */
function mostrarAlertaExito(titulo, mensaje) {
  return Swal.fire(titulo, mensaje, "success");
}

/**
 * Muestra una alerta de error
 * @param {string} titulo Título de la alerta
 * @param {string} mensaje Mensaje de la alerta
 * @returns {Promise} Promesa de SweetAlert2
 */
function mostrarAlertaError(titulo, mensaje) {
  return Swal.fire(titulo, mensaje, "error");
}

/**
 * Muestra una alerta de advertencia
 * @param {string} titulo Título de la alerta
 * @param {string} mensaje Mensaje de la alerta
 * @returns {Promise} Promesa de SweetAlert2
 */
function mostrarAlertaAdvertencia(titulo, mensaje) {
  return Swal.fire(titulo, mensaje, "warning");
}

// Funciones heredadas para mantener compatibilidad
/**
 * Función heredada para mantener compatibilidad
 * @deprecated Usar crearAmbiente() en su lugar
 */
function CrearAmbientes() {
  if (validarFormularioAmbiente()) {
    crearAmbiente();
  }
}
