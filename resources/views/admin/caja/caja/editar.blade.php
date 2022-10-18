@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('caja.update', [$caja->caja_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Caja</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
            <!--     
                <button type="button" onclick='window.location = "{{ url("caja") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idCajaNombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCajaNombre" name="idCajaNombre"  value="{{$caja->caja_nombre}}" required>
                </div>
            </div>
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <div class="form-group row">
                <label for="idCuentaContable" class="col-sm-2 col-form-label">Cuenta Contable</label>
                <div class="col-sm-10">
                    <select class="form-control select2" style="width: 100%;" id="idCuentaContable" name="idCuentaContable" require>
                        @foreach($cuentas as $cuentas)
                            @if($cuentas->cuenta_id == $caja->cuenta_id)
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
                <label for="idSucursal" class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <select class="form-control select2" style="width: 100%;" id="idSucursal" name="idSucursal" require>
                        @foreach($sucursales as $sucursal)
                            @if($sucursal->sucursal_id == $caja->sucursal_id)
                                <option value="{{$sucursal->sucursal_id}}" selected>{{$sucursal->sucursal_nombre }}</option>
                            @else 
                                <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>                                       
            <div class="form-group row">
                <label for="idcajaEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($caja->caja_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="idcajaEstado" name="idcajaEstado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idcajaEstado" name="idcajaEstado">
                        @endif
                        <label class="custom-control-label" for="idcajaEstado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection