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


<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Paciente</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Cedula</th>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th>Direccion</th>
                    <th>Fecha de Naciemiento</th>
                    <th>Nacionalidad</th>
                    <th>Celular</th>
                    <th>E-mail</th>
                    <th>Sexo</th>
                    <th>Pais</th>
                    <th>Provincia</th>
                    <th>Ciudad</th>
                    <th>Aseguradora</th>
                    <th>Empresa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pacientes as $paciente)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("paciente/{$paciente->paciente_id}/edit")}}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("paciente/{$paciente->paciente_id}")}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $paciente->paciente_cedula }}</td>
                    <td>{{ $paciente->paciente_apellidos }}</td>
                    <td>{{ $paciente->paciente_nombres }}</td>
                    <td>{{ $paciente->paciente_direccion }}</td>
                    <td>{{ $paciente->paciente_fecha_nacimiento }}</td>
                    <td>{{ $paciente->paciente_nacionalidad }}</td>
                    <td>{{ $paciente->paciente_celular }}</td>
                    <td>{{ $paciente->paciente_email }}</td>
                    <td>{{ $paciente->paciente_sexo }}</td>
                    <td>{{ $paciente->pais_nombre }}</td>
                    <td>{{ $paciente->provincia_nombre }}</td>
                    <td>{{ $paciente->ciudad_nombre }}</td>
                    <td>{{ $paciente->cliente_nombre }}</td>
                    <td>{{ $paciente->entidad_nombre }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Paciente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("paciente") }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
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

                        <div  class="form-group row">
                            <label class="col-sm-3 col-form-label">Cedula del Paciente</label>

                            <div class="col-sm-9">
                                <img style="width: 200px;" src="{{ url('img') }}/up_document.png" id="fotoPacienteP"><br>

                                <label for="fotoPaciente" ><i class='fa fa-upload' aria-hidden='true'></i> Cargar</label>
                                <input class="foto" style="display: none;" id="fotoPaciente" name="fotoPaciente" type="file"  accept=".png, .jpg, .jpeg, .gif">
                            </div>
                        </div>

                        
                        <div class="form-group row" id="marcoFotoAfiliado" style="display: none">
                            <label class="col-sm-3 col-form-label">Cedula del Afiliado</label>

                            <div class="col-sm-9">
                                <img style="width: 200px;" src="{{ url('img') }}/up_document.png" id="fotoAfiliadoP"><br>

                                <label for="fotoAfiliado"><i class='fa fa-upload' aria-hidden='true'></i> Cargar</label>
                                <input class="foto" style="display: none;" id="fotoAfiliado" name="fotoAfiliado" type="file"  accept=".png, .jpg, .jpeg, .gif">
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
<!-- /.modal -->
<!-- /.script -->
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
                    document.getElementById("idEntidad").innerHTML += "<option value='" + data[i].entidad_id + "'>" + data[i].entidad_nombre + "</option>";
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