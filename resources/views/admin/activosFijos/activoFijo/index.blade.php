@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Activos Fijos</h3>
        <div class="float-right">
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
        <a class="btn btn-info btn-sm" href="{{ asset('admin/archivos/FORMATO_ACTIVOSFIJOS.xlsx') }}" download="FORMATO ACTIVOS FIJOS"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
        <a class="btn btn-success btn-sm" href="{{ url("excelActivoFijo") }}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>
        </div>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("ActivoBuscar") }}">
        @csrf
            <div class="form-group row">
                <label for="idsucursal" class="col-sm-1 col-form-label"><center>Sucursal:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="idsucursal" name="idsucursal">
                        <option value="0" label>--TODOS--</option>  
                        @foreach($sucursales as $sucursal)                            
                            <option value="{{$sucursal->sucursal_id}}" @if(isset($sucursalselect)) @if($sucursalselect == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                                                     
                        @endforeach
                    </select> 
                </div>       
                <label for="idestado" class="col-sm-1 col-form-label"><center>Estado:</center></label>
                <div class="col-sm-2">
                    <select class="custom-select select2" id="idestado" name="idestado">
                        <option value="4" label>--TODOS--</option>  
                        @foreach($estados as $estado)                            
                            <option value="{{$estado->activo_estado}}" @if(isset($estadoselect)) @if($estadoselect == $estado->activo_estado) selected @endif @endif>
                            @if($estado->activo_estado == '1')
                                Activos
                            @else
                                Inactivos
                            @endif
                            </option>
                                                     
                        @endforeach
                        
                    </select> 
                </div>                           
                <div class="col-sm-1">
                    <center><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>                    
            </div>            
        </form>        
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr>  
                    <th></th>
                    <th>Fecha</th>
                    <th>Diario</th>
                    <th>Producto</th>
                    <th>Tipo de Activo</th>                   
                    <th>Valor</th>
                    <th>% Depreciacion</th>
                    <th>Base depreciar</th>
                    <th>Vida util</th>
                    <th>Valor util</th>
                    <th>Depreciacion Mensual</th>
                    <th>Depreciacion Anual</th>
                    <th>Depreciacion Acumulada</th>
                    <th>Descripcion</th>
                </tr>
            </thead>            
            <tbody>
            @if(isset($activoFijos))
                @foreach($activoFijos as $activoFijo)
                <tr class="text-center">
                    <td>
                    @if(isset($activoFijo->diario_id))
                        <a href="{{ url("activoFijo/{$activoFijo->activo_id}/edit")}}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                   @else
                   <a href="{{ url("activoFijo/{$activoFijo->activo_id}/editexcel")}}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                   @endif
                        <a href="{{ url("activoFijo/{$activoFijo->activo_id}")}}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("activoFijo/{$activoFijo->activo_id}/eliminar")}}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $activoFijo->activo_fecha_inicio}}</td>
                    <td>@if(isset($activoFijo->diario_id))<a href="{{ url("asientoDiario/ver/{$activoFijo->diario->diario_codigo}")}}" target="_blank">{{ $activoFijo->diario->diario_codigo}}</a> @endif</td>
                    <td>{{ $activoFijo->producto->producto_nombre}}</td>
                    <td>@if(isset($activoFijo->grupo_id)) {{ $activoFijo->grupoActivo->grupo_nombre}} @endif</td>                    
                    <td>{{ number_format($activoFijo->activo_valor,2)}}</td>
                    <td>@if(isset($activoFijo->grupo_id)) {{ $activoFijo->grupoActivo->grupo_porcentaje}} @endif</td>
                    <td>{{ number_format($activoFijo->activo_base_depreciar,2)}}</td>
                    <td>{{ number_format($activoFijo->activo_vida_util,2)}}</td>
                    <td>{{ number_format($activoFijo->activo_valor_util,2)}}</td>
                    <td>{{ number_format($activoFijo->activo_depreciacion_mensual,2)}}</td>
                    <td>{{ number_format($activoFijo->activo_depreciacion_anual,2)}}</td>
                    <td>{{ number_format($activoFijo->activo_depreciacion_acumulada,2)}}</td>
                    <td class="text-left">{{ $activoFijo->activo_descripcion}}</td>
                </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Activo Fijo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("activoFijo") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idDesde" class="col-sm-3 col-form-label">Fecha</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idSucursalGrupo" class="col-sm-3 col-form-label">Sucursal</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idSucursalGrupo" name="idSucursalGrupo" onchange="cargarGrupo();" required>
                                    <option value="" label>--Seleccione una Sucursal--</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
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
                                        <option value="{{$producto->producto_id}}">{{$producto->producto_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idGrupo" class="col-sm-3 col-form-label">Tipo de Activo</label>
                            <div class="col-sm-9">
                            <select class="custom-select select2" id="idGrupo" name="idGrupo" onchange="cargarCuentaDepreciacion();" required>
                                    <option value="" label>--Seleccione una opcion--</option>    
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idDepreciacion" class="col-sm-3 col-form-label">Cuenta Depreciacion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idDepreciacion" name="idDepreciacion" value="0" readonly required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idGasto" class="col-sm-3 col-form-label">Cuenta Gasto</label>
                            <div class="col-sm-9">
                                <label class="form-control" id="idGasto" name="idGasto" value = ""></label>
                            </div>
                        </div>
                        <div class="form-group row">
                                <label for="porcentaje_depreciacion" class="col-sm-3 col-form-label">% Depreciacion</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="porcentaje_depreciacion" name="porcentaje_depreciacion" value="0" readonly required>
                                </div>
                        </div> 
                        <div class="form-group row">
                            <label for="idProveedor" class="col-sm-3 col-form-label">Proveedor</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idProveedor" name="idProveedor" onchange="cargarFacturas();" required>
                                    <option value="" label>--Seleccione un Proveedor--</option>                                   
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{$proveedor->proveedor_id}}">{{$proveedor->proveedor_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rdDocumento" class="col-sm-3 col-form-label">Seleccione una opcion</label>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 col-form-label" style="margin-bottom : 0px;">
                                <div class="demo-checkbox">
                                    <input type="radio" value="FACTURA" onclick="activarFactura();" id="rdDocumento"
                                        class="with-gap radio-col-deep-orange" name="rdDocumento" checked required />
                                    <label class="form-check-label" for="check1">FACTURA</label>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 col-form-label" style="margin-bottom : 0px;">
                                <div class="demo-checkbox">
                                    <input type="radio" value="DIARIO" onclick="desactivarFactura();" id="rdDocumento"
                                        class="with-gap radio-col-deep-orange" name="rdDocumento"  required />
                                    <label class="form-check-label" for="check1">SOLO DIARIO</label>
                                </div>
                            </div>
                            <label for="idDiario" class="col-sm-1 col-form-label">#Diario</label>
                            <div class="col-sm-4">
                                <select class="custom-select select2" id="idDiario" name="idDiario" onchange="cargarFechaDiario();" disabled>
                                    <option value="" label>--Seleccione un Diario--</option>
                                    @foreach($diarios as $diario)
                                        <option value="{{$diario->diario_id}}">{{$diario->diario_codigo}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idFactura" class="col-sm-3 col-form-label">Factura</label>
                            <div class="col-sm-4">
                                <select class="custom-select select2" id="idFactura" name="idFactura" onchange="cargarFechaFactura();" required>
                                    <option value="" label>--Seleccione una factura--</option>
                                </select>
                            </div>
                            <label for="idDescripcion" class="col-sm-1 col-form-label">Fecha</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="idFecha" name="idFecha" value="" readonly required>
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
                                    <input type="text" class="form-control" id="idVidaUtil" name="idVidaUtil" value="0" onchange="calcularValores();" required>
                                </div>
                        </div>                  
                        <div class="form-group row">
                                <label for="idValorUtil" class="col-sm-3 col-form-label">Valor vida Util</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idValorUtil" name="idValorUtil" value="0.00" readonly required>
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
                                    <input type="text" class="form-control" id="idDepreciacionAnual" name="idDepreciacionAnual" value="0.00" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idDepreciacionAcumulada" class="col-sm-3 col-form-label">Depreciacion Acumulada</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idDepreciacionAcumulada" name="idDepreciacionAcumulada" value="0.00" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idDescripcion" class="col-sm-3 col-form-label">Descripcion</label>
                                <div class="col-sm-9">                                    
                                    <textarea type="text" class="form-control" id="idDescripcion" name="idDescripcion" placeholder="Descripcion" required></textarea>
                                </div>
                        </div>
                        </div>
                    </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>             
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
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
           /* $('#idDiario').val(data3.diario_id);
            $('#idDiario').trigger('change');*/

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