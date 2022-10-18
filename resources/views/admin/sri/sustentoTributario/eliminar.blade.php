@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar el sustento tributario?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('sustentoTributario.destroy', [$sustentoTributario->sustento_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                 <!--
                <button type="button" onclick='window.location = "{{ url("sustentoTributario") }}";'  class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
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
                    <label class="form-control">{{$sustentoTributario->sustento_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sustentoTributario->sustento_codigo}}</label>
                </div>
            </div>          
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Crédito</label>
                <div class="col-sm-10">
                    @if($sustentoTributario->sustento_credito =="1")
                    <label class="form-control">Con Crédito</label>
                    @elseif($sustentoTributario->sustento_credito == "2")
                    <label class="form-control">Sin Crédito</label>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Venta 12%</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sustentoTributario->sustento_venta12}}</label>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Venta 0%</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sustentoTributario->sustento_venta0}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Compra 12%</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sustentoTributario->sustento_compra12}}</label>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Compra 0%</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sustentoTributario->sustento_compra0}}</label>
                </div>
            </div>             
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($sustentoTributario->sustento_estado=="1")
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