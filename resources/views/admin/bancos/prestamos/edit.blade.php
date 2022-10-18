@extends ('admin.layouts.admin')
@section('principal')

<form class="form-horizontal" method="POST" action="{{ route('prestamos.update', [$prestamos->prestamo_id]) }}">
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
                    <select class="custom-select select2" id="sucursal_id" name="sucursal_id" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}" @if($prestamos->sucursal_id==$sucursal->sucursal_id) selected @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Banco</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idBanco" name="idBanco" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($bancos as $banco)
                        <option value="{{$banco->banco_id}}"  @if($prestamos->banco_id==$banco->banco_id) selected @endif>{{$banco->bancoLista->banco_lista_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>  
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Fecha Inicio</label>
                <div class="col-sm-10">                        
                    <input type="date" class="form-control" id="idFechaini" name="idFechaini" value="{{$prestamos->prestamo_inicio}}" required>                   
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Fecha Finalizacion</label>
                <div class="col-sm-10">                        
                    <input type="date" class="form-control" id="idFechafin" name="idFechafin" value="{{$prestamos->prestamo_fin}}" required>                                    
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Monto</label>
                <div class="col-sm-10">                        
                    <input type="number" class="form-control" id="idMonto" name="idMonto"  step="any" placeholder="Monto" value="{{$prestamos->prestamo_monto}}" required>                      
                </div>
            </div>
            <div class="form-group row">
                <label for="ciudad_id" class="col-sm-2 col-form-label">Interes</label>
                <div class="col-sm-10">                        
                    <input type="number" class="form-control" id="idInteres" name="idInteres" step="any" placeholder="Interes" value="{{$prestamos->prestamo_interes}}" required>                 
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Plazo</label>
                <div class="col-sm-10">                        
                    <input type="number" class="form-control" id="idPlazo" name="idPlazo"  placeholder="Plazo" value="{{$prestamos->prestamo_plazo}}" required>                     
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Cuenta Debe</label>
                <div class="col-sm-10">                        
                    <select class="custom-select select2" id="idDebe" name="idDebe" require>
                        @foreach($cuentas as $cuenta)
                            <option value="{{$cuenta->cuenta_id}}" @if($prestamos->cuentadebe->cuenta_id==$cuenta->cuenta_id) selected @endif>{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                        @endforeach
                    </select>               
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Cuenta Haber</label>
                <div class="col-sm-10">                        
                    <select class="custom-select select2" id="idHaber" name="idHaber" require>
                        @foreach($cuentas as $cuenta)
                            <option value="{{$cuenta->cuenta_id}}" @if($prestamos->cuentahaber->cuenta_id==$cuenta->cuenta_id) selected @endif>{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                        @endforeach
                    </select>                
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Observacion</label>
                <div class="col-sm-10">                        
                    <textarea type="text" class="form-control" id="idDescripcion" name="idDescripcion" placeholder="Descripcion"  >{{$prestamos->prestamo_observacion}}</textarea>                   
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