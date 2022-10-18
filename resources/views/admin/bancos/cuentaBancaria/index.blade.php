@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Cuentas Bancarias</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Numero de Cuenta</th>
                    <th>Tipo</th>  
                    <th>Saldo Inicial</th>  
                    <th>Saldo</th>
                    <th>Jefe de Cuenta</th>
                    <th>Banco</th>
                    <th>Cuenta</th>                                              
                </tr>
            </thead>            
            <tbody>
                @foreach($cuentaBancarias as $cuentaBancaria)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("cuentaBancaria/{$cuentaBancaria->cuenta_bancaria_id}/edit")}}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("cuentaBancaria/{$cuentaBancaria->cuenta_bancaria_id}")}}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("cuentaBancaria/{$cuentaBancaria->cuenta_bancaria_id}/eliminar")}}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <a href="{{ url("cuentaBancaria/new/{$cuentaBancaria->cuenta_bancaria_id}")}}" class="btn btn-xs btn-neo-morado" data-toggle="tooltip" data-placement="top" title="Configurar Cheque"><i class="fas fa-list" aria-hidden="true"></i></a>
                        <a href="{{ url("chequeImpresionPdf/imprimir/{$cuentaBancaria->cuenta_bancaria_id}")}}" class="btn btn-xs btn-neo-morado" data-toggle="tooltip" data-placement="top" title="Imprimir Cheque"><i class="fas fa-list" aria-hidden="true"></i></a>

                    </td>
                    <td>{{ $cuentaBancaria->cuenta_bancaria_numero}}</td>             
                    <td>
                        @if($cuentaBancaria->cuenta_bancaria_tipo =='1')AHORRO 
                        @elseif ($cuentaBancaria->cuenta_bancaria_tipo =='2')CORRIENTE                       
                        @endif
                    </td>

                    <td>{{ $cuentaBancaria->cuenta_bancaria_saldo_inicial}}</td> 
                    <td>{{ $cuentaBancaria->cuenta_bancaria_saldo}}</td>
                    <td>{{ $cuentaBancaria->cuenta_bancaria_jefe}}</td>               
                    <td>{{ $cuentaBancaria->banco->bancoLista->banco_lista_nombre}}</td>
                    <td>{{ $cuentaBancaria->cuenta->cuenta_numero.' - '.$cuentaBancaria->cuenta->cuenta_nombre}}</td>                       
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
                <h4 class="modal-title">Nueva Cuenta Bancaria</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("cuentaBancaria") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idBanco" class="col-sm-3 col-form-label">Banco</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idBanco" name="idBanco" require>
                                    @foreach($bancos as $banco)
                                        <option value="{{$banco->banco_id}}">{{$banco->bancoLista->banco_lista_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                                <label for="idNumero" class="col-sm-3 col-form-label">Numero de Cuenta</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idNumero" name="idNumero" placeholder="# de Cuenta" required>
                                </div>
                            </div>
                            <div class="form-group row">
                            <label for="idTipo" class="col-sm-3 col-form-label">Tipo de Cuenta</label>
                            <div class="col-sm-9">
                                <select class="custom-select" id="idTipo" name="idTipo" require>
                                    <option value="1">Ahorro</option>
                                    <option value="2">Corriente</option>                                    
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                                <label for="idInicial" class="col-sm-3 col-form-label">Saldo Inicial</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idInicial" name="idInicial" placeholder="0.00" value="0.00" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="idjefe" class="col-sm-3 col-form-label">Cuenta Jefe</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idjefe" name="idjefe" placeholder="" required>
                                </div>
                            </div>
                                              
                        <div class="form-group row">
                            <label for="idCuenta" class="col-sm-3 col-form-label">Cuenta</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idCuenta" name="idCuenta" require>
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