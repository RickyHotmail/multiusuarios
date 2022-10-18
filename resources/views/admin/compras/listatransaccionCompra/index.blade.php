@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST"  action="{{ url("listatransaccionCompra") }} "> 
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Lista de Transaccion de Compras</h3>                                 
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
                <label for="sucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal" name="sucursal">  
                        <option value="0" label>--TODOS--</option>                      
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($idsucursal)) @if($sucursal->sucursal_id==$idsucursal) selected @endif @endif>
                                {{$sucursal->sucursal_nombre}} 
                            </option>
                        @endforeach
                    </select>                                     
                </div> 
                <label for="sucursal" class="col-sm-1 col-form-label"><center>Proveedor:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="idproveedor" name="idproveedor">  
                        <option value="0" label>--TODOS--</option>                      
                        @foreach($proveedores as $proveedor)
                            <option  value="{{$proveedor->proveedor_id}}" @if(isset($idproveedor)) @if($proveedor->proveedor_id==$idproveedor) selected @endif @endif>
                                {{$proveedor->proveedor_nombre}} 
                            </option>
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
                        <th>Tipo Documento</th>
                        <th>Proveedor</th>
                        <th>Diario</th>
                        <th>Subtotal</th>
                        <th>Tarifa 0%</th>
                        <th>Tarifa 12%</th>
                        <th>Descuento</th>
                        <th>IVA</th> 
                        <th>Total</th>  
                    </tr>
                </thead>
                          
                <tbody>                                                                        
                    @if(isset($compras)) 
                        @foreach($compras as $x)
                            <tr class="text-center">
                                <td><a href="{{ url("transaccioncompra/{$x->transaccion_id}/ver") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Visualizar"><i class="fa fa-eye"></i></a>    
                                @if($x->tipoComprobante->tipo_comprobante_codigo=='04')
                                    @if(isset($x->diario->anticipoproveedor))
                                        @if($x->diario->anticipoproveedor->anticipo_valor == $x->diario->anticipoproveedor->anticipo_saldo)  
                                            <a href="{{ url("transaccioncompra/{$x->transaccion_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>   
                                            <a href="{{ url("transaccioncompra/{$x->transaccion_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>             
                                        @endif
                                    @else
                                        <a href="{{ url("transaccioncompra/{$x->transaccion_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a> 
                                        <a href="{{ url("transaccioncompra/{$x->transaccion_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>             
                                    @endif
                                @else
                                
                                    @if(count($x->notas_d_c)==0)
                                        
                                        @if(($x->transaccion_tipo_pago)=='EN EFECTIVO')
                                            <a href="{{ url("transaccioncompra/{$x->transaccion_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>  
                                            <a href="{{ url("transaccioncompra/{$x->transaccion_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>             
                                        @else
                                            @if($x->cuentaPagar->cuenta_monto == $x->cuentaPagar->cuenta_saldo)  
                                                <a href="{{ url("transaccioncompra/{$x->transaccion_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>   
                                                <a href="{{ url("transaccioncompra/{$x->transaccion_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>             
                                            @endif
                                        @endif
                                    @endif
                                @endif
                                 
                            </td>
                                <td>{{ $x->transaccion_numero}}</td>  
                                <td>{{ $x->transaccion_fecha}}</td>              
                                <td>{{ $x->tipoComprobante->tipo_comprobante_nombre}}</td>              
                                <td>{{ $x->proveedor->proveedor_nombre}}</td>
                                <td>@if(isset($x->diario->diario_codigo))<a href="{{ url("asientoDiario/ver/{$x->diario->diario_codigo}") }}" target="_blank">{{ $x->diario->diario_codigo }}</a>@endif</td>
                                <td> <?php echo '$' . number_format($x->transaccion_subtotal, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->transaccion_tarifa0, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->transaccion_tarifa12, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->transaccion_descuento, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->transaccion_porcentaje_iva, 2)?> </td>
                                <td> <?php echo '$' . number_format($x->transaccion_total, 2)?> </td>                            
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