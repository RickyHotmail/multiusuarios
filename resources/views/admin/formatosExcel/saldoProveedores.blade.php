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
            <th style="font-weight: bold;">Ruc</th>
            <th style="font-weight: bold;">Nombre</th>
            <th style="font-weight: bold;">Saldo Anterior</th>
            <th style="font-weight: bold;">Debe</th>
            <th style="font-weight: bold;">Haber</th>
            <th style="font-weight: bold;">Saldo Actual</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($datos))
            @for ($i = 1; $i <= count($datos); ++$i) 
                <tr>
                    <td>{{ $datos[$i]['ruc'] }}</td>
                    <td>{{ $datos[$i]['nom'] }}</td>
                    <td>{{ $datos[$i]['ant'] }}</td>
                    <td>{{ $datos[$i]['deb'] }}</td>
                    <td>{{ $datos[$i]['hab'] }}</td>
                    <td>{{ $datos[$i]['sal'] }}</td> 
                </tr>
            @endfor
        @endif
    </tbody>
</table>