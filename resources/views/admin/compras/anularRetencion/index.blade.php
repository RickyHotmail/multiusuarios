@extends ('admin.layouts.admin')
@section('principal')

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Retenciones Anuladas</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
    <form class="form-horizontal" method="POST"  action="{{ url("listarRetencionesAnuladas") }} " >
            @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php if(isset($fechaDesde)) echo($fechaDesde); else echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php if(isset($fechahasta)) echo($fechahasta); else echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div>    
               
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>  
        </form>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>                         
                    <th>Fecha Ret</th>                    
                    <th>Numero</th>
                    <th>Autorizacion</th>
                    <th>Fecha Anulacion</th>
                </tr>
            </thead> 
            <tbody>
                @if(isset($retencionCompras))
                    @foreach($retencionCompras as $retencionCompra)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("anularRetencion/{$retencionCompra->retencion_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>                   
                        </td>
                        <td>{{ $retencionCompra->retencion_fecha}}</td>
                        <td>{{ $retencionCompra->retencion_numero }}</td>
                        <td>{{ $retencionCompra->retencion_autorizacion }}</td>  
                        @if(isset($retencionCompra->dopcumentoanulado->documento_anulado_fecha))                             
                            <td>{{ $retencionCompra->dopcumentoanulado->documento_anulado_fecha }}</td>
                        @else
                        <td></td>
                        @endif                               
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
                <h4 class="modal-title">Nueva Retencion Anulada</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST"  action="{{ url("anularRetencion") }} " >
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                    <div class="form-group row">
                            <label for="centro_consumo_fecha_ingreso" class="col-sm-3 col-form-label">Fecha Ret</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idFechaRet" name="idFechaRet" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                            </div>
                        </div>
                    <div class="form-group row">
                            <label for="idRango" class="col-sm-3 col-form-label">Serie</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idRango" name="idRango" require>
                                    @foreach($rangoDocumentos as $rangoDocumento)
                                        <option value="{{$rangoDocumento->rango_id}}">{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{$rangoDocumento->puntoEmision->punto_serie}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                        
                        <div class="form-group row">
                            <label for="ciudad_codigo" class="col-sm-3 col-form-label">Numero</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNumero" name="idNumero" placeholder="Ej. 55" required>
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label for="ciudad_codigo" class="col-sm-3 col-form-label">Autorizacion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idAutorizacion" name="idAutorizacion" placeholder="Ej. 12345678910111213" required>
                            </div>
                        </div>                       
                        <div class="form-group row">
                            <label for="centro_consumo_fecha_ingreso" class="col-sm-3 col-form-label">Fecha Anulacion</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idFechaAnul" name="idFechaAnul" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                            </div>
                        </div>                    
                        <div class="form-group row">
                            <label for="idEmision" class="col-sm-3 col-form-label">Emisor</label>
                            <div class="col-sm-9">
                                        <select id="idEmision" name="idEmision" class="form-control show-tick " data-live-search="true" required>
                                            <option value="" label>--Seleccione--</option>
                                            <option value="ELECTRONICA">ELECTRONICA</option>
                                            <option value="FISICA">FISICA</option>
                                        </select>
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