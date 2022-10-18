@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Roles</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal"  method="POST" action="{{ url("reporteRol") }} ">
        @csrf 
            <div class="float-right">
                    <button type="submit" id="enviar" name="enviar" class="btn btn-secondary float-right"><i class="fa fa-save">Generar</i></button>
                   
            </div>   
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                
                <label for="nombre_empleado" class="col-sm-1 col-form-label"><center>empleado:</center></label>
                <div class="col-sm-5">
                    <select class="custom-select select2" id="nombre_empleado" name="nombre_empleado" >
                        <option value="0" >--TODOS--</option>                       
                        @foreach($empleado as $empleado)
                            <option  value="{{$empleado->empleado_id}}">{{$empleado->empleado_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>  
            </div>
            <div class="form-group row">
            <label for="sucursal" class="col-sm-1 col-form-label"><center>sucursales:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal" name="sucursal" >
                        <option value="0" >--TODOS--</option> 
                        @foreach($sucursales as $sucursal)
                            <option  value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div> 
            </div>
            <div class="card-body table-responsive p-0" style="height: 600px;">
                <table id="tablaingresos" class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr class="text-center">
                            <th></th>
                            <th>rubros</th>
                            <th>tipo</th> 
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($rubros as $rubro)
                        <tr class="text-center">
                                <td>   
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" id="item{{$rubro->rubro_id}}"  name="contador[]"  value="{{ $rubro->rubro_id }}" > 
                                        <label for="item{{$rubro->rubro_id}}">
                                        </label>
                                    </div>
                                </td>
                                <td>{{ $rubro->rubro_descripcion}}</td>     
                                <td>
                                    @if($rubro->rubro_tipo =='1')EGRESOS 
                                        @elseif ($rubro->rubro_tipo =='2')INGRESOS 
                                        @elseif ($rubro->rubro_tipo =='3')PROVISIONES  
                                        @elseif ($rubro->rubro_tipo =='4')OTROS  
                                    @endif
                                </td>
                            </tr>
                            <tr class="text-center">
                                @endforeach     
                                <td> 
                                  
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" id="ReservaPagado"  name="contador[]"  value="ReservaPagado" > 
                                        <label for="ReservaPagado">
                                        </label>
                                    </div>
                                </td>
                                <td>Fondo Reserva Pagado</td>     
                                <td>
                                    OTROS
                                </td>   
                            </tr> 
                            <tr class="text-center">
                                <td> 
                                    
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" id="TerceroPagado"  name="contador[]"  value="TerceroPagado" > 
                                        <label for="TerceroPagado">
                                        </label>
                                    </div>
                                </td>    
                                <td>Decimo Tercero Pagado</td>     
                                <td>
                                    OTROS
                                </td> 
                            </tr>
                            <tr class="text-center">  
                                <td>            
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" id="CuartoPagado"  name="contador[]"  value="CuartoPagado" > 
                                        <label for="CuartoPagado">
                                        </label>
                                    </div>
                                </td>
                                <td>Decimo Cuarto Pagado</td>     
                                <td>
                                    OTROS
                                </td> 
                            </tr>           
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
   
   <?php if(isset($nombre_empleado)) {  
        echo('document.getElementById("nombre_empleado").value=').$nombre_empleado; 
    } 
    if (isset($fecha_todo)) {
        echo('document.getElementById("fecha_todo").checked=true;');
    }
    if (isset($fecha_desde)) {
        echo('document.getElementById("fecha_desde").value=').$fecha_desde; 
    }
    if (isset($fecha_hasta)) {
        echo('document.getElementById("fecha_hasta").value=').$fecha_hasta; 
    }
    if (isset($sucursalid)) {
        echo('document.getElementById("sucursal").value=').$sucursalid; 
    }
    ?>

</script>

@endsection

