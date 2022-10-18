@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("cliente") }}">
@csrf
    <div class="card card-secondary">    
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title">Nuevo Cliente</h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">                           
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i><span> Guardar</span></button>                           
                        <a href= "{{ url("cliente") }}"><button type="button" id="cancelarID" name="cancelarID" class="btn btn-default btn-sm not-active-neo"><i class="fas fa-times-circle"></i><span> Atras</span></button></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idCedula" class="col-sm-3 col-form-label">Cedula/Ruc/Pasaporte</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idCedula" name="idCedula" placeholder="9999999999" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idDireccion" class="col-sm-3 col-form-label">Direccion</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idDireccion" name="idDireccion" placeholder="Direccion" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTelefono" class="col-sm-3 col-form-label">Telefono</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idTelefono" name="idTelefono" placeholder="Telefono" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCelular" class="col-sm-3 col-form-label">Celular</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idCelular" name="idCelular" placeholder="Celular" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEmail" class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control" id="idEmail" name="idEmail" placeholder="Email" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idFecha" class="col-sm-3 col-form-label">Fecha de Ingreso</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" id="idFecha" name="idFecha" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idLlevacontabilidad" class="col-sm-3 col-form-label">Lleva Contabilidad</label>
                <div class="col-sm-3">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">                                   
                        <input type="checkbox" class="custom-control-input" id="idLlevacontabilidad" name="idLlevacontabilidad">                                    
                        <label class="custom-control-label" for="idLlevacontabilidad"></label>
                    </div>
                </div>                
                <label for="idTienecredito" class="col-sm-3 col-form-label">Tiene Credito</label>
                <div class="col-sm-3">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">                                    
                        <input type="checkbox" class="custom-control-input" id="idTienecredito" name="idTienecredito">
                        <label class="custom-control-label" for="idTienecredito"></label>                                 
                    </div>
                </div>                
            </div> 
            <div class="form-group row">
                <label for="idCupoCredito" class="col-sm-3 col-form-label">Cupo Credito</label>
                <div class="col-sm-9">                    
                    <input type="number" class="form-control" id="idCupoCredito" name="idCupoCredito" placeholder="0.00" value="0.00" step="any">
                </div>                    
            </div>      
            @if($parametrizacionContable->parametrizacion_cuenta_general == '0')         
                @if(Auth::user()->empresa->empresa_contabilidad == '1')                                  
                <div class="form-group row">
                    <label for="idCuentaxcobrar" class="col-sm-3 col-form-label">Cuenta por Cobrar</label>
                    <div class="col-sm-9">
                        <select class="custom-select select2" id="idCuentaxcobrar" name="idCuentaxcobrar">
                            <option value="" label>--Seleccione una opcion--</option>    
                            @foreach($cuentas as $cuenta)
                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idCuentaAnticipo" class="col-sm-3 col-form-label">Cuenta Anticipo</label>
                    <div class="col-sm-9">
                        <select class="custom-select select2" id="idCuentaAnticipo" name="idCuentaAnticipo">
                            <option value="" label>--Seleccione una opcion--</option>    
                            @foreach($cuentas as $cuenta)
                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
            @endif
            <div class="form-group row">
                <label for="idCiudad" class="col-sm-3 col-form-label">Ciudad</label>
                <div class="col-sm-9">
                    <select class="custom-select select2" id="idCiudad" name="idCiudad" require>
                        @foreach($ciudad as $ciudad)
                            <option value="{{$ciudad->ciudad_id}}">{{$ciudad->ciudad_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTidentificacion" class="col-sm-3 col-form-label">Tipo de Identificacion</label>
                <div class="col-sm-9">
                    <select class="custom-select select2" id="idTidentificacion" name="idTidentificacion" require>
                        @foreach($tipoIdentificacion as $tipoIdentificacion)
                            <option value="{{$tipoIdentificacion->tipo_identificacion_id}}">{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipoCliente" class="col-sm-3 col-form-label">Tipo de Cliente</label>
                <div class="col-sm-9">
                    <select class="custom-select select2" id="idTipoCliente" name="idTipoCliente" require>
                        @foreach($tipoCliente as $tipoCliente)
                            <option value="{{$tipoCliente->tipo_cliente_id}}">{{$tipoCliente->tipo_cliente_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCategoria" class="col-sm-3 col-form-label">Categoria de Cliente</label>
                <div class="col-sm-9">
                    <select class="custom-select select2" id="idCategoria" name="idCategoria" require>
                        @foreach($categoriaCliente as $categoriaCliente)
                            <option value="{{$categoriaCliente->categoria_cliente_id}}">{{$categoriaCliente->categoria_cliente_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>            
        </div>
    </div>
</form>
@endsection