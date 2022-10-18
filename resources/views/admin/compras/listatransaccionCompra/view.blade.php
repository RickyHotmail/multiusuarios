@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST" action="/transaccionCompra" onsubmit="return validarForm();">
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>TRANSACCIÓN COMPRAS - Documento</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
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
                                    <label class="form-control" >{{$compras->transaccion_fecha}}</label> 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Inv.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" >{{$compras->transaccion_inventario}}</label> 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Ven.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" >{{$compras->transaccion_vencimiento}}</label> 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Imp.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" >{{$compras->transaccion_impresion}}</label> 
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
                                <label class="form-control" >{{$compras->transaccion_caducidad}}</label> 
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
                                <label class="form-control" >{{$compras->tipoComprobante->tipo_comprobante_nombre}}</label> 
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Transaccion</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control" >{{$compras->transaccion_descripcion}}</label> 
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Forma Pago</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                            <label class="form-control" >{{$compras->transaccion_tipo_pago}}</label> 
                            
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
                                <label class="form-control" >{{$compras->transaccion_serie}}</label>
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
                                <label class="form-control" id="guia_numero" name="guia_numero"  value="{{substr(str_repeat(0, 9). $compras->transaccion_numero , - 9)}}">{{substr(str_repeat(0, 9). $compras->transaccion_numero , - 9)}}</label>
                                
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
                                <label class="form-control" >{{$compras->transaccion_autorizacion}}</label>
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
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Dias de Plazo</label>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="margin-bottom : 0px;">
                            <div class="form-group">
                            <label class="form-control" >{{$compras->transaccion_dias_plazo}}</label>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Sustento</label>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                            <div class="form-group">
                            <label class="form-control" >{{$compras->sustentoTributario->sustento_codigo .' - '. $compras->sustentoTributario->sustento_nombre}} </label> 
                            
                            </div>
                        </div>
                    </div>
                  
                    <br>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                               
                                <table id="cargarItemFactura"
                                    class="table table-striped table-hover boder-sar tabla-item-factura sin-salto"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                            
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
                                        @foreach($compras->detalles as $x)
                                        <tr class="text-center">
                                            <td>{{ $x->detalle_cantidad}}</td>  
                                            <td>{{ $x->producto->producto_codigo}}</td>  
                                            <td>{{ $x->producto->producto_nombre}}</td>  
                                            <td>{{ $x->producto->producto_tiene_iva}}</td>  
                                            <td>{{ $x->detalle_iva}}</td>  
                                            <td>{{ $x->detalle_precio_unitario}}</td>  
                                            <td>{{ $x->detalle_descuento}}</td> 
                                            <td>{{ $x->detalle_total}}</td>  
                                            <td>{{ $x->bodega->bodega_nombre}}</td>  
                                            <td>{{ $x->centroConsumo->centro_consumo_nombre}}</td>  
                                            <td>{{ $x->detalle_descripcion}}</td>  
                                            <td>@if($x->producto->producto_tipo==1) ARTICULO @endif @if($x->producto->producto_tipo==2) SERVICIO @endif</td>  
                                        </tr>
                                        @endforeach
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
                                        @if(isset($compras->retencionCompra))
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
                                        <!-- <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-asumida-tab" data-toggle="pill"
                                                href="#custom-tabs-four-asumida" role="tab"
                                                aria-controls="custom-tabs-four-asumida"
                                                aria-selected="false"><b>Retencion Asumida</b></a>
                                        </li>-->
                                        @endif
                                        @if(isset($compras->facturaModificar))
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-factura-tab" data-toggle="pill"
                                                href="#custom-tabs-four-factura" role="tab"
                                                aria-controls="custom-tabs-four-factura"
                                                aria-selected="false"><b>Factura</b></a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        @if(isset($compras->retencionCompra))
                                        <div class="tab-pane fade show active" id="custom-tabs-four-retencion"
                                            role="tabpanel" aria-labelledby="custom-tabs-four-retencion-tab">
                                            <div class="row">
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"
                                                    style="margin-bottom : 0px;">
                                                    <label class="form-control" >@if(isset($compras->retencionCompra))@if($compras->retencionCompra->retencion_emision=='ELECTRONICA')Documento Electronico @endif @if($compras->retencionCompra->retencion_emision=='FISICO') Documento Electronico @endif @endif</label>
                        
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
                                                        <label class="form-control">@if(isset($compras->retencionCompra)) {{ $compras->retencionCompra->retencion_fecha }} @endif</label>
                        
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
                                                        <label class="form-control">@if(isset($compras->retencionCompra)) {{ $compras->retencionCompra->retencion_serie }} @endif</label>
                        
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
                                                        <label class="form-control" >@if(isset($compras->retencionCompra)) {{substr(str_repeat(0, 9). $compras->retencionCompra->retencion_numero , - 9)}} @endif</label>
                                                       
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-fuente" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-fuente-tab">
                                           
                                            <div class="row">
                                                <table id="cargarItemRF" class="table table-bordered">
                                                    <thead>
                                                        <tr class="letra-blanca fondo-gris-claro">
                                                        
                                                            <th>Base Retencion</th>
                                                            <th>Codigo Retencion</th>
                                                            <th>Porcentaje Retencion</th>
                                                            <th>Valor Retencion</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(isset($compras->retencionCompra))
                                                        @foreach($compras->retencionCompra->detalles as $x)
                                                        <tr class="text-center">
                                                            @if($x->detalle_tipo=='FUENTE')
                                                            <td>{{ $x->detalle_base}}</td>  
                                                            <td>{{ $x->conceptoRetencion->concepto_codigo.' - '.$x->conceptoRetencion->concepto_nombre}}</td>  
                                                            <td>{{ $x->detalle_porcentaje}}</td>  
                                                            <td>{{ $x->detalle_valor}}</td> 
                                                            @endif   
                                                        </tr>
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
                                                        <input type="hidden" value="{{$y=0}}">
                                                        @if(isset($compras->retencionCompra))
                                                            @foreach($compras->retencionCompra->detalles as $x)
                                                                @if($x->detalle_tipo=='FUENTE')
                                                                    {{ $y=$y+$x->detalle_valor}}

                                                                @endif   
                                                            @endforeach
                                                        @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-iva" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-iva-tab">

                                            <div class="row">
                                                <table id="cargarItemRI" class="table table-bordered">
                                                    <thead>
                                                        <tr class="letra-blanca fondo-gris-claro">
                                                         
                                                            <th>Base Retencion</th>
                                                            <th>Codigo Retencion</th>
                                                            <th>Porcentaje Retencion</th>
                                                            <th>Valor Retencion</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(isset($compras->retencionCompra))
                                                        @foreach($compras->retencionCompra->detalles as $x)
                                                        <tr class="text-center">
                                                            @if($x->detalle_tipo=='IVA')
                                                            <td>{{ $x->detalle_base}}</td>  
                                                            <td>{{ $x->conceptoRetencion->concepto_codigo.' - '.$x->conceptoRetencion->concepto_nombre}}</td>  
                                                            <td>{{ $x->detalle_porcentaje}}</td>  
                                                            <td>{{ $x->detalle_valor}}</td> 
                                                            @endif   
                                                        </tr>
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
                                                        <input type="hidden" value="{{$yi=0}}">
                                                        @if(isset($compras->retencionCompra))
                                                            @foreach($compras->retencionCompra->detalles as $x)
                                                                @if($x->detalle_tipo=='IVA')
                                                                    {{ $yi=$yi+$x->detalle_valor}}
                                                                   
                                                                @endif   
                                                            @endforeach
                                                        @endif
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
                                        @endif
                                        @if(isset($compras->facturaModificar->transaccion_numero))
                                        <div class="tab-pane fade show active" id="custom-tabs-four-factura" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-factura-tab">
                                            <div class="row">
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <label> Factura</label>
                                                </div>
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                    <label class="form-control" >@if(isset($compras->facturaModificar->transaccion_numero)) {{$compras->facturaModificar->transaccion_numero}} @endif</label>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <label class="form-control" >@if(isset($compras->facturaModificar->transaccion_fecha)){{$compras->facturaModificar->transaccion_fecha}} @endif</label>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <label class="form-control" >@if(isset($compras->facturaModificar->transaccion_total)){{$compras->facturaModificar->transaccion_total}} @endif</label>
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
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
                                        <td id="subtotal" width="100" class="derecha-texto negrita">{{$compras->transaccion_subtotal}}</td>
                                        <input id="idSubtotal" name="idSubtotal" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Descuento</td>
                                        <td id="descuento" class="derecha-texto negrita">{{$compras->transaccion_descuento}}</td>
                                        <input id="idDescuento" name="idDescuento" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td id="porcentajeIva" class="letra-blanca fondo-azul-claro negrita">Tarifa 12 %
                                        </td>
                                        <td id="tarifa12" class="derecha-texto negrita">{{$compras->transaccion_tarifa12}}</td>
                                        <input id="idTarifa12" name="idTarifa12" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Tarifa 0%</td>
                                        <td id="tarifa0" class="derecha-texto negrita">{{$compras->transaccion_tarifa0}}</td>
                                        <input id="idTarifa0" name="idTarifa0" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td id="iva12" class="letra-blanca fondo-azul-claro negrita">Iva 12 %</td>
                                        <td id="iva" class="derecha-texto negrita">{{$compras->transaccion_iva}}</td>
                                        <input id="idIva" name="idIva" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                        <td id="total" class="derecha-texto negrita">{{$compras->transaccion_total}}</td>
                                        <input id="idTotal" name="idTotal" type="hidden" />
                                    </tr>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6  form-control-label">
                                    <center><label>Iva Serv.</label>
                                        <input id="IvaServiciosID" name="IvaServiciosID" type="text"
                                            class="form-control centrar-texto" value="{{$compras->transaccion_ivaS}}" readonly>
                                    </center>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6  form-control-label">
                                    <center><label>Iva Bien.</label>
                                        <input id="IvaBienesID" name="IvaBienesID" type="text"
                                            class="form-control centrar-texto" value="{{$compras->transaccion_ivaB}}" readonly>
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

@endsection