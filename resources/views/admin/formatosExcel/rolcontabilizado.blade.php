<table>
    <tr>
        <td colspan="10" style="text-align: center;">NEOPAGUPA | Sistema Contable</td>
    </tr>
    <tr>
        <td colspan="10" style="text-align: center;">Rol Detallado Empleado</td>
    </tr>
</table>
<table>
    <thead>
        <?php $rol=$datos[1];  $datos=$datos[2];   ?>
        <tr class="text-center">
            <th></th>  
            <th colspan="7">Ingresos</th>  
            <th colspan="13">Egresos</th> 
            <th colspan="9">Beneficios</th> 
            <th ></th> 
        </tr>   
        <tr class="text-center">
        <th  >Empleado </th>
                <th  >Sueldo</th>                        
                <th  >Horas Extras </th>
                <th  >Vacaciones </th> 
                <th  >Viaticos </th>                       
                <th  >otros Bonificaciones </th>
                <th  >otros Ingresos </th>
                <th  >Total Ingresos </th>

                <th  >EXT. Salud</th>
                <th  >Ley salud</th>
                <th  >Comisariato </th>      
                <th  >PPQQ </th>
                <th  >Prestamos Hipotecarios</th>
                <th  >Prestamos </th>
                <th  >Multas</th>
                <th  >IESS Asum.</th>
                <th  >Apr. Perso</th>
                <th  >Anticipos</th>
                <th  >Imp. Renta</th>
                <th  >Otros Egresos</th>
                <th  >Total Egresos</th>

                <th  >Apr. Patro</th>
                <th  >Vacaciones Pagadas</th>
                <th  >Dec. Tercero</th>
                <th  >Dec. Cuarto</th>
                <th  >F. Reser.</th>
                <th  >IECE / SETEC</th>
                
                <th  >Liq. Pagar</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($rol))
            @if(count($rol)>0)
                @foreach($rol as $roles)
                <tr>
                <td >  {{$roles->empleado_nombre}}</td>
                <td >{{number_format($roles->sueldos, 2)}}</td>
                <td >{{number_format($roles->extras , 2)}}</td>
                <td >{{number_format($roles->vacaciones , 2)}}</td>
                <td >{{number_format($roles->transporte , 2)}}</td>
                <td >{{number_format($roles->otrabonifi, 2)}} </td>
                <td >{{number_format($roles->otrosingresos, 2)}} </td> 
                <td  >{{number_format($roles->ingresos, 2)}}</td>
                <td >{{number_format($roles->extsalud, 2)}} </td>
                <td >{{number_format($roles->leysal, 2) }}</td>
            
                <td >{{number_format($roles->comisariato, 2)}}</td>
                <td >{{number_format($roles->ppqq, 2)}} </td>
                <td >{{number_format($roles->hipoteca, 2)}}</td> 
                <td >{{number_format($roles->prestamos, 2)}} </td>
                <td >{{number_format($roles->multas , 2)}}</td>
                <td >{{number_format($roles->asumido , 2)}}</td>
                <td >{{number_format($roles->personal, 2) }}</td>
                <td >{{number_format($roles->anticipo, 2) }}</td>
              
                <td >{{number_format($roles->impu_renta, 2) }}</td>

                <td >{{number_format($roles->otrosegre, 2)}} </td>
                <td >{{number_format($roles->egresos, 2)}}</td>
                <td >{{number_format($roles->patronal, 2)}}</td>
                <td >{{number_format($roles->vacacionespag, 2)}}</td>
                <td  @if(number_format($roles->tercero, 2)>0) style="background:  #70B1F7;"  @else style="background:  #B1E2DD;"  @endif  >@if(number_format($roles->tercero, 2)>0) {{number_format($roles->tercero, 2)}} @else {{number_format($roles->terceroacum, 2)}}  @endif</td>                   
                <td  @if(number_format($roles->cuarto, 2)>0) style="background:  #70B1F7;"  @else style="background:  #B1E2DD;"  @endif  >@if(number_format($roles->cuarto, 2)>0) {{number_format($roles->cuarto, 2)}} @else {{number_format($roles->cuartoacum, 2)}}  @endif</td>                   
                <td  @if(number_format($roles->fondo_reserva, 2)>0) style="background:  #70B1F7;"  @else style="background:  #B1E2DD;"  @endif  >@if(number_format($roles->fondo_reserva, 2)>0) {{number_format($roles->fondo_reserva, 2)}} @else {{number_format($roles->fondoacumula, 2)}}  @endif</td>
                <td >{{number_format($roles->iecesecap, 2)}}</td>
                
                <td >{{number_format($roles->liquido_pagar, 2)}}</td>

                </tr>       
                @endforeach
            @endif
        @endif 
    </tbody>
</table>


