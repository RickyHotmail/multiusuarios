@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Rango de Documento</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Tipo Comprobate</th>
                    <th>Establecimiento</th>  
                    <th>Punto de Emision</th>   
                    <th>Inicio de Rango</th>
                    <th>Fin de Rango</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Autorizacion</th>
                    <th>Descripcion</th>                
                </tr>
            </thead> 
            <tbody>
                @foreach($rangoDocumento as $rangoDocumento)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("rangoDocumento/{$rangoDocumento->rango_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("rangoDocumento/{$rangoDocumento->rango_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("rangoDocumento/{$rangoDocumento->rango_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $rangoDocumento->tipoComprobante->tipo_comprobante_nombre}}</td> 
                    <td>{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo}}</td>
                    <td>{{ $rangoDocumento->puntoEmision->punto_serie}}</td>
                    <td>{{ $rangoDocumento->rango_inicio}}</td>
                    <td>{{ $rangoDocumento->rango_fin}}</td>  
                    <td>{{ $rangoDocumento->rango_fecha_inicio}}</td>  
                    <td>{{ $rangoDocumento->rango_fecha_fin}}</td>
                    <td>{{ $rangoDocumento->rango_autorizacion}}</td>
                    <td>{{ $rangoDocumento->rango_descripcion}}</td>                                   
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
                <h4 class="modal-title">Nuevo Rango de Documentos</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("rangoDocumento") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idTipo" class="col-sm-3 col-form-label">Tipo de Comprobante</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idTipo" name="idTipo" require>
                                    @foreach($tipoComprobantes as $tipoComprobante)
                                        <option value="{{$tipoComprobante->tipo_comprobante_id}}">{{$tipoComprobante->tipo_comprobante_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idPunto" class="col-sm-3 col-form-label">Punto de Emision</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idPunto" name="idPunto" require>
                                    @foreach($puntoEmisiones as $puntoEmision)
                                        <option value="{{$puntoEmision->punto_id}}">{{$puntoEmision->sucursal->sucursal_codigo}}{{$puntoEmision->punto_serie}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idDescripcion" class="col-sm-3 col-form-label">Descripcion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idDescripcion" name="idDescripcion" placeholder="Descripcion" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idRinicio" class="col-sm-3 col-form-label">Inicio de Rango</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idRinicio" name="idRinicio" placeholder="Inicio de Rango" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idRfin" class="col-sm-3 col-form-label">Fin de Rango</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idRfin" name="idRfin" placeholder="Fin de Rango" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idFinicio" class="col-sm-3 col-form-label">Fecha de Incio</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idFinicio" name="idFinicio" value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idFfin" class="col-sm-3 col-form-label">Fecha fin</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idFfin" name="idFfin" value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idAutorizacion" class="col-sm-3 col-form-label">Autorizacion</label>
                            <div class="col-sm-9">
                                <input type="" class="form-control" id="idAutorizacion" name="idAutorizacion" placeholder="# de autorizacion" value="0" required>
                            </div>
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