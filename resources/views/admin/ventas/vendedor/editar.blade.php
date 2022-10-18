@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('vendedor.update', [$vendedor->vendedor_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Vendedor</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--
                <button type="button" onclick='window.location = "{{ url("vendedor") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->      
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idCedula" class="col-sm-2 col-form-label">Cedula</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCedula" name="idCedula" value="{{$vendedor->vendedor_cedula}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombre" name="idNombre" value="{{$vendedor->vendedor_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idDireccion" class="col-sm-2 col-form-label">Direccion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idDireccion" name="idDireccion" value="{{$vendedor->vendedor_direccion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTelefono" class="col-sm-2 col-form-label">Telefono</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idTelefono" name="idTelefono"  value="{{$vendedor->vendedor_telefono}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEmail" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idEmail" name="idEmail" value="{{$vendedor->vendedor_email}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idPorcentaje" class="col-sm-2 col-form-label">Comision Porcentaje</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idPorcentaje" name="idPorcentaje" value="{{$vendedor->vendedor_comision_porcentaje}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idFecha" class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="idFecha" name="idFecha" value="{{$vendedor->vendedor_fecha_ingreso}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idZona" class="col-sm-2 col-form-label">Zona Asignada</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idZona" name="idZona" require>
                        @foreach($zonas as $zona)
                            @if($zona->zona_id == $vendedor->zona_id)
                                <option value="{{$zona->zona_id}}" selected>{{$zona->zona_nombre}}</option>
                            @else 
                                <option value="{{$zona->zona_id}}">{{$zona->zona_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($vendedor->vendedor_estado=="1")
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