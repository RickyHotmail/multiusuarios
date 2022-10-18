@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar este examen?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('examen.destroy', [$examen->examen_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <button type="button" onclick='window.location = "{{ url("examen") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
           
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tipo de Examen</label>
                <div class="col-sm-10">
                    @foreach($tipoExamenes as $tipoExamen)
                        @if($tipoExamen->tipo_id == $examen->tipo_id)
                            <label class="form-control">{{$tipoExamen->tipo_nombre}}</label>                     
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre del Examen</label>
                <div class="col-sm-10">
                    @foreach($producto as $productos)
                            @if($productos->producto_id == $examen->producto_id)
                            <label class="form-control">{{$productos->producto_nombre}}</label>                     
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($examen->examen_estado=="1")
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