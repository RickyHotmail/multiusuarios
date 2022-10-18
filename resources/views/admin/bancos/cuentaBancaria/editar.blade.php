@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('cuentaBancaria.update', [$cuentaBancaria->cuenta_bancaria_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Cuenta Bancaria</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--<button type="button" onclick='window.location = "{{ url("cuentaBancaria") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                 --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>     
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idBanco" class="col-sm-2 col-form-label">Banco</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idBanco" name="idBanco" require>
                        @foreach($bancos as $banco)
                            @if($banco->banco_id == $cuentaBancaria->banco_id)
                                <option value="{{$banco->banco_id}}" selected>{{$banco->bancoLista->banco_lista_nombre}}</option>
                            @else 
                                <option value="{{$banco->banco_id}}">{{$banco->bancoLista->banco_lista_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div> 
            <div class="form-group row">
                <label for="idNumero" class="col-sm-2 col-form-label"># de Cuenta</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNumero" name="idNumero" value="{{$cuentaBancaria->cuenta_bancaria_numero}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Cuenta</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="idTipo" name="idTipo"required>
                        <option value="1" @if($cuentaBancaria->cuenta_bancaria_tipo == '1') selected @endif>Ahorro</option>
                        <option value="2" @if($cuentaBancaria->cuenta_bancaria_tipo == '2') selected @endif>Corriente</option>                           
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idInicial" class="col-sm-2 col-form-label">Saldo Inicial</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idInicial" name="idInicial" value="{{$cuentaBancaria->cuenta_bancaria_saldo_inicial}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idJefe" class="col-sm-2 col-form-label">Jefe de Cuenta</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idJefe" name="idJefe" value="{{$cuentaBancaria->cuenta_bancaria_jefe}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCuenta" class="col-sm-2 col-form-label">Cuenta</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idCuenta" name="idCuenta" require>
                        @foreach($cuentas as $cuenta)
                            @if($cuenta->cuenta_id == $cuentaBancaria->cuenta_id)
                                <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                            @else 
                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>                                      
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($cuentaBancaria->cuenta_bancaria_estado=="1")
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