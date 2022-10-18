@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('provincia.update', [$provincia->provincia_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Provincia</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("provincia") }}";'  class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="provincia_nombre" class="col-sm-2 col-form-label">Nombre de Provincia</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="provincia_nombre" name="provincia_nombre" placeholder="Nombre" value="{{$provincia->provincia_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="provincia_codigo" class="col-sm-2 col-form-label">Codigo de Provincia</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="provincia_codigo" name="provincia_codigo" placeholder="Ej. 4561" value="{{$provincia->provincia_codigo}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Nombre de Pais</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="pais_id" name="pais_id" require>
                        @foreach($paises as $pais)
                            @if($pais->pais_id == $provincia->pais_id)
                                <option value="{{$pais->pais_id}}" selected>{{$pais->pais_nombre}}</option>
                            @else 
                                <option value="{{$pais->pais_id}}">{{$pais->pais_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>                                                     
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($provincia->provincia_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="provincia_estado" name="provincia_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="provincia_estado" name="provincia_estado">
                        @endif
                        <label class="custom-control-label" for="provincia_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection