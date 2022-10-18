@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
    <tr>
        <td colspan="2">
            <table>
                <tr>
                    <td class="centrar letra12 negrita">PRESCRIPCION MEDICA N°: {{ $ordenAtencion->orden_numero}}</td>
                </tr>
                <tr>
                    <--td class="centrar letra22 negrita">ORDEN DE ATENCION MEDICA</td-->
                </tr>
            </table>
        </td>
    @endsection
    <table>
        <tr class="letra12">
            <td class="negrita" style="width: 85px;">FECHA:</td>
            <td>{{ $ordenAtencion->orden_fecha }}</td>
            <td class="negrita" style="width: 125px;">Médico:</td>
            <td> @if(isset($ordenAtencion->medico->empleado)) {{$ordenAtencion->medico->empleado->empleado_nombre}} @else @if(isset($ordenAtencion->medico->proveedor)) {{$ordenAtencion->medico->proveedor->proveedor_nombre}} @endif @endif</td>
        </tr>
        <tr class="letra12">
            <td class="negrita" style="width: 85px;">Paciente:</td>
            <td>{{ $ordenAtencion->paciente->paciente_apellidos}} {{ $ordenAtencion->paciente->paciente_nombres}}</td>
            <td class="negrita" style="width: 125px;">Especialidad:</td>
            <td>{{ $ordenAtencion->especialidad->especialidad_nombre}}</td>
        </tr>  
        <tr class="letra12">
            <td class="negrita" style="width: 85px;">Cedula:</td>
            <td>{{ $ordenAtencion->paciente->paciente_cedula}}</td>
            <td class="negrita" style="width: 105px;">Aseguradora:</td>
            <td>{{ $ordenAtencion->cliente->cliente_nombre }}</td>
        </tr> 
        <tr class="letra12">
            <td class="negrita" style="width: 85px;">Compañía:</td>
            <td>{{ $empresa->empresa_nombreComercial }}</td>
            <td class="negrita" style="width: 85px;">Cobertura:</td>
            <td>100 %</td>
        </tr>   
       
    </table>

    <style>
        #example1{  
            border: 1px solid #ddd;
            border-collapse: collapse;
        }

        #example1 td {  
            border: 1px solid #ddd;
            border-collapse: collapse;
            padding-left: 5px;
            padding-right: 5px;
        }

        #example1 th {  
            border: 1px solid #ddd;
            text-align: center;
            border-collapse: collapse;
        }
    </style>

    <br>
    <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="neo-fondo-tabla">
                    <th style="width: 10%">Código</th>
                    <th style="width: 30%">Medicamento</th> 
                    <th style="width: 7%">Cant.</th> 
                    <th style="width: 53%">Indicaciones</th>                                                                                      
                </tr>
            </thead>

            <tbody>
            @foreach($prescripcionD as $detalle)
                <tr style="font-size: 12px">
                    <td style="width: 10%">{{ $detalle->producto_codigo }}</td>
                    <td style="width: 30%">{{ $detalle->producto_nombre }}</td>
                    <td style="width: 7%; text-align:center">{{ $detalle->prescripcionm_cantidad }}</td>
                    <td style="width: 53%">{{ $detalle->prescripcionm_indicacion }}</td>                                         
                </tr>
            @endforeach
            </tbody>
        </table>
@endsection