@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("especialidad/configuracionEspecialidad/guardar") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Configurar Especialidad</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("especialidad") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body" id="formularioConfiguracion">
            <div class="form-group row">
                <label for="especialidad_nombre" class="col-sm-1 col-form-label">Nombre :</label>
                <div class="col-sm-9">
                    <input type="hidden" name="especialidad_id" value="{{ $especialidad->especialidad_id }}"/>
                    <label class="form-control">{{$especialidad->especialidad_nombre}}</label>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-info" onclick="agregarItem(1);" data-toggle="tooltip" data-placement="top" title="Agregar Campo de Texto"><i class="fa fa-font"></i></button>
                    <button type="button" class="btn btn-info" onclick="agregarItem(2);" data-toggle="tooltip" data-placement="top" title="Agregar Campo Numerico"><i class="fas fa-sort-numeric-down"></i></button>
                    <!--<button type="button" class="btn btn-info" onclick="agregarItem(3);" data-toggle="tooltip" data-placement="top" title="Agregar Campo de Odontograma"><i class="far fa-file-image"></i></button>-->
                </div>
            </div> 
            <div id='CTexto' class="invisible">
                <div class="callout callout-info" style="background: #e5e5e5;" id='CTexto{ID}'>
                    <div class="row form-group">
                        <div class="col-sm-3">
                            <input class="form-control" name="Dcampo[]" placeholder="Nombre del Campo" {re}/><input type="hidden" name="Dtipo[]" value="{tipo}"/>
                        </div>
                        <div class="col-sm-9">
                            <button type="button" onclick="eliminarItem({ID},1);" class="btn btn-danger waves-effect float-right" style="padding: 2px 8px;">X</button>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <textarea class="form-control" rows='3'></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div id='CNumber' class="invisible">
                <div class="callout callout-info" style="background: #e5e5e5;" id='CNumber{ID}'>
                    <div class="row form-group">
                        <div class="col-sm-3">
                            <input class="form-control" name="Dcampo[]" placeholder="Nombre del Campo" {re}/><input type="hidden" name="Dtipo[]" value="{tipo}"/>
                        </div>
                        <div class="col-sm-9">
                            <button type="button" onclick="eliminarItem({ID},2);" class="btn btn-danger waves-effect float-right" style="padding: 2px 8px;">X</button>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-10">
                            <input class="form-control"/>
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" name="Dunidad[]" placeholder="Unidad" {re}/>
                        </div>
                    </div>
                </div>
            </div>
            <div id='CImagen' class="invisible">
                <div class="callout callout-info" style="background: #e5e5e5;" id='CImagen{ID}'>
                    <div class="row form-group">
                        <div class="col-sm-3">
                            <input class="form-control" name="Dcampo[]" placeholder="Nombre del Campo" {re}/><input type="hidden" name="Dtipo[]" value="{tipo}"/>
                        </div>
                        <div class="col-sm-9">
                            <button type="button" onclick="eliminarItem({ID},3);" class="btn btn-danger waves-effect float-right" style="padding: 2px 8px;">X</button>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <input type="file" name="DcampoUrl[]" {re}/>
                        </div>
                    </div>
                </div>
            </div>
            <?php $count = 1; ?>

            <div class="col-sm-3 form-group">
                <select name="tipo_detalle_consulta" id="tipo_detalle_consulta" class="form-control" onchange="location.href='{{ url('especialidad/configuracionEspecialidad') }}/{{ $especialidad_id }}?tipo='+this.value">
                    @foreach($tipos as $t)
                        <option value={{ $t->tipod_id }} @if($t->tipod_id==$tipod_id) selected @endif>{{ $t->tipod_descripcion }}</option>
                    @endforeach
                </select>
            </div>
            @foreach($configuracionEspecialidad as $config)
                @if($tipod_id==$config->tipod_id || $config->tipod_id==null)
                    @if($config->configuracion_tipo=='1')
                        <div class="callout callout-info" style="background: #e5e5e5;" id='CTexto{{ $count }}'>
                            <div class="row form-group">
                                <div class="col-sm-3">
                                    <input class="form-control" name="Dcampo[]" placeholder="Nombre del Campo" value="{{$config->configuracion_nombre}}" required/><input type="hidden" name="Dtipo[]" value="{{$config->configuracion_tipo}}"/>
                                </div>
                                <div class="col-sm-9">
                                    <button type="button" onclick="eliminarItem({{ $count }},1);" class="btn btn-danger waves-effect float-right" style="padding: 2px 8px;">X</button>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-12">
                                    <textarea class="form-control" rows='3'></textarea>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($config->configuracion_tipo=='2')
                        <div class="callout callout-info" style="background: #e5e5e5;" id='CNumber{{ $count }}'>
                            <div class="row form-group">
                                <div class="col-sm-3">
                                    <input class="form-control" name="Dcampo[]" placeholder="Nombre del Campo" value="{{$config->configuracion_nombre}}" required/><input type="hidden" name="Dtipo[]" value="{{$config->configuracion_tipo}}"/>
                                </div>
                                <div class="col-sm-9">
                                    <button type="button" onclick="eliminarItem({{ $count }},2);" class="btn btn-danger waves-effect float-right" style="padding: 2px 8px;">X</button>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-10">
                                    <input class="form-control"/>
                                </div>
                                <div class="col-sm-2">
                                    <input class="form-control" name="Dunidad[]" placeholder="Unidad" value="{{$config->configuracion_medida}}" required/>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>            
    </div>
</form>
<script type="text/javascript">
    id_item = '<?=$count?>';
    id_item = Number(id_item);
    function agregarItem(tipo) {
        var linea;
        if(tipo == 1){
            linea = $("#CTexto").html();
            linea = linea.replace(/{tipo}/g, 1);
        }
        if(tipo == 2){
            linea = $("#CNumber").html();
            linea = linea.replace(/{tipo}/g, 2);
        }
        if(tipo == 3){
            linea = $("#CImagen").html();
            linea = linea.replace(/{tipo}/g, 3);
        }
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{re}/g, 'required');
        $("#formularioConfiguracion ").append(linea);
        id_item = id_item + 1;
    }
    function eliminarItem(id, tipo) {
        if(tipo == 1){
            $("#CTexto" + id).remove();
        }
        if(tipo == 2){
            $("#CNumber" + id).remove();
        }
        if(tipo == 3){
            $("#CImagen" + id).remove();
        }
    }
</script>
@endsection