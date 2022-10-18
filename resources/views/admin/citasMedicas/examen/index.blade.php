@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Examen</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>    
                    <th>Nombre</th>        
                    <th>Tipo</th> 
                    
                </tr>
            </thead>
            <tbody>
                @foreach($examenes as $examen)
                <tr class="text-center">
                    <td>                        
                        <a href="{{ url("examen/{$examen->examen_id}/edit")}}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("examen/{$examen->examen_id}")}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>  
                        <a href="{{ url("examen/{$examen->examen_id}/agregarValores")}}" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Agregar Detalles"><i class="fa fa-list-alt" aria-hidden="true"></i></a>  
                        @if(count($examen->detalleslaboratorio)==0)
                            @if(count($examen->detallesexamen)==0)
                                <a href="{{ url("examen/{$examen->examen_id}/eliminar")}}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>                    
                            @endif
                        @endif
                    </td>
                    <td>{{ $examen->producto_nombre }}</td>     
                    <td>
                        @foreach($tipoExamenes as $tipoExamen)
                            @if($tipoExamen->tipo_id == $examen->tipo_id)
                                {{ $tipoExamen->tipo_nombre }}    
                            @endif                        
                        @endforeach
                    </td>                  
                                                                                         
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
                <h4 class="modal-title">Nueva Examen</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("examen") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">  
                        <div class="form-group row">
                            <label for="producto_id" class="col-sm-3 col-form-label">Analisis o Examen</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="producto_id" name="producto_id" required>
                                    <option value="" label>--Seleccione una opcion--</option>                                    
                                    @foreach($producto as $productos)                                                                                      
                                        <option value="{{ $productos->producto_id}}">{{ $productos->producto_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>           
                        
                        
                        <div class="form-group row">
                            <label for="tipo_examen" class="col-sm-3 col-form-label">Tipo del Examen</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="tipo_examen" name="tipo_examen" required>
                                    <option value="" label>--Seleccione una opcion--</option>                                    
                                    @foreach($tipoExamenes as $tipoExamen)                                                                                      
                                        <option value="{{ $tipoExamen->tipo_id}}">{{ $tipoExamen->tipo_nombre}}</option>
                                    @endforeach
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
