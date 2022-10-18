@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Medicamento</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Nombre</th>
                    <th>Composicion</th>
                    <th>Indicacion</th>
                    <th>Contraindicacion</th>   
                    <th>Tipo</th>                                                                                      
                </tr>
            </thead>
            <tbody>
                @foreach($medicamentos as $medicamento)
                <tr class="text-center">
                    <td>                        
                        <a href="{{ url("medicamento/{$medicamento->medicamento_id}/edit")}}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("medicamento/{$medicamento->medicamento_id}")}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>                   
                        <a href="{{ url("medicamento/{$medicamento->medicamento_id}/eliminar")}}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>                        
                    </td>                                        
                    <td>@if(isset($medicamento->producto->producto_nombre)) {{$medicamento->producto->producto_nombre}} @endif</td>
                    <td>{{ $medicamento->medicamento_composicion }}</td>  
                    <td>{{ $medicamento->medicamento_indicacion }}</td>  
                    <td>{{ $medicamento->medicamento_contraindicacion }}</td>           
                    <td>{{ $medicamento->tipoMedicamento->tipo_nombre }}</td>                                              
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
                <h4 class="modal-title">Nuevo Medicamento</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("medicamento") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">             
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Nombre del medicamento</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idproducto" name="idproducto" required>
                                    <option value="" label>--Seleccione una opcion--</option>                                                           
                                    @foreach($productos as $producto)
                                        <option value="{{$producto->producto_id}}">{{$producto->producto_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idComposicion" class="col-sm-3 col-form-label">Composicion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idComposicion" name="idComposicion" placeholder="Composicion" required> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idIndicacion" class="col-sm-3 col-form-label">Indicacion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idIndicacion" name="idIndicacion" placeholder="Indicacion" required> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idContraindicacion" class="col-sm-3 col-form-label">Contraindicacion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idContraindicacion" name="idContraindicacion" placeholder="Contraindicacion" required> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idTipo" class="col-sm-3 col-form-label">Tipo de medicamento</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idTipo" name="idTipo" required>
                                    <option value="" label>--Seleccione una opcion--</option>                                                           
                                    @foreach($tipoMedicamentos as $tipoMedicamento)
                                        <option value="{{$tipoMedicamento->tipo_id}}">{{$tipoMedicamento->tipo_nombre}}</option>
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