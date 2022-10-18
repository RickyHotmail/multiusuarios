<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NEOPAGUPA | Sistema Contable</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('admin/imagenes/logo2.ico')}}" />
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/select2/css/select2.min.css') }}">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/summernote/summernote-bs4.min.css') }}">
    <!-- NEOPAGUPA -->
    <link rel="stylesheet" href="{{ asset('admin/css/neopagupa.css') }}">
    <!-- Alerta toastr -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/toastr/toastr.min.css') }}">
    <!-- bootstrap-fileinput -->
    <link rel="stylesheet" href="{{ asset('admin/css/fileinput/fileinput.css') }}" media="all"  type="text/css"/>
    <link rel="stylesheet" href="{{ asset('admin/css/fileinput/explorer-fas/theme.css') }}" media="all" type="text/css"/>
    <!-- fullCalendar -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/fullcalendar/main.css') }}">
    <!-- Jquery-UI 
    <link rel="stylesheet" href="{{ asset('vendor/jquery-ui/jquery-ui.min.css') }}">-->
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
             <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Messages Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link " href="logout">
                        <button class="btn btn-neo-blue btn-sm" data-toggle="tooltip" data-placement="bottom" title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i></button>
                    </a>
                </li> 
            </ul>
        </nav>
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/" class="brand-link">
                <img src="{{ asset('admin/imagenes/logo2-02.png') }}" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light"><b>NEOPAGUPA</b></span>
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ asset('admin/imagenes/user.png') }}" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a class="d-block"></a>
                    </div>
                </div>
                <div class="mb-3 d-flex"></div>
                <!-- SidebarSearch Form -->
                <div class="form-inline">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Buscar"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                        
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div id="pageloader">
                    <img src="{{ asset('admin/imagenes/cargando.gif') }}" alt="processing..." />
                </div>
            <br>
            <!-- Content Header (Page header) -->
            <!--<div class="content-header">
                  <div class="container-fluid">
                      <div class="row mb-2">
                          <div class="col-sm-6">
                              <h1 class="m-0">@yield('titulo')</h1>
                          </div>
                          <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                              <li class="breadcrumb-item"><a href="#">Home</a></li>
                              <li class="breadcrumb-item active">Dashboard v1</li>
                            </ol>
                          </div>
                      </div>
                  </div>
              </div>-->
            <!-- /.content-header -->
            <!-- Main content -->
            <section class="content">
            <noscript>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5>
                        <i class="icon fas fa-exclamation-triangle"></i>
                        Javascript está deshabilitado en su navegador web.<br />
                        Por favor, para ver correctamente este sitio,<br />
                        <b><i>habilite javascript</i></b>.<br />
                        <br />
                        Para ver las instrucciones para habilitar javascript<br />
                        en su navegador, haga click 
                        <a href="https://support.google.com/adsense/answer/12654?hl=es-419" 
                        target="_blank" style="color: #000;"><b>aquí</b></a>.
                    </h5>
                </div>
            </noscript>
                <div class="container-fluid">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Nueva Empresa</h3>
                        </div>
                        <div class="card-body">
                            <form class="form-horizontal" method="POST" action="/empresa">
                            @csrf
                                <div class="modal-body">
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label for="Ruc" class="col-sm-3 col-form-label">Ruc</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="Ruc" name="Ruc" placeholder="Ej. 9999999999999" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idNombre" class="col-sm-3 col-form-label">Nombre Comercial</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre Comercial" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idRazon" class="col-sm-3 col-form-label">Razon Social</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="idRazon" name="idRazon" placeholder="Razon Social" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idCiudad" class="col-sm-3 col-form-label">Ciudad</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="idCiudad" name="idCiudad" placeholder="Ciudad" value="MACHALA" required> 
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idDireccion" class="col-sm-3 col-form-label">Dirección</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="idDireccion" name="idDireccion" placeholder="Direccion" value="S/D" required> 
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idTelefono" class="col-sm-3 col-form-label">Teléfono</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="idTelefono" name="idTelefono" placeholder="Ej. 022999999" value="0" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idCelular" class="col-sm-3 col-form-label">Celular</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="idCelular" name="idCelular" placeholder="Ej. 0999999999" value="0" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idRepresentante" class="col-sm-3 col-form-label">Representante Legal</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="idRepresentante" name="idRepresentante" placeholder="Representante Legal" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idFecha" class="col-sm-3 col-form-label">Fecha de Ingreso</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" id="idFecha" name="idFecha" value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idEmail" class="col-sm-3 col-form-label">Email</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" id="idEmail" name="idEmail" placeholder="Email" value="SIN@CORREO" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <div class="icheck-success d-inline">
                                                    <input type="checkbox" id="idContabilidad" name="idContabilidad" checked>
                                                    <label for="idContabilidad">Lleva Contabilidad</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idTipo" class="col-sm-3 col-form-label">Tipo de Empresa</label>
                                            <div class="col-sm-9">
                                                <select class="custom-select" id="idTipo" name="idTipo" require>
                                                    <option value="Microempresas">Microempresas</option>
                                                    <option value="Agente de Retención">Agente de Retención</option>
                                                    <option value="Contribuyente Especial">Contribuyente Especial</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idContribuyente" class="col-sm-3 col-form-label">Contri. Especial No.</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control" id="idContribuyente" name="idContribuyente" value="0" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idContabilidad2" class="col-sm-3 col-form-label">Sistema Contable</label>
                                            <div class=" col-sm-9">
                                                <div class="icheck-success d-inline">
                                                    <input type="checkbox" id="idContabilidad2" name="idContabilidad2" >
                                                    <label for="idContabilidad2"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idElectronica" class="col-sm-3 col-form-label">Facturación Electrónica</label>
                                            <div class=" col-sm-9">
                                                <div class="icheck-success d-inline">
                                                    <input type="checkbox" id="idElectronica" name="idElectronica" >
                                                    <label for="idElectronica"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idNomina" class="col-sm-3 col-form-label">Sistema de Nómina</label>
                                            <div class=" col-sm-9">
                                                <div class="icheck-success d-inline">
                                                    <input type="checkbox" id="idNomina" name="idNomina" >
                                                    <label for="idNomina"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="idMedico" class="col-sm-3 col-form-label">Sistema Médico</label>
                                            <div class=" col-sm-9">
                                                <div class="icheck-success d-inline">
                                                    <input type="checkbox" id="idMedico" name="idMedico" >
                                                    <label for="idMedico"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <footer class="main-footer">
            <strong>Copyright &copy; 2021 <a href="http://www.pagupasoft.com">PAGUPASOFT</a>.</strong>
            Todos los derechos reservados.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
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
    <!-- ChartJS -->
    <script src="{{ asset('admin/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('admin/plugins/sparklines/sparkline.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="{{ asset('admin/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset('admin/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('admin/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('admin/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('admin/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    
    <!-- AdminLTE App -->
    <script src="{{ asset('admin/dist/js/adminlte.js') }}"></script>
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
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('admin/dist/js/demo.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('admin/dist/js/pages/dashboard.js') }}"></script>
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
     <!-- bootbox -->
    <script src="{{ asset('admin/js/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('admin/js/bootbox/bootbox.locales.min.js') }}" type="text/javascript"></script>
    <!-- Page specific script -->
</body>

</html>