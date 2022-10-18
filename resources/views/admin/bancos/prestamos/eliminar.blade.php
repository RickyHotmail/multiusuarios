@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar esta Prestamo?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('prestamos.destroy', [$prestamos->prestamo_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("prestamos") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
        <div class="form-group row">
                <label class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$prestamos->sucursal->sucursal_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Banco</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$prestamos->banco->bancoLista->banco_lista_nombre }}</label>
                </div>
            </div>  
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Fecha Inicio</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$prestamos->prestamo_inicio}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Fecha Finalizacion</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$prestamos->prestamo_fin}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Monto</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{ $prestamos->prestamo_monto }}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="ciudad_id" class="col-sm-2 col-form-label">Interes</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{ $prestamos->prestamo_interes }}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Plazo</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{ $prestamos->prestamo_plazo }}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Cuenta Debe</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{ $prestamos->cuentadebe->cuenta_numero.' -  '.$prestamos->cuentadebe->cuenta_nombre}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Cuenta Haber</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{ $prestamos->cuentahaber->cuenta_numero.' -  '.$prestamos->cuentahaber->cuenta_nombre}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Observacion</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{ $prestamos->amortizacion_observacion}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($prestamos->prestamo_estado=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>            
        </div>
        <!-- /.card-body -->    
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection