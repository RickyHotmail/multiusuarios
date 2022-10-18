@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Prestamos de Producto</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("ingresoPrestamo/cuadre") }}">
        @csrf
            <div class="form-group row">
                <label for="idcliente" class="col-sm-1 col-form-label"><center>Cliente:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="idcliente" name="idcliente" >
                        <option value="0" label>--Seleccione un Cliente--</option>
                        @foreach($clientes as $cliente)                            
                            <option value="{{$cliente->cliente_id}}" @if(isset($clienteselect)) @if($clienteselect == $cliente->cliente_id) selected @endif @endif>{{$cliente->cliente_nombre}}</option>                        
                        @endforeach
                    </select> 
                </div>
                <label for="idcliente" class="col-sm-1 col-form-label"><center>Fecha Corte:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha" name="fecha" @if(isset($fecha)) value='{{$fecha}}'  @else value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif >
                </div>
                <div class="col-sm-2">   
                   <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <button type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                </div>
                
            </div> 

                 
        </form>        
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Movimientos de prestamos</h5>
        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-sm-6">
                    <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>MOVIMIENTOS INGRESOS DE PRESTAMOS</CENTER></h5>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Documento</th>
                                <th class="text-center">Numero</th>
                                <th class="text-center">Valor</th>
                                <th class="text-center">Descripcion</th>
                                                        
                                </tr>
                            </thead>
                            <tbody>                           
                                @if(isset($movimientos))
                                    @foreach($movimientos as $movimiento)     
                                        @if($movimiento->movimiento_tipo=='ENTRADA')                                                           
                                        <tr class="text-center">                                        
                                            <td class="text-center">{{ $movimiento->movimiento_fecha }}</td>
                                            <td class="text-center">{{ $movimiento->movimiento_documento}} </td>
                                            <td class="text-center">{{ $movimiento->movimiento_numero_documento }} </td>
                                            <td class="text-center">{{  number_format($movimiento->movimiento_valor,2,'.','') }}</td>   
                                            <td class="text-center">{{ $movimiento->movimiento_descripcion }} </td>
                                        
                                        </tr>  
                                        @endif                       
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                
                </div>
            
                <div class="col-sm-6">
                    <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>MOVIMIENTOS EGRESOS DE PRESTAMOS</CENTER></h5>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Documento</th>
                                <th class="text-center">Numero</th>
                                <th class="text-center">Valor</th>
                                <th class="text-center">Descripcion</th>
                                </tr>
                            </thead>
                            <tbody>                           
                                @if(isset($movimientos))
                                    @foreach($movimientos as $movimiento)     
                                        @if($movimiento->movimiento_tipo=='SALIDA')                                                           
                                        <tr class="text-center">                                        
                                            <td class="text-center">{{ $movimiento->movimiento_fecha }}</td>
                                            <td class="text-center">{{ $movimiento->movimiento_documento}} </td>
                                            <td class="text-center">{{ $movimiento->movimiento_numero_documento }} </td>
                                            <td class="text-center">{{ number_format($movimiento->movimiento_valor,2,'.','')}}</td>   
                                            <td class="text-center">{{ $movimiento->movimiento_descripcion }} </td>
                                        
                                        </tr>  
                                        @endif                       
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                
            </div> 
        </div> 
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Totales Movimientos de prestamos</h5>
        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-sm-6">
                    <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>TOTAL INGRESOS DE PRESTAMOS</CENTER></h5>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                
                                <th class="text-center">Producto</th>
                                <th class="text-center">Valor</th>
                                
                                                        
                                </tr>
                            </thead>
                            <tbody>                           
                                @if(isset($movimientostotalI))
                                    @foreach($movimientostotalI as $movimiento)     
                                                                                              
                                        <tr class="text-center">                                        
                                            <td class="text-center">{{ $movimiento->producto_nombre }} </td>
                                            <td class="text-center">{{  number_format($movimiento->sum,2,'.','') }}</td>   
                                        </tr>  
                                                   
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                
                </div>
            
                <div class="col-sm-6">
                    <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>TOTAL EGRESOS DE PRESTAMOS</CENTER></h5>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Valor</th>
                                </tr>
                            </thead>
                            <tbody>                           
                                @if(isset($movimientostotalE))
                                    @foreach($movimientostotalE as $movimiento)     
                                                                                              
                                        <tr class="text-center">                                        
                                            <td class="text-center">{{ $movimiento->producto_nombre }} </td>
                                            <td class="text-center">{{  number_format($movimiento->sum,2,'.','') }}</td>   
                                        </tr>  
                                                   
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                
            </div> 
        </div>  
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Totales Movimientos de prestamos</h5>
        <div class="col-sm-12">
            <div class="form-group row">
                <div class="col-sm-6">
                    <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>DIFERENCIA DE PRESTAMOS</CENTER></h5>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                
                                <th class="text-center">Producto</th>
                                <th class="text-center">Valor</th>
                                
                                                        
                                </tr>
                            </thead>
                            <tbody>                           
                            @if(isset($movimientostotal))
                                    @for ($i = 1; $i <= count($movimientostotal); ++$i)    
                                                                                              
                                        <tr class="text-center">                                        
                                            <td class="text-center">{{ $movimientostotal[$i]['producto']}} </td>
                                            <td class="text-center">{{  number_format($movimientostotal[$i]['valor'],2,'.','') }}</td>   
                                        </tr>  
                                                   
                                    @endfor
                                    @endif
                            </tbody>
                        </table>
                    </div>
                
                </div>
            
                <div class="col-sm-6">
                    
                    
                </div>
                
            </div> 
        </div>           
    </div>  
</div>
<!-- /.card -->
@endsection
<script type="text/javascript">
     <?php
   if(isset($clienteselect)){  
        ?>
         document.getElementById("idcliente").value='<?php echo($clienteselect); ?>';
         <?php
    }
        ?>
</script>