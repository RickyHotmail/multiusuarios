@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Producto</h3>
        <div class="float-right">
            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
            <a class="btn btn-info btn-sm" href="{{ asset('admin/archivos/FORMATO_PRODUCTOS.xlsx') }}" download="FORMATO PRODUCTOS"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
            <a class="btn btn-success btn-sm" href="{{ url("excelProducto") }}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">    
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary card-tabs">          
            <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="custom-content-below-home-tab" data-toggle="pill" href="#custom-content-below-home" role="tab" aria-controls="custom-content-below-home" aria-selected="true"><b>PRODUCTOS DE VENTA</b></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="custom-content-below-profile-tab" data-toggle="pill" href="#custom-content-below-profile" role="tab" aria-controls="custom-content-below-profile" aria-selected="false"><b>PRODUCTOS DE GASTO</b></a>
              </li>              
            </ul>
              <div class="card-body">
              <div class="tab-content" id="custom-content-below-tabContent">
              <div class="tab-pane fade show active" id="custom-content-below-home" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
              <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
                        <thead>
                        <tr class="text-center neo-fondo-tabla">
                        <th></th>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Costo</th>
                        <th>Precio PVP</th>
                        <th>Stock</th>
                        <th>Stock minimo</th>
                        <th>Stock maximo</th>
                        <th>Fecha Ingreso</th>
                        <th>Iva</th>
                        <th>Descuento</th>
                        <th>Serie</th>
                        <th>Compra Venta</th>
                        <th>Grupo</th>
                        <th>Categoria</th>
                        <th>Marca</th>
                        <th>Unidad Medida</th>
                        <th>Tamaño</th>
                        @if(Auth::user()->empresa->empresa_contabilidad == '1')  
                        <th>Venta</th>
                        <th>Inventario</th>                                        
                        <th>Gasto</th>
                        @endif
                        <th>Codigo Barras</th>
                        <th>Sucursal</th>
                    </tr>
                </thead> 
                <tbody>
                    @foreach($productos as $producto)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("producto/codigo/{$producto->producto_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Configurar Codigos"><i class="fa fa-cog" aria-hidden="true"></i></a>
                            <a href="{{ url("producto/precio/{$producto->producto_id}") }}" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Configurar precios"><i class="fa fa-tasks" aria-hidden="true"></i></a>
                            <a href="{{ url("producto/{$producto->producto_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <a href="{{ url("producto/{$producto->producto_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a href="{{ url("producto/{$producto->producto_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                        <td>{{ $producto->producto_codigo}}</td> 
                        <td>{{ $producto->producto_nombre}}</td>
                        <td>
                            @if($producto->producto_tipo =='1')ARTICULO 
                            @elseif ($producto->producto_tipo =='2')SERVICIO                            
                            @endif
                        </td>
                        <td>{{ $producto->producto_precio_costo}}</td>
                        @if(isset($producto->producto_precio1))
                            <td>{{ $producto->producto_precio1}}</td>
                        @else
                            <td></td>
                        @endif
                        <td>{{ $producto->producto_stock}}</td>                    
                        <td>{{ $producto->producto_stock_minimo}}</td>                    
                        <td>{{ $producto->producto_stock_maximo}}</td>
                        <td>{{ $producto->producto_fecha_ingreso}}</td>  
                        <td>
                            @if($producto->producto_tiene_iva=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                            @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>
                            @if($producto->producto_tiene_descuento=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>              
                        <td>
                            @if($producto->producto_tiene_serie=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td> 
                        <td>
                            @if($producto->producto_compra_venta =='1')COMPRA 
                            @elseif ($producto->producto_compra_venta =='2')VENTA 
                            @elseif ($producto->producto_compra_venta =='3')COMPRA/VENTA  
                            @endif
                        </td>   
                        <td>{{ $producto->grupo->grupo_nombre}}</td>
                        <td>{{ $producto->categoriaProducto->categoria_nombre}}</td>                    
                        <td>{{ $producto->marca->marca_nombre}}</td>                    
                        <td>{{ $producto->unidadMedida->unidad_medida_nombre}}</td>                    
                        <td>{{ $producto->tamano->tamano_nombre}}</td>
                                    
                        @if(Auth::user()->empresa->empresa_contabilidad == '1')  
                        <td>@if($producto->cuentaVenta) {{ $producto->cuentaVenta->cuenta_numero.' - '.$producto->cuentaVenta->cuenta_nombre}} @else SIN CUENTA @endif</td>         
                        <td>@if($producto->cuentaInventario) {{ $producto->cuentaInventario->cuenta_numero.' - '.$producto->cuentaInventario->cuenta_nombre}} @else SIN CUENTA @endif</td>                                                                        
                        <td>@if($producto->cuentaGasto) {{ $producto->cuentaGasto->cuenta_numero.' - '.$producto->cuentaGasto->cuenta_nombre}} @else SIN CUENTA @endif</td>                                      
                        @endif
                        <td><img style="padding-top: 4px; height: 20px;" src="data:image/png;base64,{{DNS1D::getBarcodePNG($producto->producto_codigo_barras, 'C128')}}" alt="barcode" /></td>                    
                        <td>@if(isset($producto->sucursal->sucursal_id)) {{ $producto->sucursal->sucursal_nombre }} @else Todas @endif</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
              <div class="tab-pane fade" id="custom-content-below-profile" role="tabpanel" aria-labelledby="custom-content-below-profile-tab">
              <table id="example2" class="table table-bordered table-hover table-responsive sin-salto">
                        <thead>
                        <tr class="text-center neo-fondo-tabla">
                        <th></th>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Costo</th>
                        <th>Precio PVP</th>
                        <th>Stock</th>
                        <th>Stock minimo</th>
                        <th>Stock maximo</th>
                        <th>Fecha Ingreso</th>
                        <th>Iva</th>
                        <th>Descuento</th>
                        <th>Serie</th>
                        <th>Compra Venta</th>
                        <th>Grupo</th>
                        <th>Categoria</th>
                        <th>Marca</th>
                        <th>Unidad Medida</th>
                        <th>Tamaño</th>
                        @if(Auth::user()->empresa->empresa_contabilidad == '1')  
                        <th>Venta</th>
                        <th>Inventario</th>                                        
                        <th>Gasto</th>
                        @endif
                        <th>Codigo Barras</th>
                        <th>Sucursal</th>
                    </tr>
                </thead> 
                <tbody>
                    @foreach($productosGastos as $productosGasto)
                    <tr class="text-center">
                        <td>
                            <a href="{{ url("producto/codigo/{$productosGasto->producto_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Configurar Codigos"><i class="fa fa-cog" aria-hidden="true"></i></a>       
                            <a href="{{ url("producto/precio/{$productosGasto->producto_id}") }}" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Configurar precios"><i class="fa fa-tasks" aria-hidden="true"></i></a>
                            <a href="{{ url("producto/{$productosGasto->producto_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            <a href="{{ url("producto/{$productosGasto->producto_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a href="{{ url("producto/{$productosGasto->producto_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                        <td>{{ $productosGasto->producto_codigo}}</td> 
                        <td>{{ $productosGasto->producto_nombre}}</td>
                        <td>
                            @if($productosGasto->producto_tipo =='1')ARTICULO 
                            @elseif ($productosGasto->producto_tipo =='2')SERVICIO                            
                            @endif
                        </td>
                        <td>{{ $productosGasto->producto_precio_costo}}</td>
                        @if(isset($productosGasto->producto_precio1))
                            <td>{{ $productosGasto->producto_precio1}}</td>
                        @else
                            <td></td>
                        @endif
                        <td>{{ $productosGasto->producto_stock}}</td>                    
                        <td>{{ $productosGasto->producto_stock_minimo}}</td>                    
                        <td>{{ $productosGasto->producto_stock_maximo}}</td>
                        <td>{{ $productosGasto->producto_fecha_ingreso}}</td>  
                        <td>
                            @if($productosGasto->producto_tiene_iva=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                            @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>
                        <td>
                            @if($productosGasto->producto_tiene_descuento=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td>              
                        <td>
                            @if($productosGasto->producto_tiene_serie=="1")
                                <i class="fa fa-check-circle neo-verde"></i>
                                @else
                                <i class="fa fa-times-circle neo-rojo"></i>
                            @endif
                        </td> 
                        <td>
                            @if($productosGasto->producto_compra_venta =='1')COMPRA 
                            @elseif ($productosGasto->producto_compra_venta =='2')VENTA 
                            @elseif ($productosGasto->producto_compra_venta =='3')COMPRA/VENTA  
                            @endif
                        </td>   
                        <td>{{ $productosGasto->grupo->grupo_nombre}}</td>
                        <td>{{ $productosGasto->categoriaProducto->categoria_nombre}}</td>                    
                        <td>{{ $productosGasto->marca->marca_nombre}}</td>                    
                        <td>{{ $productosGasto->unidadMedida->unidad_medida_nombre}}</td>                    
                        <td>{{ $productosGasto->tamano->tamano_nombre}}</td>
                                    
                        @if(Auth::user()->empresa->empresa_contabilidad == '1')  
                        <td>@if($productosGasto->cuentaVenta) {{ $productosGasto->cuentaVenta->cuenta_numero.' - '.$productosGasto->cuentaVenta->cuenta_nombre}} @else SIN CUENTA @endif</td>         
                        <td>@if($productosGasto->cuentaInventario) {{ $productosGasto->cuentaInventario->cuenta_numero.' - '.$productosGasto->cuentaInventario->cuenta_nombre}} @else SIN CUENTA @endif</td>                                                                        
                        <td>@if($productosGasto->cuentaGasto) {{ $productosGasto->cuentaGasto->cuenta_numero.' - '.$productosGasto->cuentaGasto->cuenta_nombre}} @else SIN CUENTA @endif</td>                                      
                        @endif
                        <td><img style="padding-top: 4px; height: 20px;" src="data:image/png;base64,{{DNS1D::getBarcodePNG($producto->producto_codigo_barras, 'C128')}}" alt="barcode" /></td>                    
                        <td>@if(isset($productosGasto->sucursal->sucursal_id)) {{ $productosGasto->sucursal->sucursal_nombre }} @else Todas @endif</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>              
            </div>                
              </div>
              <!-- /.card -->
            </div>
          </div>
        </div>        
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("producto") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">                        
                        <div class="form-group row">
                            <label for="producto_codigo" class="col-sm-3 col-form-label">Codigo</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="producto_codigo" name="producto_codigo" placeholder="Codigo" maxlength="25" required>
                            </div>
                            <div class=" col-sm-1">
                                <div class="icheck-success d-inline">
                                    <input type="checkbox" id="idCodigo" name="idCodigo" onchange="codigo();">
                                    <label for="idCodigo"></label>
                                </div>
                            </div>
                            <label for="idCodigo" class="col-sm-3 col-form-label">Codigo Automatico</label>
                        </div>
                        <div class="form-group row">
                            <label for="producto_nombre" class="col-sm-3 col-form-label">Porducto/Servicio</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="producto_nombre" name="producto_nombre" placeholder="Nombre" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="producto_codigo_barras" class="col-sm-3 col-form-label">Codigo Barras</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" maxlength="12" id="producto_codigo_barras" name="producto_codigo_barras" placeholder="Codigo Barras" onkeyup="codigoBarras();" value="0" required>
                            </div>
                            <div class="col-sm-4"><img id="IdBarras" style="padding-top: 4px;"/></div>
                        </div>
                        <div class="form-group row">
                            <label for="producto_tipo" class="col-sm-3 col-form-label">Tipo</label>
                            <div class="col-sm-9">
                                <select class="custom-select" id="producto_tipo" name="producto_tipo" onchange="tipoProdcuto()" required>
                                    <option value="1">Articulo</option>
                                    <option value="2">Servicio</option>                                    
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="producto_precio_costo" class="col-sm-3 col-form-label">Costo</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="producto_precio_costo" name="producto_precio_costo" placeholder="0.00" value="0.00" step="any" required>
                            </div>
                        </div>                 
                        <div class="form-group row">
                            <label for="producto_stock_minimo" class="col-sm-3 col-form-label">Stock Minimo</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="producto_stock_minimo" name="producto_stock_minimo" placeholder="0" value="0" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="producto_stock_maximo" class="col-sm-3 col-form-label">Stock Maximo</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="producto_stock_maximo" name="producto_stock_maximo" placeholder="0" value="0" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="producto_fecha_ingreso" class="col-sm-3 col-form-label">Fecha Ingreso</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="producto_fecha_ingreso" name="producto_fecha_ingreso" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="producto_tiene_iva" class="col-sm-3 col-form-label">Tine Iva</label>
                            <div class="col-sm-9">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="producto_tiene_iva" name="producto_tiene_iva">
                                    <label class="custom-control-label" for="producto_tiene_iva"></label>
                                </div>
                            </div>                
                        </div>
                        <div class="form-group row">
                            <label for="producto_tiene_iva" class="col-sm-3 col-form-label">Tiene Descuento</label>
                            <div class="col-sm-9">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="producto_tiene_descuento" name="producto_tiene_descuento">
                                    <label class="custom-control-label" for="producto_tiene_descuento"></label>
                                </div>
                            </div>                
                        </div>                       
                        <div class="form-group row">
                            <label for="producto_tiene_serie" class="col-sm-3 col-form-label">Tiene Serie</label>
                            <div class="col-sm-3">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">                                   
                                    <input type="checkbox" class="custom-control-input" id="producto_tiene_serie" name="producto_tiene_serie">                                    
                                    <label class="custom-control-label" for="producto_tiene_serie"></label>
                               </div>
                            </div> 
                        </div>
                        <div class="form-group row">
                            <label for="idCompraventa" class="col-sm-3 col-form-label">Compra y Venta</label>
                            <div class="col-sm-9">
                                <select class="custom-select" id="idCompraventa" name="idCompraventa" onchange="tipoProdcuto()" require>
                                    <option value="1">Compra</option>
                                    <option value="2">Venta</option>
                                    <option value="3" selected>Compra/Venta</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="grupo_id" class="col-sm-3 col-form-label">Grupo</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="grupo_id" name="grupo_id" require>
                                    @foreach($grupos as $grupo)
                                        <option value="{{$grupo->grupo_id}}">{{$grupo->grupo_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="categoria_id" class="col-sm-3 col-form-label">Categoria</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="categoria_id" name="categoria_id" onchange="codigo();" require>
                                    @foreach($categorias as $categoria)
                                        <option value="{{$categoria->categoria_id}}">{{$categoria->categoria_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="marca_id" class="col-sm-3 col-form-label">Marca</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="marca_id" name="marca_id" require>
                                    @foreach($marcas as $marca)
                                        <option value="{{$marca->marca_id}}">{{$marca->marca_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="unidad_medida_id" class="col-sm-3 col-form-label">Unidad Medida</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="unidad_medida_id" name="unidad_medida_id" require>
                                    @foreach($unidadMedidas as $unidadMedida)
                                        <option value="{{$unidadMedida->unidad_medida_id}}">{{$unidadMedida->unidad_medida_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tamano_id" class="col-sm-3 col-form-label">Tamaño</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="tamano_id" name="tamano_id" require>
                                    @foreach($tamanos as $tamano)
                                        <option value="{{$tamano->tamano_id}}">{{$tamano->tamano_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                        <div class="form-group row" id='divVentas'>
                            <label for="producto_cuenta_venta" class="col-sm-3 col-form-label">Cuenta de Venta</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="producto_cuenta_venta" name="producto_cuenta_venta" >
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id='divInventario'>
                            <label for="producto_cuenta_inventario" class="col-sm-3 col-form-label">Cuenta de Inventario</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="producto_cuenta_inventario" name="producto_cuenta_inventario" >
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id='divGasto'>
                            <label for="producto_cuenta_gasto" class="col-sm-3 col-form-label">Cuenta de Gasto o Costo</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="producto_cuenta_gasto" name="producto_cuenta_gasto">
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label for="sucursal_id" class="col-sm-3 col-form-label">Sucursal</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="sucursal_id" name="sucursal_id" require>
                                    <option value="0">Todas</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="producto_precio1" class="col-sm-3 col-form-label">Precio Venta</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="producto_precio1" name="producto_precio1" placeholder="0.000" step="any" min="0" value="0.000" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="sucursal_id" class="col-sm-3 col-form-label">Producto Camronera</label>
                            <div class="col-sm-9">
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="customRadio1" name="producto_camaronero" value="1">
                                    <label for="customRadio1" class="custom-control-label">Directo</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="customRadio2" name="producto_camaronero" value="2">
                                    <label for="customRadio2" class="custom-control-label">Indirecto</label>
                                </div>
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
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
<script type="text/javascript">
    function codigoBarras(){ 
        JsBarcode("#IdBarras", document.getElementById("producto_codigo_barras").value,
        {
            format: "CODE128",// El formato
            width: 2, // La anchura de cada barra
            height: 30, // La altura del código
            displayValue: false, // ¿Mostrar el valor (como texto) del código de barras?
            lineColor: "#000000", // Color de cada barra
            margin: 0,
            marginTop: 0, // Margen superior
        });
    }
    function codigo(){
      
        if(document.getElementById("idCodigo").checked==true){
            document.getElementById("producto_codigo").readOnly =true;
            var combo = document.getElementById("categoria_id");
            var ale=combo.options[combo.selectedIndex].text;
            
            ale=ale.substr(0,3);
            $.ajax({
                url: '{{ url("categoria/searchN") }}'+ '/' + document.getElementById("categoria_id").value,
                dataType: "json",
                type: "GET",
                data: {
                    buscar: document.getElementById("categoria_id").value
                },
                success: function(data){
                    document.getElementById("producto_codigo").value=(data[0]);      
                },
            });
        }
        else{
            document.getElementById("producto_codigo").readOnly =false;          
        }
       
    }
    function tipoProdcuto(){
        if(document.getElementById("idCompraventa").value == '3'){
            if(document.getElementById("producto_tipo").value == 1){
                document.getElementById("divVentas").classList.add('invisible');
                document.getElementById("divInventario").classList.add('invisible');
                document.getElementById("divGasto").classList.add('invisible');

                document.getElementById("divVentas").classList.remove('invisible');
                document.getElementById("divInventario").classList.remove('invisible');
                document.getElementById("divGasto").classList.remove('invisible');
            }else{
                document.getElementById("divVentas").classList.add('invisible');
                document.getElementById("divInventario").classList.add('invisible');
                document.getElementById("divGasto").classList.add('invisible');

                document.getElementById("divVentas").classList.remove('invisible');
            }
            
        }
        if(document.getElementById("idCompraventa").value == '2'){
            document.getElementById("divVentas").classList.add('invisible');
            document.getElementById("divInventario").classList.add('invisible');
            document.getElementById("divGasto").classList.add('invisible');

            document.getElementById("divVentas").classList.remove('invisible');
        }
        if(document.getElementById("idCompraventa").value == '1'){
            document.getElementById("divVentas").classList.add('invisible');
            document.getElementById("divInventario").classList.add('invisible');
            document.getElementById("divGasto").classList.add('invisible');

            document.getElementById("divGasto").classList.remove('invisible');
        }
    }
</script>
@endsection