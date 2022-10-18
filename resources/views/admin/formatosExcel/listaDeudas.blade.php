<table>
    <tr>
        <td colspan="10" style="text-align: center;">NEOPAGUPA | Sistema Contable</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">Ruc</th>
            <th style="font-weight: bold;">Nombre</th>
            <th style="font-weight: bold;">Documento</th>
            <th style="font-weight: bold;">Monto</th>
            <th style="font-weight: bold;">Saldo</th>
            <th style="font-weight: bold;">Fecha</th>
            <th style="font-weight: bold;">Termino</th>
            <th style="font-weight: bold;">Plazo</th>
            <th style="font-weight: bold;">Transc.</th>
            <th style="font-weight: bold;">Ret.</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($datos))
            @for ($i = 1; $i <= count($datos); ++$i) 
                <tr>
                    <td>{{ $datos[$i]['ruc'] }}</td>
                    <td>{{ $datos[$i]['nom'] }}</td>
                    <td>{{ $datos[$i]['doc'] }}</td>
                    <td>{{ $datos[$i]['mon'] }}</td>
                    <td>{{ $datos[$i]['sal'] }}</td>
                    <td>{{ $datos[$i]['fec'] }}</td>
                    @if($datos[$i]['tot'] == '2')
                        <td colspan="4">{{ $datos[$i]['ter'] }}</td>
                    @else
                        <td>{{ $datos[$i]['ter'] }}</td>
                        <td>{{ $datos[$i]['pla'] }}</td>
                        <td>{{ $datos[$i]['tra'] }}</td>
                        <td>{{ $datos[$i]['ret'] }}</td>
                    @endif
                </tr>
            @endfor
        @endif
    </tbody>
</table>