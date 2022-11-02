@extends ('admin.layouts.admin')
@section('principal')

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Seleccione el modelo de Impresión</h3>
        <div class="float-right">
            <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
            <a class="btn btn-success btn-sm" href="{{ url("excelProvincia") }}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{url('configurarImpresion')}}" method="POST">
            @csrf

            <select class="custom-select mb-3" id="idTipo" name="idTipo" required >
                <option value=1 @if(isset($impresion)) @if($impresion->parametrizacioni_tipo==1)selected @endif @else selected @endif> Modelo Estandard (RIDE)</option>
                <option value=2 @if(isset($impresion)) @if($impresion->parametrizacioni_tipo==2)selected @endif @endif>Punto de Venta (Recibo)</option>
                <option value=3 @if(isset($impresion)) @if($impresion->parametrizacioni_tipo==3)selected @endif @endif>Todos (RIDE | Recibo)</option>
            </select>

            <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Guardar</button>
        </form>
    </div>
</div>
@endsection