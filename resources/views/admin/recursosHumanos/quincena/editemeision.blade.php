@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <form class="form-horizontal" method="POST" action="{{ url("lquincena/puntomision") }} ">
    @csrf
    <div class="card-header">
        <h3 class="card-title">Lista de Quincenas Sin Punto de Emsion</h3>   
        <div class="float-right">
            <button type="submit" class="btn btn-success btn-sm ">Guardar</button> 
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        
             
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-5">
                    <select class="custom-select select2" id="sucursal_id" name="sucursal_id" onchange="CargarPunto();">
                        <option value="" label>Selecione la Sucursal</option>                       
                        @foreach($sucursales as $sucursal)
                            <option id="{{$sucursal->sucursal_nombre}}{{$sucursal->sucursal_nombre}}" name="{{$sucursal->sucursal_nombre}}{{$sucursal->sucursal_id}}" value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <label for="puntomision" class="col-sm-1 col-form-label"><center>Punto Emision:</center></label>
                <div class="col-sm-5">
                    <select class="custom-select select2" id="puntomision" name="puntomision" >
                        
                     
                    </select>  
                    <input  type="hidden" name="idrango" id="idrango">                                   
                </div>
            </div>
         
            <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center">
                    <th><div class="icheck-primary d-inline"><input type="checkbox" id="checkboxPrimary1" value="select" onClick="selecttotal()">
                                <label for="checkboxPrimary1"></div> </th>
                        <th>Empleado</th>
                        <th>Fecha</th>
                        <th>Valor</th>
                        <th>Tipo</th>
                        <th>Descripcion</th>
                        <th>Estado</th>
                     
                    </tr>
                </thead>
                <tbody>
                    @if(isset($quincena))
                        @foreach($quincena as $x)
                        <tr>  
                            <td>
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="item{{$x->quincena_id}}"  name="checkbox[]" value="{{ $x->quincena_id}}">
                                <label for="item{{$x->quincena_id}}">
                                </label>
                               
                            </div>
                                </td>                                 
                            <td class="text-center">{{ $x->empleado->empleado_nombre}}</td>
                            <td class="text-center">{{ $x->quincena_fecha}}</td>
                            <td class="text-center">{{ $x->quincena_valor}}</td>
                            <td class="text-center">{{ $x->quincena_tipo}}</td>
                            <td class="text-center">{{ $x->quincena_descripcion}}</td>
                            <td class="text-center">
                                    @if( $x->quincena_estado ==0) Anulado @endif 
                                    @if( $x->quincena_estado ==1) Pendiente descontar @endif 
                                    @if( $x->quincena_estado ==2) Descontado @endif            
                            </td>    
                      
                           
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        
        </div>
    </form>
</div>
<!-- /.card -->
<script>
 
 function CargarPunto(){
    document.getElementById("puntomision").innerHTML="";
    $.ajax({
        url: '{{ url("puntomision/searchN") }}'+ '/' +document.getElementById("sucursal_id").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("sucursal_id").value
        },
        success: function(data){
            
            for (var i=0; i<data.length; i++) {
                document.getElementById("puntomision").innerHTML += "<option value='"+data[i]["id"]+"'>"+data[i]["descripcion"]+"</option>";   
              
            }           
        },
    });
}  
    
</script>

@endsection

