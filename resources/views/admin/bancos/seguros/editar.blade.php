@extends ('admin.layouts.admin')
@section('principal')

<form class="form-horizontal" method="POST" action="{{ route('amortizacion.update', [$seguro->amortizacion_id]) }}">
@method('PUT')
@csrf

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Editar Prestamos</h3>
        <div class="float-right"> 
                @csrf
                <!-- 
                <button type="button" onclick='window.location = "{{ url("prestamos") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
        <div class="form-group row">
                <label class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$seguro->sucursal->sucursal_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Proveedor</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$seguro->transaccionCompra->proveedor->proveedor_nombre }}</label>
                </div>
            </div>  
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Factura</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$seguro->transaccionCompra->transaccion_numero}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$seguro->amortizacion_fecha}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Valor</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{ $seguro->amortizacion_total }}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Cuenta Debe</label>
                <div class="col-sm-10">                        
                    <select class="custom-select select2" id="idCuenta" name="idCuenta" require>
                        @foreach($cuentas as $cuenta)
                            <option value="{{$cuenta->cuenta_id}}" @if($seguro->cuentadebe->cuenta_id==$cuenta->cuenta_id) selected @endif>{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                        @endforeach
                    </select>               
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Observacion</label>
                <div class="col-sm-10">                        
                    <textarea type="text" class="form-control" id="idDescripcion" name="idDescripcion" placeholder="Descripcion"  >{{$seguro->amortizacion_observacion}}</textarea>                   
                </div>
            </div>
                     
        </div>
        <!-- /.card-body -->    
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
</form>
@endsection