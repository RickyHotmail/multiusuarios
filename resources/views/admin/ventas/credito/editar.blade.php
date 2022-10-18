@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('credito.update', [$credito->credito_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Credito</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--
                <button type="button" onclick='window.location = "{{ url("credito") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->      
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="credito_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="credito_nombre" name="credito_nombre" placeholder="Nombre" value="{{$credito->credito_nombre}}" required>
                </div>
            </div>                
            <div class="form-group row">
                <label for="credito_descripcion" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="credito_descripcion" name="credito_descripcion" placeholder="Ingrese aqui una descripcion" value="{{$credito->credito_descripcion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="credito_monto" class="col-sm-2 col-form-label">Monto</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="credito_monto" name="credito_monto" placeholder="00.0000" value="{{$credito->credito_monto}}" step="00.0001" required>
                </div>
            </div>                                                        
            <div class="form-group row">
                <label for="credito_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($credito->credito_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="credito_estado" name="credito_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="credito_estado" name="credito_estado">
                        @endif
                        <label class="custom-control-label" for="credito_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection