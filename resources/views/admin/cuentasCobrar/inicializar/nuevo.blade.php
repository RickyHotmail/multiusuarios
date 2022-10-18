@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST"  action="{{ url("inicializarCXC") }} " onsubmit="return validacion()">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Inicializar Cuentas por Cobrar</h3>
            <div class="float-right">
                <button type="submit" id="guardar" name="guardar" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="Guardar"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--
                <button type="button" onclick='window.location = "{{ url("inicializarCXC") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="form-group row">
                <label for="diario_id" class="col-sm-1 col-form-label">Diarios : </label>
                <div class="col-sm-3">
                    <input type="hidden" id="diario_id" name="diario_id" value="{{$diarioC->diario_id}}"/>
                    <label class="form-control">{{$diarioC->diario_codigo}}</label>                   
                </div>
                <label for="idfecha" class="col-sm-1 col-form-label">Fecha : </label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idfecha" name="idfecha"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                </div>                    
            </div>          
            <div class="row table-ini-cuentas">
                <div class="col-sm-12">
                    <table id="item" class="table table-striped table-hover boder-sar tabla-item-factura" style="white-space: normal!important; border-collapse: collapse;">
                        <thead>
                            <tr class="letra-blanca" style="background-color: #0c7181;">
                                <th></th>
                                <th>CÓDIGO</th>
                                <th>CUENTA</th>
                                <th>DESCRIPCIÓN </th>
                                <th class="centrar-texto">DEBE</th>
                                <th class="centrar-texto">HABER</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($diarioC))
                            <?php $debe = 0; $haber = 0; ?>
                            @foreach($diarioC->detalles as $detalle)
                            <?php $debe = $debe + $detalle->detalle_debe; $haber = $haber + $detalle->detalle_haber; ?>
                            <tr>
                                <td></td>
                                <td class="sin-salto"><b>{{ $detalle->cuenta->cuentaPadre->cuenta_numero }}</b></td>
                                <td class="sin-salto"><b>{{ $detalle->cuenta->cuentaPadre->cuenta_nombre }}</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><input type="radio" name="checkbox1[]" value="{{ $detalle->detalle_id}}" onchange="totalSeleccionado('{{ $detalle->detalle_debe }}','{{ $detalle->detalle_haber }}');" required></td>
                                <td class="sin-salto">{{ $detalle->cuenta->cuenta_numero }}</td>
                                <td class="sin-salto">&emsp;{{ $detalle->cuenta->cuenta_nombre }}</td>
                                <td>{{ $detalle->detalle_tipo_documento.' - '.$detalle->detalle_comentario }}</td>
                                <td class="centrar-texto">{{ number_format($detalle->detalle_debe,2) }}</td>
                                <td class="centrar-texto">{{ number_format($detalle->detalle_haber,2) }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                    <center><label class="col-form-label">TOTAL SELECCIONADO</label></center>
                        <input type="text" class="form-control centrar-texto" id="Idselec" name="Idselec" value="0.00" placeholder="0.00" readonly>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                    <center><label class="col-form-label">TOTAL CXC</label></center>
                        <input type="text" class="form-control centrar-texto" id="Idcxc" name="Idcxc" value="0.00" placeholder="0.00" readonly>
                    </div>
                </div>
                <div class="col-sm-4">
                    
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                    <center><label class="col-form-label">DEBE</label></center>
                        <input type="text" class="form-control centrar-texto" id="IdDebe" name="IdDebe"  @if(isset($diarioC)) value="{{number_format($debe,2)}}" @endif placeholder="0.00" readonly>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                    <center><label class="col-form-label">HABER</label></center>
                        <input type="text" class="form-control centrar-texto" id="IdHaber" name="IdHaber"  @if(isset($diarioC)) value="{{number_format($haber,2)}}" @endif placeholder="0.00" readonly>
                    </div>
                </div>
            </div>
            @if(isset($diarioC))
            <h5 class="form-control" style="color:#fff; background:#0c7181;">Cuentas por Cobrar : </h5>
            <div class="form-group row">
                <div class="col-sm-3" style="margin-bottom: 0px;">
                    <label>Cliente</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input id="clienteID" name="clienteID" type="hidden">
                            <input id="buscarCliente" name="buscarCliente" type="text" class="form-control" placeholder="Cliente">
                        </div>
                    </div>
                </div>
                <div class="col-sm-2" style="margin-bottom: 0px;">
                    <label>No. Factura</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input id="IDFactura" name="IDFactura" type="text" class="form-control" placeholder="001001000000001">
                        </div>
                    </div>
                </div>
                <div class="col-sm-2" style="margin-bottom: 0px;">
                    <label>Fecha</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="date" id="IDFecha" name="IDFecha" class="form-control" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>'>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2" style="margin-bottom: 0px;">
                    <label>Vencimiento</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="date" id="IDFechaV" name="IDFechaV" class="form-control" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>'>
                        </div>
                    </div>
                </div>
                <div class="col-sm-1" style="margin-bottom: 0px;">
                    <label>Valor</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input id="IDValor" name="IDValor" class="form-control" value="0.00">
                        </div>
                    </div>
                </div>
                <div class="col-sm-1" style="margin-bottom: 0px;">
                    <label>Saldo</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input id="IDSaldo" name="IDSaldo" class="form-control" value="0.00">
                        </div>
                    </div>
                </div>
                <div class="col-sm-1">
                    <a onclick="agregarItem();" class="btn btn-primary btn" style="margin-top: 30px;"><i class="fas fa-plus"></i></a>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-12" style="margin-bottom: 0px;">
                    @include ('admin.cuentasCobrar.inicializar.item')
                    <table id="cargarItem" class="table table-striped table-hover boder-sar tabla-item-factura" >
                        <thead>
                            <tr class="letra-blanca" style="background-color: #0c7181;">
                                <th class="filaDelgada15 text-center">Cliente</th>
                                <th class="filaDelgada15 text-center">Numero</th>
                                <th class="filaDelgada15 text-center">Valor</th>
                                <th class="filaDelgada15 text-center">Saldo</th>
                                <th class="filaDelgada15 text-center">Fecha</th>
                                <th class="filaDelgada15 text-center">Vence</th>
                                <th width="10"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $count = 1; $totalS = 0; ?>
                        @if(isset($datos))
                            @for ($i = 1; $i <= count($datos); ++$i)    
                                <tr id="row_{{ $count }}">
                                    <td class="filaDelgada15" style="padding-left: 10px !important;">{{$datos[$i]['cliNombre']}}<input type="hidden" name="Dcliente[]" value="{{$datos[$i]['cliId']}}"/></td>
                                    <td class="filaDelgada15 text-center">{{$datos[$i]['numero']}}<input type="hidden" name="Dnumero[]" value="{{$datos[$i]['numero']}}" readonly/></td>
                                    <td class="filaDelgada15 text-center">{{$datos[$i]['valor']}}<input type="hidden" name="Dvalor[]" value="{{$datos[$i]['valor']}}" readonly/></td>
                                    <td class="filaDelgada15 text-center">{{$datos[$i]['saldo']}}<input type="hidden" name="Dsaldo[]" value="{{$datos[$i]['saldo']}}" readonly/></td>
                                    <td class="filaDelgada15 text-center">{{$datos[$i]['fecha']}}<input type="hidden" name="Dfecha[]" value="{{$datos[$i]['fecha']}}" readonly/></td>
                                    <td class="filaDelgada15 text-center">{{$datos[$i]['vencimiento']}}<input type="hidden" name="Dvence[]" value="{{$datos[$i]['vencimiento']}}" readonly/></td>
                                    <td><a onclick="eliminarItem({{ $count }});" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
                                </tr>
                                <?php $count = $count + 1; $totalS=$totalS+$datos[$i]['saldo']; ?>
                            @endfor
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</form>
@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompleteClienteCxC.js') }}"></script>
@endsection
<script type="text/javascript">
    id_item = '<?=$count?>';
    id_item = Number(id_item);
    document.getElementById("Idcxc").value = Number('<?=$totalS?>').toFixed(2);
    function totalSeleccionado(debe,haber){
        if(debe > 0){
            document.getElementById("Idselec").value = Number(debe).toFixed(2);
        }else{
            document.getElementById("Idselec").value = Number(haber).toFixed(2);
        }
        
    }
    function agregarItem() {
        if(document.getElementById("buscarCliente").value != '' && document.getElementById("IDFactura").value != ''
        && document.getElementById("IDValor").value != '' && document.getElementById("IDValor").value >0
        && document.getElementById("IDSaldo").value != '' && document.getElementById("IDSaldo").value > 0){
            var linea = $("#plantillaItem").html();
            linea = linea.replace(/{ID}/g, id_item);
            linea = linea.replace(/{Cliente}/g, document.getElementById("buscarCliente").value);
            linea = linea.replace(/{Dcliente}/g, document.getElementById("clienteID").value);
            linea = linea.replace(/{Dnumero}/g, document.getElementById("IDFactura").value);
            linea = linea.replace(/{Dvalor}/g, Number(document.getElementById("IDValor").value).toFixed(2));
            linea = linea.replace(/{Dsaldo}/g, Number(document.getElementById("IDSaldo").value).toFixed(2));
            linea = linea.replace(/{Dfecha}/g, document.getElementById("IDFecha").value);
            linea = linea.replace(/{Dvence}/g, document.getElementById("IDFechaV").value);
            $("#cargarItem tbody").append(linea);
            id_item = id_item + 1;
            resetearCampos();
            calcularSeleccion();
        }
    }
    function resetearCampos() {
        document.getElementById("clienteID").value = "";
        document.getElementById("buscarCliente").value = "";
        document.getElementById("IDFactura").value = "";
        document.getElementById("IDValor").value = "0.00";
        document.getElementById("IDSaldo").value = "0.00";
    }
    function eliminarItem(id) {
        $("#row_" + id).remove();
        id_item = id_item - 1; 
        calcularSeleccion();
    }
    function calcularSeleccion(){
        document.getElementById("Idcxc").value = '0.00';
        for (var i = 1; i < id_item; i++) {
            document.getElementById("Idcxc").value = Number(Number(document.getElementById("Idcxc").value) + Number($("input[name='Dsaldo[]']")[i].value)).toFixed(2);
        }
    }
    function validacion(){
        if(document.getElementById("Idselec").value != document.getElementById("Idcxc").value){
            bootbox.alert({
            message: "El valor seleccionado no es igual al valor total de cuentas por cobrar.",
                size: 'small'
            });
            return false
        }
        return true;
    }
</script>
@endsection