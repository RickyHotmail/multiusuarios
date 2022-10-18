@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('ciudad.update', [$ciudad->ciudad_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Ciudad</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--     
                <button type="button" onclick='window.location = "{{ url("ciudad") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="provincia_nombre" class="col-sm-2 col-form-label">Nombre de Ciudad</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="ciudad_nombre" name="ciudad_nombre" placeholder="Nombre" value="{{$ciudad->ciudad_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="ciudad_codigo" class="col-sm-2 col-form-label">Codigo de Provincia</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="ciudad_codigo" name="ciudad_codigo" placeholder="Ej. 45525" value="{{$ciudad->ciudad_codigo}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="provincia_id" class="col-sm-2 col-form-label">Nombre de Provincia</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="provincia_id" name="provincia_id" require>
                        @foreach($provincias as $provincia)
                            @if($provincia->provincia_id == $ciudad->provincia_id)
                                <option value="{{$provincia->provincia_id}}" selected>{{$provincia->provincia_nombre}}</option>
                            @else 
                                <option value="{{$provincia->provincia_id}}">{{$provincia->provincia_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>                                                     
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($ciudad->ciudad_estado=="1")
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