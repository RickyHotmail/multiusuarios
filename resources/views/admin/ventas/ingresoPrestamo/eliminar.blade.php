@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary col-sm-7">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar este Ingreso de Producto?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST"
                action="{{ route('ingresoPrestamo.destroy', [$ingreso->ingreso_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                 <!--  
                <button type="button" onclick='window.location = "{{ url("listaingreso") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
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
                    <label class="form-control">{{$ingreso->ingreso_numero}}</label>
                </div>
            </div>          
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label">Fecha  </label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingreso->ingreso_fecha}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Cliente</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingreso->cliente->cliente_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Producto</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingreso->producto->producto_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Transportista</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingreso->transportista->transportista_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Placa</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingreso->ingreso_placa}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idValor" class="col-sm-2 col-form-label">Cantidad</label>
                <div class="col-sm-10">
                    <label class="form-control">{{number_format($ingreso->ingreso_valor,2)}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idMensaje" class="col-sm-2 col-form-label">Comentario</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$ingreso->ingreso_descripcion}}</label>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection