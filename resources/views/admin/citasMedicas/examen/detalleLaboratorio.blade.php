@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Detalles Laboratorio de {{$examen->producto->producto_nombre}}</h3>
        <div class="float-right">
            <button type="button" onclick='window.location = "{{ url("examen") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Nombre del detalle</th>
                    <th>Medida</th>
                    <th>Abreviatura</th>
                    <th>Valor Minimo</th>
                    <th>Valor Maximo</th>                                                                                        
                </tr>
            </thead>
            <tbody>
                @foreach($detallesLaboratorio as $detalleLaboratorios)
                    @if($detalleLaboratorios->examen_id == $examen->examen_id)
                    <tr class="text-center">
                        <td>                        
                            <a href="{{ url("valorLaboratorio/{$detalleLaboratorios->detalle_id}/agregarValorLaboratorio")}}" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Agregar valor de laboratorio"><i class="fa fa-tasks" aria-hidden="true"></i></a>
                            <a href="{{ url("valorLaboratorio/{$detalleLaboratorios->detalle_id}/agregarValorReferencial")}}" class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="top" title="Agregar valor referencial"><i class="fa fa-tasks" aria-hidden="true"></i></a>
                            <a href="{{ url("valorLaboratorio/{$detalleLaboratorios->detalle_id}/editar")}}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>                   
                            @if(count($detalleLaboratorios->valorreferencial)==0)
                                @if(count($detalleLaboratorios->valorlaboratorio)==0)    
                                    <a href="{{ url("valorLaboratorio/{$detalleLaboratorios->detalle_id}/eliminar")}}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>                    
                                @endif
                            @endif
                        </td>                                        
                        <td>{{ $detalleLaboratorios->detalle_nombre }}</td>    
                        <td>{{ $detalleLaboratorios->detalle_medida }}</td>    
                        <td>{{ $detalleLaboratorios->detalle_abreviatura }}</td>   
                        <td>{{ $detalleLaboratorios->detalle_minimo }}</td>   
                        <td>{{ $detalleLaboratorios->detalle_maximo }}</td>                                            
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nueva Detalle de Laboratorio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("detallelaboratorio") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">             
                        <div class="form-group row">
                            <label for="examen_nombre" class="col-sm-3 col-form-label">Nombre del Examen</label>
                            <div class="col-sm-9">
                                <label class="form-control" > {{$examen->producto->producto_nombre}}</label>
                                <input type="hidden" id="id_examen" name="id_examen" value="{{$examen->examen_id}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="examen_nombre" class="col-sm-3 col-form-label">Nombre del Detalle del Examen</label>
                            <div class="col-sm-9">
                            <input type="text" class="form-control" id="examen_nombre" name="examen_nombre" placeholder="Analsis">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tipo_examen" class="col-sm-3 col-form-label">Unidad de Medida</label>
                            <div class="col-sm-9">
                                 <input type="text" class="form-control" id="medida_detalle" name="medida_detalle" placeholder="Medida">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tipo_examen" class="col-sm-3 col-form-label">Abrebiatura</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="abreviatura_detalle" name="abreviatura_detalle" placeholder="Abreviatura" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tipo_examen" class="col-sm-3 col-form-label">Valor Minimo</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="detalle_minimo" name="detalle_minimo" step="0.00" min="0" placeholder="Valor Minimo" value="0" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tipo_examen" class="col-sm-3 col-form-label">Valor Maximo</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="detalle_maximo" name="detalle_maximo" placeholder="Valor Maximo" step="0.00" min="0" value="0" required>
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
@endsection