@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar esta centro de consumo?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('centroConsumo.destroy', [$centroCon->centro_consumo_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                 <!--     
                <button type="button" onclick='window.location = "{{ url("centroConsumo") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
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
                    <label class="form-control">{{$centroCon->centro_consumo_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Fecha Ingreso</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$centroCon->centro_consumo_fecha_ingreso}}</label>
                </div>
            </div>         
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$centroCon->centro_consumo_descripcion}}</label>
                </div>
            </div>    
            <div class="form-group row">
            <label for="idTipo" class="col-sm-2 col-form-label">Tipo Centro Consumo</label>
            <div class="col-sm-10">
                @if(isset($centroCon->sustento->sustento_codigo))
                    <label class="form-control">{{$centroCon->sustento->sustento_codigo.'-'.$centroCon->sustento->sustento_nombre}}</label>
                @endif                
            </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($centroCon->centro_consumo_estado=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div> 
        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection