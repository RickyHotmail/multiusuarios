@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar el documento Anulado?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('anularRetencion.destroy', [$retencionCompra->retencion_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <button type="button" onclick='window.location = "{{ url("anularRetencion") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$retencionCompra->retencion_fecha}}</label>
                </div>
            </div>                  
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Numero</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$retencionCompra->retencion_numero}}</label>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Autorizacion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$retencionCompra->retencion_autorizacion}}</label>
                </div>
            </div>
            @if(isset($retencionCompra->dopcumentoanulado->documento_anulado_fecha))
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Fecha Anulacion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$retencionCompra->dopcumentoanulado->documento_anulado_fecha}}</label>
                </div>
            </div>
            @endif            
        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection