@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar el Impuesto Renta Rol?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('impuestoRentaRol.destroy', [$impuesto->impuestos_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <button type="button" onclick='window.location = "{{ url("impuestoRentaRol") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <div class="card-body">                        
        <div class="form-group row">
            <label for="idFraccion" class="col-sm-3 col-form-label">Fraccion Basica</label>
            <div class="col-sm-9">
               <label class="form-control">{{$impuesto->impuesto_fraccion_basica}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idExceso" class="col-sm-3 col-form-label">Exceso Hasta</label>
            <div class="col-sm-9">
                <label class="form-control">{{$impuesto->impuesto_exceso_hasta}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idExcede" class="col-sm-3 col-form-label">Impuesto sobre la Fraccion Basica</label>
            <div class="col-sm-9">
                <label class="form-control">{{$impuesto->impuesto_fraccion_excede}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idSobre" class="col-sm-3 col-form-label">Impuesto sobre la Fraccion Excedente</label>
            <div class="col-sm-9">
                <label class="form-control">{{$impuesto->impuesto_sobre_fraccion}}</label>
            </div>
        </div>   
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Estado</label>
            <div class="col-sm-9">
                @if($impuesto->impuesto_estado=="1")
                <i class="fa fa-check-circle neo-verde"></i>
                @else
                <i class="fa fa-times-circle neo-rojo"></i>
                @endif
            </div>
        </div>
    </div>       
</div>
    <!-- /.card-body -->

<!-- /.card -->
@endsection