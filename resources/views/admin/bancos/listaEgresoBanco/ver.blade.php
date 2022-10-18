@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary col-sm-7">
    <div class="card-header">
        <h3 class="card-title">Egreso de Banco</h3>
 <!--
        <button onclick='window.location = "{{ url("listaEgresoBanco") }}";' class="btn btn-default btn-sm float-right"><i
                class="fa fa-undo"></i>&nbsp;Atras</button>
-->
        <button onclick="history.back()" class="btn btn-default btn-sm float-right"><i
                class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">  
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Numero</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoBanco->egreso_numero}}</label>
                </div>
            </div>          
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label">Fecha  </label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoBanco->egreso_fecha}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label">Banco</label>
                <div class="col-sm-10">
                    @if(isset($egresoBanco->transferencia))
                        <label class="form-control">{{ $egresoBanco->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</label>
                    @else
                        <label class="form-control">{{ $egresoBanco->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</label> 
                    @endif
                </div>
            </div>          
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <div class="form-group row">
                <label for="idContable" class="col-sm-2 col-form-label">Diario</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoBanco->diario->diario_codigo}}</label>
                </div>
            </div>
            @endif
            <div class="form-group row">
                <label for="idBeneficiario" class="col-sm-2 col-form-label">Beneficiario</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoBanco->egreso_beneficiario}}</label>
                </div>
            </div>            
            <div class="form-group row">
                <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                <div class="col-sm-10">
                    <label class="form-control">{{number_format($egresoBanco->egreso_valor,2)}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idMensaje" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoBanco->egreso_descripcion}}</label>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection