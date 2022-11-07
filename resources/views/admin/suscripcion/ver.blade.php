@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Suscripcion</h3>
        @if('caducado')
            <a href="{{url('/pagos')}}" class="btn btn-primary btn-sm float-right"><i class="fa fa-cart-plus"></i>&nbsp;Hacer un Pago</a>
        @endif
    </div>
    <div class="card-body">
        <div class="card-body">
            <h2>Datos de la Empresa</h2>
            <hr>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Ruc</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$suscripcion->empresa->empresa_ruc}}</label>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre Comercial</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$suscripcion->empresa->empresa_nombreComercial}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Ciudad</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$suscripcion->empresa->empresa_ciudad}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Dirección</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$suscripcion->empresa->empresa_direccion}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Teléfono</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$suscripcion->empresa->empresa_telefono}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$suscripcion->empresa->empresa_email}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($suscripcion->empresa->empresa_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            <br>
            <h2>Suscripción</h2>
            <hr>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Plan</label>
                <div class="col-sm-10">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">{{ $suscripcion->plan->plan_nombre }}</label>
                    </div>
                </div>
            </div>


            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if(!$caducado)
                        <i class="fa fa-check-circle neo-verde"></i>
                        <label class="col-sm-4 col-form-label">Tu suscripción terminará en {{$dias}} @if($dias>1) días @else día @endif</label>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                        <label class="col-sm-4 col-form-label">Tu suscripción caducó hace {{$dias}} @if($dias>1) días @else día @endif</label>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection