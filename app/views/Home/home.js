/**
 * Módulo JavaScript para el Dashboard del Sistema
 * Maneja todas las operaciones del frontend para la página principal
 */

// Constantes de configuración para el módulo de dashboard
const CONFIGURACION_DASHBOARD = {
  URLS: {
    CONTROLADOR_DASHBOARD: "../../controllers/DashboardController.php",
    ANIMACIONES_CSS: {
      EFECTO_APARICION: "fadeInUp 0.6s ease-out",
      EFECTO_CONTADOR: "contadorAnimado 1s ease-out",
      TRANSICION_SMOOTH: "all 0.3s ease"
    }
  },
  SELECTORES: {
    CONTADORES: {
      OPERATIVOS: "#lblcantidadoperativos",
      MANTENIMIENTO: "#lblcantidadactivosmantenimiento",
      BAJA: "#lblcantidadactivosbaja",
      TOTAL: "#lblcantidadactivos",
      VALORIZACION: "#lblvalordeactivos"
    },
    GRAFICOS: {
      ACTIVOS_ASIGNADOS: "#graficoActivosAsignados"
    }
  },
  ESTADOS: {
    OPERATIVA: "Operativa",
    REPARACION: "Reparación",
    BAJA: "Baja",
    TOTAL: "Total",
    VALORIZACION: "Valorizacion"
  },
  ANIMACIONES: {
    DELAY_INICIO: 100,
    INTERVALO_CONTADORES: 50
  }
};

/**
 * Inicializa el módulo del dashboard cuando el documento está listo
 */
$(document).ready(function () {
  inicializarModuloDashboard();
});

/**
 * Función principal de inicialización del módulo de dashboard
 * Configura todos los componentes necesarios al cargar la página
 */
function inicializarModuloDashboard() {
  console.log("Inicializando módulo de dashboard...");

  // Inicializar componentes principales
  cargarDatosDashboard();
  inicializarGraficoActivosAsignados();

  // Configurar efectos visuales
  configurarEfectosVisuales();

  console.log("Módulo de dashboard inicializado correctamente");
}

/**
 * Carga los datos principales del dashboard desde el servidor
 */
function cargarDatosDashboard() {
  console.log("Cargando datos del dashboard...");

  $.ajax({
    url: `${CONFIGURACION_DASHBOARD.URLS.CONTROLADOR_DASHBOARD}?action=ConteoDashboard`,
    type: "POST",
    async: false,
    success: function (respuesta) {
      try {
        const datos = typeof respuesta === "string" ? JSON.parse(respuesta) : respuesta;

        if (Array.isArray(datos)) {
          procesarDatosDashboard(datos);
          console.log("Datos del dashboard procesados correctamente");
        } else {
          console.error("La respuesta no es un array válido:", datos);
          mostrarErrorDashboard("Error en el formato de datos del servidor");
        }
      } catch (error) {
        console.error("Error al procesar datos del dashboard:", error);
        mostrarErrorDashboard("Error al procesar los datos del dashboard");
      }
    },
    error: function (xhr, estado, error) {
      console.error("Error en petición AJAX del dashboard:", {
        xhr: xhr,
        estado: estado,
        error: error
      });
      mostrarErrorDashboard("Error de conexión con el servidor");
    }
  });
}

/**
 * Procesa los datos del dashboard y actualiza los contadores
 * @param {Array} datos - Array de datos del dashboard
 */
function procesarDatosDashboard(datos) {
  console.log("Procesando datos del dashboard:", datos);

  datos.forEach((elemento, indice) => {
    const estado = elemento["Estado"];
    const cantidad = parseInt(elemento["Cantidad"] || 0);
    const valor = parseFloat(elemento["Valor"] || 0);

    switch (estado) {
      case CONFIGURACION_DASHBOARD.ESTADOS.OPERATIVA:
        actualizarContador(CONFIGURACION_DASHBOARD.SELECTORES.CONTADORES.OPERATIVOS, cantidad);
        break;

      case CONFIGURACION_DASHBOARD.ESTADOS.REPARACION:
        actualizarContador(CONFIGURACION_DASHBOARD.SELECTORES.CONTADORES.MANTENIMIENTO, cantidad);
        break;

      case CONFIGURACION_DASHBOARD.ESTADOS.BAJA:
        actualizarContador(CONFIGURACION_DASHBOARD.SELECTORES.CONTADORES.BAJA, cantidad);
        break;

      case CONFIGURACION_DASHBOARD.ESTADOS.TOTAL:
        actualizarContador(CONFIGURACION_DASHBOARD.SELECTORES.CONTADORES.TOTAL, cantidad);
        break;

      case CONFIGURACION_DASHBOARD.ESTADOS.VALORIZACION:
        actualizarValorActivos(valor);
        break;

      default:
        console.warn(`Estado no reconocido: ${estado}`);
    }
  });
}

