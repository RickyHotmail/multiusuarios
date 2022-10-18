@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Grupo de Activos Fijos</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("grupoActivoBuscar") }}">
        @csrf
            <div class="form-group row">
                <label for="idsucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="idsucursal" name="idsucursal">
                          
                        @foreach($sucursales as $sucursal)                            
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($sucursalselect)) @if($sucursalselect == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                                                     
                        @endforeach
                    </select> 
                </div>                              
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>                    
            </div>            
        </form>        
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>
                    <th>Nombre</th>
                    <th>Cuenta Depreciacion</th>  
                    <th>Cuenta Gasto</th>  
                    <th>Porcentaje</th>
                    <th>Sucursal</th>                                               
                </tr>
            </thead>            
            <tbody>
            @if(isset($grupoActivo))
                @foreach($grupoActivo as $grupoActivo)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("grupoActivo/{$grupoActivo->grupo_id}/edit")}}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("grupoActivo/{$grupoActivo->grupo_id}")}}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("grupoActivo/{$grupoActivo->grupo_id}/eliminar")}}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $grupoActivo->grupo_nombre}}</td>
                    <td>{{ $grupoActivo->cuentaDepreciacion->cuenta_numero.' -  '.$grupoActivo->cuentaDepreciacion->cuenta_nombre}}</td>
                    <td>{{ $grupoActivo->cuentaGasto->cuenta_numero.' -  '.$grupoActivo->cuentaGasto->cuenta_nombre}}</td>  
                    <td>{{ $grupoActivo->grupo_porcentaje}} %</td>
                    <td>{{ $grupoActivo->sucursal->sucursal_nombre}}</td>
                </tr>
                @endforeach
            @endif
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
                <h4 class="modal-title">Nuevo Grupo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("grupoActivo") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idSucursal" class="col-sm-3 col-form-label">Sucursal</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idSucursal" name="idSucursal" require>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                                <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" required>
                                </div>
                        </div>                        
                        <div class="form-group row">
                            <label for="idDepreciacion" class="col-sm-3 col-form-label">Cuenta Depreciacion</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idDepreciacion" name="idDepreciacion" require>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idGasto" class="col-sm-3 col-form-label">Cuenta Gasto</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idGasto" name="idGasto" require>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                            <div class="form-group row">
                                <label for="idPorcentaje" class="col-sm-3 col-form-label">Porcentaje</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="idPorcentaje" name="idPorcentaje" min="0" step=".01" value="0" required>
                                </div>
                            </div>
                        </div>
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