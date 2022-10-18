@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('grupoActivo.update', [$grupoActivo->grupo_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Grupo</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
              <!--  <button type="button" onclick='window.location = "{{ url("grupoActivo") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            -->  
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>     
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idSucursal" class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <select class="form-control select2" style="width: 100%;" id="idSucursal" name="idSucursal" required>
                        @foreach($sucursales as $sucursal)
                            @if($sucursal->sucursal_id == $grupoActivo->sucursal_id)
                                <option value="{{$sucursal->sucursal_id}}" selected>{{$sucursal->sucursal_nombre }}</option>
                            @else 
                                <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div> 
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombre" name="idNombre"  value="{{$grupoActivo->grupo_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idPorcentaje" class="col-sm-2 col-form-label">Porcentaje</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idPorcentaje" name="idPorcentaje"  value="{{$grupoActivo->grupo_porcentaje}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idDepreciacion" class="col-sm-2 col-form-label">Cuenta Depreciacion</label>
                <div class="col-sm-10">
                    <select class="form-control select2" style="width: 100%;" id="idDepreciacion" name="idDepreciacion" require>
                        @foreach($cuentas as $cuenta)
                            @if($cuenta->cuenta_id == $grupoActivo->cuenta_depreciacion)
                                <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                            @else 
                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>
            <div class="form-group row">
                <label for="idGasto" class="col-sm-2 col-form-label">Cuenta Gasto</label>
                <div class="col-sm-10">
                    <select class="form-control select2" style="width: 100%;" id="idGasto" name="idGasto" require>
                        @foreach($cuentas as $cuenta)
                            @if($cuenta->cuenta_id == $grupoActivo->cuenta_gasto)
                                <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                            @else 
                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>                                                  
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($grupoActivo->grupo_estado=="1")
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