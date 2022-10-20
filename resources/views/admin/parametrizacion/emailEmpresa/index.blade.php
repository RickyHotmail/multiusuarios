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
            @if(isset($emailEmpresa->email_neopagupa))

                <div class="form-group row">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="customCheckbox1" name="automatico" value="1" onchange="cheke();"  @if($emailEmpresa->email_neopagupa == '1') checked="" @endif>
                        <label for="customCheckbox1" class="custom-control-label">Correo Automatico</label>
                        
                    </div>
                </div>
            @endif
            <div class="form-group row">
                <label for="idServidor" class="col-sm-2 col-form-label">Servidor</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idServidor" name="idServidor" @if(isset($emailEmpresa->email_neopagupa)) @if($emailEmpresa->email_neopagupa == '1') disabled @else value="@if(!empty($emailEmpresa)){{$emailEmpresa->email_servidor}} @endif" @endif @endif required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCorreo" class="col-sm-2 col-form-label">Correo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCorreo" name="idCorreo" @if(isset($emailEmpresa->email_neopagupa)) @if($emailEmpresa->email_neopagupa == '1') disabled @else value="@if(!empty($emailEmpresa)){{$emailEmpresa->email_email}}@endif"  @endif @endif   required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idUsuario" class="col-sm-2 col-form-label">Usuario</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idUsuario" name="idUsuario" @if(isset($emailEmpresa->email_neopagupa)) @if($emailEmpresa->email_neopagupa == '1') disabled @else value="@if(!empty($emailEmpresa)){{$emailEmpresa->email_usuario}}@endif"  @endif @endif   required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idPass" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="idPass" name="idPass" @if(isset($emailEmpresa->email_neopagupa)) @if($emailEmpresa->email_neopagupa == '1') disabled @else value="@if(!empty($emailEmpresa)){{$emailEmpresa->email_pass}}@endif" @endif @endif   required>
                </div>
            </div> 
            <div class="form-group row">
                <label for="idPuerto" class="col-sm-2 col-form-label">Puerto</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idPuerto" name="idPuerto" @if(isset($emailEmpresa->email_neopagupa)) @if($emailEmpresa->email_neopagupa == '1') disabled @else value="@if(!empty($emailEmpresa)){{$emailEmpresa->email_puerto}}@endif"  @endif @endif   required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idMensaje" class="col-sm-2 col-form-label">Mensaje</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="idMensaje" name="idMensaje" rows="5" @if(isset($emailEmpresa->email_neopagupa)) @if($emailEmpresa->email_neopagupa == '1') disabled @endif @endif required> @if(isset($emailEmpresa->email_neopagupa)) @if($emailEmpresa->email_neopagupa == '0')   @if(!empty($emailEmpresa)){{$emailEmpresa->email_mensaje}}@endif @endif @endif  </textarea>
                </div>
            </div>                 
        </div>            
    </div>
</form>
@endsection
<script type="text/javascript">
    function cheke(){
       if(document.getElementById("customCheckbox1").checked==false){
        document.getElementById("idServidor").disabled = false;
        document.getElementById("idCorreo").disabled = false;
        document.getElementById("idUsuario").disabled = false;
        document.getElementById("idPass").disabled = false;
        document.getElementById("idPuerto").disabled = false;
        document.getElementById("idMensaje").disabled = false;
       
       }
       else{
        document.getElementById("idServidor").disabled = true;
        document.getElementById("idCorreo").disabled = true;
        document.getElementById("idUsuario").disabled = true;
        document.getElementById("idPass").disabled = true;
        document.getElementById("idPuerto").disabled = true;
        document.getElementById("idMensaje").disabled = true;
       
       }
    

    }
</script>