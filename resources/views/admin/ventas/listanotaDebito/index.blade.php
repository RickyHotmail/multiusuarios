@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("listanotaDebito") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Lista de Notas de Debito</h3>                                 
        </div>
        <div class="card-body">
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
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Descripcion:</center></label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="descripcion" name="descripcion"  value='' >
                </div>
                <div class="col-sm-1">
                    <button type="submit"  class="btn btn-primary btn-sm" data-toggle="modal"><i class="fa fa-search"></i></button>
                </div>
                
            </div>   
            <div class="form-group row">
                <label for="nombre_sucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal" name="sucursal" >
                        <option value="--TODOS--" label>--TODOS--</option>                       
                        @foreach($sucursal as $sucursales)
                            <option id="{{$sucursales->sucursal_nombre}}" name="{{$sucursales->sucursal_nombre}}" value="{{$sucursales->sucursal_nombre}}" @if(isset($idsucursal)) @if($sucursales->sucursal_nombre==$idsucursal) selected @endif @endif>{{$sucursales->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
            </div>
            <hr>        
            <table id="example1" class="table table-bordered table-responsive table-hover sin-salto">
                <thead>              
                    <tr class="text-center neo-fondo-tabla">     
                        <th></th>               
                        <th>Numero</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Subtotal</th>
                        <th>Tarifa 0%</th>
                        <th>Tarifa 12%</th>
                        <th>Descuento</th>
                        <th>IVA</th> 
                        <th>Total</th> 
                        <th>Factura</th>
                        <th>Diario</th> 
                    </tr>
                </thead>
                          
                <tbody>                                                                        
                    @if(isset($notaDebito)) 
                        @foreach($notaDebito as $x)
                        <tr class="text-center">
                                <td><a href="{{ url("notaDebito/{$x->nd_id}/ver") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Visualizar"><i class="fa fa-eye"></i></a>    
                            </td>  
                                <td>{{ $x->nd_numero}}</td>  
                                <td>{{ $x->nd_fecha}}</td>     
                                <td>{{ $x->factura->cliente->cliente_nombre}}</td>      
                                <td> <?php echo '$' . number_format($x->nd_subtotal, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->nd_tarifa0, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->nd_tarifa12, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->nd_descuento, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->nd_porcentaje_iva, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->nd_total, 2)?> </td>  
                                <td>{{ $x->factura->factura_numero}}</td>  
                                <td>@if($x->diario) <a href="{{ url("asientoDiario/ver/{$x->diario->diario_codigo}") }}" target="_blank">{{ $x->diario->diario_codigo }}</a> @endif</td> 
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>        
        </div>
    </div>
</form>
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
    if (isset($fecha_desde)) {
        ?>
       document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
    
    <?php
    }
    if(isset($descripcion)){  
        ?>
       document.getElementById("descripcion").value='<?php echo($descripcion); ?>';
        <?php
    }
    ?>
</script>

@endsection