@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">ROL DE PAGO</td></tr>
    @endsection
    @foreach($rol->detalles as $detalle)
        <?php $detalle_rol_fecha_inicio=$detalle->detalle_rol_fecha_inicio;?>
        
        <?php $detalle_rol_fecha_fin=$detalle->detalle_rol_fecha_fin;?>
    @endforeach
    <table style="white-space: normal!important; border-collapse: collapse;">
        <tr class="letra12">
            
                <td class="negrita" style="width: 105px;">Fecha desde:</td>
                <td>{{ $detalle_rol_fecha_inicio}}</td>
                <td class="negrita" style="width: 105px;">Fecha hasta:</td>  
                <td> 
                    {{$detalle_rol_fecha_fin }}   
                </td>   
        </tr>
        <tr class="letra12"> 
            <td class="negrita" style="width: 105px;">Nombre:</td>
            <td>{{ $rol->empleado->empleado_nombre }}</td>
            <td class="negrita" style="width: 105px;">Cedula:</td>
            <td>{{$rol->empleado->empleado_cedula}}</td>
            
        </tr>

        <tr class="letra12">
            <td class="negrita" style="width: 105px;">Pago:</td>
            <td>{{$tipo}}   </td>
            <td class="negrita" style="width: 105px;">Cargo:</td>  
            <td> 
                {{$rol->empleado->cargo->empleado_cargo_nombre }}   
            </td> 
            
            
        </tr> 
        @if($tipo=='CHEQUE' || $tipo=='TRANSFERENCIA BANCARIA')
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">Banco:</td>
            <td>{{$bancaria->banco->bancoLista->banco_lista_nombre }}   </td>
            <td class="negrita" style="width: 105px;">Cuenta:</td>
            <td>{{$bancaria->cuenta_bancaria_numero }}</td>
          
            
        </tr>  
        @endif
    </table>
    <br>
    <table style="white-space: normal!important; border-collapse: collapse;">
    <tr class="letra12"> 
             <td class="centrar letra15 negrita" style="width: 105px;">Rol del Mes de {{ucfirst($mes)}} del {{date("Y", strtotime($rol->cabecera_rol_fecha))}}</td>
    </tr>
    </table>
  
    <table style="white-space: normal!important; border-collapse: collapse;">
        <thead>
            <tr class="centrar letra12">
                <th class="cabecera-diario" colspan="2">INGRESOS</th>
                <th class="cabecera-diario" colspan="2">EGRESOS</th>   
            </tr>
        </thead>
        <tbody>

                <tr class="letra12">
                    <td colspan="2" valign="top" class="detalle-diario">  
                    <table style="vertical-align: top; border-collapse: collapse;">
                        <tbody>
                        @foreach($ingresos as $rubro)
                           
                            <tr style="text-align: left; vertical-align: top;">
                                    <td > {{$rubro->rubro_descripcion}}</td> 
                                   <?php $contador=false; $valor=0; ?>
                                @foreach($rol->detalles as $detalle)
                                    @if($detalle->rubro_id==$rubro->rubro_id)
                                        <?php $valor=$detalle->detalle_rol_valor;?>
                                    @endif
                                @endforeach
                                <td class="detalle-diario dereche">$ {{number_format($valor,2)}}</td>
                            </tr>
                           
                        @endforeach
                        </tbody>
                    </table>
                  
                    </td >
                    <td colspan="2" class="foot-rol-linea-left detalle-diario"> 
                        <table style="white-space: normal!important; ">
                            <tbody>
                            @foreach($egresos as $rubro)
                                
                                <tr >
                                        <td class="detalle-diario"> {{$rubro->rubro_descripcion}}</td> 
                                    <?php $contador=false; $valor=0; ?>
                                    @foreach($rol->detalles as $detalle)
                                        @if($detalle->rubro_id==$rubro->rubro_id)
                                            <?php $valor=$detalle->detalle_rol_valor;?>
                                        @endif
                                    @endforeach
                                    <td class="detalle-diario dereche">$ {{number_format($valor,2)}}</td>
                                </tr>
                              
                            @endforeach
                            </tbody>
                        </table>
                    </td>

                </tr>
               
               
                    <tr class="letra12">
                    <td style="border-top: 1px solid black; padding: 6px 6px;" class="detalle-diario">Total Ingresos</td>
                    <td style="border-top: 1px solid black; padding: 6px 6px;" class="detalle-diario dereche">$ {{ number_format($rol->cabecera_rol_total_ingresos,2)}}</td>
                    <td style="border-top: 1px solid black; padding: 6px 6px; border-left: 1px solid black;" class="detalle-diario">Total Egresos: </td>
                    <td style="border-top: 1px solid black; padding: 6px 6px;" class="detalle-diario dereche">$ {{ number_format(($rol->cabecera_rol_total_egresos),2)}}</td>
                    </tr>    
                    <tr class="letra12">
                        <td style="border-top: 1px solid black;" > </td>
                        <td style="border-top: 1px solid black;" > </td>
                       
                        <td style="border-top: 1px solid black;" > </td>
                        <td style="border-top: 1px solid black;" > </td>
                    </tr>
                    <tr class="letra12">
                    <td class="detalle-diario"><br><br></td>
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario"></td>
                    </tr>  
                   <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol detalle-diario">Total Ingresos: (+)</td>
                    <td  class="foot-rol-linea detalle-diario dereche">$ {{ number_format($rol->cabecera_rol_total_ingresos,2)}}</td>
                    </tr>
                    <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol detalle-diario">Total Egresos: (-)</td>
                    <td  class="foot-rol-linea detalle-diario dereche">$ {{ number_format($rol->cabecera_rol_total_egresos,2)}}</td>
                    </tr>
                  
                    
                    <tr class="letra12">
                        <td class="detalle-diario"></td>
                        <td class="detalle-diario dereche"></td>
                        <td class="foot-rol detalle-diario">Fondos Reserva: (+)</td>
                        <td class="foot-rol-linea detalle-diario dereche">$ {{ number_format($rol->cabecera_rol_fondo_reserva,2)}}</td>
                   </tr>
                   <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol detalle-diario">Decimo Tercero: (+)</td>
                    <td class="foot-rol-linea detalle-diario dereche">$ {{ number_format($rol->cabecera_rol_decimotercero,2)}}</td>
                     </tr>

                   <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol detalle-diario">Decimo Cuarto: (+)</td>
                    <td class="foot-rol-linea detalle-diario dereche">$ {{ number_format($rol->cabecera_rol_decimocuarto,2)}}</td>
                    </tr>
                    <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol detalle-diario">Viaticos: (+)</td>
                    <td class="foot-rol-linea detalle-diario dereche">$ {{ number_format($rol->cabecera_rol_viaticos,2)}}</td>
                    </tr>
                   
                    <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-diario centrar">Liquido a Pagar</td>
                    <td class="foot-rol-linea detalle-diario dereche">$ {{ number_format($rol->cabecera_rol_pago,2)}}</td>
                    </tr>
        </tbody>
    </table>
    <table style="padding-top: 100px;">
        <tr class="letra12">
            <td class="centrar" style="border-top: 1px solid black; width: 30%;white-space: pre-wrap;">Elaborado por: <br> {{ Auth::user()->user_nombre }}</td>
            <td class="centrar" style="padding-right: 15px; padding-left: 15px;"></td>
            <td class="centrar" style="border-top: 1px solid black; width: 30%; ">   Autorizado Por:   </td>
            <td class="centrar" style="padding-right: 15px; padding-left: 15px;"></td>
            <td class="centrar" style="border-top: 1px solid black; width: 30%;white-space: pre-wrap;">Empleado: <br> {{ $rol->empleado->empleado_nombre }} <br> {{ $rol->empleado->empleado_cedula }}</td>
        </tr>
    </table>
   
@endsection