<style>
    table{
        border-collapse:collapse;
    }

    *{
        font-size: 9px;
    }

    .titulo{
        text-align: center;
        font-weight: bold;
        font-size:11px;
        background-color:darkseagreen
    }
    .descripciones{
        height: 110px;
        text-align:center;
        width:17px;
    }
</style>

<table border="1" cellpadding="3">
    <thead>
        <tr>
            <td colspan="13" class="titulo">PLANILLA INDIVIDUAL</td>
        </tr>
        <tr>
            <th style="font-weight: bold" width="40px">No.</th>
            <th style="font-weight: bold" width="60px">FECHA ATENCION</th>
            <th style="font-weight: bold" width="60px">IDENTIFICACIÓN DEL BENEFICIARIO</th>
            <th style="font-weight: bold" width="120px">NOMBRE DEL BENEFICIARIO</th>
            <th style="font-weight: bold" width="40px">GÉNERO DEL BENEFICIARIO</th>
            <th style="font-weight: bold" width="60px">FECHA DE NACIMIENTO DEL BENEFICIARIO</th>
            <th style="font-weight: bold" width="50px">CÓDIGO DE LA PRESTACIÓN (TARIFARIO)</th>
            <th style="font-weight: bold" width="70px">DESCRIPCIÓN DE LA PRESTACIÓN</th>

            <th style="font-weight: bold" width="40px">DIAGNOSTICO PRINCIPAL</th>
            <th style="font-weight: bold" width="40px">DIAGNOSTICO SECUNDARIO</th>
            <th style="font-weight: bold" width="40px">CANTIDAD SOLICITADA</th>
            <th style="font-weight: bold" width="40px">VALOR UNITARIO</th>
            <th style="font-weight: bold" width="40px">VALOR TOTAL</th>
        </tr>
            <tr>
            <th class="descripciones"><font color="#2E64FE">Ingresar el número en orden ascendente, ej.: 1, 2…</font></th>
            <th class="descripciones"><font color="#2E64FE">Ingresar fecha dd/mm/aaaa de la atención</font></th>
            <th class="descripciones"><font color="#2E64FE">Ingresar el número de cédula o pasaporte del beneficiario del servicio de salud</font></th>
            <th class="descripciones"><font color="#2E64FE">Ingresar Nombre y Apellido del beneficio del servicio de salud</font></th>

            <th class="descripciones"><font color="#2E64FE" width="40px">Seleccionar género masculino o femenino</font></th>
            <th class="descripciones"><font color="#2E64FE">Ingresar fecha de nacimiento dd/mm/aaaa</font></th>
            <th class="descripciones"><font color="#2E64FE">Ingresar el código de la prestación de salud del tarifario nacional de prestaciones de servicios de salud</font></th>
            <th class="descripciones"><font color="#2E64FE">Ingresar la descripción del código de la prestación de salud</font></th>
            
            <th class="descripciones"><font color="#2E64FE">Ingresar el diagnóstico con codificación  CIE10</font></th>
            <th class="descripciones"><font color="#2E64FE">Ingresar el diagnóstico secundario con codificación CIE10</font></th>
            <th class="descripciones"><font color="#2E64FE">Ingresar la cantidad solicitada en números enteros de las prestaciones del tarifario</font></th>
            <th class="descripciones"><font color="#2E64FE">Ingresar en números el valor unitario de las prestaciones</font></th>
            <th class="descripciones"><font color="#2E64FE">Valor calculado de forma automática</font></th>
        </tr>
    </thead>
    <tbody>
        @if(isset($orden))
            <?php
                $nacimiento = new DateTime($orden->paciente->paciente_fecha_nacimiento);
                $ahora = new DateTime(date("Y-m-d"));
                $diferencia = $ahora->diff($nacimiento);
                $edad= $diferencia->format("%y");

                $sec=0;
            ?>

            <tr>
                <td>{{ $orden->orden_secuencial }}</td>
                <td>{{ $orden->orden_fecha }}</td>
                <td>&nbsp;{{ $orden->paciente->paciente_cedula }}</td>
                <td>{{ $orden->paciente->paciente_apellidos }} {{ $orden->paciente->paciente_nombres }}</td>
                <td>{{ $orden->paciente->paciente_sexo }}</td>
                <td>{{ $orden->paciente->paciente_fecha_nacimiento }}</td>
                
                <?php
                    if(isset($datos[$orden->orden_id][$orden->producto_id]))
                        echo '<td>'.$datos[$orden->orden_id][$orden->producto_id]->procedimientoA_codigo.'</td>';
                    else
                        echo "<td>? $orden->producto_id</td>";
                ?>

                <td>{{ $orden->producto->producto_nombre }} {{ $orden->especialidad->especialidad_nombre }}</td>
                

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
                                
                                if($c==2)  break;
                            }
                        }
                    }

                    for($i=$c; $i<2;  $i++){
                        echo "<td></td>";
                    }
                ?>

                <td>1</td>
                <?php 
                    if(isset($datos[$orden->orden_id][$orden->producto_id])){
                        echo '<td style="text-align: right; padding-right:6px">'.$datos[$orden->orden_id][$orden->producto_id]->procedimientoA_valor.'</td>';
                        echo '<td style="text-align: right; padding-right:6px">'.$datos[$orden->orden_id][$orden->producto_id]->procedimientoA_valor.'</td>';
                    }else
                        echo "<td style='text-align: right; padding-right:6px'>0.00</td><td style='text-align: right; padding-right:6px'>0.00</td>";
                ?>
            </tr>
            
            @if(isset($orden->expediente))
                @if(isset($orden->expediente->ordenExamen))
                    @foreach($orden->expediente->ordenExamen->detalle as $detalle)
                        @if(isset($detalle->examen))
                            @if($detalle->examen->examen_estado>=1)
                            <tr>
                                <td>{{ $orden->orden_secuencial }}</td>
                                <td>{{ $orden->orden_fecha }}</td>
                                <td>&nbsp;{{ $orden->paciente->paciente_cedula }}</td>
                                <td>{{ $orden->paciente->paciente_apellidos }} {{ $orden->paciente->paciente_nombres }}</td>
                                <td>{{ $orden->paciente->paciente_sexo }}</td>
                                <td>{{ $orden->paciente->paciente_fecha_nacimiento }}</td>
                                
                                <?php 
                                    if(isset($datos['detalle_examen'][$detalle->detalle_id]))
                                        echo '<td>'.$datos['detalle_examen'][$detalle->detalle_id]->procedimientoA_codigo.'</td>';
                                    else
                                        echo "<td>?</td>";
                                ?>

                                <td>{{ $detalle->examen->producto->producto_nombre }}</td>

                                <?php 
                                    $expediente=$orden->expediente;
                                    $c=0;
                                    
                                    if($expediente){
                                        $diagnostico=$expediente->diagnostico;
                                
                                        if($diagnostico){
                                            $diagDetalle=$diagnostico->detallediagnostico;
                                            
                                            foreach($diagDetalle as $detD){
                                                $c++;

                                                $detD->enfermedad;
                                                echo '<td>'.$detD->enfermedad->enfermedad_codigo.'</td>';
                                                
                                                if($c==2)  break;
                                            }
                                        }
                                    }

                                    for($i=$c; $i<2;  $i++){
                                        echo "<td></td>";
                                    }
                                ?>

                                <td>1</td>
                                
                                <?php 
                                    if(isset($datos['detalle_examen'][$detalle->detalle_id])){
                                        echo '<td style="text-align: right; padding-right:6px">'.$datos['detalle_examen'][$detalle->detalle_id]->procedimientoA_valor.'</td>';
                                        echo '<td style="text-align: right; padding-right:6px">'.$datos['detalle_examen'][$detalle->detalle_id]->procedimientoA_valor.'</td>';
                                    }else
                                        echo "<td style='text-align: right; padding-right:6px'>0.00</td><td style='text-align: right; padding-right:6px'>0.00</td>";
                                ?>
                                
                            </tr>
                            @endif
                        @endif
                    @endforeach 
                @endif
            
                @if(isset($orden->expediente->ordenImagen))
                    @foreach($orden->expediente->ordenImagen->detalleImagen as $detalle)
                        @if(isset($detalle->imagen))
                            @if($detalle->detalle_estado>=1)
                            <tr>
                                <td>{{ $orden->orden_secuencial }}</td>
                                <td>{{ $orden->orden_fecha }}</td>
                                <td>&nbsp;{{ $orden->paciente->paciente_cedula }}</td>
                                <td>{{ $orden->paciente->paciente_apellidos }} {{ $orden->paciente->paciente_nombres }}</td>
                                <td>{{ $orden->paciente->paciente_sexo }}</td>
                                <td>{{ $orden->paciente->paciente_fecha_nacimiento }}</td>
                                
                                <?php 
                                    if(isset($datos['detalle_imagen'][$detalle->detalle_id]))
                                        echo '<td>'.$datos['detalle_imagen'][$detalle->detalle_id]->procedimientoA_codigo.'</td>';
                                    else
                                        echo "<td>?</td>";
                                ?>

                                <td>{{ $detalle->imagen->producto->producto_nombre }}</td>

                                <?php 
                                    $expediente=$orden->expediente;
                                    $c=0;
                                    
                                    if($expediente){
                                        $diagnostico=$expediente->diagnostico;
                                
                                        if($diagnostico){
                                            $diagDetalle=$diagnostico->detallediagnostico;
                                            
                                            foreach($diagDetalle as $detD){
                                                $c++;

                                                $detD->enfermedad;
                                                echo '<td>'.$detD->enfermedad->enfermedad_codigo.'</td>';
                                                
                                                if($c==2)  break;
                                            }
                                        }
                                    }

                                    for($i=$c; $i<2;  $i++){
                                        echo "<td></td>";
                                    }
                                ?>

                                <td>1</td>
                                
                                <?php 
                                    if(isset($datos['detalle_imagen'][$detalle->detalle_id])){
                                        echo '<td style="text-align: right; padding-right:6px">'.$datos['detalle_imagen'][$detalle->detalle_id]->procedimientoA_valor.'</td>';
                                        echo '<td style="text-align: right; padding-right:6px">'.$datos['detalle_imagen'][$detalle->detalle_id]->procedimientoA_valor.'</td>';
                                    }else
                                        echo "<td style='text-align: right; padding-right:6px'>0.00</td><td style='text-align: right; padding-right:6px'>0.00</td>";
                                ?>
                                
                            </tr>
                            @endif
                        @endif
                    @endforeach 
                @endif
            
                @if(isset($orden->expediente->prescripcion))
                    @foreach($orden->expediente->prescripcion->presMedicamento as $detalle)
                        @if(isset($detalle->medicamento))
                            @if($detalle->prescripcionm_estado==1)
                            <tr>
                                <td>{{ $orden->orden_secuencial }}</td>
                                <td>{{ $orden->orden_fecha }}</td>
                                <td>&nbsp;{{ $orden->paciente->paciente_cedula }}</td>
                                <td>{{ $orden->paciente->paciente_apellidos }} {{ $orden->paciente->paciente_nombres }}</td>
                                <td>{{ $orden->paciente->paciente_sexo }}</td>
                                <td>{{ $orden->paciente->paciente_fecha_nacimiento }}</td>
                                
                                <?php 
                                    if(isset($datos['detalle_medicamento'][$detalle->prescripcionM_id]))
                                        echo '<td>'.$datos['detalle_medicamento'][$detalle->prescripcionM_id]->procedimientoA_codigo.'</td>';
                                    else
                                        echo "<td>?</td>";
                                ?>

                                <td>{{ $detalle->medicamento->producto->producto_nombre }}</td>

                                <?php 
                                    $expediente=$orden->expediente;
                                    $c=0;
                                    
                                    if($expediente){
                                        $diagnostico=$expediente->diagnostico;
                                
                                        if($diagnostico){
                                            $diagDetalle=$diagnostico->detallediagnostico;
                                            
                                            foreach($diagDetalle as $detD){
                                                $c++;

                                                $detD->enfermedad;
                                                echo '<td>'.$detD->enfermedad->enfermedad_codigo.'</td>';
                                                
                                                if($c==2)  break;
                                            }
                                        }
                                    }

                                    for($i=$c; $i<2;  $i++){
                                        echo "<td></td>";
                                    }
                                ?>

                                <td>1</td>
                                
                                <?php 
                                    if(isset($datos['detalle_medicamento'][$detalle->prescripcionM_id])){
                                        echo '<td style="text-align: right; padding-right:6px">'.$datos['detalle_medicamento'][$detalle->prescripcionM_id]->procedimientoA_valor.'</td>';
                                        echo '<td style="text-align: right; padding-right:6px">'.$datos['detalle_medicamento'][$detalle->prescripcionM_id]->procedimientoA_valor.'</td>';
                                    }else
                                        echo "<td style='text-align: right; padding-right:6px'>0.00</td><td style='text-align: right; padding-right:6px'>0.00</td>";
                                ?>
                            </tr>
                            @endif
                        @endif
                    @endforeach 
                @endif
            @endif
        @else
            <tr>
                <td>no hay datos</td>
            </tr>
        @endif
    </tbody>
</table>