@extends ('admin.layouts.admin')
@section('principal')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Perfil de Usuario</h3>
        </div>       
        <div class="card-body">            
            <div class="form-group row">
                <div class="col-sm-4">               
                    <div class="card card-secondary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">    
                                <i class="fa fa-user fa-5x"></i>
                            </div>

                            <h3 class="profile-username text-center" >{{$usuario->user_nombre}}</h3>

                            <p class="text-muted text-center">{{$usuario->user_username}}</p>

                            <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Cedula</b> <a class="float-right">{{$usuario->user_cedula}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Correo</b> <a class="float-right">{{$usuario->user_correo}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Telefono</b> <a class="float-right">S/N</a>
                            </li>
                            </ul>
                            <form class="form-horizontal" method="POST" action="/perfil">
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
                            </form>
                            <a href="/#" class="btn btn-secondary btn-block" data-toggle="modal" data-target="#modal-nuevo"><b>Cambiar Contraseña</b></a>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>                              
            </div>
        </div>
    </div>
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Cambiar Contraseña</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal"  onsubmit="return verificarClave();" method="POST" action="{{ url("perfil") }}">
            @csrf   
                <div class="modal-body">
                    <div class="card-body">                        
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Clave Actual</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="idActual" name="idActual" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Clave Nueva</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="idNueva" name="idNueva" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Confirmar Nueva Clave </label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="idNueva2" name="idNueva2" required>
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
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script type="text/javascript">
    function verificarClave() {
        if (document.getElementById("idNueva").value === document.getElementById("idNueva2").value) {
            return true;
        } else {
            alert('Las Claves nueva son diferentes');
            return false;
        }

    }
</script>
@endsection