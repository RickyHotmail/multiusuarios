@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Tipo de Emepleado</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Descripción</th>
                    <th>Categoría</th>        
                    <th>Sucursal</th>                                                                  
                </tr>
            </thead>
            <tbody>
                @foreach($tiposEmpleado as $tipoEmpleado)
                <tr class="text-center">
                    <td>                        
                        <a href="{{ url("tipoEmpleado/{$tipoEmpleado->tipo_id}/edit") }}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("tipoEmpleado/{$tipoEmpleado->tipo_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>                   
                        <a href="{{ url("tipoEmpleado/{$tipoEmpleado->tipo_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>                        
                    </td>                                        
                    <td>{{ $tipoEmpleado->tipo_descripcion }}</td>
                    <td>{{ $tipoEmpleado->tipo_categoria }}</td>             
                    <td>@if(isset($tipoEmpleado->sucursal_id)) {{$tipoEmpleado->sucursal->sucursal_nombre}} @endif</td>                                                      
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Tipo de Emepleado</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("tipoEmpleado") }} "> 
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idCodigo" class="col-sm-2 col-form-label">Descripcion</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="tipo_descripcion" name="tipo_descripcion" placeholder="Descripción" required>
                            </div>
                        </div>                
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-2 col-form-label">Categoría</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="tipo_categoria" name="tipo_categoria" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    <option value="ADMINISTRATIVO">ADMINISTRATIVO</option>
                                    <option value="OPERATIVO">OPERATIVO</option>              
                                    <option value="OPERATIVO">OPERATIVO CONTROL DIAS</option>                                    
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-2 col-form-label">Sucursal</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="sucursal" name="sucursal" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                    @endforeach                           
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-2"> </div>
                            <label class="col-sm-4 col-form-label"><CENTER>CUENTA DEBE</CENTER></label>
                            <label class="col-sm-4 col-form-label"><CENTER>CUENTA HABER</CENTER></label>
                            <label class="col-sm-2 col-form-label"><CENTER>CATEGORIA</CENTER></label>
                        </div>
                        <div class="form-group row">
                            @foreach($rubros as $rubro)
                            <label class="col-sm-2 col-form-label">{{ $rubro->rubro_descripcion }}</label>
                            <div class="col-sm-4">  
                                <select class="form-control select2" style="width: 100%;" id="{{ $rubro->rubro_id }}-debe" name="{{ $rubro->rubro_id }}-debe">
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">  
                                <select class="form-control select2" style="width: 100%;" id="{{ $rubro->rubro_id }}-haber" name="{{ $rubro->rubro_id }}-haber">
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select class="custom-select" id="{{ $rubro->rubro_id }}-categoria" name="{{ $rubro->rubro_id }}-categoria" >
                                    <option value=""  selected>--Seleccione una opcion--</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{$categoria->categoria_id}}">{{$categoria->categoria_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endforeach
                            
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection