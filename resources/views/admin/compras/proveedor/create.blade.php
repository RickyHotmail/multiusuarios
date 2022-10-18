@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("proveedor") }}">
@csrf
    <div class="card card-secondary">    
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title">Nuevo Proveedor</h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">                           
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i><span> Guardar</span></button>                           
                        <a href= "{{ url("proveedor") }}"><button type="button" id="cancelarID" name="cancelarID" class="btn btn-default btn-sm not-active-neo"><i class="fas fa-times-circle"></i><span> Atras</span></button></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idRuc" class="col-sm-3 col-form-label">Ruc</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idRuc" name="idRuc" placeholder="#Ruc" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-3 col-form-label">Razón Social</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombreComercial" class="col-sm-3 col-form-label">Nombre Comercial</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idNombreComercial" name="idNombreComercial" placeholder="Nombre Comercial" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idGerente" class="col-sm-3 col-form-label">Gerente</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idGerente" name="idGerente" placeholder="Gerente" required>
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
                    <input type="text" class="form-control" id="idEmail" name="idEmail" placeholder="Email" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idActividad" class="col-sm-3 col-form-label">Actividad</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idActividad" name="idActividad" placeholder="Actividad que realiza" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idFecha" class="col-sm-3 col-form-label">Fecha de Ingreso</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" id="idFecha" name="idFecha" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <div class="form-group row">
                <label for="idTipo" class="col-sm-3 col-form-label">Tipo de Proveedor</label>
                <div class="col-sm-9">
                    <select class="custom-select" id="idTipo" name="idTipo" require>
                        <option value="Ninguno">Ninguno</option>
                        <option value="Microempresas">Microempresas</option>
                        <option value="Agente de Retención">Agente de Retención</option>
                        <option value="Contribuyente Especial">Contribuyente Especial</option>
                    </select>
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
                <label for="idContribuyente" class="col-sm-3 col-form-label">Contribuyente Especial</label>
                <div class="col-sm-3">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">                                    
                        <input type="checkbox" class="custom-control-input" id="idContribuyente" name="idContribuyente">
                        <label class="custom-control-label" for="idContribuyente"></label>                                 
                    </div>
                </div>                
            </div>            
                @if($parametrizacionContable->parametrizacion_cuenta_general == '0')                               
                <div class="form-group row">
                    <label for="idCuentaxpagar" class="col-sm-3 col-form-label">Cuenta por Pagar</label>
                    <div class="col-sm-9">
                        <select class="custom-select select2" id="idCuentaxpagar" name="idCuentaxpagar" require>
                            @foreach($cuentas as $cuenta)
                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idCuentaAnticipo" class="col-sm-3 col-form-label">Cuenta Anticipo</label>
                    <div class="col-sm-9">
                        <select class="custom-select select2" id="idCuentaAnticipo" name="idCuentaAnticipo" require>
                            @foreach($cuentas as $cuenta)
                                <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
            @endif
            <div class="form-group row">
                <label for="idSujeto" class="col-sm-3 col-form-label">Tipo de Sujeto</label>
                <div class="col-sm-9">
                    <select class="custom-select select2" id="idSujeto" name="idSujeto" require>
                        @foreach($tipoSujetos as $tipoSujeto)
                            <option value="{{$tipoSujeto->tipo_sujeto_id}}">{{$tipoSujeto->tipo_sujeto_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>  
            <div class="form-group row">
                <label for="idCiudad" class="col-sm-3 col-form-label">Ciudad</label>
                <div class="col-sm-9">
                    <select class="custom-select select2" id="idCiudad" name="idCiudad" require>
                        @foreach($ciudades as $ciudad)
                            <option value="{{$ciudad->ciudad_id}}">{{$ciudad->ciudad_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTidentificacion" class="col-sm-3 col-form-label">Tipo de Identificacion</label>
                <div class="col-sm-9">
                    <select class="custom-select select2" id="idTidentificacion" name="idTidentificacion" require>
                        @foreach($tipoIdentificaciones as $tipoIdentificacion)
                            <option value="{{$tipoIdentificacion->tipo_identificacion_id}}">{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>                      
            
            <div class="form-group row">
                <label for="idCategoria" class="col-sm-3 col-form-label">Categoria de Proveedor</label>
                <div class="col-sm-9">
                    <select class="custom-select select2" id="idCategoria" name="idCategoria" require>
                        @foreach($categoriaProveedores as $categoriaProveedor)
                            <option value="{{$categoriaProveedor->categoria_proveedor_id}}">{{$categoriaProveedor->categoria_proveedor_nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection