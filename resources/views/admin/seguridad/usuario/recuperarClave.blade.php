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
        <style>
            .modal-content{
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%)
            }

            .card-secondary{
                position: absolute;
                left: 0px;
                top: 0px;
                width: 100%;
            }

            .card-error {
                position: absolute;
                left: 0px;
                top: 50px;
                width: 100%;
            }
        </style>
        @if(session('error'))
            <div class="card-error">
                <div class="card-header bg-danger">
                    <h3 class="card-title">{{session('error')}}</h3>
                </div>          
            </div>
        @endif
        <div class="card card-secondary">
            <div class="card-header bg-warning">
                <h3 class="card-title">Ingresa la información necesaria, para recuperar tu Cuenta!</h3>
            </div>          
        </div>
        <div class="modal-content p-0 m-0 col-xl-4 col-lg-6">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Recuperación de Cuenta</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url('recuperarClave') }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">                        
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Ingresa el Ruc de la Empresa</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idRuc" name="idRuc" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">El Correo de tu Cuenta</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="idCorreo" name="idCorreo" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>            
        </div>
    </body>
</html>