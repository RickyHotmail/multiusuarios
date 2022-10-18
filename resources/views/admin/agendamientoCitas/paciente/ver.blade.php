@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Paciente</h3>
        <button onclick='window.location = "{{ url("paciente") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="form-group row">
            <label for="idNombre" class="col-sm-3 col-form-label">Cedula</label>
            <div class="col-sm-9">
                <label class="form-control">{{$paciente->paciente_cedula}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
            <div class="col-sm-9">
                <label class="form-control">{{$paciente->paciente_apellidos .' '. $paciente->paciente_nombres}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idNombre" class="col-sm-3 col-form-label">Sexo</label>
            <div class="col-sm-4">
                <label class="form-control">{{$paciente->paciente_sexo}}</label>
            </div>
            <label for="idNombre" class="col-sm-2 col-form-label">Fecha de Nacimiento</label>
            <div class="col-sm-3">
                <label class="form-control">{{$paciente->paciente_fecha_nacimiento}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idSexo" class="col-sm-3 col-form-label">Tipo de Dependencia</label>
            <div class="col-sm-9">
                <label class="form-control">{{$paciente->tipoDependencia->tipod_nombre}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="tetx" class="col-sm-3 col-form-label">Es Dependiente</label>
            <div class="custom-control custom-checkbox"> 
            @if($paciente->paciente_dependiente == "1")                                   
                <input type="checkbox" class="custom-control-input" id="id_dependiente" name="id_dependiente" disabled checked onclick="check();">
                <label for="id_dependiente" class="custom-control-label"></label>
            @else
                <input type="checkbox" class="custom-control-input" id="id_dependiente" name="id_dependiente" disabled onclick="check();">
                <label for="id_dependiente" class="custom-control-label"></label>
            @endif     
            </div>                                      
        </div>

        @if($paciente->paciente_dependiente == "1")      
            <div class="form-group row">
                <label for="idCiAfiliado" class="col-sm-3 col-form-label">CI de Afiliado</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idCiAfiliado" name="idCiAfiliado" value="{{$paciente->paciente_cedula_afiliado}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombreAfiliado" class="col-sm-3 col-form-label">Nombre de Afiliado</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idNombreAfiliado" name="idNombreAfiliado" value="{{$paciente->paciente_nombre_afiliado}}" required>
                </div>
            </div>
        @endif

        <div  class="form-group row">
            <label class="col-sm-3 col-form-label">Cedula del Paciente</label>

            <div class="col-sm-3">
                @if( $paciente->documento_paciente!=null && $paciente->documento_paciente!="" )
                    <img style="width: 200px;" src="{{ url('') }}/{{$paciente->documento_paciente}}" id="fotoPacienteP"><br>
                @else
                    <img style="width: 200px;" src="{{ url('img') }}/up_document.png" id="fotoPacienteP"><br>
                @endif


                <!--label for="fotoPaciente" ><i class='fa fa-upload' aria-hidden='true'></i> Cargar</label>
                <input class="foto" style="display: none;" id="fotoPaciente" name="fotoPaciente" type="file"  accept=".png, .jpg, .jpeg, .gif"-->
            </div>
        </div>

        
        <div class="form-group row" id="marcoFotoAfiliado" style="@if($paciente->paciente_dependiente=='0') display: none  @endif">
            <label class="col-sm-3 col-form-label">Cedula del Afiliado</label>

            <div class="col-sm-3">
                @if($paciente->documento_afiliado!=null && $paciente->documento_afiliado!="")
                    <img style="width: 200px;" src="{{ url('') }}/{{$paciente->documento_afiliado}}" id="fotoAfiliadoP"><br>
                @else
                    <img style="width: 200px;" src="{{ url('img') }}/up_document.png" id="fotoAfiliadoP"><br>
                @endif

                <!--label for="fotoAfiliado"><i class='fa fa-upload' aria-hidden='true'></i> Cargar</label>
                <input class="foto" style="display: none;" id="fotoAfiliado" name="fotoAfiliado" type="file"  accept=".png, .jpg, .jpeg, .gif"-->
            </div>
        </div>

        <div class="form-group row">
            <label for="idNombre" class="col-sm-3 col-form-label">Direccion</label>
            <div class="col-sm-9">
                <label class="form-control">{{$paciente->paciente_direccion}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idNombre" class="col-sm-3 col-form-label">Celular</label>
            <div class="col-sm-9">
                <label class="form-control">{{$paciente->paciente_celular}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idNombre" class="col-sm-3 col-form-label">E-mail</label>
            <div class="col-sm-9">
                <label class="form-control">{{$paciente->paciente_email}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idNombre" class="col-sm-3 col-form-label">Pais de Nacimiento</label>
            <div class="col-sm-4">
                <label class="form-control">{{$paciente->pais_nombre}}</label>
            </div>
            <label for="idNombre" class="col-sm-2 col-form-label">Nacionalidad</label>
            <div class="col-sm-3">
                <label class="form-control">{{$paciente->paciente_nacionalidad}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idNombre" class="col-sm-3 col-form-label">Provincia de Recidencia</label>
            <div class="col-sm-4">
                <label class="form-control">{{$paciente->provincia_nombre}}</label>
            </div>
            <label for="idNombre" class="col-sm-2 col-form-label">Ciudad</label>
            <div class="col-sm-3">
                <label class="form-control">{{$paciente->ciudad_nombre}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idNombre" class="col-sm-3 col-form-label">Aseguradora</label>
            <div class="col-sm-4">
                <label class="form-control">{{$paciente->cliente_nombre}}</label>
            </div>
            <label for="idNombre" class="col-sm-2 col-form-label">Empresa</label>
            <div class="col-sm-3">
                <label class="form-control">{{$paciente->entidad_nombre}}</label>
            </div>
        </div>
        <div class="form-group row">
            
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Estado</label>
            <div class="col-sm-9 col-form-label">
                @if($paciente->paciente_estado=="1")
                <i class="fa fa-check-circle neo-verde"></i>
                @else
                <i class="fa fa-times-circle neo-rojo"></i>
                @endif
            </div>
        </div>
    </div>
    <!-- /.card-body -->
    <!-- /.card-footer -->
</div>
<!-- /.card-body -->
</div>
<!-- /.card -->
@endsection