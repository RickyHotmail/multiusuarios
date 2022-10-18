@extends ('admin.layouts.admin')
@section('principal')
@csrf
    <style>
        .modal-content{
            position: absolute;
            left: 50%;
            transform: translate(-50%);
        }
    </style>
    <div class="card card-secondary">
        <div class="card-header bg-warning">
            <h3 class="card-title">Se restableció la Contraseña de su cuenta, cambiar inmediatamente</h3>
        </div>          
    </div>
    <div class="modal-content p-0 m-0 col-xl-4 col-lg-6">
        <div class="modal-header bg-secondary">
            <h4 class="modal-title">Cambio de Contraseña</h4>
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