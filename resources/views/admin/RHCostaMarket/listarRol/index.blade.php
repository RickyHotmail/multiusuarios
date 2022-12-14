@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Roles</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal"  method="POST" action="{{ url("listaRolCM") }} ">
        @csrf 
            <div class="float-right">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
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
                        <option value="0" label>--TODOS--</option>                       
                        @foreach($empleado as $empleado)
                            <option id="{{$empleado->empleado_nombre}}" name="{{$empleado->empleado_nombre}}" value="{{$empleado->empleado_id}}">{{$empleado->empleado_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
               
            </div>
            <div class="form-group row">
            <label for="sucursal" class="col-sm-1 col-form-label"><center>Sucursales:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal" name="sucursal" >
                        <option value="0" >--TODOS--</option> 
                        @foreach($sucursales as $sucursal)
                            <option  value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div> 
            </div>
            <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center">
                        <th></th>
                        <th>Tipo de Rol</th>
                        <th>Empleado</th>
                        <th>Fecha</th>
                        <th>Sueldo</th>
                        <th>Pago</th>  
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)   
                        <tr>  
                            <td class="text-center">
                            @if($datos[$i]["tipo"]=='OPERATIVO') <a href="{{url("roloperativoCM/{$datos[$i]["idrol"]}/eliminar")}}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a> @ENDIF 
                            @if($datos[$i]["tipo"]=='INDIVIDUAL') <a href="{{url("rolindividualCM/{$datos[$i]["idrol"]}/eliminar")}}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a> @ENDIF    
                            @if($datos[$i]["tipo"]=='OPERATIVO') <a href="{{url("roloperativoCM/{$datos[$i]["idrol"]}")}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a> @ENDIF 
                            @if($datos[$i]["tipo"]=='INDIVIDUAL' || $datos[$i]["tipo"]=='CONSOLIDADO') <a href="{{url("rolindividualCM/{$datos[$i]["idrol"]}")}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>@ENDIF                   
                            @if($datos[$i]["cheque"]==1)
                                @if($datos[$i]["tipo"]=='OPERATIVO') <a href="{{url("roloperativoCM/{$datos[$i]["idrol"]}/cambiocheque")}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Cambio Cheque"><i class="fas fa-money-check-alt"></i> </a> <a href="{{ url("roloperativoCM/{$datos[$i]["idrol"]}/anular") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Anular"><i class="fa fa-ban" aria-hidden="true"></i></a>@ENDIF 
                                @if($datos[$i]["tipo"]=='INDIVIDUAL' || $datos[$i]["tipo"]=='CONSOLIDADO') <a href="{{url("rolindividualCM/{$datos[$i]["idrol"]}/cambiocheque")}}"  class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Cambio Cheque"><i class="fas fa-money-check-alt"></i> </a>  <a href="{{ url("rolindividualCM/{$datos[$i]["idrol"]}/anular") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Anular Cheque"><i class="fa fa-ban" aria-hidden="true"></i></a>@ENDIF                                 
                            @endif
                            <a href="{{url("rolCM/{$datos[$i]["idrol"]}/imprimir")}}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir Rol"><i class="fa fa-print"></i></a>                   
                            <a href="{{url("rolCM/{$datos[$i]["idrol"]}/imprimirdiario")}}" target="_blank" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Imprimir Asiento"><i class="fa fa-print"></i></a>   
                            <a href="{{url("rolCM/{$datos[$i]["idrol"]}/imprimirdiariocontabilizado")}}" target="_blank" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Imprimir Diario Contabilizado"><i class="fa fa-print"></i></a>       
                        </td>      
                            <td class="text-center">{{ $datos[$i]["tipo"]}}</td>                         
                            <td class="text-center">{{ $datos[$i]["nombre"]}}</td>
                            <td class="text-center">{{ $datos[$i]["fecha"]}}</td>

                            <td class="text-center">{{ number_format($datos[$i]["sueldo"],2)}}</td>
                            <td class="text-center">{{ $datos[$i]["pago"]}}</td>    
                        
                            
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </form>
    </div>
</div>

<script>
   
    if('<?php echo($nombre_empleado); ?>'){  
        document.getElementById("nombre_empleado").value='<?php echo($nombre_empleado); ?>';
    }
    if('<?php echo($fecha_todo); ?>'){  
        document.getElementById("fecha_todo").checked=true;
    }
    if('<?php echo($fecha_desde); ?>'){  
        document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
    }
    if('<?php echo($fecha_hasta); ?>'){  
        document.getElementById("fecha_hasta").value='<?php echo($fecha_hasta); ?>';
    }
    if('<?php echo($sucursalid); ?>'){  
        document.getElementById("sucursal").value='<?php echo($sucursalid); ?>';
    }
    
    
</script>

@endsection

