@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("detalle") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Nuevo valor laboratorio</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("examen") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="modal-body">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4 col-form-label" >
                        <label>Nombre</label>
                        <div class="">                                
                            <input type="text" class="form-control" id="valor_nombre" name="valor_nombre" placeholder="Nombre">
                            <input class="invisible" id="detalle_id" name="detalle_id" value="{{$detalle_id}}">
                        </div>
                    </div>
                    <div class="col-sm-1 col-form-label" align="right">
                        <a onclick="agregarItem();" class="btn btn-primary btn-venta"><i class="fas fa-plus"></i></a>
                    </div>
                    <a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                    <div class="col-sm-6 col-form-label" style="margin-bottom: 0px;">
                    @include ('admin.citasMedicas.examen.itemNuevoValorLab')
                        <div class="table-responsive">                        
                            <table id="cargarItemNuevoValorLab" class="table table-striped table-hover" style="margin-bottom: 6px;">
                                <thead>
                                    <tr class="letra-blanca fondo-azul-claro text-center">
                                        <th>Nombre</th>                                                
                                        <th width="10"></th>
                                    </tr>
                                </thead>
                                <tbody>   
                                    <?php $cont = 1;?>
                                        @foreach($valoresLab as $valorLab)
                                            @if($valorLab->detalle_id == $detalle_id)
                                            <tr class="text-center"  id="row_<?php echo $cont; ?>">      
                                                <td>{{ $valorLab->valor_nombre }}<input class="invisible" name="Vnombre[]" value="{{  $valorLab->valor_nombre }}" /><input class="invisible" name="VdetalleId[]" value="{{  $valorLab->detalle_id }}" />
                                                <input class="invisible" id="valorL_id" name="valorL_id" value="{{$valorLab->valor_id}}"></td>           
                                                <td><a onclick="eliminarItem(<?php echo $cont; ?>);" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
                                                <?php $cont = $cont + 1;?>                                   
                                            </tr>
                                            @endif
                                        @endforeach     
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>                                   
            </div>
        </div>
    </div>
</form>
<script>        
    if( <?php echo $cont;?> == 1){
        var id_item = 1;
    }else{
        id_item = <?php echo $cont;?>;
    }         
    function agregarItem() {           
        if(document.getElementById("valor_nombre").value != ''){     
            var linea = $("#plantillaItemNuevoValorLab").html();
            linea = linea.replace(/{ID}/g, id_item);
            linea = linea.replace(/{Vnombre}/g, document.getElementById("valor_nombre").value);
            linea = linea.replace(/{VdetalleId}/g, document.getElementById("detalle_id").value);
            linea = linea.replace(/{valorL_id}/g, document.getElementById("valorL_id").value);

            $("#cargarItemNuevoValorLab tbody").append(linea);
            id_item = id_item + 1;            
            resetearCampos();
        }        
    }
    function resetearCampos() {
        document.getElementById("valor_nombre").value = "";
    }
    function eliminarItem(id) {
        $("#row_" + id).remove();
        id_item = id_item - 1;
    }
</script>
@endsection