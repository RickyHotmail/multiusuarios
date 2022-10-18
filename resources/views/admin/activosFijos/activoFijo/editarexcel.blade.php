@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('activoFijo.update', [$activoFijo->activo_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Activo Fijo</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
            <!--     <button type="button" onclick='window.location = "{{ url("activoFijo") }}";'  class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>   -->         
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>     
            </div>
        </div>
        <div class="card-body">
                        <div class="form-group row">
                            <label for="idDesde" class="col-sm-3 col-form-label">Fecha</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idDesde" name="idDesde"  value="{{$activoFijo->activo_fecha_inicio}}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idSucursalGrupo" class="col-sm-3 col-form-label">Sucursal</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idSucursalGrupo" name="idSucursalGrupo" onchange="cargarGrupo();" required>
                                    <option value="" label>--Seleccione una Sucursal--</option>
                                    @foreach($sucursales as $sucursal)
                                        @if($sucursal->sucursal_id == $activoFijo->sucursal_id)
                                            <option value="{{$sucursal->sucursal_id}}" selected>{{$sucursal->sucursal_nombre}}</option>
                                        @else 
                                            <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idProducto" class="col-sm-3 col-form-label">Producto</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idProducto" name="idProducto" required>
                                    <option value="" label>--Seleccione un prodcuto--</option>                                  
                                    @foreach($productos as $producto)
                                        @if($producto->producto_id == $activoFijo->producto_id)
                                            <option value="{{$producto->producto_id}}" selected>{{$producto->producto_nombre}}</option>
                                        @else 
                                                <option value="{{$producto->producto_id}}">{{$producto->producto_nombre}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idGrupo" class="col-sm-3 col-form-label">Tipo de Activo</label>
                            <div class="col-sm-9">
                            <select class="custom-select select2" id="idGrupo" name="idGrupo" onchange="cargarCuentaDepreciacion();" required>
                                    @foreach($gruposActivo as $grupoActivo)
                                        @if($grupoActivo->grupo_id == $activoFijo->grupo_id)
                                            <option value="{{$grupoActivo->grupo_id}}" selected>{{$grupoActivo->grupo_nombre}}</option>
                                        @else 
                                                <option value="{{$grupoActivo->grupo_id}}">{{$grupoActivo->grupo_nombre}}</option>
                                        @endif
                                    @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idDepreciacion" class="col-sm-3 col-form-label">Cuenta Depreciacion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idDepreciacion" name="idDepreciacion" value="{{$activoFijo->grupoActivo->cuentaDepreciacion->cuenta_nombre}}"  readonly required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idGasto" class="col-sm-3 col-form-label">Cuenta Gasto</label>
                            <div class="col-sm-9">
                                <label class="form-control" id="idGasto" name="idGasto" value="{{$activoFijo->grupoActivo->cuentaGasto->cuenta_nombre}}">{{$activoFijo->grupoActivo->cuentaGasto->cuenta_nombre}}</label>
                            </div>
                        </div>
                        <div class="form-group row">
                                <label for="porcentaje_depreciacion" class="col-sm-3 col-form-label">% Depreciacion</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="porcentaje_depreciacion" name="porcentaje_depreciacion" value="{{$activoFijo->grupoActivo->grupo_porcentaje}}" readonly required>
                                </div>
                        </div> 
                        <div class="form-group row">
                                <label for="idValor" class="col-sm-3 col-form-label">Valor</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idValor" name="idValor" value="{{$activoFijo->activo_valor}}" onkeyup="calcularValores();" onclick="calcularValores();"  required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idVidaUtil" class="col-sm-3 col-form-label">% Vida Util</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idVidaUtil" name="idVidaUtil" value="{{$activoFijo->activo_vida_util}}"  onkeyup="calcularValores();" onclick="calcularValores();"  required>
                                </div>
                        </div>                  
                        <div class="form-group row">
                                <label for="idValorUtil" class="col-sm-3 col-form-label">Valor vida Util</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idValorUtil" name="idValorUtil" value="{{number_format($activoFijo->activo_valor_util,2)}}" readonly required>
                                </div>
                        </div>                                      
                        <div class="form-group row">
                                <label for="idBaseDepreciar" class="col-sm-3 col-form-label">Base a depreciar</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idBaseDepreciar" name="idBaseDepreciar" value="{{number_format($activoFijo->activo_base_depreciar,2)}}" readonly required>
                                </div>
                        </div>                  
                        <div class="form-group row">
                                <label for="idDepreciacionMensual" class="col-sm-3 col-form-label">Depreciacion Mensual</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idDepreciacionMensual" name="idDepreciacionMensual" value="{{number_format($activoFijo->activo_depreciacion_mensual,2)}}" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idDepreciacionAnual" class="col-sm-3 col-form-label">Depreciacion Anual</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idDepreciacionAnual" name="idDepreciacionAnual" value="{{number_format($activoFijo->activo_depreciacion_anual,2)}}" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idDepreciacionAcumulada" class="col-sm-3 col-form-label">Depreciacion Acumulada</label>
                                <div class="col-sm-9">                                    
                                    <input type="text" class="form-control" id="idDepreciacionAcumulada" name="idDepreciacionAcumulada" value="{{number_format($activoFijo->activo_depreciacion_acumulada,2)}}" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idDescripcion" class="col-sm-3 col-form-label">Descripcion</label>
                                <div class="col-sm-9">                                    
                                    <textarea type="text" class="form-control" id="idDescripcion" name="idDescripcion" required>{{$activoFijo->activo_descripcion}}</textarea>
                                </div>
                        </div>
                        <div class="form-group row">
                        <label for="idEstado" class="col-sm-3 col-form-label">Estado</label>
                        <div class="col-sm-9">
                            <div class="col-form-label custom-control custom-switch custom-switch-off-danger custom-switch-on-success"> 
                                    <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" @if($activoFijo->activo_estado=="1") checked @endif>
                                <label class="custom-control-label" for="idEstado"></label>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
                    
    </div>
</form>
<script type="text/javascript">
function cargarGrupo(){ 
    limpiarGrupo();   
    $.ajax({
        url: '{{ url("grupoSucursal/searchN") }}'+ '/' +document.getElementById("idSucursalGrupo").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("idSucursalGrupo").value
        },
        success: function(data){
            document.getElementById("idGrupo").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            for (var i=0; i<data.length; i++) {
                document.getElementById("idGrupo").innerHTML += "<option value='"+data[i].grupo_id+"'>"+data[i].grupo_nombre+"</option>";
            }           
        },
    });
}

function cargarCuentaDepreciacion(){    
    $.ajax({
        url: '{{ url("cuentaDepreciacion/searchN") }}'+ '/' +document.getElementById("idGrupo").value,
        dataType: "json",
        type: "GET",
        data2: {
            buscar: document.getElementById("idGrupo").value
        },        
        success: function(data2){
            document.getElementById("idDepreciacion").value = "";                                         
            document.getElementById("idDepreciacion").value = data2.cuenta_numero+" - "+data2.cuenta_nombre;
                                    
        },
    });
    cargarCuentaGasto();
}
function cargarCuentaGasto(){    
    $.ajax({
        url: '{{ url("cuentaGasto/searchN") }}'+ '/' +document.getElementById("idGrupo").value,
        dataType: "json",
        type: "GET",
        data3: {
            buscar: document.getElementById("idGrupo").value
        },        
        success: function(data3){           
            document.getElementById("idGasto").innerHTML = "<label value=''></label>";                                             
            document.getElementById("idGasto").innerHTML += "<label value='"+data3.cuenta_id+"'>"+data3.cuenta_numero+" - "+data3.cuenta_nombre+"</label>";           
                                    
        },
    });
    cargarPorcentaje();
}
function cargarPorcentaje(){    
    $.ajax({
        url: '{{ url("porcentajeDepreciacion/searchN") }}'+ '/' +document.getElementById("idGrupo").value,
        dataType: "json",
        type: "GET",
        data3: {
            buscar: document.getElementById("idGrupo").value
        },        
        success: function(data3){
            document.getElementById("porcentaje_depreciacion").value = data3.grupo_porcentaje;
        },
    });
}
function cargarFacturas(){    
    $.ajax({
        url: '{{ url("facturaCompra/searchN") }}'+ '/' +document.getElementById("idProveedor").value,
        dataType: "json",
        type: "GET",
        data3: {
            buscar: document.getElementById("idProveedor").value
        },        
        success: function(data3){
            document.getElementById("idFactura").innerHTML = "<option value='' label>--Seleccione una factura--</option>"; 
            for (var i=0; i<data3.length; i++) {
                document.getElementById("idFactura").innerHTML += "<option value='"+data3[i].transaccion_id+"'>"+data3[i].transaccion_numero+"</option>";
            }                       
        },
    });
    document.getElementById("idFecha").value = "";
}
function desactivarFactura(){
    document.getElementById("idFactura").innerHTML = "<option value='' label>--Seleccione una factura--</option>";
    document.getElementById("idFactura").disabled = true;
    document.getElementById("idProveedor").disabled = true;
    $('#idFactura').val(null).trigger('change');
    $('#idDiario').val(null).trigger('change');
    document.getElementById("idFecha").value = "";
    document.getElementById("idValor").value = "";
    document.getElementById("idValorUtil").value = 0;
    document.getElementById("idDepreciacionAnual").value = 0;        
    document.getElementById("idBaseDepreciar").value = 0;
    document.getElementById("idVidaUtil").value = 0;    
    document.getElementById("idDepreciacionMensual").value = 0;
    document.getElementById("idDiario").disabled = false;



}
function activarFactura(){
    cargarFacturas();
    document.getElementById("idFactura").disabled = false;
    if (document.getElementById("rdDocumento").value = "FACTURA"){
        document.getElementById("idValor").value = "";
        document.getElementById("idValorUtil").value = 0;
        document.getElementById("idDepreciacionAnual").value = 0;        
        document.getElementById("idBaseDepreciar").value = 0;
        document.getElementById("idVidaUtil").value = 0;
        document.getElementById("idDepreciacionMensual").value = 0;
        document.getElementById("idProveedor").disabled = false;
        document.getElementById("idDiario").disabled = true;        

    }
}
function limpiarGrupo(){
    document.getElementById("idDepreciacion").value = "";
    document.getElementById("idGasto").innerHTML = "<label value=''></label>";          
    document.getElementById("porcentaje_depreciacion").value = 0;   
}

function cargarFechaFactura(){    
    $.ajax({
        url: '{{ url("fechaDocumento/searchN") }}'+ '/' +document.getElementById("idFactura").value,
        dataType: "json",
        type: "GET",
        data3: {
            buscar: document.getElementById("idFactura").value
        },        
        success: function(data3){
            document.getElementById("idValor").value = Number(data3.transaccion_subtotal).toFixed(2);
            document.getElementById("idFecha").value = "";                                        
            document.getElementById("idFecha").value = data3.transaccion_fecha;            
            /*('#idDiario').val(data3.diario_id);*/
            /*$('#idDiario').trigger('change');*/

        },
    });
}
function cargarFechaDiario(){    
    $.ajax({
        url: '{{ url("fechaDiario/searchN") }}'+ '/' +document.getElementById("idDiario").value,
        dataType: "json",
        type: "GET",
        data3: {
            buscar: document.getElementById("idDiario").value
        },        
        success: function(data3){
            document.getElementById("idFecha").value = "";                                            
            document.getElementById("idFecha").value = data3.diario_fecha;
                                  
        },
    });
    totalDiario();
}
function totalDiario(){    
    $.ajax({
        url: '{{ url("sumaDiario/searchN") }}'+ '/' +document.getElementById("idDiario").value,
        dataType: "json",
        type: "GET",
        data3: {
            buscar: document.getElementById("idDiario").value
        },        
        success: function(data3){ 
            if (document.getElementById("rdDocumento").value = "DIARIO"){
                document.getElementById("idValor").value = Number(data3.total_diario).toFixed(2);
            }                        
        },
    });
}
function calcularValores() {
        if (document.getElementById("idValor").value != "" && document.getElementById("idVidaUtil").value != "") {
            //valor de vida util
            document.getElementById("idValorUtil").value = Number(parseFloat(document.getElementById("idValor").value) *
                parseFloat(document.getElementById("idVidaUtil").value) / parseFloat(100)).toFixed(2);
            //base a depreciar idBaseDepreciar
            document.getElementById("idBaseDepreciar").value = Number(parseFloat(document.getElementById("idValor").value) -
                parseFloat(document.getElementById("idValorUtil").value)).toFixed(2);
        } else {
            document.getElementById("idValorUtil").value = 0;
        }
        calcularDepresiacionAnual();
    }
    function calcularDepresiacionAnual() {      
        //idDepreciacionAnual
        if (document.getElementById("porcentaje_depreciacion").value != "" && document.getElementById("idBaseDepreciar").value != "") {            
            //base a depreciar idBaseDepreciar
            document.getElementById("idDepreciacionAnual").value =Number(Number(document.getElementById("idBaseDepreciar").value) *
            Number(document.getElementById("porcentaje_depreciacion").value) / parseFloat(100)).toFixed(2);
        } else {
            document.getElementById("idDepreciacionAnual").value = 0;
        }
        calcularDepresiacionMensual();
    }
    function calcularDepresiacionMensual() {      
        //idDepreciacionAnual
        if (document.getElementById("idDepreciacionAnual").value != "") {            
            //base a depreciar idBaseDepreciar
            document.getElementById("idDepreciacionMensual").value =Number(Number(document.getElementById("idDepreciacionAnual").value) /
            parseFloat(12)).toFixed(2);
        } else {
            document.getElementById("idDepreciacionMensual").value = 0;
        }
    }

</script>
@endsection