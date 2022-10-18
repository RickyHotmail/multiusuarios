@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
    <tr>
        <td colspan="2">
            <table>
                <tr>
                    <td class="centrar letra12 negrita">RECLAMO N°: {{ $orden->orden_reclamo}}</td>
                    <td class="centrar letra12 negrita">ORDEN MEDICA N°: {{ $orden->orden_numero}}</td>
                </tr>
                <tr>
                    <td class="centrar letra22 negrita">ORDEN DE ATENCION MEDICA</td>
                </tr>
            </table>
        </td>
    @endsection
    <table>
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">FECHA:</td>
            <td>{{ $orden->orden_fecha }}</td>
            <td class="negrita" style="width: 125px;">Médico:</td>
            <td> @if(isset($orden->medico->empleado)) {{$orden->medico->empleado->empleado_nombre}} @else @if(isset($orden->medico->proveedor)) {{$orden->medico->proveedor->proveedor_nombre}} @endif @endif</td>
        </tr>
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">Paciente:</td>
            <td>{{ $orden->paciente->paciente_apellidos}} {{ $orden->paciente->paciente_nombres}}</td>
            <td class="negrita" style="width: 125px;">Especialidad:</td>
            <td>{{ $orden->especialidad->especialidad_nombre}}</td>
        </tr>  
        <tr class="letra12">
            <td class="negrita" style="width: 125px;">Cedula:</td>
            <td>{{ $orden->paciente->paciente_cedula}}</td>
            <td class="negrita" style="width: 105px;">Aseguradora:</td>
            <td>{{ $orden->cliente->cliente_nombre }}</td>
        </tr> 
        <tr class="letra12">
            <td class="negrita" style="width: 125px;">Compañia:</td>
            <td>{{ $empresa->empresa_nombreComercial }}</td>
            <td class="negrita" style="width: 105px;">Cobertura:</td>
            <td>{{ $orden->cliente->orden_cobertura_porcentaje }} %</td>
        </tr>   
       
    </table>

    <br>
    <table style="white-space: normal!important;" id="tabladetalle">>
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra10"> 
                <th>Cantidad</th>   
                <th>Detalle</th>                 
                <th>Valor</th>
                <th>Cobertura</th>
                <th>Copago</th>
            </tr>
        </thead>
        <tbody>                   
                    <tr class="letra10" style="border: 1px solid black;">              
                        <td align="center">1</td>          
                        <td align="left">{{$orden->producto->producto_nombre  }}</td>
                        <td align="right">{{$orden->orden_precio }}</td>
                        <td align="right">{{$orden->orden_cobertura  }}</td>                   
                        <td align="right">{{$orden->orden_copago }}</td>
                    </tr>    
               
          
            <tr class="letra10"  style="border: 1px solid black;">
                <td align="right" style="border: 1px solid black;" colspan="2">TOTAL</td>     
                <td align="right" style="border: 1px solid black;">{{ $orden->orden_precio}}</td>
                <td align="right" style="border: 1px solid black;">{{ $orden->orden_cobertura}}</td>
                <td align="right" style="border: 1px solid black;">{{ $orden->orden_copago}}</td>
            </tr>   
        </tbody>
    </table>
@endsection