@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url('actualizarSignosOrdenAtencion') }}">
    <input type="hidden" name="orden" value="{{ $orden->orden_id }}">
    <!--@method('PUT')-->
    @csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Signos Vitales</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick="history.back();" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="fecha" class="col-sm-2 col-form-label">Fecha:</label>
                <div class="col-sm-4">    
                    <input type="date" class="form-control" id="fecha" name="fecha" value="{{$orden->orden_fecha}}" readonly>              
                </div>
                <label for="hora" class="col-sm-2 col-form-label">Hora:</label>
                <div class="col-sm-4">                                                          
                    <input type="time" class="form-control" id="hora" name="hora" value="{{$orden->orden_hora}}" readonly>                                    
                </div>
            </div>
            <div class="form-group row">
                <label for="medico" class="col-sm-2 col-form-label">Médico tratante:</label>
                <div class="col-sm-10"> 
                    <input type="text" class="form-control" id="medico" name="medico"  placeholder="Médico" value="{{$orden->medico->empleado->empleado_nombre}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="medico" class="col-sm-2 col-form-label">Paciente:</label>
                <div class="col-sm-10"> 
                    <input type="text" class="form-control" id="medico" name="medico"  placeholder="Médico" value="{{$orden->paciente->paciente_apellidos.' '.$orden->paciente->paciente_nombres}}" readonly>                      
                </div>
            </div>

            <div class="form-group row">
                @foreach($orden->expediente->signosVitales as $signos)
                    <input type="hidden" name="signo_id[]" value="{{ $signos->signo_id }}">
                    <input type="hidden" name="expediente[]" value="{{ $signos->expediente_id }}">
                    <input type="hidden" name="tipo[]" value="{{ $signos->signo_tipo }}">

                    <input type="hidden" name="signo_nombre[]" value="{{ $signos->signo_nombre }}">
                    <input type="hidden" name="signo_medida[]" value="{{ $signos->signo_medida }}">

                    <label for="signo_pas" class="col-sm-2 col-form-label">{{ $signos->signo_nombre }} ({{ $signos->signo_medida }})</label>              
                    <div class="col-sm-4 mb-2">                                
                        <input type="number" min="0"  step="0.5" class="form-control"  name="signo_valor[]"  placeholder="Signo pas" value="{{ $signos->signo_valor }}" required>
                    </div>
                @endforeach
            </div>
            
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-9">
                    <div class="col-form-label custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                       
                        <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection