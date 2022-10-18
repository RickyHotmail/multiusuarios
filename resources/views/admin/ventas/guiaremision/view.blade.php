@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Guias Remision</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("guia/consultar") }}">
        @csrf
            <div class="form-group row">
                <label for="nombre_cliente" class="col-sm-1 col-form-label"><center>Cliente:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="nombre_cliente" name="nombre_cliente" >  
                        <option value="0" >--TODOS--</option>                      
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente->cliente_id}}">{{$cliente->cliente_nombre}}</option>
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
                <label for="sucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal" name="sucursal" >
                        <option value="0" >--TODOS--</option>                       
                        @foreach($sucursales as $sucursal)
                            <option  value="{{$sucursal->sucursal_id}}" @if(isset($idsucursal)) @if($idsucursal==$sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <label for="estados" class="col-sm-1 col-form-label"><center>Estados:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select" id="estados" name="estados" >
                        <option value="--TODOS--" label>--TODOS--</option>                        
                        @foreach($estados as $estado)
                            <option  value="{{$estado->gr_estado}}">
                            @if( $estado->gr_estado ==0) Anulado @endif
                            @if( $estado->gr_estado ==1) Activo @endif
                            @if( $estado->gr_estado ==2) Facturada @endif
                            </option>
                        @endforeach
                    </select>                                     
                </div>
                <div class="col-sm-2">            
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                  
                </div>
            </div>
            <table id="example1" class="table table-bordered table-responsive table-hover sin-salto">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th></th>
                        <th></th>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Transportista</th>
                        <th>Partida</th>
                        <th>Destino</th>
                        <th>Estado</th>
                        <th>Autorizacion</th>
                        <th>Factura</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($guias))
                        @foreach($guias as $x)
                        <tr>  
                            @if ( $x->gr_estado ==1)                       
                                <td><input id="GUIA_ID" name="GUIA_ID" value="{{ $x->gr_id}}" type="hidden"></td> 
                            @else
                                <td> </td> 
                            @endif 
                             <td>
                                <a href="{{ url("guia/{$x->gr_id}/visualizar") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>  
                                @if($x->gr_estado == '1')
                                <a href="{{ url("guia/{$x->gr_id}/veranular") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Anular"><i class="fas fa-ban"></i></a>
                                @endif
                                <a href="{{ url("guia/{$x->gr_id}/imprimir") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>                     
                            </td>                                        
                            <td class="text-center">{{ $x->gr_numero}}</td>
                            <td class="text-center">{{ $x->gr_fecha}}</td>
                            <td class="text-center">{{ $x->cliente_nombre}}</td>
                            <td class="text-center">{{ $x->transportista_nombre}}</td>
                            <td class="text-center">{{ $x->gr_punto_partida}}</td>
                            <td class="text-center">{{ $x->gr_punto_destino}}</td>
                            <td class="text-center">
                                    @if( $x->gr_estado ==0) Anulado @endif 
                                    @if( $x->gr_estado ==1) Activo @endif 
                                    @if( $x->gr_estado ==2) Facturada @endif            
                            </td>    
                            <td class="text-center">{{ $x->gr_autorizacion}}</td>
                            <td class="text-center">@if(isset($x->Factura)) {{ $x->Factura->factura_numero}} @endif</td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </form>
</div>
<script>
     <?php
    if(isset($fecha_todo)){  
        echo('document.getElementById("fecha_todo").checked=true;');
    }
    if(isset($fecha_hasta)){  
        ?>
         document.getElementById("fecha_hasta").value='<?php echo($fecha_hasta); ?>';
         <?php
    }
    if(isset($fecha_desde)){  
        ?>
         document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
         <?php
    }
    if(isset($valorestados)){  
        ?>
         document.getElementById("estados").value='<?php echo($valorestados); ?>';
         <?php
    }
    if(isset($nombre_cliente)){  
        ?>
       document.getElementById("nombre_cliente").value='<?php echo($nombre_cliente); ?>';
        <?php
    }
    if(isset($idsucursal)){  
     ?>

      document.getElementById("sucursal").value='<?php echo($idsucursal); ?>';
          <?php
    }
        ?>
   
    
</script>
@endsection

