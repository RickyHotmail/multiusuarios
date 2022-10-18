@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('examen.update', [$examen->examen_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Examen</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("examen") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">

            <div class="form-group row">
                <label for="tipo_id" class="col-sm-2 col-form-label">Tipo de Examen</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="tipo_id" name="tipo_id" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($tipoExamenes as $tipoExamen)
                            @if($tipoExamen->tipo_id == $examen->tipo_id)
                                <option value="{{ $tipoExamen->tipo_id }}" selected>{{ $tipoExamen->tipo_nombre }}</option>   
                            @else
                                <option value="{{ $tipoExamen->tipo_id }}">{{ $tipoExamen->tipo_nombre }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="producto_id" class="col-sm-2 col-form-label">Nombre de Examen</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="producto_id" name="producto_id" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($producto as $productos)
                            @if($productos->producto_id == $examen->producto_id)
                                <option value="{{ $productos->producto_id }}" selected>{{ $productos->producto_nombre }}</option>   
                            @else
                                <option value="{{ $productos->producto_id }}">{{ $productos->producto_nombre }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>                                                     
            <div class="form-group row">
                <label for="examen_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($examen->examen_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="examen_estado" name="examen_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="examen_estado" name="examen_estado">
                        @endif
                        <label class="custom-control-label" for="examen_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection