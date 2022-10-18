@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("signosVitales") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Nuevos Signos Vitales</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='history.back();' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="modal-body">
            <div class="card-body">                
                <div class="form-group row">
                    <label for="fecha" class="col-sm-2 col-form-label">Fecha:</label>
                    <div class="col-sm-4">
                        <input type="hidden"  id="idorden" name="idorden" value="{{ $ordenAtencion->orden_id }}" readonly>                                
                        <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $ordenAtencion->orden_fecha }}" readonly>
                    </div>
                    <label for="hora" class="col-sm-2 col-form-label">Hora:</label>
                    <div class="col-sm-4">                                                          
                        <input type="time" class="form-control" id="hora" name="hora" value="{{ $ordenAtencion->orden_hora }}" readonly>                  
                    </div>
                </div>
                <div class="form-group row">
                    <label for="medico" class="col-sm-2 col-form-label">Médico tratante:</label>
                    <div class="col-sm-10"> 
                    <input type="text" class="form-control" id="medico" name="medico"  placeholder="Médico" value="@if($ordenAtencion->medico->proveedor){{$ordenAtencion->medico->proveedor->proveedor_nombre}} @else @if($ordenAtencion->medico->empleado) {{$ordenAtencion->medico->empleado->empleado_nombre}} @endif @endif  " readonly>                            
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Paciente" class="col-sm-2 col-form-label">Paciente:</label>
                    <div class="col-sm-10"> 
                        <input type="text" class="form-control" id="Paciente" name="Paciente"  placeholder="Paciente" value="{{$ordenAtencion->paciente->paciente_apellidos }} {{$ordenAtencion->paciente->paciente_nombres }}    " readonly>                           
                    </div>
                </div>
                <?php $count=1;?>
                @if(isset($signosvitales))
                    @foreach($signosvitales as $signosvital)
                        @if(($count % 2) != 0)
                        <div class="form-group row">
                        @endif
                            <label for="id{{$signosvital->signose_nombre}}" class="col-sm-2 col-form-label">{{$signosvital->signose_nombre}}:</label>
                            <div class="col-sm-3">    
                                <input @if($signosvital->signose_tipo==1) type="text" @endif @if($signosvital->signose_tipo==2) type="number" min="0"  step="0.01" required @endif class="form-control" id="id{{$signosvital->signose_nombre}}" name="valor[]" value="" required> 
                                <input type="hidden" name="nombre[]" value="{{$signosvital->signose_nombre}}">  
                                <input type="hidden" name="tipo[]" value="{{$signosvital->signose_tipo}}">
                                <input type="hidden" name="medida[]" value="{{$signosvital->signose_medida}}">
                                <input type="hidden" name="ide[]" value="{{$signosvital->signose_id}}">      
                            </div>
                            <label for="id{{$signosvital->signose_nombre}}" class="col-sm-1 col-form-label">{{$signosvital->signose_medida}}</label>    
                        @if(($count % 2) == 0)        
                        </div>
                        @endif
                        <?php $count++;?>   
                    @endforeach
                    @if((($count-1) % 2) != 0)
                        </div>
                    @endif 
                @endif                                          
            </div>
        </div>
    </div>
</form>
@endsection