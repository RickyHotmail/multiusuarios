@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Tarjeta de Credito</h3>
         <!--    
        <button onclick='window.location = "{{ url("tarjetaCredito") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Nombre de la Tarjeta de Credito</label>
                <div class="col-sm-9">
                    <label class="form-control">{{$tarjetaCredito->tarjeta_nombre}}</label>
                </div>
            </div>  
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Cuenta</label>
                <div class="col-sm-9">
                    @foreach($cuentas as $cuenta)
                        @if($cuenta->cuenta_id == $tarjetaCredito->cuenta_id)
                            <label class="form-control">{{$cuenta->cuenta_nombre}}</label>
                        @endif
                    @endforeach
                </div>
            </div>                   
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Estado</label>
                <div class="col-sm-9">
                    @if($tarjetaCredito->tarjeta_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection