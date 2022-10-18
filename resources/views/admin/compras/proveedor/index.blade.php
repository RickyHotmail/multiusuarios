@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Proveedores</h3>
        <div class="float-right">
            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
            <a class="btn btn-info btn-sm" href="{{ asset('admin/archivos/FORMATO_PROVEEDORES.xlsx') }}" download="FORMATO_PROVEEDORES"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
            <a class="btn btn-success btn-sm" href="{{ url("excelProveedor")}}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>  
     </div>
    </div>
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Tipo de Sujeto</th>
                    <th>Tipo de  Identificacion</th>         
                    <th>Ruc</th>
                    <th>Nombre</th>
                    <th>Nombre Comercial</th>
                    <th>Gerente</th>
                    <th>Direccion</th>
                    <th>Telefono</th>
                    <th>Celular</th>
                    <th>Email</th>
                    <th>Actividad</th>
                    <th>Fecha de Ingreso</th>
                    @if(Auth::user()->empresa->empresa_contabilidad == '1')
                    <th>Tipo</th>
                    <th>Lleva Contabilidad</th>
                    <th>Contribuyente</th>
                    <th>Cuenta por Pagar</th>
                    <th>Cuenta Anticipo</th>
                    @endif
                         
                    <th>Ciudad</th> 
                    <th>Categoria de Proveedor</th>              
                </tr>
            </thead> 
            <tbody>
                @foreach($proveedores as $proveedor)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("proveedor/{$proveedor->proveedor_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("proveedor/{$proveedor->proveedor_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("proveedor/{$proveedor->proveedor_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $proveedor->tipoSujeto->tipo_sujeto_nombre}}</td>
                    <td>{{ $proveedor->tipoIdentificacion->tipo_identificacion_nombre}}</td>
                    <td>{{ $proveedor->proveedor_ruc}}</td>
                    <td>{{ $proveedor->proveedor_nombre}}</td>
                    <td>{{ $proveedor->proveedor_nombre_comercial}}</td>
                    <td>{{ $proveedor->proveedor_gerente}}</td>   
                    <td>{{ $proveedor->proveedor_direccion}}</td>
                    <td>{{ $proveedor->proveedor_telefono}}</td>
                    <td>{{ $proveedor->proveedor_celular}}</td>
                    <td>{{ $proveedor->proveedor_email}}</td>
                    <td>{{ $proveedor->proveedor_actividad}}</td>
                    <td>{{ $proveedor->proveedor_fecha_ingreso}}</td>
                    @if(Auth::user()->empresa->empresa_contabilidad == '1')
                    <td>{{ $proveedor->proveedor_tipo}}</td>
                    <td>
                         @if($proveedor->proveedor_lleva_contabilidad=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </td>
                    <td>
                         @if($proveedor->proveedor_contribuyente=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                            @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </td>
                        @if(isset($proveedor->cuentaPagar->cuenta_nombre))
                            <td>{{ $proveedor->cuentaPagar->cuenta_nombre}}</td>
                        @else                           
                            <td>Parametrizar cuentas</td>
                        @endif
                        @if(isset($proveedor->cuentaAnticipo->cuenta_nombre))                            
                            <td>{{ $proveedor->cuentaAnticipo->cuenta_nombre}}</td>
                        @else
                            <td>Parametrizar cuentas</td>
                        @endif                    
                    @endif
                   
                    <td>{{ $proveedor->ciudad->ciudad_nombre}}</td>                  
                    <td>{{ $proveedor->categoriaProveedor->categoria_proveedor_nombre}}</td>
                                   
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
                <h4 class="modal-title">Nuevo Proveedor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("proveedor") }} "> 
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row" >
                            <label for="idSujeto" class="col-sm-3 col-form-label">Tipo de Sujeto</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idSujeto" name="idSujeto"   required>
                                    @foreach($tipoSujetos as $tipoSujeto)
                                        <option value="{{$tipoSujeto->tipo_sujeto_codigo}}"  @if($tipoSujeto->tipo_sujeto_codigo=='02') selected @endif>{{$tipoSujeto->tipo_sujeto_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label for="idTidentificacion" class="col-sm-3 col-form-label">Tipo de Identificacion</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idTidentificacion" name="idTidentificacion" required>
                                    @foreach($tipoIdentificaciones as $tipoIdentificacion)
                                        <option value="{{$tipoIdentificacion->tipo_identificacion_id}}">{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>           
                        <div class="form-group row">
                            <label for="idRuc" class="col-sm-3 col-form-label">Ruc</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idRuc" name="idRuc" placeholder="#Ruc" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombre" id="idNombre" class="col-sm-3 col-form-label">Nombres</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombres" required>
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
                            @if(isset($parametrizacionContable->parametrizacion_cuenta_general))                          
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
                                @endif
                            @endif
                            @if(isset($parametrizacionContableProveedor->parametrizacion_cuenta_general))                          
                                @if($parametrizacionContableProveedor->parametrizacion_cuenta_general == '0')    
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
                        @endif
                        
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