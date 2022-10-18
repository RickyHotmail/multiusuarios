@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('rubro.update', [$rubro->rubro_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Rubro</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("rubro") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="rubro_nombre" class="col-sm-2 col-form-label">Identificador</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="rubro_nombre" name="rubro_nombre" placeholder="Rubro" value="{{$rubro->rubro_nombre}}" readonly required>
                </div>
            </div>
            <div class="form-group row">
                <label for="rubro_descripcion" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="rubro_descripcion" name="rubro_descripcion" placeholder="Ingrese aqui una descripcion" value="{{$rubro->rubro_descripcion}}" required>
                </div>
            </div>    

            <div class="form-group row">
                <label for="rubro_tipo" class="col-sm-2 col-form-label">Tipo de Rubro</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="rubro_tipo" name="rubro_tipo" value="{{$rubro->rubro_tipo}}" require>
                        @if($rubro->rubro_tipo == 1)<option value="1" selected>EGRESOS</option>@else <option value="1">EGRESOS</option>@endif
                        @if($rubro->rubro_tipo == 2)<option value="2" selected>INGRESOS</option>@else <option value="2">INGRESOS</option>@endif
                        @if($rubro->rubro_tipo == 3)<option value="3" selected>PROVISIONES</option>@else <option value="3">PROVISIONES</option>@endif
                        @if($rubro->rubro_tipo == 4)<option value="4" selected>OTROS</option>@else <option value="4">OTROS</option>@endif
                    </select>
                </div>
            </div>                                           
            <div class="form-group row">
                <label for="rubro_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($rubro->rubro_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="rubro_estado" name="rubro_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="rubro_estado" name="rubro_estado">
                        @endif
                        <label class="custom-control-label" for="rubro_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection