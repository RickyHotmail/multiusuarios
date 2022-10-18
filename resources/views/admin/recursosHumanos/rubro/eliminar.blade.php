@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar este rubro?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('rubro.destroy', [$rubro->rubro_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("rubro") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$rubro->rubro_nombre}}</label>
                </div>
            </div>        
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tipo de Rubro</label>
                <div class="col-sm-10">
                    <label class="form-control">
                        @if($rubro->rubro_tipo == 1) EGRESOS @endif
                        @if($rubro->rubro_tipo == 2) INGRESOS @endif
                        @if($rubro->rubro_tipo == 3) PROVICIONES @endif
                        @if($rubro->rubro_tipo == 4) OTROS @endif
                    </label>
                </div>
            </div>    
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$rubro->rubro_descripcion}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($rubro->rubro_estado=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
        </div>    
        <!-- /.card-body -->    
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection