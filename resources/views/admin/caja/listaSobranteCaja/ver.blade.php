@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Ver Sobrante de Caja</h3>
         <!--  
        <button onclick='window.location = "{{ url("listaSobranteCaja") }}";' class="btn btn-default btn-sm float-right"><i
                class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">       
        <div class="card-body">
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sobranteCaja->sobrante_fecha}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idSucursal" class="col-sm-2 col-form-label">Numero</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sobranteCaja->sobrante_numero}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sobranteCaja->sobrante_observacion}}</label>
                </div>
            </div>
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <div class="form-group row">
                <label for="idContable" class="col-sm-2 col-form-label">Diario</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sobranteCaja->diario->diario_codigo}}</label>
                </div>
            </div>
            @endif
            <div class="form-group row">
                <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                <div class="col-sm-10">
                    <label class="form-control">${{ number_format($sobranteCaja->sobrante_monto,2)}}</label>
                </div>
            </div>
        </div> 
    </div>
</div>
@endsection