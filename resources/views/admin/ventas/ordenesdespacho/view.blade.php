@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("ordenDespacho/guia") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Lista de Ordenes de Despacho</h3>    
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="nombre_cliente" class="col-sm-1 col-form-label"><center>Cliente:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="nombre_cliente" name="nombre_cliente" >
                        <option value="--TODOS--" label>--TODOS--</option>                       
                        @foreach($clientes as $cliente)
                            <option id="{{$cliente->cliente_nombre}}" name="{{$cliente->cliente_nombre}}" value="{{$cliente->cliente_nombre}}">{{$cliente->cliente_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                </div>
                <div class="col-sm-1">
                    <div class="icheck-secondary">
                        <input type="checkbox" id="fecha_todo" name="fecha_todo">
                        <label for="fecha_todo" class="custom-checkbox"><center>Todo</center></label>
                    </div>                    
                </div>
                
            </div>
            <div class="form-group row">
                <label for="nombre_sucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal" name="sucursal" >
                        <option value="--TODOS--" label>--TODOS--</option>                       
                        @foreach($sucursal as $sucursales)
                            <option id="{{$sucursales->sucursal_nombre}}" name="{{$sucursales->sucursal_nombre}}" value="{{$sucursales->sucursal_nombre}}">{{$sucursales->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <label for="estados" class="col-sm-1 col-form-label"><center>Estados:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select" id="estados" name="estados" >  
                        <option value="--TODOS--" label>--TODOS--</option>                     
                        @foreach($estados as $estado)
                            <option id="{{$estado->orden_estado}}" name="{{$estado->orden_estado}}" value="{{$estado->orden_estado}}">
                            @if ($estado->orden_estado ==0)
                                Anulado
                            @endif
                            @if ($estado->orden_estado ==1)
                                Activo
                            @endif
                            @if ($estado->orden_estado ==2)
                                Despachado
                            @endif
                            @if ($estado->orden_estado ==3)
                                Facturado
                            @endif                
                            </option>
                        @endforeach
                    </select>                                     
                </div>
                <div class="col-sm-2">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <button type="submit" id="extraer" name="extraer" class="btn btn-success"><i class="fa fa-save"></i><span> Generar Guia</span></button>  
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
                    <thead>
                        <tr class="text-center">
                            <th><input type="checkbox" id="toggle" value="select" onClick="do_this()"/> </th>
                            <th></th>
                            <th>Fecha</th>
                            <th>N° de Orden de Despacho</th>
                            <th>Cliente</th>
                            <th>Tipo de Pago</th>
                            <th>Estado</th>
                            <th>Guia de Remision</th>
                            <th>Observación</th>
                            <th>Matriz</th>
                        </tr>
                    </thead>
                    <tbody>
                   
                      
                            @if(isset($ordenes))
                                @foreach($ordenes as $x)
                                <tr> 
                                    @csrf
                                    @if( $x->orden_estado ==1) 
                                    <td><input type="checkbox" id="{{ $x->orden_numero}}" name="checkbox[]" value="{{ $x->orden_id}}"> 
                                    </td>    
                                    @else
                                        <td>
                                        </td>
                                    @endif             
                                    <td>              
                                    <input id="GUIA_ID[]" name="GUIA_ID[]" value="{{ $x->orden_id}}" type="hidden">
                                    <a href="{{ url("ordenDespacho/{$x->orden_id}/visualizar") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>   
                                    <a href="{{ url("ordenDespacho/{$x->orden_id}/imprimir") }}" target="_blank" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>  
                                    @if( $x->orden_estado ==1) 
                                        <a href="{{ url("ordenDespacho/{$x->orden_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a> 
                                    @endif   
                                    <!-- 
                                    @if( $x->orden_estado ==1)   
                                        <a href="{{ url("ordenDespacho/{$x->orden_id}/editar") }}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>              
                                        <a href="{{ url("ordenDespacho/{$x->orden_id}/anular") }}" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Anular"><i class="fas fa-ban" aria-hidden="true"></i></a>         
                                       
                                    @endif
                                    -->
                                    </td>                            
                                    <td class="text-center">{{ $x->orden_fecha}}</td>
                                    <td class="text-center">{{ $x->orden_numero}}</td>
                                    <td class="text-center">{{ $x->cliente_nombre}}</td>
                                    <td class="text-center">{{ $x->orden_tipo_pago}}</td>   
                                   
                                    <td class="text-center"> 
                                        @if ($x->orden_estado ==0)
                                            Anulado
                                        @endif
                                        @if ($x->orden_estado ==1)
                                            Activo
                                        @endif
                                        @if ($x->orden_estado ==2)
                                            Despachado
                                        @endif
                                        @if ($x->orden_estado ==3)
                                            Facturado
                                        @endif   
                                    </td>
                                    <td class="text-center">@if( isset($x->guia->gr_numero)) {{ $x->guia->gr_numero}} @endif</td>
                                    <td class="text-center">{{ $x->orden_comentario}}</td>      
                                    <td class="text-center">{{ $x->sucursal_nombre}}</td>      
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
     <?php
    if(isset($fecha_todo)){  
        echo('document.getElementById("fecha_todo").checked=true;');
    }
    if(isset($valorestados)){  
        ?>
         document.getElementById("estados").value='<?php echo($valorestados); ?>';
         <?php
    }
    if(isset($valor_cliente)){  
        ?>
       document.getElementById("nombre_cliente").value='<?php echo($valor_cliente); ?>';
        <?php
    }
    if(isset($idsucursal)){ 
     ?>
    document.getElementById("sucursal").value='<?php echo($idsucursal); ?>';
        <?php
    }
    if(isset($fecha_desde)){ 
     ?>
      document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
      <?php
    }
    if(isset($fecha_hasta)){ 
     ?>
     document.getElementById("fecha_hasta").value='<?php echo($fecha_hasta); ?>';
     <?php
    }
     ?>
</script>
@endsection


