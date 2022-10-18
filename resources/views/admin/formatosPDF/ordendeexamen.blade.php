@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2"><table><tr><td class="centrar letra22 negrita">ORDEN DE EXAMEN N° {{$analisis->analisis_numero}} </td></tr></table></td>
    @endsection
    <table>
        <tr class="letra14">
            <td class="negrita" style="width: 105px;">FECHA:</td>
            <td>{{ $analisis->analisis_fecha }}</td>
            <td class="negrita" style="width: 105px;">Sucurlas:</td>
            <td>{{ $analisis->sucursal->sucursal_nombre}}</td>
        </tr>
        <tr class="letra14">
            <td class="negrita" style="width: 125px;">CLIENTE:</td>
            <td>{{ $orden->expediente->ordenatencion->paciente->paciente_apellidos }} {{ $orden->expediente->ordenatencion->paciente->paciente_nombres}}</td>
            <td class="negrita" style="width: 125px;">Sexo:</td>
            <td>{{ $orden->expediente->ordenatencion->paciente->paciente_sexo }}</td>
            </tr>        
            <tr class="letra14">
            <td class="negrita" style="width: 125px;">Edad:</td>
            <input type="hidden" value="<?php $cumpleanos = new DateTime($orden->expediente->ordenatencion->paciente->paciente_fecha_nacimiento); $hoy = new DateTime(); $annos = $hoy->diff($cumpleanos); ?>">
            <td><?php echo $annos->y; ?> </td>
            <td class="negrita" style="width: 125px;">Medico:</td>
            <td>Dr. @if($orden->expediente->ordenatencion->medico->empleado){{ $orden->expediente->ordenatencion->medico->empleado->empleado_nombre }} @else {{ $orden->expediente->ordenatencion->medico->proveedor->proveedor_nombre }} @endif</td>
        </tr>
    </table>
    <br>
    
    <table tyle="white-space: normal!important;" id="tabladetalle">
        <?php $c=0 ?>
        @foreach($ordenes->detalle as $detalle)
            <?php $c++ ?>
            <tr style="border: 1px solid black;">
                <td  style="border: 1px solid black;" class="letra14">{{ $c }}.- &nbsp;&nbsp; {{ $detalle->examen->producto->producto_codigo }} - {{$detalle->examen->producto->producto_nombre }}</td>
            </tr> 
        @endforeach 
    </table>
    <br>
    <table >
        <tr class="letra14">
            <td  style="font-weight: bold;"  class="letra14">Otros Examenes:   <div  class="letra14" style=" font-weight: normal;"><b></b>{{ $orden->analisis->analisis_otros}} </div> </td>
        </td>
        <br>    
        </tr>
    </table>
    
<table>
  <tr>
    <td class="centrar letra22 negrita">ETIQUETAS </td>
  </tr>
</table>

<table>
{{--@foreach($etiquetas as $etiqueta) --}}
{{--<tr style="min-width: 235px; height: 70px;background-color: #000000; color: rgb(255, 255, 255); text-align: center;">--}}
{{--<td >{{$etiqueta->tipo_nombre }}<br>N° {{$analisis->analisis_numero}} <br> {{ $orden->expediente->ordenatencion->paciente->paciente_apellidos }} {{ $orden->expediente->ordenatencion->paciente->paciente_nombres}}</td>      --}}
{{--</tr>--}}
{{--@endforeach--}}
</table>


@endsection


