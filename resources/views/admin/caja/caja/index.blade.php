@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Cajas</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Sucursal</th>
                    <th>Nombre</th>
                    @if(Auth::user()->empresa->empresa_contabilidad == '1') 
                    <th>Cuenta</th>        
                    @endif                                
                </tr>
            </thead>            
            <tbody>
                @foreach($cajas as $caja)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("caja/{$caja->caja_id}/cajausers") }}" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Asignar Usuarios"><i class="fa fa-tasks" aria-hidden="true"></i></a>
                        <a href="{{ url("caja/{$caja->caja_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("caja/{$caja->caja_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("caja/{$caja->caja_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $caja->sucursal->sucursal_nombre}}</td>  
                    <td>{{ $caja->caja_nombre}}</td>       
                    @if(Auth::user()->empresa->empresa_contabilidad == '1')             
                    <td>{{ $caja->cuenta->cuenta_numero.'  -  '.$caja->cuenta->cuenta_nombre}}</td>       
                    @endif              
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
                <h4 class="modal-title">Nueva Caja</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("caja") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                    <div class="form-group row">
                        <label for="idSucursal" class="col-sm-3 col-form-label">Sucursal</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idSucursal" name="idSucursal" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                                          
                        <div class="form-group row">
                            <label for="idCajaNombre" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCajaNombre" name="idCajaNombre" placeholder="Caja" required>
                            </div>
                        </div>
                        @if(Auth::user()->empresa->empresa_contabilidad == '1')
                        <div class="form-group row">
                            <label for="idCuenta" class="col-sm-3 col-form-label">Cuenta Contable</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" style="width: 100%;" id="idCuenta" name="idCuenta" requireD>
                                    @foreach($cuentas as $cuentas)
                                        <option value="{{$cuentas->cuenta_id}}">{{$cuentas->cuenta_numero.'  - '.$cuentas->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        @endif                                                                       
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