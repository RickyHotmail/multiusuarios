<table>
    <tr>
        <td colspan="10" style="text-align: center;">NEOPAGUPA | Sistema Contable</td>
    </tr>
    <tr>
        <td colspan="10" style="text-align: center;">ESTADO DE CUENTA DE PROVEEDORES</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">Documento</th>
            <th style="font-weight: bold;">Numero</th>
            <th style="font-weight: bold;">Fecha</th>
            <th style="font-weight: bold;">Monto</th>
            <th style="font-weight: bold;">Saldo</th>
            <th style="font-weight: bold;">Pago</th>
            <th style="font-weight: bold;">Fecha Pago</th>
            <th style="font-weight: bold;">Diario</th>
            <th style="font-weight: bold;">Descripción.</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($datos))
            @for ($i = 1; $i <= count($datos); ++$i) 
                <tr>
                    @if($datos[$i]['tot'] == '1')
                        <td style="background:  #A7CCF3;" colspan="3">{{ $datos[$i]['nom'] }}</td>
                        <td style="background:  #A7CCF3;">{{ number_format($datos[$i]['mon'],2) }}</td>
                        <td style="background:  #A7CCF3;">{{ number_format($datos[$i]['sal'],2) }}</td>
                        <td style="background:  #A7CCF3;">{{ number_format($datos[$i]['pag'],2) }}</td>
                        <td style="background:  #A7CCF3;" colspan="3"></td>  
                    @endif
                    @if($datos[$i]['tot'] == '2')    
                        <td style="background:  #F3DCA7;">{{ $datos[$i]['doc'] }}</td>
                        <td style="background:  #F3DCA7;">{{ $datos[$i]['num'] }}</td>
                        <td style="background:  #F3DCA7;">{{ $datos[$i]['fec'] }}</td>
                        <td style="background:  #F3DCA7;">{{ number_format($datos[$i]['mon'],2) }}</td>
                        <td style="background:  #F3DCA7;">{{ number_format($datos[$i]['sal'],2) }}</td>
                        <td style="background:  #F3DCA7;">{{ number_format($datos[$i]['pag'],2) }}</td>
                        <td style="background:  #F3DCA7;">{{ $datos[$i]['fep'] }}</td>
                        <td style="background:  #F3DCA7;">{{ $datos[$i]['dia'] }}</td>   
                        <td style="background:  #F3DCA7;">{{ $datos[$i]['tip'] }}</td>   
                    @endif 
                    @if($datos[$i]['tot'] == '3')    
                        <td>{{ $datos[$i]['doc'] }}</td>
                        <td>{{ $datos[$i]['num'] }}</td>
                        <td>{{ $datos[$i]['fec'] }}</td>
                        <td>{{ $datos[$i]['mon'] }}</td>
                        <td>{{ $datos[$i]['sal'] }}</td>
                        <td>{{ number_format($datos[$i]['pag'],2) }}</td>
                        <td>{{ $datos[$i]['fep'] }}</td>
                        <td>{{ $datos[$i]['dia'] }}</td>   
                        <td>{{ $datos[$i]['tip'] }}</td>   
                    @endif 
                </tr>
            @endfor
        @endif
    </tbody>
</table>