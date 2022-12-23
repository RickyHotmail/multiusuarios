@extends ('admin.layouts.admin')
@section('principal')

<style>
     .fa-upload{
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

    .lbl-cargar{
        height: 33px;
        margin: 0px
    }
    
    .td-paciente, .td-documento{
        display: flex;
        justify-items: center;
        height: 100%;
    }

    .ver-paciente, .ver-afiliado, .ver-documento{
        border-radius: 5px 0px 0px 5px;
    }


    /*
    .lbl-cargar{
        position: absolute;
        top: 50px;
        top: 100%;
        left: 100%;
        transform: translate(-100%, -100%);
        border-radius: 5px 0px 0px 0px;
        width: 100%;
    }
    */
    .fa-upload{
        border-radius: 0px 5px 5px 0px;
        height: 33px;
    }
</style>
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Ordenes de Atencion - Verificador de Documentos</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="GET" action="{{ url("verificarDocumentos") }}">
            @csrf
            
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label">Medicos:</label>

                <div class="col-sm-4">
                    <select name="medico_id" class="form-control">
                        <option value=0 @if($seleccionado==0) selected @endif >Todos</option>

                        @foreach($medicos as $medico)
                            <option value="{{ $medico->medico_id }}" @if($seleccionado==$medico->medico_id) selected @endif>
                                @if($medico->empleado)) 
                                    {{$medico->empleado->empleado_nombre}}
                                @elseif ($medico->proveedor))
                                    {{$medico->proveedor->proveedor_nombre}}
                                @else
                                    -
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="fecha_desde" class="col-sm-2 col-form-label">Desde:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                        </div>
                        <label for="fecha_desde" class="col-sm-2 col-form-label">Hasta:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                        </div>
                    </div>
                </div>
                <label for="idBanco" class="col-lg-1 col-md-1 col-form-label">Sucursal :</label>
                <div class="col-lg-4 col-md-4">
                    <select class="custom-select select2" id="sucursal_id" name="sucursal_id" required>
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($sucurslaC)) @if($sucurslaC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
        <table id="example1" class="table table-bordered table-hover">
            <thead>
                <tr class="text-center  neo-fondo-tabla">
                    <th>Orden</th>   
                    <th>Fecha/Hora</th>
                    <th>Paciente</th>
                    <th>Especialidad</th>
                    <!--th>Medico</th-->
                    <th>Personales</th>

                    @if($documentos!=null)
                        @foreach($documentos as $doc)
                            <th>{{ $doc->documento_nombre }}</th>
                        @endforeach
                    @endif
                </tr>
            </thead>
            <tbody>
            @if(isset($ordenesAtencion))
                @foreach($ordenesAtencion as $ordenAtencion)
                    <tr class="text-center">
                        <td>{{ $ordenAtencion->orden_numero }}&nbsp;
                            @if($ordenAtencion->orden_iess==1)
                                <img src="{{ asset('img/iess.png')  }}" width="50px">
                            @endif
                        </td>
                        <td>
                            {{ $ordenAtencion->orden_fecha }} <br>
                            {{ $ordenAtencion->orden_hora }}
                        </td>
                        <td>
                            {{ $ordenAtencion->paciente->paciente_apellidos}} <br>
                            {{ $ordenAtencion->paciente->paciente_nombres }}
                        </td>
                        <td>@if(isset($ordenAtencion->especialidad->especialidad_nombre )) {{$ordenAtencion->especialidad->especialidad_nombre}} @endif</td>
                        <td>
                            <div class="td-paciente">
                                @if($ordenAtencion->paciente->documento_paciente!=null)
                                    <a class="btn btn-sm btn-primary ver-paciente" id="verPaciente{{ $ordenAtencion->paciente_id }}" href="{{ $ordenAtencion->paciente->documento_paciente }}" target="_blank" data-toggle="tooltip" data-original-title="Ver Documento">Paciente &nbsp;&nbsp;<i class="fas fa-eye"></i></a>
                                @else
                                    <a class="btn btn-xs btn-danger ver-paciente" id="verPaciente{{ $ordenAtencion->paciente_id }}" target="_blank">Paciente &nbsp;&nbsp;<i id="i-paciente{{ $ordenAtencion->paciente_id }}" class="fas fa-exclamation-triangle"></i></a>
                                @endif

                                <label for="documentoPaciente{{ $ordenAtencion->paciente_id }}" class="lbl-cargar">
                                    <i class='fa fa-upload' aria-hidden='true'></i>
                                </label>
                                <input onchange="subirDocumentoPaciente('paciente',{{ $ordenAtencion->paciente_id }});" style="display: none" class="foto" id="documentoPaciente{{ $ordenAtencion->paciente_id }}" data-toggle="tooltip" data-placement="top" title="Subir escaneado" name="fotoDocumento" type="file"  accept=".pdf">

                                @if($ordenAtencion->paciente->paciente_dependiente==1)
                                    @if($ordenAtencion->paciente->documento_afiliado!=null)
                                        <a class="btn btn-sm btn-primary ver-afiliado" href="{{ $ordenAtencion->paciente->documento_afiliado }}" target="_blank" data-original-title="Ver Documento">
                                            Afiliado &nbsp;&nbsp;
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @else
                                        <a class="btn btn-xs btn-danger ver-afiliado">
                                            Afiliado &nbsp;&nbsp;
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </a>
                                    @endif

                                    <label for="documentoAfiliado{{ $ordenAtencion->paciente_id }}"><i class='fa fa-upload lbl-cargar' aria-hidden='true'></i></label>
                                    <input onchange="subirDocumentoPaciente('afiliado',{{ $ordenAtencion->paciente_id }});" style="display: none" class="foto" id="documentoAfiliado{{ $ordenAtencion->paciente_id }}" data-toggle="tooltip" data-placement="top" title="Subir escaneado" name="fotoDocumento" type="file"  accept=".pdf">
                                @endif
                            </div>
                        </td>
                        @if($documentos!=null)
                            @foreach($documentos as $docEmp)
                                <td>
                                    <div class="td-documento">
                                        <?php $encontrado=false; ?>
                                        @foreach($ordenAtencion->documentos as $docOrden)
                                            @if($docEmp->documento_id==$docOrden->documento_id)
                                                <a class="btn btn-sm btn-success ver-documento" href="{{ $docOrden->doccita_url }}" id="verDoc{{$docEmp->documento_id}}{{$ordenAtencion->orden_id}}" target="_blank">
                                                    &nbsp;&nbsp;<i class="fas fa-eye" id="i-doc{{$docEmp->documento_id}}{{$ordenAtencion->orden_id}}"></i>&nbsp;&nbsp;
                                                </a>
                                                <?php $encontrado=true; ?>
                                                @break
                                            @endif
                                        @endforeach
                                        @if(!$encontrado)
                                            <a id="verDoc{{$docEmp->documento_id}}{{$ordenAtencion->orden_id}}" class="btn btn-sm btn-danger ver-documento" target="_blank">
                                                &nbsp;&nbsp;<i class="fas fa-exclamation-triangle" id="i-doc{{$docEmp->documento_id}}{{$ordenAtencion->orden_id}}"></i>&nbsp;&nbsp;
                                            </a>
                                        @endif
                                        
                                        <label for="docFile{{ $docEmp->documento_id }}{{$ordenAtencion->orden_id}}" class="lbl-cargar"><i class='fa fa-upload' aria-hidden='true'></i></label>
                                        <input onchange="subirDocumentoOrden({{$ordenAtencion->orden_id}}, {{ $docEmp->documento_id }});" style="display: none" class="foto" id="docFile{{ $docEmp->documento_id }}{{$ordenAtencion->orden_id}}" data-toggle="tooltip" data-placement="top" title="Subir escaneado" name="fotoDocumento" type="file"  accept=".pdf">
                                        <!--a class="btn btn-sm btn-success" style="display: none" id="verDoc{{$docEmp->documento_id}}" target="_blank">&nbsp;&nbsp;<i class="fas fa-eye"></i>&nbsp;&nbsp;</a-->
                                    </div>
                                </td>
                            @endforeach
                        @endif
                    </tr> 
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<script>
    function subirDocumentoPaciente(tipo, id){
        var formData = new FormData();

        if(tipo=="paciente")
            formData.append('documento', document.getElementById("documentoPaciente"+id).files[0]);
        else
            formData.append('documento', document.getElementById("documentoAfiliado"+id).files[0]);

        formData.append("_token","{{ csrf_token() }}")
        formData.append("paciente_id", id)
        formData.append("tipo", tipo)

        $.ajax({
            url: "{{ url('subirDocumentoPaciente') }}",
            method: "post",
            dataType: "json",
            contentType: false,
            processData: false,
            data: formData,
            success: function(data) {
                console.log(data)

                if(data.result="OK"){
                    if(tipo=="paciente"){
                        document.getElementById("verPaciente"+id).style.display="initial"
                        document.getElementById("verPaciente"+id).href=data.documento

                        document.getElementById("verPaciente"+id).classList.remove("btn-danger")
                        document.getElementById("verPaciente"+id).classList.add("btn-primary")

                        document.getElementById("i-paciente"+id).classList.remove("fa-exclamation-triangle")
                        document.getElementById("i-paciente"+id).classList.add("fa-eye")
                    }
                    else{
                        document.getElementById("verAfiliado"+id).style.display="initial"
                        document.getElementById("verAfiliado"+id).href=data.documento

                        document.getElementById("verAfiliado"+id).classList.remove("btn-danger")
                        document.getElementById("verAfiliado"+id).classList.add("btn-primary")

                        document.getElementById("i-afiliado"+id).classList.remove("fa-exclamation-triangle")
                        document.getElementById("i-afiliado"+id).classList.add("fa-eye")
                    }
                    //alert("Documento subido correctamente")
                    console.log(data)
                }
            }
        });
    }

    function subirDocumentoOrden(orden_id, documento_id){
        var formData = new FormData();
        
        formData.append('documento', document.getElementById("docFile"+documento_id+orden_id).files[0]);
        formData.append("_token","{{ csrf_token() }}")
        formData.append("orden_id", orden_id)
        formData.append("documento_id", documento_id)

        $.ajax({
            url: "{{ url('subirDocumentoOrden') }}",
            method: "post",
            dataType: "json",
            contentType: false,
            processData: false,
            data: formData,
            success: function(data) {
                console.log(data)

                if(data.result="OK"){
                    document.getElementById("verDoc"+documento_id+orden_id).style.display="initial"
                    document.getElementById("verDoc"+documento_id+orden_id).href=data.documento


                    document.getElementById("verDoc"+documento_id+orden_id).classList.remove("btn-danger")
                    document.getElementById("verDoc"+documento_id+orden_id).classList.add("btn-success")

                    document.getElementById("i-doc"+documento_id+orden_id).classList.remove("fa-exclamation-triangle")
                    document.getElementById("i-doc"+documento_id+orden_id).classList.add("fa-eye")
                }
            }
        });
    }
</script>
@endsection