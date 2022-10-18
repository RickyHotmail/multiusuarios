@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('especialidad.update', [$especialidad->especialidad_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Especialidad</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("especialidad") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idCodigo" class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCodigo" name="idCodigo"  value="{{$especialidad->especialidad_codigo}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="especialidad_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="especialidad_nombre" name="especialidad_nombre"  value="{{$especialidad->especialidad_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="especialidad_tipo" class="col-sm-2 col-form-label">Tipo</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="especialidad_tipo" name="especialidad_tipo" required>
                        <option value="1" @if($especialidad->especialidad_tipo == '1') selected @endif>ESPECIALISTA</option>
                        <option value="2" @if($especialidad->especialidad_tipo == '2') selected @endif>GENERAL</option>  
                        <option value="2" @if($especialidad->especialidad_tipo == '3') selected @endif>ODONTOLOGIA</option>                                                    
                    </select>
                </div>
            </div>          
            <div class="form-group row">
                <label for="especialidad_duracion" class="col-sm-2 col-form-label">Duración</label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="especialidad_duracion" name="especialidad_duracion" required>
                        <?php $count = 5; ?>
                        @while($count <= 150)
                        <option value="{{ $count }}" @if($especialidad->especialidad_duracion == $count) selected @endif>{{$count.' min'}}</option>
                        <?php $count = $count +5; ?>
                        @endwhile
                    </select>
                </div>
                <label for="especialidad_flexible" class="col-sm-2 col-form-label">Duración Flexible</label>
                <div class="col-sm-4">
                    <input type="checkbox" name="especialidad_flexible" data-bootstrap-switch data-off-color="danger" data-on-color="success" @if($especialidad->especialidad_flexible == 1) checked @endif>
                </div>
            </div>                    
            <div class="form-group row">
                <label for="especialidad_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($especialidad->especialidad_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="especialidad_estado" name="especialidad_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="especialidad_estado" name="especialidad_estado">
                        @endif
                        <label class="custom-control-label" for="especialidad_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection