<!-- //* IMPORTACIÓN PRINCIPAL DE CDN DE BOOTSTRAP -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->

<!-- <script
    type="text/javascript"
    src="https://cdn.jsdelivr.net/npm/mdb-ui-kit@9.0.0/js/mdb.umd.min.js"></script> -->
<!-- MDB -->
<!-- <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
<script
  type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.0.0/mdb.umd.min.js"
></script> -->
<!-- MDB -->
<!-- jQuery -->
<script src="../../../public/js/lib/jquery/jquery-3.7.1.min.js"></script>
<!-- jQuery UI -->
<script src="../../../public/plugins/jquery-ui/jquery-ui.min.js"></script>

<!-- Luego Select2 -->

<!-- Select2 -->
<script src="../../../public/plugins/select2/js/select2.full.min.js"></script>
<!-- <script src="../../../public/plugins/select2/js/select2.min.js"></script> -->
<!-- <script src="../../../public/plugins/select2/js/select2.full.js"></script> -->
<!-- <script src="../../../public/plugins/select2/js/select2.js"></script> -->
<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->
<link rel="stylesheet" href="../../../public/plugins/select2/css/select2.min.css">
<!-- <link rel="stylesheet" href="../../../public/plugins/select2/css/select2.css"> -->

<!-- Bootstrap 4 -->
<script src="../../../public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- bs-custom-file-input -->
<script src="../../../public/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- Select2 -->
<!-- <script src="/public/plugins/select2/dist/js/select2.min.js"></script> -->
<!-- ChartJS -->
<script src="../../../public/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="../../../public/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="../../../public/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../../../public/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="../../../public/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- MomentJS -->
<script src="../../../public/plugins/moment/moment.min.js"></script>
<script src="../../../public/plugins/moment/locales.min.js"></script>
<!-- daterangepicker -->
<script src="../../../public/plugins/daterangepicker/daterangepicker.js"></script>
<!-- fullCalendar 2.2.5 -->
<script src="../../../public/plugins/fullcalendar/main.js"></script>
<script src="../../../public/plugins/fullcalendar/locales/es.js"></script>
<!-- Tempusdominus Bootstrap 4 -->

<script src="../../../public/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../../../public/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../../../public/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../../../public/js/adminlte.js"></script>
<!-- SweetAlert2 -->
<!-- SweetAlert2 -->
<script src="../../../public/plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- xlsx.js -->
<script src="../../../public/plugins/xlsx/jszip.js"></script>
<script src="../../../public/plugins/xlsx/xlsx.js"></script>

<!-- DataTables -->
<script src="../../../public/plugins/datatables2/datatables.min.js"></script>
<!-- <script src="/public/plugins/datatables/datatables.min.js"></script>
    <script src="/public/plugins/datatables/Buttons-1.7.0/js/dataTables.buttons.min.js"></script> -->

<!-- Toastr -->
<script src="../../../public/plugins/toastr/toastr.min.js"></script>
<!-- Bootbox -->
<script src="../../../public/plugins/bootbox/bootbox.all.min.js"></script>
<!-- date-range-picker -->
<!-- Eliminar esta línea duplicada -->
<!-- <script src="/public/plugins/daterangepicker/daterangepicker.js"></script> -->
<!-- bootstrap color picker -->

<script src="../../../public/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Bootstrap Switch -->

<script src="../../../public/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- BS-Stepper -->
<script src="../../../public/plugins/bs-stepper/js/bs-stepper.min.js"></script>

<!-- Dropzone -->
<!-- <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script> -->
<script src="../../../public/plugins/dropzone/dropzone.js"></script>

<!-- FancyBox -->
<script src="../../../public/plugins/fancybox/dist/fancybox/fancybox.umd.js"></script>

<!-- bootstrap color picker -->
<script src="../../../public/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>

<!-- Notificaciones Push -->
<!-- <script src="/public/plugins/pushjs/bin/push.min.js"></script> -->

<!-- BOOTSTRAP 5
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
-->

<!-- <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script> -->
<script>
    function get_HoraDiaActual() {
        moment.locale('es', {
            months: 'Enero_Febrero_Marzo_Abril_Mayo_Junio_Julio_Agosto_Septiembre_Octubre_Noviembre_Diciembre'.split('_'),
            monthsShort: 'Enero._Feb._Mar_Abr._May_Jun_Jul._Ago_Sept._Oct._Nov._Dec.'.split('_'),
            weekdays: 'Domingo_Lunes_Martes_Miercoles_Jueves_Viernes_Sabado'.split('_'),
            weekdaysShort: 'Dom._Lun._Mar._Mier._Jue._Vier._Sab.'.split('_'),
            weekdaysMin: 'Do_Lu_Ma_Mi_Ju_Vi_Sa'.split('_')
        });
        const diahoraactual = moment().format('LLLL');
        return diahoraactual;
    }

    $(document).ready(function() {
        //getStateDarkMode()
        setInterval(() => {
            $("#diaactual").html(get_HoraDiaActual())
        }, 1000);
    });
</script>