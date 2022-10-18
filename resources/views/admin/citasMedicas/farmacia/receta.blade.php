@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Despacho de Prescripciones</h3>
        <div class="float-right">
            <button type="button" onclick='window.location = "{{ url("receta") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-6">
                <div  style="border-radius: 5px; background:#ddd; padding: 10px">
                    <h4 class="text-center">Paciente</h4>
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label>Cédula: </label>
                            <input readonly class="form-control" type="text" value="{{ $ordenAtencion->paciente->paciente_cedula}}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row pl-2 pr-2">
                                    <label>Nombres: </label>
                                    <input readonly class="form-control" type="text" value="{{ $ordenAtencion->paciente->paciente_apellidos}} {{ $ordenAtencion->paciente->paciente_nombres}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label>Dirección: </label>
                                    <input readonly class="form-control" type="text" value="{{ $ordenAtencion->paciente->paciente_direccion}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div  style="border-radius: 5px; background:#ddd; padding: 10px">
                    <h4 class="text-center">Datos de la Orden</h4>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row pl-2 pr-2">
                                        <label>Fecha: </label>
                                        <input readonly class="form-control" type="text" value="{{ $ordenAtencion->orden_fecha}} {{ $ordenAtencion->orden_hora}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <label>Orden: </label>
                                        <input readonly class="form-control text-right" type="text" value="{{ $ordenAtencion->orden_id}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <label>Doctor: </label>
                                    <input readonly class="form-control" type="text" value="{{ $ordenAtencion->medico->empleado->empleado_nombre}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">

                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        
        
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Código</th>
                    <th>Medicamento</th> 
                    <th>Cantidad</th> 
                    <th>Indicaciones</th>                                                                                      
                </tr>
            </thead>

            <tbody>
            @foreach($prescripcionDet as $detalle)
                <tr class="text-center">
                    <th></th>
                    <td>{{ $detalle->producto_codigo }}</td>
                    <td>{{ $detalle->producto_nombre }}</td>
                    <td>{{ $detalle->prescripcionm_cantidad }}</td>
                    <td>{{ $detalle->prescripcionm_indicacion }}</td>                                         
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="text-right mt-2">
            @if($prescripcionCab->prescripcion_estado==1)
                <a href="{{ url('receta') }}/entregar/{{ $ordenAtencion->orden_id }}" onclick="return confirm('Marcar esta Prescripción como Entregada, se imprimirá una Receta Médica')" class="btn btn-primary">
                    <i class="far fa-flag mr-1"></i>
                    Despachar
                </a>
            @elseif($prescripcionCab->prescripcion_estado==2)
                <a target="_blank" href="{{ url('receta/imprimir' )}}/{{ $ordenAtencion->orden_id }}" class="btn btn-warning">
                    <i class="fa fa-print"></i>
                    Imprimir
                </a>
            @endif
        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.modal -->
@endsection