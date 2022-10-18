@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Prestamos</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("prestamos/buscar") }}">
            @csrf
            <div class="form-group row">
                <label for="nombre_sucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="nombre_sucursal" name="nombre_sucursal" >
                        <option value="0" label>--TODOS--</option>                         
                        @foreach($sucursalesP as $sucursal)
                            <option   value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                <label for="nombre_banco" class="col-sm-1 col-form-label"><center>Bancos:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="nombre_banco" name="nombre_banco" >
                        <option value="0" label>--TODOS--</option>                         
                        @foreach($bancosP as $banco)
                            <option  value="{{$banco->banco_id}}">{{$banco->banco_lista_nombre}}</option>
                        @endforeach
                    </select>                                     
                </div>
                    
            </div>
            <div class="form-group row">
                <label for="idestado" class="col-sm-1 col-form-label"><center>Estado:</center></label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="idestado" name="idestado">
                            <option value="0" label>--TODOS--</option>                 
                        @foreach($estados as $estado)                            
                            <option value="{{$estado->pestamo_estado}}" >
                                @if($estado->pestamo_estado == '1')
                                    Por Pagar
                                @else
                                    Cancelado
                                @endif
                            </option>                           
                        @endforeach
                    </select> 
                </div>    
                
                <div class="col-sm-1">            
                    <button type="submit"  class="btn btn-primary btn-sm" data-toggle="modal"><i class="fa fa-search"></i></button>                         
                </div>    
            </div>
   
            <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th></th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Banco</th>
                        <th>Monto</th>
                        <th>Interes</th>      
                        <th>Plazo</th>     
                        <th>Pago Interes</th> 
                        <th>Pago Total</th>     
                        <th>Observacion</th>     
                        <th>Cuenta Debe</th>     
                        <th>Cuenta Haber</th>                                  
                    </tr>
                </thead>            
                <tbody>
                    @if(isset($prestamos))
                        @foreach($prestamos as $prestamo)
                        <tr class="text-center">
                            <td>
                                <a href="{{ url("prestamos/{$prestamo->prestamo_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <a href="{{ url("prestamos/{$prestamo->prestamo_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                <a href="{{ url("prestamos/{$prestamo->prestamo_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                <a href="{{ url("detalleprestamos/{$prestamo->prestamo_id}/agregar") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Agregar Interes"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                <a href="{{ url("detalleprestamos/{$prestamo->prestamo_id}") }}" class="btn btn-xs btn-secondary"  data-toggle="tooltip" data-placement="top" title="Lista de Interes"><i class="fa fa-list" aria-hidden="true"></i></a>
                            </td>
                            <td>{{ $prestamo->prestamo_inicio }}</td>       
                            <td>{{ $prestamo->prestamo_fin }}</td>       
                            <td>{{ $prestamo->banco->bancoLista->banco_lista_nombre }}</td>  
                            <td>{{ $prestamo->prestamo_monto }}</td>       
                            
                            <td>{{ $prestamo->prestamo_interes }}</td>  
                            <td>{{ $prestamo->prestamo_plazo }}</td>  
                            <td>{{ $prestamo->prestamo_total_interes }}</td>  
                            <td>{{ $prestamo->prestamo_pago_total }}</td>  
                            <td>{{ $prestamo->prestamo_observacion }}</td>  
                            <td>{{ $prestamo->cuentadebe->cuenta_numero.' -  '.$prestamo->cuentadebe->cuenta_nombre}}</td>
                            <td>{{ $prestamo->cuentahaber->cuenta_numero.' -  '.$prestamo->cuentahaber->cuenta_nombre}}</td>             
                        </tr>
                        @endforeach
                    @endif    
                </tbody>
            </table>
        </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Prestamo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("prestamos") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="sucursal_id" class="col-sm-3 col-form-label">Sucursal</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="sucursal_id" name="sucursal_id" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label for="idBanco" class="col-sm-3 col-form-label">Banco</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idBanco" name="idBanco" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($bancos as $banco)
                                    <option value="{{$banco->banco_id}}">{{$banco->bancoLista->banco_lista_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label for="idFechaini" class="col-sm-3 col-form-label">Fecha Inicio</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idFechaini" name="idFechaini" value='<?php echo (date("Y") . "-" . date("m") . "-" . date("d")); ?>' required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idFechafin" class="col-sm-3 col-form-label">Fecha Finalizacion</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idFechafin" name="idFechafin" value='<?php echo (date("Y") . "-" . date("m") . "-" . date("d")); ?>' required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idMonto" class="col-sm-3 col-form-label">Monto</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="idMonto" name="idMonto"  step="any" placeholder="Monto" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idInteres" class="col-sm-3 col-form-label">Interes</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="idInteres" name="idInteres" step="any" placeholder="Interes" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idPlazo" class="col-sm-3 col-form-label">Plazo</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="idPlazo" name="idPlazo"  placeholder="Plazo" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idDebe" class="col-sm-3 col-form-label">Cuenta Debe</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idDebe" name="idDebe" require>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idHaber" class="col-sm-3 col-form-label">Cuenta Haber</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idHaber" name="idHaber" require>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.'  - '.$cuenta->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>  
                        <div class="form-group row">
                                <label for="idDescripcion" class="col-sm-3 col-form-label">Observación</label>
                                <div class="col-sm-9">                                    
                                    <textarea type="text" class="form-control" id="idDescripcion" name="idDescripcion" placeholder="Descripcion" ></textarea>
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