@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('listaEgresoBanco.destroy', [$egresoBanco->egreso_id]) }}">
@method('DELETE')
@csrf
    <div class="card card-secondary col-sm-7">
        <div class="card-header">
            <h3 class="card-title">¿Esta seguro de eliminar este Egreso de Banco?</h3>
            <div class="float-right">
                <button type="button" onclick="anularCheque();" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <button type="submit"  id="IDeliminar" name="eliminar" class="invisible"><i class="fa fa-trash"></i></button>
                <!--
                <button type="button" onclick='window.location = "{{ url("listaEgresoBanco") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->   
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm "><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="card-body">  
                <div class="form-group row">
                    <label for="idTipo" class="col-sm-2 col-form-label">Numero</label>
                    <div class="col-sm-10">
                        <input type="hidden" id="chequeNumero" name="chequeNumero" @if(isset($egresoBanco->cheque->cheque_id)) value="{{$egresoBanco->cheque->cheque_numero}}" @else value='0' @endif/>
                        <input type="hidden" id="anularChequeID" name="anularChequeID" value="no"/>
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
</form>
<script type="text/javascript">
    function anularCheque(){
        if(document.getElementById("chequeNumero").value != '0'){
            bootbox.confirm({
                message: "¿Desea anular el cheque No. "+document.getElementById("chequeNumero").value+"?",
                buttons: {
                    confirm: {
                        label: 'SI',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'NO',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if(result == true){
                        document.getElementById("anularChequeID").value = 'si';
                    }
                    bootbox.confirm({
                        message: "¿Estás seguro de eliminar esto egreso?",
                        buttons: {
                            confirm: {
                                label: 'SI',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: 'NO',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if(result == true){
                                $("#IDeliminar").click();
                            }
                        }
                    });
                }
            });
        }else{
            $("#IDeliminar").click();
        }
    }
</script>
@endsection