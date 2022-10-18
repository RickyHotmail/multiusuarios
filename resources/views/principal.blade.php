<!DOCTYPE html>
<html lang="es">
@include('admin.layouts.head')
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
                <li class="nav-item nombreEmpresa">
                    <b>{{ Auth::user()->empresa->empresa_razonSocial }}</b>
                </li>
                <li class="nav-item">
                    <a class="brand-link" href="{{ url("logout") }}" style="width: 70px;">
                        <button class="btn btn-neo-blue btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i></button>
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
                        <a class="d-block" style="white-space: pre-wrap;">{{ Auth::user()->user_nombre }}</a>
                    </div>
                </div>
                <div class="mb-3 d-flex"></div>
                <!-- SidebarSearch Form -->
                <div class="form-inline">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Buscar" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar"><i class="fas fa-search fa-fw"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                        @include('admin.layouts.menuAdmin')
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
            <div class="nav navbar navbar-expand-lg navbar-white navbar-light border-bottom p-0 tab-frame-neo-fondo">
                
                <ul class="navbar-nav" role="tablist"></ul>
            </div>
            <div class="tab-content">
                @if ($message = Session::get('cambio clave'))
                    <div class="alert alert-info alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                <div class="tab-empty">
                <center>
                    <img src="{{ asset('admin/imagenes/logo-01.png') }}" alt="NEOPAGUPA" style="opacity: .8" height="700">
                </center>
                </div>
                <div class="tab-loading">
                    <div>
                        <h2 class="display-4">Cargando... <i class="fa fa-sync fa-spin"></i></h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->
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
    
</body>
@include('admin.layouts.footer')
</html>