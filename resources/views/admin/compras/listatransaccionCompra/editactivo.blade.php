@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST" action="{{ route('transaccionCActivoFijo.update', [$compras->transaccion_id]) }}" onsubmit="return validarForm();">
        @method('PUT')
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>TRANSACCIÓN COMPRAS - Documentos</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                         <!--     
                        <button type="button"  onclick='window.location = "{{ url("listatransaccionCompra") }}";'  class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
                        --> 
                        <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                    
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Doc.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                <input type="date" class="form-control" id="transaccion_fecha"
                                        name="transaccion_fecha"
                                        value='{{$compras->transaccion_fecha}}' required>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Inv.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                <input type="date" class="form-control" id="transaccion_inventario"
                                        name="transaccion_inventario"
                                        value='{{$compras->transaccion_inventario}}' required>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Ven.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                <input type="date" class="form-control" id="transaccion_vencimiento"
                                        name="transaccion_vencimiento"
                                        value='{{$compras->transaccion_vencimiento}}' required>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Imp.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                <input type="date" class="form-control" id="transaccion_impresion"
                                        name="transaccion_impresion"
                                        value='{{$compras->transaccion_impresion}}' required>
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Proveedor</label>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                            <div class="form-group">
                            <label class="form-control" >{{$compras->proveedor->proveedor_nombre}}</label> 
                            <input type="hidden" id="proveedorID" name="proveedorID" value="{{$compras->proveedor->proveedor_id}}">
                            <input id="buscarProveedor" name="buscarProveedor" type="hidden" class="form-control"
                                    placeholder="Proveedor" value="{{$compras->proveedor->proveedor_nombre}}">    
                        </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>RUC/CI</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                            <label class="form-control" >{{$compras->proveedor->proveedor_ruc}}</label> 
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Cadu.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                <input type="date" class="form-control" id="transaccion_caducidad"
                                        name="transaccion_caducidad"
                                        value='{{$compras->transaccion_caducidad}}' required>
                               
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Comprobante</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="tipo_comprobante_id" name="tipo_comprobante_id"
                                    data-live-search="true">
                                    @foreach($comprobantes as $comprobante)
                                    <option value="{{$comprobante->tipo_comprobante_id}}">
                                        {{$comprobante->tipo_comprobante_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="hidden" value="{{$compras->tipoComprobante->tipo_comprobante_nombre}}" >
                               
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Transaccion</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                            <select id="transaccion_descripcion" name="transaccion_descripcion"
                                    class="form-control custom-select" data-live-search="true">
                                    <option value="COMPRA">COMPRA</option>
                                    <option value="DESCUENTO">DESCUENTO</option>
                                    <option value="DEVOLUCION DE PRODUCTO">DEVOLUCION DE PRODUCTO</option>
                                </select>
                            
                                <input type="hidden" value="{{$compras->transaccion_descripcion}}" >
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Forma Pago</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                            <select id="transaccion_tipo_pago" name="transaccion_tipo_pago" 
                                    class="form-control custom-select" data-live-search="true">
                                    <option value="CONTADO">CONTADO</option>
                                    <option value="CREDITO">CREDITO</option>
                                    <option value="EN EFECTIVO">EN EFECTIVO</option>
                                </select>
                            
                            <input type="hidden" value="{{$compras->transaccion_tipo_pago}}" >
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>No. Serie</label>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                <input type="text" id="transaccion_serie" name="transaccion_serie"
                                        class="form-control " placeholder="001001" value="{{$compras->transaccion_serie}}" required>
                                
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>No. Docu.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                <input type="text" id="transaccion_secuencial" name="transaccion_secuencial"
                                        class="form-control " placeholder="0000000001" value="{{substr(str_repeat(0, 9). $compras->transaccion_numero , - 9)}}" required>
                                
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Autorizacion</label>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                <input type="text" id="transaccion_autorizacion" name="transaccion_autorizacion"
                                        class="form-control " value="{{$compras->transaccion_autorizacion}}"
                                        placeholder="1702201205176001321000110010030001000011234567816" required>
                              
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>% IVA </label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                            <label class="form-control" >{{$compras->transaccion_porcentaje_iva}} %</label>
                            <input type="hidden" id="transaccion_porcentaje_iva" name="transaccion_porcentaje_iva"
                                   value="{{$compras->transaccion_porcentaje_iva}}">
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Dias de Plazo</label>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="margin-bottom : 0px;">
                            <div class="form-group">
                            <input type="number" id="transaccion_dias_plazo" name="transaccion_dias_plazo"
                                    class="form-control " value="{{$compras->transaccion_dias_plazo}}" required>
                           
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Sustento</label>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                            <div class="form-group">
                            <select class="form-control select2" id="sustento_id" name="sustento_id" 
                                    data-live-search="true">
                                    @foreach($sustentos as $sustento)
                                    <option value="{{$sustento->sustento_id}}">{{$sustento->sustento_codigo .' - '. $sustento->sustento_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                     
                               
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label "
                            style="margin-bottom : 0px;">
                            <label>Tipo Activo</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <select id="activo_id" name="activo_id" class="form-control show-tick select2" data-live-search="true">
                                    @foreach($activos as $activo)
                                        <option value="{{ $activo->grupo_id }}" @if($activo->grupo_id==$compras->activo->grupo_id) selected @endif>{{ $activo->grupo_nombre }}</option>   
                                    @endforeach    
                                    </select>
                                </div>
                            </div>
                        </div>      
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>% Vida Util</label>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="number" id="idVidaUtil" name="idVidaUtil"
                                    class="form-control" value="{{$compras->activo->activo_vida_util}}" required>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Descripcion </label>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" id="idDescripcion" name="idDescripcion"
                                    class="form-control " value="{{$compras->activo->activo_descripcion}}" required>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row" style="background: #dadada; padding-top: 20px;margin-top: 5px;">
                        <div class="col-sm-4" style="margin-bottom: 0px;">
                            <label>Nombre de Producto</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="codigoProducto" name="idProducto" type="hidden">
                                    <input id="idProductoID" name="idProductoID" type="hidden">
                                    <input id="tipoProductoID" name="tipoProductoID" type="hidden">
                                    <input id="buscarProducto" name="buscarProducto" type="text" class="form-control" placeholder="Buscar producto" >
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label></label></center>
                            <center>
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <div class="custom-control custom-checkbox">
                                        <input id="tieneIva" name="tieneIva" type="checkbox"
                                            class="custom-control-input" disabled />
                                        <label for="tieneIva" class="custom-control-label">Iva</label>
                                    </div>
                                </div>
                            </center>
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 0px;">
                            <center><label>Cantidad</label></center>
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_cantidad"
                                        name="id_cantidad" type="number" class="form-control" placeholder="0" value="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <center><label>Precio</label></center>
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_pu" name="id_pu"
                                        type="text" class="form-control centrar-texto" placeholder="Precio" value="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <center><label>Desc. %</label></center>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_descuento" name="id_descuento" type="text" class="form-control centrar-texto"
                                        placeholder="Descuento" value="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <center><label>Total</label></center>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_total" name="id_total" type="text" class="form-control centrar-texto""
                                        placeholder="Total" value="0.00" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="background: #dadada;">
                        <div class="col-sm-4" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <input id="descripcionProducto" name="descripcionProducto" type="text"
                                    class="form-control" placeholder="Descripcion">
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Bodega</label></center>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="bodegaProducto" name="bodegaProducto"
                                    data-live-search="true">
                                    @foreach($bodegas as $bodega)
                                    <option value="{{$bodega->bodega_id}}">{{$bodega->bodega_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>C. Consumo</label></center>
                        </div>
                        <div class="col-sm-3" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="ccProducto" name="ccProducto"
                                    data-live-search="true">
                                    @foreach($centros as $centro)
                                    <option value="{{$centro->centro_consumo_id}}">{{$centro->centro_consumo_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                            <center><a onclick="agregarItem();" class="btn btn-primary"><i class="fas fa-plus"></i></a>
                            </center>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                            @include ('admin.compras.listatransaccionCompra.items')
                                <table id="cargarItem"
                                    class="table table-striped table-hover boder-sar tabla-item-factura sin-salto"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                          
                                            <th width="40"></th>
                                            <th width="90">Cantidad</th>
                                            <th width="120">Codigo</th>
                                            <th width="250">Producto</th>
                                            <th width="75">Con Iva</th>
                                            <th width="100">Iva</th>
                                            <th width="100">P.U.</th>
                                            <th width="100">Descuento</th>
                                            <th width="100">Total</th>
                                        
                                            <th>Bodega</th>
                                            <th>C. Consumo</th>
                                            <th>Descripcion</th>
                                            <th>Bien/Serv.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                   
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                            <div class="card card-primary card-outline card-tabs">
                                <div class="card-header p-0 pt-1 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist"
                                        style="border-bottom: 1px solid #c3c4c5;">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-retencion-tab"
                                                data-toggle="pill" href="#custom-tabs-four-retencion" role="tab"
                                                aria-controls="custom-tabs-four-retencion" aria-selected="true"><b>No.
                                                    Retencion</b></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-fuente-tab" data-toggle="pill"
                                                href="#custom-tabs-four-fuente" role="tab"
                                                aria-controls="custom-tabs-four-fuente"
                                                aria-selected="false"><b>Retencion Fuente</b></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-iva-tab" data-toggle="pill"
                                                href="#custom-tabs-four-iva" role="tab"
                                                aria-controls="custom-tabs-four-iva" aria-selected="false"><b>Retencion
                                                    Iva</b></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-factura-tab" data-toggle="pill"
                                                href="#custom-tabs-four-factura" role="tab"
                                                aria-controls="custom-tabs-four-factura"
                                                aria-selected="false"><b>Factura</b></a>
                                        </li>
                                    </ul>
                                    
                                </div>
                               
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-retencion"
                                            role="tabpanel" aria-labelledby="custom-tabs-four-retencion-tab">
                                            <div class="row">
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"
                                                    style="margin-bottom : 0px;">
                                                    <div class="demo-checkbox">
                                                        <input type="radio" value="ELECTRONICA" id="check1"
                                                            class="with-gap radio-col-deep-orange" name="tipoDoc" disabled
                                                            @if(isset($compras->retencionCompra))@if($compras->retencionCompra->retencion_emision=='ELECTRONICA') checked @endif @endif required />
                                                        <label for="check1">Documento Electronico</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"
                                                    style="margin-bottom : 0px;">
                                                    <div class="demo-checkbox">
                                                        <input type="radio" value="FISICA" id="check2"
                                                            class="with-gap radio-col-deep-orange" name="tipoDoc" disabled
                                                            @if(isset($compras->retencionCompra)) @if($compras->retencionCompra->retencion_emision=='FISICO') checked @endif @endif required />
                                                        <label for="check2">Documento Fisico</label>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"
                                                    style="margin-bottom : 0px;">
                                                  
                  
                                                   
                                                  
                                                    
                 
                                                    <div class="form-group clearfix float-right">
                                                        <div class="form-group clearfix">
                                                            <div class="icheck-primary d-inline">
                                                                <input type="checkbox" id="checkboxPrimary1" name="editret" onchange="editaretencion();" >
                                                                <label for="checkboxPrimary1">Editar Retencion
                                                                </label>
                                                            </div>
                                                            <input type="hidden" id="idretencion" name="idretencion" @if(isset($compras->retencionCompra->retencion_id)) value="{{$compras->retencionCompra->retencion_id}}" @endif>
                                                            <input type="hidden" id="fecharetencion" name="fecharetencion" @if(isset($compras->retencionCompra->retencion_id)) value="{{$compras->retencionCompra->retencion_fecha}}" @endif>
                                                            <input type="hidden" id="codigoretencion" name="codigoretencion" @if(isset($compras->retencionCompra->retencion_id)) value="{{substr(str_repeat(0, 9). $compras->retencionCompra->retencion_numero , - 9)}}" @endif>
                                                            <input type="hidden" id="serieretencion" name="serieretencion" @if(isset($compras->retencionCompra->retencion_id)) value="{{$compras->retencionCompra->retencion_serie}}" @endif>
                                                            <input type="hidden" id="docretencion" name="docretencion" @if(isset($compras->retencionCompra->retencion_id)) value="@if($compras->retencionCompra->retencion_emision=='ELECTRONICA') ELECTRONICA @else FISICO @endif" @endif>
                                                        </div>
                                                    </div>
                                                </div>                 
                                            </div><br>
                                            <div class="row">
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <center><label>Fecha Retencion</label></center>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="retencion_fecha" name="retencion_fecha"
                                                                type="date" class="form-control"
                                                               value="@if(isset($compras->retencionCompra)){{$compras->retencionCompra->retencion_fecha}}@endif" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <center><label>Serie</label></center>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                          <input id="punto_id" name="punto_id"
                                                                value=""
                                                                type="hidden">
                                                            <input id="rango_id" name="rango_id"
                                                                value="" type="hidden">
                                                            <input id="retencion_id" name="retencion_id"
                                                                value="$compras->retencionCompra->retencion_id" type="hidden">
                                                            <input id="retencion_serie" name="retencion_serie"
                                                                type="text" class="form-control"
                                                                value="@if(isset($compras->retencionCompra)){{$compras->retencionCompra->retencion_serie}}@endif" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <center><label>Numero</label></center>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="retencion_secuencial" name="retencion_secuencial"
                                                                type="text" class="form-control"
                                                                value="@if(isset($compras->retencionCompra)){{substr(str_repeat(0, 9). $compras->retencionCompra->retencion_numero , - 9)}}@endif" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                           
                                           
                                        </div>
                                       
                                        <div class="tab-pane fade" id="custom-tabs-four-fuente" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-fuente-tab">
                                            <div class="row">
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Base</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <input id="baseFuente" name="baseFuente" type="text"
                                                            class="form-control" placeholder="0.00" value="0.00" onkeyup="calcularRF();" onclick="calcularRF();"
                                                            >
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="conceptoFuenteID"
                                                            name="conceptoFuenteID" data-live-search="true"
                                                            onchange="calcularRF()"> 
                                                            <option value="" label>--Seleccione una opcion--</option>
                                                            @foreach($conceptosFuente as $concepto)
                                                            <option value="{{$concepto->concepto_id}}">
                                                                {{$concepto->concepto_codigo}} -
                                                                {{$concepto->concepto_porcentaje}}% -
                                                                {{$concepto->concepto_nombre}}</option>
                                                            @endforeach
                                                        </select>
                                                        <select class="invisible" id="conceptoFuenteIDAux"
                                                            name="conceptoFuenteIDAux">
                                                            <option value="" label>--Seleccione una opcion--</option>
                                                            @foreach($conceptosFuente as $concepto)
                                                            <option value="{{$concepto->concepto_id}}">
                                                                {{$concepto->concepto_porcentaje}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Valor</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="valorFuente" name="valorFuente" type="text"
                                                                class="form-control" placeholder="0.00" value="0.00"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <center><button type="button" id="buttonRF" onclick="agregarItemRF();" disabled
                                                            class="btn btn-primary"><i class="fa fa-plus"></i></button>
                                                    </center>
                                                </div>
                                            </div>
                                            <div class="row">
                                            <?php $id_itemRF=1;$id_itemRFtotal=0;?>
                                            @include ('admin.compras.transaccionCompra.itemRetencionFuente')
                                              
                                                <table id="cargarItemRF" class="table table-bordered">
                                                    <thead>
                                                        <tr class="letra-blanca fondo-gris-claro">
                                                            <th></th>
                                                            <th>Base Retencion</th>
                                                            <th>Codigo Retencion</th>
                                                            <th>Porcentaje Retencion</th>
                                                            <th>Valor Retencion</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>  
                                                    @if(isset($compras->retencionCompra))
                                                        @foreach($compras->retencionCompra->detalles as $x)
                                                            @if($x->detalle_tipo=='FUENTE')
                                                            <tr class="text-center" id="rowRF_<?php echo $id_itemRF; ?>">
                                                                <td><a onclick="eliminarItemF({{$id_itemRF}}, '{{$x->detalle_valor}}', '{{$x->detalle_base}}');" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
                                                                <td>{{ $x->detalle_base}}<input class="invisible" name="DbaseRF[]" value="{{$x->detalle_base}}" /></td>  
                                                                <td>{{ $x->conceptoRetencion->concepto_nombre}}<input class="invisible" name="DcodigoRF[]" value="{{$x->conceptoRetencion->concepto_nombre}}" /><input class="invisible" name="DRFID[]" value="{{$x->conceptoRetencion->concepto_id}}" /></td>  
                                                                <td>{{ $x->detalle_porcentaje}}<input class="invisible" name="DporcentajeRF[]" value="{{ $x->detalle_porcentaje}}" /></td>  
                                                                <td>{{ $x->detalle_valor}}<input class="invisible" name="DvalorRF[]" value="{{$x->detalle_valor}}" /></td> 
                                                                <?php $id_itemRF++;?> 
                                                                <?php $id_itemRFtotal=$id_itemRFtotal+$x->detalle_base;?> 
                                                            </tr>
                                                            @endif 
                                                        @endforeach
                                                    @endif
                                                    </tbody>
                                                </table>
                                                <input type="hidden" id="totalBaseFuenteId" value="{{$id_itemRFtotal}}">
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9"
                                                    style="margin-bottom: 0px;"></div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Total</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                        <?php $y=0; ?>
                                                        @if(isset($compras->retencionCompra))
                                                            @foreach($compras->retencionCompra->detalles as $x)
                                                                @if($x->detalle_tipo=='FUENTE')
                                                                  
                                                                    <?php  $y=$y+$x->detalle_valor ?>

                                                                @endif   
                                                            @endforeach
                                                        @endif
                                                        <input id="id_total_fuente" class="form-control" name="id_total_fuente"
                                                                type="text" value="{{$y}}" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade show " id="custom-tabs-four-iva" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-iva-tab">
                                            <div class="row">
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Base</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="baseIva" name="baseIva" type="text"
                                                                class="form-control" placeholder="Total" value="0.00" onkeyup="calcularRI();" onclick="calcularRI();"
                                                                >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="conceptoIvaID"
                                                            name="conceptoIvaID" data-live-search="true"
                                                            onchange="calcularRI()">
                                                            <option value="" label>--Seleccione una opcion--</option>
                                                            @foreach($conceptosIva as $concepto)
                                                            <option value="{{$concepto->concepto_id}}">
                                                                {{$concepto->concepto_codigo}} -
                                                                {{$concepto->concepto_porcentaje}}% -
                                                                {{$concepto->concepto_nombre}}</option>
                                                            @endforeach
                                                        </select>
                                                        <select class="invisible" id="conceptoIvaIDAux"
                                                            name="conceptoIvaIDAux">
                                                            <option value="" label>--Seleccione una opcion--</option>
                                                            @foreach($conceptosIva as $concepto)
                                                            <option value="{{$concepto->concepto_id}}">
                                                                {{$concepto->concepto_porcentaje}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Valor</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="valorIva" name="valorIva" type="text"
                                                                class="form-control" placeholder="Total" value="0.00"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <center><button type="button" id="buttonRI" onclick="agregarItemRI();" disabled
                                                            class="btn btn-primary"><i class="fa fa-plus"></i></button>
                                                    </center>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <?php $id_itemRI=1;?>
                                                @include ('admin.compras.transaccionCompra.itemRetencionIva')
                                                <table id="cargarItemRI" class="table table-bordered">
                                                   <thead>
                                                        <tr class="letra-blanca fondo-gris-claro">  
                                                            <th></th>                                              
                                                            <th>Base Retencion</th>
                                                            <th>Codigo Retencion</th>
                                                            <th>Porcentaje Retencion</th>
                                                            <th>Valor Retencion</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(isset($compras->retencionCompra))
                                                        @foreach($compras->retencionCompra->detalles as $x)
                                                            @if($x->detalle_tipo=='IVA')
                                                            <tr class="text-center"  id="rowRI_<?php echo $id_itemRI; ?>">
                                                                <td><a onclick="eliminarItemI({{$id_itemRI}}, '{{$x->detalle_valor}}');" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
                                                                <td>{{ $x->detalle_base}}<input class="invisible" name="DbaseRI[]" value="{{ $x->detalle_base}}" /></td>  
                                                                <td>{{ $x->conceptoRetencion->concepto_nombre}}<input class="invisible" name="DcodigoRI[]" value="{{ $x->conceptoRetencion->concepto_nombre}}" /><input class="invisible" name="DRIID[]" value="{{ $x->conceptoRetencion->concepto_id}}" /></td>  
                                                                <td>{{ $x->detalle_porcentaje}}<input class="invisible" name="DporcentajeRI[]" value="{{ $x->detalle_porcentaje}}" /></td>  
                                                                <td>{{ $x->detalle_valor}}<input class="invisible" name="DvalorRI[]" value="{{ $x->detalle_valor}}" /></td> 
                                                                <?php $id_itemRI++;?> 
                                                            </tr>
                                                            @endif  
                                                        @endforeach
                                                    @endif
                                                    </tbody>
                                                </table>
                                               
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9"
                                                    style="margin-bottom: 0px;"></div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Total</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                        <?php $yi=0; ?>
                                                        @if(isset($compras->retencionCompra))
                                                            @foreach($compras->retencionCompra->detalles as $x)
                                                                @if($x->detalle_tipo=='IVA')
                                                                    <?php $yi=$yi+$x->detalle_valor; ?>
                                                                @endif   
                                                            @endforeach
                                                        @endif
                                                        <input id="id_total_iva" class="form-control" name="id_total_iva"
                                                                type="text" value="{{$yi}}" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                     
                                        
                                        <div class="tab-pane fade" id="custom-tabs-four-asumida" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-asumida-tab">
                                            <div class="row">

                                            </div>
                                        </div>
                                        
                                        <div class="tab-pane fade" id="custom-tabs-four-factura" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-factura-tab">
                                            <div id="idSeleccionarFactura" class="row @if(is_null($compras->transaccion_factura_manual) == false) invisible @endif">
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <label>Seleccionar Factura</label>
                                                </div>
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <select class="form-control select" id="factura_id"
                                                            name="factura_id" data-live-search="true" onchange="cargarDatosFactura();">
                                                            <option value="0" label>--Seleccione una opcion--</option>
                                                            @if(isset($compras->facturaModificar->transaccion_id))
                                                            <option value="{{$compras->facturaModificar->transaccion_id}}" selected>{{$compras->facturaModificar->transaccion_numero}}</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <input id="fechaFacturaID" name="fechaFacturaID" type="date"
                                                        @if(isset($compras->facturaModificar->transaccion_id))
                                                            value="{{$compras->facturaModificar->transaccion_fecha}}"
                                                        @else
                                                            value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>"
                                                        @endif
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <input id="totalFacturaID" name="totalFacturaID" type="text"
                                                            class="form-control" placeholder="0.00" 
                                                            @if(isset($compras->facturaModificar->transaccion_id))
                                                                value="{{number_format($compras->facturaModificar->transaccion_total,2)}}"
                                                            @else
                                                                value="0.00"
                                                            @endif
                                                            >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group clearfix">
                                                        <div class="form-group clearfix">
                                                            <div class="icheck-primary d-inline">
                                                                <input type="checkbox" id="manualID" name="manualID" onchange="OtraFactura();" 
                                                                @if(is_null($compras->transaccion_factura_manual) == false) checked @endif>
                                                                <label for="manualID">Otra Factura
                                                                </label>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="idOtraFactura" class="row @if(is_null($compras->transaccion_factura_manual) == true) invisible @endif">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Serie Factura</label>
                                                        <input id="serieFacManual" name="serieFacManual" type="text"
                                                            placeholder="001001" @if(is_null($compras->transaccion_factura_manual) == false) value="{{ substr($compras->transaccion_factura_manual,0,3).substr($compras->transaccion_factura_manual,4,3) }}" @endif
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Numero Factura</label>
                                                        <input id="scuencialFacManual" name="scuencialFacManual" type="text"
                                                            placeholder="000000001" @if(is_null($compras->transaccion_factura_manual) == false) value="{{ substr($compras->transaccion_factura_manual,8) }}" @endif
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Autorizacion Factura</label>
                                                        <input id="autorizacionFacManual" name="autorizacionFacManual" type="text"
                                                            placeholder="1702201205176001321000110010030001000011234567816" @if(is_null($compras->transaccion_autorizacion_manual) == false) value="{{ $compras->transaccion_autorizacion_manual }}" @endif
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  form-control-label">
                                <table class="table table-totalVenta">
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita" width="90">Sub-Total
                                        </td>
                                        <td id="subtotal" width="100" class="derecha-texto negrita">0.00</td>
                                        <input id="idSubtotal" name="idSubtotal" type="hidden" value="0.00" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Descuento</td>
                                        <td id="descuento" class="derecha-texto negrita">0.00</td>
                                        <input id="idDescuento" name="idDescuento" type="hidden" value="0" />
                                    </tr>
                                    <tr>
                                        <td id="porcentajeIva" class="letra-blanca fondo-azul-claro negrita">Tarifa 12 %
                                        </td>
                                        <td id="tarifa12" class="derecha-texto negrita"> 0.00</td>
                                        <input id="idTarifa12" name="idTarifa12" type="hidden" value="0" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Tarifa 0%</td>
                                        <td id="tarifa0" class="derecha-texto negrita">0.00</td>
                                        <input id="idTarifa0" name="idTarifa0" type="hidden" value="0" />
                                    </tr>
                                    <tr>
                                        <td id="iva12" class="letra-blanca fondo-azul-claro negrita">Iva 12 %</td>
                                        <td id="iva" class="derecha-texto negrita"> 0.00</td>
                                        <input id="idIva" name="idIva" type="hidden" value="0" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                        <td id="total" class="derecha-texto negrita">0.00</td>
                                        <input id="idTotal" name="idTotal" type="hidden" value="0" />
                                    </tr>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6  form-control-label">
                                    <center><label>Iva Serv.</label>
                                        <input id="IvaServiciosID" name="IvaServiciosID" type="text"
                                            class="form-control centrar-texto" value="0.00" readonly>
                                    </center>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6  form-control-label">
                                    <center><label>Iva Bien.</label>
                                        <input id="IvaBienesID" name="IvaBienesID" type="text"
                                            class="form-control centrar-texto" value="0.00" readonly>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- /.card -->
@section('scriptAjax')

<script src="{{ asset('admin/js/ajax/autocompleteProductoCompra.js') }}"></script>
@endsection
<script type="text/javascript">
     var id_item = 1;
     if( <?php echo $id_itemRF;?> == 1){
        var id_itemRF = 1;
    }else{
        id_itemRF = <?php echo $id_itemRF;?>;
    }   
    if( <?php echo $id_itemRI;?> == 1){
        var id_itemRI = 1;
    }else{
        id_itemRI = <?php echo $id_itemRI;?>;
    }   
   
   function cargarmetodo() {
    document.getElementById("tipo_comprobante_id").value = '<?php echo($compras->tipo_comprobante_id); ?>';
    document.getElementById("transaccion_descripcion").value = '<?php echo($compras->transaccion_descripcion); ?>';
        document.getElementById("transaccion_tipo_pago").value = '<?php echo($compras->transaccion_tipo_pago); ?>';
        document.getElementById("sustento_id").value = '<?php echo($compras->sustento_id); ?>';
       cargarItem();
      
       cargarFactura();
       
       document.getElementById("totalBaseFuenteId").value=Number(document.getElementById("totalBaseFuenteId").value).toFixed(2);

        
       
       }

    
      

    document.getElementById("idTarifa0").value = 0;
    document.getElementById("idTarifa12").value = 0;
    var porcentajeIva = '<?php echo($compras->transaccion_porcentaje_iva); ?>';
    porcentajeIva = parseFloat(porcentajeIva) / 100;
    
    function calcularTotal() {
    document.getElementById("buscarProducto").classList.remove('is-invalid');
    document.getElementById("id_total").value = Number(document.getElementById("id_cantidad").value * document
        .getElementById("id_pu").value).toFixed(2);
} 
function facturaleer(){

    <?php if(isset($compras->transaccion_id_f)) { ?>
      
        document.getElementById("factura_id").value='<?php echo($compras->transaccion_id_f); ?>';
        cargarDatosFactura()
    <?php } ?>


}
function cargarItem() {
    <?php foreach($compras->detalles as $x) { ?>
    total = Number("{{number_format($x->detalle_total, 2,'.','')}}");
    descuento = Number("{{ number_format($x->detalle_descuento, 2,'.','')}}") ;
 
  
        var linea = $("#plantillaItem").html();
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{Dcantidad}/g,'{{$x->detalle_cantidad}}');
        linea = linea.replace(/{Dcodigo}/g, '{{$x->producto->producto_codigo}}');
        linea = linea.replace(/{DprodcutoID}/g,'{{$x->producto->producto_id}}');
        linea = linea.replace(/{Dnombre}/g, '{{$x->producto->producto_nombre}}' );
     
     
        if( '{{$x->producto->producto_tiene_iva}}'=="1"){
            linea = linea.replace(/{Diva}/g, "SI"); 
            linea = linea.replace(/{DViva}/g, "{{number_format($x->detalle_iva, 2,'.','')}}");
          
            iva = "SI";
        }
        else{
            linea = linea.replace(/{Diva}/g, "NO");
            linea = linea.replace(/{DViva}/g, "{{number_format($x->detalle_iva, 2,'.','')}}");
            iva = "NO";

        }
        linea = linea.replace(/{Dpu}/g, "{{ number_format($x->detalle_precio_unitario, 2,'.','')}}" );
        linea = linea.replace(/{Ddescuento}/g, "{{ number_format($x->detalle_descuento, 2,'.','')}}" );
        linea = linea.replace(/{Dtotal}/g, "{{number_format($x->detalle_total, 2,'.','')}}" );

        linea = linea.replace(/{Dbodega}/g, '{{$x->bodega->bodega_id}}' );
        linea = linea.replace(/{DbodegaAux}/g, '{{$x->bodega->bodega_nombre}}' );
        linea = linea.replace(/{Dcconsumo}/g, '{{$x->centroConsumo->centro_consumo_id}}' );
        linea = linea.replace(/{DcconsumoAux}/g, '{{$x->centroConsumo->centro_consumo_nombre}}' );
        linea = linea.replace(/{Ddescripcion}/g, '{{$x->detalle_descripcion}}' );
        linea = linea.replace(/{DbienServ}/g, '@if($x->producto->producto_tipo == 1) Bien @else Servicio @endif');
           
        $("#cargarItem tbody").append(linea);
        id_item = id_item + 1;

       
        cargarTotales(iva, total, descuento, '@if($x->producto->producto_tipo == 1) Bien @else Servicio @endif');
      
        resetearCampos();
        
     
       
        <?php } ?>

        
       
}



function agregarItem() {
    if (document.getElementById("id_total").value > 0 && document.getElementById("idProductoID").value != '') {
        total = Number(document.getElementById("id_total").value);
        descuento = Number(total * (document.getElementById("id_descuento").value / 100));
        var linea = $("#plantillaItem").html();
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{Dcantidad}/g, document.getElementById("id_cantidad").value);
        linea = linea.replace(/{Dcodigo}/g, document.getElementById("codigoProducto").value);
        linea = linea.replace(/{DprodcutoID}/g, document.getElementById("idProductoID").value);
        linea = linea.replace(/{Dnombre}/g, document.getElementById("buscarProducto").value);
        if (document.getElementById("tieneIva").checked) {
            linea = linea.replace(/{Diva}/g, "SI");
            linea = linea.replace(/{DViva}/g, round(Number((total - descuento) * porcentajeIva)));
            iva = "SI";
        } else {
            linea = linea.replace(/{Diva}/g, "NO");
            linea = linea.replace(/{DViva}/g, "0.00");
            iva = "NO";
        }
        linea = linea.replace(/{Dpu}/g, document.getElementById("id_pu").value);
        linea = linea.replace(/{Ddescuento}/g, Number(descuento).toFixed(2));
        linea = linea.replace(/{Dtotal}/g, Number(total - descuento).toFixed(2));
       
        var aux = document.getElementById("bodegaProducto");
        linea = linea.replace(/{DbodegaAux}/g, aux.options[aux.selectedIndex].text);
        linea = linea.replace(/{Dbodega}/g, document.getElementById("bodegaProducto").value);
        var aux = document.getElementById("ccProducto");
        linea = linea.replace(/{DcconsumoAux}/g, aux.options[aux.selectedIndex].text);
        linea = linea.replace(/{Dcconsumo}/g, document.getElementById("ccProducto").value);
        linea = linea.replace(/{Ddescripcion}/g, document.getElementById("descripcionProducto").value);
        linea = linea.replace(/{DbienServ}/g, document.getElementById("tipoProductoID").value);
        $("#cargarItem tbody").append(linea);
        id_item = id_item + 1;
        cargarTotales(iva, total, descuento, document.getElementById("tipoProductoID").value);
        resetearCampos();
        document.getElementById("baseFuente").value = document.getElementById("idSubtotal").value;
        document.getElementById("baseIva").value = document.getElementById("idIva").value;
    }   
}

function resetearCampos() {
    document.getElementById("descripcionProducto").value = "";
    document.getElementById("id_cantidad").value = 1;
    document.getElementById("codigoProducto").value = "";
    document.getElementById("idProductoID").value = "";
    document.getElementById("buscarProducto").value = "";
    document.getElementById("id_pu").value = "0.00";
    document.getElementById("id_descuento").value = "0.00";
    document.getElementById("id_total").value = "0.00";
}
function eliminarItem(id, iva, total, descuento, tipo) {
    cargarTotales(iva, total * (-1), descuento * (-1), tipo);
    $("#row_" + id).remove();
    calcularIvasBienServicios();
}
function calcularIvasBienServicios(){
    document.getElementById("IvaBienesID").value = '0.00';
    document.getElementById("IvaServiciosID").value = '0.00';
    for (var i = 1; i < id_item; i++) {
        if($("input[name='Dtotal[]']")[i]){
            if($("input[name='Diva[]']")[i].value == "SI"){
                var ivaAux = Number($("input[name='Dtotal[]']")[i].value) * porcentajeIva;
                if ($("input[name='DbienServ[]']")[i].value == "Bien") {
                    document.getElementById("IvaBienesID").value = Number(document.getElementById("IvaBienesID").value) + Number(ivaAux);
                } else {
                    document.getElementById("IvaServiciosID").value = Number(document.getElementById("IvaServiciosID").value) + Number(ivaAux);
                }
            }
        }
    }
    document.getElementById("IvaBienesID").value = Number(document.getElementById("IvaBienesID").value ).toFixed(2);
    document.getElementById("IvaServiciosID").value = Number(document.getElementById("IvaServiciosID").value ).toFixed(2);
}
function cargarTotales(iva, total, descuento, tipo) {
    if (iva == "SI") {
        document.getElementById("IvaBienesID").value = 0;
        document.getElementById("IvaServiciosID").value = 0;
        for (var i = 1; i < id_item; i++) {
            if($("input[name='Dtotal[]']")[i]){
                if($("input[name='Diva[]']")[i].value == "SI"){
                    var ivaAux = Number($("input[name='Dtotal[]']")[i].value) * porcentajeIva;
                    if ($("input[name='DbienServ[]']")[i].value.trim() == "Bien") {
                        document.getElementById("IvaBienesID").value = Number(document.getElementById("IvaBienesID").value) + Number(ivaAux);
                    } else {
                        document.getElementById("IvaServiciosID").value = Number(document.getElementById("IvaServiciosID").value) + Number(ivaAux);
                    }
                }
            }
        }
        document.getElementById("IvaBienesID").value = Number(document.getElementById("IvaBienesID").value ).toFixed(2);
        document.getElementById("IvaServiciosID").value = Number(document.getElementById("IvaServiciosID").value ).toFixed(2);
        var tip=tipo;
   /*     var tip=tipo;
        var ivaAux = Number(Number(total) * porcentajeIva).toFixed(2);
        if (tip.trim() == "Bien") {
            document.getElementById("IvaBienesID").value = Number(Number(document.getElementById("IvaBienesID").value) + Number(ivaAux)).toFixed(2);
        }
        if (tip.trim() == "Servicio") {
            document.getElementById("IvaServiciosID").value = Number(Number(document.getElementById("IvaServiciosID").value) + Number(ivaAux)).toFixed(2);
        }*/
    }
    var ivaAux = Number(Number(total) * porcentajeIva).toFixed(2);
    var subtotal = Number(Number(document.getElementById("subtotal").innerHTML) + total).toFixed(2);
    document.getElementById("subtotal").innerHTML = subtotal;
    document.getElementById("idSubtotal").value = subtotal;

    var tarifa12 = Number(Number(document.getElementById("tarifa12").innerHTML) + total - descuento).toFixed(2);
    var tarifa0 = Number(Number(document.getElementById("tarifa0").innerHTML) + total - descuento).toFixed(2);

    var descuento = Number(Number(document.getElementById("descuento").innerHTML) + descuento).toFixed(2);
    document.getElementById("descuento").innerHTML = descuento;
    document.getElementById("idDescuento").value = descuento;

    if (iva == "SI") {
        document.getElementById("tarifa12").innerHTML = tarifa12;
        document.getElementById("idTarifa12").value = tarifa12;
    } else {
        document.getElementById("tarifa0").innerHTML = tarifa0;
        document.getElementById("idTarifa0").value = tarifa0;
    }
    calcularTotales();
}

function calcularTotales() {
    var iva = round(Number(document.getElementById("tarifa12").innerHTML) * porcentajeIva);
    document.getElementById("iva").innerHTML = iva;
    document.getElementById("idIva").value = iva;

    var total = Number(Number(document.getElementById("tarifa12").innerHTML) + Number(document.getElementById("tarifa0")
        .innerHTML) + Number(document.getElementById("iva").innerHTML)).toFixed(2);

    document.getElementById("total").innerHTML = total;
    document.getElementById("idTotal").value = total;
}




function ponerCeros(num) {
    num = num + '';
    while (num.length <= 1) {
        num = '0' + num;
    }
    return num;
}

function round(num) {
        var m = Number((Math.abs(num) * 100).toPrecision(15));
         m =Math.round(m) / 100 * Math.sign(num);
         return (m).toFixed(2);
    }


/************PROCESO DE RETENCION EN LA FUENTE******************/
function calcularRF() {
    codRF = document.getElementById("conceptoFuenteID");
    porcentajeRF = document.getElementById("conceptoFuenteIDAux");
    porcentajeRF = porcentajeRF.options[codRF.selectedIndex].text;
    document.getElementById("valorFuente").value = round((Number(document.getElementById("baseFuente").value) * Number(
        porcentajeRF)) / 100);
}

function agregarItemRF() {
    if(document.getElementById("checkboxPrimary1").checked==true){
        baseRF = Number(document.getElementById("baseFuente").value);
        porcentajeRF = document.getElementById("conceptoFuenteIDAux");
        codRF = document.getElementById("conceptoFuenteID");
        valorRF = Number(document.getElementById("valorFuente").value);

        var linea = $("#plantillaItemRF").html();
        linea = linea.replace(/{ID}/g, id_itemRF);
        linea = linea.replace(/{DbaseRF}/g, round(baseRF));
        linea = linea.replace(/{DcodigoRF}/g, codRF.options[codRF.selectedIndex].text);
        linea = linea.replace(/{DRFID}/g, codRF.value);
        linea = linea.replace(/{DporcentajeRF}/g, porcentajeRF.options[codRF.selectedIndex].text);
        linea = linea.replace(/{DvalorRF}/g, round(valorRF));
        $("#cargarItemRF tbody").append(linea);
        id_itemRF = id_itemRF + 1;
        totalRF(valorRF,baseRF);
        resetearCamposRF();
    }
}

function totalRF(valorF,baseF) {
    document.getElementById("id_total_fuente").value = round(Number(document.getElementById("id_total_fuente").value) +
        Number(valorF));
    document.getElementById("totalBaseFuenteId").value = round(Number(document.getElementById("totalBaseFuenteId").value) +
        Number(baseF));
}

function resetearCamposRF() {
    document.getElementById("baseFuente").value = "0.00";
    document.getElementById("valorFuente").value = "0.00";
}

function eliminarItemF(id, valorF,baseF) {
    if(document.getElementById("checkboxPrimary1").checked==true){
        $("#rowRF_" + id).remove();
        totalRF(valorF * (-1), baseF * (-1));
    }
}
/************PROCESO DE RETENCION DE IVA******************/
function calcularRI() {
    codRI = document.getElementById("conceptoIvaID");
    porcentajeRI = document.getElementById("conceptoIvaIDAux");
    porcentajeRI = porcentajeRI.options[codRI.selectedIndex].text
    document.getElementById("valorIva").value = round((Number(document.getElementById("baseIva").value) * Number(
        porcentajeRI)) / 100);
}

function agregarItemRI() {
    if(document.getElementById("checkboxPrimary1").checked==true){
        baseRI = Number(document.getElementById("baseIva").value);
        porcentajeRI = document.getElementById("conceptoIvaIDAux");
        codRI = document.getElementById("conceptoIvaID");
        valorRI = Number(document.getElementById("valorIva").value);

        var linea = $("#plantillaItemRI").html();
        linea = linea.replace(/{ID}/g, id_itemRI);
        linea = linea.replace(/{DbaseRI}/g, round(baseRI));
        linea = linea.replace(/{DcodigoRI}/g, codRI.options[codRI.selectedIndex].text);
        linea = linea.replace(/{DRIID}/g, codRI.value);
        linea = linea.replace(/{DporcentajeRI}/g, porcentajeRI.options[codRI.selectedIndex].text);
        linea = linea.replace(/{DvalorRI}/g, round(valorRI));
        $("#cargarItemRI tbody").append(linea);
        id_itemRI = id_itemRI + 1;
        totalRI(valorRI);
        resetearCamposRI();
    }
}

function totalRI(valorI) {
    document.getElementById("id_total_iva").value = round(Number(document.getElementById("id_total_iva").value) +
        Number(valorI));
}

function resetearCamposRI() {
    document.getElementById("baseIva").value = "0.00";
    document.getElementById("valorIva").value = "0.00";
}

function eliminarItemI(id, valorI) {
    if(document.getElementById("checkboxPrimary1").checked==true){
        $("#rowRI_" + id).remove();
        totalRI(valorI * (-1));
    }
}




function cargarFactura(){
    $.ajax({
        url: '{{ url("facturasCompraactiva/searchN") }}'+ '/' + document.getElementById("proveedorID").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("proveedorID").value
        },
        success: function(data){
            document.getElementById("factura_id").innerHTML = "<option value='0' label>--Seleccione una opcion--</option>";
            for (var i=0; i<data.length; i++) {
                document.getElementById("factura_id").innerHTML += "<option value='"+data[i].transaccion_id+"'>"+data[i].transaccion_numero+"</option>";
            } 
          
            facturaleer();          
        },
    });
}
function cargarDatosFactura(){
    $.ajax({
        url: '{{ url("datosFactCompra/searchN") }}'+ '/' + document.getElementById("factura_id").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("factura_id").value
        },
        success: function(data){
            document.getElementById("fechaFacturaID").value = data.transaccion_fecha;       
            document.getElementById("totalFacturaID").value = Number(data.transaccion_total).toFixed(2);          
        },
    });
}

function editaretencion(){

    if(document.getElementById("checkboxPrimary1").checked==true){
       
        $.ajax({
            url: '{{ url("nuevartencion/searchN") }}'+ '/' + document.getElementById("idretencion").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("idretencion").value
            },
            success: function(data){
               
                document.getElementById("punto_id").value = data[0];       
                document.getElementById("rango_id").value = data[1];
                document.getElementById("retencion_serie").value = data[2];
                document.getElementById("retencion_secuencial").value = data[3];
             //   document.getElementById("retencion_fecha").value = "<?php echo(date("Y")."-".date("m")."-".date("d")); ?>";
            },
        });
        document.getElementById("check1").disabled=false;
        document.getElementById("check2").disabled=false;
        document.getElementById("buttonRF").disabled=false;
        document.getElementById("buttonRI").disabled=false;

        document.getElementById("retencion_fecha").disabled=false;
        document.getElementById("retencion_serie").disabled=false;
        document.getElementById("retencion_secuencial").disabled=false;

        document.getElementById("factura_id").disabled=false;
     

        
    }
    else{
        document.getElementById("retencion_serie").value =  document.getElementById("serieretencion").value;
        document.getElementById("retencion_secuencial").value =  document.getElementById("codigoretencion").value;
        document.getElementById("retencion_fecha").value =  document.getElementById("fecharetencion").value;
        document.getElementById("check1").disabled=true;
        document.getElementById("check2").disabled=true;
        document.getElementById("buttonRF").disabled=true;
        document.getElementById("buttonRI").disabled=true;

        document.getElementById("retencion_fecha").disabled=true;
        document.getElementById("retencion_serie").disabled=true;
        document.getElementById("retencion_secuencial").disabled=true;

        document.getElementById("factura_id").disabled=true;
    }

}
function validarForm(){
    comprobante = document.getElementById("tipo_comprobante_id");
    comprobanteCodigo = document.getElementById("tipo_comprobante_codigo");
    $subtotal=round(Number(document.getElementById("idSubtotal").value)-Number(document.getElementById("idDescuento").value));
    if(comprobanteCodigo.options[comprobante.selectedIndex].value != '04' ){
        if($subtotal != document.getElementById("totalBaseFuenteId").value){
            bootbox.alert({
                message: "El total se retención en la fuente es diferente del subtotal de la factura.",
                size: 'small'
            });
            return false;
        }
    }
        if(document.getElementById("proveedorID").value == ''){
            bootbox.alert({
                message: "Seleccione un proveedor antes de guardar.",
                size: 'small'
            });
            return false;
        }

   
    if(comprobanteCodigo.options[comprobante.selectedIndex].value == '04' || comprobanteCodigo.options[comprobante.selectedIndex].value == '05'){
        if(document.getElementById("manualID").checked){
            if(document.getElementById("serieFacManual").value == ''){
                bootbox.alert({
                    message: "Tiene que registrar la serie de la factura",
                    size: 'small'
                });
                return false
            }
            if(document.getElementById("scuencialFacManual").value == ''){
                bootbox.alert({
                    message: "Tiene que registrar el secuencial de la factura",
                    size: 'small'
                });
                return false
            }
            if(document.getElementById("autorizacionFacManual").value == ''){
                bootbox.alert({
                    message: "Tiene que registrar la autorizacion de la factura",
                    size: 'small'
                });
                return false
            }
        }else{
            if(document.getElementById("factura_id").value == '0'){
                bootbox.alert({
                    message: "Seleccione la factura antes de guardar",
                    size: 'small'
                });
                return false
            }
        }
    }
    var a = document.getElementById("cargarItemRI");
    var rows = a.rows.length;
    var a2 = document.getElementById("cargarItemRF");
    var rows2 = a2.rows.length;

    if ((rows+rows2)+Number(id_itemRI) == 2 && comprobanteCodigo.options[comprobante.selectedIndex].value != '04'){
        alert('Registre datos de retencion antes de guardar');
        return false
    }
    if((rows+rows2) > 2 && comprobanteCodigo.options[comprobante.selectedIndex].value == '04'){
        alert('Elimine los datos de retencion antes de guardar');
        return false
    }
    if(document.getElementById("idTotal").value == 0){
        bootbox.alert({
            message: "El total de la compra tiene que set mayor a 0.00",
            size: 'small'
        });
        return false
    }
    return true;
}
function OtraFactura(){
    if(document.getElementById("manualID").checked){
        document.getElementById("idSeleccionarFactura").classList.add('invisible');
        document.getElementById("idOtraFactura").classList.remove('invisible');
    }else{
        document.getElementById("idOtraFactura").classList.add('invisible');
        document.getElementById("idSeleccionarFactura").classList.remove('invisible');
    }
}
</script>
@endsection
