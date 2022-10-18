@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("valorReferencial") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Nuevo valor referencial</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("examen/{$detalle_id->examen_id}/agregarValores") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="modal-body">
            <div class="card-body">
                <div class="row">
                    
                    <div class="col-sm-3 col-form-label" >
                        <label>Columna 1</label>
                        <div class="">                                
                            <input type="text" class="form-control" id="valor_columna1" name="valor_columna1" placeholder="Columna 1">
                            <input class="invisible" id="detalle_id" name="detalle_id" value="{{$detalle_id->detalle_id}}">
                            <input class="invisible" id="examen_id" name="examen_id" value="{{$detalle_id->examen_id}}">
                        </div>
                    </div>
                    <div class="col-sm-2 col-form-label" >
                        <label>Columna 2</label>
                        <div class="">                                
                            <input type="text" class="form-control" id="valor_columna2" name="valor_columna2" placeholder="Columna 2">
                        </div>
                    </div>
                    
                    <div class="col-sm-1 col-form-label" align="right">
                        <a onclick="agregarItem();" class="btn btn-primary btn-venta"><i class="fas fa-plus"></i></a>
                    </div>
                    <div class="col-sm-12 col-form-label" style="margin-bottom: 0px;">
                    <br>
                        <div class="table-responsive">
                            @include ('admin.citasMedicas.examen.itemNuevoValorRef')
                            <table id="cargarItemNuevoValorRef" class="table table-striped table-hover" style="margin-bottom: 6px;">
                                <thead>
                                    <tr class="letra-blanca fondo-azul-claro text-center">
                                        <th>Columna 1</th> 
                                        <th>Columna 2</th>                                              
                                        <th width="10"></th>
                                    </tr>
                                </thead>
                                <tbody> 
                                        <?php $cont = 1;?>
                                        @foreach($detallesLaboratorio as $detallesLaboratori)
                                            <tr class="text-center"  id="row_<?php echo $cont; ?>">      
                                                <td>{{ $detallesLaboratori->valor_Columna1 }}<input class="invisible" name="Rcolumna1[]" value="{{  $detallesLaboratori->valor_Columna1 }}" /></td> 
                                                <td>{{ $detallesLaboratori->valor_Columna2 }}<input class="invisible" name="Rcolumna2[]" value="{{  $detallesLaboratori->valor_Columna2 }}" /><input class="invisible" name="VdetalleId[]" value="{{  $detallesLaboratori->detalle_id }}" />
                                                <input class="invisible" id="valorL_id" name="valorL_id" value="{{$detallesLaboratori->valor_id}}"></td>           
                                                <td><a onclick="eliminarItem(<?php echo $cont; ?>);" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
                                                <?php $cont = $cont + 1;?>                                   
                                            </tr>
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
           
           
            var linea = $("#plantillaItemNuevoValorReferencial").html();
          
            linea = linea.replace(/{ID}/g, id_item);
           
            linea = linea.replace(/{Rcolumna1}/g, document.getElementById("valor_columna1").value);
            linea = linea.replace(/{Rcolumna2}/g, document.getElementById("valor_columna2").value);
        
            linea = linea.replace(/{VdetalleId}/g, document.getElementById("detalle_id").value);

            $("#cargarItemNuevoValorRef tbody").append(linea);
            id_item = id_item + 1; 
     
            resetearCampos();
            
    }
    function resetearCampos() {
        document.getElementById("valor_columna1").value = "";
        document.getElementById("valor_columna2").value = "";
     
    }
    function eliminarItem(id) {
        $("#row_" + id).remove();
        id_item = id_item - 1;
    }
</script>
@endsection