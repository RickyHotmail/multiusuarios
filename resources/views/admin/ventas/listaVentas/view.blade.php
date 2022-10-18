@extends ('admin.layouts.admin')
@section('principal')
<div class="card">
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaVentas/buscar") }}">
        @csrf
            <div>
                <h3 class="card-title">Lista de Ventas</h3>        
                <button type="submit"  class="btn btn-primary btn-sm float-right" data-toggle="modal"><i class="fa fa-search"></i></button>                         
            </div>
            <br>
            <hr> 
            <!-- /.Fecha -->
            <div class="form-group row">
                <label for="nombre_cliente" class="col-sm-1 col-form-label"><center>Cliente:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select" id="nombre_cliente" name="nombre_cliente" >
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

            <!-- /.Cliente -->
            <div class="form-group row"> 
                <label for="nombre_bodega" class="col-sm-1 col-form-label"><center>Bodega:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select" id="nombre_bodega" name="nombre_bodega" >
                        <option value="--TODOS--" label>--TODOS--</option>           
                        @foreach($bodegas as $bodega)
                            <option id="{{$bodega->bodega_nombre}}" name="{{$bodega->bodega_nombre}}" value="{{$bodega->bodega_nombre}}">{{$bodega->bodega_nombre}}</option>
                            <?php  ?>
                        @endforeach
                    </select>                                    
                </div>
                <label for="nombre_emision" class="col-sm-1 col-form-label"><center>Emision:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select" id="nombre_emision" name="nombre_emision" >
                    <option value="--TODOS--" label>--TODOS--</option>           
                        @foreach($puntoEmisiones as $puntoEmision)
                            <option id="{{$puntoEmision->punto_serie}}" name="{{$puntoEmision->punto_serie}}" value="{{$puntoEmision->punto_serie}}">{{$puntoEmision->punto_serie.' - '.$puntoEmision->punto_descripcion}}</option>
                        @endforeach
                    </select>                                     
                </div>
                

            </div>
            
        </form>
        <hr>        
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            
            <thead>
                      
                <tr class="text-center">                    
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
                        <td> <?php echo '$' . number_format($sub_total, 2) ?> </td>
                        <td> <?php echo '$' . number_format($tarifa0, 2) ?> </td>
                        <td> <?php echo '$' . number_format($tarifa12, 2) ?> </td>
                        <td> <?php echo '$' . number_format($desc, 2) ?> </td>
                        <td> <?php echo '$' . number_format($iva, 2) ?> </td>
                        <td> <?php echo '$' . number_format($total, 2) ?> </td>
                    </tr>
                    @foreach($reporteVentas as $x)
                        <tr class="text-center">
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
  
    if(isset($nombre_emision)){  
        ?>
       document.getElementById("nombre_emision").value='<?php echo($nombre_emision); ?>';
        <?php
    }
     ?>
    
</script>
@endsection