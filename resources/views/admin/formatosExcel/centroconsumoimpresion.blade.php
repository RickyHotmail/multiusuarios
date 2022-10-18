<table>
    <tr>
        <td colspan="10" style="text-align: center;">NEOPAGUPA | Sistema Contable</td>
    </tr>
    <tr>
        <td colspan="10" style="text-align: center;">CENTRO DE CONSUMO</td>
    </tr>
</table>
<?php $fechas=$datos[2]; $dias=$datos[1]; $hectarea=$datos[4];  $datos=$datos[3];   ?>
<table>
    <thead>
        <tr class="text-center">
            <th  style=" font-weight: bold; ">CONTROL PRESUPUESTAL </th>
            @if(isset($fechas))
                @for ($i = 1; $i <= count($fechas); ++$i)
                    <th  style=" font-weight: bold; text-align: center;">{{ $fechas[$i]['fecha'] }}</th>
                @endfor
            @endif
            <th  style="font-weight: bold; text-align: center;">Total 2022</th>
            <th  style="font-weight: bold; text-align: center;">Variacion</th>
        </tr>   
    </thead>
    <tbody>
        <tr > 
            <td style="background:  #dee2e6; font-weight: bold;">HECTAREAS</td>
            @if(isset($fechas))
                    @for ($i = 1; $i <= count($fechas); ++$i)
                <td style="background:  #dee2e6; font-weight: bold; text-align: right;">@if(isset($hectarea)) {{$hectarea}} @endif</td>
                @endfor
            @endif
            <td style="background:  #dee2e6; font-weight: bold; text-align: right;">@if(isset($hectarea)) {{$hectarea}} @endif</td>
            <td style="background:  #dee2e6; font-weight: bold;">%</td>
        </tr>
        <tr > 
                <td style="background:  #dee2e6; font-weight: bold;">DIAS</td>
                <?php $tota=0?>
                @if(isset($dias))
                        @for ($i = 1; $i <= count($dias); ++$i)
                        <td style="background:  #dee2e6; font-weight: bold; text-align: right;">{{ $dias[$i]['fecha'] }}</td>
                        <?php $tota=$tota+$dias[$i]['fecha']?>
                    @endfor
                @endif
                <td style="background:  #dee2e6; font-weight: bold; text-align: right;">{{$tota}}</td>
                <td style="background:  #dee2e6; font-weight: bold;"></td>
            </tr>
            <tr> 
                <td style="background:  #dee2e6; font-weight: bold;">RUBROS DEL PRESUPUESTO</td>
                @if(isset($fechas))
                        @for ($i = 1; $i <= count($fechas); ++$i)
                    <td style="background:  #dee2e6; font-weight: bold;"></td>
                    @endfor
                @endif
                <td style="background:  #dee2e6; font-weight: bold;"></td>
                <td style="background:  #dee2e6; font-weight: bold;"></td>
            </tr>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)
                    @if($datos[$i]['tot'] == '1')
                    <tr> 
                        <td style="background:  #97cdb5; font-weight: bold;">{{ $datos[$i]['doc'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #97cdb5; font-weight: bold; text-align: right;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #97cdb5; font-weight: bold; text-align: right;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #97cdb5; font-weight: bold; text-align: right;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '2')
                    <tr>
                        <td>{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="text-align: right;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="text-align: right;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="text-align: right;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '3')
                    <tr>
                        <td style="background:  #ede3c5; font-weight: bold;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #ede3c5; font-weight: bold; text-align: right;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #ede3c5; font-weight: bold; text-align: right;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #ede3c5; font-weight: bold; text-align: right;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '4')
                    <tr>
                        <td style="background:  #97cdb5; font-weight: bold;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #97cdb5; font-weight: bold; text-align: right;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #97cdb5; font-weight: bold; text-align: right;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #97cdb5; font-weight: bold; text-align: right;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '5')
                    <tr>
                        <td style="background:  #ede3c5;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #ede3c5; text-align: right;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #ede3c5; text-align: right;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #ede3c5; text-align: right;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '6')
                    <tr>
                        <td style="background:  #97cdb5; font-weight: bold;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #97cdb5; font-weight: bold; text-align: right;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #97cdb5; font-weight: bold;text-align: right;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #97cdb5; font-weight: bold; text-align: right;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '7')
                    <tr>
                        <td style="background:  #ede3c5; font-weight: bold;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #ede3c5; font-weight: bold; text-align: right;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #ede3c5; font-weight: bold; text-align: right;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #ede3c5; font-weight: bold; text-align: right;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                    @if($datos[$i]['tot'] == '8')
                    <tr>
                        <td style="background:  #97cdb5; font-weight: bold;">{{ $datos[$i]['cat'] }}</td>
                        @for ($j = 1; $j <= count($fechas); ++$j)
                            <td style="background:  #97cdb5; font-weight: bold;text-align: right;">{{ number_format($datos[$i][$fechas[$j]['fecha']],2) }}</td>
                        @endfor
                        <td style="background:  #97cdb5; font-weight: bold; text-align: right;">{{ number_format($datos[$i]['cos'],2) }}</td>
                        <td style="background:  #97cdb5; font-weight: bold; text-align: right;">{{ number_format($datos[$i]['por'],2) }}</td>
                    </tr>
                    @endif
                @endfor
            @endif     
    </tbody>
</table>