@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('proveedor.update', [$proveedor->proveedor_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Proveedor</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--     
                <button type="button"  onclick='window.location = "{{ url("proveedor") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idSujeto" class="col-sm-2 col-form-label">Tipo de Sujeto</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idSujeto" name="idSujeto" require>
                        @foreach($tipoSujetos as $tipoSujeto)
                            @if($tipoSujeto->tipo_sujeto_id == $proveedor->tipo_sujeto_id)
                                <option value="{{$tipoSujeto->tipo_sujeto_id}}" selected>{{$tipoSujeto->tipo_sujeto_nombre}}</option>
                            @else 
                                <option value="{{$tipoSujeto->tipo_sujeto_id}}">{{$tipoSujeto->tipo_sujeto_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>
            <div class="form-group row">
                <label for="idTidentificacion" class="col-sm-2 col-form-label">Tipo de Identificacion</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idTidentificacion" name="idTidentificacion" require>
                        @foreach($tipoIdentificaciones as $tipoIdentificacion)
                            @if($tipoIdentificacion->tipo_identificacion_id == $proveedor->tipo_identificacion_id)
                                <option value="{{$tipoIdentificacion->tipo_identificacion_id}}" selected>{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                            @else 
                                <option value="{{$tipoIdentificacion->tipo_identificacion_id}}">{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>  
            <div class="form-group row">
                <label for="idRuc" class="col-sm-2 col-form-label">Ruc</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idRuc" name="idRuc" value="{{$proveedor->proveedor_ruc}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Razón Social</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombre" name="idNombre" value="{{$proveedor->proveedor_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombreComercial" class="col-sm-2 col-form-label">Nombre Comercial</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idNombreComercial" name="idNombreComercial" value="{{$proveedor->proveedor_nombre_comercial}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idGerente" class="col-sm-2 col-form-label">Gerente</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idGerente" name="idGerente" value="{{$proveedor->proveedor_gerente}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idDireccion" class="col-sm-2 col-form-label">Direccion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idDireccion" name="idDireccion" value="{{$proveedor->proveedor_direccion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTelefono" class="col-sm-2 col-form-label">Telefono</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idTelefono" name="idTelefono"  value="{{$proveedor->proveedor_telefono}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idCelular" class="col-sm-2 col-form-label">Celular</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCelular" name="idCelular" value="{{$proveedor->proveedor_celular}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEmail" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idEmail" name="idEmail" value="{{$proveedor->proveedor_email}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idActividad" class="col-sm-2 col-form-label">Actividad</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idActividad" name="idActividad" value="{{$proveedor->proveedor_actividad}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idAutorizacion" class="col-sm-2 col-form-label">Fecha de Ingreso</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="idFecha" name="idFecha" value="{{$proveedor->proveedor_fecha_ingreso}}" required>
                </div>
            </div>
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Proveedor</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="idTipo" name="idTipo"required>
                        <option value="Ninguno" @if($proveedor->proveedor_tipo == 'Ninguno') selected @endif>Ninguno</option>
                        <option value="Microempresas" @if($proveedor->proveedor_tipo == 'Microempresas') selected @endif>Microempresas</option>
                        <option value="Agente de Retención" @if($proveedor->proveedor_tipo == 'Agente de Retención') selected @endif>Agente de Retención</option>
                        <option value="Contribuyente Especial" @if($proveedor->proveedor_tipo == 'Contribuyente Especial') selected @endif>Contribuyente Especial</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idLlevacontabilidad" class="col-sm-2 col-form-label">Lleva Contabilidad</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($proveedor->proveedor_lleva_contabilidad=="1")
                            <input type="checkbox" class="custom-control-input" id="idLlevacontabilidad" name="idLlevacontabilidad" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idLlevacontabilidad" name="idLlevacontabilidad">
                        @endif
                        <label class="custom-control-label" for="idLlevacontabilidad"></label>
                    </div>
                </div>                
            </div>
            <div class="form-group row">
                <label for="idContribuyente" class="col-sm-2 col-form-label">Contribuyente</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($proveedor->proveedor_contribuyente=="1")
                            <input type="checkbox" class="custom-control-input" id="idContribuyente" name="idContribuyente" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idContribuyente" name="idContribuyente">
                        @endif
                        <label class="custom-control-label" for="idContribuyente"></label>
                    </div>
                </div>                
            </div>               
                @if($parametrizacionContable->parametrizacion_cuenta_general == '0') 
                <div class="form-group row">
                    <label for="idCuentaxpagar" class="col-sm-2 col-form-label">Cuenta por Pagar</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="idCuentaxpagar" name="idCuentaxpagar" require>
                            @foreach($cuentas as $cuenta)
                                @if($cuenta->cuenta_id == $proveedor->proveedor_cuenta_pagar)
                                    <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @else 
                                    <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>                    
                </div>
                @endif
                @if($parametrizacionContableProveedor->parametrizacion_cuenta_general == '0') 
                <div class="form-group row">
                    <label for="idCuentaAnticipo" class="col-sm-2 col-form-label">Cuenta de Anticipo</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="idCuentaAnticipo" name="idCuentaAnticipo" require>
                            @foreach($cuentas as $cuenta)
                                @if($cuenta->cuenta_id == $proveedor->proveedor_cuenta_anticipo)
                                    <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @else 
                                    <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>                    
                </div>
                @endif
            @endif
           
            <div class="form-group row">
                <label for="idCiudad" class="col-sm-2 col-form-label">Ciudad</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idCiudad" name="idCiudad" require>
                        @foreach($ciudades as $ciudad)
                            @if($ciudad->ciudad_id == $proveedor->ciudad_id)
                                <option value="{{$ciudad->ciudad_id}}" selected>{{$ciudad->ciudad_nombre}}</option>
                            @else 
                                <option value="{{$ciudad->ciudad_id}}">{{$ciudad->ciudad_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div> 
            <div class="form-group row">
                <label for="idCategoria" class="col-sm-2 col-form-label">Categoria de Proveedor</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idCategoria" name="idCategoria" require>
                        @foreach($categoriaProveedores as $categoriaProveedor)
                            @if($categoriaProveedor->categoria_proveedor_id == $proveedor->categoria_proveedor_id)
                                <option value="{{$categoriaProveedor->categoria_proveedor_id}}" selected>{{$categoriaProveedor->categoria_proveedor_nombre}}</option>
                            @else 
                                <option value="{{$categoriaProveedor->categoria_proveedor_id}}">{{$categoriaProveedor->categoria_proveedor_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>                                                             
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($proveedor->proveedor_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="bodega_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection