@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Compras</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaCompras") }} "> 
        @csrf
            <div class="form-group row">
                <label for="idDescripcion" class="col-sm-1 col-form-label"><center>Proveedor</center></label>
                <div class="col-sm-4">
                    <select class="form-control select2" id="idProveedor" name="idProveedor" data-live-search="true">
                        <option id="todos" name="todos" value="--TODOS--" label>--TODOS--</option>  
                        @foreach($proveedores as $proveedor)
                            <option id="{{$proveedor->proveedor_id}}" name="{{$proveedor->proveedor_id}}" value="{{$proveedor->proveedor_id}}"  @if(isset($nombre_cliente)) @if($proveedor->proveedor_id==$nombre_cliente) selected @endif @endif>                                
                                {{$proveedor->proveedor_nombre}}
                            </option>
                        @endforeach
                    </select>  
                </div>
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <div class="col-sm-1">
                    <div class="icheck-secondary">
                        <input type="checkbox" id="fecha_todo" name="fecha_todo" >
                        <label for="fecha_todo" style="border-right: 10px;"><center>Todo</center></label>
                    </div>  
                </div>
            </div>   
            <div class="form-group row">  
                <label for="sucursal" class="col-sm-1 col-form-label"><center>Sucursal</center></label>
                <div class="col-sm-4">
                    <select class="form-control select2" id="sucursal" name="sucursal" data-live-search="true">
                        <option value="--TODOS--" label>--TODOS--</option>  
                        @foreach($sucursales as $sucursal)
                            <option id="{{$sucursal->sucursal_nombre}}" name="{{$sucursal->sucursal_nombre}}" value="{{$sucursal->sucursal_nombre}}" @if(isset($idsucursal)) @if($sucursal->sucursal_nombre==$idsucursal) selected @endif @endif>                                
                                {{$sucursal->sucursal_nombre}}
                            </option>
                        @endforeach
                    </select>  
                </div>
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>            
        </form>
        <hr>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th>Documento</th>
                    <th>Fecha</th>
                    <th>Numero</th>
                    <th>Proveedor</th>
                    <th>Forma de Pago</th>
                    <th># Autorizacion</th>  
                    <th>Subtotal</th>
                    <th>Tarifa 0%</th>                  
                    <th>Tarifa 12%</th>
                    <th>Descuento</th>
                    <th>Iva</th>
                    <th>Total</th>
                </tr>
            </thead>
            <?php $cont = $sub_total = $tarifa0 = $tarifa12 = $desc = $iva = $total = 0.00;?> 
            @if(isset($transaccionCompras))
                @foreach($transaccionCompras as $y)
                    <?php $cont = $cont + 1; $sub_total = $sub_total + $y->transaccion_subtotal; $tarifa0 = $tarifa0 + $y->transaccion_tarifa0; $tarifa12 = $tarifa12 + $y->transaccion_tarifa12; 
                    $desc = $desc + $y->transaccion_descuento; $iva = $iva + $y->transaccion_iva; $total = $total + $y->transaccion_total;?>
                @endforeach
            @endif  
            <tbody> 
                @if(isset($transaccionCompras))
                    <tr class="text-center">
                        <td></td>
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
                    </tr>   
                    @foreach($transaccionCompras as $transaccionCompra)
                    <tr>
                        <td class="text-center">{{ $transaccionCompra->tipoComprobante->tipo_comprobante_nombre}}</td>
                        <td class="text-center">{{ $transaccionCompra->transaccion_fecha}}</td>
                        <td class="text-center">{{ $transaccionCompra->transaccion_numero}}</td>
                        <td class="text-center">{{ $transaccionCompra->proveedor->proveedor_nombre}}</td>
                        <td class="text-center">{{ $transaccionCompra->transaccion_tipo_pago}}</td>
                        <td class="text-center">{{ $transaccionCompra->transaccion_autorizacion}}</td> 
                        <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_subtotal,2)}}</td>
                        <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa0,2)}}</td>
                        <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa12,2)}}</td>
                        <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_descuento,2)}}</td>
                        <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_iva,2)}}</td>
                        <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_total,2)}}</td>
                    </tr>                         
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<script>
     <?php
    if(isset($fecha_todo)){  
        echo('document.getElementById("fecha_todo").checked=true;');
    }
    if(isset($fecF)){  
        ?>
         document.getElementById("fecha_hasta").value='<?php echo($fecF); ?>';
         <?php
    }
    if(isset($fecI)){  
        ?>
         document.getElementById("fecha_desde").value='<?php echo($fecI); ?>';
         <?php
    }
    ?>
</script>
@endsection
