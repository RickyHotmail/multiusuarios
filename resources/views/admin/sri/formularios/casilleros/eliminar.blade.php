@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar este Casillero?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('casilleroTributario.destroy', [$casillero->casillero_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
            <!--     
                <button type="button" onclick='window.location = "{{ url("caja") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
            <div class="form-group row">
                <label for="idCodigoCasillero" class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$casillero->casillero_codigo}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCajaNombre" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                <label class="form-control">{{$casillero->casillero_descripcion}}</label>
                </div>
            </div> 
            <div class="form-group row">
                <label for="idCajaNombre" class="col-sm-2 col-form-label">Tipo</label>
                <div class="col-sm-10">
                <label class="form-control">{{$casillero->casillero_tipo}}</label>
                </div>
            </div>            
            <div class="form-group row">
                <label for="idcajaEstado" class="col-sm-2 col-form-label">Estado</label>
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
        <!-- /.card-body -->    
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection