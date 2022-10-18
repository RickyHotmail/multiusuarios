@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("ventaActivo") }}">
@csrf
    <div class="card card-secondary">    
    <!-- /.card-header -->
        <div class="card-header">
                <div class="row">
                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                        <h2 class="card-title">Nueva Venta activo</h2>
                    </div>
                    <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                        <div class="float-right">                           
                            <button id="guardarID" type="submit" class="btn btn-success btn-sm"><i
                                    class="fa fa-save"></i><span> Guardar</span></button>
                            <button id="idrecalcular" type="button" onclick="calcularValores()" class="btn btn-info btn-sm"><i
                                    class="fa fa-save"></i><span> Recalcular</span></button>
                            <a href="{{ url("ventaActivo")}}"><button type="button" id="cancelarID" name="cancelarID"
                                class="btn btn-default btn-sm not-active-neo"><i
                                    class="fas fa-times-circle"></i><span> Atras</span></button></a>
                        </div>
                    </div>
                </div>
        </div>
            <div class="card-body">
                        <div class="form-group row">
                            <label for="idSucursal" class="col-sm-3 col-form-label">Sucursal</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idSucursal" name="idSucursal" onchange="cargarActivoxSucursal();" required>
                                    <option value="" label>--Seleccione una Sucursal--</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idActivoFijo" class="col-sm-3 col-form-label">Activo Fijo</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idActivoFijo" name="idActivoFijo" onchange="cargarActivo();" require>
                                    <option value="" label>--Seleccione un Activo Fijo--</option>                                    
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row">
                                <label for="porcentaje_depreciacion" class="col-sm-3 col-form-label">% Depreciacion</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="porcentaje_depreciacion" name="porcentaje_depreciacion" value="0" readonly required>
                                </div>
                        </div>                       
                        <div class="form-group row">
                                <label for="idValor" class="col-sm-3 col-form-label">Valor</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idValor" name="idValor" value="0" onchange="calcularValores();" required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idVidaUtil" class="col-sm-3 col-form-label">% Vida Util</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idVidaUtil" name="idVidaUtil" value="0" onchange="calcularValores();" readonly required>
                                </div>
                        </div>                  
                        <div class="form-group row">
                                <label for="idValorUtil" class="col-sm-3 col-form-label">Valor vida Util</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idValorUtil" name="idValorUtil" value="0.00"  readonly required>
                                </div>
                        </div>                                      
                        <div class="form-group row">
                                <label for="idBaseDepreciar" class="col-sm-3 col-form-label">Base a depreciar</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idBaseDepreciar" name="idBaseDepreciar" value="0.00" readonly required>
                                </div>
                        </div>                  
                        <div class="form-group row">
                                <label for="idDepreciacionMensual" class="col-sm-3 col-form-label">Depreciacion Mensual</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idDepreciacionMensual" name="idDepreciacionMensual" value="0.00" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idDepreciacionAnual" class="col-sm-3 col-form-label">Depreciacion Anual</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idDepreciacionAnual" name="idDepreciacionAnual" value="0.00"  readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idDepreciacionAcumulada" class="col-sm-3 col-form-label">Depreciacion Acumulada</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idDepreciacionAcumulada" name="idDepreciacionAcumulada" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idTotalVentas" class="col-sm-3 col-form-label">Total de Ventas</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idTotalVentas" name="idTotalVentas" value="0.00" readonly required>
                                </div>
                        </div>
                        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Venta</h5>
                            <div class="form-group row">
                                <label for="idFecha" class="col-sm-3 col-form-label">Fecha</label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="idFecha" name="idFecha"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                                </div>
                            </div>                                           
                            <div class="form-group row">
                                <label for="idMonto" class="col-sm-3 col-form-label">Monto</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="idMonto" name="idMonto" onchange="restarVenta(); "value="0" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="idDescripcion" class="col-sm-3 col-form-label">Descripcion</label>
                                <div class="col-sm-9">                                    
                                    <textarea type="text" class="form-control" id="idDescripcion" name="idDescripcion" placeholder="Descripcion" required></textarea>
                                </div>
                            </div> 
            </div>
    <!-- /.card-body -->
    </div>
