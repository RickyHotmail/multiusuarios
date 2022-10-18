@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('banco.update', [$banco->banco_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Banco</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- <button type="button" onclick='window.location = "{{ url("banco") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
              -->      
              <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>     
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idLista" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idLista" name="idLista" require>
                        @foreach($bancoLista as $bancoLista)
                            @if($banco->banco_lista_id == $bancoLista->banco_lista_id)
                                <option value="{{$bancoLista->banco_lista_id}}" selected>{{$bancoLista->banco_lista_nombre}}</option>
                            @else 
                                <option value="{{$bancoLista->banco_lista_id}}">{{$bancoLista->banco_lista_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idDireccion" class="col-sm-2 col-form-label">Direccion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idDireccion" name="idDireccion"  value="{{$banco->banco_direccion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTelefono" class="col-sm-2 col-form-label">Telefono</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idTelefono" name="idTelefono"  value="{{$banco->banco_telefono}}" required>
                </div>
            </div>    
            <div class="form-group row">
                <label for="idEmail" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idEmail" name="idEmail"  value="{{$banco->banco_email}}" required>
                </div>
            </div>            
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($banco->banco_estado=="1")
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