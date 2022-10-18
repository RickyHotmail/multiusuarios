@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST"  action="{{ url("transferencia") }} " onsubmit="return validarForm();">
        @csrf
        <div class="card-header">
            <div class="row">
               
                <div class="col-sm-12">
                    <div class="float-right">
                        <button type="button" id="nuevoID" onclick="nuevo()" class="btn btn-primary btn-sm"><i
                                class="fas fa-receipt"></i><span> Nuevo</span></button>
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm" disabled><i
                                class="fa fa-save"></i><span> Guardar</span></button>
                        <button type="button" id="cancelarID" name="cancelarID" onclick="javascript:location.reload()"
                            class="btn btn-danger btn-sm not-active-neo" disabled><i
                                class="fas fa-times-circle"></i><span> Cancelar</span></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row clearfix form-horizontal">
                        
                        
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Siembras</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select class="form-control custom-select" id="idsiembra"
                                    name="idsiembra" data-live-search="true" onchange="cargarDatos();" required>
                                        <option value="" label>--Seleccione una opcion--</option>
                                        @foreach($siembras as $siembra)
                                        <option value="{{$siembra->siembra_id}}">{{$siembra->siembra_codigo}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Area</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="Area"
                                        name="Area"
                                       value=""  readonly>
                                
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Siembra</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="SiembraN"
                                        name="SiembraN"
                                       value="" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Volumen</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="Volumen"
                                        name="Volumen"
                                       value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Laboratorio</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="Laboratorio"
                                        name="Laboratorio"
                                       value="" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Sistema de Cultivo</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="SistemaCultivo"
                                        name="SistemaCultivo"
                                       value="" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Origen Nauplio</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="OrigenNauplio"
                                        name="OrigenNauplio"
                                       value="" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Densidad</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="Densidad"
                                        name="Densidad"
                                       value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Longitud Larva</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="LongitudLarva"
                                        name="LongitudLarva"
                                       value="" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Peso</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="Peso"
                                        name="Peso"
                                       value="" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Inicio Siembra</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="InicioSiembra"
                                        name="InicioSiembra"
                                       value="" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Inicio Costo</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="InicioCosto"
                                        name="InicioCosto"
                                       value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Costo</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="text" class="form-control" id="CostoN"
                                        name="CostoN"
                                       value="" readonly>
                            </div>
                        </div>
                       
                      
                        

                    </div>
                    
                    <div class="row" style="background: #dadada; padding-top: 20px;margin-top: 5px;">
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Piscina Transferir</label></center>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <select class="custom-select select2" id="idPiscina" name="idPiscina" onchange="codigo();" required>
                                            <option value="0" label>--Seleccione una opcion--</option>
                                            @foreach($piscinas as $piscina)
                                            <option value="{{$piscina->piscina_id}}">{{$piscina->piscina_nombre}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Codigo</label></center>
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <input id="id_Codigo" name="id_Codigo" type="text" class="form-control centrar-texto"
                                        value="" readonly>
                                <input type="hidden" id="idSecuencial" name="idSecuencial" 
                                        value="" >
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Area</label></center>
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 0px;">
                           
                            <div class="form-group">
                                <div class="form-line">
                                    <input  id="id_Area"
                                        name="id_Area" type="number" class="form-control"   onkeyup="calculo();" onclick="calculo();" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Longitud Juvenil</label></center>
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <input id="id_Longitud" name="id_Longitud" type="text" class="form-control centrar-texto"
                                        value="0.00" disabled>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Volumen</label></center>
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input  id="id_Volumen" name="id_Volumen"
                                        type="text" class="form-control centrar-texto"  value="0.00" disabled>
                                </div>
                            </div>
                        </div>
                       
                        
                    </div>
                    <div class="row" style="background: #dadada;">
                       
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Numero Juvenil</label></center>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_Numero" name="id_Numero" type="text" class="form-control centrar-texto"
                                        value="0.00" onkeyup="calculo();" onclick="calculo();" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Cosecha Juvenil</label></center>
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 0px;">
                           
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_Cosecha" name="id_Cosecha" type="text" class="form-control centrar-texto"
                                        value="0.00" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Peso Juvenil</label></center>
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <input id="id_Peso" name="id_Peso" type="text" class="form-control centrar-texto"
                                        value="0.00" disabled>
                            </div>
                        </div>
                       
                        
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Juvenil</label></center>
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <input id="id_Juvenil" name="id_Juvenil" type="text" class="form-control centrar-texto"
                                        value="0.00" disabled>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Transferencia</label></center>
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <input id="id_Transferencia" name="id_Transferencia" type="text" class="form-control centrar-texto"
                                         value="0.00" disabled>
                            </div>
                        </div>
                       
                       
                    </div>
                    <div class="row" style="background: #dadada;">  
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Fecha</label></center>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input  id="id_Fecha" name="id_Fecha"
                                        type="date" class="form-control centrar-texto"   value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Densidad</label></center>
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <input id="id_Densidad" name="id_Densidad" type="text" class="form-control centrar-texto"
                                        value="0.00" disabled >
                            </div>
                        </div>
                        
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Sistema Cultivo</label></center>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <input id="id_Cultivo" name="id_Cultivo" type="text" class="form-control centrar-texto"
                                         value=""  readonly>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                            <center><button type="button" id="addID" name="addID"  onclick="agregarItem();" class="btn btn-primary" disabled><i
                                class="fas fa-plus" ></i><span> Agregar</span></button>
                            </center>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 c|ol-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                                @include ('admin.camaronera.transferencia.items')
                                <table id="cargarItem"
                                    class="table table-striped table-hover boder-sar tabla-item-factura sin-salto"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                            <th></th>
                                            <th >Piscina</th>
                                            <th>Codigo</th>
                                            <th >Area</th>
                                            <th >Fecha</th>
                                            <th >Volumen</th>
                                            <th >Cose. Juv.</th>
                                            <th >N° Juv.</th>
                                            <th >Pes. Juv.</th>
                                            <th >Juvenil</th>
                                            <th >Trasnferencia</th>
                                            <th>Long. Juv.</th>
                                            <th>Densidad</th>
                                            <th>Sist. Cult.</th>
                                            
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
                            
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  form-control-label">
                                <table class="table table-totalVenta">
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita" width="90">Total
                                        </td>
                                        <td id="Total" width="100" class="derecha-texto negrita">0.00</td>
                                        <input id="idTotal" name="idTotal" type="hidden" value="0" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Total Agregado</td>
                                        <td id="Subtotal" class="derecha-texto negrita">0.00</td>
                                        <input id="idSubtotal" name="idSubtotal" type="hidden"  value="0"/>
                                    </tr>
                                    <tr>
                                        <td  class="letra-blanca fondo-azul-claro negrita">Diferencia
                                        </td>
                                        <td id="Diferencia" class="derecha-texto negrita">0.00</td>
                                        <input id="idDiferencia" name="idDiferencia" type="hidden" value="0" />
                                    </tr>
                                    
                                </table>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    var id_item = 1;
   
    function cargarDatos(){
        $.ajax({
            url: '{{ url("Siembra/searchN") }}'+ '/' + document.getElementById("idsiembra").value,
            dataType: "json",
            type: "GET",
            data: {
    
            },
            success: function(data){
                document.getElementById("Area").value = data[0].piscina_espejo_agua;       
                document.getElementById("Volumen").value = data[0].piscina_volumen_agua;   
                document.getElementById("Laboratorio").value = data[0].laboratorio_nombre; 
                document.getElementById("SistemaCultivo").value = data[0].siembra_cultivo;    
                document.getElementById("OrigenNauplio").value = data[0].nauplio_nombre; 
                document.getElementById("LongitudLarva").value = data[0].siembra_longitud; 
                document.getElementById("Peso").value = data[0].siembra_peso; 
                document.getElementById("InicioSiembra").value = data[0].siembra_fecha_siembra;    
                document.getElementById("InicioCosto").value = data[0].siembra_fecha_costo; 
                document.getElementById("SiembraN").value = data[0].siembra_larvas; 
                document.getElementById("Densidad").value = data[0].siembra_densidad; 
                document.getElementById("Total").innerHTML = data[0].piscina_volumen_agua; 
                document.getElementById("CostoN").value = data[0].siembra_costo;  
                document.getElementById("Total").innerHTML = round(data[0].siembra_larvas);   
                document.getElementById("idTotal").value = round(data[0].siembra_larvas);  
            },
        });
        segumineto();
        codigo();
    }
    function segumineto(){    
        $.ajax({
            url: '{{ url("SiembraM/searchN") }}'+ '/' + document.getElementById("idsiembra").value,
            dataType: "json",
            type: "GET",
            data: {
    
            },
            success: function(data){
                document.getElementById("id_Cultivo").value = data[0];       
            },
        });
    }
    function calculo(){
        document.getElementById("id_Densidad").value=round(document.getElementById("id_Numero").value/document.getElementById("id_Area").value);
    }
    function codigo(){
        var codigo='';
        if(document.getElementById("id_Cultivo").value=='MONOFASICO'){
            codigo='.M';
        }
        if(document.getElementById("id_Cultivo").value=='BIFASICO'){
            codigo='.B';
        }
        if(document.getElementById("id_Cultivo").value=='TRIFASICO'){
            codigo='.T';
        }
        $.ajax({
        url: '{{ url("codigosiembra") }}'+'/'+document.getElementById("idPiscina").value,
        dataType: "json",
        type: "GET",
        data: { 
         
        },
        success: function(data){           
            document.getElementById("id_Codigo").value=data[0]+codigo;
            document.getElementById("idSecuencial").value=data[1];
            document.getElementById("id_Volumen").value=data[2];
            document.getElementById("id_Area").value=data[3];
        },
    });
   
    }
    function round(num) {
        var m = Number((Math.abs(num) * 100).toPrecision(15));
         m =Math.round(m) / 100 * Math.sign(num);
         return (m).toFixed(2);
    } 
    function agregarItem() {
      
        piscina = document.getElementById("idPiscina");
      
        piscinaid = piscina.options[piscina.selectedIndex].text;

        if(document.getElementById("idPiscina").value != '' ){
       
        total = round(document.getElementById("id_Numero").value);
        var linea = $("#plantillaItem").html();
                linea = linea.replace(/{ID}/g, id_item);
                linea = linea.replace(/{Didpiscina}/g, document.getElementById("idPiscina").value);
                linea = linea.replace(/{Dsecuencial}/g, document.getElementById("idSecuencial").value);
               
                linea = linea.replace(/{Dpiscina}/g, piscinaid);
                linea = linea.replace(/{Dcodigo}/g, document.getElementById("id_Codigo").value);
                linea = linea.replace(/{Darea}/g, document.getElementById("id_Area").value);
                
                linea = linea.replace(/{Dvolumen}/g, round(document.getElementById("id_Volumen").value));
                linea = linea.replace(/{Dcjuve}/g, round(document.getElementById("id_Cosecha").value));
                linea = linea.replace(/{Dnumero}/g, round(document.getElementById("id_Numero").value));
                linea = linea.replace(/{Dpjuve}/g, round(document.getElementById("id_Peso").value));
                linea = linea.replace(/{Dfecha}/g, (document.getElementById("id_Fecha").value));
                linea = linea.replace(/{Djuve}/g, round(document.getElementById("id_Juvenil").value));
                linea = linea.replace(/{Dtrasn}/g, round(document.getElementById("id_Transferencia").value));
                linea = linea.replace(/{Dljuve}/g, round(document.getElementById("id_Longitud").value));
                linea = linea.replace(/{Ddensidad}/g, round(document.getElementById("id_Densidad").value));
                linea = linea.replace(/{Dscult}/g, document.getElementById("id_Cultivo").value);
               
                $("#cargarItem tbody").append(linea);
                id_item = id_item + 1;
                sumatotal(total);
                resetearCampos();
               // resetearCampos();
               
            
        }else{
            bootbox.alert({
                message: "Seleccione un centro de consumo.",
                size: 'small'
            });
        }
    } 
    function eliminarItem(id, valorI) {
    $("#row_" + id).remove();
    sumatotal(valorI * (-1));
}
function sumatotal(valorI) {
    document.getElementById("Subtotal").innerHTML=round(Number(document.getElementById("Subtotal").innerHTML)+Number(valorI));
    document.getElementById("idSubtotal").value = document.getElementById("Subtotal").innerHTML;
    document.getElementById("Diferencia").innerHTML = round(Number(document.getElementById("Total").innerHTML)-Number(document.getElementById("Subtotal").innerHTML));
    document.getElementById("idDiferencia").value = (document.getElementById("Diferencia").innerHTML);
}
function nuevo() {
    $('#idsiembra').css('pointer-events', 'none');
    document.getElementById("id_Area").disabled = false;
    document.getElementById("id_Volumen").disabled = false;
    document.getElementById("id_Numero").disabled = false;
    document.getElementById("id_Cosecha").disabled = false;
    document.getElementById("id_Peso").disabled = false;
    document.getElementById("id_Juvenil").disabled = false;
    document.getElementById("id_Transferencia").disabled = false;
    document.getElementById("id_Fecha").disabled = false;
    document.getElementById("id_Densidad").disabled = false;
    document.getElementById("id_Longitud").disabled = false;
    document.getElementById("addID").disabled = false; 
    document.getElementById("nuevoID").disabled = true; 
    document.getElementById("guardarID").disabled = false; 
    document.getElementById("cancelarID").disabled = false;
    document.getElementById("id_Transferencia").disabled = false;
}
function resetearCampos() {
    document.getElementById("idPiscina").value = "0";
    
    document.getElementById("id_Area").value = "0.00";
    document.getElementById("id_Longitud").value = "0.00";
    document.getElementById("id_Volumen").value = "0.00";
    document.getElementById("id_Numero").value = "0.00";
    document.getElementById("id_Cosecha").value = "0.00";
    document.getElementById("id_Peso").value = "0.00";
    document.getElementById("id_Juvenil").value = "0.00";
    document.getElementById("id_Transferencia").value = "0.00";
    document.getElementById("id_Densidad").value = "0.00";

}

</script>
    
@endsection