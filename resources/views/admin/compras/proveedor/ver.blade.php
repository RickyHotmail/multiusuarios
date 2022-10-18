@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Proveedor</h3>
         <!--     
        <button onclick='window.location = "{{ url("proveedor") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <div class="card-body">
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Tipo de Sujeto</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->tipoSujeto->tipo_sujeto_nombre}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Tipo de Identificacion</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->tipoIdentificacion->tipo_identificacion_nombre}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Ruc</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$proveedor->proveedor_ruc}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Razón Social</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$proveedor->proveedor_nombre}}</label>
                </div>
            </div>  
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Nombre Comercial</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->proveedor_nombre_comercial}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Gerente</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->proveedor_gerente}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Direccion</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->proveedor_direccion}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Telefono</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->proveedor_telefono}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Celular</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->proveedor_celular}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->proveedor_email}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Actividad</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->proveedor_actividad}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->proveedor_fecha_ingreso}}</label>                          
                </div>
            </div>
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tipo de Proveedor</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$proveedor->proveedor_tipo}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">LLeva Contabilidad</label>
                <div class="col-sm-10">
                    @if($proveedor->proveedor_lleva_contabilidad=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Contribuyente</label>
                <div class="col-sm-10">
                    @if($proveedor->proveedor_contribuyente=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            @if($parametrizacionContable->parametrizacion_cuenta_general == '0')
                @if(isset($proveedor->cuentaPagar->cuenta_numero))
                    <div class="form-group row">
                        <label for="" class="col-sm-2 col-form-label">Cuenta por Pagar</label>
                        <div class="col-sm-10">                        
                            <label class="form-control">{{$proveedor->cuentaPagar->cuenta_numero.' - '.$proveedor->cuentaPagar->cuenta_nombre}}</label>                          
                        </div>
                    </div>
                @endif
                    @if(isset($proveedor->cuentaAnticipo->cuenta_numero))
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">Cuenta de Anticipo</label>
                            <div class="col-sm-10">                        
                                <label class="form-control">{{$proveedor->cuentaAnticipo->cuenta_numero.' - '.$proveedor->cuentaAnticipo->cuenta_nombre}}</label>                          
                            </div>
                        </div>
                    @endif
                @endif
            @endif
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Ciudad</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->ciudad->ciudad_nombre}}</label>                          
                </div>
            </div>            
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Categoria Proveedor</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$proveedor->categoriaProveedor->categoria_proveedor_nombre}}</label>                          
                </div>
            </div>                        
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($proveedor->proveedor_estado=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection