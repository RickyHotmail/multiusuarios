@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('cuenta.guardarCuentas', [$cuentaPadre->cuenta_id]) }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Agregar Cuenta</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--
                <button type="button"  onclick='window.location = "{{ url("cuenta") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="cuenta_nivel" class="col-sm-3 col-form-label">Nivel</label>
                <div class="col-sm-9">
                    <input type="hidden"id="cuenta_nivel" name="cuenta_nivel" value="{{$cuentaPadre->cuenta_nivel + 1 }}">
                    <label class="form-control">{{$cuentaPadre->cuenta_nivel + 1 }}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="cuenta_numero" class="col-sm-3 col-form-label">Numero</label>
                <div class="col-sm-3">
                    <input type="hidden" id="cuenta_padre" name="cuenta_padre" value="{{$cuentaPadre->cuenta_numero}}"/>
                    <label class="form-control">{{$cuentaPadre->cuenta_numero.'.'}}</label>
                </div>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="cuenta_numero" name="cuenta_numero" placeholder="Ej. 1.1.1.1" value="{{$secuencial}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="cuenta_nombre" class="col-sm-3 col-form-label">Nombre</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="cuenta_nombre" name="cuenta_nombre" placeholder="Nombre" required>
                </div>
            </div>                               
        </div>      
    </div>
</form>
@endsection