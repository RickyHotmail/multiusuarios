@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal">
       
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Orden de Despacho</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                         <!--
                        <a href="{{ url("listaOrdenes") }}" class="btn btn-danger btn-sm">Regresar</a>  
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
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                            <label>NUMERO</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                <label class="form-control derecha-texto" id="orden_serie" name="orden_serie"  value="{{$orden->orden_serie}}">{{$orden->orden_serie}}</label>   
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="orden_numero" name="orden_numero"  value="{{$orden->orden_numero}}">{{ substr(str_repeat(0, 9).$orden->orden_secuencial, - 9)}}</label>   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label" style="padding-left: 55px;">
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label derecha-texto">
                            <label>TOTAL</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                    
                                    <input type="text" id="idTotalorden" name="idTotalorden"
                                        class="form-control campo-total-global derecha-texto" placeholder="Total"
                                        disabled style="background-color: black" value="{{$orden->orden_total}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>CLIENTE :</label>
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input id="clienteID" name="clienteID" type="hidden">
                                <label class="form-control"   value="{{$orden->cliente->cliente_nombre}}">{{$orden->cliente->cliente_nombre}}</label>   
                               
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Fecha :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="orden_fecha" name="orden_fecha"  value="{{$orden->orden_fecha}}">{{$orden->orden_fecha}}</label>   
                                    
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>RUC/CI :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="idCedula" name="idCedula"  value="{{$orden->cliente->cliente_cedula}}">{{$orden->cliente->cliente_cedula}}</label>   
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>TIPO :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="idTipoCliente" name="idTipoCliente"  value="{{$orden->cliente->tipoCliente->tipo_cliente_nombre}}">{{$orden->cliente->tipoCliente->tipo_cliente_nombre}}</label>   
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3  form-control-label  centrar-texto"
                            style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label>BODEGA</label>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>DIRECCION :</label>
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="idDireccion" name="idDireccion"  value="{{$orden->cliente->cliente_direccion}}">{{$orden->cliente->cliente_direccion}}</label>   
                                   
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 alinear-izquierda" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control" id="bodega_id" name="bodega_id"  value="{{$orden->bodega_nombre}}">{{$orden->bodega_nombre}}</label>   
                               
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>TELEFONO :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="idTelefono" name="idTelefono"  value="{{$orden->cliente->cliente_telefono}}">{{$orden->cliente->cliente_telefono}}</label>   
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>PAGO :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control" id="orden_tipo_pago" name="orden_tipo_pago"  value="{{$orden->orden_tipo_pago}}">{{$orden->orden_tipo_pago}}</label>   
                                
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>% IVA :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control" value="{{$orden->orden_porcentaje_iva}}">{{$orden->orden_porcentaje_iva}}</label>   
                                <input id="orden_porcentaje_iva" name="orden_porcentaje_iva" type="hidden"  class="form-control"
                                        placeholder="Disponible" value="{{$orden->orden_porcentaje_iva}}" >
                            </div>
                        </div>
                        
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>VENDEDOR :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="vendedor_id" name="vendedor_id"  value="{{$orden->vendedor->vendedor_id}}">{{$orden->vendedor->vendedor_nombre}}</label>   
                                    
                                </div>
                            </div>
                        </div>
                        
                        
                       
                    </div>
                    
                    <hr>
                    <div class="row">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="codigoProducto" name="idProducto" type="hidden">
                                    <input id="idProductoID" name="idProductoID" type="hidden">
                                    <input id="buscarProducto" name="buscarProducto" type="hidden" class="form-control"
                                        placeholder="Buscar producto" >
                                    <span id="errorStock" class="text-danger invisible">El producto no tiene stock
                                        disponible.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="opacity:0; position:absolute; left:9999px;">
                            <center>
                                
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <div class="custom-control custom-checkbox">
                                        <input id="tieneIva" name="tieneIva" type="checkbox"
                                            class="custom-control-input" disabled />
                                        <label for="tieneIva" type="hidden"  class="custom-control-label"></label>
                                    </div>
                                </div>
                            </center>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_disponible" name="id_disponible" type="hidden"  class="form-control"
                                        placeholder="Disponible" value="0" type="hidden">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                           
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_cantidad"
                                        name="id_cantidad" type="hidden"  class="form-control" placeholder="Cantidad"
                                        value="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_pu" name="id_pu"
                                    type="hidden" class="form-control" placeholder="Precio" value="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_descuento" name="id_descuento" type="hidden" class="form-control"
                                        placeholder="Descuento" value="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                        
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_total" name="id_total" type="hidden" class="form-control"
                                        placeholder="Total" value="0.00" disabled>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                                @include ('admin.ventas.ordenesdespacho.itemOrdenesPresentar')
                                <table id="cargarItemorden"
                                    class="table table-striped table-hover boder-sar tabla-item-orden"
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
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orden->detalles as $x)
                                        <tr>
                                        <td >{{ $x->detalle_cantidad}}</td>
                                        <td >{{ $x->producto->producto_codigo}}</td>
                                        <td >{{ $x->detalle_descripcion}}</td>
                                        <td > @if($x->producto->producto_tiene_iva=='1') SI @else NO @endif</td>  
                                        <td ><?php echo number_format($x->detalle_iva, 2)?></td>
                                        <td ><?php echo number_format($x->detalle_precio_unitario, 2)?></td>
                                        <td ><?php echo number_format($x->detalle_descuento, 2)?></td>   
                                        <td ><?php echo number_format($x->detalle_total, 2)?></td>   
                                        
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
                            <div class="card card-primary card-outline card-tabs">
                                <div class="card-header p-0 pt-1 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist"
                                        style="border-bottom: 1px solid #c3c4c5;">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-otros-tab"
                                                data-toggle="pill" href="#custom-tabs-four-otros" role="tab"
                                                aria-controls="custom-tabs-four-otros"
                                                aria-selected="false"><b>Otros</b></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-credito-tab" data-toggle="pill"
                                                href="#custom-tabs-four-credito" role="tab"
                                                aria-controls="custom-tabs-four-credito"
                                                aria-selected="true"><b>Credito</b></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-otros"
                                            role="tabpanel" aria-labelledby="custom-tabs-four-otros-tab">
                                            <div class="row clearfix form-horizontal">
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                                    style="margin-bottom : 0px;">
                                                    <label>Comentario:</label>
                                                </div>
                                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <label class="form-control" rows=3 id="orden_comentario" name="orden_comentario"  value="{{$orden->orden_comentario}}">{{$orden->orden_comentario}}</label>   
                                
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-credito" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-credito-tab">
                                            <div class="row clearfix form-horizontal">
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                                    style="margin-bottom : 0px;">
                                                    <label>Monto de Credito:</label>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="idMontoCredito" type="number"
                                                                class="form-control " placeholder="00" required
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                                    style="margin-bottom : 0px;">
                                                    <label>Monto ordendo:</label>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="idMontoordendo" type="number"
                                                                class="form-control " placeholder="00" required
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row clearfix form-horizontal">
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                                    style="margin-bottom : 0px;">
                                                    <label>Saldo:</label>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input type="number" class="form-control " placeholder="00"
                                                                required disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                                    style="margin-bottom : 0px;">
                                                    <label>Dias de plazo:</label>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input type="number" id="orden_dias_plazo"
                                                                name="orden_dias_plazo" class="form-control "
                                                                placeholder="00" value="0" onkeyup="calcularFecha()"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row clearfix form-horizontal">
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                                    style="margin-bottom : 0px;">
                                                    <label>Tipo de Credito:</label>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="idTipoCredito" type="text" class="form-control "
                                                                placeholder="00" required disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                                    style="margin-bottom : 0px;">
                                                    <label>Fecha Termino:</label>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input type="date" id="orden_fecha_termino"
                                                                name="orden_fecha_termino" class="form-control "
                                                                value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>'
                                                                required readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                            <table class="table table-totalVenta">
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita" width="90">Sub-Total
                                    </td>
                                    <td id="subtotal" width="100" class="derecha-texto negrita"><?php echo number_format($orden->orden_subtotal, 2)?></td>
                                    <input id="idSubtotal" name="idSubtotal" type="hidden" />
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita">Descuento</td>
                                    <td id="descuento" class="derecha-texto negrita"><?php echo number_format($orden->orden_descuento, 2)?></td>
                                    <input id="idDescuento" name="idDescuento" type="hidden" />
                                </tr>
                                <tr>
                                    <td id="porcentajeIva" class="letra-blanca fondo-azul-claro negrita">Tarifa 12 %
                                    </td>
                                    <td id="tarifa12" class="derecha-texto negrita"><?php echo number_format($orden->orden_tarifa12, 2)?></td>
                                    <input id="idTarifa12" name="idTarifa12" type="hidden" />
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita">Tarifa 0%</td>
                                    <td id="tarifa0" class="derecha-texto negrita"><?php echo number_format($orden->orden_tarifa0, 2)?></td>
                                    <input id="idTarifa0" name="idTarifa0" type="hidden" />
                                </tr>
                                <tr>
                                    <td id="iva12" class="letra-blanca fondo-azul-claro negrita">Iva 12 %</td>
                                    <td id="iva" class="derecha-texto negrita"><?php echo number_format($orden->orden_iva, 2)?></td>
                                    <input id="idIva" name="idIva" type="hidden" />
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                    <td id="total" class="derecha-texto negrita"><?php echo number_format($orden->orden_total, 2)?></td>
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
@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompleteCliente.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompleteProducto.js') }}"></script>
@endsection
<script type="text/javascript">
function cargarmetodo() {
    seleccionarIva();

}
var id_item = 1;



