@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
            <tr><td colspan="2" class="centrar letra15 negrita">REPORTE DE PRESTAMOS DE PRODUCTO</td></tr>           
    @endsection    
    <h5 class="form-control" style="color:#fff; background:#17a2b8;">DATOS DE GENERALES</h5>
    <table style="white-space: normal!important; border-collapse: collapse;">
            <tr class="letra14">
                <td class="negrita" >Ruc:</td>
                <td>{{$cliente->cliente_cedula}}</td>  
                <td class="negrita" >Cliente:</td>
                <td>{{$cliente->cliente_nombre}}</td>                           
            </tr>           
            <tr class="letra14">                
                <td class="negrita" >Fecha de Corte:</td>
                <td>{{$fecha}}</td> 
                               
            </tr> 
                
    </table >    
    <h5 class="form-control" style="color:#fff; background:#17a2b8;">MOVIMIENTOS DE INGRESOS</h5>
    <table class="conBorder">
        <tbody>
            <tr>    
                <td> <!-- TABLA MONEDAS -->
                    <table>
                    <thead>
                        <tr>    
                            <th  scope="col" style="border-bottom: 1px solid black;">Fecha</th>
                            <th  scope="col" style="border-bottom: 1px solid black;">Documento</th>
                            <th  scope="col" style="border-bottom: 1px solid black;">Numero</th>
                    
                            <th  scope="col" style="border-bottom: 1px solid black;">Valor</th>
                        </tr>
                    </thead>
                        <tbody>
                           
                                @if(isset($movimientos))
                                    @foreach($movimientos as $movimiento)     
                                        @if($movimiento->movimiento_tipo=='ENTRADA')      
                                        <tr>       
                                        <td><center>{{ $movimiento->movimiento_fecha }}<center></td>
                                            <td><center>{{ $movimiento->movimiento_documento }}<center></td>
                                            <td><center>{{ $movimiento->movimiento_numero_documento }}<center></td>

                                            <td><center>{{  number_format($movimiento->movimiento_valor,2,'.','') }}<center></td>  
                                        </tr>
                                        @endif                       
                                    @endforeach
                                @endif
                            
                                               
                        </tbody> 
                    </table>
                    <!-- FIN  MONEDAS -->
                </td> <!-- TABLA BILLETES -->
                  
            </tr>
        </tbody>
    </table>
    <h5 class="form-control" style="color:#fff; background:#17a2b8;">MOVIMIENTOS DE EGRESOS</h5>
    <table class="conBorder">
        <tbody>
            <tr>    
                <td> <!-- TABLA MONEDAS -->
                    <table>
                    <thead>
                        <tr>    
                            <th  scope="col" style="border-bottom: 1px solid black;">Fecha</th>
                            <th  scope="col" style="border-bottom: 1px solid black;">Documento</th>
                            <th  scope="col" style="border-bottom: 1px solid black;">Numero</th>
         
                            <th  scope="col" style="border-bottom: 1px solid black;">Valor</th>
                        </tr>
                    </thead>
                        <tbody>
                           
                                @if(isset($movimientos))
                                    @foreach($movimientos as $movimiento)     
                                        @if($movimiento->movimiento_tipo=='SALIDA') 
                                        <tr>            
                                            <td><center>{{ $movimiento->movimiento_fecha }}<center></td>
                                            <td><center>{{ $movimiento->movimiento_documento }}<center></td>
                                            <td><center>{{ $movimiento->movimiento_numero_documento }}<center></td>

                                            <td><center>{{  number_format($movimiento->movimiento_valor,2,'.','') }}<center></td>  
                                        </tr>
                                        @endif                       
                                    @endforeach
                                @endif
                            
                                               
                        </tbody> 
                    </table>
                    <!-- FIN  MONEDAS -->
                </td> <!-- TABLA BILLETES -->
                  
            </tr>
        </tbody>
    </table>
    <h5 class="form-control" style="color:#fff; background:#17a2b8;">TOTAL PRODUCTOS DE INGRESOS</h5>
    <table class="conBorder">
        <tbody>
            <tr>    
                <td> <!-- TABLA MONEDAS -->
                    <table>
                    <thead>
                        <tr>    
                            <th  scope="col" style="border-bottom: 1px solid black;">Producto</th>
                            <th  scope="col" style="border-bottom: 1px solid black;">Valor</th>
                           
                        </tr>
                    </thead>
                        <tbody>
                           
                                @if(isset($movimientostotalI))
                                    @foreach($movimientostotalI as $movimiento)        
                                    <tr>            
                                            <td><center>{{ $movimiento->producto_nombre}}<center></td>
                                            <td><center>{{ number_format($movimiento->sum,2,'.','') }}<center></td> 
                                    </tr>           
                                    @endforeach
                                @endif
                            
                                               
                        </tbody> 
                    </table>
                    <!-- FIN  MONEDAS -->
                </td> <!-- TABLA BILLETES -->
                  
            </tr>
        </tbody>
    </table>
    <h5 class="form-control" style="color:#fff; background:#17a2b8;">TOTAL PRODUCTOS DE EGRESOS</h5>
    <table class="conBorder">
        <tbody>
            <tr>    
                <td> <!-- TABLA MONEDAS -->
                    <table>
                    <thead>
                        <tr>    
                            <th  scope="col" style="border-bottom: 1px solid black;">Producto</th>
                            <th  scope="col" style="border-bottom: 1px solid black;">Valor</th>
                        </tr>
                    </thead>
                        <tbody>
                            
                                @if(isset($movimientostotalE))
                                    @foreach($movimientostotalE as $movimiento)            
                                    <tr>        
                                            <td><center>{{ $movimiento->producto_nombre}}<center></td>
                                            <td><center>{{ number_format($movimiento->sum,2,'.','') }}<center></td> 
                                    </tr>            
                                    @endforeach
                                @endif
                           
                                               
                        </tbody> 
                    </table>
                    <!-- FIN  MONEDAS -->
                </td> <!-- TABLA BILLETES -->
                  
            </tr>
        </tbody>
    </table>
    <br>
    <h5 class="form-control" style="color:#fff; background:#17a2b8;">DIFERENCIA DE PRESTAMOS</h5>
    <!-- TABLE DE FACTURAS DE CONTADO -->
    <table class="conBorder">
        <tbody>
            <tr>    
                <td> <!-- TABLA MONEDAS -->
                    <table>
                    <thead>
                        <tr>    
                            <th  scope="col" style="border-bottom: 1px solid black;">Producto</th>
                            <th  scope="col" style="border-bottom: 1px solid black;">Valor</th>
                        </tr>
                    </thead>
                        <tbody>
                         
                                @if(isset($movimientostotal))
                                    @for ($i = 1; $i <= count($movimientostotal); ++$i)  
                                    <tr>                   
                                            <td><center>{{ $movimientostotal[$i]['producto']}}<center></td>
                                            <td><center>{{ number_format($movimientostotal[$i]['valor'],2,'.','') }}<center></td>     
                                    </tr>        
                                    @endfor
                                @endif
                           
                                               
                        </tbody> 
                    </table>
                    <!-- FIN  MONEDAS -->
                </td> <!-- TABLA BILLETES -->
                  
            </tr>
        </tbody>
    </table>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <table class="table">
        <thead>
            <tr class="letra12">
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <tr class="letra12">
            <td><center>----------------------------</center></td>
            <td><center>----------------------------</center></td>
            <td><center>----------------------------</center></td>
            </tr>
            <tr class="letra12">
            <td width="50%" ><center>RECIBIDO</center></td>
            <td width="50%"><center>ENTREGADO</center></td>
            <td width="50%"><center>REVISADO POR</center></td>
            </tr>
        </tbody>
    </table>            

@endsection
