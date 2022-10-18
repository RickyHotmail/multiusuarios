@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary col-sm-7">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar este Egreso de Caja?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('listaEgresoCaja.destroy', [$egresoCaja->egreso_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                 <!--  
                <button type="button" onclick='window.location = "{{ url("listaEgresoCaja") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="card-body">       
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Numero</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoCaja->egreso_numero}}</label>
                </div>
            </div>    
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoCaja->egreso_fecha}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Tipo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoCaja->egreso_tipo}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Caja</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoCaja->arqueo->caja->caja_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idMensaje" class="col-sm-2 col-form-label">Motivo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoCaja->movimiento->tipo_nombre}}</label>
                </div>
            </div>
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <div class="form-group row">
                <label for="idContable" class="col-sm-2 col-form-label">Diario</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoCaja->diario->diario_codigo}}</label>
                </div>
            </div>
            @endif
            <div class="form-group row">
                <label for="idBeneficiario" class="col-sm-2 col-form-label">Beneficiario</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoCaja->egreso_beneficiario}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                <div class="col-sm-10">
                    <label class="form-control">{{number_format($egresoCaja->egreso_valor,2)}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idMensaje" class="col-sm-2 col-form-label">Comentario</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$egresoCaja->egreso_descripcion}}</label>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection