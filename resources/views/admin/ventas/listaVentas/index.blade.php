@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("listaVentas") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Lista de Ventas</h3>                                 
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-7">
                    <div class="form-group row">
                        <label for="nombre_cliente" class="col-sm-2 col-form-label"><center>Cliente:</center></label>
                        <div class="col-sm-8">
                            <select class="custom-select select2" id="nombre_cliente" name="nombre_cliente" >
                                <option value="0" label>--TODOS--</option>           
                                    @foreach($clientes as $cliente)
                                        <option  value="{{$cliente->cliente_id}}">{{$cliente->cliente_nombre}}</option>
                                    @endforeach
                            </select>                                     
                        </div>
                    </div>   
                    <div class="form-group row"> 
                        <label for="fecha_desde" class="col-sm-2 col-form-label"><center>Fecha:</center></label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                        </div>
                        <div class="col-sm-4">
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
                        <label for="nombre_bodega" class="col-sm-2 col-form-label"><center>Bodega:</center></label>
                        <div class="col-sm-8">
                            <select class="custom-select select2" id="nombre_bodega" name="nombre_bodega" >
                                <option value="0" label>--TODOS--</option>           
                                @foreach($bodegas as $bodega)
                                    <option  value="{{$bodega->bodega_id}}">{{$bodega->bodega_nombre}}</option>
                                @endforeach
                            </select>                                    
                        </div>
                    </div>   
                    <div class="form-group row"> 
                        <label for="sucursal" class="col-sm-2 col-form-label"><center>Sucursal:</center></label>
                        <div class="col-sm-8">
                            <select class="custom-select select2" id="sucursal" name="sucursal" >
                                <option  value="0" label>--TODOS--</option>           
                                @foreach($surcursal as $surcursales)
                                    <option  value="{{$surcursales->sucursal_id}}">{{$surcursales->sucursal_nombre}}</option>
                                @endforeach
                            </select>                                       
                        </div>
                        <div class="col-sm-1">
                            <button type="submit"  class="btn btn-primary btn-sm" data-toggle="modal"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Resumen por Grupo</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0" style="height: 180px;">
                        <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Grupo</th>
                                <th style="width: 200px">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($datos))
                                @if(count($datos)>0)
                                <?php $total=0?>
                                    @for ($i = 1; $i <= count($datos); ++$i)  
                                        <tr>
                                            <td>{{ $datos[$i]['Numero']}}</td>
                                            <td>{{ $datos[$i]['Grupo']}}</td>
                                            <td><?php echo '$ ' .number_format($datos[$i]['valor'], 2) ?> <?php $total=$total+$datos[$i]['valor'];?></td>
                                        </tr>
                                    @endfor
                                @endif
                            @endif
                            <tr>
                            <td colspan="2">TOTAL</td>
                            <td>@if(isset($total))<?php echo '$ ' .number_format($total, 2) ?>@else 0.00 @endif</td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                </div>
                    
                </div> 
            </div>
            <hr>        
            <table id="example1" class="table table-bordered table-responsive table-hover sin-salto">
                <thead>              
                    <tr class="text-center neo-fondo-tabla"> 
                        <th></th>                   
                        <th>Forma Pago</th>
                        <th>Cliente</th>
                        <th>Factura</th>
                        <th>Fecha</th>
                        <th>Sub-Total</th>
                        <th>Tarifa 0%</th>
                        <th>Tarifa 12%</th>
                        <th>Descuento</th>
                        <th>IVA</th> 
                        <th>Total</th> 
                        <th>Retencion</th>                       
                        <th>Cod Ret Fuente</th>     
                        <th>Cod Ret Iva</th>
                        <th>Valor Ret</th>    
                    </tr>
                </thead>
                <?php $cont = $sub_total = $tarifa0 = $tarifa12 = $desc = $iva = $total = 0.00;?>                
                @if(isset($reporteVentas))
                    @foreach($reporteVentas as $y)
                        <?php $cont = $cont + 1; $sub_total = $sub_total + $y->factura_subtotal; $tarifa0 = $tarifa0 + $y->factura_tarifa0; $tarifa12 = $tarifa12 + $y->factura_tarifa12; 
                        $desc = $desc + $y->factura_descuento; $iva = $iva + $y->factura_iva; $total = $total + $y->factura_total;?>
                    @endforeach
                @endif            
                <tbody>                                                                        
                    @if(isset($reporteVentas)) 
                        <tr class="text-center">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td> <?php echo '$' . number_format($sub_total, 2) ?> </td>
                            <td> <?php echo '$' . number_format($tarifa0, 2) ?> </td>
                            <td> <?php echo '$' . number_format($tarifa12, 2) ?> </td>
                            <td> <?php echo '$' . number_format($desc, 2) ?> </td>
                            <td> <?php echo '$' . number_format($iva, 2) ?> </td>
                            <td> <?php echo '$' . number_format($total, 2) ?> </td>
                            <td></td> 
                            <td></td>                                                    
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach($reporteVentas as $x)                            
                                <tr class="text-center">
                                    <td>
                                        <a href="{{ url("factura/{$x->factura_id}/ver") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Visualizar"><i class="fa fa-eye"></i></a> 
                                        <a href="{{ url("factura/{$x->factura_id}/imprimir") }}" target="_blank" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Imprimir"><i class="fa fa-print"></i></a>
                                    </td>
                                    <td>{{ $x->factura_tipo_pago}}</td>                        
                                    <td>{{ $x->cliente_nombre}}</td>
                                    <td>{{ $x->factura_numero}}</td>
                                    <td>{{ $x->factura_fecha}}</td>
                                    <td> <?php echo '$' . number_format($x->factura_subtotal, 2)?> </td>
                                    <td> <?php echo '$' . number_format($x->factura_tarifa0, 2)?> </td>
                                    <td> <?php echo '$' . number_format($x->factura_tarifa12, 2)?> </td>
                                    <td> <?php echo '$' . number_format($x->factura_descuento, 2)?> </td>
                                    <td> <?php echo '$' . number_format($x->factura_iva, 2)?> </td>
                                    <td> <?php echo '$' . number_format($x->factura_total, 2)?> </td>
                                    @if(isset($x->retencion->factura_id))
                                        <td>{{ $x->retencion->retencion_numero}}</td>
                                        <?php $valoRet = 0;$cont=true; $cont2=true;?>
                                            @foreach($x->retencion->detalles as $detalle)
                                                <?php $valoRet = $valoRet + $detalle->detalle_valor;
                                                ?> 
                                                @if($detalle->detalle_tipo == 'FUENTE')
                                                    <td>{{$detalle->conceptoRetencion->concepto_codigo}}</td>   
                                                @else
                                                    @if(count($x->retencion->detalles)==1)
                                                        <td></td>
                                                    @endif                                            
                                                @endif 
                                                @if($detalle->detalle_tipo == 'IVA')    
                                                    <td>{{$detalle->conceptoRetencion->concepto_codigo}}</td> 
                                                @else
                                                    @if(count($x->retencion->detalles)==1)
                                                        <td></td>
                                                    @endif                                                
                                                @endif 
                                            @endforeach
                                        <td><?php echo '$' . number_format($valoRet, 2) ?> </td>                                          
                                    @else 
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>                                                                                                                  
                                    @endif                                    
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
    if(isset($fecha_desde)){  
        ?>
       document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
        <?php
    }
     ?>
    <?php 
    if(isset($nombre_cliente)){  
        ?>
       document.getElementById("nombre_cliente").value='<?php echo($nombre_cliente); ?>';
        <?php
    }
     ?>
     <?php 
    if(isset($valor_bodega)){  
        ?>
       document.getElementById("nombre_bodega").value='<?php echo($valor_bodega); ?>';
        <?php
    }
     ?> 
     <?php
    if(isset($idsucursal)){  
        ?>
       document.getElementById("sucursal").value='<?php echo($idsucursal); ?>';
        <?php
    }
     ?>
    
</script>

@endsection