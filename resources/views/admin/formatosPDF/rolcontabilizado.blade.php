@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">Reporte de Empleados</td></tr>
        <tr><td colspan="2" class="centrar letra15 borde-gris">FECHA :  {{ $desde }}  AL {{ $hasta }} </td></tr>
    @endsection

    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra12">
                <th></th>
                <th colspan="7">Ingresos</th>  
                <th colspan="13">Egresos</th> 
                <th colspan="9">Beneficios</th> 
                <th ></th> 
            </tr>
            <tr style="border: 1px solid black;" class="centrar letra12">
                <th style="font-size: 5px;">Empleado </th>
                <th style="font-size: 5px;">Sueldo</th>                        
                <th style="font-size: 5px;">Horas Extras </th>
                <th style="font-size: 5px;">Vacaciones </th> 
                <th style="font-size: 5px;">Viaticos </th>                       
                <th style="font-size: 5px;">otros Bonificaciones </th>
                <th style="font-size: 5px;">otros Ingresos </th>
                <th style="font-size: 5px;">Total Ingresos </th>

                <th style="font-size: 5px;">EXT. Salud</th>
                <th style="font-size: 5px;">Ley salud</th>
                <th style="font-size: 5px;">Comisariato </th>      
                <th style="font-size: 5px;">PPQQ </th>
                <th style="font-size: 5px;">Prestamos Hipotecarios</th>
                <th style="font-size: 5px;">Prestamos </th>
                <th style="font-size: 5px;">Multas</th>
                <th style="font-size: 5px;">IESS Asum.</th>
                <th style="font-size: 5px;">Apr. Perso</th>
                <th style="font-size: 5px;">Anticipos</th>
                <th style="font-size: 5px;">Imp. Renta</th>
                <th style="font-size: 5px;">Otros Egresos</th>
                <th style="font-size: 5px;">Total Egresos</th>

                <th style="font-size: 5px;">Apr. Patro</th>
                <th style="font-size: 5px;">Vacaciones Pagadas</th>
                <th style="font-size: 5px;">Dec. Tercero</th>
                <th style="font-size: 5px;">Dec. Cuarto</th>
                <th style="font-size: 5px;">F. Reser.</th>
                <th style="font-size: 5px;">IECE / SETEC</th>
                
                <th style="font-size: 5px;">Liq. Pagar</th>
            </tr>
            
           
        </thead>
        <tbody>
            @if(isset($rol))
                @if(count($rol)>0)
                    @foreach($rol as $roles)
                    <tr style="font-size: 7px; border: 1px solid black;">
                    <td style="border: 1px solid black;"><input type="hidden" class="form-controltext"   name="empleadoid[]" value="{{$roles->empleado_id}}" required readonly> {{$roles->empleado_nombre}}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->sueldos, 2)}}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->extras , 2)}}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->vacaciones , 2)}}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->transporte , 2)}}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->otrabonifi, 2)}} </td>
                    <td style="border: 1px solid black;">{{number_format($roles->otrosingresos, 2)}} </td> 
                    <td  style="border: 1px solid black;">{{number_format($roles->ingresos, 2)}}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->extsalud, 2)}} </td>
                    <td style="border: 1px solid black;">{{number_format($roles->leysal, 2) }}</td>
                
                    <td style="border: 1px solid black;">{{number_format($roles->comisariato, 2)}}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->ppqq, 2)}} </td>
                    <td style="border: 1px solid black;">{{number_format($roles->hipoteca, 2)}}</td> 
                    <td style="border: 1px solid black;">{{number_format($roles->prestamos, 2)}} </td>
                    <td style="border: 1px solid black;">{{number_format($roles->multas , 2)}}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->asumido , 2)}}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->personal, 2) }}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->anticipo, 2) }}</td>
                  
                    <td style="border: 1px solid black;">{{number_format($roles->impu_renta, 2) }}</td>

                    <td style="border: 1px solid black;">{{number_format($roles->otrosegre, 2)}} </td>
                    <td style="border: 1px solid black;">{{number_format($roles->egresos, 2)}}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->patronal, 2)}}</td>
                    <td style="border: 1px solid black;">{{number_format($roles->vacacionespag, 2)}}</td>
                    <td  @if(number_format($roles->tercero, 2)>0) style="background:  #70B1F7; border: 1px solid black;"  @else style="background:  #B1E2DD; border: 1px solid black;"  @endif  >@if(number_format($roles->tercero, 2)>0) {{number_format($roles->tercero, 2)}} @else {{number_format($roles->terceroacum, 2)}}  @endif</td>                   
                    <td  @if(number_format($roles->cuarto, 2)>0) style="background:  #70B1F7; border: 1px solid black;"  @else style="background:  #B1E2DD; border: 1px solid black;"  @endif  >@if(number_format($roles->cuarto, 2)>0) {{number_format($roles->cuarto, 2)}} @else {{number_format($roles->cuartoacum, 2)}}  @endif</td>                   
                    <td  @if(number_format($roles->fondo_reserva, 2)>0) style="background:  #70B1F7; border: 1px solid black;"  @else style="background:  #B1E2DD; border: 1px solid black;"  @endif  >@if(number_format($roles->fondo_reserva, 2)>0) {{number_format($roles->fondo_reserva, 2)}} @else {{number_format($roles->fondoacumula, 2)}}  @endif</td>
                    <td style="border: 1px solid black;">{{number_format($roles->iecesecap, 2)}}</td>
                    
                    <td style="border: 1px solid black;">{{number_format($roles->liquido_pagar, 2)}}</td>

                    </tr>       
                    @endforeach
                @endif
            @endif 
        </tbody>
    </table>
    
    

    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra12">
             
                <th  colspan="2"> Ingresos</th>
                <th colspan="2">Egresos</th>                        
                <th  colspan="2">Provisiones </th>               
                                  
            </tr>
           
            
           
        </thead>
        <tbody>
            @if(isset($rol))
            @if(count($rol)>0)
                @for ($i = 1; $i <= count($datos); ++$i)  
                <tr style="border: 1px solid black;" class="centrar letra12">
                        <td colspan="6"> {{ $datos[$i]['tipo'] }}</td>
                      
                </tr>
                <tr style="border: 1px solid black;" class="centrar letra12">
                    <td style="border: 1px solid black;">sueldo </td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['sueldos'], 2) }} </td>
                   
                    <td style="border: 1px solid black;" >extsalud </td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['extsalud'], 2) }} </td>
                    
                    <td  style="border: 1px solid black;">Apor. Patro. </td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['patronal'], 2) }} </td>
                   
                </tr>
                <tr style="border: 1px solid black;" class="centrar letra12">
                    <td style="border: 1px solid black;">Horas Extras </td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['extras'], 2) }}</td>
                   
                    <td style="border: 1px solid black;">leysal </td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['leysal'] , 2)}} </td>
                   
                    <td style="border: 1px solid black;">Vacaciones Pag </td>
                    <td style="border: 1px solid black;"> {{number_format( $datos[$i]['vacacionesacu'] , 2)}}</td>
                   
                </tr>
                <tr style="border: 1px solid black;" class="centrar letra12">
                    <td style="border: 1px solid black;">Transporte</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['transporte'], 2) }} </td>
                   
                    <td style="border: 1px solid black;"> Prestamos Quirografarios</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['ppqq'], 2) }} </td>
                   
                    <td style="border: 1px solid black;">Deci. Tercero </td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['tercero'] , 2)}}  </td>
                    
                
                </tr>
                <tr style="border: 1px solid black;" class="centrar letra12">
                    <td style="border: 1px solid black;">Otras Bonificaciones </td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['otrabonifi'] , 2)}} </td>
                 
                    <td style="border: 1px solid black;"> Hipoteca</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['hipoteca'] , 2)}} </td>
                   
                    <td style="border: 1px solid black;">Deci. Cuarto</td>
                    <td style="border: 1px solid black;">{{number_format( $datos[$i]['cuarto'], 2) }} </td>
                  
                    
                </tr>
                <tr style="border: 1px solid black;" class="centrar letra12">
                    <td style="border: 1px solid black;">Otras Ingresos  </td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['otrosingresos'], 2) }} </td>
              
                    <td style="border: 1px solid black;">Comisariato</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['comisariato'] , 2)}} </td>
                  
                    <td style="border: 1px solid black;">F. Reserva</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['fondo_reserva'] , 2)}} </td>
                
                    
                </tr>                    
                <tr style="border: 1px solid black;" class="centrar letra12">
                    <td style="border: 1px solid black;"> Vacaciones</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['vacaciones'], 2) }} </td>
                  
                    <td style="border: 1px solid black;">IESS Asum.</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['asumido'], 2) }} </td>
                    
                   
                    <td style="border: 1px solid black;">F. Reserva(Acu)</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['fondo_reservaacu'], 2) }}</td>
                 
                </tr>
                <tr style="border: 1px solid black;" class="centrar letra12">
                <td style="border: 1px solid black;"> Total Ingresos</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['ingresos'], 2) }} </td>
                   
                    <td style="border: 1px solid black;">Apor. Perso.</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['personal'] , 2)}} </td>
                  
                    <td style="border: 1px solid black;"> IESCE/SECAP</td>
                    <td style="border: 1px solid black;"> {{number_format( $datos[$i]['iecesecap'] , 2)}}</td>
                  
                
                </tr>
                <tr style="border: 1px solid black;" class="centrar letra12">
                    <td style="border: 1px solid black;"> </td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;">Anticipos</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['anticipo'], 2) }} </td>
               
                    <td style="border: 1px solid black;">D. Tercero(Acu)</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['terceroacu'], 2) }} </td>
                  
                </tr>
                
                <tr style="border: 1px solid black;" class="centrar letra12">
                    <td style="border: 1px solid black;"> </td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;">Impu. Renta</td>
                    <td style="border: 1px solid black;">{{number_format( $datos[$i]['impu_renta'], 2) }} </td>
                
                    <td style="border: 1px solid black;">D. Cuarto(Acu)</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['cuartoacu'], 2) }} </td>
                   
                
                </tr>
                <tr style="border: 1px solid black;" class="centrar letra12">
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"> </td>
                    <td style="border: 1px solid black;">Multas</td>
                    <td style="border: 1px solid black;"> {{number_format( $datos[$i]['multas'], 2) }}</td>
                  
                    <td style="border: 1px solid black;">Liquido Pagar</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['liquido_pagar'], 2) }} </td>
                 
                
                </tr>
                <tr style="border: 1px solid black;" class="centrar letra12">
                    <td style="border: 1px solid black;"> </td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;">Otros Egresos</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['otrosegre'], 2) }} </td>
                    
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"> </td>
                </tr>
                <tr style="border: 1px solid black;" class="centrar letra12">
                    <td style="border: 1px solid black;"> </td>
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;">Total Egresos</td>
                    <td style="border: 1px solid black;">{{ number_format($datos[$i]['egresos'] , 2)}} </td>
                 
                    <td style="border: 1px solid black;"></td>
                    <td style="border: 1px solid black;"> </td>
                </tr>
                @endfor
            @endif 
            @endif 
        </tbody>
    </table>
<style>
    tbody {
   border-top: 1px solid #000;
   border-bottom: 1px solid #000;
}
tbody th, tfoot th {
   border: 0;
}
th.name {
   width: 25%;
}
th.location {
   width: 20%;
}
th.lasteruption {
   width: 30%;
}
th.eruptiontype {
   width: 25%;
}
tfoot {
   text-align: center;
   color: #555;
   font-size: 0.8em;
}
</style>
@endsection