@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">ROL DE EMPLEADO</td></tr>
    @endsection
    @foreach($rol->detalles as $detalle)
    <table style="white-space: normal!important; border-collapse: collapse;">
        <tr class="letra12">
            
                <td class="negrita" style="width: 105px;">Fecha desde:</td>
                <td>{{ $detalle->detalle_rol_fecha_inicio}}</td>
                <td class="negrita" style="width: 105px;">Fecha hasta:</td>  
                <td> 
                    {{$detalle->detalle_rol_fecha_fin }}   
                </td>   
        </tr>
        <tr class="letra12"> 
            <td class="negrita" style="width: 105px;">Cedula:</td>
            <td>{{$rol->empleado_cedula}}</td>
            <td class="negrita" style="width: 105px;">Nombre:</td>
            <td>{{ $rol->empleado_nombre }}</td>
        </tr>
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">Dias trabajados:</td>
            <td>{{ $detalle->detalle_rol_dias}}</td>
            <td class="negrita" style="width: 105px;">Cargo:</td>  
            <td> 
                {{$rol->empleado_cargo_nombre }}   
            </td> 
        </tr>  
    </table>
    <br>
    <table style="white-space: normal!important; border-collapse: collapse;">
    <tr class="letra12"> 
             <td class="centrar letra15 negrita" style="width: 105px;">Rol del Mes de {{ $mes }} del {{date("Y", strtotime($rol->cabecera_rol_fecha))}}</td>
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
                        <td class="detalle-diario">Sueldo por  {{$detalle->detalle_rol_dias}} dias:</td>
                        <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_total_dias,2)}}</td>
                        <td class="foot-rol-linea-left detalle-diario">IESS :</td>
                        <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_iess,2)}}</td>
                </tr>
                <tr class="letra12">
                    <td class="detalle-diario">Vacaciones:</td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_vacaciones,2)}}</td>
                    <td class="foot-rol-linea-left detalle-diario">IESS Asumido :</td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_iess_asumido,2)}}</td>
                </tr>
                <tr class="letra12">
                    <td class="detalle-diario">Horas Extras:</td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_valor_he,2)}}</td>
                    <td class="foot-rol-linea-left detalle-diario">Anticipos :</td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_total_anticipo,2)}}</td>
                </tr>
                    
                <tr class="letra12">
                    <td class="detalle-diario">Horas Suplementarias:</td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_horas_suplementarias,2)}}</td>
                    <td class="foot-rol-linea-left detalle-diario">Imp. Renta :</td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_impuesto_renta,2)}}</td>
                </tr>
                <tr class="letra12">
                    <td class="detalle-diario">Viaticos:</td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_transporte,2)}}</td>
                    <td class="foot-rol-linea-left detalle-diario">EXT. Salud: </td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_ext_salud,2)}}</td>
                </tr>
                <tr class="letra12">
                    <td class="detalle-diario">otros Bonificaciones	:</td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_otra_bonificacion,2)}}</td>
                    <td class="foot-rol-linea-left detalle-diario">Alimentación: </td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_total_comisariato,2)}}</td>
                </tr>
               
                <tr class="letra12">
                    <td class="detalle-diario">otros Ingresos :</td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_otros_ingresos,2) }}</td>
                    <td class="foot-rol-linea-left detalle-diario">PPQQ: </td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_prestamo_quirografario,2)}}</td>
                </tr>
                <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol-linea-left detalle-diario">Prestamos Hipotecarios: </td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_prestamo_hipotecario,2)}}</td>
                </tr>
                <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol-linea-left detalle-diario">Prestamos: </td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_prestamo,2)}}</td>
                </tr>
                <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol-linea-left detalle-diario">Multas: </td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_multa,2)}}</td>
                </tr>
                <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol-linea-left detalle-diario">Otros Egresos: </td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_otros_egresos,2)}}</td>
                </tr>
                <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol-linea-left detalle-diario">ley salud: </td>
                    <td class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_ley_sol,2)}}</td>
                </tr>
                    <tr class="letra12">
                    <td style="border-top: 1px solid black; padding: 6px 6px;" class="detalle-diario">Total Ingresos</td>
                    <td style="border-top: 1px solid black; padding: 6px 6px;" class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_total_ingreso,2)}}</td>
                    <td style="border-top: 1px solid black; padding: 6px 6px; border-left: 1px solid black;" class="detalle-diario">Total Egresos: </td>
                    <td style="border-top: 1px solid black; padding: 6px 6px;" class="detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_total_egreso,2)}}</td>
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
                    <td class="foot-rol detalle-diario">Total Ingresos</td>
                    <td  class="foot-rol-linea detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_total_ingreso,2)}}</td>
                    </tr>
                    <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol detalle-diario">Total Egresos: </td>
                    <td  class="foot-rol-linea detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_total_egreso,2)}}</td>
                    </tr>
                    <tr class="letra12">
                        <td class="detalle-diario"></td>
                        <td class="detalle-diario dereche"></td>
                        <td class="foot-rol detalle-diario">Fondos Reserva :</td>
                        <td class="foot-rol-linea detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_fondo_reserva,2)}}</td>
                   </tr>
                   <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol detalle-diario">Decimo Tercero :</td>
                    <td class="foot-rol-linea detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_decimo_tercero,2)}}</td>
                     </tr>

                   <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol detalle-diario">Decimo Cuarto :</td>
                    <td class="foot-rol-linea detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_decimo_cuarto,2)}}</td>
                    </tr>
                    <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol detalle-diario">Vac. Pagadas :</td>
                    <td  class="foot-rol-linea detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_vacaciones_anticipadas,2)}}</td>
                    </tr>
                    <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-rol detalle-diario">Quincena :</td>
                    <td  class="foot-rol-linea detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_quincena,2)}}</td>
                    </tr>
                    
                    <tr class="letra12">
                    <td class="detalle-diario"></td>
                    <td class="detalle-diario dereche"></td>
                    <td class="foot-diario centrar">Liquido a Pagar</td>
                    <td class="foot-rol-linea detalle-diario dereche">$ {{ number_format($detalle->detalle_rol_liquido_pagar,2)}}</td>
                    </tr>
        </tbody>
    </table>
    <table style="padding-top: 100px;">
        <tr class="letra12">
            <td class="centrar" style="border-top: 1px solid black; width: 30%;white-space: pre-wrap;">Elaborado por: <br> {{ Auth::user()->user_nombre }}</td>
            <td class="centrar" style="padding-right: 15px; padding-left: 15px; width: 5%;"></td>
            <td class="centrar" style="border-top: 1px solid black; width: 30%; ">   Gerente:   </td>
            <td class="centrar" style="padding-right: 15px; padding-left: 15px; width: 5%;"></td>
            <td class="centrar" style="border-top: 1px solid black; width: 30%;white-space: pre-wrap;">Empleado: <br> {{ $rol->empleado->empleado_nombre }}</td>
        </tr>
    </table>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    @endforeach
@endsection