<table>
    
        <thead>
            <tr class="text-center">
             
                <th  colspan="2"> Ingresos</th>
                <th colspan="2">Egresos</th>                        
                <th  colspan="2">Provisiones </th>               
                                  
            </tr>
        </thead>
        <tbody>
            @if(isset($rol))
            @if(count($rol)>0)
                @for ($i = 1; $i <= count($datos); ++$i)  
                <tr class="text-center">
                        <td colspan="6"> {{ $datos[$i]['tipo'] }}</td>
                      
                </tr>
                <tr>
                    <td >sueldo </td>
                    <td>{{ number_format($datos[$i]['sueldos'], 2) }} </td>
                   
                    <td >extsalud </td>
                    <td>{{ number_format($datos[$i]['extsalud'], 2) }} </td>
                    
                    <td >Apor. Patro. </td>
                    <td>{{ number_format($datos[$i]['patronal'], 2) }} </td>
                   
                </tr>
                <tr>
                    <td >Horas Extras </td>
                    <td>{{ number_format($datos[$i]['extras'], 2) }}</td>
                   
                    <td >leysal </td>
                    <td>{{ number_format($datos[$i]['leysal'] , 2)}} </td>
                   
                    <td >Vacaciones Pag </td>
                    <td> {{number_format( $datos[$i]['vacacionesacu'] , 2)}}</td>
                   
                </tr>
                <tr>
                    <td >Transporte</td>
                    <td>{{ number_format($datos[$i]['transporte'], 2) }} </td>
                   
                    <td > Prestamos Quirografarios</td>
                    <td>{{ number_format($datos[$i]['ppqq'], 2) }} </td>
                   
                    <td >Deci. Tercero </td>
                    <td>{{ number_format($datos[$i]['tercero'] , 2)}}  </td>
                    
                
                </tr>
                <tr>
                    <td >Otras Bonificaciones </td>
                    <td>{{ number_format($datos[$i]['otrabonifi'] , 2)}} </td>
                 
                    <td > Hipoteca</td>
                    <td>{{ number_format($datos[$i]['hipoteca'] , 2)}} </td>
                   
                    <td >Deci. Cuarto</td>
                    <td>{{number_format( $datos[$i]['cuarto'], 2) }} </td>
                  
                    
                </tr>
                <tr>
                    <td >Otras Ingresos  </td>
                    <td>{{ number_format($datos[$i]['otrosingresos'], 2) }} </td>
              
                    <td >Comisariato</td>
                    <td>{{ number_format($datos[$i]['comisariato'] , 2)}} </td>
                  
                    <td >F. Reserva</td>
                    <td>{{ number_format($datos[$i]['fondo_reserva'] , 2)}} </td>
                
                    
                </tr>                    
                <tr>
                    <td > Vacaciones</td>
                    <td>{{ number_format($datos[$i]['vacaciones'], 2) }} </td>
                  
                    <td >IESS Asum.</td>
                    <td>{{ number_format($datos[$i]['asumido'], 2) }} </td>
                    
                   
                    <td >F. Reserva(Acu)</td>
                    <td>{{ number_format($datos[$i]['fondo_reservaacu'], 2) }}</td>
                 
                </tr>
                <tr>
                <td > Total Ingresos</td>
                    <td>{{ number_format($datos[$i]['ingresos'], 2) }} </td>
                   
                    <td >Apor. Perso.</td>
                    <td>{{ number_format($datos[$i]['personal'] , 2)}} </td>
                  
                    <td > IESCE/SECAP</td>
                    <td> {{number_format( $datos[$i]['iecesecap'] , 2)}}</td>
                  
                
                </tr>
                <tr>
                    <td> </td>
                    <td></td>
                    <td >Anticipos</td>
                    <td>{{ number_format($datos[$i]['anticipo'], 2) }} </td>
               
                    <td >D. Tercero(Acu)</td>
                    <td>{{ number_format($datos[$i]['terceroacu'], 2) }} </td>
                  
                </tr>
                
                <tr>
                    <td> </td>
                    <td></td>
                    <td >Impu. Renta</td>
                    <td>{{number_format( $datos[$i]['impu_renta'], 2) }} </td>
                
                    <td >D. Cuarto(Acu)</td>
                    <td>{{ number_format($datos[$i]['cuartoacu'], 2) }} </td>
                   
                
                </tr>
                <tr>
                    <td></td>
                    <td> </td>
                    <td >Multas</td>
                    <td> {{number_format( $datos[$i]['multas'], 2) }}</td>
                  
                    <td >Liquido Pagar</td>
                    <td>{{ number_format($datos[$i]['liquido_pagar'], 2) }} </td>
                 
                
                </tr>
                <tr>
                    <td> </td>
                    <td></td>
                    <td >Otros Egresos</td>
                    <td>{{ number_format($datos[$i]['otrosegre'], 2) }} </td>
                    
                    <td></td>
                    <td> </td>
                </tr>
                <tr>
                    <td> </td>
                    <td></td>
                    <td >Total Egresos</td>
                    <td>{{ number_format($datos[$i]['egresos'] , 2)}} </td>
                 
                    <td></td>
                    <td> </td>
                </tr>
                @endfor
            @endif 
            @endif 
        </tbody>
    </table>