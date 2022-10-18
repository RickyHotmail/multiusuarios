@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('parametrizacionContable.update', [$parametrizacionContable->parametrizacion_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Parametrizacion Contable</h3>
            <div class="float-right">
                    <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                    <button type="button" onclick='window.location = "{{ url("parametrizacionContable") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombre" name="idNombre"  value="{{$parametrizacionContable->sucursal->sucursal_nombre}}" required readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombre" name="idNombre"  value="{{$parametrizacionContable->parametrizacion_nombre}}" required readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCuentaContable" class="col-sm-2 col-form-label">Cuenta Contable</label>
                <div class="col-sm-10">
                    <select class="form-control select2" style="width: 100%;" id="idCuentaContable" name="idCuentaContable" require>
                        @foreach($cuentas as $cuentas)
                            @if($cuentas->cuenta_id == $parametrizacionContable->cuenta_id)
                                <option value="{{$cuentas->cuenta_id}}" selected>{{$cuentas->cuenta_numero.'  - '.$cuentas->cuenta_nombre}}</option>
                            @else 
                                <option value="{{$cuentas->cuenta_id}}">{{$cuentas->cuenta_numero.'  - '.$cuentas->cuenta_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>   
            <div class="form-group row">
                <label for="idGeneral" class="col-sm-2 col-form-label">Usar una cuenta general</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">  
                        @if($parametrizacionContable->parametrizacion_cuenta_general=="1")    
                            <input type="checkbox" class="custom-control-input" id="idGeneral" name="idGeneral" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idGeneral" name="idGeneral">
                        @endif
                        <label class="custom-control-label" for="idGeneral"></label>
                    </div>
                </div>
            </div>                           
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($parametrizacionContable->parametrizacion_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>                
            </div>  
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</form>
@endsection