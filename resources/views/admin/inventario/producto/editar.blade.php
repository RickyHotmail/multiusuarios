@extends ('admin.layouts.admin')
@section('principal')

<form class="form-horizontal" method="POST" action="{{ route('producto.update', [$producto->producto_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Producto</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--   
                <button type="button" onclick='window.location = "{{ url("producto") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="producto_codigo" class="col-sm-3 col-form-label">Codigo</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="producto_codigo" name="producto_codigo" value="{{$producto->producto_codigo}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="producto_nombre" class="col-sm-3 col-form-label">Porducto/Servicio</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="producto_nombre" name="producto_nombre" value="{{$producto->producto_nombre}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="producto_codigo_barras" class="col-sm-3 col-form-label">Codigo de Barras</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" maxlength="12" id="producto_codigo_barras" name="producto_codigo_barras" value="{{$producto->producto_codigo_barras}}" onkeyup="codigoBarras();" required>
                        </div>
                        <div class="col-sm-5"><img id="IdBarras" style="padding-top: 4px;" src="data:image/png;base64,{{DNS1D::getBarcodePNG($producto->producto_codigo_barras, 'C128')}}" alt="barcode" /></div>
                    </div>         
                    <div class="form-group row">
                        <label for="producto_tipo" class="col-sm-3 col-form-label">Tipo</label>
                        <div class="col-sm-9">
                            <select class="custom-select" id="producto_tipo" name="producto_tipo" onchange="tipoProdcuto()" required>
                                <option value="1" @if($producto->producto_tipo == '1') selected @endif>Articulo</option>
                                <option value="2" @if($producto->producto_tipo == '2') selected @endif>Servicio</option>                           
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="producto_precio_costo" class="col-sm-3 col-form-label">Costo</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="producto_precio_costo" name="producto_precio_costo" value="{{$producto->producto_precio_costo}}" step="any" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="producto_stock" class="col-sm-3 col-form-label">Stock</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="producto_stock" name="producto_stock" value="{{$producto->producto_stock}}" disabled>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="producto_stock_minimo" class="col-sm-3 col-form-label">Stock minimo</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="producto_stock_minimo" name="producto_stock_minimo" value="{{$producto->producto_stock_minimo}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="producto_stock_maximo" class="col-sm-3 col-form-label">Stock maximo</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="producto_stock_maximo" name="producto_stock_maximo" value="{{$producto->producto_stock_maximo}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="producto_fecha_ingreso" class="col-sm-3 col-form-label">Fecha Ingreso</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="producto_fecha_ingreso" name="producto_fecha_ingreso" value="{{$producto->producto_fecha_ingreso}}" required>
                        </div>
                    </div>                                         
                    <div class="form-group row">
                        <label for="producto_precio1" class="col-sm-3 col-form-label">Precio Venta</label>
                        <div class="col-sm-5">
                            <input type="number" class="form-control" id="producto_precio1" name="producto_precio1" placeholder="0.00" step="any" value="{{$producto->producto_precio1}}" required>
                        </div>
                        <label for="producto_estado" class="col-sm-2 col-form-label">Estado</label>
                        <div class="col-sm-2">
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success" style="padding-top: 8px;">
                                @if($producto->producto_estado=="1")
                                    <input type="checkbox" class="custom-control-input" id="producto_estado" name="producto_estado" checked>
                                @else
                                    <input type="checkbox" class="custom-control-input" id="producto_estado" name="producto_estado">
                                @endif
                                <label class="custom-control-label" for="producto_estado"></label>
                            </div>
                        </div>                
                    </div> 
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="sucursal_id" class="col-sm-3 col-form-label">Sucursal</label>
                        <div class="col-sm-9">
                            <select class="custom-select select2" id="sucursal_id" name="sucursal_id" require>
                                <option value="0">Todas</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->sucursal_id}}" @if(isset($producto->sucursal->sucursal_id)) @if($producto->sucursal_id == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                    <div class="form-group row @if($producto->producto_compra_venta == '1') invisible @endif" id='divVentas'>
                        <label for="producto_cuenta_venta" class="col-sm-3 col-form-label">Cuenta Venta</label>
                        <div class="col-sm-9">
                            <select class="custom-select select2" id="producto_cuenta_venta" name="producto_cuenta_venta">
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($cuentas as $cuenta)
                                    @if($cuenta->cuenta_id == $producto->producto_cuenta_venta)
                                        <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @else 
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                    
                    </div>
                    <div class="form-group row @if($producto->producto_compra_venta == '1' or $producto->producto_compra_venta == '2') invisible @endif" id='divInventario'>
                        <label for="producto_cuenta_inventario" class="col-sm-3 col-form-label">Cuenta Inventario</label>
                        <div class="col-sm-9">
                            <select class="custom-select select2" id="producto_cuenta_inventario" name="producto_cuenta_inventario">
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($cuentas as $cuenta)
                                    @if($cuenta->cuenta_id == $producto->producto_cuenta_inventario)
                                        <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @else 
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                    
                    </div>
                    <div class="form-group row @if($producto->producto_compra_venta == '2') invisible @endif" id='divGasto'>
                        <label for="producto_cuenta_gasto" class="col-sm-3 col-form-label">Cuenta Gasto o Costo</label>
                        <div class="col-sm-9">
                            <select class="custom-select select2" id="producto_cuenta_gasto" name="producto_cuenta_gasto">
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($cuentas as $cuenta)
                                    @if($cuenta->cuenta_id == $producto->producto_cuenta_gasto)
                                        <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @else 
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                    
                    </div>
                @endif  
                    <div class="form-group row">
                        <label for="producto_compra_venta" class="col-sm-3 col-form-label">Compra y Venta</label>
                        <div class="col-sm-9">
                            <select class="custom-select" id="producto_compra_venta" name="producto_compra_venta" onchange="tipoProdcuto()" required>
                                <option value="1" @if($producto->producto_compra_venta == '1') selected @endif>Compra</option>
                                <option value="2" @if($producto->producto_compra_venta == '2') selected @endif>Venta</option>
                                <option value="3" @if($producto->producto_compra_venta == '3') selected @endif>Compra/Venta</option>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="grupo_id" class="col-sm-3 col-form-label">Grupo</label>
                        <div class="col-sm-9">
                            <select class="custom-select select2" id="grupo_id" name="grupo_id" required>
                                <?php $esUnExamen=false?>
                                @foreach($grupos as $grupo)
                                    @if($grupo->grupo_id == $producto->grupo_id)
                                        <option value="{{$grupo->grupo_id}}" selected>{{$grupo->grupo_nombre}}</option>
                                    @else 
                                        <option value="{{$grupo->grupo_id}}">{{$grupo->grupo_nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                    
                    </div>

                    
                    <div class='form-group row' id="container-categoria">
                        <label for="categoria_id" class="col-sm-3 col-form-label">Categoria</label>
                        <div class="col-sm-9">
                            <select class="custom-select select2" id="categoria_id" name="categoria_id" required>
                                @foreach($categorias as $categoria)
                                    @if($categoria->categoria_id == $producto->categoria_id)
                                        <option value="{{$categoria->categoria_id}}" selected>{{$categoria->categoria_nombre}}</option>
                                    @else 
                                        <option value="{{$categoria->categoria_id}}">{{$categoria->categoria_nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="marca_id" class="col-sm-3 col-form-label">Marca</label>
                        <div class="col-sm-9">
                            <select class="custom-select select2" id="marca_id" name="marca_id" required>
                                @foreach($marcas as $marca)
                                    @if($marca->marca_id == $producto->marca_id)
                                        <option value="{{$marca->marca_id}}" selected>{{$marca->marca_nombre}}</option>
                                    @else 
                                        <option value="{{$marca->marca_id}}">{{$marca->marca_nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                    
                    </div>
                    <div class="form-group row">
                        <label for="unidad_medida_id" class="col-sm-3 col-form-label">Unidad de Medida</label>
                        <div class="col-sm-9">
                            <select class="custom-select select2" id="unidad_medida_id" name="unidad_medida_id" required>
                                @foreach($unidadMedidas as $unidadMedida)
                                    @if($unidadMedida->unidad_medida_id == $producto->unidad_medida_id)
                                        <option value="{{$unidadMedida->unidad_medida_id}}" selected>{{$unidadMedida->unidad_medida_nombre}}</option>
                                    @else 
                                        <option value="{{$unidadMedida->unidad_medida_id}}">{{$unidadMedida->unidad_medida_nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                    
                    </div>
                    <div class="form-group row">
                        <label for="tamano_id" class="col-sm-3 col-form-label">Tamano</label>
                        <div class="col-sm-9">
                            <select class="custom-select select2" id="tamano_id" name="tamano_id" required>
                                @foreach($tamanos as $tamano)
                                    @if($tamano->tamano_id == $producto->tamano_id)
                                        <option value="{{$tamano->tamano_id}}" selected>{{$tamano->tamano_nombre}}</option>
                                    @else 
                                        <option value="{{$tamano->tamano_id}}">{{$tamano->tamano_nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                    
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <div class="row">
                                <label for="producto_tiene_iva" class="col-sm-5 col-form-label">Tiene Iva</label>
                                <div class="col-sm-7">
                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success" style="padding-top: 8px;">
                                        @if($producto->producto_tiene_iva=="1")
                                            <input type="checkbox" class="custom-control-input" id="producto_tiene_iva" name="producto_tiene_iva" checked>
                                        @else
                                            <input type="checkbox" class="custom-control-input" id="producto_tiene_iva" name="producto_tiene_iva">
                                        @endif
                                        <label class="custom-control-label" for="producto_tiene_iva"></label>
                                    </div>
                                </div>  
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="row">
                                <label for="producto_tiene_descuento" class="col-sm-7 col-form-label">Tiene descuento</label>
                                <div class="col-sm-5">
                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success" style="padding-top: 8px;">
                                        @if($producto->producto_tiene_descuento=="1")
                                            <input type="checkbox" class="custom-control-input" id="producto_tiene_descuento" name="producto_tiene_descuento" checked>
                                        @else
                                            <input type="checkbox" class="custom-control-input" id="producto_tiene_descuento" name="producto_tiene_descuento">
                                        @endif
                                        <label class="custom-control-label" for="producto_tiene_descuento"></label>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="row">
                                <label for="producto_tiene_serie" class="col-sm-5 col-form-label">Tiene Serie</label>
                                <div class="col-sm-7">
                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success" style="padding-top: 8px;">
                                        @if($producto->producto_tiene_serie=="1")
                                            <input type="checkbox" class="custom-control-input" id="producto_tiene_serie" name="producto_tiene_serie" checked>
                                        @else
                                            <input type="checkbox" class="custom-control-input" id="producto_tiene_serie" name="producto_tiene_serie">
                                        @endif
                                        <label class="custom-control-label" for="producto_tiene_serie"></label>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        
                           
                    </div>
                    <div class="form-group row">
                        <label for="marca_id" class="col-sm-3 col-form-label">Producto Camronera</label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" id="customRadio1" name="producto_camaronero" value="1" @if($producto->producto_camaronero=="1") checked @endif>
                                <label for="customRadio1" class="custom-control-label">Directo</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" id="customRadio2" name="producto_camaronero" value="2" @if($producto->producto_camaronero=="2") checked @endif>
                                <label for="customRadio2" class="custom-control-label">Indirecto</label>
                            </div>
                        </div>    
                    </div>
            </div>   
        </div>         
    </div>
</form>
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
    function tipoProdcuto(){
        if(document.getElementById("producto_compra_venta").value == '3'){
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
        if(document.getElementById("producto_compra_venta").value == '2'){
            document.getElementById("divVentas").classList.add('invisible');
            document.getElementById("divInventario").classList.add('invisible');
            document.getElementById("divGasto").classList.add('invisible');

            document.getElementById("divVentas").classList.remove('invisible');
        }
        if(document.getElementById("producto_compra_venta").value == '1'){
            document.getElementById("divVentas").classList.add('invisible');
            document.getElementById("divInventario").classList.add('invisible');
            document.getElementById("divGasto").classList.add('invisible');

            document.getElementById("divGasto").classList.remove('invisible');
        }
    }
</script>
@endsection