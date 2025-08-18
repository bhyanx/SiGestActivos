$(document).ready(() => {
    init();
});
  
  /**
   * Inicializa las funcionalidades de la página al cargar.
   */
function init() {
  ListarCategorias();
}
  
  /**
   * Abre el modal para crear un nuevo Categorias y limpia los campos del formulario.
   */
  $("#btnnuevo").click(() => {
    $("#ModalCategorias").modal("show");
    // Limpiar campos del formulario
    $("#nombre").val("");
    $("#descripcion").val("");
    $("#vidaUtilEstandar").val("");
    $("#estado").val("1");
    $("#codigoClase").val("");
  });
  
  /**
   * Vincula el evento de guardar al botón correspondiente en el modal.
   * Previene el comportamiento por defecto para evitar recarga de la página.
   */
  $("#btnGuardarCategoria").click((event) => {
    event.preventDefault();
    CrearCategorias();
  });
  
  /**
   * Lista los Categoriass en una tabla DataTable con datos obtenidos del servidor.
   */
  function ListarCategorias() {
    $("#tblCategorias").DataTable({
      layout: {
        topStart: {
          buttons: [
            {
              extend: "excelHtml5",
              title: "Listado de Categorias",
              text: "<i class='fas fa-file-excel'></i> Exportar",
              autoFilter: true,
              sheetName: "Data",
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5], // sin columna de acciones
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
      autoWidth: true,
      ajax: {
        url: "../../controllers/CategoriaActivoContoller.php?action=ListarCategorias",
        type: "POST",
        dataType: "json",
        data: {
          IdCategorias: "",
          Nombre: "",
          Descripción: "",
          VidaUtilEstadar: "",
          Estado: "",
          CodigoClase: "",
        },
        dataSrc: function (json) {
          console.log("Consultar response:", json);
          return json || [];
        },
        error: function (xhr, status, error) {
          console.log("Error en AJAX:", xhr.responseText, status, error);
          Swal.fire("Listar Categorias", "Error al cargar datos: " + error, "error");
        },
      },
      bDestroy: true,
      responsive: true,
      bInfo: true,
      iDisplayLength: 10,
      autoWidth: false,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
      },
  
      columns: [
        {
          data: null,
          render: function (data, type, row, meta) {
            return meta.row + 1; // columna #
          },
        },
        { data: "idCategoria", visible: false, searchable: false },
        { data: "nombre" },
        { data: "descripcion" },
        { data: "vidaUtilEstandar" },
        {
          data: "estado",
          render: function (data, type, row) {
            return data == 1
              ? '<span class="badge badge-success">Activo</span>'
              : '<span class="badge badge-danger">Inactivo</span>';
          },
        },
        { data: "codigoClase" },
        { 
            data: null,
            orderable: false,
            render: function (data, type, row) {
                return `
                <button class="btn btn-sm btn-danger"
                onclick="desactivarCategoria(${row.idCategoria})">
                    <i class="fas fa-ban"></i>
                </button>`;
            },
        },
      ],
    });
  }
  
  /**
   * Registra un nuevo Categorias con los datos ingresados en el formulario del modal.
   */
  function CrearCategorias() {
    let nombre = $("#nombre").val();
    let descripcion = $("#descripcion").val();
    let vidaUtilEstandar = $("#vidaUtilEstandar").val();
    let estado = $("#estado").val() || 1;
    let codigoClase = $("#codigoClase").val();
  
    if (!nombre) {
      Swal.fire(
        "Crear Categorias",
        "Por favor, complete el campo obligatorio (Nombre).",
        "warning"
      );
      return;
    }
  
    $.ajax({
      url: "../../controllers/CategoriaActivoContoller.php?action=RegistrarCategoria",
      type: "POST",
      dataType: "json",
      data: {
        nombre: nombre,
        descripcion: descripcion,
        vidaUtilEstandar: vidaUtilEstandar,
        estado: estado,
        codigoClase: codigoClase,
      },
      success: (res) => {
        console.log("Respuesta del servidor:", res);
        if (res && res.status) {
          Swal.fire(
            "Crear Categorias",
            "Categorias registrado con éxito.",
            "success"
          ).then(() => {
            $("#ModalCategorias").modal("hide");
            ListarCategorias();
          });
        } else {
          Swal.fire(
            "Crear Categorias",
            "Error al registrar el Categorias: " + (res ? res.message : "Respuesta inválida del servidor"),
            "error"
          );
        }
      },
      error: (xhr, status, error) => {
        Swal.fire(
          "Crear Categorias",
          "Error al registrar el Categorias: " + error,
          "error"
        );
      },
    });
  }
  
  /**
   * Desactiva un Categorias cambiando su estado a 0.
   */
  function desactivarCategoria(idCategoria) {
    Swal.fire({
      title: '¿Está seguro de desactivar este Categorias?',
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
          url: "../../controllers/CategoriaActivoContoller.php?action=DesactivarCategoria",
          type: "POST",
          dataType: "json",
          data: {
            IdCategoria: idCategoria
          },
          success: (res) => {
            if (res && res.status) {
              Swal.fire(
                'Desactivado!',
                'El Categorias ha sido desactivado.',
                'success'
              ).then(() => {
                ListarCategorias();
              });
            } else {
              Swal.fire(
                'Error',
                'Hubo un problema al desactivar el Categorias: ' + (res ? res.message : 'Respuesta inválida del servidor'),
                'error'
              );
            }
          },
          error: (xhr, status, error) => {
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
   * Función para editar una categoría (placeholder para futura implementación)
   */
  function editarCategoria(idCategoria) {
    // TODO: Implementar funcionalidad de edición
    Swal.fire('Información', 'Función de edición en desarrollo', 'info');
  }
  