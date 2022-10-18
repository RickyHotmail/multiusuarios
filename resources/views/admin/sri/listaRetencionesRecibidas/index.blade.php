@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Retenciones de Ventas Recibidas</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("listaRetencionRecibida") }}">
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div> 
                <label for="nombre_sucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="sucursal" name="sucursal" >
                        <option value="--TODOS--" label>--TODOS--</option>                       
                        @foreach($sucursal as $sucursales)
                            <option id="{{$sucursales->sucursal_nombre}}" name="{{$sucursales->sucursal_nombre}}" value="{{$sucursales->sucursal_nombre}}" @if(isset($idsucursal)) @if($sucursales->sucursal_nombre==$idsucursal) selected @endif @endif>{{$sucursales->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>                              
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>            
        </form>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th># Documento</th>
                    <th>Proveedor</th>
                    <th>No. Retencion</th>  
                    <th>Diario</th>  
                    <th>Base</th>
                    <th>Valor Ret</th>                  
                    <th>Codigo</th>
                    <th>Concepto Retencion</th>
                    <th>Porcentaje</th>
                    <th>Procesadas</th>
                </tr>
            </thead>
            <tbody>           
                @if(isset($retencionVentas))
                    @foreach($retencionVentas as $retencion)
                        @foreach($retencion->detalles as $detalle)                           
                            <tr @if(date("Y-m-d", strtotime($retencion->created_at)) == $fechaActual) style="background:  #c7f0e4;" @endif>
                                <td class="text-center">{{ $retencion->retencion_fecha}}</td>
                                <td>
                                    @if($retencion->factura) Factura @endif
                                    @if($retencion->notaDebito) Nota de débito @endif
                                </td>
                                <td>
                                    @if($retencion->factura) {{ $retencion->factura->factura_numero }} @endif
                                    @if($retencion->notaDebito) {{ $retencion->notaDebito->nd_numero }} @endif 
                                </td>
                                <td>
                                    @if($retencion->factura) {{ $retencion->factura->cliente->cliente_nombre }} @endif
                                    @if($retencion->notaDebito) {{ $retencion->notaDebito->factura->cliente->cliente_nombre }} @endif 
                                </td>
                                <td>{{ $retencion->retencion_numero}}</td>       
                                <td><a href="{{ url("asientoDiario/ver/{$retencion->diario->diario_codigo}") }}" target="_blank">{{ $retencion->diario->diario_codigo }}</a></td>                    
                                <td>${{ number_format($detalle->detalle_base,2)}}</td>
                                <td>${{ number_format($detalle->detalle_valor,2)}}</td>
                                <td>{{ $detalle->conceptoRetencion->concepto_codigo}}</td>       
                                <td>{{ $detalle->conceptoRetencion->concepto_nombre}}</td> 
                                <td>{{ $detalle->detalle_porcentaje}}%</td>
                                @if(date("Y-m-d", strtotime($retencion->created_at)) == $fechaActual)
                                    <td>HOY</td>
                                @else
                                    <td></td>
                                @endif
                            </tr>                            
                        @endforeach
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
   if(isset($fecha_hasta)){  
        ?>
         document.getElementById("idHasta").value='<?php echo($fecha_hasta); ?>';
         <?php
    }
    if (isset($fecha_desde)) {
        ?>
       document.getElementById("idDesde").value='<?php echo($fecha_desde); ?>';
    
    <?php
    }
        $fechaActual = date("Y-m-d")        
        ?>
   
</script>
@endsection