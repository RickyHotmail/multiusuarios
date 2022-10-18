@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar este prodcuto?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('producto.destroy', [$producto->producto_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <!--   
                <button type="button" onclick='window.location = "{{ url("producto") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <label class="col-sm-3 col-form-label">Codigo</label>
                    <div class="col-sm-9">
                        <label class="form-control">{{$producto->producto_codigo}}</label>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-3 col-form-label">Porducto/Servicio</label>
                    <div class="col-sm-9">
                        <label class="form-control">{{$producto->producto_nombre}}</label>
                    </div>
                </div>  
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Codigo de Barras</label>
                    <div class="col-sm-4">                        
                        <label class="form-control">{{$producto->producto_codigo_barras}}</label>                          
                    </div>
                    <div class="col-sm-5"><img style="padding-top: 4px;" src="data:image/png;base64,{{DNS1D::getBarcodePNG($producto->producto_codigo_barras, 'C128')}}" alt="barcode" /></div>
                </div>
                <div class="row">
                    <label for="producto_tipo" class="col-sm-3 col-form-label">Tipo</label>
                    <div class="col-sm-9">
                        <label class="form-control">
                            @if($producto->producto_tipo == '1') Articulo @endif
                            @if($producto->producto_tipo == '2') Servicio @endif
                        </label>
                    </div>
                </div>
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Costo</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">{{$producto->producto_precio_costo}}</label>                          
                    </div>
                </div>
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Stock</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">{{$producto->producto_stock}}</label>                          
                    </div>
                </div>
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Stock minimo</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">{{$producto->producto_stock_minimo}}</label>                          
                    </div>
                </div>
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Stock maximo</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">{{$producto->producto_stock_maximo}}</label>                          
                    </div>
                </div>
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Fecha Ingreso</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">{{$producto->producto_fecha_ingreso}}</label>                          
                    </div>
                </div>                                         
                <div class="row">
                    <label for="producto_precio1" class="col-sm-3 col-form-label">Precio Venta</label>
                            <div class="col-sm-5">
                                <label class="form-control derecha-texto">{{number_format($producto->producto_precio1,2)}}</label>
                            </div>
                    <label class="col-sm-2 col-form-label">Estado</label>
                    <div class="col-sm-2">
                        @if($producto->producto_estado=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                        @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Cuenta de Venta</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">@if($producto->cuentaVenta) {{$producto->cuentaVenta->cuenta_numero.' - '.$producto->cuentaVenta->cuenta_nombre}} @else SIN CUENTA @endif</label>                          
                    </div>
                </div>
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Cuenta Inventario</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">@if($producto->cuentaInventario) {{$producto->cuentaInventario->cuenta_numero.' - '.$producto->cuentaInventario->cuenta_nombre}} @else SIN CUENTA @endif</label>                          
                    </div>
                </div>
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Cuenta Gasto</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">@if($producto->cuentaGasto) {{$producto->cuentaGasto->cuenta_numero.' - '.$producto->cuentaGasto->cuenta_nombre}} @else SIN CUENTA @endif</label>                          
                    </div>
                </div>
                @endif
                <div class="row">
                    <label for="producto_compra_venta" class="col-sm-3 col-form-label">Compra y Venta</label>
                    <div class="col-sm-9">
                        <label class="form-control">
                            @if($producto->producto_compra_venta == '1') Compra @endif
                            @if($producto->producto_compra_venta == '2') Venta @endif
                            @if($producto->producto_compra_venta == '3') Compra/Venta @endif
                        </label> 
                    </div>
                </div> 
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Grupo</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">{{$producto->grupo->grupo_nombre}}</label>                          
                    </div>
                </div> 
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Categoria</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">{{$producto->categoriaProducto->categoria_nombre}}</label>                          
                    </div>
                </div>
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Marca</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">{{$producto->marca->marca_nombre}}</label>                          
                    </div>
                </div>
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Unidad de Medida</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">{{$producto->unidadMedida->unidad_medida_nombre}}</label>                          
                    </div>
                </div>
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Tamaño</label>
                    <div class="col-sm-9">                        
                        <label class="form-control">{{$producto->tamano->tamano_nombre}}</label>                          
                    </div>
                </div> 
                <div class="row">
                    <div class="col-sm-4">
                        <div class="row">
                            <label for="producto_tiene_iva" class="col-sm-5 col-form-label">Tiene Iva</label>
                            <div class="col-sm-7">
                                @if($producto->producto_tiene_iva=="1")
                                    <i class="fa fa-check-circle neo-verde" style="padding-top: 12px; padding-bottom: 20px;"></i>
                                @else
                                    <i class="fa fa-times-circle neo-rojo" style="padding-top: 12px; padding-bottom: 20px;"></i>
                                @endif
                            </div>  
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="row">
                            <label for="producto_tiene_descuento" class="col-sm-7 col-form-label">Tiene descuento</label>
                            <div class="col-sm-5">
                                @if($producto->producto_tiene_descuento=="1")
                                    <i class="fa fa-check-circle neo-verde" style="padding-top: 12px; padding-bottom: 20px;"></i>
                                @else
                                    <i class="fa fa-times-circle neo-rojo" style="padding-top: 12px; padding-bottom: 20px;"></i>
                                @endif
                            </div> 
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="row">
                            <label for="producto_tiene_serie" class="col-sm-5 col-form-label">Tiene Serie</label>
                            <div class="col-sm-7">
                                @if($producto->producto_tiene_serie=="1")
                                    <i class="fa fa-check-circle neo-verde" style="padding-top: 12px; padding-bottom: 20px;"></i>
                                @else
                                    <i class="fa fa-times-circle neo-rojo" style="padding-top: 12px; padding-bottom: 20px;"></i>
                                @endif
                            </div>
                        </div>
                    </div>   
                   
                     
                </div>
                <div class="row">
                    <label for="" class="col-sm-3 col-form-label">Producto Camronera</label>
                    <div class="col-sm-9">    
                        @if($producto->producto_camaronero=="1")
                            <label class="form-control">Directo</label>   
                        @endif             
                        @if($producto->producto_camaronero=="2")
                        <label class="form-control">Indirecto</label>         
                        @endif                    
                                    
                    </div>
                </div>   
            </div>
        </div>
    </div>
</div>
<!-- /.card -->
@endsection