@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de vacaciones</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("lvacaciones") }} "> 
        @csrf
         
        <hr>
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
            <div class="col-sm-1">
                <div class="icheck-success">
                    <input type="checkbox" id="fecha_todo" name="fecha_todo">
                    <label for="fecha_todo" class="custom-checkbox"><center>Todo</center></label>
                </div>                    
            </div>
            <label for="nombre_empleado" class="col-sm-1 col-form-label"><center>empleado:</center></label>
            <div class="col-sm-4">
                <select class="custom-select select2" id="nombre_empleado" name="nombre_empleado" >
                    <option value="--TODOS--" label>--TODOS--</option>                       
                    @foreach($empleado as $empleado)
                        <option id="{{$empleado->empleado_nombre}}" name="{{$empleado->empleado_nombre}}" value="{{$empleado->empleado_id}}">{{$empleado->empleado_nombre}}</option>
                    @endforeach
                </select>                                     
            </div>
        </div>
        <div class="form-group row">
            <label for="estados" class="col-sm-1 col-form-label"><center>Estados:</center></label>
            <div class="col-sm-4">
                <select class="custom-select" id="estados" name="estados" >  
                    <option value="--TODOS--" label>--TODOS--</option>                     
                    @foreach($estados as $estado)
                        <option id="{{$estado->vacacion_estado}}" name="{{$estado->vacacion_estado}}" value="{{$estado->vacacion_estado}}">
                        @if ($estado->vacacion_estado ==0)
                            Anulado
                        @endif
                        @if ($estado->vacacion_estado ==1)
                            Pendiente Descontar
                        @endif
                        @if ($estado->vacacion_estado ==2)
                            Descontado
                        @endif
                                    
                        </option>
                    @endforeach
                </select>                                     
            </div> 
        </div>
        <div class="card-body">
            <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center">
                        <th></th>
                        <th>Numero</th>
                        <th>Empleado</th>
                        <th>Fecha</th>
                        <th>Valor</th>
                        <th>Tipo</th>
                        <th>Descripcion</th>
                        <th>Estado</th>
                     
                    </tr>
                </thead>
                <tbody>
                    @if(isset($vacacion))
                        @foreach($vacacion as $x)
                        <tr>  
                            <td class="text-center"> 

                            <a href="{{ url("vacacion/{$x->vacacion_id}/ver") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>                   
                            @if(!isset($x->detalle_rol_id))
                            <a href="{{ url("vacacion/{$x->vacacion_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>                        
                            @endif
                            @if($x->vacacion_tipo=='Cheque')
                            <a href="{{ url("vacacion/{$x->vacacion_id}/anular") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Anularlo"><i class="fa fa-ban" aria-hidden="true"></i></a>                        
                            @endif
                            <a href="{{ url("vacacion/{$x->vacacion_id}/imprimir") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir Diario"><i class="fa fa-print"></i></a>                   
                            @if($x->vacacion_tipo=="Cheque" )
                                @foreach($x->diario->detalles as $i)
                                    @if($i->cheque)
                                        <a href="{{ url("/cheque/imprimir/{$i->cheque->cheque_id}") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir Cheque"><i class="fas fa-money-check-alt" aria-hidden="true"></i></a>                        
                                    @endif   
                                @endforeach
                            @endif
                            </td>           
                            <td class="text-center">{{ $x->vacacion_numero}}</td>                    
                            <td class="text-center">{{ $x->empleado->empleado_nombre}}</td>
                            <td class="text-center">{{ $x->vacacion_fecha}}</td>
                            <td class="text-center">{{ $x->vacacion_valor}}</td>
                            <td class="text-center">{{ $x->vacacion_tipo}}</td>
                            <td class="text-center">{{ $x->vacacion_descripcion}}</td>
                            <td class="text-center">
                                    @if( $x->vacacion_estado ==0) Anulado @endif 
                                    @if( $x->vacacion_estado ==1) Pendiente descontar @endif 
                                    @if( $x->vacacion_estado ==2) Descontado @endif            
                            </td>    
                      
                           
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    </form>
</div>
<!-- /.card -->
<script>
   if('<?php echo($estadoactual); ?>'){  
        document.getElementById("estados").value='<?php echo($estadoactual); ?>';
    }
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
    
    
</script>

@endsection

