@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("prefactura/buscar") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Lista de Prefacturas</h3>    
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="nombre_cliente" class="col-sm-1 col-form-label"><center>Cliente:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="nombre_cliente" name="nombre_cliente" >
                        <option value="0" label>--TODOS--</option>                       
                        @foreach($clientes as $cliente)
                            <option  value="{{$cliente->cliente_id}}">{{$cliente->cliente_nombre}}</option>
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
                <label for="nombre_sucursal" class="col-sm-1 col-form-label"><center>Estados:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="estado" name="estado" >
                        <option value="3" label>--TODOS--</option>                       
                        @foreach($estados as $estado)
                            <option  value="{{$estado->prefactura_estado}}">
                            @if ($estado->prefactura_estado =='0')
                                Anulado
                            @endif
                            @if ($estado->prefactura_estado =='1')
                                Generado
                            @endif
                            @if ($estado->prefactura_estado =='2')
                                Facturado
                            @endif
                            </option>
                        @endforeach
                    </select>                                     
                </div>
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Descripcion:</center></label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="descripcion" name="descripcion"  value='' >
                </div>
                <div class="col-sm-2">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
                    <thead>
                        <tr class="text-center">
                            
                            <th></th>
                            <th>Fecha</th>
                            <th>Codigo</th>
                            <th>Cliente</th>
                            <th>Tipo de Pago</th>
                            <th>Total</th>
                            <th>Comentario</th>
                            <th>Estado</th>
                           
                            
                        </tr>
                    </thead>
                    <tbody>
                   
                      
                            @if(isset($prefacturas))
                                @foreach($prefacturas as $x)
                                <tr> 
                                    @csrf
                                                
                                    <td>  
                                    @if( $x->prefactura_estado =='1')             
                                    <a href="{{ url("prefactura/{$x->prefactura_id}/facturar") }}" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Facturar"><i class="fa fa-save"></i></a>   
                                    @endif   
                                    
                                    <a href="{{ url("prefactura/{$x->prefactura_id}/imprimir") }}" target="_blank" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>  
                                    @if( $x->prefactura_estado =='1') 
                                        <a href="{{ url("prefactura/{$x->prefactura_id}/anular") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Anular"><i class="fa fa-trash" aria-hidden="true"></i></a> 
                                    @endif   
                                  
                                    </td>                            
                                    <td class="text-center">{{ $x->prefactura_fecha}}</td>
                                    <td class="text-center">{{ $x->prefactura_numero}}</td>
                                    <td class="text-center">{{ $x->cliente_nombre}}</td>
                                    <td class="text-center">{{ $x->prefactura_tipo_pago}}</td>   
                                    <td class="text-center">{{ $x->prefactura_total}}</td>
                                    <td class="text-center">{{ $x->prefactura_comentario}} </td>   
                                    <td class="text-center"> 
                                        @if ($x->prefactura_estado =='0')
                                            Anulado
                                        @endif
                                        @if ($x->prefactura_estado =='1')
                                            Generado
                                        @endif
                                        @if ($x->prefactura_estado =='2')
                                            Facturado
                                        @endif
                                       
                                    </td>
                                   
                                  
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
         document.getElementById("estado").value='<?php echo($valorestados); ?>';
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


