@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
   
    <form  method="POST" action="{{ url("ordenImagen") }}/{{ $orden->orden_id }}/guardarImagenes" enctype="multipart/form-data">
        @csrf
        
        <div class="col-sm-12">
            <div class="card card-secondary ">
                <div class="card-header">
                    <h3 class="card-title">Subir Documentos de Imagenes</h3>
                    <div class="float-right">
                        <button type="submit" name="guardar" id="guardar" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                        <button type="button" onclick='window.location = "{{ url("ordenImagen") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                    </div>
                </div>
                <div class="p-5">
                    <div class="card-body">
                        <table>
                            <thead class="invisible">
                                <tr class="text-center">
                                    <th>
                                        <input type="hidden" id="orden_id" name="orden_id" value="{{ $orden->orden_id}} ">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orden->detalleImagen as $det)
                                <tr>
                                    <td class="filaDelgada30">
                                        <div class="row">
                                            <label class="mr-3" style="font-size: 18px; font-weight: bold !important;">- &nbsp; {{  $det->imagen->producto->producto_nombre}}</label>
                                            
                                            @if($det->detalle_estado==1)
                                                <label style="font-size: 18px; font-weight: normal">
                                                    estado: <a class="btn btn-sm btn-outline-warning"><i class="fas fa-exclamation-circle"></i>  PENDIENTE</a>
                                                </label>
                                            @else
                                                <label>
                                                    estado: <a class="btn btn-sm btn-outline-success"><i class="fa fa-check"></i>LISTO</a>
                                                </label>
                                            @endif
                                        </div>

                                        @if($det->detalle_estado=='1')
                                            <div class="row ml-5 mb-5">
                                                <img class="mr-3" src="{{ asset('/img/no_file.png') }}" style="width: 80px; height: 80px">
                                                
                                                <div style="margin-top: auto; margin-bottom: auto;">
                                                    <input name="imagenes_{{ $det->detalle_id }}[]" type="file" class="btn btn-sm btn-success" accept="application/pdf">
                                                </div>
                                            </div>
                                        @else
                                            <div class="row ml-5 mb-5">
                                                <img class="mr-3" src="{{ asset('/img/pdf_file.png') }}" style="width: 80px; height: 80px">
                                                <div style="margin-top: auto; margin-bottom: auto;">
                                                    <a class="btn btn-sm  btn-success">
                                                        <i class="fa fa-search"></i>
                                                        Ver Documento
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
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
