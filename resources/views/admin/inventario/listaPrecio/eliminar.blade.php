@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar esta lista de precios?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('listaPrecio.destroy', [$lista->lista_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <!--   
                <button type="button" onclick='window.location = "{{ url("listaPrecio") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <label class="col-sm-2 col-form-label">Nombre</label>
            <div class="col-sm-10">
                <label class="form-control">{{$lista->lista_nombre}}</label>
            </div>
        </div>   
        <div class="row">
            <label class="col-sm-2 col-form-label">Estado</label>
            <div class="col-sm-2">
                @if($lista->lista_estado=="1")
                    <i class="fa fa-check-circle neo-verde" style="padding-top: 12px; padding-bottom: 20px;"></i>
                @else
                    <i class="fa fa-times-circle neo-rojo" style="padding-top: 12px; padding-bottom: 20px;"></i>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- /.card -->
@endsection