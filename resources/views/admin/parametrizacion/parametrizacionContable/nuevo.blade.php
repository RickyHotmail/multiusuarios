@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("parametrizacionContable") }}">
@csrf
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Parametrizacion Contable</h3>
        <button onclick='window.location = "{{ url("parametrizacionContable") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        <button type="submit" class="btn btn-success btn-sm float-right">Guardar</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <select class="form-control select2" style="width: 100%;" id="idsucursal" name="idsucursal" require>
                    @foreach($sucursales as $sucursal)
                                <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                    @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombre" name="idNombre"  value="" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Cuenta Contable</label>
                <div class="col-sm-10">
                    <select class="form-control select2" style="width: 100%;" id="idCuentaContable" name="idCuentaContable" require>
                        @foreach($cuentas as $cuenta)
                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                        @endforeach
                    </select>
                </div>  
            </div>            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Cuenta General</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">  
                            <input type="checkbox" class="custom-control-input" id="idGeneral" name="idGeneral">
                        <label class="custom-control-label" for="idGeneral"></label>
                    </div>
                </div>
            </div>        
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>                
            </div> 
           
        <!-- /.card-body -->        
        <!-- /.card-footer -->
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
</form>
@endsection