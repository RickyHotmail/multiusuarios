@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("emailEmpresa") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Configurar Correo Empresa</h3>
            <button type="submit" class="btn btn-success btn-sm float-right"><i class="fa fa-save"></i>&nbsp;Guardar</button>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="form-group row">
                <label for="idServidor" class="col-sm-2 col-form-label">Servidor</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idServidor" name="idServidor" value="@if(!empty($emailEmpresa)){{$emailEmpresa->email_servidor}}@endif" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCorreo" class="col-sm-2 col-form-label">Correo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCorreo" name="idCorreo"  value="@if(!empty($emailEmpresa)){{$emailEmpresa->email_email}}@endif" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idUsuario" class="col-sm-2 col-form-label">Usuario</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idUsuario" name="idUsuario"  value="@if(!empty($emailEmpresa)){{$emailEmpresa->email_usuario}}@endif" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idPass" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="idPass" name="idPass"  value="@if(!empty($emailEmpresa)){{$emailEmpresa->email_pass}}@endif" required>
                </div>
            </div> 
            <div class="form-group row">
                <label for="idPuerto" class="col-sm-2 col-form-label">Puerto</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idPuerto" name="idPuerto"  value="@if(!empty($emailEmpresa)){{$emailEmpresa->email_puerto}}@endif" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idMensaje" class="col-sm-2 col-form-label">Mensaje</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="idMensaje" name="idMensaje" rows="5" required> @if(!empty($emailEmpresa)){{$emailEmpresa->email_mensaje}}@endif </textarea>
                </div>
            </div>                 
        </div>            
    </div>
</form>
@endsection