document.getElementById("idTarifa0").value = 0;
document.getElementById("idTarifa12").value = 0;
var combo = document.getElementById("orden_porcentaje_iva").value;
var porcentajeIva = <?php echo $orden->orden_porcentaje_iva; ?>;
porcentajeIva = parseFloat(porcentajeIva) / 100;



function agregarItem() {
   
       
        total = Number(document.getElementById("id_total").value);
 
        descuento = Number(total * (document.getElementById("id_descuento").value / 100));
        var linea = $("#plantillaItemOrdenvi").html();
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{Dcantidad}/g, document.getElementById("id_cantidad").value);
        linea = linea.replace(/{Dcodigo}/g, document.getElementById("codigoProducto").value);
        linea = linea.replace(/{DprodcutoID}/g, document.getElementById("idProductoID").value);
        linea = linea.replace(/{Dnombre}/g, document.getElementById("buscarProducto").value);
        if (document.getElementById("tieneIva").checked) {
            linea = linea.replace(/{Diva}/g, "SI");
            linea = linea.replace(/{DViva}/g, Number((total - descuento) * porcentajeIva).toFixed(2));
            iva = "SI";
        } else {
            linea = linea.replace(/{Diva}/g, "NO");
            linea = linea.replace(/{DViva}/g, "0.00");
            iva = "NO";
        }
        
        linea = linea.replace(/{Dpu}/g, document.getElementById("id_pu").value);
        linea = linea.replace(/{Ddescuento}/g, Number(descuento).toFixed(2));
        linea = linea.replace(/{Dtotal}/g, Number(total - descuento).toFixed(2));
        $("#cargarItemorden tbody").append(linea);
        id_item = id_item + 1;
        cargarTotales(iva, total, descuento);
       
        resetearCampos();
    
}

