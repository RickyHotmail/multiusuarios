@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('tarjetaCredito.update', [$tarjetaCredito->tarjeta_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Tarjeta de Credito</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--    
                <button type="button" onclick='window.location = "{{ url("tarjetaCredito") }}";'  class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Nombre de la tarjeta de credit</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" value="{{$tarjetaCredito->tarjeta_nombre}}" required>
                </div>
            </div> 
            <div class="form-group row">
                <label for="idCuenta" class="col-sm-2 col-form-label">Cuenta</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idCuenta" name="idCuenta" required>
                    <option value="">--Seleccione una opcion--</option>
                        @foreach($cuentas as $cuenta)
                            @if($cuenta->cuenta_id == $tarjetaCredito->cuenta_id)
                                <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_nombre}}</option>
                            @endif
                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>            
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($tarjetaCredito->tarjeta_estado == "1")
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