@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" ">
        @method('DELETE')
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Egreso de Bodega</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                                <!--     
                                <a href="{{ url("egresoBodega") }}" class="btn btn-danger btn-sm not-active-neo"><i
                                class="fas fa-times-circle"></i><span> Cancelar</span></a>  
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
                            <label>Numero</label>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                            <div class="form-group">
                                <div class="form-line">
                                    
                                    <input id="egreso_id" name="egreso_id" value="{{$egreso->cabecera_egreso_id}}" type="hidden">   
                                    <label class="form-control" id="egreso_serie" name="egreso_serie">{{$egreso->cabecera_egreso_serie}}</label>
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="egreso_numero" name="egreso_numero"  value="{{substr(str_repeat(0, 9). $egreso->cabecera_egreso_numero , - 9)}}">{{substr(str_repeat(0, 9). $egreso->cabecera_egreso_numero , - 9)}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Fecha :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                <label class="form-control" id="egreso_fecha" name="egreso_fecha"  value="{{$egreso->cabecera_egreso_fecha}}">{{$egreso->cabecera_egreso_fecha}}</label>
                                 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1  form-control-label  centrar-texto"
                            style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label>Bodega :</label>
                            </div>
                        </div>  
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 alinear-izquierda" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control"   value="{{$egreso->bodega->bodega_nombre}}">{{$egreso->bodega->bodega_nombre}}</label>  
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Destino :</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                            <div class="form-group">
                                 <label class="form-control"  value="{{$egreso->cabecera_egreso_destino}}">{{$egreso->cabecera_egreso_destino}}</label>
                                
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Destinatario :</label>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control"   value="{{$egreso->cabecera_egreso_destinatario}}">{{$egreso->cabecera_egreso_destinatario}}</label>           
                                   
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row clearfix form-horizontal">

                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Motivo :</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control"   value="{{$egreso->cabecera_egreso_motivo}}">{{$egreso->cabecera_egreso_motivo}}</label>  
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Movimiento:</label>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control"   value="{{$egreso->tipo->tipo_nombre}}">{{$egreso->tipo->tipo_nombre}}</label>  
                            </div>
                        </div>
                        @if(Auth::user()->empresa->empresa_contabilidad == '1')
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>N° DIARIO:</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control"   value="{{$egreso->diario->diario_codigo}}">{{$egreso->diario->diario_codigo}}</label>  
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row" style="display: none;">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" style="margin-bottom: 0px;">
                            <label>Nombre de Producto</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="codigoProducto" name="idProducto" type="hidden">
                                    <input id="idProductoID" name="idProductoID" type="hidden">
                                    <input id="buscarProducto" name="buscarProducto" type="text" class="form-control"
                                        placeholder="Buscar producto" >
                                        <input id="descripcionProducto" name="descripcionProducto" type="text" class="form-control"
                                         >    
                                    <span id="errorStock" class="text-danger invisible">El producto no tiene stock
                                        disponible.</span>
                                </div>
                            </div>
                        </div>
                       
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>Disponible</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_disponible" name="id_disponible" type="number" class="form-control"
                                        placeholder="Disponible" value="0" >
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>Cantidad</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_cantidad"
                                        name="id_cantidad" type="number" class="form-control" placeholder="Cantidad"
                                        value="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>Precio</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_pu" name="id_pu"
                                        type="text" class="form-control" placeholder="Precio" value="0.00">
                                </div>
                            </div>
                        </div>
                       
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>Total</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_total" name="id_total" type="text" class="form-control"
                                        placeholder="Total" value="0.00" >
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row" style="display: none;">
                        
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="cuentaProductoID" name="cuentaProductoID"
                                    data-live-search="true">
                                    @foreach($cuentas as $cuenta)
                                    <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero}}
                                        {{$cuenta->cuenta_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>C. Consumo</label>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="idconsumo" name="idconsumo"
                                    data-live-search="true">
                                    @foreach($centros as $centro)
                                    <option value="{{$centro->centro_consumo_id}}">{{$centro->centro_consumo_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                       
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                                @include ('admin.inventario.egresoBodega.itemEgresoPresentar')
                                <table id="cargarItemegreso"
                                    class="table table-striped table-hover boder-sar tabla-item-orden"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                        <th>Cantidad</th>
                                            <th>Codigo</th>
                                            <th>Producto</th>
                                           
                                            <th>P.U.</th>
                                            <th>Total</th>
                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row" >
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
                                                            <div class="form-control ">{{ $egreso->cabecera_egreso_comentario }}</div>
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
                                    <td id="subtotal" width="100" class="derecha-texto negrita">0.00</td>
                                    <input id="idSubtotal" name="idSubtotal" type="hidden" />
                                </tr>
                    
                               
                                <tr>
                                    <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                    <td id="total" class="derecha-texto negrita">0.00</td>
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

@endsection
<script type="text/javascript">


    function cargarmetodo() {
    
        <?php
        
            foreach ($egreso->detalles as $x) {
                ?>  
           
                document.getElementById("id_total").value=Number('<?php echo($x->detalle_egreso_precio_unitario * $x->detalle_egreso_cantidad); ?>');
                document.getElementById("id_cantidad").value='<?php echo $x->detalle_egreso_cantidad; ?>';
                document.getElementById("codigoProducto").value='<?php echo $x->producto->producto_codigo; ?>';
                document.getElementById("idProductoID").value='<?php echo $x->producto->producto_id; ?>';
                document.getElementById("buscarProducto").value='<?php echo $x->producto->producto_nombre; ?>';
                document.getElementById("id_pu").value=Number('<?php echo $x->detalle_egreso_precio_unitario; ?>');

                agregarItem();

        <?php
            }

        ?>
    }
    var id_item = 1;



    function nuevo() {

        $('#bodega_id').css('pointer-events', 'none');
    
    }


    function agregarItem() {
    
            var combo2 = document.getElementById("idconsumo");
            total = Number(document.getElementById("id_total").value);
            
            var linea = $("#plantillaItemEgresoprese").html();
            linea = linea.replace(/{ID}/g, id_item);
            linea = linea.replace(/{Dcantidad}/g, document.getElementById("id_cantidad").value);
            linea = linea.replace(/{Dcodigo}/g, document.getElementById("codigoProducto").value);
            linea = linea.replace(/{DprodcutoID}/g, document.getElementById("idProductoID").value);
            linea = linea.replace(/{Dnombre}/g, document.getElementById("buscarProducto").value);
            linea = linea.replace(/{Dpu}/g, document.getElementById("id_pu").value);
        
            linea = linea.replace(/{Dtotal}/g, Number(total).toFixed(2));
            $("#cargarItemegreso tbody").append(linea);
            id_item = id_item + 1;
           
            cargarTotales(total);
           
            resetearCampos();

  
        
    }

    function cargarTotales(total) {
       
        var subtotal = Number(Number(document.getElementById("subtotal").innerHTML) + total).toFixed(2);
     
        document.getElementById("subtotal").innerHTML = subtotal;
        document.getElementById("idSubtotal").value = subtotal;
       
        document.getElementById("total").innerHTML = subtotal;
        document.getElementById("idTotal").value = subtotal;
     
      
    }


    function resetearCampos() {
        document.getElementById("id_cantidad").value = 1;
        document.getElementById("codigoProducto").value = "";
        document.getElementById("idProductoID").value = "";
        document.getElementById("descripcionProducto").value = "";
        document.getElementById("buscarProducto").value = "";
        document.getElementById("id_disponible").value = "0";
        document.getElementById("id_pu").value = "0.00";
        
    }

    function eliminarItem(id, total) {
        cargarTotales( total * (-1));
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

    function calcularFecha() {
        let hoy = new Date();
        let semMiliSeg = 1000 * 60 * 60 * 24 * document.getElementById("egreso_dias_plazo").value;
        let suma = hoy.getTime() + semMiliSeg;
        let fecha = new Date(suma);
        document.getElementById("egreso_fecha_termino").value = fecha.getFullYear() + '-' + ponerCeros(fecha.getMonth() +
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