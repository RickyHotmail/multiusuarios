@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Reporte de Venta por Producto</h3>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("reporteVentaProductoC") }}">
        @csrf
        <div class="form-group row">
                <label for="nombre_cuenta" class="col-sm-1 col-form-label"><center>Producto:</center></label>
                <div class="col-sm-3">
                    <select class="custom-select select2" id="productoID" name="productoID" require>
                        <option value="0">Todos</option>
                        @foreach($productos as $producto)
                            <option value="{{$producto->producto_id}}" @if(isset($productoC)) @if($productoC == $producto->producto_id) selected @endif @endif>{{$producto->producto_nombre}}</option>
                        @endforeach
                    </select>                    
                </div>
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Bodega</center></label>
                <div class="col-sm-3">
                    <select id="bodega_id" name="bodega_id" class="form-control show-tick"
                        data-live-search="true">
                        <option value="0">Todos</option>
                        @foreach($bodegas as $bodega)
                            <option value="{{ $bodega->bodega_id }}"@if(isset($bodegac)) @if($bodegac == $bodega->bodega_id) selected @endif @endif>{{ $bodega->bodega_nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <label for="nombre_cuenta" class="col-sm-1 col-form-label"><center>Cliente:</center></label>
                <div class="col-sm-3">
                    <select class="custom-select select2" id="clienteID" name="clienteID" required>
                        <option value="0">Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente->cliente_id}}" @if(isset($clienteC)) @if($clienteC == $cliente->cliente_id) selected @endif @endif>{{$cliente->cliente_nombre}}</option>
                        @endforeach
                    </select>                    
                </div> 
            </div>
            <div class="form-group row">                
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-3">
                @if(isset($fechaselect2))
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value="{{$fechaselect2}}" required>
                @else
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                @endif
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-3">
                @if(isset($fechaselect))
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value="{{$fechaselect}}" required>
                @else
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                @endif
                </div>          
                <div class="col-sm-1">
                    <button type="submit" id="buscarReporte" name="buscarReporte" class="btn btn-primary"><i class="fa fa-search"></i></button>
                </div>       
            </div>            
            <div class="card-body table-responsive p-0" style="height: 540px;">
                <table id="example4" class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr>  
                            <th>No Documento</th>
                            <th>Codigo</th>
                            <th>Producto</th>
                            <th>Fecha</th>                        
                            <th>Cantidad</th>
                            <th>PVP</th>
                            <th>IVA</th>
                            <th>Sub 12%</th>
                            <th>Sub 0%</th>
                            <th>Total</th>                           
                            <th>Cliente</th>  
                            <th>Peso libras</th>
                            <th>Peso Kilos</th>
                            <th>Peso TM</th>                          
                            <th>Numero Orden</th>
                            <th>Observacion</th>
                        </tr>
                    </thead>            
                    <tbody>
                    @if(isset($ventaxProductoMatriz))
                        @for ($i = 1; $i <= count($ventaxProductoMatriz); ++$i)               
                        <tr class="text-left">
                            <td>{{ $ventaxProductoMatriz[$i]['Documento'] }}</td>
                            <td>{{ $ventaxProductoMatriz[$i]['Codigo']}}</td>
                            <td>{{ $ventaxProductoMatriz[$i]['Producto']}}</td>                        
                            <td>{{ $ventaxProductoMatriz[$i]['Fecha']}}</td>
                            <td>{{ $ventaxProductoMatriz[$i]['Cantidad']}}</td>
                            <td>{{ number_format($ventaxProductoMatriz[$i]['Pvp'],2,'.','')}}</td>
                            <td>{{ number_format($ventaxProductoMatriz[$i]['Iva'],2,'.','')}}</td>
                            <td>{{ number_format($ventaxProductoMatriz[$i]['Subtotal12'],2,'.','')}}</td>
                            <td>{{ number_format($ventaxProductoMatriz[$i]['Subtotal0'],2,'.','')}}</td>
                            <td>{{ number_format($ventaxProductoMatriz[$i]['Total'],2,'.','')}}</td>
                            <td>{{ $ventaxProductoMatriz[$i]['Cliente']}}</td>
                            <td>{{ $ventaxProductoMatriz[$i]['libras']}}</td>
                            <td>{{ $ventaxProductoMatriz[$i]['kilos']}}</td>
                            <td>{{ $ventaxProductoMatriz[$i]['tm']}}</td>
                            <td>{{ $ventaxProductoMatriz[$i]['Orden']}}</td>
                            <td>{{ $ventaxProductoMatriz[$i]['Observacion']}}</td>      
                        </tr>
                        @endfor
                    @endif                    
                        <tr> 
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>   
                            @if($totalCantidad!=0)                     
                                <th>{{$totalCantidad}}</th>
                            @else
                                <th></th>
                            @endif
                            <th></th>
                            @if($totalIva!=0)    
                                <th>{{number_format($totalIva,2,'.','')}}</th>
                            @else
                                <th></th>
                            @endif
                            @if($total12!=0)   
                                <th>{{number_format($total12,2,'.','')}}</th>
                            @else
                                <th></th>
                            @endif
                            @if($total0!=0)
                                <th>{{number_format($total0,2,'.','')}}</th>
                            @else
                                <th></th>
                            @endif
                            @if($totales!=0)
                                <th>{{number_format($totales,2,'.','')}}</th>
                            @else
                                <th></th>
                            @endif                        
                            <th></th>  
                            @if($totalLibras!=0)
                                <th>{{$totalLibras}}</th>
                            @else
                                <th></th>
                            @endif
                            @if($totalKilos!=0)
                                <th>{{$totalKilos}}</th>
                            @else
                                <th></th>
                            @endif
                            @if($totalTm!=0)
                                <th>{{$totalTm}}</th>  
                            @else
                                <th></th>  
                            @endif                        
                            <th></th>
                            <th></th>
                        </tr>                    
                    </tbody>
                </table>
            </div>
        </form>    
    </div>
</div>
@endsection