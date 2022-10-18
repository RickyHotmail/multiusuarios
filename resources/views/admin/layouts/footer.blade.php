    <!-- jQuery -->
    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
    $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- ChartJS  revisar
    <script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script>-->
    <!-- Sparkline revisar
    <script src="{{ asset('admin/plugins/sparklines/sparkline.js') }}"></script>-->
    <!-- JQVMap  revisar
    <script src="{{ asset('admin/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>-->
    <!-- Select2 -->
    <script src="{{ asset('admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="{{ asset('admin/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('admin/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/dropzone/min/dropzone.min.js') }}"></script>
    
    <!-- daterangepicker -->
    <script src="{{ asset('admin/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('admin/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- bs-custom-file-input -->
    <script src="{{ asset('admin/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <!-- Bootstrap Switch -->
    <script src="{{ asset('admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <!-- NEOPAGUPA -->
    <script  src="{{ asset('admin/js/neopagupa.js') }}"></script>
    <!-- Alerta toastr -->
    <script src="{{ asset('admin/plugins/toastr/toastr.min.js') }}"></script>
    <!-- bootstrap-fileinput -->
    <script src="{{ asset('admin/js/fileinput/fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/js/fileinput/fas/theme.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/js/fileinput/explorer-fas/theme.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/js/locales/es.js') }}" type="text/javascript"></script>
    <!-- fullCalendar 2.2.5 -->
    <script src="{{ asset('admin/plugins/fullcalendar/main.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('admin/dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('admin/dist/js/demo.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) revisar
    <script src="{{ asset('admin/dist/js/pages/dashboard.js') }}"></script>-->
     <!-- bootbox -->
    <script src="{{ asset('admin/js/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/js/bootbox/bootbox.locales.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/js/select3.full.min.js') }}"></script>
    <script src="{{ asset('admin/js/jsTreeTable/javascript.js') }}"></script>
    <!-- Page specific script -->
    @yield('scriptAjax')
<script type="text/javascript">
   /* $(document).bind("contextmenu",function(e) {
        e.preventDefault();
    });
    $(document).keydown(function(e){
        if(e.which === 123){
        return false;
        }
    });*/
    function do_this(){

        var checkboxes = document.getElementsByName('checkbox[]');
        var button = document.getElementById('toggle');

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
    }
    function selecttotal(){

    var checkboxes = document.getElementsByName('checkbox[]');
    var button = document.getElementById('checkboxPrimary1');

    if(button.value == 'select'){
        for (var i in checkboxes){
            checkboxes[i].checked = 'FALSE';
        }
        button.value = 'deselect'
    }else{
        for (var i in checkboxes){
            checkboxes[i].checked = '';
        }
        button.value = 'select';
    }
    }
    function SELECTITEMS(){

    var checkboxes = document.getElementsByName('contador[]');
    var button = document.getElementById('checkboxPrimary1');

    if(button.value == 'deselect'){
        for (var i in checkboxes){
            checkboxes[i].checked = 'FALSE';
        }
        button.value = 'select'
    }else{
        for (var i in checkboxes){
            checkboxes[i].checked = '';
        }
        button.value = 'deselect';
    }
}
    window.onload = function() {
        if (document.getElementById("urlPDF")) {
            window.open( document.getElementById("urlPDF").value);
        }
        if (document.getElementById("urlPDF2")) {
            window.open(document.getElementById("urlPDF2").value);
        }
        if (document.getElementById("urldiario")) {
            window.open(document.getElementById("urldiario").value);
        }
        if (document.getElementById("urlcheque")) {
            window.open(document.getElementById("urlcheque").value);
        }
        if (document.getElementById("urlrol")) {
            window.open(document.getElementById("urlrol").value);
        }
        if(window.cargarmetodo){
            cargarmetodo();
        }
        
    
    }
    $(document).ready(function() {
        $('form').submit(function() {
            //$(this).find("button[type='submit']").prop('disabled',true);
            //$("#pageloader").fadeIn();
        });
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
            event.preventDefault();
            return false;
            }
        });
        @yield('scriptCode')
        setTimeout(function() {
            $(".mensajeria1").fadeOut(1500);
        },2000);
        setTimeout(function() {
            $(".mensajeria2").fadeOut(1500);
        },2000);
        $('.select2').select2();
        $('.duallistbox').bootstrapDualListbox();
    });
    $(function() {
        $("#example1").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "bSort": false,
            "ordering": false,
            "buttons": ["copy", "excel"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
    $(function() {
        $("#example2").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "bSort": false,
            "ordering": false,
            "buttons": ["copy", "excel"]
        }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
    });
    $(function() {
        $("#example3").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "bSort": false,
            "ordering": false,
            "buttons": ["copy", "excel"]
        }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');
    });
    $(function() {
        $("#example4").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "paging": false,
            "bSort": false,
            "ordering": false,
            "buttons": ["copy", "excel"]
        }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');
    });
    $(function() {
        $("#example11").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "paging": false,
            "bSort": false,
            "ordering": false,
            "buttons": ["copy", "excel"]
        }).buttons().container().appendTo('#example11_wrapper .col-md-6:eq(0)');
    });
    $(function() {
        $("#example22").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "paging": false,
            "bSort": false,
            "ordering": false,
            "buttons": ["copy", "excel"]
        }).buttons().container().appendTo('#example22_wrapper .col-md-6:eq(0)');
    });
    $(function() {
        $("#example33").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "paging": false,
            "bSort": false,
            "ordering": false,
            "buttons": ["copy", "excel"]
        }).buttons().container().appendTo('#example33_wrapper .col-md-6:eq(0)');
    });
    $(function() {
        $("#example44").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "paging": false,
            "bSort": false,
            "ordering": false,
            "buttons": ["copy", "excel"]
        }).buttons().container().appendTo('#example44_wrapper .col-md-6:eq(0)');
    });
    $(function() {
        $("#example5").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "paging": false,
            "bSort": false,
            "ordering": false,
            "dom": 'none',
        });
    });
    $(function() {
        $("#exampleBuscar").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "bSort": false,
            "ordering": false,
            "bPaginate":false,
            "bInfo":false,
            "scrollY":650,
            "deferRender":true,
            "scroller":true,
            "dom": 'frtipP',
            "classes": {
                "sFilter":"dataTables_filterNeo"
            },
        });
    });
    $(function() {
        $("#tableBuscar").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "bSort": false,
            "ordering": false,
            "bPaginate":false,
            "bInfo":false,
            "scrollY":650,
            "deferRender":true,
            "scroller":true,
            "dom": 'frtipP',
            "classes": {
                "sFilter":"dataTables_buscar"
            },
        });
    });
    $(function() {
        $("#tableCons1").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "bSort": false,
            "ordering": false,
            "bPaginate":false,
            //"scrollY":500,
            "deferRender":true,
            //"scroller":true,
            "buttons": ["copy", "excel"]
        }).buttons().container().appendTo('#tableCons1_wrapper .col-md-6:eq(0)');
    });
    $(function() {
        $("#tableCons2").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "bSort": false,
            "ordering": false,
            "bPaginate":false,
           // "scrollY":500,
            "deferRender":true,
            //"scroller":true,
            "buttons": ["copy", "excel"]
        }).buttons().container().appendTo('#tableCons2_wrapper .col-md-6:eq(0)');
    });
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });
    var toggler = document.getElementsByClassName("caret");
    var i;

    for (i = 0; i < toggler.length; i++) {
    toggler[i].addEventListener("click", function() {
        this.parentElement.querySelector(".nested").classList.toggle("active");
        this.classList.toggle("caret-down");
        });
    }
    $(function () {
        bsCustomFileInput.init();
    });
    $(function () {
        $("input[data-bootstrap-switch]").each(function(){
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
        })
    });
    /*window.location.hash="no-back-button";
    window.location.hash="Again-No-back-button" //chrome
    window.onhashchange=function(){window.location.hash="no-back-button";}*/
    //check box select all
    @yield('scriptCalendar')
</script>