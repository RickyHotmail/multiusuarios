@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("listaFactura") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Lista de Facturas</h3>                                 
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
                    <div class="icheck-secondary">
                        <input type="checkbox" id="fecha_todo" name="fecha_todo" @if(isset($todo)) @if($todo == 'on') checked @endif @endif>
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
                        <option value="0" label>--TODOS--</option>                       
                        @foreach($sucursal as $sucursales)
                            <option value="{{$sucursales->sucursal_id}}">{{$sucursales->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <label for="estado" class="col-sm-1 col-form-label"><center>Estados:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="estado_id" name="estado_id" >
                        <option value="0" label>--TODOS--</option>                       
                        @foreach($estados as $estado)
                            <option  value="{{$estado->factura_estado}}">
                            @if($estado->factura_estado == '1') ACTIVA @else ANULADA @endif </option>
                        @endforeach
                    </select>                                     
                </div>
            </div> 
            <hr>        
            <table id="example1" class="table table-bordered table-responsive table-hover sin-salto">
                <thead>              
                    <tr class="text-center neo-fondo-tabla">    
                        <th></th>                
                        <th>Factura</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Subtotal</th>
                        <th>Tarifa 0%</th>
                        <th>Tarifa 12%</th>
                        <th>Descuento</th>
                        <th>IVA</th> 
                        <th>Total</th> 
                        <th>Diario</th> 
                        <th>Diario Costo</th> 
                        <th>Estado</th> 
                    </tr>
                </thead>
                          
                <tbody>                                                                        
                    @if(isset($facturas)) 
                        @foreach($facturas as $x)
                            <tr class="text-center">
                                <td>
                                    <a href="{{ url("factura/{$x->factura_id}/ver") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Visualizar"><i class="fa fa-eye"></i></a> 
                                    <a href="{{ url("factura/{$x->factura_id}/imprimir") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>    
                                    <a href="{{ url("factura/{$x->factura_id}/imprimirRecibo") }}" target="_blank" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Imprimir Recibo"><i class="fa fa-print"></i></a>                   
                                </td>
                                <td>{{ $x->factura_numero}}</td>  
                                <td>{{ $x->factura_fecha}}</td>                                      
                                <td>{{ $x->cliente_nombre}}</td>
                                <td> <?php echo '$' . number_format($x->factura_subtotal, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->factura_tarifa0, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->factura_tarifa12, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->factura_descuento, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->factura_iva, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->factura_total, 2)?> </td> 
                                <td>@if($x->diario) <a href="{{ url("asientoDiario/ver/{$x->diario->diario_codigo}") }}" target="_blank">{{ $x->diario->diario_codigo }}</a> @endif</td> 
                                <td>@if($x->diarioCosto) <a href="{{ url("asientoDiario/ver/{$x->diarioCosto->diario_codigo}") }}" target="_blank">{{ $x->diarioCosto->diario_codigo }}</a> @endif</td>                           
                                <td>@if($x->factura_estado == '1') ACTIVA @else ANULADA @endif</td>
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
     if(isset($idsucursal)){ 
      ?>
     document.getElementById("sucursal").value='<?php echo($idsucursal); ?>';
         <?php
     }
     if(isset($idestado)){  
      ?>
      document.getElementById("estado_id").value='<?php echo($idestado); ?>';
         <?php
     }
    
      ?>
</script>

@endsection