@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('bodeguero.update', [$bodeguero->bodeguero_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Bodeguero</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("bodeguero") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="bodeguero_cedula" class="col-sm-2 col-form-label">Cedula</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bodeguero_cedula" name="bodeguero_cedula" placeholder="0875465726" value="{{$bodeguero->bodeguero_cedula}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="bodeguero_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bodeguero_nombre" name="bodeguero_nombre" placeholder="Nombre" value="{{$bodeguero->bodeguero_nombre}}" required>
                </div>
            </div>                
            <div class="form-group row">
                <label for="bodeguero_direccion" class="col-sm-2 col-form-label">Direccion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bodeguero_direccion" name="bodeguero_direccion" placeholder="Av. Ejemplo" value="{{$bodeguero->bodeguero_direccion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="bodeguero_telefono" class="col-sm-2 col-form-label">Telefono</label>
                <div class="col-sm-10">
                    <input type="tel" class="form-control" id="bodeguero_telefono" name="bodeguero_telefono" placeholder="029814654" value="{{$bodeguero->bodeguero_telefono}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="bodeguero_email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="bodeguero_email" name="bodeguero_email" placeholder="ejem@plo.com" value="{{$bodeguero->bodeguero_email}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="bodeguero_fecha_ingreso" class="col-sm-2 col-form-label">Fecha Ingreso</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="bodeguero_fecha_ingreso" name="bodeguero_fecha_ingreso" value="{{$bodeguero->bodeguero_fecha_ingreso}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="bodeguero_fecha_salida" class="col-sm-2 col-form-label">Fecha Salida</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="bodeguero_fecha_salida" name="bodeguero_fecha_salida" value="{{$bodeguero->bodeguero_fecha_salida}}">
                </div>
            </div>
            <div class="form-group row">
                <label for="bodega_id" class="col-sm-2 col-form-label">Bodega</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="bodega_id" name="bodega_id" require>
                        @foreach($bodegas as $bodega)
                            @if($bodega->bodega_id == $bodeguero->bodega_id)
                                <option value="{{$bodega->bodega_id}}" selected>{{$bodega->bodega_nombre}}</option>
                            @else 
                                <option value="{{$bodega->bodega_id}}">{{$bodega->bodega_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>                                                               
            <div class="form-group row">
                <label for="bodeguero_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($bodeguero->bodeguero_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="bodeguero_estado" name="bodeguero_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="bodeguero_estado" name="bodeguero_estado">
                        @endif
                        <label class="custom-control-label" for="bodeguero_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection