@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('medicamento.update', [$medicamento->medicamento_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Medicamento</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("medicamento") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idproducto" name="idproducto" required>
                        <option value="" label>--Seleccione una opcion--</option>                                                           
                        @foreach($productos as $producto)
                            <option value="{{$producto->producto_id}}" @if($medicamento->producto_id==$producto->producto_id) checked @endif>{{$producto->producto_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idComposicion" class="col-sm-2 col-form-label">Composicion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idComposicion" name="idComposicion"  value="{{$medicamento->medicamento_composicion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idIndicacion" class="col-sm-2 col-form-label">Indicacion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idIndicacion" name="idIndicacion"  value="{{$medicamento->medicamento_indicacion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idContraindicacion" class="col-sm-2 col-form-label">Contraindicacion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idContraindicacion" name="idContraindicacion"  value="{{$medicamento->medicamento_contraindicacion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Medicamento</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idTipo" name="idTipo" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($tipoMedicamentos as $tipoMedicamento)
                            @if($tipoMedicamento->tipo_id == $medicamento->tipo_id)
                                <option value="{{$tipoMedicamento->tipo_id}}" selected>{{$tipoMedicamento->tipo_nombre}}</option>   
                            @else
                                <option value="{{$tipoMedicamento->tipo_id}}">{{$tipoMedicamento->tipo_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>                  
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($medicamento->medicamento_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection