<table>
    <tr>
        <td colspan="10" style="text-align: center;">NEOPAGUPA | Sistema Contable</td>
    </tr>
    <tr>
        <td colspan="10" style="text-align: center;">CONCILIACION BANCARIA DEL {{$datos[27]}} AL {{$datos[28]}}</td>
    </tr>
</table>
<?php 
    $saldo = 0; 
    if(isset($saldoAnteriorContable)){
        $saldo = $saldoAnteriorContable;
    }
?>
<table>
    <thead>
        <tr>                            
            <th style="background:  #A7CCF3;">Fecha</th>                           
            <th style="background:  #A7CCF3;">Tipo</th>
            <th style="background:  #A7CCF3;">Numero</th>
            <th style="background:  #A7CCF3;">Crédito</th>
            <th style="background:  #A7CCF3;">Débito</th>
            <th style="background:  #A7CCF3;">Conc.</th>
            <th style="background:  #A7CCF3;">Fecha Conc.</th>
            <th style="background:  #A7CCF3;">Saldo</th>
            <th style="background:  #A7CCF3;">Diario</th>                            
            <th style="background:  #A7CCF3;">Referencia</th>                                               
        </tr>
    </thead>
    <tbody>                   
        <tr> 
            @if(isset($datos[0]))
                <td colspan="7" >SALDO ANTERIOR</td>                           
                <td >{{ number_format($datos[0],2,'.','') }}</td>                            
                <td colspan="2"></td>
            @endif
        </tr>
        @if(isset($datos[22]))
            @for ($i = 0; $i < count($datos[22]); ++$i)        
            <?php $saldo = $saldo + $datos[22][$i]['credito'] - $datos[22][$i]['debito']; ?>       
            <tr>                                
                <td>{{ $datos[22][$i]['fecha'] }}</td>                                
                <td>{{ $datos[22][$i]['tipo'] }}</td>
                <td>{{ $datos[22][$i]['numero']}}</td>
                <td>{{ number_format($datos[22][$i]['credito'],2,'.','')}}</td>
                <td>{{ number_format($datos[22][$i]['debito'],2,'.','')}}</td>
                <td style="text-align: center;">
                    @if($datos[22][$i]['conciliacion'] == true)
                        X
                    @endif
                </td> 
                <td>{{ $datos[22][$i]['fechaConsiliacion']}}</td>   
                <td>{{ $saldo}}</td>                           
                <td>
                    @if(is_array($datos[22][$i]['diario']))
                        @for($cd = 0; $cd < count($datos[22][$i]['diario']); $cd++)
                            {{ $datos[22][$i]['diario'][$cd].' - ' }}
                        @endfor
                    @else
                        {{ $datos[22][$i]['diario']}}
                    @endif
                </td>                                
                <td>{{ $datos[22][$i]['referencia']}}</td>                            
            </tr>
            @endfor
        @endif
    </tbody>
</table>
<table>
    <tr colspan="">
        <td colspan="10" style="text-align: center;">POR CONCILIAR EN OTROS MESES</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th style="background:  #A7CCF3;">Fecha</th>
            <th style="background:  #A7CCF3;">Tipo</th>
            <th style="background:  #A7CCF3;">Numero</th>
            <th style="background:  #A7CCF3;">Crédito</th>
            <th style="background:  #A7CCF3;">Débito</th>
            <th style="background:  #A7CCF3;">Cons.</th>
            <th style="background:  #A7CCF3;">Fecha Conc.</th>
            <th style="background:  #A7CCF3;">Saldo</th>
            <th style="background:  #A7CCF3;">Diario</th>                        
            <th style="background:  #A7CCF3;">Referencia</th>                                               
        </tr>
    </thead>
    <tbody>
    @if(isset($datos[23]))
            @for ($c = 0; $c < count($datos[23]); ++$c)      
            <?php $saldo = $saldo + $datos[23][$c]['credito'] - $datos[23][$c]['debito']; ?>         
            <tr>                            
                <td>{{ $datos[23][$c]['fecha'] }}</td>                            
                <td>{{ $datos[23][$c]['tipo'] }}</td>
                <td>{{ $datos[23][$c]['numero']}}</td>
                <td>{{ number_format($datos[23][$c]['credito'],2,'.','')}}</td>
                <td>{{ number_format($datos[23][$c]['debito'],2,'.','')}}</td>
                <td style="text-align: center;">
                    @if($datos[23][$c]['conciliacion'] == true)
                        X
                    @endif
                </td>
                <td>{{ $datos[23][$c]['fechaConsiliacion']}}</td>    
                <td>{{ $saldo}}</td>                              
                <td>
                    @if(is_array($datos[23][$c]['diario']))
                        @for($cd = 0; $cd < count($datos[23][$c]['diario']); $cd++)
                            {{ $datos[23][$c]['diario'][$cd].' - ' }}
                        @endfor
                    @else
                        {{ $datos[23][$c]['diario']}}
                    @endif
                </td>                    
                <td>{{ $datos[23][$c]['referencia']}}</td>                            
            </tr>
            @endfor
        @endif                    
    </tbody>
