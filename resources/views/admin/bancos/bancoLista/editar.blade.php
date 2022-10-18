@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('bancoLista.update', [$bancoList->banco_lista_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Banco Lista</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
               <!--  <button type="button" onclick='window.location = "{{ url("bancoLista") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->   
               <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>     
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="banco_lista_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="banco_lista_nombre" name="banco_lista_nombre" placeholder="Banco" value="{{$bancoList->banco_lista_nombre}}" required>
                </div>
            </div>                                       
            <div class="form-group row">
                <label for="banco_lista_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($bancoList->banco_lista_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="banco_lista_estado" name="banco_lista_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="banco_lista_estado" name="banco_lista_estado">
                        @endif
                        <label class="custom-control-label" for="banco_lista_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection