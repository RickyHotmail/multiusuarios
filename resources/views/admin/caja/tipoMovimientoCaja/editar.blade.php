@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('tipoMovimientoCaja.update', [$tipoMovimientoCaja->tipo_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Tipo de Movimiento de Caja</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--  
                <button type="button" onclick='window.location = "{{ url("tipoMovimientoCaja") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">   
            <div class="form-group row">
                <label for="idSucursal" class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idSucursal" name="idSucursal" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}" @if($sucursal->sucursal_id == $tipoMovimientoCaja->sucursal_id) selected @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>          
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombre" name="idNombre"  value="{{$tipoMovimientoCaja->tipo_nombre}}" required>
                </div>
            </div>
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <div class="form-group row">
                <label for="idCuenta" class="col-sm-2 col-form-label">Cuenta</label>
                <div class="col-sm-10">
                    <select class="form-control select2" style="width: 100%;" id="idCuenta" name="idCuenta" require>
                        @foreach($cuentas as $cuentas)
                            @if($cuentas->cuenta_id == $tipoMovimientoCaja->cuenta_id)
                                <option value="{{$cuentas->cuenta_id}}" selected>{{$cuentas->cuenta_numero.'  - '.$cuentas->cuenta_nombre}}</option>
                            @else 
                                <option value="{{$cuentas->cuenta_id}}">{{$cuentas->cuenta_numero.'  - '.$cuentas->cuenta_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>  
            @endif              
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($tipoMovimientoCaja->tipo_estado=="1")
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