<!-- /.card -->
</form>
<script type="text/javascript">
function totalVentas(){    
    $.ajax({
        url: '{{ url("sumaVentasActivo/searchN") }}'+ '/' +document.getElementById("idActivoFijo").value,
        dataType: "json",
        type: "GET",
        data3: {
            buscar: document.getElementById("idActivoFijo").value
        },        
        success: function(data3){ 
            document.getElementById("idTotalVentas").value = Number(Number(data3.venta_monto)) + Number(document.getElementById("idMonto").value);
                                   
        },
    });    
}
function calcularValores(){
     if (document.getElementById("idValor").value != "" && document.getElementById("idVidaUtil").value != "") {
        //valor de vida util
            document.getElementById("idValorUtil").value = Number(parseFloat(document.getElementById("idValor").value) *
            parseFloat(document.getElementById("idVidaUtil").value) / parseFloat(100)).toFixed(2);
        //base a depreciar idBaseDepreciar
            document.getElementById("idBaseDepreciar").value = Number(parseFloat(document.getElementById("idValor").value) -
            parseFloat(document.getElementById("idValorUtil").value)).toFixed(2);            
            //depreciacion anual

            document.getElementById("idDepreciacionAnual").value = Number(Number(document.getElementById("idBaseDepreciar").value) *
            Number(document.getElementById("porcentaje_depreciacion").value) / parseFloat(100)).toFixed(2);           
            //depreciaicon mensual
            document.getElementById("idDepreciacionMensual").value = Number(Number(document.getElementById("idDepreciacionAnual").value) /
            parseFloat(12)).toFixed(2);
        } else {
            document.getElementById("idValorUtil").value = 0;
        } 
    }
    function restarVenta(){
        
        //idDepreciacionAnual
        if (document.getElementById("idTotalVentas").value != "") {            
            //base a depreciar idBaseDepreciar
            document.getElementById("idValor").value =Number(Number(document.getElementById("idValor").value) -
            parseFloat(document.getElementById("idMonto").value)).toFixed(2);            
        } else {
            document.getElementById("idMonto").value = 0;
        }
    }
function cargarActivoxSucursal(){
    $.ajax({
        url: '{{ url("activoSucursal/searchN") }}'+ '/' +document.getElementById("idSucursal").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("idSucursal").value
        },
        success: function(data){
            document.getElementById("idActivoFijo").innerHTML = "<option value=''>--Seleccione un Activo Fijo--</option>";
            for (var i=0; i<data.length; i++) {
                document.getElementById("idActivoFijo").innerHTML += "<option value='"+data[i].activo_id+"'>"+data[i].producto_nombre+"-"+data[i].activo_descripcion+"</option>";
            }           
        },
    });
}
function cargarActivo(){    
    limpiarCampos();
    $.ajax({
        url: '{{ url("activoVentaActivo/searchN") }}'+ '/' +document.getElementById("idActivoFijo").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("idActivoFijo").value
        },
        success: function(data){
            document.getElementById("idValor").value = data.activo_valor;
            document.getElementById("porcentaje_depreciacion").value = data.activo_depreciacion;
            document.getElementById("idVidaUtil").value = data.activo_vida_util;
            document.getElementById("idValorUtil").value = data.activo_valor_util;
            document.getElementById("idBaseDepreciar").value = data.activo_base_depreciar;
            document.getElementById("idDepreciacionMensual").value = data.activo_depreciacion_mensual;
            document.getElementById("idDepreciacionAnual").value = data.activo_depreciacion_anual;
            document.getElementById("idDepreciacionAcumulada").value = data.activo_depreciacion_acumulada;
            totalVentas();                  
        },
    });
}
function limpiarCampos(){    
    document.getElementById("idValor").value = 0;
    document.getElementById("idVidaUtil").value = 0;
    document.getElementById("idValorUtil").value = 0;
    document.getElementById("idBaseDepreciar").value = 0;
    document.getElementById("idDepreciacionMensual").value = 0;
    document.getElementById("idDepreciacionAnual").value = 0;
    document.getElementById("idDepreciacionAcumulada").value = 0;
}

</script>
@endsection