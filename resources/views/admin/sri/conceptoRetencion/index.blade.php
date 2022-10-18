@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Concepto de Retencion</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Porcentaje</th>
                    <th>Tipo</th>
                    <th>Objeto</th>
                    <th>Cuenta Emitida</th>
                    <th>Cuenta Recibida</th>                                       
                </tr>
            </thead> 
            <tbody>
                @foreach($conceptoRetenciones as $conceptoRetencion)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("conceptoRetencion/{$conceptoRetencion->concepto_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("conceptoRetencion/{$conceptoRetencion->concepto_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("conceptoRetencion/{$conceptoRetencion->concepto_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $conceptoRetencion->concepto_codigo }}</td> 
                    <td>{{ $conceptoRetencion->concepto_nombre}}</td>
                    <td>{{ $conceptoRetencion->concepto_porcentaje }}</td>
                    <td>
                        @if($conceptoRetencion->concepto_tipo =='1')Retencion en la fuente 
                        @elseif ($conceptoRetencion->concepto_tipo =='2')IVA                            
                        @endif
                    </td>    
                    <td>{{ $conceptoRetencion->concepto_objeto }}</td>              
                    <td>{{ $conceptoRetencion->cuentaEmitida->cuenta_nombre }}</td>
                    <td>{{ $conceptoRetencion->cuentaRecibida->cuenta_nombre }}</td>     
     
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
                <h4 class="modal-title">Nuevo Concepto de Retencion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("conceptoRetencion") }}" >
                @csrf
                <div class="modal-body">
                    <div class="card-body">                        
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCodigo" class="col-sm-3 col-form-label">Codigo</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCodigo" name="idCodigo" placeholder="Ingrese aqui codigo" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idPorcentaje" class="col-sm-3 col-form-label">Porcentaje</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="idPorcentaje" name="idPorcentaje" step="0.01" placeholder="00" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idTipo" class="col-sm-3 col-form-label">Tipo</label>
                            <div class="col-sm-9">
                                <select class="custom-select" id="idTipo" name="idTipo" require>
                                    <option value="1">Retencion en la fuente</option>
                                    <option value="2">IVA</option>                                    
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idObjeto" class="col-sm-3 col-form-label">Objeto</label>
                            <div class="col-sm-9">
                                <select class="custom-select" id="idObjeto" name="idObjeto" require>
                                    <option value="FUENTE">FUENTE</option>
                                    <option value="BIENES">BIENES</option>
                                    <option value="SERVICIOS">SERVICIOS</option>                                    
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idEmitida" class="col-sm-3 col-form-label">Cuenta Emitida</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" id="idEmitida" name="idEmitida" require>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idRecibida" class="col-sm-3 col-form-label">Cuenta Recibida</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" id="idRecibida" name="idRecibida" require>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
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