/**
 * Actualiza un contador específico con animación
 * @param {string} selector - Selector del elemento a actualizar
 * @param {number} valor - Nuevo valor del contador
 */
function actualizarContador(selector, valor) {
  const elemento = $(selector);
  if (elemento.length) {
    // Animar el contador
    animarContador(elemento, valor);
    console.log(`Contador ${selector} actualizado a: ${valor}`);
  } else {
    console.warn(`Elemento no encontrado: ${selector}`);
  }
}

/**
 * Actualiza el valor de los activos con formato de moneda
 * @param {number} valor - Valor total de los activos
 */
function actualizarValorActivos(valor) {
  const elemento = $(CONFIGURACION_DASHBOARD.SELECTORES.CONTADORES.VALORIZACION);
  if (elemento.length) {
    const valorFormateado = formatearMoneda(valor);
    elemento.html(valorFormateado);
    console.log(`Valor de activos actualizado: ${valorFormateado}`);
  }
}

/**
 * Formatea un número como moneda peruana
 * @param {number} valor - Valor a formatear
 * @returns {string} Valor formateado como moneda
 */
function formatearMoneda(valor) {
  return "S/. " + valor.toLocaleString("es-PE", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

/**
 * Muestra un mensaje de error en el dashboard
 * @param {string} mensaje - Mensaje de error a mostrar
 */
function mostrarErrorDashboard(mensaje) {
  console.error(`Error en dashboard: ${mensaje}`);

  // Mostrar error en los contadores principales
  const contadores = [
    CONFIGURACION_DASHBOARD.SELECTORES.CONTADORES.TOTAL,
    CONFIGURACION_DASHBOARD.SELECTORES.CONTADORES.OPERATIVOS,
    CONFIGURACION_DASHBOARD.SELECTORES.CONTADORES.MANTENIMIENTO,
    CONFIGURACION_DASHBOARD.SELECTORES.CONTADORES.BAJA
  ];

  contadores.forEach(selector => {
    $(selector).html('<span class="text-danger">Error</span>');
  });

  $(CONFIGURACION_DASHBOARD.SELECTORES.CONTADORES.VALORIZACION)
    .html('<span class="text-danger">Error</span>');
}

/**
 * Inicializa el gráfico de activos asignados vs no asignados
 */
function inicializarGraficoActivosAsignados() {
  console.log("Inicializando gráfico de activos asignados...");

  $.ajax({
    url: `${CONFIGURACION_DASHBOARD.URLS.CONTROLADOR_DASHBOARD}?action=TotalActivosAsignados`,
    type: "POST",
    success: function (respuesta) {
      procesarDatosActivosAsignados(respuesta);
    },
    error: function (error) {
      console.error("Error al obtener activos asignados:", error);
      procesarDatosActivosAsignados(null);
    }
  });
}

/**
 * Procesa los datos de activos asignados y obtiene los no asignados
 * @param {Object|string} respuesta - Respuesta del servidor
 */
function procesarDatosActivosAsignados(respuesta) {
  console.log("Procesando datos de activos asignados:", respuesta);

  let datosAsignados;
  try {
    datosAsignados = typeof respuesta === "string" ? JSON.parse(respuesta) : respuesta;
  } catch (error) {
    console.error("Error al parsear datos de asignados:", error);
    datosAsignados = { cantidad: 0 };
  }

  // Obtener datos de no asignados
  $.ajax({
    url: `${CONFIGURACION_DASHBOARD.URLS.CONTROLADOR_DASHBOARD}?action=TotalActivosNoAsignados`,
    type: "POST",
    success: function (respuesta) {
      procesarDatosActivosNoAsignados(respuesta, datosAsignados);
    },
    error: function (error) {
      console.error("Error al obtener activos no asignados:", error);
      procesarDatosActivosNoAsignados(null, datosAsignados);
    }
  });
}

/**
 * Procesa los datos de activos no asignados y crea el gráfico
 * @param {Object|string} respuesta - Respuesta del servidor
 * @param {Object} datosAsignados - Datos de activos asignados
 */
function procesarDatosActivosNoAsignados(respuesta, datosAsignados) {
  console.log("Procesando datos de activos no asignados:", respuesta);

  let datosNoAsignados;
  try {
    datosNoAsignados = typeof respuesta === "string" ? JSON.parse(respuesta) : respuesta;
  } catch (error) {
    console.error("Error al parsear datos de no asignados:", error);
    datosNoAsignados = { cantidad: 0 };
  }

  const cantidadAsignados = datosAsignados?.cantidad || 0;
  const cantidadNoAsignados = datosNoAsignados?.cantidad || 0;

  console.log("Datos finales para gráfico:", {
    asignados: cantidadAsignados,
    noAsignados: cantidadNoAsignados
  });

  crearGraficoDistribucion(cantidadAsignados, cantidadNoAsignados);
}

/**
 * Crea el gráfico de distribución de activos asignados vs no asignados
 * @param {number} cantidadAsignados - Número de activos asignados
 * @param {number} cantidadNoAsignados - Número de activos no asignados
 */
function crearGraficoDistribucion(cantidadAsignados, cantidadNoAsignados) {
  const elementoCanvas = document.getElementById("graficoActivosAsignados");

  if (!elementoCanvas) {
    console.error("No se encontró el elemento canvas para el gráfico");
    return;
  }

  const contexto = elementoCanvas.getContext("2d");

  // Destruir gráfico existente si lo hay
  if (window.graficoActivos) {
    window.graficoActivos.destroy();
  }

  // Crear nuevo gráfico
  window.graficoActivos = new Chart(contexto, {
    type: "doughnut",
    data: {
      labels: ["Activos Asignados", "Activos No Asignados"],
      datasets: [{
        data: [cantidadAsignados, cantidadNoAsignados],
        backgroundColor: [
          "#28a745", // Verde para asignados
          "#dc3545", // Rojo para no asignados
        ],
        borderColor: ["#ffffff", "#ffffff"],
        borderWidth: 2,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            font: {
              size: 14,
              family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
            },
            padding: 20
          }
        },
        tooltip: {
          callbacks: {
            label: function (contexto) {
              const etiqueta = contexto.label || "";
              const valor = contexto.raw || 0;
              const total = contexto.dataset.data.reduce((acumulador, valorActual) => acumulador + valorActual, 0);
              const porcentaje = total > 0 ? Math.round((valor / total) * 100) : 0;
              return `${etiqueta}: ${valor} (${porcentaje}%)`;
            }
          },
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          titleColor: '#fff',
          bodyColor: '#fff',
          borderColor: '#fff',
          borderWidth: 1
        }
      },
      animation: {
        duration: 1500,
        easing: 'easeInOutQuart'
      }
    }
  });

  console.log("Gráfico de distribución creado correctamente");
}

/**
 * Configura efectos visuales para el dashboard
 */
function configurarEfectosVisuales() {
  // Agregar estilos CSS para animaciones
  if (!$('#estilos-dashboard').length) {
    const estilosDashboard = `
      <style id="estilos-dashboard">
        .contador-dashboard {
          transition: ${CONFIGURACION_DASHBOARD.URLS.ANIMACIONES_CSS.TRANSICION_SMOOTH};
          font-weight: bold;
          text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .card-dashboard {
          transition: ${CONFIGURACION_DASHBOARD.URLS.ANIMACIONES_CSS.TRANSICION_SMOOTH};
          border-radius: 10px;
          box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .card-dashboard:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        @keyframes contadorAnimado {
          0% { transform: scale(0.8); opacity: 0.5; }
          50% { transform: scale(1.1); opacity: 1; }
          100% { transform: scale(1); opacity: 1; }
        }

        .contador-animado {
          animation: ${CONFIGURACION_DASHBOARD.URLS.ANIMACIONES_CSS.EFECTO_CONTADOR};
        }

        @keyframes fadeInUp {
          from {
            opacity: 0;
            transform: translateY(20px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        .fade-in-up {
          animation: ${CONFIGURACION_DASHBOARD.URLS.ANIMACIONES_CSS.EFECTO_APARICION};
        }
      </style>
    `;

    $("head").append(estilosDashboard);
  }

  // Aplicar clases de animación a los contadores
  setTimeout(() => {
    Object.values(CONFIGURACION_DASHBOARD.SELECTORES.CONTADORES).forEach(selector => {
      $(selector).addClass('contador-dashboard');
    });
  }, CONFIGURACION_DASHBOARD.ANIMACIONES.DELAY_INICIO);
}

/**
 * Anima un contador numérico
 * @param {jQuery} elemento - Elemento jQuery a animar
 * @param {number} valorFinal - Valor final del contador
 */
function animarContador(elemento, valorFinal) {
  const valorActual = parseInt(elemento.text()) || 0;
  const diferencia = valorFinal - valorActual;
  const pasos = 20;
  const incremento = diferencia / pasos;
  let contador = 0;

  elemento.addClass('contador-animado');

  const timer = setInterval(() => {
    contador++;
    const valorActual = Math.round(contador * incremento);
    elemento.text(valorActual);

    if (contador >= pasos) {
      clearInterval(timer);
      elemento.text(valorFinal);
      setTimeout(() => {
        elemento.removeClass('contador-animado');
      }, 1000);
    }
  }, CONFIGURACION_DASHBOARD.ANIMACIONES.INTERVALO_CONTADORES);
}

/**
 * Función heredada para mantener compatibilidad
 * @deprecated Usar inicializarModuloDashboard() en su lugar
 */
function init() {
  inicializarModuloDashboard();
}

/**
 * Función heredada para mantener compatibilidad
 * @deprecated Usar cargarDatosDashboard() en su lugar
 */
function Dashboard() {
  cargarDatosDashboard();
}

/**
 * Función heredada para mantener compatibilidad
 * @deprecated Usar inicializarGraficoActivosAsignados() en su lugar
 */
function cargarGraficoActivosAsignados() {
  inicializarGraficoActivosAsignados();
}

function cargarGraficoActivosAsignados() {
  // Obtener datos de activos asignados
  $.ajax({
    url: "../../controllers/DashboardController.php?action=TotalActivosAsignados",
    type: "POST",
    success: function (response) {
      console.log("Respuesta de activos asignados:", response);
      let dataAsignados;
      try {
        // Intentar parsear la respuesta si viene como string
        dataAsignados =
          typeof response === "string" ? JSON.parse(response) : response;
        console.log("Datos de activos asignados procesados:", dataAsignados);
      } catch (e) {
        console.error("Error al parsear datos de activos asignados:", e);
        dataAsignados = { cantidad: 0 };
      }

      // Obtener datos de activos no asignados
      $.ajax({
        url: "../../controllers/DashboardController.php?action=TotalActivosNoAsignados",
        type: "POST",
        success: function (response) {
          console.log("Respuesta de activos no asignados:", response);
          let dataNoAsignados;
          try {
            // Intentar parsear la respuesta si viene como string
            dataNoAsignados =
              typeof response === "string" ? JSON.parse(response) : response;
            console.log(
              "Datos de activos no asignados procesados:",
              dataNoAsignados
            );
          } catch (e) {
            console.error("Error al parsear datos de activos no asignados:", e);
            dataNoAsignados = { cantidad: 0 };
          }

          // Procesar datos para el gráfico
          const cantidadAsignados =
            dataAsignados && dataAsignados.cantidad !== undefined
              ? dataAsignados.cantidad
              : 0;
          const cantidadNoAsignados =
            dataNoAsignados && dataNoAsignados.cantidad !== undefined
              ? dataNoAsignados.cantidad
              : 0;

          console.log("Cantidad de activos asignados:", cantidadAsignados);
          console.log("Cantidad de activos no asignados:", cantidadNoAsignados);

          // Crear el gráfico
          crearGraficoDistribucion(cantidadAsignados, cantidadNoAsignados);
        },
        error: function (error) {
          console.error("Error al obtener activos no asignados:", error);
        },
      });
    },
    error: function (error) {
      console.error("Error al obtener activos asignados:", error);
    },
  });
}

function crearGraficoDistribucion(cantidadAsignados, cantidadNoAsignados) {
  // Obtener el contexto del canvas
  const ctx = document
    .getElementById("graficoActivosAsignados")
    .getContext("2d");

  // Destruir el gráfico si ya existe
  if (window.graficoActivos) {
    window.graficoActivos.destroy();
  }

  // Crear nuevo gráfico
  window.graficoActivos = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: ["Activos Asignados", "Activos No Asignados"],
      datasets: [
        {
          data: [cantidadAsignados, cantidadNoAsignados],
          backgroundColor: [
            "#28a745", // Verde para asignados
            "#dc3545", // Rojo para no asignados
          ],
          borderColor: ["#ffffff", "#ffffff"],
          borderWidth: 2,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            font: {
              size: 14,
            },
          },
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const label = context.label || "";
              const value = context.raw || 0;
              const total = context.dataset.data.reduce(
                (acc, val) => acc + val,
                0
              );
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            },
          },
        },
      },
    },
  });
}
