@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary col-sm-7">
    <div class="card-header">
        <h3 class="card-title">Ingreso de Banco</h3>
<!--
        <button onclick='window.location = "{{ url("listaIngresoBanco") }}";' class="btn btn-default btn-sm float-right"><i
                class="fa fa-undo"></i>&nbsp;Atras</button>
                -->   
        <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>          
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">  
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Numero</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingresoBanco->ingreso_numero}}</label>
                </div>
            </div>          
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label">Fecha  </label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingresoBanco->ingreso_fecha}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label">Movimiento  </label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingresoBanco->tipoMovimientoBanco->tipo_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label">Tipo  </label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingresoBanco->deposito->deposito_tipo}}</label>
                </div>
            </div>   
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label">Banco  </label>
                <div class="col-sm-10">
                    <label class="form-control">{{ $ingresoBanco->deposito->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label"># de Cuenta  </label>
                <div class="col-sm-10">
                    <label class="form-control">{{ $ingresoBanco->deposito->cuentaBancaria->cuenta_bancaria_numero}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label"># Deposito  </label>
                <div class="col-sm-10">
                    <label class="form-control">{{ $ingresoBanco->deposito->deposito_numero}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                <div class="col-sm-10">
                    <label class="form-control">{{number_format($ingresoBanco->ingreso_valor,2)}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idBeneficiario" class="col-sm-2 col-form-label">Depositante</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingresoBanco->ingreso_beneficiario}}</label>
                </div>
            </div>            
           
            <div class="form-group row">
                <label for="idMensaje" class="col-sm-2 col-form-label">Motivo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingresoBanco->ingreso_descripcion}}</label>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection