<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEOPAGUPA | Sistema Contable</title>


    <link rel="stylesheet" href="{{ asset('admin/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/dist/css/adminlte.min.css') }}">

</head>
<body>
 
        <tr><td colspan="2" class="centrar letra20 negrita">MAYORIZACIÓN DE CUENTAS</td></tr>
        <tr><td colspan="2" class="centrar letra15">DEL {{ $desde }} AL {{ $hasta }}</td></tr>

    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
        <tr style="border: 1px solid black;" class="centrar letra10">
                <th>Cuenta</th>
                <th>Fecha</th>
                <th>Número</th>
                <th>Debe</th>
                <th>Haber</th>
                <th>Saldo</th>
                <th>Referencia</th>
                <th>Diario</th>
                <th>Sucursal</th>  
            </tr>
        </thead>
        <tbody>
            @if(isset($datos))
                <?php 
                    $anterior=0;
                    $actual=0;
                    $tfila=0;  //0.-cabecera  1.-detalle   2.-sumas
                    
                    $saldo=0;
                    $debe=0;
                    $haber=0;
                ?>

                @foreach($datos as $dato)    
                    <?php 
                        $actual=$dato->cuenta_numero;
                        if($anterior!=$actual) $tfila=0;
                        if($anterior==$actual) $tfila=1;
                    ?>

                    @if($tfila==0)
                        @if($anterior>0)
                            <tr class="letra10">
                                <td style="background:  #BCC0C4;"></td>
                                <td style="background:  #BCC0C4;"></td>
                                <td style="background:  #BCC0C4;"></td>
                                <td class="derecha-texto" style="background:  #BCC0C4;">{{ number_format($debe,2) }}</td>
                                <td class="derecha-texto" style="background:  #BCC0C4;">{{ number_format($haber,2) }}</td>
                                <td  style="background:  #BCC0C4;"></td>
                                <td style="background:  #BCC0C4;"></td>
                                <td style="background:  #BCC0C4;"></td>
                                <td style="background:  #BCC0C4;"></td>
                            </tr>

                            <?php 
                                $debe=0;
                                $haber=0;
                            ?>
                        @endif
                        <tr class="letra10">
                            <td style="background:  #70B1F7;">
                                {{ $dato->cuenta_numero }}   -   {{ $dato->cuenta_nombre }}
                            </td>
                            <td style="background:  #70B1F7;"></td>
                            <td style="background:  #70B1F7;"></td>
                            <td style="background:  #70B1F7;"></td>
                            <td  class="derecha-texto" style="background:  #70B1F7;"></td>
                            <td  class="derecha-texto" style="background:  #70B1F7;"></td>

                            <?php
                                /*
                                foreach($saldos as $sd){
                                    if($sd->cuenta_id==$dato->cuenta_id){
                                        $saldo=$sd->saldo;
                                        break;
                                    }
                                }
                                */
                                
                                $saldo=0;
                                $key= array_search($dato->cuenta_id, array_column($saldos, 'cuenta_id'));
                                if($key>0) $saldo=$saldos[$key]->saldo;
                            ?>
                            <td class="derecha-texto" style="background:  #70B1F7;">{{ number_format($saldo,2) }}</td>
                            <td style="background:  #70B1F7;"></td>
                            <td style="background:  #70B1F7;"></td>
                            <td style="background:  #70B1F7;"></td>
                        </tr>
                        
                    @endif
                    <?php 
                        $debe=$debe+$dato->detalle_debe;
                        $haber=$haber+$dato->detalle_haber;
                    
                        $saldo+=$dato->detalle_debe;
                        $saldo-=$dato->detalle_haber;
                    ?>
                    <tr class="letra10">
                        <td></td>
                        <td>{{ $dato->diario_fecha }}</td>
                        <td class="text-center">{{ $dato->detalle_tipo_documento }} # {{ $dato->detalle_numero_documento }}</td>
                        <td class="derecha-texto">{{ number_format($dato->detalle_debe,2) }}</td>
                        <td class="derecha-texto">{{ number_format($dato->detalle_haber,2) }}</td>
                        <td class="derecha-texto">{{ number_format($saldo,2) }}</td>
                        <td class="text-center">{{ $dato->diario_beneficiario }}</td>
                        <td class="text-center">{{ $dato->diario_codigo }}</td>
                        <td class="text-center">{{ $dato->sucursal_nombre }}</td>
                    </tr>

                    <?php $anterior=$actual?>
                @endforeach
            @endif
        </tbody>
    </table>

</body>
</html>