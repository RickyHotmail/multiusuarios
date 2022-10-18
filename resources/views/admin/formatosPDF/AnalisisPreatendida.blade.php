@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">Analisis Laboratorio</td></tr>
    @endsection
    <table >
        <tr class="letra12"> 
            <td class="negrita" style="width:10px;" >Fecha:</td>
            <td style="width:40px;">{{ $ordenAtencion->orden_fecha }} </td>
        </tr>
        <tr class="letra12"> 
            <td class="negrita" style="width:10px;" >PARA:</td>
            <td style="width:40px;">{{ $orden->paciente_apellidos }} {{ $orden->paciente_nombres }} </td>
        </tr>
        <tr class="letra12"> 
            <td class="negrita" style="width:10px;" >Codigo:</td>
            <td style="width:40px;">{{ $orden->orden_codigo }} </td>
        </tr>
        <tr class="letra12"> 
            <td class="negrita" style="width:10px;" >Numero:</td>
            <td style="width:40px;">{{ $orden->orden_numero }} </td>
        </tr>
    </table>
    
@endsection