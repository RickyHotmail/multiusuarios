@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Cliente</h3>
        <!--
        <button onclick='window.location = "{{ url("cliente") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        -->      
        <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>  
    </div>
    <!-- /.card-header -->
    <div class="card-body">
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Tipo de Identificacion</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$cliente->tipoIdentificacion->tipo_identificacion_nombre}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Cedula/Ruc/Pasaporte</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$cliente->cliente_cedula}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$cliente->cliente_nombre}}</label>
                </div>
            </div>  
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Tipo de Cliente</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$cliente->tipoCliente->tipo_cliente_nombre}}</label>                          
                </div>
            </div>
            @if($cliente->tipoCliente->tipo_cliente_nombre=='Aseguradora')
            <div class="form-group row" >
                <label class="col-sm-2 col-form-label">Siglas de seguro</label>
                <div class="col-sm-10">
                    <label class="form-control">  {{$cliente->cliente_abreviatura}}</label>
                </div>
            </div> 
            @endif 
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Direccion</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$cliente->cliente_direccion}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Telefono</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$cliente->cliente_telefono}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Celular</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$cliente->cliente_celular}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$cliente->cliente_fecha_ingreso}}</label>                          
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">LLeva Contabilidad</label>
                <div class="col-sm-10">
                    @if($cliente->cliente_lleva_contabilidad=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tiene Credito</label>
                <div class="col-sm-10">
                    @if($cliente->cliente_tiene_credito=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            @if($parametrizacionContable->parametrizacion_cuenta_general == '0')
                @if(Auth::user()->empresa->empresa_contabilidad == '1')
                    @if(isset($cliente->cuentaCobrar->cuenta_nombre))
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">Cuenta por Cobrar</label>
                            <div class="col-sm-10">                        
                                <label class="form-control">{{$cliente->cuentaCobrar->cuenta_numero.' - '.$cliente->cuentaCobrar->cuenta_nombre}}</label>                          
                            </div>
                        </div>
                    @endif
                    @if(isset($cliente->cuentaAnticipo->cuenta_nombre))
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">Cuenta de Anticipo</label>
                            <div class="col-sm-10">                        
                                <label class="form-control">{{$cliente->cuentaAnticipo->cuenta_numero.' - '.$cliente->cuentaAnticipo->cuenta_nombre}}</label>                          
                            </div>
                        </div>
                    @endif
                @endif
            @endif
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Ciudad</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$cliente->ciudad->ciudad_nombre}}</label>                          
                </div>
            </div> 
            <div class="form-group row">
                <label for="" class="col-sm-2 col-form-label">Categoria de Cliente</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$cliente->categoriaCliente->categoria_cliente_nombre}}</label>                          
                </div>
            </div>   
            @if(isset($cliente->listaPrecio->lista_id))
                <div class="form-group row">
                    <label for="" class="col-sm-2 col-form-label">Lista Precio</label>
                    <div class="col-sm-10">                        
                        <label class="form-control">{{$cliente->listaPrecio->lista_nombre}}</label>                          
                    </div>
                </div>
            @endif           
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($cliente->cliente_estado=="1")
                        <i class="fa fa-check-circle neo-verde"></i>
                    @else
                        <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<script type="text/javascript">
       
</script>
@endsection