@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('bodega.update', [$bodega->bodega_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Bodega</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("bodega") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="bodega_nombre" class="col-sm-2 col-form-label">Nombre de Bodega</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bodega_nombre" name="bodega_nombre" placeholder="Nombre" value="{{$bodega->bodega_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="bodega_descripcion" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bodega_descripcion" name="bodega_descripcion" placeholder="Descripcion" value="{{$bodega->bodega_descripcion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="bodega_direccion" class="col-sm-2 col-form-label">Direccion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bodega_direccion" name="bodega_direccion" placeholder="Av. Ejemplo" value="{{$bodega->bodega_direccion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="bodega_telefono" class="col-sm-2 col-form-label">Telefono</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bodega_telefono" name="bodega_telefono" placeholder="0123456789" value="{{$bodega->bodega_telefono}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="bodega_fax" class="col-sm-2 col-form-label">Fax</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="bodega_fax" name="bodega_fax" placeholder="0123456789" value="0123456789" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="ciudad_id" class="col-sm-2 col-form-label">Ciudad</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="ciudad_id" name="ciudad_id" require>
                        @foreach($ciudades as $ciudad)
                            @if($ciudad->ciudad_id == $bodega->bodega_id)
                                <option value="{{$ciudad->ciudad_id}}" selected>{{$ciudad->ciudad_nombre}}</option>
                            @else 
                                <option value="{{$ciudad->ciudad_id}}">{{$ciudad->ciudad_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="sucursal_id" name="sucursal_id" require>
                        @foreach($sucursales as $sucursal)
                            @if($sucursal->sucursal_id == $bodega->sucursal_id)
                                <option value="{{$sucursal->sucursal_id}}" selected>{{$sucursal->sucursal_nombre}}</option>
                            @else 
                                <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>                                                   
            <div class="form-group row">
                <label for="bodega_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($bodega->bodega_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="bodega_estado" name="bodega_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="bodega_estado" name="bodega_estado">
                        @endif
                        <label class="custom-control-label" for="bodega_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection