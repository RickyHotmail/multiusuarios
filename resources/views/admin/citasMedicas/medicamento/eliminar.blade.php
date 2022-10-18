@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar el medicamento?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('medicamento.destroy', [$medicamento->medicamento_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <button type="button" onclick='window.location = "{{ url("medicamento") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre del medicamento</label>
                <div class="col-sm-10">
                    <label class="form-control">@if(isset($medicamento->producto->producto_nombre)) {{$medicamento->producto->producto_nombre}} @endif</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Composicion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$medicamento->medicamento_composicion}}</label>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Indicacion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$medicamento->medicamento_indicacion}}</label>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Contraindicacion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$medicamento->medicamento_contraindicacion}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="usuario_id" class="col-sm-2 col-form-label">Tipo de medicamento</label>
                <div class="col-sm-10">
                    @foreach($tipoMedicamentos as $tipoMedicamento)
                        @if($tipoMedicamento->tipo_id == $medicamento->tipo_id)
                            <label class="form-control">{{$tipoMedicamento->tipo_nombre}}</label>                     
                        @endif
                    @endforeach
                </div>
            </div>                  
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($medicamento->medicamento_estado=="1")
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