function cargarTotales(iva, total, descuento) {
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
   
    var iva = Number(Number(document.getElementById("tarifa12").innerHTML) * porcentajeIva).toFixed(2);
  
    document.getElementById("iva").innerHTML = iva;
    document.getElementById("idIva").value = iva;

    var total = Number(Number(document.getElementById("tarifa12").innerHTML) + Number(document.getElementById("tarifa0")
        .innerHTML) + Number(document.getElementById("iva").innerHTML)).toFixed(2);

    document.getElementById("total").innerHTML = total;
    document.getElementById("idTotal").value = total;
    document.getElementById("idMontoordendo").value = total;
    document.getElementById("idTotalorden").value = total;
}

function resetearCampos() {
    document.getElementById("id_cantidad").value = 1;
    document.getElementById("codigoProducto").value = "";
    document.getElementById("idProductoID").value = "";
    document.getElementById("buscarProducto").value = "";
    document.getElementById("id_disponible").value = "0";
    document.getElementById("id_pu").value = "0.00";
    document.getElementById("id_descuento").value = "0.00";
    document.getElementById("id_total").value = "0.00";
}

function eliminarItem(id, iva, total, descuento) {
    cargarTotales(iva, total * (-1), descuento * (-1));
    $("#row_" + id).remove();

}

function calcularTotal() {
    document.getElementById("buscarProducto").classList.remove('is-invalid');
    document.getElementById("errorStock").classList.add('invisible');
    if (parseFloat(document.getElementById("id_cantidad").value) > parseFloat(document.getElementById("id_disponible")
            .value)) {
        document.getElementById("id_cantidad").value = 1;
        document.getElementById("buscarProducto").classList.add('is-invalid');
        document.getElementById("errorStock").classList.remove('invisible');
    }
    document.getElementById("id_total").value = Number(document.getElementById("id_cantidad").value * document
        .getElementById("id_pu").value).toFixed(2);
}

function seleccionarIva() {
    var combo = document.getElementById("orden_porcentaje_iva").value;
    porcentajeIva = combo;
    porcentajeIva = parseFloat(porcentajeIva) / 100;
   
    document.getElementById("porcentajeIva").innerHTML = "Tarifa " + combo;
    document.getElementById("iva12").innerHTML = "Iva " + combo;
}



function calcularFecha() {
    let hoy = new Date();
    let semMiliSeg = 1000 * 60 * 60 * 24 * document.getElementById("orden_dias_plazo").value;
    let suma = hoy.getTime() + semMiliSeg;
    let fecha = new Date(suma);
    document.getElementById("orden_fecha_termino").value = fecha.getFullYear() + '-' + ponerCeros(fecha.getMonth() +
        1) + '-' + ponerCeros(fecha.getDate());
}

function ponerCeros(num) {
    num = num + '';
    while (num.length <= 1) {
        num = '0' + num;
    }
    return num;
}
</script>
@endsection