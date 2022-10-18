@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('puntoEmision.update', [$puntoEmision->punto_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Punto de Emision</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--     
                <button type="button" onclick='window.location = "{{ url("puntoEmision") }}";'  class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idSucursal" class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idSucursal" name="idSucursal" style="pointer-events : none; " require>
                        @foreach($sucursales as $sucursal)
                            @if($puntoEmision->sucursal_id == $sucursal->sucursal_id)
                                <option value="{{$sucursal->sucursal_id}}" selected>{{$sucursal->sucursal_codigo}} - {{$sucursal->sucursal_nombre}}</option>
                            @else 
                                <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_codigo}} - {{$sucursal->sucursal_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div> 
            <div class="form-group row">
                <label for="idSerie" class="col-sm-2 col-form-label">Código</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idSerie" name="idSerie" placeholder="Nombre" value="{{$puntoEmision->punto_serie}}" required readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCodigo" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idDescripcion" name="idDescripcion" value="{{$puntoEmision->punto_descripcion}}" required>
                </div>
            </div>               
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($puntoEmision->punto_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>                
            </div>
        </div>            
    </div>
</form>
@endsection