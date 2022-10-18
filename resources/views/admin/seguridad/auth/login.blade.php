<!DOCTYPE html>
<html lang="en">

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
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin/dist/css/adminlte.min.css') }}">
    <!-- NEOPAGUPA -->
    <link rel="stylesheet" href="{{ asset('admin/css/neopagupa.css') }}">
</head>
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
<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-info">
            <div class="card-header text-center">
                <h1><b>NEO</b><b class="neo-color">PAGUPA</b></h1>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Iniciar Sesión</p>

                <form method="POST" action="{{ url("sesion") }}">
                    @csrf
                    @if ($errors->has('user_username'))
                    <center>
                        <div class="neo-red-noti">
                            <span class="help-block">
                                <strong>{{ $errors->first('user_username') }}</strong>
                            </span>
                        </div>
                    </center>
                    @endif
                    @if($requerirRuc)
                    <div class="input-group mb-3">
                        <input type="text" name="idRuc" class="form-control" placeholder="Ruc Empresa" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-university"></span>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="input-group mb-3">
                        <input type="text" name="idUsername" class="form-control" placeholder="Username" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="idPassword" class="form-control" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-7">
                            <div class="icheck-info">
                                <input type="checkbox" id="remember">
                                <label for="remember">
                                    Recuerdame
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-5">
                            <button type="submit" class="btn btn-info btn-block">Iniciar Sesión</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <!-- /.social-auth-links -->
                <hr>

                <div class="row">
                    <!--div class="col-6">
                        <p class="mb-1 text-center">
                            <a href="forgot-password.html">Olvidé mi Clave</a>
                        </p>
                    </div-->
                    <div class="col-12">
                        <p class="mb-1 text-center">
                            No tienes Cuenta, <a href="{{url('/registro')}}">Registrarme</a>
                        </p>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('admin/dist/js/adminlte.min.js') }}"></script>
</body>

</html>