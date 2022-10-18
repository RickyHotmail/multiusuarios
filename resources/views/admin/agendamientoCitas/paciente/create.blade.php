@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("paciente") }}">
@csrf
    <div class="card card-secondary">    
    <!-- /.card-header -->
        <div class="card-header">
                <div class="row">
                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                        <h2 class="card-title">Nuevo Paciente</h2>
                    </div>
                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                        <div class="float-right">                           
                            <button id="guardarID" type="submit" class="btn btn-success btn-sm"><i
                                    class="fa fa-save"></i><span> Guardar</span></button>                           
                            <a href= "/paciente"><button type="button" id="cancelarID" name="cancelarID"
                                class="btn btn-default btn-sm not-active-neo"><i
                                    class="fas fa-times-circle"></i><span> Atras</span></button></a>
                        </div>
                    </div>
                </div>
        </div>
        <div class="card-body">
                        <div class="form-group row">
                            <label for="idTipoIdentificacion" class="col-sm-3 col-form-label">Tipo de Documento</label>
                            <div class="col-sm-9">
                                <select id="idTipoIdentificacion" name="idTipoIdentificacion" class="form-control show-tick " data-live-search="true" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($tiposIdentificacion as $tipoIdentificacion)
                                    <option value="{{$tipoIdentificacion->tipo_identificacion_id}}">{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNumero" class="col-sm-3 col-form-label">Número de Documento</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNumero" name="idNumero" placeholder="Número de Documento" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idApellidos" class="col-sm-3 col-form-label">Apellidos</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idApellidos" name="idApellidos" placeholder="Apellidos" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombres" class="col-sm-3 col-form-label">Nombres</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNombres" name="idNombres" placeholder="Nombres" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idSexo" class="col-sm-3 col-form-label">Sexo</label>
                            <div class="col-sm-3">
                                <select id="idSexo" name="idSexo" class="form-control show-tick " data-live-search="true" required>
                                    <option value="" label>--Seleccione--</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>
                            </div>
                            <label for="idFechaNac" class="col-sm-3 col-form-label">Fecha de Nacimiento</label>
                            <div class="col-sm-3">
                                <input type="date" class="form-control" id="idFechaNac" name="idFechaNac" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idSexo" class="col-sm-3 col-form-label">Tipo de Dependencia</label>
                            <div class="col-sm-9">
                                <select id="idTipoDependencia" name="idTipoDependencia" class="form-control show-tick " data-live-search="true" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($tiposDependencias as $tipo)
                                        <option value="{{$tipo->tipod_id}}">{{$tipo->tipod_nombre}}</option>
                                    @endforeach                              
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tetx" class="col-sm-3 col-form-label">Es Dependiente</label>
                            <div class="custom-control custom-checkbox">                                    
                                <input type="checkbox" class="custom-control-input" id="id_dependiente" name="id_dependiente" onclick="check();">
                                <label for="id_dependiente" class="custom-control-label"></label>
                            </div>                                        
                        </div>
                        <div class="form-group row">
                            <label for="idCiAfiliado" class="col-sm-3 col-form-label">CI: Afiliado</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCiAfiliado" name="idCiAfiliado" disabled required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombreAfiliado" class="col-sm-3 col-form-label">Nombre Afiliado</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNombreAfiliado" name="idNombreAfiliado" disabled required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idDireccion" class="col-sm-3 col-form-label">Direccion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idDireccion" name="idDireccion" placeholder="Direccion" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCelular" class="col-sm-3 col-form-label">Celular</label>
                            <div class="col-sm-9">
                                <input type="phone" class="form-control" id="idCelular" name="idCelular" placeholder="Celular" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idEmail" class="col-sm-3 col-form-label">E-mail</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="idEmail" name="idEmail" placeholder="E-mail" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idPais" class="col-sm-3 col-form-label">Pais de Nacimiento</label>
                            <div class="col-sm-4">
                                <select id="idPais" name="idPais" class="form-control select2" data-live-search="true" onchange="cargarProvincias();" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($paises as $pais)
                                    <option value="{{$pais->pais_id}}">{{$pais->pais_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="idNacionalidad" class="col-sm-2 col-form-label">Nacionalidad</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="idNacionalidad" name="idNacionalidad" placeholder="Nacionalidad" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idProvincia" class="col-sm-3 col-form-label">Provincia de Recidencia</label>
                            <div class="col-sm-4">
                                <select id="idProvincia" name="idProvincia" class="form-control select2" data-live-search="true" onchange="cargarCiudades();" disabled required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                </select>
                            </div>
                            <label for="idCiudad" class="col-sm-1 col-form-label">Ciudad</label>
                            <div class="col-sm-4">
                                <select id="idCiudad" name="idCiudad" class="form-control select2" data-live-search="true" disabled required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                </select>
                            </div>
                        </div>                        
                        <div class="form-group row">
                            <label for="idAseguradora" class="col-sm-3 col-form-label">Aseguradora</label>
                            <div class="col-sm-9">
                                <select id="idAseguradora" name="idAseguradora" class="form-control select2" onchange="cargarAseguradoras();" required>
                                    <option value="" label>--Seleccione una opcion una opcion--</option>
                                    @foreach($clientesAseguradoras as $clienteAseguradora)
                                        <option value="{{$clienteAseguradora->cliente_id}}">{{$clienteAseguradora->cliente_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idEntidad" class="col-sm-3 col-form-label">Empresa</label>
                            <div class="col-sm-9">
                                <select id="idEntidad" name="idEntidad"  class="form-control select2" disabled required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                </select>
                            </div>
                        </div>
                    </div>
    </div>

</form>
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
    function cargarAseguradoras() {
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
                    document.getElementById("idEntidad").innerHTML += "<option value='" + data[i].entidada_id + "'>" + data[i].entidad_nombre + "</option>";
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
                        document.getElementById("idProvincia").innerHTML += "<option value='" + data[i].provincia_id + "'>" + data[i].provincia_nombre + "</option>";
                    }   
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
                        document.getElementById("idCiudad").innerHTML += "<option value='" + data[i].ciudad_id + "'>" + data[i].ciudad_nombre + "</option>";
                    }
                }
            },
            error: function(data) {
                document.getElementById("idCiudad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            },
        });
    }
</script>
@endsection