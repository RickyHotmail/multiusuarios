@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Bancos</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>
                    <th>Nombre</th>
                    <th>Direccion</th>  
                    <th>Telefono</th>  
                    <th>Email</th>                                         
                </tr>
            </thead>            
            <tbody>
                @foreach($banco as $banco)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("banco/{$banco->banco_id}/edit")}}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("banco/{$banco->banco_id}")}}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("banco/{$banco->banco_id}/eliminar")}}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $banco->bancoLista->banco_lista_nombre}}</td>
                    <td>{{ $banco->banco_direccion}}</td> 
                    <td>{{ $banco->banco_telefono}}</td> 
                    <td>{{ $banco->banco_email}}</td>                  
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
                <h4 class="modal-title">Nuevo Banco</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("banco") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">                        
                        <div class="form-group row">
                            <label for="idLista" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idLista" name="idLista" require>
                                    @foreach($bancoLista as $bancoLista)
                                        <option value="{{$bancoLista->banco_lista_id}}">{{$bancoLista->banco_lista_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                            <div class="form-group row">
                                <label for="idDireccion" class="col-sm-3 col-form-label">Direccion</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idDireccion" name="idDireccion" placeholder="Direccion" value="S/D" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="idTelefono" class="col-sm-3 col-form-label">Telefono</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idTelefono" name="idTelefono" placeholder="Telefono" value="0" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="idEmail" class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="idEmail" name="idEmail" placeholder="SIN@CORREO" value="SIN@CORREO" required>
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
   
</div>
<!-- /.modal -->
@endsection