</table>
<table>
    <tr>
        <td style="background:  #A7CCF3;">Saldo Anterior Contable</td>
        <td>{{ $datos[0]}}</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">Saldo inicial estado de cuenta</td>
        <td>{{ $datos[31]}}</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">Cheques Girados y No Cobrados</td>
        <td>{{ $datos[3]}}</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">Depostios / Creditos</td>
        <td>{{ $datos[29]}}</td>
    </tr>}
    <tr>
        <td style="background:  #A7CCF3;">Cheques / debitos</td>
        <td>{{ $datos[30]}}</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">Cheques Girados y No Cobrados</td>
        <td>{{ $datos[3]}}</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">Saldo Contable Actual</td>
        <td>{{ $datos[1]}}</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">Saldo Estado de Cuenta</td>
        <td>{{ $datos[2]}}</td>
    </tr>
</table>
<table>
    <tr>
        <td style="background:  #A7CCF3;"></td>
        <td style="background:  #A7CCF3;">Conciliado</td>
        <td style="background:  #A7CCF3;">No Conciliado</td>
        <td style="background:  #A7CCF3;">Conciliado Otros</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">+ Depositos</td>
        <td>{{ $datos[4]}}</td>
        <td>{{ $datos[5]}}</td>
        <td>{{ $datos[6]}}</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">+ Notas de Crédito</td>
        <td>{{ $datos[9]}}</td>
        <td>{{ $datos[10]}}</td>
        <td>{{ $datos[11]}}</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">- Notas de Débito</td>
        <td>{{ $datos[7]}}</td>
        <td>{{ $datos[8]}}</td>
        <td>{{ $datos[12]}}</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">- Cheques Egresos</td>
        <td>{{ $datos[13]}}</td>
        <td>{{ $datos[14]}}</td>
        <td>{{ $datos[15]}}</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">- Transferencias Egresos</td>
        <td>{{ $datos[16]}}</td>
        <td>{{ $datos[17]}}</td>
        <td>{{ $datos[18]}}</td>
    </tr>
    <tr>
        <td style="background:  #A7CCF3;">+ Transferencias Ingresos</td>
        <td>{{ $datos[19]}}</td>
        <td>{{ $datos[20]}}</td>
        <td>{{ $datos[21]}}</td>
    </tr>
</table>
<table>
    <tr colspan="">
        <td colspan="10" style="text-align: center;">CHEQUES ANULADOS</td>
    </tr>
</table>
<table>
    <thead>
        <tr class="text-center">
            <th style="background:  #A7CCF3;">Fecha</th>                        
            <th style="background:  #A7CCF3;">Numero</th>
            <th style="background:  #A7CCF3;">Valor</th>
            <th style="background:  #A7CCF3;">Beneficiario</th>                                         
            <th style="background:  #A7CCF3;" class="text-left">Referencia</th>                                               
        </tr>
    </thead>
    <tbody>
    @if(isset($datos[24]))
        @for ($c = 0; $c < count($datos[24]); ++$c) 
        <tr class="text-center">                       
            <td >{{ $datos[24][$c]['fecha']}}</td>
            <td >{{ $datos[24][$c]['numero']}}</td>
            <td >{{ number_format($datos[24][$c]['valor'],2)}}</td>
            <td >{{ $datos[24][$c]['beneficiario']}}</td> 
            <td class="text-left">{{ $datos[24][$c]['referencia']}}</td>
        </tr>
        @endfor
    @endif
    </tbody>
</table>