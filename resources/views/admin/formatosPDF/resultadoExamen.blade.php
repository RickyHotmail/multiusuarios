@extends ('admin.layouts.encabesadoclinico')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2"><table><tr><td class="centrar letra22 negrita">ORDEN DE EXAMEN N° {{$analisis->analisis_numero}} </td></tr></table></td>
    @endsection
    <table class="clase_table">
        <tr class="letra14">
            <td class="negrita" style="width: 80px;">FECHA:</td>
            <td>{{ $analisis->analisis_fecha }}</td>
            <td class="negrita" style="width: 80px;">Sucurlas:</td>
            <td>{{ $analisis->sucursal->sucursal_nombre}}</td>
        </tr>
        <tr>
            <td class="negrita" style="width: 80px;">Paciente:</td>
            <td>{{ $analisis->orden->expediente->ordenatencion->paciente->paciente_apellidos }} {{  $analisis->orden->expediente->ordenatencion->paciente->paciente_nombres}}</td>
            <td class="negrita" style="width: 80px;">Sexo:</td>
            <td>{{  $analisis->orden->expediente->ordenatencion->paciente->paciente_sexo }}</td>
        </tr>        
        <tr class="letra14">
            <td class="negrita" style="width: 80px;">Edad:</td>
            <input type="hidden" value="<?php $cumpleanos = new DateTime( $analisis->orden->expediente->ordenatencion->paciente->paciente_fecha_nacimiento); $hoy = new DateTime(); $annos = $hoy->diff($cumpleanos); ?>">
            <td><?php echo $annos->y; ?> </td>
            <td class="negrita" style="width: 80px;">Medico:</td>
            <td>Dr. @if( $analisis->orden->expediente->ordenatencion->medico->empleado){{  $analisis->orden->expediente->ordenatencion->medico->empleado->empleado_nombre }} @else {{  $analisis->orden->expediente->ordenatencion->medico->proveedor->proveedor_nombre }} @endif</td>
        </tr>
        <tr class="letra14">
            <td class="negrita" style="width: 80px;">Sede:</td>
            <td>{{ $analisis->sucursal->sucursal_nombre}}</td>
            <td class="negrita" style="width: 80px;">Aseguradora:</td>
            <td>{{ $analisis->orden->expediente->ordenatencion->cliente->cliente_nombre}}</td>
        </tr>  
    </table>
    <br>

    <table tyle="white-space: normal!important;" >
                    <tr >
                        <td  class="negrita letra14"></td>
                        <td  class="negrita letra14 centrar">RESULTADO</td>
                        <td  class="negrita letra14 "> VALORES REFERENCIALES</td>
                    </tr> 
                    @foreach($analisis->detalles as $detallesanalaisis)
                    <tr >
                        <td  class="negrita letra14">{{$detallesanalaisis->producto->producto_nombre}}</td>
                        <td  class="letra14"></td>
                        <td  class="letra14"> </td>
                    </tr> 
                    <?php $count=1; ?>
                        @foreach($detallesanalaisis->detalles as $detallesvalor)
                        <tr >
                            <td  class="letra14">{{$count.'. '.$detallesvalor->detalle_descripcion }}</td>
                            <td  class="letra14 centrar">{{$detallesvalor->detalle_valor }} {{$detallesvalor->detalle_unidad }}</td>
                            <td  class="letra12">
                            @foreach($detallesvalor->detalles as $detallesrefe)
                                {{$detallesrefe->detalle_Columna1 }} {{$detallesrefe->detalle_Columna2 }}
                            <br>
                            @endforeach 
                            </td>
                            <?php $count++; ?>
                        </tr> 
                        @endforeach 
                    @endforeach 
                    <br>
                   
    </table>

    <style>
    .clase_table {
        border-collapse:separate;
        border:solid black 1px;
        border-radius:10px;
        -moz-border-radius:10px;
        -webkit-border-radius: 5px;  
    }
    </style>



@endsection


