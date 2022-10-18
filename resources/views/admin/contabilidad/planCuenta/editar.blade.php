@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('cuenta.update', [$cuenta->cuenta_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Cuenta</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--
                <button type="button" onclick='window.location = "{{ url("cuenta") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="cuenta_nivel" class="col-sm-3 col-form-label">Nivel</label>
                <div class="col-sm-9">
                    <label class="form-control">{{$cuenta->cuenta_nivel}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="cuenta_numero" class="col-sm-3 col-form-label">Numero</label>
                <div class="col-sm-9">
                    <label class="form-control">{{$cuenta->cuenta_numero}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label for="cuenta_nombre" class="col-sm-3 col-form-label">Nombre</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="cuenta_nombre" name="cuenta_nombre" placeholder="Nombre" value="{{$cuenta->cuenta_nombre}}" required>
                </div>
            </div> 
            <div class="form-group row">
                <label for="cuenta_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($cuenta->cuenta_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="cuenta_estado" name="cuenta_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="cuenta_estado" name="cuenta_estado">
                        @endif
                        <label class="custom-control-label" for="cuenta_estado"></label>
                    </div>
                </div>                
            </div>                              
        </div>            
    </div>
</form>
@endsection