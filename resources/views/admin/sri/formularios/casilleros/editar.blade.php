@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('casilleroTributario.update', [$casillero->casillero_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Casillero</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
            <!--     
                <button type="button" onclick='window.location = "{{ url("caja") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idCodigoCasillero" class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCodigoCasillero" name="idCodigoCasillero"  value="{{$casillero->casillero_codigo}}" required>
                </div>
            </div>   
            <div class="form-group row">
                <label for="idCasilleroDescripcion" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCasilleroDescripcion" name="idCasilleroDescripcion"  value="{{$casillero->casillero_descripcion}}" required>
                </div>
            </div>           
            <div class="form-group row">
                <label for="idCasilleroTipo" class="col-sm-2 col-form-label">Tipo</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="idCasilleroTipo" name="idCasilleroTipo" required>
                            <option value="COMPRAS 0%" @if($casillero->casillero_tipo=="COMPRAS 0%") selected @endif>Compras 0%</option>
                            <option value="COMPRAS 12%" @if($casillero->casillero_tipo=="COMPRAS 12%") selected @endif>Compras 12%</option>                           
                            <option value="VENTAS 0%" @if($casillero->casillero_tipo=="VENTAS 0%") selected @endif>Ventas 0%</option>    
                            <option value="VENTAS 12%" @if($casillero->casillero_tipo=="VENTAS 12%") selected @endif>Ventas 12%</option> 
                        </select>
                    </div>
                </div>                              
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($casillero->casillero_estado=="1")
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