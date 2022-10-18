@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('rangoCheque.update', [$rangoCheque->rango_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Rango de Documento</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
            <!-- 
                <button type="button" onclick='window.location = "{{ url("rangoCheque") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">      
            <div class="form-group row">
                <label for="idBancaria" class="col-sm-2 col-form-label">Cuenta Bancaria</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idBancaria" name="idBancaria" require>
                        @foreach($cuentaBancarias as $cuentaBancaria)
                            @if($cuentaBancaria->cuenta_bancaria_id == $rangoCheque->cuenta_bancaria_id)
                                <option value="{{$cuentaBancaria->cuenta_bancaria_id}}" selected>{{$cuentaBancaria->cuenta_bancaria_numero.'  -  '.$cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</option>
                            @else 
                                <option value="{{$cuentaBancaria->cuenta_bancaria_id}}">{{$cuentaBancaria->cuenta_bancaria_numero.'  -  '.$cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>        
            <div class="form-group row">
                <label for="idRinicio" class="col-sm-2 col-form-label">Rango de Inicio</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idRinicio" name="idRinicio" placeholder="" value="{{$rangoCheque->rango_inicio}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idRfin" class="col-sm-2 col-form-label">Rango de fin</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idRfin" name="idRfin" placeholder="Av. Ejemplo" value="{{$rangoCheque->rango_fin}}" required>
                </div>
            </div>                                                                                 
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($rangoCheque->rango_estado=="1")
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