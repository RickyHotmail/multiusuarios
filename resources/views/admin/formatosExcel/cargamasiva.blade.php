<table>
    <tr>
        <td colspan="32" style="text-align: center; font-weight: bold">NEOPAGUPA | Sistema Contable</td>
    </tr>
    <tr>
        <td colspan="14"></td>
        @if(isset($datos))
            @foreach($datos['ordenes'] as $orden)
                <td colspan="4" style="text-align: center"><strong>Sucursal:</strong> {{ $orden->sucursal->sucursal_nombre }}</td>
                @break;
            @endforeach
        @endif
    </tr>
</table>
<table>
    <thead>
        <tr>
            @if($datos['config'][31])<th style="font-weight: bold">REGION</th>@endif
            @if($datos['config'][32])<th style="font-weight: bold">CONTRATO</th>@endif

            @if($datos['config'][22])<th style="font-weight: bold">CODIGO PRODUCTO/CÓDIGO PROCEDIMIENTO</th>@endif
            @if($datos['config'][23])<th style="font-weight: bold">PRODUCTO/SERVICO</th>@endif

            @if($datos['config'][0])<th style="font-weight: bold">CEDULA CLIENTE</th>@endif
            @if($datos['config'][1])<th style="font-weight: bold">NOMBRES CLIENTE</th>@endif
            @if($datos['config'][2])<th style="font-weight: bold">NUM PERSONA</th>@endif
            @if($datos['config'][3])<th style="font-weight: bold">DIRECCIÓN</th>@endif
            
            
            @if($datos['config'][4])<th style="font-weight: bold">CEDULA PACIENTE</th>@endif
            <th style="font-weight: bold">NOMBRE PACIENTE</th>
            @if($datos['config'][6])<th style="font-weight: bold">FECHA NACIMIENTO</th>@endif
            <th style="font-weight: bold">EDAD</th>
            
            <th style="font-weight: bold">DX1</th>
            <th style="font-weight: bold">DX2</th>
            <th style="font-weight: bold">DX3</th>
            @if($datos['config'][21])<th style="font-weight: bold">OBSERVACIÓN</th>@endif
            
            @if($datos['config'][8])<th style="font-weight: bold">CEDULA DOCTOR</th>@endif
            @if($datos['config'][9])<th style="font-weight: bold">NOMBRE DOCTOR</th>@endif

            


            <th style="font-weight: bold">CANTIDAD</th>
            <th style="font-weight: bold">TOTAL</th>
            @if($datos['config'][17])<th style="font-weight: bold">VALOR FEE</th>@endif
            <th style="font-weight: bold">VALOR COPAGO</th>
            <th style="font-weight: bold">VALOR NO CUBIERTO</th>
            



            <th style="font-weight: bold">ORDEN MAS LINEA DETALLE</th>
            <th style="font-weight: bold">LINEA DETALLE</th>
            <th style="font-weight: bold">ORDEN/RECETA</th>

            @if($datos['config'][34])<th style="font-weight: bold">NIVEL</th>@endif
            @if($datos['config'][35])<th style="font-weight: bold">PLAN</th>@endif

            <th style="font-weight: bold">PVP</th>
            @if($datos['config'][14])<th style="font-weight: bold">APLICA IVA</th>@endif
            @if($datos['config'][15])<th style="font-weight: bold">IVA_VALOR_FACTURADO</th>@endif
            <th style="font-weight: bold">IVA_VALOR_X_FACTURAR</th>
            <th style="font-weight: bold">ES_INTERNO</th>

            <th style="font-weight: bold">ES_TTO_CONTINUO</th>
            <th style="font-weight: bold">OBSERVACION_TTO_CONTINUO</th>
            <th style="font-weight: bold">OBS_TTO_CONTINUO_ORDEN</th>


            @if($datos['config'][32])<th style="font-weight: bold">PVP TOTAL</th>@endif
            @if($datos['config'][32])<th style="font-weight: bold">DESCUENTO</th>@endif
            @if($datos['config'][32])<th style="font-weight: bold">N. IDENTIFICACIÓN CITA MÉDICA</th>@endif


            <th style="font-weight: bold">BENEFICIO</th>
            <th style="font-weight: bold">DEDUCIBLE</th>


            @if($datos['config'][32])<th style="font-weight: bold">NÚMERO DE FACTURA</th>@endif
            @if($datos['config'][29])<th style="font-weight: bold">NÚMERO DE AUTORIZACIÓN SRI</th>@endif
            @if($datos['config'][27])<th style="font-weight: bold">TIPO DE DOCUMENTO</th>@endif
            @if($datos['config'][25])<th style="font-weight: bold">FECHA INICIO AUTORIZACIÓN SRI</th>@endif
            @if($datos['config'][26])<th style="font-weight: bold">FECHA FIN AUTORIZACION SRI</th>@endif
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
                    $fechaNacimiento=$nacimiento->format("d-m-Y");
                    $sec=0;
                ?>
                <?php
                    $timestamp = strtotime($orden->orden_fecha); 
                    $fechaOrden = date("d-m-Y", $timestamp );
                ?>

                <tr>
                    @if($datos['config'][31])<td>-</td>@endif
                    @if($datos['config'][32])<td>-</td>@endif




                    @if($datos['config'][22])
                        @if($orden->producto)
                            <td>{{ $orden->producto->producto_codigo }}</td>
                        @else
                            <td>-</td>
                        @endif
                    @endif

                    @if($datos['config'][23])
                        @if($orden->producto)
                            <td>{{ $orden->producto->producto_nombre }}</td>
                        @else
                            <td>-</td>
                        @endif
                    @endif
                   
                    @if($datos['config'][0])<td> {{ $orden->cliente->cliente_cedula }}</td>@endif
                    @if($datos['config'][1])<td> {{ $orden->cliente->cliente_nombre }}</td>@endif
                    @if($datos['config'][2])<td> {{ $orden->cliente->cliente_telefono }}</td>@endif
                    @if($datos['config'][3])<td> {{ $orden->cliente->cliente_direccion }}</td>@endif

                    @if($datos['config'][4])<td> {{ $orden->paciente->paciente_cedula }}</td>@endif
                    <td> {{ $orden->paciente->paciente_apellidos }} {{ $orden->paciente->paciente_nombres }}</td>
                    @if($datos['config'][7])<td> {{ $fechaNacimiento }}</td>@endif
                    <td> {{ $edad }} años</td>

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
                                    echo '<td>'.$detalle->enfermedad->enfermedad_nombre.'</td>';
                                    
                                    if($c==3)  break;
                                }
                            }
                        }

                        for($i=$c; $i<3;  $i++){
                            echo "<td></td>";
                        }
                    ?>

                    @if($datos['config'][21])<td> {{ $orden->observacion }}</td>@endif
                    
                    @if( $orden->medico->empleado)
                        @if($datos['config'][8]) <td>{{ $orden->medico->empleado->empleado_cedula }}</td>@endif
                        @if($datos['config'][9])<td>{{ $orden->medico->empleado->empleado_nombre }}</td>@endif
                    @else
                        @if($datos['config'][8])<td></td>@endif
                        @if($datos['config'][9])<td></td>@endif
                    @endif

                    <td>1</td>
                    <td>{{ $orden->orden_precio }}</td>

                    @if($datos['config'][17])<td>{{ $orden->orden_cobertura_porcentaje }} %</td>@endif
                    <td>{{ $orden->orden_copago }}</td>
                    <td>{{ $orden->orden_cobertura }}</td>

                    <td></td>
                    <td></td>
                    <td></td>

                    @if($datos['config'][34])<th style="font-weight: bold"></th>@endif
                    @if($datos['config'][35])<th style="font-weight: bold"></th>@endif

                    <td>0.00</td>
                    @if($datos['config'][14])
                        <?php if(isset($orden->producto)){ ?>
                            @if($orden->producto->producto_tiene_iva)
                                <td>SI</td>
                            @else
                                <td>NO</td>
                            @endif
                        <?php } else { ?>
                            <td>NO</td>
                        <?php }?>
                    @endif

                    @if($datos['config'][15])<th style="font-weight: bold">0.00</th>@endif

                    <td></td>
                    <td></td>

                    <td></td>
                    <td></td>
                    <td></td>

                    @if($datos['config'][32])<th style="font-weight: bold"></th>@endif
                    @if($datos['config'][32])<th style="font-weight: bold"></th>@endif
                    @if($datos['config'][32])<th style="font-weight: bold"></th>@endif

                    <td></td>
                    <td></td>

                    {{-- datos de factura --}}
                    @if($datos['config'][32])
                        <th style="font-weight: bold">
                        @if($orden->factura)
                            {{ $orden->factura->factura_numero }}
                        @endif
                        </th>
                    @endif
                    @if($datos['config'][29])
                        <th style="font-weight: bold">
                            @if($orden->factura)
                                {{ $orden->factura->factura_autorizacion }}
                            @endif
                        </th>
                    @endif
                    @if($datos['config'][27])
                        <th style="font-weight: bold">
                            @if($orden->factura)
                                {{ $orden->factura->factura_emision }}
                            @endif
                        </th>
                    @endif
                    @if($datos['config'][25])
                        <th style="font-weight: bold">
                            @if($orden->factura)
                                
                            @endif
                        </th>
                    @endif
                    @if($datos['config'][26])
                        <th style="font-weight: bold">
                            @if($orden->factura)
                                
                            @endif
                        </th>
                    @endif  
                </tr>
            @endforeach
        @else
            <tr>
                <td>sdasdas</td>
            </tr>
        @endif
    </tbody>
</table>