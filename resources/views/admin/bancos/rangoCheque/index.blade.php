@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Rango de Cheque</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Rango Inicio</th>
                    <th>Rango Fin</th>   
                    <th>Cuenta Bancaria</th>                               
                </tr>
            </thead> 
            <tbody>
                @foreach($rangoCheques as $rangoCheque)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("rangoCheque/{$rangoCheque->rango_id}/edit")}}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("rangoCheque/{$rangoCheque->rango_id}")}}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("rangoCheque/{$rangoCheque->rango_id}/eliminar")}}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>                                   
                    <td>{{ $rangoCheque->rango_inicio}}</td>
                    <td>{{ $rangoCheque->rango_fin}}</td>
                    <td>{{ $rangoCheque->cuentaBancaria->cuenta_bancaria_numero.'  -  '.$rangoCheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</td> 
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
                <h4 class="modal-title">Nuevo Rango de Cheques</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("rangoCheque") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idBancaria" class="col-sm-3 col-form-label">Cuenta Bancaria</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idBancaria" name="idBancaria" require>
                                    @foreach($cuentaBancarias as $cuentaBancaria)
                                        <option value="{{$cuentaBancaria->cuenta_bancaria_id}}">{{$cuentaBancaria->cuenta_bancaria_numero.'  -  '.$cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</option>
                                    @endforeach
                                </select>
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