@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
            <tr><td colspan="2" class="centrar letra15 negrita">REPORTE DE CIERRE DE CAJA DIARIO</td></tr>           
    @endsection    
    <h5 class="form-control" style="color:#fff; background:#17a2b8;">DATOS DE GENERALES</h5>
    <table style="white-space: normal!important; border-collapse: collapse;">
            <tr class="letra14">
                <td class="negrita" style="width: 150px;">Fecha de Cierre:</td>
                <td>{{$arqueoCaja->arqueo_fecha}}</td>  
                <td class="negrita" style="width: 150px;">Caja:</td>
                <td>{{$arqueoCaja->caja->caja_nombre}}</td>                           
            </tr>           
            <tr class="letra14">                
                <td class="negrita" style="width: 150px;">Hora de Cierre:</td>
                <td>{{$arqueoCaja->arqueo_hora}}</td> 
                <td class="negrita" style="width: 150px;">Usuario:</td>
                <td>{{$arqueoCaja->usuario->user_nombre}}</td>               
            </tr> 
            <tr class="letra14">                
                <td class="negrita" style="width: 150px;">Monto:</td>
                <td>${{number_format($arqueoCaja->arqueo_monto, 2)}}</td>             
            </tr>         
    </table >    
    <h5 class="form-control" style="color:#fff; background:#17a2b8;">CONTEO DE EFECTIVO</h5>
    <table class="conBorder">
        <thead>
            <tr>    
                <th bgcolor="#C9C9C9" scope="col" style="border-bottom: 1px solid black;">Monedas</th>
                <th  bgcolor="#C9C9C9" scope="col" style="border-bottom: 1px solid black;">Billetes</th>
            </tr>
        </thead>
        <tbody>
            <tr>    
                <td> <!-- TABLA MONEDAS -->
                    <table>
                    <thead>
                        <tr>    
                            <th  scope="col" style="border-bottom: 1px solid black;">Denominacion</th>
                            <th  scope="col" style="border-bottom: 1px solid black;">Cantidad</th>
                        </tr>
                    </thead>
                        <tbody>
                            <tr>
                                <td><center>Moneda 0,01 ctvos<center></td>
                                <td><center>{{$arqueoCaja->arqueo_moneda01}}<center></td>                              
                            </tr>
                            <tr>
                                <td><center>Moneda 0,05 ctvos<center></td>
                                <td><center>{{$arqueoCaja->arqueo_moneda05}}<center></td>                              
                            </tr>
                            <tr>        
                                <td><center>Moneda 0.10 ctvos<center></td>                      
                                <td><center>{{$arqueoCaja->arqueo_moneda10}}<center></td>                                
                            </tr>
                            <tr>         
                                <td><center>Moneda $0.25 ctvos<center></td>   
                                <td><center>{{$arqueoCaja->arqueo_moneda25}}<center></td>                              
                            </tr>
                            <tr>
                                <td><center>Moneda 0.50 ctvos<center></td>                                
                                <td><center>{{$arqueoCaja->arqueo_moneda50}}<center></td>                              
                            </tr>
                            <tr>
                                <td><center>Moneda $1.00 dolar<center></td>
                                <td><center>{{$arqueoCaja->arqueo_moneda1}}<center></td>
                            </tr>                            
                        </tbody> 
                    </table>
                    <!-- FIN  MONEDAS -->
                </td> <!-- TABLA BILLETES -->
                <td>
                    <table>
                        <thead>
                            <tr>    
                                <th  scope="col" style="border-bottom: 1px solid black;">Denominacion</th>
                                <th  scope="col" style="border-bottom: 1px solid black;">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><center>Billete $1<center></td>
                                <td><center>{{$arqueoCaja->arqueo_billete1}}<center></td>                              
                            </tr>
                            <tr>
                                <td><center>Billete $5<center></td>
                                <td><center>{{$arqueoCaja->arqueo_billete5}}<center></td>                              
                            </tr>
                            <tr>        
                                <td><center>Billete $10<center></td>                      
                                <td><center>{{$arqueoCaja->arqueo_billete10}}<center></td>                                
                            </tr>
                            <tr>         
                                <td><center>Billete $20<center></td>   
                                <td><center>{{$arqueoCaja->arqueo_billete20}}<center></td>                              
                            </tr>
                            <tr>
                                <td><center>Billete $50<center></td>                                
                                <td><center>{{$arqueoCaja->arqueo_billete50}}<center></td>                              
                            </tr>
                            <tr>
                                <td><center>Billete $100<center></td>
                                <td><center>{{$arqueoCaja->arqueo_billete100}}<center></td>
                            </tr>                            
                        </tbody>                   
                    </table>
                    <!-- FIN  BILLETES -->
                </td>     
            </tr>
        </tbody>
    </table>
    <h5 class="form-control" style="color:#fff; background:#17a2b8;">DETALLE DE MOVIMIENTOS</h5>
            <!-- TABLE DE FACTURAS EN EFECTIVO -->
            <table class="conBorder">
                <thead>
                    <tr class="text-center neo-fondo-tabla">  
                        <th class="letra12" bgcolor="#C9C9C9" colspan=5 style="border-bottom: 1px solid black; ">Ventas EN EFECTIVO</th>                                                 
                    </tr>
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; ">Fecha</th>
                        <th class="left" style="border-bottom: 1px solid black; ">Numero</th>
                        <th class="left" style="border-bottom: 1px solid black; ">Nombre</th>
                        <th style="border-bottom: 1px solid black; ">Cantidad</th> 
                        <th style="border-bottom: 1px solid black; ">Valor</th>                           
                    </tr>
                </thead>            
                <tbody>
                <?php $canti=0;?>       
                     
                    @if(isset($MatrizVentasEfectivo))
                       @for ($i = 1; $i <= count($MatrizVentasEfectivo); ++$i)               
                        <tr class="letra12">
                            <td  width="20"><center>{{ $MatrizVentasEfectivo[$i]['fecha'] }}</center></td>
                            <td class="left" width="20">{{ $MatrizVentasEfectivo[$i]['numero']}}</td>
                            <td class="left" width="30%" style="white-space: pre-wrap;">{{ $MatrizVentasEfectivo[$i]['nombre']}}</td> 
                            <td class="center" width="5%">{{ $MatrizVentasEfectivo[$i]['cantidad']}}</td>    
                            <?php $canti=$canti+$MatrizVentasEfectivo[$i]['cantidad'];?>                       
                            <td><center>{{ $MatrizVentasEfectivo[$i]['valor']}}</center></td>
                        </tr>
                        @endfor
                    @endif
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; ">Total</th>
                        <th class="left" style="border-bottom: 1px solid black; ">{{$canti}}</th> 
                        <th style="border-bottom: 1px solid black; ">${{number_format($sumarEfectivo, 2)}}</th>                           
                    </tr>
                </tbody>
            </table>
            <br>
            <!-- TABLE DE FACTURAS DE CONTADO -->
             <table class="conBorder">
                <thead>
                    <tr class="text-center neo-fondo-tabla">  
                        <th class="letra12" bgcolor="#C9C9C9" colspan=5 style="border-bottom: 1px solid black; ">Ventas de CONTADO</th>                                                 
                    </tr>
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; ">Fecha</th>
                        <th class="left" style="border-bottom: 1px solid black; ">Numero</th>
                        <th class="left" style="border-bottom: 1px solid black;">Nombre</th>
                        <th style="border-bottom: 1px solid black; ">Cantidad</th> 
                        <th style="border-bottom: 1px solid black; ">Valor</th>                           
                    </tr>
                </thead>            
                <tbody>
                    @if(isset($MatrizVentasContado))
                        @for ($i = 1; $i <= count($MatrizVentasContado); ++$i)               
                        <tr class="letra12">
                            <td><center>{{ $MatrizVentasContado[$i]['fecha'] }}</center></td>
                            <td class="left" width="20">{{ $MatrizVentasContado[$i]['numero']}}</td>
                            <td class="left" width="30%" style="white-space: pre-wrap;">{{ $MatrizVentasContado[$i]['nombre']}}</td> 
                            <td class="center" width="5%">{{ $MatrizVentasContado[$i]['cantidad']}}</td> 
                            <td><center>{{ $MatrizVentasContado[$i]['valor']}}</center></td>
                        </tr>
                        @endfor
                    @endif
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; "></th>
                        <th style="border-bottom: 1px solid black; ">Total</th> 
                        <th style="border-bottom: 1px solid black; ">${{number_format($sumarContado, 2)}}</th>                           
                    </tr>
                </tbody>
            </table>
            <br>
            <!-- TABLE DE FACTURAS DE CREDITO -->
            <table class="conBorder">
                <thead>
                    <tr class="text-center neo-fondo-tabla">  
                        <th class="letra12" bgcolor="#C9C9C9" colspan=5 style="border-bottom: 1px solid black; ">Ventas de CREDITO</th>                                                 
                    </tr>
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; ">Fecha</th>
                        <th class="left" style="border-bottom: 1px solid black; ">Numero</th>
                        <th class="left" style="border-bottom: 1px solid black; ">Nombre</th>
                        <th style="border-bottom: 1px solid black; ">cantidad</th> 
                        <th style="border-bottom: 1px solid black; ">Valor</th>                           
                    </tr>
                </thead>            
                <tbody>
                    @if(isset($MatrizVentasCredito))
                        @for ($i = 1; $i <= count($MatrizVentasCredito); ++$i)               
                        <tr class="letra12">
                            <td><center>{{ $MatrizVentasCredito[$i]['fecha'] }}</center></td>
                            <td class="left" width="20">{{ $MatrizVentasCredito[$i]['numero']}}</td>
                            <td class="left" width="30%" style="white-space: pre-wrap;">{{ $MatrizVentasCredito[$i]['nombre']}}</td>
                            <td class="center" width="5%">{{ $MatrizVentasCredito[$i]['cantidad']}}</td> 
                            <td><center>{{ $MatrizVentasCredito[$i]['valor']}}</center></td>
                        </tr>
                        @endfor
                    @endif
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; "></th>
                        <th style="border-bottom: 1px solid black; ">Total</th> 
                        <th style="border-bottom: 1px solid black; ">${{number_format($sumarCredito, 2)}}</th>                           
                    </tr>
                </tbody>
            </table>
            <br>
            <!-- TABLE DE EGRESO DE CAJA -->
            <table class="conBorder">
                <thead>
                    <tr class="text-center neo-fondo-tabla">  
                        <th class="letra12" bgcolor="#C9C9C9" colspan=4 style="border-bottom: 1px solid black; ">Egresos de Caja</th>                                                 
                    </tr>
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; ">Fecha</th>
                        <th class="left" style="border-bottom: 1px solid black; ">Descripcion</th>
                        <th style="border-bottom: 1px solid black; ">Diario</th> 
                        <th style="border-bottom: 1px solid black; ">Valor</th>                           
                    </tr>
                </thead>            
                <tbody>
                    @if(isset($MatrizEgresoCaja))
                        @for ($i = 1; $i <= count($MatrizEgresoCaja); ++$i)               
                        <tr class="letra12">
                            <td><center>{{ $MatrizEgresoCaja[$i]['fecha'] }}</center></td>
                            <td class="left" width="50%" style="white-space: pre-wrap;">{{ $MatrizEgresoCaja[$i]['descripcion']}}</td>
                            <td class="right">{{ $MatrizEgresoCaja[$i]['diario']}}</td> 
                            <td><center>{{ $MatrizEgresoCaja[$i]['valor']}}</center></td>
                        </tr>
                        @endfor
                    @endif
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; "></th>
                        <th style="border-bottom: 1px solid black; ">Total</th> 
                        <th style="border-bottom: 1px solid black; ">${{number_format($sumarEgreso, 2)}}</th>                           
                    </tr>
                </tbody>
            </table>
            <br>           
            <!-- TABLA DE INGRESOS DE CAJA -->
            <table class="conBorder">
                <thead>
                    <tr class="text-center neo-fondo-tabla">  
                        <th  class="letra12" bgcolor="#C9C9C9" colspan=4 style="border-bottom: 1px solid black; ">Ingresos de Caja</th>                                                 
                    </tr>
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; ">Fecha</th>
                        <th class="left" style="border-bottom: 1px solid black; ">Descripcion</th>
                        <th style="border-bottom: 1px solid black; ">Diario</th> 
                        <th style="border-bottom: 1px solid black; ">Valor</th>                           
                    </tr>
                </thead>            
                <tbody>
                    @if(isset($MatrizIngresoCaja))
                        @for ($i = 1; $i <= count($MatrizIngresoCaja); ++$i)               
                        <tr class="letra12">
                            <td><center>{{ $MatrizIngresoCaja[$i]['fecha'] }}</center></td>
                            <td class="left" width="50%" style="white-space: pre-wrap;">{{ $MatrizIngresoCaja[$i]['descripcion']}}</td>
                            <td class="right">{{ $MatrizIngresoCaja[$i]['diario']}}</td> 
                            <td><center>{{ $MatrizIngresoCaja[$i]['valor']}}</center></td>
                        </tr>
                        @endfor
                    @endif
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; "></th>
                        <th style="border-bottom: 1px solid black; ">Total</th> 
                        <th style="border-bottom: 1px solid black; ">${{number_format($sumarIngreso, 2)}}</th>                           
                    </tr>
                </tbody>
            </table>
            <div style="page-break-inside: avoid;">
            <br>
            <!-- TABLA DE FALTANTE DE CAJA -->
            <table class="conBorder">
                <thead>
                    <tr class="text-center neo-fondo-tabla">  
                        <th  class="letra12" bgcolor="#C9C9C9" colspan=4 style="border-bottom: 1px solid black; ">Faltantes de Caja</th>                                                 
                    </tr>
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; ">Fecha</th>
                        <th class="left" style="border-bottom: 1px solid black; ">Descripcion</th>
                        <th style="border-bottom: 1px solid black; ">Diario</th> 
                        <th style="border-bottom: 1px solid black; ">Valor</th>                           
                    </tr>
                </thead>            
                <tbody>
                    @if(isset($MatrizFaltanteCaja))
                        @for ($i = 1; $i <= count($MatrizFaltanteCaja); ++$i)               
                        <tr class="letra12">
                            <td><center>{{ $MatrizFaltanteCaja[$i]['fecha'] }}</center></td>
                            <td class="left" width="50%" style="white-space: pre-wrap;">{{ $MatrizFaltanteCaja[$i]['descripcion']}}</td>
                            <td class="right">{{ $MatrizFaltanteCaja[$i]['diario']}}</td> 
                            <td><center>{{ $MatrizFaltanteCaja[$i]['valor']}}</center></td>
                        </tr>
                        @endfor
                    @endif
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; "></th>
                        <th style="border-bottom: 1px solid black; ">Total</th> 
                        <th style="border-bottom: 1px solid black; ">${{number_format($sumarFaltante, 2)}}</th>                           
                    </tr>
                </tbody>
            </table>
            <div style="page-break-inside: avoid;">
            <br>
            <!-- TABLA DE SOBRANTE DE CAJA -->
            <table class="conBorder">
                <thead>
                    <tr class="text-center neo-fondo-tabla">  
                        <th  class="letra12" bgcolor="#C9C9C9" colspan=4 style="border-bottom: 1px solid black; ">Sobrantes de Caja</th>                                                 
                    </tr>
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; ">Fecha</th>
                        <th class="left" style="border-bottom: 1px solid black; ">Descripcion</th>
                        <th style="border-bottom: 1px solid black; ">Diario</th> 
                        <th style="border-bottom: 1px solid black; ">Valor</th>                           
                    </tr>
                </thead>            
                <tbody>
                    @if(isset($MatrizSobranteCaja))
                        @for ($i = 1; $i <= count($MatrizSobranteCaja); ++$i)               
                        <tr class="letra12">
                            <td><center>{{ $MatrizSobranteCaja[$i]['fecha'] }}</center></td>
                            <td class="left" width="50%" style="white-space: pre-wrap;">{{ $MatrizSobranteCaja[$i]['descripcion']}}</td>
                            <td class="right">{{ $MatrizSobranteCaja[$i]['diario']}}</td> 
                            <td><center>{{ $MatrizSobranteCaja[$i]['valor']}}</center></td>
                        </tr>
                        @endfor
                    @endif
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; "></th>
                        <th style="border-bottom: 1px solid black; ">Total</th> 
                        <th style="border-bottom: 1px solid black; ">${{number_format($sumarSobrante, 2)}}</th>                           
                    </tr>
                </tbody>
            </table> 
            <div style="page-break-inside: avoid;">
            <br>
             <!-- TABLA DE CXC DE CAJA -->
             <table class="conBorder">
                <thead>
                    <tr class="text-center neo-fondo-tabla">  
                        <th  class="letra12" bgcolor="#C9C9C9" colspan=4 style="border-bottom: 1px solid black; ">CXC de Clientes</th>                                                 
                    </tr>
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; ">Fecha</th>
                        <th class="left" style="border-bottom: 1px solid black; ">Descripcion</th>
                        <th style="border-bottom: 1px solid black; ">Diario</th> 
                        <th style="border-bottom: 1px solid black; ">Valor</th>                           
                    </tr>
                </thead>            
                <tbody>
                    @if(isset($MatrizCuentaCobrar))
                        @for ($i = 1; $i <= count($MatrizCuentaCobrar); ++$i)               
                        <tr class="letra12">
                            <td><center>{{ $MatrizCuentaCobrar[$i]['fecha'] }}</center></td>
                            <td class="left" width="50%" style="white-space: pre-wrap;">{{ $MatrizCuentaCobrar[$i]['descripcion']}}</td>
                            <td class="right">{{ $MatrizCuentaCobrar[$i]['diario']}}</td> 
                            <td><center>{{ $MatrizCuentaCobrar[$i]['valor']}}</center></td>
                        </tr>
                        @endfor
                    @endif
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; "></th>
                        <th style="border-bottom: 1px solid black; ">Total</th> 
                        <th style="border-bottom: 1px solid black; ">${{number_format($sumarCXC, 2)}}</th>                           
                    </tr>
                </tbody>
            </table>
            <div style="page-break-inside: avoid;">
            <br>
            <!-- TABLA DE CXP DE CAJA -->
            <table class="conBorder">
                <thead>
                    <tr class="text-center neo-fondo-tabla">  
                        <th  class="letra12" bgcolor="#C9C9C9" colspan=4 style="border-bottom: 1px solid black; ">CXP de Provedores</th>                                                 
                    </tr>
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; ">Fecha</th>
                        <th class="left" style="border-bottom: 1px solid black; ">Descripcion</th>
                        <th style="border-bottom: 1px solid black; ">Diario</th> 
                        <th style="border-bottom: 1px solid black; ">Valor</th>                           
                    </tr>
                </thead>            
                <tbody>
                    @if(isset($MatrizCuentaPagar))
                        @for ($i = 1; $i <= count($MatrizCuentaPagar); ++$i)               
                        <tr class="letra12">
                            <td><center>{{ $MatrizCuentaPagar[$i]['fecha'] }}</center></td>
                            <td class="left" width="50%" style="white-space: pre-wrap;">{{ $MatrizCuentaPagar[$i]['descripcion']}}</td>
                            <td class="right">{{ $MatrizCuentaPagar[$i]['diario']}}</td> 
                            <td><center>{{ $MatrizCuentaPagar[$i]['valor']}}</center></td>
                        </tr>
                        @endfor
                    @endif
                    <tr class="letra12">  
                        <th style="border-bottom: 1px solid black; "></th>
                        <th class="left" style="border-bottom: 1px solid black; "></th>
                        <th style="border-bottom: 1px solid black; ">Total</th> 
                        <th style="border-bottom: 1px solid black; ">${{number_format($sumarCXP, 2)}}</th>                           
                    </tr>
                </tbody>
            </table>           
            <!--<div style = "display:block; clear:both; page-break-after:always;"></div>-->
            <div style="page-break-inside: avoid;">
            <br>
            <br>
            <table class="conBorder" style="padding-left: 200px; padding-right: 200px;">
                <thead>
                    <tr class="text-center neo-fondo-tabla">  
                        <th  class="letra12" bgcolor="#C9C9C9" colspan=2 style="border-bottom: 1px solid black; ">Arqueo Segun Movimientos</th>                                                 
                    </tr>                  
                </thead>            
                <tbody>
                    @if(isset($MatrizResumenArqueo))
                        @for ($i = 1; $i <= count($MatrizResumenArqueo); ++$i)               
                        <tr class="letra12">
                            <td><center>Saldo Inicial</center></td>
                            <td><center>{{ $MatrizResumenArqueo[$i]['saldoInicial'] }}</center></td>                           
                        </tr>
                        <tr class="letra12">
                            <td><center>Ventas en Efectivo</center></td>                          
                            <td><center>{{ $MatrizResumenArqueo[$i]['VentasEfectivosum']}}</center></td>
                        </tr>
                        <tr class="letra12">
                            <td><center>CXC de Clientes</center></td>                          
                            <td><center>{{ $MatrizResumenArqueo[$i]['CobrosCliente']}}</center></td>
                        </tr>
                        <tr class="letra12">
                            <td><center>CXP de Proveedores</center></td>                          
                            <td><center>{{ $MatrizResumenArqueo[$i]['PagosProveedor']}}</center></td>
                        </tr>
                        <tr class="letra12">
                            <td><center>Egresos de caja</center></td>                          
                            <td><center>{{ $MatrizResumenArqueo[$i]['Egresos']}}</center></td>
                        </tr>
                        <tr class="letra12">
                            <td><center>Ingresos de Caja</center></td>
                            <td><center>{{ $MatrizResumenArqueo[$i]['Ingresos']}}</center></td>
                        </tr>
                        <tr class="letra12">
                            <td><center>Faltantes de Caja</center></td>
                            <td><center>{{ $MatrizResumenArqueo[$i]['Faltantes'] }}</center></td>                           
                        </tr>
                        <tr class="letra12"> 
                            <td><center>Sobrantes de Caja</center></td>                         
                            <td><center>{{ $MatrizResumenArqueo[$i]['Sobrantes']}}</center></td>
                        </tr>
                        <tr class="letra12"> 
                            <td><center>Saldo a Conciliar</center></td> 
                            <td><center>{{ $MatrizResumenArqueo[$i]['saldoConciliar']}}</center></td>
                        </tr>
                        <tr class="letra12">  
                            <td><center>Conteo de Efectivo</center></td>
                            <td><center>{{ $MatrizResumenArqueo[$i]['conteoEfectivo']}}</center></td>
                        </tr>
                        @endfor
                    @endif
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
