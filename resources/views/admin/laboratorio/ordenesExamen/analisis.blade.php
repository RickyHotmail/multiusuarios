@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title pt-2">Analisis de Laboratario</h3>
            </div>
            <div class="col-md-6 text-right">
                <form method="POST" action='{{ url("analisisLaboratorio/cargarDatosExamenes") }}'>
                    @csrf
                    <button class="btn btn-sm btn-success" ><i class="fas fa-sync"></i> Actualizar</button>
                </form> 
            </div>
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Sucursal</th> 
                    <th>Codigo</th>
                    <th>Paciente</th> 
                    <th>Fecha</th> 
                    <th>Otros Examenes</th>                                                                                       
                </tr>
            </thead>
            <tbody>
                <?php $count=1;?>
                @foreach($analisisuser as $ordenanalisisuser)
                    @if($ordenanalisisuser->analisis_estado == 3)  
                        @foreach($ordenanalisisuser->detalles as $detalles)
                            @if($detalles->detalle_estado=='1')
                                <?php $count=2;?>
                            @endif
                        @endforeach
                        <tr class="text-center">
                            <td> 
                                <a href="{{ url("analisisLaboratorio/{$ordenanalisisuser->orden->orden_id}/imprimirorden") }}" target="_blank" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Imprimir Ordenr"><i class="fa fa-print"></i></a>
                                
                                @if($ordenanalisisuser->analisis_estado == 3)
                                    {{$ordenanalisisuser->analisis_estado}}
                                    <a target="_blank" href="{{ url("analisisLaboratorio/{$ordenanalisisuser->analisis_laboratorio_id}/resultados") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver Resultados"><i class="fa fa-vial"></i></a> 

                                    @if($count=='1')
                                        <a href="{{ url("analisisLaboratorio/{$ordenanalisisuser->analisis_laboratorio_id}/enviar") }}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Enviar por Correo"><i class="fas fa-envelope"></i></a> 
                                    @endif
                                @elseif ($ordenanalisisuser->analisis_estado == 2)
                                    <a class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="No enviado al Lab."><i class="fas fa-exclamation-circle"></i>&nbsp; NO ENVIADO</a> 
                                
                                @else
                                    <a class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Sin Datos Aún"><i class="fas fa-exclamation-circle"></i>&nbsp; PENDIENTE</a> 
                                @endif
                            </td>    
                            <td>{{ $ordenanalisisuser->sucursal->sucursal_nombre }}</td>
                            <td>{{ $ordenanalisisuser->analisis_numero }}</td>
                            <td>{{ $ordenanalisisuser->orden->expediente->ordenatencion->paciente->paciente_apellidos}} {{ $ordenanalisisuser->orden->expediente->ordenatencion->paciente->paciente_nombres }}<br>
                            <td>{{ $ordenanalisisuser->analisis_fecha }}</td>
                            <td>{{ $ordenanalisisuser->analisis_otros }}</td>                                         
                        </tr>
                    @endif       
                @endforeach
                @foreach($analisis as $ordenanalisis)
                    <tr class="text-center">
                        <td> 
                            {{$ordenanalisis->analisis_estado}}
                            <a href="{{ url("analisisLaboratorio/{$ordenanalisis->orden->orden_id}/imprimirorden") }}" target="_blank" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Imprimir Orden"><i class="fa fa-print"></i></a>  
                            @if($ordenanalisis->analisis_estado == 3)   
                                <a  target="_blank" href="{{ url("analisisLaboratorio/{$ordenanalisis->analisis_laboratorio_id}/resultados") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver Resultados"><i class="fa fa-vial"></i></a> 
                                <a href="{{ url("analisisLaboratorio/{$ordenanalisis->analisis_laboratorio_id}/enviar") }}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Enviar por Correo"><i class="fas fa-envelope"></i></a> 
                            @elseif ($ordenanalisis->analisis_estado == 2)
                                <a class="btn btn-xs btn-outline-primary" data-toggle="tooltip" data-placement="top" title="Sin Datos Aún"><i class="fas fa-exclamation-circle"></i>&nbsp; PENDIENTE</a> 
                            @else
                                <a class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="No enviado al Lab."><i class="fas fa-exclamation-circle"></i>&nbsp; NO ENVIADO</a> 
                            @endif
                        </td>    
                        <td>{{ $ordenanalisis->sucursal->sucursal_nombre }}</td>
                        <td>{{ $ordenanalisis->analisis_numero }}</td>
                        <td>{{ $ordenanalisis->orden->expediente->ordenatencion->paciente->paciente_apellidos}} {{ $ordenanalisis->orden->expediente->ordenatencion->paciente->paciente_nombres }}<br>
                        <td>{{ $ordenanalisis->analisis_fecha }}</td>
                        <td>{{ $ordenanalisis->analisis_otros }}</td>                                         
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.modal -->
@endsection