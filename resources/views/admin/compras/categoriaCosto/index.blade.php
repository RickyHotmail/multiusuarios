@extends ('admin.layouts.admin')
@section('principal')
<form method="POST" action="{{ url("categoriaCosto") }} ">
     @csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Categoria Costo</h3>
            <button type="submit" class="btn btn-default btn-sm float-right"><i class="fa fa-save"></i>&nbsp;Guardar</button>
        </div> 
        <div class="card-body">
        
                <table  class="table table-bordered table-hover table-responsive sin-salto">
                    <thead>
                        <tr class="text-center neo-fondo-tabla">
                            <th></th>
                            <th>Nombre</th>
                            <th>General</th>  
                            <th>Costo</th>  
                            <th>Racewas</th>  
                            <th>Sin Aplicación</th>  
                            <th>Visible</th>                        
                        </tr>
                    </thead> 
                    <tbody>
                        @foreach($categorias as $categoria)
                       
                        <tr class="text-center">
                            <td></td>
                            <td>{{ $categoria->categoria_nombre}} <input type="hidden" name="idcategoria[]" id="idcategoria[]" value="{{$categoria->categoria_id}}"></td>
                            
                            <td><input type="checkbox"  id="general{{$categoria->categoria_id}}" @if($categoria->categoriac_general=='1') checked @endif  onclick="check({{$categoria->categoria_id}})"> <input type="hidden" name="tgeneral[]" id="tgeneral{{$categoria->categoriac_id}}"  value="{{$categoria->categoriac_general}}"  > </td>  
                            <td><input type="checkbox" id="costo{{$categoria->categoria_id}}" @if($categoria->categoriac_costo=='1') checked @endif  onclick="check({{$categoria->categoria_id}})"> <input type="hidden" name="tcosto[]" id="tcosto{{$categoria->categoriac_id}}" value="{{$categoria->categoriac_costo}}" > </td> 
                            <td><input type="checkbox" id="racewas{{$categoria->categoria_id}}" @if($categoria->categoriac_racewas=='1') checked @endif  onclick="check({{$categoria->categoria_id}})"> <input type="hidden" name="tracewas[]" id="tracewas{{$categoria->categoriac_id}}" value="{{$categoria->categoriac_racewas}}"> </td> 
                            <td><input type="checkbox" id="aplicacion{{$categoria->categoria_id}}" @if($categoria->categoriac_sin_aplicacion=='1') checked @endif  onclick="check({{$categoria->categoria_id}})"> <input type="hidden" name="taplicacion[]" id="taplicacion{{$categoria->categoriac_id}}" value="{{$categoria->categoriac_sin_aplicacion}}"> </td> 
                            <td><input type="checkbox" id="visible{{$categoria->categoria_id}}" @if($categoria->categoriac_visible=='1') checked @endif  onclick="check({{$categoria->categoria_id}})"> <input type="hidden" name="tvisible[]" id="tvisible{{$categoria->categoriac_id}}" value="{{$categoria->categoriac_visible}}"> </td> 
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
        
        </div>   
    </div>
</form>
<script type="text/javascript">
    function check(id) {
       if(document.getElementById("general"+id).checked==true){
        document.getElementById("tgeneral"+id).value="1";
       }
       else{
        document.getElementById("tgeneral"+id).value="0";
       }
       if(document.getElementById("costo"+id).checked==true){
        document.getElementById("tcosto"+id).value="1";
       }
       else{
        document.getElementById("tcosto"+id).value="0";
       }
       if(document.getElementById("racewas"+id).checked==true){
        document.getElementById("tracewas"+id).value="1";
       }
       else{
        document.getElementById("tracewas"+id).value="0";
       }
       if(document.getElementById("aplicacion"+id).checked==true){
        document.getElementById("taplicacion"+id).value="1";
       }
       else{
        document.getElementById("taplicacion"+id).value="0";
       }
       if(document.getElementById("visible"+id).checked==true){
        document.getElementById("tvisible"+id).value="1";
       }
       else{
        document.getElementById("tvisible"+id).value="0";
       }

    }
</script>
@endsection