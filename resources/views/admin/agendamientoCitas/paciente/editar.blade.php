@extends ('admin.layouts.admin')
@section('principal')

<style>
    label[for="fotoAfiliado"], label[for="fotoPaciente"]{
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        background-color: #106BA0;
        display: inline-block;
        transition: all .5s;
        cursor: pointer;
        padding: 5px 10px !important;
        text-transform: uppercase;
        width: fit-content;
        text-align: center;
    }
</style>
<form class="form-horizontal" method="POST" action="{{ route('paciente.update', [$paciente->paciente_id]) }}" enctype="multipart/form-data">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Paciente</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("paciente") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idTipoIdentificacion" class="col-sm-3 col-form-label">Tipo de Documento</label>
                <div class="col-sm-9">
                    <select id="idTipoIdentificacion" name="idTipoIdentificacion" class="form-control show-tick " data-live-search="true" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($tiposIdentificacion as $tipoIdentificacion)
                            @if($tipoIdentificacion->tipo_identificacion_id == $paciente->tipo_identificacion_id)
                                <option value="{{$tipoIdentificacion->tipo_identificacion_id}}" selected>{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                            @else
                                <option value="{{$tipoIdentificacion->tipo_identificacion_id}}">{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNumero" class="col-sm-3 col-form-label">Número de Documento</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idNumero" name="idNumero" value="{{$paciente->paciente_cedula}}" placeholder="Número de Documento" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idApellidos" class="col-sm-3 col-form-label">Apellidos</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idApellidos" name="idApellidos" value="{{$paciente->paciente_apellidos}}" placeholder="Apellidos" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombres" class="col-sm-3 col-form-label">Nombres</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idNombres" name="idNombres" value="{{$paciente->paciente_nombres}}" placeholder="Nombres" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idSexo" class="col-sm-3 col-form-label">Sexo</label>
                <div class="col-sm-3">
                    <select id="idSexo" name="idSexo" class="form-control show-tick " data-live-search="true" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        <option value="Masculino" @if($paciente->paciente_sexo == "Masculino") selected @endif>Masculino</option>
                        <option value="Femenino" @if($paciente->paciente_sexo == "Femenino") selected @endif>Femenino</option>
                    </select>
                </div>
                <label for="idFechaNac" class="col-sm-3 col-form-label">Fecha de Nacimiento</label>
                <div class="col-sm-3">
                    <input type="date" class="form-control" id="idFechaNac" name="idFechaNac" value="{{$paciente->paciente_fecha_nacimiento}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idSexo" class="col-sm-3 col-form-label">Tipo de Dependencia</label>
                <div class="col-sm-9">
                    <select id="idTipoDependencia" name="idTipoDependencia" class="form-control show-tick " data-live-search="true" required>
                    <option value="" label>--Seleccione una opcion--</option>
                    @foreach($tiposDependencias as $tipo)
                        @if($tipo->tipod_id == $paciente->tipod_id)
                        <option value="{{$tipo->tipod_id}}" selected>{{$tipo->tipod_nombre}}</option>
                        @else
                        <option value="{{$tipo->tipod_id}}">{{$tipo->tipod_nombre}}</option>
                        @endif
                    @endforeach                             
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="tetx" class="col-sm-3 col-form-label">Es Dependiente</label>
                <div class="custom-control custom-checkbox"> 
                @if($paciente->paciente_dependiente == "1")                                   
                    <input type="checkbox" class="custom-control-input" id="id_dependiente" name="id_dependiente" checked onclick="check();">
                    <label for="id_dependiente" class="custom-control-label"></label>
                @else
                    <input type="checkbox" class="custom-control-input" id="id_dependiente" name="id_dependiente" onclick="check();">
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
            @else
                <div class="form-group row">
                    <label for="idCiAfiliado" class="col-sm-3 col-form-label">CI de Afiliado</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="idCiAfiliado" name="idCiAfiliado" value="{{$paciente->paciente_cedula_afiliado}}" disabled required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idNombreAfiliado" class="col-sm-3 col-form-label">Nombre de Afiliado</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="idNombreAfiliado" name="idNombreAfiliado" value="{{$paciente->paciente_nombre_afiliado}}" disabled required>
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


                    <label for="fotoPaciente" ><i class='fa fa-upload' aria-hidden='true'></i> Cargar</label>
                    <input class="foto" style="display: none;" id="fotoPaciente" name="fotoPaciente" type="file"  accept=".png, .jpg, .jpeg, .gif">
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

                    <label for="fotoAfiliado"><i class='fa fa-upload' aria-hidden='true'></i> Cargar</label>
                    <input class="foto" style="display: none;" id="fotoAfiliado" name="fotoAfiliado" type="file"  accept=".png, .jpg, .jpeg, .gif">
                </div>
            </div>
            
            
            <div class="form-group row">
                <label for="idDireccion" class="col-sm-3 col-form-label">Direccion</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idDireccion" name="idDireccion" value="{{$paciente->paciente_direccion}}" placeholder="Direccion" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCelular" class="col-sm-3 col-form-label">Celular</label>
                <div class="col-sm-9">
                    <input type="phone" class="form-control" id="idCelular" name="idCelular" value="{{$paciente->paciente_celular}}" placeholder="Celular" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEmail" class="col-sm-3 col-form-label">E-mail</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control" id="idEmail" name="idEmail" value="{{$paciente->paciente_email}}" placeholder="E-mail" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idPais" class="col-sm-3 col-form-label">Pais de Nacimiento</label>
                <div class="col-sm-4">
                    <select id="idPais" name="idPais" class="form-control select2" data-live-search="true" onchange="cargarProvincias();" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($paises as $pais)
                            @if($pais->pais_id == $paciente->pais_id)
                                <option value="{{$pais->pais_id}}" selected>{{$pais->pais_nombre}}</option>
                            @else
                                <option value="{{$pais->pais_id}}">{{$pais->pais_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <label for="idNacionalidad" class="col-sm-2 col-form-label">Nacionalidad</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="idNacionalidad" name="idNacionalidad"  value="{{$paciente->paciente_nacionalidad}}" placeholder="Nacionalidad" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idProvincia" class="col-sm-3 col-form-label">Provincia de Recidencia</label>
                <div class="col-sm-4">
                    <input type="hidden" id="provinciaIDAux" value="{{ $paciente->provincia_id }}"/>
                    <select id="idProvincia" name="idProvincia" class="form-control select2" data-live-search="true" onchange="cargarCiudades();" required>
                        <option value="" label>--Seleccione una opcion--</option>
                    </select>
                </div>
                <label for="idCiudad" class="col-sm-1 col-form-label">Ciudad</label>
                <div class="col-sm-4">
                    <input type="hidden" id="ciudadIDAux" value="{{ $paciente->ciudad_id }}"/>
                    <select id="idCiudad" name="idCiudad" class="form-control select2" data-live-search="true" required>
                        <option value="" label>--Seleccione una opcion--</option>
                    </select>
                </div>
            </div>                        
            <div class="form-group row">
                <label for="idAseguradora" class="col-sm-3 col-form-label">Aseguradora</label>
                <div class="col-sm-9">
                    <select id="idAseguradora" name="idAseguradora" class="form-control select2" onchange="cargarEntidades();" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($clientesAseguradoras as $clienteAseguradora)
                            @if($clienteAseguradora->cliente_id == $paciente->cliente_id)
                            <option value="{{$clienteAseguradora->cliente_id}}" selected>{{$clienteAseguradora->cliente_nombre}}</option>
                            @else
                            <option value="{{$clienteAseguradora->cliente_id}}">{{$clienteAseguradora->cliente_nombre}}</option>
                            @endif
                        @endforeach                            
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEntidad" class="col-sm-3 col-form-label">Empresa</label>
                <div class="col-sm-9">
                    <input type="hidden" id="entidadIDAux" value="{{ $paciente->entidada_id }}"/>
                    <select id="idEntidad" name="idEntidad"  class="form-control select2" required>
                        <option value="" label>--Seleccione una opcion--</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEstado" class="col-sm-3 col-form-label">Estado</label>
                <div class="col-sm-9">
                    <div class="col-form-label custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($paciente->paciente_estado=="1")
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    document.getElementById("fotoAfiliado").addEventListener("change", function () {
        readImage(this)
    });
    document.getElementById("fotoPaciente").addEventListener("change", function () {
        readImage(this)
    });
    

    function readImage (input) {
        console.log("input")
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                //console.log("dir " +e.target.result)
                $('#'+input.name+'P').attr('src', e.target.result); // Renderizamos la imagen
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById("id_dependiente").addEventListener("click", function(){
        if(document.getElementById("id_dependiente").checked){
            document.getElementById("marcoFotoAfiliado").style.display=""
            document.getElementById("fotoAfiliadoP").src="/img/up_document.png"
        }
        else{
            document.getElementById("marcoFotoAfiliado").style.display="none"
            document.getElementById("fotoAfiliadoP").src=""
        }
    })

    
</script>

@endsection
<script type="text/javascript">
    function check(){        
        if(document.getElementById("id_dependiente").checked){
            document.getElementById("idCiAfiliado").disabled = false;
            document.getElementById("idNombreAfiliado").disabled = false;

        }else{
            document.getElementById("idCiAfiliado").disabled = true;
            document.getElementById("idNombreAfiliado").disabled = true;
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        cargarProvincias();
        cargarEntidades();
    });
    
    function cargarEntidades() {
        document.getElementById("idEntidad").disabled = false;
        $.ajax({
            url: '{{ url("entidad/searchN") }}'+ '/' +document.getElementById("idAseguradora").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("idAseguradora").value
            },
            success: function(data) {
                document.getElementById("idEntidad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                for (var i = 0; i < data.length; i++) {
                    if(document.getElementById("entidadIDAux").value == data[i].entidad_id){
                        document.getElementById("idEntidad").innerHTML += "<option value='" + data[i].entidad_id + "' selected>" + data[i].entidad_nombre + "</option>";
                    }
                    else{
                        document.getElementById("idEntidad").innerHTML += "<option value='" + data[i].entidad_id + "'>" + data[i].entidad_nombre + "</option>";
                    }
                }
            },
            error: function(data) {
                document.getElementById("idEntidad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                document.getElementById("idEntidad").disabled = true;
            },
        });
    }
    function cargarProvincias() {
        document.getElementById("idProvincia").disabled = false;
        $.ajax({
            url: '{{ url("provincia/searchN") }}'+ '/' +document.getElementById("idPais").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("idPais").value
            },
            success: function(data) {
                if(data.length == 0){
                    document.getElementById("idProvincia").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                    document.getElementById("idCiudad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                }else{
                    document.getElementById("idProvincia").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                    document.getElementById("idCiudad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                    for (var i = 0; i < data.length; i++) {
                        if(document.getElementById("provinciaIDAux").value == data[i].provincia_id){
                            document.getElementById("idProvincia").innerHTML += "<option value='" + data[i].provincia_id + "' selected>" + data[i].provincia_nombre + "</option>";
                        }
                        else{
                            document.getElementById("idProvincia").innerHTML += "<option value='" + data[i].provincia_id + "'>" + data[i].provincia_nombre + "</option>";
                        }
                    }    
                    cargarCiudades();
                }
            },
            error: function(data) {
                document.getElementById("idProvincia").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                document.getElementById("idCiudad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            },
        });
    }
    function cargarCiudades() {
        document.getElementById("idCiudad").disabled = false;
        $.ajax({
            url: '{{ url("ciudad/searchN") }}'+ '/' +document.getElementById("idProvincia").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("idProvincia").value
            },
            success: function(data) {
                if(data.length == 0){
                    document.getElementById("idCiudad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                }else{
                    document.getElementById("idCiudad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                    for (var i = 0; i < data.length; i++) {
                        if(document.getElementById("ciudadIDAux").value == data[i].ciudad_id){
                            document.getElementById("idCiudad").innerHTML += "<option value='" + data[i].ciudad_id + "' selected>" + data[i].ciudad_nombre + "</option>";
                        }
                        else{
                            document.getElementById("idCiudad").innerHTML += "<option value='" + data[i].ciudad_id + "'>" + data[i].ciudad_nombre + "</option>";
                        }
                    }
                }
            },
            error: function(data) {
                document.getElementById("idCiudad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            },
        });
    }
</script>