@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" >
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Nota de Crédito</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <!--
                        <button type="button" onclick='window.location = "{{ url("listanotaCredito") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
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
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="padding-top: 10px;">
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                                    <label>NUMERO</label>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <label class="form-control" >{{$notaCredito->nc_serie}}</label>             
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                    <div class="form-group">
                                        <div class="form-line">
                                        <label class="form-control" id="guia_numero" name="guia_numero"  value="{{substr(str_repeat(0, 9). $notaCredito->nc_numero , - 9)}}">{{substr(str_repeat(0, 9). $notaCredito->nc_numero , - 9)}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>CLIENTE :</label>
                                </div>
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <label class="form-control" >{{$notaCredito->factura->cliente->cliente_nombre}}</label> 
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>RUC/CI :</label>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <label class="form-control" >{{$notaCredito->factura->cliente->cliente_cedula}}</label> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>TIPO :</label>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <label class="form-control" >{{$notaCredito->factura->cliente->tipoCliente->tipo_cliente_nombre}}</label> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>DIRECCION :</label>
                                </div>
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <div class="form-line">
                                         <label class="form-control" >{{$notaCredito->factura->cliente->cliente_direccion}}</label> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>VENDEDOR :</label>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <label class="form-control" >{{$notaCredito->factura->vendedor->vendedor_nombre}}</label> 
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>FECHA :</label>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <label class="form-control" >{{$notaCredito->nc_fecha}}</label> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>MOTIVO :</label>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <label class="form-control" >{{$notaCredito->nc_comentario}}</label> 
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                    <label class="form-control" >@if($notaCredito->nc_emision=='ELECTRONICA')Documento Electronico @endif @if($notaCredito->nc_emision=='FISICO') Documento Electronico @endif</label> 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                            style="border-radius: 5px; border: 1px solid #ccc9c9;padding-top: 10px;">
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label">
                                    <label>TOTAL</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="idTotalnc" name="idTotalnc"
                                                class="form-control campo-total-global derecha-texto"
                                                placeholder="Total" disabled style="background-color: black"
                                                value="{{$notaCredito->nc_total}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>Bodega :</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <label class="form-control" >{{$notaCredito->factura->bodega->bodega_nombre}}</label> 
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>No Factura :</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <label class="form-control" >{{$notaCredito->factura->factura_numero}}</label> 
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>Valor Factura :</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <label class="form-control" >{{$notaCredito->factura->factura_total}}</label> 
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>Fecha Factura :</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <label class="form-control" >{{$notaCredito->factura->factura_fecha}}</label> 
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>Tarifa de IVA :</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="margin-bottom : 0px;">
                                    <div class="input-group mb-3">
                                        <label class="form-control" >{{$notaCredito->nc_porcentaje_iva}} %</label> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                              
                                <table id="cargarItemnc"
                                    class="table table-striped table-hover boder-sar tabla-item-factura"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                            <th>Cantidad</th>
                                            <th>Codigo</th>
                                            <th>Producto</th>
                                            <th>Con Iva</th>
                                            <th>Iva</th>
                                            <th>P.U.</th>
                                            <th>Descuento</th>
                                            <th>Total</th>
                                            <th width="40"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($notaCredito->detalles as $x)
                                        <tr class="text-center">   
                                            <td>{{ $x->detalle_cantidad}}</td>  
                                            <td>{{ $x->producto->producto_codigo}}</td>  
                                            <td>{{ $x->producto->producto_nombre}}</td>  
                                            <td>{{ $x->producto->producto_tiene_iva}}</td>  
                                            <td>{{ $x->detalle_iva}}</td>  
                                            <td>{{ $x->detalle_precio_unitario}}</td>  
                                            <td>{{ $x->detalle_descuento}}</td>  
                                            <td>{{ $x->detalle_total}}</td>
                                        </tr>  
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                            <table class="table table-totalVenta">
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita" width="90">Sub-Total
                                    </td>
                                    <td id="subtotal" width="100" class="derecha-texto negrita">{{$notaCredito->nc_subtotal}}</td>
                                    <input id="idSubtotal" name="idSubtotal" type="hidden" />
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita">Descuento</td>
                                    <td id="descuento" class="derecha-texto negrita">{{$notaCredito->nc_descuento}}</td>
                                    <input id="idDescuento" name="idDescuento" type="hidden" />
                                </tr>
                                <tr>
                                    <td id="porcentajeIva" class="letra-blanca fondo-azul-claro negrita">Tarifa 12 %
                                    </td>
                                    <td id="tarifa12" class="derecha-texto negrita">{{$notaCredito->nc_tarifa12}}</td>
                                    <input id="idTarifa12" name="idTarifa12" type="hidden" />
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita">Tarifa 0%</td>
                                    <td id="tarifa0" class="derecha-texto negrita">{{$notaCredito->nc_tarifa0}}</td>
                                    <input id="idTarifa0" name="idTarifa0" type="hidden" />
                                </tr>
                                <tr>
                                    <td id="iva12" class="letra-blanca fondo-azul-claro negrita">Iva 12 %</td>
                                    <td id="iva" class="derecha-texto negrita">{{$notaCredito->nc_iva}}</td>
                                    <input id="idIva" name="idIva" type="hidden" />
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                    <td id="total" class="derecha-texto negrita">{{$notaCredito->nc_total}}</td>
                                    <input id="idTotal" name="idTotal" type="hidden" />
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- /.card -->

@endsection