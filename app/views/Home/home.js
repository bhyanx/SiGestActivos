/**
 * Dashboard - Funciones principales
 */

// URLs del controlador
const URL_DASHBOARD = "../../controllers/DashboardController.php";

// Selectores de elementos
const SELECTORES = {
  CONTADORES: {
    OPERATIVOS: "#lblcantidadoperativos",
    MANTENIMIENTO: "#lblcantidadactivosmantenimiento",
    BAJA: "#lblcantidadactivosbaja",
    TOTAL: "#lblcantidadactivos",
    VALORIZACION: "#lblvalordeactivos"
  },
  GRAFICO: "#graficoActivosAsignados"
};

// Estados de activos
const ESTADOS = {
  OPERATIVA: "Operativa",
  REPARACION: "Reparación",
  BAJA: "Baja",
  TOTAL: "Total",
  VALORIZACION: "Valorizacion"
};

/**
 * Inicializa el dashboard al cargar la página
 */
$(document).ready(function () {
  cargarDatosDashboard();
  cargarGraficoActivosAsignados();
  configurarEfectosVisuales();
});

/**
 * Carga los datos del dashboard
 */
function cargarDatosDashboard() {
  $.ajax({
    url: URL_DASHBOARD + "?action=ConteoDashboard",
    type: "POST",
    success: function (respuesta) {
      try {
        const datos = typeof respuesta === "string" ? JSON.parse(respuesta) : respuesta;
        if (Array.isArray(datos)) {
          procesarDatosDashboard(datos);
        } else {
          mostrarErrorDashboard();
        }
      } catch (error) {
        mostrarErrorDashboard();
      }
    },
    error: function () {
      mostrarErrorDashboard();
    }
  });
}

/**
 * Procesa los datos del dashboard
 */
function procesarDatosDashboard(datos) {
  datos.forEach(function(item) {
    const estado = item.Estado;
    const cantidad = parseInt(item.Cantidad || 0);
    const valor = parseFloat(item.Valor || 0);

    if (estado === ESTADOS.OPERATIVA) {
      actualizarContador(SELECTORES.CONTADORES.OPERATIVOS, cantidad);
    } else if (estado === ESTADOS.REPARACION) {
      actualizarContador(SELECTORES.CONTADORES.MANTENIMIENTO, cantidad);
    } else if (estado === ESTADOS.BAJA) {
      actualizarContador(SELECTORES.CONTADORES.BAJA, cantidad);
    } else if (estado === ESTADOS.TOTAL) {
      actualizarContador(SELECTORES.CONTADORES.TOTAL, cantidad);
    } else if (estado === ESTADOS.VALORIZACION) {
      actualizarValorActivos(valor);
    }
  });
}

/**
 * Actualiza un contador con animación
 */
function actualizarContador(selector, valor) {
  const elemento = $(selector);
  if (elemento.length) {
    animarContador(elemento, valor);
  }
}

/**
 * Actualiza el valor de los activos
 */
function actualizarValorActivos(valor) {
  const valorFormateado = "S/. " + valor.toLocaleString("es-PE", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
  $(SELECTORES.CONTADORES.VALORIZACION).text(valorFormateado);
}

/**
 * Muestra error en el dashboard
 */
function mostrarErrorDashboard() {
  const contadores = [
    SELECTORES.CONTADORES.TOTAL,
    SELECTORES.CONTADORES.OPERATIVOS,
    SELECTORES.CONTADORES.MANTENIMIENTO,
    SELECTORES.CONTADORES.BAJA,
    SELECTORES.CONTADORES.VALORIZACION
  ];

  contadores.forEach(function(selector) {
    $(selector).html('<span class="text-danger">Error</span>');
  });
}

/**
 * Carga el gráfico de activos asignados
 */
function cargarGraficoActivosAsignados() {
  $.ajax({
    url: URL_DASHBOARD + "?action=TotalActivosAsignados",
    type: "POST",
    success: function (respuesta) {
      const asignados = parsearRespuesta(respuesta);
      cargarDatosNoAsignados(asignados);
    },
    error: function () {
      crearGraficoDistribucion(0, 0);
    }
  });
}

/**
 * Carga datos de activos no asignados
 */
function cargarDatosNoAsignados(asignados) {
  $.ajax({
    url: URL_DASHBOARD + "?action=TotalActivosNoAsignados",
    type: "POST",
    success: function (respuesta) {
      const noAsignados = parsearRespuesta(respuesta);
      crearGraficoDistribucion(asignados, noAsignados);
    },
    error: function () {
      crearGraficoDistribucion(asignados, 0);
    }
  });
}

/**
 * Parsea respuesta JSON
 */
function parsearRespuesta(respuesta) {
  try {
    const data = typeof respuesta === "string" ? JSON.parse(respuesta) : respuesta;
    return data && data.cantidad !== undefined ? data.cantidad : 0;
  } catch (error) {
    return 0;
  }
}

/**
 * Configura efectos visuales para el dashboard
 */
function configurarEfectosVisuales() {
  // Aplicar efectos visuales después de un pequeño delay
  setTimeout(() => {
    // Los contadores ya tienen la clase contador-dashboard aplicada en el HTML
    // El gráfico ya tiene la clase fade-in-up aplicada en el HTML
  }, 100);
}

/**
 * Anima un contador numérico
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
  }, 50);
}

/**
 * Crea el gráfico de distribución
 */
function crearGraficoDistribucion(asignados, noAsignados) {
  const ctx = document.getElementById("graficoActivosAsignados");
  if (!ctx) return;

  // Destruir gráfico anterior
  if (window.graficoActivos) {
    window.graficoActivos.destroy();
  }

  window.graficoActivos = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: ["Asignados", "No Asignados"],
      datasets: [{
        data: [asignados, noAsignados],
        backgroundColor: ["#28a745", "#dc3545"],
        borderColor: ["#ffffff", "#ffffff"]
        //borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: "bottom" },
        tooltip: {
          callbacks: {
            label: function(context) {
              const valor = context.raw;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const porcentaje = total > 0 ? Math.round((valor / total) * 100) : 0;
              return `${context.label}: ${valor} (${porcentaje}%)`;
            }
          }
        }
      }
    }
  });
}

