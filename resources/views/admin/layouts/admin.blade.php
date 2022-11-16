<!DOCTYPE html>
<html lang="es">
@include('admin.layouts.head')
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
    <div class="">
            <div id="pageloader">
                    <img src="{{ asset('admin/imagenes/cargando.gif') }}" alt="processing..." />
                </div>
            <br>
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
                    @if(!empty($successMsg))
                    <div class="mensajeria1 alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i>{{ $successMsg }}</h5>
                    </div>
                    @endif  
                                   
                    @if(session('success'))
                    <div class="mensajeria1 alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i>{{ session('success') }}</h5>
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="mensajeria2 alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i>{{ session('error') }}</h5>
                    </div>
                    @endif
                    @if(session('error2'))
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i>{{ session('error2') }}</h5>
                    </div>
                    @endif
                    @if(!empty($error2Msg))
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i>{{ $error2Msg }}</h5>
                    </div>
                    @endif
                    @if(session('pdf'))
                        <?php $url = session('pdf'); ?>
                        <input type="hidden" id="urlPDF" value="{{ url("{$url}") }}" />
                    @endif
                    @if(session('pdf2'))
                        <?php $url = session('pdf2'); ?>
                        <input type="hidden" id="urlPDF2" value="{{ url("{$url}") }}" />
                    @endif
                    @if(session('diario'))
                        @if(!empty(session('diario')))
                            <?php $url = session('diario'); ?>
                            <input type="hidden" id="urldiario" value="{{ url("{$url}") }}" />
                        @endif
                    @endif
                    @if(session('cheque'))
                        @if(!empty(session('cheque')))
                            <?php $url = session('cheque'); ?>
                            <input type="hidden" id="urlcheque" value="{{ url("{$url}") }}" />
                        @endif
                    @endif
                    @if(session('rol'))
                        <?php $url = session('rol'); ?>
                        <input type="hidden" id="urlrol" value="{{ url("{$url}") }}" />
                    @endif
                    @yield('principal')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
    </div>
    <!-- ./wrapper -->
    @include('admin.layouts.footer')
</body>

</html>