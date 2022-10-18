@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('tipoEmpleado.update', [$tipoEmpleado->tipo_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Tipo Emepleado</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--
                <button type="button"  onclick='window.location = "{{ url("tipoEmpleado") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idCodigo" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="tipo_descripcion" name="tipo_descripcion"
                        placeholder="Descripción" value="{{ $tipoEmpleado->tipo_descripcion }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Categoría</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="tipo_categoria" name="tipo_categoria" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @if($tipoEmpleado->tipo_categoria =='ADMINISTRATIVO')<option value="ADMINISTRATIVO" selected>ADMINISTRATIVO</option>  @else <option value="ADMINISTRATIVO">ADMINISTRATIVO</option>@endif
                        @if($tipoEmpleado->tipo_categoria =='OPERATIVO')<option value="OPERATIVO" selected>OPERATIVO</option>  @else <option value="OPERATIVO">OPERATIVO</option>@endif
                        @if($tipoEmpleado->tipo_categoria =='OPERATIVO CONTROL DIAS')<option value="OPERATIVO CONTROL DIAS" selected>OPERATIVO CONTROL DIAS</option>  @else <option value="OPERATIVO CONTROL DIAS">OPERATIVO CONTROL DIAS</option>@endif
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="sucursal" name="sucursal" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($tipoEmpleado->sucursal_id)) @if($tipoEmpleado->sucursal_id==$sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                        @endforeach                           
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="tipo_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($tipoEmpleado->tipo_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="tipo_estado" name="tipo_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="tipo_estado" name="tipo_estado">
                        @endif
                        <label class="custom-control-label" for="tipo_estado"></label>
                    </div>
                </div>                
            </div> 
            <div class="form-group row">
                <div class="col-sm-2"> </div>
                <label class="col-sm-4 col-form-label">
                    <CENTER>CUENTA DEBE</CENTER>
                </label>
                <label class="col-sm-4 col-form-label">
                    <CENTER>CUENTA HABER</CENTER>
                </label>
                <label class="col-sm-2 col-form-label">
                    <CENTER>CATEGORIA</CENTER>
                </label>
            </div>
            <div class="form-group row">
                @foreach($rubros as $rubro)
                <label class="col-sm-2 col-form-label">{{ $rubro->rubro_descripcion }}</label>
                <div class="col-sm-4">
                    <select class="form-control select2" style="width: 100%;" id="{{ $rubro->rubro_id }}-debe"
                        name="{{ $rubro->rubro_id }}-debe">
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($cuentas as $cuenta)
                        <option value="{{$cuenta->cuenta_id}}"
                        @foreach($tipoEmpleado->detalles as $aux) @if($aux->cuenta_debe == $cuenta->cuenta_id and $rubro->rubro_id == $aux->rubro_id) selected @endif @endforeach>
                            {{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <select class="form-control select2" style="width: 100%;" id="{{ $rubro->rubro_id }}-haber"
                        name="{{ $rubro->rubro_id }}-haber">
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($cuentas as $cuenta)
                        <option value="{{$cuenta->cuenta_id}}"
                            @foreach($tipoEmpleado->detalles as $aux) 
                                @if($aux->cuenta_haber == $cuenta->cuenta_id and $rubro->rubro_id == $aux->rubro_id) selected @endif 
                            @endforeach>
                            {{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="custom-select" id="{{ $rubro->rubro_id }}-categoria" name="{{ $rubro->rubro_id }}-categoria" >
                    <option value=""  selected>--Seleccione una opcion--</option>
                    @foreach($categorias as $categoria)
                        <option value="{{$categoria->categoria_id}}"
                            @foreach($tipoEmpleado->detalles as $aux)
                                @if($aux->categoria_id==$categoria->categoria_id and $rubro->rubro_id == $aux->rubro_id) selected @endif
                            @endforeach >
                            {{$categoria->categoria_nombre}}
                        </option>
                    @endforeach
                </select>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</form>
@endsection