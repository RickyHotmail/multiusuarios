@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('impuestoRentaRol.update', [$impuesto->impuestos_id]) }}">
@method('PUT')
@csrf  
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Impuesto Renta Rol</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("impuestoRentaRol") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">                      
            <div class="form-group row">
                <label for="idFraccion" class="col-sm-3 col-form-label">Fraccion Basica</label>
                <div class="col-sm-9">
                <input type="number" class="form-control" id="idFraccion" name="idFraccion" value="{{$impuesto->impuesto_fraccion_basica}}"  step="0.01" placeholder="Fraccion Basica" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idExceso" class="col-sm-3 col-form-label">Exceso Hasta</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="idExceso" name="idExceso" value="{{$impuesto->impuesto_exceso_hasta}}"  step="0.01" placeholder="Exceso Hasta" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idExcede" class="col-sm-3 col-form-label">Impuesto sobre la Fraccion Basica</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="idExcede" name="idExcede" value="{{$impuesto->impuesto_fraccion_excede}}"  step="0.01" placeholder="Impuesto sobre la Fraccion Basica" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idSobre" class="col-sm-3 col-form-label">Impuesto sobre la Fraccion Excedente</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="idSobre" name="idSobre" value="{{$impuesto->impuesto_sobre_fraccion}}" min="0" max="100"  step="0.01" placeholder="Impuesto sobre la Fraccion Excedente" required>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($impuesto->impuesto_estado=="1")
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