@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Reporte de Ordenes de Despacho</h3>    
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("ordenDespacho/buscar") }}">
        @csrf
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
                    <div class="icheck-success">
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
                    <button type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>                       
                </div>               
            </div>
            <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center">
                    
                        <th>Documento</th>
                        <th>N° de Documento</th>
                        <th>Fecha</th>
                        <th>Codigo Producto</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>PVP</th>
                        <th>Iva</th>
                        <th>Sub 12%</th>
                        <th>Sub 0%</th>
                        <th>Total</th>
                        <th>Transaccion</th>
                        <th>Cliente</th>
                        <th>Peso Libras</th>
                        <th>Peso Kilos</th>
                        <th>Peso TM</th>
                        <th>Factura</th>
                        <th>Comentario Orden</th>  
                        <th>Comentario Factura</th>
                        <th>Embarcacion</th> 
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @foreach($datos as $x)
                        <tr class="text-center">
                        
                            <td >ORDEN DESPACHO</td>
                            <td >{{$x->orden_numero}} <input type="hidden" name="orden_numero[]" value="{{ $x->orden_numero }}"/></td>
                            <td >{{$x->orden_fecha}} <input type="hidden" name="orden_fecha[]" value="{{ $x->orden_fecha }}"/></td>
                            <td >{{$x->producto_codigo}} <input type="hidden" name="producto_codigo[]" value="{{ $x->producto_codigo }}"/></td>
                            <td >{{$x->detalle_descripcion}} <input type="hidden" name="detalle_descripcion[]" value="{{ $x->detalle_descripcion }}"/></td>
                            <td >{{$x->detalle_cantidad}} <input type="hidden" name="detalle_cantidad[]" value="{{ $x->detalle_cantidad }}"/></td>
                            <td ><?php echo '$ ' .number_format($x->detalle_precio_unitario, 2) ?> <input type="hidden" name="precio[]" value="$ {{ number_format($x->detalle_precio_unitario, 2) }}"/></td>
                            <td ><?php echo '$ ' .number_format($x->detalle_iva, 2) ?> <input type="hidden" name="iva[]" value="$ {{ $x->detalle_iva }}"/></td>
                            <td ><?php  if($x->detalle_iva!=0){echo '$ ' .number_format($x->detalle_total, 2);}else{echo('$ 0.00');} ?> <input type="hidden" name="sub12[]" value="<?php  if($x->detalle_iva!=0){echo '$ ' .number_format($x->detalle_total, 2);}else{echo  '$ 0.00' ;} ?>"/></td>
                            <td ><?php  if($x->detalle_iva==0){ echo '$ ' .number_format($x->detalle_total, 2);} else{echo('$ 0.00');} ?> <input type="hidden" name="sub0[]" value="<?php  if($x->detalle_iva==0){ echo '$ ' .number_format($x->detalle_total, 2);} else{echo '$ 0.00';} ?>"/></td>
                            <td ><?php echo '$ ' .number_format($x->detalle_total+$x->detalle_iva, 2) ?> <input type="hidden" name="total[]" value="$ {{ number_format($x->detalle_total+$x->detalle_iva, 2) }}"/></td>
    
                            <td >VENTA<input type="hidden" name="transaccion[]" value="VENTA"/></td>
                            <td >{{$x->cliente_nombre}} <input type="hidden" name="cliente_nombre[]" value="{{ $x->cliente_nombre }}"/></td>
                            <td >{{$x->tamano_nombre*$x->detalle_cantidad}} <input type="hidden" name="libras[]" value="{{ $x->tamano_nombre*$x->detalle_cantidad }}"/></td>
                            <td >{{ number_format(($x->tamano_nombre*$x->detalle_cantidad)*0.453592,2)}} <input type="hidden" name="kilos[]" value="{{ number_format(($x->tamano_nombre*$x->detalle_cantidad)*0.453592,2) }}"/></td>
                            <td >{{ number_format((($x->tamano_nombre*$x->detalle_cantidad)*0.453592)/1000,2)}} <input type="hidden" name="tm[]" value="{{ number_format((($x->tamano_nombre*$x->detalle_cantidad)*0.453592)/1000,2) }}"/></td>
                            <td >@if(isset($x->Factura->factura_id)) {{$x->Factura->factura_numero}} @endif<input type="hidden" name="factura[]" value="@if(isset($x->Factura->factura_id)) {{$x->Factura->factura_numero}} @endif"/></td>
                            <td >{{$x->orden_comentario}}<input type="hidden" name="orden_comentario[]" value="{{ $x->orden_comentario }}"/></td> 
                            <td >@if(isset($x->Factura->factura_id)){{$x->Factura->factura_comentario}}@endif<input type="hidden" name="orden_comentario[]" value="@if(isset($x->Factura->factura_id)){{$x->Factura->factura_comentario}}@endif"/></td>       
                            <td >@if(isset($x->gr_id)){{$x->guia->Transportista->transportista_embarcacion}}@endif<input type="hidden" name="embarcacion[]" value="@if(isset($x->gr_id)){{$x->guia->Transportista->transportista_embarcacion}}@endif"/></td>       
                        </tr>
                        @endforeach
                    @endif
                    
                </tbody>
            </table>
        </form>
    </div>
</div>
<!-- /.card -->
<script type="text/javascript">
function cargarmetodo() {
    <?php
    if(isset($fecha_todo)){  
        echo('document.getElementById("fecha_todo").checked=true;');
    }
    if(isset($nombre_cliente)){  
        ?>
         document.getElementById("nombre_cliente").value='<?php echo($nombre_cliente); ?>';
    <?php
    }
    if (isset($idestado)) {
        ?>
       document.getElementById("estados").value='<?php echo($idestado); ?>';
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
      
     
}
</script>
@endsection

