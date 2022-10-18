<table>
    <tr>
        <td colspan="13" style="text-align: center">PLANILLA INDIVIDUAL</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th style="font-weight: bold">No.</th>
            <th style="font-weight: bold">FECHA ATENCION</th>
            <th style="font-weight: bold">IDENTIFICACIÓN DEL BENEFICIARIO</th>
            <th style="font-weight: bold">NOMBRE DEL BENEFICIARIO</th>
            <th style="font-weight: bold">GÉNERO DEL BENEFICIARIO</th>
            <th style="font-weight: bold">FECHA DE NACIMIENTO DEL BENEFICIARIO</th>
            <th style="font-weight: bold">CÓDIGO DE LA PRESTACIÓN (TARIFARIO)</th>
            <th style="font-weight: bold">DESCRIPCIÓN DE LA PRESTACIÓN</th>

            <th style="font-weight: bold">DIAGNOSTICO PRINCIPAL</th>
            <th style="font-weight: bold">DIAGNOSTICO SECUNDARIO</th>
            <th style="font-weight: bold">CANTIDAD SOLICITADA</th>
            <th style="font-weight: bold">VALOR UNITARIO</th>
            <th style="font-weight: bold">VALOR TOTAL</th>
        </tr>
            <tr>
            <th style="height: 110px; text-align:center; width:17px">Ingresar el número en orden ascendente, ej.: 1, 2…</th>
            <th style="height: 110px; text-align:center; width:17px">Ingresar fecha dd/mm/aaaa de la atención</th>
            <th style="height: 110px; text-align:center; width:17px">Ingresar el número de cédula o pasaporte del beneficiario del servicio de salud</th>
            <th style="height: 110px; text-align:center; width:17px">Ingresar Nombre y Apellido del beneficio del servicio de salud</th>

            <th style="height: 110px; text-align:center; width:17px">Seleccionar género masculino o femenino</th>
            <th style="height: 110px; text-align:center; width:17px">Ingresar fecha de nacimiento dd/mm/aaaa</th>
            <th style="height: 110px; text-align:center; width:17px">Ingresar el código de la prestación de salud del tarifario nacional de prestaciones de servicios de salud</th>
            <th style="height: 110px; text-align:center; width:17px">Ingresar la descripción del código de la prestación de salud</th>
            
            <th style="height: 110px; text-align:center; width:17px">Ingresar el diagnóstico con codificación  CIE10</th>
            <th style="height: 110px; text-align:center; width:17px">Ingresar el diagnóstico secundario con codificación CIE10</th>
            <th style="height: 110px; text-align:center; width:17px">Ingresar la cantidad solicitada en números enteros de las prestaciones del tarifario</th>
            <th style="height: 110px; text-align:center; width:17px">Ingresar en números el valor unitario de las prestaciones</th>
            <th style="height: 110px; text-align:center; width:17px">Valor calculado de forma automática</th>
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
                            echo '<td>'.$datos[$orden->orden_id][$orden->producto_id]->procedimientoA_valor.'</td>';
                            echo '<td>'.$datos[$orden->orden_id][$orden->producto_id]->procedimientoA_valor.'</td>';
                        }else
                            echo "<td>0.00</td><td>0.00</td>";
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
                                            echo '<td>'.$datos['detalle_examen'][$detalle->detalle_id]->procedimientoA_valor.'</td>';
                                            echo '<td>'.$datos['detalle_examen'][$detalle->detalle_id]->procedimientoA_valor.'</td>';
                                        }else
                                            echo "<td>0.00</td><td>0.00</td>";
                                    ?>
                                    
                                </tr>
                                @endif
                            @endif
                        @endforeach 
                    @endif
                @endif

                @if(isset($orden->expediente))
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
                                            echo '<td>'.$datos['detalle_imagen'][$detalle->detalle_id]->procedimientoA_valor.'</td>';
                                            echo '<td>'.$datos['detalle_imagen'][$detalle->detalle_id]->procedimientoA_valor.'</td>';
                                        }else
                                            echo "<td>0.00</td><td>0.00</td>";
                                    ?>
                                    
                                </tr>
                                @endif
                            @endif
                        @endforeach 
                    @endif
                @endif

                @if(isset($orden->expediente))
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
                                            echo '<td>'.$datos['detalle_medicamento'][$detalle->prescripcionM_id]->procedimientoA_valor.'</td>';
                                            echo '<td>'.$datos['detalle_medicamento'][$detalle->prescripcionM_id]->procedimientoA_valor.'</td>';
                                        }else
                                            echo "<td>0.00</td><td>0.00</td>";
                                    ?>
                                    
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