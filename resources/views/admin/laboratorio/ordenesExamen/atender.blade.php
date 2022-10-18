@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
   
    <form  method="POST" action="{{ url("analisisLaboratorio") }}">
    @csrf
    <div class="card-header">
        <h3 class="card-title">Atencion de Analisis de Laboratario</h3>
        <div class="float-right">
            <button type="submit" name="guardar" id="guardar" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
            <button type="button" onclick='window.location = "{{ url("analisisLaboratorio") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        </div>
    </div>
    <div class="row">
        <!-- Tabla de empelados -->
            <div class="col-sm-2">
                <div class="card card-secondary " style="height: 700px;">
                    <div class="card-header">
                        <h3 class="card-title">Tipo de Analisis</h3>
                    </div>
                    <div class="card-body">
                        <table id="tableBuscar" class="table table-hover table-responsive">
                            <thead class="invisible">
                                <tr class="text-center">
                                    <th><input type="hidden" id="idanalisis" name="idanalisis" value="{{$analisis->analisis_laboratorio_id}}"></th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analisis->detalles as $laboratorio)
                                <tr>
                                    <td class="filaDelgada30"><div class="custom-control custom-radio"><input type="radio" class="custom-control-input" id="{{ $laboratorio->producto->producto_id}}"  onchange="this.form.submit()" name="radioempleado" value="{{  $laboratorio->producto->producto_id}}" @if(isset($idchek)) @if($laboratorio->producto->producto_id == $idchek) checked @endif @endif > <label for="{{  $laboratorio->producto->producto_id}}" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">{{  $laboratorio->producto->producto_nombre}}</label></div>                                
                                        @if($laboratorio->detalle_estado=='1')
                                        <span class="badge bg-danger">Subir</span>
                                        @else
                                        <span class="badge bg-success">Enviado</span>
                                        @endif
                                </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                   

                </div>
            </div>
            <div class="col-sm-10">
               
                <div class="row">
                    
                    <div class="card-body table-responsive p-0" > 
                      
                            <table id="tabla" class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th class="text-center-encabesado">CONCEPTO</th>
                                        <th class="text-center-encabesado">RESULTADOS</th>
                                        <th class="text-center-encabesado">ABREVI.</th>
                                        <th class="text-center-encabesado">MEDIDA</th>
                                        <th class="text-center-encabesado">MIN REF.</th>
                                        <th class="text-center-encabesado">MAX REF.</th>
                                    </tr>
                                </thead>
                                <tbody>
                               
                                
                                @if(isset($datos))
                                    @for ($i = 1; $i <= count($datos); ++$i) 
                                    <?php  $activador=0;?>
                                       <tr>
                                            <td class="text-center">{{ $datos[$i]['item1'] }} <input type="hidden" id="detalle_Valor[]" name="detalle_Valor[]" value="{{ $datos[$i]['item1'] }}">
                                            <input type="hidden" id="detalle_Valorid[]" name="detalle_Valorid[]" value="{{ $datos[$i]['item2'] }}"></td>
                                            @if(isset($datos2))
                                                @for ($j = 1; $j <= count($datos2); ++$j) 
                                                    @if($datos2[$j]['detalle']==$datos[$i]['item2'])
                                                        @if($datos2[$j]['text']=='1')
                                                           <?php  $activador=1;?>
                                                        @endif 
                                                    @endif
                                                @endfor
                                            @endif
                                            @if($activador==1)
                                                <td> <input type="text"  class="form-control" id="valores[]" name="valores[]" value="@if(isset($datos[$i]['item3'])){{$datos[$i]['item3']}}@endif" required> </td>
                                            @else
                                                <td> 
                                                    <select class="custom-select" id="valores[]" name="valores[]"  require>
                                            @if(isset($datos2))
                                                @for ($j = 1; $j <= count($datos2); ++$j) 
                                                    @if($datos2[$j]['detalle']==$datos[$i]['item2'])
                                                        <option @if(isset($datos[$i]['item3']))@if($datos[$i]['item3']==$datos2[$j]['nombre']) selected @endif @endif value="{{$datos2[$j]['nombre']}}" >{{$datos2[$j]['nombre']}}</option>
                                                    @endif
                                                @endfor
                                            @endif
                                                    </select> 
                                                </td>
                                            @endif
                                                <td class="text-center"> {{ $datos[$i]['abreviatura'] }} </td>
                                                <td class="text-center">{{ $datos[$i]['Medida'] }} <input type="hidden"  class="form-control" id="unidad[]" name="unidad[]" value="{{ $datos[$i]['Medida'] }}"> </td>
                                                <td class="text-center"> {{ $datos[$i]['Max'] }} </td>
                                                <td class="text-center">{{ $datos[$i]['Min'] }} </td>
                                        </tr>
                                    @endfor
                                @endif
                                </tbody>
                            </table>
                        
                    </div>
                </div>   
            </div> 
        
    </div>
    </form>   
    <!-- /.card-body -->
</div>
<!-- /.modal -->
<script type="text/javascript">
  
    function agregar() {
        $.ajax({
        url: '{{ url("laboratorio/searchN") }}'+'/'+$('input:radio[name=radioempleado]:checked').val(),
        dataType: "json",
        async:false,
        type: "GET",
        data: {
            buscar: $('input:radio[name=radioempleado]:checked').val()
        },
        success: function(data){
            $("#tabla > tbody").html("");
            for (var i=0; i<data.length; i++) {
                var linea = $("#plantillaItem").html();
                    linea = linea.replace(/{valor}/g, data[i].detalle_nombre );
                    linea = linea.replace(/{valorid}/g, data[i].detalle_id );
                    $.ajax({
                        url: '{{ url("laboratoriovalores/searchN") }}'+ '/' +data[i].detalle_id,
                        dataType: "json",
                        type: "GET",
                        async:false,
                        data: {
                            buscar: data[i].detalle_id
                        },
                        success: function(data){
                            if(data.length<=0){
                                linea =linea.replace(/{valor2}/g, '<input type="text" id="valores[]" name="valores[]" value="" class="form-control"  required >' );
                            }
                            else{
                                var select='';
                                for (var i=0; i<data.length; i++) {
                                    select+='<option value="'+data[i].valor_id+'" >'+data[i].valor_nombre+'</option>';
                                }
                                linea =linea.replace(/{valor2}/g, '<select class="custom-select" id="valores[]" name="valores[]"  require>'+select+'</select>' );                                
                            }
           
                        },
                    });
                    
                    $("#tabla tbody").append(linea);         
            }           
        },
    });
    }
    function clickButton(){

    $.ajax({
            type:"post",
            url:'{{ url("analisisLaboratorio") }}',
            data: 
            {  
               'idanalisis' :document.getElementById('idanalisis').value,
              
            },
            cache:false,
            success: function (html) 
            {
               alert('Data Send');
               $('#msg').html(html);
            }
            });
            return false;
     }
</script>
@endsection
