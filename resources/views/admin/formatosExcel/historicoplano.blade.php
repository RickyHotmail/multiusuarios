<table>
    <tr>
        <td colspan="32" style="text-align: center">NEOPAGUPA | Sistema Contable</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            
            <th style="font-weight: bold">Anexo 1</th>
            <th></th>
            <th></th>
            <th style="font-weight: bold">Anexo 2</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th style="font-weight: bold">Anexo 3</th>
            <th style="font-weight: bold">Anexo 4</th>
            <th></th>
            <th style="font-weight: bold">Anexo 5</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th style="font-weight: bold">Anexo 6</th>
            <th></th>
            <th></th>
            <th style="font-weight: bold">Anexo 7</th>
            <th></th>
            <th style="font-weight: bold">Anexo 8</th>
        </tr>
        <tr>
            <th style="font-weight: bold">DEPENDENCIA</th>
            <th style="font-weight: bold">SECUENCIA</th>
            <th style="font-weight: bold">FECHA ATENCION</th>
            <th style="font-weight: bold">TIPO SEGURO</th>
            <th style="font-weight: bold">CEDULA PACIENTE</th>
            <th style="font-weight: bold">NOMBRE PACIENTE</th>
            <th style="font-weight: bold">SEXO</th>
            <th style="font-weight: bold">FECHA NAC PAC</th>
            <th style="font-weight: bold">EDAD PACIENTE</th>
            <th style="font-weight: bold">TIPO EXAMEN</th>
            <th style="font-weight: bold">CODIGO TARIF</th>
            <th style="font-weight: bold">DESCRIPCION PROCE TAR</th>
            <th style="font-weight: bold">DIAG PRI - CIE10</th>
            <th style="font-weight: bold">DIAG 2</th>
            <th style="font-weight: bold">DIAG 3</th>
            <th style="font-weight: bold">CANTIDAD</th>
            <th style="font-weight: bold">PRECIO UNITARIO</th>
            <th style="font-weight: bold">TIEMPO</th>
            <th style="font-weight: bold">PARENTESCO</th>
            <th style="font-weight: bold">CEDULA AFILIADO</th>
            <th style="font-weight: bold">NOMBRE AFILIADO</th>
            <th style="font-weight: bold">TIPO DERIVACION</th>
            <th style="font-weight: bold">SECUENCIA</th>
            <th style="font-weight: bold"></th>
            <th style="font-weight: bold">TIPO ADIG</th>
            <th style="font-weight: bold">DIASG 4</th>
            <th style="font-weight: bold">DIASG 5</th>
            <th style="font-weight: bold">DIASG 6</th>
            <th style="font-weight: bold"></th>
            <th style="font-weight: bold">IVA</th>
            <th style="font-weight: bold">% IVA</th>
            <th style="font-weight: bold">LETRA</th>
            
            
        </tr>
    </thead>
    <tbody>
        @if(isset($datos))
            @foreach($datos['ordenes'] as $orden)
                <?php
                    $nacimiento = new DateTime($orden->paciente->paciente_fecha_nacimiento);
                    $ahora = new DateTime(date("Y-m-d"));
                    $diferencia = $ahora->diff($nacimiento);
                    $edad= $diferencia->format("%y");

                    $sec=0;
                ?>

                <tr>
                    <td>{{ $orden->especialidad->especialidad_codigo }}</td>
                    <td>-</td>
                    <td>{{ $orden->orden_fecha }}</td>
                    <td>{{ $orden->tipoSeguro->tipo_codigo }}</td>

                    <td>&nbsp;{{ $orden->paciente->paciente_cedula }}'</td>
                    <td>{{ $orden->paciente->paciente_nombres }}</td>
                    <td>{{ $orden->paciente->paciente_sexo }}</td>
                    <td>{{ $orden->paciente->paciente_fecha_nacimiento }}</td>
                    <td>{{ $edad }}</td>

                    <td>{{--$tipo_examen--}}</td>
                    <td>{{--$codigo_tar--}}</td>
                    <td>{{ $orden->orden_observacion }}</td>

                    <?php 
                        $expediente=$orden->expediente;
                        $c=0;
                        
                        if($expediente){
                            $diagnostico=$expediente->diagnostico;
                    
                            if($diagnostico){
                                $diagDetalle=$diagnostico->detallediagnostico;
                                
                                foreach($diagDetalle as $detalle){
                                    $c++;

                                    $detalle->enfermedad;
                                    echo '<td>'.$detalle->enfermedad->enfermedad_codigo.'</td>';
                                    
                                    if($c==3)  break;
                                }
                            }
                        }

                        for($i=$c; $i<3;  $i++){
                            echo "<td></td>";
                        }
                    ?>

                    <td>{{--$cantidad--}}</td>
                    <td>{{--$precio_unitario--}}</td>
                    <td>{{--$tiempo--}}</td>

                    <td>{{ $orden->paciente->tipoDependencia->tipod_codigo }}</td>
                    <td>&nbsp;{{ $orden->orden_cedula_afiliado }}</td>
                    <td>{{ $orden->orden_nombre_afiliado }}</td>

                    <td>CVENC</td>
                    <td>{{--$secuencia --}}</td>
                    <td></td>

                    <td>D</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>


                    <td>0</td>
                    <td>0</td>
                    <td>F</td>
                </tr>

                @if(isset($orden->expediente))
                    @if(isset($orden->expediente->ordenExamen))
                        @foreach($orden->expediente->ordenExamen->detalle as $detalle)
                            @if(isset($detalle->examen))
                                @if($detalle->examen->examen_estado>=1)
                                    <?php
                                        $sec++;
                                    ?>
                                    <tr>
                                        <td>{{ $orden->especialidad->especialidad_codigo }}</td>
                                        <td>{{ $sec }}</td>
                                        <td>{{ $orden->orden_fecha }}</td>
                                        <td>{{ $orden->tipoSeguro->tipo_codigo }}</td>

                                        <td>&nbsp;{{ $orden->paciente->paciente_cedula }}</td>
                                        <td>{{ $orden->paciente->paciente_nombres }}</td>
                                        <td>{{ $orden->paciente->paciente_sexo }}</td>
                                        <td>{{ $orden->paciente->paciente_fecha_nacimiento }}</td>
                                        <td>{{ $edad }}</td>

                                        <td>{{ $detalle->examen->tipoExamen->tipo_nombre }}</td>
                                        <td><?php if(isset($datos[$orden->orden_id][$orden->producto_id])) echo $datos[$orden->orden_id][$orden->producto_id]->procedimientoA_codigo  ?></td>
                                        <td>{{ $detalle->examen->producto->producto_nombre }}</td>

                                        <?php 
                                            $expediente=$orden->expediente;
                                            $c=0;
                                            
                                            if($expediente){
                                                $diagnostico=$expediente->diagnostico;
                                        
                                                if($diagnostico){
                                                    $diagDetalle=$diagnostico->detallediagnostico;
                                                    
                                                    foreach($diagDetalle as $detalle){
                                                        $c++;

                                                        $detalle->enfermedad;
                                                        echo '<td>'.$detalle->enfermedad->enfermedad_codigo.'</td>';
                                                        
                                                        if($c==3)  break;
                                                    }
                                                }
                                            }

                                            for($i=$c; $i<3;  $i++){
                                                echo "<td></td>";
                                            }
                                        ?>

                                        <td>{{1}}</td>
                                        @if($detalle->examen)
                                            <td>{{ $detalle->examen->producto->producto_precio }}</td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        <td>{{--$tiempo--}}</td>

                                        <td>{{ $orden->paciente->tipoDependencia->tipod_codigo }}</td>
                                        <td>&nbsp;{{ $orden->orden_cedula_afiliado }}</td>
                                        <td>{{ $orden->orden_nombre_afiliado }}</td>

                                        <td>CVENC</td>
                                        <td>{{--$secuencia --}}</td>
                                        <td></td>

                                        <td>D</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>


                                        <td>0</td>
                                        <td>0</td>
                                        <td>F</td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach
                    @endif
                @endif
            @endforeach
        @else
            <tr>
                <td>sdasdas</td>
            </tr>
        @endif
    </tbody>
</table>