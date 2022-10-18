@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('sustentoTributario.update', [$sustentoTributario->sustento_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Sustento Tributario</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--
                <button type="button" onclick='window.location = "{{ url("sustentoTributario") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->      
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
            </div>
        </div>
        <div class="card-body">                
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombre" name="idNombre" value="{{$sustentoTributario->sustento_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCodigo" class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCodigo" name="idCodigo" value="{{$sustentoTributario->sustento_codigo}}" required>
                </div>
            </div>   
            <div class="form-group row">
                <label for="idCredito" class="col-sm-2 col-form-label">Crédito</label>
                <div class="col-sm-10">
                    <select id="idCredito" name="idCredito" class="form-control show-tick " data-live-search="true" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        <option value="1" @if($sustentoTributario->sustento_credito == "1") selected @endif>Con Crédito</option>
                        <option value="2" @if($sustentoTributario->sustento_credito == "2") selected @endif>Sin Crédito</option>
                    </select>
                </div>
            </div>  
            <div class="form-group row">
                <label for="idVenta12" class="col-sm-2 col-form-label">Venta 12%</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idVenta12" name="idVenta12" value="{{$sustentoTributario->sustento_venta12}}" required>
                </div>
            </div> 
            <div class="form-group row">
                <label for="idVenta0" class="col-sm-2 col-form-label">Venta 0%</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idVenta0" name="idVenta0" value="{{$sustentoTributario->sustento_venta0}}" required>
                </div>
            </div>    
            <div class="form-group row">
                <label for="idCompra12" class="col-sm-2 col-form-label">Compra 12%</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCompra12" name="idCompra12" value="{{$sustentoTributario->sustento_compra12}}" required>
                </div>
            </div>   
            <div class="form-group row">
                <label for="idCompra0" class="col-sm-2 col-form-label">Compra 0%</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCompra0" name="idCompra0" value="{{$sustentoTributario->sustento_compra0}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($sustentoTributario->sustento_estado=="1")
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